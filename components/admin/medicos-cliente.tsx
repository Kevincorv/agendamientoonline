"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { useFormStatus } from "react-dom";

type Win = typeof window & {
  showToast?: (m: string, t?: "success" | "error" | "info") => void;
  openDeleteModal?: (action: string, text?: string) => void;
};

interface Medico {
  id: number; nombre: string; apellido: string; email: string; telefono: string;
  especialidad_id: number; especialidad_nombre: string; matricula: string;
  descripcion: string; disponible: number; activo: number;
}
interface Especialidad { id: number; nombre: string }

export function MedicosCliente({ medicos, especialidades }: { medicos: Medico[]; especialidades: Especialidad[] }) {
  const router = useRouter();
  const [editing, setEditing] = useState<Medico | null>(null);
  const [creating, setCreating] = useState(false);

  return (
    <>
      <div className="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
        <div>
          <h1 className="text-xl font-extrabold text-slate-800">Médicos</h1>
          <p className="text-sm text-slate-400">Gestión de profesionales</p>
        </div>
        <button type="button" className="btn btn-primary" onClick={() => setCreating(true)}>
          <i className="fas fa-plus"></i> Nuevo Médico
        </button>
      </div>

      <div className="card overflow-hidden fade-up">
        <div className="overflow-x-auto">
          <table className="data-table resp-table">
            <thead>
              <tr>
                <th>Médico</th>
                <th>Especialidad</th>
                <th>Contacto</th>
                <th>Estado</th>
                <th>Disponible</th>
                <th className="text-right">Acc.</th>
              </tr>
            </thead>
            <tbody>
              {medicos.length === 0 ? (
                <tr>
                  <td colSpan={6}>
                    <div className="empty-state">
                      <i className="fas fa-user-md"></i>
                      <h3>Sin médicos</h3>
                      <p>Agregá el primer médico</p>
                    </div>
                  </td>
                </tr>
              ) : (
                medicos.map((m) => (
                  <tr key={m.id} className={m.activo ? "" : "opacity-60"}>
                    <td data-label="Médico">
                      <div className="flex items-center gap-2.5">
                        <div
                          className="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold"
                          style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
                        >
                          {m.nombre.charAt(0).toUpperCase()}{m.apellido.charAt(0).toUpperCase()}
                        </div>
                        <div>
                          <p className="font-semibold text-slate-800 text-sm">Dr. {m.nombre} {m.apellido}</p>
                          <p className="text-[10px] text-slate-400">{m.matricula || "Sin matrícula"}</p>
                        </div>
                      </div>
                    </td>
                    <td data-label="Especialidad">
                      <span className="text-sm text-slate-600">{m.especialidad_nombre}</span>
                    </td>
                    <td data-label="Contacto">
                      <p className="text-xs text-slate-600">{m.email || "—"}</p>
                      <p className="text-xs text-slate-400">{m.telefono || "—"}</p>
                    </td>
                    <td data-label="Estado">
                      <span className={`badge badge-${m.activo ? "green" : "red"}`}>
                        {m.activo ? "Activo" : "Inactivo"}
                      </span>
                    </td>
                    <td data-label="Disponible">
                      <button
                        onClick={async () => {
                          const r = await fetch("/api/admin/medicos/toggle", {
                            method: "POST",
                            body: new URLSearchParams({ id: String(m.id) }),
                          });
                          const res = await r.json();
                          (window as Win).showToast?.(res.message ?? "OK", res.success ? "success" : "error");
                          if (res.success) router.refresh();
                        }}
                        className="relative inline-flex items-center cursor-pointer"
                      >
                        <span
                          className="w-9 h-5 rounded-full transition"
                          style={{ background: m.disponible ? "#10b981" : "#cbd5e1" }}
                        >
                          <span
                            className="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"
                            style={{ transform: m.disponible ? "translateX(16px)" : "none" }}
                          />
                        </span>
                      </button>
                    </td>
                    <td data-label="Acc.">
                      <div className="flex items-center justify-end gap-1">
                        <button type="button" className="btn btn-icon btn-sm btn-ghost text-amber-600" onClick={() => setEditing(m)}>
                          <i className="fas fa-pen"></i>
                        </button>
                        {m.activo && (
                          <button
                            type="button"
                            className="btn btn-icon btn-sm btn-ghost text-red-500"
                            onClick={() => {
                              (window as Win).openDeleteModal?.(
                                "/api/admin/medicos/eliminar",
                                `Se desactivará el médico Dr. ${m.nombre} ${m.apellido}.`
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
                                hidden.value = String(m.id);
                              }
                            }}
                          >
                            <i className="fas fa-trash-alt"></i>
                          </button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {(creating || editing) && (
        <div className="modal-overlay active" onClick={(e) => { if (e.target === e.currentTarget) { setCreating(false); setEditing(null); } }}>
          <div className="modal-box" style={{ maxWidth: 540 }}>
            <div className="flex items-center justify-between px-5 py-4 border-b border-slate-100">
              <h3 className="text-base font-extrabold text-slate-800">{editing ? "Editar Médico" : "Nuevo Médico"}</h3>
              <button type="button" onClick={() => { setCreating(false); setEditing(null); }} className="text-slate-400 hover:text-slate-600">
                <i className="fas fa-times"></i>
              </button>
            </div>
            <form
              action={async (fd) => {
                const endpoint = editing ? "/api/admin/medicos/editar" : "/api/admin/medicos/crear";
                if (editing) fd.append("id", String(editing.id));
                const r = await fetch(endpoint, { method: "POST", body: fd });
                const res = await r.json();
                (window as Win).showToast?.(res.message ?? "OK", res.success ? "success" : "error");
                if (res.success) {
                  setCreating(false);
                  setEditing(null);
                  router.refresh();
                }
              }}
              className="p-5 space-y-4"
            >
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-xs font-semibold text-slate-500 mb-1">Nombre *</label>
                  <input type="text" name="nombre" required defaultValue={editing?.nombre ?? ""} className="input-field" />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-slate-500 mb-1">Apellido *</label>
                  <input type="text" name="apellido" required defaultValue={editing?.apellido ?? ""} className="input-field" />
                </div>
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">Especialidad *</label>
                <select name="especialidad_id" required defaultValue={editing?.especialidad_id ?? ""} className="input-field">
                  <option value="">Seleccionar</option>
                  {especialidades.map((e) => <option key={e.id} value={e.id}>{e.nombre}</option>)}
                </select>
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-xs font-semibold text-slate-500 mb-1">Email</label>
                  <input type="email" name="email" defaultValue={editing?.email ?? ""} className="input-field" />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-slate-500 mb-1">Teléfono</label>
                  <input type="tel" name="telefono" defaultValue={editing?.telefono ?? ""} className="input-field" />
                </div>
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-xs font-semibold text-slate-500 mb-1">Matrícula</label>
                  <input type="text" name="matricula" defaultValue={editing?.matricula ?? ""} className="input-field" />
                </div>
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">Descripción</label>
                <textarea name="descripcion" rows={2} defaultValue={editing?.descripcion ?? ""} className="input-field resize-none" />
              </div>
              <Submit label={editing ? "Guardar cambios" : "Crear médico"} />
            </form>
          </div>
        </div>
      )}
    </>
  );
}

function Submit({ label }: { label: string }) {
  const { pending } = useFormStatus();
  return (
    <button type="submit" className="btn btn-primary w-full" disabled={pending}>
      {pending ? <span className="spinner" /> : <><i className="fas fa-save"></i> {label}</>}
    </button>
  );
}
