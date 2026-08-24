import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { obtenerMedicoPorUsuarioId, toggleMedicoDisponible } from "@/lib/repos/medicos";

export async function POST() {
  const session = await auth();
  if (!session?.user || session.user.rol !== "medico") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const med = await obtenerMedicoPorUsuarioId(session.user.id);
  if (!med) return NextResponse.json({ success: false, message: "Médico no encontrado" }, { status: 404 });
  await toggleMedicoDisponible(med.id);
  return NextResponse.json({ success: true, message: "Disponibilidad actualizada." });
}
