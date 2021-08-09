<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Api;

use Dhl\Sdk\Paket\Retoure\Exception\RequestValidatorException;

/**
 * Interface ReturnLabelRequestBuilderInterface
 *
 * @api
 * @author Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link   https://www.netresearch.de/
 */
interface ReturnLabelRequestBuilderInterface
{
    /**
     * Set account related data.
     *
     * The name of the return recipient (receiverId) can be found in the
     * DHL business customer portal. The billing number will be printed on
     * the label.
     *
     * @param string $receiverId Receiver ID (Retourenempfängername)
     * @param string|null $billingNumber Billing Number (Abrechnungsnummer)
     * @return ReturnLabelRequestBuilderInterface
     */
    public function setAccountDetails($receiverId, $billingNumber = null);

    /**
     * Set shipment reference (optional).
     *
     * The shipment reference is used to identify a return in the DHL business
     * customer portal listing. It is not printed on the label.
     *
     * @param string $shipmentReference
     * @return ReturnLabelRequestBuilderInterface
     */
    public function setShipmentReference($shipmentReference);

    /**
     * Request only PDF shipping label (optional).
     *
     * By default, PDF label and QR code image will be requested.
     *
     * @return ReturnLabelRequestBuilderInterface
     */
    public function setDocumentTypePdf();

    /**
     * Request only QR code image (optional).
     *
     * By default, PDF label and QR code image will be requested.
     *
     * @return ReturnLabelRequestBuilderInterface
     */
    public function setDocumentTypeQr();

    /**
     * Set package related data (optional).
     *
     * @param int|null $weightInGrams Total weight of all included items (plus tare weight).
     * @param float|null $amount Total monetary value of all included items.
     *
     * @return ReturnLabelRequestBuilderInterface
     */
    public function setPackageDetails($weightInGrams = null, $amount = null);

    /**
     * Set the sender of the return shipment (the consumer).
     *
     * @param string $name
     * @param string $countryCode
     * @param string $postalCode
     * @param string $city
     * @param string $streetName
     * @param string $streetNumber
     * @param string|null $company
     * @param string|null $nameAddition
     * @param string|null $state
     * @param string|null $countryName
     * @return ReturnLabelRequestBuilderInterface
     */
    public function setShipperAddress(
        $name,
        $countryCode,
        $postalCode,
        $city,
        $streetName,
        $streetNumber,
        $company = null,
        $nameAddition = null,
        $state = null,
        $countryName = null
    );

    /**
     * Set contact data of the sender (the consumer, optional).
     *
     * @param string $email
     * @param string|null $phoneNumber
     * @return ReturnLabelRequestBuilderInterface
     */
    public function setShipperContact($email, $phoneNumber = null);

    /**
     * Set customs details, mandatory if customs form ("CN23") is required.
     *
     * @param string $currency Currency the returned goods were payed in.
     * @param string|null $originalShipmentNumber Original shipment number.
     * @param string|null $originalOperator Company that delivered the original parcel.
     * @param string|null $accompanyingDocument Additional documents.
     * @param string|null $originalInvoiceNumber Invoice number of the returned goods.
     * @param string|null $originalInvoiceDate Date of the invoice.
     * @param string|null $comment
     * @return ReturnLabelRequestBuilderInterface
     */
    public function setCustomsDetails(
        $currency,
        $originalShipmentNumber = null,
        $originalOperator = null,
        $accompanyingDocument = null,
        $originalInvoiceNumber = null,
        $originalInvoiceDate = null,
        $comment = null
    );

    /**
     * Add an item to be declared, mandatory if customs form ("CN23") is required.
     *
     * @param int $qty Amount of items declared per position.
     * @param string $description Description of the returned item.
     * @param float $value Monetary value of returned item.
     * @param int $weightInGrams Weight of the returned item.
     * @param string $sku Article reference of the returned item.
     * @param string|null $countryOfOrigin Country the returned item was produced.
     * @param string|null $tariffNumber Customs tariff number.
     * @return ReturnLabelRequestBuilderInterface
     */
    public function addCustomsItem(
        $qty,
        $description,
        $value,
        $weightInGrams,
        $sku,
        $countryOfOrigin = null,
        $tariffNumber = null
    );

    /**
     * Create the return label request and reset the builder data.
     *
     * @return \JsonSerializable
     *
     * @throws RequestValidatorException
     */
    public function create();
}
