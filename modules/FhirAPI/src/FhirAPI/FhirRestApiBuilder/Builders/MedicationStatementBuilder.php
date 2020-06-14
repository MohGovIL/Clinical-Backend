<?php
/**
 * Date: 26/05/20
 * @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class Fhir Condition BUILDER
 */


namespace FhirAPI\FhirRestApiBuilder\Builders;

/* important need to register at fhir_rest_elements table*/

class MedicationStatementBuilder extends Builder
{
    private $valueSet = null;
    private const TYPE = "MedicationStatement";
    public function __construct($apiVersion)
    {
        parent::__construct($apiVersion);
        parent::setType(self::TYPE);
        parent::setPart($this->valueSet);

    }
}
