import { redirect } from "next/navigation";
import Link from "next/link";
import { auth } from "@/lib/auth";
import { obtenerCitaPorId } from "@/lib/repos/citas";
import { listarMedicosAdmin } from "@/lib/repos/medicos";
import { listarEspecialidadesAdmin } from "@/lib/repos/especialidades";
import { generarSlots } from "@/lib/repos/horarios";
import { todayInTz, toMysqlDate } from "@/lib/time";
import { ReagendarCliente } from "@/components/reagendar-cliente";

export const dynamic = "force-dynamic";

interface SP {
  searchParams: Promise<Record<string, string | string[] | undefined>>;
}

export default async function ReagendarPage({ searchParams }: SP) {
  const session = await auth();
  if (!session?.user) redirect("/paciente/login");
  if (session.user.rol !== "paciente") redirect("/admin/dashboard");

  const sp = await searchParams;
  const id = Number(sp.id ?? 0);
  if (!id) redirect("/paciente/dashboard");

  const cita = await obtenerCitaPorId(id);
  if (!cita || cita.email !== session.user.email) redirect("/paciente/dashboard");

  const fecha = typeof sp.fecha === "string" && sp.fecha ? sp.fecha : "";
  const medicoId = Number(sp.medico_id ?? 0) || cita.medico_id;

  const [medicos, especialidades] = await Promise.all([
    listarMedicosAdmin(),
    listarEspecialidadesAdmin(),
  ]);

  const slots = fecha && fecha >= todayInTz() ? await generarSlots(medicoId, fecha) : [];

  return (
    <ReagendarCliente
      cita={{
        id: cita.id,
        fecha: toMysqlDate(cita.fecha),
        hora: String(cita.hora).slice(0, 5),
        medico_id: cita.medico_id,
        medico_nombre: String(cita.medico_nombre ?? ""),
        medico_apellido: String(cita.medico_apellido ?? ""),
        especialidad: String(cita.especialidad ?? ""),
        motivo: String(cita.motivo ?? ""),
        estado: String(cita.estado ?? ""),
      }}
      medicos={medicos.map((m) => ({ id: m.id, nombre: m.nombre, apellido: m.apellido, especialidad_id: m.especialidad_id }))}
      especialidades={especialidades.filter((e) => e.activo === 1).map((e) => ({ id: e.id, nombre: e.nombre }))}
      initialMedicoId={medicoId}
      fecha={fecha}
      slots={slots}
    />
  );
}
