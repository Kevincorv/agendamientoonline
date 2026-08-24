#!/usr/bin/env node
// Diagnostica por qué el login falla.
import mysql from "mysql2/promise";

const url = process.env.DATABASE_URL;
const u = new URL(url);
const conn = await mysql.createConnection({
  host: u.hostname,
  port: Number(u.port || 4000),
  user: decodeURIComponent(u.username),
  password: decodeURIComponent(u.password),
  database: u.pathname.replace(/^\//, ""),
  ssl: { rejectUnauthorized: true, minVersion: "TLSv1.2" },
});

console.log("─── Diagnóstico login ───\n");

const [u1] = await conn.execute("SELECT id, email, activo, rol_id, locked_until, login_attempts, LEFT(password, 30) AS hash_prefix FROM usuarios WHERE email = ?", ["admin@clinicasanluis.com"]);
console.log("1. Usuario encontrado:");
console.log("  ", u1[0]);

const [u2] = await conn.execute("SELECT id, email, activo, rol_id FROM usuarios WHERE LOWER(email) = LOWER(?) AND activo = 1", ["admin@clinicasanluis.com"]);
console.log("\n2. Con JOIN (LOWER + activo=1):");
console.log("  ", u2.length ? u2[0] : "NINGUNO ← FALLA AQUÍ");

const [r1] = await conn.execute("SELECT * FROM roles WHERE id = ?", [u1[0].rol_id]);
console.log("\n3. Rol asociado:");
console.log("  ", r1[0] ?? "ROL NO ENCONTRADO");

// Probar bcrypt directamente
const bcrypt = (await import("bcryptjs")).default;
const [u3] = await conn.execute("SELECT password FROM usuarios WHERE email = ?", ["admin@clinicasanluis.com"]);
const ok = await bcrypt.compare("admin123", u3[0].password);
console.log(`\n4. bcrypt.compare("admin123", hash): ${ok ? "OK ✓" : "FALLA ✗"}`);

await conn.end();
