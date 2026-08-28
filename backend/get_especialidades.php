<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once 'conexion.php';

try {
    $stmt = $pdo->query("SELECT * FROM tb_especialidades");
    $especialidades = $stmt->fetchAll();
    echo json_encode(["status" => "success", "data" => $especialidades], JSON_UNESCAPED_UNICODE);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>