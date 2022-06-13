<?php

class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $perfil;
    public $activo;
    public $fechaIngreso;
    public $fechaBaja;

    public function __construct() {}

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, perfil, activo, fechaIngreso) 
                                                        VALUES (:usuario, :clave, :perfil, :activo, :fechaIngreso)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
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

    public static function obtenerUsuario($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE id = :idUsuario");
        $consulta->bindValue(':idUsuario', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function obtenerUnoPorUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Usuario');
    }

    public static function modificarUsuario($usr)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $claveHash = password_hash($usr->clave, PASSWORD_DEFAULT);
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios 
                                                        SET usuario = :usuario, clave = :clave, perfil = :perfil 
                                                        WHERE id = :id");
        $consulta->bindValue(':id', $usr->id, PDO::PARAM_INT);
        $consulta->bindValue(':usuario', $usr->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $usr->perfil, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function suspenderUsuario($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios 
                                                        SET fechaBaja = :fechaBaja, activo = :activo 
                                                        WHERE id = :id");
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
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }
}