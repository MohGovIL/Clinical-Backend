<?php

/**
 * Date: 05/01/20
 * @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 */

namespace ClinikalAPI\Service;

use ClinikalAPI\Model\ManageTemplatesLettersTable;

trait ManageTemplatesLettersService
{

    public function getLetterList()
    {
        $letters= array();
        $FormContextMapTable= $this->container->get(ManageTemplatesLettersTable::class);
        $param=array("active"=>"1");
        $dbData=$FormContextMapTable->getDataByParams($param);

        foreach($dbData as $index => $letter){
            $letters[$letter['letter_name']] = array("letter_post_json" => json_decode($letter['letter_post_json']));
        }

        return $letters;

    }




}


