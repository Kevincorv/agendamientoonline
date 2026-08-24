import { randomBytes } from "node:crypto";
import { query, queryOne, execute } from "../db";
import type { Cita } from "../types";
export type CitaDetalle = Cita & {
  medico_nombre?: string;
  medico_apellido?: string;
  especialidad?: string | null;
  estado?: string | null;
  estado_color?: string | null;
};

export interface CitaListado extends CitaDetalle {}

export interface ListarCitasFiltros {
  fecha?: string;
  medico_id?: number;
  estado_id?: number;
  q?: string;
  porPagina?: number;
  pagina?: number;
}

export interface ListarCitasResultado {
  datos: CitaListado[];
  total: number;
  pagina: number;
  paginas: number;
  porPagina: number;
}

export async function crearCita(data: {
  medico_id: number;
  especialidad_id: number;
  nombre_paciente: string;
  telefono: string;
  email: string;
  motivo: string;
  fecha: string;
  hora: string;
}): Promise<string> {
  const token = randomBytes(32).toString("hex");
  await execute(
    `INSERT INTO citas
       (medico_id, especialidad_id, nombre_paciente, telefono, email, motivo, fecha, hora, token_cancelacion, estado_id)
     VALUES
       (:medico_id, :especialidad_id, :nombre_paciente, :telefono, :email, :motivo, :fecha, :hora, :token, 1)`,
    { ...data, token }
  );
  return token;
}

export function obtenerCitaPorToken(token: string) {
  return queryOne<CitaDetalle>(
    `SELECT c.*,
            m.nombre AS medico_nombre, m.apellido AS medico_apellido,
            e.nombre AS especialidad,
            ec.nombre AS estado, ec.color AS estado_color
     FROM citas c
     LEFT JOIN medicos m ON c.medico_id = m.id
     LEFT JOIN especialidades e ON c.especialidad_id = e.id
     LEFT JOIN estados_citas ec ON c.estado_id = ec.id
     WHERE c.token_cancelacion = :token`,
    { token }
  );
}

export function obtenerCitaPorId(id: number) {
  return queryOne<CitaDetalle>(
    `SELECT c.*,
            m.nombre AS medico_nombre, m.apellido AS medico_apellido,
            e.nombre AS especialidad,
            ec.nombre AS estado, ec.color AS estado_color
     FROM citas c
     LEFT JOIN medicos m ON c.medico_id = m.id
     LEFT JOIN especialidades e ON c.especialidad_id = e.id
     LEFT JOIN estados_citas ec ON c.estado_id = ec.id
     WHERE c.id = :id`,
    { id }
  );
}

export async function estaDisponible(medicoId: number, fecha: string, hora: string): Promise<boolean> {
  const r = await queryOne<{ c: number }>(
    `SELECT COUNT(*) AS c FROM citas
     WHERE medico_id = :mid AND fecha = :fecha AND hora = :hora AND estado_id NOT IN (3)`,
    { mid: medicoId, fecha, hora }
  );
  return r ? r.c === 0 : true;
}

export async function cancelarPorToken(token: string): Promise<boolean> {
  const { affectedRows } = await execute(
    `UPDATE citas SET estado_id = 3
     WHERE token_cancelacion = :token AND estado_id NOT IN (3,4)`,
    { token }
  );
  return affectedRows > 0;
}

export async function cambiarEstado(id: number, estado: number, notas = ""): Promise<boolean> {
  const { affectedRows } = await execute(
    `UPDATE citas SET estado_id = :estado, notas_medico = :notas WHERE id = :id`,
    { id, estado, notas }
  );
  return affectedRows > 0;
}

export async function eliminarCita(id: number): Promise<boolean> {
  const { affectedRows } = await execute(`DELETE FROM citas WHERE id = :id`, { id });
  return affectedRows > 0;
}

export async function cambiarMedico(
  id: number,
  medicoId: number,
  fecha: string,
  hora: string
): Promise<boolean> {
  const { affectedRows } = await execute(
    `UPDATE citas
        SET medico_id = :medico, fecha = :fecha, hora = :hora, estado_id = 1
      WHERE id = :id`,
    { id, medico: medicoId, fecha, hora }
  );
  return affectedRows > 0;
}

export async function reagendarCita(
  id: number,
  medicoId: number,
  fecha: string,
  hora: string
): Promise<boolean> {
  const { affectedRows } = await execute(
    `UPDATE citas
        SET medico_id = :medico, fecha = :fecha, hora = :hora, estado_id = 1
      WHERE id = :id`,
    { id, medico: medicoId, fecha, hora }
  );
  return affectedRows > 0;
}

