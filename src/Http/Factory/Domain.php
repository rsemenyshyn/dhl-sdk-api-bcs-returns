<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use \InvalidArgumentException;

/**
 * Domain class file.
 *
 * This class is a simple implementation of the DomainInterface.
 *
 * @author Anastaszor
 */
class Domain implements DomainInterface {

    /**
     * The parts of the domain. Order is :
     * www.example.com -> [0 => 'www', 1 => 'example', 2 => 'com'].
     *
     * @var array<integer, string>
     */
    protected $_parts = [];

    /**
     * Builds a new Domain with the given parts.
     *
     * @param string|array<integer, string> $parts
     * @throws InvalidArgumentException
     */
    public function __construct($parts = [])
    {
        if(\is_string($parts))
            $parts = \explode('.', $parts);

        try
        {
            foreach($parts as $k => $part)
            {
                $this->_parts[] = $this->checkPart($k, $part);
            }
        }
        catch(InvalidArgumentException $e)
        {
            $message = 'Failed to set domain "{domain}", validity check failed : {msg}';
            $context = ['{domain}' => \implode('.', $parts), '{msg}' => $e->getMessage()];

            throw new InvalidArgumentException(\strtr($message, $context), -1, $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Stringable::__toString()
     */
    public function __toString()
    {
        return $this->getCanonicalRepresentation();
    }

    /**
     * Checks whether the given part is valid.
     *
     * @param integer $partNb
     * @param string $part
     * @return string
     * @throws InvalidArgumentException
     */
    protected function checkPart(int $partNb, string $part)
    {
        // RFC1034: <label> ::= <letter> [ [ <ldh-str> ] <let-dig> ]

        $len = (int) \mb_strlen($part);
        if(0 >= $len)
        {
            $message = 'The given part nb. {k} should not be empty.';
            $context = ['{k}' => $partNb + 1];

            throw new InvalidArgumentException(\strtr($message, $context));
        }

        if(64 < $len)
        {
            $message = 'The given part nb. {k} "{domain}" should be less than 64 characters, {len} given.';
            $context = ['{k}' => $partNb + 1, '{domain}' => $part, '{len}' => $len];

            throw new InvalidArgumentException(\strtr($message, $context));
        }

        for($i = 0; $i < $len; $i++)
        {
            $chr = $part[$i];
            $ord = \ord($chr);
            // used to be only letters as the rfc1035 specifies
            // but there are real domains that begin with a digit
            if(0 === $i) // first character, only letters (or digits)
            {
                if(!$this->isLetterOrDigit($ord))
                {
                    $message = 'The given character {i} of part nb. {k} should be a letter, "{chr}" given.';
                    $context = ['{i}' => $i + 1, '{k}' => $partNb + 1, '{chr}' => $chr];

                    throw new InvalidArgumentException(\strtr($message, $context));
                }
            }

            if($len - 1 === $i) // last character
            {
                if(!$this->isLetterOrDigit($ord))
                {
                    $message = 'The given character {i} of part nb. {k} should be a letter or a digit, "{chr}" given.';
                    $context = ['{i}' => $i + 1, '{k}' => $partNb + 1, '{chr}' => $chr];

                    throw new InvalidArgumentException(\strtr($message, $context));
                }
            }

            if(!$this->isLetterOrDigitOrHyphen($ord))
            {
                $message = 'The given character {i} of part nb. {k} should be a letter, digit or hyphen, "{chr}" given.';
                $context = ['{i}' => $i + 1, '{k}' => $partNb + 1, '{chr}' => $chr];

                throw new InvalidArgumentException(\strtr($message, $context));
            }
        }

        return (string) \mb_strtolower($part);
    }

    /**
     * Gets whether the given ascii code is a letter or digit or hyphen.
     *
     * @param integer $ord
     * @return boolean
     */
    protected function isLetterOrDigitOrHyphen(int $ord)
    {
        return $this->isHyphen($ord) || $this->isLetterOrDigit($ord);
    }

    /**
     * Gets whether the given ascii code is a letter or digit.
     *
     * @param integer $ord
     * @return boolean
     */
    protected function isLetterOrDigit(int $ord)
    {
        return $this->isLetter($ord) || $this->isDigit($ord);
    }

    /**
     * Gets whether the given ascii code is a letter.
     *
     * @param integer $ord
     * @return boolean
     */
    protected function isLetter(int $ord)
    {
        return (65 <= $ord && 90 >= $ord) || (97 <= $ord && 122 >= $ord);
    }

    /**
     * Gets whether the given ascii code is a digit.
     *
     * @param integer $ord
     * @return boolean
     */
    protected function isDigit(int $ord)
    {
        return 48 <= $ord && 57 >= $ord;
    }

    /**
     * Gets whether the given ascii code is an hyphen.
     *
     * @param integer $ord
     * @return boolean
     */
    protected function isHyphen(int $ord)
    {
        return 45 === $ord;
    }

    /**
     * {@inheritDoc}
     * @see \PhpExtended\Domain\DomainInterface::isEmpty()
     */
    public function isEmpty()
    {
        return $this->getDepths() === 0;
    }

    /**
     * {@inheritDoc}
     * @see \PhpExtended\Domain\DomainInterface::getDepths()
     */
    public function getDepths() : int
    {
        return \count($this->_parts);
    }

    /**
     * {@inheritDoc}
     * @see \PhpExtended\Domain\DomainInterface::append()
     */
    public function append(string $part)
    {
        $newParts = $this->_parts;

        try
        {
            \array_unshift($newParts, $this->checkPart(0, $part));
        }
        catch(InvalidArgumentException $e)
        {
            $message = 'Failed to append part "{part}", validity check failed : {msg}';
            $context = ['{part}' => $part, '{msg}' => $e->getMessage()];

            throw new InvalidArgumentException(\strtr($message, $context), -1, $e);
        }

        return new self($newParts);
    }

    /**
     * {@inheritDoc}
     * @see \PhpExtended\Domain\DomainInterface::getParent()
     * @throws InvalidArgumentException
     */
    public function getParent()
    {
        return new self(\array_slice($this->_parts, 1));
    }

    /**
     * {@inheritDoc}
     * @see \PhpExtended\Domain\DomainInterface::getCanonicalRepresentation()
     */
    public function getCanonicalRepresentation()
    {
        return \implode('.', $this->_parts);
    }

}
