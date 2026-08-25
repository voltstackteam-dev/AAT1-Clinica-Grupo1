<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['id_medico']) && !empty($data['id_cliente']) && !empty($data['id_sala']) && !empty($data['id_horario']) && !empty($data['motivo_consulta'])) {
    try {
        $sql = "INSERT INTO tb_citas (id_medico, id_cliente, id_sala, id_horario, motivo_consulta, estado_cita) 
                VALUES (:id_medico, :id_cliente, :id_sala, :id_horario, :motivo, 'pendiente')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_medico' => $data['id_medico'],
            ':id_cliente' => $data['id_cliente'],
            ':id_sala' => $data['id_sala'],
            ':id_horario' => $data['id_horario'],
            ':motivo' => $data['motivo_consulta']
        ]);

        $update = $pdo->prepare("UPDATE tb_horarios SET disponibilidad = 0 WHERE id_horario = :id_horario");
        $update->execute([':id_horario' => $data['id_horario']]);

        echo json_encode(["status" => "success", "message" => "Cita agendada con éxito"]);
    } catch (\PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
}
?>