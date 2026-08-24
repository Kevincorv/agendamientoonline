"use client";

import { useEffect, useRef } from "react";

interface Props {
  type: "bar" | "doughnut";
  labels: string[];
  data: number[];
}

// Wrapper sobre Chart.js: carga el bundle on-demand y dibuja.
export function DashboardCharts({ type, labels, data }: Props) {
  const ref = useRef<HTMLCanvasElement>(null);
  const chartRef = useRef<unknown>(null);

  useEffect(() => {
    let cancelled = false;
    (async () => {
      const mod = await import("chart.js/auto");
      if (cancelled || !ref.current) return;
      const Chart = mod.default;
      if (chartRef.current) (chartRef.current as { destroy: () => void }).destroy();

      if (type === "bar") {
        chartRef.current = new Chart(ref.current, {
          type: "bar",
          data: {
            labels,
            datasets: [
              {
                label: "Citas",
                data,
                backgroundColor: "rgba(2,132,199,.55)",
                borderColor: "#0284c7",
                borderWidth: 1,
                borderRadius: 3,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
              y: { beginAtZero: true, grid: { color: "rgba(0,0,0,.04)" }, ticks: { font: { size: 9 } } },
              x: { grid: { display: false }, ticks: { font: { size: 9 } } },
            },
          },
        });
      } else {
        chartRef.current = new Chart(ref.current, {
          type: "doughnut",
          data: {
            labels,
            datasets: [
              {
                data,
                backgroundColor: ["#f59e0b", "#10b981", "#ef4444", "#3b82f6"],
                borderWidth: 0,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "62%",
            plugins: {
              legend: { position: "bottom", labels: { padding: 8, usePointStyle: true, font: { size: 10 } } },
            },
          },
        });
      }
    })();
    return () => {
      cancelled = true;
      if (chartRef.current) (chartRef.current as { destroy: () => void }).destroy();
    };
  }, [type, labels, data]);

  return <canvas ref={ref} />;
}
