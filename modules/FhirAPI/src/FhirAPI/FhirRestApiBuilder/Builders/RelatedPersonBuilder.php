<?php
/**
 * Date: 05/03/20
 * @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class Fhir RelatedPerson BUILDER
 */


namespace FhirAPI\FhirRestApiBuilder\Builders;

/* important need to register at fhir_rest_elements table*/

class RelatedPersonBuilder extends Builder
{
    private $valueSet = null;
    private const TYPE = "RelatedPerson";
    public function __construct($apiVersion)
    {
        parent::__construct($apiVersion);
        parent::setType(self::TYPE);
        parent::setPart($this->valueSet);

    }
}


