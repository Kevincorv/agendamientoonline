import { query, queryOne } from "../db";
import type { Auditoria } from "../types";

export interface AuditoriaConJoin extends Auditoria {}

export interface AuditoriaFiltros {
  usuario_id?: number | null;
  accion?: string | null;
  tabla?: string | null;
  fecha_desde?: string | null;
  fecha_hasta?: string | null;
  ip?: string | null;
}

export interface AuditoriaListado {
  datos: AuditoriaConJoin[];
  total: number;
  pagina: number;
  paginas: number;
  porPagina: number;
}

export async function listarAuditoria(
  filtros: AuditoriaFiltros = {},
  pagina = 1,
  porPagina = 25
): Promise<AuditoriaListado> {
  const where: string[] = ["1=1"];
  const params: Record<string, unknown> = { lim: porPagina, off: (pagina - 1) * porPagina };

  if (filtros.usuario_id) {
    where.push("a.usuario_id = :uid");
    params.uid = filtros.usuario_id;
  }
  if (filtros.accion) {
    where.push("a.accion = :acc");
    params.acc = filtros.accion;
  }
  if (filtros.tabla) {
    where.push("a.tabla = :tab");
    params.tab = filtros.tabla;
  }
  if (filtros.fecha_desde) {
    where.push("DATE(a.created_at) >= :fd");
    params.fd = filtros.fecha_desde;
  }
  if (filtros.fecha_hasta) {
    where.push("DATE(a.created_at) <= :fh");
    params.fh = filtros.fecha_hasta;
  }
  if (filtros.ip) {
    where.push("a.ip LIKE :ip");
    params.ip = `%${filtros.ip}%`;
  }

  const sqlWhere = where.join(" AND ");

  const totalRow = await queryOne<{ c: number }>(
    `SELECT COUNT(*) AS c FROM auditoria a WHERE ${sqlWhere}`,
    params
  );
  const total = Number(totalRow?.c ?? 0);

  const datos = await query<AuditoriaConJoin[]>(
    `SELECT a.* FROM auditoria a
     WHERE ${sqlWhere}
     ORDER BY a.created_at DESC
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

export function obtenerAuditoriaPorId(id: number) {
  return queryOne<AuditoriaConJoin>("SELECT * FROM auditoria WHERE id = :id", { id });
}

export function listarAccionesUnicas() {
  return query<{ accion: string }[]>(
    "SELECT DISTINCT accion FROM auditoria WHERE accion IS NOT NULL ORDER BY accion ASC"
  );
}

export function listarTablasUnicas() {
  return query<{ tabla: string }[]>(
    "SELECT DISTINCT tabla FROM auditoria WHERE tabla IS NOT NULL ORDER BY tabla ASC"
  );
}
