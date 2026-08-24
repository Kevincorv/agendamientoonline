"use client";

import { useState } from "react";
import { useFormStatus } from "react-dom";
import { useRouter } from "next/navigation";

type Win = typeof window & {
  showToast?: (m: string, t?: "success" | "error" | "info") => void;
  openDeleteModal?: (action: string, text?: string) => void;
};

interface Feriado { id: number; fecha: string; motivo: string; activo: number }

export function FeriadosCliente({ feriados }: { feriados: Feriado[] }) {
  const router = useRouter();
  const [creating, setCreating] = useState(false);

  return (
    <>
      <div className="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
        <div>
          <h1 className="text-xl font-extrabold text-slate-800">Feriados</h1>
          <p className="text-sm text-slate-400">Días no laborables en los que no se atiende</p>
        </div>
        <button type="button" className="btn btn-primary" onClick={() => setCreating(true)}>
          <i className="fas fa-plus"></i> Nuevo Feriado
        </button>
      </div>

      <div className="card overflow-hidden fade-up">
        <div className="overflow-x-auto">
          <table className="data-table resp-table">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th className="text-right">Acc.</th>
              </tr>
            </thead>
            <tbody>
              {feriados.length === 0 ? (
                <tr>
                  <td colSpan={4}>
                    <div className="empty-state">
                      <i className="fas fa-calendar-times"></i>
                      <h3>Sin feriados registrados</h3>
                      <p>Agregá los días feriados del año</p>
                    </div>
                  </td>
                </tr>
              ) : (
                feriados.map((f) => (
                  <tr key={f.id}>
                    <td data-label="Fecha">
                      <span className="text-sm font-medium text-slate-700">{f.fecha.split("-").reverse().join("/")}</span>
                    </td>
                    <td data-label="Motivo">
                      <p className="text-sm text-slate-700">{f.motivo}</p>
                    </td>
                    <td data-label="Estado">
                      <span className={`badge badge-${f.activo ? "yellow" : "gray"}`}>
                        {f.activo ? "Activo" : "Inactivo"}
                      </span>
                    </td>
                    <td data-label="Acc.">
                      <div className="flex items-center justify-end gap-1">
                        <button
                          type="button"
                          className="btn btn-icon btn-sm btn-ghost text-amber-600"
                          onClick={async () => {
                            const r = await fetch("/api/admin/feriados/toggle", {
                              method: "POST",
                              body: new URLSearchParams({ id: String(f.id) }),
                            });
                            const res = await r.json();
                            (window as Win).showToast?.(res.success ? "Estado actualizado" : "Error", res.success ? "success" : "error");
                            if (res.success) router.refresh();
                          }}
                        >
                          <i className="fas fa-power-off"></i>
                        </button>
                        <button
                          type="button"
                          className="btn btn-icon btn-sm btn-ghost text-red-500"
                          onClick={() => {
                            (window as Win).openDeleteModal?.(
                              "/api/admin/feriados/eliminar",
                              `Se eliminará el feriado "${f.motivo}" del ${f.fecha}.`
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
                              hidden.value = String(f.id);
                            }
                          }}
                        >
                          <i className="fas fa-trash-alt"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {creating && (
        <div className="modal-overlay active" onClick={(e) => { if (e.target === e.currentTarget) setCreating(false); }}>
          <div className="modal-box" style={{ maxWidth: 460 }}>
            <div className="flex items-center justify-between px-5 py-4 border-b border-slate-100">
              <h3 className="text-base font-extrabold text-slate-800">Nuevo Feriado</h3>
              <button type="button" onClick={() => setCreating(false)} className="text-slate-400 hover:text-slate-600">
                <i className="fas fa-times"></i>
              </button>
            </div>
            <form
              action={async (fd) => {
                const r = await fetch("/api/admin/feriados/crear", { method: "POST", body: fd });
                const res = await r.json();
                (window as Win).showToast?.(res.message ?? "Resultado", res.success ? "success" : "error");
                if (res.success) {
                  setCreating(false);
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
                <label className="block text-xs font-semibold text-slate-500 mb-1">Motivo *</label>
                <input type="text" name="motivo" required className="input-field" placeholder="Ej: Día de la Independencia" />
              </div>
              <Submit />
            </form>
          </div>
        </div>
      )}
    </>
  );
}

function Submit() {
  const { pending } = useFormStatus();
  return (
    <button type="submit" className="btn btn-primary w-full" disabled={pending}>
      {pending ? <span className="spinner" /> : <><i className="fas fa-save"></i> Guardar</>}
    </button>
  );
}
