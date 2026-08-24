import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { marcarLeida } from "@/lib/repos/notificaciones";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user) return NextResponse.json({ success: false }, { status: 401 });
  const formData = await req.formData();
  const id = Number(formData.get("id") ?? 0);
  if (id) await marcarLeida(id);
  return NextResponse.json({ success: true });
}
