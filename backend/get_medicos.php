<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once 'conexion.php';

$id_especialidad = isset($_GET['id_especialidad']) ? intval($_GET['id_especialidad']) : 0;

try {
    if ($id_especialidad > 0) {
        $stmt = $pdo->prepare("SELECT m.id_medico, m.nombre_med, m.apellido_med, m.telefono_med, m.equipo_disponible, e.nombre_especialidad 
                               FROM tb_medicos m 
                               INNER JOIN tb_especialidades e ON m.id_especialidad = e.id_especialidad 
                               WHERE m.id_especialidad = :id");
        $stmt->execute(['id' => $id_especialidad]);
    } else {
        $stmt = $pdo->query("SELECT m.id_medico, m.nombre_med, m.apellido_med, m.telefono_med, m.equipo_disponible, e.nombre_especialidad 
                             FROM tb_medicos m 
                             INNER JOIN tb_especialidades e ON m.id_especialidad = e.id_especialidad");
    }
    $medicos = $stmt->fetchAll();
    echo json_encode(["status" => "success", "data" => $medicos], JSON_UNESCAPED_UNICODE);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>