<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * StreamFactory class file.
 * 
 * This class creates streams based on the stream implementations of the
 * php-extended/php-http-message-psr7 library.
 * 
 * @author Anastaszor
 */
class StreamFactory implements StreamFactoryInterface
{
	
	/**
	 * {@inheritDoc}
	 * @see \Psr\Http\Message\StreamFactoryInterface::createStream()
	 */
	public function createStream($content = '')
	{
		$resource = fopen('php://temp', 'r+');
		fwrite($resource, $content);
		return new FileStream($resource, strlen($content));
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Psr\Http\Message\StreamFactoryInterface::createStreamFromFile()
	 */
	public function createStreamFromFile($filename, $mode = 'r')
	{
		return $this->createStreamFromResource(fopen($filename, $mode));
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Psr\Http\Message\StreamFactoryInterface::createStreamFromResource()
	 */
	public function createStreamFromResource($resource)
	{
		return new FileStream($resource);
	}
	
}
