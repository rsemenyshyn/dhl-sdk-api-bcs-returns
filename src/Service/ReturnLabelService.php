<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Service;

use Dhl\Sdk\Paket\Retoure\Api\ReturnLabelServiceInterface;
use Dhl\Sdk\Paket\Retoure\Exception\AuthenticationErrorException;
use Dhl\Sdk\Paket\Retoure\Exception\DetailedErrorException;
use Dhl\Sdk\Paket\Retoure\Exception\ServiceExceptionFactory;
use Dhl\Sdk\Paket\Retoure\Service\ReturnLabelService\Confirmation;
use Dhl\Sdk\Paket\Retoure\Serializer\JsonSerializer;
use Dhl\Sdk\Paket\Retoure\Http\Factory\RequestFactory;
use Dhl\Sdk\Paket\Retoure\Http\Factory\StreamFactory;
use Http\Client\HttpClient;

/**
 * Class ReturnLabelService
 *
 * @author Andreas Müller <andreas.mueller@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class ReturnLabelService implements ReturnLabelServiceInterface
{
    const OPERATION_BOOK_LABEL = 'returns/';

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @var StreamFactory
     */
    private $streamFactory;

    public function __construct(
        $client,
        $baseUrl,
        $serializer,
        $requestFactory,
        $streamFactory
    ) {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->serializer = $serializer;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    public function bookLabel(\JsonSerializable $returnOrder)
    {
        $uri = sprintf('%s/%s', $this->baseUrl, self::OPERATION_BOOK_LABEL);

        try {
            $payload = $this->serializer->encode($returnOrder);
            $stream = $this->streamFactory->createStream($payload);

            $httpRequest = $this->requestFactory->createRequest('POST', $uri)->withBody($stream);

            $response = $this->client->sendRequest($httpRequest);
            $responseJson = (string) $response->getBody();
        } catch (AuthenticationErrorException $exception) {
            throw ServiceExceptionFactory::createAuthenticationException($exception);
        } catch (DetailedErrorException $exception) {
            throw ServiceExceptionFactory::createDetailedServiceException($exception);
        } catch (\Exception $exception) {
            throw ServiceExceptionFactory::create($exception);
        }

        $responseData = $this->serializer->decode($responseJson);

        $shipmentNumber = $responseData['shipmentNumber'] ?: '';
        $labelData = $responseData['labelData'] ?: '';
        $qrLabelData = $responseData['qrLabelData'] ?: '';
        $routingCode = $responseData['routingCode'] ?: '';

        return new Confirmation($shipmentNumber, $labelData, $qrLabelData, $routingCode);
    }
}
