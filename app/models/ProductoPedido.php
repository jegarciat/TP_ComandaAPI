<?php

class ProductoPedido
{
    public $id;
    public $idPedido;
    public $idProducto;
    public $idUsuario;
    public $idEstadoPedido;
    public $cantidad;
    public $subtotal;
    public $demora;

    public function __construct() {}

    public function crearProductoPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productospedidos (idPedido, idProducto, idUsuario, idEstadoPedido, cantidad, subtotal) 
                                                        VALUES 
                                                        (:idPedido, :idProducto, :idUsuario, :idEstadoPedido, :cantidad, :subtotal)");
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':idEstadoPedido', 1, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':subtotal', $this->subtotal, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodosPorIdPedido($idPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productospedidos WHERE idPedido = :idPedido");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }

    public static function obtenerPorEstadoYSector($idEstado, $idSector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idPedido, productospedidos.idProducto, idUsuario, idEstadoPedido, cantidad, subtotal 
                                                        FROM productospedidos 
                                                        INNER JOIN productos 
                                                        WHERE productospedidos.idProducto = productos.idProducto 
                                                        AND productos.idSector = :idSector 
                                                        AND productospedidos.idEstadoPedido = :idEstado");
        $consulta->bindValue(':idEstado', $idEstado, PDO::PARAM_INT);
        $consulta->bindValue(':idSector', $idSector, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }

    public static function obtenerProductoPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productospedidos WHERE id = :idPedido");
        $consulta->bindValue(':idPedido', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('ProductoPedido');
    }

    public static function prepararPedido($idProductoPedido, $demora)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productospedidos SET idEstadoPedido = :idEstadoPedido, demora = :demora 
                                                        WHERE id = :id");
        $consulta->bindValue(':id', $idProductoPedido, PDO::PARAM_INT);
        $consulta->bindValue(':idEstadoPedido', 2, PDO::PARAM_INT);
        $consulta->bindValue(':demora', $demora, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function modificarEstado($idPedido, $idEstado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productospedidos SET idEstadoPedido = :idEstadoPedido 
                                                        WHERE idPedido = :id");
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':idEstadoPedido', $idEstado, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public function GetSubTotal()
    {
        $precioUnitario = Producto::obtenerProducto($this->idProducto)->precio;
        return $precioUnitario * $this->cantidad;
    }

    public static function obtenerProductoPorVentas($masVendido = false)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        if ($masVendido) {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT productospedidos.idProducto, COUNT(*) AS cantidad 
                                                            FROM productospedidos 
                                                            GROUP BY productospedidos.idProducto
                                                            ORDER BY cantidad DESC LIMIT 1");
        } else {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT productospedidos.idProducto, COUNT(*) AS cantidad 
                                                            FROM productospedidos 
                                                            GROUP BY productospedidos.idProducto
                                                            ORDER BY cantidad ASC LIMIT 1");
        }
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerProductoMenosVendido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.idMesa, COUNT(*) AS cantidad 
                                                        FROM pedidos 
                                                        GROUP BY pedidos.idMesa 
                                                        ORDER BY cantidad DESC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
}