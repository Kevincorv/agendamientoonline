import type { Metadata, Viewport } from "next";
import type { ReactNode } from "react";
import "./globals.css";
import { env } from "@/lib/env";
import { Providers } from "./providers";
import { AppScripts } from "@/components/app-scripts";

export const metadata: Metadata = {
  title: { default: `${env.appName} - Sistema de Citas`, template: `%s — ${env.appName}` },
  description: "Sistema de agendamiento de citas médicas online.",
  manifest: "/manifest.json",
  applicationName: env.appName,
  appleWebApp: { capable: true, statusBarStyle: "default", title: env.appName },
  icons: {
    icon: "/assets/icons/icon-192.svg",
    apple: "/assets/icons/icon-192.svg",
  },
};

export const viewport: Viewport = {
  themeColor: "#0284c7",
  width: "device-width",
  initialScale: 1,
};

export default function RootLayout({ children }: { children: ReactNode }) {
  return (
    <html lang="es">
      <head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="" />
        <link
          href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
          rel="stylesheet"
        />
        <link
          rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        />
      </head>
      <body>
        <Providers>
          {children}
          <AppScripts />
        </Providers>
      </body>
    </html>
  );
}
