import { query, queryOne, execute } from "../db";
import type { Horario } from "../types";
import { todayInTz, nowTimeInTz, diaSemana } from "../time";

export interface Slot {
  hora: string | null;
  disponible: boolean;
  mensaje?: string;
}

export async function obtenerHorariosPorMedico(medicoId: number): Promise<Horario[]> {
  return query<Horario[]>(
    "SELECT * FROM horarios WHERE medico_id = :id ORDER BY dia_semana, hora_inicio",
    { id: medicoId }
  );
}

// Port fiel de Horario::generarSlots (PHP).
export async function generarSlots(medicoId: number, fecha: string): Promise<Slot[]> {
  const medico = await queryOne<{ disponible: number }>(
    "SELECT disponible FROM medicos WHERE id = :id",
    { id: medicoId }
  );
  if (!medico || medico.disponible === 0) {
    return [{ hora: null, disponible: false, mensaje: "El médico no está disponible por el momento." }];
  }

  const fer = await queryOne<{ c: number }>(
    "SELECT COUNT(*) AS c FROM feriados WHERE fecha = :fecha AND activo = 1",
    { fecha }
  );
  if (fer && fer.c > 0) {
    return [{ hora: null, disponible: false, mensaje: "Fecha feriada — no hay atención." }];
  }

  const bloq = await queryOne<{ c: number }>(
    "SELECT COUNT(*) AS c FROM bloqueos_medico WHERE medico_id = :id AND fecha = :fecha",
    { id: medicoId, fecha }
  );
  if (bloq && bloq.c > 0) {
    return [{ hora: null, disponible: false, mensaje: "El médico no atiende esta fecha." }];
  }

  const dia = diaSemana(fecha);
  const horarios = await query<Horario[]>(
    "SELECT * FROM horarios WHERE medico_id = :id AND dia_semana = :dia AND activo = 1",
    { id: medicoId, dia }
  );

  const today = todayInTz();
  const now = nowTimeInTz();

  const ocup = await query<{ hora: string }[]>(
    "SELECT hora FROM citas WHERE medico_id = :mid AND fecha = :fecha AND estado_id NOT IN (3)",
    { mid: medicoId, fecha }
  );
  const ocupados = new Set(ocup.map((r) => String(r.hora).slice(0, 5)));

  const slots: Slot[] = [];
  const vistos = new Set<string>();

  for (const h of horarios) {
    const [hi, mi] = String(h.hora_inicio).split(":").map(Number);
    const [hf, mf] = String(h.hora_fin).split(":").map(Number);
    const inicio = hi * 60 + mi;
    const fin = hf * 60 + mf;
    const intervalo = h.intervalo_minutos && +h.intervalo_minutos > 0 ? +h.intervalo_minutos : 30;

    if (!inicio || !fin || fin <= inicio) continue;

    for (let t = inicio; t < fin; t += intervalo) {
      const hh = Math.floor(t / 60);
      const mm = t % 60;
      const horaSlot = `${String(hh).padStart(2, "0")}:${String(mm).padStart(2, "0")}`;

      if (vistos.has(horaSlot)) continue;
      vistos.add(horaSlot);

      if (fecha === today && horaSlot <= now) continue;

      slots.push({ hora: horaSlot, disponible: !ocupados.has(horaSlot) });
    }
  }

  return slots;
}

export async function crearHorario(data: {
  medico_id: number;
  dia_semana: number;
  hora_inicio: string;
  hora_fin: string;
  duracion: number;
  intervalo_minutos: number;
}): Promise<number> {
  const { insertId } = await execute(
    `INSERT INTO horarios (medico_id, dia_semana, hora_inicio, hora_fin, duracion, intervalo_minutos, activo)
     VALUES (:medico_id, :dia_semana, :hora_inicio, :hora_fin, :duracion, :intervalo_minutos, 1)`,
    data
  );
  return insertId;
}

export async function eliminarHorario(id: number): Promise<boolean> {
  const { affectedRows } = await execute("DELETE FROM horarios WHERE id = :id", { id });
  return affectedRows > 0;
}

export async function obtenerBloqueosPorMedico(medicoId: number) {
  return query(
    "SELECT * FROM bloqueos_medico WHERE medico_id = :id ORDER BY fecha DESC",
    { id: medicoId }
  );
}

export async function obtenerTodosBloqueos() {
  return query(
    `SELECT b.*, m.nombre AS medico_nombre, m.apellido AS medico_apellido
     FROM bloqueos_medico b
     LEFT JOIN medicos m ON b.medico_id = m.id
     ORDER BY b.fecha DESC`
  );
}

export async function crearBloqueo(medicoId: number, fecha: string, motivo: string | null): Promise<boolean> {
  try {
    await execute(
      `INSERT INTO bloqueos_medico (medico_id, fecha, motivo) VALUES (:medico_id, :fecha, :motivo)`,
      { medico_id: medicoId, fecha, motivo }
    );
    return true;
  } catch {
    return false;
  }
}

export async function eliminarBloqueo(id: number): Promise<boolean> {
  const { affectedRows } = await execute("DELETE FROM bloqueos_medico WHERE id = :id", { id });
  return affectedRows > 0;
}
