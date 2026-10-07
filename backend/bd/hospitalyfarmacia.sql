-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-10-2026 a las 00:36:41
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
-- Base de datos: `hospitalyfarmacia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id_administrador` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id_administrador`, `id_usuario`, `nombre`, `apellido`, `telefono`) VALUES
(1, 5, 'Roberto', 'Sánchez', '22360001'),
(2, 6, 'Laura', 'Gomez', '22360002');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_medicamentos`
--

CREATE TABLE `categorias_medicamentos` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias_medicamentos`
--

INSERT INTO `categorias_medicamentos` (`id_categoria`, `nombre_categoria`, `descripcion`) VALUES
(1, 'Analgésicos', 'Alivia el dolor'),
(2, 'Antiinflamatorios', 'Reduce la inflamación y alivia el dolor'),
(3, 'Antipireticos', 'Para reducir la fiebre'),
(4, 'Antibióticos', 'Para tratar infecciones bacterianas'),
(5, 'Antihipertensivos', 'Controlan la presión arterial'),
(6, 'Antidiabéticos', 'Regulan los niveles de glucosa en la sangre'),
(7, 'Antihistamínico', 'Para las alergias'),
(8, 'Vitaminas', 'Vitaminas varios');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id_cita` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_medico` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `motivo_consulta` text DEFAULT NULL,
  `estado` enum('PENDIENTE','CONFIRMADA','EN_PROCESO','CANCELADA','COMPLETADA') NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id_cita`, `id_paciente`, `id_medico`, `id_sala`, `fecha_hora`, `motivo_consulta`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2026-09-11 11:00:00', 'Consulta General', 'COMPLETADA', '2026-09-10 06:32:43', '2026-09-26 06:08:06'),
