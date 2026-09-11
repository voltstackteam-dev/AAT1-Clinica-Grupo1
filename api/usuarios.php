<?php
require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/jwt.php';


$metodo = iniciarApi();
ejecutarApi(function () use ($conexion, $metodo): void {
    verificarRol([1]);
    $base = 'SELECT 
                u.id_usuario,
                u.email,
                u.id_rol,
                u.activo,
                u.created_at,
                r.nombre_rol 
            FROM usuarios u 
            JOIN roles r 
            ON r.id_rol=u.id_rol';
            
    if ($metodo === 'GET') {
        $p = [];
        $sql = $base;
        if (isset($_GET['id'])) {
            $sql .= ' WHERE u.id_usuario=:id';
            $p[':id'] = idRequerido();
        }
        $s = $conexion->prepare($sql . ' ORDER BY u.id_usuario DESC');
        $s->execute($p);
        $data = isset($_GET['id']) ? $s->fetch() : $s->fetchAll();
        if (isset($_GET['id']) && !$data) responderError('Usuario no encontrado.', 404);
        responder(['success' => true, 'cantidad' => is_array($data) ? count($data) : 1, 'data' => $data]);
    }
    if ($metodo === 'DELETE') {
        $s = $conexion->prepare('DELETE FROM usuarios WHERE id_usuario=:id');
        $s->execute([':id' => idRequerido()]);
        if (!$s->rowCount()) responderError('Usuario no encontrado.', 404);
        responder(['success' => true, 'mensaje' => 'Usuario eliminado.']);
    }
    $d = leerJson();
    requerirCampos($d, ['email', 'id_rol']);
    if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) responderError('Email invalido.');
    if (!existe($conexion, 'roles', 'id_rol', (int)$d['id_rol'])) responderError('El rol no existe.');
    $p = [':email' => trim($d['email']), ':rol' => (int)$d['id_rol'], ':activo' => isset($d['activo']) ? (int)(bool)$d['activo'] : 1];
    if ($metodo === 'POST') {
        requerirCampos($d, ['contrasenia']);
        $p[':clave'] = password_hash($d['contrasenia'], PASSWORD_DEFAULT);
        $s = $conexion->prepare('INSERT INTO usuarios (email,contrasenia,id_rol,activo) VALUES (:email,:clave,:rol,:activo)');
        $s->execute($p);
        responder(['success' => true, 'id_usuario' => (int)$conexion->lastInsertId()], 201);
    }
    if ($metodo === 'PUT') {
        $p[':id'] = idRequerido();
        $set = 'email=:email,id_rol=:rol,activo=:activo';
        if (!empty($d['contrasenia'])) {
            $set .= ',contrasenia=:clave';
            $p[':clave'] = password_hash($d['contrasenia'], PASSWORD_DEFAULT);
        }
        $s = $conexion->prepare("UPDATE usuarios SET {$set} WHERE id_usuario=:id");
        $s->execute($p);
        responder(['success' => true, 'mensaje' => 'Usuario actualizado.']);
    }
    responderError('Metodo no permitido.', 405);
});
