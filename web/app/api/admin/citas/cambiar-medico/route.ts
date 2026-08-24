import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { cambiarMedico, estaDisponible } from "@/lib/repos/citas";
import { crearNotificacion } from "@/lib/repos/notificaciones";
import { auditLog } from "@/lib/audit";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const form = await req.formData();
  const citaId = Number(form.get("cita_id") ?? 0);
  const nuevoMedico = Number(form.get("medico_id") ?? 0);
  const nuevaFecha = String(form.get("fecha") ?? "");
  const nuevaHora = String(form.get("hora") ?? "");

  if (!citaId || !nuevoMedico || !nuevaFecha || !nuevaHora) {
    return NextResponse.json({ success: false, message: "Faltan parámetros." });
  }
  if (!(await estaDisponible(nuevoMedico, nuevaFecha, nuevaHora))) {
    return NextResponse.json({ success: false, message: "El horario seleccionado ya no está disponible." });
  }
  const ok = await cambiarMedico(citaId, nuevoMedico, nuevaFecha, nuevaHora);
  if (ok) {
    await auditLog({
      usuarioId: session.user.id,
      usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
      accion: "cambiar_medico",
      tabla: "citas",
      registroId: citaId,
      descripcion: `Cita #${citaId} reasignada al médico #${nuevoMedico} el ${nuevaFecha} a las ${nuevaHora}`,
    });
    await crearNotificacion(
      "Cita reasignada",
      `Cita #${citaId} fue reasignada a otro médico`,
      "warning"
    );
    return NextResponse.json({ success: true, message: "Cita reasignada correctamente." });
  }
  return NextResponse.json({ success: false, message: "Error al reasignar la cita." });
}
