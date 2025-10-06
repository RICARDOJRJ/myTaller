-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-10-2025 a las 00:19:44
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gestion_talleres`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `telefono`, `direccion`) VALUES
(7, 'JUAN MIGUEL', '0412-4856323', 'Calle Ficticia 123, Ciudad del Sol');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_empresa`
--

CREATE TABLE `configuracion_empresa` (
  `id` int(11) NOT NULL DEFAULT 1,
  `nombre_empresa` varchar(255) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `numero_fiscal` varchar(50) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_empresa`
--

INSERT INTO `configuracion_empresa` (`id`, `nombre_empresa`, `direccion`, `telefono`, `email`, `numero_fiscal`, `logo_path`) VALUES
(1, 'TALLER MECÁNICO Y@NIS TORRES', 'Calle Ficticia 123, Ciudad del Sol', '55-1234-5678', 'contacto@taller-excelsior.com', 'RFC-ABC123456', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_de_trabajo`
--

CREATE TABLE `ordenes_de_trabajo` (
  `id` int(11) NOT NULL,
  `vehiculo_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Pendiente','En Proceso','Completada','Cancelada') DEFAULT 'Pendiente',
  `costo_mano_obra` decimal(10,2) DEFAULT 0.00,
  `impuesto_porcentaje` decimal(5,2) DEFAULT 0.16
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenes_de_trabajo`
--

INSERT INTO `ordenes_de_trabajo` (`id`, `vehiculo_id`, `fecha`, `descripcion`, `estado`, `costo_mano_obra`, `impuesto_porcentaje`) VALUES
(9, 26, '2025-10-04', 'NUEVA PRACTICA PARA VERIFICAR MEJORAR', 'Pendiente', 200.00, 0.10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_repuestos`
--

CREATE TABLE `ordenes_repuestos` (
  `orden_id` int(11) NOT NULL,
  `repuesto_id` int(11) NOT NULL,
  `cantidad_utilizada` int(11) NOT NULL,
  `precio_unitario_venta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `repuestos`
--

CREATE TABLE `repuestos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `repuestos`
--

INSERT INTO `repuestos` (`id`, `nombre`, `cantidad`, `precio`) VALUES
(1, 'Filtro de Aire', 10, 15.50),
(2, 'Bujías (juego)', 5, 25.00),
(3, 'Aceite de Motor 5W-30', 20, 8.75),
(5, 'CAMARA', 1, 50.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `placa` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id`, `cliente_id`, `marca`, `modelo`, `placa`) VALUES
(26, 7, 'HUNDAY', 'NUEVO', '0415 - BC456');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuracion_empresa`
--
ALTER TABLE `configuracion_empresa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indices de la tabla `ordenes_de_trabajo`
--
ALTER TABLE `ordenes_de_trabajo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehiculo_id` (`vehiculo_id`);

--
-- Indices de la tabla `ordenes_repuestos`
--
ALTER TABLE `ordenes_repuestos`
  ADD PRIMARY KEY (`orden_id`,`repuesto_id`),
  ADD KEY `repuesto_id` (`repuesto_id`);

--
-- Indices de la tabla `repuestos`
--
ALTER TABLE `repuestos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `placa` (`placa`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `ordenes_de_trabajo`
--
ALTER TABLE `ordenes_de_trabajo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `repuestos`
--
ALTER TABLE `repuestos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ordenes_de_trabajo`
--
ALTER TABLE `ordenes_de_trabajo`
  ADD CONSTRAINT `ordenes_de_trabajo_ibfk_1` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ordenes_repuestos`
--
ALTER TABLE `ordenes_repuestos`
  ADD CONSTRAINT `ordenes_repuestos_ibfk_1` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_de_trabajo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ordenes_repuestos_ibfk_2` FOREIGN KEY (`repuesto_id`) REFERENCES `repuestos` (`id`);

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
