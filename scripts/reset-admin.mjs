#!/usr/bin/env node
// Resetea la contraseña del admin a "admin123" (bcrypt cost 10).
// Uso: node --env-file=.env.local scripts/reset-admin.mjs

import { readFileSync, existsSync } from "node:fs";
import { resolve, dirname } from "node:path";
import { fileURLToPath } from "node:url";
import mysql from "mysql2/promise";
import bcrypt from "bcryptjs";

const __dirname = dirname(fileURLToPath(import.meta.url));

const url = process.env.DATABASE_URL;
if (!url) { console.error("❌ Falta DATABASE_URL"); process.exit(1); }
const u = new URL(url);
const ssl = u.searchParams.get("ssl-mode") || "REQUIRED";

const conn = await mysql.createConnection({
  host: u.hostname,
  port: Number(u.port || 4000),
  user: decodeURIComponent(u.username),
  password: decodeURIComponent(u.password),
  database: u.pathname.replace(/^\//, ""),
  ssl: { rejectUnauthorized: true, minVersion: "TLSv1.2" },
  namedPlaceholders: true,
});

const NEW_PWD = process.argv[2] || "admin123";
const hash = await bcrypt.hash(NEW_PWD, 10);

const [r] = await conn.execute(
  `UPDATE usuarios
     SET password = :pwd,
         login_attempts = 0,
         locked_until = NULL
   WHERE email = :email`,
  { pwd: hash, email: "admin@clinicasanluis.com" }
);
console.log(`✓ Filas actualizadas: ${r.affectedRows}`);
const [rows] = await conn.execute(
  "SELECT id, nombre, email, rol_id FROM usuarios WHERE email = :email",
  { email: "admin@clinicasanluis.com" }
);
console.log("Usuario:", rows[0]);
console.log(`\nCredenciales: admin@clinicasanluis.com / ${NEW_PWD}`);
await conn.end();
