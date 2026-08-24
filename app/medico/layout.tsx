import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { MedicoShell } from "@/components/medico-shell";
import { MedicoHeader } from "@/components/medico-header";

export const dynamic = "force-dynamic";

export default function MedicoLayout({ children }: { children: React.ReactNode }) {
  return <MedicoShell>{children}</MedicoShell>;
}

// Re-export so child pages can import if needed
export { MedicoHeader };
