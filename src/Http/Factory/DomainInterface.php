<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

use \InvalidArgumentException;

/**
 * DomainInterface interface file.
 *
 * This interface represents a single domain name.
 *
 * @author Anastaszor
 */
interface DomainInterface {

    /**
     * Gets whether this domain name is empty.
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * Gets the depths of the domain name, effectively counting the number
     * of dots in the domain.
     *
     * @return integer
     */
    public function getDepths();

    /**
     * Gets a new domain name with the part appended at the beginning of the
     * domain name.
     *
     * @param string $part
     * @return DomainInterface
     * @throws InvalidArgumentException if the part is invalid
     */
    public function append(string $part);

    /**
     * Gets a new domain name with the more specific part stripped of this
     * domain name.
     *
     * @return DomainInterface
     */
    public function getParent();

    /**
     * Gets a string representation of this domain that is compliant with
     * RFC 1034.
     *
     * @return string
     */
    public function getCanonicalRepresentation();

}
