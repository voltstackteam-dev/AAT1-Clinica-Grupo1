<?php

$servidor = getenv('DB_HOST') ?: 'localhost';
$puerto = getenv('DB_PORT') ?: '3308';
$usuario = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$baseDatos = getenv('DB_NAME') ?: 'hospital';

try {

    $conexion = new PDO(
        "mysql:host=$servidor;port=$puerto;dbname=$baseDatos;charset=utf8mb4",
        $usuario,
        $password,
       [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => "Error de conexión a la base de datos"
        /* ,
        "error" => $e->getMessage() */
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

?>
