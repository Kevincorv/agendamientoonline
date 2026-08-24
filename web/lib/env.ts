export const env = {
  appName: process.env.APP_NAME ?? "CITAS MÉDICAS ONLINE",
  appUrl: (process.env.APP_URL ?? "http://localhost:3000").replace(/\/$/, ""),
  appVersion: process.env.APP_VERSION ?? "2.0.0",
  timezone: process.env.TIMEZONE ?? "America/Asuncion",
  authSecret: process.env.AUTH_SECRET ?? "",
  trustHost: process.env.AUTH_TRUST_HOST === "true",
  mail: {
    host: process.env.MAIL_HOST ?? "smtp.gmail.com",
    port: Number(process.env.MAIL_PORT ?? 587),
    user: process.env.MAIL_USER ?? "",
    pass: process.env.MAIL_PASS ?? "",
    fromName: process.env.MAIL_FROM_NAME ?? "CITAS MÉDICAS ONLINE",
    recordatorioHoras: Number(process.env.MAIL_RECORDATORIO_HORAS ?? 24),
  },
};

export const dbConfig = {
  url: process.env.DATABASE_URL,
  host: process.env.DB_HOST,
  port: Number(process.env.DB_PORT ?? 3306),
  user: process.env.DB_USER ?? "",
  password: process.env.DB_PASS ?? "",
  database: process.env.DB_NAME ?? "clinica_san_luis",
};
