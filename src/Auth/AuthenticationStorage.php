<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Auth;

use Dhl\Sdk\Paket\Retoure\Api\Data\AuthenticationStorageInterface;

/**
 * Class AuthenticationStorage
 *
 * @author Andreas Müller <andreas.mueller@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class AuthenticationStorage implements AuthenticationStorageInterface
{
    /**
     * @var string
     */
    private $applicationId;

    /**
     * @var string
     */
    private $applicationToken;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $signature;

    public function __construct($applicationId, $applicationToken, $user, $signature) {
        $this->applicationId = $applicationId;
        $this->applicationToken = $applicationToken;
        $this->user = $user;
        $this->signature = $signature;
    }

    public function getApplicationId()
    {
        return $this->applicationId;
    }

    public function getApplicationToken()
    {
        return $this->applicationToken;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getSignature()
    {
        return $this->signature;
    }
}
