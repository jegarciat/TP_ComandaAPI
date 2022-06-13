<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombre = $parametros['nombre'];
    $precio = $parametros['precio'];
    $sector = $parametros['sector'];

    $producto = new Producto();
    $producto->nombre = $nombre;
    $producto->precio = $precio;
    $producto->sector = $sector;
    $producto->crearProducto();

    $payload = json_encode(array("mensaje" => "Producto creado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos Producto por id
    $productoId = $args['idProducto'];
    $producto = Producto::obtenerProducto($productoId);
    $payload = json_encode($producto);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Producto::obtenerTodos();
    $payload = json_encode(array("listaProductos" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $productoId = $parametros['productoId'];
    $nombre = $parametros['nombre'];
    $precio = $parametros['precio'];
    $sector = $parametros['sector'];

    $producto = new Producto();
    $producto->idProducto = $productoId;
    $producto->nombre = $nombre;
    $producto->precio = $precio;
    $producto->sector = $sector;
    $respuesta = Producto::modificarProducto($producto);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Producto modificado con exito."));
    } else {
      $payload = json_encode(array("mensaje" => "Error al modificar el producto."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $productoId = $parametros['productoId'];
    $respuesta = Producto::borrarProducto($productoId);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Producto borrado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Error al borrar el producto."));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
