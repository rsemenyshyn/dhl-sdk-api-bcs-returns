<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Exception;


/**
 * Class ServiceExceptionFactory
 *
 * A service exception factory to create specific exception instances.
 *
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class ServiceExceptionFactory
{
    /**
     * Create a service exception.
     *
     * @param \Throwable $exception
     * @return ServiceException
     */
    public static function create($exception)
    {
        return new ServiceException($exception->getMessage(), $exception->getCode(), $exception);
    }

    /**
     * Create a HTTP client exception.
     *
     * @param \Exception $exception
     * @return ServiceException
     */
    public static function createServiceException($exception)
    {
        if (!$exception instanceof \Exception) {
            return new ServiceException('Unknown exception occurred', 0);
        }

        return self::create($exception);
    }

    /**
     * Create a detailed service exception.
     *
     * @param \Throwable $exception
     * @return DetailedServiceException
     */
    public static function createDetailedServiceException($exception)
    {
        return new DetailedServiceException($exception->getMessage(), $exception->getCode(), $exception);
    }

    /**
     * Create an authentication exception.
     *
     * @param \Throwable $exception
     * @return AuthenticationException
     */
    public static function createAuthenticationException($exception)
    {
        return new AuthenticationException($exception->getMessage(), $exception->getCode(), $exception);
    }
}
