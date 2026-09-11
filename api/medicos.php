<?php
require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/jwt.php';


$metodo = iniciarApi();
ejecutarApi(function () use ($conexion, $metodo): void {
    $base = 'SELECT 
                m.id_medico, 
                m.id_usuario, 
                m.id_especialidad, 
                m.nombre, 
                m.apellido, 
                m.colegiado_num, 
                m.telefono, 
                u.email, 
                e.nombre_especialidad 
            FROM medicos m 
            JOIN usuarios u 
            ON u.id_usuario=m.id_usuario 
            JOIN especialidades e 
            ON e.id_especialidad=m.id_especialidad';

    if ($metodo === 'GET') {
        $where = [];
        $p = [];
        if (isset($_GET['id'])) {
            $where[] = 'm.id_medico=:id';
            $p[':id'] = idRequerido();
        }
        if (isset($_GET['id_especialidad'])) {
            $where[] = 'm.id_especialidad=:especialidad';
            $p[':especialidad'] = (int)$_GET['id_especialidad'];
        }
        $sql = $base . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY m.apellido, m.nombre';
        $s = $conexion->prepare($sql);
        $s->execute($p);
        $data = isset($_GET['id']) ? $s->fetch() : $s->fetchAll();
        if (isset($_GET['id']) && !$data) 
            responderError('Medico no encontrado.', 404);
        responder(['success' => true, 'cantidad' => is_array($data) ? count($data) : 1, 'data' => $data]);
    }
    verificarRol([1]);
    if ($metodo === 'DELETE') {
        $s = $conexion->prepare('DELETE FROM medicos WHERE id_medico=:id');
        $s->execute([':id' => idRequerido()]);
        if (!$s->rowCount()) responderError('Medico no encontrado.', 404);
        responder(['success' => true, 'mensaje' => 'Medico eliminado.']);
    }
    $d = leerJson();
    requerirCampos($d, ['id_usuario', 'id_especialidad', 'nombre', 'apellido', 'telefono']);
    foreach ([['usuarios', 'id_usuario'], ['especialidades', 'id_especialidad']] as [$t, $c]) if (!existe($conexion, $t, $c, (int)$d[$c])) responderError("El {$c} indicado no existe.");
    $p = [':usuario' => (int)$d['id_usuario'], ':especialidad' => (int)$d['id_especialidad'], ':nombre' => trim($d['nombre']), ':apellido' => trim($d['apellido']), ':colegiado' => $d['colegiado_num'] ?? null, ':telefono' => trim($d['telefono'])];
    if ($metodo === 'POST') {
        $s = $conexion->prepare('INSERT INTO medicos (id_usuario,id_especialidad,nombre,apellido,colegiado_num,telefono) VALUES (:usuario,:especialidad,:nombre,:apellido,:colegiado,:telefono)');
        $s->execute($p);
        responder(['success' => true, 'id_medico' => (int)$conexion->lastInsertId()], 201);
    }
    if ($metodo === 'PUT') {
        $p[':id'] = idRequerido();
        $s = $conexion->prepare('UPDATE medicos SET id_usuario=:usuario,id_especialidad=:especialidad,nombre=:nombre,apellido=:apellido,colegiado_num=:colegiado,telefono=:telefono WHERE id_medico=:id');
        $s->execute($p);
        responder(['success' => true, 'mensaje' => 'Medico actualizado.']);
    }
    responderError('Metodo no permitido.', 405);
});
