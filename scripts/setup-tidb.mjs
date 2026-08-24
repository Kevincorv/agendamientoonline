#!/usr/bin/env node
// Setup de la base TiDB: crea la DB clinica_san_luis e importa el schema.
// Uso:  node --env-file=.env.local scripts/setup-tidb.mjs

import { readFileSync, existsSync } from "node:fs";
import { resolve, dirname } from "node:path";
import { fileURLToPath } from "node:url";
import mysql from "mysql2/promise";

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, "..");

const url = process.env.DATABASE_URL;
if (!url) {
  console.error("❌ Falta DATABASE_URL en .env.local");
  process.exit(1);
}

const u = new URL(url);
const DB_NAME = u.pathname.replace(/^\//, "");
const host = u.hostname;
const port = Number(u.port || 4000);
const user = decodeURIComponent(u.username);
const password = decodeURIComponent(u.password);
const ssl = u.searchParams.get("ssl-mode") || "REQUIRED";

const schemaPath = resolve(ROOT, "database", "schema.tidb.sql");
if (!existsSync(schemaPath)) {
  console.error(`❌ No se encontró el schema en: ${schemaPath}`);
  process.exit(1);
}

console.log("─── TiDB Setup ─────────────────────────────────────");
console.log(`Host:     ${host}:${port}`);
console.log(`User:     ${user}`);
console.log(`Database: ${DB_NAME}`);
console.log(`SSL:      ${ssl}`);
console.log(`Schema:   ${schemaPath}`);
console.log("─────────────────────────────────────────────────────");

async function main() {
  // 1) Conectar al system DB para crear la base
  const sysUrl = new URL(url);
  sysUrl.pathname = "/sys";
  const sysConn = await mysql.createConnection({
    host,
    port,
    user,
    password,
    database: "sys",
    ssl: { rejectUnauthorized: true },
    multipleStatements: true,
  });
  console.log("✓ Conectado a /sys");

  console.log(`→ Creando base '${DB_NAME}' si no existe...`);
  await sysConn.query(
    `CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin`
  );
  console.log(`✓ Base '${DB_NAME}' lista`);
  await sysConn.end();

  // 2) Conectar a la base y ejecutar el schema
  const conn = await mysql.createConnection({
    host,
    port,
    user,
    password,
    database: DB_NAME,
    ssl: { rejectUnauthorized: true },
    multipleStatements: true,
  });
  console.log(`✓ Conectado a '${DB_NAME}'`);

  const sql = readFileSync(schemaPath, "utf8");
  console.log(`→ Importando schema (${(sql.length / 1024).toFixed(1)} KB)...`);

  // Dividir por statements; el schema trae sentencias separadas por ;
  // y usa comentarios "--". Ya tiene multiplesStatements habilitado.
  try {
    await conn.query(sql);
    console.log("✓ Schema importado correctamente");
  } catch (err) {
    console.error("❌ Error importando schema:");
    console.error(err.message);
    process.exit(1);
  }

  // 3) Verificar
  const [tables] = await conn.query("SHOW TABLES");
  console.log(`✓ Tablas creadas: ${tables.length}`);
  for (const row of tables) {
    const name = Object.values(row)[0];
    const [count] = await conn.query(`SELECT COUNT(*) AS c FROM \`${name}\``);
    console.log(`   • ${name.padEnd(25)} ${count[0].c} filas`);
  }

  await conn.end();
  console.log("\n🎉 ¡Listo! Tu base TiDB está configurada.");
  console.log("Credenciales del admin:");
  console.log("   Email:      admin@clinicasanluis.com");
  console.log("   Contraseña: admin123 (cambiala después del primer login)");
}

main().catch((err) => {
  console.error("❌ Error fatal:", err.message);
  process.exit(1);
});
