<?php
/**
 * NotFound
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
 * NotFound
 *
 * @category  Resty/Api/Handlers
 * @package   Resty/Api/Handlers
 * @author    Federico Lozada Mosto <mosto.federico@gmail.com>
 * @copyright 2018 Federico Lozada Mosto <mosto.federico@gmail.com>
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link      http://www.mostofreddy.com.ar
 */
class NotFound extends AbstractHandler
{
    const HTTP_STATUS = 404;
    /**
     * Invoca la respuesta
     *
     * @param ServerRequestInterface $request  Instancia de ServerRequestInterface
     * @param ResponseInterface      $response Instancia de ResponseInterface
     *
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $this->response(
            $response,
            $this->render($request)
        );
    }

    /**
     * Renderiza el mensaje de error
     * 
     * Usa JSON API Errors (http://jsonapi.org/format/#errors)
     *
     * @param ServerRequestInterface $request Requets
     *
     * @return array
     */
    protected function render(ServerRequestInterface $request):array
    {
        return [
            'errors' => [
                [
                    'id' => $this->generateUid(),
                    'status' => static::HTTP_STATUS,
                    'title'  => 'Request not found',
                    'meta' => [
                        'http_method' => $request->getMethod(),
                        'uri' => $request->getUri()->__toString()
                    ]
                ]
            ]
        ];
    }
}
