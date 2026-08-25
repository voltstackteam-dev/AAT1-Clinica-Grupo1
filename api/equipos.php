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

            // Buscar un equipo por ID

            if (isset($_GET['id'])) {

                $id = $_GET['id'];

                $sql = "SELECT
                            e.id_equipo,
                            e.nombre,
                            e.id_especialidad,
                            es.nombre_especialidad,
                            e.cantidad
                        FROM tb_equipos e

                        INNER JOIN tb_especialidades es
                            ON e.id_especialidad = es.id_especialidad

                        WHERE e.id_equipo = :id";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id',
                    $id,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $equipo = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($equipo) {

                    echo json_encode([
                        "success" => true,
                        "data" => $equipo
                    ]);

                } else {

                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "Equipo no encontrado"
                    ]);
                }

            } else {

                // Listar todos los equipos

                $sql = "SELECT
                            e.id_equipo,
                            e.nombre,
                            e.id_especialidad,
                            es.nombre_especialidad,
                            e.cantidad
                        FROM tb_equipos e

                        INNER JOIN tb_especialidades es
                            ON e.id_especialidad = es.id_especialidad

                        ORDER BY e.id_equipo DESC";

                $stmt = $conexion->prepare($sql);

                $stmt->execute();

                $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($equipos),
                    "data" => $equipos
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
                !isset($datos['nombre']) ||
                !isset($datos['id_especialidad']) ||
                !isset($datos['cantidad'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre, especialidad y cantidad son obligatorios"
                ]);

                exit;
            }

            if ($datos['cantidad'] < 0) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "La cantidad no puede ser negativa"
                ]);

                exit;
            }


            /*Verificar especialidad*/

            $sql = "SELECT
                        id_especialidad
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
                ]);

                exit;
            }


            /*  Insertar equipo */

            $sql = "INSERT INTO tb_equipos
                    (
                        nombre,
                        id_especialidad,
                        cantidad
                    )
                    VALUES
                    (
                        :nombre,
                        :id_especialidad,
                        :cantidad
                    )";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre',
                $datos['nombre']
            );

            $stmt->bindValue(
                ':id_especialidad',
                $datos['id_especialidad'],
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':cantidad',
                $datos['cantidad'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            $id = $conexion->lastInsertId();

            http_response_code(201);

            echo json_encode([
                "success" => true,
                "mensaje" => "Equipo creado correctamente",
                "id_equipo" => $id
            ]);

            break;


      
        /*  PUT  */

        case 'PUT':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID del equipo"
                ]);

                exit;
            }

            $id = $_GET['id'];

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (
                !isset($datos['nombre']) ||
                !isset($datos['id_especialidad']) ||
                !isset($datos['cantidad'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre, especialidad y cantidad son obligatorios"
                ]);

                exit;
            }

            if ($datos['cantidad'] < 0) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "La cantidad no puede ser negativa"
                ]);

                exit;
            }


            $sql = "UPDATE tb_equipos
                    SET
                        nombre = :nombre,
                        id_especialidad = :id_especialidad,
                        cantidad = :cantidad
                    WHERE id_equipo = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre',
                $datos['nombre']
            );

            $stmt->bindValue(
                ':id_especialidad',
                $datos['id_especialidad'],
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':cantidad',
                $datos['cantidad'],
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
                    "mensaje" => "Equipo actualizado correctamente"
                ]);

            } else {

                echo json_encode([
                    "success" => false,
                    "mensaje" => "No se encontró el equipo o no hubo cambios"
                ]);
            }

            break;


         /* DELETE */

        case 'DELETE':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID del equipo"
                ]);

                exit;
            }

            $id = $_GET['id'];

            $sql = "DELETE FROM tb_equipos
                    WHERE id_equipo = :id";

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
                    "mensaje" => "Equipo eliminado correctamente"
                ]);

            } else {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Equipo no encontrado"
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