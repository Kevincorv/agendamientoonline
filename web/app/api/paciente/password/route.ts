import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { cambiarPassword } from "@/lib/repos/pacientes";
import { auditLog } from "@/lib/audit";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol !== "paciente") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const fd = await req.formData();
  const actual = String(fd.get("password_actual") ?? "");
  const nueva = String(fd.get("password_nueva") ?? "");
  const confirmar = String(fd.get("password_confirmar") ?? "");

  if (nueva.length < 6) {
    return NextResponse.json({ success: false, message: "La nueva contraseña debe tener al menos 6 caracteres." });
  }
  if (nueva !== confirmar) {
    return NextResponse.json({ success: false, message: "Las contraseñas no coinciden." });
  }
  const res = await cambiarPassword(session.user.id, actual, nueva);
  if (!res.ok) return NextResponse.json({ success: false, message: res.error ?? "Error" });
  await auditLog({
    usuarioId: session.user.id,
    usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
    accion: "cambiar_password",
    tabla: "usuarios",
    registroId: session.user.id,
    descripcion: "Paciente cambió su contraseña",
  });
  return NextResponse.json({ success: true, message: "Contraseña actualizada correctamente." });
}
