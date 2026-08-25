<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$claveSecreta = "CLINICA_GRUPO1_JWT_SECRET_2026_MUY_SEGURA_8fK29xP7";

function generarToken($usuario)
{
    global $claveSecreta;

    $tiempoActual = time();

    $payload = [
        "iat" => $tiempoActual,
        "exp" => $tiempoActual + (60 * 60),
        "id_usuario" => $usuario["id_usuario"],
        "nombre_usuario" => $usuario["nombre_usuario"],
        "id_rol" => $usuario["id_rol"]
    ];

    return JWT::encode(
        $payload,
        $claveSecreta,
        "HS256"
    );
}

function validarToken()
{
    global $claveSecreta;

    $headers = getallheaders();

    if (!isset($headers["Authorization"])) {

        http_response_code(401);

        echo json_encode([
            "success" => false,
            "mensaje" => "Token no proporcionado"
        ]);

        exit;
    }

    $authorization = $headers["Authorization"];

    if (!preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {

        http_response_code(401);

        echo json_encode([
            "success" => false,
            "mensaje" => "Formato de token inválido"
        ]);

        exit;
    }

    $token = $matches[1];

    try {

        $datos = JWT::decode(
            $token,
            new Key($claveSecreta, "HS256")
        );

        return $datos;

    } catch (Exception $e) {

        http_response_code(401);

        echo json_encode([
            "success" => false,
            "mensaje" => "Token inválido o expirado"
        ]);

        exit;
    }
}

/* VERIFICAR ROL */
function verificarRol($rolesPermitidos)
{
    $usuario = validarToken();

    if (!in_array($usuario->id_rol, $rolesPermitidos)) {

        http_response_code(403);

        echo json_encode([
            "success" => false,
            "mensaje" => "No tiene permisos para realizar esta acción"
        ]);

        exit;
    }

    return $usuario;
}