// ─── Listado administrativo con paginación y filtros ─────────

export async function listarCitasAdmin(filtros: ListarCitasFiltros = {}): Promise<ListarCitasResultado> {
  const porPagina = filtros.porPagina ?? 15;
  const pagina = filtros.pagina ?? 1;
  const offset = (pagina - 1) * porPagina;

  const where: string[] = ["1=1"];
  const params: Record<string, unknown> = { lim: porPagina, off: offset };

  if (filtros.fecha) {
    where.push("c.fecha = :fecha");
    params.fecha = filtros.fecha;
  }
  if (filtros.medico_id) {
    where.push("c.medico_id = :medico");
    params.medico = filtros.medico_id;
  }
  if (filtros.estado_id) {
    where.push("c.estado_id = :estado");
    params.estado = filtros.estado_id;
  }
  if (filtros.q) {
    where.push("(LOWER(c.nombre_paciente) LIKE :q OR c.telefono LIKE :q OR c.email LIKE :q)");
    params.q = `%${filtros.q.toLowerCase()}%`;
  }

  const whereSql = where.join(" AND ");

  const totalRow = await queryOne<{ c: number }>(
    `SELECT COUNT(*) AS c FROM citas c WHERE ${whereSql}`,
    params
  );
  const total = totalRow?.c ?? 0;

  const datos = await query<CitaListado[]>(
    `SELECT c.*,
            m.nombre AS medico_nombre, m.apellido AS medico_apellido,
            e.nombre AS especialidad,
            ec.nombre AS estado, ec.color AS estado_color
     FROM citas c
     LEFT JOIN medicos m ON c.medico_id = m.id
     LEFT JOIN especialidades e ON c.especialidad_id = e.id
     LEFT JOIN estados_citas ec ON c.estado_id = ec.id
     WHERE ${whereSql}
     ORDER BY c.fecha DESC, c.hora DESC
     LIMIT :lim OFFSET :off`,
    params
  );

  return {
    datos,
    total,
    pagina,
    paginas: Math.max(1, Math.ceil(total / porPagina)),
    porPagina,
  };
}

export async function obtenerCitasPorPacienteEmail(
  email: string,
  soloFuturas = false,
  limit?: number
) {
  const whereFuturas = "AND c.fecha >= CURDATE() AND c.estado_id NOT IN (3)";
  const sqlBase = `SELECT c.*,
                          m.nombre AS medico_nombre, m.apellido AS medico_apellido,
                          e.nombre AS especialidad,
                          ec.nombre AS estado, ec.color AS estado_color
                   FROM citas c
                   LEFT JOIN medicos m ON c.medico_id = m.id
                   LEFT JOIN especialidades e ON c.especialidad_id = e.id
                   LEFT JOIN estados_citas ec ON c.estado_id = ec.id
                   WHERE c.email = :email ${soloFuturas ? whereFuturas : ""}
                   ORDER BY c.fecha DESC, c.hora DESC`;
  const sql = limit ? `${sqlBase} LIMIT :lim` : sqlBase;
  const params: Record<string, unknown> = { email };
  if (limit) params.lim = limit;
  return query<CitaDetalle[]>(sql, params);
}

export async function citasDelMedico(medicoId: number, fecha?: string): Promise<CitaDetalle[]> {
  if (fecha) {
    return query<CitaDetalle[]>(
      `SELECT c.*,
              m.nombre AS medico_nombre, m.apellido AS medico_apellido,
              e.nombre AS especialidad,
              ec.nombre AS estado, ec.color AS estado_color
       FROM citas c
       LEFT JOIN medicos m ON c.medico_id = m.id
       LEFT JOIN especialidades e ON c.especialidad_id = e.id
       LEFT JOIN estados_citas ec ON c.estado_id = ec.id
       WHERE c.medico_id = :mid AND c.fecha = :fecha
       ORDER BY c.hora ASC`,
      { mid: medicoId, fecha }
    );
  }
  return query<CitaDetalle[]>(
    `SELECT c.*,
            m.nombre AS medico_nombre, m.apellido AS medico_apellido,
            e.nombre AS especialidad,
            ec.nombre AS estado, ec.color AS estado_color
     FROM citas c
     LEFT JOIN medicos m ON c.medico_id = m.id
     LEFT JOIN especialidades e ON c.especialidad_id = e.id
     LEFT JOIN estados_citas ec ON c.estado_id = ec.id
     WHERE c.medico_id = :mid
     ORDER BY c.fecha ASC, c.hora ASC`,
    { mid: medicoId }
  );
}

// ─── Dashboard ────────────────────────────────────────────────

