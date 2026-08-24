import { query, execute } from "../db";
import type { Notificacion } from "../types";

export interface NotificacionExt extends Notificacion {}

export async function crearNotificacion(
  titulo: string,
  mensaje: string,
  tipo = "info",
  usuarioId: number | null = null
): Promise<void> {
  await execute(
    `INSERT INTO notificaciones (usuario_id, titulo, mensaje, tipo) VALUES (:uid, :titulo, :mensaje, :tipo)`,
    { uid: usuarioId, titulo, mensaje, tipo }
  );
}

export async function obtenerNoLeidas(usuarioId: number | null = null, limit = 10) {
  const sql = usuarioId
    ? `SELECT * FROM notificaciones
       WHERE (usuario_id = :uid OR usuario_id IS NULL) AND leido = 0
       ORDER BY created_at DESC LIMIT :lim`
    : `SELECT * FROM notificaciones
       WHERE usuario_id IS NULL AND leido = 0
       ORDER BY created_at DESC LIMIT :lim`;
  const params: Record<string, unknown> = usuarioId
    ? { uid: usuarioId, lim: limit }
    : { lim: limit };
  return query<Notificacion[]>(sql, params);
}

export async function contarNoLeidas(usuarioId: number | null = null): Promise<number> {
  const sql = usuarioId
    ? `SELECT COUNT(*) AS c FROM notificaciones
       WHERE (usuario_id = :uid OR usuario_id IS NULL) AND leido = 0`
    : `SELECT COUNT(*) AS c FROM notificaciones
       WHERE usuario_id IS NULL AND leido = 0`;
  const r = await query<{ c: number }[]>(
    sql,
    usuarioId ? { uid: usuarioId } : {}
  );
  return Number(r[0]?.c ?? 0);
}

export async function marcarLeidas(usuarioId: number | null = null): Promise<void> {
  const sql = usuarioId
    ? `UPDATE notificaciones SET leido = 1
       WHERE (usuario_id = :uid OR usuario_id IS NULL) AND leido = 0`
    : `UPDATE notificaciones SET leido = 1
       WHERE usuario_id IS NULL AND leido = 0`;
  await execute(sql, usuarioId ? { uid: usuarioId } : {});
}

export async function marcarLeida(id: number): Promise<void> {
  await execute("UPDATE notificaciones SET leido = 1 WHERE id = :id", { id });
}
