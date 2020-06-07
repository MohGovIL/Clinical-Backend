<?php

/**
 * Date: 02/06/2020
 * @author  Dror Golan <drorgo@matrix.co.il>
 */

namespace ClinikalAPI\Service;


use ClinikalAPI\Model\GetTemplatesServiceTable;

trait FormTemplatesService
{
    /**
     * @param string $service_type
     * @param string $reason_code
     * @param integer $form_id
     * @param string $form_field
     * @return array with {"message_id":{$record}}
     */

    public function getTemplatesForForm($form_id,$form_field,$service_type,$reason_code)
    {
        $templates=array();
        $FormContextMapTable= $this->container->get(GetTemplatesServiceTable::class);
        $dbData=$FormContextMapTable->getTemplatesForForm($form_id,$form_field,$service_type,$reason_code);
        foreach($dbData as $index => $record){
            if($record['title']) {
               //In the future if we would like the id of this template use this :
               // $templates[$record['message_id']] = $record['title'];
                $templates[] = $record['title'];
            }
        }
        return $templates;
    }
}


