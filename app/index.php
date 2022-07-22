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
require_once './middlewares/AutentificadorJWT.php';
require_once './middlewares/MWPerfiles.php';
require_once './middlewares/MWValidador.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/EncuestaController.php';

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
$app->group('/login', function (RouteCollectorProxy $group){
  $group->post('[/]', \UsuarioController::class . ':Login');
});

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/obtenerUsuario/{idUsuario}', \UsuarioController::class . ':TraerUno');
  $group->post('/alta', \UsuarioController::class . ':CargarUno')->add(\MWValidador::class . ':ValidarPerfilYSector');
  $group->put('/modificar', \UsuarioController::class . ':ModificarUno')->add(\MWValidador::class . ':ValidarPerfilYSector');
  $group->put('/suspender', \UsuarioController::class . ':SuspenderUno');
  $group->delete('/borrar', \UsuarioController::class . ':BorrarUno');
  $group->get('/descargar', \UsuarioController::class . ':DescargarCSV');
  $group->get('/cargar', \UsuarioController::class . ':CargarCSV');
})->add(\MWPerfiles::class . ':EsSocio');

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos')->add(\MWPerfiles::class . ':EsEmpleado');
  $group->get('/obtenerProducto/{idProducto}', \ProductoController::class . ':TraerUno')->add(\MWPerfiles::class . ':EsEmpleado');
  $group->post('/alta', \ProductoController::class . ':CargarUno')->add(\MWValidador::class . ':ValidarSector')->add(\MWPerfiles::class . ':EsSocio');
  $group->put('/modificar', \ProductoController::class . ':ModificarUno')->add(\MWValidador::class . ':ValidarSector')->add(\MWPerfiles::class . ':EsSocio');
  $group->delete('/borrar', \ProductoController::class . ':BorrarUno')->add(\MWPerfiles::class . ':EsSocio');
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos')->add(\MWPerfiles::class . ':EsEmpleado');
  $group->get('/obtenerMesa/{idMesa}', \MesaController::class . ':TraerUno')->add(\MWPerfiles::class . ':EsEmpleado');
  $group->post('/alta', \MesaController::class . ':CargarUno')->add(\MWValidador::class . ':ValidarEstadoMesa')->add(\MWPerfiles::class . ':EsEmpleado');
  $group->put('/modificar', \MesaController::class . ':ModificarUno')->add(\MWValidador::class . ':ValidarEstadoMesa')->add(\MWPerfiles::class . ':EsEmpleado');
  $group->delete('/borrar', \MesaController::class . ':BorrarUno')->add(\MWPerfiles::class . ':EsSocio');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(\MWPerfiles::class . ':EsSocio');
  $group->get('/pendientes/{idSector}', \PedidoController::class . ':TraerPendientesPorSector')->add(\MWPerfiles::class . ':EsEmpleado');
  $group->get('/paraServir/{idSector}', \PedidoController::class . ':TraerListosPorSector')->add(\MWPerfiles::class . ':EsEmpleado');
  $group->post('/alta', \PedidoController::class . ':CargarUno')->add(\MWPerfiles::class . ':EsMozo');
  $group->post('/alta/subirImagen', \PedidoController::class . ':AgregarImagen')->add(\MWPerfiles::class . ':EsMozo');
  $group->put('/preparar', \PedidoController::class . ':ModificarUno')->add(\MWPerfiles::class . ':EsEmpleado');
  $group->put('/terminarPedidos', \PedidoController::class . ':TerminarPedidos')->add(\MWPerfiles::class . ':EsEmpleado');
  $group->put('/entregar', \PedidoController::class . ':EntregarPedidos')->add(\MWPerfiles::class . ':EsMozo');
  $group->put('/cobrar', \PedidoController::class . ':CobrarPedido')->add(\MWPerfiles::class . ':EsMozo');
  $group->put('/cerrarMesa', \MesaController::class . ':CerrarMesa')->add(\MWPerfiles::class . ':EsSocio');
  $group->delete('/borrar', \PedidoController::class . ':BorrarUno')->add(\MWPerfiles::class . ':EsSocio');
  $group->get('/cartaPDF', \ProductoController::class . ':DescargarPDF')->add(\MWPerfiles::class . ':EsMozo');
});

$app->group('/clientes', function (RouteCollectorProxy $group){
  $group->get('/obtenerPedido/{idPedido}/{codigo_mesa}', \PedidoController::class . ':TraerUno');
  $group->post('/entregarEncuesta', \EncuestaController::class . ':CargarUno');
});

$app->group('/administracion', function (RouteCollectorProxy $group){
  $group->get('/descargar', \EncuestaController::class . ':DescargarCSV');
  $group->get('/cargar', \EncuestaController::class . ':CargarCSV');
  //Estadisticas
  $group->get('/mejoresEncuestas', \EncuestaController::class . ':TraerMejoresEncuestas');
  $group->get('/mesasMasUsada', \MesaController::class . ':TraerMesasMasUsadas');
  $group->get('/entregadosTarde', \PedidoController::class . ':TraerEntregadosTarde');
  $group->get('/masVendido', \PedidoController::class . ':TraerMasVendido');
  $group->get('/menosVendido', \PedidoController::class . ':TraerMenosVendido');
})->add(\MWPerfiles::class . ':EsSocio');

$app->get('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("TP Comanda Jorge García - Programación 3.");
  return $response;
});

$app->run();
