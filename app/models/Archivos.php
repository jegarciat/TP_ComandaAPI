<?php
class Archivos
{
    public static function GuardarImagenPedido($destino, $objeto)
    {
        $nombreCompleto = $_FILES["imagenPedido"]["name"];
        $arrayNombre = explode(".", $nombreCompleto);
        $extensionImg = $arrayNombre[1];
        $fechaPedido = date("Y-m-d");
        $objFormateado = $objeto->nombre_cliente . "_" . $objeto->idMesa . "_" . $fechaPedido;
        $nombreFinal = $objFormateado . "." . $extensionImg;
        $destino .= $nombreFinal;
        move_uploaded_file($_FILES["imagenPedido"]["tmp_name"], $destino);
        return $nombreFinal;
    }
}
?>