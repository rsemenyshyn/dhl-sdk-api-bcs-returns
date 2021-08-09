<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Api;

use Dhl\Sdk\Paket\Retoure\Api\Data\AuthenticationStorageInterface;
use Dhl\Sdk\Paket\Retoure\Exception\ServiceException;
use Psr\Log\LoggerInterface;

/**
 * Interface ServiceFactoryInterface
 *
 * @api
 * @author Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link   https://www.netresearch.de/
 */
interface ServiceFactoryInterface {
    const BASE_URL_PRODUCTION = 'https://cig.dhl.de/services/production/rest';
    const BASE_URL_SANDBOX = 'https://cig.dhl.de/services/sandbox/rest';

    /**
     * Create the service able to perform return shipment label requests.
     *
     * @param AuthenticationStorageInterface $authStorage
     * @param LoggerInterface $logger
     * @param bool $sandboxMode
     *
     * @return ReturnLabelServiceInterface
     *
     * @throws ServiceException
     */
    public function createReturnLabelService($authStorage, $logger, $sandboxMode = false);
}
