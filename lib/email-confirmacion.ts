import { env } from "./env";
import { enviarCorreo } from "./email";
import { formatearFecha } from "./time";
import type { CitaDetalle } from "./repos/citas";

// Port fiel de helpers/email.php (enviarConfirmacionCita).
export async function enviarConfirmacionCita(cita: CitaDetalle): Promise<void> {
  if (!cita.email) return;
  const cancelUrl = `${env.appUrl}/cancelar-cita?token=${cita.token_cancelacion}`;
  const asunto = `Confirmación de cita - ${env.appName}`;
  const html = `
  <html><body style='font-family: Arial, sans-serif; color: #333;'>
    <div style='max-width:600px;margin:auto;padding:20px;border:1px solid #e5e7eb;border-radius:8px;'>
      <h2 style='color:#1e40af;'>✅ Cita Agendada - ${env.appName}</h2>
      <p>Estimado/a <strong>${cita.nombre_paciente}</strong>,</p>
      <p>Su cita ha sido registrada exitosamente:</p>
      <table style='width:100%;border-collapse:collapse;'>
        <tr><td style='padding:8px;background:#f3f4f6;'><strong>Médico:</strong></td><td style='padding:8px;'>${cita.medico_nombre ?? ""} ${cita.medico_apellido ?? ""}</td></tr>
        <tr><td style='padding:8px;background:#f3f4f6;'><strong>Especialidad:</strong></td><td style='padding:8px;'>${cita.especialidad ?? ""}</td></tr>
        <tr><td style='padding:8px;background:#f3f4f6;'><strong>Fecha:</strong></td><td style='padding:8px;'>${formatearFecha(cita.fecha)}</td></tr>
        <tr><td style='padding:8px;background:#f3f4f6;'><strong>Hora:</strong></td><td style='padding:8px;'>${cita.hora}</td></tr>
      </table>
      <p style='margin-top:20px;'>Si necesita cancelar su cita, haga clic en el siguiente enlace:</p>
      <a href='${cancelUrl}' style='background:#ef4444;color:white;padding:10px 20px;text-decoration:none;border-radius:6px;'>Cancelar Cita</a>
      <p style='margin-top:20px;font-size:12px;color:#6b7280;'>Este es un correo automático, por favor no responda a este mensaje.</p>
    </div>
  </body></html>`;
  await enviarCorreo(cita.email, asunto, html);
}
