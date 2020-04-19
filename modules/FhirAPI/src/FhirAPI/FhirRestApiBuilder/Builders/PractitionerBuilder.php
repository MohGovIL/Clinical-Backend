<?php
/**
 * Date: 29/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class Fhir Organization BUILDER
 */

namespace FhirAPI\FhirRestApiBuilder\Builders;



use FhirAPI\FhirRestApiBuilder\Parts\Registry;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Context;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Patient;


class PractitionerBuilder extends Builder
{
    private $practitioner = null;
    private const TYPE = "Practitioner";
    public function __construct($apiVersion)
    {
        parent::__construct($apiVersion);
        parent::setType(self::TYPE);
        parent::setPart($this->practitioner);
        //parent::setSearchParams(['active']);
     }
}
