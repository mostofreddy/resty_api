<?php
/**
 * Api
 *
 * PHP version 7.1+
 *
 * Copyright (c) 2018 Federico Lozada Mosto <mosto.federico@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @category  Resty/Api
 * @package   Resty/Api
 * @author    Federico Lozada Mosto <mosto.federico@gmail.com>
 * @copyright 2018 Federico Lozada Mosto <mosto.federico@gmail.com>
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link      http://www.mostofreddy.com.ar
 */
namespace Resty\Api;

// PHP
use Exception;
// PSR
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
// Slim
use Slim\App;
use Slim\Container;
use Slim\Http\Headers;
// Api
use Resty\Api\Handlers\Error;
use Resty\Api\Handlers\NotFound;
use Resty\Api\Handlers\NotAllowed;
use Resty\Api\Http\Response;

/**
 * Api
 *
 * @category  Resty/Api
 * @package   Resty/Api
 * @author    Federico Lozada Mosto <mosto.federico@gmail.com>
 * @copyright 2018 Federico Lozada Mosto <mosto.federico@gmail.com>
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link      http://www.mostofreddy.com.ar
 */
class Api extends App
{
    /**
     * Create new application
     *
     * @param ContainerInterface|array $container Either a ContainerInterface or an associative array of app settings
     * @throws InvalidArgumentException when no container is provided that implements ContainerInterface
     */
    public function __construct($container = [])
    {
        $container = $this->registerDefaultServices($container);
        parent::__construct($container);
    }

    /**
     * Define servicios para la api
     *
     * @param Container|array $container Instancia de container
     *
     * @return Container|array
     */
    protected function registerDefaultServices($container)
    {
        if (!isset($container['errorHandler'])) {
            $container['errorHandler'] = function (Container $container) {
                $error = (new Error())
                    ->setDisplayErrorDetails($container->get('settings')['displayErrorDetails']);
                return $error;
            };
        }

        if (!isset($container['phpErrorHandler'])) {
            $container['phpErrorHandler'] = function (Container $container) {
                $error = (new Error())
                    ->setDisplayErrorDetails($container->get('settings')['displayErrorDetails']);
                return $error;
            };
        }

        if (!isset($container['notFoundHandler'])) {
            $container['notFoundHandler'] = function (Container $container) {
                return (new NotFound())
                    ->setDisplayErrorDetails($container->get('settings')['displayErrorDetails']);
            };
        }

        if (!isset($container['notAllowedHandler'])) {
            $container['notAllowedHandler'] = function (Container $container) {
                return (new NotAllowed())
                    ->setDisplayErrorDetails($container->get('settings')['displayErrorDetails']);
            };
        }

        $container['response'] = function (Container $container) {
            $headers = new Headers(['Content-Type' => 'application/json;charset=utf-8']);
            $response = new Response(200, $headers);
            return $response->withProtocolVersion($container->get('settings')['httpVersion']);
        };

        return $container;
    }

    /**
     * Redefine el handler de errores para procesar excepciones con handlers personalizados
     *
     * @param Exception              $e        Excepción
     * @param ServerRequestInterface $request  Instancia de Request
     * @param ResponseInterface      $response Instancia de Response
     *
     * @return ResponseInterface
     * @throws Exception if a handler is needed and not found
     */
    protected function handleException(Exception $e, ServerRequestInterface $request, ResponseInterface $response)
    {
        $container = $this->getContainer();
        $classNameException = get_class($e);

        // Verifica si esta defindo un handler personalizado para el tipo de excepcion 
        if (array_key_exists($classNameException, $container->get('customErrorHandler'))) {
            $classNameHandler = $container->get('customErrorHandler')[$classNameException];
            $handler = (new $classNameHandler())
                ->setDisplayErrorDetails($container->get('settings')['displayErrorDetails']);

            return $handler($request, $response, $e);
        }

        return parent::handleException($e, $request, $response);
    }
}
