import { NextResponse, type NextRequest } from "next/server";
import { getToken } from "next-auth/jwt";

// Protege /admin/*, /medico/*, /paciente/* (igual que AuthMiddleware PHP)
export async function middleware(req: NextRequest) {
  const token = await getToken({ req, secret: process.env.AUTH_SECRET });
  const path = req.nextUrl.pathname;

  // Páginas de login/registro públicas
  if (
    path === "/admin/login" ||
    path === "/paciente/login" ||
    path === "/paciente/registro"
  ) {
    return NextResponse.next();
  }

  const isStaffArea = path.startsWith("/admin");
  const isMedicoArea = path.startsWith("/medico");
  const isPacienteArea = path.startsWith("/paciente");
  const authed = !!token;
  const rol = (token?.rol as string | undefined) ?? "";

  if ((isStaffArea || isMedicoArea) && !authed) {
    return NextResponse.redirect(new URL("/admin/login", req.url));
  }
  if (isPacienteArea && !authed) {
    return NextResponse.redirect(new URL("/paciente/login", req.url));
  }
  if (authed) {
    if (isPacienteArea && rol !== "paciente") {
      return NextResponse.redirect(new URL("/admin/dashboard", req.url));
    }
    if ((isStaffArea || isMedicoArea) && rol === "paciente") {
      return NextResponse.redirect(new URL("/paciente/dashboard", req.url));
    }
  }
  return NextResponse.next();
}

export const config = {
  matcher: ["/admin/:path*", "/medico/:path*", "/paciente/:path*"],
};
