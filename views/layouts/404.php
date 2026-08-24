<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/functions.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 - Página no encontrada | <?= APP_NAME ?></title>
<meta name="theme-color" content="#0284c7">
<link rel="manifest" href="<?= APP_URL ?>/public/manifest.json">
<link rel="icon" type="image/svg+xml" href="<?= APP_URL ?>/public/assets/icons/icon-192.svg">
<link rel="apple-touch-icon" href="<?= APP_URL ?>/public/assets/icons/icon-192.svg">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-8xl font-bold text-blue-200">404</h1>
        <h2 class="text-2xl font-bold text-gray-700 mt-4">Página no encontrada</h2>
        <p class="text-gray-400 mt-2">La ruta que buscas no existe.</p>
        <a href="<?= APP_URL ?>/"
           class="mt-6 inline-block bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition">
            Volver al inicio
        </a>
    </div>
</body>
</html>
