<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'conexion.php';

$categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';

try {
    if (!empty($categoria) && $categoria !== 'Todos') {
        $stmt = $pdo->prepare("SELECT * FROM tb_medicamentos WHERE categoria = :cat");
        $stmt->execute([':cat' => $categoria]);
    } else {
        $stmt = $pdo->query("SELECT * FROM tb_medicamentos");
    }

    $medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "total" => count($medicamentos),
        "data" => $medicamentos
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Error al consultar medicamentos: " . $e->getMessage()
    ]);
}
?>