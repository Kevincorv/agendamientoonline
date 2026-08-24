import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { listarAccionesUnicas, listarAuditoria, listarTablasUnicas } from "@/lib/repos/auditoria";
import { AuditoriaCliente } from "@/components/admin/auditoria-cliente";
import { toMysqlDateTime } from "@/lib/time";

export const dynamic = "force-dynamic";

interface SP {
  searchParams: Promise<Record<string, string | string[] | undefined>>;
}

export default async function AuditoriaPage({ searchParams }: SP) {
  const session = await auth();
  if (!session?.user) redirect("/admin/login");
  if (session.user.rol === "paciente" || session.user.rol === "medico") redirect("/admin/dashboard");

  const sp = await searchParams;
  const filtros = {
    usuario_id: Number(sp.usuario_id ?? 0) || null,
    accion: typeof sp.accion === "string" && sp.accion ? sp.accion : null,
    tabla: typeof sp.tabla === "string" && sp.tabla ? sp.tabla : null,
    fecha_desde: typeof sp.fecha_desde === "string" && sp.fecha_desde ? sp.fecha_desde : null,
    fecha_hasta: typeof sp.fecha_hasta === "string" && sp.fecha_hasta ? sp.fecha_hasta : null,
    ip: typeof sp.ip === "string" && sp.ip ? sp.ip : null,
  };
  const pagina = Number(sp.pagina ?? 1) || 1;

  const [listado, acciones, tablas] = await Promise.all([
    listarAuditoria(filtros, pagina, 25),
    listarAccionesUnicas(),
    listarTablasUnicas(),
  ]);

  return (
    <AuditoriaCliente
      logs={listado.datos.map((l) => ({
        ...l,
        created_at: toMysqlDateTime(l.created_at),
      }))}
      paginacion={{ total: listado.total, pagina: listado.pagina, paginas: listado.paginas, porPagina: listado.porPagina }}
      acciones={acciones.map((a) => a.accion)}
      tablas={tablas.map((t) => t.tabla ?? "").filter(Boolean)}
      filtros={filtros}
    />
  );
}
