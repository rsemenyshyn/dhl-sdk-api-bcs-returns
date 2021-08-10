<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

/**
 * UploadedFile class file.
 *
 * This class is a simple implementation of the UploadedFileInterface.
 *
 * @author Anastaszor
 */
class UploadedFile implements UploadedFileInterface {

    /**
     * The name of the file which was given by the user for this file.
     *
     * @var string
     */
    protected $_name;

    /**
     * The actual path of this file.
     *
     * @var string
     */
    protected $_tempName;

    /**
     * The mime type which was given by the user for this file.
     *
     * @var string
     */
    protected $_type;

    /**
     * The size of the file, given by php.
     *
     * @var integer
     */
    protected $_size;

    /**
     * The php error for this file.
     *
     * @var integer
     */
    protected $_error;

    /**
     * The stream for this file.
     *
     * @var ?StreamInterface
     */
    protected $_stream;

    /**
     * Constructor of the UploadedFile. This class is instanciated by
     * the ServerRequest::collectFileRecursive() method.
     *
     * @param string $name the original name of the file being uploaded
     * @param string $tempName the path of the uploaded file on the server
     * @param string $type the MIME-type of the uploaded file (such as "image/gif")
     * @param integer $size the actual size of the uploaded file in bytes
     * @param integer $error the error code
     * @param StreamInterface $stream
     */
    public function __construct(string $name, string $tempName, string $type, int $size, int $error, $stream = null)
    {
        $this->_name = $name;
        $this->_tempName = $tempName;
        $this->_type = $type;
        $this->_size = $size;
        $this->_error = $error;
        $this->_stream = $stream;
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
     * @see \Psr\Http\Message\UploadedFileInterface::getStream()
     */
    public function getStream()
    {
        if(null === $this->_stream)
            $this->_stream = new FileStream($this->_tempName);

        return $this->_stream;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\UploadedFileInterface::moveTo()
     */
    public function moveTo($targetPath)
    {
        $dir = \dirname($targetPath);
        $realdir = \realpath($dir);
        if(false === $realdir)
        {
            $message = 'The given path "{path}" does not point to an existing directory.';
            $context = ['{path}' => $targetPath];

            throw new InvalidArgumentException(\strtr($message, $context));
        }

        if(!\is_writable($realdir))
        {
            $message = 'The given path "{path}" that points to "{real}" is not writeable.';
            $context = ['{path}' => $targetPath, '{real}' => $realdir];

            throw new InvalidArgumentException(\strtr($message, $context));
        }

        if(\UPLOAD_ERR_OK !== $this->_error)
        {
            $message = 'Impossible to move file: the uploaded file has an error : {err}';
            $context = ['{err}' => $this->_error];

            throw new RuntimeException(\strtr($message, $context));
        }

        $realpath = \str_replace($dir, $realdir, $targetPath);

        if(\is_uploaded_file($this->_tempName))
        {
            if(!\move_uploaded_file($this->_tempName, $realpath))
            {
                $message = 'Impossible to move uploaded file from {src} to {dst}.';
                $context = ['{src}' => $this->_tempName, '{dst}' => $realpath];

                throw new RuntimeException(\strtr($message, $context));
            }
        }

        if(!\is_uploaded_file($this->_tempName))
        {
            if(!\rename($this->_tempName, $realpath))
            {
                $message = 'Impossible to move file from {src} to {dst}.';
                $context = ['{src}' => $this->_tempName, '{dst}' => $realpath];

                throw new RuntimeException(\strtr($message, $context));
            }
        }

        $this->_tempName = $realpath;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\UploadedFileInterface::getSize()
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\UploadedFileInterface::getError()
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\UploadedFileInterface::getClientFilename()
     */
    public function getClientFilename()
    {
        return $this->_name;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\UploadedFileInterface::getClientMediaType()
     */
    public function getClientMediaType()
    {
        return $this->_type;
    }

}
