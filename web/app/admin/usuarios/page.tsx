import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { obtenerRoles, obtenerUsuarios } from "@/lib/repos/usuarios";
import { UsuariosCliente } from "@/components/admin/usuarios-cliente";

export const dynamic = "force-dynamic";

export default async function UsuariosPage() {
  const session = await auth();
  if (!session?.user) redirect("/admin/login");
  if (session.user.rol === "paciente") redirect("/paciente/dashboard");
  if (session.user.rol === "medico") redirect("/medico/dashboard");

  const [usuarios, roles] = await Promise.all([obtenerUsuarios(), obtenerRoles()]);
  return (
    <UsuariosCliente
      currentUserId={session.user.id}
      usuarios={usuarios.map((u) => ({
        id: u.id,
        nombre: u.nombre,
        apellido: u.apellido,
        email: u.email,
        rol_id: u.rol_id ?? 0,
        rol_nombre: u.rol_nombre ?? "—",
        activo: u.activo,
        login_attempts: u.login_attempts ?? 0,
        locked_until: u.locked_until ? (u.locked_until instanceof Date ? u.locked_until.toISOString() : String(u.locked_until)) : null,
        last_login: u.last_login ? (u.last_login instanceof Date ? u.last_login.toISOString().slice(0, 19).replace("T", " ") : String(u.last_login)) : null,
      }))}
      roles={roles.map((r) => ({ id: (r as { id: number }).id, nombre: (r as { nombre: string }).nombre }))}
    />
  );
}
