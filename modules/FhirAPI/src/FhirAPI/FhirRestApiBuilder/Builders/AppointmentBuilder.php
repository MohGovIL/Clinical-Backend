<?php
/**
 * Date: 29/01/20
 *  @author Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class Fhir Organization BUILDER
 */

namespace FhirAPI\FhirRestApiBuilder\Builders;



use FhirAPI\FhirRestApiBuilder\Parts\Registry;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Context;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Appointment;


class AppointmentBuilder extends Builder
{
    private $appointment = null;
    private const TYPE = "Appointment";
    public function __construct($apiVersion)
    {
        parent::__construct($apiVersion);
        parent::setType(self::TYPE);
        parent::setPart($this->appointment);
        parent::setSearchParams(['active']);
     }
}
