<?php
/**
 * User: Eyal Wolanowski eyalvo@matrix.co.il
 * Date: 17/05/20
 * Time: 16:58
 */

namespace ClinikalAPI\Service;

use GenericTools\Service\SseService;
use ClinikalAPI\Model\ClinikalPatientTrackingChangesTable;
use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use GenericTools\Service\AclCheckExtendedService;

trait ApiTools
{


    /**
     * Check if current user has a given permission
     *
     * @param string $section
     * @param string $value
     * @return bool
     */
    public function checkAcl($section, $value)
    {

        $AclCheckExtendedService= $this->container->get(AclCheckExtendedService::class);
        if( !$AclCheckExtendedService->authorizationCheck($section,$value,false,'write')
            && !$AclCheckExtendedService->authorizationCheck($section,$value,false,'view')
        ){
            ErrorCodes::http_response_code('401','Unauthorized');
        }
        return true;
    }

    /**
     * START SSE live updates
     *
     * @param string $facility_id
     * @return array
     * @throws
     */
    public function patientsTrackingCheckRefresh($facility_id)
    {
        $cptcTable=$this->container->get(ClinikalPatientTrackingChangesTable::class);
        $date=$cptcTable->getLastUpdateDate($facility_id);
        $sse = new SseService($cptcTable,'getLastUpdateDate',$facility_id);
        $sse->connect();
        return array();
    }


    /**
     * Check if current user has a given permission
     *
     * @param string $section
     * @param string $value
     * @return string
     */
    public function getAclType($section, $value)
    {
        $AclCheckExtendedService= $this->container->get(AclCheckExtendedService::class);
        if($AclCheckExtendedService->authorizationCheck($section,$value,false,'write')){
             return "write";
        }
        if($AclCheckExtendedService->authorizationCheck($section,$value,false,'view')){
             return "view";
        }
        return "none";
    }

}


