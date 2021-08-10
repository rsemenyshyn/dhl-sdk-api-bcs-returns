<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use \Iterator;

/**
 * ParserInterface interface file.
 *
 * This interface represents a single parser capabilities. A parser is an object
 * that transforms strings or streams of data into object form.
 *
 * @author Anastaszor
 * @template T of object
 */
interface ParserInterface {

    /**
     * Parses the given data into the object it represents. If the parsing
     * cannot be done, then a ParseException is thrown.
     *
     * @param string $data
     * @phpstan-return T
     * @return object
     * @throws ParseException
     */
    public function parse($data);

    /**
     * Parses the given datas into the objects they represent. If one of the
     * given datas cannot be parsed, then a ParseException is thrown.
     *
     * @param string[] $datas
     * @phpstan-return T[]
     * @return object[]
     * @throws ParseException
     */
    public function parseAll(array $datas);

    /**
     * Gets an iterator that parses one element after another. If this iterator
     * encounters a value that cannot be parsed, then a ParseException MUST be
     * thrown (usually during the next() step).
     *
     * @param Iterator<string> $dataIterator
     * @return Iterator<T>
     */
    public function parseIterator(Iterator $dataIterator);

}
