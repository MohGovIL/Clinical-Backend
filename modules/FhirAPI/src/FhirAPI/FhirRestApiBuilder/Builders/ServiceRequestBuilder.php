<?php
/**
 * Date: 15/07/20
 * @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class Fhir ServiceRequest BUILDER
 */


namespace FhirAPI\FhirRestApiBuilder\Builders;

/* important need to register at fhir_rest_elements table*/

class ServiceRequestBuilder extends Builder
{
    private $valueSet = null;
    private const TYPE = "ServiceRequest";
    public function __construct($apiVersion)
    {
        parent::__construct($apiVersion);
        parent::setType(self::TYPE);
        parent::setPart($this->valueSet);

    }
}
