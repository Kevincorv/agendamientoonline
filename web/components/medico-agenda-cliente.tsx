"use client";

import Link from "next/link";
import { formatearFecha } from "@/lib/time";

const ESTADO: Record<string, [string, string]> = {
  pendiente: ["warning", "Pendiente"],
  confirmada: ["info", "Confirmada"],
  cancelada: ["danger", "Cancelada"],
  atendida: ["success", "Atendida"],
};

export function MedicoAgendaCliente({
  medico,
  porFecha,
}: {
  medico: { id: number; nombre: string; apellido: string };
  porFecha: Record<string, Array<{ id: number; nombre_paciente: string; hora: string; telefono: string; estado: string; estado_id: number; motivo: string }>>;
}) {
  const fechas = Object.keys(porFecha).sort();
  return (
    <>
      <div className="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
        <div>
          <h1 className="text-xl font-extrabold text-slate-800">Agenda completa</h1>
          <p className="text-sm text-slate-400">Dr. {medico.nombre} {medico.apellido} · Todas las citas</p>
        </div>
        <Link href="/medico/dashboard" className="btn btn-secondary btn-sm">
          <i className="fas fa-tachometer-alt"></i> Volver al día
        </Link>
      </div>

      {fechas.length === 0 ? (
        <div className="card p-10 text-center text-slate-400 fade-up">
          <i className="fas fa-calendar-times text-3xl mb-2 block"></i>
          No tenés citas agendadas
        </div>
      ) : (
        <div className="space-y-5">
          {fechas.map((f) => (
            <div key={f} className="card overflow-hidden fade-up">
              <div className="px-5 py-3 border-b border-slate-100 flex items-center gap-3">
                <div className="w-10 h-10 rounded-lg flex items-center justify-center" style={{ background: "#e0f2fe" }}>
                  <i className="fas fa-calendar-day text-sky-600"></i>
                </div>
                <div>
                  <h2 className="text-sm font-bold text-slate-800">{formatearFecha(f)}</h2>
                  <p className="text-xs text-slate-400">{porFecha[f].length} citas</p>
                </div>
              </div>
              <div className="overflow-x-auto">
                <table className="data-table resp-table">
                  <thead>
                    <tr>
                      <th>Hora</th>
                      <th>Paciente</th>
                      <th>Teléfono</th>
                      <th>Motivo</th>
                      <th>Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    {porFecha[f].map((c) => (
                      <tr key={c.id}>
                        <td data-label="Hora"><span className="font-mono text-sm">{c.hora}</span></td>
                        <td data-label="Paciente"><p className="font-semibold text-slate-800 text-sm">{c.nombre_paciente}</p></td>
                        <td data-label="Teléfono" className="text-xs text-slate-500">{c.telefono || "—"}</td>
                        <td data-label="Motivo" className="text-xs text-slate-500 max-w-xs truncate">{c.motivo || "—"}</td>
                        <td data-label="Estado">
                          <span className={`badge badge-${ESTADO[c.estado]?.[0] ?? "secondary"}`}>
                            {ESTADO[c.estado]?.[1] ?? c.estado}
                          </span>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          ))}
        </div>
      )}
    </>
  );
}
