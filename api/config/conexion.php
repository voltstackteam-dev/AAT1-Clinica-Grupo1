<?php

$servidor = "localhost";
$usuario = "root";
$password = "";
$baseDatos = "db_sys_citas";

try {

    $conexion = new PDO(
        "mysql:host=$servidor;port=3307;dbname=$baseDatos;charset=utf8mb4",
        $usuario,
        $password,
       [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
/*     $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION */
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => "Error de conexión a la base de datos"
        /* ,
        "error" => $e->getMessage() */
    ]);

    exit;
}

?>