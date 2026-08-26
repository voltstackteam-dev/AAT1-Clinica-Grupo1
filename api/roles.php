<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

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

            if (isset($_GET['id'])) {

                $id = $_GET['id'];

                $sql = "SELECT
                            id_rol,
                            nombre_rol
                        FROM tb_roles
                        WHERE id_rol = :id";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id',
                    $id,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $rol = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($rol) {

                    echo json_encode([
                        "success" => true,
                        "data" => $rol
                    ]);

                } else {

                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "Rol no encontrado"
                    ]);
                }

            } else {

                $sql = "SELECT
                            id_rol,
                            nombre_rol
                        FROM tb_roles
                        ORDER BY id_rol DESC";

                $stmt = $conexion->prepare($sql);

                $stmt->execute();

                $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($roles),
                    "data" => $roles
                ]);
            }

            break;


          /*   POST  */

        case 'POST':

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (!isset($datos['nombre_rol'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "El nombre del rol es obligatorio"
                ]);

                exit;
            }

            $sql = "INSERT INTO tb_roles
                    (
                        nombre_rol
                    )
                    VALUES
                    (
                        :nombre_rol
                    )";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_rol',
                $datos['nombre_rol']
            );

            $stmt->execute();

            $id = $conexion->lastInsertId();

            http_response_code(201);

            echo json_encode([
                "success" => true,
                "mensaje" => "Rol creado correctamente",
                "id_rol" => $id
            ]);

            break;


        
        /*  PUT  */

        case 'PUT':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID del rol"
                ]);

                exit;
            }

            $id = $_GET['id'];

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (!isset($datos['nombre_rol'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "El nombre del rol es obligatorio"
                ]);

                exit;
            }

            $sql = "UPDATE tb_roles
                    SET
                        nombre_rol = :nombre_rol
                    WHERE id_rol = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_rol',
                $datos['nombre_rol']
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
                    "mensaje" => "Rol actualizado correctamente"
                ]);

            } else {

                echo json_encode([
                    "success" => false,
                    "mensaje" => "No se encontró el rol o no hubo cambios"
                ]);
            }

            break;

 /*  MÉTODO NO PERMITIDO  */

        case 'DELETE':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID del rol"
                ]);

                exit;
            }

            $id = $_GET['id'];

            $sql = "DELETE FROM tb_roles
                    WHERE id_rol = :id";

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
                    "mensaje" => "Rol eliminado correctamente"
                ]);

            } else {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Rol no encontrado"
                ]);
            }

            break;


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