<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id_medicamento'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "ID no proporcionado"]);
    exit();
}

$id_medicamento = intval($data['id_medicamento']);
$nuevo_stock = isset($data['stock']) ? intval($data['stock']) : null;
$nuevo_precio = isset($data['precio']) ? floatval($data['precio']) : null;

try {
    $campos = [];
    $parametros = [':id' => $id_medicamento];

    if ($nuevo_stock !== null) {
        $campos[] = "stock = :stock";
        $parametros[':stock'] = $nuevo_stock;
    }
    if ($nuevo_precio !== null) {
        $campos[] = "precio = :precio";
        $parametros[':precio'] = $nuevo_precio;
    }

    if (empty($campos)) {
        echo json_encode(["status" => "warning", "message" => "Sin datos para actualizar"]);
        exit();
    }

    $sql = "UPDATE tb_medicamentos SET " . implode(", ", $campos) . " WHERE id_medicamento = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);

    echo json_encode([
        "status" => "success",
        "message" => "Medicamento actualizado exitosamente"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>