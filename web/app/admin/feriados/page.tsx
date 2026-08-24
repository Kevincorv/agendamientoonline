import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { listarFeriados } from "@/lib/repos/feriados";
import { FeriadosCliente } from "@/components/admin/feriados-cliente";

export const dynamic = "force-dynamic";

export default async function FeriadosPage() {
  const session = await auth();
  if (!session?.user) redirect("/admin/login");
  if (session.user.rol === "paciente") redirect("/paciente/dashboard");
  if (session.user.rol === "medico") redirect("/medico/dashboard");

  const feriados = await listarFeriados(true);
  return (
    <FeriadosCliente
      feriados={feriados.map((f) => ({
        id: f.id,
        fecha: f.fecha instanceof Date ? f.fecha.toISOString().slice(0, 10) : String(f.fecha),
        motivo: f.motivo,
        activo: f.activo,
      }))}
    />
  );
}
