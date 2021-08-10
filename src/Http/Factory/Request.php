<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Request class file.
 *
 * This class is a simple implementation of the RequestInterface.
 *
 * @author Anastaszor
 */
class Request extends Message implements RequestInterface {

    const ORIGIN_FORM = 'origin-form';
    const ABSOLUTE_FORM = 'absolute-form';
    const AUTHORITY_FORM = 'authority-form';
    const ASTERISK_FORM = 'asterisk-form';

    /**
     * The allowed verbs for this request.
     *
     * @var string[]
     */
    protected static $_allowedHttpVerbs = [
        'GET',
        'HEAD',
        'POST',
        'PUT',
        'DELETE',
        'TRACE',
        'OPTIONS',
        'CONNECT',
        'PATCH',
    ];

    /**
     * The available request targets forms, verbatim  (one of self::ORIGIN_FORM,
     * ::ABSOLUTE_FORM, ::AUTHORITY_FORM, and ::ASTERISK_FORM).
     *
     * @var string
     */
    protected $_requestTarget = self::ORIGIN_FORM;

    /**
     * The method of the request.
     *
     * @var string
     */
    protected $_method = 'GET';

    /**
     * The target uri of the request.
     *
     * @var ?UriInterface
     */
    protected $_uri;

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\RequestInterface::getRequestTarget()
     */
    public function getRequestTarget()
    {
        return $this->_requestTarget;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\RequestInterface::withRequestTarget()
     */
    public function withRequestTarget($requestTarget)
    {
        switch($requestTarget)
        {
            case self::ORIGIN_FORM:
            case self::ABSOLUTE_FORM:
            case self::AUTHORITY_FORM:
            case self::ASTERISK_FORM:
                $newobj = clone $this;
                $newobj->_requestTarget = $requestTarget;

                return $newobj;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\RequestInterface::getMethod()
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\RequestInterface::withMethod()
     */
    public function withMethod($method)
    {
        if(\in_array(\mb_strtoupper($method), Request::$_allowedHttpVerbs))
        {
            $newobj = clone $this;
            $newobj->_method = $method;

            return $newobj;
        }

        throw new InvalidArgumentException(\strtr('The given method "{name}" is not allowed, allowed methods are {list}.', [$method, \implode(', ', Request::$_allowedHttpVerbs)]));
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\RequestInterface::getUri()
     * @throws InvalidArgumentException
     */
    public function getUri()
    {
        if(null === $this->_uri)
            $this->_uri = new Uri();

        return $this->_uri;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\RequestInterface::withUri()
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        if($uri === $this->_uri)
            return $this;

        $newobj = clone $this;
        if($preserveHost && null !== $this->_uri)
        {
            try
            {
                $uri = $uri->withHost($this->_uri->getHost());
            }
            catch(InvalidArgumentException $e)
            {
                // nothing to do
            }
        }
        $newobj->_uri = $uri;

        return $newobj;
    }

}
