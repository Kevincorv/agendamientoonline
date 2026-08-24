import { NextResponse } from "next/server";
import { generarSlots } from "@/lib/repos/horarios";

export async function GET(req: Request) {
  const { searchParams } = new URL(req.url);
  const medicoId = Number(searchParams.get("medico_id") ?? 0);
  const fecha = String(searchParams.get("fecha") ?? "");
  if (!medicoId || !fecha) {
    return NextResponse.json({ success: false, message: "Faltan parámetros." });
  }
  const slots = await generarSlots(medicoId, fecha);
  return NextResponse.json({ success: true, slots });
}
