"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";

type Win = typeof window & { showToast?: (m: string, t?: "success" | "error" | "info") => void };

interface Proxima {
  id: number; nombre_paciente: string; telefono: string; email: string;
  hora: string; fecha: string; estado: string; estado_id: number; motivo: string;
  especialidad: string | null; medico_nombre: string; medico_apellido: string;
}
interface HistItem { id: number; fecha: string; estado: string; especialidad: string | null }

const ESTADO_BADGE: Record<string, [string, string]> = {
  pendiente: ["warning", "Pendiente"],
  confirmada: ["info", "Confirmada"],
  cancelada: ["danger", "Cancelada"],
  atendida: ["success", "Atendida"],
  "no asistio": ["secondary", "No asistió"],
};

export function PacienteDashboardCliente({
  user, proximas, historial,
}: {
  user: { nombre: string; apellido: string; email: string };
  proximas: Proxima[];
  historial: HistItem[];
}) {
  const router = useRouter();
  const [search, setSearch] = useState("");
  const nombre = `${user.nombre} ${user.apellido}`.trim();
  const initial = nombre.charAt(0).toUpperCase();

  const filtered = proximas.filter((c) => {
    if (!search) return true;
    const text = `${c.especialidad ?? ""} ${c.medico_nombre} ${c.medico_apellido} ${c.fecha} ${c.estado}`.toLowerCase();
    return text.includes(search.toLowerCase());
  });

  async function cancelar(id: number) {
    if (!confirm("¿Cancelar esta cita?")) return;
    const r = await fetch("/api/paciente/citas/cancelar", {
      method: "POST",
      body: new URLSearchParams({ id: String(id) }),
    });
    const res = await r.json();
    (window as Win).showToast?.(res.message ?? "OK", res.success ? "success" : "error");
    if (res.success) router.refresh();
  }

  return (
    <div className="max-w-5xl mx-auto px-4 sm:px-6 py-6 fade-up">
      {/* Top bar */}
      <div className="flex items-center gap-3 mb-6">
        <div className="relative flex-1">
          <i className="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
          <input
            type="text"
            placeholder="Buscar por médico, especialidad, fecha..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="w-full h-11 pl-10 pr-4 rounded-2xl border border-slate-200 bg-white text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition"
          />
        </div>
        <Link href="/agendar" className="h-11 px-5 rounded-2xl flex items-center gap-2 text-sm font-bold text-white shadow-md hover:shadow-lg transition-all" style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}>
          <i className="fas fa-plus"></i>
          <span className="hidden sm:inline">Nueva Cita</span>
        </Link>
        <div className="hidden md:flex items-center gap-2">
          <Link href="/paciente/historial" className="btn btn-secondary btn-sm">
            <i className="fas fa-history"></i> Historial
          </Link>
          <Link href="/paciente/perfil" className="btn btn-secondary btn-sm">
            <i className="fas fa-user-cog"></i> Perfil
          </Link>
        </div>
      </div>

      {/* Welcome banner */}
      <div className="rounded-2xl p-5 sm:p-6 mb-6 text-white shadow-lg" style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}>
        <div className="flex items-center gap-4">
          <div className="w-14 h-14 rounded-xl flex items-center justify-center text-2xl font-bold bg-white/20 backdrop-blur-sm flex-shrink-0 shadow-inner">
            {initial}
          </div>
          <div>
            <h1 className="text-lg sm:text-xl font-extrabold">Bienvenido, {nombre}</h1>
            <p className="text-sm text-white/70">Gestioná tus citas médicas de forma rápida y sencilla</p>
          </div>
        </div>
      </div>

      {/* Próximas citas */}
      <div className="card mb-6">
        <div className="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
          <h2 className="text-sm font-bold text-slate-700">
            <i className="fas fa-calendar-day text-sky-500 mr-2"></i>Próximas Citas
          </h2>
          {proximas.length > 0 && (
            <Link href="/paciente/historial" className="text-xs font-semibold text-sky-600 hover:text-sky-700 transition whitespace-nowrap">
              Ver historial <i className="fas fa-arrow-right ml-1"></i>
            </Link>
          )}
        </div>
        {filtered.length === 0 ? (
          <div className="text-center py-12 text-slate-400">
            <i className="far fa-calendar-check text-4xl mb-3 block mx-auto"></i>
            <p className="text-sm font-medium">{search ? "Sin resultados" : "No tenés citas próximas"}</p>
            {!search && (
              <Link href="/agendar" className="btn btn-primary btn-sm mt-4 inline-flex">
                <i className="fas fa-plus"></i> Agendar Cita
              </Link>
            )}
          </div>
        ) : (
          <div className="divide-y divide-slate-100">
            {filtered.map((c) => (
              <div key={c.id} className="px-5 py-4 hover:bg-slate-50 transition flex flex-col sm:flex-row sm:items-center gap-3">
                <div className="flex items-center gap-4 flex-1 min-w-0">
                  <div className="text-center flex-shrink-0 w-14 py-1 rounded-lg bg-sky-50">
                    <p className="text-lg font-extrabold text-sky-600 leading-tight">
                      {c.fecha.split("-")[2]}
                    </p>
                    <p className="text-[10px] text-sky-500 font-bold uppercase">
                      {new Date(c.fecha + "T00:00:00").toLocaleString("es", { month: "short" })}
                    </p>
                  </div>
                  <div className="min-w-0 flex-1">
                    <p className="text-sm font-bold text-slate-800 truncate">{c.especialidad ?? "Consulta"}</p>
                    <p className="text-xs text-slate-500 mt-0.5">
                      <i className="far fa-clock mr-1"></i>{c.hora} ·
                      Dr. {c.medico_nombre} {c.medico_apellido}
                    </p>
                  </div>
                  <span className={`badge badge-${ESTADO_BADGE[c.estado]?.[0] ?? "secondary"} text-[10px] flex-shrink-0 hidden sm:inline-flex`}>
                    {ESTADO_BADGE[c.estado]?.[1] ?? c.estado}
                  </span>
                </div>
                <div className="flex items-center gap-1 sm:flex-shrink-0 pl-16 sm:pl-0">
                  <Link href={`/paciente/comprobante?id=${c.id}`} className="btn btn-icon btn-sm btn-ghost text-sky-600" target="_blank" title="Comprobante">
                    <i className="fas fa-print"></i>
                  </Link>
                  <Link href={`/paciente/reagendar?id=${c.id}`} className="btn btn-icon btn-sm btn-ghost text-amber-600" title="Reagendar">
                    <i className="fas fa-exchange-alt"></i>
                  </Link>
                  {c.estado_id !== 3 && c.estado_id !== 4 && (
                    <button onClick={() => cancelar(c.id)} className="btn btn-icon btn-sm btn-ghost text-red-500 hover:bg-red-50" title="Cancelar">
                      <i className="fas fa-times"></i>
                    </button>
                  )}
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Historial reciente */}
      {historial.length > 0 && (
        <div className="card">
          <div className="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 className="text-sm font-bold text-slate-700">
              <i className="fas fa-history text-slate-400 mr-2"></i>Citas Anteriores
            </h2>
            <Link href="/paciente/historial" className="text-xs font-semibold text-sky-600 hover:text-sky-700 transition">
              Ver todo <i className="fas fa-arrow-right ml-1"></i>
            </Link>
          </div>
          <div className="divide-y divide-slate-50">
            {historial.map((c) => (
              <div key={c.id} className="px-5 py-3 flex items-center gap-3 text-sm hover:bg-slate-50 transition">
                <span className="text-slate-400 text-xs font-mono w-16 flex-shrink-0">
                  {c.fecha.split("-").reverse().slice(0, 2).join("/")}
                </span>
                <span className="text-slate-600 flex-1 truncate">{c.especialidad ?? "—"}</span>
                <span className={`badge badge-${ESTADO_BADGE[c.estado]?.[0] ?? "secondary"} text-[10px] flex-shrink-0`}>
                  {ESTADO_BADGE[c.estado]?.[1] ?? c.estado}
                </span>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
