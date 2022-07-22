<?php

class Encuesta
{
    public $idEncuesta;
    public $idPedido;
    public $puntajeMesa;
    public $puntajeRestaurante;
    public $puntajeMozo;
    public $puntajeCocinero;
    public $comentarios;
    public $promedio;

    public function __construct()
    {
    }

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas 
                                                        (idPedido, puntajeMesa, puntajeRestaurante, puntajeMozo, puntajeCocinero, promedio, comentarios) 
                                                        VALUES 
                                                        (:idPedido, :puntajeMesa, :puntajeRestaurante, :puntajeMozo, :puntajeCocinero, :promedio, :comentarios)");
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeMesa', $this->puntajeMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeRestaurante', $this->puntajeRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeMozo', $this->puntajeMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeCocinero', $this->puntajeCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':promedio', $this->promedio, PDO::PARAM_STR);
        $consulta->bindValue(':comentarios', $this->comentarios, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function obtenerEncuesta($idEncuesta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas WHERE idEncuesta = :idEncuesta");
        $consulta->bindValue(':idEncuesta', $idEncuesta, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Encuesta');
    }

    public static function modificarEncuesta($encuesta)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE encuestas 
                                                        SET puntajeMesa = :puntajeMesa, 
                                                        puntajeRestaurante = :puntajeRestaurante,
                                                        puntajeMozo = :puntajeMozo, 
                                                        puntajeCocinero = :puntajeCocinero, 
                                                        comentarios = :comentarios, 
                                                        promedio = :promedio 
                                                        WHERE idEncuesta = :idEncuesta");
        $consulta->bindValue(':idEncuesta', $encuesta->idEncuesta, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeMesa', $encuesta->puntajeMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeRestaurante', $encuesta->puntajeRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeMozo', $encuesta->puntajeMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeCocinero', $encuesta->untajeCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':comentarios', $encuesta->comentarios, PDO::PARAM_STR);
        $consulta->bindValue(':promedio', $encuesta->promedio, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public function ObtenerPromedio()
    {
        return ($this->puntajeMesa + $this->puntajeMozo + $this->puntajeRestaurante + $this->puntajeCocinero) / 4;
    }

    public static function borrarEncuesta($idEncuesta)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM encuestas WHERE idEncuesta = :idEncuesta");
        $consulta->bindValue(':idEncuesta', $idEncuesta, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    private static function promedioMaximo()
    {
        $encuestas = Encuesta::obtenerTodos();
        $promMax = 0;

        foreach ($encuestas as $encuesta) {
            if ($encuesta->promedio > $promMax) {
                $promMax = $encuesta->promedio;
            }
        }
        return $promMax;
    }

    public static function obtenerMejoresEncuestas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas WHERE promedio = :promedio");
        $promedio = Encuesta::promedioMaximo();
        $consulta->bindValue(':promedio', $promedio, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public function EncabezadoToCsv()
    {
        return "ID;ID PEDIDO;MESA;RESTAURANTE;MOZO;COCINERO;COMENTARIOS;PROMEDIO\n";
    }
}
