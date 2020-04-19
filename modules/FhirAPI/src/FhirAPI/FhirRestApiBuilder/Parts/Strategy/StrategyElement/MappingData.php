<?php


namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement;

interface MappingData
{
    public function fhirToDb($FhirObject);
    public function DBToFhir(...$params);
    public function validateDb($data);           //check if array is a valid db row
    public function initFhirObject();
}
