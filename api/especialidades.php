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
            ], JSON_UNESCAPED_UNICODE);

            exit;


                /* GET */
        case 'GET':

            if (isset($_GET['id'])) {

                $id = $_GET['id'];

                $sql = "SELECT
                            id_especialidad,
                            nombre_especialidad
                        FROM tb_especialidades
                        WHERE id_especialidad = :id";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id',
                    $id,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $especialidad = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($especialidad) {

                    echo json_encode([
                        "success" => true,
                        "data" => $especialidad
                    ], JSON_UNESCAPED_UNICODE);

                } else {

                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "Especialidad no encontrada"
                    ]);
                }

            } else {

                $sql = "SELECT
                            id_especialidad,
                            nombre_especialidad
                        FROM tb_especialidades
                        ORDER BY id_especialidad DESC";

                $stmt = $conexion->prepare($sql);

                $stmt->execute();

                $especialidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($especialidades),
                    "data" => $especialidades
                ], JSON_UNESCAPED_UNICODE);
            }

            break;


              /*   POST  */

        case 'POST':

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (!isset($datos['nombre_especialidad'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "El nombre de la especialidad es obligatorio"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $sql = "INSERT INTO tb_especialidades
                    (
                        nombre_especialidad
                    )
                    VALUES
                    (
                        :nombre_especialidad
                    )";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_especialidad',
                $datos['nombre_especialidad']
            );

            $stmt->execute();

            $id = $conexion->lastInsertId();

            http_response_code(201);

            echo json_encode([
                "success" => true,
                "mensaje" => "Especialidad creada correctamente",
                "id_especialidad" => $id
            ], JSON_UNESCAPED_UNICODE);

            break;


        
        /*  PUT  */

        case 'PUT':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID de la especialidad"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $id = $_GET['id'];

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (!isset($datos['nombre_especialidad'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "El nombre de la especialidad es obligatorio"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $sql = "UPDATE tb_especialidades
                    SET
                        nombre_especialidad = :nombre_especialidad
                    WHERE id_especialidad = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_especialidad',
                $datos['nombre_especialidad']
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
                    "mensaje" => "Especialidad actualizada correctamente"
                ], JSON_UNESCAPED_UNICODE);

            } else {

                echo json_encode([
                    "success" => false,
                    "mensaje" => "No se encontró la especialidad o no hubo cambios"
                ], JSON_UNESCAPED_UNICODE);
            }

            break;

  /* DELETE */

        case 'DELETE':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID de la especialidad"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $id = $_GET['id'];

            $sql = "DELETE FROM tb_especialidades
                    WHERE id_especialidad = :id";

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
                    "mensaje" => "Especialidad eliminada correctamente"
                ], JSON_UNESCAPED_UNICODE);

            } else {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Especialidad no encontrada"
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