import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { desbloquearUsuario } from "@/lib/repos/usuarios";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente" || session.user.rol === "medico") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const fd = await req.formData();
  const id = Number(fd.get("id") ?? 0);
  if (!id) return NextResponse.json({ success: false, message: "ID requerido" });
  const ok = await desbloquearUsuario(id);
  if (ok) return NextResponse.json({ success: true, message: "Usuario desbloqueado." });
  return NextResponse.json({ success: false, message: "Error al desbloquear." });
}
