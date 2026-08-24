import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { crearBloqueo } from "@/lib/repos/horarios";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente" || session.user.rol === "medico") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const fd = await req.formData();
  const medicoId = Number(fd.get("medico_id") ?? 0);
  const fecha = String(fd.get("fecha") ?? "");
  const motivo = String(fd.get("motivo") ?? "") || null;
  if (!medicoId || !fecha) {
    return NextResponse.json({ success: false, message: "Completa todos los campos." });
  }
  const ok = await crearBloqueo(medicoId, fecha, motivo);
  if (ok) return NextResponse.json({ success: true, message: "Fecha bloqueada para el médico." });
  return NextResponse.json({ success: false, message: "Error al bloquear (puede que ya exista)." });
}
