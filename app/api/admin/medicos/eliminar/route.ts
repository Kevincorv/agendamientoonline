import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { eliminarMedico, obtenerMedicoPorId } from "@/lib/repos/medicos";
import { auditLog } from "@/lib/audit";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente" || session.user.rol === "medico") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const fd = await req.formData();
  const id = Number(fd.get("id") ?? 0);
  if (!id) return NextResponse.json({ success: false, message: "ID requerido" });
  const m = await obtenerMedicoPorId(id);
  await eliminarMedico(id);
  await auditLog({
    usuarioId: session.user.id,
    usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
    accion: "eliminar",
    tabla: "medicos",
    registroId: id,
    descripcion: `Médico desactivado: ${m ? `${m.nombre} ${m.apellido}` : "#" + id}`,
  });
  return NextResponse.json({ success: true, message: "Médico desactivado." });
}
