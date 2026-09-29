<?php

require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/jwt.php';

$metodo = iniciarApi();

ejecutarApi(function () use ($conexion, $metodo): void {

    if ($metodo !== 'POST') {
        responderError('Metodo no permitido.', 405);
    }

    // Solo un médico puede completar una consulta
    $usuario = verificarRol([2]);

    $d = leerJson();

    requerirCampos($d, [
        'id_cita',
        'diagnostico'
    ]);

    $idCita = (int) $d['id_cita'];
    $diagnostico = trim($d['diagnostico']);

    $observaciones = isset($d['observaciones'])
        ? trim((string) $d['observaciones'])
        : null;

    // Indica si el paciente no necesita receta
    $sinReceta = !empty($d['sin_receta']);

    // Lista de medicamentos enviados desde Angular
    $medicamentos = isset($d['medicamentos']) && is_array($d['medicamentos'])
        ? $d['medicamentos']
        : [];

    if ($diagnostico === '') {
        responderError('El diagnóstico es obligatorio.', 400);
    }

    /*
     * Si se marcó "sin receta", no deben existir medicamentos.
     */
    if ($sinReceta) {
        $medicamentos = [];
    }

    /*
     * 1. Obtener la cita
     */
    $consulta = $conexion->prepare(
        'SELECT
            id_cita,
            id_paciente,
            id_medico,
            estado
         FROM citas
         WHERE id_cita = :id'
    );

    $consulta->execute([
        ':id' => $idCita
    ]);

    $cita = $consulta->fetch();

    if (!$cita) {
        responderError('La cita indicada no existe.', 404);
    }

    /*
     * 2. Verificar que la cita pertenezca al médico
     */
    $perfil = $conexion->prepare(
        'SELECT id_medico
         FROM medicos
         WHERE id_usuario = :usuario'
    );

    $perfil->execute([
        ':usuario' => (int) $usuario->id_usuario
    ]);

    $idMedico = (int) $perfil->fetchColumn();

    if (!$idMedico) {
        responderError(
            'La cuenta no tiene un perfil médico asociado.',
            403
        );
    }

    if ($idMedico !== (int) $cita['id_medico']) {
        responderError(
            'Solo puede completar consultas de sus propios pacientes.',
            403
        );
    }

    /*
     * 3. La cita debe estar EN_PROCESO
     */
    if ($cita['estado'] !== 'EN_PROCESO') {
        responderError(
            'La cita debe estar EN_PROCESO para poder completarse.',
            409
        );
    }

    /*
     * 4. Evitar crear dos historiales para la misma cita
     */
    $existeHistorial = $conexion->prepare(
        'SELECT id_historial
         FROM historial_medico
         WHERE id_cita = :id_cita'
    );

    $existeHistorial->execute([
        ':id_cita' => $idCita
    ]);

    if ($existeHistorial->fetchColumn()) {
        responderError(
            'Esta cita ya tiene un historial médico registrado.',
            409
        );
    }

    /*
     * 5. Validar los medicamentos
     */
    foreach ($medicamentos as $medicamento) {

        if (
            !isset($medicamento['id_medicamento']) ||
            !isset($medicamento['dosis']) ||
            !isset($medicamento['frecuencia']) ||
            !isset($medicamento['duracion_dias']) ||
            !isset($medicamento['cantidad_prescrita'])
        ) {
            responderError(
                'Cada medicamento debe tener dosis, frecuencia, duración y cantidad.',
                400
            );
        }

        $idMedicamento = (int) $medicamento['id_medicamento'];
        $dosis = (string) $medicamento['dosis'];
        $frecuenciaHoras = (int) $medicamento['frecuencia'];
        $duracionDias = (int) $medicamento['duracion_dias'];
        $cantidadPrescrita = (int) $medicamento['cantidad_prescrita'];

        if (
            $idMedicamento <= 0 ||
            $dosis === '' ||
            $frecuenciaHoras <= 0 ||
            $duracionDias <= 0 ||
            $cantidadPrescrita <= 0
        ) {
            responderError(
                'Los datos de cada medicamento deben ser válidos.',
                400
            );
        }

        /*
         * Verificar que el medicamento exista
         */
        $verificarMedicamento = $conexion->prepare(
            'SELECT id_medicamento
             FROM tb_medicamentos
             WHERE id_medicamento = :id'
        );

        $verificarMedicamento->execute([
            ':id' => $idMedicamento
        ]);

        if (!$verificarMedicamento->fetchColumn()) {
            responderError(
                'Uno de los medicamentos seleccionados no existe.',
                404
            );
        }
    }

    /*
     * 6. Iniciar transacción
     */
    $conexion->beginTransaction();

    try {

        /*
         * Crear historial médico
         */
        $historial = $conexion->prepare(
            'INSERT INTO historial_medico
                (
                    id_cita,
                    diagnostico,
                    receta,
                    observaciones
                )
             VALUES
                (
                    :id_cita,
                    :diagnostico,
                    NULL,
                    :observaciones
                )'
        );

        $historial->execute([
            ':id_cita' => $idCita,
            ':diagnostico' => $diagnostico,
            ':observaciones' => $observaciones ?: null
        ]);

        $idHistorial = (int) $conexion->lastInsertId();

        $idReceta = null;

        /*
         * 7. Crear receta si existen medicamentos
         */
        if (count($medicamentos) > 0) {

            /*
             * Una consulta = una receta
             */
            $receta = $conexion->prepare(
                'INSERT INTO recetas
                    (
                        id_historial,
                        id_paciente,
                        id_medico,
                        estado
                    )
                 VALUES
                    (
                        :id_historial,
                        :id_paciente,
                        :id_medico,
                        "EMITIDA"
                    )'
            );

            $receta->execute([
                ':id_historial' => $idHistorial,
                ':id_paciente' => (int) $cita['id_paciente'],
                ':id_medico' => $idMedico
            ]);

            $idReceta = (int) $conexion->lastInsertId();

            /*
             * Preparar inserción de detalles
             */
            $detalle = $conexion->prepare(
                'INSERT INTO receta_detalles
                    (
                        id_receta,
                        id_medicamento,
                        dosis,
                        frecuencia,
                        duracion_dias,
                        cantidad_prescrita
                    )
                 VALUES
                    (
                        :id_receta,
                        :id_medicamento,
                        :dosis,
                        :frecuencia,
                        :duracion_dias,
                        :cantidad_prescrita
                    )'
            );

            /*
             * Insertar cada medicamento
             */
            foreach ($medicamentos as $medicamento) {

                $idMedicamento = (int) $medicamento['id_medicamento'];
                $dosis = (string) $medicamento['dosis'];
                $frecuenciaHoras = (int) $medicamento['frecuencia'];
                $duracionDias = (int) $medicamento['duracion_dias'];
                $cantidadPrescrita = (int) $medicamento['cantidad_prescrita'];

                $detalle->execute([
                    ':id_receta' => $idReceta,
                    ':id_medicamento' => $idMedicamento,
                    ':dosis' => $dosis . ' tableta(s)',
                    ':frecuencia' => 'Cada ' . $frecuenciaHoras . ' horas',
                    ':duracion_dias' => $duracionDias,
                    ':cantidad_prescrita' => $cantidadPrescrita
                ]);
            }
        }

        /*
         * 8. Completar la cita
         */
        $actualizar = $conexion->prepare(
            'UPDATE citas
             SET estado = "COMPLETADA",
                 updated_at = CURRENT_TIMESTAMP
             WHERE id_cita = :id_cita'
        );

        $actualizar->execute([
            ':id_cita' => $idCita
        ]);

        /*
         * 9. Confirmar todo
         */
        $conexion->commit();

        responder([
            'success' => true,
            'mensaje' => $idReceta
                ? 'Consulta, historial médico y receta guardados correctamente.'
                : 'Consulta e historial médico guardados correctamente.',
            'id_cita' => $idCita,
            'id_historial' => $idHistorial,
            'id_receta' => $idReceta,
            'estado' => 'COMPLETADA'
        ]);

    } catch (Throwable $e) {

        /*
         * Si algo falla, no guardamos nada a medias.
         */
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }

        responderError(
            'No se pudo guardar la consulta: ' . $e->getMessage(),
            500
        );
    }
});