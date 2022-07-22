<?php
require_once './models/Mesa.php';
require_once './models/Operacion.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigo = $parametros['codigo'];
    $idEstado = $parametros['idEstado'];

    // Creamos el Mesa
    $mesa = new Mesa();
    $mesa->codigo = $codigo;
    $mesa->idEstado = $idEstado;
    $mesa->crearMesa();

    $payload = json_encode(array("mensaje" => "Mesa creada con exito"));
    $operacion = new Operacion();
    $opUsr = Operacion::ObtenerUsuario($request);
    $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se abrio una nueva mesa", date("Y-m-d"));
    $operacion->crearOperacion();

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos Mesa por id
    $idMesa = $args['idMesa'];
    $mesa = Mesa::obtenerMesa($idMesa);

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

  public function TraerMesasMasUsadas($request, $response, $args)
  {
    $idMesa = Mesa::obtenerMasUsada()["idMesa"];
    $mesa = Mesa::obtenerMesa($idMesa);

    if($mesa !== false)
    {
      $payload = json_encode($mesa);
    }
    else{
      $payload = json_encode(array("mensaje" => "No hay mesas"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $idMesa = $parametros['idMesa'];
    $idEstado = $parametros['idEstado'];
    
    if ($idEstado != 4) {
      $respuesta = Mesa::modificarMesa($idMesa, $idEstado);

      if ($respuesta > 0) {
        $payload = json_encode(array("mensaje" => "Mesa modificar con exito."));
        $operacion = new Operacion();
        $opUsr = Operacion::ObtenerUsuario($request);
        $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se cambio el estado de la mesa", date("Y-m-d"));
        $operacion->crearOperacion();
      } else {
        $payload = json_encode(array("mensaje" => "Error al modificar la mesa."));
      }
    } else {
      $payload = json_encode(array("mensaje" => "Solo los socios pueden cerrar la mesa."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function CerrarMesa($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $idMesa = $parametros['idMesa'];
    $respuesta = Mesa::modificarMesa($idMesa, 4);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Mesa cerrada."));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se cerro una mesa", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array("mensaje" => "Error al cerrar la mesa."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $idMesa = $parametros['idMesa'];
    $respuesta = Mesa::borrarMesa($idMesa);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Mesa borrada con exito."));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se elimino una mesa", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array("mensaje" => "Error al borrar la mesa."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
