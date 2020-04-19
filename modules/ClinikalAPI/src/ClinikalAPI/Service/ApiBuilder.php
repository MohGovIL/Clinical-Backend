<?php
/**
 * Date: 05/01/20
 * @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 * This class creates acl filtered api calls array
 */

namespace ClinikalAPI\Service;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use GenericTools\Service\AclCheckExtendedService;
use RestConfig;
use ClinikalAPI\Model\TranslationTables;
use OpenEMR\RestControllers\AuthRestController;
use ClinikalAPI\Service\Settings;
use Interop\Container\ContainerInterface;
use FhirAPI\Service\FhirApiBuilder;
use GenericTools\Model\ListsTable;
use Zend\Db\TableGateway\TableGateway;

class ApiBuilder
{
    private $adapter=null;
    private $container=null;

    CONST MOH_COUNTRY="moh country";
    CONST MOH_CITIES="mh_cities";
    CONST MOH_STREETS="mh_streets";

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->adapter = $container->get('Zend\Db\Adapter\Adapter');
    }


    /**
     * return array with all api calls
     * the array is filtered by acl
     *
     * @return array
     */
    public function getApi()
    {
        $extend_route_map = [
            "GET /api/translation/:lid" => function ($lid) {
                //exit php or return 401 if not authorized
                $this->checkAcl("clinikal_api", "general_settings");
                $transTable = new TranslationTables($this->adapter);
                return $transTable->getAllTranslationByLangId($lid);
            },
            "GET /api/settings/globals/:uid" => function ($uid) {
                //exit php or return 401 if not authorized
                $this->checkAcl("clinikal_api", "general_settings");
                return (new Settings($this->container))->getGlobalsSettings($uid);

            },
            "GET /api/settings/menu/:menu_name" => function ($menu_name) {
                $this->checkAcl("clinikal_api", "general_settings");
                return (new Settings($this->container))->getMenuSettings($menu_name);

            },
            "GET /api/lists/cities" => function () {
                //exit php or return 401 if not authorized
                $this->checkAcl("clinikal_api", "lists");
                $listsTable=$this->container->get(ListsTable::class);
                return $listsTable->getListNormalized(self::MOH_CITIES,null,null,null,false);
            },
            "GET /api/lists/streets/:city_id" => function ($city_id) {
                //exit php or return 401 if not authorized
                $this->checkAcl("clinikal_api", "lists");
                $listsTable=$this->container->get(ListsTable::class);
                return $listsTable->getListNormalized(self::MOH_STREETS,'notes',$city_id,null,null,false);
            },


        ];

        return $extend_route_map;
    }


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


}

