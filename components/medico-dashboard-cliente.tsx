"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";

type Win = typeof window & { showToast?: (m: string, t?: "success" | "error" | "info") => void };

interface Cita {
  id: number; nombre_paciente: string; telefono: string; email: string;
  hora: string; motivo: string; estado: string; estado_id: number;
}

const ESTADO: Record<string, [string, string]> = {
  pendiente: ["warning", "Pendiente"],
  confirmada: ["info", "Confirmada"],
  cancelada: ["danger", "Cancelada"],
  atendida: ["success", "Atendida"],
};

export function MedicoDashboardCliente({
  medico, fecha, citas, stats, proximaCita,
}: {
  medico: { id: number; nombre: string; apellido: string; especialidad_nombre: string | null };
  fecha: string;
  citas: Cita[];
  stats: { totalAtendidas: number; pendientesHoy: number; totalHoy: number };
  proximaCita: { id: number; nombre_paciente: string; hora: string } | null;
}) {
  const router = useRouter();
  const [notas, setNotas] = useState<Record<number, string>>({});
  const [openNotas, setOpenNotas] = useState<number | null>(null);

  async function setEstado(citaId: number, estadoId: number, nota?: string) {
    const fd = new FormData();
    fd.append("cita_id", String(citaId));
    fd.append("estado_id", String(estadoId));
    if (nota) fd.append("notas", nota);
    const r = await fetch("/api/medico/citas/estado", { method: "POST", body: fd });
    const res = await r.json();
    (window as Win).showToast?.(res.message ?? "OK", res.success ? "success" : "error");
    if (res.success) router.refresh();
  }

  return (
    <>
      <div className="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
        <div>
          <h1 className="text-xl font-extrabold text-slate-800">Dr. {medico.nombre} {medico.apellido}</h1>
          <p className="text-sm text-slate-400">{medico.especialidad_nombre ?? "—"} · Agenda del día</p>
        </div>
        <div className="flex items-center gap-2">
          <input
            type="date"
            value={fecha}
            onChange={(e) => router.push(`/medico/dashboard?fecha=${e.target.value}`)}
            className="input-field"
          />
          <Link href="/medico/agenda" className="btn btn-secondary btn-sm">
            <i className="fas fa-calendar-week"></i> Agenda completa
          </Link>
        </div>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
        <div className="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex items-center gap-3 fade-up">
          <div className="w-10 h-10 rounded-lg flex items-center justify-center" style={{ background: "#e0f2fe" }}>
            <i className="fas fa-calendar-day text-sky-600"></i>
          </div>
          <div>
            <p className="text-2xl font-extrabold text-slate-800">{stats.totalHoy}</p>
            <p className="text-xs text-slate-400">Citas hoy</p>
          </div>
        </div>
        <div className="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex items-center gap-3 fade-up" style={{ animationDelay: ".05s" }}>
          <div className="w-10 h-10 rounded-lg flex items-center justify-center" style={{ background: "#fef9c3" }}>
            <i className="fas fa-clock text-amber-600"></i>
          </div>
          <div>
            <p className="text-2xl font-extrabold text-slate-800">{stats.pendientesHoy}</p>
            <p className="text-xs text-slate-400">Pendientes</p>
          </div>
        </div>
        <div className="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex items-center gap-3 fade-up" style={{ animationDelay: ".1s" }}>
          <div className="w-10 h-10 rounded-lg flex items-center justify-center" style={{ background: "#d1fae5" }}>
            <i className="fas fa-check-circle text-emerald-600"></i>
          </div>
          <div>
            <p className="text-2xl font-extrabold text-slate-800">{stats.totalAtendidas}</p>
            <p className="text-xs text-slate-400">Atendidas hoy</p>
          </div>
        </div>
      </div>

      {proximaCita && (
        <div className="card p-5 mb-5 fade-up" style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)", borderRadius: 14 }}>
          <p className="text-xs text-sky-200 uppercase font-bold tracking-wider">Próxima cita</p>
          <div className="flex items-center gap-4 mt-2 text-white">
            <div
              className="w-12 h-12 rounded-xl flex items-center justify-center text-white text-xl font-bold"
              style={{ background: "rgba(255,255,255,.2)" }}
            >
              {proximaCita.nombre_paciente.charAt(0).toUpperCase()}
            </div>
            <div className="flex-1">
              <p className="font-extrabold">{proximaCita.nombre_paciente}</p>
              <p className="text-sm text-sky-200 font-mono">{proximaCita.hora}</p>
            </div>
          </div>
        </div>
      )}

      <div className="card overflow-hidden fade-up">
        <div className="px-5 py-3 border-b border-slate-100">
          <h2 className="text-sm font-bold text-slate-700">Citas del día</h2>
        </div>
        {citas.length === 0 ? (
          <div className="empty-state">
            <i className="fas fa-calendar-times"></i>
            <h3>Sin citas</h3>
            <p>No tenés citas para esta fecha</p>
          </div>
        ) : (
          <div className="divide-y divide-slate-100">
            {citas.map((c) => (
              <div key={c.id} className="p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                <div className="text-center flex-shrink-0 w-14 py-1 rounded-lg bg-sky-50">
                  <p className="text-lg font-extrabold text-sky-600">{c.hora}</p>
                </div>
                <div className="flex-1 min-w-0">
                  <p className="font-bold text-slate-800">{c.nombre_paciente}</p>
                  <p className="text-xs text-slate-500">
                    <i className="fas fa-phone text-sky-500 mr-1"></i>{c.telefono || "—"}
                    {c.email && <><span className="mx-2">·</span><i className="fas fa-envelope text-sky-500 mr-1"></i>{c.email}</>}
                  </p>
                  {c.motivo && <p className="text-xs text-slate-400 mt-1 truncate"><i className="fas fa-comment-medical mr-1"></i>{c.motivo}</p>}
                </div>
                <span className={`badge badge-${ESTADO[c.estado]?.[0] ?? "secondary"}`}>
                  {ESTADO[c.estado]?.[1] ?? c.estado}
                </span>
                <div className="flex items-center gap-1 flex-wrap">
                  {c.estado_id !== 4 && c.estado_id !== 3 && (
                    <button onClick={() => setOpenNotas(openNotas === c.id ? null : c.id)} className="btn btn-secondary btn-sm">
                      <i className="fas fa-sticky-note"></i> Notas
                    </button>
                  )}
                  {c.estado_id === 1 && (
                    <button onClick={() => setEstado(c.id, 2)} className="btn btn-primary btn-sm">
                      <i className="fas fa-check"></i> Confirmar
                    </button>
                  )}
                  {(c.estado_id === 1 || c.estado_id === 2) && (
                    <button onClick={() => setEstado(c.id, 4, notas[c.id])} className="btn btn-success btn-sm">
                      <i className="fas fa-check-double"></i> Atender
                    </button>
                  )}
                  {c.estado_id !== 3 && c.estado_id !== 4 && (
                    <button onClick={() => setEstado(c.id, 3, "Cancelada por el médico")} className="btn btn-danger btn-sm">
                      <i className="fas fa-times"></i> Cancelar
                    </button>
                  )}
                </div>
                {openNotas === c.id && (
                  <div className="w-full mt-2">
                    <textarea
                      placeholder="Notas del médico..."
                      className="input-field resize-none w-full"
                      rows={2}
                      value={notas[c.id] ?? ""}
                      onChange={(e) => setNotas({ ...notas, [c.id]: e.target.value })}
                    />
                  </div>
                )}
              </div>
            ))}
          </div>
        )}
      </div>
    </>
  );
}
