<?php
require_once './models/Pedido.php';
require_once './models/ProductoPedido.php';
require_once './models/Producto.php';
require_once './models/Mesa.php';
require_once './models/Archivos.php';
require_once './models/Operacion.php';
require_once './middlewares/AutentificadorJWT.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $data = json_decode(file_get_contents('php://input'), true);

    $idUsuario = $data["idUsuario"];
    $idMesa = $data["idMesa"];
    $nombre_cliente = $data["nombre_cliente"];
    $productosPedidos = $data["productosPedidos"];

    $mesa = Mesa::obtenerMesa($idMesa);
    $usuario = Usuario::obtenerUsuarioPorID($idUsuario);
    if ($mesa !== false && $usuario !== false && is_array($productosPedidos)) {
      $pedido = new Pedido();
      $pedido->idUsuario = $idUsuario;
      $pedido->idMesa = $idMesa;
      $pedido->nombre_cliente = $nombre_cliente;

      $ultimoID = $pedido->crearPedido();
      $importeTotal = 0;
      foreach ($productosPedidos as $productoPedido) {
        $auxProducto = new ProductoPedido();
        $auxProducto->idPedido = $ultimoID;
        $auxProducto->idProducto = $productoPedido["idProducto"];
        $auxProducto->idUsuario = $productoPedido["idUsuario"];
        $auxProducto->cantidad = $productoPedido["cantidad"];
        $auxProducto->subtotal = $auxProducto->GetSubTotal();
        $importeTotal += $auxProducto->subtotal;
        $auxProducto->crearProductoPedido();
      }
      Pedido::agregarImporte($ultimoID, $importeTotal);
      Mesa::modificarMesa($pedido->idMesa, 1);

      $operacion = new Operacion();
      $operacion->Crear($usuario->idUsuario, $usuario->idSector, "Se cargo un pedido", date("Y-m-d"));
      $operacion->crearOperacion();

      $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
    } else {
      $payload = json_encode(array("error" => "Datos inválidos para crear el pedido."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function AgregarImagen($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $idPedido = $parametros['idPedido'];
    $pedido = Pedido::obtenerPedido($idPedido);

    if ($_FILES["imagenPedido"]["size"] > 0) {
      $directorio = "./FotosDePedidos/";
      $ruta_imagenPedido = Archivos::GuardarImagenPedido($directorio, $pedido);
      $pedido->ruta_imagen = $ruta_imagenPedido;
      $respuesta = Pedido::subirImagen($pedido);
      if ($respuesta > 0) {
        $payload = json_encode(array("mensaje" => "Imagen subida correctamente."));
      } else {
        $payload = json_encode(array("error" => "No se subio la imagen."));
      }
    } else {
      $payload = json_encode(array("error" => "No existe la imagen."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos Pedido por id
    $idPedido = $args['idPedido'];
    $pedido = Pedido::obtenerPedido($idPedido); 
    if($pedido !== false)
    {
      if($pedido->demoraEstimada != null)
      {
        $payload = json_encode(array('mensaje' => 'Tu pedido estará listo en ' . $pedido->demoraEstimada . " minutos."));
      }
      else{
        $payload = json_encode(array('mensaje' => 'Tu pedido está pendiente.'));
      }
    }
    else{
      $payload = json_encode(array('error' => 'El pedido no existe.'));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Pedido::obtenerTodos();
    if ($lista !== false) {
      $payload = json_encode(array("listaPedidos" => $lista));
    } else {
      $payload = json_encode(array('error' => 'No hay pedidos registrados.'));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerEntregadosTarde($request, $response, $args)
  {
    $lista = Pedido::obtenerEntregadosTarde();
    if ($lista !== false) {
      $payload = json_encode(array("Pedidos que se entregaron tarde" => $lista));
    } else {
      $payload = json_encode(array('error' => 'No hay pedidos que se entregaron tarde.'));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerMasVendido($request, $response, $args)
  {
    $id = ProductoPedido::obtenerProductoPorVentas(true)["idProducto"];
    $productoMasVendido = Producto::obtenerProducto($id);
    if ($productoMasVendido !== false) {
      $payload = json_encode(array("Producto mas vendido" => $productoMasVendido));
    } else {
      $payload = json_encode(array('error' => 'No hay pedidos.'));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerMenosVendido($request, $response, $args)
  {
    $id = ProductoPedido::obtenerProductoPorVentas()["idProducto"];
    $productoMasVendido = Producto::obtenerProducto($id);
    if ($productoMasVendido !== false) {
      $payload = json_encode(array("Producto menos vendido" => $productoMasVendido));
    } else {
      $payload = json_encode(array('error' => 'No hay pedidos.'));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerPendientesPorSector($request, $response, $args)
  {
    $idSector = $args['idSector'];
    $lista = ProductoPedido::obtenerPorEstadoYSector(1, $idSector);

    if ($lista !== false && count($lista) > 0) {
      $payload = json_encode(array("lista de pendientes" => $lista));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Listar pendientes por sector", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array('mensaje' => 'No hay pedidos pendientes.'));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerListosPorSector($request, $response, $args)
  {
    $idSector = $args['idSector'];
    $lista = ProductoPedido::obtenerPorEstadoYSector(3, $idSector);

    if ($lista !== false && count($lista) > 0) {
      $payload = json_encode(array("Pedidos listos para servir" => $lista));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Listar listos por sector", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array('mensaje' => 'No hay pedidos listos para servir.'));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $idPedido = $parametros['idPedido'];
    $demoraMax = 0;

    $listaProductosPedidos = ProductoPedido::obtenerTodosPorIdPedido($idPedido);
    foreach ($listaProductosPedidos as $pedido) {
      $demoraRandom = rand(5, 30);
      ProductoPedido::prepararPedido($pedido->id, $demoraRandom);
      if($demoraRandom > $demoraMax){
        $demoraMax = $demoraRandom;
      }
    }
    $pedido = Pedido::obtenerPedido($idPedido);
    $pedido->idEstado = 2;
    $pedido->demoraEstimada = $demoraMax;
    $respuesta = Pedido::modificarPedido($pedido);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Pedido en preparación y con una demora de " . $demoraMax . " minutos."));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se puso el pedido en preparacion", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array("mensaje" => "Error al intentar cambiar el estado y agregar la demora."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TerminarPedidos($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $idPedido = $parametros['idPedido'];

    $listaProductosPedidos = ProductoPedido::obtenerTodosPorIdPedido($idPedido);
    foreach ($listaProductosPedidos as $pedido) {
      ProductoPedido::modificarEstado($idPedido, 3);
    }

    $pedido = Pedido::obtenerPedido($idPedido);
    $pedido->idEstado = 3;
    $respuesta = Pedido::modificarPedido($pedido);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Pedidos listos para entregar."));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se terminaron los pedidos", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array("mensaje" => "Error al intentar cambiar el estado de los pedidos."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function EntregarPedidos($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $idPedido = $parametros['idPedido'];
    $demoraFinal = $parametros['demoraFinal'];

    $listaProductosPedidos = ProductoPedido::obtenerTodosPorIdPedido($idPedido);
    foreach ($listaProductosPedidos as $pedido) {
      ProductoPedido::modificarEstado($idPedido, 4);
    }

    $pedido = Pedido::obtenerPedido($idPedido);
    $pedido->idEstado = 4;
    $pedido->demoraFinal = $demoraFinal;
    $respuesta = Pedido::modificarPedido($pedido);
    Mesa::modificarMesa($pedido->idMesa, 2);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Pedidos entregados."));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se entregaron pedidos", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array("mensaje" => "Error al entregar los pedidos."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function CobrarPedido($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $idPedido = $parametros['idPedido'];

    $pedido = Pedido::obtenerPedido($idPedido);
    $mesa = Mesa::obtenerMesa($pedido->idMesa);
    $mesa->importeAcumulado += $pedido->importe;
    Mesa::agregarImporte($mesa->id, $mesa->importeAcumulado);
    $respuesta = Mesa::modificarMesa($mesa->id, 3);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Pedidos cobrado."));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se cobro el pedido", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array("mensaje" => "Error al cobrar el pedido."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $idPedido = $parametros['idPedido'];
    $respuesta = Pedido::borrarPedido($idPedido);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se elimino un pedido", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array("mensaje" => "Error al borrar el pedido."));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
