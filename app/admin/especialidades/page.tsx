import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { listarEspecialidadesAdmin } from "@/lib/repos/especialidades";
import { EspecialidadesCliente } from "@/components/admin/especialidades-cliente";

export const dynamic = "force-dynamic";

export default async function EspecialidadesPage() {
  const session = await auth();
  if (!session?.user) redirect("/admin/login");
  if (session.user.rol === "paciente") redirect("/paciente/dashboard");
  if (session.user.rol === "medico") redirect("/medico/dashboard");

  const especialidades = await listarEspecialidadesAdmin();

  return (
    <EspecialidadesCliente
      especialidades={especialidades.map((e) => ({
        id: e.id,
        nombre: e.nombre,
        descripcion: e.descripcion ?? "",
        icono: e.icono,
        activo: e.activo,
      }))}
    />
  );
}
