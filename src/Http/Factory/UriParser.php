<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use InvalidArgumentException;
use Psr\Http\Message\UriFactoryInterface;

/**
 * UriParser class file.
 *
 * This class is a simple implementation of the UriParserInterface.
 *
 * @author Anastaszor
 * @extends \PhpExtended\Parser\AbstractParser<UriFactoryInterface>
 */
class UriParser extends AbstractParser implements UriParserInterface
{

    /**
     * {@inheritDoc}
     * @see \PhpExtended\Parser\ParserInterface::parse()
     */
    public function parse($data)
    {
        $data = (string) $data;

        $uri = new Uri();
        $carrier = new UriCarrier($uri, $data, $data, 0);

        $carrier = $this->parseScheme($carrier);
        $carrier = $this->parseUserInfo($carrier);
        $carrier = $this->parseHostname($carrier);
        $carrier = $this->parsePath($carrier);
        $carrier = $this->parseQuery($carrier);
        $carrier = $this->parseFragment($carrier);

        return $carrier->getUri();
    }

    /**
     * Parses the scheme part of the uri from the remaining data.
     *
     * @param UriCarrier $carrier
     * @return UriCarrier
     * @throws ParseException
     */
    public function parseScheme(UriCarrier $carrier)
    {
        if(!$carrier->hasData())
            return $carrier;

        $index = $carrier->getIndex();
        $remaining = $carrier->getRemaining();
        $uri = $carrier->getUri();

        do
        {
            $schpos = \mb_strpos($remaining, '//');
            if(false === $schpos)
                return $carrier;

            $scheme = (string) \mb_substr($remaining, 0, $schpos);
            $strlen = (int) \mb_strlen($scheme);
            if(0 < $strlen)
            {
                if($scheme[$strlen - 1] === ':')
                    $scheme = (string) \mb_substr($scheme, 0, -1);

                try
                {
                    $uri = $uri->withScheme($scheme);
                }
                catch(InvalidArgumentException $e)
                {
                    $message = 'Failed to parse scheme value';

                    throw new ParseException(UriFactoryInterface::class, $carrier->getBase(), $index + 1, $message, -1, $e);
                }
            }

            $index += (int) \mb_strlen($scheme) + 2;
            $remaining = (string) \mb_substr($remaining, $schpos + 2);
        }
        while(\mb_strpos($remaining, '://') !== false);

        return new UriCarrier($uri, $carrier->getBase(), $remaining, $index);
    }

    /**
     * Parses the user info part of the uri from the remaining data.
     *
     * @param UriCarrier $carrier
     * @return UriCarrier
     */
    public function parseUserInfo(UriCarrier $carrier)
    {
        if(!$carrier->hasData())
            return $carrier;

        $index = $carrier->getIndex();
        $data = $carrier->getRemaining();
        $arobpos = \mb_strpos($data, '@');
        if(false === $arobpos)
            return $carrier;

        $pathpos = \mb_strpos($data, '/');
        if(false === $pathpos) $pathpos = \mb_strlen($data);
        $quespos = \mb_strpos($data, '?');
        if(false === $quespos) $quespos = \mb_strlen($data);
        $hashpos = \mb_strpos($data, '#');
        if(false === $hashpos) $hashpos = \mb_strlen($data);
        $minLength = \min((int) \mb_strlen($data), $pathpos, $quespos, $hashpos);

        if($arobpos > $minLength)
            return $carrier;

        $user = (string) \mb_substr($data, 0, $arobpos);
        $pswd = null;
        $remaining = (string) \mb_substr($data, $arobpos + 1);
        $uri = $carrier->getUri();

        $colonpos = \mb_strpos($user, ':');
        if(false !== $colonpos)
        {
            $pswd = (string) \mb_substr($user, $colonpos + 1);
            $user = (string) \mb_substr($user, 0, $colonpos);
        }

        $uri = $uri->withUserInfo($user, $pswd);
        $index += $arobpos + 1;

        return new UriCarrier($uri, $carrier->getBase(), $remaining, $index);
    }

