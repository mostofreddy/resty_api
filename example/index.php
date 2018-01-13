<?php
require '../vendor/autoload.php';
// PSR
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CustomErrorException extends \Exception
{
}

class CustomErrorHandler extends \Resty\Api\Handlers\Error
{
    /**
     * Invoca la respuesta
     *
     * @param ServerRequestInterface $request   Instancia de ServerRequestInterface
     * @param ResponseInterface      $response  Instancia de ResponseInterface
     * @param \Throwable             $exception Instancia de Throwable
     *
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, \Throwable $exception)
    {
        return $this->response(
            $response,
            ['custom_message' => $exception->getMessage()]
        );
    }
}

$config = [
    'customErrorHandler' => [
        'CustomErrorException' => 'CustomErrorHandler'
    ]
];

$app = new Resty\Api\Api($config);

$app->get('/', function ($request, $response, $args) {
    return $response->write("Welcome to Slim!");
});

$app->get('/hello[/{name}]', function ($request, $response, $args) {
    return $response->write("Hello, " . $args['name']);
})->setArgument('name', 'World!');

// Error
$app->get('/error/1', function ($request, $response, $args) {
    throw new \Exception("Error");
});

$app->get('/error/custom', function ($request, $response, $args) {
    throw new \CustomErrorException("Error custom");
});

$app->get('/error/otro', function ($request, $response, $args) {
    $result = eval("2*'7'");
    return $response->abort(['mensaje' => "Esto esta mal"]);
});

// Shorcuts
$app->get('/ok', function ($request, $response, $args) {
    return $response->ok(['mensaje' => "Esta ok!"]);
});

$app->get('/500', function ($request, $response, $args) {
    return $response->abort(['mensaje' => "Esto esta mal"]);
});

$app->get('/404', function ($request, $response, $args) {
    return $response->abort404(['mensaje' => "No se encontro"]);
});

$app->run();
