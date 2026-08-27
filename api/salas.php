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

            //Buscar sala por ID

            if (isset($_GET['id'])) {

                $id = intval($_GET['id']);

                $sql = "SELECT
                            s.id_sala,
                            s.nombre_sala,
                            s.id_especialidad,
                            e.nombre_especialidad
                        FROM tb_salas s

                        INNER JOIN tb_especialidades e
                            ON s.id_especialidad = e.id_especialidad

                        WHERE s.id_sala = :id";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id',
                    $id,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $sala = $stmt->fetch();

                if ($sala) {

                    echo json_encode([
                        "success" => true,
                        "data" => $sala
                    ], JSON_UNESCAPED_UNICODE);

                } else {

                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "Sala no encontrada"
                    ], JSON_UNESCAPED_UNICODE);
                }

            } else {

            //Listar todas las salas
                $sql = "SELECT
                            s.id_sala,
                            s.nombre_sala,
                            s.id_especialidad,
                            e.nombre_especialidad

                        FROM tb_salas s

                        INNER JOIN tb_especialidades e
                            ON s.id_especialidad = e.id_especialidad

                        ORDER BY s.id_sala DESC";

                $stmt = $conexion->prepare($sql);

                $stmt->execute();

                $salas = $stmt->fetchAll();

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($salas),
                    "data" => $salas
                ], JSON_UNESCAPED_UNICODE);
            }

            break;


         /*   POST  */

        case 'POST':

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (!$datos){
                
                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre de sala y especialidad son obligatorios"
                ]);

                exit;
            }

             if (
                !isset($datos['nombre_sala']) ||
                !isset($datos['id_especialidad'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre de sala y especialidad son obligatorios"
                ]);

                exit;
            }

            //Verificar especialidad
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


            /* Insertar sala */

            $sql = "INSERT INTO tb_salas
                    (
                        nombre_sala,
                        id_especialidad
                    )
                    VALUES
                    (
                        :nombre_sala,
                        :id_especialidad
                    )";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_sala',
                $datos['nombre_sala']
            );

            $stmt->bindValue(
                ':id_especialidad',
                $datos['id_especialidad'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            $id = $conexion->lastInsertId();

            http_response_code(201);

            echo json_encode([
                "success" => true,
                "mensaje" => "Sala creada correctamente",
                "id_sala" => $id
            ], JSON_UNESCAPED_UNICODE);

            break;

       
        /*  PUT  */

        case 'PUT':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID de la sala"
                ]);

                exit;
            }

            $id = intval($_GET['id']);

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (
                !isset($datos['nombre_sala']) ||
                !isset($datos['id_especialidad'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre de sala y especialidad son obligatorios"
                ]);

                exit;
            }


            //Verificar especialidad 
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

            //Actualizar sala
            $sql = "UPDATE tb_salas
                    SET
                        nombre_sala = :nombre_sala,
                        id_especialidad = :id_especialidad
                    WHERE id_sala = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_sala',
                $datos['nombre_sala']
            );

            $stmt->bindValue(
                ':id_especialidad',
                $datos['id_especialidad'],
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
                    "mensaje" => "Sala actualizada correctamente"
                ], JSON_UNESCAPED_UNICODE);

            } else {

                echo json_encode([
                    "success" => false,
                    "mensaje" => "No se encontró la sala o no hubo cambios"
                ], JSON_UNESCAPED_UNICODE);
            }

            break;


         /* DELETE */
        case 'DELETE':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID de la sala"
                ]);

                exit;
            }

            $id = intval($_GET['id']);

            $sql = "DELETE FROM tb_salas
                    WHERE id_sala = :id";

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
                    "mensaje" => "Sala eliminada correctamente"
                ], JSON_UNESCAPED_UNICODE);

            } else {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Sala no encontrada"
                ], JSON_UNESCAPED_UNICODE);
            }

            break;

            //Método no permitido

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