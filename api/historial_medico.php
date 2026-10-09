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
     * Médico (rol 2):
     * puede consultar el historial de un paciente
     * que tenga una cita con ese médico.
     *
     * Paciente (rol 3):
     * solamente puede consultar su propio historial.
     */
    $usuario = verificarRol([1, 2, 3]);

    $idPaciente = null;

    /*
     * PACIENTE
     *
     * Obtenemos el paciente relacionado
     * con su usuario.
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
     * MÉDICO
     *
     * El médico debe enviar:
     *
     * historial_medico.php?id_paciente=5
     *
     * Primero obtenemos su id_medico.
     */
    if ((int) $usuario->id_rol === 2) {

        if (!isset($_GET['id_paciente'])) {
            responderError(
                'Debe indicar el paciente cuyo historial desea consultar.',
                400
            );
        }

        $idPaciente = (int) $_GET['id_paciente'];

        if ($idPaciente <= 0) {
            responderError(
                'El paciente indicado no es valido.',
                400
            );
        }

        /*
         * Obtenemos el perfil médico relacionado
         * con el usuario que inició sesión.
         */
        $medico = $conexion->prepare(
            'SELECT id_medico
             FROM medicos
             WHERE id_usuario = :usuario'
        );

        $medico->execute([
            ':usuario' => (int) $usuario->id_usuario
        ]);

        $idMedico = (int) $medico->fetchColumn();

        if (!$idMedico) {
            responderError(
                'La cuenta no tiene un perfil médico asociado.',
                403
            );
        }

        /*
         * SEGURIDAD
         *
         * Comprobamos que el paciente tenga
         * al menos una cita con este médico.
         *
         * Esto evita que un médico pueda consultar
         * arbitrariamente el historial de cualquier paciente.
         */
        $autorizacion = $conexion->prepare(
            'SELECT 1
             FROM citas
             WHERE id_paciente = :paciente
               AND id_medico = :medico
             LIMIT 1'
        );

        $autorizacion->execute([
            ':paciente' => $idPaciente,
            ':medico' => $idMedico
        ]);

        if (!$autorizacion->fetchColumn()) {
            responderError(
                'No tiene permiso para consultar el historial de este paciente.',
                403
            );
        }
    }

    /*
     * Obtenemos los historiales médicos.
     *
     * Administrador:
     *   todos los historiales.
     *
     * Paciente:
     *   solamente su historial.
     *
     * Médico:
     *   solamente el historial del paciente solicitado.
     *
     * IMPORTANTE:
     * El historial NO se filtra por médico.
     *
     * Por eso aparecerán también las consultas
     * realizadas por otros médicos.
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
     * Para pacientes y médicos filtramos
     * por el paciente correspondiente.
     *
     * Para administradores no agregamos filtro.
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
