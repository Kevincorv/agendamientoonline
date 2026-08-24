"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { signOut, useSession } from "next-auth/react";
import { env } from "@/lib/env";
import { useEffect } from "react";

type Win = typeof window & {
  showToast?: (m: string, t?: "success" | "error" | "info") => void;
  openDeleteModal?: (action: string, text?: string) => void;
  closeDeleteModal?: () => void;
  toggleSidebar?: () => void;
};

const PAGE_META: Record<string, [string, string, string]> = {
  "/medico/dashboard": ["Dashboard", "fa-tachometer-alt", "Inicio"],
  "/medico/agenda": ["Agenda", "fa-calendar-check", "Gestión"],
  "/medico/perfil": ["Mi Perfil", "fa-user-md", "Cuenta"],
};

export function MedicoShell({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const { data: session } = useSession();
  const user = session?.user;
  const initial = (user?.nombre ?? "M").charAt(0).toUpperCase();

  return (
    <>
      <div id="sidebar-overlay" onClick={() => (window as Win).toggleSidebar?.()} />
      <aside id="sidebar">
        <div className="flex items-center gap-2.5 px-4 h-14 border-b border-slate-700/50 flex-shrink-0">
          <div
            className="w-8 h-8 rounded-xl flex items-center justify-center text-white flex-shrink-0"
            style={{ background: "linear-gradient(135deg,#7c3aed,#a78bfa)" }}
          >
            <i className="fas fa-user-md text-xs"></i>
          </div>
          <div>
            <p className="text-white font-extrabold text-sm leading-tight">{env.appName}</p>
            <p className="text-sky-400 text-[10px] font-semibold">Portal Médico</p>
          </div>
        </div>

        <nav className="flex-1 overflow-y-auto overflow-x-hidden py-3">
          <p className="nav-group">Principal</p>
          <SideLink href="/medico/dashboard" icon="fa-tachometer-alt" label="Dashboard" active={pathname === "/medico/dashboard"} />
          <SideLink href="/medico/agenda" icon="fa-calendar-check" label="Agenda" active={pathname.startsWith("/medico/agenda")} />

          <p className="nav-group mt-3">Cuenta</p>
          <SideLink href="/medico/perfil" icon="fa-user-md" label="Mi Perfil" active={pathname.startsWith("/medico/perfil")} />
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
              <p className="text-white text-xs font-bold truncate">Dr. {user?.nombre} {user?.apellido}</p>
              <p className="text-slate-500 text-[10px]">Médico</p>
            </div>
          </div>
          <a href="/" target="_blank" rel="noreferrer" className="nav-item mb-0.5">
            <i className="fas fa-external-link-alt"></i><span>Ver Sitio</span>
          </a>
          <button onClick={() => signOut({ callbackUrl: "/admin/login" })} className="nav-item w-full text-left">
            <i className="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span>
          </button>
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
            <Link href="/medico/dashboard" className="text-slate-400 hover:text-sky-500 transition text-sm">
              <i className="fas fa-home"></i>
            </Link>
            <span className="text-slate-300 text-xs">/</span>
            <span className="text-slate-700 font-bold text-sm truncate">
              {PAGE_META[pathname]?.[0] ?? "Portal"}
            </span>
          </div>

          <div className="flex-1"></div>

          <div className="flex items-center gap-2">
            <div className="flex items-center gap-2 px-2 py-1.5">
              <div className="avatar avatar-sm" style={{ background: "linear-gradient(135deg,#7c3aed,#a78bfa)" }}>
                {initial}
              </div>
              <span className="text-sm font-semibold text-slate-700 hidden sm:block">
                Dr. {user?.nombre}
              </span>
            </div>
          </div>
        </header>

        <div id="toastContainer"></div>

        <main className="flex-1 p-5 lg:p-7" style={{ minHeight: "calc(100vh - 56px)" }}>
          {children}
        </main>
      </div>

      <DeleteModal />
    </>
  );
}

function SideLink({ href, icon, label, active }: { href: string; icon: string; label: string; active: boolean }) {
  return (
    <Link href={href} className={`nav-item ${active ? "active" : ""}`}>
      <i className={`fas ${icon}`}></i>
      <span>{label}</span>
    </Link>
  );
}

function DeleteModal() {
  useEffect(() => {
    const w = window as Win;
    w.openDeleteModal = (action, text = "Esta acción no se puede deshacer.") => {
      const modal = document.getElementById("deleteModal");
      const form = document.getElementById("deleteModalForm") as HTMLFormElement | null;
      const label = document.getElementById("deleteModalText");
      if (!modal || !form || !label) return;
      form.dataset.endpoint = action;
      form.action = "javascript:void(0)";
      label.textContent = text;
      modal.classList.add("active");
      document.body.style.overflow = "hidden";
    };
    w.closeDeleteModal = () => {
      document.getElementById("deleteModal")?.classList.remove("active");
      document.body.style.overflow = "";
    };
    const df = document.getElementById("deleteModalForm") as HTMLFormElement | null;
    if (df) {
      df.addEventListener("submit", async (e) => {
        e.preventDefault();
        const endpoint = df.dataset.endpoint;
        if (!endpoint) return;
        const submitBtn = df.querySelector('button[type="submit"]') as HTMLButtonElement | null;
        if (submitBtn) submitBtn.disabled = true;
        try {
          const r = await fetch(endpoint, { method: "POST", body: new FormData(df) });
          const res = await r.json().catch(() => ({ success: r.ok, message: r.ok ? "OK" : "Error" }));
          w.showToast?.(res.message ?? (res.success ? "Operación exitosa" : "Error"), res.success ? "success" : "error");
          if (res.success) {
            w.closeDeleteModal?.();
            setTimeout(() => window.location.reload(), 500);
          }
        } catch {
          w.showToast?.("Error de red", "error");
        } finally {
          if (submitBtn) submitBtn.disabled = false;
        }
      });
    }
  }, []);
  return (
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
  );
}
