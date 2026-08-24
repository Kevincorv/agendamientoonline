import { NextResponse } from "next/server";
import bcrypt from "bcryptjs";
import { auth } from "@/lib/auth";
import { crearUsuario, obtenerUsuarioPorEmail } from "@/lib/repos/usuarios";
import { auditLog } from "@/lib/audit";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente" || session.user.rol === "medico") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const fd = await req.formData();
  const data = {
    nombre: String(fd.get("nombre") ?? "").trim(),
    apellido: String(fd.get("apellido") ?? "").trim(),
    email: String(fd.get("email") ?? "").trim().toLowerCase(),
    password: String(fd.get("password") ?? ""),
    rol_id: Number(fd.get("rol_id") ?? 0),
  };
  if (!data.nombre || !data.apellido || !data.email || data.password.length < 6 || !data.rol_id) {
    return NextResponse.json({ success: false, message: "Completá todos los campos. Contraseña mínimo 6." });
  }
  const exists = await obtenerUsuarioPorEmail(data.email);
  if (exists) return NextResponse.json({ success: false, message: "Email ya registrado." });
  const hash = await bcrypt.hash(data.password, 10);
  const id = await crearUsuario({ ...data, password: hash });
  await auditLog({
    usuarioId: session.user.id,
    usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
    accion: "crear",
    tabla: "usuarios",
    registroId: id,
    descripcion: `Usuario creado: ${data.nombre} ${data.apellido} (${data.email})`,
  });
  return NextResponse.json({ success: true, message: "Usuario creado correctamente." });
}
