"use client";

import { useRouter, useSearchParams } from "next/navigation";
import { useState } from "react";

interface Log {
  id: number;
  usuario_id: number | null;
  usuario_nombre: string | null;
  accion: string;
  tabla: string | null;
  registro_id: number | null;
  descripcion: string | null;
  ip: string | null;
  created_at: string;
}
interface Props {
  logs: Log[];
  paginacion: { total: number; pagina: number; paginas: number; porPagina: number };
  acciones: string[];
  tablas: string[];
  filtros: {
    usuario_id?: number | null;
    accion?: string | null;
    tabla?: string | null;
    fecha_desde?: string | null;
    fecha_hasta?: string | null;
    ip?: string | null;
  };
}

export function AuditoriaCliente({ logs, paginacion, acciones, tablas, filtros }: Props) {
  const router = useRouter();
  const sp = useSearchParams();
  const [detalle, setDetalle] = useState<Log | null>(null);

  function page(n: number) {
    const params = new URLSearchParams(sp.toString());
    params.set("pagina", String(n));
    router.push(`/admin/auditoria?${params.toString()}`);
  }

  return (
    <>
      <div className="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
        <div>
          <h1 className="text-xl font-extrabold text-slate-800">Auditoría</h1>
          <p className="text-sm text-slate-400">Registro de actividades del sistema</p>
        </div>
      </div>

      <form method="GET" action="/admin/auditoria" className="card p-4 sm:p-5 mb-6 fade-up">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1">Acción</label>
            <select name="accion" defaultValue={filtros.accion ?? ""} className="input-field">
              <option value="">Todas</option>
              {acciones.map((a) => <option key={a} value={a}>{a}</option>)}
            </select>
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1">Tabla</label>
            <select name="tabla" defaultValue={filtros.tabla ?? ""} className="input-field">
              <option value="">Todas</option>
              {tablas.map((t) => <option key={t} value={t}>{t}</option>)}
            </select>
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1">Desde</label>
            <input type="date" name="fecha_desde" defaultValue={filtros.fecha_desde ?? ""} className="input-field" />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1">Hasta</label>
            <input type="date" name="fecha_hasta" defaultValue={filtros.fecha_hasta ?? ""} className="input-field" />
          </div>
        </div>
        <div className="flex gap-2 mt-3">
          <button className="btn btn-primary" type="submit"><i className="fas fa-search"></i> Filtrar</button>
          <a className="btn btn-secondary" href="/admin/auditoria"><i className="fas fa-times"></i> Limpiar</a>
        </div>
      </form>

      <div className="card overflow-hidden fade-up">
        <div className="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <p className="text-xs text-slate-400">
            <span className="font-semibold text-slate-600">{paginacion.total}</span> registros · Pág. {paginacion.pagina}/{paginacion.paginas}
          </p>
        </div>
        <div className="overflow-x-auto">
          <table className="data-table resp-table">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Tabla</th>
                <th>Descripción</th>
                <th>IP</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {logs.length === 0 ? (
                <tr><td colSpan={7}><div className="empty-state"><i className="fas fa-history"></i><h3>Sin registros</h3></div></td></tr>
              ) : (
                logs.map((l) => (
                  <tr key={l.id}>
                    <td data-label="Fecha">
                      <p className="text-xs font-mono text-slate-500 whitespace-nowrap">{l.created_at}</p>
                    </td>
                    <td data-label="Usuario">
                      <p className="text-xs text-slate-700">{l.usuario_nombre ?? <span className="text-slate-400">—</span>}</p>
                    </td>
                    <td data-label="Acción">
                      <span className="badge badge-blue">{l.accion}</span>
                    </td>
                    <td data-label="Tabla">
                      <span className="text-xs text-slate-500">{l.tabla ?? "—"}</span>
                    </td>
                    <td data-label="Descripción">
                      <p className="text-xs text-slate-600 max-w-md truncate">{l.descripcion ?? ""}</p>
                    </td>
                    <td data-label="IP">
                      <span className="text-xs font-mono text-slate-400">{l.ip ?? "—"}</span>
                    </td>
                    <td>
                      <button
                        className="btn btn-icon btn-sm btn-ghost"
                        onClick={() => setDetalle(l)}
                        title="Ver detalle"
                      >
                        <i className="fas fa-eye"></i>
                      </button>
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
              {Array.from({ length: paginacion.paginas }, (_, i) => i + 1)
                .filter((p) => Math.abs(p - paginacion.pagina) <= 2)
                .map((p) => (
                  <button key={p} className={`page-btn ${p === paginacion.pagina ? "active" : ""}`} onClick={() => page(p)}>{p}</button>
                ))}
            </div>
          </div>
        )}
      </div>

      {detalle && (
        <div className="modal-overlay active" onClick={(e) => { if (e.target === e.currentTarget) setDetalle(null); }}>
          <div className="modal-box" style={{ maxWidth: 600 }}>
            <div className="flex items-center justify-between px-5 py-4 border-b border-slate-100">
              <h3 className="text-base font-extrabold text-slate-800">
                <i className="fas fa-history text-sky-500 mr-2"></i>Detalle de auditoría
              </h3>
              <button type="button" onClick={() => setDetalle(null)} className="text-slate-400 hover:text-slate-600">
                <i className="fas fa-times"></i>
              </button>
            </div>
            <div className="p-5 space-y-3 text-sm">
              <Field label="Fecha" value={detalle.created_at} />
              <Field label="Usuario" value={detalle.usuario_nombre ?? "—"} />
              <Field label="Acción" value={detalle.accion} />
              <Field label="Tabla" value={detalle.tabla ?? "—"} />
              <Field label="Registro ID" value={String(detalle.registro_id ?? "—")} />
              <Field label="IP" value={detalle.ip ?? "—"} />
              <Field label="Descripción" value={detalle.descripcion ?? "—"} />
            </div>
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
      <p className="font-semibold text-slate-700 text-sm break-words">{value}</p>
    </div>
  );
}
