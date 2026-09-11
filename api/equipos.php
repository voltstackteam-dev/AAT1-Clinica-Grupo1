<?php
require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
$metodo = iniciarApi();
ejecutarApi(function () use ($conexion, $metodo): void {
    $base = 'SELECT e.id_equipo,e.nombre_equipo,e.id_sala,e.cantidad,s.nombre_sala FROM equipos e LEFT JOIN salas s ON s.id_sala=e.id_sala';
    if ($metodo === 'GET') {
        $p = [];
        $sql = $base;
        if (isset($_GET['id'])) {
            $sql .= ' WHERE e.id_equipo=:id';
            $p[':id'] = idRequerido();
        }
        $s = $conexion->prepare($sql . ' ORDER BY e.nombre_equipo');
        $s->execute($p);
        $data = isset($_GET['id']) ? $s->fetch() : $s->fetchAll();
        if (isset($_GET['id']) && !$data) responderError('Equipo no encontrado.', 404);
        responder(['success' => true, 'cantidad' => is_array($data) ? count($data) : 1, 'data' => $data]);
    }
    $d = leerJson();
    requerirCampos($d, ['nombre_equipo', 'cantidad']);
    $sala = !empty($d['id_sala']) ? (int)$d['id_sala'] : null;
    if ($sala && !existe($conexion, 'salas', 'id_sala', $sala)) responderError('La sala no existe.');
    $p = [':nombre' => trim($d['nombre_equipo']), ':sala' => $sala, ':cantidad' => (int)$d['cantidad']];
    if ($p[':cantidad'] < 1) responderError('La cantidad debe ser mayor que cero.');
    if ($metodo === 'POST') {
        $s = $conexion->prepare('INSERT INTO equipos (nombre_equipo,id_sala,cantidad) VALUES (:nombre,:sala,:cantidad)');
        $s->execute($p);
        responder(['success' => true, 'id_equipo' => (int)$conexion->lastInsertId()], 201);
    }
    if ($metodo === 'PUT') {
        $p[':id'] = idRequerido();
        $s = $conexion->prepare('UPDATE equipos SET nombre_equipo=:nombre,id_sala=:sala,cantidad=:cantidad WHERE id_equipo=:id');
        $s->execute($p);
        responder(['success' => true, 'mensaje' => 'Equipo actualizado.']);
    }
    if ($metodo === 'DELETE') {
        $s = $conexion->prepare('DELETE FROM equipos WHERE id_equipo=:id');
        $s->execute([':id' => idRequerido()]);
        if (!$s->rowCount()) responderError('Equipo no encontrado.', 404);
        responder(['success' => true, 'mensaje' => 'Equipo eliminado.']);
    }
    responderError('Metodo no permitido.', 405);
});
