import { query, queryOne, execute } from "../db";
import type { Medico } from "../types";

export interface MedicoDetalle extends Medico {
  especialidad_nombre: string | null;
}

export function obtenerMedicos(): Promise<MedicoDetalle[]> {
  return query<MedicoDetalle[]>(
    `SELECT m.*, e.nombre AS especialidad_nombre
     FROM medicos m LEFT JOIN especialidades e ON m.especialidad_id = e.id
     WHERE m.activo = 1 ORDER BY m.apellido ASC`
  );
}

export function listarMedicosAdmin(): Promise<MedicoDetalle[]> {
  return query<MedicoDetalle[]>(
    `SELECT m.*, e.nombre AS especialidad_nombre
     FROM medicos m LEFT JOIN especialidades e ON m.especialidad_id = e.id
     ORDER BY m.activo DESC, m.apellido ASC`
  );
}

export function obtenerMedicosPorEspecialidad(espId: number): Promise<MedicoDetalle[]> {
  return query<MedicoDetalle[]>(
    `SELECT m.*, e.nombre AS especialidad_nombre
     FROM medicos m LEFT JOIN especialidades e ON m.especialidad_id = e.id
     WHERE m.especialidad_id = :esp AND m.activo = 1 AND m.disponible = 1
     ORDER BY m.apellido ASC`,
    { esp: espId }
  );
}

export function obtenerMedicoPorId(id: number) {
  return queryOne<MedicoDetalle>(
    `SELECT m.*, e.nombre AS especialidad_nombre
     FROM medicos m LEFT JOIN especialidades e ON m.especialidad_id = e.id
     WHERE m.id = :id`,
    { id }
  );
}

export function obtenerMedicoPorUsuarioId(usuarioId: number) {
  return queryOne<MedicoDetalle>(
    `SELECT m.*, e.nombre AS especialidad_nombre
     FROM medicos m LEFT JOIN especialidades e ON m.especialidad_id = e.id
     WHERE m.usuario_id = :uid AND m.activo = 1`,
    { uid: usuarioId }
  );
}

export async function crearMedico(data: {
  nombre: string;
  apellido: string;
  email: string | null;
  telefono: string | null;
  especialidad_id: number;
  matricula: string | null;
  descripcion: string | null;
}): Promise<number> {
  const { insertId } = await execute(
    `INSERT INTO medicos
       (nombre, apellido, email, telefono, especialidad_id, matricula, descripcion, disponible, activo)
     VALUES
       (:nombre, :apellido, :email, :telefono, :especialidad_id, :matricula, :descripcion, 1, 1)`,
    data
  );
  return insertId;
}

export async function actualizarMedico(
  id: number,
  data: {
    nombre: string;
    apellido: string;
    email: string | null;
    telefono: string | null;
    especialidad_id: number;
    matricula: string | null;
    descripcion: string | null;
  }
): Promise<boolean> {
  const { affectedRows } = await execute(
    `UPDATE medicos
        SET nombre=:nombre, apellido=:apellido, email=:email, telefono=:telefono,
            especialidad_id=:especialidad_id, matricula=:matricula, descripcion=:descripcion
      WHERE id=:id`,
    { ...data, id }
  );
  return affectedRows > 0;
}

export async function eliminarMedico(id: number): Promise<boolean> {
  const { affectedRows } = await execute(
    `UPDATE medicos SET activo = 0 WHERE id = :id`,
    { id }
  );
  return affectedRows > 0;
}

export async function toggleMedicoDisponible(id: number): Promise<void> {
  await execute(
    `UPDATE medicos SET disponible = 1 - disponible WHERE id = :id`,
    { id }
  );
}

export async function medicosTop(limit = 5) {
  return query<Array<{ id: number; nombre: string; apellido: string; especialidad: string | null; total: number }>>(
    `SELECT m.id, m.nombre, m.apellido, e.nombre AS especialidad, COUNT(c.id) AS total
     FROM medicos m
     LEFT JOIN especialidades e ON m.especialidad_id = e.id
     LEFT JOIN citas c ON c.medico_id = m.id
     WHERE m.activo = 1
     GROUP BY m.id, m.nombre, m.apellido, e.nombre
     ORDER BY total DESC
     LIMIT :lim`,
    { lim: limit }
  );
}

