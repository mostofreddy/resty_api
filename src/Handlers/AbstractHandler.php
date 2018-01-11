<?php
/**
 * AbstractHandler
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

/**
 * AbstractHandler
 *
 * @category  Resty/Api/Handlers
 * @package   Resty/Api/Handlers
 * @author    Federico Lozada Mosto <mosto.federico@gmail.com>
 * @copyright 2018 Federico Lozada Mosto <mosto.federico@gmail.com>
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link      http://www.mostofreddy.com.ar
 */
abstract class AbstractHandler
{
    const HTTP_STATUS = 500;
    /**
     * Indica si se muestra todo el detalle del error o no
     * @var boolean
     */
    protected $displayErrorDetails = false;

    protected $uidLength = 32;

    /**
     * Setea si se muestra el detalle del error o no
     *
     * @param bool $displayErrorDetails True|False
     *
     * @return self
     */
    public function setDisplayErrorDetails(bool $displayErrorDetails):self
    {
        $this->displayErrorDetails = $displayErrorDetails;
        return $this;
    }

    /**
     * Transforma la respuesta en json
     *
     * @param ResponseInterface $response Instancia de ResponseInterface
     * @param mixed             $output   String de respuesta en formato JSON
     *
     * @return ResponseInterface
     */
    protected function response(ResponseInterface $response, $output):ResponseInterface
    {
        return $response->withJson(
            $output,
            static::HTTP_STATUS,
            JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING | JSON_PRESERVE_ZERO_FRACTION
        );
    }

    /**
     * Generea una cadena de bytes aleatorios para ser usados como UID
     * 
     * @return string
     */
    protected function generateUid():string
    {
        return bin2hex(random_bytes($this->uidLength));
    }
}
