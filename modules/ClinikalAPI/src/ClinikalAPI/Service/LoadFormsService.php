<?php

/**
 * Date: 05/01/20
 * @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 */

namespace ClinikalAPI\Service;

use ClinikalAPI\Model\FormContextMapTable;

trait LoadFormsService
{
    /**
     * get relevant forms by params
     *
     * return data  by reason code if exist if not
     * return data  by service type if exist if not
     * return empty array
     *
     * @param string $service_type
     * @param string $reason_code
     * @return array with {"component":"","form_name":"","order":""}
     *
     */

    use ApiTools;

    public function loadForms($service_type,$reason_code)
    {
        $FormContextMapTable= $this->container->get(FormContextMapTable::class);
        $dbData=$FormContextMapTable->getActiveForms($service_type,$reason_code);

        $service_type= array();
        $reason_code= array();

        foreach($dbData as $index => $record){

            if($record['context_type']==="reason_code"){
                $reason_code[]=$this->getRespondRecord($record);
            }elseif(empty($reason_code) && $record['context_type']==="service_type"){
                $service_type[]=$this->getRespondRecord($record);
            }
        }
        if(empty($reason_code)){
            return $service_type;
        }else{
            return $reason_code;
        }
    }

    /**
     * create return record from db data
     *
     * @param array $form
     * @return array with {"component":"","form_name":"","order":""}
     */
    private function getRespondRecord($form){
        $respondRecord= array();
        $respondRecord["component"]=$form['directory'];
        $respondRecord["form_name"]=$form['name'];
        $respondRecord["order"]=$form['priority'];

        $acoArr=explode("|",$form['aco_spec']);
        if(count($acoArr)>1){
            $respondRecord["permission"]=$this->getAclType($acoArr[0], $acoArr[1]);
        }else{
            $respondRecord["permission"]="none";
        }


        return $respondRecord;
    }


}


