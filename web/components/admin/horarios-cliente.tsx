"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { useFormStatus } from "react-dom";

type Win = typeof window & {
  showToast?: (m: string, t?: "success" | "error" | "info") => void;
  openDeleteModal?: (action: string, text?: string) => void;
};

interface Medico { id: number; nombre: string; apellido: string; especialidad_nombre: string | null }
interface Horario { id: number; dia_semana: number; hora_inicio: string; hora_fin: string; intervalo_minutos: number }
interface Bloqueo { id: number; fecha: string; motivo: string | null }

const DIAS = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];

export function HorariosCliente({
  medicos,
  horarios,
  bloqueos,
}: {
  medicos: Medico[];
  horarios: Record<number, Horario[]>;
  bloqueos: Record<number, Bloqueo[]>;
}) {
  const router = useRouter();
  const [selected, setSelected] = useState<number | null>(medicos[0]?.id ?? null);
  const [bloqueando, setBloqueando] = useState(false);

  const med = medicos.find((m) => m.id === selected) ?? null;
  const blocks = med ? horarios[med.id] ?? [] : [];
  const blocksBloq = med ? bloqueos[med.id] ?? [] : [];

  return (
    <>
      <div className="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
        <div>
          <h1 className="text-xl font-extrabold text-slate-800">Horarios</h1>
          <p className="text-sm text-slate-400">Bloques de atención y bloqueos por fecha</p>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div className="card overflow-hidden fade-up">
          <div className="px-4 py-3 border-b border-slate-100">
            <h2 className="text-sm font-bold text-slate-700">Médicos</h2>
          </div>
          <div className="max-h-[600px] overflow-y-auto">
            {medicos.map((m) => (
              <button
                key={m.id}
                onClick={() => setSelected(m.id)}
                className={`w-full text-left px-4 py-3 border-b border-slate-100 hover:bg-slate-50 transition flex items-center gap-2.5 ${
                  selected === m.id ? "bg-sky-50" : ""
                }`}
              >
                <div
                  className="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                  style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
                >
                  {m.nombre.charAt(0).toUpperCase()}
                </div>
                <div className="min-w-0 flex-1">
                  <p className="font-semibold text-slate-800 text-sm truncate">Dr. {m.nombre} {m.apellido}</p>
                  <p className="text-xs text-slate-400 truncate">{m.especialidad_nombre ?? "—"}</p>
                </div>
              </button>
            ))}
          </div>
        </div>

        <div className="md:col-span-2 space-y-4">
          {med ? (
            <>
              <div className="card p-5 fade-up">
                <div className="flex items-center justify-between mb-4">
                  <h2 className="text-sm font-bold text-slate-700">
                    Bloques de Dr. {med.nombre} {med.apellido}
                  </h2>
                  <span className="text-[10px] text-slate-400">{blocks.length} bloques</span>
                </div>
                {blocks.length === 0 ? (
                  <div className="empty-state">
                    <i className="fas fa-clock"></i>
                    <h3>Sin horarios</h3>
                    <p>Este médico no tiene bloques configurados</p>
                  </div>
                ) : (
                  <div className="space-y-2">
                    {blocks.map((b) => (
                      <div key={b.id} className="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                        <div className="flex items-center gap-3">
                          <span className="badge badge-blue">{DIAS[b.dia_semana]}</span>
                          <span className="font-mono text-sm">{b.hora_inicio} — {b.hora_fin}</span>
                          <span className="text-[10px] text-slate-400">cada {b.intervalo_minutos} min</span>
                        </div>
                        <button
                          className="btn btn-icon btn-sm btn-ghost text-red-500"
                          onClick={() => {
                            (window as Win).openDeleteModal?.(
                              "/api/admin/horarios/eliminar",
                              `Se eliminará el bloque del ${DIAS[b.dia_semana]} ${b.hora_inicio}-${b.hora_fin}.`
                            );
                            const form = document.getElementById("deleteModalForm") as HTMLFormElement | null;
                            if (form) {
                              let hidden = form.querySelector('input[name="id"]') as HTMLInputElement | null;
                              if (!hidden) {
                                hidden = document.createElement("input");
                                hidden.type = "hidden";
                                hidden.name = "id";
                                form.appendChild(hidden);
                              }
                              hidden.value = String(b.id);
                            }
                          }}
                        >
                          <i className="fas fa-trash"></i>
                        </button>
                      </div>
                    ))}
                  </div>
                )}
                <NuevoBloqueForm medicoId={med.id} onCreated={() => router.refresh()} />
              </div>

              <div className="card p-5 fade-up" style={{ animationDelay: ".05s" }}>
                <div className="flex items-center justify-between mb-4">
                  <h2 className="text-sm font-bold text-slate-700">Bloqueos por fecha</h2>
                  <button className="btn btn-secondary btn-sm" onClick={() => setBloqueando(true)}>
                    <i className="fas fa-ban"></i> Bloquear fecha
                  </button>
                </div>
                {blocksBloq.length === 0 ? (
                  <p className="text-xs text-slate-400 text-center py-3">Sin bloqueos</p>
                ) : (
                  <div className="space-y-2">
                    {blocksBloq.map((b) => (
                      <div key={b.id} className="flex items-center justify-between p-3 rounded-lg bg-red-50 border border-red-100">
                        <div>
                          <p className="text-sm font-semibold text-slate-800">{b.fecha}</p>
                          {b.motivo && <p className="text-xs text-slate-500">{b.motivo}</p>}
                        </div>
                        <button
                          className="btn btn-icon btn-sm btn-ghost text-red-500"
                          onClick={async () => {
                            const r = await fetch("/api/admin/horarios/desbloquear", {
                              method: "POST",
                              body: new URLSearchParams({ id: String(b.id) }),
                            });
                            const res = await r.json();
                            (window as Win).showToast?.(res.message ?? "OK", res.success ? "success" : "error");
                            if (res.success) router.refresh();
                          }}
                        >
                          <i className="fas fa-unlock"></i>
                        </button>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </>
          ) : (
            <div className="card p-10 text-center text-slate-400">
              <i className="fas fa-user-md text-3xl mb-2 block"></i>
              Seleccioná un médico para gestionar sus horarios
            </div>
          )}
        </div>
      </div>

      {bloqueando && med && (
        <div className="modal-overlay active" onClick={(e) => { if (e.target === e.currentTarget) setBloqueando(false); }}>
          <div className="modal-box" style={{ maxWidth: 420 }}>
            <div className="flex items-center justify-between px-5 py-4 border-b border-slate-100">
              <h3 className="text-base font-extrabold text-slate-800">Bloquear fecha</h3>
              <button type="button" onClick={() => setBloqueando(false)} className="text-slate-400 hover:text-slate-600">
                <i className="fas fa-times"></i>
              </button>
            </div>
            <form
              action={async (fd) => {
                fd.append("medico_id", String(med.id));
                const r = await fetch("/api/admin/horarios/bloquear", { method: "POST", body: fd });
                const res = await r.json();
                (window as Win).showToast?.(res.message ?? "OK", res.success ? "success" : "error");
                if (res.success) {
                  setBloqueando(false);
                  router.refresh();
                }
              }}
              className="p-5 space-y-4"
            >
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">Fecha *</label>
                <input type="date" name="fecha" required className="input-field" />
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">Motivo</label>
                <input type="text" name="motivo" className="input-field" placeholder="Ej: Feriado, licencia" />
              </div>
              <Submit />
            </form>
          </div>
        </div>
      )}
    </>
  );
}

function NuevoBloqueForm({ medicoId, onCreated }: { medicoId: number; onCreated: () => void }) {
  const [open, setOpen] = useState(false);
  return (
    <div className="mt-4 pt-4 border-t border-slate-100">
      {open ? (
        <form
          action={async (fd) => {
            fd.append("medico_id", String(medicoId));
            const r = await fetch("/api/admin/horarios/crear", { method: "POST", body: fd });
            const res = await r.json();
            (window as Win).showToast?.(res.message ?? "OK", res.success ? "success" : "error");
            if (res.success) {
              setOpen(false);
              onCreated();
            }
          }}
          className="grid grid-cols-1 sm:grid-cols-5 gap-2"
        >
          <select name="dia_semana" required className="input-field sm:col-span-1">
            {DIAS.map((d, i) => <option key={i} value={i}>{d}</option>)}
          </select>
          <input type="time" name="hora_inicio" required className="input-field" />
          <input type="time" name="hora_fin" required className="input-field" />
          <input type="number" name="duracion" min={5} max={120} step={5} defaultValue={30} className="input-field" placeholder="min" />
          <div className="flex gap-2">
            <Submit />
            <button type="button" className="btn btn-secondary" onClick={() => setOpen(false)}>
              <i className="fas fa-times"></i>
            </button>
          </div>
        </form>
      ) : (
        <button type="button" className="btn btn-secondary btn-sm" onClick={() => setOpen(true)}>
          <i className="fas fa-plus"></i> Agregar bloque
        </button>
      )}
    </div>
  );
}

function Submit() {
  const { pending } = useFormStatus();
  return (
    <button type="submit" className="btn btn-primary" disabled={pending}>
      {pending ? <span className="spinner" /> : <><i className="fas fa-save"></i> Guardar</>}
    </button>
  );
}
