<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use InvalidArgumentException;
use Psr\Http\Message\UriFactoryInterface;

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
     * The parser of for the uris.
     *
     * @var ?UriParserInterface
     */
    protected static $_parser;

    /**
     * {@inheritDoc}
     * @see \Stringable::__toString()
     */
    public function __toString()
    {
        return static::class.'@'.\spl_object_hash($this);
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\UriFactoryInterface::createUri()
     */
    public function createUri($uri = '')
    {
        if(null === static::$_parser) {
            static::$_parser = new UriParser();
        }
        try {
            return static::$_parser->parse($uri);
        } catch(\Exception $e) {
            throw new InvalidArgumentException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

}

