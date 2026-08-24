import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import {
  reportesCitas,
  reportesPorEspecialidad,
  reportesPorMedico,
  reportesStats,
} from "@/lib/repos/citas";
import { listarMedicosAdmin } from "@/lib/repos/medicos";
import { listarEspecialidadesAdmin } from "@/lib/repos/especialidades";
import { ReportesCliente } from "@/components/admin/reportes-cliente";

export const dynamic = "force-dynamic";

interface SP {
  searchParams: Promise<Record<string, string | string[] | undefined>>;
}

export default async function ReportesPage({ searchParams }: SP) {
  const session = await auth();
  if (!session?.user) redirect("/admin/login");
  if (session.user.rol === "paciente" || session.user.rol === "medico") redirect("/admin/dashboard");

  const sp = await searchParams;
  const filtros = {
    desde: typeof sp.desde === "string" ? sp.desde : new Date().toISOString().slice(0, 8) + "01",
    hasta: typeof sp.hasta === "string" ? sp.hasta : new Date().toISOString().slice(0, 10),
    medico_id: Number(sp.medico_id ?? 0) || undefined,
    especialidad_id: Number(sp.especialidad_id ?? 0) || undefined,
    estado_id: Number(sp.estado_id ?? 0) || undefined,
    q: typeof sp.q === "string" ? sp.q : "",
  };
  const pagina = Number(sp.pagina ?? 1) || 1;

  const [stats, porMedico, porEsp, citas, medicos, especialidades] = await Promise.all([
    reportesStats(filtros),
    reportesPorMedico(filtros),
    reportesPorEspecialidad(filtros),
    reportesCitas(filtros, pagina, 20),
    listarMedicosAdmin(),
    listarEspecialidadesAdmin(),
  ]);

  const exportUrl = `/api/admin/citas/exportar?${new URLSearchParams(
    Object.entries(filtros).map(([k, v]) => [k, String(v ?? "")])
  ).toString()}`;

  return (
    <ReportesCliente
      stats={{
        total: Number(stats?.total ?? 0),
        pendientes: Number(stats?.pendientes ?? 0),
        confirmadas: Number(stats?.confirmadas ?? 0),
        canceladas: Number(stats?.canceladas ?? 0),
        atendidas: Number(stats?.atendidas ?? 0),
      }}
      porMedico={porMedico}
      porEspecialidad={porEsp}
      citas={citas.datos}
      paginacion={{ total: citas.total, pagina: citas.pagina, paginas: citas.paginas, porPagina: citas.porPagina }}
      filtros={filtros}
      medicos={medicos.map((m) => ({ id: m.id, nombre: m.nombre, apellido: m.apellido }))}
      especialidades={especialidades.map((e) => ({ id: e.id, nombre: e.nombre }))}
      exportUrl={exportUrl}
    />
  );
}
