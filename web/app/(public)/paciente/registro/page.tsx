"use client";

import { useActionState } from "react";
import Link from "next/link";
import { registrarPaciente } from "./actions";

export default function RegistroPage() {
  const [state, formAction, pending] = useActionState(registrarPaciente, undefined);

  return (
    <div className="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
      <div className="w-full max-w-md fade-up">
        <div className="text-center mb-8">
          <div
            className="w-16 h-16 rounded-xl flex items-center justify-center text-white mx-auto mb-4"
            style={{ background: "linear-gradient(135deg,#059669,#10b981)" }}
          >
            <i className="fas fa-user-plus text-2xl"></i>
          </div>
          <h1 className="text-2xl font-extrabold text-slate-800">Crear Cuenta</h1>
          <p className="text-sm text-slate-500 mt-1">Registrate para agendar y gestionar tus citas</p>
        </div>

        <div className="card p-6">
          {state?.error && (
            <div className="mb-4 flex items-center gap-3 p-3 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-800">
              <i className="fas fa-exclamation-circle text-red-500"></i>
              {state.error}
            </div>
          )}
          <form action={formAction} data-loading>
            <div className="grid grid-cols-2 gap-3 mb-4">
              <div className="floating-label">
                <input type="text" name="nombre" required autoComplete="given-name" placeholder=" " />
                <label>Nombre</label>
              </div>
              <div className="floating-label">
                <input type="text" name="apellido" required autoComplete="family-name" placeholder=" " />
                <label>Apellido</label>
              </div>
            </div>

            <div className="floating-label mb-4">
              <input type="email" name="email" required autoComplete="email" inputMode="email" placeholder=" " />
              <label>Correo electrónico</label>
            </div>

            <div className="floating-label mb-4">
              <input type="password" name="password" required minLength={6} autoComplete="new-password" placeholder=" " />
              <label>Contraseña (mín. 6 caracteres)</label>
            </div>

            <div className="floating-label mb-6">
              <input type="password" name="password_confirm" required minLength={6} autoComplete="new-password" placeholder=" " />
              <label>Confirmar contraseña</label>
            </div>

            <button type="submit" className="btn btn-primary w-full" disabled={pending}>
              {pending ? <span className="spinner" /> : <><i className="fas fa-user-check"></i> Crear Cuenta</>}
            </button>
          </form>

          <div className="mt-6 text-center">
            <Link href="/paciente/login" className="text-xs font-semibold text-sky-600 hover:text-sky-700 transition inline-flex items-center gap-1">
              <i className="fas fa-sign-in-alt"></i> ¿Ya tenés cuenta? Iniciá sesión
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
