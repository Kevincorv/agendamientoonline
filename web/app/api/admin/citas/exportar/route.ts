import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { exportarCitasCSV } from "@/lib/repos/citas";

export async function GET(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente") {
    return NextResponse.json({ error: "No autorizado" }, { status: 401 });
  }
  const { searchParams } = new URL(req.url);
  const filtros = {
    fecha: searchParams.get("fecha") ?? undefined,
    medico_id: Number(searchParams.get("medico_id") ?? 0) || undefined,
    estado_id: Number(searchParams.get("estado_id") ?? 0) || undefined,
    q: searchParams.get("q") ?? undefined,
  };
  const csv = await exportarCitasCSV(filtros);
  const fecha = new Date().toISOString().slice(0, 10);
  return new NextResponse("\uFEFF" + csv, {
    status: 200,
    headers: {
      "Content-Type": "text/csv; charset=utf-8",
      "Content-Disposition": `attachment; filename="citas_${fecha}.csv"`,
    },
  });
}
