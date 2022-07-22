<?php

class Operacion
{
    public $id;
    public $idUsuario;
    public $idSector;
    public $descripcion;
    public $fecha;

    public function __construct()
    {
    }

    public function Crear($idUsuario, $idSector, $descripcion, $fecha)
    {
        $this->idUsuario = $idUsuario;
        $this->idSector = $idSector;
        $this->descripcion = $descripcion;
        $this->fecha = $fecha;
    }

    public function crearOperacion()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO operaciones (idUsuario, idSector, descripcion, fecha) 
                                                        VALUES (:idUsuario, :idSector, :descripcion, :fecha)");
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':idSector', $this->idSector, PDO::PARAM_INT);
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM operaciones");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Operacion');
    }

    public static function obtenerOperacion($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM operacion WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Operacion');
    }

    public static function obtenerCantidadOperacionesPorSector($idSector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM operaciones WHERE idSector = :idSector");
        $consulta->bindValue(':idSector', $idSector, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Operacion');
    }

    public static function ObtenerUsuario($request)
    {
        $header = $request->getHeaderLine('Authorization');
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            try {
                $usuario = Usuario::obtenerUsuarioPorID(AutentificadorJWT::ObtenerData($token)->idUsuario);
                if ($usuario !== false) {
                    return $usuario;
                }
            } catch (Exception $e) {
                var_dump($e->getMessage());
            }
        }

        return null;
    }
}
