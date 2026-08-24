"use server";

import { redirect } from "next/navigation";
import { crearCita, obtenerCitaPorToken, estaDisponible } from "@/lib/repos/citas";
import { todayInTz, nowTimeInTz } from "@/lib/time";
import { crearNotificacion } from "@/lib/notifications";
import { enviarConfirmacionCita } from "@/lib/email-confirmacion";

export type GuardarState = { error?: string } | undefined;

export async function guardarCita(_prev: GuardarState, formData: FormData): Promise<GuardarState> {
  const medicoId = Number(formData.get("medico_id")) || 0;
  const especialidadId = Number(formData.get("especialidad_id")) || 0;
  const nombre_paciente = String(formData.get("nombre_paciente") ?? "").trim();
  const telefono = String(formData.get("telefono") ?? "").trim();
  const email = String(formData.get("email") ?? "").trim();
  const motivo = String(formData.get("motivo") ?? "").trim();
  const fecha = String(formData.get("fecha") ?? "");
  const hora = String(formData.get("hora") ?? "");

  if (!medicoId || !especialidadId || !nombre_paciente || !telefono || !fecha || !hora) {
    return { error: "Por favor completá todos los campos obligatorios." };
  }
  if (fecha < todayInTz()) return { error: "La fecha no puede ser en el pasado." };
  if (fecha === todayInTz() && hora < nowTimeInTz()) {
    return { error: "Este horario ya pasó. Seleccioná otro." };
  }
  if (!(await estaDisponible(medicoId, fecha, hora))) {
    return { error: "Este horario ya no está disponible. Por favor seleccioná otro." };
  }

  const token = await crearCita({
    medico_id: medicoId,
    especialidad_id: especialidadId,
    nombre_paciente,
    telefono,
    email,
    motivo,
    fecha,
    hora,
  });

  const cita = await obtenerCitaPorToken(token);
  if (cita) {
    await crearNotificacion(
      "Nueva cita registrada",
      `${cita.nombre_paciente} - ${cita.fecha} ${cita.hora}`,
      "info"
    );
    if (cita.email) {
      await enviarConfirmacionCita(cita).catch(() => {});
    }
  }

  redirect(`/confirmacion?token=${token}`);
}
