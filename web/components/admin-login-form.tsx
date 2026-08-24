"use client";

import { useState } from "react";
import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { signIn } from "next-auth/react";
import { env } from "@/lib/env";

type Win = typeof window & {
  showToast?: (m: string, t?: "success" | "error" | "info") => void;
};

export function AdminLoginForm() {
  const router = useRouter();
  const sp = useSearchParams();
  const callbackUrl = sp.get("callbackUrl") || "/admin/dashboard";
  const urlError = sp.get("error");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPwd, setShowPwd] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(urlError ? "Credenciales incorrectas o cuenta bloqueada." : null);

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setError(null);
    const res = await signIn("credentials", { email, password, redirect: false });
    setLoading(false);
    if (res?.error) {
      setError("Credenciales incorrectas o cuenta bloqueada.");
      (window as Win).showToast?.("Credenciales incorrectas o cuenta bloqueada.", "error");
    } else {
      // Detectamos el rol desde el endpoint /api/me para redirigir bien
      try {
        const r = await fetch("/api/me");
        const data = await r.json();
        const url =
          data.rol === "medico" ? "/medico/dashboard" :
          data.rol === "paciente" ? "/paciente/dashboard" :
          callbackUrl;
        router.push(url);
      } catch {
        router.push(callbackUrl);
      }
      router.refresh();
    }
  }

  return (
    <div
      style={{
        background: "linear-gradient(135deg,#0f172a 0%,#1e3a5f 50%,#0e7490 100%)",
        minHeight: "100vh",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        padding: 12,
      }}
    >
      <div className="w-full max-w-md fade-up px-2 sm:px-0">
        <div className="text-center mb-6 sm:mb-8">
          <div
            className="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl"
            style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
          >
            <i className="fas fa-hospital-alt text-white text-2xl"></i>
          </div>
          <h1 className="text-white font-bold text-2xl">{env.appName}</h1>
          <p className="text-sky-300 text-sm mt-1">Panel de Administración</p>
        </div>

        <div className="bg-white rounded-2xl shadow-2xl p-8">
          {error && (
            <div className="mb-5 flex items-center gap-3 p-3.5 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-800">
              <i className="fas fa-exclamation-circle text-red-500"></i>
              {error}
            </div>
          )}

          <form onSubmit={onSubmit} className="space-y-5">
            <div>
              <label className="block text-sm font-semibold text-slate-700 mb-1.5">Correo electrónico</label>
              <div className="relative">
                <span className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">
                  <i className="fas fa-envelope"></i>
                </span>
                <input
                  type="email"
                  required
                  autoFocus
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="w-full border-2 border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm focus:border-sky-500 focus:outline-none"
                  placeholder="correo@clinica.com"
                />
              </div>
            </div>

            <div>
              <label className="block text-sm font-semibold text-slate-700 mb-1.5">Contraseña</label>
              <div className="relative">
                <span className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">
                  <i className="fas fa-lock"></i>
                </span>
                <input
                  type={showPwd ? "text" : "password"}
                  required
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="w-full border-2 border-slate-200 rounded-xl pl-10 pr-12 py-3 text-sm focus:border-sky-500 focus:outline-none"
                  placeholder="••••••••"
                />
                <button
                  type="button"
                  onClick={() => setShowPwd((v) => !v)}
                  className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                >
                  <i className={`fas ${showPwd ? "fa-eye-slash" : "fa-eye"} text-sm`}></i>
                </button>
              </div>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full py-3 rounded-xl font-bold text-white text-base transition-all shadow-lg hover:shadow-xl disabled:opacity-60"
              style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
            >
              {loading ? (
                <span className="spinner inline-block align-middle mr-2" style={{ width: 14, height: 14, borderWidth: 2 }} />
              ) : (
                <i className="fas fa-sign-in-alt mr-2"></i>
              )}
              Iniciar Sesión
            </button>
          </form>

          <div className="mt-6 pt-5 border-t border-slate-100 text-center">
            <Link href="/" className="text-slate-400 hover:text-sky-600 text-sm font-medium transition">
              <i className="fas fa-arrow-left mr-1"></i>Volver al sitio
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
