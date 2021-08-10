<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * UriFactory class file.
 *
 * This class creates uris based on the uri implementations of the
 * php-extended/php-http-message-psr7 library.
 *
 * @author Anastaszor
 */
class UriFactory implements UriFactoryInterface
{

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\UriFactoryInterface::createUri()
     */
    public function createUri(string $uri = '')
    {
        return Uri::parseFromString($uri);
    }

}
