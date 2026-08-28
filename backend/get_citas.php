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

try {
    $sql = "SELECT 
                c.id_cita,
                c.codigo_operacion,
                c.nombre_paciente,
                c.dpi_paciente,
                c.email_paciente,
                c.telefono_paciente,
                c.fecha_cita,
                c.hora_cita,
                c.motivo_consulta,
                c.diagnosticos AS observaciones,
                c.estado_cita,
                CONCAT('Dr. ', m.nombre_med, ' ', m.apellido_med) AS nombre_medico,
                e.nombre_especialidad
            FROM tb_citas c
            LEFT JOIN tb_medicos m ON c.id_medico = m.id_medico
            LEFT JOIN tb_especialidades e ON m.id_especialidad = e.id_especialidad
            ORDER BY c.id_cita DESC";

    $stmt = $pdo->query($sql);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "total" => count($citas),
        "data" => $citas
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Error al consultar citas: " . $e->getMessage()
    ]);
}
?>