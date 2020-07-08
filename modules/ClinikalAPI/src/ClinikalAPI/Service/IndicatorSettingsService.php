<?php

/**
 * Date: 01/07/2020
 * @author  Dror Golan <drorgo@matrix.co.il>
 */

namespace ClinikalAPI\Service;


use \GenericTools\Model\ListsTable;


trait IndicatorSettingsService
{
    public function getIndicatorSettings($indicator)
    {

        $indicators=array();
        $indicators['constant'];
        $indicators['variant'];
        $listsTable=$this->container->get(ListsTable::class);
        $lionicListTable =  $listsTable->getAllList($indicator,'seq','ASC');
        foreach($lionicListTable as $key=>$val){
            $searchParam = $val['title'];
            $arrTemp=[];
            switch($searchParam){
                case "Oxygen saturation in Blood":
                case "Heart rate --sitting":
                case "Pain severity - 0-10 verbal numeric rating [Score] - Reported":
                case "Body temperature":
                case "Diastolic blood pressure":
                case "Systolic blood pressure":
                case "Glucose [Mass/volume] in Serum, Plasma or Blood":
                case "Respiratory rate --resting":

                        $arrTemp['description'] =  $val['title'];
                        $arrTemp['code'] = $key;
                        $arrTemp['unit'] = $val['subtype'];
                    if($val['notes']!=='') {
                        $notes = json_decode($val['notes']);
                        $arrTemp['label'] =  $notes->label;
                        $arrTemp['mask'] = $notes->mask;
                        $indicators['variant'][$key]=$arrTemp;
                    }else{
                        $arrTemp['label'] = $val['mapping'];
                        $indicators['variant'][$key]=$arrTemp;
                    }
                break;
                case "Body height --standing":
                case "Body weight Estimated":

                $arrTemp['description'] =  $val['title'];
                $arrTemp['code'] = $key;
                $arrTemp['unit'] = $val['subtype'];
                if($val['notes']!=='') {
                    $notes = json_decode($val['notes']);
                    $arrTemp['label'] =  $notes->label;
                    $arrTemp['mask'] = $notes->mask;
                    $indicators['constant'][$key]=$arrTemp;
                }else{
                    $arrTemp['label'] = $val['mapping'];
                    $indicators['constant'][$key]=$arrTemp;
                }
                break;
            }

        }
        return $indicators;
    }
}


