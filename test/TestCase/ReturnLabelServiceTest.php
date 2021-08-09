<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Service;

use Dhl\Sdk\Paket\Retoure\Auth\AuthenticationStorage;
use Dhl\Sdk\Paket\Retoure\Exception\AuthenticationException;
use Dhl\Sdk\Paket\Retoure\Exception\DetailedServiceException;
use Dhl\Sdk\Paket\Retoure\Exception\RequestValidatorException;
use Dhl\Sdk\Paket\Retoure\Exception\ServiceException;
use Dhl\Sdk\Paket\Retoure\Http\HttpServiceFactory;
use Dhl\Sdk\Paket\Retoure\Model\RequestType\ReturnOrder;
use Dhl\Sdk\Paket\Retoure\Test\Expectation\ReturnLabelServiceTestExpectation as Expectation;
use Dhl\Sdk\Paket\Retoure\Test\Provider\ReturnLabelServiceTestProvider;
use Http\Client\Exception\NetworkException;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;

/**
 * Class ReturnLabelServiceTest
 *
 * @author Andreas Müller <andreas.mueller@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class ReturnLabelServiceTest extends TestCase
{
    /**
     * @return \JsonSerializable[][]|string[][]
     * @throws RequestValidatorException
     */
    public function successDataProvider(): array
    {
        return ReturnLabelServiceTestProvider::labelSuccess();
    }

    /**
     * @return \JsonSerializable[][]|int[][]|string[][]
     * @throws RequestValidatorException
     */
    public function errorDataProvider(): array
    {
        return ReturnLabelServiceTestProvider::labelError();
    }

    /**
     * @return \JsonSerializable[][]
     * @throws RequestValidatorException
     */
    public function networkErrorDataProvider(): array
    {
        return ReturnLabelServiceTestProvider::networkError();
    }

    /**
     * Assert successful return label within EU being processed properly.
     *
     * @test
     * @dataProvider successDataProvider
     *
     * @param \JsonSerializable|ReturnOrder $returnOrder
     * @param string $responseBody
     * @throws ServiceException
     */
    public function bookLabelSuccess(ReturnOrder $returnOrder, string $responseBody)
    {
        $httpClient = new Client();

        $responseFactory = Psr17FactoryDiscovery::findResponseFactory();
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        $returnLabelResponse = $responseFactory
            ->createResponse(201, 'Created')
            ->withBody($streamFactory->createStream($responseBody));

        $httpClient->setDefaultResponse($returnLabelResponse);
        $authStorage = new AuthenticationStorage('4pp-1D', '4pp-t0k3N', 'u53R', 'p455w0rD');
        $logger = new TestLogger();
        $serviceFactory = new HttpServiceFactory($httpClient);
        $service = $serviceFactory->createReturnLabelService($authStorage, $logger, true);
        $confirmation = $service->bookLabel($returnOrder);

        $lastRequest = $httpClient->getLastRequest();
        $requestBody = (string)$lastRequest->getBody();

        Expectation::assertLabelRequest($returnOrder, $requestBody);
        Expectation::assertLabelResponse($confirmation, $responseBody);
    }

    /**
     * @test
     * @dataProvider errorDataProvider
     *
     * @param \JsonSerializable $returnOrder
     * @param int $statusCode
     * @param string $contentType
     * @param string $responseBody
     *
     * @throws ServiceException
     */
    public function returnLabelError(
        \JsonSerializable $returnOrder,
        int $statusCode,
        string $contentType,
        string $responseBody
    ) {
        $this->expectExceptionCode($statusCode);

        if ($statusCode === 401) {
            $this->expectException(AuthenticationException::class);
            $this->expectExceptionMessageRegExp('/^Authentication failed\./');
        } elseif (($statusCode >= 400) && ($statusCode < 500)) {
            if ($contentType === 'application/json') {
                $this->expectException(DetailedServiceException::class);
            } else {
                $this->expectException(ServiceException::class);
            }
        } elseif (($statusCode >= 500) && ($statusCode < 600)) {
            $this->expectException(ServiceException::class);
        }

        $httpClient = new Client();
        $responseFactory = Psr17FactoryDiscovery::findResponseFactory();
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        $returnLabelResponse = $responseFactory
            ->createResponse($statusCode)
            ->withBody($streamFactory->createStream($responseBody))
            ->withHeader('Content-Type', $contentType);
        $httpClient->setDefaultResponse($returnLabelResponse);
        $authStorage = new AuthenticationStorage('4pp-1D', '4pp-t0k3N', 'u53R', 'p455w0rD');
        $logger = new TestLogger();
        $serviceFactory = new HttpServiceFactory($httpClient);
        $service = $serviceFactory->createReturnLabelService($authStorage, $logger, true);

        try {
            $service->bookLabel($returnOrder);
        } catch (ServiceException $exception) {
            $lastRequest = $httpClient->getLastRequest();
            $requestBody = (string)$lastRequest->getBody();

            Expectation::assertErrorLogged($logger, $responseBody);
            Expectation::assertCommunicationLogged($logger, $requestBody, $responseBody);

            throw $exception;
        }
    }

    /**
     * Assert network errors being handled properly.
     *
     * @test
     * @dataProvider networkErrorDataProvider
     * @param \JsonSerializable $returnOrder
     * @throws ServiceException
     */
    public function networkError(\JsonSerializable $returnOrder)
    {
        $this->expectException(ServiceException::class);

        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();
        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $payload = json_encode($returnOrder);
        $stream = $streamFactory->createStream($payload);

        $httpClient = new Client();

        $authStorage = new AuthenticationStorage('4pp-1D', '4pp-t0k3N', 'u53R', 'p455w0rD');
        $logger = new TestLogger();
        $serviceFactory = new HttpServiceFactory($httpClient);
        $service = $serviceFactory->createReturnLabelService($authStorage, $logger, true);

        $httpRequest = $requestFactory->createRequest('POST', 'http://shagflksagfda.com');
        $httpRequest->withBody($stream);
        $httpClient->setDefaultException(
            new NetworkException('Could not resolve host: shagflksagfda.com', $httpRequest)
        );

        try {
            $service->bookLabel($returnOrder);
        } catch (ServiceException $exception) {
            $lastRequest = $httpClient->getLastRequest();
            $requestBody = (string)$lastRequest->getBody();

            Expectation::assertErrorLogged($logger);
            Expectation::assertCommunicationLogged($logger, $requestBody);
            throw $exception;
        }
    }
}
