<?php
/**
 * Created by DROR GOLAN.
 * User: drorgo
 */

namespace Formhandler\View\Helper;
use Formhandler\Model\customDB;
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;




class HelperBase extends AbstractHelper
{
    protected $dbAdapter;
    protected $sm;
    private $edit_params = null;

    public function setEditParams($edit_params){
        $this->edit_params = $edit_params;
    }


    protected function getCustomDB(){
        $CustomDb = new CustomDb( $this->dbAdapter);
        return $CustomDb;
    }


    public function __construct($container)
    {
        $this->container = $container;
    }


    public function setDbAdapter($adapter){
        $this->dbAdapter=$adapter;
    }


    public function __invoke($data= null)
    {
        echo('base');
    }

    public function getSM(){
        return  $this->container;
    }

    public function getDbAdapter(){
        return  $this->dbAdapter;
    }

}