    /**
     * Parses the hostname part of the uri from the remaining data.
     *
     * @param UriCarrier $carrier
     * @return UriCarrier
     * @throws ParseException
     */
    public function parseHostname(UriCarrier $carrier)
    {
        if(!$carrier->hasData())
            return $carrier;

        $index = $carrier->getIndex();
        $data = $carrier->getRemaining();
        $pathpos = \mb_strpos($data, '/');
        if(false === $pathpos) $pathpos = null;
        $quespos = \mb_strpos($data, '?');
        if(false === $quespos) $quespos = null;
        $hashpos = \mb_strpos($data, '#');
        if(false === $hashpos) $hashpos = null;
        $maxlength = !empty($quespos) ? $quespos : (!empty($hashpos) ? $hashpos : (int) \mb_strlen($data));

        $host = (string) \mb_substr($data, 0, $maxlength);
        $remaining = (string) \mb_substr($data, $maxlength);
        $uri = $carrier->getUri();

        $colonpos = \mb_strrpos($host, ':');
        if(false !== $colonpos)
        {
            $port = (int) (string) \mb_substr($host, $colonpos + 1);
            $host = (string) \mb_substr($host, 0, $colonpos);

            try
            {
                $uri = $uri->withPort($port);
            }
            catch(InvalidArgumentException $e)
            {
                $index += (int) \mb_strlen($host);
                $message = 'Failed to parse port value';

                throw new ParseException(UriFactoryInterface::class, $carrier->getBase(), $index + 1, $message, -1, $e);
            }
        }

        try
        {
            $uri = $uri->withHost($host);
            $index += $maxlength;
        }
        catch(InvalidArgumentException $e)
        {
            $message = 'Failed to parse host value';

            throw new ParseException(UriFactoryInterface::class, $carrier->getBase(), $index + 1, $message, -1, $e);
        }

        return new UriCarrier($uri, $carrier->getBase(), $remaining, $index);
    }

    /**
     * Parses the path part of the uri from the remaining data.
     *
     * @param UriCarrier $carrier
     * @return UriCarrier
     * @throws ParseException
     */
    public function parsePath(UriCarrier $carrier)
    {
        if(!$carrier->hasData())
            return $carrier;

        $index = $carrier->getIndex();
        $data = $carrier->getRemaining();
        $quespos = \mb_strpos($data, '?');
        if(false === $quespos) $quespos = null;
        $hashpos = \mb_strpos($data, '#');
        if(false === $hashpos) $hashpos = null;
        $maxlength = !empty($quespos) ? $quespos : (!empty($hashpos) ? $hashpos : (int) \mb_strlen($data));

        $path = (string) \mb_substr($data, 0, $maxlength);
        $remaining = (string) \mb_substr($data, $maxlength);

        try
        {
            $uri = $carrier->getUri()->withPath($path);
            $index += (int) \mb_strlen($path);
        }
            // @codeCoverageIgnoreStart
        catch(InvalidArgumentException $e)
        {
            $message = 'Failed to parse path value';

            throw new ParseException(UriFactoryInterface::class, $carrier->getBase(), $index + 1, $message, -1, $e);
        }
        // @codeCoverageIgnoreEnd

        return new UriCarrier($uri, $carrier->getBase(), $remaining, $index);
    }

    /**
     * Parses the query part of the uri from the remaining data.
     *
     * @param UriCarrier $carrier
     * @return UriCarrier
     * @throws ParseException
     */
    public function parseQuery(UriCarrier $carrier)
    {
        if(!$carrier->hasData())
            return $carrier;

        $index = $carrier->getIndex();
        $data = $carrier->getRemaining();
        $quespos = \mb_strpos($data, '?');
        if(false === $quespos)
            return $carrier;

        $query = (string) \mb_substr($data, $quespos + 1);
        $remaining = '';

        $hashpos = \mb_strpos($data, '#', $quespos);
        if(false !== $hashpos)
        {
            $index += $quespos;
            $query = (string) \mb_substr($data, $quespos + 1, $hashpos - $quespos - 1);
            $remaining = (string) \mb_substr($data, $hashpos);
        }

        try
        {
            $uri = $carrier->getUri()->withQuery($query);
            $index += (int) \mb_strlen($query);
        }
            // @codeCoverageIgnoreStart
        catch(InvalidArgumentException $e)
        {
            $message = 'Failed to parse query value';

            throw new ParseException(UriFactoryInterface::class, $carrier->getBase(), $index + 1, $message, -1, $e);
        }
        // @codeCoverageIgnoreEnd

        return new UriCarrier($uri, $carrier->getBase(), $remaining, $carrier->getIndex() + (int) \mb_strlen($query));
    }

    /**
     * Parses the fragment part of the uri from the remaining data.
     *
     * @param UriCarrier $carrier
     * @return UriCarrier
     */
    public function parseFragment(UriCarrier $carrier)
    {
        if(!$carrier->hasData())
            return $carrier;

        $data = $carrier->getRemaining();
        $hashpos = \mb_strpos($data, '#');
        // @codeCoverageIgnoreStart
        if(false === $hashpos)
            return $carrier;
        // @codeCoverageIgnoreEnd
        $fragment = (string) \mb_substr($data, $hashpos + 1);

        $uri = $carrier->getUri()->withFragment($fragment);

        return new UriCarrier($uri, $carrier->getBase(), '', $carrier->getIndex());
    }

}
