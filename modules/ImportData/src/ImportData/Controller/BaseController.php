<?php

namespace ImportData\Controller;

use ImportData\Model;
use Laminas\InputFilter\InputFilter;
use Laminas\Mvc\Controller\AbstractActionController;
use Application\Listener\Listener;

use Laminas\Mvc\Controller\ActionController;
use Laminas\View\Model\ViewModel;
use Interop\Container\ContainerInterface;

class BaseController extends AbstractActionController
{


    const ASSETS_FOLDER = 'a';
    const MODULE_FOLDER = 'b';

    private $configParams = null;

    /**
     * path to file after base pass from ModuleconfigController
     * @var array
     */

    protected $jsFiles = array(
/*        array(self::ASSETS_FOLDER , '/jquery-min-1-11-1/index.js'),
        array(self::ASSETS_FOLDER , '/jquery-ui-1-11-4/jquery-ui.js'),
        array(self::ASSETS_FOLDER , '/bootstrap-3-3-4/dist/js/bootstrap.js')*/
    );

    /**
     * path to file after base pass from ModuleconfigController
     * @var array
     */
    protected $cssFiles = array(
/*        array(self::ASSETS_FOLDER , '/bootstrap-3-3-4/dist/css/bootstrap.css'),
        array(self::ASSETS_FOLDER , '/jquery-ui-1-10-4/themes/base/minified/jquery-ui.min.css'),
        array(self::MODULE_FOLDER ,'/style.css')*/
    );

    public function __construct(ContainerInterface $container)
    {

        //load translation class
        $this->translate = new Listener();
        $this->container = $container;
    }

    /**
     * Add js files per method.
     * @param $method __METHOD__ magic constant or __class__
     * @return array
     */
    protected function getJsFiles($method = null)
    {

     /*   switch($method) {

            case 'ImportData\Controller\ImportDataController::edmInitAction':
                $this->jsFiles[] = array(self::MODULE_FOLDER,'/bootstrap-filestyle.min.js');
                break;
        }

        return $this->jsFiles;*/
     }

    /**
     * Add css files per method.
     * get array with 3 parameters
     * 1 - mandatory - file location from zend/public or openemr/assent
     * 2 - mandatory - file name
     * 3- optinal - css media
     * @param $method __METHOD__ magic constant
     * @return array
     */
    protected function getCssFiles($method = null)
    {
        //adding bootstrap rtl for rtl languages
     /*   if ($_SESSION['language_direction'] == 'rtl') {
            $this->cssFiles[] = array(self::ASSETS_FOLDER,'/bootstrap-rtl-3-3-4/dist/css/bootstrap-rtl.min.css');
        }
*/
        switch($method) {


            case 'ImportData\Controller\ImportDataController::indexAction':
                //$this->cssFiles[] = array(self::ASSETS_FOLDER,'/datatables.net-jqui-1-10-13/css/dataTables.jqueryui.min.css');
                $this->cssFiles[] = array(self::MODULE_FOLDER,'/lottery_patients_print.css', 'print');
                break;


                break;
        }

        return $this->cssFiles;
    }


    /**
     * get the current language
     * @return mixed
     */
    protected function getLanguage(){

        /*
        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Laminas\Db\Adapter\Adapter');
        $sql = new customDB($dbAdapter);

        $lang = $sql->getCurrentLang();

        return $lang;
        */
        return 1;
    }

    /**
     * @return post params as array
     */
    protected function getPostParamsArray()
    {
        $putParams = array();
        parse_str($this->getRequest()->getContent(), $putParams);
        return $putParams;
    }
    /**
     * return current user id
     * @return int
     */
    protected function getUserId(){

        return $_SESSION['authUserID'];
    }

    /**
     * enable to add validation for inputs that doesn't exist in the server
     * @param $name - name of input
     * @param $rule - name of rule from getJsValidateConstraints()
     */

    protected function getBasePath(){
        return $this->getRequest()->getUri()->getScheme()."://". $this->getRequest()->getUri()->getHost().$this->getRequest()->getUri()->getPath();
    }
    /**
     * @param $data
     * @param bool $convertToJson
     * @param int $responsecode
     * @return \Laminas\Stdlib\ResponseInterface
     * @comment to use this function return this $response in your controller
     */
    protected function responseWithNoLayout($data, $convertToJson=true, $responsecode=200){
        $response = $this->getResponse();
        $response->setStatusCode($responsecode);
        if($convertToJson) {
            $response->setContent(json_encode($data));
        }
        else{
            $response->setContent($data);
        }
        return $response;
    }

    /**
     *Uniform stracture for ajax response
     * */
    protected function ajaxOutPut($data, $code = 0, $status = 'success'){

        return $this->responseWithNoLayout(array(
            'code' => $code,
            'status' => $status,
            'output' => $data
        ));
    }



    /**
     * function for debugger
     * */
    protected function die_r($dada) {
        echo "<pre>";
        print_r($dada);
        echo "</pre>";
        die;
    }

    protected function getCustomDB(){

        $dbAdapter = $this->container->get('Laminas\Db\Adapter\Adapter');
        $CustomDb = new CustomDb($dbAdapter);
        return $CustomDb;
    }







}
