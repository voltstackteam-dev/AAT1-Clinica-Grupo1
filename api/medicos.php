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

            // Buscar médico por ID

            if (isset($_GET['id'])) {

                $id = $_GET['id'];

                $sql = "SELECT
                            m.id_medico,
                            m.nombre_med,
                            m.apellido_med,
                            m.id_especialidad,
                            e.nombre_especialidad,
                            m.telefono_med,
                            m.equipo_disponible,
                            m.id_usuario,
                            u.nombre_usuario
                        FROM tb_medicos m

                        INNER JOIN tb_especialidades e
                            ON m.id_especialidad = e.id_especialidad

                        INNER JOIN tb_usuarios u
                            ON m.id_usuario = u.id_usuario

                        WHERE m.id_medico = :id";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id',
                    $id,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $medico = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($medico) {

                    echo json_encode([
                        "success" => true,
                        "data" => $medico
                    ]);

                } else {

                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "Médico no encontrado"
                    ]);
                }

            } else {

                // Listar todos los médicos

                $sql = "SELECT
                            m.id_medico,
                            m.nombre_med,
                            m.apellido_med,
                            m.id_especialidad,
                            e.nombre_especialidad,
                            m.telefono_med,
                            m.equipo_disponible,
                            m.id_usuario,
                            u.nombre_usuario

                        FROM tb_medicos m

                        INNER JOIN tb_especialidades e
                            ON m.id_especialidad = e.id_especialidad

                        INNER JOIN tb_usuarios u
                            ON m.id_usuario = u.id_usuario

                        ORDER BY m.id_medico DESC";

                $stmt = $conexion->prepare($sql);

                $stmt->execute();

                $medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($medicos),
                    "data" => $medicos
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
                !isset($datos['nombre_med']) ||
                !isset($datos['apellido_med']) ||
                !isset($datos['id_especialidad']) ||
                !isset($datos['id_usuario'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre, apellido, especialidad e usuario son obligatorios"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            /*Verificar que la especialidad exista*/

            $sql = "SELECT id_especialidad
                    FROM tb_especialidades
                    WHERE id_especialidad = :id_especialidad";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id_especialidad',
                $datos['id_especialidad'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            if (!$stmt->fetch()) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "La especialidad indicada no existe"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Verificar que el usuario exista */

            $sql = "SELECT id_usuario
                    FROM tb_usuarios
                    WHERE id_usuario = :id_usuario";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id_usuario',
                $datos['id_usuario'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            if (!$stmt->fetch()) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "El usuario indicado no existe"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Insertar médico */

            $sql = "INSERT INTO tb_medicos
                    (
                        nombre_med,
                        apellido_med,
                        id_especialidad,
                        telefono_med,
                        equipo_disponible,
                        id_usuario
                    )
                    VALUES
                    (
                        :nombre_med,
                        :apellido_med,
                        :id_especialidad,
                        :telefono_med,
                        :equipo_disponible,
                        :id_usuario
                    )";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_med',
                $datos['nombre_med']
            );

            $stmt->bindValue(
                ':apellido_med',
                $datos['apellido_med']
            );

            $stmt->bindValue(
                ':id_especialidad',
                $datos['id_especialidad'],
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':telefono_med',
                $datos['telefono_med'] ?? null
            );

            $stmt->bindValue(
                ':equipo_disponible',
                $datos['equipo_disponible'] ?? null
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
                "mensaje" => "Médico creado correctamente",
                "id_medico" => $id
            ], JSON_UNESCAPED_UNICODE);

            break;


      
        /*  PUT  */

        case 'PUT':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID del médico"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $id = $_GET['id'];

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (
                !isset($datos['nombre_med']) ||
                !isset($datos['apellido_med']) ||
                !isset($datos['id_especialidad']) ||
                !isset($datos['id_usuario'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre, apellido, especialidad e usuario son obligatorios"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $sql = "UPDATE tb_medicos
                    SET
                        nombre_med = :nombre_med,
                        apellido_med = :apellido_med,
                        id_especialidad = :id_especialidad,
                        telefono_med = :telefono_med,
                        equipo_disponible = :equipo_disponible,
                        id_usuario = :id_usuario

                    WHERE id_medico = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_med',
                $datos['nombre_med']
            );

            $stmt->bindValue(
                ':apellido_med',
                $datos['apellido_med']
            );

            $stmt->bindValue(
                ':id_especialidad',
                $datos['id_especialidad'],
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':telefono_med',
                $datos['telefono_med'] ?? null
            );

            $stmt->bindValue(
                ':equipo_disponible',
                $datos['equipo_disponible'] ?? null
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
                    "mensaje" => "Médico actualizado correctamente"
                ], JSON_UNESCAPED_UNICODE);

            } else {

                echo json_encode([
                    "success" => false,
                    "mensaje" => "No se encontró el médico o no hubo cambios"
                ], JSON_UNESCAPED_UNICODE);
            }

            break;


          /* DELETE */

        case 'DELETE':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID del médico"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $id = $_GET['id'];

            $sql = "DELETE FROM tb_medicos
                    WHERE id_medico = :id";

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
                    "mensaje" => "Médico eliminado correctamente"
                ], JSON_UNESCAPED_UNICODE);

            } else {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Médico no encontrado"
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