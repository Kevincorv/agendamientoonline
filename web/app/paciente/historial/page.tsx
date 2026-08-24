import { redirect } from "next/navigation";
import Link from "next/link";
import { auth } from "@/lib/auth";
import { obtenerCitasPaciente } from "@/lib/repos/pacientes";
import { formatearFecha } from "@/lib/time";

export const dynamic = "force-dynamic";

const ESTADO_BADGE: Record<string, [string, string]> = {
  pendiente: ["warning", "Pendiente"],
  confirmada: ["info", "Confirmada"],
  cancelada: ["danger", "Cancelada"],
  atendida: ["success", "Atendida"],
  "no asistio": ["secondary", "No asistió"],
};

export default async function PacienteHistorialPage() {
  const session = await auth();
  if (!session?.user) redirect("/paciente/login");
  if (session.user.rol !== "paciente") redirect("/admin/dashboard");

  const citas = await obtenerCitasPaciente(session.user.email ?? "", false);

  return (
    <div className="max-w-5xl mx-auto px-4 sm:px-6 py-6 fade-up">
      <div className="flex items-center justify-between mb-5 flex-wrap gap-3">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-800">Historial de citas</h1>
          <p className="text-sm text-slate-400">Todas tus citas pasadas y futuras</p>
        </div>
        <Link href="/paciente/dashboard" className="btn btn-secondary btn-sm">
          <i className="fas fa-arrow-left"></i> Volver
        </Link>
      </div>

      {citas.length === 0 ? (
        <div className="card p-10 text-center text-slate-400">
          <i className="fas fa-history text-3xl mb-2 block"></i>
          <p className="text-sm font-semibold">Sin citas registradas</p>
          <Link href="/agendar" className="btn btn-primary btn-sm mt-4 inline-flex">
            <i className="fas fa-plus"></i> Agendar primera cita
          </Link>
        </div>
      ) : (
        <div className="card overflow-hidden">
          <div className="overflow-x-auto">
            <table className="data-table resp-table">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Especialidad</th>
                  <th>Médico</th>
                  <th>Estado</th>
                  <th className="text-right">Acc.</th>
                </tr>
              </thead>
              <tbody>
                {citas.map((c) => (
                  <tr key={c.id as number}>
                    <td data-label="Fecha" className="text-sm font-medium text-slate-700">
                      {formatearFecha(String(c.fecha))}
                    </td>
                    <td data-label="Hora" className="font-mono text-sm">
                      {String(c.hora).slice(0, 5)}
                    </td>
                    <td data-label="Especialidad" className="text-sm text-slate-600">
                      {c.especialidad ? String(c.especialidad) : "—"}
                    </td>
                    <td data-label="Médico" className="text-sm text-slate-600">
                      Dr. {String(c.medico_nombre ?? "")} {String(c.medico_apellido ?? "")}
                    </td>
                    <td data-label="Estado">
                      <span className={`badge badge-${ESTADO_BADGE[String(c.estado)]?.[0] ?? "secondary"}`}>
                        {ESTADO_BADGE[String(c.estado)]?.[1] ?? String(c.estado)}
                      </span>
                    </td>
                    <td data-label="Acc.">
                      <Link href={`/paciente/comprobante?id=${c.id}`} className="btn btn-icon btn-sm btn-ghost text-sky-600" target="_blank" title="Comprobante">
                        <i className="fas fa-print"></i>
                      </Link>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );
}
