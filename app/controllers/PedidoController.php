<?php
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/Mesa.php';
require_once './models/Archivos.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $idUsuario = $parametros['idUsuario'];
    $idMesa = $parametros['idMesa'];
    $idProducto = $parametros['idProducto'];
    $nombre_cliente = $parametros['nombre_cliente'];
    $cantidad = $parametros['cantidad'];
    $demora = $parametros['demora'];
    $producto = Producto::obtenerProducto($idProducto);
    $mesa = Mesa::obtenerMesa($idMesa);
    $usuario = Usuario::obtenerUsuario($idUsuario);
    // var_dump($producto);
    // var_dump($mesa);
    // var_dump($usuario);
    if($producto !== false && $mesa !== false && $usuario !== false)
    {
      // Creamos el Pedido
      $pedido = new Pedido();
      $pedido->idUsuario = $idUsuario;
      $pedido->idMesa = $idMesa;
      $pedido->idProducto = $idProducto;
      $pedido->nombre_cliente = $nombre_cliente;
      $pedido->cantidad = $cantidad;
      $pedido->subtotal = $pedido->GetSubtotal($producto->precio);
      $pedido->demora = $demora;
      
      //var_dump($_FILES["imagenPedido"]["size"]);
      if($_FILES["imagenPedido"]["size"] > 0)
      {
        $directorio = "./FotosDePedidos/";
        $ruta_imagenPedido = Archivos::GuardarImagenPedido($directorio, $pedido);
        //var_dump($ruta_imagenPedido);
        $pedido->ruta_imagen = $ruta_imagenPedido;
      }
      //var_dump($pedido);
      $pedido->crearPedido();
      $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
    }
    else{
      $payload = json_encode(array("mensaje" => "El producto, la mesa, o el usuario no existen."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos Pedido por id
    $pedidoId = $args['idPedido'];
    $pedido = Pedido::obtenerPedido($pedidoId); 
    $payload = json_encode($pedido);
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Pedido::obtenerTodos();
    $payload = json_encode(array("listaPedidos" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $pedidoId = $parametros['pedidoId'];
    $nombre_cliente = $parametros['nombre_cliente'];
    $estado = $parametros['estado'];
    $demora = $parametros['demora'];
    
    $pedido = new Pedido();
    $pedido->id = $pedidoId;
    $pedido->nombre_cliente = $nombre_cliente;
    $pedido->estado = $estado;
    $pedido->demora = $demora;
    $respuesta = Pedido::modificarPedido($pedido);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Pedido modificado con exito."));
    } else {
      $payload = json_encode(array("mensaje" => "Error al modificar el pedido."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $pedidoId = $parametros['pedidoId'];
    $respuesta = Pedido::borrarPedido($pedidoId);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Error al borrar el pedido."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
