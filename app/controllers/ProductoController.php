<?php
require_once './models/Producto.php';
require_once './models/Usuario.php';
require_once './models/Operacion.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombre = $parametros['nombre'];
    $precio = $parametros['precio'];
    $idSector = $parametros['idSector'];

    $producto = new Producto();
    $producto->nombre = $nombre;
    $producto->precio = $precio;
    $producto->idSector = $idSector;
    $producto->crearProducto();

    $payload = json_encode(array("mensaje" => "Producto creado con exito"));
    $operacion = new Operacion();
    $opUsr = Operacion::ObtenerUsuario($request);
    $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se agrego un nuevo producto", date("Y-m-d"));
    $operacion->crearOperacion();

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos Producto por id
    $idProducto = $args['idProducto'];
    $producto = Producto::obtenerProducto($idProducto);
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

    $idProducto = $parametros['idProducto'];
    $nombre = $parametros['nombre'];
    $precio = $parametros['precio'];
    $idSector = $parametros['idSector'];

    $producto = new Producto();
    $producto->idProducto = $idProducto;
    $producto->nombre = $nombre;
    $producto->precio = $precio;
    $producto->idSector = $idSector;
    $respuesta = Producto::modificarProducto($producto);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Producto modificado con exito."));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se modifico un producto", date("Y-m-d"));
      $operacion->crearOperacion();
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

    $idProducto = $parametros['idProducto'];
    $respuesta = Producto::borrarProducto($idProducto);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Producto borrado con exito"));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se elimino un producto", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array("mensaje" => "Error al borrar el producto."));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function DescargarPDF($request, $response, $args)
  {
    $datos = Producto::obtenerTodos();

    if ($datos) {
      $pdf = new FPDF();
      $pdf->AddPage();
      $pdf->SetFont('Arial', 'B', 20);
      $pdf->Cell(0, 20, 'Carta de Productos', "TB", 2, 'C');
      $pdf->Image('Archivos/carta.png', 10, 13, 13);
      $pdf->Image('Archivos/carta.png', 187, 13, 13);
      $pdf->Ln(3);

      $header = array('ID', 'NOMBRE', 'PRECIO', 'SECTOR');
      $pdf->SetFont('Arial', 'B', 10);
    
      // Anchuras de las columnas
      $w = array(28, 68, 48, 48);
      // Cabecera
      for ($i = 0; $i < count($header); $i++)
        $pdf->Cell($w[$i], 10, $header[$i], 1, 0, 'C');
      $pdf->Ln();
      // Datos
      foreach ($datos as $row) {
        $sector = Usuario::obtenerSector($row->idSector);
        $pdf->Cell($w[0], 10, $row->idProducto, 1, 0, 'C');
        $pdf->Cell($w[1], 10, $row->nombre, 1, 0, 'C');
        $pdf->Cell($w[2], 10, $row->precio, 1, 0, 'C');
        $pdf->Cell($w[3], 10, $sector["nombre"], 1, 0, 'C');
        $pdf->Ln();
      }

      $pdf->Output('F', './Archivos/' . 'productos' . '.pdf');
      $payload = json_encode(array("mensaje" => 'Carta de productos generada en PDF'));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se descargaron productos en PDF", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array("error" => 'No se pudo generar el archivo'));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
