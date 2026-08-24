"use client";

import { useState } from "react";
import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { signIn } from "next-auth/react";

export default function PacienteLoginPage() {
  const router = useRouter();
  const sp = useSearchParams();
  const callbackUrl = sp.get("callbackUrl") || "/paciente/dashboard";
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setError(null);
    const res = await signIn("credentials", { email, password, redirect: false });
    setLoading(false);
    if (res?.error) {
      setError("Credenciales incorrectas.");
    } else {
      // Validamos que sea paciente
      try {
        const r = await fetch("/api/me");
        const data = await r.json();
        if (data.rol !== "paciente") {
          setError("Esta área es solo para pacientes.");
          setLoading(false);
          return;
        }
      } catch {}
      router.push(callbackUrl);
      router.refresh();
    }
  }

  return (
    <div
      className="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4"
      style={{ background: "linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 50%,#f0fdf4 100%)" }}
    >
      <div className="w-full max-w-md fade-up">
        <div className="text-center mb-8">
          <div
            className="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-xl ring-4 ring-white/50"
            style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
          >
            <i className="fas fa-user-injured text-white text-3xl"></i>
          </div>
          <h1 className="text-2xl font-extrabold text-slate-800">Portal del Paciente</h1>
          <p className="text-sm text-slate-500 mt-1">Ingresá con tu cuenta para gestionar tus citas</p>
        </div>

        <div className="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl shadow-sky-100/50 border border-white/60 p-8">
          {error && (
            <div className="mb-5 flex items-center gap-3 p-3.5 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-800">
              <i className="fas fa-exclamation-circle text-red-500"></i>
              {error}
            </div>
          )}
          <form onSubmit={onSubmit} className="space-y-5">
            <div className="floating-label">
              <input
                type="email"
                required
                autoComplete="email"
                inputMode="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="input-field"
                placeholder=" "
              />
              <label>Correo electrónico</label>
            </div>

            <div className="floating-label">
              <input
                type="password"
                required
                autoComplete="current-password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className="input-field"
                placeholder=" "
              />
              <label>Contraseña</label>
            </div>

            <div className="flex items-center justify-between text-sm">
              <label className="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" className="rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                <span className="text-slate-500">Recordarme</span>
              </label>
              <Link href="/paciente/registro" className="font-semibold text-sky-600 hover:text-sky-700 transition">
                ¿No tenés cuenta?
              </Link>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="btn btn-primary w-full py-3.5 rounded-2xl shadow-lg hover:shadow-xl transition-all"
            >
              {loading ? <span className="spinner" /> : <><i className="fas fa-sign-in-alt"></i> Iniciar Sesión</>}
            </button>
          </form>

          <div className="mt-6 pt-6 border-t border-slate-100 text-center">
            <Link href="/" className="text-slate-400 hover:text-sky-600 transition inline-flex items-center gap-2 text-sm font-medium">
              <i className="fas fa-arrow-left"></i> Volver al inicio
            </Link>
            <span className="mx-3 text-slate-200">|</span>
            <Link href="/admin" className="text-slate-400 hover:text-sky-600 transition inline-flex items-center gap-2 text-sm font-medium">
              <i className="fas fa-lock"></i> Acceso personal
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}