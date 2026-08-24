"use server";

import bcrypt from "bcryptjs";
import { redirect } from "next/navigation";
import { queryOne, execute } from "@/lib/db";
import { auditLog } from "@/lib/audit";
import { crearNotificacion } from "@/lib/notifications";

export type RegistroState = { error?: string } | undefined;

export async function registrarPaciente(_prev: RegistroState, formData: FormData): Promise<RegistroState> {
  const nombre = String(formData.get("nombre") ?? "").trim();
  const apellido = String(formData.get("apellido") ?? "").trim();
  const email = String(formData.get("email") ?? "").trim().toLowerCase();
  const password = String(formData.get("password") ?? "");
  const confirmar = String(formData.get("password_confirm") ?? "");

  if (!nombre || !apellido || !email || !password) return { error: "Completá todos los campos obligatorios." };
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return { error: "Email inválido." };
  if (password.length < 6) return { error: "La contraseña debe tener al menos 6 caracteres." };
  if (password !== confirmar) return { error: "Las contraseñas no coinciden." };

  const existe = await queryOne("SELECT id FROM usuarios WHERE LOWER(email) = :email", { email });
  if (existe) return { error: "Este email ya está registrado." };

  const hash = await bcrypt.hash(password, 10);
  await execute(
    `INSERT INTO usuarios (nombre, apellido, email, password, rol_id, activo)
     VALUES (:nombre, :apellido, :email, :password, 6, 1)`,
    { nombre, apellido, email, password: hash }
  );

  await auditLog({
    accion: "registro",
    tabla: "usuarios",
    descripcion: `Paciente registrado: ${email}`,
  });
  await crearNotificacion("Nuevo paciente registrado", `${nombre} ${apellido} - ${email}`, "success");

  redirect("/paciente/login?ok=1");
}
