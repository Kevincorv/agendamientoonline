import { query, queryOne, execute } from "../db";
import type { Usuario } from "../types";

export interface UsuarioConRol extends Usuario {
  rol_nombre?: string | null;
  permisos_count?: number;
}

export function obtenerUsuarios(): Promise<UsuarioConRol[]> {
  return query<UsuarioConRol[]>(
    `SELECT u.*, r.nombre AS rol_nombre
     FROM usuarios u LEFT JOIN roles r ON u.rol_id = r.id
     ORDER BY u.activo DESC, u.nombre ASC`
  );
}

export function obtenerRoles() {
  return query("SELECT * FROM roles ORDER BY id ASC");
}

export function obtenerUsuarioPorId(id: number) {
  return queryOne<UsuarioConRol>(
    `SELECT u.*, r.nombre AS rol_nombre
     FROM usuarios u LEFT JOIN roles r ON u.rol_id = r.id
     WHERE u.id = :id`,
    { id }
  );
}

export function obtenerUsuarioPorEmail(email: string) {
  return queryOne<UsuarioConRol>(
    `SELECT u.*, r.nombre AS rol_nombre
     FROM usuarios u LEFT JOIN roles r ON u.rol_id = r.id
     WHERE LOWER(u.email) = :email`,
    { email: email.toLowerCase() }
  );
}

export async function crearUsuario(data: {
  nombre: string;
  apellido: string;
  email: string;
  password: string; // hash
  rol_id: number;
}): Promise<number> {
  const { insertId } = await execute(
    `INSERT INTO usuarios (nombre, apellido, email, password, rol_id, activo)
     VALUES (:nombre, :apellido, :email, :password, :rol_id, 1)`,
    data
  );
  return insertId;
}

export async function actualizarUsuario(
  id: number,
  data: { nombre: string; email: string; rol_id: number; password?: string }
): Promise<boolean> {
  if (data.password) {
    const { affectedRows } = await execute(
      `UPDATE usuarios
          SET nombre = :nombre, email = :email, rol_id = :rol_id, password = :password
        WHERE id = :id`,
      { ...data, id }
    );
    return affectedRows > 0;
  }
  const { affectedRows } = await execute(
    `UPDATE usuarios
        SET nombre = :nombre, email = :email, rol_id = :rol_id
      WHERE id = :id`,
    { nombre: data.nombre, email: data.email, rol_id: data.rol_id, id }
  );
  return affectedRows > 0;
}

export async function eliminarUsuario(id: number): Promise<boolean> {
  const { affectedRows } = await execute("UPDATE usuarios SET activo = 0 WHERE id = :id", { id });
  return affectedRows > 0;
}

export async function desbloquearUsuario(id: number): Promise<boolean> {
  const { affectedRows } = await execute(
    "UPDATE usuarios SET login_attempts = 0, locked_until = NULL WHERE id = :id",
    { id }
  );
  return affectedRows > 0;
}

export async function actualizarPasswordUsuario(id: number, hash: string): Promise<boolean> {
  const { affectedRows } = await execute(
    "UPDATE usuarios SET password = :password WHERE id = :id",
    { id, password: hash }
  );
  return affectedRows > 0;
}
