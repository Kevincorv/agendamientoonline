import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { toggleFeriado } from "@/lib/repos/feriados";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente" || session.user.rol === "medico") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const fd = await req.formData();
  const id = Number(fd.get("id") ?? 0);
  if (!id) return NextResponse.json({ success: false, message: "ID requerido" });
  await toggleFeriado(id);
  return NextResponse.json({ success: true, message: "Estado actualizado." });
}
