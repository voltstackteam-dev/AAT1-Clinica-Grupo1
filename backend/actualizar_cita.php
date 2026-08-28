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

$id_cita = isset($data['id_cita']) ? intval($data['id_cita']) : 0;
$accion  = trim($data['accion'] ?? ''); // 'autorizar', 'cancelar', 'notas'
$notas   = trim($data['observaciones'] ?? '');

if ($id_cita <= 0 || empty($accion)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
    exit();
}

try {
    if ($accion === 'autorizar') {
        $stmt = $pdo->prepare("UPDATE tb_citas SET estado_cita = 'confirmada' WHERE id_cita = :id");
        $stmt->execute([':id' => $id_cita]);
        $msg = "Cita confirmada y autorizada";
    } elseif ($accion === 'cancelar') {
        $stmt = $pdo->prepare("UPDATE tb_citas SET estado_cita = 'cancelada' WHERE id_cita = :id");
        $stmt->execute([':id' => $id_cita]);
        $msg = "Cita cancelada correctamente";
    } elseif ($accion === 'notas') {
        $stmt = $pdo->prepare("UPDATE tb_citas SET diagnosticos = :notas WHERE id_cita = :id");
        $stmt->execute([':notas' => $notas, ':id' => $id_cita]);
        $msg = "Observaciones actualizadas";
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Acción no válida"]);
        exit();
    }

    echo json_encode(["status" => "success", "message" => $msg]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>