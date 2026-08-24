import { redirect } from "next/navigation";
import Link from "next/link";
import { obtenerCitaPorToken } from "@/lib/repos/citas";
import { formatearFecha } from "@/lib/time";
import { env } from "@/lib/env";

export const dynamic = "force-dynamic";

export default async function ConfirmacionPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | string[] | undefined>>;
}) {
  const sp = await searchParams;
  const token = typeof sp.token === "string" ? sp.token : "";
  const cita = token ? await obtenerCitaPorToken(token) : null;
  if (!cita) redirect("/");

  const detalles: Array<[string, string, string]> = [
    ["fa-user", "Paciente", cita.nombre_paciente],
    ["fa-user-md", "Médico", `Dr. ${cita.medico_nombre ?? ""} ${cita.medico_apellido ?? ""}`],
    ["fa-stethoscope", "Especialidad", cita.especialidad ?? ""],
    ["fa-calendar", "Fecha", formatearFecha(cita.fecha)],
    ["fa-clock", "Hora", String(cita.hora ?? "").slice(0, 5)],
  ];
  const cancelUrl = `${env.appUrl}/cancelar-cita?token=${cita.token_cancelacion}`;

  return (
    <div className="max-w-2xl mx-auto px-4 py-16 fade-up">
      <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-10 text-center">
        <div className="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg" style={{ background: "linear-gradient(135deg,#22c55e,#16a34a)" }}>
          <i className="fas fa-check text-white text-3xl"></i>
        </div>
        <h1 className="text-3xl font-extrabold text-slate-800 mb-2">¡Cita Registrada!</h1>
        <p className="text-slate-500 mb-8">Su solicitud fue recibida. El personal confirmará el turno pronto.</p>

        <div className="bg-slate-50 rounded-2xl p-6 text-left space-y-3 mb-7 border border-slate-100">
          {detalles.map(([icon, label, val]) => (
            <div key={label} className="flex items-center justify-between py-1.5 border-b border-slate-100 last:border-0">
              <span className="flex items-center gap-2 text-slate-500 text-sm">
                <i className={`fas ${icon} text-sky-400 w-4 text-center`}></i> {label}
              </span>
              <span className="font-semibold text-slate-800 text-sm">{val}</span>
            </div>
          ))}
        </div>

        <div className="inline-flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 text-sm font-semibold px-4 py-2 rounded-full mb-7">
          <i className="fas fa-clock"></i> Pendiente de Confirmación
        </div>

        <div className="bg-red-50 border border-red-100 rounded-xl p-4 mb-7 text-left">
          <p className="text-red-700 text-sm font-semibold mb-2">
            <i className="fas fa-info-circle mr-1"></i>Guardá este enlace para cancelar si lo necesitás
          </p>
          <a href={cancelUrl} className="text-red-500 text-xs break-all hover:underline">{cancelUrl}</a>
        </div>

        <Link href="/" className="inline-flex items-center gap-2 text-white font-bold px-8 py-3 rounded-full shadow-lg hover:shadow-xl transition-all" style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}>
          <i className="fas fa-home"></i> Volver al Inicio
        </Link>
      </div>
    </div>
  );
}
