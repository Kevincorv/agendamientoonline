"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import { signOut, useSession } from "next-auth/react";
import { env } from "@/lib/env";

type Win = typeof window & {
  showToast?: (m: string, t?: "success" | "error" | "info") => void;
  openDeleteModal?: (action: string, text?: string) => void;
  closeDeleteModal?: () => void;
  toggleSidebar?: () => void;
  toggleNotif?: () => void;
};

const PAGE_META: Record<string, [string, string, string]> = {
  "/admin/dashboard": ["Dashboard", "fa-tachometer-alt", "Inicio"],
  "/admin/citas": ["Citas", "fa-calendar-check", "Gestión"],
  "/admin/medicos": ["Médicos", "fa-user-md", "Gestión"],
  "/admin/especialidades": ["Especialidades", "fa-stethoscope", "Gestión"],
  "/admin/usuarios": ["Usuarios", "fa-users-cog", "Gestión"],
  "/admin/horarios": ["Horarios", "fa-clock", "Gestión"],
  "/admin/feriados": ["Feriados", "fa-calendar-times", "Gestión"],
  "/admin/auditoria": ["Auditoría", "fa-history", "Sistema"],
  "/admin/reportes": ["Reportes", "fa-chart-bar", "Sistema"],
};

function currentMeta(path: string) {
  for (const k of Object.keys(PAGE_META)) {
    if (path.startsWith(k)) return { key: k, ...Object.fromEntries([["v", PAGE_META[k]]]) } as { key: string; v: [string, string, string] };
  }
  return { key: "/admin/dashboard", v: PAGE_META["/admin/dashboard"] };
}

