import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { obtenerNoLeidas, contarNoLeidas } from "@/lib/repos/notificaciones";

export async function GET() {
  const session = await auth();
  if (!session?.user) return NextResponse.json({ success: false }, { status: 401 });
  const userId = session.user.id ?? null;
  const [data, count] = await Promise.all([
    obtenerNoLeidas(userId, 10),
    contarNoLeidas(userId),
  ]);
  return NextResponse.json({ success: true, count, data });
}
