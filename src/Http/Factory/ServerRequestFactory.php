<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * ServerRequestFactory class file.
 * 
 * This class creates server requests based on the request and uri
 * implementations of the php-extended/php-http-message-psr7 library.
 * 
 * @author Anastaszor
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{
	
	/**
	 * {@inheritDoc}
	 * @see \Psr\Http\Message\ServerRequestFactoryInterface::createServerRequest()
	 */
	public function createServerRequest(string $method, $uri, array $serverParams = [])
	{
		$sr = new ServerRequest();
		if(isset($serverParams['_COOKIE']) && is_array($serverParams['_COOKIE']))
		{
			$sr = $sr->withCookieParams($serverParams['_COOKIE']);
			unset($serverParams['_COOKIE']);
		}
		if(isset($serverParams['_GET']) && is_array($serverParams['_GET']))
		{
			$sr = $sr->withQueryParams($serverParams['_GET']);
			unset($serverParams['_GET']);
		}
		if(isset($serverParams['_POST']) && is_array($serverParams['_POST']))
		{
			$sr = $sr->withBody($serverParams['_POST']);
			unset($serverParams['_POST']);
		}
		if(isset($serverParams['_FILES']) && is_array($serverParams['_FILES']))
		{
			$sr = $sr->withUploadedFiles(self::collectFiles($serverParams['_FILES']));
			unset($serverParams['_FILES']);
		}
		foreach($serverParams as $name => $value)
		{
			$sr = $sr->withAttribute($name, $value);
		}
		return $sr;
	}
	
	/**
	 * Processes all the possible files.
	 * 
	 * @param array $files
	 * @return \PhpExtended\HttpMessage\UploadedFile[]
	 */
	protected static function collectFiles(array $files)
	{
		$objects = array();
		foreach($files as $class => $info)
			$objects[$class] = self::collectFilesRecursive($class, $info['name'], $info['tmp_name'], $info['type'], $info['size'], $info['error']);
		
		return $objects;
	}
	
	/**
	 * Processes incoming files.
	 *
	 * @param string $key key for identifiing uploaded file
	 * @param mixed $names file names provided by PHP
	 * @param mixed $tmp_names temporary file names provided by PHP
	 * @param mixed $types filetypes provided by PHP
	 * @param mixed $sizes file sizes provided by PHP
	 * @param mixed $errors uploading issues provided by PHP
	 * @return \PhpExtended\HttpMessage\UploadedFile[]
	 */
	protected static function collectFilesRecursive($key, $names, $tmp_names, $types, $sizes, $errors)
	{
		$uffactory = new UploadedFileFactory();
		$stfactory = new StreamFactory();
		$objects = array();
		if(is_array($names))
		{
			foreach($names as $item => $name)
			{
				unset($name);
				$objects[$key][$item] = self::collectFilesRecursive($item, $names[$item], $tmp_names[$item], $types[$item], $sizes[$item], $errors[$item]);
			}
		}
		else
			$objects[$key] = $uffactory->createUploadedFile($stfactory->createStreamFromFile($tmp_names), $sizes, $errors, $tmp_names, $types);
		
		return $objects;
	}
	
}
