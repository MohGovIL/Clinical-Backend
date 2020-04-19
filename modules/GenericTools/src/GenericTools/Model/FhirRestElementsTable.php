<?php
/**
 * Date: 29/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class FHIR REST BUILDING TYPES TABLE
 */
namespace GenericTools\Model;

use Zend\Db\TableGateway\TableGateway;

class FhirRestElementsTable
{

    use baseTable;
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }


    /**
     * @param null $sortBy
     * lname Sort by last name in case more variables are needed add their corresponding functions
     * @return array
     */


    public function fetchAll($sortBy = null)
    {
        $rsArray=array();
        $rs =$this->tableGateway->select();
        foreach($rs as $r) {
            $rsArray[]=get_object_vars($r);
        }
        if($sortBy == 'lname') {
            usort($rsArray, function ($a, $b) {
                return $a['lname'] <=> $b['lname'];
            });
        }
        return $rsArray;
    }

    public function getIsActivedByElementName($name)
    {
        $sql="SELECT active FROM " . $this->tableGateway->table . " WHERE name = ? ";

        $statement = $this->tableGateway->adapter->createStatement($sql, array($name));
        $return = $statement->execute();

        $results = array();
        foreach ($return as $row) {
            $results[] = $row;
        }

        return $results[0];
    }


    public function getElementByName($name)
    {
        $sql="SELECT * FROM " . $this->tableGateway->table . " WHERE name = ?";

        $statement = $this->tableGateway->adapter->createStatement($sql, array($pid));
        $return = $statement->execute();

        $results = array();
        foreach ($return as $row) {
            $results[] = $row;
        }

        return $results[0];
    }

}
