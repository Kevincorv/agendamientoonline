import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { marcarLeidas } from "@/lib/repos/notificaciones";

export async function POST() {
  const session = await auth();
  if (!session?.user) return NextResponse.json({ success: false }, { status: 401 });
  await marcarLeidas(session.user.id);
  return NextResponse.json({ success: true });
}
