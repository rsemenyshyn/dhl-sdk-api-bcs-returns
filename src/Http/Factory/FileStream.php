<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use Exception;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * FileStream class file.
 *
 * This class represents a read and write stream implementation of
 * the StreamInterface which relies on underlying files.
 *
 * @author Anastaszor
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @psalm-suppress PropertyNotSetInConstructor
 */
class FileStream implements StreamInterface {

    /**
     * The target path of the file.
     *
     * @var string
     */
    protected $_path;

    /**
     * The handle of the file at target path.
     *
     * @var ?resource
     */
    protected $_handle;

    /**
     * The total length of the file.
     *
     * @var integer
     */
    protected $_fileLength;

    /**
     * Whether the stream is in detached state. In detached state, the
     * stream is unusable.
     *
     * @var boolean
     */
    protected $_detached = false;

    /**
     * Builds a new FileStream object with the given target path.
     *
     * @param string|resource $pathOrResource
     * @param ?integer $fileLength the length of the stream
     * @throws RuntimeException if the file does not exists at the given path
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct($pathOrResource, $fileLength = null)
    {
        if(null !== $fileLength)
            $this->_fileLength = $fileLength;

        if(\is_string($pathOrResource))
        {
            $realpath = \realpath($pathOrResource);
            if(false === $realpath)
            {
                $message = 'The file path does not point to an existing file.';
                $context = ['{path}' => $pathOrResource];

                throw new RuntimeException(\strtr($message, $context));
            }

            if(!\is_file($realpath))
            {
                $message = 'The file at path "{path}" is not a file.';
                $context = ['{path}' => $realpath];

                throw new RuntimeException(\strtr($message, $context));
            }

            $this->_path = $realpath;
            if(empty($this->_fileLength) && null === $fileLength)
                $this->_fileLength = (int) \filesize($this->_path);
        }

        if(\is_resource($pathOrResource))
        {
            $this->_handle = $pathOrResource;
            /** @psalm-suppress PossiblyInvalidCast */
            $this->_path = (string) $this->getMetadata('uri');
            if(null === $fileLength)
            {
                if(empty($this->_fileLength))
                    $this->_fileLength = (int) \filesize($this->_path);
                if(empty($this->_fileLength))
                    $this->_fileLength = (int) $this->getMetadata('unread_bytes');
                if(empty($this->_fileLength))
                {
                    $stat = \fstat($this->_handle);
                    /** @phpstan-ignore-next-line */
                    $this->_fileLength = (int) (!empty($stat['size']) ? $stat['size'] : 0);
                }
            }
        }

        $this->_detached = false;
        $this->rewind();
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::__toString()
     */
    public function __toString()
    {
        if($this->_detached)
            return '';

        if(null === $this->_handle)
        {
            $res = \file_get_contents($this->_path);
            if(false === $res) return '';

            return $res;
        }

        try
        {
            $this->rewind();

            return $this->read($this->getSize());
        }
        catch(Exception $e)
        {
            return '';
        }
    }

    /**
     * Ensures that the handle exists for this stream.
     *
     * @throws RuntimeException if the handle cannot be opened or locked
     */
    protected function ensureStream()
    {
        if($this->_detached)
            throw new RuntimeException('The stream is in detached state.');

        if(null === $this->_handle)
        {
            if(!\is_file($this->_path))
            {
                $message = 'The file at path "{path}" is not a file.';
                $context = ['{path}' => $this->_path];

                throw new RuntimeException(\strtr($message, $context));
            }

            $handle = \fopen($this->_path, 'c+');
            if(false === $handle)
            {
                $message = 'Impossible to open file {path}.';
                $context = ['{path}' => $this->_path];

                throw new RuntimeException(\strtr($message, $context));
            }

            $this->_handle = $handle;
            if(!\flock($this->_handle, \LOCK_EX))
            {
                $message = 'Impossible to lock file {path}.';
                $context = ['{path}' => $this->_path];

                throw new RuntimeException(\strtr($message, $context));
            }
        }
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::close()
     */
    public function close()
    {
        if(null !== $this->_handle)
        {
            \flock($this->_handle, \LOCK_UN);
            /** @psalm-suppress InvalidPropertyAssignmentValue */
            \fclose($this->_handle);
            $this->_handle = null;
        }
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::detach()
     * @return null|resource
     */
    public function detach()
    {
        $this->_detached = true;
        $this->close();

        return null;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::getSize()
     */
    public function getSize()
    {
        return $this->_fileLength;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::tell()
     */
    public function tell()
    {
        if($this->_detached || null === $this->_handle)
            return 0;

        $res = false;
        if(null !== $this->_handle)
            $res = \ftell($this->_handle);
        if(false !== $res)
            return $res;

        throw new RuntimeException('Impossible to tell the stream position.');
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::eof()
     */
    public function eof()
    {
        if($this->_detached || null === $this->_handle)
            return true;

        try
        {
            return $this->tell() === $this->getSize();
        }
        catch(RuntimeException $e)
        {
            return true;
        }
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::isSeekable()
     */
    public function isSeekable()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::seek()
     * @param integer $offset
     * @param integer $whence
     * @throws RuntimeException
     */
    public function seek($offset, $whence = \SEEK_SET)
    {
        $this->ensureStream();
        $res = -1;
        if(null !== $this->_handle)
            $res = \fseek($this->_handle, $offset, $whence);
        if(-1 === $res)
            throw new RuntimeException('Impossible to seek the file.');
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::rewind()
     */
    public function rewind()
    {
        $this->seek(0, \SEEK_SET);
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::isWritable()
     */
    public function isWritable()
    {
        return \is_writable($this->_path);
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::write()
     */
    public function write($string)
    {
        $this->ensureStream();
        $res = false;
        if(null !== $this->_handle)
            $res = \fwrite($this->_handle, $string);
        if(false !== $res)
            return $res;
        // calculate the new length given the emplacement we're in the stream
        $this->_fileLength = \max($this->_fileLength, $this->tell() + (int) \mb_strlen($string, '8bit'));

        throw new RuntimeException('Impossible to write to file.');
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::isReadable()
     */
    public function isReadable()
    {
        return \is_readable($this->_path);
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::read()
     */
    public function read($length)
    {
        $this->ensureStream();
        if(0 >= $length)
            return '';
        $res = false;
        if(null !== $this->_handle)
            $res = \fread($this->_handle, $length);
        if(false === $res)
        {
            $message = 'Impossible to read the next {n} bytes.';
            $context = ['{n}' => $length];

            throw new RuntimeException(\strtr($message, $context));
        }

        return $res;
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::getContents()
     */
    public function getContents()
    {
        $this->ensureStream();

        return $this->read($this->getSize());
    }

    /**
     * {@inheritDoc}
     * @see \Psr\Http\Message\StreamInterface::getMetadata()
     * @param ?string $key
     */
    public function getMetadata($key = null)
    {
        try
        {
            $this->ensureStream();
        }
        catch(RuntimeException $e)
        {
            return null;
        }

        $smd = null;
        if(null !== $this->_handle)
            $smd = \stream_get_meta_data($this->_handle);
        if(null === $key)
            return $smd;
        if(isset($smd[$key]))
            return $smd[$key];

        return null;
    }

    /**
     * Closes the stream at destruction of this object.
     */
    public function __destruct()
    {
        $this->detach();
    }

}
