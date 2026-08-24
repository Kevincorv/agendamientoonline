import { obtenerEspecialidades } from "@/lib/repos/especialidades";
import { obtenerMedicosPorEspecialidad, obtenerMedicoPorId } from "@/lib/repos/medicos";
import { generarSlots } from "@/lib/repos/horarios";
import { todayInTz, addDays } from "@/lib/time";
import { AgendarWizard } from "@/components/agendar-wizard";

export const dynamic = "force-dynamic";

export default async function AgendarPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | string[] | undefined>>;
}) {
  const p = await searchParams;
  const especialidadId = Number(p.especialidad_id) || 0;
  const medicoId = Number(p.medico_id) || 0;
  const fecha = typeof p.fecha === "string" ? p.fecha : "";
  const hora = typeof p.hora === "string" ? p.hora : "";

  const especialidades = await obtenerEspecialidades();
  let medicos: Awaited<ReturnType<typeof obtenerMedicosPorEspecialidad>> = [];
  if (especialidadId) medicos = await obtenerMedicosPorEspecialidad(especialidadId);
  let medicoSel: Awaited<ReturnType<typeof obtenerMedicoPorId>> = null;
  if (medicoId) medicoSel = await obtenerMedicoPorId(medicoId);
  let slots: Awaited<ReturnType<typeof generarSlots>> = [];
  const today = todayInTz();
  if (medicoId && fecha && fecha >= today) slots = await generarSlots(medicoId, fecha);

  return (
    <AgendarWizard
      especialidades={especialidades.map((e) => ({ id: e.id, nombre: e.nombre, icono: e.icono }))}
      medicos={medicos.map((m) => ({
        id: m.id,
        nombre: m.nombre,
        apellido: m.apellido,
        descripcion: m.descripcion,
        especialidad_nombre: m.especialidad_nombre ?? null,
      }))}
      medicoSel={
        medicoSel
          ? {
              nombre: medicoSel.nombre,
              apellido: medicoSel.apellido,
              especialidad_nombre: medicoSel.especialidad_nombre ?? null,
            }
          : null
      }
      slots={slots}
      especialidadId={especialidadId}
      medicoId={medicoId}
      fecha={fecha}
      hora={hora}
      minDate={today}
      maxDate={addDays(today, 60)}
    />
  );
}
