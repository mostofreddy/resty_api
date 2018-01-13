<?php
/**
 * Api
 *
 * PHP version 7.1+
 *
 * Copyright (c) 2018 Federico Lozada Mosto <mosto.federico@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @category  Resty/Api/Http
 * @package   Resty/Api/Http
 * @author    Federico Lozada Mosto <mosto.federico@gmail.com>
 * @copyright 2018 Federico Lozada Mosto <mosto.federico@gmail.com>
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link      http://www.mostofreddy.com.ar
 */
namespace Resty\Api\Http;

// PHP
use \InvalidArgumentException;
// Slim
use Slim\Http\Response as SlimResponse;

/**
 * Api
 *
 * @category  Resty/Api/Http
 * @package   Resty/Api/Http
 * @author    Federico Lozada Mosto <mosto.federico@gmail.com>
 * @copyright 2018 Federico Lozada Mosto <mosto.federico@gmail.com>
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link      http://www.mostofreddy.com.ar
 */
class Response extends SlimResponse
{
    const ERR_INVALID_HTTP_CODE = 'Invalid http code';

    /**
     * Valida que el http code pasado sea válido
     *
     * @param int $status Http Code
     *
     * @throws \InvalidArgumentException Si el http code es inválido
     * @return true
     */
    protected function validHttpCode($status)
    {
        if (!in_array($status, array_keys(static::$messages))) {
            throw new InvalidArgumentException(static::ERR_INVALID_HTTP_CODE);
        }
        return true;
    }

    /**
     * Redefine el método write de la clase Slim\Http\Response para que se formatee en json
     *
     * @param string $data Datos a devolver
     *
     * @return Response
     */
    public function write($data)
    {
        return $this->withJson($data, 200);
    }

    /**
     * Json.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method prepares the response object to return an HTTP Json
     * response to the client.
     *
     * @param  mixed  $data   The data
     * @param  int    $status The HTTP status code.
     * @param  int    $encodingOptions Json encoding options
     * @throws \RuntimeException
     * @return static
     */
    public function withJson($data, $status = null, $encodingOptions = 0)
    {
        $encodingOptions = JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING | JSON_PRESERVE_ZERO_FRACTION;
        return parent::withJson($data, $status, $encodingOptions);
    }

    /************************************************************************
     * Shortcuts
     ***********************************************************************/

    /**
     * Shortcut: Responde 2xx
     *
     * @param mixed   $message Respuesta
     * @param integer $status  Http Code. Valores posibles: 2xx. Defecto: 200
     *
     * @throws \InvalidArgumentException Si el http code es inválido
     * @return Response
     */
    public function ok($message, int $status = 200)
    {
        $this->validHttpCode($status);
        return $this->withJson($message, $status);
    }

    /**
     * Shortcut: Aborta una transacción por algún motivo. Por defecto devuelve un http code 500
     *
     * @param mixed   $message Respuesta
     * @param integer $status  Http Code. Valores posibles: 4xx & 5xx. Defecto: 500
     *
     * @throws \InvalidArgumentException Si el http code es inválido
     * @return Response
     */
    public function abort($message, int $status = 500)
    {
        $this->validHttpCode($status);
        return $this->withJson(['errors' => $message], $status);
    }

    /**
     * Shortcut: Aborta el request por un error 404
     *
     * @param mixed $message Detalle del mensaje
     *
     * @return Response
     */
    public function abort404($message)
    {
        return $this->withJson($message, 404);
    }
}
