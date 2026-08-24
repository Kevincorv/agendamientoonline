import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { crearMedico } from "@/lib/repos/medicos";
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
    email: String(fd.get("email") ?? "").trim() || null,
    telefono: String(fd.get("telefono") ?? "").trim() || null,
    especialidad_id: Number(fd.get("especialidad_id") ?? 0),
    matricula: String(fd.get("matricula") ?? "").trim() || null,
    descripcion: String(fd.get("descripcion") ?? "").trim() || null,
  };
  if (!data.nombre || !data.apellido || !data.especialidad_id) {
    return NextResponse.json({ success: false, message: "Faltan datos obligatorios." });
  }
  const id = await crearMedico(data);
  await auditLog({
    usuarioId: session.user.id,
    usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
    accion: "crear",
    tabla: "medicos",
    registroId: id,
    descripcion: `Médico creado: ${data.nombre} ${data.apellido}`,
  });
  return NextResponse.json({ success: true, message: "Médico creado correctamente." });
}
