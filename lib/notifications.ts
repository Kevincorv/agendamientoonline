import { execute } from "./db";

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
