<?php


header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "config/conexion.php";
require_once "config/jwt.php";

$metodo = $_SERVER['REQUEST_METHOD'];

try {

    switch ($metodo) {

        case 'OPTIONS':

            http_response_code(200);

            echo json_encode([
                "success" => true,
                "mensaje" => "Preflight OK"
            ]);

            exit;


        case 'POST':

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            /* verificar que lleguen los datos */

            if (
                !isset($datos['nombre_usuario']) ||
                !isset($datos['contrasenia'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Usuario y contraseña son obligatorios"
                ]);

                exit;
            }

            /* buscar usuario */
            $sql = "SELECT
                        id_usuario,
                        nombre_usuario,
                        contrasenia,
                        id_rol
                    FROM tb_usuarios
                    WHERE nombre_usuario = :nombre_usuario";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_usuario',
                $datos['nombre_usuario']
            );

            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            /* usuario no encontrado */

            if (!$usuario) {

                http_response_code(401);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Usuario o contraseña incorrectos"
                ]);

                exit;
            }

            /* COMPROBAR CONTRASEÑA  */

            if ($datos['contrasenia'] !== $usuario['contrasenia']) {

                http_response_code(401);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Usuario o contraseña incorrectos"
                ]);

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
                    "nombre_usuario" => $usuario["nombre_usuario"],
                    "id_rol" => $usuario["id_rol"]
                ]
            ]);

            break;

/* Método no permitido */
        default:

            http_response_code(405);

            echo json_encode([
                "success" => false,
                "mensaje" => "Método no permitido"
            ]);

            break;
    }

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => "Error en la API",
        "error" => $e->getMessage()
    ]);
}

$conexion = null;


?> 