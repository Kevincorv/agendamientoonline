import { NextResponse } from "next/server";
import bcrypt from "bcryptjs";
import { auth } from "@/lib/auth";
import { actualizarUsuario } from "@/lib/repos/usuarios";
import { auditLog } from "@/lib/audit";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente" || session.user.rol === "medico") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const fd = await req.formData();
  const id = Number(fd.get("id") ?? 0);
  const data: { nombre: string; email: string; rol_id: number; password?: string } = {
    nombre: String(fd.get("nombre") ?? "").trim(),
    email: String(fd.get("email") ?? "").trim().toLowerCase(),
    rol_id: Number(fd.get("rol_id") ?? 0),
  };
  const pwd = String(fd.get("password") ?? "");
  if (pwd) data.password = await bcrypt.hash(pwd, 10);
  if (!id || !data.nombre || !data.email || !data.rol_id) {
    return NextResponse.json({ success: false, message: "Faltan datos obligatorios." });
  }
  const ok = await actualizarUsuario(id, data);
  if (ok) {
    await auditLog({
      usuarioId: session.user.id,
      usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
      accion: "editar",
      tabla: "usuarios",
      registroId: id,
      descripcion: `Usuario editado: ${data.nombre} <${data.email}>`,
    });
    return NextResponse.json({ success: true, message: "Usuario actualizado correctamente." });
  }
  return NextResponse.json({ success: false, message: "Error al actualizar el usuario." });
}
