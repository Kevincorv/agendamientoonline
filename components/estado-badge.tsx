export function EstadoBadge({ estado }: { estado?: string | null }) {
  const map: Record<string, [string, string]> = {
    pendiente: ["warning", "Pendiente"],
    confirmada: ["info", "Confirmada"],
    cancelada: ["danger", "Cancelada"],
    atendida: ["success", "Atendida"],
    "no asistio": ["secondary", "No asistió"],
  };
  const [variant, text] = map[estado ?? ""] ?? ["secondary", estado ?? "—"];
  return <span className={`badge badge-${variant}`}>{text}</span>;
}
