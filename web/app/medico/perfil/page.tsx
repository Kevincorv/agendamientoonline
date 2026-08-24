import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { obtenerMedicoPorUsuarioId } from "@/lib/repos/medicos";
import { MedicoPerfilCliente } from "@/components/medico-perfil-cliente";

export const dynamic = "force-dynamic";

export default async function MedicoPerfilPage() {
  const session = await auth();
  if (!session?.user) redirect("/admin/login");
  if (session.user.rol !== "medico") redirect("/admin/dashboard");

  const med = await obtenerMedicoPorUsuarioId(session.user.id);
  if (!med) redirect("/admin/login");

  return (
    <MedicoPerfilCliente
      medico={{
        id: med.id,
        nombre: med.nombre,
        apellido: med.apellido,
        email: med.email ?? "",
        telefono: med.telefono ?? "",
        matricula: med.matricula ?? "",
        descripcion: med.descripcion ?? "",
        disponible: med.disponible,
        especialidad_nombre: med.especialidad_nombre,
      }}
    />
  );
}
