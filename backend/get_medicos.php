<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

require_once 'conexion.php';

$idEspecialidad = isset($_GET['id_especialidad']) ? intval($_GET['id_especialidad']) : 0;

try {
    if ($idEspecialidad > 0) {
        $stmt = $pdo->prepare("SELECT m.id_medico, m.nombre_med, m.apellido_med, m.telefono_med, m.equipo_disponible, m.foto_med, e.nombre_especialidad 
                               FROM tb_medicos m 
                               JOIN tb_especialidades e ON m.id_especialidad = e.id_especialidad 
                               WHERE m.id_especialidad = :id");
        $stmt->execute(['id' => $idEspecialidad]);
    } else {
        $stmt = $pdo->query("SELECT m.id_medico, m.nombre_med, m.apellido_med, m.telefono_med, m.equipo_disponible, m.foto_med, e.nombre_especialidad 
                             FROM tb_medicos m 
                             JOIN tb_especialidades e ON m.id_especialidad = e.id_especialidad");
    }
    
    $medicos = $stmt->fetchAll();
    echo json_encode(["status" => "success", "data" => $medicos]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>