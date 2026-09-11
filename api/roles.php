<?php
require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
$metodo = iniciarApi();
ejecutarApi(function () use ($conexion, $metodo): void {
    if ($metodo === 'GET') {
        $sql = 'SELECT id_rol, nombre_rol FROM roles';
        $parametros = [];
        if (isset($_GET['id'])) {
            $sql .= ' WHERE id_rol = :id';
            $parametros[':id'] = idRequerido();
        }
        $sql .= ' ORDER BY nombre_rol';
        $stmt = $conexion->prepare($sql);
        $stmt->execute($parametros);
        $data = isset($_GET['id']) ? $stmt->fetch() : $stmt->fetchAll();
        if (isset($_GET['id']) && !$data) responderError('Rol no encontrado.', 404);
        responder(['success' => true, 'cantidad' => is_array($data) ? count($data) : 1, 'data' => $data]);
    }
    $datos = leerJson();
    if ($metodo === 'POST') {
        requerirCampos($datos, ['nombre_rol']);
        $stmt = $conexion->prepare('INSERT INTO roles (nombre_rol) VALUES (:nombre)');
        $stmt->execute([':nombre' => trim($datos['nombre_rol'])]);
        responder(['success' => true, 'id_rol' => (int) $conexion->lastInsertId()], 201);
    }
    if ($metodo === 'PUT') {
        requerirCampos($datos, ['nombre_rol']);
        $stmt = $conexion->prepare('UPDATE roles SET nombre_rol = :nombre WHERE id_rol = :id');
        $stmt->execute([':nombre' => trim($datos['nombre_rol']), ':id' => idRequerido()]);
        responder(['success' => true, 'mensaje' => 'Rol actualizado.']);
    }
    if ($metodo === 'DELETE') {
        $stmt = $conexion->prepare('DELETE FROM roles WHERE id_rol = :id');
        $stmt->execute([':id' => idRequerido()]);
        if (!$stmt->rowCount()) responderError('Rol no encontrado.', 404);
        responder(['success' => true, 'mensaje' => 'Rol eliminado.']);
    }
    responderError('Metodo no permitido.', 405);
});
