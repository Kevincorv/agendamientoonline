import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { listarMedicosAdmin } from "@/lib/repos/medicos";
import {
  obtenerBloqueosPorMedico,
  obtenerHorariosPorMedico,
  obtenerTodosBloqueos,
} from "@/lib/repos/horarios";
import { HorariosCliente } from "@/components/admin/horarios-cliente";

export const dynamic = "force-dynamic";

export default async function HorariosPage() {
  const session = await auth();
  if (!session?.user) redirect("/admin/login");
  if (session.user.rol === "paciente") redirect("/paciente/dashboard");
  if (session.user.rol === "medico") redirect("/medico/dashboard");

  const medicos = await listarMedicosAdmin();
  const horariosPorMedico: Record<number, Array<{ id: number; dia_semana: number; hora_inicio: string; hora_fin: string; intervalo_minutos: number }>> = {};
  const bloqueosPorMedico: Record<number, Array<{ id: number; fecha: string; motivo: string | null }>> = {};
  await Promise.all(
    medicos.map(async (m) => {
      horariosPorMedico[m.id] = (await obtenerHorariosPorMedico(m.id)).map((h) => ({
        id: h.id,
        dia_semana: h.dia_semana,
        hora_inicio: String(h.hora_inicio).slice(0, 5),
        hora_fin: String(h.hora_fin).slice(0, 5),
        intervalo_minutos: h.intervalo_minutos,
      }));
      bloqueosPorMedico[m.id] = (await obtenerBloqueosPorMedico(m.id)).map((b) => ({
        id: (b as { id: number }).id,
        fecha: String((b as { fecha: string }).fecha),
        motivo: ((b as { motivo: string | null }).motivo) ?? null,
      }));
    })
  );

  return (
    <HorariosCliente
      medicos={medicos.map((m) => ({ id: m.id, nombre: m.nombre, apellido: m.apellido, especialidad_nombre: m.especialidad_nombre }))}
      horarios={horariosPorMedico}
      bloqueos={bloqueosPorMedico}
    />
  );
}
