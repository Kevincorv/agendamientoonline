import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { obtenerMedicoPorUsuarioId } from "@/lib/repos/medicos";
import { citasDelMedico, obtenerCitaPorId } from "@/lib/repos/citas";
import { todayInTz } from "@/lib/time";
import { MedicoDashboardCliente } from "@/components/medico-dashboard-cliente";

export const dynamic = "force-dynamic";

export default async function MedicoDashboardPage({ searchParams }: { searchParams: Promise<Record<string, string | string[] | undefined>> }) {
  const session = await auth();
  if (!session?.user) redirect("/admin/login");
  if (session.user.rol !== "medico") redirect("/admin/dashboard");

  const med = await obtenerMedicoPorUsuarioId(session.user.id);
  if (!med) redirect("/admin/login");

  const sp = await searchParams;
  const fecha = typeof sp.fecha === "string" && sp.fecha ? sp.fecha : todayInTz();
  const citas = await citasDelMedico(med.id, fecha);

  // Estadísticas simples
  const totalAtendidas = citas.filter((c) => c.estado_id === 4).length;
  const pendientesHoy = citas.filter((c) => c.estado_id === 1 || c.estado_id === 2).length;
  const totalHoy = citas.length;

  let proximaCita: Awaited<ReturnType<typeof obtenerCitaPorId>> = null;
  const now = new Date().toTimeString().slice(0, 5);
  for (const c of citas) {
    if ((c.estado_id === 1 || c.estado_id === 2) && String(c.hora).slice(0, 5) >= now) {
      proximaCita = await obtenerCitaPorId(c.id);
      break;
    }
  }

  return (
    <MedicoDashboardCliente
      medico={{ id: med.id, nombre: med.nombre, apellido: med.apellido, especialidad_nombre: med.especialidad_nombre }}
      fecha={fecha}
      citas={citas.map((c) => ({
        id: c.id,
        nombre_paciente: c.nombre_paciente,
        telefono: c.telefono ?? "",
        email: c.email ?? "",
        hora: String(c.hora).slice(0, 5),
        motivo: c.motivo ?? "",
        estado: c.estado ?? "",
        estado_id: c.estado_id,
      }))}
      stats={{ totalAtendidas, pendientesHoy, totalHoy }}
      proximaCita={proximaCita ? {
        id: proximaCita.id,
        nombre_paciente: proximaCita.nombre_paciente,
        hora: String(proximaCita.hora).slice(0, 5),
      } : null}
    />
  );
}
