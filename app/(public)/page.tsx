import Link from "next/link";
import { query } from "@/lib/db";
import type { Especialidad, Medico } from "@/lib/types";
import { FaqList } from "@/components/faq";

export const dynamic = "force-dynamic";

const BENEFITS: Array<[string, string, string]> = [
  ["fa-clock", "Sin Esperas", "Agendá tu cita en menos de 2 minutos. Olvidate de las largas filas y las llamadas telefónicas."],
  ["fa-shield-alt", "Seguro y Confiable", "Tus datos están protegidos. Sistema seguro con confirmación por email y recordatorios."],
  ["fa-mobile-alt", "Desde Cualquier Lugar", "Accedé desde tu celular, tablet o computadora. Disponible 24/7."],
  ["fa-calendar-alt", "Gestión de Turnos", "Elegí el día y horario que mejor se adapte a tu agenda. Cancelá o reprogramá fácilmente."],
  ["fa-user-md", "Mejores Profesionales", "Contamos con un equipo médico de primer nivel en diversas especialidades."],
  ["fa-heartbeat", "Cuidado Integral", "Atención personalizada para vos y tu familia. Tu salud es nuestra prioridad."],
];

const PASOS: Array<[string, string, string, string]> = [
  ["fa-stethoscope", "1", "Elige Especialidad", "Seleccioná la especialidad médica que necesitás"],
  ["fa-user-md", "2", "Selecciona Médico", "Elegí al profesional de tu preferencia"],
  ["fa-calendar-alt", "3", "Fecha y Horario", "Seleccioná el día y el turno disponible"],
  ["fa-check-circle", "4", "Confirmá tu Cita", "Completá tus datos y recibí la confirmación"],
];

const ESP_COLORS = ["#0284c7", "#7c3aed", "#0891b2", "#059669", "#d97706", "#dc2626"];

async function getHomeData() {
  const [especialidades, medicos] = await Promise.all([
    query<Especialidad[]>(
      `SELECT id, nombre, icono, descripcion FROM especialidades WHERE activo = 1 ORDER BY nombre ASC`
    ),
    query<Medico[]>(
      `SELECT m.id, m.nombre, m.apellido, m.especialidad_id, e.nombre AS especialidad_nombre
       FROM medicos m
       JOIN especialidades e ON m.especialidad_id = e.id
       WHERE m.activo = 1 AND m.disponible = 1
       ORDER BY m.creado_en DESC
       LIMIT 6`
    ),
  ]);
  return { especialidades, medicos };
}

