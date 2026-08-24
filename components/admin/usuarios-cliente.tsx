"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { useFormStatus } from "react-dom";

type Win = typeof window & {
  showToast?: (m: string, t?: "success" | "error" | "info") => void;
  openDeleteModal?: (action: string, text?: string) => void;
};

interface Usuario {
  id: number; nombre: string; apellido: string; email: string;
  rol_id: number; rol_nombre: string; activo: number;
  login_attempts: number; locked_until: string | null; last_login: string | null;
}
interface Rol { id: number; nombre: string }

export function UsuariosCliente({
  currentUserId,
  usuarios,
  roles,
}: {
  currentUserId: number;
  usuarios: Usuario[];
  roles: Rol[];
}) {
  const router = useRouter();
  const [editing, setEditing] = useState<Usuario | null>(null);
  const [creating, setCreating] = useState(false);

  return (
    <>
      <div className="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
        <div>
          <h1 className="text-xl font-extrabold text-slate-800">Usuarios</h1>
          <p className="text-sm text-slate-400">Administradores, recepcionistas, médicos y pacientes</p>
        </div>
        <button type="button" className="btn btn-primary" onClick={() => setCreating(true)}>
          <i className="fas fa-plus"></i> Nuevo Usuario
        </button>
      </div>

      <div className="card overflow-hidden fade-up">
        <div className="overflow-x-auto">
          <table className="data-table resp-table">
            <thead>
              <tr>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Último acceso</th>
                <th className="text-right">Acc.</th>
              </tr>
            </thead>
            <tbody>
              {usuarios.length === 0 ? (
                <tr>
                  <td colSpan={6}>
                    <div className="empty-state">
                      <i className="fas fa-users-cog"></i>
                      <h3>Sin usuarios</h3>
                    </div>
                  </td>
                </tr>
              ) : (
                usuarios.map((u) => {
                  const locked = u.locked_until && new Date(u.locked_until) > new Date();
                  return (
                    <tr key={u.id} className={u.activo ? "" : "opacity-60"}>
                      <td data-label="Usuario">
                        <div className="flex items-center gap-2.5">
                          <div
                            className="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold"
                            style={{ background: "linear-gradient(135deg,#7c3aed,#a78bfa)" }}
                          >
                            {u.nombre.charAt(0).toUpperCase()}{u.apellido.charAt(0).toUpperCase()}
                          </div>
                          <div>
                            <p className="font-semibold text-slate-800 text-sm">{u.nombre} {u.apellido}</p>
                            {locked && <p className="text-[10px] text-red-500">Bloqueado</p>}
                          </div>
                        </div>
                      </td>
                      <td data-label="Email">
                        <p className="text-xs text-slate-600">{u.email}</p>
                      </td>
                      <td data-label="Rol">
                        <span className="badge badge-sky">{u.rol_nombre}</span>
                      </td>
                      <td data-label="Estado">
                        <span className={`badge badge-${u.activo ? "green" : "red"}`}>
                          {u.activo ? "Activo" : "Inactivo"}
                        </span>
                      </td>
                      <td data-label="Último acceso">
                        <p className="text-xs text-slate-500">{u.last_login ?? "Nunca"}</p>
                      </td>
                      <td data-label="Acc.">
                        <div className="flex items-center justify-end gap-1">
                          {locked && (
                            <button
                              className="btn btn-icon btn-sm btn-ghost text-emerald-600"
                              title="Desbloquear"
                              onClick={async () => {
                                const r = await fetch("/api/admin/usuarios/desbloquear", {
                                  method: "POST",
                                  body: new URLSearchParams({ id: String(u.id) }),
                                });
                                const res = await r.json();
                                (window as Win).showToast?.(res.message ?? "OK", res.success ? "success" : "error");
                                if (res.success) router.refresh();
                              }}
                            >
                              <i className="fas fa-unlock"></i>
                            </button>
                          )}
                          <button className="btn btn-icon btn-sm btn-ghost text-amber-600" onClick={() => setEditing(u)} title="Editar">
                            <i className="fas fa-pen"></i>
                          </button>
                          {u.id !== currentUserId && (
                            <button
                              className="btn btn-icon btn-sm btn-ghost text-red-500"
                              onClick={() => {
                                (window as Win).openDeleteModal?.(
                                  "/api/admin/usuarios/eliminar",
                                  `Se eliminará al usuario ${u.nombre} ${u.apellido}.`
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
                                  hidden.value = String(u.id);
                                }
                              }}
                              title="Eliminar"
                            >
                              <i className="fas fa-trash-alt"></i>
                            </button>
                          )}
                        </div>
                      </td>
                    </tr>
                  );
                })
              )}
            </tbody>
          </table>
        </div>
      </div>

      {(creating || editing) && (
        <div className="modal-overlay active" onClick={(e) => { if (e.target === e.currentTarget) { setCreating(false); setEditing(null); } }}>
          <div className="modal-box" style={{ maxWidth: 480 }}>
            <div className="flex items-center justify-between px-5 py-4 border-b border-slate-100">
              <h3 className="text-base font-extrabold text-slate-800">{editing ? "Editar Usuario" : "Nuevo Usuario"}</h3>
              <button type="button" onClick={() => { setCreating(false); setEditing(null); }} className="text-slate-400 hover:text-slate-600">
                <i className="fas fa-times"></i>
              </button>
            </div>
            <form
              action={async (fd) => {
                const endpoint = editing ? "/api/admin/usuarios/editar" : "/api/admin/usuarios/crear";
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
                <label className="block text-xs font-semibold text-slate-500 mb-1">Email *</label>
                <input type="email" name="email" required defaultValue={editing?.email ?? ""} className="input-field" />
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">Rol *</label>
                <select name="rol_id" required defaultValue={editing?.rol_id ?? ""} className="input-field">
                  <option value="">Seleccionar</option>
                  {roles.map((r) => <option key={r.id} value={r.id}>{r.nombre}</option>)}
                </select>
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">
                  Contraseña {editing && <span className="text-[10px] text-slate-400">(dejar vacío para no cambiar)</span>}
                </label>
                <input type="password" name="password" minLength={6} className="input-field" placeholder={editing ? "••••••" : "Mínimo 6 caracteres"} />
              </div>
              <Submit label={editing ? "Guardar cambios" : "Crear usuario"} />
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
