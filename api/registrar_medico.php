<?php

require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/jwt.php';

$metodo = iniciarApi();

ejecutarApi(function () use ($conexion, $metodo): void {
    verificarRol([1]);

    if ($metodo !== 'POST') {
        responderError('Método no permitido.', 405);
    }

    $datos = leerJson();
    $limites = [
        'nombre' => 100,
        'apellido' => 100,
        'telefono' => 20,
        'email' => 100
    ];

    foreach ($limites as $campo => $limite) {
        if (!isset($datos[$campo]) || !is_string($datos[$campo])) {
            responderError("El campo {$campo} es obligatorio.");
        }

        $datos[$campo] = trim($datos[$campo]);

        if ($datos[$campo] === '' || mb_strlen($datos[$campo]) > $limite) {
            responderError("El campo {$campo} debe contener entre 1 y {$limite} caracteres.");
        }
    }

    if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        responderError('Introduce un correo electrónico válido.');
    }

    $contrasenia = $datos['contrasenia'] ?? null;

    if (
        !is_string($contrasenia) ||
        strlen($contrasenia) < 8 ||
        strlen($contrasenia) > 72 ||
        trim($contrasenia) === '' ||
        str_contains($contrasenia, "\0")
    ) {
        responderError('La contraseña debe tener entre 8 y 72 bytes y no estar vacía.');
    }

    $especialidad = filter_var($datos['id_especialidad'] ?? null, FILTER_VALIDATE_INT);

    if (!$especialidad || !existe($conexion, 'especialidades', 'id_especialidad', $especialidad)) {
        responderError('Selecciona una especialidad válida.');
    }

    $colegiado = $datos['colegiado_num'] ?? '';

    if (!is_string($colegiado) || mb_strlen(trim($colegiado)) > 45) {
        responderError('El número de colegiado admite hasta 45 caracteres.');
    }

    $colegiado = trim($colegiado);
    $comprobar = $conexion->prepare(
        'SELECT id_usuario
        FROM usuarios
        WHERE email = :email'
    );
    $comprobar->execute([':email' => $datos['email']]);

    if ($comprobar->fetchColumn()) {
        responderError('Ese correo ya tiene una cuenta. Utiliza un correo diferente.', 409);
    }

    $hash = password_hash($contrasenia, PASSWORD_DEFAULT);
    $conexion->beginTransaction();

    try {
        $usuario = $conexion->prepare(
            'INSERT INTO usuarios (
                email,
                contrasenia,
                id_rol,
                activo
            ) VALUES (
                :email,
                :contrasenia,
                2,
                1
            )'
        );
        $usuario->execute([
            ':email' => $datos['email'],
            ':contrasenia' => $hash
        ]);
        $idUsuario = (int) $conexion->lastInsertId();

        $medico = $conexion->prepare(
            'INSERT INTO medicos (
                id_usuario,
                id_especialidad,
                nombre,
                apellido,
                colegiado_num,
                telefono
            ) VALUES (
                :usuario,
                :especialidad,
                :nombre,
                :apellido,
                :colegiado,
                :telefono
            )'
        );
        $medico->execute([
            ':usuario' => $idUsuario,
            ':especialidad' => $especialidad,
            ':nombre' => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':colegiado' => $colegiado === '' ? null : $colegiado,
            ':telefono' => $datos['telefono']
        ]);
        $idMedico = (int) $conexion->lastInsertId();
        $conexion->commit();
    } catch (Throwable $error) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }

        throw $error;
    }

    responder([
        'success' => true,
        'mensaje' => 'Médico y cuenta de acceso registrados correctamente.',
        'id_medico' => $idMedico,
        'id_usuario' => $idUsuario
    ], 201);
});
