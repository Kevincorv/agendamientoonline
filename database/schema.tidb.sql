-- ============================================================
-- CITAS MÉDICAS ONLINE — Esquema + datos seed adaptado a TiDB
-- Generado por scripts/prepare-tidb.mjs desde el dump phpMyAdmin.
-- Collations: utf8mb4_bin (compatible TiDB Serverless).
-- Importar con: mysql --ssl-mode=REQUIRED ... < schema.tidb.sql
-- ============================================================

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 21-08-2026 a las 18:25:40
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `clinica_san_luis`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

DROP TABLE IF EXISTS `auditoria`;
CREATE TABLE IF NOT EXISTS `auditoria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `usuario_nombre` varchar(150) DEFAULT NULL,
  `accion` varchar(50) NOT NULL,
  `tabla` varchar(50) DEFAULT NULL,
  `registro_id` int DEFAULT NULL,
  `descripcion` text,
  `datos_antes` json DEFAULT NULL,
  `datos_despues` json DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_auditoria_usuario` (`usuario_id`),
  KEY `idx_auditoria_tabla` (`tabla`,`registro_id`),
  KEY `idx_auditoria_fecha` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id`, `usuario_id`, `usuario_nombre`, `accion`, `tabla`, `registro_id`, `descripcion`, `datos_antes`, `datos_despues`, `ip`, `user_agent`, `created_at`) VALUES
(1, 3, 'Recepción Recepción', 'acceso_denegado', NULL, NULL, 'Sin rol suficiente para: /clinica-san-luis/admin/usuarios', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-07 22:53:51'),
(2, 3, 'Recepción Recepción', 'acceso_denegado', NULL, NULL, 'Sin rol suficiente para: /clinica-san-luis/admin/usuarios', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-07 22:55:47'),
(3, 3, 'Recepción Recepción', 'acceso_denegado', NULL, NULL, 'Sin rol suficiente para: /clinica-san-luis/admin/usuarios', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-07 22:55:47'),
(4, 3, 'Recepción Recepción', 'logout', 'usuarios', 3, 'Logout: recepcion@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-07 22:55:50'),
(5, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-07 22:55:54'),
(6, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-24 21:00:29'),
(7, 1, 'Admin Sistema', 'logout', 'usuarios', 1, 'Logout: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-24 22:20:26'),
(8, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 12:39:58'),
(9, 1, 'Admin Sistema', 'logout', 'usuarios', 1, 'Logout: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 13:51:36'),
(10, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 13:51:44'),
(11, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 9, 'Cita #9 → estado 4 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 14:11:09'),
(12, 1, 'Admin Sistema', 'cancelar', 'citas', 11, 'Cita #11 cancelada por token por Andrea Gonzales', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 14:12:09'),
(13, 1, 'Admin Sistema', 'registro', 'usuarios', NULL, 'Paciente registrado: babeha4785@brixozu.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 14:15:38'),
(14, 1, 'Admin Sistema', 'login', 'usuarios', 4, 'Login paciente: babeha4785@brixozu.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 14:15:47'),
(15, 1, 'Admin Sistema', 'logout', 'usuarios', 4, 'Logout paciente: babeha4785@brixozu.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 14:36:36'),
(16, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 14:37:07'),
(17, 1, 'Admin Sistema', 'login', 'usuarios', 4, 'Login paciente: babeha4785@brixozu.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 19:00:01'),
(18, 1, 'Admin Sistema', 'logout', 'usuarios', 4, 'Logout paciente: babeha4785@brixozu.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 19:00:44'),
(19, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 19:01:06'),
(20, 1, 'Admin Sistema', 'logout', 'usuarios', 1, 'Logout: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 19:01:48'),
(21, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 22:25:40'),
(22, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 10, 'Cita #10 → estado 2 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 22:25:52'),
(23, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 10, 'Cita #10 → estado 1 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 22:25:53'),
(24, 1, 'Admin Sistema', 'login', 'usuarios', 4, 'Login paciente: babeha4785@brixozu.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 22:27:10'),
(25, 1, 'Admin Sistema', 'cancelar', 'citas', 13, 'Cita #13 cancelada por token por kevin rolon', NULL, NULL, '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', '2026-06-28 22:29:35'),
(26, 1, 'Admin Sistema', 'logout', 'usuarios', 4, 'Logout paciente: babeha4785@brixozu.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 22:31:38'),
(27, NULL, NULL, 'logout', 'usuarios', NULL, 'Logout: ', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 22:31:41'),
(28, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 22:32:13'),
(29, 1, 'Admin Sistema', 'logout', 'usuarios', 1, 'Logout: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 22:33:09'),
(30, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:38:33'),
(31, 1, 'Admin Sistema', 'crear_backup', 'backups', NULL, 'Backup creado: backup_2026-06-29_14-47-07.sql', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:47:07'),
(32, 1, 'Admin Sistema', 'eliminar_backup', 'backups', NULL, 'Backup eliminado: backup_2026-06-29_14-47-07.sql', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:47:13'),
(33, 1, 'Admin Sistema', 'eliminar', 'citas', 10, 'Cita #10 eliminada', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:50:44'),
(34, 1, 'Admin Sistema', 'eliminar', 'citas', 14, 'Cita #14 eliminada', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:50:46'),
(35, 1, 'Admin Sistema', 'eliminar', 'citas', 12, 'Cita #12 eliminada', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:50:48'),
(36, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 15, 'Cita #15 → estado 2 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:58:41'),
(37, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 15, 'Cita #15 → estado 3 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:58:44'),
(38, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 15, 'Cita #15 → estado 4 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:58:46'),
(39, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 15, 'Cita #15 → estado 1 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:01:54'),
(40, 1, 'Admin Sistema', 'registro', 'usuarios', NULL, 'Paciente registrado: prueba@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:07:32'),
(41, 1, 'Admin Sistema', 'login', 'usuarios', 5, 'Login paciente: prueba@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:07:38'),
(42, 1, 'Admin Sistema', 'crear', 'especialidades', 6, 'Especialidad creada: Traumatología', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:09:16'),
(43, 1, 'Admin Sistema', 'editar', 'especialidades', 6, 'Especialidad editada: Traumatología', '{\"nombre\": \"Traumatología\"}', '{\"nombre\": \"Traumatología\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:09:27'),
(44, 1, 'Admin Sistema', 'eliminar', 'especialidades', 0, 'Especialidad desactivada: #0', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:09:29'),
(45, 1, 'Admin Sistema', 'eliminar', 'especialidades', 0, 'Especialidad desactivada: #0', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:09:31'),
(46, 1, 'Admin Sistema', 'cancelar', 'citas', 16, 'Cita #16 cancelada por token por david lopez', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:11:34'),
(47, 1, 'Admin Sistema', 'eliminar', 'especialidades', 6, 'Especialidad desactivada: Traumatología', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:20:23'),
(48, 1, 'Admin Sistema', 'crear_medico_auto', 'medicos', 18, 'Médico automático creado para Traumatología', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:20:45'),
(49, 1, 'Admin Sistema', 'crear', 'especialidades', 7, 'Especialidad creada: Traumatología', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:20:45'),
(50, 1, 'Admin Sistema', 'logout', 'usuarios', 5, 'Logout paciente: prueba@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:22:05'),
(51, NULL, NULL, 'login', 'usuarios', 5, 'Login paciente: prueba@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:22:23'),
(52, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:24:23'),
(53, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 16, 'Cita #16 → estado 2 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:24:34'),
(54, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 15, 'Cita #15 → estado 2 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:24:36'),
(55, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 13, 'Cita #13 → estado 2 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:24:37'),
(56, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 11, 'Cita #11 → estado 2 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:24:38'),
(57, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 16, 'Cita #16 → estado 3 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:24:50'),
(58, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 15, 'Cita #15 → estado 3 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:24:51'),
(59, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 13, 'Cita #13 → estado 3 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:24:53'),
(60, 1, 'Admin Sistema', 'cambiar_estado', 'citas', 11, 'Cita #11 → estado 3 (AJAX)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:24:55'),
(61, 1, 'Admin Sistema', 'logout', 'usuarios', 5, 'Logout paciente: prueba@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:29:11'),
(62, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:30:45'),
(63, 1, 'Admin Sistema', 'login', 'usuarios', 5, 'Login paciente: prueba@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:31:38'),
(64, 1, 'Admin Sistema', 'logout', 'usuarios', 1, 'Logout: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:33:56'),
(65, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:34:06'),
(66, 1, 'Admin Sistema', 'logout', 'usuarios', 1, 'Logout: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:34:08'),
(67, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:34:11'),
(68, 1, 'Admin Sistema', 'crear_medico_auto', 'medicos', 20, 'Médico automático creado para Neurologo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:37:10'),
(69, 1, 'Admin Sistema', 'crear', 'especialidades', 8, 'Especialidad creada: Neurologo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:37:10'),
(70, 1, 'Admin Sistema', 'eliminar', 'especialidades', 8, 'Especialidad desactivada: Neurologo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:37:23'),
(71, 1, 'Admin Sistema', 'logout', 'usuarios', 1, 'Logout: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:50:20'),
(72, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:50:28'),
(73, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:57:46'),
(74, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 16:12:57'),
(75, 1, 'Admin Sistema', 'logout', 'usuarios', 1, 'Logout: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 17:29:39'),
(76, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 17:29:50'),
(77, 1, 'Admin Sistema', 'crear_backup', 'backups', NULL, 'Backup creado: backup_2026-06-29_17-29-57.sql', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 17:29:57'),
(78, 1, 'Admin Sistema', 'logout', 'usuarios', 1, 'Logout: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 18:14:29'),
(79, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 18:30:16'),
(80, 1, 'Admin Sistema', 'crear_backup', 'backups', NULL, 'Backup creado: backup_2026-06-29_18-31-43.sql', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 18:31:43'),
(81, 1, 'Admin Sistema', 'login', 'usuarios', 5, 'Login paciente: prueba@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 18:33:23'),
(82, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 23:31:46'),
(83, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 18:20:37'),
(84, 1, 'Admin Sistema', 'login', 'usuarios', 1, 'Login exitoso: admin@clinicasanluis.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 14:16:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bloqueos_medico`
--

