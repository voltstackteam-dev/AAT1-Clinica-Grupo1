<?php

function iniciarApi(): string
{
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Origin: http://localhost:4200');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if ($metodo === 'OPTIONS') {
        http_response_code(204);
        exit;
    }

    return $metodo;
}

function leerJson(): array
{
    $datos = json_decode(file_get_contents('php://input'), true);
    if (!is_array($datos)) {
        responderError('El cuerpo debe ser un objeto JSON valido.', 400);
    }
    return $datos;
}

function responder(array $datos, int $codigo = 200): void
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

function responderError(string $mensaje, int $codigo = 400): void
{
    responder(['success' => false, 'mensaje' => $mensaje], $codigo);
}

function requerirCampos(array $datos, array $campos): void
{
    foreach ($campos as $campo) {
        if (!array_key_exists($campo, $datos) || $datos[$campo] === '') {
            responderError("El campo {$campo} es obligatorio.");
        }
    }
}

function idRequerido(): int
{
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        responderError('Debe indicar un ID valido.');
    }
    return $id;
}

function existe(PDO $conexion, string $tabla, string $columna, int $id): bool
{
    $permitidos = [
        'usuarios' => 'id_usuario', 'roles' => 'id_rol', 'pacientes' => 'id_paciente',
        'medicos' => 'id_medico', 'salas' => 'id_sala', 'especialidades' => 'id_especialidad',
        'administradores' => 'id_administrador', 'equipos' => 'id_equipo',
        'citas' => 'id_cita', 'disponibilidades' => 'id_disponibilidad'
    ];
    if (!isset($permitidos[$tabla]) || $permitidos[$tabla] !== $columna) {
        throw new InvalidArgumentException('Tabla no permitida.');
    }
    $stmt = $conexion->prepare("SELECT 1 FROM {$tabla} WHERE {$columna} = :id");
    $stmt->execute([':id' => $id]);
    return (bool) $stmt->fetchColumn();
}

function ejecutarApi(callable $accion): void
{
    try {
        $accion();
    } catch (PDOException $e) {
        $codigo = $e->getCode() === '23000' ? 409 : 500;
        responderError($codigo === 409 ? 'La operacion viola una relacion o un valor unico.' : 'Error al procesar la solicitud.', $codigo);
    } catch (InvalidArgumentException $e) {
        responderError($e->getMessage());
    }
}
