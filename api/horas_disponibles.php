<?php
require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/jwt.php';

$metodo = iniciarApi();

ejecutarApi(function () use ($conexion, $metodo): void {
    if ($metodo !== 'GET') {
        responderError('Método no permitido.', 405);
    }

    verificarRol([1, 2, 3]);
    header('Cache-Control: no-store');

    $medico = filter_input(INPUT_GET, 'id_medico', FILTER_VALIDATE_INT);
    $sala = filter_input(INPUT_GET, 'id_sala', FILTER_VALIDATE_INT);
    $valorFecha = $_GET['fecha'] ?? '';
    $fecha = is_string($valorFecha)
        ? DateTimeImmutable::createFromFormat('!Y-m-d', $valorFecha)
        : false;

    if (
        !$medico || $medico < 1 || !$sala || $sala < 1 ||
        !$fecha || $fecha->format('Y-m-d') !== $valorFecha
    ) {
        responderError('Indica un médico, una sala y una fecha válidos.');
    }

    $dias = [1 => 'LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES'];
    $dia = $dias[(int) $fecha->format('N')] ?? null;

    if (!$dia) {
        responder(['success' => true, 'data' => []]);
    }

    $consulta = $conexion->prepare(
        'SELECT hora_inicio, hora_fin
         FROM disponibilidades
         WHERE id_medico = :medico
             AND dia_semana = :dia'
    );
    $consulta->execute([':medico' => $medico, ':dia' => $dia]);
    $bloques = $consulta->fetchAll();

    // La consulta solo expone horas ocupadas, sin datos de otros pacientes.
    $consulta = $conexion->prepare(
        "SELECT TIME(fecha_hora)
         FROM citas
         WHERE fecha_hora >= :inicio
             AND fecha_hora < :fin
             AND estado <> 'CANCELADA'
             AND (id_medico = :medico OR id_sala = :sala)"
    );
    $consulta->execute([
        ':inicio' => $fecha->format('Y-m-d 00:00:00'),
        ':fin' => $fecha->modify('+1 day')->format('Y-m-d 00:00:00'),
        ':medico' => $medico,
        ':sala' => $sala
    ]);
    $ocupadas = $consulta->fetchAll(PDO::FETCH_COLUMN);
    $horas = [];

    for ($hora = 8; $hora < 17; $hora++) {
        $valor = sprintf('%02d:00:00', $hora);

        if (in_array($valor, $ocupadas, true)) {
            continue;
        }

        foreach ($bloques as $bloque) {
            if ($valor >= $bloque['hora_inicio'] && $valor < $bloque['hora_fin']) {
                $horas[] = substr($valor, 0, 5);
                break;
            }
        }
    }

    responder(['success' => true, 'data' => $horas]);
});
