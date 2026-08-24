"use client";

import { useEffect } from "react";

// Replica el comportamiento global de public/assets/app.js (PHP):
// toast, mobile drawer, delete modal, sidebar toggle, back-to-top, ripple, ESC.
// Las funciones se exponen en window para ser llamadas desde onClick inline.

type AnyWin = typeof window & {
  showToast?: (m: string, t?: "success" | "error" | "info") => void;
  openDrawer?: () => void;
  closeDrawer?: () => void;
  openDeleteModal?: (action: string, text?: string) => void;
  closeDeleteModal?: () => void;
  toggleSidebar?: () => void;
  toggleNotif?: () => void;
  toggleDropdown?: (btn: HTMLElement) => void;
  performSearch?: (q: string) => void;
};

export function AppScripts() {
  useEffect(() => {
    const w = window as AnyWin;

    // ─── Toast ───
    w.showToast = (message, type = "success") => {
      const container = document.getElementById("toastContainer");
      if (!container) return;
      const toast = document.createElement("div");
      toast.className = `toast toast-${type}`;
      const icon =
        type === "success" ? "fa-check-circle" : type === "error" ? "fa-exclamation-circle" : "fa-info-circle";
      toast.innerHTML = `<i class="fas ${icon}"></i>${message}`;
      container.appendChild(toast);
      setTimeout(() => {
        toast.classList.add("removing");
        setTimeout(() => toast.remove(), 260);
      }, 4000);
    };

    // ─── Mobile drawer ───
    w.openDrawer = () => {
      document.getElementById("mobileDrawer")?.classList.add("open");
      document.getElementById("drawerOverlay")?.classList.add("active");
      document.body.style.overflow = "hidden";
    };
    w.closeDrawer = () => {
      document.getElementById("mobileDrawer")?.classList.remove("open");
      document.getElementById("drawerOverlay")?.classList.remove("active");
      document.body.style.overflow = "";
    };

    // ─── Delete modal ───
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

    // ─── Delete modal submit (intercept) ───
    const deleteForm = document.getElementById("deleteModalForm") as HTMLFormElement | null;
    if (deleteForm) {
      deleteForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const endpoint = deleteForm.dataset.endpoint;
        if (!endpoint) return;
        const submitBtn = deleteForm.querySelector('button[type="submit"]') as HTMLButtonElement | null;
        if (submitBtn) submitBtn.disabled = true;
        try {
          const r = await fetch(endpoint, { method: "POST", body: new FormData(deleteForm) });
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

    // ─── Sidebar toggle (admin) ───
    w.toggleSidebar = () => {
      const sidebar = document.getElementById("sidebar");
      const overlay = document.getElementById("sidebar-overlay");
      if (!sidebar || !overlay) return;
      sidebar.classList.toggle("visible");
      overlay.classList.toggle("active");
      document.body.style.overflow = sidebar.classList.contains("visible") ? "hidden" : "";
    };

    w.toggleNotif = () => {
      document.getElementById("notifDropdown")?.classList.toggle("open");
    };
    w.toggleDropdown = (btn: HTMLElement) => {
      const menu = btn.closest(".dropdown")?.querySelector(".dropdown-menu");
      menu?.classList.toggle("open");
    };

    // ─── Back to top FAB ───
    const fab = document.createElement("button");
    fab.className = "fab no-print";
    fab.innerHTML = '<i class="fas fa-arrow-up"></i>';
    fab.setAttribute("aria-label", "Volver arriba");
    fab.onclick = () => window.scrollTo({ top: 0, behavior: "smooth" });
    document.body.appendChild(fab);
    const onScroll = () => {
      fab.style.display = window.scrollY > 400 ? "flex" : "none";
    };
    window.addEventListener("scroll", onScroll);
    fab.style.display = "none";

    // ─── Ripple on buttons ───
    const onRippleClick = (e: MouseEvent) => {
      const btn = (e.target as HTMLElement).closest<HTMLElement>(
        ".btn-primary, .btn, .quick-action"
      );
      if (!btn) return;
      const rect = btn.getBoundingClientRect();
      const ripple = document.createElement("span");
      ripple.className = "ripple-effect";
      const size = Math.max(rect.width, rect.height);
      ripple.style.width = ripple.style.height = `${size}px`;
      ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
      ripple.style.top = `${e.clientY - rect.top - size / 2}px`;
      btn.style.position = "relative";
      btn.style.overflow = "hidden";
      btn.appendChild(ripple);
      setTimeout(() => ripple.remove(), 600);
    };
    document.addEventListener("click", onRippleClick);

    // ─── Click outside: cerrar dropdowns ───
    const onDocClick = (e: MouseEvent) => {
      document.querySelectorAll<HTMLElement>(".dropdown-menu.open").forEach((m) => {
        if (!e.target || !(e.target as HTMLElement).closest(".dropdown")) {
          m.classList.remove("open");
        }
      });
      const notif = document.getElementById("notifDropdown");
      if (notif?.classList.contains("open") && !(e.target as HTMLElement).closest(".relative")) {
        notif.classList.remove("open");
      }
      const modal = document.getElementById("deleteModal");
      if (modal && e.target === modal) w.closeDeleteModal?.();
      document.querySelectorAll<HTMLElement>(".modal-overlay.active").forEach((m) => {
        if (e.target === m) m.classList.remove("active");
      });
    };
    document.addEventListener("click", onDocClick);

    // ─── ESC: cerrar modales/dropdowns ───
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") {
        w.closeDeleteModal?.();
        document.querySelectorAll<HTMLElement>(".modal-overlay.active").forEach((m) =>
          m.classList.remove("active")
        );
        document.querySelectorAll<HTMLElement>(".dropdown-menu.open").forEach((m) =>
          m.classList.remove("open")
        );
      }
    };
    document.addEventListener("keydown", onKey);

    // ─── Sidebar link close (mobile) ───
    document.querySelectorAll<HTMLElement>("#sidebar a[href]").forEach((link) => {
      link.addEventListener("click", () => {
        if (window.innerWidth < 768) {
          document.getElementById("sidebar")?.classList.remove("visible");
          document.getElementById("sidebar-overlay")?.classList.remove("active");
          document.body.style.overflow = "";
        }
      });
    });

    // ─── Service worker (PWA) — solo producción ───
    if (process.env.NODE_ENV === "production" && "serviceWorker" in navigator) {
      navigator.serviceWorker.register("/sw.js").catch(() => {});
    }

    return () => {
      window.removeEventListener("scroll", onScroll);
      document.removeEventListener("click", onRippleClick);
      document.removeEventListener("click", onDocClick);
      document.removeEventListener("keydown", onKey);
      fab.remove();
    };
  }, []);

  return null;
}
