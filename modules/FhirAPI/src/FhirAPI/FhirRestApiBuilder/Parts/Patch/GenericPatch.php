<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 18/02/20
 * Time: 15:57
 */


namespace FhirAPI\FhirRestApiBuilder\Parts\Patch;
use GenericTools\Model\PatientsTable;

class GenericPatch extends PatchBase
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function patch()
    {
        $dataToUpdate = $this->getDbDataToUpdate();

        if (!empty($dataToUpdate)) {
            return $this->saveData($dataToUpdate);
        } else {
            $requestFlag=$this->isRequestFullyProcessed();
            if($requestFlag){
                return $this->dataNotValidErrorBundle();
            }else{
                return $this->createErrorBundle();
            }
        }
    }

    private function saveData($dataToUpdate)
    {
        $elementId = $this->paramsFromUrl;
        return $this->mapping->updateDbData($dataToUpdate,$elementId[0]);
    }

}
