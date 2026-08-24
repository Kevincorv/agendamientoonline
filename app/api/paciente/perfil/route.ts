import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { actualizarNombre } from "@/lib/repos/pacientes";
import { auditLog } from "@/lib/audit";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol !== "paciente") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const fd = await req.formData();
  const nombre = String(fd.get("nombre") ?? "").trim();
  const apellido = String(fd.get("apellido") ?? "").trim();
  if (!nombre || !apellido) {
    return NextResponse.json({ success: false, message: "Completá nombre y apellido." });
  }
  const ok = await actualizarNombre(session.user.id, nombre, apellido);
  if (ok) {
    await auditLog({
      usuarioId: session.user.id,
      usuarioNombre: `${nombre} ${apellido}`,
      accion: "editar",
      tabla: "usuarios",
      registroId: session.user.id,
      descripcion: "Paciente actualizó su perfil",
    });
    return NextResponse.json({ success: true, message: "Perfil actualizado correctamente." });
  }
  return NextResponse.json({ success: false, message: "Error al actualizar el perfil." });
}
