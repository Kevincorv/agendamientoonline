import { PacienteShell } from "@/components/paciente-shell";
import "../../styles/app.css";

export const dynamic = "force-dynamic";

export default function PacienteLayout({ children }: { children: React.ReactNode }) {
  return <PacienteShell>{children}</PacienteShell>;
}
