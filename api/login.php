<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'OPTIONS') {
            http_response_code(200);

            echo json_encode([
                "success" => true,
                "mensaje" => "Preflight OK"
            ]);

            exit;
            }


require_once "config/conexion.php";
require_once "config/jwt.php";

try {

    switch ($metodo) {

        case 'POST':

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            /* verificar que lleguen los datos */

            if (
                !isset($datos['email']) ||
                !isset($datos['contrasenia'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Usuario y contraseña son obligatorios"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            /* buscar usuario */
            $sql = "SELECT
                        id_usuario,
                        email,
                        contrasenia,
                        id_rol,
                        activo
                    FROM usuarios
                    WHERE email = :email";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':email',
                $datos['email']
            );

            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            /* usuario no encontrado */

            if (!$usuario) {

                http_response_code(401);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Usuario o contraseña incorrectos"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            /* Validar si el usuario está activo */
            if (isset($usuario['activo']) && $usuario['activo'] == 0) {
                http_response_code(403);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "La cuenta está desactivada"
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            /* COMPROBAR CONTRASEÑA  */

            if (!password_verify($datos['contrasenia'], $usuario['contrasenia']) && !hash_equals($usuario['contrasenia'], $datos['contrasenia'])) {

                http_response_code(401);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Usuario o contraseña incorrectos"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            /*             GENERAR JWT *///

            $token = generarToken($usuario);
//respuesta
            echo json_encode([
                "success" => true,
                "mensaje" => "Inicio de sesión correcto",
                "token" => $token,
                "usuario" => [
                    "id_usuario" => $usuario["id_usuario"],
                    "email" => $usuario["email"],
                    "id_rol" => $usuario["id_rol"]
                ]
            ], JSON_UNESCAPED_UNICODE);

            break;

/* Método no permitido */
        default:

            http_response_code(405);

            echo json_encode([
                "success" => false,
                "mensaje" => "Método no permitido"
            ], JSON_UNESCAPED_UNICODE);

            break;
    }

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => "Error en la API",
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conexion = null;


?> 
