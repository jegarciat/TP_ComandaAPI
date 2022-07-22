<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MWValidador
{
    public function ValidarPerfilYSector(Request $request, RequestHandler $handler): Response
    {
        $parametros = $request->getParsedBody();
        $response = new Response();
        $sector = Usuario::obtenerSector($parametros['idSector']);
        $perfil = Usuario::obtenerPerfil($parametros['idPerfil']);
        $flagSector = is_array($sector) ? true : false;
        $flagPerfil = is_array($perfil) ? true : false;

        if (!$flagPerfil && !$flagSector) {
            $response->getBody()->write(json_encode(array("error" => "Acción denegada. El perfil y el sector ingresado no existen.")));
            $response = $response->withStatus(401);
        } elseif (!$flagPerfil) {
            $response->getBody()->write(json_encode(array("error" => "Acción denegada. El perfil ingresado no existe.")));
            $response = $response->withStatus(401);
        } elseif (!$flagSector) {
            $response->getBody()->write(json_encode(array("error" => "Acción denegada. El sector ingresado no existe.")));
            $response = $response->withStatus(401);
        } else {
            $response = $handler->handle($request);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ValidarSector(Request $request, RequestHandler $handler): Response
    {
        $parametros = $request->getParsedBody();
        $response = new Response();
        $sector = Usuario::obtenerSector($parametros['idSector']);
        if (!is_array($sector)) {
            $response->getBody()->write(json_encode(array("error" => "Acción denegada. El sector ingresado no existe.")));
            $response = $response->withStatus(401);
        } else {
            $response = $handler->handle($request);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ValidarEstadoMesa(Request $request, RequestHandler $handler): Response
    {
        $parametros = $request->getParsedBody();
        $response = new Response();
        $mesa = Mesa::obtenerEstado($parametros['idEstado']);
        if (!is_array($mesa)) {
            $response->getBody()->write(json_encode(array("error" => "Acción denegada. El estado ingresado no es válido.")));
            $response = $response->withStatus(401);
        } else {
            $response = $handler->handle($request);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
