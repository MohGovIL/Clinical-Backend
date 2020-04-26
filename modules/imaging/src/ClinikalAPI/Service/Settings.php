<?php
/**
 * Date: 05/01/20
 * @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 * This class creates acl filtered api calls array
 */

namespace ClinikalAPI\Service;

use OpenEMR\RestControllers\RestControllerHelper;
use PHP_CodeSniffer\Reports\Json;
use GenericTools\Model\AclTables;
use GenericTools\Model\LangLanguagesTable;
use GenericTools\Model\UserTable;
use Interop\Container\ContainerInterface;
use ClinikalAPI\Service\ApiBuilder;

class Settings
{
    private $adapter=null;
    private $container=null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
    }

    /**
     * return array with needed global settings
     *
     * @return array
     */
    public function getGlobalsSettings($uid)
    {

        $aclTables = new AclTables($this->adapter);
        $userTable = $this->container->get(UserTable::class);
        $langLanguagesTable= $this->container->get(LangLanguagesTable::class);
        $user=$userTable->getUser($uid);

        $langId=(!is_null($_SESSION['language_choice'])) ? $_SESSION['language_choice'] : $langLanguagesTable->getLangIdByGlobals();
        $settings = array(
            "facility" => $user->facility_id,
            "lang_id" => $langId,
            "lang_code" => $langLanguagesTable->getLangCode($langId),
            "lang_dir" => getLanguageDir($langId),
            "format_date" => DateFormatRead('validateJS'),
            "user_role" => $aclTables->whatIsUserAroGroups($uid),
            "user_aco" =>$aclTables->getAcoForThisGroup($uid),
            "clinikal_vertical" => 'imaging'
        );

        return RestControllerHelper::responseHandler($settings, null, 200);
    }

    /**
     * return the json of the menu by given name
     * the json is filtered by acl
     *
     * @return Json
     */
    public function getMenuSettings($menuName)
    {
        $file = file_get_contents($GLOBALS['fileroot'] . "/sites/default/documents/custom_menus/" . $menuName . ".json");

        if($file!==false){
            $menu_parsed = json_decode($file, true);
            $menu_parsed = $this->checkMenuAcl($menu_parsed);
        }else{
            $menu_parsed=array();
        }
        return RestControllerHelper::responseHandler($menu_parsed, null, 200);
    }



    /**
     * remove menu items that the current user does not have permission to watch
     *
     * @param array $menu
     * @return array
     */
    private function checkMenuAcl($menu)
    {
        foreach ($menu as $index => $element) {

            if (!empty($element['acl_req']) && !empty($element['acl_req'][0]) && !empty($element['acl_req'][1])) {
                $checkAcl = ApiBuilder::authorization_check($element['acl_req'][0], $element['acl_req'][1]);
                if (!$checkAcl) {
                    unset($menu[$index]);
                }
                if (!empty($element['children'])) {
                    $element['children'] = $this->checkMenuAcl($element['children']);
                }
            }
            //todo check global settings - global_req
            if (isset($menu[$index]['acl_req'])) {
                unset($menu[$index]['acl_req']);
            }
            if (isset($menu[$index]['global_req'])) {
                unset($menu[$index]['global_req']);
            }
        }

        return $menu;
    }


}

