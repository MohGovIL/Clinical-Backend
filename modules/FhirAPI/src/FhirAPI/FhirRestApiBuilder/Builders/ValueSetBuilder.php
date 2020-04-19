<?php


namespace FhirAPI\FhirRestApiBuilder\Builders;

class ValueSetBuilder extends Builder
{
    private $valueSet = null;
    private const TYPE = "ValueSet";
    public function __construct($apiVersion)
    {
        parent::__construct($apiVersion);
        parent::setType(self::TYPE);
        parent::setPart($this->valueSet);

    }
}
