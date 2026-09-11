<?php
require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/jwt.php';


$metodo = iniciarApi();
ejecutarApi(function () use ($conexion, $metodo): void {
    verificarRol([1]);
    $base = 'SELECT 
                a.id_administrador, 
                a.id_usuario,a.nombre,
                a.apellido,a.telefono,u.email 
            FROM administradores a 
            JOIN usuarios u 
            ON u.id_usuario=a.id_usuario';
    if ($metodo === 'GET') {
        $p = [];
        $sql = $base;
        if (isset($_GET['id'])) {
            $sql .= ' WHERE a.id_administrador=:id';
            $p[':id'] = idRequerido();
        }
        $s = $conexion->prepare($sql . ' ORDER BY a.apellido,a.nombre');
        $s->execute($p);
        $data = isset($_GET['id']) ? $s->fetch() : $s->fetchAll();
        if (isset($_GET['id']) && !$data) responderError('Administrador no encontrado.', 404);
        responder(['success' => true, 'cantidad' => is_array($data) ? count($data) : 1, 'data' => $data]);
    }
    if ($metodo === 'DELETE') {
        $s = $conexion->prepare('DELETE FROM administradores WHERE id_administrador=:id');
        $s->execute([':id' => idRequerido()]);
        if (!$s->rowCount()) responderError('Administrador no encontrado.', 404);
        responder(['success' => true, 'mensaje' => 'Administrador eliminado.']);
    }
    $d = leerJson();
    requerirCampos($d, ['id_usuario', 'nombre', 'apellido']);
    if (!existe($conexion, 'usuarios', 'id_usuario', (int)$d['id_usuario'])) responderError('El usuario no existe.');
    $p = [':usuario' => (int)$d['id_usuario'], ':nombre' => trim($d['nombre']), ':apellido' => trim($d['apellido']), ':telefono' => $d['telefono'] ?? null];
    if ($metodo === 'POST') {
        $s = $conexion->prepare('INSERT INTO administradores (id_usuario,nombre,apellido,telefono) VALUES (:usuario,:nombre,:apellido,:telefono)');
        $s->execute($p);
        responder(['success' => true, 'id_administrador' => (int)$conexion->lastInsertId()], 201);
    }
    if ($metodo === 'PUT') {
        $p[':id'] = idRequerido();
        $s = $conexion->prepare('UPDATE administradores SET id_usuario=:usuario,nombre=:nombre,apellido=:apellido,telefono=:telefono WHERE id_administrador=:id');
        $s->execute($p);
        responder(['success' => true, 'mensaje' => 'Administrador actualizado.']);
    }
    responderError('Metodo no permitido.', 405);
});
