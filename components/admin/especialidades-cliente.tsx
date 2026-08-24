"use client";

import { useState } from "react";
import { useFormStatus } from "react-dom";
import { crearEspAction, editarEspAction, eliminarEspAction } from "@/app/admin/especialidades/actions";

type Win = typeof window & {
  showToast?: (m: string, t?: "success" | "error" | "info") => void;
  openDeleteModal?: (action: string, text?: string) => void;
  closeDeleteModal?: () => void;
};

interface Especialidad {
  id: number;
  nombre: string;
  descripcion: string;
  icono: string;
  activo: number;
}

const ICONOS = [
  "fa-stethoscope", "fa-heartbeat", "fa-tooth", "fa-eye",
  "fa-brain", "fa-bone", "fa-lungs", "fa-baby",
  "fa-spa", "fa-procedures", "fa-ambulance", "fa-notes-medical",
];

export function EspecialidadesCliente({ especialidades }: { especialidades: Especialidad[] }) {
  const [editing, setEditing] = useState<Especialidad | null>(null);
  const [creating, setCreating] = useState(false);

  return (
    <>
      <div className="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
        <div>
          <h1 className="text-xl font-extrabold text-slate-800">Especialidades</h1>
          <p className="text-sm text-slate-400">Gestión de especialidades médicas</p>
        </div>
        <button type="button" className="btn btn-primary" onClick={() => setCreating(true)}>
          <i className="fas fa-plus"></i> Nueva Especialidad
        </button>
      </div>

      <div className="card overflow-hidden fade-up">
        <div className="overflow-x-auto">
          <table className="data-table resp-table">
            <thead>
              <tr>
                <th>Icono</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th className="text-right">Acc.</th>
              </tr>
            </thead>
            <tbody>
              {especialidades.length === 0 ? (
                <tr>
                  <td colSpan={5}>
                    <div className="empty-state">
                      <i className="fas fa-stethoscope"></i>
                      <h3>Sin especialidades</h3>
                      <p>Creá la primera especialidad</p>
                    </div>
                  </td>
                </tr>
              ) : (
                especialidades.map((e) => (
                  <tr key={e.id}>
                    <td data-label="Icono">
                      <div
                        className="w-9 h-9 rounded-xl flex items-center justify-center"
                        style={{ background: "linear-gradient(135deg,#e0f2fe,#bae6fd)" }}
                      >
                        <i className={`fas ${e.icono} text-sky-600`}></i>
                      </div>
                    </td>
                    <td data-label="Nombre">
                      <p className="font-semibold text-slate-800 text-sm">{e.nombre}</p>
                    </td>
                    <td data-label="Descripción">
                      <p className="text-xs text-slate-500 max-w-md truncate">{e.descripcion || "—"}</p>
                    </td>
                    <td data-label="Estado">
                      <span className={`badge badge-${e.activo ? "green" : "red"}`}>
                        {e.activo ? "Activa" : "Inactiva"}
                      </span>
                    </td>
                    <td data-label="Acc.">
                      <div className="flex items-center justify-end gap-1">
                        <button type="button" className="btn btn-icon btn-sm btn-ghost text-amber-600" onClick={() => setEditing(e)} title="Editar">
                          <i className="fas fa-pen"></i>
                        </button>
                        {e.activo ? (
                          <button
                            type="button"
                            className="btn btn-icon btn-sm btn-ghost text-red-500"
                            onClick={() => {
                              (window as Win).openDeleteModal?.(
                                `/api/admin/especialidades/eliminar`,
                                `Se desactivará la especialidad "${e.nombre}".`
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
                                hidden.value = String(e.id);
                              }
                            }}
                            title="Desactivar"
                          >
                            <i className="fas fa-trash-alt"></i>
                          </button>
                        ) : null}
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
          <div className="modal-box" style={{ maxWidth: 500 }}>
            <div className="flex items-center justify-between px-5 py-4 border-b border-slate-100">
              <h3 className="text-base font-extrabold text-slate-800">
                {editing ? "Editar Especialidad" : "Nueva Especialidad"}
              </h3>
              <button type="button" onClick={() => { setCreating(false); setEditing(null); }} className="text-slate-400 hover:text-slate-600 transition">
                <i className="fas fa-times"></i>
              </button>
            </div>
            <form
              action={async (fd) => {
                if (editing) await editarEspAction(fd);
                else await crearEspAction(fd);
                setCreating(false);
                setEditing(null);
                (window as Win).showToast?.(editing ? "Especialidad actualizada" : "Especialidad creada", "success");
              }}
              className="p-5 space-y-4"
            >
              {editing && <input type="hidden" name="id" value={editing.id} />}
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">Nombre *</label>
                <input
                  type="text"
                  name="nombre"
                  required
                  defaultValue={editing?.nombre ?? ""}
                  className="input-field"
                />
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">Descripción</label>
                <textarea name="descripcion" rows={3} defaultValue={editing?.descripcion ?? ""} className="input-field resize-none" />
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">Icono</label>
                <select name="icono" defaultValue={editing?.icono ?? "fa-stethoscope"} className="input-field">
                  {ICONOS.map((i) => (
                    <option key={i} value={i}>{i}</option>
                  ))}
                </select>
                <p className="text-[10px] text-slate-400 mt-1">FontAwesome class (fa-*)</p>
              </div>
              <SubmitButton label={editing ? "Guardar cambios" : "Crear"} />
              <p className="text-[11px] text-slate-400">
                <i className="fas fa-info-circle text-sky-500"></i> Al crear se genera automáticamente un médico por defecto con horarios.
              </p>
            </form>
          </div>
        </div>
      )}
    </>
  );
}

function SubmitButton({ label }: { label: string }) {
  const { pending } = useFormStatus();
  return (
    <button type="submit" className="btn btn-primary w-full" disabled={pending}>
      {pending ? <span className="spinner" /> : <><i className="fas fa-save"></i> {label}</>}
    </button>
  );
}
