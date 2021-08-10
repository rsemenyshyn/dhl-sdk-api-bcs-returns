<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * ResponseFactory class file.
 * 
 * This class creates responses based on the response implementation of the
 * php-extended/php-http-message-psr7 library.
 * 
 * @author Anastaszor
 */
class ResponseFactory implements ResponseFactoryInterface
{
	
	/**
	 * {@inheritDoc}
	 * @see \Psr\Http\Message\ResponseFactoryInterface::createResponse()
	 */
	public function createResponse($code = 200, $reasonPhrase = '')
	{
		$response = new Response();
		$response = $response->withStatus($code, $reasonPhrase);
		return $response;
	}
	
}
