import { NextResponse } from "next/server";
import { auth } from "@/lib/auth";
import { crearHorario } from "@/lib/repos/horarios";
import { auditLog } from "@/lib/audit";

export async function POST(req: Request) {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente" || session.user.rol === "medico") {
    return NextResponse.json({ success: false, message: "No autorizado" }, { status: 401 });
  }
  const fd = await req.formData();
  const medicoId = Number(fd.get("medico_id") ?? 0);
  const dia = Number(fd.get("dia_semana") ?? -1);
  const inicio = String(fd.get("hora_inicio") ?? "");
  const fin = String(fd.get("hora_fin") ?? "");
  const duracion = Number(fd.get("duracion") ?? 30);
  if (!medicoId || dia < 0 || dia > 6 || !inicio || !fin) {
    return NextResponse.json({ success: false, message: "Completá todos los campos obligatorios." });
  }
  if (inicio >= fin) {
    return NextResponse.json({ success: false, message: "La hora de inicio debe ser menor a la hora de fin." });
  }
  try {
    await crearHorario({
      medico_id: medicoId,
      dia_semana: dia,
      hora_inicio: inicio,
      hora_fin: fin,
      duracion,
      intervalo_minutos: duracion,
    });
    await auditLog({
      usuarioId: session.user.id,
      usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
      accion: "crear",
      tabla: "horarios",
      descripcion: `Bloque horario creado para médico #${medicoId}, día ${dia}`,
    });
    return NextResponse.json({ success: true, message: "Bloque horario agregado correctamente." });
  } catch {
    return NextResponse.json({ success: false, message: "Error al crear el bloque horario." });
  }
}
