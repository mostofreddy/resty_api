<?php
/**
 * Error
 *
 * PHP version 7.1+
 *
 * Copyright (c) 2018 Federico Lozada Mosto <mosto.federico@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @category  Resty/Api/Handlers
 * @package   Resty/Api/Handlers
 * @author    Federico Lozada Mosto <mosto.federico@gmail.com>
 * @copyright 2018 Federico Lozada Mosto <mosto.federico@gmail.com>
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link      http://www.mostofreddy.com.ar
 */
namespace Resty\Api\Handlers;

// PSR
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
// Api
use Resty\Api\Handlers\AbstractHandler;

/**
 * Error
 *
 * @category  Resty/Api/Handlers
 * @package   Resty/Api/Handlers
 * @author    Federico Lozada Mosto <mosto.federico@gmail.com>
 * @copyright 2018 Federico Lozada Mosto <mosto.federico@gmail.com>
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link      http://www.mostofreddy.com.ar
 */
class Error extends AbstractHandler
{
    const HTTP_STATUS = 500;
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
            $this->render($exception, $request)
        );
    }

    /**
     * Renderiza el mensaje de error
     * 
     * Usa JSON API Errors (http://jsonapi.org/format/#errors)
     *
     * @param \Throwable             $exception Excepcion
     * @param ServerRequestInterface $request   Request
     * 
     * @return array
     */
    protected function render(\Throwable $exception, ServerRequestInterface $request):array
    {
        $errors = [];
        $pointer = $request->getUri()->__toString();
        
        do {
            $errors[] = $this->renderExceptionMessage($exception, $pointer);
        } while ($this->displayErrorDetails && $exception = $exception->getPrevious());

        return ['errors' => $errors];
    }

    /**
     * Formatea cada uno de los errores de la excepcion
     * 
     * Usa JSON API Errors (http://jsonapi.org/format/#errors)
     * 
     * @param \Throwable  $exception Excepcion
     * @param string|null $pointer   Endpoint donde ocurrio el error
     * 
     * @return array
     */
    protected function renderExceptionMessage(\Throwable $exception, string $pointer = null):array
    {
        $error = [
            'id' => $this->generateUid(),
            'status' => static::HTTP_STATUS,
            'title'  => "App exception",
            'detail' => $exception->getMessage()
        ];

        if ($this->displayErrorDetails) {
            $error['source'] = [
                'pointer' => $pointer
            ];
            $error['meta'] = [
                'file'  => $exception->getFile(),
                'line'  => $exception->getFile(),
                'type'  => get_class($exception),
                'code'  => $exception->getCode(),
                'trace' => explode("\n", $exception->getTraceAsString())
            ];
        }
        return $error;
    }
}
