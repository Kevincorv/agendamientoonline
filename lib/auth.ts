import NextAuth from "next-auth";
import Credentials from "next-auth/providers/credentials";
import bcrypt from "bcryptjs";
import { randomBytes } from "node:crypto";
import { headers } from "next/headers";
import { queryOne, execute } from "./db";
import { env } from "./env";
import { auditLog } from "./audit";
import type { Usuario } from "./types";

const MAX_INTENTOS = 5;
const TIEMPO_BLOQUEO = 15; // minutos

async function obtenerIp(): Promise<string | null> {
  try {
    const h = await headers();
    return h.get("x-forwarded-for")?.split(",")[0]?.trim() ?? null;
  } catch {
    return null;
  }
}

async function registrarSesion(usuarioId: number): Promise<void> {
  const ip = await obtenerIp();
  let ua: string | null = null;
  try {
    ua = (await headers()).get("user-agent");
  } catch {
    /* noop */
  }
  const token = randomBytes(32).toString("hex");
  await execute(
    `INSERT INTO sesiones (usuario_id, session_token, ip, user_agent)
     VALUES (:uid, :token, :ip, :ua)`,
    { uid: usuarioId, token, ip, ua }
  );
}

export const { handlers, auth, signIn, signOut } = NextAuth({
  secret: env.authSecret,
  trustHost: env.trustHost,
  session: { strategy: "jwt" },
  pages: { signIn: "/admin/login", error: "/admin/login?error=Credentials" },
  providers: [
    Credentials({
      credentials: { email: {}, password: {} },
      async authorize(credentials) {
        const email = String(credentials?.email ?? "").trim().toLowerCase();
        const password = String(credentials?.password ?? "");
        if (!email || !password) return null;

        const user = await queryOne<Usuario>(
          `SELECT u.*, r.nombre AS rol
           FROM usuarios u
           JOIN roles r ON u.rol_id = r.id
           WHERE LOWER(u.email) = :email AND u.activo = 1`,
          { email }
        );
        if (!user) {
          await auditLog({
            accion: "login_fallido",
            descripcion: `Email inexistente: ${email}`,
          });
          return null;
        }

        // ── Bloqueo por intentos ──
        if (user.locked_until && new Date(user.locked_until) > new Date()) {
          await auditLog({
            accion: "login_bloqueado",
            descripcion: `Cuenta bloqueada: ${email}`,
          });
          return null;
        }

        // ── Verificar contraseña (bcrypt $2y$ compatible) ──
        const ok = await bcrypt.compare(password, user.password);
        if (!ok) {
          const intentos = (user.login_attempts ?? 0) + 1;
          if (intentos >= MAX_INTENTOS) {
            const locked = new Date(Date.now() + TIEMPO_BLOQUEO * 60000)
              .toISOString()
              .slice(0, 19)
              .replace("T", " ");
            await execute(
              `UPDATE usuarios SET login_attempts = :intentos, locked_until = :locked WHERE id = :id`,
              { intentos, locked, id: user.id }
            );
            await auditLog({
              accion: "cuenta_bloqueada",
              descripcion: `Bloqueada tras ${intentos} intentos: ${email}`,
            });
          } else {
            await execute(
              `UPDATE usuarios SET login_attempts = :intentos WHERE id = :id`,
              { intentos, id: user.id }
            );
            await auditLog({
              accion: "login_fallido",
              descripcion: `Contraseña incorrecta: ${email} (intento ${intentos})`,
            });
          }
          return null;
        }

        // ── Login exitoso ──
        const ip = await obtenerIp();
        await execute(
          `UPDATE usuarios
             SET login_attempts = 0, locked_until = NULL, last_login = NOW(), last_ip = :ip
           WHERE id = :id`,
          { ip, id: user.id }
        );
        await registrarSesion(user.id);
        await auditLog({
          usuarioId: user.id,
          usuarioNombre: `${user.nombre} ${user.apellido}`,
          accion: "login",
          tabla: "usuarios",
          registroId: user.id,
          descripcion: `Login exitoso: ${user.email}`,
        });

        return {
          id: user.id,
          nombre: user.nombre,
          apellido: user.apellido,
          email: user.email,
          rol: user.rol ?? "",
          rolId: user.rol_id,
        } as never;
      },
    }),
  ],
  callbacks: {
    jwt({ token, user }) {
      if (user) {
        const u = user as unknown as {
          id: number;
          nombre: string;
          apellido: string;
          rol: string;
          rolId: number | null;
        };
        token.id = u.id;
        token.nombre = u.nombre;
        token.apellido = u.apellido;
        token.rol = u.rol;
        token.rolId = u.rolId;
      }
      return token;
    },
    session({ session, token }) {
      const user = session.user as
        | { id: number; nombre: string; apellido: string; rol: string; rolId: number | null }
        | undefined;
      if (user) {
        user.id = token.id;
        user.nombre = token.nombre;
        user.apellido = token.apellido;
        user.rol = token.rol;
        user.rolId = token.rolId;
      }
      return session;
    },
  },
});
