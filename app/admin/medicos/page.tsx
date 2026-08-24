import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { listarMedicosAdmin } from "@/lib/repos/medicos";
import { listarEspecialidadesAdmin } from "@/lib/repos/especialidades";
import { MedicosCliente } from "@/components/admin/medicos-cliente";

export const dynamic = "force-dynamic";

export default async function MedicosPage() {
  const session = await auth();
  if (!session?.user) redirect("/admin/login");
  if (session.user.rol === "paciente") redirect("/paciente/dashboard");
  if (session.user.rol === "medico") redirect("/medico/dashboard");

  const [medicos, especialidades] = await Promise.all([
    listarMedicosAdmin(),
    listarEspecialidadesAdmin(),
  ]);

  return (
    <MedicosCliente
      medicos={medicos.map((m) => ({
        id: m.id,
        nombre: m.nombre,
        apellido: m.apellido,
        email: m.email ?? "",
        telefono: m.telefono ?? "",
        especialidad_id: m.especialidad_id,
        especialidad_nombre: m.especialidad_nombre ?? "",
        matricula: m.matricula ?? "",
        descripcion: m.descripcion ?? "",
        disponible: m.disponible,
        activo: m.activo,
      }))}
      especialidades={especialidades.filter((e) => e.activo === 1).map((e) => ({ id: e.id, nombre: e.nombre }))}
    />
  );
}
