import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { cambiarEstado, obtenerCitaPorId } from "@/lib/repos/citas";
import { auditLog } from "@/lib/audit";
import { crearNotificacion } from "@/lib/repos/notificaciones";
import { obtenerMedicoPorUsuarioId } from "@/lib/repos/medicos";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol !== "medico") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const med = await obtenerMedicoPorUsuarioId(session.user.id);
  if (!med) return NextResponse.json({ success: false, message: "Médico no encontrado" }, { status: 404 });

  const fd = await req.formData();
  const id = Number(fd.get("cita_id") ?? 0);
  const estado = Number(fd.get("estado_id") ?? 0);
  const notas = String(fd.get("notas") ?? "");
  if (![1, 2, 3, 4].includes(estado) || !id) {
    return NextResponse.json({ success: false, message: "Parámetros inválidos" });
  }

  // Verificar que la cita pertenezca al médico
  const cita = await obtenerCitaPorId(id);
  if (!cita || cita.medico_id !== med.id) {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 403 });
  }

  const ok = await cambiarEstado(id, estado, notas);
  if (ok) {
    await auditLog({
      usuarioId: session.user.id,
      usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
      accion: "cambiar_estado",
      tabla: "citas",
      registroId: id,
      descripcion: `Médico cambió cita #${id} → estado ${estado}`,
    });
    await crearNotificacion("Estado de cita actualizado", `Cita #${id} cambio a estado ${estado}`, "info");
    return NextResponse.json({ success: true, message: "Estado actualizado." });
  }
  return NextResponse.json({ success: false, message: "Error al actualizar." });
}
