<?php

// Reutiliza la conexión central de la API para mantener una sola configuración.
require_once __DIR__ . '/../api/config/conexion.php';

// Los endpoints históricos de Farmacia utilizan el nombre $pdo.
$pdo = $conexion;

?>
