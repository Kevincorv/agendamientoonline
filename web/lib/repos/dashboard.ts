import { query } from "../db";

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

export async function especialidadesMasSolicitadas(limit = 5) {
  return query<Array<{ id: number; nombre: string; total: number }>>(
    `SELECT e.id, e.nombre, COUNT(c.id) AS total
     FROM especialidades e
     LEFT JOIN citas c ON c.especialidad_id = e.id
     WHERE e.activo = 1
     GROUP BY e.id, e.nombre
     ORDER BY total DESC
     LIMIT :lim`,
    { lim: limit }
  );
}
