<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 8/16/16
 * Time: 1:46 PM
 */

namespace ReportTool\Model;

use Zend\Exception;
use Zend\Db\Sql\Sql;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\ValidatorChain;

class CustomDB implements InputFilterAwareInterface
{
    public $sql;
    public function __construct($dbAdapter)
    {

        $this->sql = new Sql($dbAdapter);
    }

    public function CreateReportSql($filters,$procedureName)
    {
        $filterStr=implode($filters,',');
        $sql = "CALL " . $procedureName . "(" . $filterStr . ")";
        $res = sqlStatement($sql);
        $dataSet=array();
        while(  $row = sqlFetchArray($res)) {
            $dataSet[] =$row;
        }
        return $dataSet;
    }

    /**
     * Set input filter
     *
     * @param  InputFilterInterface $inputFilter
     * @return InputFilterAwareInterface
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        // TODO: Implement setInputFilter() method.
    }

    /**
     * Retrieve input filter
     *
     * @return InputFilterInterface
     */
    public function getInputFilter()
    {
        // TODO: Implement getInputFilter() method.
    }


    public function findPatientByName($searchTerm)
    {
        $sql = "SELECT pid as id,concat(lname,' ',fname) as text FROM patient_data WHERE CONCAT( lname,  ' ', fname ) LIKE  '".$searchTerm."'  LIMIT 500" ;
        $res = sqlStatement($sql);
        $dataSet=array();
        while(  $row = sqlFetchArray($res)) {
            $dataSet[] =$row;
        }
        return $dataSet;
    }




}
