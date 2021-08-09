<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Model\RequestType;

/**
 * Class CustomsDocument
 *
 * @author Andreas Müller <andreas.mueller@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class CustomsDocument implements \JsonSerializable
{
    /**
     * @var string $currency Currency the returned goods were payed in: [EUR, GBP, CHF]
     */
    private $currency;

    /**
     * @var CustomsDocumentPosition[] $positions The customs items to be declared.
     */
    private $positions;

    /**
     * @var string|null $originalShipmentNumber Original shipment number.
     */
    private $originalShipmentNumber;

    /**
     * @var string|null $originalOperator Company that delivered the original parcel.
     */
    private $originalOperator;

    /**
     * @var string|null $acommpanyingDocument Additional documents.
     */
    private $acommpanyingDocument;

    /**
     * @var string|null $originalInvoiceNumber Invoice number of the returned goods.
     */
    private $originalInvoiceNumber;

    /**
     * @var string|null $originalInvoiceDate Date of the invoice.
     */
    private $originalInvoiceDate;

    /**
     * @var string|null $comment Comment.
     */
    private $comment;

    /**
     * CustomsDocument constructor.
     * @param string $currency
     * @param CustomsDocumentPosition[] $positions
     */
    public function __construct(
        string $currency,
        array $positions
    ) {
        $this->currency = $currency;
        $this->positions = $positions;
    }

    public function setOriginalShipmentNumber($originalShipmentNumber = '')
    {
        $this->originalShipmentNumber = $originalShipmentNumber;

        return $this;
    }

    public function setOriginalOperator($originalOperator = '')
    {
        $this->originalOperator = $originalOperator;

        return $this;
    }

    public function setAccompanyingDocument($accompanyingDocument = '')
    {
        $this->acommpanyingDocument = $accompanyingDocument;

        return $this;
    }

    public function setOriginalInvoiceNumber($originalInvoiceNumber = '')
    {
        $this->originalInvoiceNumber = $originalInvoiceNumber;

        return $this;
    }
    public function setOriginalInvoiceDate($originalInvoiceDate = '')
    {
        $this->originalInvoiceDate = $originalInvoiceDate;

        return $this;
    }

    public function setComment($comment = '')
    {
        $this->comment = $comment;

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
