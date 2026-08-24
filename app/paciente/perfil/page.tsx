import { redirect } from "next/navigation";
import Link from "next/link";
import { auth } from "@/lib/auth";
import { obtenerUsuarioPorId } from "@/lib/repos/usuarios";
import { PacientePerfilCliente } from "@/components/paciente-perfil-cliente";

export const dynamic = "force-dynamic";

export default async function PacientePerfilPage() {
  const session = await auth();
  if (!session?.user) redirect("/paciente/login");
  if (session.user.rol !== "paciente") redirect("/admin/dashboard");

  const u = await obtenerUsuarioPorId(session.user.id);
  if (!u) redirect("/paciente/login");

  return (
    <PacientePerfilCliente
      user={{ id: u.id, nombre: u.nombre, apellido: u.apellido, email: u.email, last_login: u.last_login ?? null }}
    />
  );
}
