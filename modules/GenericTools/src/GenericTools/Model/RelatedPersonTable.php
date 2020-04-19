<?php


namespace GenericTools\Model;

use Zend\Db\TableGateway\TableGateway;

class RelatedPersonTable
{
    use baseTable;
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

}
