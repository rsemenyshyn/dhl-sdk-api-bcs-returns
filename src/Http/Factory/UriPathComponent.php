<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

/**
 * UriPathComponent class file.
 *
 * This class manages the path part of an uri.
 *
 * @author Anastaszor
 */
class UriPathComponent {

    /**
     * The parts of the path.
     *
     * @var array<int, string>
     */
    protected $_parts = [];

    /**
     * Whether the.
     *
     * @var boolean
     */
    protected $_slashEnd = false;

    /**
     * Builds a new UriPathComponent with the given path.
     *
     * @param string $path
     */
    public function __construct($path = null)
    {
        $this->absorb($path);
    }

    /**
     * {@inheritDoc}
     * @see \Stringable::__toString()
     */
    public function __toString()
    {
        return \implode('/', \array_map('rawurlencode', $this->_parts))
            .($this->_slashEnd ? '/' : '');
    }

    /**
     * Absorb the given path fragment as relative path.
     *
     * @param string $path
     * @return UriPathComponent
     */
    public function absorb($path)
    {
        $this->_slashEnd = false;
        $path = (string) $path;

        foreach(\explode('/', $path) as $pathPart)
        {
            $part = \str_replace('+', ' ', \rawurldecode($pathPart));
            if(!empty($part))
            {
                if('.' === $pathPart)
                    continue;

                if('..' === $pathPart)
                {
                    unset($this->_parts[\count($this->_parts) - 1]);
                    continue;
                }

                $this->_parts[\count($this->_parts)] = $part;
            }
        }

        if(\count($this->_parts) > 0)
        {
            $pathlen = (int) \mb_strlen($path, '8bit');
            if(0 < $pathlen && $path[$pathlen - 1] === '/')
                $this->_slashEnd = true;
        }

        return $this;
    }

}
