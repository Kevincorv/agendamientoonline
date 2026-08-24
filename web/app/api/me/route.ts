import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";

export async function GET() {
  const session = await auth();
  if (!session?.user) return NextResponse.json({ authenticated: false }, { status: 401 });
  return NextResponse.json({
    authenticated: true,
    rol: session.user.rol,
    id: session.user.id,
    nombre: session.user.nombre,
    apellido: session.user.apellido,
    email: session.user.email,
  });
}
