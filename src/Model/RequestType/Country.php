<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Model\RequestType;

/**
 * Class Country
 *
 * @author Andreas Müller <andreas.mueller@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class Country implements \JsonSerializable {
    /**
     * @var string
     */
    private $countryISOCode;

    /**
     * @var string|null
     */
    private $country;

    /**
     * @var string|null
     */
    private $state;

    public function __construct($countryISOCode) {
        $this->countryISOCode = $countryISOCode;
    }

    public function setCountry($country = '') {
        $this->country = $country;
        return $this;
    }

    public function setState($state = '') {
        $this->state = $state;
        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return mixed[] Serializable object properties
     */
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}
