import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { cambiarEstado, obtenerCitaPorId } from "@/lib/repos/citas";
import { crearNotificacion } from "@/lib/repos/notificaciones";
import { auditLog } from "@/lib/audit";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const form = await req.formData();
  const id = Number(form.get("cita_id") ?? form.get("id") ?? 0);
  const estado = Number(form.get("estado_id") ?? 0);
  const notas = String(form.get("notas") ?? "");
  if (![1, 2, 3, 4].includes(estado)) {
    return NextResponse.json({ success: false, message: "Estado inválido" });
  }
  if (!id) return NextResponse.json({ success: false, message: "ID requerido" });

  const ok = await cambiarEstado(id, estado, notas);
  if (ok) {
    await auditLog({
      usuarioId: session.user.id,
      usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
      accion: "cambiar_estado",
      tabla: "citas",
      registroId: id,
      descripcion: `Cita #${id} → estado ${estado} (AJAX)`,
    });
    await crearNotificacion(
      "Estado de cita actualizado (AJAX)",
      `Cita #${id} cambio a estado ${estado}`,
      "info"
    );
    return NextResponse.json({ success: true, message: "Estado actualizado correctamente." });
  }
  return NextResponse.json({ success: false, message: "Error al actualizar el estado." });
}
