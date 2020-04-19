<?php


namespace GenericTools\Library\CreateLetter;

use GenericTools\Model\ListsTable;
use GenericTools\Traits\magicMethods;
use Interop\Container\ContainerInterface;

abstract class AbstractPDFLetter
{
    private $storage = NULL;
    private $filename = NULL;
    private $getPdfService = NULL;

    /**
     * AbstractPDFLetter constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if(!property_exists($this, 'LETTER_NAME') ){
            throw new \Exception(xlt("Variable LETTER_NAME not find"));
        }
    }

    /**
     * @return path to template
     */
    public function getTemplateRoute()
    {
        return $this::LETTER_TEMPLATE;
    }

    /**
     * @return mixed
     */
    public function getTemplateName()
    {
        return static::$LETTER_NAME;
    }

    /**
     * @param $params
     */
    function setParams($params)
    {
        $this->controller   = $params["controller"];
        $this->storage      = $params['storage'];
        $this->filename     = $params['filename'];
        $this->params       = $params;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function draw($params)
    {
        $this->setParams($params);
        $isSuccess = $this->collectData(); //Collect data for letter, into view
        if(!$isSuccess) {
            throw new \Exception(xlt("Please select a patient."));
        }
        $this->getPdfService = $this->controller->container->get('GenericTools\Service\PdfService');
        $this->getPdfService->fileName($this->filename);
        $this->getPdfService->setStandardHeaderFooter(($this->viewParams['showDate'] ? $this->viewParams['showDate']:false));
        $this->getPdfService->body($this->getTemplateRoute(), $this->viewParams);


        $this->getPdfService->returnBinaryString();
        $this->letter_binary = $this->getPdfService->render();


        if (isset($params['onlyIO']) && $params['onlyIO']==="onlyIO") {
            return $this->letter_binary;
        }


        if ($params['storage']!="") {
            $this->getPdfService->outputBinaryPdfToBrowser($this->letter_binary, $this->filename);
        }

        return $this->letter_binary;
    }

    /*
   * 1 - first parametr $dataToParse - array of elemnts after function fetchByIncident
   * 2 - second parametr $arrayOptions, array of links:
   *  key of $dataToParse["injury_form"] and list_id in list_options "moh_rab_waysinjury",
   * 3 - third parametr symbol for glue elements of array.
   * return array of $resultList[key_name] = string,of,elements,separated,by comma or symbol
   * */
    public function parseDataListToChain($dataToParse, $arrayOptions, $glue_symbol = ', ')
    {
        $resultList = [];
        foreach ($arrayOptions as $key => $list_id) {
            $list = $this->controller->container->get(ListsTable::class)->getAllList($list_id);
            $dataToParse[$key] = explode(",", $dataToParse[$key]);
            foreach ($dataToParse[$key] as $k){
                $resultList[$key][] = xlt($list[$k]["title"]);
            }

            //glue array to string
            if(strlen ($glue_symbol) > 0){
                $resultList[$key] = implode($glue_symbol, $resultList[$key]);
            }
        }
        return $resultList;
    }

}
