<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Service\ReturnLabelService;

use Dhl\Sdk\Paket\Retoure\Api\Data\ConfirmationInterface;

/**
 * Class Confirmation
 *
 * @author Andreas Müller <andreas.mueller@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class Confirmation implements ConfirmationInterface
{
    /**
     * @var string
     */
    private $shipmentNumber;

    /**
     * @var string
     */
    private $labelData;

    /**
     * @var string
     */
    private $qrLabelData;

    /**
     * @var string
     */
    private $routingCode;

    public function __construct($shipmentNumber, $labelData, $qrLabelData, $routingCode) {
        $this->shipmentNumber = $shipmentNumber;
        $this->labelData = $labelData;
        $this->qrLabelData = $qrLabelData;
        $this->routingCode = $routingCode;
    }

    public function getShipmentNumber()
    {
        return $this->shipmentNumber;
    }

    public function getLabelData()
    {
        return $this->labelData;
    }

    public function getQrLabelData()
    {
        return $this->qrLabelData;
    }

    public function getRoutingCode()
    {
        return $this->routingCode;
    }
}
