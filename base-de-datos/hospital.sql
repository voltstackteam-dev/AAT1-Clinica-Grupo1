CREATE DATABASE  IF NOT EXISTS `hospital` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `hospital`;
-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: localhost    Database: hospital
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `administradores`
--

DROP TABLE IF EXISTS `administradores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `administradores` (
  `id_administrador` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_administrador`),
  UNIQUE KEY `id_usuario_UNIQUE` (`id_usuario`),
  CONSTRAINT `fk_administradores_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administradores`
--

LOCK TABLES `administradores` WRITE;
/*!40000 ALTER TABLE `administradores` DISABLE KEYS */;
/*!40000 ALTER TABLE `administradores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `citas`
--

DROP TABLE IF EXISTS `citas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `citas` (
  `id_cita` int(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL,
  `id_medico` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `motivo_consulta` text DEFAULT NULL,
  `estado` enum('PENDIENTE','CONFIRMADA','CANCELADA','COMPLETADA') NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_cita`),
  KEY `fk_citas_pacientes_idx` (`id_paciente`),
  KEY `fk_citas_medicos_idx` (`id_medico`),
  KEY `fk_citas_salas_idx` (`id_sala`),
  KEY `idx_fecha_hora` (`fecha_hora`),
  CONSTRAINT `fk_citas_medicos` FOREIGN KEY (`id_medico`) REFERENCES `medicos` (`id_medico`) ON UPDATE CASCADE,
  CONSTRAINT `fk_citas_pacientes` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON UPDATE CASCADE,
  CONSTRAINT `fk_citas_salas` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id_sala`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citas`
--

LOCK TABLES `citas` WRITE;
/*!40000 ALTER TABLE `citas` DISABLE KEYS */;
/*!40000 ALTER TABLE `citas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disponibilidades`
--

DROP TABLE IF EXISTS `disponibilidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disponibilidades` (
  `id_disponibilidad` int(11) NOT NULL AUTO_INCREMENT,
  `id_medico` int(11) NOT NULL,
  `dia_semana` enum('LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  PRIMARY KEY (`id_disponibilidad`),
  KEY `fk_disponibilidades_medicos_idx` (`id_medico`),
  CONSTRAINT `fk_disponibilidades_medicos` FOREIGN KEY (`id_medico`) REFERENCES `medicos` (`id_medico`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disponibilidades`
--

LOCK TABLES `disponibilidades` WRITE;
/*!40000 ALTER TABLE `disponibilidades` DISABLE KEYS */;
/*!40000 ALTER TABLE `disponibilidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipos`
--

DROP TABLE IF EXISTS `equipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipos` (
  `id_equipo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_equipo` varchar(100) NOT NULL,
  `id_sala` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_equipo`),
  KEY `fk_equipos_salas_idx` (`id_sala`),
  CONSTRAINT `fk_equipos_salas` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id_sala`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipos`
--

LOCK TABLES `equipos` WRITE;
/*!40000 ALTER TABLE `equipos` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `especialidades`
--

DROP TABLE IF EXISTS `especialidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `especialidades` (
  `id_especialidad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_especialidad` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id_especialidad`),
  UNIQUE KEY `nombre_especialidad_UNIQUE` (`nombre_especialidad`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `especialidades`
--

LOCK TABLES `especialidades` WRITE;
/*!40000 ALTER TABLE `especialidades` DISABLE KEYS */;
INSERT INTO `especialidades` VALUES (1,'Medicina General','Atención médica primaria'),(2,'Pediatría','Atención médica infantil'),(3,'Cardiología','Enfermedades del corazón');
/*!40000 ALTER TABLE `especialidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_medico`
--

DROP TABLE IF EXISTS `historial_medico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_medico` (
  `id_historial` int(11) NOT NULL AUTO_INCREMENT,
  `id_cita` int(11) NOT NULL,
  `diagnostico` text NOT NULL,
  `receta` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_atencion` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_historial`),
  UNIQUE KEY `id_cita_UNIQUE` (`id_cita`),
  CONSTRAINT `fk_historial_citas` FOREIGN KEY (`id_cita`) REFERENCES `citas` (`id_cita`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_medico`
--

LOCK TABLES `historial_medico` WRITE;
/*!40000 ALTER TABLE `historial_medico` DISABLE KEYS */;
/*!40000 ALTER TABLE `historial_medico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medicos`
--

DROP TABLE IF EXISTS `medicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medicos` (
  `id_medico` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_especialidad` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `colegiado_num` varchar(45) DEFAULT NULL,
  `telefono` varchar(20) NOT NULL,
  PRIMARY KEY (`id_medico`),
  UNIQUE KEY `id_usuario_UNIQUE` (`id_usuario`),
  KEY `fk_medicos_especialidades_idx` (`id_especialidad`),
  CONSTRAINT `fk_medicos_especialidades` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidades` (`id_especialidad`) ON UPDATE CASCADE,
  CONSTRAINT `fk_medicos_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicos`
--

LOCK TABLES `medicos` WRITE;
/*!40000 ALTER TABLE `medicos` DISABLE KEYS */;
/*!40000 ALTER TABLE `medicos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pacientes`
--

DROP TABLE IF EXISTS `pacientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pacientes` (
  `id_paciente` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_paciente`),
  UNIQUE KEY `id_usuario_UNIQUE` (`id_usuario`),
  CONSTRAINT `fk_pacientes_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pacientes`
--

LOCK TABLES `pacientes` WRITE;
/*!40000 ALTER TABLE `pacientes` DISABLE KEYS */;
/*!40000 ALTER TABLE `pacientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(45) NOT NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `nombre_rol_UNIQUE` (`nombre_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'administrador'),(2,'medico'),(3,'paciente');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salas`
--

DROP TABLE IF EXISTS `salas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salas` (
  `id_sala` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_sala` varchar(50) NOT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `id_especialidad` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_sala`),
  KEY `fk_salas_especialidades_idx` (`id_especialidad`),
  CONSTRAINT `fk_salas_especialidades` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidades` (`id_especialidad`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salas`
--

LOCK TABLES `salas` WRITE;
/*!40000 ALTER TABLE `salas` DISABLE KEYS */;
/*!40000 ALTER TABLE `salas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `contrasenia` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `fk_usuarios_roles_idx` (`id_rol`),
  CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-25 13:26:59
