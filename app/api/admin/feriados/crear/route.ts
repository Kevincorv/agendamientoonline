import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { crearFeriado } from "@/lib/repos/feriados";
import { crearNotificacion } from "@/lib/repos/notificaciones";
import { auditLog } from "@/lib/audit";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente" || session.user.rol === "medico") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const fd = await req.formData();
  const fecha = String(fd.get("fecha") ?? "").trim();
  const motivo = String(fd.get("motivo") ?? "").trim();
  if (!fecha || !motivo) {
    return NextResponse.json({ success: false, message: "Completá todos los campos." });
  }
  const ok = await crearFeriado(fecha, motivo);
  if (ok) {
    await auditLog({
      usuarioId: session.user.id,
      usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
      accion: "crear",
      tabla: "feriados",
      descripcion: `Feriado creado: ${fecha} - ${motivo}`,
    });
    await crearNotificacion("Nuevo feriado registrado", `${motivo} - ${fecha}`, "info");
    return NextResponse.json({ success: true, message: "Feriado agregado correctamente." });
  }
  return NextResponse.json({ success: false, message: "Error al agregar el feriado (puede que ya exista)." });
}
