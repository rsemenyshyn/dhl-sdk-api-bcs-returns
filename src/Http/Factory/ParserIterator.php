<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use Iterator;
use IteratorIterator;

/**
 * ParserIterator class file.
 *
 * This class represents an iterator that iterates over an iterator of strings
 * and returns their parsed form on stream.
 *
 * @author Anastaszor
 * @template T of object
 * @extends \IteratorIterator<integer, T, \Iterator<integer, T>>
 * @implements \Iterator<integer, T>
 */
class ParserIterator extends IteratorIterator implements Iterator
{

    /**
     * The parser that parses the values of the inner iterator.
     *
     * @var ParserInterface<T>
     */
    protected $_parser;

    /**
     * Builds a new ParserIterator with the given.
     *
     * @param ParserInterface<T> $parser
     * @param Iterator<integer, string|T> $iterator
     */
    public function __construct(ParserInterface $parser, Iterator $iterator)
    {
        $this->_parser = $parser;
        /** @phpstan-ignore-next-line */ /** @psalm-suppress InvalidArgument */
        parent::__construct($iterator);
    }

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
     * @see \IteratorIterator::current()
     * @phpstan-return T
     * @return object
     * @throws ParseException
     */
    public function current()
    {
        $parentcurrent = parent::current();

        /** @phpstan-ignore-next-line */ /** @psalm-suppress DocblockTypeContradiction,MixedArgument */
        if(\is_string($parentcurrent))
            $parentcurrent = $this->_parser->parse($parentcurrent);

        return $parentcurrent;
    }

}
