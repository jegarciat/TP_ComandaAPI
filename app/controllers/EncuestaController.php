<?php
require_once './models/Encuesta.php';
require_once './models/Pedido.php';
require_once './models/Operacion.php';
require_once './interfaces/IApiUsable.php';

class EncuestaController extends Encuesta implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idPedido = $parametros['idPedido'];
        $codigoMesa = $parametros['codigoMesa'];
        $puntajeMesa = $parametros['puntajeMesa'];
        $puntajeRestaurante = $parametros['puntajeRestaurante'];
        $puntajeMozo = $parametros['puntajeMozo'];
        $puntajeCocinero = $parametros['puntajeCocinero'];
        $comentarios = $parametros['comentarios'];

        if(Pedido::obtenerPedido($idPedido) !== false)
        {
            $encuesta = new Encuesta();
            $encuesta->idPedido = $idPedido;
            $encuesta->puntajeMesa = $puntajeMesa;
            $encuesta->puntajeRestaurante = $puntajeRestaurante;
            $encuesta->puntajeMozo = $puntajeMozo;
            $encuesta->puntajeCocinero = $puntajeCocinero;
            $encuesta->comentarios = $comentarios;
            $encuesta->promedio = $encuesta->ObtenerPromedio();
            $encuesta->crearEncuesta();
    
            $payload = json_encode(array("mensaje" => "Encuesta creada con exito."));
            $operacion = new Operacion();
            $opUsr = Operacion::ObtenerUsuario($request);
            $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se encuesto un pedido", date("Y-m-d"));
            $operacion->crearOperacion();
        }
        else{
            $payload = json_encode(array("mensaje" => "El pedido encuestado no existe."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $idEncuesta = $args['idEncuesta'];
        $encuesta = Encuesta::obtenerEncuesta($idEncuesta);

        if ($encuesta !== false) {
            $payload = json_encode($encuesta);
        } else {
            $payload = json_encode(array('error' => 'No existe el idPedido'));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Encuesta::obtenerTodos();

        if ($lista !== false) {
            $payload = json_encode(array("lista de Encuestas" => $lista));
        } else {
            $payload = json_encode(array('error' => 'No hay encuestas registradas.'));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerMejoresEncuestas($request, $response, $args)
    {
        $lista = Encuesta::obtenerMejoresEncuestas();

        if ($lista !== false) {
            $payload = json_encode(array("Encuestas con mejores comentarios" => $lista));
        } else {
            $payload = json_encode(array('error' => 'No hay encuestas registradas.'));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idEncuesta = $parametros['idEncuesta'];
        $puntajeMesa = $parametros['puntajeMesa'];
        $puntajeRestaurante = $parametros['puntajeRestaurante'];
        $puntajeMozo = $parametros['puntajeMozo'];
        $puntajeCocinero = $parametros['puntajeCocinero'];

        $encuesta = new Encuesta();
        $encuesta->idEncuesta = $idEncuesta;
        $encuesta->puntajeMesa = $puntajeMesa;
        $encuesta->puntajeRestaurante = $puntajeRestaurante;
        $encuesta->puntajeMozo = $puntajeMozo;
        $encuesta->puntajeCocinero = $puntajeCocinero;
        $respuesta = Encuesta::modificarEncuesta($encuesta);

        if ($respuesta > 0) {
            $payload = json_encode(array("mensaje" => "Encuesta modificada con exito."));
            $operacion = new Operacion();
            $opUsr = Operacion::ObtenerUsuario($request);
            $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se modifico una encuesta", date("Y-m-d"));
            $operacion->crearOperacion();
        } else {
            $payload = json_encode(array("mensaje" => "Error al modificar la encuesta."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idEncuesta = $parametros['idEncuesta'];
        $respuesta = Encuesta::borrarEncuesta($idEncuesta);

        if ($respuesta > 0) {
            $payload = json_encode(array("mensaje" => "Encuesta borrada con exito."));
            $operacion = new Operacion();
            $opUsr = Operacion::ObtenerUsuario($request);
            $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se elimino una encuesta", date("Y-m-d"));
            $operacion->crearOperacion();
        } else {
            $payload = json_encode(array("mensaje" => "Error al borrar la encuesta."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function DescargarCSV($request, $response, $args)
    {
      $lista = Encuesta::obtenerTodos();
  
      if ($lista !== false) {
        $archivo = fopen("./Archivos/encuestas.csv", "w");
        if ($archivo !== false) {
          fwrite($archivo, $lista[0]->EncabezadoToCsv());
          foreach ($lista as $Encuesta) {
            fputcsv($archivo, (array)$Encuesta, ";", " ");
          }
        }
        $payload = json_encode(array("listaEncuestas" => $lista));
        $operacion = new Operacion();
        $opUsr = Operacion::ObtenerUsuario($request);
        $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se descargaron encuestas en CSV", date("Y-m-d"));
        $operacion->crearOperacion();
      } else {
        $payload = json_encode(array('error' => 'No hay encuestas registrados.'));
      }
  
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
  
    public function CargarCSV($request, $response, $args)
    {
      $nombreArchivo = "./Archivos/encuestas.csv";
      $archivo = fopen($nombreArchivo, "r");
      $lista = [];
  
      if ($archivo !== false) {
        while (!feof($archivo)) {
          $lectura = fgetcsv($archivo, filesize($nombreArchivo), ";", " ");
          if ($lectura !== false && $lectura[0] !== "ID") {
            $encuesta = new Encuesta();
            $encuesta->idEncuesta = $lectura[0];
            $encuesta->idPedido = $lectura[1];
            $encuesta->puntajeMesa = $lectura[2];
            $encuesta->puntajeRestaurante = $lectura[3];
            $encuesta->puntajeMozo = $lectura[4];
            $encuesta->puntajeCocinero = $lectura[5];
            $encuesta->comentarios = $lectura[6];
            $encuesta->promedio = $lectura[7];
            $lista[] = $encuesta;
          }
        }
        $payload = json_encode(array("listaEncuestas" => $lista));
        $operacion = new Operacion();
        $opUsr = Operacion::ObtenerUsuario($request);
        $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se cargaron encuestas en CSV", date("Y-m-d"));
        $operacion->crearOperacion();
      } else {
        $payload = json_encode(array('error' => 'No hay encuestas registrados.'));
      }
  
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
}
