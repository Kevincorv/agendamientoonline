import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { obtenerCitasPaciente } from "@/lib/repos/pacientes";
import { obtenerMedicoPorUsuarioId } from "@/lib/repos/medicos";
import { PacienteDashboardCliente } from "@/components/paciente-dashboard-cliente";

export const dynamic = "force-dynamic";

export default async function PacienteDashboardPage() {
  const session = await auth();
  if (!session?.user) redirect("/paciente/login");
  if (session.user.rol !== "paciente") redirect("/admin/dashboard");

  const email = session.user.email ?? "";
  const [proximas, historial] = await Promise.all([
    obtenerCitasPaciente(email, true),
    obtenerCitasPaciente(email, false, 5),
  ]);

  return (
    <PacienteDashboardCliente
      user={{ nombre: session.user.nombre, apellido: session.user.apellido, email: session.user.email ?? "" }}
      proximas={proximas.map((c) => ({
        id: c.id as number,
        nombre_paciente: String(c.nombre_paciente ?? ""),
        telefono: String(c.telefono ?? ""),
        email: String(c.email ?? ""),
        hora: String(c.hora).slice(0, 5),
        fecha: String(c.fecha),
        estado: String(c.estado ?? ""),
        estado_id: Number(c.estado_id),
        motivo: String(c.motivo ?? ""),
        especialidad: c.especialidad ? String(c.especialidad) : null,
        medico_nombre: c.medico_nombre ? String(c.medico_nombre) : "",
        medico_apellido: c.medico_apellido ? String(c.medico_apellido) : "",
      }))}
      historial={historial.map((c) => ({
        id: c.id as number,
        fecha: String(c.fecha),
        estado: String(c.estado ?? ""),
        especialidad: c.especialidad ? String(c.especialidad) : null,
      }))}
    />
  );
}