export function AdminShell({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const { data: session } = useSession();
  const { v: currentPage } = currentMeta(pathname);
  const user = session?.user;
  const initial = (user?.nombre ?? "A").charAt(0).toUpperCase();
  const [now, setNow] = useState<string>("");

  useEffect(() => {
    setNow(new Date().toLocaleString("es-PY", { dateStyle: "long", timeStyle: "short" }));
  }, []);

  return (
    <>
      <div id="sidebar-overlay" onClick={() => (window as Win).toggleSidebar?.()} />
      <aside id="sidebar">
        <div className="flex items-center gap-2.5 px-4 h-14 border-b border-slate-700/50 flex-shrink-0">
          <div
            className="w-8 h-8 rounded-xl flex items-center justify-center text-white flex-shrink-0"
            style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
          >
            <i className="fas fa-hospital-alt text-xs"></i>
          </div>
          <div>
            <p className="text-white font-extrabold text-sm leading-tight">{env.appName}</p>
            <p className="text-sky-400 text-[10px] font-semibold">Panel de Administración</p>
          </div>
        </div>

        <nav className="flex-1 overflow-y-auto overflow-x-hidden py-3">
          <p className="nav-group">Principal</p>
          <SidebarLink href="/admin/dashboard" icon="fa-tachometer-alt" label="Dashboard" active={pathname === "/admin/dashboard"} />
          <SidebarLink href="/admin/citas" icon="fa-calendar-check" label="Citas" active={pathname.startsWith("/admin/citas")} />

          <p className="nav-group mt-3">Configuración</p>
          <SidebarLink href="/admin/medicos" icon="fa-user-md" label="Médicos" active={pathname.startsWith("/admin/medicos")} />
          <SidebarLink href="/admin/especialidades" icon="fa-stethoscope" label="Especialidades" active={pathname.startsWith("/admin/especialidades")} />
          <SidebarLink href="/admin/usuarios" icon="fa-users-cog" label="Usuarios" active={pathname.startsWith("/admin/usuarios")} />
          <SidebarLink href="/admin/horarios" icon="fa-clock" label="Horarios" active={pathname.startsWith("/admin/horarios")} />

          <p className="nav-group mt-3">Sistema</p>
          <SidebarLink href="/admin/feriados" icon="fa-calendar-times" label="Feriados" active={pathname.startsWith("/admin/feriados")} />
          <SidebarLink href="/admin/auditoria" icon="fa-history" label="Auditoría" active={pathname.startsWith("/admin/auditoria")} />
          <SidebarLink href="/admin/reportes" icon="fa-chart-bar" label="Reportes" active={pathname.startsWith("/admin/reportes")} />
        </nav>

        <div className="px-3 py-3 border-t border-slate-700/50">
          <div className="flex items-center gap-3 px-3 py-2 mb-1">
            <div
              className="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
              style={{ background: "linear-gradient(135deg,#7c3aed,#a78bfa)" }}
            >
              {initial}
            </div>
            <div className="min-w-0 flex-1">
              <p className="text-white text-xs font-bold truncate">{user?.nombre ?? "Administrador"}</p>
              <p className="text-slate-500 text-[10px]">{user?.rol ?? "admin"}</p>
            </div>
          </div>
          <a href="/" target="_blank" rel="noreferrer" className="nav-item mb-0.5">
            <i className="fas fa-external-link-alt"></i><span>Ver Sitio</span>
          </a>
          <button
            onClick={() => signOut({ callbackUrl: "/admin/login" })}
            className="nav-item w-full text-left"
          >
            <i className="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span>
          </button>
          <p className="text-center text-[10px] text-slate-600 mt-2">{env.appVersion}</p>
        </div>
      </aside>

      <div className="main-content sidebar-transition min-h-screen flex flex-col" style={{ marginLeft: 250 }}>
        <header className="topbar gap-2">
          <button
            type="button"
            onClick={() => (window as Win).toggleSidebar?.()}
            className="md:hidden w-9 h-9 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 transition flex-shrink-0"
          >
            <i className="fas fa-bars text-base"></i>
          </button>

          <div className="flex items-center gap-2 min-w-0">
            <Link href="/admin/dashboard" className="text-slate-400 hover:text-sky-500 transition text-sm">
              <i className="fas fa-home"></i>
            </Link>
            <span className="text-slate-300 text-xs">/</span>
            <span className="text-slate-700 font-bold text-sm truncate">{currentPage[0]}</span>
            <span className="text-slate-400 text-xs hidden sm:inline">· {currentPage[2]}</span>
          </div>

          <div className="flex-1"></div>

          <div className="search-wrap hidden md:block">
            <i className="fas fa-search"></i>
            <input
              type="text"
              placeholder="Buscar..."
              onKeyDown={(e) => {
                if (e.key === "Enter" && (e.target as HTMLInputElement).value.trim()) {
                  const q = (e.target as HTMLInputElement).value.trim();
                  window.location.href = `/admin/citas?q=${encodeURIComponent(q)}`;
                }
              }}
            />
          </div>

          <div className="flex items-center gap-2">
            <div className="relative">
              <button
                type="button"
                className="btn btn-icon btn-ghost relative"
                onClick={() => (window as Win).toggleNotif?.()}
              >
                <i className="far fa-bell"></i>
                <span id="notifBadge" className="notif-dot hidden">0</span>
              </button>
              <div
                id="notifDropdown"
                className="dropdown-menu"
                style={{ width: 320, right: 0, maxHeight: 400, overflowY: "auto" }}
                onClick={(e) => e.stopPropagation()}
              >
                <div className="px-3 py-2 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                  <p className="text-xs font-bold text-slate-700">Notificaciones</p>
                  <button
                    type="button"
                    className="text-[10px] text-sky-600 hover:text-sky-800 font-semibold"
                    onClick={() => fetch("/api/notificaciones/marcar-leidas", { method: "POST" }).then(() => cargarNotifs())}
                  >
                    <i className="fas fa-check-double"></i> Marcar leídas
                  </button>
                </div>
                <div id="notifList" className="py-1"></div>
              </div>
            </div>

            <div className="flex items-center gap-2 px-2 py-1.5">
              <div className="avatar avatar-sm" style={{ background: "linear-gradient(135deg,#7c3aed,#a78bfa)" }}>
                {initial}
              </div>
              <span className="text-sm font-semibold text-slate-700 hidden sm:block">
                {user?.nombre ?? "Admin"}
              </span>
            </div>
          </div>
        </header>

        <div id="toastContainer"></div>

        <main className="flex-1 p-5 lg:p-7" style={{ minHeight: "calc(100vh - 56px)" }}>
          {children}
        </main>

        <footer className="px-7 py-3 border-t border-slate-100 text-[10px] text-slate-400 flex justify-between">
          <span>{now}</span>
          <span>{env.appName} · v{env.appVersion}</span>
        </footer>
      </div>

      {/* Modal genérico de eliminación */}
      <div id="deleteModal" className="modal-overlay" onClick={(e) => { if (e.target === e.currentTarget) (window as Win).closeDeleteModal?.(); }}>
        <div className="modal-box" style={{ maxWidth: 400 }}>
          <div className="flex items-start gap-4">
            <div className="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
              <i className="fas fa-trash-alt text-red-500"></i>
            </div>
            <div className="flex-1">
              <h3 className="text-base font-extrabold text-slate-800 mb-1">Confirmar eliminación</h3>
              <p id="deleteModalText" className="text-sm text-slate-500">Esta acción no se puede deshacer.</p>
            </div>
          </div>
          <div className="flex justify-end gap-3 mt-6">
            <button type="button" className="btn btn-secondary" onClick={() => (window as Win).closeDeleteModal?.()}>Cancelar</button>
            <form id="deleteModalForm" method="POST" action="">
              <button type="submit" className="btn btn-danger"><i className="fas fa-trash"></i> Eliminar</button>
            </form>
          </div>
        </div>
      </div>

      <NotifPoller />
    </>
  );
}

function SidebarLink({ href, icon, label, active }: { href: string; icon: string; label: string; active: boolean }) {
  return (
    <Link href={href} className={`nav-item ${active ? "active" : ""}`}>
      <i className={`fas ${icon}`}></i>
      <span>{label}</span>
    </Link>
  );
}

function NotifPoller() {
  useEffect(() => {
    cargarNotifs();
    const t = setInterval(cargarNotifs, 30000);
    return () => clearInterval(t);
  }, []);
  return null;
}

async function cargarNotifs() {
  try {
    const r = await fetch("/api/notificaciones");
    if (!r.ok) return;
    const res = await r.json();
    if (!res.success) return;
    const badge = document.getElementById("notifBadge");
    const list = document.getElementById("notifList");
    if (badge) {
      badge.textContent = res.count;
      badge.classList.toggle("hidden", res.count === 0);
    }
    if (!list) return;
    if (!res.data || res.data.length === 0) {
      list.innerHTML = '<div class="py-6 text-center text-xs text-slate-400"><i class="far fa-bell-slash mr-1"></i> Sin notificaciones</div>';
      return;
    }
    const tipoIcon: Record<string, string> = {
      info: "fa-info-circle text-sky-500",
      warning: "fa-exclamation-triangle text-amber-500",
      success: "fa-check-circle text-emerald-500",
      danger: "fa-times-circle text-red-500",
    };
    const html = res.data
      .map(
        (n: { id: number; titulo: string; mensaje?: string; created_at: string; tipo: string }) => {
          const icon = tipoIcon[n.tipo] || "fa-info-circle text-slate-400";
          return `<div class="px-3 py-2.5 border-b border-slate-50 hover:bg-slate-50 transition cursor-pointer" onclick="fetch('/api/notificaciones/marcar',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id=${n.id}'}).then(()=>window.__reloadNotifs?.())">
            <div class="flex items-start gap-2.5">
              <i class="fas ${icon} mt-0.5"></i>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-slate-700">${esc(n.titulo)}</p>
                ${n.mensaje ? `<p class="text-[11px] text-slate-400 truncate">${esc(n.mensaje)}</p>` : ""}
                <p class="text-[10px] text-slate-300 mt-0.5">${timeAgo(n.created_at)}</p>
              </div>
            </div>
          </div>`;
        }
      )
      .join("");
    list.innerHTML = html;
  } catch {}
}

function esc(s: string) {
  if (!s) return "";
  const d = document.createElement("div");
  d.textContent = s;
  return d.innerHTML;
}

function timeAgo(dt?: string) {
  if (!dt) return "";
  const d = new Date(String(dt).replace(" ", "T") + "Z");
  const now = new Date();
  const sec = Math.floor((now.getTime() - d.getTime()) / 1000);
  if (sec < 60) return "ahora";
  const min = Math.floor(sec / 60);
  if (min < 60) return `hace ${min}m`;
  const hr = Math.floor(min / 60);
  if (hr < 24) return `hace ${hr}h`;
  const day = Math.floor(hr / 24);
  return `hace ${day}d`;
}
