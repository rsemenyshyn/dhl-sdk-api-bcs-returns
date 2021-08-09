<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Api\Data;

/**
 * Interface ConfirmationInterface
 *
 * @api
 * @author Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link   https://www.netresearch.de/
 */
interface ConfirmationInterface
{
    /**
     * Obtain the shipment number of the created return label.
     *
     * @return string
     */
    public function getShipmentNumber();

    /**
     * Obtain the base64 encoded label PDF binary.
     *
     * @return string
     */
    public function getLabelData();

    /**
     * Obtain the base64 encoded QR code PNG binary.
     *
     * @return string
     */
    public function getQrLabelData();

    /**
     * Obtain the routing code of the created return label.
     *
     * @return string
     */
    public function getRoutingCode();
}
