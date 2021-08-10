<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use RuntimeException;

/**
 * ParseException class file.
 *
 * This exception is thrown when the data given to the parser cannot be
 * interpreted as valid data to be packed into an object.
 *
 * @author Anastaszor
 */
class ParseException extends RuntimeException
{
    /**
     * The name of the target class.
     *
     * @var class-string
     */
    protected $_classname;

    /**
     * The data that failed to be parsed.
     *
     * @var ?string
     */
    protected $_data;

    /**
     * The offset where this exception occured.
     *
     * @var integer
     */
    protected $_offset;

    /**
     * Builds a new ParseException with the given target class name, data and
     * offset of failed parsing. If a message is provided, it will be appened
     * to the default message.
     *
     * @param class-string $classname
     * @param string $data
     * @param integer $offset
     * @param string $message
     * @param integer $code
     * @param Throwable $previous
     */
    public function __construct($classname, $data, $offset, $message = null, $code = null, $previous = null)
    {
        $this->_classname = $classname;
        $this->_data = $data;
        $this->_offset = $offset;

        $newmsg = 'Failed to parse "{data}" at {offset} for class {class}';
        $newmsg = \strtr($newmsg, [
            '{data}' => null === $data ? '(null)' : $data,
            '{offset}' => (string) $offset,
            '{class}' => $classname,
        ]);

        if(null !== $message)
            $newmsg .= ' : '.$message;

        if(null === $code)
            $code = -1;

        parent::__construct($newmsg, $code, $previous);
    }

    /**
     * Gets the classname in which the data wanted to be parsed to.
     *
     * @return class-string
     */
    public function getClassname()
    {
        return $this->_classname;
    }

    /**
     * Gets the data that failed to be parsed.
     *
     * @return ?string
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Gets the offset at which this exception occured.
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->_offset;
    }

}
