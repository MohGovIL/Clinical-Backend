<?php

/**
 * Date: 01/07/2020
 * @author  Dror Golan <drorgo@matrix.co.il>
 */

namespace ClinikalAPI\Service;

use ClinikalAPI\Model\FormContextMapTable;
use ClinikalAPI\Model\ListOptionsTable;

trait IndicatorsSettingsService
{
    use ApiTools;

    public function getIndicators()
    {
        $listOptionsTable= $this->container->get(ListOptionsTable::class);
        $dbData=$listOptionsTable->getLionicIndicators($service_type,$reason_code);

        $lionicCodes= array();


        foreach($dbData as $index => $record){

            $lionicCodes [] = $record;
        }
        if(empty($lionicCodes)){
            return $lionicCodes;
        }else{
            return [];
        }
    }
}



