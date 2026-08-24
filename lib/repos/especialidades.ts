import { query, queryOne, execute } from "../db";
import type { Especialidad } from "../types";

export function obtenerEspecialidades(): Promise<Especialidad[]> {
  return query<Especialidad[]>(
    "SELECT * FROM especialidades WHERE activo = 1 ORDER BY nombre ASC"
  );
}

export function listarEspecialidadesAdmin(): Promise<Especialidad[]> {
  return query<Especialidad[]>(
    "SELECT * FROM especialidades ORDER BY activo DESC, nombre ASC"
  );
}

export function obtenerEspecialidadPorId(id: number) {
  return queryOne<Especialidad>("SELECT * FROM especialidades WHERE id = :id", { id });
}

export async function crearEspecialidad(data: {
  nombre: string;
  descripcion: string | null;
  icono: string;
}): Promise<number> {
  const { insertId } = await execute(
    `INSERT INTO especialidades (nombre, descripcion, icono, activo) VALUES (:nombre, :descripcion, :icono, 1)`,
    data
  );
  return insertId;
}

export async function actualizarEspecialidad(
  id: number,
  data: { nombre: string; descripcion: string | null; icono: string }
): Promise<boolean> {
  const { affectedRows } = await execute(
    `UPDATE especialidades
        SET nombre=:nombre, descripcion=:descripcion, icono=:icono
      WHERE id=:id`,
    { ...data, id }
  );
  return affectedRows > 0;
}

export async function eliminarEspecialidad(id: number): Promise<boolean> {
  const { affectedRows } = await execute(
    `UPDATE especialidades SET activo = 0 WHERE id = :id`,
    { id }
  );
  return affectedRows > 0;
}
