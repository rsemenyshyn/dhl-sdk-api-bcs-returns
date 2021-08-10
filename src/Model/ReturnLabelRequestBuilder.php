<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Model;

use Dhl\Sdk\Paket\Retoure\Api\ReturnLabelRequestBuilderInterface;
use Dhl\Sdk\Paket\Retoure\Model\RequestType\Country;
use Dhl\Sdk\Paket\Retoure\Model\RequestType\CustomsDocument;
use Dhl\Sdk\Paket\Retoure\Model\RequestType\CustomsDocumentPosition;
use Dhl\Sdk\Paket\Retoure\Model\RequestType\ReturnOrder;
use Dhl\Sdk\Paket\Retoure\Model\RequestType\SimpleAddress;

/**
 * Class ReturnLabelRequestBuilder
 *
 * @author Andreas Müller <andreas.mueller@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class ReturnLabelRequestBuilder implements ReturnLabelRequestBuilderInterface
{
    /**
     * @var mixed[]
     */
    private $data = [];

    public function setAccountDetails($receiverId, $billingNumber = null) {
        $this->data['receiverId'] = $receiverId;
        $this->data['billingNumber'] = $billingNumber;
        return $this;
    }

    public function setShipmentReference($shipmentReference) {
        $this->data['shipmentReference'] = $shipmentReference;
        return $this;
    }

    public function setDocumentTypePdf() {
        $this->data['returnDocumentType'] = ReturnOrder::DOCUMENT_TYPE_PDF;
        return $this;
    }

    public function setDocumentTypeQr() {
        $this->data['returnDocumentType'] = ReturnOrder::DOCUMENT_TYPE_QR;
        return $this;
    }

    public function setPackageDetails($weightInGrams = null, $amount = null) {
        $this->data['package']['weight'] = $weightInGrams;
        $this->data['package']['amount'] = $amount;
        return $this;
    }

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
    ) {
        $this->data['shipper']['address']['name'] = $name;
        $this->data['shipper']['address']['countryCode'] = $countryCode;
        $this->data['shipper']['address']['postalCode'] = $postalCode;
        $this->data['shipper']['address']['city'] = $city;
        $this->data['shipper']['address']['streetName'] = $streetName;
        $this->data['shipper']['address']['streetNumber'] = $streetNumber;
        $this->data['shipper']['address']['company'] = $company;
        $this->data['shipper']['address']['nameAddition'] = $nameAddition;
        $this->data['shipper']['address']['state'] = $state;
        $this->data['shipper']['address']['countryName'] = $countryName;

        return $this;
    }

    public function setShipperContact($email, $phoneNumber = null) {
        $this->data['shipper']['contact']['email'] = $email;
        $this->data['shipper']['contact']['phoneNumber'] = $phoneNumber;
        return $this;
    }

    public function setCustomsDetails(
        $currency,
        $originalShipmentNumber = null,
        $originalOperator = null,
        $accompanyingDocument = null,
        $originalInvoiceNumber = null,
        $originalInvoiceDate = null,
        $comment = null
    ) {
        $this->data['customsDetails']['currency'] = $currency;
        $this->data['customsDetails']['originalShipmentNumber'] = $originalShipmentNumber;
        $this->data['customsDetails']['originalOperator'] = $originalOperator;
        $this->data['customsDetails']['accompanyingDocument'] = $accompanyingDocument;
        $this->data['customsDetails']['originalInvoiceNumber'] = $originalInvoiceNumber;
        $this->data['customsDetails']['originalInvoiceDate'] = $originalInvoiceDate;
        $this->data['customsDetails']['comment'] = $comment;

        return $this;
    }

    public function addCustomsItem(
        $qty,
        $description,
        $value,
        $weightInGrams,
        $sku,
        $countryOfOrigin = null,
        $tariffNumber = null
    ) {
        $this->data['customsDetails']['items'][] = [
            'qty' => $qty,
            'description' => $description,
            'value' => $value,
            'weight' => $weightInGrams,
            'sku' => $sku,
            'countryOfOrigin' => $countryOfOrigin,
            'tariffNumber' => $tariffNumber
        ];

        return $this;
    }

    public function create() {
        ReturnLabelRequestValidator::validate($this->data);

        $country = new Country($this->data['shipper']['address']['countryCode']);
        $country->setState($this->data['shipper']['address']['state']);
        $country->setCountry($this->data['shipper']['address']['countryName']);

        $senderAddress = new SimpleAddress(
            $this->data['shipper']['address']['name'],
            $this->data['shipper']['address']['streetName'],
            $this->data['shipper']['address']['streetNumber'],
            $this->data['shipper']['address']['postalCode'],
            $this->data['shipper']['address']['city']
        );
        $senderAddress->setCountry($country);
        $senderAddress->setName2($this->data['shipper']['address']['company']);
        $senderAddress->setName3($this->data['shipper']['address']['nameAddition']);

        if (isset($this->data['customsDetails'], $this->data['customsDetails']['items'])) {
            $positions = array_map(
                static function (array $itemData) {
                    $position = new CustomsDocumentPosition(
                        $itemData['qty'],
                        $itemData['weight'],
                        $itemData['description'],
                        $itemData['value'],
                        $itemData['sku']
                    );
                    $position->setOriginCountry($itemData['countryOfOrigin']);
                    $position->setTariffNumber($itemData['tariffNumber']);

                    return $position;
                },
                $this->data['customsDetails']['items']
            );

            $customsDocument = new CustomsDocument($this->data['customsDetails']['currency'], $positions);
            $customsDocument->setOriginalShipmentNumber($this->data['customsDetails']['originalShipmentNumber']);
            $customsDocument->setOriginalOperator($this->data['customsDetails']['originalOperator']);
            $customsDocument->setOriginalInvoiceNumber($this->data['customsDetails']['originalInvoiceNumber']);
            $customsDocument->setAccompanyingDocument($this->data['customsDetails']['accompanyingDocument']);
            $customsDocument->setOriginalInvoiceDate($this->data['customsDetails']['originalInvoiceDate']);
            $customsDocument->setComment($this->data['customsDetails']['comment']);
        } else {
            $customsDocument = null;
        }

        $returnOrder = new ReturnOrder($this->data['receiverId'], $senderAddress);
        $returnOrder->setCustomerReference($this->data['billingNumber']);
        $returnOrder->setShipmentReference(!empty($this->data['shipmentReference']) ? $this->data['shipmentReference'] : null);
        $returnOrder->setReturnDocumentType(!empty($this->data['returnDocumentType']) ? $this->data['returnDocumentType'] : ReturnOrder::DOCUMENT_TYPE_BOTH);

        $returnOrder->setEmail(!empty($this->data['shipper']) && !empty($this->data['shipper']['contact']) && !empty($this->data['shipper']['contact']['email']) ? $this->data['shipper']['contact']['email'] : null);
        $returnOrder->setTelephoneNumber(!empty($this->data['shipper']) && !empty($this->data['shipper']['contact']) && !empty($this->data['shipper']['contact']['phoneNumber']) ? $this->data['shipper']['contact']['phoneNumber'] : null);

        $returnOrder->setValue(!empty($this->data['package']) && !empty($this->data['package']['amount']) ? $this->data['package']['amount'] : null);
        $returnOrder->setWeightInGrams(!empty($this->data['package']) && !empty($this->data['package']['weight']) ? $this->data['package']['weight'] : null);
        $returnOrder->setCustomsDocument($customsDocument);

        $this->data = [];

        return $returnOrder;
    }
}
