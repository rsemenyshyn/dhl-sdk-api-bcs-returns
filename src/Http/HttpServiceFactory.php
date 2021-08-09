<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Http;

use Dhl\Sdk\Paket\Retoure\Api\ServiceFactoryInterface;
use Dhl\Sdk\Paket\Retoure\Exception\ServiceExceptionFactory;
use Dhl\Sdk\Paket\Retoure\Http\ClientPlugin\ReturnLabelErrorPlugin;
use Dhl\Sdk\Paket\Retoure\Serializer\JsonSerializer;
use Dhl\Sdk\Paket\Retoure\Service\ReturnLabelService;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\ContentLengthPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\LoggerPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\Exception\NotFoundException;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Message\Authentication\BasicAuth;
use Http\Message\Formatter\FullHttpMessageFormatter;
use Http\Client\HttpClient;
use PhpExtended\HttpMessage\RequestFactory;
use PhpExtended\HttpMessage\StreamFactory;
use Psr\Log\LoggerInterface;

/**
 * Create a service instance for REST web service communication.
 *
 * @author Andreas Müller <andreas.mueller@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class HttpServiceFactory implements ServiceFactoryInterface
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function createReturnLabelService($authStorage, $logger, $sandboxMode = false) {
        $appAuth = new BasicAuth($authStorage->getApplicationId(), $authStorage->getApplicationToken());
        $userAuth = base64_encode($authStorage->getUser() . ':' . $authStorage->getSignature());
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'DPDHL-User-Authentication-Token' => $userAuth,
        ];

        $client = new PluginClient(
            $this->httpClient,
            [
                new HeaderDefaultsPlugin($headers),
                new AuthenticationPlugin($appAuth),
                new ContentLengthPlugin(),
                new LoggerPlugin($logger, new FullHttpMessageFormatter(null)),
                new ReturnLabelErrorPlugin()
            ]
        );

        try {
            $requestFactory = new RequestFactory();
            $streamFactory = new StreamFactory();
        } catch (NotFoundException $exception) {
            throw ServiceExceptionFactory::create($exception);
        }

        return new ReturnLabelService(
            $client,
            $sandboxMode ? self::BASE_URL_SANDBOX : self::BASE_URL_PRODUCTION,
            new JsonSerializer(),
            $requestFactory,
            $streamFactory
        );
    }
}
