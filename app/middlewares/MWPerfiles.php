<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MWPerfiles
{
    public function EsSocio(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            try {
                $data = AutentificadorJWT::ObtenerData($token);
                if ($data->idPerfil == 2 || $data->idPerfil == 1) {
                    $response = $handler->handle($request);
                } else {
                    $response->getBody()->write(json_encode(array("error" => "Acceso denegado. Solo los socios y administradores tienen acceso.")));
                    $response = $response->withStatus(401);
                }
            } catch (Exception $e) {
                $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            }
        } else {
            $response->getBody()->write(json_encode(array("error" => "Falta ingresar el token")));
            $response = $response->withStatus(401);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function EsEmpleado(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            try {
                $data = AutentificadorJWT::ObtenerData($token);
                $perfiles = Usuario::obtenerPerfiles();
                $idPerfiles = [];
                foreach ($perfiles as $value) {
                    $idPerfiles[] = $value["id"];
                }
                if (in_array($data->idPerfil, $idPerfiles)) {
                    $response = $handler->handle($request);
                } else {
                    $response->getBody()->write(json_encode(array("error" => "Acceso denegado. Solo los empleados tienen acceso.")));
                    $response = $response->withStatus(401);
                }
            } catch (Exception $e) {
                $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            }
        } else {
            $response->getBody()->write(json_encode(array("error" => "Falta ingresar el token")));
            $response = $response->withStatus(401);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function EsMozo(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $response = new Response();
        if (!empty($header))
        {
            $token = trim(explode("Bearer", $header)[1]);
            try {
                $data = AutentificadorJWT::ObtenerData($token);
                if ($data->idPerfil == 3 || $data->idPerfil == 2 || $data->idPerfil == 1)
                {
                    $response = $handler->handle($request);
                }
                else
                {
                    $response->getBody()->write(json_encode(array("error" => "Acceso denegado. Solo los mozos, socios y administradores tienen acceso.")));
                    $response = $response->withStatus(401);
                }
            } catch (Exception $e) {
                $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            }
        }
        else
        {
            $response->getBody()->write(json_encode(array("error" => "Falta ingresar el token")));
            $response = $response->withStatus(401);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}