<?php
require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/jwt.php';

$metodo = iniciarApi();

function validarHorario(array $datos): array
{
    requerirCampos($datos, ['id_medico', 'dia_semana', 'hora_inicio', 'hora_fin']);
    $dia = strtoupper(trim($datos['dia_semana']));
    $inicio = substr($datos['hora_inicio'], 0, 8);
    $fin = substr($datos['hora_fin'], 0, 8);
    if (!in_array($dia, ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES'], true)) responderError('Los horarios solo pueden registrarse de lunes a viernes.');
    if ($inicio < '08:00:00' || $fin > '17:00:00' || $inicio >= $fin) responderError('El horario debe estar entre 08:00 y 17:00 y tener una hora de inicio menor a la final.');
    return [(int)$datos['id_medico'], $dia, $inicio, $fin];
}

ejecutarApi(function () use ($conexion, $metodo): void {
    $base = 'SELECT d.id_disponibilidad,d.id_medico,d.dia_semana,d.hora_inicio,d.hora_fin,CONCAT(m.nombre," ",m.apellido) medico FROM disponibilidades d JOIN medicos m ON m.id_medico=d.id_medico';
    if ($metodo === 'GET') {
        $parametros = [];
        $sql = $base;
        if (isset($_GET['id'])) {
            $sql .= ' WHERE d.id_disponibilidad=:id';
            $parametros[':id'] = idRequerido();
        } elseif (isset($_GET['id_medico'])) {
            $sql .= ' WHERE d.id_medico=:medico';
            $parametros[':medico'] = (int)$_GET['id_medico'];
        }
        $consulta = $conexion->prepare($sql . ' ORDER BY FIELD(d.dia_semana,"LUNES","MARTES","MIERCOLES","JUEVES","VIERNES"),d.hora_inicio');
        $consulta->execute($parametros);
        $datos = isset($_GET['id']) ? $consulta->fetch() : $consulta->fetchAll();
        if (isset($_GET['id']) && !$datos) responderError('Disponibilidad no encontrada.', 404);
        responder(['success' => true, 'cantidad' => is_array($datos) ? count($datos) : 1, 'data' => $datos]);
    }

    verificarRol([1]);
    if ($metodo === 'DELETE') {
        $id = idRequerido();
        $consulta = $conexion->prepare('SELECT id_medico FROM disponibilidades WHERE id_disponibilidad=:id');
        $consulta->execute([':id' => $id]);
        $idMedico = (int)$consulta->fetchColumn();
        if (!$idMedico) responderError('Disponibilidad no encontrada.', 404);
        $conexion->prepare('DELETE FROM disponibilidades WHERE id_disponibilidad=:id')->execute([':id' => $id]);
        responder(['success' => true, 'mensaje' => 'Disponibilidad eliminada.']);
    }

    $datos = leerJson();
    [$idMedico, $dia, $inicio, $fin] = validarHorario($datos);
    if (!existe($conexion, 'medicos', 'id_medico', $idMedico)) responderError('El médico no existe.');
    $idActual = $metodo === 'PUT' ? idRequerido() : 0;
    $conflicto = $conexion->prepare('SELECT 1 FROM disponibilidades WHERE id_medico=:medico AND dia_semana=:dia AND hora_inicio<:fin AND hora_fin>:inicio AND id_disponibilidad<>:id');
    $conflicto->execute([':medico' => $idMedico, ':dia' => $dia, ':inicio' => $inicio, ':fin' => $fin, ':id' => $idActual]);
    if ($conflicto->fetchColumn()) responderError('Ese horario se cruza con una disponibilidad existente.', 409);

    if ($metodo === 'POST') {
        $consulta = $conexion->prepare('INSERT INTO disponibilidades (id_medico,dia_semana,hora_inicio,hora_fin) VALUES (:medico,:dia,:inicio,:fin)');
        $consulta->execute([':medico' => $idMedico, ':dia' => $dia, ':inicio' => $inicio, ':fin' => $fin]);
        responder(['success' => true, 'id_disponibilidad' => (int)$conexion->lastInsertId()], 201);
    }
    if ($metodo === 'PUT') {
        $consulta = $conexion->prepare('UPDATE disponibilidades SET id_medico=:medico,dia_semana=:dia,hora_inicio=:inicio,hora_fin=:fin WHERE id_disponibilidad=:id');
        $consulta->execute([':medico' => $idMedico, ':dia' => $dia, ':inicio' => $inicio, ':fin' => $fin, ':id' => $idActual]);
        responder(['success' => true, 'mensaje' => 'Disponibilidad actualizada.']);
    }
    responderError('Método no permitido.', 405);
});
