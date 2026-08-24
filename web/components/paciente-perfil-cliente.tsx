"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { useFormStatus } from "react-dom";
import Link from "next/link";

type Win = typeof window & { showToast?: (m: string, t?: "success" | "error" | "info") => void };

export function PacientePerfilCliente({
  user,
}: {
  user: { id: number; nombre: string; apellido: string; email: string; last_login: string | null };
}) {
  const router = useRouter();
  const [tab, setTab] = useState<"datos" | "password">("datos");
  return (
    <div className="max-w-2xl mx-auto px-4 py-6 fade-up">
      <div className="flex items-center justify-between mb-5">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-800">Mi Perfil</h1>
          <p className="text-sm text-slate-400">Tus datos personales y de cuenta</p>
        </div>
        <Link href="/paciente/dashboard" className="btn btn-secondary btn-sm">
          <i className="fas fa-arrow-left"></i> Volver
        </Link>
      </div>

      <div className="card p-5 mb-4 flex items-center gap-4" style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}>
        <div className="w-16 h-16 rounded-xl flex items-center justify-center text-2xl font-bold bg-white/20 backdrop-blur-sm flex-shrink-0 text-white">
          {user.nombre.charAt(0).toUpperCase()}{user.apellido.charAt(0).toUpperCase()}
        </div>
        <div className="text-white flex-1 min-w-0">
          <p className="font-extrabold truncate">{user.nombre} {user.apellido}</p>
          <p className="text-xs text-sky-200 truncate">{user.email}</p>
          {user.last_login && <p className="text-[10px] text-sky-300 mt-0.5">Último acceso: {user.last_login}</p>}
        </div>
      </div>

      <div className="flex border-b border-slate-200 mb-5">
        <button
          onClick={() => setTab("datos")}
          className={`px-4 py-2 text-sm font-bold border-b-2 transition ${tab === "datos" ? "border-sky-500 text-sky-600" : "border-transparent text-slate-500 hover:text-slate-700"}`}
        >
          <i className="fas fa-user mr-1"></i> Datos personales
        </button>
        <button
          onClick={() => setTab("password")}
          className={`px-4 py-2 text-sm font-bold border-b-2 transition ${tab === "password" ? "border-sky-500 text-sky-600" : "border-transparent text-slate-500 hover:text-slate-700"}`}
        >
          <i className="fas fa-key mr-1"></i> Contraseña
        </button>
      </div>

      {tab === "datos" ? (
        <form
          action={async (fd) => {
            const r = await fetch("/api/paciente/perfil", { method: "POST", body: fd });
            const res = await r.json();
            (window as Win).showToast?.(res.message ?? "OK", res.success ? "success" : "error");
            if (res.success) router.refresh();
          }}
          className="card p-6 space-y-4"
        >
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-sm font-semibold text-slate-700 mb-1.5">Nombre *</label>
              <input type="text" name="nombre" defaultValue={user.nombre} className="input-field" required />
            </div>
            <div>
              <label className="block text-sm font-semibold text-slate-700 mb-1.5">Apellido *</label>
              <input type="text" name="apellido" defaultValue={user.apellido} className="input-field" required />
            </div>
          </div>
          <div>
            <label className="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
            <input type="email" value={user.email} disabled className="input-field opacity-60 cursor-not-allowed" />
            <p className="text-[10px] text-slate-400 mt-1">El email no puede modificarse</p>
          </div>
          <Submit label="Guardar cambios" />
        </form>
      ) : (
        <form
          action={async (fd) => {
            const r = await fetch("/api/paciente/password", { method: "POST", body: fd });
            const res = await r.json();
            (window as Win).showToast?.(res.message ?? "OK", res.success ? "success" : "error");
            if (res.success) {
              const f = (document.getElementById("form-pwd") as HTMLFormElement | null);
              f?.reset();
            }
          }}
          id="form-pwd"
          className="card p-6 space-y-4"
        >
          <div>
            <label className="block text-sm font-semibold text-slate-700 mb-1.5">Contraseña actual *</label>
            <input type="password" name="password_actual" required minLength={6} className="input-field" />
          </div>
          <div>
            <label className="block text-sm font-semibold text-slate-700 mb-1.5">Nueva contraseña *</label>
            <input type="password" name="password_nueva" required minLength={6} className="input-field" />
          </div>
          <div>
            <label className="block text-sm font-semibold text-slate-700 mb-1.5">Confirmar nueva contraseña *</label>
            <input type="password" name="password_confirmar" required minLength={6} className="input-field" />
          </div>
          <Submit label="Cambiar contraseña" />
        </form>
      )}
    </div>
  );
}

function Submit({ label }: { label: string }) {
  const { pending } = useFormStatus();
  return (
    <button type="submit" className="btn btn-primary" disabled={pending}>
      {pending ? <span className="spinner" /> : <><i className="fas fa-save"></i> {label}</>}
    </button>
  );
}
