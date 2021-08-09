<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Exception;

use Http\Client\Exception\HttpException;

/**
 * A detailed HTTP authentication exception.
 *
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class AuthenticationErrorException extends HttpException
{
}
