import { query, queryOne, execute } from "../db";
import type { Feriado } from "../types";

export function listarFeriados(includeInactivos = false): Promise<Feriado[]> {
  const sql = includeInactivos
    ? "SELECT * FROM feriados ORDER BY activo DESC, fecha ASC"
    : "SELECT * FROM feriados WHERE activo = 1 ORDER BY fecha ASC";
  return query<Feriado[]>(sql);
}

export function obtenerFeriadoPorId(id: number) {
  return queryOne<Feriado>("SELECT * FROM feriados WHERE id = :id", { id });
}

export async function crearFeriado(fecha: string, motivo: string): Promise<boolean> {
  try {
    await execute(
      "INSERT INTO feriados (fecha, motivo, activo) VALUES (:fecha, :motivo, 1)",
      { fecha, motivo }
    );
    return true;
  } catch {
    return false;
  }
}

export async function eliminarFeriado(id: number): Promise<boolean> {
  const { affectedRows } = await execute("DELETE FROM feriados WHERE id = :id", { id });
  return affectedRows > 0;
}

export async function toggleFeriado(id: number): Promise<void> {
  await execute("UPDATE feriados SET activo = 1 - activo WHERE id = :id", { id });
}
