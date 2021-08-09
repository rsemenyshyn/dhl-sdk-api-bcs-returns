<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Api\Data;

/**
 * Interface AuthenticationStorageInterface
 *
 * @api
 * @author Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link   https://www.netresearch.de/
 */
interface AuthenticationStorageInterface
{
    public function getApplicationId();

    public function getApplicationToken();

    public function getUser();

    public function getSignature();
}
