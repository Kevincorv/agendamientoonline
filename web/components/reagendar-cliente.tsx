"use client";

import { useRouter } from "next/navigation";
import Link from "next/link";
import { useState } from "react";

type Win = typeof window & { showToast?: (m: string, t?: "success" | "error" | "info") => void };

interface Slot { hora: string | null; disponible: boolean; mensaje?: string }

export function ReagendarCliente({
  cita, medicos, especialidades, initialMedicoId, fecha, slots,
}: {
  cita: { id: number; fecha: string; hora: string; medico_id: number; medico_nombre: string; medico_apellido: string; especialidad: string; motivo: string; estado: string };
  medicos: Array<{ id: number; nombre: string; apellido: string; especialidad_id: number }>;
  especialidades: Array<{ id: number; nombre: string }>;
  initialMedicoId: number;
  fecha: string;
  slots: Slot[];
}) {
  const router = useRouter();
  const [medicoId, setMedicoId] = useState(initialMedicoId);
  const [loading, setLoading] = useState(false);

  function nav(params: Record<string, string | number>) {
    const sp = new URLSearchParams();
    sp.set("id", String(cita.id));
    if (medicoId) sp.set("medico_id", String(medicoId));
    if (fecha) sp.set("fecha", fecha);
    for (const [k, v] of Object.entries(params)) sp.set(k, String(v));
    router.push(`/paciente/reagendar?${sp.toString()}`);
  }

  async function guardar(e: React.FormEvent) {
    e.preventDefault();
    const form = e.currentTarget as HTMLFormElement;
    const fd = new FormData(form);
    setLoading(true);
    const r = await fetch("/api/paciente/citas/reagendar", { method: "POST", body: fd });
    setLoading(false);
    const res = await r.json();
    (window as Win).showToast?.(res.message ?? "OK", res.success ? "success" : "error");
    if (res.success) setTimeout(() => router.push("/paciente/dashboard"), 600);
  }

  const minDate = new Date().toISOString().slice(0, 10);

  return (
    <div className="max-w-3xl mx-auto px-4 py-6 fade-up">
      <div className="flex items-center justify-between mb-5">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-800">Reagendar cita</h1>
          <p className="text-sm text-slate-400">Elegí nueva fecha, hora y médico</p>
        </div>
        <Link href="/paciente/dashboard" className="btn btn-secondary btn-sm">
          <i className="fas fa-arrow-left"></i> Volver
        </Link>
      </div>

      <div className="card p-5 mb-5 bg-sky-50 border border-sky-100">
        <p className="text-xs text-sky-700 font-bold uppercase mb-1">Cita actual</p>
        <p className="text-sm text-slate-700">
          <strong>Dr. {cita.medico_nombre} {cita.medico_apellido}</strong> · {cita.especialidad} · {cita.fecha} {cita.hora}
        </p>
      </div>

      <form onSubmit={guardar} className="card p-6 space-y-5">
        <input type="hidden" name="cita_id" value={cita.id} />

        <div>
          <label className="block text-sm font-semibold text-slate-700 mb-1.5">Médico</label>
          <select
            name="medico_id"
            className="input-field"
            value={medicoId}
            onChange={(e) => {
              setMedicoId(Number(e.target.value));
              nav({ medico_id: e.target.value });
            }}
            required
          >
            {medicos.filter(m => m.id > 0).map((m) => {
              const esp = especialidades.find((e) => e.id === m.especialidad_id);
              return (
                <option key={m.id} value={m.id}>
                  Dr. {m.nombre} {m.apellido} {esp ? `(${esp.nombre})` : ""}
                </option>
              );
            })}
          </select>
        </div>

        <div>
          <label className="block text-sm font-semibold text-slate-700 mb-1.5">Nueva fecha</label>
          <input
            type="date"
            name="fecha"
            className="input-field md:w-64"
            min={minDate}
            defaultValue={fecha}
            onChange={(e) => e.target.value && nav({ fecha: e.target.value })}
            required
          />
        </div>

        {fecha && (
          <div>
            <label className="block text-sm font-semibold text-slate-700 mb-1.5">Horario disponible</label>
            {slots.length === 0 ? (
              <p className="text-xs text-slate-400">Cargá una fecha para ver horarios</p>
            ) : slots.length === 1 && slots[0].hora === null ? (
              <div className="bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-700">
                {slots[0].mensaje ?? "No hay horarios disponibles."}
              </div>
            ) : (
              <div className="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-7 gap-2">
                {slots.map((s, i) => (
                  <label
                    key={i}
                    className={`py-2 px-1 rounded-lg text-xs font-semibold text-center cursor-pointer transition ${
                      s.disponible ? "slot-available" : "slot-taken"
                    }`}
                  >
                    <input
                      type="radio"
                      name="hora"
                      value={s.hora ?? ""}
                      disabled={!s.disponible}
                      required
                      className="sr-only"
                    />
                    {s.hora}
                  </label>
                ))}
              </div>
            )}
          </div>
        )}

        <div className="flex gap-3 pt-2">
          <button type="submit" className="btn btn-primary flex-1" disabled={loading}>
            {loading ? <span className="spinner" /> : <><i className="fas fa-save"></i> Confirmar reagendado</>}
          </button>
          <Link href="/paciente/dashboard" className="btn btn-secondary flex-1">Cancelar</Link>
        </div>
      </form>
    </div>
  );
}
