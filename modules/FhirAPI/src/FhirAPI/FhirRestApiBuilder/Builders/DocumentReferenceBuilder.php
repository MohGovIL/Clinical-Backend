<?php


namespace FhirAPI\FhirRestApiBuilder\Builders;


class DocumentReferenceBuilder extends Builder
{
    private $documentReference = null;
    private const TYPE = "DocumentReference";
    public function __construct($apiVersion)
    {
        parent::__construct($apiVersion);
        parent::setType(self::TYPE);
        parent::setPart($this->documentReference);
        //parent::setSearchParams(['patient']);
    }
}
