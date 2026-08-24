import mysql from "mysql2/promise";
import { dbConfig, env } from "./env";

// Detecta si la URL requiere SSL (TiDB Serverless o cualquier cluster con ssl-mode).
const urlNeedsSsl = !!dbConfig.url && /[?&]ssl(-mode)?=(REQUIRED|VERIFY_CA|VERIFY_IDENTITY)/i.test(dbConfig.url);

// Pool global. Soporta TiDB Serverless (TLS) vía DATABASE_URL o vars separadas.
export const pool: mysql.Pool = dbConfig.url
  ? mysql.createPool({
      uri: dbConfig.url,
      waitForConnections: true,
      connectionLimit: 10,
      namedPlaceholders: true,
      ssl: urlNeedsSsl ? { rejectUnauthorized: true, minVersion: "TLSv1.2" } : undefined,
    })
  : mysql.createPool({
      host: dbConfig.host,
      port: dbConfig.port,
      user: dbConfig.user,
      password: dbConfig.password,
      database: dbConfig.database,
      waitForConnections: true,
      connectionLimit: 10,
      namedPlaceholders: true,
      ssl: { rejectUnauthorized: true, minVersion: "TLSv1.2" },
    });

// Sincronizar timezone de MySQL con PHP (America/Asuncion)
pool.on("connection", (conn) => {
  conn.promise().query(`SET time_zone = '${env.timezone}'`).catch(() => {
    /* TiDB puede no aceptar el nombre; ignoramos */
  });
});

// Helper: ejecuta query con placeholders :nombre (estilo PDO) y devuelve rows
export async function query<T = Record<string, unknown>[]>(
  sql: string,
  params: Record<string, unknown> = {}
): Promise<T> {
  const [rows] = await pool.query(sql, params as never);
  return rows as T;
}

export async function queryOne<T = Record<string, unknown>>(
  sql: string,
  params: Record<string, unknown> = {}
): Promise<T | null> {
  const rows = await query<T[]>(sql, params);
  return Array.isArray(rows) && rows.length ? rows[0] : null;
}

// Para INSERT/UPDATE/DELETE; devuelve insertId / affectedRows
export async function execute(
  sql: string,
  params: Record<string, unknown> = {}
): Promise<{ insertId: number; affectedRows: number }> {
  const [res] = await pool.execute(sql, params as never);
  const r = res as mysql.ResultSetHeader;
  return { insertId: r.insertId ?? 0, affectedRows: r.affectedRows ?? 0 };
}
