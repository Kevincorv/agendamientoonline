// Helpers de fecha/hora en la zona horaria del sistema (America/Asuncion),
// independientes del TZ del proceso (Vercel corre en UTC por defecto).

const TZ = process.env.TIMEZONE ?? "America/Asuncion";

export function todayInTz(): string {
  const p = new Intl.DateTimeFormat("en-CA", {
    timeZone: TZ,
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  }).formatToParts(new Date());
  const y = p.find((x) => x.type === "year")!.value;
  const m = p.find((x) => x.type === "month")!.value;
  const d = p.find((x) => x.type === "day")!.value;
  return `${y}-${m}-${d}`;
}

export function nowTimeInTz(): string {
  const p = new Intl.DateTimeFormat("en-GB", {
    timeZone: TZ,
    hour: "2-digit",
    minute: "2-digit",
    hourCycle: "h23",
  }).formatToParts(new Date());
  const h = p.find((x) => x.type === "hour")!.value;
  const m = p.find((x) => x.type === "minute")!.value;
  return `${h}:${m}`;
}

// Día de la semana 0=Domingo .. 6=Sábado (igual que PHP date('w'))
export function diaSemana(fecha: string): number {
  return new Date(`${fecha}T00:00:00`).getDay();
}

export function formatearFecha(fecha: string | Date | null | undefined): string {
  if (!fecha) return "";
  if (fecha instanceof Date) {
    const y = fecha.getFullYear();
    const m = String(fecha.getMonth() + 1).padStart(2, "0");
    const d = String(fecha.getDate()).padStart(2, "0");
    return `${d}/${m}/${y}`;
  }
  const s = String(fecha);
  const [y, m, d] = s.split(/[- T]/);
  if (!y || !m || !d) return s;
  return `${d}/${m}/${y}`;
}

// Convierte un valor que puede ser Date|string|null|undefined a string ISO local
// en formato "YYYY-MM-DD HH:mm:ss" (lo que usa MySQL DATETIME).
export function toMysqlDateTime(v: unknown): string {
  if (!v) return "";
  if (v instanceof Date) {
    const y = v.getFullYear();
    const mo = String(v.getMonth() + 1).padStart(2, "0");
    const d = String(v.getDate()).padStart(2, "0");
    const h = String(v.getHours()).padStart(2, "0");
    const mi = String(v.getMinutes()).padStart(2, "0");
    const s = String(v.getSeconds()).padStart(2, "0");
    return `${y}-${mo}-${d} ${h}:${mi}:${s}`;
  }
  return String(v);
}

// Convierte a fecha "YYYY-MM-DD" (DATE de MySQL).
export function toMysqlDate(v: unknown): string {
  if (!v) return "";
  if (v instanceof Date) {
    const y = v.getFullYear();
    const mo = String(v.getMonth() + 1).padStart(2, "0");
    const d = String(v.getDate()).padStart(2, "0");
    return `${y}-${mo}-${d}`;
  }
  return String(v);
}

export function addDays(fecha: string, dias: number): string {
  const dt = new Date(`${fecha}T00:00:00`);
  dt.setDate(dt.getDate() + dias);
  const y = dt.getFullYear();
  const m = String(dt.getMonth() + 1).padStart(2, "0");
  const d = String(dt.getDate()).padStart(2, "0");
  return `${y}-${m}-${d}`;
}
