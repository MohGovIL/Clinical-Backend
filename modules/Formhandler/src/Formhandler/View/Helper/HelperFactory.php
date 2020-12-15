<?php
/**
 * Created by eyal.
 * User: eyalvo
 */

namespace Formhandler\View\Helper;

use Laminas\ServiceManager\ServiceLocatorAwareInterface;


class HelperFactory  extends HelperBase
{

    public function __invoke($className= null,$dataFileName= null,$formName = null, $jsSort = true)
    {
        $container=$this->getSM();
        $dbAdapter=$this->getDbAdapter();
        $class='Formhandler\View\Helper\\'.$className;
        $helper= new $class($container);
        $helper->setDbAdapter( $dbAdapter);
        $get_array=[];
        parse_str($_SERVER['QUERY_STRING'], $get_array);
        $helper->setEditParams($get_array);
        if(is_null($formName)) {
            $frmName = $_SERVER['QUERY_STRING'];
            $frmName = explode("&", $frmName)[0];
            $formName = explode('=', $frmName)[1];
        }

        $path = $GLOBALS['incdir'] . '/forms/' . $formName . '/' . $dataFileName . '.json';

        try {
            $dataFile = file_get_contents($path, false);
        }
        catch(Error $err){

        }

        return $helper->__invoke($dataFile, null, null, $jsSort);
    }

}

