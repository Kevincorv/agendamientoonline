import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { listarCitasAdmin } from "@/lib/repos/citas";
import { listarMedicosAdmin } from "@/lib/repos/medicos";
import { formatearFecha } from "@/lib/time";
import { EstadoBadge } from "@/components/estado-badge";
import { CitasCliente } from "@/components/admin/citas-cliente";

export const dynamic = "force-dynamic";

interface SP {
  searchParams: Promise<Record<string, string | string[] | undefined>>;
}

export default async function AdminCitasPage({ searchParams }: SP) {
  const session = await auth();
  if (!session?.user) redirect("/admin/login");
  if (session.user.rol === "paciente") redirect("/paciente/dashboard");
  if (session.user.rol === "medico") redirect("/medico/dashboard");

  const sp = await searchParams;
  const filtros = {
    fecha: typeof sp.fecha === "string" ? sp.fecha : "",
    medico_id: Number(sp.medico_id ?? 0) || undefined,
    estado_id: Number(sp.estado_id ?? 0) || undefined,
    q: typeof sp.q === "string" ? sp.q : "",
    porPagina: Number(sp.porPagina ?? 15) || 15,
    pagina: Number(sp.pagina ?? 1) || 1,
  };

  const [resultado, medicos] = await Promise.all([
    listarCitasAdmin(filtros),
    listarMedicosAdmin(),
  ]);

  const rows = resultado.datos.map((c) => ({
    id: c.id,
    nombre_paciente: c.nombre_paciente,
    telefono: c.telefono ?? "",
    medico_nombre: c.medico_nombre ?? "",
    medico_apellido: c.medico_apellido ?? "",
    especialidad: c.especialidad ?? "",
    fecha: c.fecha instanceof Date ? c.fecha.toISOString().slice(0, 10) : String(c.fecha),
    hora: String(c.hora ?? "").slice(0, 5),
    estado: c.estado ?? "",
    estado_id: c.estado_id,
    medico_id: c.medico_id,
  }));

  return (
    <CitasCliente
      citas={rows}
      medicos={medicos.map((m) => ({
        id: m.id,
        nombre: m.nombre,
        apellido: m.apellido,
      }))}
      paginacion={{
        total: resultado.total,
        pagina: resultado.pagina,
        paginas: resultado.paginas,
        porPagina: resultado.porPagina,
      }}
      filtros={filtros}
    />
  );
}
