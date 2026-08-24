import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { estaDisponible, obtenerCitaPorId, reagendarCita } from "@/lib/repos/citas";
import { auditLog } from "@/lib/audit";
import { crearNotificacion } from "@/lib/repos/notificaciones";
import { todayInTz } from "@/lib/time";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol !== "paciente") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const fd = await req.formData();
  const id = Number(fd.get("cita_id") ?? 0);
  const medicoId = Number(fd.get("medico_id") ?? 0);
  const fecha = String(fd.get("fecha") ?? "");
  const hora = String(fd.get("hora") ?? "");

  if (!id || !medicoId || !fecha || !hora) {
    return NextResponse.json({ success: false, message: "Completá todos los campos." });
  }
  if (fecha < todayInTz()) {
    return NextResponse.json({ success: false, message: "La fecha no puede ser en el pasado." });
  }
  const cita = await obtenerCitaPorId(id);
  if (!cita || cita.email !== session.user.email) {
    return NextResponse.json({ success: false, message: "Cita no encontrada." });
  }
  if (!(await estaDisponible(medicoId, fecha, hora))) {
    return NextResponse.json({ success: false, message: "Ese horario ya no está disponible." });
  }
  const ok = await reagendarCita(id, medicoId, fecha, hora);
  if (ok) {
    await auditLog({
      usuarioId: session.user.id,
      usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
      accion: "reagendar",
      tabla: "citas",
      registroId: id,
      descripcion: `Paciente ${session.user.email} reagendó cita #${id} a ${fecha} ${hora}`,
    });
    await crearNotificacion("Cita reagendada", `Paciente ${session.user.email} reagendó cita #${id}`, "info");
    return NextResponse.json({ success: true, message: "Cita reagendada correctamente." });
  }
  return NextResponse.json({ success: false, message: "Error al reagendar la cita." });
}
