<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * UploadedFileFactory class file.
 * 
 * This class creates uploaded files based on the uploaded file implementation
 * of the php-extended/php-http-message-psr7 library.
 * 
 * @author Anastaszor
 */
class UploadedFileFactory implements UploadedFileFactoryInterface
{
	
	/**
	 * {@inheritDoc}
	 * @see \Psr\Http\Message\UploadedFileFactoryInterface::createUploadedFile()
	 */
	public function createUploadedFile($stream, $size = null, $error = \UPLOAD_ERR_OK, $clientFilename = null, $clientMediaType = null)
	{
		return new UploadedFile($clientFilename, $clientFilename, $clientMediaType, $size, $error, $stream);
	}
	
}
