<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
// require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/obtenerUsuario/{idUsuario}', \UsuarioController::class . ':TraerUno');
    $group->post('/alta', \UsuarioController::class . ':CargarUno');
    $group->put('/modificar', \UsuarioController::class . ':ModificarUno');
    $group->put('/suspender', \UsuarioController::class . ':SuspenderUno');
    $group->delete('/borrar', \UsuarioController::class . ':BorrarUno');
  });

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/obtenerProducto/{idProducto}', \ProductoController::class . ':TraerUno');
    $group->post('/alta', \ProductoController::class . ':CargarUno');
    $group->put('/modificar', \ProductoController::class . ':ModificarUno');
    $group->delete('/borrar', \ProductoController::class . ':BorrarUno');
  });

$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->get('/obtenerMesa/{idMesa}', \MesaController::class . ':TraerUno');
    $group->post('/alta', \MesaController::class . ':CargarUno');
    $group->put('/modificar', \MesaController::class . ':ModificarUno');
    $group->delete('/borrar', \MesaController::class . ':BorrarUno');
  });

$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    $group->get('/obtenerPedido/{idPedido}', \PedidoController::class . ':TraerUno');
    $group->post('/alta', \PedidoController::class . ':CargarUno');
    $group->put('/modificar', \PedidoController::class . ':ModificarUno');
    $group->delete('/borrar', \PedidoController::class . ':BorrarUno');
  });

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Slim Framework 4 PHP. Probando TP Comanda.");
    return $response;

});

$app->run();
