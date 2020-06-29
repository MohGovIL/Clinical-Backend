<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\ValueSet;

use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\Service\FhirBaseMapping;
use Interop\Container\ContainerInterface;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRValueSet;

class FhirValueSetMapping extends FhirBaseMapping  implements MappingData
{
    private $adapter = null;
    private $container = null;
    private $FHIRValueSet = null;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->FHIRValueSet = new FHIRValueSet;
    }

    public function fhirToDb($FhirObject){}

    public function DBToFhir(...$params){
        $data = $params[0];
        $vs_id = $data[0]['vs_id'];
        $vs_title = $data[0]['vs_title'];
        $vs_status = $data[0]['vs_status'];
        $operations = $params[1];

        $dbArray = [];
        $dbArray['id'] = $this->createFHIRId($vs_id);
        $dbArray['title'] = $this->createFHIRString($vs_title);
        $dbArray['status'] = $this->createFHIRCode($vs_status);

        // build expansion (contains the actual codes)
        if(in_array('$expand', $operations)) {
            $expansion = [];
            $expansion['timestamp'] = $this->createFHIRDateTime(date("Y-m-d h:i:s"));

            // build 'contains'
            $contains = [];
            foreach ($data as $row) {
                $contains[] = array(
                    'system' =>  self::LIST_SYSTEM_LINK . $this->createFHIRUri($row['system']),
                    'code' => $this->createFHIRCode($row['code']),
                    'display' => $this->createFHIRString($row['display'])
                );
            }
            $expansion['contains'] = $contains;

            $dbArray['expansion'] = $expansion;
        }

        $this->FHIRValueSet = new FHIRValueSet($dbArray);
        return $this->FHIRValueSet;
    }

    public function parsedJsonToDb($parsedData){}

    public function validateDb($data){}

    public function initFhirObject()
    {
        // TODO: Implement initFhirObject() method.
        /*
        $FHIRValueSet = new FHIRValueSet;
        $this->FHIRValueSet=$FHIRValueSet;
        return $FHIRValueSet;
        */
    }


}
