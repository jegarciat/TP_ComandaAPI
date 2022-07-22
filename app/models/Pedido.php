<?php

class Pedido
{
    public $id;
    public $idUsuario;
    public $idMesa;
    public $idEstado;
    public $nombre_cliente;
    public $demoraEstimada;
    public $demoraFinal;
    public $ruta_imagen;
    public $importe;
    public $fecha;

    public function __construct() {}

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (idUsuario, idMesa, idEstado, nombre_cliente, ruta_imagen) 
                                                        VALUES 
                                                        (:idUsuario, :idMesa, :idEstado, :nombre_cliente, :ruta_imagen)");
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idEstado', 1, PDO::PARAM_INT);
        $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':ruta_imagen', $this->ruta_imagen, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerEntregadosTarde()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE demoraFinal > demoraEstimada");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE id = :idPedido");
        $consulta->bindValue(':idPedido', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function modificarPedido($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET nombre_cliente = :nombre_cliente, 
                                                        idEstado = :idEstado, demoraEstimada = :demoraEstimada, demoraFinal = :demoraFinal, importe = :importe WHERE id = :id");
        $consulta->bindValue(':id', $pedido->id, PDO::PARAM_INT);
        $consulta->bindValue(':idEstado', $pedido->idEstado, PDO::PARAM_INT);
        $consulta->bindValue(':nombre_cliente', $pedido->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':demoraEstimada', $pedido->demoraEstimada, PDO::PARAM_INT);
        $consulta->bindValue(':demoraFinal', $pedido->demoraFinal, PDO::PARAM_INT);
        $consulta->bindValue(':importe', $pedido->importe, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function subirImagen($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET ruta_imagen = :ruta WHERE id = :id");
        $consulta->bindValue(':id', $pedido->id, PDO::PARAM_INT);
        $consulta->bindValue(':ruta', $pedido->ruta_imagen, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function agregarImporte($id, $importeTotal)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET importe = :importe WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':importe', $importeTotal, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function borrarPedido($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public function GetImporteFinal($listaProductosPedidos)
    {
        $importeAcumulado = 0;

        if($listaProductosPedidos != null){
            foreach ($listaProductosPedidos as $value) {
                $importeAcumulado += $value->subtotal;
            }
        }

        return $importeAcumulado;
    }
}