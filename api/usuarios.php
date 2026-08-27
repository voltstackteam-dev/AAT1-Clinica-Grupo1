<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "config/conexion.php";

$metodo = $_SERVER['REQUEST_METHOD'];

try {

    switch ($metodo) {

     
      /* OPTIONS  */

        case 'OPTIONS':

            http_response_code(200);

            echo json_encode([
                "success" => true,
                "mensaje" => "Preflight OK"
            ]);

            exit;


        /* GET */
        case 'GET':

            // Buscar usuario por ID
            if (isset($_GET['id'])) {

                $id = $_GET['id'];

                $sql = "SELECT
                            u.id_usuario,
                            u.nombre_usuario,
                            u.id_rol,
                            r.nombre_rol
                        FROM tb_usuarios u
                        INNER JOIN tb_roles r
                            ON u.id_rol = r.id_rol
                        WHERE u.id_usuario = :id";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id',
                    $id,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($usuario) {

                    echo json_encode([
                        "success" => true,
                        "data" => $usuario
                    ]);

                } else {

                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "Usuario no encontrado"
                    ]);
                }

            } else {

                // Listar todos los usuarios

                $sql = "SELECT
                            u.id_usuario,
                            u.nombre_usuario,
                            u.id_rol,
                            r.nombre_rol
                        FROM tb_usuarios u
                        INNER JOIN tb_roles r
                            ON u.id_rol = r.id_rol
                        ORDER BY u.id_usuario DESC";

                $stmt = $conexion->prepare($sql);

                $stmt->execute();

                $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($usuarios),
                    "data" => $usuarios
                ]);
            }

            break;


   /*   POST  */

        case 'POST':

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (
                !isset($datos['nombre_usuario']) ||
                !isset($datos['contrasenia']) ||
                !isset($datos['id_rol'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre de usuario, contraseña e ID de rol son obligatorios"
                ]);

                exit;
            }

            // Verificar que el nombre de usuario no exista

            $sqlVerificar = "SELECT id_usuario
                             FROM tb_usuarios
                             WHERE nombre_usuario = :nombre_usuario";

            $stmtVerificar = $conexion->prepare($sqlVerificar);

            $stmtVerificar->bindValue(
                ':nombre_usuario',
                $datos['nombre_usuario']
            );

            $stmtVerificar->execute();

            if ($stmtVerificar->fetch()) {

                http_response_code(409);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "El nombre de usuario ya existe"
                ]);

                exit;
            }


            $sql = "INSERT INTO tb_usuarios
                    (
                        nombre_usuario,
                        contrasenia,
                        id_rol
                    )
                    VALUES
                    (
                        :nombre_usuario,
                        :contrasenia,
                        :id_rol
                    )";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_usuario',
                $datos['nombre_usuario']
            );

            /*
             * Por ahora utilizamos la contraseña recibida.
             * Más adelante, cuando hagamos LOGIN/JWT,
             * podemos mejorar esto utilizando password_hash().
             */

            $stmt->bindValue(
                ':contrasenia',
                $datos['contrasenia']
            );

            $stmt->bindValue(
                ':id_rol',
                $datos['id_rol'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            $id = $conexion->lastInsertId();

            http_response_code(201);

            echo json_encode([
                "success" => true,
                "mensaje" => "Usuario creado correctamente",
                "id_usuario" => $id
            ]);

            break;


     
        /*  PUT  */

        case 'PUT':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID del usuario"
                ]);

                exit;
            }

            $id = $_GET['id'];

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (
                !isset($datos['nombre_usuario']) ||
                !isset($datos['contrasenia']) ||
                !isset($datos['id_rol'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre de usuario, contraseña e ID de rol son obligatorios"
                ]);

                exit;
            }


            $sql = "UPDATE tb_usuarios
                    SET
                        nombre_usuario = :nombre_usuario,
                        contrasenia = :contrasenia,
                        id_rol = :id_rol
                    WHERE id_usuario = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_usuario',
                $datos['nombre_usuario']
            );

            $stmt->bindValue(
                ':contrasenia',
                $datos['contrasenia']
            );

            $stmt->bindValue(
                ':id_rol',
                $datos['id_rol'],
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':id',
                $id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                echo json_encode([
                    "success" => true,
                    "mensaje" => "Usuario actualizado correctamente"
                ]);

            } else {

                echo json_encode([
                    "success" => false,
                    "mensaje" => "No se encontró el usuario o no hubo cambios"
                ]);
            }

            break;


        /* DELETE */

        case 'DELETE':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID del usuario"
                ]);

                exit;
            }

            $id = $_GET['id'];

            $sql = "DELETE FROM tb_usuarios
                    WHERE id_usuario = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id',
                $id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                echo json_encode([
                    "success" => true,
                    "mensaje" => "Usuario eliminado correctamente"
                ]);

            } else {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Usuario no encontrado"
                ]);
            }

            break;

 /*  MÉTODO NO PERMITIDO  */

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