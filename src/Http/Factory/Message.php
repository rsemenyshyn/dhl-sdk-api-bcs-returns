<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Message class file.
 *
 * This class is a simple implementation of the MessageInterface.
 *
 * @author Anastaszor
 */
abstract class Message implements MessageInterface {

    const HTTP_1_0 = '1.0';
    const HTTP_1_1 = '1.1';

    /**
     * The protocol version as string.
     *
     * @var string (e.g. "1.0" or "1.1")
     */
    protected $_protocolVersion = self::HTTP_1_1;

    /**
     * The http headers as string array.
     *
     * @var array<string, array<int, string>>
     */
    protected $_headers = [];

    /**
     * The http header keys with lowercase. The values are in the $_headers.
     *
     * // lowercase header => real header case value
     * @var array<string, string>
     */
    protected $_lkeys = [];

    /**
     * The body of the message.
     *
     * @var ?StreamInterface
     */
    protected $_body;

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
     * @see \Psr\Http\Message\MessageInterface::getProtocolVersion()
     */
    public function getProtocolVersion()
    {
        return $this->_protocolVersion;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\MessageInterface::withProtocolVersion()
     */
    public function withProtocolVersion($version)
    {
        switch($version)
        {
            case self::HTTP_1_0:
            case self::HTTP_1_1:
                if($version === $this->_protocolVersion)
                    return $this;

                $newobj = clone $this;
                $newobj->_protocolVersion = $version;

                return $newobj;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\MessageInterface::getHeaders()
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\MessageInterface::hasHeader()
     */
    public function hasHeader($name)
    {
        return isset($this->_lkeys[\mb_strtolower($name)]);
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\MessageInterface::getHeader()
     */
    public function getHeader($name)
    {
        if($this->hasHeader($name))
            return $this->_headers[$this->_lkeys[(string) \mb_strtolower($name)]];

        return [];
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\MessageInterface::getHeaderLine()
     */
    public function getHeaderLine($name)
    {
        if($this->hasHeader($name))
            return \implode(', ', $this->_headers[$this->_lkeys[(string) \mb_strtolower($name)]]);

        return '';
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\MessageInterface::withHeader()
     */
    public function withHeader($name, $value)
    {
        $newobj = clone $this;

        if(isset($newobj->_lkeys[\mb_strtolower($name)]))
            unset($newobj->_headers[$newobj->_lkeys[(string) \mb_strtolower($name)]]);

        if(!\is_array($value))
            $value = [$value];

        /** @psalm-suppress MixedPropertyTypeCoercion */
        $newobj->_headers[$name] = \array_unique($value);
        $newobj->_lkeys[(string) \mb_strtolower($name)] = $name;

        return $newobj;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\MessageInterface::withAddedHeader()
     */
    public function withAddedHeader($name, $value)
    {
        $newobj = clone $this;
        if(!\is_array($value))
            $value = [$value];

        if(!isset($newobj->_headers[$name]))
            $newobj->_headers[$name] = [];

        foreach($value as $valueElem)
        {
            if(!\in_array($valueElem, $newobj->_headers[$name]))
                $newobj->_headers[$name][] = $valueElem;
        }
        $newobj->_lkeys[(string) \mb_strtolower($name)] = $name;

        return $newobj;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\MessageInterface::withoutHeader()
     */
    public function withoutHeader($name)
    {
        $newobj = clone $this;
        if(isset($newobj->_lkeys[\mb_strtolower($name)]))
            unset($newobj->_headers[$newobj->_lkeys[(string) \mb_strtolower($name)]], $newobj->_lkeys[(string) \mb_strtolower($name)]);

        return $newobj;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\MessageInterface::getBody()
     */
    public function getBody()
    {
        if(null === $this->_body)
            $this->_body = new StringStream();

        return $this->_body;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\MessageInterface::withBody()
     */
    public function withBody(StreamInterface $body)
    {
        $newobj = clone $this;
        $newobj->_body = $body;

        return $newobj;
    }

}
