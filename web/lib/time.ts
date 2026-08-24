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

export function formatearFecha(fecha: string): string {
  const [y, m, d] = (fecha || "").split("-");
  if (!y || !m || !d) return fecha || "";
  return `${d}/${m}/${y}`;
}

export function addDays(fecha: string, dias: number): string {
  const dt = new Date(`${fecha}T00:00:00`);
  dt.setDate(dt.getDate() + dias);
  const y = dt.getFullYear();
  const m = String(dt.getMonth() + 1).padStart(2, "0");
  const d = String(dt.getDate()).padStart(2, "0");
  return `${y}-${m}-${d}`;
}
