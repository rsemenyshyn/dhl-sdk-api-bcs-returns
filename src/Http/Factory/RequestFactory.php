<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

/**
 * RequestFactory class file.
 * 
 * This class creates requests based on the request and uri implementations
 * of the php-extended/php-http-message-psr7 library.
 * 
 * @author Anastaszor
 */
class RequestFactory implements RequestFactoryInterface
{
	
	/**
	 * {@inheritDoc}
	 * @see \Psr\Http\Message\RequestFactoryInterface::createRequest()
	 */
	public function createRequest($method, $uri)
	{
		$request = new Request();
		$request = $request->withMethod($method);
		$urifactory = new UriFactory();
		$request = $request->withUri($urifactory->createUri($uri));
		return $request;
	}
	
}
