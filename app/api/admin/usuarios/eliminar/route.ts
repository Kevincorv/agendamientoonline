import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { eliminarUsuario } from "@/lib/repos/usuarios";
import { auditLog } from "@/lib/audit";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente" || session.user.rol === "medico") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const fd = await req.formData();
  const id = Number(fd.get("id") ?? 0);
  if (!id) return NextResponse.json({ success: false, message: "ID requerido" });
  if (id === session.user.id) {
    return NextResponse.json({ success: false, message: "No podés eliminar tu propio usuario." });
  }
  const ok = await eliminarUsuario(id);
  if (ok) {
    await auditLog({
      usuarioId: session.user.id,
      usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
      accion: "eliminar",
      tabla: "usuarios",
      registroId: id,
      descripcion: `Usuario #${id} eliminado`,
    });
    return NextResponse.json({ success: true, message: "Usuario eliminado correctamente." });
  }
  return NextResponse.json({ success: false, message: "Error al eliminar el usuario." });
}
