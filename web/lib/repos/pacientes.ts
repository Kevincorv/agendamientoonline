import { query, queryOne, execute } from "../db";
import { auth } from "../auth";
import { queryOne as _q } from "../db";
import { auditLog } from "../audit";
import bcrypt from "bcryptjs";

export interface Paciente {
  id: number;
  nombre: string;
  apellido: string;
  email: string;
  telefono?: string | null;
  rol_id: number;
  activo: number;
  creado_en: string;
  last_login?: string | null;
}

export async function obtenerCitasPaciente(email: string, soloFuturas = false, limit?: number) {
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
  return query<Array<Record<string, unknown>>>(sql, params);
}

export async function cambiarPassword(
  id: number,
  actual: string,
  nueva: string
): Promise<{ ok: boolean; error?: string }> {
  const u = await queryOne<{ password: string }>(
    "SELECT password FROM usuarios WHERE id = :id",
    { id }
  );
  if (!u) return { ok: false, error: "Usuario no encontrado" };
  const ok = await bcrypt.compare(actual, u.password);
  if (!ok) return { ok: false, error: "Contraseña actual incorrecta" };
  const hash = await bcrypt.hash(nueva, 10);
  await execute("UPDATE usuarios SET password = :password WHERE id = :id", {
    id,
    password: hash,
  });
  return { ok: true };
}

export async function actualizarNombre(id: number, nombre: string, apellido: string) {
  const { affectedRows } = await execute(
    "UPDATE usuarios SET nombre = :nombre, apellido = :apellido WHERE id = :id",
    { id, nombre, apellido }
  );
  return affectedRows > 0;
}
