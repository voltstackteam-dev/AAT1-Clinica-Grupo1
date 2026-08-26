<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Responder inmediatamente a peticiones preflight (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'conexion.php';

// Obtener los datos JSON enviados desde Angular
$inputData = json_decode(file_get_contents("php://input"), true);

if (!$inputData) {
    echo json_encode([
        "status" => "error",
        "message" => "No se recibieron datos en formato JSON"
    ]);
    exit();
}

// Extraer y limpiar los campos del formulario
$nombre_paciente   = trim($inputData['nombre_paciente'] ?? '');
$dpi_paciente      = trim($inputData['dpi_paciente'] ?? '');
$email_paciente    = trim($inputData['email_paciente'] ?? '');
$telefono_paciente = trim($inputData['telefono_paciente'] ?? '');
$id_especialidad   = isset($inputData['id_especialidad']) ? intval($inputData['id_especialidad']) : 1;
$fecha_cita        = $inputData['fecha_cita'] ?? null;
$hora_cita         = $inputData['hora_cita'] ?? null;
$motivo_consulta   = $inputData['motivo_consulta'] ?? 'Consulta Médica General';

// 1. Validar campos obligatorios
if (empty($nombre_paciente) || empty($dpi_paciente) || empty($fecha_cita) || empty($hora_cita)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Faltan datos obligatorios (Nombre, DPI, Fecha u Hora)"
    ]);
    exit();
}

// 2. Validar rango de horario de atención médica (08:00 AM a 04:00 PM)
$horaInt = intval(substr($hora_cita, 0, 2));

if ($horaInt < 8 || $horaInt > 16) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "El horario de atención es exclusivamente de 08:00 AM a 04:00 PM."
    ]);
    exit();
}

try {
    // 3. Generar código de operación único para el panel clínico (ej. V-4821)
    $codigo_operacion = 'V-' . rand(1000, 9999);

    // 4. Asignar automáticamente un médico de la especialidad elegida si existe
    $stmtMed = $pdo->prepare("SELECT id_medico FROM tb_medicos WHERE id_especialidad = :id_esp LIMIT 1");
    $stmtMed->execute([':id_esp' => $id_especialidad]);
    $medico = $stmtMed->fetch(PDO::FETCH_ASSOC);
    $id_medico = $medico ? $medico['id_medico'] : 1;

    // 5. Insertar la cita en tb_citas
    $sql = "INSERT INTO tb_citas (
                codigo_operacion, 
                nombre_paciente, 
                dpi_paciente, 
                email_paciente, 
                telefono_paciente, 
                id_medico, 
                fecha_cita, 
                hora_cita, 
                motivo_consulta, 
                estado_cita
            ) VALUES (
                :codigo_operacion, 
                :nombre_paciente, 
                :dpi_paciente, 
                :email_paciente, 
                :telefono_paciente, 
                :id_medico, 
                :fecha_cita, 
                :hora_cita, 
                :motivo_consulta, 
                'pendiente'
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':codigo_operacion'  => $codigo_operacion,
        ':nombre_paciente'   => $nombre_paciente,
        ':dpi_paciente'      => $dpi_paciente,
        ':email_paciente'    => $email_paciente,
        ':telefono_paciente' => $telefono_paciente,
        ':id_medico'         => $id_medico,
        ':fecha_cita'        => $fecha_cita,
        ':hora_cita'         => $hora_cita,
        ':motivo_consulta'   => $motivo_consulta
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Cita agendada correctamente",
        "codigo_operacion" => $codigo_operacion,
        "id_cita" => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Error en la base de datos: " . $e->getMessage()
    ]);
}
?>