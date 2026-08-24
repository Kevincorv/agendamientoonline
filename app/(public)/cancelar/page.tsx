import { redirect } from "next/navigation";
import Link from "next/link";
import { obtenerCitaPorToken } from "@/lib/repos/citas";
import { formatearFecha } from "@/lib/time";
import { cancelarCita } from "./actions";

export const dynamic = "force-dynamic";

export default async function CancelarPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | string[] | undefined>>;
}) {
  const sp = await searchParams;
  const token = typeof sp.token === "string" ? sp.token : "";
  const cita = token ? await obtenerCitaPorToken(token) : null;
  if (!cita) redirect("/");

  const rows: Array<[string, string]> = [
    ["Paciente", cita.nombre_paciente],
    ["Médico", `Dr. ${cita.medico_nombre ?? ""} ${cita.medico_apellido ?? ""}`],
    ["Fecha", formatearFecha(cita.fecha)],
    ["Hora", String(cita.hora ?? "").slice(0, 5)],
  ];

  return (
    <div className="max-w-lg mx-auto px-4 py-16 fade-up">
      <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-10">
        <div className="text-center mb-7">
          <div className="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md" style={{ background: "linear-gradient(135deg,#ef4444,#dc2626)" }}>
            <i className="fas fa-calendar-times text-white text-2xl"></i>
          </div>
          <h1 className="text-2xl font-extrabold text-slate-800">Cancelar Cita</h1>
          <p className="text-slate-400 text-sm mt-1">Esta acción no se puede deshacer</p>
        </div>

        <div className="bg-slate-50 border border-slate-100 rounded-xl p-5 space-y-2.5 text-sm mb-7">
          {rows.map(([label, val]) => (
            <div key={label} className="flex justify-between items-center border-b border-slate-100 last:border-0 pb-2 last:pb-0">
              <span className="text-slate-400">{label}</span>
              <span className="font-semibold text-slate-800">{val}</span>
            </div>
          ))}
        </div>

        <form action={cancelarCita}>
          <input type="hidden" name="token" value={cita.token_cancelacion ?? ""} />
          <button type="submit" className="w-full py-3.5 rounded-xl text-white font-bold text-base shadow-lg hover:opacity-90 transition mb-3" style={{ background: "linear-gradient(135deg,#ef4444,#dc2626)" }}>
            <i className="fas fa-times-circle mr-2"></i>Confirmar Cancelación
          </button>
        </form>

        <Link href="/" className="block text-center text-slate-400 hover:text-slate-600 text-sm font-medium transition py-2">
          Volver sin cancelar
        </Link>
      </div>
    </div>
  );
}
