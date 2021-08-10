<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use \Iterator;
use \ReflectionClass;


/**
 * AbstractParser class file.
 *
 * This class represents a generic parser that must be extended.
 *
 * @author Anastaszor
 * @template T of object
 * @implements ParserInterface<T>
 */
abstract class AbstractParser implements ParserInterface
{

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
     * @see \PhpExtended\Parser\ParserInterface::parseAll()
     */
    public function parseAll(array $datas)
    {
        $parsed = [];

        foreach($datas as $datum)
            $parsed[] = $this->parse($datum);

        return $parsed;
    }

    /**
     * {@inheritDoc}
     * @see \PhpExtended\Parser\ParserInterface::parseIterator()
     */
    public function parseIterator(Iterator $dataIterator)
    {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        return new ParserIterator($this, $dataIterator);
    }

    /**
     * Gets the names of the tokens from their constant values.
     *
     * @param integer[] $tokenValues
     * @return string
     */
    public function getTokenNameList(array $tokenValues)
    {
        $names = [];

        foreach($tokenValues as $tokenValue)
        {
            $names[] = $this->getTokenName($tokenValue);
        }

        return \implode(', ', $names);
    }

    /**
     * Gets the name of the token from its constant value.
     *
     * @param ?integer $tokenValue
     * @return string
     */
    public function getTokenName($tokenValue)
    {
        $rclass = new ReflectionClass($this);

        foreach($rclass->getConstants() as $name => $value)
        {
            if($value === $tokenValue)
                return $name;
        }

        return 'T_UNKNOWN';
    }

}
