<?php
require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/jwt.php';
$metodo = iniciarApi();
function validarDisponibilidad(PDO $conexion, int $medico, string $fechaHora): void
{
    $fecha = DateTime::createFromFormat('Y-m-d H:i', $fechaHora) ?: DateTime::createFromFormat('Y-m-d H:i:s', $fechaHora);
    if (!$fecha) responderError('La fecha y hora no tienen un formato valido.');
    $dias = [1 => 'LUNES', 2 => 'MARTES', 3 => 'MIERCOLES', 4 => 'JUEVES', 5 => 'VIERNES'];
    $dia = $dias[(int)$fecha->format('N')] ?? null;
    $hora = $fecha->format('H:i:s');
    if (!$dia || $hora < '08:00:00' || $hora >= '17:00:00') responderError('Las citas solo se atienden de lunes a viernes entre 08:00 y 17:00.');
    $s = $conexion->prepare('SELECT 1 FROM disponibilidades WHERE id_medico=:medico AND dia_semana=:dia AND hora_inicio<=:hora AND hora_fin>:hora');
    $s->execute([':medico' => $medico, ':dia' => $dia, ':hora' => $hora]);
    if (!$s->fetchColumn()) responderError('El medico no tiene disponibilidad en ese horario.', 409);
}
function autorizarGestionCita(PDO $conexion, object $usuario, array $cita): void
{
    $rol = (int)$usuario->id_rol;
    if ($rol === 1) return;
    if ($rol === 2) {
        $perfil = $conexion->prepare('SELECT id_medico FROM medicos WHERE id_usuario=:usuario');
        $perfil->execute([':usuario' => (int)$usuario->id_usuario]);
        if ((int)$perfil->fetchColumn() !== (int)$cita['id_medico']) responderError('Solo puede gestionar las citas asignadas a su perfil.', 403);
        return;
    }
    $perfil = $conexion->prepare('SELECT id_paciente FROM pacientes WHERE id_usuario=:usuario');
    $perfil->execute([':usuario' => (int)$usuario->id_usuario]);
    if ((int)$perfil->fetchColumn() !== (int)$cita['id_paciente']) responderError('Solo puede gestionar sus propias citas.', 403);
}
ejecutarApi(function () use ($conexion, $metodo): void {
    $base = 'SELECT c.id_cita,c.id_paciente,c.id_medico,c.id_sala,c.fecha_hora,c.motivo_consulta,c.estado,CONCAT(p.nombre," ",p.apellido) paciente,CONCAT(m.nombre," ",m.apellido) medico,e.nombre_especialidad,s.nombre_sala FROM citas c JOIN pacientes p ON p.id_paciente=c.id_paciente JOIN medicos m ON m.id_medico=c.id_medico JOIN especialidades e ON e.id_especialidad=m.id_especialidad JOIN salas s ON s.id_sala=c.id_sala';
    if ($metodo === 'GET') {
        $usuario = verificarRol([1, 2, 3]);
        $p = [];
        $sql = $base;
        if ((int)$usuario->id_rol === 3) {
            $sql .= ' WHERE p.id_usuario=:usuario';
            $p[':usuario'] = (int)$usuario->id_usuario;
        } elseif ((int)$usuario->id_rol === 2) {
            $perfil = $conexion->prepare('SELECT id_medico FROM medicos WHERE id_usuario=:usuario');
            $perfil->execute([':usuario' => (int)$usuario->id_usuario]);
            $idMedico = (int)$perfil->fetchColumn();
            if (!$idMedico) responderError('La cuenta no tiene un perfil médico asociado.', 403);
            $sql .= ' WHERE c.id_medico=:medico';
            $p[':medico'] = $idMedico;
        } elseif (isset($_GET['id_medico'])) {
            $sql .= ' WHERE c.id_medico=:medico';
            $p[':medico'] = (int)$_GET['id_medico'];
        }
        $s = $conexion->prepare($sql . ' ORDER BY c.fecha_hora DESC');
        $s->execute($p);
        responder(['success' => true, 'data' => $s->fetchAll()]);
    }
    $d = leerJson();
    if ($metodo === 'POST') {
        $usuario = verificarRol([3]);
        requerirCampos($d, ['id_medico', 'id_sala', 'fecha_hora']);
        $paciente = $conexion->prepare('SELECT id_paciente FROM pacientes WHERE id_usuario=:usuario');
        $paciente->execute([':usuario' => (int)$usuario->id_usuario]);
        $idPaciente = (int)$paciente->fetchColumn();
        if (!$idPaciente) responderError('La cuenta no tiene un perfil de paciente asociado.', 403);
        foreach ([['medicos', 'id_medico'], ['salas', 'id_sala']] as [$t, $c]) if (!existe($conexion, $t, $c, (int)$d[$c])) responderError("El {$c} indicado no existe.");
        $fecha = str_replace('T', ' ', $d['fecha_hora']);
        validarDisponibilidad($conexion, (int)$d['id_medico'], $fecha);
        $s = $conexion->prepare("SELECT 1 FROM citas WHERE fecha_hora=:fecha AND estado <> 'CANCELADA' AND (id_medico=:medico OR id_sala=:sala)");
        $s->execute([':fecha' => $fecha, ':medico' => (int)$d['id_medico'], ':sala' => (int)$d['id_sala']]);
        if ($s->fetch()) responderError('El medico o la sala ya tienen una cita en esa fecha y hora.', 409);
        $s = $conexion->prepare('INSERT INTO citas (id_paciente,id_medico,id_sala,fecha_hora,motivo_consulta,estado) VALUES (:paciente,:medico,:sala,:fecha,:motivo,"PENDIENTE")');
        $s->execute([':paciente' => $idPaciente, ':medico' => (int)$d['id_medico'], ':sala' => (int)$d['id_sala'], ':fecha' => $fecha, ':motivo' => $d['motivo_consulta'] ?? null]);
        responder(['success' => true, 'id_cita' => (int)$conexion->lastInsertId()], 201);
    }
    if ($metodo === 'PUT') {
        $usuario = verificarRol([1, 2, 3]);
        requerirCampos($d, ['id_cita']);

        $idCita = (int)$d['id_cita'];
        $consulta = $conexion->prepare('SELECT id_cita, id_paciente, id_medico, id_sala, estado FROM citas WHERE id_cita=:id');
        $consulta->execute([':id' => $idCita]);
        $cita = $consulta->fetch();
        if (!$cita) responderError('La cita indicada no existe.', 404);
        autorizarGestionCita($conexion, $usuario, $cita);

        if (isset($d['fecha_hora'])) {
            if (!in_array($cita['estado'], ['PENDIENTE', 'CONFIRMADA'], true)) responderError('Solo se pueden reprogramar citas pendientes o confirmadas.', 409);
            $fecha = str_replace('T', ' ', $d['fecha_hora']);
            validarDisponibilidad($conexion, (int)$cita['id_medico'], $fecha);
            $conflicto = $conexion->prepare("SELECT 1 FROM citas WHERE id_cita<>:id AND fecha_hora=:fecha AND estado <> 'CANCELADA' AND (id_medico=:medico OR id_sala=:sala)");
            $conflicto->execute([':id' => $idCita, ':fecha' => $fecha, ':medico' => (int)$cita['id_medico'], ':sala' => (int)$cita['id_sala']]);
            if ($conflicto->fetchColumn()) responderError('El médico o la sala ya tienen una cita en esa fecha y hora.', 409);
            $actualizar = $conexion->prepare('UPDATE citas SET fecha_hora=:fecha, estado="PENDIENTE", updated_at=CURRENT_TIMESTAMP WHERE id_cita=:id');
            $actualizar->execute([':fecha' => $fecha, ':id' => $idCita]);
            responder(['success' => true, 'mensaje' => 'Cita reprogramada. Requiere confirmación médica.', 'estado' => 'PENDIENTE', 'fecha_hora' => $fecha]);
        }

        requerirCampos($d, ['estado']);
        $nuevoEstado = strtoupper(trim($d['estado']));

        if ((int)$usuario->id_rol === 3) {
            if (!in_array($cita['estado'], ['PENDIENTE', 'CONFIRMADA'], true) || $nuevoEstado !== 'CANCELADA') responderError('Solo puede cancelar citas pendientes o confirmadas.', 403);
        } elseif ((int)$usuario->id_rol === 2) {
            if ($cita['estado'] !== 'PENDIENTE' || !in_array($nuevoEstado, ['CONFIRMADA', 'CANCELADA'], true)) responderError('Un médico solo puede confirmar o rechazar una cita pendiente.', 403);
        } else {
            $transiciones = [
                'PENDIENTE' => ['CONFIRMADA', 'CANCELADA'],
                'CONFIRMADA' => ['COMPLETADA', 'CANCELADA']
            ];
            if (!in_array($nuevoEstado, $transiciones[$cita['estado']] ?? [], true)) {
                responderError('No se puede realizar ese cambio de estado para esta cita.', 409);
            }
        }

        $actualizar = $conexion->prepare('UPDATE citas SET estado=:estado, updated_at=CURRENT_TIMESTAMP WHERE id_cita=:id');
        $actualizar->execute([':estado' => $nuevoEstado, ':id' => $idCita]);
        responder(['success' => true, 'mensaje' => 'Estado de la cita actualizado.', 'estado' => $nuevoEstado]);
    }
    responderError('Metodo no permitido.', 405);
});
