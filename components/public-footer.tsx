import Link from "next/link";
import { env } from "@/lib/env";

export function PublicFooter() {
  const appName = env.appName;
  return (
    <footer className="bg-white border-t border-slate-100 no-print">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 py-12">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
          <div className="sm:col-span-2 lg:col-span-1">
            <div className="flex items-center gap-2.5 mb-3">
              <div
                className="w-9 h-9 rounded-xl flex items-center justify-center text-white"
                style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
              >
                <i className="fas fa-heartbeat text-sm"></i>
              </div>
              <span className="font-extrabold text-slate-800 text-sm">{appName}</span>
            </div>
            <p className="text-xs text-slate-400 leading-relaxed max-w-xs">
              Sistema de gestión de citas médicas online. Agendá tu consulta de forma rápida, segura y sin filas.
            </p>
          </div>
          <div>
            <h4 className="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Navegación</h4>
            <ul className="space-y-2">
              <li><Link href="/" className="text-xs text-slate-400 hover:text-sky-600 transition">Inicio</Link></li>
              <li><Link href="/agendar" className="text-xs text-slate-400 hover:text-sky-600 transition">Agendar Cita</Link></li>
              <li><Link href="/#especialidades" className="text-xs text-slate-400 hover:text-sky-600 transition">Especialidades</Link></li>
              <li><Link href="/#medicos" className="text-xs text-slate-400 hover:text-sky-600 transition">Médicos</Link></li>
            </ul>
          </div>
          <div>
            <h4 className="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Accesos</h4>
            <ul className="space-y-2">
              <li><Link href="/admin" className="text-xs text-slate-400 hover:text-sky-600 transition">Panel Administrativo</Link></li>
              <li><Link href="/admin" className="text-xs text-slate-400 hover:text-sky-600 transition">Iniciar Sesión</Link></li>
            </ul>
          </div>
          <div>
            <h4 className="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Contacto</h4>
            <ul className="space-y-2">
              <li className="text-xs text-slate-400 flex items-center gap-2">
                <i className="fas fa-phone text-sky-500 w-3"></i> (021) 000-000
              </li>
              <li className="text-xs text-slate-400 flex items-center gap-2">
                <i className="fas fa-envelope text-sky-500 w-3"></i> info@clinica.com
              </li>
            </ul>
          </div>
        </div>
        <div className="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
          <p className="text-slate-400 text-xs">&copy; {new Date().getFullYear()} {appName}. Todos los derechos reservados.</p>
          <p className="text-slate-300 text-xs">
            Hecho con <i className="fas fa-heart text-red-400"></i> para la comunidad
          </p>
        </div>
      </div>
    </footer>
  );
}
