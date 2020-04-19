<?php

namespace FhirAPI\FhirRestApiBuilder\Builders;



use FhirAPI\FhirRestApiBuilder\Parts\Registry;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Context;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\HealthcareService;


class HealthcareServiceBuilder extends Builder
{
    private $healthcareService = null;
    private const TYPE = "HealthcareService";
    public function __construct($apiVersion)
    {
        parent::__construct($apiVersion);
        parent::setType(self::TYPE);
        parent::setPart($this->HealthcareService);
        parent::setSearchParams(['active', 'service-type', 'organization', 'name']);

    }
}
