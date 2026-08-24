"use client";

import { useRouter, useSearchParams } from "next/navigation";
import { useState } from "react";

type Win = typeof window & {
  showToast?: (m: string, t?: "success" | "error" | "info") => void;
  openDeleteModal?: (action: string, text?: string) => void;
  closeDeleteModal?: () => void;
};

interface Cita {
  id: number;
  nombre_paciente: string;
  telefono: string;
  medico_nombre: string;
  medico_apellido: string;
  especialidad: string;
  fecha: string;
  hora: string;
  estado: string;
  estado_id: number;
  medico_id: number;
}
interface Medico { id: number; nombre: string; apellido: string }
interface Paginacion { total: number; pagina: number; paginas: number; porPagina: number }
interface Filtros { fecha?: string; medico_id?: number; estado_id?: number; q?: string; porPagina?: number; pagina?: number }

const ESTADO_LABEL: Record<string, string> = {
  pendiente: "warning", confirmada: "info", cancelada: "danger", atendida: "success", "no asistio": "secondary",
};

export function CitasCliente({
  citas,
  medicos,
  paginacion,
  filtros,
}: {
  citas: Cita[];
  medicos: Medico[];
  paginacion: Paginacion;
  filtros: Filtros;
}) {
  const router = useRouter();
  const sp = useSearchParams();
  const [edit, setEdit] = useState<{ id: number; medicoId: number; fecha: string; hora: string } | null>(null);
  const [loadingDet, setLoadingDet] = useState<number | null>(null);
  const [detalle, setDetalle] = useState<Record<string, unknown> | null>(null);

  function changePage(n: number) {
    const params = new URLSearchParams(sp.toString());
    params.set("pagina", String(n));
    router.push(`/admin/citas?${params.toString()}`);
  }
  function changePorPagina(n: number) {
    const params = new URLSearchParams(sp.toString());
    params.set("porPagina", String(n));
    params.set("pagina", "1");
    router.push(`/admin/citas?${params.toString()}`);
  }

  async function cambiarEstado(id: number, estadoId: number) {
    const fd = new FormData();
    fd.append("cita_id", String(id));
    fd.append("estado_id", String(estadoId));
    const r = await fetch("/api/admin/citas/estado", { method: "POST", body: fd });
    const res = await r.json();
    (window as Win).showToast?.(res.message ?? "Estado actualizado", res.success ? "success" : "error");
    if (res.success) router.refresh();
  }

  async function verDetalle(id: number) {
    setLoadingDet(id);
    setDetalle(null);
    const r = await fetch(`/api/admin/citas/detalle?id=${id}`);
    const res = await r.json();
    setLoadingDet(null);
    if (!res.success) {
      (window as Win).showToast?.(res.message ?? "Error", "error");
      return;
    }
    setDetalle(res.cita);
  }

  function fmtFecha(f: string) {
    const [y, m, d] = f.split("-");
    return d && m && y ? `${d}/${m}/${y}` : f;
  }

  function openEdit(c: Cita) {
    setEdit({ id: c.id, medicoId: c.medico_id, fecha: c.fecha, hora: c.hora });
    setTimeout(() => cargarSlotsEdit(c.medico_id, c.fecha, c.hora), 50);
  }

  async function cargarSlotsEdit(medicoId: number, fecha: string, currentHora?: string) {
    const sel = document.getElementById("editHora") as HTMLSelectElement | null;
    if (!sel) return;
    sel.innerHTML = '<option value="">Cargando horarios...</option>';
    sel.disabled = true;
    const r = await fetch(`/api/admin/slots?medico_id=${medicoId}&fecha=${fecha}`);
    const res = await r.json();
    sel.innerHTML = "";
    if (!res.success || !res.slots || res.slots.length === 0) {
      sel.innerHTML = '<option value="">No hay horarios disponibles</option>';
      return;
    }
    const slots = res.slots as Array<{ hora: string | null; disponible: boolean; mensaje?: string }>;
    if (slots.length === 1 && slots[0].hora === null) {
      sel.innerHTML = `<option value="">${esc(slots[0].mensaje ?? "No disponible")}</option>`;
      return;
    }
    let hayDisponibles = false;
    slots.forEach((s) => {
      const opt = document.createElement("option");
      opt.value = s.hora ?? "";
      opt.textContent = s.hora ?? "";
      if (!s.disponible) {
        opt.disabled = true;
        opt.textContent += " (ocupado)";
      } else {
        hayDisponibles = true;
      }
      sel.appendChild(opt);
    });
    if (currentHora) sel.value = currentHora;
    if (!hayDisponibles) sel.innerHTML = '<option value="">Todos los horarios están ocupados</option>';
    sel.disabled = false;
  }

  async function guardarEdit(e: React.FormEvent) {
    e.preventDefault();
    if (!edit) return;
    const form = e.currentTarget as HTMLFormElement;
    const fd = new FormData(form);
    const r = await fetch("/api/admin/citas/cambiar-medico", { method: "POST", body: fd });
    const res = await r.json();
    (window as Win).showToast?.(res.message ?? "Resultado", res.success ? "success" : "error");
    if (res.success) {
      setEdit(null);
      setTimeout(() => router.refresh(), 600);
    }
  }

  function deleteCita(c: Cita) {
    (window as Win).openDeleteModal?.(
      `/api/admin/citas/eliminar`,
      `Se eliminará la cita de ${c.nombre_paciente} — ${fmtFecha(c.fecha)} a las ${c.hora}.`
    );
    const form = document.getElementById("deleteModalForm") as HTMLFormElement | null;
    if (form) {
      // Agregar input hidden con el id (lo limpiamos al cerrar)
      let hidden = form.querySelector('input[name="id"]') as HTMLInputElement | null;
      if (!hidden) {
        hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.name = "id";
        form.appendChild(hidden);
      }
      hidden.value = String(c.id);
    }
  }

  return (
    <>
      <div className="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
        <div>
          <h1 className="text-xl font-extrabold text-slate-800">Citas</h1>
          <p className="text-sm text-slate-400">Gestión de citas médicas</p>
        </div>
        <div className="flex items-center gap-2">
          <a href={`/api/admin/citas/exportar?${new URLSearchParams(Object.entries(filtros).map(([k, v]) => [k, String(v ?? "")])).toString()}`} className="btn btn-secondary btn-sm">
            <i className="fas fa-file-export"></i> Exportar
          </a>
          <button type="button" className="btn btn-secondary btn-sm" onClick={() => window.print()}>
            <i className="fas fa-print"></i> Imprimir
          </button>
        </div>
      </div>

      <form method="GET" action="/admin/citas" className="card p-4 sm:p-5 mb-6 fade-up">
        <div className="flex flex-wrap gap-3 sm:gap-4 items-end">
          <div className="flex-1 min-w-[200px]">
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">Buscar paciente</label>
            <div className="search-wrap" style={{ maxWidth: "100%" }}>
              <i className="fas fa-search"></i>
              <input type="text" name="q" placeholder="Nombre o teléfono..." defaultValue={filtros.q ?? ""} className="!pl-8 !bg-white" />
            </div>
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">Fecha</label>
            <input type="date" name="fecha" defaultValue={filtros.fecha ?? ""} className="input-field" />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5">Estado</label>
            <select name="estado_id" className="input-field" defaultValue={filtros.estado_id ?? ""}>
              <option value="">Todos</option>
              <option value="1">Pendiente</option>
              <option value="2">Confirmada</option>
              <option value="3">Cancelada</option>
              <option value="4">Atendida</option>
            </select>
          </div>
          <div className="flex gap-2">
            <button type="submit" className="btn btn-primary"><i className="fas fa-search"></i> Filtrar</button>
            <a href="/admin/citas" className="btn btn-secondary"><i className="fas fa-times"></i></a>
          </div>
        </div>
      </form>

      <div className="card overflow-hidden fade-up">
        <div className="px-5 py-3 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2">
          <p className="text-xs text-slate-400">
            <span className="font-semibold text-slate-600">{paginacion.total}</span> citas encontradas
            <span className="hidden sm:inline">· Pág. {paginacion.pagina}/{paginacion.paginas}</span>
          </p>
          <div className="flex items-center gap-2">
            <span className="text-xs text-slate-400">Mostrar</span>
            <select className="text-xs border border-slate-200 rounded-lg px-2 py-1 outline-none" value={paginacion.porPagina} onChange={(e) => changePorPagina(Number(e.target.value))}>
              <option value="15">15</option>
              <option value="30">30</option>
              <option value="50">50</option>
            </select>
          </div>
        </div>
        <div className="overflow-x-auto">
          <table className="data-table resp-table">
            <thead>
              <tr>
                <th>Paciente</th>
                <th>Médico / Especialidad</th>
                <th>Fecha / Hora</th>
                <th>Estado</th>
                <th>Cambiar estado</th>
                <th className="text-right">Acc.</th>
              </tr>
            </thead>
            <tbody>
              {citas.length === 0 ? (
                <tr>
                  <td colSpan={6}>
                    <div className="empty-state">
                      <i className="fas fa-calendar-times"></i>
                      <h3>No se encontraron citas</h3>
                      <p>Intenta cambiar los filtros de búsqueda</p>
                    </div>
                  </td>
                </tr>
              ) : (
                citas.map((c) => (
                  <tr key={c.id}>
                    <td data-label="Paciente">
                      <div className="flex items-center gap-2.5">
                        <div className="avatar avatar-sm" style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}>
                          {c.nombre_paciente.charAt(0).toUpperCase()}
                        </div>
                        <div>
                          <p className="font-semibold text-slate-800 text-sm">{c.nombre_paciente}</p>
                          <p className="text-slate-400 text-xs">{c.telefono}</p>
                        </div>
                      </div>
                    </td>
                    <td data-label="Médico">
                      <p className="text-sm font-medium text-slate-700">Dr. {c.medico_nombre} {c.medico_apellido}</p>
                      <p className="text-xs text-slate-400">{c.especialidad}</p>
                    </td>
                    <td data-label="Fecha / Hora">
                      <p className="text-sm text-slate-700 font-medium">{fmtFecha(c.fecha)}</p>
                      <p className="font-mono text-xs text-slate-500">{c.hora}</p>
                    </td>
                    <td data-label="Estado">
                      <span className={`badge badge-${ESTADO_LABEL[c.estado] ?? "secondary"}`}>
                        {c.estado.charAt(0).toUpperCase() + c.estado.slice(1)}
                      </span>
                    </td>
                    <td data-label="Cambiar estado">
                      <select
                        className="text-xs border border-slate-200 rounded-lg px-2 py-1.5 outline-none focus:border-sky-500"
                        defaultValue={c.estado_id}
                        onChange={(e) => cambiarEstado(c.id, Number(e.target.value))}
                        style={{ minWidth: 100 }}
                      >
                        <option value="1">Pendiente</option>
                        <option value="2">Confirmar</option>
                        <option value="3">Cancelar</option>
                        <option value="4">Atender</option>
                      </select>
                    </td>
                    <td data-label="Acc.">
                      <div className="flex items-center justify-end gap-1">
                        <button type="button" className="btn btn-icon btn-sm btn-ghost" onClick={() => verDetalle(c.id)} title="Ver detalle">
                          <i className="fas fa-eye"></i>
                        </button>
                        <button type="button" className="btn btn-icon btn-sm btn-ghost text-amber-600 hover:bg-amber-50" onClick={() => openEdit(c)} title="Editar cita">
                          <i className="fas fa-pen"></i>
                        </button>
                        <button type="button" className="btn btn-icon btn-sm btn-ghost text-red-500 hover:bg-red-50" onClick={() => deleteCita(c)} title="Eliminar">
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

        {paginacion.paginas > 1 && (
          <div className="pagination flex-wrap">
            <p className="text-xs text-slate-400 mr-auto">Página {paginacion.pagina} de {paginacion.paginas}</p>
            <div className="flex gap-1">
              {paginacion.pagina > 1 && (
                <button className="page-btn" onClick={() => changePage(1)}>
                  <i className="fas fa-angle-double-left"></i>
                </button>
              )}
              {Array.from({ length: paginacion.paginas }, (_, i) => i + 1)
                .filter((p) => Math.abs(p - paginacion.pagina) <= 2)
                .map((p) => (
                  <button key={p} className={`page-btn ${p === paginacion.pagina ? "active" : ""}`} onClick={() => changePage(p)}>
                    {p}
                  </button>
                ))}
              {paginacion.pagina < paginacion.paginas && (
                <button className="page-btn" onClick={() => changePage(paginacion.paginas)}>
                  <i className="fas fa-angle-double-right"></i>
                </button>
              )}
            </div>
          </div>
        )}
      </div>

      {/* Detalle Modal */}
      {detalle !== null && (
        <div className="modal-overlay active" onClick={(e) => { if (e.target === e.currentTarget) setDetalle(null); }}>
          <div className="modal-box" style={{ maxWidth: 500 }}>
            <div className="flex items-center justify-between px-5 py-4 border-b border-slate-100">
              <h3 className="text-base font-extrabold text-slate-800">
                <i className="fas fa-calendar-check text-sky-500 mr-2"></i>Detalle de Cita
              </h3>
              <button type="button" onClick={() => setDetalle(null)} className="text-slate-400 hover:text-slate-600 transition">
                <i className="fas fa-times"></i>
              </button>
            </div>
            <div className="p-5 space-y-3 text-sm">
              <div className="grid grid-cols-2 gap-3">
                <div className="col-span-2 flex items-center gap-3 pb-3 border-b border-slate-100">
                  <div
                    className="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold"
                    style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
                  >
                    {String(detalle.nombre_paciente).charAt(0).toUpperCase()}
                  </div>
                  <div className="flex-1">
                    <p className="font-bold text-slate-800">{String(detalle.nombre_paciente)}</p>
                    <p className="text-xs text-slate-400">{String(detalle.telefono ?? "")}</p>
                  </div>
                  <span className={`badge badge-${ESTADO_LABEL[String(detalle.estado)] ?? "secondary"}`}>
                    {String(detalle.estado)}
                  </span>
                </div>
                <Field label="Especialidad" value={String(detalle.especialidad ?? "—")} />
                <Field label="Médico" value={`Dr. ${detalle.medico_nombre ?? ""} ${detalle.medico_apellido ?? ""}`} />
                <Field label="Fecha" value={fmtFecha(String(detalle.fecha))} />
                <Field label="Hora" value={String(detalle.hora).slice(0, 5)} />
                {detalle.email ? <div className="col-span-2"><Field label="Email" value={String(detalle.email)} /></div> : null}
                {detalle.motivo ? <div className="col-span-2"><Field label="Motivo" value={String(detalle.motivo)} /></div> : null}
                {detalle.notas_medico ? <div className="col-span-2"><Field label="Notas del médico" value={String(detalle.notas_medico)} /></div> : null}
              </div>
            </div>
          </div>
        </div>
      )}
      {loadingDet !== null && (
        <div className="modal-overlay active" onClick={(e) => { if (e.target === e.currentTarget) setLoadingDet(null); }}>
          <div className="modal-box" style={{ maxWidth: 500 }}>
            <div className="flex justify-center py-4">
              <div className="w-8 h-8 border-2 border-sky-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
          </div>
        </div>
      )}

      {/* Editar Modal */}
      {edit && (
        <div className="modal-overlay active" onClick={(e) => { if (e.target === e.currentTarget) setEdit(null); }}>
          <div className="modal-box" style={{ maxWidth: 480 }}>
            <div className="flex items-center justify-between px-5 py-4 border-b border-slate-100">
              <h3 className="text-base font-extrabold text-slate-800">
                <i className="fas fa-pen text-amber-500 mr-2"></i>Reasignar Cita
              </h3>
              <button type="button" onClick={() => setEdit(null)} className="text-slate-400 hover:text-slate-600 transition">
                <i className="fas fa-times"></i>
              </button>
            </div>
            <form onSubmit={guardarEdit} className="p-5">
              <input type="hidden" name="cita_id" value={edit.id} />
              <p className="text-xs text-slate-600 bg-amber-50 rounded-lg px-3 py-2 border border-amber-200 mb-4">
                <i className="fas fa-info-circle text-amber-500 mr-1"></i>
                Reasignar a otro médico. La cita volverá a estado <strong>pendiente</strong>.
              </p>
              <div className="mb-3">
                <label className="block text-xs font-semibold text-slate-500 mb-1">Nuevo médico</label>
                <select
                  name="medico_id"
                  className="input-field"
                  value={edit.medicoId}
                  onChange={(e) => {
                    setEdit({ ...edit, medicoId: Number(e.target.value) });
                    cargarSlotsEdit(Number(e.target.value), edit.fecha);
                  }}
                  required
                >
                  <option value="">Seleccionar médico</option>
                  {medicos.map((m) => (
                    <option key={m.id} value={m.id}>Dr. {m.nombre} {m.apellido}</option>
                  ))}
                </select>
              </div>
              <div className="mb-3">
                <label className="block text-xs font-semibold text-slate-500 mb-1">Nueva fecha</label>
                <input
                  type="date"
                  name="fecha"
                  className="input-field"
                  value={edit.fecha}
                  onChange={(e) => {
                    setEdit({ ...edit, fecha: e.target.value });
                    cargarSlotsEdit(edit.medicoId, e.target.value);
                  }}
                  required
                />
              </div>
              <div className="mb-3">
                <label className="block text-xs font-semibold text-slate-500 mb-1">Nueva hora</label>
                <select id="editHora" name="hora" className="input-field" required defaultValue={edit.hora}>
                  <option value="">Selecciona médico y fecha primero</option>
                </select>
              </div>
              <button type="submit" className="btn btn-primary w-full">
                <i className="fas fa-save"></i> Guardar cambios
              </button>
            </form>
          </div>
        </div>
      )}
    </>
  );
}

function Field({ label, value }: { label: string; value: string }) {
  return (
    <div className="bg-slate-50 rounded-lg p-3">
      <p className="text-[10px] text-slate-400 font-semibold uppercase mb-1">{label}</p>
      <p className="font-semibold text-slate-700 text-sm">{value}</p>
    </div>
  );
}

function esc(s: string) {
  if (!s) return "";
  const d = document.createElement("div");
  d.textContent = s;
  return d.innerHTML;
}
