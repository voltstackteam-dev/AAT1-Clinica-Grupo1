<?php
require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
$metodo = iniciarApi();
ejecutarApi(function () use ($conexion, $metodo): void {
    if ($metodo === 'GET') {
        $sql = 'SELECT id_especialidad, nombre_especialidad, descripcion FROM especialidades'; $params = [];
        if (isset($_GET['id'])) { $sql .= ' WHERE id_especialidad = :id'; $params[':id'] = idRequerido(); }
        $sql .= ' ORDER BY nombre_especialidad'; $stmt = $conexion->prepare($sql); $stmt->execute($params);
        $data = isset($_GET['id']) ? $stmt->fetch() : $stmt->fetchAll();
        if (isset($_GET['id']) && !$data) responderError('Especialidad no encontrada.', 404);
        responder(['success' => true, 'cantidad' => is_array($data) ? count($data) : 1, 'data' => $data]);
    }
    $datos = leerJson(); requerirCampos($datos, ['nombre_especialidad']);
    if ($metodo === 'POST') { $stmt = $conexion->prepare('INSERT INTO especialidades (nombre_especialidad, descripcion) VALUES (:nombre, :descripcion)'); $stmt->execute([':nombre' => trim($datos['nombre_especialidad']), ':descripcion' => $datos['descripcion'] ?? null]); responder(['success' => true, 'id_especialidad' => (int) $conexion->lastInsertId()], 201); }
    if ($metodo === 'PUT') { $stmt = $conexion->prepare('UPDATE especialidades SET nombre_especialidad = :nombre, descripcion = :descripcion WHERE id_especialidad = :id'); $stmt->execute([':nombre' => trim($datos['nombre_especialidad']), ':descripcion' => $datos['descripcion'] ?? null, ':id' => idRequerido()]); responder(['success' => true, 'mensaje' => 'Especialidad actualizada.']); }
    if ($metodo === 'DELETE') { $stmt = $conexion->prepare('DELETE FROM especialidades WHERE id_especialidad = :id'); $stmt->execute([':id' => idRequerido()]); if (!$stmt->rowCount()) responderError('Especialidad no encontrada.', 404); responder(['success' => true, 'mensaje' => 'Especialidad eliminada.']); }
    responderError('Metodo no permitido.', 405);
});