export interface DashboardStats {
  total: number;
  pendientes: number;
  confirmadas: number;
  canceladas: number;
  atendidas: number;
  hoy: number;
  esta_semana: number;
  este_mes: number;
}

export async function estadisticasDashboard(): Promise<DashboardStats> {
  const row = await queryOne<{
    total: number; pendientes: number; confirmadas: number;
    canceladas: number; atendidas: number;
    hoy: number; esta_semana: number; este_mes: number;
  }>(
    `SELECT
       COUNT(*) AS total,
       SUM(estado_id = 1) AS pendientes,
       SUM(estado_id = 2) AS confirmadas,
       SUM(estado_id = 3) AS canceladas,
       SUM(estado_id = 4) AS atendidas,
       SUM(fecha = CURDATE()) AS hoy,
       SUM(YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)) AS esta_semana,
       SUM(YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE())) AS este_mes
     FROM citas`
  );
  return {
    total: Number(row?.total ?? 0),
    pendientes: Number(row?.pendientes ?? 0),
    confirmadas: Number(row?.confirmadas ?? 0),
    canceladas: Number(row?.canceladas ?? 0),
    atendidas: Number(row?.atendidas ?? 0),
    hoy: Number(row?.hoy ?? 0),
    esta_semana: Number(row?.esta_semana ?? 0),
    este_mes: Number(row?.este_mes ?? 0),
  };
}

export async function proximasCitas(limit = 8) {
  return query<CitaDetalle[]>(
    `SELECT c.*,
            m.nombre AS medico_nombre, m.apellido AS medico_apellido,
            e.nombre AS especialidad,
            ec.nombre AS estado, ec.color AS estado_color
     FROM citas c
     LEFT JOIN medicos m ON c.medico_id = m.id
     LEFT JOIN especialidades e ON c.especialidad_id = e.id
     LEFT JOIN estados_citas ec ON c.estado_id = ec.id
     WHERE c.fecha >= CURDATE() AND c.estado_id NOT IN (3)
     ORDER BY c.fecha ASC, c.hora ASC
     LIMIT :lim`,
    { lim: limit }
  );
}

export async function contarPacientesUnicos(): Promise<number> {
  const r = await queryOne<{ c: number }>(
    `SELECT COUNT(DISTINCT email) AS c FROM citas WHERE email IS NOT NULL AND email <> ''`
  );
  return Number(r?.c ?? 0);
}

export async function tasaCancelaciones(): Promise<number> {
  const r = await queryOne<{ total: number; canc: number }>(
    `SELECT COUNT(*) AS total,
            SUM(estado_id = 3) AS canc
     FROM citas`
  );
  if (!r || !r.total) return 0;
  return Math.round((Number(r.canc ?? 0) / Number(r.total)) * 100);
}

export async function citasPorDiaSemana(): Promise<number[]> {
  // Devuelve un array con el conteo de citas de los últimos 7 días
  // ordenados de [hace 6 días, ..., hoy]
  const rows = await query<Array<{ dia: string; total: number }>>(
    `SELECT DATE(fecha) AS dia, COUNT(*) AS total
     FROM citas
     WHERE fecha BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND CURDATE()
     GROUP BY DATE(fecha)
     ORDER BY dia ASC`
  );
  const map = new Map(rows.map((r) => [String(r.dia), Number(r.total)]));
  const out: number[] = [];
  const today = new Date();
  for (let i = 6; i >= 0; i--) {
    const d = new Date(today);
    d.setDate(d.getDate() - i);
    const key = d.toISOString().slice(0, 10);
    out.push(map.get(key) ?? 0);
  }
  return out;
}

// ─── Reportes ─────────────────────────────────────────────────

export interface ReportesFiltros {
  desde?: string;
  hasta?: string;
  medico_id?: number;
  especialidad_id?: number;
  estado_id?: number;
  q?: string;
}

function whereReportes(f: ReportesFiltros): { sql: string; params: Record<string, unknown> } {
  const where: string[] = ["1=1"];
  const params: Record<string, unknown> = {};
  if (f.desde) {
    where.push("c.fecha >= :desde");
    params.desde = f.desde;
  }
  if (f.hasta) {
    where.push("c.fecha <= :hasta");
    params.hasta = f.hasta;
  }
  if (f.medico_id) {
    where.push("c.medico_id = :medico");
    params.medico = f.medico_id;
  }
  if (f.especialidad_id) {
    where.push("c.especialidad_id = :esp");
    params.esp = f.especialidad_id;
  }
  if (f.estado_id) {
    where.push("c.estado_id = :estado");
    params.estado = f.estado_id;
  }
  if (f.q) {
    where.push("(LOWER(c.nombre_paciente) LIKE :q OR c.telefono LIKE :q OR c.email LIKE :q)");
    params.q = `%${f.q.toLowerCase()}%`;
  }
  return { sql: where.join(" AND "), params };
}

