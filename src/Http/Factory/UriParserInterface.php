<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use Psr\Http\Message\UriFactoryInterface;

/**
 * UriParserInterface interface file.
 *
 * This parser parses unified resource identifiers (URIs).
 *
 * @author Anastaszor
 * @extends \PhpExtended\Parser\ParserInterface<UriFactoryInterface>
 */
interface UriParserInterface extends ParserInterface
{

    /**
     * {@inheritDoc}
     * @see \PhpExtended\Parser\ParserInterface::parse()
     */
    public function parse($data);

}
