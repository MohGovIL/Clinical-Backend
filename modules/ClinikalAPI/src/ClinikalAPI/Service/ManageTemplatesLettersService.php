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
        $FormContextMapTable= $this->container->get(ManageTemplatesLettersTable::class);
        $param=array();
        $dbData=$FormContextMapTable->getDataByParams($param);

        return $dbData;

    }




}


