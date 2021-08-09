<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Model\RequestType;

/**
 * Class ReturnOrder
 *
 * @author Andreas Müller <andreas.mueller@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class ReturnOrder implements \JsonSerializable
{
    const DOCUMENT_TYPE_PDF = 'SHIPMENT_LABEL';
    const DOCUMENT_TYPE_QR = 'QR_LABEL';
    const DOCUMENT_TYPE_BOTH = 'BOTH';

    /**
     * @var string
     */
    private $receiverId;

    /**
     * @var SimpleAddress
     */
    private $senderAddress;

    /**
     * @var string|null
     */
    private $customerReference;

    /**
     * @var string|null
     */
    private $shipmentReference;

    /**
     * @var string|null $returnDocumentType The type of document(s) to return [SHIPMENT_LABEL, QR_LABEL, BOTH]
     */
    private $returnDocumentType;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $telephoneNumber;

    /**
     * @var float|null
     */
    private $value;

    /**
     * @var int|null
     */
    private $weightInGrams;

    /**
     * @var CustomsDocument|null
     */
    private $customsDocument;

    public function __construct(
        string $receiverId,
        SimpleAddress $senderAddress
    ) {
        $this->receiverId = $receiverId;
        $this->senderAddress = $senderAddress;
    }

    public function setCustomerReference($customerReference = '')
    {
        $this->customerReference = $customerReference;

        return $this;
    }

    public function setShipmentReference($shipmentReference = '')
    {
        $this->shipmentReference = $shipmentReference;

        return $this;
    }

    public function setReturnDocumentType($returnDocumentType = '')
    {
        $this->returnDocumentType = $returnDocumentType;

        return $this;
    }

    public function setEmail($email = '')
    {
        $this->email = $email;

        return $this;
    }

    public function setTelephoneNumber($telephoneNumber = '')
    {
        $this->telephoneNumber = $telephoneNumber;

        return $this;
    }

    public function setValue($value = 0)
    {
        $this->value = $value;

        return $this;
    }

    public function setWeightInGrams($weightInGrams = 0)
    {
        $this->weightInGrams = $weightInGrams;

        return $this;
    }

    public function setCustomsDocument($customsDocument = null)
    {
        $this->customsDocument = $customsDocument;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return mixed[] Serializable object properties
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
