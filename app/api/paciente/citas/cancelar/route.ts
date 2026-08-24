import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { cambiarEstado, obtenerCitaPorId } from "@/lib/repos/citas";
import { auditLog } from "@/lib/audit";
import { crearNotificacion } from "@/lib/repos/notificaciones";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol !== "paciente") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const fd = await req.formData();
  const id = Number(fd.get("id") ?? fd.get("cita_id") ?? 0);
  if (!id) return NextResponse.json({ success: false, message: "ID requerido" });

  const cita = await obtenerCitaPorId(id);
  if (!cita || cita.email !== session.user.email) {
    return NextResponse.json({ success: false, message: "Cita no encontrada" });
  }

  const ok = await cambiarEstado(id, 3, "Cancelada por el paciente");
  if (ok) {
    await auditLog({
      usuarioId: session.user.id,
      usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
      accion: "cancelar",
      tabla: "citas",
      registroId: id,
      descripcion: `Paciente ${session.user.email} canceló cita #${id}`,
    });
    await crearNotificacion("Cita cancelada", `Paciente ${session.user.email} canceló cita #${id}`, "warning");
    return NextResponse.json({ success: true, message: "Cita cancelada correctamente." });
  }
  return NextResponse.json({ success: false, message: "No se pudo cancelar la cita." });
}