DROP TABLE IF EXISTS `bloqueos_medico`;
CREATE TABLE IF NOT EXISTS `bloqueos_medico` (
  `id` int NOT NULL AUTO_INCREMENT,
  `medico_id` int NOT NULL,
  `fecha` date NOT NULL,
  `motivo` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `medico_fecha` (`medico_id`,`fecha`),
  KEY `medico_id` (`medico_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

DROP TABLE IF EXISTS `citas`;
CREATE TABLE IF NOT EXISTS `citas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_paciente` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `telefono` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `motivo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `token_cancelacion` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `notas_medico` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `medico_id` int NOT NULL,
  `especialidad_id` int DEFAULT NULL,
  `estado_id` int NOT NULL DEFAULT '1',
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_cancelacion` (`token_cancelacion`),
  KEY `fk_citas_medicos` (`medico_id`),
  KEY `fk_citas_estados` (`estado_id`),
  KEY `fk_citas_especialidades` (`especialidad_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id`, `nombre_paciente`, `telefono`, `email`, `motivo`, `token_cancelacion`, `notas_medico`, `fecha`, `hora`, `medico_id`, `especialidad_id`, `estado_id`, `creado_en`) VALUES
(1, 'Pedro Ramírez', '0982000001', 'pedro@email.com', 'Consulta general', '58f29ade9f1e6440bd503ff80f4a5e92d81c47f543619d95fe2103ed421b6aff', NULL, '2026-04-29', '08:30:00', 1, NULL, 1, '2026-04-29 16:44:42'),
(2, 'Ana López', '0982000002', 'ana@email.com', 'Control pediátrico', '92d482384f96604b6e4a94c3fc9438eea6334025f389f2904639cc94299eaa0a', NULL, '2026-04-29', '09:00:00', 2, NULL, 1, '2026-04-29 16:44:42'),
(3, 'Luis Martínez', '0982000003', 'luis@email.com', 'Chequeo cardiológico', '298f3648f8e3204554eaa3e10629343486a0be8539f1634174d1ff8da6df7fd7', NULL, '2026-04-29', '10:00:00', 3, NULL, 2, '2026-04-29 16:44:42'),
(4, 'sasa', '4643546546', '', 'dolor de cabeza constante', 'fffe9369a90be0c539434f064c5ddbe9d90969481214815350c7b6474c72e393', '', '2026-05-29', '08:30:00', 1, 1, 4, '2026-04-29 17:29:42'),
(5, 'kevin rolon', '53541351', '', 'dolor de cabeza y mareos', 'f1f4dd8733353c6320ed41c4036ef312a62ece2a157e68a3b4d567685015db98', '', '2026-04-30', '10:00:00', 1, 1, 2, '2026-04-29 18:23:21'),
(6, 'sasa', '53541351', '', 'Constante dolores de cabeza', '654c388df8a4e39d5f3f3d18b6633fbfc3e42a9b89854fc7f963356c0ebea3e6', '', '2026-05-22', '09:30:00', 1, 1, 2, '2026-05-07 19:46:04'),
(7, 'Andrea Gonzales', '0995666111', 'paciente@gmail.com', 'dolor de cabeza', '36c5721c037f45047c4b2e694de05aabcdbff7b9c30f49f64b615824d614d962', '', '2026-05-15', '11:30:00', 1, 1, 2, '2026-05-08 02:45:30'),
(8, 'sasa', '53541351', 'recepcion@gmail.com', 'dolor de cabeza constante', '9bf515e35862e8b0830d15631ca88f1794a445e2990392c1b5041ceb070e1b1c', '', '2026-06-26', '08:30:00', 1, 1, 2, '2026-06-24 23:49:20'),
(9, 'sasa', '53541351', 'babeha4785@brixozu.com', 'dolor de cabeza constante', '1dce75a1b8528e380c800d4e09f6704f41f4395677a518213ab23083903af29a', '', '2026-06-30', '08:00:00', 1, 1, 4, '2026-06-28 15:39:19'),
(11, 'Andrea Gonzales', '0985550664', '', 'dolor', 'e3ac09fec2796292a4f34da44adb5efb285e939b493f147a5d29d31fa07bdd98', '', '2026-06-29', '08:00:00', 7, 3, 3, '2026-06-28 17:12:04'),
(13, 'kevin rolon', '645654654', '', 'dolor de cabeza', '238abdb9ac44d6e99299a940830065b39b16f92eee56edf9a11c6bf3d60fe074', '', '2026-06-30', '10:00:00', 1, 1, 3, '2026-06-29 01:29:15'),
(15, 'david lopez', '55555555', '', 'dolor constantes y mareos', '8aac7936e90fb033f5e85cafb57557c4ecbd25ee4c16baf5638bd69f0864275d', '', '2026-07-03', '11:30:00', 11, 1, 3, '2026-06-29 18:58:30'),
(16, 'david lopez', '098666669666', '', 'holaaaaaaaaaaaa', '38969f96271b73b56152f7388d3c1bc96db32b387d1298515eee8a30309f8bb3', '', '2026-08-27', '11:30:00', 4, 1, 3, '2026-06-29 19:11:01'),
(17, 'fernando', '46546546', '', 'doloreessssssssss', '7d06a2792472fab98a39f0d99e02a90ea649a6100b3e35cd74994bf5a9e70eb7', NULL, '2026-07-04', '17:00:00', 8, 5, 1, '2026-06-29 19:26:36'),
(18, 'Andrea Gonzales', '0995666111', '', 'sdasda', '0a80525baf825d610a42d6a99bff9dd4a16f9e19838dff606adae9b326c6f56e', NULL, '2026-07-03', '11:00:00', 18, 7, 1, '2026-06-29 19:30:32'),
(19, 'sasa', '53541351', '', 'aSASDSAD', 'be1dbb2005f6558f61c5514878afc991cd6169189895e885b666500725f66961', NULL, '2026-07-03', '07:00:00', 1, 1, 1, '2026-06-29 19:32:12'),
(20, 'david lopez', '4643546546', '', 'aaaaaaaaaaaaaaaaaaa', 'd00dd3efe9ce8edb9d94f7af0ddc2fe7af1e0dce12b472061f60d2b1bdd22953', NULL, '2026-07-02', '07:30:00', 6, 5, 1, '2026-06-29 19:39:01'),
(21, 'sasa', '575754', '', 'fhjfjhj', '512f740567bc609d900e84508693659d9675fa261ac9209a3ce54913571e77ce', NULL, '2026-07-02', '09:30:00', 2, 2, 1, '2026-06-29 21:03:35'),
(22, 'kevin', '5465465', '', 'dolores de cabeza constantes', '63a683bac02d4664dcdc1caf0587ef88c51a47274c3bef624e682153f1c1a925', NULL, '2026-07-02', '07:30:00', 1, 1, 1, '2026-06-29 22:29:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialidades`
--

DROP TABLE IF EXISTS `especialidades`;
CREATE TABLE IF NOT EXISTS `especialidades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `icono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'fa-stethoscope',
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `especialidades`
--

INSERT INTO `especialidades` (`id`, `nombre`, `icono`, `descripcion`, `activo`, `creado_en`) VALUES
(1, 'Clínica General', 'fa-stethoscope', 'Atención médica general para pacientes adultos.', 1, '2026-04-29 16:44:42'),
(2, 'Pediatría', 'fa-child', 'Atención médica integral para niños y adolescentes.', 1, '2026-04-29 16:44:42'),
(3, 'Cardiología', 'fa-stethoscope', 'Diagnóstico y tratamiento de enfermedades del corazón.', 1, '2026-04-29 16:44:42'),
(4, 'Dermatología', 'fa-spa', 'Tratamiento de enfermedades de la piel.', 1, '2026-04-29 16:44:42'),
(5, 'Cirujano', 'fa-stethoscope', 'Especialista en Rodillas', 1, '2026-04-29 17:22:46'),
(6, 'Traumatología', 'fa-stethoscope', 'holaa', 0, '2026-06-29 19:09:16'),
(7, 'Traumatología', 'fa-bone', '', 1, '2026-06-29 19:20:45'),
(8, 'Neurologo', 'fa-stethoscope', '', 0, '2026-06-29 19:37:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_citas`
--

DROP TABLE IF EXISTS `estados_citas`;
CREATE TABLE IF NOT EXISTS `estados_citas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `color` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '#6B7280',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `estados_citas`
--

INSERT INTO `estados_citas` (`id`, `nombre`, `color`) VALUES
(1, 'pendiente', '#F59E0B'),
(2, 'confirmada', '#3B82F6'),
(3, 'cancelada', '#EF4444'),
(4, 'atendida', '#10B981'),
(5, 'no asistio', '#6B7280');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `feriados`
--

DROP TABLE IF EXISTS `feriados`;
CREATE TABLE IF NOT EXISTS `feriados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `motivo` varchar(200) NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fecha` (`fecha`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `feriados`
--

INSERT INTO `feriados` (`id`, `fecha`, `motivo`, `activo`, `created_at`) VALUES
(1, '2026-01-01', 'Ano Nuevo', 1, '2026-06-28 17:26:54'),
(2, '2026-05-01', 'Dia del Trabajador', 1, '2026-06-28 17:26:54'),
(3, '2026-05-15', 'Dia de la Independencia', 1, '2026-06-28 17:26:54'),
(4, '2026-12-25', 'Navidad', 1, '2026-06-28 17:26:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

DROP TABLE IF EXISTS `horarios`;
CREATE TABLE IF NOT EXISTS `horarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `medico_id` int NOT NULL,
  `dia_semana` tinyint NOT NULL COMMENT '0=Domingo, 1=Lunes, ..., 6=Sábado',
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `duracion` int NOT NULL DEFAULT '30' COMMENT 'Duración en minutos por turno',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `intervalo_minutos` int NOT NULL DEFAULT '30',
  PRIMARY KEY (`id`),
  KEY `fk_horarios_medicos` (`medico_id`)
) ENGINE=InnoDB AUTO_INCREMENT=224 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id`, `medico_id`, `dia_semana`, `hora_inicio`, `hora_fin`, `duracion`, `activo`, `intervalo_minutos`) VALUES
(6, 2, 1, '09:00:00', '13:00:00', 30, 1, 30),
(7, 2, 2, '09:00:00', '13:00:00', 30, 1, 30),
(8, 2, 3, '09:00:00', '13:00:00', 30, 1, 30),
(9, 2, 4, '09:00:00', '13:00:00', 30, 1, 30),
(10, 2, 5, '09:00:00', '13:00:00', 30, 1, 30),
(11, 3, 1, '14:00:00', '18:00:00', 30, 1, 30),
(12, 3, 2, '14:00:00', '18:00:00', 30, 1, 30),
(13, 3, 3, '14:00:00', '18:00:00', 30, 1, 30),
(14, 3, 4, '14:00:00', '18:00:00', 30, 1, 30),
(15, 3, 5, '14:00:00', '18:00:00', 30, 1, 30),
(16, 1, 1, '07:00:00', '12:00:00', 30, 1, 30),
(17, 1, 1, '14:00:00', '18:00:00', 30, 1, 30),
(18, 1, 2, '07:00:00', '12:00:00', 30, 1, 30),
(19, 1, 2, '14:00:00', '18:00:00', 30, 1, 30),
(20, 1, 3, '07:00:00', '12:00:00', 30, 1, 30),
(21, 1, 3, '14:00:00', '18:00:00', 30, 1, 30),
(22, 1, 4, '07:00:00', '12:00:00', 30, 1, 30),
(23, 1, 4, '14:00:00', '18:00:00', 30, 1, 30),
(24, 1, 5, '07:00:00', '12:00:00', 30, 1, 30),
(25, 1, 5, '14:00:00', '18:00:00', 30, 1, 30),
(26, 1, 6, '07:00:00', '12:00:00', 30, 1, 30),
(27, 1, 6, '14:00:00', '18:00:00', 30, 1, 30),
(28, 5, 1, '07:00:00', '12:00:00', 30, 1, 30),
(29, 5, 1, '14:00:00', '18:00:00', 30, 1, 30),
(30, 5, 2, '07:00:00', '12:00:00', 30, 1, 30),
(31, 5, 2, '14:00:00', '18:00:00', 30, 1, 30),
(32, 5, 3, '07:00:00', '12:00:00', 30, 1, 30),
(33, 5, 3, '14:00:00', '18:00:00', 30, 1, 30),
(34, 5, 4, '07:00:00', '12:00:00', 30, 1, 30),
(35, 5, 4, '14:00:00', '18:00:00', 30, 1, 30),
(36, 5, 5, '07:00:00', '12:00:00', 30, 1, 30),
(37, 5, 5, '14:00:00', '18:00:00', 30, 1, 30),
(38, 5, 6, '07:00:00', '12:00:00', 30, 1, 30),
(39, 5, 6, '14:00:00', '18:00:00', 30, 1, 30),
(40, 6, 1, '07:00:00', '12:00:00', 30, 1, 30),
(41, 6, 1, '14:00:00', '18:00:00', 30, 1, 30),
(42, 6, 2, '07:00:00', '12:00:00', 30, 1, 30),
(43, 6, 2, '14:00:00', '18:00:00', 30, 1, 30),
(44, 6, 3, '07:00:00', '12:00:00', 30, 1, 30),
(45, 6, 3, '14:00:00', '18:00:00', 30, 1, 30),
(46, 6, 4, '07:00:00', '12:00:00', 30, 1, 30),
(47, 6, 4, '14:00:00', '18:00:00', 30, 1, 30),
(48, 6, 5, '07:00:00', '12:00:00', 30, 1, 30),
(49, 6, 5, '14:00:00', '18:00:00', 30, 1, 30),
(50, 6, 6, '07:00:00', '12:00:00', 30, 1, 30),
(51, 6, 6, '14:00:00', '18:00:00', 30, 1, 30),
(52, 7, 1, '07:00:00', '12:00:00', 30, 1, 30),
(53, 7, 1, '14:00:00', '18:00:00', 30, 1, 30),
(54, 7, 2, '07:00:00', '12:00:00', 30, 1, 30),
(55, 7, 2, '14:00:00', '18:00:00', 30, 1, 30),
(56, 7, 3, '07:00:00', '12:00:00', 30, 1, 30),
(57, 7, 3, '14:00:00', '18:00:00', 30, 1, 30),
(58, 7, 4, '07:00:00', '12:00:00', 30, 1, 30),
(59, 7, 4, '14:00:00', '18:00:00', 30, 1, 30),
(60, 7, 5, '07:00:00', '12:00:00', 30, 1, 30),
(61, 7, 5, '14:00:00', '18:00:00', 30, 1, 30),
(62, 7, 6, '07:00:00', '12:00:00', 30, 1, 30),
(63, 7, 6, '14:00:00', '18:00:00', 30, 1, 30),
(64, 4, 1, '07:00:00', '12:00:00', 30, 1, 30),
(65, 4, 1, '14:00:00', '18:00:00', 30, 1, 30),
(66, 4, 2, '07:00:00', '12:00:00', 30, 1, 30),
(67, 4, 2, '14:00:00', '18:00:00', 30, 1, 30),
(68, 4, 3, '07:00:00', '12:00:00', 30, 1, 30),
(69, 4, 3, '14:00:00', '18:00:00', 30, 1, 30),
(70, 4, 4, '07:00:00', '12:00:00', 30, 1, 30),
(71, 4, 4, '14:00:00', '18:00:00', 30, 1, 30),
(72, 4, 5, '07:00:00', '12:00:00', 30, 1, 30),
(73, 4, 5, '14:00:00', '18:00:00', 30, 1, 30),
(74, 8, 1, '07:00:00', '12:00:00', 30, 1, 30),
(75, 8, 1, '14:00:00', '18:00:00', 30, 1, 30),
(76, 8, 2, '07:00:00', '12:00:00', 30, 1, 30),
(77, 8, 2, '14:00:00', '18:00:00', 30, 1, 30),
(78, 8, 3, '07:00:00', '12:00:00', 30, 1, 30),
(79, 8, 3, '14:00:00', '18:00:00', 30, 1, 30),
(80, 8, 4, '07:00:00', '12:00:00', 30, 1, 30),
(81, 8, 4, '14:00:00', '18:00:00', 30, 1, 30),
(82, 8, 5, '07:00:00', '12:00:00', 30, 1, 30),
(83, 8, 5, '14:00:00', '18:00:00', 30, 1, 30),
(84, 9, 1, '07:00:00', '12:00:00', 30, 1, 30),
(85, 9, 1, '14:00:00', '18:00:00', 30, 1, 30),
(86, 9, 2, '07:00:00', '12:00:00', 30, 1, 30),
(87, 9, 2, '14:00:00', '18:00:00', 30, 1, 30),
(88, 9, 3, '07:00:00', '12:00:00', 30, 1, 30),
(89, 9, 3, '14:00:00', '18:00:00', 30, 1, 30),
(90, 9, 4, '07:00:00', '12:00:00', 30, 1, 30),
(91, 9, 4, '14:00:00', '18:00:00', 30, 1, 30),
(92, 9, 5, '07:00:00', '12:00:00', 30, 1, 30),
(93, 9, 5, '14:00:00', '18:00:00', 30, 1, 30),
(94, 10, 1, '07:00:00', '12:00:00', 30, 1, 30),
(95, 10, 1, '14:00:00', '18:00:00', 30, 1, 30),
(96, 10, 2, '07:00:00', '12:00:00', 30, 1, 30),
(97, 10, 2, '14:00:00', '18:00:00', 30, 1, 30),
(98, 10, 3, '07:00:00', '12:00:00', 30, 1, 30),
(99, 10, 3, '14:00:00', '18:00:00', 30, 1, 30),
(100, 10, 4, '07:00:00', '12:00:00', 30, 1, 30),
(101, 10, 4, '14:00:00', '18:00:00', 30, 1, 30),
(102, 10, 5, '07:00:00', '12:00:00', 30, 1, 30),
(103, 10, 5, '14:00:00', '18:00:00', 30, 1, 30),
(104, 11, 1, '07:00:00', '12:00:00', 30, 1, 30),
(105, 11, 1, '14:00:00', '18:00:00', 30, 1, 30),
(106, 11, 2, '07:00:00', '12:00:00', 30, 1, 30),
(107, 11, 2, '14:00:00', '18:00:00', 30, 1, 30),
(108, 11, 3, '07:00:00', '12:00:00', 30, 1, 30),
(109, 11, 3, '14:00:00', '18:00:00', 30, 1, 30),
(110, 11, 4, '07:00:00', '12:00:00', 30, 1, 30),
(111, 11, 4, '14:00:00', '18:00:00', 30, 1, 30),
(112, 11, 5, '07:00:00', '12:00:00', 30, 1, 30),
(113, 11, 5, '14:00:00', '18:00:00', 30, 1, 30),
(114, 12, 1, '07:00:00', '12:00:00', 30, 1, 30),
(115, 12, 1, '14:00:00', '18:00:00', 30, 1, 30),
(116, 12, 2, '07:00:00', '12:00:00', 30, 1, 30),
(117, 12, 2, '14:00:00', '18:00:00', 30, 1, 30),
(118, 12, 3, '07:00:00', '12:00:00', 30, 1, 30),
(119, 12, 3, '14:00:00', '18:00:00', 30, 1, 30),
(120, 12, 4, '07:00:00', '12:00:00', 30, 1, 30),
(121, 12, 4, '14:00:00', '18:00:00', 30, 1, 30),
(122, 12, 5, '07:00:00', '12:00:00', 30, 1, 30),
(123, 12, 5, '14:00:00', '18:00:00', 30, 1, 30),
(124, 13, 1, '07:00:00', '12:00:00', 30, 1, 30),
(125, 13, 1, '14:00:00', '18:00:00', 30, 1, 30),
(126, 13, 2, '07:00:00', '12:00:00', 30, 1, 30),
(127, 13, 2, '14:00:00', '18:00:00', 30, 1, 30),
(128, 13, 3, '07:00:00', '12:00:00', 30, 1, 30),
(129, 13, 3, '14:00:00', '18:00:00', 30, 1, 30),
(130, 13, 4, '07:00:00', '12:00:00', 30, 1, 30),
(131, 13, 4, '14:00:00', '18:00:00', 30, 1, 30),
(132, 13, 5, '07:00:00', '12:00:00', 30, 1, 30),
(133, 13, 5, '14:00:00', '18:00:00', 30, 1, 30),
(134, 14, 1, '07:00:00', '12:00:00', 30, 1, 30),
(135, 14, 1, '14:00:00', '18:00:00', 30, 1, 30),
(136, 14, 2, '07:00:00', '12:00:00', 30, 1, 30),
(137, 14, 2, '14:00:00', '18:00:00', 30, 1, 30),
(138, 14, 3, '07:00:00', '12:00:00', 30, 1, 30),
(139, 14, 3, '14:00:00', '18:00:00', 30, 1, 30),
(140, 14, 4, '07:00:00', '12:00:00', 30, 1, 30),
(141, 14, 4, '14:00:00', '18:00:00', 30, 1, 30),
(142, 14, 5, '07:00:00', '12:00:00', 30, 1, 30),
(143, 14, 5, '14:00:00', '18:00:00', 30, 1, 30),
(144, 15, 1, '07:00:00', '12:00:00', 30, 1, 30),
(145, 15, 1, '14:00:00', '18:00:00', 30, 1, 30),
(146, 15, 2, '07:00:00', '12:00:00', 30, 1, 30),
(147, 15, 2, '14:00:00', '18:00:00', 30, 1, 30),
(148, 15, 3, '07:00:00', '12:00:00', 30, 1, 30),
(149, 15, 3, '14:00:00', '18:00:00', 30, 1, 30),
(150, 15, 4, '07:00:00', '12:00:00', 30, 1, 30),
(151, 15, 4, '14:00:00', '18:00:00', 30, 1, 30),
(152, 15, 5, '07:00:00', '12:00:00', 30, 1, 30),
(153, 15, 5, '14:00:00', '18:00:00', 30, 1, 30),
(154, 16, 1, '07:00:00', '12:00:00', 30, 1, 30),
(155, 16, 1, '14:00:00', '18:00:00', 30, 1, 30),
(156, 16, 2, '07:00:00', '12:00:00', 30, 1, 30),
(157, 16, 2, '14:00:00', '18:00:00', 30, 1, 30),
(158, 16, 3, '07:00:00', '12:00:00', 30, 1, 30),
(159, 16, 3, '14:00:00', '18:00:00', 30, 1, 30),
(160, 16, 4, '07:00:00', '12:00:00', 30, 1, 30),
(161, 16, 4, '14:00:00', '18:00:00', 30, 1, 30),
(162, 16, 5, '07:00:00', '12:00:00', 30, 1, 30),
(163, 16, 5, '14:00:00', '18:00:00', 30, 1, 30),
(164, 17, 1, '07:00:00', '12:00:00', 30, 1, 30),
(165, 17, 1, '14:00:00', '18:00:00', 30, 1, 30),
(166, 17, 2, '07:00:00', '12:00:00', 30, 1, 30),
(167, 17, 2, '14:00:00', '18:00:00', 30, 1, 30),
(168, 17, 3, '07:00:00', '12:00:00', 30, 1, 30),
(169, 17, 3, '14:00:00', '18:00:00', 30, 1, 30),
(170, 17, 4, '07:00:00', '12:00:00', 30, 1, 30),
(171, 17, 4, '14:00:00', '18:00:00', 30, 1, 30),
(172, 17, 5, '07:00:00', '12:00:00', 30, 1, 30),
(173, 17, 5, '14:00:00', '18:00:00', 30, 1, 30),
(174, 14, 6, '07:00:00', '12:00:00', 30, 1, 30),
(175, 14, 6, '14:00:00', '18:00:00', 30, 1, 30),
(176, 3, 6, '07:00:00', '12:00:00', 30, 1, 30),
(177, 3, 6, '14:00:00', '18:00:00', 30, 1, 30),
(178, 9, 6, '07:00:00', '12:00:00', 30, 1, 30),
(179, 9, 6, '14:00:00', '18:00:00', 30, 1, 30),
(180, 17, 6, '07:00:00', '12:00:00', 30, 1, 30),
(181, 17, 6, '14:00:00', '18:00:00', 30, 1, 30),
(182, 8, 6, '07:00:00', '12:00:00', 30, 1, 30),
(183, 8, 6, '14:00:00', '18:00:00', 30, 1, 30),
(184, 4, 6, '07:00:00', '12:00:00', 30, 1, 30),
(185, 4, 6, '14:00:00', '18:00:00', 30, 1, 30),
(186, 11, 6, '07:00:00', '12:00:00', 30, 1, 30),
(187, 11, 6, '14:00:00', '18:00:00', 30, 1, 30),
(188, 10, 6, '07:00:00', '12:00:00', 30, 1, 30),
(189, 10, 6, '14:00:00', '18:00:00', 30, 1, 30),
(190, 15, 6, '07:00:00', '12:00:00', 30, 1, 30),
(191, 15, 6, '14:00:00', '18:00:00', 30, 1, 30),
(192, 16, 6, '07:00:00', '12:00:00', 30, 1, 30),
(193, 16, 6, '14:00:00', '18:00:00', 30, 1, 30),
(194, 13, 6, '07:00:00', '12:00:00', 30, 1, 30),
(195, 13, 6, '14:00:00', '18:00:00', 30, 1, 30),
(196, 2, 6, '07:00:00', '12:00:00', 30, 1, 30),
(197, 2, 6, '14:00:00', '18:00:00', 30, 1, 30),
(198, 12, 6, '07:00:00', '12:00:00', 30, 1, 30),
(199, 12, 6, '14:00:00', '18:00:00', 30, 1, 30),
(200, 18, 1, '07:00:00', '12:00:00', 30, 1, 30),
(201, 18, 1, '14:00:00', '18:00:00', 30, 1, 30),
(202, 18, 2, '07:00:00', '12:00:00', 30, 1, 30),
(203, 18, 2, '14:00:00', '18:00:00', 30, 1, 30),
(204, 18, 3, '07:00:00', '12:00:00', 30, 1, 30),
(205, 18, 3, '14:00:00', '18:00:00', 30, 1, 30),
(206, 18, 4, '07:00:00', '12:00:00', 30, 1, 30),
(207, 18, 4, '14:00:00', '18:00:00', 30, 1, 30),
(208, 18, 5, '07:00:00', '12:00:00', 30, 1, 30),
(209, 18, 5, '14:00:00', '18:00:00', 30, 1, 30),
(210, 18, 6, '07:00:00', '12:00:00', 30, 1, 30),
(211, 18, 6, '14:00:00', '18:00:00', 30, 1, 30),
(212, 20, 1, '07:00:00', '12:00:00', 30, 1, 30),
(213, 20, 1, '14:00:00', '18:00:00', 30, 1, 30),
(214, 20, 2, '07:00:00', '12:00:00', 30, 1, 30),
(215, 20, 2, '14:00:00', '18:00:00', 30, 1, 30),
(216, 20, 3, '07:00:00', '12:00:00', 30, 1, 30),
(217, 20, 3, '14:00:00', '18:00:00', 30, 1, 30),
(218, 20, 4, '07:00:00', '12:00:00', 30, 1, 30),
(219, 20, 4, '14:00:00', '18:00:00', 30, 1, 30),
(220, 20, 5, '07:00:00', '12:00:00', 30, 1, 30),
(221, 20, 5, '14:00:00', '18:00:00', 30, 1, 30),
(222, 20, 6, '07:00:00', '12:00:00', 30, 1, 30),
(223, 20, 6, '14:00:00', '18:00:00', 30, 1, 30);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicos`
--

DROP TABLE IF EXISTS `medicos`;
CREATE TABLE IF NOT EXISTS `medicos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `telefono` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `especialidad_id` int NOT NULL,
  `matricula` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `disponible` tinyint(1) NOT NULL DEFAULT '1',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `usuario_id` int DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_medicos_especialidades` (`especialidad_id`),
  KEY `fk_medicos_usuarios` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `medicos`
--

INSERT INTO `medicos` (`id`, `nombre`, `apellido`, `email`, `telefono`, `especialidad_id`, `matricula`, `descripcion`, `disponible`, `activo`, `usuario_id`, `creado_en`) VALUES
(1, 'Juan', 'Pérez', 'jperez@clinicasanluis.com', '0981000001', 1, 'MAT-1001', 'Médico clínico.', 1, 1, NULL, '2026-04-29 16:44:42'),
(2, 'María', 'Gómez', 'mgomez@clinicasanluis.com', '0981000002', 2, 'MAT-1002', 'Especialista en pediatría.', 1, 1, NULL, '2026-04-29 16:44:42'),
(3, 'Carlos', 'Fernández', 'cfernandez@clinicasanluis.com', '0981000003', 3, 'MAT-1003', 'Especialista en cardiología.', 1, 1, NULL, '2026-04-29 16:44:42'),
(4, 'THIAGO', '-', 'medico@clinicasanluis.com', '4643546546', 1, '54687468', '', 1, 1, NULL, '2026-04-29 17:03:35'),
(5, 'Oscar', 'Perez', 'medico@clinicasanluis.com', '53541351', 4, '54687468', '', 1, 1, NULL, '2026-04-29 17:33:07'),
(6, 'Jesús', 'López', '', '4643546546', 5, '465465464', '', 1, 1, NULL, '2026-04-29 17:39:54'),
(7, 'Pedro', 'Marecos', 'medico@clinicasanluis.com', '213268651', 3, '57557888', '', 1, 1, NULL, '2026-04-29 17:40:36'),
(8, 'José', 'Perez', 'medico@clinicasanluis.com', '4643546', 5, '165465', 'Neurólogo', 1, 1, NULL, '2026-05-07 19:55:56'),
(9, 'José', 'Perez', 'drjos@clinica.com', '53541351', 3, '54687468', 'corazon', 1, 1, NULL, '2026-05-08 02:22:42'),
(10, 'Ana', 'Giménez', 'anagimenez@clinica.com', '0981123457', 1, 'M-1002', 'Médico de Clínica General', 1, 1, NULL, '2026-06-29 18:55:54'),
(11, 'Roberto', 'Acosta', 'robertoacosta@clinica.com', '0981123458', 1, 'M-1003', 'Especialista en Clínica General', 1, 1, NULL, '2026-06-29 18:55:54'),
(12, 'Laura', 'Villalba', 'lauravillalba@clinica.com', '0982123456', 2, 'M-2001', 'Pediatra especialista en neonatología', 1, 1, NULL, '2026-06-29 18:55:54'),
(13, 'Miguel', 'Ávalos', 'miguelavalos@clinica.com', '0982123457', 2, 'M-2002', 'Pediatra especialista en adolescencia', 1, 1, NULL, '2026-06-29 18:55:54'),
(14, 'Ruth', 'Benítez', 'ruthbenitez@clinica.com', '0983123457', 3, 'M-3002', 'Cardióloga intervencionista', 1, 1, NULL, '2026-06-29 18:55:54'),
(15, 'Carmen', 'Duarte', 'carmenduarte@clinica.com', '0984123456', 4, 'M-4001', 'Dermatóloga especialista en cáncer de piel', 1, 1, NULL, '2026-06-29 18:55:54'),
(16, 'Fabián', 'Rojas', 'fabianrojas@clinica.com', '0984123457', 4, 'M-4002', 'Dermatólogo especialista en cosmiatría', 1, 1, NULL, '2026-06-29 18:55:54'),
(17, 'Héctor', 'Galeano', 'hectorgaleano@clinica.com', '0985123456', 5, 'M-5002', 'Cirujano general especialista en laparoscopía', 1, 1, NULL, '2026-06-29 18:55:54'),
(18, 'Traumatología', '(por defecto)', '', '', 7, '', 'Médico automático de Traumatología', 1, 0, NULL, '2026-06-29 19:20:45'),
(19, 'José', 'Marecos', 'josemarecos@gmail.com', '79879879879', 7, '66666666444', '', 1, 1, NULL, '2026-06-29 19:36:39'),
(20, 'Neurologo', '(por defecto)', '', '', 8, '', 'Médico automático de Neurologo', 1, 1, NULL, '2026-06-29 19:37:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text,
  `tipo` varchar(20) DEFAULT 'info',
  `leido` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `leido` (`leido`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `usuario_id`, `titulo`, `mensaje`, `tipo`, `leido`, `created_at`) VALUES
(1, NULL, 'Nueva cita registrada', 'sasa - 2026-06-30 07:30:00', 'info', 0, '2026-06-28 18:57:58'),
(2, NULL, 'Estado de cita actualizado (AJAX)', 'Cita #10 cambio a estado 2', 'info', 0, '2026-06-28 22:25:52'),
(3, NULL, 'Estado de cita actualizado (AJAX)', 'Cita #10 cambio a estado 1', 'info', 0, '2026-06-28 22:25:53'),
(4, NULL, 'Nueva cita registrada', 'kevin rolon - 2026-06-30 10:00:00', 'info', 0, '2026-06-28 22:29:15'),
(5, NULL, 'Nueva cita registrada', 'kevin rolon - 2026-07-03 11:30:00', 'info', 0, '2026-06-29 15:49:46'),
(6, NULL, 'Nueva cita registrada', 'david lopez - 2026-07-03 11:30:00', 'info', 0, '2026-06-29 15:58:30'),
(7, NULL, 'Estado de cita actualizado (AJAX)', 'Cita #15 cambio a estado 2', 'info', 0, '2026-06-29 15:58:41'),
(8, NULL, 'Estado de cita actualizado (AJAX)', 'Cita #15 cambio a estado 3', 'info', 0, '2026-06-29 15:58:44'),
(9, NULL, 'Estado de cita actualizado (AJAX)', 'Cita #15 cambio a estado 4', 'info', 0, '2026-06-29 15:58:46'),
(10, NULL, 'Estado de cita actualizado (AJAX)', 'Cita #15 cambio a estado 1', 'info', 0, '2026-06-29 15:01:54'),
(11, NULL, 'Nuevo paciente registrado', 'José Perez - prueba@gmail.com', 'success', 0, '2026-06-29 15:07:32'),
(12, NULL, 'Nueva cita registrada', 'david lopez - 2026-08-27 11:30:00', 'info', 0, '2026-06-29 15:11:01'),
(13, NULL, 'Estado de cita actualizado (AJAX)', 'Cita #16 cambio a estado 2', 'info', 0, '2026-06-29 15:24:34'),
(14, NULL, 'Estado de cita actualizado (AJAX)', 'Cita #15 cambio a estado 2', 'info', 0, '2026-06-29 15:24:36'),
(15, NULL, 'Estado de cita actualizado (AJAX)', 'Cita #13 cambio a estado 2', 'info', 0, '2026-06-29 15:24:37'),
(16, NULL, 'Estado de cita actualizado (AJAX)', 'Cita #11 cambio a estado 2', 'info', 0, '2026-06-29 15:24:38'),
(17, NULL, 'Estado de cita actualizado (AJAX)', 'Cita #16 cambio a estado 3', 'info', 0, '2026-06-29 15:24:50'),
(18, NULL, 'Estado de cita actualizado (AJAX)', 'Cita #15 cambio a estado 3', 'info', 0, '2026-06-29 15:24:51'),
(19, NULL, 'Estado de cita actualizado (AJAX)', 'Cita #13 cambio a estado 3', 'info', 0, '2026-06-29 15:24:53'),
(20, NULL, 'Estado de cita actualizado (AJAX)', 'Cita #11 cambio a estado 3', 'info', 0, '2026-06-29 15:24:55'),
(21, NULL, 'Nueva cita registrada', 'fernando - 2026-07-04 17:00:00', 'info', 0, '2026-06-29 15:26:36'),
(22, NULL, 'Nueva cita registrada', 'Andrea Gonzales - 2026-07-03 11:00:00', 'info', 0, '2026-06-29 15:30:32'),
(23, NULL, 'Nueva cita registrada', 'sasa - 2026-07-03 07:00:00', 'info', 0, '2026-06-29 15:32:12'),
(24, NULL, 'Nueva cita registrada', 'david lopez - 2026-07-02 07:30:00', 'info', 0, '2026-06-29 15:39:01'),
(25, NULL, 'Nueva cita registrada', 'sasa - 2026-07-02 09:30:00', 'info', 0, '2026-06-29 17:03:35'),
(26, NULL, 'Nueva cita registrada', 'kevin - 2026-07-02 07:30:00', 'info', 0, '2026-06-29 18:29:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

DROP TABLE IF EXISTS `permisos`;
CREATE TABLE IF NOT EXISTS `permisos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `accion` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`, `modulo`, `accion`) VALUES
(1, 'citas.ver', 'citas', 'ver'),
(2, 'citas.crear', 'citas', 'crear'),
(3, 'citas.editar', 'citas', 'editar'),
(4, 'citas.eliminar', 'citas', 'eliminar'),
(5, 'citas.cambiar_estado', 'citas', 'cambiar_estado'),
(6, 'medicos.ver', 'medicos', 'ver'),
(7, 'medicos.crear', 'medicos', 'crear'),
(8, 'medicos.editar', 'medicos', 'editar'),
(9, 'medicos.eliminar', 'medicos', 'eliminar'),
(10, 'especialidades.ver', 'especialidades', 'ver'),
(11, 'especialidades.crear', 'especialidades', 'crear'),
(12, 'especialidades.eliminar', 'especialidades', 'eliminar'),
(13, 'usuarios.ver', 'usuarios', 'ver'),
(14, 'usuarios.crear', 'usuarios', 'crear'),
(15, 'usuarios.eliminar', 'usuarios', 'eliminar'),
(16, 'pacientes.ver', 'pacientes', 'ver'),
(17, 'pacientes.historia', 'pacientes', 'historia'),
(18, 'dashboard.ver', 'dashboard', 'ver'),
(19, 'reportes.ver', 'reportes', 'ver'),
(20, 'auditoria.ver', 'auditoria', 'ver'),
(21, 'configuracion.ver', 'configuracion', 'ver'),
(22, 'configuracion.editar', 'configuracion', 'editar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'administrador', NULL),
(2, 'recepcionista', NULL),
(3, 'medico', NULL),
(4, 'admin', 'Acceso total al sistema'),
(5, 'recepcion', 'Gestión de citas y pacientes'),
(6, 'paciente', 'Portal del paciente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permiso`
--

DROP TABLE IF EXISTS `rol_permiso`;
CREATE TABLE IF NOT EXISTS `rol_permiso` (
  `rol_id` int NOT NULL,
  `permiso_id` int NOT NULL,
  PRIMARY KEY (`rol_id`,`permiso_id`),
  KEY `permiso_id` (`permiso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `rol_permiso`
--

INSERT INTO `rol_permiso` (`rol_id`, `permiso_id`) VALUES
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(4, 2),
(5, 2),
(6, 2),
(4, 3),
(5, 3),
(4, 4),
(3, 5),
(4, 5),
(5, 5),
(4, 6),
(5, 6),
(4, 7),
(4, 8),
(4, 9),
(4, 10),
(5, 10),
(4, 11),
(4, 12),
(4, 13),
(4, 14),
(4, 15),
(3, 16),
(4, 16),
(5, 16),
(3, 17),
(4, 17),
(3, 18),
(4, 18),
(5, 18),
(4, 19),
(4, 20),
(4, 21),
(4, 22);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones`
--

DROP TABLE IF EXISTS `sesiones`;
CREATE TABLE IF NOT EXISTS `sesiones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `session_token` varchar(64) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_activity` datetime DEFAULT CURRENT_TIMESTAMP,
  `activa` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_token` (`session_token`),
  KEY `idx_sesiones_usuario` (`usuario_id`),
  KEY `idx_sesiones_token` (`session_token`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `sesiones`
--

INSERT INTO `sesiones` (`id`, `usuario_id`, `session_token`, `ip`, `user_agent`, `created_at`, `last_activity`, `activa`) VALUES
(1, 1, '6d2054a592f13cd6b80ce916fb85dd0973afc1dd0b0f21e303173366ca8a1df0', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-07 22:55:54', '2026-05-07 22:55:54', 1),
(2, 1, '59ad4bb78ebe22f9ac7c1c08f07c1755097d8be2054143e21020a5161412e346', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-24 21:00:29', '2026-06-24 21:00:29', 1),
(3, 1, '58e1a3ec85eb88115f3bd4924c90ab3541b9c51fc78c4d5d8c8e7ffdb6ddbbb5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 12:39:58', '2026-06-28 12:39:58', 1),
(4, 1, '66c144c0ea851bf0183ce155e092d4d0994834a9db1a80d9d1b0b1cb251febdb', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 13:51:44', '2026-06-28 13:51:44', 1),
(5, 1, '36d256e608b50b0776429382e8d30344ffd51e366ee7a0f5ad67a6d465db2b8f', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 14:37:07', '2026-06-28 14:37:07', 1),
(6, 1, 'ee157c21e8246f08080c510f4e5f94e0eb84417b89345d3035c70f9fb5d6e45b', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 19:01:06', '2026-06-28 19:01:06', 1),
(7, 1, 'dc6209859401b2c411bdc28ab758cf12d6930a1b091ea2c1ce5108262020db98', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 22:25:40', '2026-06-28 22:25:40', 1),
(8, 1, 'ebcd433e9347ea1205549fe583e9774b796021aefccfb2a7fcc3cf43cf2ebd11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 22:32:13', '2026-06-28 22:32:13', 1),
(9, 1, '948590bbfcc728a71ad538965848166dcbecb521887d14ee4c14344562fcd20a', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:38:33', '2026-06-29 15:38:33', 1),
(10, 1, '882eb6eb8bae0f364a607a739f0ed1e706b9d47d75fed904869a89fc5888e5a8', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:24:23', '2026-06-29 15:24:23', 1),
(11, 1, '7c29ae42268772561151b37019e94c3e3240fe47b82f60070ef2641bdd857d9e', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:30:45', '2026-06-29 15:30:45', 1),
(12, 1, '06a1bd9a167cef071be8c9ecc2662bac8c4b7c18c28a9bda011ead0dc7271bf2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:34:06', '2026-06-29 15:34:06', 1),
(13, 1, '9de656042b2c53a5fb96e25186ff6ff5f80fe0b0a88178cc600d6fdbf6a469b9', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:34:11', '2026-06-29 15:34:11', 1),
(14, 1, '9d0bbc87d04472af662a931bad05ff09524962a7191a9608392f4acf0f097076', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:50:28', '2026-06-29 15:50:28', 1),
(15, 1, 'e450946f5c66bf5f729039f354f9799d48d06ff3d2ec8d1f757561d12e9c8d10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 15:57:46', '2026-06-29 15:57:46', 1),
(16, 1, '36a20114134f960c81c3cd1ba83648f2e3300e204614769f7ba73c32a988abbd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 16:12:57', '2026-06-29 16:12:57', 1),
(17, 1, '9b6f0a5585df95db86c345e1efa90167a185c5961847e34dc714da61814034c7', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 17:29:50', '2026-06-29 17:29:50', 1),
(18, 1, 'c5a5f1a98718cf8d9a24a36c1f46f09979aa582f1b76363f39ffb69bda9bc994', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-29 18:30:16', '2026-06-29 18:30:16', 1),
(19, 1, '278622972946c474d9db2afbbc82b11872335d50664f1f1fb98bd1f8319c4978', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 23:31:46', '2026-07-01 23:31:46', 1),
(20, 1, 'e151cedfba57c085af7583e24bf329fc73a8b28f7a689ff2493e8528186f6aac', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 18:20:37', '2026-08-10 18:20:37', 1),
(21, 1, '85d83b6b99ba8c774c02450976a2a89c19edb792cd1994bec7dff52c59fff3fd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 14:16:29', '2026-08-21 14:16:29', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `rol_id` int DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `login_attempts` tinyint DEFAULT '0',
  `locked_until` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_ip` varchar(45) COLLATE utf8mb4_bin DEFAULT NULL,
  `two_factor_secret` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_usuarios_roles` (`rol_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `rol_id`, `activo`, `creado_en`, `login_attempts`, `locked_until`, `last_login`, `last_ip`, `two_factor_secret`, `two_factor_enabled`) VALUES
(1, 'Admin', 'Sistema', 'admin@clinicasanluis.com', '$2y$10$2Y5c.6U5v.a5.woY9NEEJehk514ESv/zdE1j8N5LAW5siSRL/YnzW', 1, 1, '2026-04-29 16:44:42', 0, NULL, '2026-08-21 14:16:29', '::1', NULL, 0),
(2, 'Recepcion', 'Sistema', 'recepcion@clinicasanluis.com', 'TU_HASH_BCRYPT_AQUI', 2, 0, '2026-04-29 16:44:42', 0, NULL, NULL, NULL, NULL, 0),
(3, 'Recepción', 'Recepción', 'recepcion@gmail.com', '$2y$10$RXLGWA0tlF/IAG1.Ui43neBmyv6rByEESAxlptOD0rxZlhGJH4PfW', 2, 1, '2026-05-08 01:08:51', 0, NULL, NULL, NULL, NULL, 0),
(4, 'José', 'Marecos', 'babeha4785@brixozu.com', '$2y$10$gs6uQSQFxKLwnBBFjXs1O./ty7hRdqhMGsbucy/NMIkxyF9Jl9.bS', 6, 1, '2026-06-28 17:15:38', 0, NULL, '2026-06-28 22:27:10', '::1', NULL, 0),
(5, 'José', 'Perez', 'prueba@gmail.com', '$2y$10$.iIkGZBJ3R5R8UIOnvD8d.K/zoh0IOZS7ixHbFT8tVpBA2WZbre8i', 6, 1, '2026-06-29 19:07:32', 0, NULL, '2026-06-29 18:33:23', '::1', NULL, 0);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `bloqueos_medico`
--
ALTER TABLE `bloqueos_medico`
  ADD CONSTRAINT `fk_bloqueo_medico` FOREIGN KEY (`medico_id`) REFERENCES `medicos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `fk_citas_especialidades` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_citas_estados` FOREIGN KEY (`estado_id`) REFERENCES `estados_citas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_citas_medicos` FOREIGN KEY (`medico_id`) REFERENCES `medicos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `fk_horarios_medicos` FOREIGN KEY (`medico_id`) REFERENCES `medicos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `medicos`
--
ALTER TABLE `medicos`
  ADD CONSTRAINT `fk_medicos_especialidades` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_medicos_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `rol_permiso`
--
ALTER TABLE `rol_permiso`
  ADD CONSTRAINT `rol_permiso_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rol_permiso_ibfk_2` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD CONSTRAINT `sesiones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
