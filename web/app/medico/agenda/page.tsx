import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { obtenerMedicoPorUsuarioId } from "@/lib/repos/medicos";
import { citasDelMedico } from "@/lib/repos/citas";
import { MedicoAgendaCliente } from "@/components/medico-agenda-cliente";

export const dynamic = "force-dynamic";

export default async function MedicoAgendaPage() {
  const session = await auth();
  if (!session?.user) redirect("/admin/login");
  if (session.user.rol !== "medico") redirect("/admin/dashboard");

  const med = await obtenerMedicoPorUsuarioId(session.user.id);
  if (!med) redirect("/admin/login");

  const citas = await citasDelMedico(med.id);

  // Agrupar por fecha
  const porFecha: Record<string, Array<{ id: number; nombre_paciente: string; hora: string; telefono: string; estado: string; estado_id: number; motivo: string }>> = {};
  for (const c of citas) {
    if (!porFecha[c.fecha]) porFecha[c.fecha] = [];
    porFecha[c.fecha].push({
      id: c.id,
      nombre_paciente: c.nombre_paciente,
      hora: String(c.hora).slice(0, 5),
      telefono: c.telefono ?? "",
      estado: c.estado ?? "",
      estado_id: c.estado_id,
      motivo: c.motivo ?? "",
    });
  }

  return (
    <MedicoAgendaCliente
      medico={{ id: med.id, nombre: med.nombre, apellido: med.apellido }}
      porFecha={porFecha}
    />
  );
}
