import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { obtenerCitaPorId } from "@/lib/repos/citas";

export async function GET(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const { searchParams } = new URL(req.url);
  const id = Number(searchParams.get("id") ?? 0);
  if (!id) return NextResponse.json({ success: false, message: "ID requerido" });
  const cita = await obtenerCitaPorId(id);
  if (!cita) return NextResponse.json({ success: false, message: "Cita no encontrada" });
  return NextResponse.json({ success: true, cita });
}
