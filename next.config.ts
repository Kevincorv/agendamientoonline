import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  reactStrictMode: true,
  serverExternalPackages: ["mysql2", "bcryptjs", "nodemailer"],
};

export default nextConfig;
