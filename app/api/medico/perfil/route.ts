import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { actualizarMedico, obtenerMedicoPorUsuarioId } from "@/lib/repos/medicos";
import { auditLog } from "@/lib/audit";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol !== "medico") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const med = await obtenerMedicoPorUsuarioId(session.user.id);
  if (!med) return NextResponse.json({ success: false, message: "Médico no encontrado" }, { status: 404 });

  const fd = await req.formData();
  const data = {
    nombre: String(fd.get("nombre") ?? "").trim(),
    apellido: String(fd.get("apellido") ?? "").trim(),
    email: String(fd.get("email") ?? "").trim() || null,
    telefono: String(fd.get("telefono") ?? "").trim() || null,
    matricula: String(fd.get("matricula") ?? "").trim() || null,
    descripcion: String(fd.get("descripcion") ?? "").trim() || null,
    especialidad_id: med.especialidad_id,
  };
  if (!data.nombre || !data.apellido) {
    return NextResponse.json({ success: false, message: "Completá nombre y apellido." });
  }
  const ok = await actualizarMedico(med.id, data);
  if (ok) {
    await auditLog({
      usuarioId: session.user.id,
      usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
      accion: "editar",
      tabla: "medicos",
      registroId: med.id,
      descripcion: `Médico editó su perfil: ${data.nombre} ${data.apellido}`,
    });
    return NextResponse.json({ success: true, message: "Perfil actualizado correctamente." });
  }
  return NextResponse.json({ success: false, message: "Error al actualizar el perfil." });
}
