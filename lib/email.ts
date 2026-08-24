import nodemailer from "nodemailer";
import { env } from "./env";

let transporter: nodemailer.Transporter | null = null;

function getTransporter(): nodemailer.Transporter {
  if (!transporter) {
    transporter = nodemailer.createTransport({
      host: env.mail.host,
      port: env.mail.port,
      secure: env.mail.port === 465,
      auth: env.mail.user ? { user: env.mail.user, pass: env.mail.pass } : undefined,
    });
  }
  return transporter;
}

export async function enviarCorreo(
  to: string,
  asunto: string,
  html: string
): Promise<void> {
  if (!env.mail.user) {
    console.warn("[email] MAIL_USER no configurado, se omite envío a", to);
    return;
  }
  await getTransporter().sendMail({
    from: `"${env.mail.fromName}" <${env.mail.user}>`,
    to,
    subject: asunto,
    html,
  });
}
