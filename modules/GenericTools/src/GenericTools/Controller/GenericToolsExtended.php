<?php
/**
 * Created by PhpStorm.
 * User: yuriyge
 * Date: 4/15/19
 * Time: 12:17 PM
 */

namespace GenericTools\Controller;

use GenericTools\Controller\BaseController;
use Laminas\ServiceManager\ServiceManager;

class GenericToolsExtended extends BaseController
{

    public function getExtendObject(ServiceManager $sm, $classModelName)
    {
        $classExtended = "GenericToolsExtended\\Module";

        if (class_exists($classExtended)) {
            $classModel = "GenericToolsExtended\\Model\\" . $classModelName . "Extended";
            try{
                $sm->get($classModelName."Extended");
            } catch (\Exception $e){
                //nothing
            }

            if (class_exists($classModel, false)) {
                return new $classModel;
            }
        }
        $classNameExists = str_replace("Controller", "Model", __NAMESPACE__) . "\\" . $classModelName;
        return new $classNameExists;
    }


}
