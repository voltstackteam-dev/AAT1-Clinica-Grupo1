<?php

require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/jwt.php';

$metodo = iniciarApi();

ejecutarApi(function () use ($conexion, $metodo): void {

    $usuario = validarToken();
    $idUsuario = (int) $usuario->id_usuario;

    if ($metodo === 'GET') {
        $s = $conexion->prepare(
            'SELECT id_usuario, email, id_rol, activo, created_at
             FROM usuarios
             WHERE id_usuario = :id'
        );

        $s->execute([':id' => $idUsuario]);
        $data = $s->fetch();

        if (!$data) {
            responderError('Usuario no encontrado.', 404);
        }

        responder([
            'success' => true,
            'data' => $data
        ]);
    }

    if ($metodo === 'PUT') {
        $d = leerJson();

        requerirCampos($d, ['email']);

        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            responderError('Email invalido.');
        }

        $p = [
            ':email' => trim($d['email']),
            ':id' => $idUsuario
        ];

        $set = 'email = :email';

        if (!empty($d['contrasenia'])) {
            $set .= ', contrasenia = :clave';
            $p[':clave'] = password_hash($d['contrasenia'], PASSWORD_DEFAULT);
        }

        $s = $conexion->prepare(
            "UPDATE usuarios SET {$set} WHERE id_usuario = :id"
        );

        $s->execute($p);

        responder([
            'success' => true,
            'mensaje' => 'Perfil actualizado correctamente.'
        ]);
    }

    responderError('Metodo no permitido.', 405);
});