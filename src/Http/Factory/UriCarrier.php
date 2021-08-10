<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use Psr\Http\Message\UriFactoryInterface;

/**
 * UriCarrier class file.
 *
 * This class represents a carrier for uri in the building process.
 *
 * @author Anastaszor
 */
class UriCarrier {

    /**
     * The current uri.
     *
     * @var UriFactoryInterface
     */
    protected $_uri;

    /**
     * The full string of data to be parsed from the beginning.
     *
     * @var string
     */
    protected $_base;

    /**
     * The remaining data string.
     *
     * @var string
     */
    protected $_remaining;

    /**
     * The current index of the parsing.
     *
     * @var integer
     */
    protected $_index;

    /**
     * Builds a new UriCarrier with the given uri and remaining string.
     *
     * @param UriFactoryInterface $uri
     * @param string $base
     * @param string $remaining
     * @param integer $index
     */
    public function __construct($uri, string $base, string $remaining, int $index)
    {
        $this->_uri = $uri;
        $this->_base = $base;
        $this->_remaining = $remaining;
        $this->_index = $index;
    }

    /**
     * {@inheritDoc}
     * @see \Stringable::__toString()
     */
    public function __toString()
    {
        return $this->_uri->__toString().' << '.$this->_remaining;
    }

    /**
     * Gets whether this carrier has still data remaining.
     *
     * @return boolean
     */
    public function hasData()
    {
        return !empty($this->_remaining);
    }

    /**
     * Gets the current building uri.
     *
     * @return UriFactoryInterface
     */
    public function getUri()
    {
        return $this->_uri;
    }

    /**
     * Gets the base string.
     *
     * @return string
     */
    public function getBase()
    {
        return $this->_base;
    }

    /**
     * Gets the remaining string data.
     *
     * @return string
     */
    public function getRemaining()
    {
        return $this->_remaining;
    }

    /**
     * Gets the current parsing index.
     *
     * @return integer
     */
    public function getIndex()
    {
        return $this->_index;
    }

}
