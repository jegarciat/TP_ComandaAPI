<?php
require_once './models/Usuario.php';
require_once './models/Operacion.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
  public static function Login($request, $response, $args)
  {
      $parametros = $request->getParsedBody();
      $usr = $parametros['usuario'];
      $clave = $parametros['clave'];
      $usuario = Usuario::obtenerUsuarioPorNombre($usr);
      
      if ($usuario !== false && $usuario->clave == $clave) {
          $perfil = Usuario::obtenerPerfil($usuario->idPerfil);
          $datos = array('usuario' => $usr, 'clave' => $clave, 'idPerfil' => $usuario->idPerfil, 'idUsuario' => $usuario->idUsuario, 'idSector' => $usuario->idSector);
          $token = AutentificadorJWT::CrearToken($datos);
          $payload = json_encode(array("token" => $token, "mensaje" => "Bienvenido " . $perfil["nombre"]));
          $operacion = new Operacion();
          $operacion->Crear($usuario->idUsuario, $usuario->idSector, "Login", date("Y-m-d"));
          $operacion->crearOperacion();
      }else {
          $payload = json_encode(array('error' => 'No existe el usuario'));        
          $response = $response->withStatus(401);
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
  }

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $clave = $parametros['clave'];
    $idPerfil = $parametros['idPerfil'];
    $idSector = $parametros['idSector'];

    // Creamos el usuario
    $usr = new Usuario();
    $usr->usuario = $usuario;
    $usr->clave = $clave;
    $usr->idPerfil = $idPerfil;
    $usr->idSector = $idSector;
    $usr->fechaIngreso = date("Y-m-d H:i:s");
    $usr->crearUsuario();
    $operacion = new Operacion();
    $opUsr = Operacion::ObtenerUsuario($request);
    $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Alta de usuario", date("Y-m-d"));
    $operacion->crearOperacion();
    $payload = json_encode(array("mensaje" => "Usuario creado con exito."));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por id
    $idUsuario = $args['idUsuario'];
    $usuario = Usuario::obtenerUsuarioPorID($idUsuario);

    if ($usuario !== false) {
      $payload = json_encode($usuario);
    } else {
      $payload = json_encode(array('error' => 'No existe el usuario'));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::obtenerTodos();

    if ($lista !== false) {
      $payload = json_encode(array("listaUsuario" => $lista));
    } else {
      $payload = json_encode(array('error' => 'No hay usuarios registrados.'));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $idUsuario = $parametros['idUsuario'];
    $usuario = $parametros['usuario'];
    $clave = $parametros['clave'];
    $idPerfil = $parametros['idPerfil'];
    $idSector = $parametros['idSector'];

    $usr = new Usuario();
    $usr->idUsuario = $idUsuario;
    $usr->usuario = $usuario;
    $usr->clave = $clave;
    $usr->idPerfil = $idPerfil;
    $usr->idSector = $idSector;
    $respuesta = Usuario::modificarUsuario($usr);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Usuario modificado con exito."));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se modifico usuario", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array("mensaje" => "Error al modificar el usuario."));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $idUsuario = $parametros['idUsuario'];
    $respuesta = Usuario::borrarUsuario($idUsuario);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Usuario borrado con exito."));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se elimino un usuario", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array("mensaje" => "Error al borrar el usuario."));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function SuspenderUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $idUsuario = $parametros['idUsuario'];
    $respuesta = Usuario::suspenderUsuario($idUsuario);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Usuario suspendido con exito."));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se suspendio un usuario", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array("mensaje" => "Error al suspender el usuario."));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function DescargarCSV($request, $response, $args)
  {
    $lista = Usuario::obtenerTodos();

    if ($lista !== false) {
      $archivo = fopen("./Archivos/usuarios.csv", "w");
      if ($archivo !== false) {
        fwrite($archivo, $lista[0]->EncabezadoToCsv());
        foreach ($lista as $usuario) {
          fputcsv($archivo, (array)$usuario, ";", " ");
        }
      }
      $payload = json_encode(array("listaUsuario" => $lista));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Descargar usuarios en CSV", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array('error' => 'No hay usuarios registrados.'));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function CargarCSV($request, $response, $args)
  {
    $nombreArchivo = "./Archivos/usuarios.csv";
    $archivo = fopen($nombreArchivo, "r");
    $lista = [];

    if ($archivo !== false) {
      while (!feof($archivo)) {
        $lectura = fgetcsv($archivo, filesize($nombreArchivo), ";", " ");
        if ($lectura !== false && $lectura[0] !== "ID") {
          $usuario = new Usuario();
          $usuario->idUsuario = $lectura[0];
          $usuario->usuario = $lectura[1];
          $usuario->clave = $lectura[2];
          $usuario->idPerfil = $lectura[3];
          $usuario->idSector = $lectura[4];
          $usuario->activo = $lectura[5];
          $usuario->fechaIngreso = $lectura[6];
          $lista[] = $usuario;
        }
      }
      $payload = json_encode(array("listaUsuario" => $lista));
      $operacion = new Operacion();
      $opUsr = Operacion::ObtenerUsuario($request);
      $operacion->Crear($opUsr->idUsuario, $opUsr->idSector, "Se cargaron usuarios desde CSV", date("Y-m-d"));
      $operacion->crearOperacion();
    } else {
      $payload = json_encode(array('error' => 'No hay usuarios registrados.'));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
