<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\ListsTable;
use GenericTools\Model\UtilsTraits\JoinBuilder;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
use laminas\Db\Sql\Expression;
use laminas\Db\Sql\Select;

class MedicationStatementSearch extends BaseSearch
{
    const OUTCOME_LIST ='outcome';

    use JoinBuilder;
    public $paramsToDB = array();
    public $MAIN_TABLE = 'lists';

    public function search()
    {
        $this->paramHandler('_id', 'id');
        $this->paramHandler('active', 'active');
        $this->paramHandler('context','encounter');

        if (isset($this->searchParams['status'])) {
            $code = $this->searchParams['status'][0]['value'];

            // to support search by status string
            if (!ctype_digit($code)) {
                $ListsTable = $this->container->get(ListsTable::class);
                $listOutcome = array_flip($ListsTable->getListNormalized(self::OUTCOME_LIST));
                $code = $listOutcome[$code];
                if (!is_null($code)) {
                    $this->searchParams['status'][0]['value'] = $code;
                } else {
                    unset($this->searchParams['status']);
                }
            }
        }

        $this->paramHandler('status', 'outcome');

        if (isset($this->searchParams['code:of-type'])) {
            $codeSearch = $this->searchParams['code:of-type'][0]['value'];     // format |system|code|identifier
            $codeSearchArr = explode('|', $codeSearch);
            if (count($codeSearchArr) > 2) {
                $codeSearch = $codeSearchArr[1] . ':' . $codeSearchArr[2];
                $this->searchParams['code:of-type'][0]['value'] = $codeSearch;
            }
            $this->paramHandler('code:of-type', 'diagnosis');
        }

        $this->paramHandler('patient', 'pid');

        $this->paramsToDB[$this->MAIN_TABLE . '.' . "type"] = "medication";

        $this->searchParams = $this->paramsToDB;
        $this->runMysqlQuery();
        return $this->FHIRBundle;

    }

}
