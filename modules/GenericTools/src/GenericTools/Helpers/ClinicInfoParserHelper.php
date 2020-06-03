<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 25/06/19
 * Time: 16:17
 */

namespace GenericTools\Helpers;


use GenericTools\Controller\GenericToolsController;
use GenericTools\Library\FacilityAddons\FacilityWorkingHours;
use Laminas\Db\Exception\ErrorException;

class ClinicInfoParserHelper
{


  public function ClinicInfoParserHelper($type){
      if($type=='working_hours') {
          echo FacilityWorkingHours::parseFacilityWorkingHoursAsStamp();
      }
  }



}
