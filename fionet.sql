-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         9.1.0 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para fionet
CREATE DATABASE IF NOT EXISTS `fionet` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `fionet`;

-- Volcando estructura para tabla fionet.administradores
CREATE TABLE IF NOT EXISTS `administradores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre_completo` varchar(150) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla fionet.administradores: 1 rows
/*!40000 ALTER TABLE `administradores` DISABLE KEYS */;
INSERT IGNORE INTO `administradores` (`id`, `usuario`, `password`, `nombre_completo`, `email`, `creado_en`) VALUES
	(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador Principal', 'admin@fionet.com', '2025-09-23 23:12:22');
/*!40000 ALTER TABLE `administradores` ENABLE KEYS */;

-- Volcando estructura para tabla fionet.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `localidad_id` int NOT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dni` (`dni`),
  KEY `localidad_id` (`localidad_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla fionet.clientes: 2 rows
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT IGNORE INTO `clientes` (`id`, `nombre`, `apellido`, `dni`, `email`, `telefono`, `localidad_id`, `creado_en`) VALUES
	(11, 'florencia', 'perez', '11444777', NULL, NULL, 10, '2025-10-07 00:57:58'),
	(12, 'fernando', 'ojeda', '22555888', NULL, NULL, 11, '2025-10-07 01:19:13');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;

-- Volcando estructura para tabla fionet.localidades
CREATE TABLE IF NOT EXISTS `localidades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla fionet.localidades: 2 rows
/*!40000 ALTER TABLE `localidades` DISABLE KEYS */;
INSERT IGNORE INTO `localidades` (`id`, `nombre`, `creado_en`) VALUES
	(10, 'La Leonesa', '2025-10-07 00:57:58'),
	(11, 'Empedrado', '2025-10-07 01:19:13');
/*!40000 ALTER TABLE `localidades` ENABLE KEYS */;

-- Volcando estructura para tabla fionet.reclamos
CREATE TABLE IF NOT EXISTS `reclamos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero_ticket` varchar(10) NOT NULL,
  `cliente_id` int NOT NULL,
  `descripcion` text NOT NULL,
  `respuestas` text COMMENT 'JSON con respuestas técnicas',
  `estado` enum('Pendiente','En Proceso','Resuelto','Cancelado') DEFAULT 'Pendiente',
  `prioridad` enum('Baja','Media','Alta') DEFAULT 'Media',
  `asignado_a` int DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_ticket` (`numero_ticket`),
  KEY `cliente_id` (`cliente_id`),
  KEY `asignado_a` (`asignado_a`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla fionet.reclamos: 2 rows
/*!40000 ALTER TABLE `reclamos` DISABLE KEYS */;
INSERT IGNORE INTO `reclamos` (`id`, `numero_ticket`, `cliente_id`, `descripcion`, `respuestas`, `estado`, `prioridad`, `asignado_a`, `creado_en`, `actualizado_en`) VALUES
	(11, 'FIO-001', 11, 'no funciona hace una semana', '{"router":"S\\u00ed","cables":"S\\u00ed","dispositivo":"S\\u00ed","wifi":"S\\u00ed","los":"No","contrasena":"No","error":"S\\u00ed"}', 'En Proceso', 'Media', NULL, '2025-10-07 00:57:58', '2025-10-07 00:59:00'),
	(12, 'FIO-002', 12, 'sin servicio', '{"router":"No","cables":"S\\u00ed","dispositivo":"S\\u00ed","wifi":"No","los":"S\\u00ed","contrasena":"S\\u00ed","error":"S\\u00ed"}', 'Resuelto', 'Media', NULL, '2025-10-07 01:19:13', '2025-10-07 01:21:06');
/*!40000 ALTER TABLE `reclamos` ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
