<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "config/conexion.php";

$metodo = $_SERVER['REQUEST_METHOD'];

try {

    switch ($metodo) {

        /*  OPTIONS  */

        case 'OPTIONS':

            http_response_code(200);

            echo json_encode([
                "success" => true,
                "mensaje" => "Preflight OK"
            ], JSON_UNESCAPED_UNICODE);

            exit;


        /* GET */

        case 'GET':

            if (isset($_GET['id'])) {

                $id = $_GET['id'];

                $sql = "SELECT
                            a.id_administrador,
                            a.nombre_adm,
                            a.apellido_adm,
                            a.id_usuario,
                            u.nombre_usuario
                        FROM tb_administradores a
                        INNER JOIN tb_usuarios u
                            ON a.id_usuario = u.id_usuario
                        WHERE a.id_administrador = :id";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id',
                    $id,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $administrador = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($administrador) {

                    echo json_encode([
                        "success" => true,
                        "data" => $administrador
                    ], JSON_UNESCAPED_UNICODE);

                } else {

                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "Administrador no encontrado"
                    ], JSON_UNESCAPED_UNICODE);
                }

            } else {

                $sql = "SELECT
                            a.id_administrador,
                            a.nombre_adm,
                            a.apellido_adm,
                            a.id_usuario,
                            u.nombre_usuario
                        FROM tb_administradores a
                        INNER JOIN tb_usuarios u
                            ON a.id_usuario = u.id_usuario
                        ORDER BY a.id_administrador DESC";

                $stmt = $conexion->prepare($sql);

                $stmt->execute();

                $administradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($administradores),
                    "data" => $administradores
                ], JSON_UNESCAPED_UNICODE);
            }

            break;


        /*   POST  */

        case 'POST':

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (
                !isset($datos['nombre_adm']) ||
                !isset($datos['apellido_adm']) ||
                !isset($datos['id_usuario'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre, apellido e ID de usuario son obligatorios"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Verificar que el usuario exista */
            
            $sqlUsuario = "SELECT
                                id_usuario
                           FROM tb_usuarios
                           WHERE id_usuario = :id_usuario";

            $stmtUsuario = $conexion->prepare($sqlUsuario);

            $stmtUsuario->bindValue(
                ':id_usuario',
                $datos['id_usuario'],
                PDO::PARAM_INT
            );

            $stmtUsuario->execute();

            if (!$stmtUsuario->fetch()) {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "El usuario indicado no existe"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Verificar si ya tiene administrador */
            $sqlExiste = "SELECT
                            id_administrador
                          FROM tb_administradores
                          WHERE id_usuario = :id_usuario";

            $stmtExiste = $conexion->prepare($sqlExiste);

            $stmtExiste->bindValue(
                ':id_usuario',
                $datos['id_usuario'],
                PDO::PARAM_INT
            );

            $stmtExiste->execute();

            if ($stmtExiste->fetch()) {

                http_response_code(409);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Este usuario ya está registrado como administrador"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            $sql = "INSERT INTO tb_administradores
                    (
                        nombre_adm,
                        apellido_adm,
                        id_usuario
                    )
                    VALUES
                    (
                        :nombre_adm,
                        :apellido_adm,
                        :id_usuario
                    )";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_adm',
                $datos['nombre_adm']
            );

            $stmt->bindValue(
                ':apellido_adm',
                $datos['apellido_adm']
            );

            $stmt->bindValue(
                ':id_usuario',
                $datos['id_usuario'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            $id = $conexion->lastInsertId();

            http_response_code(201);

            echo json_encode([
                "success" => true,
                "mensaje" => "Administrador creado correctamente",
                "id_administrador" => $id
            ], JSON_UNESCAPED_UNICODE);

            break;


        /*  PUT  */

        case 'PUT':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID del administrador"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $id = $_GET['id'];

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (
                !isset($datos['nombre_adm']) ||
                !isset($datos['apellido_adm']) ||
                !isset($datos['id_usuario'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre, apellido e ID de usuario son obligatorios"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            $sql = "UPDATE tb_administradores
                    SET
                        nombre_adm = :nombre_adm,
                        apellido_adm = :apellido_adm,
                        id_usuario = :id_usuario
                    WHERE id_administrador = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_adm',
                $datos['nombre_adm']
            );

            $stmt->bindValue(
                ':apellido_adm',
                $datos['apellido_adm']
            );

            $stmt->bindValue(
                ':id_usuario',
                $datos['id_usuario'],
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
                    "mensaje" => "Administrador actualizado correctamente"
                ], JSON_UNESCAPED_UNICODE);

            } else {

                echo json_encode([
                    "success" => false,
                    "mensaje" => "No se encontró el administrador o no hubo cambios"
                ], JSON_UNESCAPED_UNICODE);
            }

            break;


        /*  DELETE  */

        case 'DELETE':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID del administrador"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $id = $_GET['id'];

            $sql = "DELETE FROM tb_administradores
                    WHERE id_administrador = :id";

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
                    "mensaje" => "Administrador eliminado correctamente"
                ], JSON_UNESCAPED_UNICODE);

            } else {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Administrador no encontrado"
                ], JSON_UNESCAPED_UNICODE);
            }

            break;


        /*  MÉTODO NO PERMITIDO  */

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