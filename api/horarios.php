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

            //Buscar un horario por ID
            

            if (isset($_GET['id'])) {

                $id = $_GET['id'];

                $sql = "SELECT
                            h.id_horario,
                            h.fecha,
                            h.hora_reserva,
                            h.disponibilidad,
                            h.id_medico,
                            m.nombre_med,
                            m.apellido_med,
                            e.id_especialidad,
                            e.nombre_especialidad

                        FROM tb_horarios h

                        INNER JOIN tb_medicos m
                            ON h.id_medico = m.id_medico

                        INNER JOIN tb_especialidades e
                            ON m.id_especialidad = e.id_especialidad

                        WHERE h.id_horario = :id";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id',
                    $id,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $horario = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($horario) {

                    echo json_encode([
                        "success" => true,
                        "data" => $horario
                    ], JSON_UNESCAPED_UNICODE);

                } else {

                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "Horario no encontrado"
                    ], JSON_UNESCAPED_UNICODE);
                }

            }

            /* Buscar horarios por médico */

            elseif (isset($_GET['id_medico'])) {

                $id_medico = $_GET['id_medico'];

                $sql = "SELECT
                            h.id_horario,
                            h.fecha,
                            h.hora_reserva,
                            h.disponibilidad,
                            h.id_medico,
                            m.nombre_med,
                            m.apellido_med,
                            e.nombre_especialidad

                        FROM tb_horarios h

                        INNER JOIN tb_medicos m
                            ON h.id_medico = m.id_medico

                        INNER JOIN tb_especialidades e
                            ON m.id_especialidad = e.id_especialidad

                        WHERE h.id_medico = :id_medico

                        ORDER BY h.fecha ASC,
                                 h.hora_reserva ASC";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id_medico',
                    $id_medico,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($horarios),
                    "data" => $horarios
                ], JSON_UNESCAPED_UNICODE);
            }

            /* Buscar horarios disponibles */

            elseif (isset($_GET['disponibles'])) {

                $sql = "SELECT
                            h.id_horario,
                            h.fecha,
                            h.hora_reserva,
                            h.disponibilidad,
                            h.id_medico,
                            m.nombre_med,
                            m.apellido_med,
                            e.nombre_especialidad

                        FROM tb_horarios h

                        INNER JOIN tb_medicos m
                            ON h.id_medico = m.id_medico

                        INNER JOIN tb_especialidades e
                            ON m.id_especialidad = e.id_especialidad

                        WHERE h.disponibilidad = 1

                        ORDER BY h.fecha ASC,
                                 h.hora_reserva ASC";

                $stmt = $conexion->prepare($sql);

                $stmt->execute();

                $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($horarios),
                    "data" => $horarios
                ], JSON_UNESCAPED_UNICODE);
            }

            /*  Listar todos */

            else {

                $sql = "SELECT
                            h.id_horario,
                            h.fecha,
                            h.hora_reserva,
                            h.disponibilidad,
                            h.id_medico,
                            m.nombre_med,
                            m.apellido_med,
                            e.nombre_especialidad

                        FROM tb_horarios h

                        INNER JOIN tb_medicos m
                            ON h.id_medico = m.id_medico

                        INNER JOIN tb_especialidades e
                            ON m.id_especialidad = e.id_especialidad

                        ORDER BY h.fecha ASC,
                                 h.hora_reserva ASC";

                $stmt = $conexion->prepare($sql);

                $stmt->execute();

                $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($horarios),
                    "data" => $horarios
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
                !isset($datos['fecha']) ||
                !isset($datos['hora_reserva']) ||
                !isset($datos['id_medico'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Fecha, hora y médico son obligatorios"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Verificar médico */

            $sql = "SELECT
                        id_medico
                    FROM tb_medicos
                    WHERE id_medico = :id_medico";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id_medico',
                $datos['id_medico'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            if (!$stmt->fetch()) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "El médico indicado no existe"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Verificar que no exista otro horario igual para el médico  */

            $sql = "SELECT
                        id_horario
                    FROM tb_horarios
                    WHERE fecha = :fecha
                    AND hora_reserva = :hora_reserva
                    AND id_medico = :id_medico";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':fecha',
                $datos['fecha']
            );

            $stmt->bindValue(
                ':hora_reserva',
                $datos['hora_reserva']
            );

            $stmt->bindValue(
                ':id_medico',
                $datos['id_medico'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            if ($stmt->fetch()) {

                http_response_code(409);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "El médico ya tiene un horario registrado para esa fecha y hora"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /*  Crear horario */

            $sql = "INSERT INTO tb_horarios
                    (
                        fecha,
                        hora_reserva,
                        disponibilidad,
                        id_medico
                    )
                    VALUES
                    (
                        :fecha,
                        :hora_reserva,
                        :disponibilidad,
                        :id_medico
                    )";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':fecha',
                $datos['fecha']
            );

            $stmt->bindValue(
                ':hora_reserva',
                $datos['hora_reserva']
            );

            $stmt->bindValue(
                ':disponibilidad',
                $datos['disponibilidad'] ?? 1,
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':id_medico',
                $datos['id_medico'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            $id = $conexion->lastInsertId();

            http_response_code(201);

            echo json_encode([
                "success" => true,
                "mensaje" => "Horario creado correctamente",
                "id_horario" => $id
            ], JSON_UNESCAPED_UNICODE);

            break;


       
        /*  PUT  */

        case 'PUT':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID del horario"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $id = $_GET['id'];

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (
                !isset($datos['fecha']) ||
                !isset($datos['hora_reserva']) ||
                !isset($datos['id_medico']) ||
                !isset($datos['disponibilidad'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Fecha, hora, disponibilidad y médico son obligatorios"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            $sql = "UPDATE tb_horarios
                    SET
                        fecha = :fecha,
                        hora_reserva = :hora_reserva,
                        disponibilidad = :disponibilidad,
                        id_medico = :id_medico
                    WHERE id_horario = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':fecha',
                $datos['fecha']
            );

            $stmt->bindValue(
                ':hora_reserva',
                $datos['hora_reserva']
            );

            $stmt->bindValue(
                ':disponibilidad',
                $datos['disponibilidad'],
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':id_medico',
                $datos['id_medico'],
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
                    "mensaje" => "Horario actualizado correctamente"
                ]);

            } else {

                echo json_encode([
                    "success" => false,
                    "mensaje" => "No se encontró el horario o no hubo cambios"
                ], JSON_UNESCAPED_UNICODE);
            }

            break;


          /* DELETE */

        case 'DELETE':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID del horario"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $id = $_GET['id'];

            $sql = "DELETE FROM tb_horarios
                    WHERE id_horario = :id";

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
                    "mensaje" => "Horario eliminado correctamente"
                ], JSON_UNESCAPED_UNICODE);

            } else {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Horario no encontrado"
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