(2, 4, 1, 1, '2026-09-30 13:00:00', 'Gripe', 'COMPLETADA', '2026-09-26 00:14:19', '2026-09-26 07:19:09'),
(3, 4, 1, 1, '2026-09-29 14:00:00', 'Seguimiento a la consulta de la migraña', 'COMPLETADA', '2026-09-28 20:57:00', '2026-09-28 22:02:08'),
(4, 3, 1, 1, '2026-10-01 16:00:00', 'Dolor de estomado', 'COMPLETADA', '2026-09-28 23:12:02', '2026-09-29 02:19:11'),
(5, 4, 1, 2, '2026-10-02 10:00:00', 'Dolores musculares y fiebres altas', 'COMPLETADA', '2026-09-28 23:49:15', '2026-10-01 02:17:20'),
(6, 3, 1, 2, '2026-10-01 14:00:00', 'Fiebre alta', 'EN_PROCESO', '2026-10-01 02:25:44', '2026-10-01 02:30:32'),
(7, 4, 2, 1, '2026-10-12 11:00:00', 'Seguimiento a la gripe', 'EN_PROCESO', '2026-10-07 18:18:05', '2026-10-07 20:16:03'),
(8, 1, 2, 1, '2026-10-12 12:00:00', 'consulta pediadtrica', 'COMPLETADA', '2026-10-07 20:08:45', '2026-10-07 20:10:52'),
(9, 1, 2, 1, '2026-10-26 13:00:00', 'consulta de seguimiento', 'CONFIRMADA', '2026-10-07 21:00:34', '2026-10-07 21:00:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `disponibilidades`
--

CREATE TABLE `disponibilidades` (
  `id_disponibilidad` int(11) NOT NULL,
  `id_medico` int(11) NOT NULL,
  `dia_semana` enum('LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `disponibilidades`
--

INSERT INTO `disponibilidades` (`id_disponibilidad`, `id_medico`, `dia_semana`, `hora_inicio`, `hora_fin`) VALUES
(1, 1, 'LUNES', '08:00:00', '12:00:00'),
(2, 1, 'MARTES', '13:00:00', '17:00:00'),
(3, 1, 'MIERCOLES', '08:00:00', '17:00:00'),
(4, 1, 'JUEVES', '13:00:00', '17:00:00'),
(5, 1, 'VIERNES', '08:00:00', '12:00:00'),
(6, 2, 'LUNES', '08:00:00', '17:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id_equipo` int(11) NOT NULL,
  `nombre_equipo` varchar(100) NOT NULL,
  `id_sala` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialidades`
--

CREATE TABLE `especialidades` (
  `id_especialidad` int(11) NOT NULL,
  `nombre_especialidad` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `especialidades`
--

INSERT INTO `especialidades` (`id_especialidad`, `nombre_especialidad`, `descripcion`) VALUES
(1, 'Medicina General', 'Atención médica primaria'),
(2, 'Pediatría', 'Atención médica infantil'),
(3, 'Cardiología', 'Enfermedades del corazón');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_medico`
--

CREATE TABLE `historial_medico` (
  `id_historial` int(11) NOT NULL,
  `id_cita` int(11) NOT NULL,
  `diagnostico` text NOT NULL,
  `receta` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_atencion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_medico`
--

INSERT INTO `historial_medico` (`id_historial`, `id_cita`, `diagnostico`, `receta`, `observaciones`, `fecha_atencion`) VALUES
(1, 2, 'migraña', NULL, 'cada dos días', '2026-09-26 07:19:09'),
(2, 3, 'Gripe', NULL, 'Mantenerse hidratado y reposo', '2026-09-28 22:02:08'),
(3, 4, 'Amebas', NULL, 'Dieta de comida blanda', '2026-09-29 02:19:11'),
(4, 5, 'Posible infección', NULL, 'Reposo e hidratación', '2026-10-01 02:17:20'),
(5, 8, 'mocos', NULL, 'dolor en la nariz', '2026-10-07 20:10:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotes_inventario`
--

CREATE TABLE `lotes_inventario` (
  `id_lote` int(11) NOT NULL,
  `id_medicamento` int(11) NOT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `numero_lote` varchar(50) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `cantidad_inicial` int(11) NOT NULL,
  `cantidad_actual` int(11) NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `fecha_ingreso` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicos`
--

CREATE TABLE `medicos` (
  `id_medico` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_especialidad` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `colegiado_num` varchar(45) DEFAULT NULL,
  `telefono` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicos`
--

INSERT INTO `medicos` (`id_medico`, `id_usuario`, `id_especialidad`, `nombre`, `apellido`, `colegiado_num`, `telefono`) VALUES
(1, 3, 1, 'Carlos', 'López', '14205', '3214-5678'),
(2, 4, 2, 'Ana', 'Martinez', '18340', '5555-0202');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `id_paciente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pacientes`
--

INSERT INTO `pacientes` (`id_paciente`, `id_usuario`, `nombre`, `apellido`, `fecha_nacimiento`, `telefono`, `direccion`, `created_at`) VALUES
(1, 1, 'Juan Manuel', 'Chica', '1995-04-12', '4123-5678', 'Guatemala', '2026-09-10 05:04:40'),
(3, 2, 'Maria', 'Garcia', '1990-11-20', '5987-1234', 'Guatemala', '2026-09-10 05:05:48'),
(4, 7, 'Andrea', 'Marroquin', '1996-06-20', '55951893', 'Ciudad', '2026-09-25 18:29:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id_proveedor` int(11) NOT NULL,
  `nombre_empresa` varchar(100) NOT NULL,
  `nit_runc` varchar(20) DEFAULT NULL,
  `contacto_nombre` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recetas`
--

CREATE TABLE `recetas` (
  `id_receta` int(11) NOT NULL,
  `id_historial` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_medico` int(11) NOT NULL,
  `fecha_emision` timestamp NULL DEFAULT current_timestamp(),
  `estado` enum('EMITIDA','DESPACHADA_PARCIAL','DESPACHADA_TOTAL','CANCELADA') NOT NULL DEFAULT 'EMITIDA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recetas`
--

INSERT INTO `recetas` (`id_receta`, `id_historial`, `id_paciente`, `id_medico`, `fecha_emision`, `estado`) VALUES
(1, 1, 4, 1, '2026-09-26 07:19:09', 'EMITIDA'),
(2, 2, 4, 1, '2026-09-28 22:02:08', 'EMITIDA'),
(3, 3, 3, 1, '2026-09-29 02:19:11', 'EMITIDA'),
(4, 4, 4, 1, '2026-10-01 02:17:20', 'EMITIDA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receta_detalles`
--

CREATE TABLE `receta_detalles` (
  `id_receta_detalle` int(11) NOT NULL,
  `id_receta` int(11) NOT NULL,
  `id_medicamento` int(11) NOT NULL,
  `dosis` varchar(100) NOT NULL,
  `frecuencia` varchar(100) NOT NULL,
  `duracion_dias` int(11) NOT NULL,
  `cantidad_prescrita` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `receta_detalles`
--

INSERT INTO `receta_detalles` (`id_receta_detalle`, `id_receta`, `id_medicamento`, `dosis`, `frecuencia`, `duracion_dias`, `cantidad_prescrita`) VALUES
(1, 1, 5, '1', '8', 1, 1),
(2, 2, 1, '1 tableta(s)', 'Cada 24 horas', 5, 5),
(3, 2, 2, '1 tableta(s)', 'Cada 12 horas', 5, 10),
(4, 3, 1, '1 tableta(s)', 'Cada 6 horas', 5, 20),
(5, 4, 1, '1 tableta(s)', 'Cada 12 horas', 2, 4),
(6, 4, 2, '1 tableta(s)', 'Cada 6 horas', 3, 12),
(7, 4, 1, '2 tableta(s)', 'Cada 6 horas', 5, 40);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
(1, 'administrador'),
(2, 'medico'),
(3, 'paciente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salas`
--

CREATE TABLE `salas` (
  `id_sala` int(11) NOT NULL,
  `nombre_sala` varchar(50) NOT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `id_especialidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `salas`
--

INSERT INTO `salas` (`id_sala`, `nombre_sala`, `ubicacion`, `id_especialidad`) VALUES
(1, 'Consultorio A1', 'Sección A', 1),
(2, 'Consultorio B2', 'Sección B', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_medicamentos`
--

CREATE TABLE `tb_medicamentos` (
  `id_medicamento` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `categoria` varchar(60) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `principio_activo` varchar(150) NOT NULL,
  `requiere_receta` tinyint(1) NOT NULL DEFAULT 0,
  `presentacion` varchar(50) NOT NULL,
  `imagen_url` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_medicamentos`
--

INSERT INTO `tb_medicamentos` (`id_medicamento`, `id_categoria`, `nombre`, `categoria`, `precio`, `stock`, `principio_activo`, `requiere_receta`, `presentacion`, `imagen_url`) VALUES
(1, 1, 'Paracetamol 500 mg', 'Analgésicos', 12.50, 25, '', 0, '', NULL),
(2, 1, 'Ibuprofeno 400 mg', 'Analgésicos', 18.00, 18, '', 0, '', NULL),
(3, 1, 'Amoxicilina 500 mg', 'Antibióticos', 42.00, 10, '', 0, '', NULL),
(4, 7, 'Loratadina 10 mg', 'Antihistamínicos', 24.50, 20, '', 0, '', NULL),
(5, 8, 'Vitamina C 1000 mg', 'Vitaminas', 35.00, 30, '', 0, '', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasenia` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `email`, `contrasenia`, `id_rol`, `activo`, `created_at`) VALUES
(1, 'paciente_juan@gmail.com', 'clave123', 3, 1, '2026-09-10 01:18:41'),
(2, 'paciente_maria@gmail.com', 'clave123', 3, 1, '2026-09-10 04:52:06'),
(3, 'dr_carlos@gmail.com', 'clave123', 2, 1, '2026-09-10 04:52:59'),
(4, 'dra_ana@gmail.com', 'clave123', 2, 1, '2026-09-10 04:53:21'),
(5, 'admin_roberto@gmail.com', 'clave123', 1, 1, '2026-09-10 04:53:52'),
(6, 'admin_laura@gmail.com', 'clave123', 1, 1, '2026-09-10 04:54:15'),
(7, 'andrealissa1996@gmail.com', '$2y$10$3FwMwzZNDvkJdqIMWyw4feAFyiQcoXGsCSuas7AcUc2KSQdfupKsW', 3, 1, '2026-09-25 18:29:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_farmacia`
--

CREATE TABLE `ventas_farmacia` (
  `id_venta` int(11) NOT NULL,
  `id_paciente` int(11) DEFAULT NULL,
  `id_receta` int(11) DEFAULT NULL,
  `id_usuario_cajero` int(11) NOT NULL,
  `fecha_venta` timestamp NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metodo_pago` enum('EFECTIVO','TARJETA','TRANSFERENCIA') NOT NULL DEFAULT 'EFECTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id_administrador`),
  ADD UNIQUE KEY `id_usuario_UNIQUE` (`id_usuario`);

--
-- Indices de la tabla `categorias_medicamentos`
--
ALTER TABLE `categorias_medicamentos`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre_categoria_UNIQUE` (`nombre_categoria`);

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id_cita`),
  ADD KEY `fk_citas_pacientes_idx` (`id_paciente`),
  ADD KEY `fk_citas_medicos_idx` (`id_medico`),
  ADD KEY `fk_citas_salas_idx` (`id_sala`),
  ADD KEY `idx_fecha_hora` (`fecha_hora`);

--
-- Indices de la tabla `disponibilidades`
--
ALTER TABLE `disponibilidades`
  ADD PRIMARY KEY (`id_disponibilidad`),
  ADD KEY `fk_disponibilidades_medicos_idx` (`id_medico`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id_equipo`),
  ADD KEY `fk_equipos_salas_idx` (`id_sala`);

--
-- Indices de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  ADD PRIMARY KEY (`id_especialidad`),
  ADD UNIQUE KEY `nombre_especialidad_UNIQUE` (`nombre_especialidad`);

--
-- Indices de la tabla `historial_medico`
--
ALTER TABLE `historial_medico`
  ADD PRIMARY KEY (`id_historial`),
  ADD UNIQUE KEY `id_cita_UNIQUE` (`id_cita`);

--
-- Indices de la tabla `lotes_inventario`
--
ALTER TABLE `lotes_inventario`
  ADD PRIMARY KEY (`id_lote`),
  ADD KEY `fk_lotes_medicamentos_idx` (`id_medicamento`),
  ADD KEY `fk_lotes_proveedores_idx` (`id_proveedor`);

--
-- Indices de la tabla `medicos`
--
ALTER TABLE `medicos`
  ADD PRIMARY KEY (`id_medico`),
  ADD UNIQUE KEY `id_usuario_UNIQUE` (`id_usuario`),
  ADD KEY `fk_medicos_especialidades_idx` (`id_especialidad`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id_paciente`),
  ADD UNIQUE KEY `id_usuario_UNIQUE` (`id_usuario`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `recetas`
--
ALTER TABLE `recetas`
  ADD PRIMARY KEY (`id_receta`),
  ADD KEY `fk_recetas_historial_idx` (`id_historial`),
  ADD KEY `fk_recetas_pacientes_idx` (`id_paciente`),
  ADD KEY `fk_recetas_medicos_idx` (`id_medico`);

--
-- Indices de la tabla `receta_detalles`
--
ALTER TABLE `receta_detalles`
  ADD PRIMARY KEY (`id_receta_detalle`),
  ADD KEY `fk_recetadetalles_recetas_idx` (`id_receta`),
  ADD KEY `fk_recetadetalles_medicamentos_idx` (`id_medicamento`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre_rol_UNIQUE` (`nombre_rol`);

--
-- Indices de la tabla `salas`
--
ALTER TABLE `salas`
  ADD PRIMARY KEY (`id_sala`),
  ADD KEY `fk_salas_especialidades_idx` (`id_especialidad`);

--
-- Indices de la tabla `tb_medicamentos`
--
ALTER TABLE `tb_medicamentos`
  ADD PRIMARY KEY (`id_medicamento`),
  ADD KEY `fk_medicamentos_categorias_idx` (`id_categoria`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`),
  ADD KEY `fk_usuarios_roles_idx` (`id_rol`);

--
-- Indices de la tabla `ventas_farmacia`
--
ALTER TABLE `ventas_farmacia`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `fk_ventas_pacientes_idx` (`id_paciente`),
  ADD KEY `fk_ventas_recetas_idx` (`id_receta`),
  ADD KEY `fk_ventas_usuarios_idx` (`id_usuario_cajero`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id_administrador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `categorias_medicamentos`
--
ALTER TABLE `categorias_medicamentos`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id_cita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `disponibilidades`
--
ALTER TABLE `disponibilidades`
  MODIFY `id_disponibilidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id_equipo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  MODIFY `id_especialidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `historial_medico`
--
ALTER TABLE `historial_medico`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `lotes_inventario`
--
ALTER TABLE `lotes_inventario`
  MODIFY `id_lote` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `medicos`
--
ALTER TABLE `medicos`
  MODIFY `id_medico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id_paciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recetas`
--
ALTER TABLE `recetas`
  MODIFY `id_receta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `receta_detalles`
--
ALTER TABLE `receta_detalles`
  MODIFY `id_receta_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `salas`
--
ALTER TABLE `salas`
  MODIFY `id_sala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tb_medicamentos`
--
ALTER TABLE `tb_medicamentos`
  MODIFY `id_medicamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `ventas_farmacia`
--
ALTER TABLE `ventas_farmacia`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD CONSTRAINT `fk_administradores_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `fk_citas_medicos` FOREIGN KEY (`id_medico`) REFERENCES `medicos` (`id_medico`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_citas_pacientes` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_citas_salas` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id_sala`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `disponibilidades`
--
ALTER TABLE `disponibilidades`
  ADD CONSTRAINT `fk_disponibilidades_medicos` FOREIGN KEY (`id_medico`) REFERENCES `medicos` (`id_medico`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD CONSTRAINT `fk_equipos_salas` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id_sala`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `historial_medico`
--
ALTER TABLE `historial_medico`
  ADD CONSTRAINT `fk_historial_citas` FOREIGN KEY (`id_cita`) REFERENCES `citas` (`id_cita`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `lotes_inventario`
--
ALTER TABLE `lotes_inventario`
  ADD CONSTRAINT `fk_lotes_medicamentos` FOREIGN KEY (`id_medicamento`) REFERENCES `tb_medicamentos` (`id_medicamento`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lotes_proveedores` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `medicos`
--
ALTER TABLE `medicos`
  ADD CONSTRAINT `fk_medicos_especialidades` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidades` (`id_especialidad`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_medicos_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD CONSTRAINT `fk_pacientes_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recetas`
--
ALTER TABLE `recetas`
  ADD CONSTRAINT `fk_recetas_historial` FOREIGN KEY (`id_historial`) REFERENCES `historial_medico` (`id_historial`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recetas_medicos` FOREIGN KEY (`id_medico`) REFERENCES `medicos` (`id_medico`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recetas_pacientes` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `receta_detalles`
--
ALTER TABLE `receta_detalles`
  ADD CONSTRAINT `fk_recetadetalles_medicamentos` FOREIGN KEY (`id_medicamento`) REFERENCES `tb_medicamentos` (`id_medicamento`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recetadetalles_recetas` FOREIGN KEY (`id_receta`) REFERENCES `recetas` (`id_receta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `salas`
--
ALTER TABLE `salas`
  ADD CONSTRAINT `fk_salas_especialidades` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidades` (`id_especialidad`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `tb_medicamentos`
--
ALTER TABLE `tb_medicamentos`
  ADD CONSTRAINT `fk_medicamentos_categorias` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_medicamentos` (`id_categoria`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas_farmacia`
--
ALTER TABLE `ventas_farmacia`
  ADD CONSTRAINT `fk_ventas_pacientes` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ventas_recetas` FOREIGN KEY (`id_receta`) REFERENCES `recetas` (`id_receta`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ventas_usuarios` FOREIGN KEY (`id_usuario_cajero`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