export default async function HomePage() {
  const { especialidades, medicos } = await getHomeData();

  return (
    <>
      {/* ─── HERO ─── */}
      <section
        className="relative overflow-hidden"
        style={{ background: "linear-gradient(135deg,#0f172a 0%,#1e3a5f 40%,#0e7490 100%)" }}
      >
        <div className="absolute inset-0 overflow-hidden pointer-events-none">
          <div
            className="absolute -top-40 -right-40 w-80 h-80 rounded-full opacity-20"
            style={{ background: "radial-gradient(circle,#38bdf8 0%,transparent 70%)" }}
          ></div>
          <div
            className="absolute -bottom-40 -left-40 w-80 h-80 rounded-full opacity-10"
            style={{ background: "radial-gradient(circle,#0ea5e9 0%,transparent 70%)" }}
          ></div>
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28 relative">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div className="fade-up">
              <span className="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-sky-200 text-xs font-semibold px-4 py-1.5 rounded-full mb-5">
                <i className="fas fa-shield-alt"></i> Sistema seguro de turnos online
              </span>
              <h1 className="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-5">
                Agenda tu cita
                <br />
                <mark
                  style={{
                    background: "rgba(56,189,248,0.18)",
                    color: "#7dd3fc",
                    padding: "0 0.2em",
                    borderRadius: "0.2em",
                    WebkitBoxDecorationBreak: "clone",
                    boxDecorationBreak: "clone",
                  }}
                >
                  médica en minutos
                </mark>
              </h1>
              <p className="text-slate-300 text-lg mb-8 max-w-lg">
                Sin filas, sin llamadas. Elegí especialidad, médico y horario de forma rápida y segura desde cualquier dispositivo.
              </p>
              <div className="flex flex-wrap gap-3">
                <Link
                  href="/agendar"
                  className="inline-flex items-center gap-2 px-8 py-4 rounded-full text-base font-bold text-white shadow-xl hover:shadow-2xl transition-all hover:scale-105"
                  style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
                >
                  <i className="fas fa-calendar-check"></i> Agendar Cita Ahora
                </Link>
                <Link
                  href="#especialidades"
                  className="inline-flex items-center gap-2 px-8 py-4 rounded-full text-base font-semibold text-white/80 border border-white/20 hover:bg-white/10 transition-all"
                >
                  Ver Especialidades <i className="fas fa-arrow-right text-xs"></i>
                </Link>
              </div>

              <div className="flex items-center gap-8 mt-12 pt-8 border-t border-white/10">
                <div>
                  <p className="text-2xl font-extrabold text-white">+500</p>
                  <p className="text-sky-200 text-xs">Citas realizadas</p>
                </div>
                <div>
                  <p className="text-2xl font-extrabold text-white">+50</p>
                  <p className="text-sky-200 text-xs">Médicos activos</p>
                </div>
                <div>
                  <p className="text-2xl font-extrabold text-white">15</p>
                  <p className="text-sky-200 text-xs">Especialidades</p>
                </div>
              </div>
            </div>

            <div className="hidden lg:flex justify-center relative fade-up" style={{ animationDelay: ".15s" }}>
              <div className="relative">
                <div className="bg-white/5 backdrop-blur-xl rounded-3xl p-8 border border-white/10 shadow-2xl w-80">
                  <div className="flex items-center gap-3 mb-6">
                    <div
                      className="w-12 h-12 rounded-2xl flex items-center justify-center"
                      style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
                    >
                      <i className="fas fa-calendar-check text-white text-lg"></i>
                    </div>
                    <div>
                      <p className="text-white font-bold text-sm">Próxima Cita</p>
                      <p className="text-sky-200 text-xs">Confirmada</p>
                    </div>
                  </div>
                  <div className="space-y-4">
                    <div className="flex items-center gap-3 p-3 rounded-xl bg-white/5">
                      <i className="fas fa-user text-sky-300"></i>
                      <div>
                        <p className="text-white text-xs font-semibold">María González</p>
                        <p className="text-sky-200 text-[10px]">Paciente</p>
                      </div>
                    </div>
                    <div className="flex items-center gap-3 p-3 rounded-xl bg-white/5">
                      <i className="fas fa-user-md text-sky-300"></i>
                      <div>
                        <p className="text-white text-xs font-semibold">Dr. Carlos Martínez</p>
                        <p className="text-sky-200 text-[10px]">Cardiología</p>
                      </div>
                    </div>
                    <div className="flex items-center gap-3 p-3 rounded-xl bg-white/5">
                      <i className="fas fa-clock text-sky-300"></i>
                      <div>
                        <p className="text-white text-xs font-semibold">15 Jul 2026 · 10:30</p>
                        <p className="text-sky-200 text-[10px]">Consulta</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="absolute -top-4 -right-4 bg-emerald-500 rounded-full px-4 py-2 shadow-lg">
                  <p className="text-white text-xs font-bold flex items-center gap-1">
                    <i className="fas fa-check-circle"></i> Confirmado
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="absolute bottom-0 left-0 right-0 leading-none">
          <svg viewBox="0 0 1200 80" preserveAspectRatio="none" className="w-full h-12 md:h-16 fill-slate-50">
            <path d="M0,40 C300,80 900,0 1200,40 L1200,80 L0,80 Z" />
          </svg>
        </div>
      </section>

      {/* ─── BENEFITS ─── */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-14 fade-up">
            <span className="text-sky-600 font-bold text-sm tracking-widest uppercase">Beneficios</span>
            <h2 className="text-3xl font-extrabold text-slate-800 mt-2">¿Por qué elegirnos?</h2>
            <p className="text-slate-400 mt-2 max-w-lg mx-auto">
              Simplificamos el proceso de agendamiento para que te enfoques en tu salud
            </p>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {BENEFITS.map(([icon, title, desc], i) => (
              <div key={i} className="card p-6 text-center hover:shadow-lg transition-all duration-200 fade-up group">
                <div
                  className="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4 transition-colors group-hover:bg-sky-100"
                  style={{ background: "linear-gradient(135deg,#e0f2fe,#bae6fd)" }}
                >
                  <i className={`fas ${icon} text-sky-600 text-xl`}></i>
                </div>
                <h3 className="font-bold text-slate-800 mb-1.5">{title}</h3>
                <p className="text-slate-500 text-sm leading-relaxed">{desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ─── CÓMO FUNCIONA ─── */}
      <section className="py-20 bg-slate-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-14 fade-up">
            <span className="text-sky-600 font-bold text-sm tracking-widest uppercase">Proceso simple</span>
            <h2 className="text-3xl font-extrabold text-slate-800 mt-2">¿Cómo funciona?</h2>
            <p className="text-slate-400 mt-2">Agendá tu cita en 4 pasos simples</p>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {PASOS.map(([icon, num, title, desc], i) => (
              <div key={i} className="bg-white rounded-2xl p-7 border border-slate-100 shadow-sm text-center relative fade-up card-hover">
                <div className="absolute -top-4 left-1/2 -translate-x-1/2">
                  <div
                    className="w-8 h-8 rounded-full text-white text-sm font-bold flex items-center justify-center shadow-md"
                    style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
                  >
                    {num}
                  </div>
                </div>
                <div
                  className="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4 mt-3"
                  style={{ background: "linear-gradient(135deg,#e0f2fe,#bae6fd)" }}
                >
                  <i className={`fas ${icon} text-sky-600 text-xl`}></i>
                </div>
                <h3 className="font-bold text-slate-800 mb-1 text-base">{title}</h3>
                <p className="text-slate-500 text-sm">{desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ─── ESPECIALIDADES ─── */}
      {especialidades.length > 0 && (
        <section id="especialidades" className="py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-14 fade-up">
              <span className="text-sky-600 font-bold text-sm tracking-widest uppercase">Cobertura médica</span>
              <h2 className="text-3xl font-extrabold text-slate-800 mt-2">Nuestras Especialidades</h2>
              <p className="text-slate-400 mt-2">Contamos con profesionales en diversas áreas de la medicina</p>
            </div>
            <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
              {especialidades.map((esp, idx) => {
                const color = ESP_COLORS[idx % 6];
                return (
                  <Link
                    key={esp.id}
                    href={`/agendar?especialidad_id=${esp.id}`}
                    className="bg-slate-50 hover:bg-sky-50 border border-slate-100 hover:border-sky-200 rounded-2xl p-5 text-center transition-all duration-200 group"
                  >
                    <div
                      className="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3"
                      style={{ background: "linear-gradient(135deg,#e0f2fe,#bae6fd)" }}
                    >
                      <i className={`fas ${esp.icono || "fa-stethoscope"} text-xl`} style={{ color }}></i>
                    </div>
                    <p className="font-semibold text-slate-700 text-xs leading-tight">{esp.nombre}</p>
                  </Link>
                );
              })}
            </div>
          </div>
        </section>
      )}

      {/* ─── MÉDICOS ─── */}
      {medicos.length > 0 && (
        <section id="medicos" className="py-20 bg-slate-50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-14 fade-up">
              <span className="text-sky-600 font-bold text-sm tracking-widest uppercase">Profesionales</span>
              <h2 className="text-3xl font-extrabold text-slate-800 mt-2">Nuestro Equipo Médico</h2>
              <p className="text-slate-400 mt-2">Conocé a los profesionales que te atenderán</p>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {medicos.map((m) => (
                <div key={m.id} className="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden card-hover fade-up">
                  <div className="h-20 relative" style={{ background: "linear-gradient(135deg,#0f172a,#0e7490)" }}>
                    <div className="absolute -bottom-6 left-6">
                      <div
                        className="w-14 h-14 rounded-2xl border-4 border-white flex items-center justify-center text-white text-xl shadow-md"
                        style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
                      >
                        <i className="fas fa-user-md"></i>
                      </div>
                    </div>
                  </div>
                  <div className="pt-10 px-6 pb-6">
                    <h3 className="font-bold text-slate-800">Dr. {m.nombre} {m.apellido}</h3>
                    <p className="text-sky-600 text-sm font-medium mb-4">{m.especialidad_nombre ?? ""}</p>
                    <Link
                      href={`/agendar?medico_id=${m.id}&especialidad_id=${m.especialidad_id}`}
                      className="block w-full text-center py-2.5 rounded-xl text-sm font-semibold text-white transition-all"
                      style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
                    >
                      <i className="fas fa-calendar-plus mr-1.5"></i>Agendar Cita
                    </Link>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* ─── FAQ ─── */}
      <section className="py-20 bg-white">
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-14 fade-up">
            <span className="text-sky-600 font-bold text-sm tracking-widest uppercase">FAQ</span>
            <h2 className="text-3xl font-extrabold text-slate-800 mt-2">Preguntas Frecuentes</h2>
          </div>
          <FaqList />
        </div>
      </section>

      {/* ─── CTA FINAL ─── */}
      <section className="py-20" style={{ background: "linear-gradient(135deg,#0f172a 0%,#1e3a5f 50%,#0e7490 100%)" }}>
        <div className="max-w-3xl mx-auto px-4 text-center">
          <div
            className="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl"
            style={{ background: "linear-gradient(135deg,#e0f2fe,#bae6fd)" }}
          >
            <i className="fas fa-calendar-check text-sky-600 text-2xl"></i>
          </div>
          <h2 className="text-3xl font-extrabold text-white mb-3">¿Listo para agendar?</h2>
          <p className="text-sky-200 text-lg mb-8">El proceso tarda menos de 2 minutos. Sin registrarse.</p>
          <Link
            href="/agendar"
            className="inline-flex items-center gap-2 px-10 py-4 rounded-full text-base font-bold text-white shadow-xl hover:shadow-2xl transition-all hover:scale-105"
            style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
          >
            <i className="fas fa-arrow-right"></i> Comenzar Ahora
          </Link>
        </div>
      </section>
    </>
  );
}
