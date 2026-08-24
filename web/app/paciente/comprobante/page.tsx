import { redirect } from "next/navigation";
import Link from "next/link";
import { auth } from "@/lib/auth";
import { obtenerCitaPorId } from "@/lib/repos/citas";
import { formatearFecha } from "@/lib/time";
import { env } from "@/lib/env";

export const dynamic = "force-dynamic";

export default async function ComprobantePage({ searchParams }: { searchParams: Promise<Record<string, string | string[] | undefined>> }) {
  const session = await auth();
  if (!session?.user) redirect("/paciente/login");
  if (session.user.rol !== "paciente") redirect("/admin/dashboard");

  const sp = await searchParams;
  const id = Number(sp.id ?? 0);
  if (!id) redirect("/paciente/dashboard");

  const cita = await obtenerCitaPorId(id);
  if (!cita || cita.email !== session.user.email) redirect("/paciente/dashboard");

  return (
    <div className="max-w-2xl mx-auto px-4 py-8 fade-up">
      <div className="mb-4 flex items-center justify-between no-print">
        <Link href="/paciente/dashboard" className="btn btn-secondary btn-sm">
          <i className="fas fa-arrow-left"></i> Volver
        </Link>
        <button onClick={() => window.print()} className="btn btn-primary btn-sm">
          <i className="fas fa-print"></i> Imprimir
        </button>
      </div>

      <div className="bg-white rounded-2xl shadow-md border border-slate-200 p-8">
        <div className="text-center mb-6">
          <div
            className="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-md"
            style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
          >
            <i className="fas fa-heartbeat text-white text-2xl"></i>
          </div>
          <h1 className="text-xl font-extrabold text-slate-800">{env.appName}</h1>
          <p className="text-sm text-slate-400">Comprobante de cita médica</p>
        </div>

        <div className="border-t border-b border-slate-200 py-5 space-y-3">
          <Row label="Paciente" value={cita.nombre_paciente} />
          <Row label="Documento / Email" value={cita.email ?? "—"} />
          <Row label="Teléfono" value={cita.telefono ?? "—"} />
          <Row label="Médico" value={`Dr. ${cita.medico_nombre} ${cita.medico_apellido}`} />
          <Row label="Especialidad" value={cita.especialidad ?? "—"} />
          <Row label="Fecha" value={formatearFecha(cita.fecha)} />
          <Row label="Hora" value={String(cita.hora).slice(0, 5)} />
          <Row label="Estado" value={cita.estado ?? "—"} />
          {cita.motivo && <Row label="Motivo" value={cita.motivo} />}
          {cita.notas_medico && <Row label="Notas del médico" value={cita.notas_medico} />}
        </div>

        <div className="mt-6 text-center text-[10px] text-slate-400">
          <p>Comprobante generado el {new Date().toLocaleString("es-PY")}</p>
          <p>Presentá este comprobante al momento de la consulta.</p>
        </div>
      </div>
    </div>
  );
}

function Row({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex justify-between gap-4">
      <span className="text-sm text-slate-500">{label}</span>
      <span className="text-sm font-semibold text-slate-800 text-right">{value}</span>
    </div>
  );
}
