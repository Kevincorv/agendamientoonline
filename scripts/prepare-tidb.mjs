#!/usr/bin/env node
// Adapta el dump MySQL (phpMyAdmin) a TiDB Serverless.
// TiDB no soporta las collations utf8mb4_0900_ai_ci / utf8mb4_unicode_ci;
// las reemplazamos por utf8mb4_bin (soportada). El resto (FKs, ENGINE, AUTO_INCREMENT) es compatible.
//
// Uso:  node scripts/prepare-tidb.mjs [sql_entrada] [sql_salida]
// Por defecto lee de la carpeta Downloads del usuario y escribe database/schema.tidb.sql

import { readFileSync, writeFileSync, mkdirSync, existsSync } from "node:fs";
import { dirname, join } from "node:path";
import { homedir } from "node:os";

const input = process.argv[2] || join(homedir(), "Downloads", "clinica_san_luis.sql");
const output = process.argv[3] || join(process.cwd(), "database", "schema.tidb.sql");

if (!existsSync(input)) {
  console.error(`No se encontró el SQL de entrada: ${input}`);
  process.exit(1);
}

let sql = readFileSync(input, "utf8");

// 1) Collations no soportadas por TiDB → utf8mb4_bin
sql = sql.replace(/utf8mb4_0900_ai_ci/g, "utf8mb4_bin");
sql = sql.replace(/utf8mb4_unicode_ci/g, "utf8mb4_bin");

// 2) Cabecera
const header = `-- ============================================================
-- CITAS MÉDICAS ONLINE — Esquema + datos seed adaptado a TiDB
-- Generado por scripts/prepare-tidb.mjs desde el dump phpMyAdmin.
-- Collations: utf8mb4_bin (compatible TiDB Serverless).
-- Importar con: mysql --ssl-mode=REQUIRED ... < schema.tidb.sql
-- ============================================================

`;

mkdirSync(dirname(output), { recursive: true });
writeFileSync(output, header + sql, "utf8");
console.log(`OK -> ${output}`);
console.log(`Tamaño: ${Buffer.byteLength(header + sql)} bytes`);
