"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { signOut, useSession } from "next-auth/react";
import { useEffect, useState } from "react";
import { env } from "@/lib/env";

type Win = typeof window & {
  showToast?: (m: string, t?: "success" | "error" | "info") => void;
  openDeleteModal?: (action: string, text?: string) => void;
  closeDeleteModal?: () => void;
  openDrawer?: () => void;
  closeDrawer?: () => void;
};

export function PacienteShell({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const router = useRouter();
  const { data: session } = useSession();
  const user = session?.user;
  const initial = (user?.nombre ?? "P").charAt(0).toUpperCase();
  const [notifs, setNotifs] = useState<{ id: number; titulo: string; mensaje: string | null; created_at: string; tipo: string }[]>([]);
  const [notifCount, setNotifCount] = useState(0);
  const [notifOpen, setNotifOpen] = useState(false);
  const [drawerOpen, setDrawerOpen] = useState(false);

  useEffect(() => {
    cargarNotifs();
    const t = setInterval(cargarNotifs, 30000);
    return () => clearInterval(t);
  }, []);

  useEffect(() => {
    const w = window as Win;
    w.openDrawer = () => setDrawerOpen(true);
    w.closeDrawer = () => setDrawerOpen(false);
  }, []);

  async function cargarNotifs() {
    try {
      const r = await fetch("/api/notificaciones");
      if (!r.ok) return;
      const res = await r.json();
      if (!res.success) return;
      setNotifs(res.data || []);
      setNotifCount(res.count || 0);
    } catch {}
  }

  async function marcarLeidas() {
    await fetch("/api/notificaciones/marcar-leidas", { method: "POST" });
    cargarNotifs();
  }

  return (
    <div className="min-h-screen flex flex-col bg-slate-50">
      {/* Mobile Drawer */}
      <div
        className={`drawer-overlay ${drawerOpen ? "active" : ""}`}
        onClick={() => setDrawerOpen(false)}
      />
      <div className={`mobile-drawer ${drawerOpen ? "open" : ""}`}>
        <div className="flex items-center justify-between px-5 py-5 border-b border-slate-100">
          <div className="flex items-center gap-2.5">
            <div
              className="w-8 h-8 rounded-xl flex items-center justify-center text-white"
              style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
            >
              <i className="fas fa-heartbeat text-xs"></i>
            </div>
            <span className="font-extrabold text-slate-800 text-sm">{env.appName}</span>
          </div>
          <button onClick={() => setDrawerOpen(false)} className="text-slate-400 hover:text-slate-600 text-lg">
            <i className="fas fa-times"></i>
          </button>
        </div>
        <div className="flex-1 px-3 py-4">
          <Link onClick={() => setDrawerOpen(false)} href="/" className="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 font-semibold hover:bg-sky-50 transition">
            <i className="fas fa-home text-sky-500 w-5 text-center"></i> Inicio
          </Link>
          <Link onClick={() => setDrawerOpen(false)} href="/agendar" className="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 font-semibold hover:bg-sky-50 transition">
            <i className="fas fa-calendar-plus text-sky-500 w-5 text-center"></i> Agendar Cita
          </Link>
          <hr className="my-3 border-slate-100" />
          <Link onClick={() => setDrawerOpen(false)} href="/paciente/dashboard" className="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 font-medium hover:bg-slate-50 transition">
            <i className="fas fa-user-injured w-5 text-center"></i> Mi Cuenta
          </Link>
          <Link onClick={() => setDrawerOpen(false)} href="/admin" className="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 font-medium hover:bg-slate-50 transition">
            <i className="fas fa-lock w-5 text-center"></i> Acceso Personal
          </Link>
        </div>
      </div>

      {/* Navbar */}
      <nav className="bg-white/80 backdrop-blur-lg sticky top-0 z-50 border-b border-slate-100">
        <div className="max-w-5xl mx-auto px-4 sm:px-6">
          <div className="flex items-center justify-between h-16">
            <button
              onClick={() => setDrawerOpen(true)}
              className="md:hidden w-9 h-9 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 transition"
            >
              <i className="fas fa-bars text-base"></i>
            </button>

            <Link href="/" className="flex items-center gap-2.5 group">
              <div
                className="w-9 h-9 rounded-xl flex items-center justify-center text-white group-hover:shadow-lg transition-all"
                style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
              >
                <i className="fas fa-heartbeat text-sm"></i>
              </div>
              <span className="font-extrabold text-slate-800 text-lg hidden sm:inline">{env.appName}</span>
            </Link>

            <div className="hidden md:flex items-center gap-1">
              <Link href="/" className="px-4 py-2 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Inicio</Link>
              <Link href="/agendar" className="px-4 py-2 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Agendar</Link>
              <Link href="/paciente/dashboard" className="px-4 py-2 rounded-xl text-sm font-semibold text-sky-600 hover:bg-sky-50 transition">Mi Cuenta</Link>
            </div>

            <div className="flex items-center gap-1.5 sm:gap-3">
              <Link
                href="/agendar"
                className="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold text-white shadow-md hover:shadow-lg transition-all"
                style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
              >
                <i className="fas fa-calendar-plus"></i>
                <span className="hidden sm:inline">Agendar</span>
              </Link>
              <button
                onClick={() => signOut({ callbackUrl: "/" })}
                className="text-slate-500 hover:text-sky-600 text-sm font-semibold transition flex items-center gap-1 px-2"
                title="Cerrar sesión"
              >
                <i className="fas fa-sign-out-alt text-xs"></i>
                <span className="hidden sm:inline">Salir</span>
              </button>
            </div>
          </div>
        </div>
      </nav>

      <div id="toastContainer" className="toast-container"></div>

      <main className="flex-1">{children}</main>

      <footer className="bg-white border-t border-slate-100">
        <div className="max-w-5xl mx-auto px-4 sm:px-6 py-6 text-center text-xs text-slate-400">
          <p>&copy; {new Date().getFullYear()} {env.appName}. Todos los derechos reservados.</p>
        </div>
      </footer>
    </div>
  );
}
