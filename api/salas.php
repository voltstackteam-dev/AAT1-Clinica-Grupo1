<?php
require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
$metodo = iniciarApi();
ejecutarApi(function () use ($conexion, $metodo): void {
    $base = 'SELECT s.id_sala, s.nombre_sala, s.ubicacion, s.id_especialidad, e.nombre_especialidad FROM salas s LEFT JOIN especialidades e ON e.id_especialidad = s.id_especialidad';
    if ($metodo === 'GET') {
        $params = [];
        $sql = $base;
        if (isset($_GET['id'])) {
            $sql .= ' WHERE s.id_sala = :id';
            $params[':id'] = idRequerido();
        }
        $sql .= ' ORDER BY s.nombre_sala';
        $stmt = $conexion->prepare($sql);
        $stmt->execute($params);
        $data = isset($_GET['id']) ? $stmt->fetch() : $stmt->fetchAll();
        if (isset($_GET['id']) && !$data) responderError('Sala no encontrada.', 404);
        responder(['success' => true, 'cantidad' => is_array($data) ? count($data) : 1, 'data' => $data]);
    }
    $datos = leerJson();
    requerirCampos($datos, ['nombre_sala']);
    $especialidad = !empty($datos['id_especialidad']) ? (int) $datos['id_especialidad'] : null;
    if ($especialidad && !existe($conexion, 'especialidades', 'id_especialidad', $especialidad)) responderError('La especialidad no existe.');
    if ($metodo === 'POST') {
        $stmt = $conexion->prepare('INSERT INTO salas (nombre_sala, ubicacion, id_especialidad) VALUES (:nombre, :ubicacion, :especialidad)');
        $stmt->execute([':nombre' => trim($datos['nombre_sala']), ':ubicacion' => $datos['ubicacion'] ?? null, ':especialidad' => $especialidad]);
        responder(['success' => true, 'id_sala' => (int) $conexion->lastInsertId()], 201);
    }
    if ($metodo === 'PUT') {
        $stmt = $conexion->prepare('UPDATE salas SET nombre_sala = :nombre, ubicacion = :ubicacion, id_especialidad = :especialidad WHERE id_sala = :id');
        $stmt->execute([':nombre' => trim($datos['nombre_sala']), ':ubicacion' => $datos['ubicacion'] ?? null, ':especialidad' => $especialidad, ':id' => idRequerido()]);
        responder(['success' => true, 'mensaje' => 'Sala actualizada.']);
    }
    if ($metodo === 'DELETE') {
        $stmt = $conexion->prepare('DELETE FROM salas WHERE id_sala = :id');
        $stmt->execute([':id' => idRequerido()]);
        if (!$stmt->rowCount()) responderError('Sala no encontrada.', 404);
        responder(['success' => true, 'mensaje' => 'Sala eliminada.']);
    }
    responderError('Metodo no permitido.', 405);
});
