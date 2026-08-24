"use client";

import Link from "next/link";
import { useSession } from "next-auth/react";
import { env } from "@/lib/env";

type Win = typeof window & {
  openDrawer?: () => void;
  closeDrawer?: () => void;
};

export function PublicHeader() {
  const { data: session } = useSession();
  const appName = env.appName;
  const openDrawer = () => (window as Win).openDrawer?.();
  const closeDrawer = () => (window as Win).closeDrawer?.();

  return (
    <>
      {/* Mobile Drawer */}
      <div id="drawerOverlay" className="drawer-overlay" onClick={closeDrawer} />
      <div id="mobileDrawer" className="mobile-drawer">
        <div className="flex items-center justify-between px-5 py-5 border-b border-slate-100">
          <div className="flex items-center gap-2.5">
            <div
              className="w-8 h-8 rounded-xl flex items-center justify-center text-white"
              style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
            >
              <i className="fas fa-heartbeat text-xs"></i>
            </div>
            <span className="font-extrabold text-slate-800 text-sm">{appName}</span>
          </div>
          <button onClick={closeDrawer} className="text-slate-400 hover:text-slate-600 text-lg">
            <i className="fas fa-times"></i>
          </button>
        </div>
        <div className="flex-1 px-3 py-4">
          <Link
            href="/"
            className="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 font-semibold hover:bg-sky-50 transition"
          >
            <i className="fas fa-home text-sky-500 w-5 text-center"></i> Inicio
          </Link>
          <Link
            href="/agendar"
            className="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 font-semibold hover:bg-sky-50 transition"
          >
            <i className="fas fa-calendar-plus text-sky-500 w-5 text-center"></i> Agendar Cita
          </Link>
          <hr className="my-3 border-slate-100" />
          <Link
            href="/paciente/dashboard"
            className="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 font-medium hover:bg-slate-50 transition"
          >
            <i className="fas fa-user-injured w-5 text-center"></i> Mi Cuenta
          </Link>
          <Link
            href="/admin"
            className="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 font-medium hover:bg-slate-50 transition"
          >
            <i className="fas fa-lock w-5 text-center"></i> Acceso Personal
          </Link>
        </div>
      </div>

      {/* Navbar */}
      <nav id="mainNav" className="bg-white/80 backdrop-blur-lg sticky top-0 z-50 border-b border-slate-100">
        <div className="max-w-7xl mx-auto px-4 sm:px-6">
          <div className="flex items-center justify-between h-16">
            <button
              onClick={openDrawer}
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
              <span className="font-extrabold text-slate-800 text-lg hidden sm:inline">{appName}</span>
            </Link>

            <div className="hidden md:flex items-center gap-1">
              <Link href="/" className="px-4 py-2 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">
                Inicio
              </Link>
              <Link href="/#especialidades" className="px-4 py-2 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">
                Especialidades
              </Link>
              <Link href="/#medicos" className="px-4 py-2 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">
                Médicos
              </Link>
              <Link href="/agendar" className="px-4 py-2 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">
                Agendar
              </Link>
              {session?.user && (
                <Link href="/paciente/dashboard" className="px-4 py-2 rounded-xl text-sm font-semibold text-sky-600 hover:bg-sky-50 transition">
                  Mi Cuenta
                </Link>
              )}
            </div>

            <div className="flex items-center gap-1.5 sm:gap-3">
              <Link
                href="/agendar"
                className="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold text-white shadow-md hover:shadow-lg transition-all"
                style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
              >
                <i className="fas fa-calendar-plus"></i>
                <span className="hidden sm:inline">Agendar Cita</span>
              </Link>
              <Link
                href="/paciente/login"
                className="text-slate-500 hover:text-sky-600 text-sm font-semibold transition flex items-center gap-1 px-2"
              >
                <i className="fas fa-user-injured text-xs"></i>
                <span className="hidden sm:inline">Paciente</span>
              </Link>
              <Link
                href="/admin"
                className="text-slate-400 hover:text-slate-700 text-sm font-medium transition flex items-center gap-1 px-2"
              >
                <i className="fas fa-lock text-xs"></i>
                <span className="hidden sm:inline">Personal</span>
              </Link>
            </div>
          </div>
        </div>
      </nav>
    </>
  );
}
