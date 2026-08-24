import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import {
  citasPorDiaSemana,
  estadisticasDashboard,
  proximasCitas,
  tasaCancelaciones,
  contarPacientesUnicos,
} from "@/lib/repos/citas";
import { especialidadesMasSolicitadas, medicosTop } from "@/lib/repos/dashboard";
import { DashboardCharts } from "@/components/dashboard-charts";
import { EstadoBadge } from "@/components/estado-badge";
import { formatearFecha } from "@/lib/time";

export const dynamic = "force-dynamic";

export default async function AdminDashboardPage() {
  const session = await auth();
  if (!session?.user) redirect("/admin/login");
  if (session.user.rol === "paciente") redirect("/paciente/dashboard");
  if (session.user.rol === "medico") redirect("/medico/dashboard");

  const [stats, recientes, pacientesUnicos, tasaC, espTop, medTop, porDia] = await Promise.all([
    estadisticasDashboard(),
    proximasCitas(8),
    contarPacientesUnicos(),
    tasaCancelaciones(),
    especialidadesMasSolicitadas(5),
    medicosTop(5),
    citasPorDiaSemana(),
  ]);

  const user = session.user;

  return (
    <>
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6" id="kpiGrid">
        <Kpi label="Total Citas" value={stats.total} icon="fa-calendar-day" color="#0284c7" bg="#e0f2fe" />
        <Kpi label="Pendientes" value={stats.pendientes} icon="fa-clock" color="#d97706" bg="#fef9c3" />
        <Kpi label="Pacientes" value={pacientesUnicos} icon="fa-users" color="#10b981" bg="#d1fae5" />
        <Kpi
          label={`Canc. ${tasaC}%`}
          value={stats.canceladas}
          icon="fa-times-circle"
          color="#ef4444"
          bg="#fee2e2"
        />
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div className="md:col-span-2 bg-white rounded-xl border border-slate-100 shadow-sm p-4 fade-up">
          <div className="flex items-center justify-between mb-3">
            <h3 className="text-xs font-bold text-slate-600 uppercase tracking-wider">Citas por día</h3>
            <span className="text-[10px] text-slate-400">Últimos 7 días</span>
          </div>
          <div className="chart-wrap" style={{ height: 160 }}>
            <DashboardCharts type="bar" labels={["Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"]} data={porDia} />
          </div>
        </div>

        <div className="bg-white rounded-xl border border-slate-100 shadow-sm p-4 fade-up" style={{ animationDelay: "0.1s" }}>
          <div className="flex items-center justify-between mb-3">
            <h3 className="text-xs font-bold text-slate-600 uppercase tracking-wider">Distribución</h3>
            <span className="text-[10px] text-slate-400">Estados</span>
          </div>
          <div className="chart-wrap" style={{ height: 160 }}>
            <DashboardCharts
              type="doughnut"
              labels={["Pendientes", "Confirmadas", "Canceladas", "Atendidas"]}
              data={[stats.pendientes, stats.confirmadas, stats.canceladas, stats.atendidas]}
            />
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div className="bg-white rounded-xl border border-slate-100 shadow-sm p-4 fade-up">
          <h3 className="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">
            <i className="fas fa-star text-amber-400 mr-1"></i> Especialidades
          </h3>
          {espTop.length === 0 ? (
            <p className="text-xs text-slate-400 text-center py-4">Sin datos</p>
          ) : (
            <div className="space-y-2">
              {espTop.map((e, i) => (
                <div key={e.id} className="flex items-center gap-2">
                  <span className="text-xs font-bold text-slate-400 w-4">{i + 1}.</span>
                  <div className="flex-1 min-w-0">
                    <p className="text-xs font-semibold text-slate-700 truncate">{e.nombre}</p>
                    <div className="w-full h-1.5 rounded-full bg-slate-100 mt-1">
                      <div
                        className="h-1.5 rounded-full"
                        style={{
                          width: `${Math.min(100, e.total * 10)}%`,
                          background: "linear-gradient(90deg,#0284c7,#0e7490)",
                        }}
                      />
                    </div>
                  </div>
                  <span className="text-xs font-bold text-slate-500">{e.total}</span>
                </div>
              ))}
            </div>
          )}
        </div>

        <div className="bg-white rounded-xl border border-slate-100 shadow-sm p-4 fade-up" style={{ animationDelay: "0.05s" }}>
          <h3 className="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">
            <i className="fas fa-user-md text-sky-500 mr-1"></i> Médicos Top
          </h3>
          {medTop.length === 0 ? (
            <p className="text-xs text-slate-400 text-center py-4">Sin datos</p>
          ) : (
            <div className="space-y-2">
              {medTop.map((m) => (
                <div key={m.id} className="flex items-center gap-2">
                  <div
                    className="w-6 h-6 rounded-full flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0"
                    style={{ background: "linear-gradient(135deg,#7c3aed,#a78bfa)" }}
                  >
                    {m.nombre.charAt(0).toUpperCase()}
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-xs font-semibold text-slate-700 truncate">Dr. {m.apellido}</p>
                    <p className="text-[10px] text-slate-400">{m.especialidad ?? "—"}</p>
                  </div>
                  <span className="text-xs font-bold text-slate-500">{m.total}</span>
                </div>
              ))}
            </div>
          )}
        </div>

        <div className="fade-up" style={{ animationDelay: "0.1s" }}>
          <h3 className="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Acciones</h3>
          <div className="flex flex-col gap-2">
            <QuickAction href="/admin/citas" icon="fa-calendar-plus" label="Nueva Cita" gradient="linear-gradient(135deg,#0284c7,#0e7490)" />
            <QuickAction href="/admin/medicos" icon="fa-user-md" label="Nuevo Médico" gradient="linear-gradient(135deg,#7c3aed,#a78bfa)" />
            <QuickAction href="/admin/usuarios" icon="fa-users" label="Usuarios" gradient="linear-gradient(135deg,#059669,#34d399)" />
            <QuickAction href="/admin/horarios" icon="fa-clock" label="Horarios" gradient="linear-gradient(135deg,#d97706,#f59e0b)" />
          </div>
        </div>

        <div className="md:col-span-3 bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden fade-up" style={{ animationDelay: "0.05s" }}>
          <div className="flex items-center justify-between px-4 py-3 border-b border-slate-100">
            <div className="flex items-center gap-2.5">
              <div className="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style={{ background: "#e0f2fe" }}>
                <i className="fas fa-calendar-alt text-xs" style={{ color: "#0284c7" }}></i>
              </div>
              <div>
                <h2 className="text-sm font-bold text-slate-800">Próximas Citas</h2>
                <p className="text-[10px] text-slate-400">Citas agendadas para los próximos días</p>
              </div>
            </div>
            <a href="/admin/citas" className="text-xs font-bold text-sky-600 hover:text-sky-700 transition whitespace-nowrap">
              Ver todas <i className="fas fa-arrow-right ml-1"></i>
            </a>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full data-table resp-table">
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
                {recientes.length === 0 ? (
                  <tr>
                    <td colSpan={6} className="text-center py-10">
                      <div className="text-slate-300 text-3xl mb-2"><i className="fas fa-calendar-times"></i></div>
                      <p className="text-sm font-semibold text-slate-500">Sin citas próximas</p>
                      <p className="text-xs text-slate-400 mt-0.5">No hay citas agendadas para los próximos días</p>
                    </td>
                  </tr>
                ) : (
                  recientes.map((c) => (
                    <tr key={c.id}>
                      <td data-label="Paciente">
                        <div className="flex items-center gap-2.5">
                          <div
                            className="w-7 h-7 rounded-full flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0"
                            style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
                          >
                            {c.nombre_paciente.charAt(0).toUpperCase()}
                          </div>
                          <div className="min-w-0">
                            <p className="font-semibold text-slate-800 text-sm leading-tight truncate">{c.nombre_paciente}</p>
                            <p className="text-slate-400 text-[11px]">{c.telefono ?? ""}</p>
                          </div>
                        </div>
                      </td>
                      <td data-label="Médico">
                        <p className="text-sm text-slate-700 truncate">Dr. {c.medico_nombre} {c.medico_apellido}</p>
                      </td>
                      <td data-label="Especialidad">
                        <span className="text-sm text-slate-500">{c.especialidad ?? "—"}</span>
                      </td>
                      <td data-label="Fecha">
                        <span className="text-sm font-medium text-slate-600 whitespace-nowrap">{formatearFecha(c.fecha)}</span>
                      </td>
                      <td data-label="Hora">
                        <span className="font-mono text-sm text-slate-700">{String(c.hora ?? "").slice(0, 5)}</span>
                      </td>
                      <td data-label="Estado">
                        <EstadoBadge estado={c.estado} />
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </>
  );
}

function Kpi({ label, value, icon, color, bg }: { label: string; value: number; icon: string; color: string; bg: string }) {
  return (
    <div className="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex items-center gap-3 fade-up">
      <div className="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style={{ background: bg }}>
        <i className={`fas ${icon} text-base`} style={{ color }}></i>
      </div>
      <div>
        <p className="text-xl font-extrabold text-slate-800">{value}</p>
        <p className="text-xs font-medium text-slate-400">{label}</p>
      </div>
    </div>
  );
}

function QuickAction({ href, icon, label, gradient }: { href: string; icon: string; label: string; gradient: string }) {
  return (
    <a
      href={href}
      className="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-white border border-slate-100 shadow-sm hover:shadow-md transition-all text-sm font-semibold text-slate-700"
    >
      <div className="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-white text-xs" style={{ background: gradient }}>
        <i className={`fas ${icon}`}></i>
      </div>
      {label}
    </a>
  );
}
