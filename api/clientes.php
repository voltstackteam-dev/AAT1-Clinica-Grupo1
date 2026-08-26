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

            // Si viene un ID
            if (isset($_GET['id'])) {

                $id = $_GET['id'];

                $sql = "SELECT
                            id_cliente,
                            nombre_cli,
                            apellido_cli,
                            nacimiento_cli,
                            telefono_cli,
                            id_usuario
                        FROM tb_clientes
                        WHERE id_cliente = :id";

                $stmt = $conexion->prepare($sql);

                $stmt->bindValue(
                    ':id',
                    $id,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($cliente) {

                    echo json_encode([
                        "success" => true,
                        "data" => $cliente
                    ]);

                } else {

                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "mensaje" => "Cliente no encontrado"
                    ]);
                }

            } else {

                // Listar todos los clientes

                $sql = "SELECT
                            id_cliente,
                            nombre_cli,
                            apellido_cli,
                            nacimiento_cli,
                            telefono_cli,
                            id_usuario
                        FROM tb_clientes
                        ORDER BY id_cliente DESC";

                $stmt = $conexion->prepare($sql);

                $stmt->execute();

                $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "cantidad" => count($clientes),
                    "data" => $clientes
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
                !isset($datos['nombre_cli']) ||
                !isset($datos['apellido_cli'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre y apellido son obligatorios"
                ]);

                exit;
            }

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
                $datos['nacimiento_cli'] ?? null
            );

            $stmt->bindValue(
                ':telefono_cli',
                $datos['telefono_cli'] ?? null
            );

            $stmt->bindValue(
                ':id_usuario',
                $datos['id_usuario'] ?? null,
                $datos['id_usuario'] !== null
                    ? PDO::PARAM_INT
                    : PDO::PARAM_NULL
            );

            $stmt->execute();

            $id = $conexion->lastInsertId();

            http_response_code(201);

            echo json_encode([
                "success" => true,
                "mensaje" => "Cliente creado correctamente",
                "id_cliente" => $id
            ]);

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

            $id = $_GET['id'];

            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            if (
                !isset($datos['nombre_cli']) ||
                !isset($datos['apellido_cli'])
            ) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Nombre y apellido son obligatorios"
                ]);

                exit;
            }

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
                $datos['nacimiento_cli'] ?? null
            );

            $stmt->bindValue(
                ':telefono_cli',
                $datos['telefono_cli'] ?? null
            );

            $stmt->bindValue(
                ':id_usuario',
                $datos['id_usuario'] ?? null,
                $datos['id_usuario'] !== null
                    ? PDO::PARAM_INT
                    : PDO::PARAM_NULL
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
                ]);

            } else {

                echo json_encode([
                    "success" => false,
                    "mensaje" => "No se encontró el cliente o no hubo cambios"
                ]);
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

            $id = $_GET['id'];

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
                ]);

            } else {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "mensaje" => "Cliente no encontrado"
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