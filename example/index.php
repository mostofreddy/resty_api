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
    $response->write("Welcome to Slim!");
    return $response;
});

$app->get('/hello[/{name}]', function ($request, $response, $args) {
    $response->write("Hello, " . $args['name']);
    return $response;
})->setArgument('name', 'World!');

$app->get('/error/1', function ($request, $response, $args) {
    //throw new \Exception("error 1");
    throw new \CustomErrorException("error 1");
    return $response;
});

$app->run();
