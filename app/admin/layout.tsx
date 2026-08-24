import "@/styles/app.css";
import "@/styles/admin.css";
import { AdminShell } from "@/components/admin-shell";

export default function AdminLayout({ children }: { children: React.ReactNode }) {
  return <AdminShell>{children}</AdminShell>;
}
