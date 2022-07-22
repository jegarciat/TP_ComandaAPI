<?php

class Usuario
{
    public $idUsuario;
    public $usuario;
    public $clave;
    public $idPerfil;
    public $idSector;
    public $activo;
    public $fechaIngreso;
    public $fechaBaja;

    public function __construct() {}

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, idPerfil, idSector, activo, fechaIngreso) 
                                                        VALUES (:usuario, :clave, :idPerfil, :idSector, :activo, :fechaIngreso)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':idPerfil', $this->idPerfil, PDO::PARAM_INT);
        $consulta->bindValue(':idSector', $this->idSector, PDO::PARAM_INT);
        $consulta->bindValue(':activo', true, PDO::PARAM_BOOL);
        $consulta->bindValue(':fechaIngreso', $this->fechaIngreso, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE activo = :activo");
        $consulta->bindValue(':activo', true, PDO::PARAM_BOOL);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuarioPorID($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE idUsuario = :idUsuario");
        $consulta->bindValue(':idUsuario', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
    
    public static function obtenerUsuarioPorNombre($usr)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usr, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function obtenerPerfil($idPerfil)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT nombre FROM perfiles WHERE id = :idPerfil");
        $consulta->bindValue(':idPerfil', $idPerfil, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerSector($idSector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT nombre FROM sectores WHERE id = :idSector");
        $consulta->bindValue(':idSector', $idSector, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerPerfiles()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM perfiles");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function modificarUsuario($usr)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios 
                                                    SET usuario = :usuario, clave = :clave, idPerfil = :idPerfil, idSector = :idSector 
                                                    WHERE idUsuario = :id");

        $claveHash = password_hash($usr->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':id', $usr->idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':usuario', $usr->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash, PDO::PARAM_STR);
        $consulta->bindValue(':idPerfil', $usr->idPerfil, PDO::PARAM_INT);
        $consulta->bindValue(':idSector', $usr->idSector, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public static function suspenderUsuario($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios 
                                                        SET fechaBaja = :fechaBaja, activo = :activo 
                                                        WHERE idUsuario = :id");
        $fecha = date("Y-m-d");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', $fecha);
        $consulta->bindValue(':activo', false, PDO::PARAM_BOOL);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public static function borrarUsuario($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM usuarios WHERE idUsuario = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public function EncabezadoToCsv()
    {
        return "ID;USUARIO;CLAVE;ID PERFIL;ID SECTOR;ACTIVO;FECHA INGRESO;\n";
    }
}