<?php

require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/jwt.php';

$metodo = iniciarApi();

ejecutarApi(function () use ($conexion, $metodo): void {

    if ($metodo !== 'GET') {
        responderError('Metodo no permitido.', 405);
    }

    /*
     * Administrador (rol 1):
     * puede consultar todos los historiales médicos.
     *
     * Paciente (rol 3):
     * solamente puede consultar su propio historial.
     */
    $usuario = verificarRol([1, 3]);

    $idPaciente = null;

    /*
     * Si es paciente, obtenemos el paciente
     * relacionado con su usuario.
     */
    if ((int) $usuario->id_rol === 3) {

        $paciente = $conexion->prepare(
            'SELECT id_paciente
             FROM pacientes
             WHERE id_usuario = :usuario'
        );

        $paciente->execute([
            ':usuario' => (int) $usuario->id_usuario
        ]);

        $idPaciente = (int) $paciente->fetchColumn();

        if (!$idPaciente) {
            responderError(
                'La cuenta no tiene un perfil de paciente asociado.',
                403
            );
        }
    }

    /*
     * Obtenemos los historiales médicos.
     *
     * Si es administrador:
     *   obtiene todos los historiales.
     *
     * Si es paciente:
     *   obtiene solamente su historial.
     */
    $sql = '
        SELECT
            h.id_historial,
            h.id_cita,
            h.diagnostico,
            h.observaciones,
            h.fecha_atencion,

            c.fecha_hora,
            c.motivo_consulta,

            CONCAT(p.nombre, " ", p.apellido) AS paciente,

            CONCAT(m.nombre, " ", m.apellido) AS medico,

            e.nombre_especialidad

        FROM historial_medico h

        INNER JOIN citas c
            ON c.id_cita = h.id_cita

        INNER JOIN pacientes p
            ON p.id_paciente = c.id_paciente

        INNER JOIN medicos m
            ON m.id_medico = c.id_medico

        INNER JOIN especialidades e
            ON e.id_especialidad = m.id_especialidad
    ';

    $parametros = [];

    /*
     * Solamente agregamos el filtro cuando
     * el usuario es paciente.
     */
    if ($idPaciente !== null) {

        $sql .= ' WHERE c.id_paciente = :paciente ';

        $parametros[':paciente'] = $idPaciente;
    }

    $sql .= ' ORDER BY h.fecha_atencion DESC';

    $consulta = $conexion->prepare($sql);

    $consulta->execute($parametros);

    $historial = $consulta->fetchAll();

    /*
     * Para cada consulta buscamos su receta
     * y los medicamentos asociados.
     */
    foreach ($historial as &$item) {

        $receta = $conexion->prepare(
            'SELECT
                r.id_receta,
                r.fecha_emision,
                r.estado

             FROM recetas r

             WHERE r.id_historial = :historial

             ORDER BY r.fecha_emision DESC

             LIMIT 1'
        );

        $receta->execute([
            ':historial' => (int) $item['id_historial']
        ]);

        $datosReceta = $receta->fetch();

        /*
         * Si la consulta no tiene receta,
         * devolvemos null.
         */
        if (!$datosReceta) {
            $item['receta'] = null;
            continue;
        }

        /*
         * Buscamos los medicamentos de la receta.
         */
        $detalle = $conexion->prepare(
            'SELECT
                rd.id_medicamento,
                rd.dosis,
                rd.frecuencia,
                rd.duracion_dias,
                rd.cantidad_prescrita,

                med.nombre AS nombre_medicamento

             FROM receta_detalles rd

             INNER JOIN tb_medicamentos med
                ON med.id_medicamento = rd.id_medicamento

             WHERE rd.id_receta = :receta

             ORDER BY rd.id_receta_detalle ASC'
        );

        $detalle->execute([
            ':receta' => (int) $datosReceta['id_receta']
        ]);

        $datosReceta['detalles'] = $detalle->fetchAll();

        $item['receta'] = $datosReceta;
    }

    unset($item);

    responder([
        'success' => true,
        'data' => $historial
    ]);
});