export async function reportesStats(f: ReportesFiltros) {
  const { sql, params } = whereReportes(f);
  return queryOne<{
    total: number; pendientes: number; confirmadas: number; canceladas: number; atendidas: number;
  }>(
    `SELECT COUNT(*) AS total,
            SUM(c.estado_id = 1) AS pendientes,
            SUM(c.estado_id = 2) AS confirmadas,
            SUM(c.estado_id = 3) AS canceladas,
            SUM(c.estado_id = 4) AS atendidas
     FROM citas c WHERE ${sql}`,
    params
  );
}

export async function reportesPorMedico(f: ReportesFiltros) {
  const { sql, params } = whereReportes(f);
  return query<Array<{ id: number; nombre: string; apellido: string; especialidad: string | null; total: number }>>(
    `SELECT m.id, m.nombre, m.apellido, e.nombre AS especialidad, COUNT(c.id) AS total
     FROM medicos m
     LEFT JOIN especialidades e ON m.especialidad_id = e.id
     LEFT JOIN citas c ON c.medico_id = m.id AND ${sql.replace(/c\./g, "c.")}
     WHERE m.activo = 1
     GROUP BY m.id, m.nombre, m.apellido, e.nombre
     ORDER BY total DESC`,
    params
  );
}

export async function reportesPorEspecialidad(f: ReportesFiltros) {
  const { sql, params } = whereReportes(f);
  return query<Array<{ id: number; nombre: string; total: number }>>(
    `SELECT e.id, e.nombre, COUNT(c.id) AS total
     FROM especialidades e
     LEFT JOIN citas c ON c.especialidad_id = e.id AND ${sql}
     WHERE e.activo = 1
     GROUP BY e.id, e.nombre
     ORDER BY total DESC`,
    params
  );
}

export async function reportesCitas(f: ReportesFiltros, pagina = 1, porPagina = 20) {
  const { sql, params } = whereReportes(f);
  const offset = (pagina - 1) * porPagina;
  const totalRow = await queryOne<{ c: number }>(
    `SELECT COUNT(*) AS c FROM citas c WHERE ${sql}`, params
  );
  const datos = await query<CitaDetalle[]>(
    `SELECT c.*,
            m.nombre AS medico_nombre, m.apellido AS medico_apellido,
            e.nombre AS especialidad,
            ec.nombre AS estado, ec.color AS estado_color
     FROM citas c
     LEFT JOIN medicos m ON c.medico_id = m.id
     LEFT JOIN especialidades e ON c.especialidad_id = e.id
     LEFT JOIN estados_citas ec ON c.estado_id = ec.id
     WHERE ${sql}
     ORDER BY c.fecha DESC, c.hora DESC
     LIMIT :lim OFFSET :off`,
    { ...params, lim: porPagina, off: offset }
  );
  return {
    datos,
    total: Number(totalRow?.c ?? 0),
    pagina,
    paginas: Math.max(1, Math.ceil(Number(totalRow?.c ?? 0) / porPagina)),
    porPagina,
  };
}

export async function exportarCitasCSV(f: ReportesFiltros): Promise<string> {
  const { sql, params } = whereReportes(f);
  const rows = await query<CitaDetalle[]>(
    `SELECT c.*,
            m.nombre AS medico_nombre, m.apellido AS medico_apellido,
            e.nombre AS especialidad,
            ec.nombre AS estado
     FROM citas c
     LEFT JOIN medicos m ON c.medico_id = m.id
     LEFT JOIN especialidades e ON c.especialidad_id = e.id
     LEFT JOIN estados_citas ec ON c.estado_id = ec.id
     WHERE ${sql}
     ORDER BY c.fecha DESC, c.hora DESC`,
    params
  );
  const esc = (v: unknown) => `"${String(v ?? "").replace(/"/g, '""')}"`;
  const header = ["Paciente", "Teléfono", "Email", "Médico", "Especialidad", "Fecha", "Hora", "Estado", "Motivo"].join(",");
  const body = rows
    .map((r) =>
      [
        r.nombre_paciente,
        r.telefono,
        r.email,
        `Dr. ${r.medico_nombre ?? ""} ${r.medico_apellido ?? ""}`,
        r.especialidad,
        r.fecha,
        String(r.hora ?? "").slice(0, 5),
        r.estado,
        r.motivo,
      ]
        .map(esc)
        .join(",")
    )
    .join("\n");
  return `${header}\n${body}`;
}
