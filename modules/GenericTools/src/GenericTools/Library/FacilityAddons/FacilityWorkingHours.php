<?php

/**
 * Class FacilityWorkingHours
 * Created by Dror Golan 28/11/2019
 * This is a static class that parse the working hours created in
 * The UI facility addons and saved in the DB in the form of a JSON.
 */

namespace GenericTools\Library\FacilityAddons;

use GenericTools\Model\Facility;
use PhpOffice\PhpSpreadsheet\Calculation\Token\Stack;

class FacilityWorkingHours
{
    //return array of days enum to use with the json data format.
    static function getDaysArray(){
        return  ['st','fr','th','we','tu','mo','su'];
    }
    //fill the days into a stack a container to use in order to check days elements
    static function fillDaysIntoStack(&$stackDaysArray,$fromday=false){

        $days = self::getDaysArray();
        $counter = 0 ;
        if($fromday) {

            foreach ($days as $key=>$value) {
                if($fromday == $value) {
                    $keyToUnset = $key;
                }
            }

            for ($i = $keyToUnset ; $i <= 6 ;$i++){
                unset ($days[$i]);
            }


            while (!empty($days)) {
                $stackDaysArray->push('string', $days[$counter]);
                unset($days[$counter++]);
            }
        }
        else{
            $stackDaysArray->push('string','st');
            $stackDaysArray->push('string','fr');
            $stackDaysArray->push('string','th');
            $stackDaysArray->push('string','we');
            $stackDaysArray->push('string','tu');
            $stackDaysArray->push('string','mo');
            $stackDaysArray->push('string','su');
        }

    }

    static function insideStampLoop($start,$end,$element,&$counter,&$infoArray,$dayFromStack,&$stamp,&$result){
        foreach ($infoArray['days_info'][$dayFromStack['value']] as $key=>$hours) {
            if($start == $hours['start_at'] && $end == $hours['end_at'] ){
                $stamp = self::getDayEnum($element) . " - " . self::getDayEnum($dayFromStack['value']);
                unset($infoArray['days_info'][$dayFromStack['value']][$key]);
                $counter--;
                continue;
            }
            else{
                if($counter >0)
                    continue;

                $result =false;
                return $stamp;
            }
            $counter++;
        }
        if($counter >0) {
            $result = false;
        }
        else{
            $result = true;
        }
        return $stamp;
    }
    //check for continuity in dats hours and collect them if no continuity collect singl day
    static function collectContinuityOfDayInArray($element,$start,$end,&$infoArray){

        $stackDaysArrayTemp = new Stack();
        $stamp =  self::getDayEnum($element);
        self::fillDaysIntoStack($stackDaysArrayTemp,$element);
        while($stackDaysArrayTemp->count() > 0) {

            $dayFromStack = $stackDaysArrayTemp->pop();
            if($infoArray['days_info'][$dayFromStack['value']] == null)
                return $stamp;
            $result = true;
            $counter = 1;
            $stamp =  self::insideStampLoop($start,$end,$element,$counter,$infoArray,$dayFromStack,$stamp,$result);
          /*  foreach ($infoArray['days_info'][$dayFromStack['value']] as $key=>$hours) {
                if($start == $hours['start_at'] && $end == $hours['end_at'] ){
                    $stamp = self::getDayEnum($element) . " - " . self::getDayEnum($dayFromStack['value']);
                    unset($infoArray['days_info'][$dayFromStack['value']][$key]);
                    $counter--;
                    continue;
                }
                else{
                    if($counter >0)
                        continue;

                    return $stamp;
                }
                $counter++;
            }*/
            if($result == false){
                return $stamp != "" ? $stamp : self::getDayEnum($element);
            }
        }
        return $stamp != "" ? $stamp : self::getDayEnum($element);
    }

    //return day enum in a format of full day name or prefix day
    static function getDayEnum($day,$title=false){
        $dayTitle = $dayEnum = "";
        switch ($day) {

            case 'su' :
                $dayTitle = xlt('Sunday');
                $dayEnum = xlt('Su{{Sunday}}')."'";
                break;

            case 'mo' :
                $dayTitle = xlt('Monday');
                $dayEnum = xlt('Mo{{Monday}}')."'";
                break;

            case 'tu' :
                $dayTitle = xlt('Tuesday');
                $dayEnum = xlt('Tu{{Tuesday}}')."'";
                break;

            case 'we' :

                $dayTitle = xlt('Wednesday');
                $dayEnum = xlt('We{{Wednesday}}')."'";
                break;

            case 'th' :

                $dayTitle = xlt('Thursday');
                $dayEnum = xlt('Th{{Thursday}}')."'";
                break;

            case 'fr' :

                $dayTitle = xlt('Friday');
                $dayEnum = xlt('Fr{{Friday}}')."'";
                break;

            case 'st' :

                $dayTitle = xlt('Saturday');
                $dayEnum = xlt('Sa{{Saturday}}')."'";
                break;
        }
        if($title) {
            return $dayTitle;
        }


        return $dayEnum;

    }

    //create the visiulization of the working hours as a stmap to be added into pdfs docs ui .......
    static function createContinuityOfDaysStamp($infoArray){

        if($infoArray == NULL)
            return "";


        $stamp = '<div><table align="'. ($_SESSION['language_direction']=='rtl'?'right':'left').'">';
        $stackDaysArray = new Stack();

        self::fillDaysIntoStack($stackDaysArray);



        while($stackDaysArray->count()>0) {
            $dayFromStack = $stackDaysArray->pop();

            while(sizeof($infoArray['days_info'][$dayFromStack['value']]) > 0)
            {
                foreach ($infoArray['days_info'][$dayFromStack['value']] as $key => $hours) {



                    $start_at_marker = $hours['start_at'];
                    $end_at_marker = $hours['end_at'];
                    unset($infoArray['days_info'][$dayFromStack['value']][$key]);

                    $stamp .= "<tr><td>" . xlt("Day") . " " . self::collectContinuityOfDayInArray( $dayFromStack['value'], $start_at_marker, $end_at_marker, $infoArray) . "</td><td>"  . $end_at_marker. " - ".$start_at_marker  ."</td></tr>";

                }
                $dayFromStack = $stackDaysArray->pop();
            }

        }
        $stamp .= "</table></div>";
        return $stamp;

    }

    //get The current facility it is the same code in generic tools.
    // In order to avoid calling it from there I copied the static function to here .

    static public function getCurrentFacility($onlyId = false, $onlyName = false)
    {

        if ($GLOBALS['login_into_facility']) {
            $facility = getFacility();
        } else {
            // get default of user
            $facility = getFacility(-1);
        }
        if ($onlyId) {
            return $facility['id'];
        }
        if ($onlyName) {
            return  $facility['name'];
        }
        $facilityObject = new Facility();
        $facilityObject->exchangeArray($facility);
        return $facilityObject;
    }

    //parse the working hours as a text stamp
    static function parseFacilityWorkingHoursAsStamp(){

        $facility =self::getCurrentFacility();
        if($facility->info == null)
            return "";

        $infoString = $facility->info;
        $infoArray = json_decode($infoString,true);
        $continuityArrayToParse = self::createContinuityOfDaysStamp($infoArray);
        return $continuityArrayToParse;//."<br/><br/> --FOR TEST ONLY--- </br></br>".json_encode($infoString);

    }

}
