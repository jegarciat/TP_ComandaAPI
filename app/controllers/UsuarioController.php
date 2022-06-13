<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $clave = $parametros['clave'];
    $perfil = $parametros['perfil'];

    // Creamos el usuario
    $usr = new Usuario();
    $usr->usuario = $usuario;
    $usr->clave = $clave;
    $usr->perfil = $perfil;
    $usr->fechaIngreso = date("Y-m-d H:i:s");
    $usr->crearUsuario();

    $payload = json_encode(array("mensaje" => "Usuario creado con exito."));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por id
    $usuarioId = $args['idUsuario'];
    $usuario = Usuario::obtenerUsuario($usuarioId);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::obtenerTodos();
    $payload = json_encode(array("listaUsuario" => $lista));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuarioId = $parametros['usuarioId'];
    $usuario = $parametros['usuario'];
    $clave = $parametros['clave'];
    $perfil = $parametros['perfil'];

    $usr = new Usuario();
    $usr->id = $usuarioId;
    $usr->usuario = $usuario;
    $usr->clave = $clave;
    $usr->perfil = $perfil;
    $respuesta = Usuario::modificarUsuario($usr);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Usuario modificado con exito."));
    } else {
      $payload = json_encode(array("mensaje" => "Error al modificar el usuario."));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuarioId = $parametros['usuarioId'];
    $respuesta = Usuario::borrarUsuario($usuarioId);

    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Usuario borrado con exito."));
    } else {
      $payload = json_encode(array("mensaje" => "Error al borrar el usuario."));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function SuspenderUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuarioId = $parametros['usuarioId'];
    $respuesta = Usuario::suspenderUsuario($usuarioId);
    if ($respuesta > 0) {
      $payload = json_encode(array("mensaje" => "Usuario suspendido con exito."));
    } else {
      $payload = json_encode(array("mensaje" => "Error al suspender el usuario."));
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
