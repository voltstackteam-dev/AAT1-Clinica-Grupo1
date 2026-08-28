<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "config/conexion.php";
require_once "config/jwt.php";

$metodo = $_SERVER['REQUEST_METHOD'];


/* OPTIONS */

if($metodo === 'OPTIONS'){

    http_response_code(200);

            echo json_encode([
                "success" => true,
                "mensaje" => "Preflight OK"
            ], JSON_UNESCAPED_UNICODE);

            exit;

}

/* VALIDAR JWT */

$usuarioToken = validarToken();

try {

    switch ($metodo) {

        /* GET */

        case 'GET':

            /* Buscar una cita por ID */

            if (isset($_GET['id'])) {

                $id = $_GET['id'];

                $sql = "SELECT
                            c.id_cita,

                            c.id_medico,
                            CONCAT(m.nombre_med, ' ', m.apellido_med) AS medico,

                            c.id_cliente,
                            CONCAT(cl.nombre_cli, ' ', cl.apellido_cli) AS cliente,

                            c.id_sala,
                            s.nombre_sala,

                            c.id_horario,
                            h.fecha,
                            h.hora_reserva,

                            c.motivo_consulta,
                            c.diagnosticos,
                            c.recetas,
                            c.estado_cita

                        FROM tb_citas c

                        INNER JOIN tb_medicos m
                            ON c.id_medico = m.id_medico

                        INNER JOIN tb_clientes cl
                            ON c.id_cliente = cl.id_cliente

                        INNER JOIN tb_salas s
                            ON c.id_sala = s.id_sala

                        INNER JOIN tb_horarios h
                            ON c.id_horario = h.id_horario

                        WHERE c.id_cita = :id";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id',
                    $id,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $cita = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($cita) {

                    echo json_encode([
                        "success" => true,
                        "data" => $cita
                    ]);

                } else {

                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "Cita no encontrada"
                    ], JSON_UNESCAPED_UNICODE);
                }

            }

            /* Filtrar por cliente */

            elseif (isset($_GET['id_cliente'])) {

                $id_cliente = $_GET['id_cliente'];

                $sql = "SELECT
                            c.id_cita,

                            c.id_medico,
                            CONCAT(m.nombre_med, ' ', m.apellido_med) AS medico,

                            c.id_cliente,

                            c.id_sala,
                            s.nombre_sala,

                            c.id_horario,
                            h.fecha,
                            h.hora_reserva,

                            c.motivo_consulta,
                            c.diagnosticos,
                            c.recetas,
                            c.estado_cita

                        FROM tb_citas c

                        INNER JOIN tb_medicos m
                            ON c.id_medico = m.id_medico

                        INNER JOIN tb_salas s
                            ON c.id_sala = s.id_sala

                        INNER JOIN tb_horarios h
                            ON c.id_horario = h.id_horario

                        WHERE c.id_cliente = :id_cliente

                        ORDER BY h.fecha DESC, h.hora_reserva DESC";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id_cliente',
                    $id_cliente,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($citas),
                    "data" => $citas
                ], JSON_UNESCAPED_UNICODE);
            }

            /*  Filtrar por médico  */

            elseif (isset($_GET['id_medico'])) {

                $id_medico = $_GET['id_medico'];

                $sql = "SELECT
                            c.id_cita,

                            c.id_medico,

                            c.id_cliente,
                            CONCAT(cl.nombre_cli, ' ', cl.apellido_cli) AS cliente,

                            c.id_sala,
                            s.nombre_sala,

                            c.id_horario,
                            h.fecha,
                            h.hora_reserva,

                            c.motivo_consulta,
                            c.diagnosticos,
                            c.recetas,
                            c.estado_cita

                        FROM tb_citas c

                        INNER JOIN tb_clientes cl
                            ON c.id_cliente = cl.id_cliente

                        INNER JOIN tb_salas s
                            ON c.id_sala = s.id_sala

                        INNER JOIN tb_horarios h
                            ON c.id_horario = h.id_horario

                        WHERE c.id_medico = :id_medico

                        ORDER BY h.fecha DESC, h.hora_reserva DESC";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id_medico',
                    $id_medico,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($citas),
                    "data" => $citas
                ], JSON_UNESCAPED_UNICODE);
            }

            /* Filtrar por estado */

            elseif (isset($_GET['estado'])) {

                $estado = $_GET['estado'];

                $sql = "SELECT
                            c.id_cita,

                            c.id_medico,
                            CONCAT(m.nombre_med, ' ', m.apellido_med) AS medico,

                            c.id_cliente,
                            CONCAT(cl.nombre_cli, ' ', cl.apellido_cli) AS cliente,

                            c.id_sala,
                            s.nombre_sala,

                            c.id_horario,
                            h.fecha,
                            h.hora_reserva,

                            c.motivo_consulta,
                            c.diagnosticos,
                            c.recetas,
                            c.estado_cita

                        FROM tb_citas c

                        INNER JOIN tb_medicos m
                            ON c.id_medico = m.id_medico

                        INNER JOIN tb_clientes cl
                            ON c.id_cliente = cl.id_cliente

                        INNER JOIN tb_salas s
                            ON c.id_sala = s.id_sala

                        INNER JOIN tb_horarios h
                            ON c.id_horario = h.id_horario

                        WHERE c.estado_cita = :estado

                        ORDER BY h.fecha DESC, h.hora_reserva DESC";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':estado',
                    $estado
                );

                $stmt->execute();

                $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($citas),
                    "data" => $citas
                ], JSON_UNESCAPED_UNICODE);
            }

            /* Listar todas las citas */

            else {

                $sql = "SELECT
                            c.id_cita,

                            c.id_medico,
                            CONCAT(m.nombre_med, ' ', m.apellido_med) AS medico,

                            c.id_cliente,
                            CONCAT(cl.nombre_cli, ' ', cl.apellido_cli) AS cliente,

                            c.id_sala,
                            s.nombre_sala,

                            c.id_horario,
                            h.fecha,
                            h.hora_reserva,

                            c.motivo_consulta,
                            c.diagnosticos,
                            c.recetas,
                            c.estado_cita

                        FROM tb_citas c

                        INNER JOIN tb_medicos m
                            ON c.id_medico = m.id_medico

                        INNER JOIN tb_clientes cl
                            ON c.id_cliente = cl.id_cliente

                        INNER JOIN tb_salas s
                            ON c.id_sala = s.id_sala

                        INNER JOIN tb_horarios h
                            ON c.id_horario = h.id_horario

                        ORDER BY h.fecha DESC, h.hora_reserva DESC";

                $stmt = $conexion->prepare($sql);

                $stmt->execute();

                $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($citas),
                    "data" => $citas
                ], JSON_UNESCAPED_UNICODE);
            }

            break;


        /* POST - RESERVAR CITA */

        case 'POST':

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            /* Validar campos obligatorios */

            if (
                !isset($datos['id_medico']) ||
                !isset($datos['id_cliente']) ||
                !isset($datos['id_sala']) ||
                !isset($datos['id_horario']) ||
                !isset($datos['motivo_consulta'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Médico, cliente, sala, horario y motivo de consulta son obligatorios"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Iniciar transacción */

            $conexion->beginTransaction();


            /* Verificar cliente */

            $sql = "SELECT id_cliente
                    FROM tb_clientes
                    WHERE id_cliente = :id_cliente";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id_cliente',
                $datos['id_cliente'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            if (!$stmt->fetch()) {

                $conexion->rollBack();

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "El cliente no existe"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Verificar médico */

            $sql = "SELECT
                        id_medico,
                        id_especialidad
                    FROM tb_medicos
                    WHERE id_medico = :id_medico";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id_medico',
                $datos['id_medico'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            $medico = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$medico) {

                $conexion->rollBack();

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "El médico no existe"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Verificar sala */

            $sql = "SELECT
                        id_sala,
                        id_especialidad
                    FROM tb_salas
                    WHERE id_sala = :id_sala";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id_sala',
                $datos['id_sala'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            $sala = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$sala) {

                $conexion->rollBack();

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "La sala no existe"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Verificar que la sala corresponda a la especialidad del médico */

            if (
                $sala['id_especialidad'] !=
                $medico['id_especialidad']
            ) {

                $conexion->rollBack();

                http_response_code(409);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "La sala seleccionada no corresponde a la especialidad del médico"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Verificar horario */

            $sql = "SELECT
                        id_horario,
                        fecha,
                        hora_reserva,
                        disponibilidad,
                        id_medico
                    FROM tb_horarios
                    WHERE id_horario = :id_horario
                    FOR UPDATE";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id_horario',
                $datos['id_horario'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            $horario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$horario) {

                $conexion->rollBack();

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "El horario no existe"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Verificar que el horario pertenezca al médico */

            if (
                $horario['id_medico'] !=
                $datos['id_medico']
            ) {

                $conexion->rollBack();

                http_response_code(409);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "El horario seleccionado no pertenece al médico"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Verificar disponibilidad */

            if ((int)$horario['disponibilidad'] !== 1) {

                $conexion->rollBack();

                http_response_code(409);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "El horario seleccionado no está disponible"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Verificar que no exista otra cita activa para ese horario */

            $sql = "SELECT id_cita
                    FROM tb_citas
                    WHERE id_horario = :id_horario
                    AND estado_cita IN
                    (
                        'pendiente',
                        'confirmada'
                    )
                    LIMIT 1";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id_horario',
                $datos['id_horario'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            if ($stmt->fetch()) {

                $conexion->rollBack();

                http_response_code(409);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Ya existe una cita activa para este horario"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Verificar que la sala no esté ocupada en ese horario  */

            $sql = "SELECT id_cita
                    FROM tb_citas
                    WHERE id_sala = :id_sala
                    AND id_horario = :id_horario
                    AND estado_cita IN
                    (
                        'pendiente',
                        'confirmada'
                    )
                    LIMIT 1";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id_sala',
                $datos['id_sala'],
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':id_horario',
                $datos['id_horario'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            if ($stmt->fetch()) {

                $conexion->rollBack();

                http_response_code(409);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "La sala ya está ocupada en ese horario"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Crear cita */

            $sql = "INSERT INTO tb_citas
                    (
                        id_medico,
                        id_cliente,
                        id_sala,
                        id_horario,
                        motivo_consulta,
                        diagnosticos,
                        recetas,
                        estado_cita
                    )
                    VALUES
                    (
                        :id_medico,
                        :id_cliente,
                        :id_sala,
                        :id_horario,
                        :motivo_consulta,
                        :diagnosticos,
                        :recetas,
                        'pendiente'
                    )";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id_medico',
                $datos['id_medico'],
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':id_cliente',
                $datos['id_cliente'],
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':id_sala',
                $datos['id_sala'],
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':id_horario',
                $datos['id_horario'],
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':motivo_consulta',
                $datos['motivo_consulta']
            );

            $stmt->bindValue(
                ':diagnosticos',
                $datos['diagnosticos'] ?? null
            );

            $stmt->bindValue(
                ':recetas',
                $datos['recetas'] ?? null
            );

            $stmt->execute();

            $id_cita = $conexion->lastInsertId();


            /*  Marcar horario como ocupado */

            $sql = "UPDATE tb_horarios
                    SET disponibilidad = 0
                    WHERE id_horario = :id_horario";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id_horario',
                $datos['id_horario'],
                PDO::PARAM_INT
            );

            $stmt->execute();


            /*  Confirmar transacción */

            $conexion->commit();

            http_response_code(201);

            echo json_encode([
                "success" => true,
                "mensaje" => "Cita reservada correctamente",
                "id_cita" => $id_cita
            ], JSON_UNESCAPED_UNICODE);

            break;


        /* PUT
        Actualizar / reprogramar / cancelar */

        case 'PUT':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID de la cita"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $id = $_GET['id'];

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );


            /* Buscar cita actual */

            $sql = "SELECT *
                    FROM tb_citas
                    WHERE id_cita = :id
                    FOR UPDATE";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id',
                $id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            $citaActual = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$citaActual) {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Cita no encontrada"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* CANCELAR CITA */

            if (
                isset($datos['estado_cita']) &&
                $datos['estado_cita'] === 'cancelada'
            ) {

                $conexion->beginTransaction();

                $sql = "UPDATE tb_citas
                        SET estado_cita = 'cancelada'
                        WHERE id_cita = :id";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id',
                    $id,
                    PDO::PARAM_INT
                );

                $stmt->execute();


                /* Liberar horario */

                $sql = "UPDATE tb_horarios
                        SET disponibilidad = 1
                        WHERE id_horario = :id_horario";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id_horario',
                    $citaActual['id_horario'],
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $conexion->commit();

                echo json_encode([
                    "success" => true,
                    "mensaje" => "Cita cancelada correctamente"
                ], JSON_UNESCAPED_UNICODE);

                break;
            }


            /* REPROGRAMAR CITA */

            if (isset($datos['id_horario'])) {

                $nuevoHorario = $datos['id_horario'];

                $conexion->beginTransaction();


                /* Obtener nuevo horario */

                $sql = "SELECT
                            id_horario,
                            fecha,
                            hora_reserva,
                            disponibilidad,
                            id_medico
                        FROM tb_horarios
                        WHERE id_horario = :id_horario
                        FOR UPDATE";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id_horario',
                    $nuevoHorario,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $horarioNuevo = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$horarioNuevo) {

                    $conexion->rollBack();

                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "El nuevo horario no existe"
                    ], JSON_UNESCAPED_UNICODE);

                    exit;
                }


                /* Verificar que pertenezca al médico */

                if (
                    $horarioNuevo['id_medico'] !=
                    $citaActual['id_medico']
                ) {

                    $conexion->rollBack();

                    http_response_code(409);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "El nuevo horario no pertenece al médico de la cita"
                    ], JSON_UNESCAPED_UNICODE);

                    exit;
                }


                /* Verificar disponibilidad */

                if ((int)$horarioNuevo['disponibilidad'] !== 1) {

                    $conexion->rollBack();

                    http_response_code(409);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "El nuevo horario no está disponible"
                    ], JSON_UNESCAPED_UNICODE);

                    exit;
                }


                /* Verificar que no exista otra cita */

                $sql = "SELECT id_cita
                        FROM tb_citas
                        WHERE id_horario = :id_horario
                        AND id_cita <> :id_cita
                        AND estado_cita IN
                        (
                            'pendiente',
                            'confirmada'
                        )
                        LIMIT 1";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id_horario',
                    $nuevoHorario,
                    PDO::PARAM_INT
                );

                $stmt->bindValue(
                    ':id_cita',
                    $id,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                if ($stmt->fetch()) {

                    $conexion->rollBack();

                    http_response_code(409);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "El nuevo horario ya tiene una cita activa"
                    ], JSON_UNESCAPED_UNICODE);

                    exit;
                }


                /* Liberar horario anterior */

                $sql = "UPDATE tb_horarios
                        SET disponibilidad = 1
                        WHERE id_horario = :id_horario";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id_horario',
                    $citaActual['id_horario'],
                    PDO::PARAM_INT
                );

                $stmt->execute();


                /* Ocupar nuevo horario */

                $sql = "UPDATE tb_horarios
                        SET disponibilidad = 0
                        WHERE id_horario = :id_horario";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id_horario',
                    $nuevoHorario,
                    PDO::PARAM_INT
                );

                $stmt->execute();


                /* Actualizar cita */

                $sql = "UPDATE tb_citas
                        SET
                            id_horario = :id_horario,
                            id_sala = :id_sala
                        WHERE id_cita = :id";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id_horario',
                    $nuevoHorario,
                    PDO::PARAM_INT
                );

                $stmt->bindValue(
                    ':id_sala',
                    $datos['id_sala'] ?? $citaActual['id_sala'],
                    PDO::PARAM_INT
                );

                $stmt->bindValue(
                    ':id',
                    $id,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $conexion->commit();

                echo json_encode([
                    "success" => true,
                    "mensaje" => "Cita reprogramada correctamente"
                ], JSON_UNESCAPED_UNICODE);

                break;
            }


            /* ACTUALIZACIÓN NORMAL */

            $sql = "UPDATE tb_citas
                    SET
                        id_medico = :id_medico,
                        id_cliente = :id_cliente,
                        id_sala = :id_sala,
                        motivo_consulta = :motivo_consulta,
                        diagnosticos = :diagnosticos,
                        recetas = :recetas,
                        estado_cita = :estado_cita
                    WHERE id_cita = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id_medico',
                $datos['id_medico'] ?? $citaActual['id_medico'],
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':id_cliente',
                $datos['id_cliente'] ?? $citaActual['id_cliente'],
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':id_sala',
                $datos['id_sala'] ?? $citaActual['id_sala'],
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':motivo_consulta',
                $datos['motivo_consulta'] ??
                $citaActual['motivo_consulta']
            );

            $stmt->bindValue(
                ':diagnosticos',
                $datos['diagnosticos'] ??
                $citaActual['diagnosticos']
            );

            $stmt->bindValue(
                ':recetas',
                $datos['recetas'] ??
                $citaActual['recetas']
            );

            $stmt->bindValue(
                ':estado_cita',
                $datos['estado_cita'] ??
                $citaActual['estado_cita']
            );

            $stmt->bindValue(
                ':id',
                $id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            echo json_encode([
                "success" => true,
                "mensaje" => "Cita actualizada correctamente"
            ], JSON_UNESCAPED_UNICODE);

            break;


        /* DELETE */

        case 'DELETE':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID de la cita"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $id = $_GET['id'];

            $conexion->beginTransaction();


            /* Obtener horario */

            $sql = "SELECT id_horario
                    FROM tb_citas
                    WHERE id_cita = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id',
                $id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            $cita = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cita) {

                $conexion->rollBack();

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Cita no encontrada"
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            /* Eliminar cita */

            $sql = "DELETE FROM tb_citas
                    WHERE id_cita = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id',
                $id,
                PDO::PARAM_INT
            );

            $stmt->execute();


            /* Liberar horario */

            $sql = "UPDATE tb_horarios
                    SET disponibilidad = 1
                    WHERE id_horario = :id_horario";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':id_horario',
                $cita['id_horario'],
                PDO::PARAM_INT
            );

            $stmt->execute();

            $conexion->commit();

            echo json_encode([
                "success" => true,
                "mensaje" => "Cita eliminada correctamente"
            ], JSON_UNESCAPED_UNICODE);

            break;


        /* MÉTODO NO PERMITIDO */

        default:

            http_response_code(405);

            echo json_encode([
                "success" => false,
                "mensaje" => "Método no permitido"
            ], JSON_UNESCAPED_UNICODE);

            break;
    }

} catch (PDOException $e) {

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => "Error en la API",
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conexion = null;

?>