<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use Psr\Http\Message\ServerRequestInterface;

/**
 * ServerRequest class file.
 *
 * This class is a simple implementation of the ServerRequestInterface.
 *
 * @author Anastaszor
 */
class ServerRequest extends Request implements ServerRequestInterface {

    /**
     * The cookies which will be used to process the request.
     *
     * @var array<string, string>
     */
    protected $_cookies = [];

    /**
     * The query params which will be used to process the request.
     *
     * @var array<string, string>
     */
    protected $_query = [];

    /**
     * The query body which will be used to process the request.
     *
     * @var array<string, string>
     */
    protected $_sbody = [];

    /**
     * The uploaded files which will be used to process the request.
     *
     * @var array<string, \Psr\Http\Message\UploadedFileInterface>
     */
    protected $_files = [];

    /**
     * The additional attributes of this request.
     *
     * @var array<string, null|boolean|integer|float|string|object|array<integer|string, null|boolean|integer|float|string|object|array>>
     */
    protected $_attributes = [];

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
     * @see \Psr\Http\Message\ServerRequestInterface::getServerParams()
     * @return array<string, string>
     * @SuppressWarnings(PHPMD.Superglobals)
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function getServerParams()
    {
        global $argv, $argc;

        $params = [];
        $params = \array_merge($params, $_SERVER);
        $params = \array_merge($params, $_ENV);

        /** @psalm-suppress RedundantCondition */
        if(isset($argv) && !empty($argv))
            $params['argv'] = $argv;

        /** @psalm-suppress RedundantCondition */
        if(isset($argc) && !empty($argc))
            $params['argc'] = $argc;

        /** @psalm-suppress MixedReturnTypeCoercion */
        return $params;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\ServerRequestInterface::getCookieParams()
     * @return array<string, string>
     */
    public function getCookieParams()
    {
        return $this->_cookies;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\ServerRequestInterface::withCookieParams()
     * @param array<string, string> $cookies
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function withCookieParams(array $cookies)
    {
        $newobj = clone $this;
        $newobj->_cookies = \array_merge($this->_cookies, $cookies);

        return $newobj;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\ServerRequestInterface::getQueryParams()
     * @return array<string, string>
     */
    public function getQueryParams()
    {
        return $this->_query;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\ServerRequestInterface::withQueryParams()
     * @param array<string, string> $query
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function withQueryParams(array $query)
    {
        $newobj = clone $this;
        $newobj->_query = $query;

        return $newobj;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\ServerRequestInterface::getUploadedFiles()
     * @return array<string, \Psr\Http\Message\UploadedFileInterface>
     */
    public function getUploadedFiles()
    {
        return $this->_files;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\ServerRequestInterface::withUploadedFiles()
     * @param array<string, \Psr\Http\Message\UploadedFileInterface> $uploadedFiles
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $newobj = clone $this;
        $newobj->_files = \array_merge_recursive($this->_files, $uploadedFiles);

        return $newobj;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\ServerRequestInterface::getParsedBody()
     * @return array<string, string>
     */
    public function getParsedBody()
    {
        return $this->_sbody;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\ServerRequestInterface::withParsedBody()
     * @param array<string, string> $data
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function withParsedBody($data)
    {
        $newobj = clone $this;
        $newobj->_sbody = $data;

        return $newobj;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\ServerRequestInterface::getAttributes()
     * @return array<string, null|boolean|integer|float|string|object|array<integer|string, null|boolean|integer|float|string|object|array>>
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\ServerRequestInterface::getAttribute()
     */
    public function getAttribute($name, $default = null)
    {
        if(isset($this->_attributes[$name]))
            return $this->_attributes[$name];

        return $default;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\ServerRequestInterface::withAttribute()
     */
    public function withAttribute($name, $value)
    {
        $newobj = clone $this;
        /** @psalm-suppress MixedPropertyTypeCoercion */
        $newobj->_attributes[$name] = $value;

        return $newobj;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\ServerRequestInterface::withoutAttribute()
     */
    public function withoutAttribute($name)
    {
        if(!isset($this->_attributes[$name]))
            return $this;

        $newobj = clone $this;
        unset($newobj->_attributes[$name]);

        return $newobj;
    }

}
