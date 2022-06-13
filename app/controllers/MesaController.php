<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigo = $parametros['codigo'];
    $estado = $parametros['estado'];

    // Creamos el Mesa
    $mesa = new Mesa();
    $mesa->codigo = $codigo;
    $mesa->estado = $estado;
    $mesa->crearMesa();

    $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos Mesa por id
    $mesaId = $args['idMesa'];
    $mesa = Mesa::obtenerMesa($mesaId);

    if($mesa !== false)
    {
      $payload = json_encode($mesa);
    }
    else{
      $payload = json_encode(array("mensaje" => "No existe la mesa"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Mesa::obtenerTodos();
    $payload = json_encode(array("listaMesas" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $mesaId = $parametros['mesaId'];
    $estado = $parametros['estado'];
    $respuesta = Mesa::modificarMesa($mesaId, $estado);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Mesa modificar con exito."));
    } else {
      $payload = json_encode(array("mensaje" => "Error al modificar la mesa."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $mesaId = $parametros['mesaId'];
    $respuesta = Mesa::borrarMesa($mesaId);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Mesa borrada con exito."));
    } else {
      $payload = json_encode(array("mensaje" => "Error al borrar la mesa."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
