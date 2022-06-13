<?php
class Archivos
{
    public static function GuardarImagenPedido($destino, $objeto)
    {
        $nombreCompleto = $_FILES["imagenPedido"]["name"];
        //var_dump($nombreCompleto);
        $arrayNombre = explode(".", $nombreCompleto);
        $extensionImg = $arrayNombre[1];
        $fechaPedido = date("Y-m-d");
        $objFormateado = $objeto->nombre_cliente . "_" . $objeto->idMesa . "_" . $fechaPedido;
        //var_dump($objFormateado);
        $nombreFinal = $objFormateado . "." . $extensionImg;
        //var_dump($nombreFinal);
        $destino .= $nombreFinal;
        //var_dump($destino);
        move_uploaded_file($_FILES["imagenPedido"]["tmp_name"], $destino);
        return $nombreFinal;
    }
}
?>