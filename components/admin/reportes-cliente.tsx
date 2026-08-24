"use client";

import { useRouter, useSearchParams } from "next/navigation";

interface Filtros {
  desde: string; hasta: string;
  medico_id?: number; especialidad_id?: number; estado_id?: number; q?: string;
}
interface Row { id: number; nombre_paciente: string; fecha: string; hora: string; medico_nombre?: string; medico_apellido?: string; especialidad?: string | null; estado?: string | null; }

export function ReportesCliente({
  stats, porMedico, porEspecialidad, citas, paginacion, filtros, medicos, especialidades, exportUrl,
}: {
  stats: { total: number; pendientes: number; confirmadas: number; canceladas: number; atendidas: number };
  porMedico: Array<{ id: number; nombre: string; apellido: string; especialidad: string | null; total: number }>;
  porEspecialidad: Array<{ id: number; nombre: string; total: number }>;
  citas: Row[];
  paginacion: { total: number; pagina: number; paginas: number; porPagina: number };
  filtros: Filtros;
  medicos: Array<{ id: number; nombre: string; apellido: string }>;
  especialidades: Array<{ id: number; nombre: string }>;
  exportUrl: string;
}) {
  const router = useRouter();
  const sp = useSearchParams();

  function page(n: number) {
    const params = new URLSearchParams(sp.toString());
    params.set("pagina", String(n));
    router.push(`/admin/reportes?${params.toString()}`);
  }

  const maxMed = Math.max(1, ...porMedico.map((m) => Number(m.total)));
  const maxEsp = Math.max(1, ...porEspecialidad.map((e) => Number(e.total)));

  return (
    <>
      <div className="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
        <div>
          <h1 className="text-xl font-extrabold text-slate-800">Reportes</h1>
          <p className="text-sm text-slate-400">Análisis y estadísticas de citas</p>
        </div>
        <a href={exportUrl} className="btn btn-secondary btn-sm">
          <i className="fas fa-file-export"></i> Exportar CSV
        </a>
      </div>

      <form method="GET" action="/admin/reportes" className="card p-4 sm:p-5 mb-6 fade-up">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1">Desde</label>
            <input type="date" name="desde" defaultValue={filtros.desde} className="input-field" />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1">Hasta</label>
            <input type="date" name="hasta" defaultValue={filtros.hasta} className="input-field" />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1">Médico</label>
            <select name="medico_id" defaultValue={filtros.medico_id ?? ""} className="input-field">
              <option value="">Todos</option>
              {medicos.map((m) => <option key={m.id} value={m.id}>Dr. {m.nombre} {m.apellido}</option>)}
            </select>
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1">Especialidad</label>
            <select name="especialidad_id" defaultValue={filtros.especialidad_id ?? ""} className="input-field">
              <option value="">Todas</option>
              {especialidades.map((e) => <option key={e.id} value={e.id}>{e.nombre}</option>)}
            </select>
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1">Estado</label>
            <select name="estado_id" defaultValue={filtros.estado_id ?? ""} className="input-field">
              <option value="">Todos</option>
              <option value="1">Pendiente</option>
              <option value="2">Confirmada</option>
              <option value="3">Cancelada</option>
              <option value="4">Atendida</option>
            </select>
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1">Buscar</label>
            <input type="text" name="q" defaultValue={filtros.q ?? ""} className="input-field" placeholder="Nombre..." />
          </div>
        </div>
        <div className="flex gap-2 mt-3">
          <button className="btn btn-primary" type="submit"><i className="fas fa-search"></i> Filtrar</button>
          <a className="btn btn-secondary" href="/admin/reportes"><i className="fas fa-times"></i> Limpiar</a>
        </div>
      </form>

      <div className="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        <Stat label="Total" value={stats.total} color="#0284c7" bg="#e0f2fe" />
        <Stat label="Pendientes" value={stats.pendientes} color="#d97706" bg="#fef9c3" />
        <Stat label="Confirmadas" value={stats.confirmadas} color="#3b82f6" bg="#dbeafe" />
        <Stat label="Atendidas" value={stats.atendidas} color="#10b981" bg="#d1fae5" />
        <Stat label="Canceladas" value={stats.canceladas} color="#ef4444" bg="#fee2e2" />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div className="card p-4 fade-up">
          <h3 className="text-sm font-bold text-slate-700 mb-4">Citas por Médico</h3>
          {porMedico.length === 0 ? (
            <p className="text-xs text-slate-400 text-center py-4">Sin datos</p>
          ) : (
            <div className="space-y-3">
              {porMedico.slice(0, 10).map((m) => (
                <div key={m.id}>
                  <div className="flex justify-between mb-1">
                    <p className="text-xs font-semibold text-slate-700">Dr. {m.nombre} {m.apellido}</p>
                    <p className="text-xs font-bold text-slate-500">{m.total}</p>
                  </div>
                  <div className="w-full h-2 rounded-full bg-slate-100">
                    <div className="h-2 rounded-full" style={{ width: `${(Number(m.total) / maxMed) * 100}%`, background: "linear-gradient(90deg,#0284c7,#0e7490)" }} />
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>

        <div className="card p-4 fade-up" style={{ animationDelay: ".05s" }}>
          <h3 className="text-sm font-bold text-slate-700 mb-4">Citas por Especialidad</h3>
          {porEspecialidad.length === 0 ? (
            <p className="text-xs text-slate-400 text-center py-4">Sin datos</p>
          ) : (
            <div className="space-y-3">
              {porEspecialidad.slice(0, 10).map((e) => (
                <div key={e.id}>
                  <div className="flex justify-between mb-1">
                    <p className="text-xs font-semibold text-slate-700">{e.nombre}</p>
                    <p className="text-xs font-bold text-slate-500">{e.total}</p>
                  </div>
                  <div className="w-full h-2 rounded-full bg-slate-100">
                    <div className="h-2 rounded-full" style={{ width: `${(Number(e.total) / maxEsp) * 100}%`, background: "linear-gradient(90deg,#7c3aed,#a78bfa)" }} />
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>

      <div className="card overflow-hidden fade-up">
        <div className="px-5 py-3 border-b border-slate-100">
          <h3 className="text-sm font-bold text-slate-700">Listado de citas</h3>
          <p className="text-xs text-slate-400">{paginacion.total} resultados</p>
        </div>
        <div className="overflow-x-auto">
          <table className="data-table resp-table">
            <thead>
              <tr>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Especialidad</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              {citas.length === 0 ? (
                <tr><td colSpan={6}><div className="empty-state"><i className="fas fa-calendar-times"></i><h3>Sin resultados</h3></div></td></tr>
              ) : (
                citas.map((c) => (
                  <tr key={c.id}>
                    <td data-label="Paciente" className="font-semibold text-slate-800 text-sm">{c.nombre_paciente}</td>
                    <td data-label="Médico" className="text-sm text-slate-600">Dr. {c.medico_nombre} {c.medico_apellido}</td>
                    <td data-label="Especialidad" className="text-xs text-slate-500">{c.especialidad ?? "—"}</td>
                    <td data-label="Fecha" className="text-sm text-slate-600">{c.fecha}</td>
                    <td data-label="Hora" className="text-sm font-mono">{String(c.hora).slice(0, 5)}</td>
                    <td data-label="Estado">
                      <span className={`badge badge-${c.estado === "atendida" ? "success" : c.estado === "confirmada" ? "info" : c.estado === "cancelada" ? "danger" : "yellow"}`}>
                        {c.estado}
                      </span>
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
    </>
  );
}

function Stat({ label, value, color, bg }: { label: string; value: number; color: string; bg: string }) {
  return (
    <div className="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
      <div className="w-10 h-10 rounded-lg flex items-center justify-center" style={{ background: bg }}>
        <span className="font-extrabold text-lg" style={{ color }}>{value}</span>
      </div>
      <div>
        <p className="text-xs text-slate-400">{label}</p>
        <p className="text-sm font-bold text-slate-700">{value}</p>
      </div>
    </div>
  );
}
