import { headers } from "next/headers";
import { query } from "./db";

export interface AuditInput {
  usuarioId?: number | null;
  usuarioNombre?: string | null;
  accion: string;
  tabla?: string | null;
  registroId?: number | null;
  descripcion?: string | null;
  datosAntes?: unknown;
  datosDespues?: unknown;
}

export async function auditLog(input: AuditInput): Promise<void> {
  try {
    let ip: string | null = null;
    let ua: string | null = null;
    try {
      const h = await headers();
      ip = h.get("x-forwarded-for")?.split(",")[0]?.trim() ?? null;
      ua = h.get("user-agent");
    } catch {
      /* fuera de request */
    }
    await query(
      `INSERT INTO auditoria
         (usuario_id, usuario_nombre, accion, tabla, registro_id, descripcion,
          datos_antes, datos_despues, ip, user_agent)
       VALUES
         (:usuario_id, :usuario_nombre, :accion, :tabla, :registro_id, :descripcion,
          :datos_antes, :datos_despues, :ip, :user_agent)`,
      {
        usuario_id: input.usuarioId ?? null,
        usuario_nombre: input.usuarioNombre ?? null,
        accion: input.accion,
        tabla: input.tabla ?? null,
        registro_id: input.registroId ?? null,
        descripcion: input.descripcion ?? null,
        datos_antes: input.datosAntes ? JSON.stringify(input.datosAntes) : null,
        datos_despues: input.datosDespues ? JSON.stringify(input.datosDespues) : null,
        ip,
        user_agent: ua,
      }
    );
  } catch (e) {
    console.error("auditLog error:", e);
  }
}
