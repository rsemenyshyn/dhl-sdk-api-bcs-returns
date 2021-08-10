<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

/**
 * UriQueryComponent class file.
 *
 * This class represents
 *
 * @author Anastaszor
 */
class UriQueryComponent {

    /**
     * Gets the query parts.
     *
     * @var array<string, array<int, string>>
     */
    protected $_parts = [];

    /**
     * Builds a new UriQueryComponent with the given query.
     *
     * @param string $query
     */
    public function __construct($query = null)
    {
        $this->absorb($query);
    }

    /**
     * {@inheritDoc}
     * @see \Stringable::__toString()
     */
    public function __toString()
    {
        $parts = [];

        foreach($this->_parts as $key => $data)
        {
            foreach($data as $datum)
            {
                $parts[] = \rawurlencode((string) $key).'='.\rawurlencode((string) $datum);
            }
        }

        return \implode('&', $parts);
    }

    /**
     * @param string $query
     * @return UriQueryComponent
     */
    public function absorb($query)
    {
        foreach(\explode('&', (string) $query) as $queryPart)
        {
            $parts = \explode('=', $queryPart, 2);

            $key = \str_replace('+', ' ', \rawurldecode($parts[0]));

            if(empty($key))
                continue;

            $value = \str_replace('+', ' ', \rawurldecode(!empty($parts[1]) ? $parts[1] : ''));

            if(isset($this->_parts[$key]))
            {
                $this->_parts[$key][] = $value;
                continue;
            }

            $this->_parts[$key] = [$value];
        }

        return $this;
    }

    /**
     * Gets whether this query component is empty.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return 0 === \count($this->_parts);
    }

}
