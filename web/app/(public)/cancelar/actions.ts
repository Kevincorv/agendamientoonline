"use server";

import { redirect } from "next/navigation";
import { obtenerCitaPorToken, cancelarPorToken } from "@/lib/repos/citas";
import { auditLog } from "@/lib/audit";
import { crearNotificacion } from "@/lib/notifications";

export async function cancelarCita(formData: FormData): Promise<void> {
  const token = String(formData.get("token") ?? "");
  const cita = token ? await obtenerCitaPorToken(token) : null;
  if (!cita) redirect("/");

  const ok = await cancelarPorToken(token);
  if (ok) {
    await auditLog({
      accion: "cancelar",
      tabla: "citas",
      registroId: cita.id,
      descripcion: `Cita #${cita.id} cancelada por token por ${cita.nombre_paciente}`,
    });
    await crearNotificacion(
      "Cita cancelada",
      `${cita.nombre_paciente} canceló cita #${cita.id}`,
      "warning"
    );
  }
  redirect("/");
}
