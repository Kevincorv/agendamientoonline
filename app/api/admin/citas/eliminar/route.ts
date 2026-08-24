import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { eliminarCita } from "@/lib/repos/citas";
import { auditLog } from "@/lib/audit";
import { crearNotificacion } from "@/lib/repos/notificaciones";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const form = await req.formData();
  const id = Number(form.get("id") ?? 0);
  if (!id) return NextResponse.json({ success: false, message: "ID requerido" });

  const ok = await eliminarCita(id);
  if (ok) {
    await auditLog({
      usuarioId: session.user.id,
      usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
      accion: "eliminar",
      tabla: "citas",
      registroId: id,
      descripcion: `Cita #${id} eliminada`,
    });
    await crearNotificacion("Cita eliminada", `Cita #${id} fue eliminada`, "danger");
    return NextResponse.json({ success: true, message: "Cita eliminada correctamente." });
  }
  return NextResponse.json({ success: false, message: "No se pudo eliminar la cita." });
}
