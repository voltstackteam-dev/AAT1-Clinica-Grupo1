<?php
require_once __DIR__ . '/config/api.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/jwt.php';

$metodo = iniciarApi();

function perfilPaciente(PDO $conexion, int $idUsuario): int
{
    $consulta = $conexion->prepare('SELECT id_paciente FROM pacientes WHERE id_usuario=:usuario');
    $consulta->execute([':usuario' => $idUsuario]);
    return (int)$consulta->fetchColumn();
}

ejecutarApi(function () use ($conexion, $metodo): void {
    $base = 'SELECT 
                p.id_paciente,
                p.id_usuario,
                p.nombre,
                p.apellido,
                p.fecha_nacimiento,
                p.telefono,
                p.direccion,
                p.created_at,
                u.email,
                u.activo 
            FROM pacientes p 
            JOIN usuarios u 
            ON u.id_usuario=p.id_usuario';

    if ($metodo === 'GET') {
        $usuario = verificarRol([1, 3]);
        $parametros = [];
        $sql = $base;
        if ((int)$usuario->id_rol === 3) {
            $sql .= ' WHERE p.id_usuario=:usuario';
            $parametros[':usuario'] = (int)$usuario->id_usuario;
        } elseif (isset($_GET['id'])) {
            $sql .= ' WHERE p.id_paciente=:id';
            $parametros[':id'] = idRequerido();
        }
        $consulta = $conexion->prepare($sql . ' ORDER BY p.apellido,p.nombre');
        $consulta->execute($parametros);
        $datos = (int)$usuario->id_rol === 3 || isset($_GET['id']) ? $consulta->fetch() : $consulta->fetchAll();
        if (!$datos) responderError('Paciente no encontrado.', 404);
        responder(['success' => true, 'cantidad' => is_array($datos) ? count($datos) : 1, 'data' => $datos]);
    }
    if ($metodo === 'DELETE') {
        verificarRol([1]);
        $consulta = $conexion->prepare('DELETE FROM pacientes WHERE id_paciente=:id');
        $consulta->execute([':id' => idRequerido()]);
        if (!$consulta->rowCount()) responderError('Paciente no encontrado.', 404);
        responder(['success' => true, 'mensaje' => 'Paciente eliminado.']);
    }

    $datos = leerJson();
    requerirCampos($datos, ['nombre', 'apellido', 'fecha_nacimiento', 'telefono']);
    $parametros = [':nombre' => trim($datos['nombre']), ':apellido' => trim($datos['apellido']), ':fecha' => $datos['fecha_nacimiento'], ':telefono' => trim($datos['telefono']), ':direccion' => $datos['direccion'] ?? null];

    if ($metodo === 'POST') {
        $conexion->beginTransaction();
        try {
            if (!empty($datos['id_usuario'])) {
                verificarRol([1]);
                $idUsuario = (int)$datos['id_usuario'];
                if (!existe($conexion, 'usuarios', 'id_usuario', $idUsuario)) responderError('El usuario no existe.');
            } else {
                requerirCampos($datos, ['email', 'contrasenia']);
                if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) responderError('Email inválido.');
                $crearUsuario = $conexion->prepare('INSERT INTO usuarios (email,contrasenia,id_rol,activo) VALUES (:email,:clave,3,1)');
                $crearUsuario->execute([':email' => trim($datos['email']), ':clave' => password_hash($datos['contrasenia'], PASSWORD_DEFAULT)]);
                $idUsuario = (int)$conexion->lastInsertId();
            }
            $parametros[':usuario'] = $idUsuario;
            $crearPaciente = $conexion->prepare
                ('INSERT INTO pacientes 
                    (id_usuario,
                    nombre,
                    apellido,
                    fecha_nacimiento,
                    telefono,
                    direccion)
                     VALUES (:usuario,:nombre,:apellido,:fecha,:telefono,:direccion)');
                     
            $crearPaciente->execute($parametros);
            $conexion->commit();
            responder(['success' => true, 'id_paciente' => (int)$conexion->lastInsertId(), 'id_usuario' => $idUsuario], 201);
        } catch (Throwable $e) {
            if ($conexion->inTransaction()) $conexion->rollBack();
            throw $e;
        }
    }

    if ($metodo === 'PUT') {
        $usuario = verificarRol([1, 3]);
        $idPaciente = idRequerido();
        if ((int)$usuario->id_rol === 3) {
            if (perfilPaciente($conexion, (int)$usuario->id_usuario) !== $idPaciente) responderError('Solo puede editar su propio perfil.', 403);
            $idUsuario = (int)$usuario->id_usuario;
        } else {
            requerirCampos($datos, ['id_usuario']);
            $idUsuario = (int)$datos['id_usuario'];
            if (!existe($conexion, 'usuarios', 'id_usuario', $idUsuario)) responderError('El usuario no existe.');
        }
        $parametros[':usuario'] = $idUsuario;
        $parametros[':id'] = $idPaciente;
        $actualizar = $conexion->prepare('UPDATE pacientes SET id_usuario=:usuario,nombre=:nombre,apellido=:apellido,fecha_nacimiento=:fecha,telefono=:telefono,direccion=:direccion WHERE id_paciente=:id');
        $actualizar->execute($parametros);
        responder(['success' => true, 'mensaje' => 'Paciente actualizado.']);
    }

    responderError('Método no permitido.', 405);
});
