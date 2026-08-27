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

            // Buscar clientes por ID
            if (isset($_GET['id'])) {

                $id = $_GET['id'];

                $sql = "SELECT
                        c.id_cliente,
                        c.nombre_cli,
                    c.apellido_cli,
                        c.nacimiento_cli,
                        c.telefono_cli,
                        c.id_usuario,
                        u.nombre_usuario
                        FROM tb_clientes c
                         INNER JOIN tb_usuarios u
                            ON c.id_usuario = u.id_usuario
                        WHERE c.id_cliente = :id";


                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id',
                    $id,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $cliente = $stmt->fetch();

                if ($cliente) {

                    echo json_encode([
                        "success" => true,
                        "data" => $cliente
                    ], JSON_UNESCAPED_UNICODE);

                } else {

                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "Cliente no encontrado"
                    ], JSON_UNESCAPED_UNICODE);
                }

            } else {

                // Listar todos los clientes

                $sql = "SELECT
                            c.id_cliente,
                            c.nombre_cli,
                            c.apellido_cli,
                            c.nacimiento_cli,
                            c.telefono_cli,
                            c.id_usuario,
                            u.nombre_usuario
                        FROM tb_clientes c
                        INNER JOIN tb_usuarios u
                            ON c.id_usuario = u.id_usuario
                        ORDER BY c.id_cliente DESC";

                $stmt = $conexion->prepare($sql);

                $stmt->execute();

                $clientes = $stmt->fetchAll();

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($clientes),
                    "data" => $clientes
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
                    "mensaje" => "Nombre y apellido son obligatorios"
                ]);

                exit;
            }

            //Validar campos obligatorios según la BD

            if(
                 !isset($datos['nombre_cli']) ||
                !isset($datos['apellido_cli']) ||
                !isset($datos['nacimiento_cli']) ||
                !isset($datos['id_usuario'])

            ){
                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre, apellido, fecha de nacimiento e usuario son obligatorios"
                ]);

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
                ]);

                exit;
            }


            /* Insertar cliente */

            $sql = "INSERT INTO tb_clientes
                    (
                        nombre_cli,
                        apellido_cli,
                        nacimiento_cli,
                        telefono_cli,
                        id_usuario
                    )
                    VALUES
                    (
                        :nombre_cli,
                        :apellido_cli,
                        :nacimiento_cli,
                        :telefono_cli,
                        :id_usuario
                    )";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_cli',
                $datos['nombre_cli']
            );

            $stmt->bindValue(
                ':apellido_cli',
                $datos['apellido_cli']
            );

            $stmt->bindValue(
                ':nacimiento_cli',
                $datos['nacimiento_cli']
            );

            $stmt->bindValue(
                ':telefono_cli',
                $datos['telefono_cli'] ?? null
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
                "mensaje" => "Cliente creado correctamente",
                "id_cliente" => $id
            ], JSON_UNESCAPED_UNICODE);

            break;

       
        /*  PUT  */

        case 'PUT':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID del cliente"
                ]);

                exit;
            }

            $id = intval($_GET['id']);

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (!$datos){
                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre y apellido son obligatorios"
                ]);

                exit;
            }


             if (
                !isset($datos['nombre_cli']) ||
                !isset($datos['apellido_cli']) ||
                !isset($datos['nacimiento_cli']) ||
                !isset($datos['id_usuario'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre, apellido, fecha de nacimiento e usuario son obligatorios"
                ]);

                exit;
            }


         /* Verificar usuario */

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
                ]);

                exit;
            }


            /* Actualizar cliente */

            $sql = "UPDATE tb_clientes
                    SET
                        nombre_cli = :nombre_cli,
                        apellido_cli = :apellido_cli,
                        nacimiento_cli = :nacimiento_cli,
                        telefono_cli = :telefono_cli,
                        id_usuario = :id_usuario
                    WHERE id_cliente = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindValue(
                ':nombre_cli',
                $datos['nombre_cli']
            );

            $stmt->bindValue(
                ':apellido_cli',
                $datos['apellido_cli']
            );

            $stmt->bindValue(
                ':nacimiento_cli',
                $datos['nacimiento_cli']
            );

            $stmt->bindValue(
                ':telefono_cli',
                $datos['telefono_cli'] ?? null
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
                    "mensaje" => "Cliente actualizado correctamente"
                ], JSON_UNESCAPED_UNICODE);

            } else {

                echo json_encode([
                    "success" => false,
                    "mensaje" => "No se encontró el cliente o no hubo cambios"
                ], JSON_UNESCAPED_UNICODE);
            }

            break;

        /* DELETE */

        case 'DELETE':

            if (!isset($_GET['id'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Debe indicar el ID del cliente"
                ]);

                exit;
            }

            $id = intval($_GET['id']);

            $sql = "DELETE FROM tb_clientes
                    WHERE id_cliente = :id";

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
                    "mensaje" => "Cliente eliminado correctamente"
                ],  JSON_UNESCAPED_UNICODE);

            } else {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Cliente no encontrado"
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