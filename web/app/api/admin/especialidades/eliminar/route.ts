import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { eliminarEspecialidad, obtenerEspecialidadPorId } from "@/lib/repos/especialidades";
import { auditLog } from "@/lib/audit";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente" || session.user.rol === "medico") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const form = await req.formData();
  const id = Number(form.get("id") ?? 0);
  if (!id) return NextResponse.json({ success: false, message: "ID requerido" });
  const esp = await obtenerEspecialidadPorId(id);
  await eliminarEspecialidad(id);
  await auditLog({
    usuarioId: session.user.id,
    usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
    accion: "eliminar",
    tabla: "especialidades",
    registroId: id,
    descripcion: `Especialidad desactivada: ${esp?.nombre ?? "#" + id}`,
  });
  return NextResponse.json({ success: true, message: "Especialidad desactivada." });
}
