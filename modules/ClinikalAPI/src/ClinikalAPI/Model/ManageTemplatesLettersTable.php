<?php


namespace ClinikalAPI\Model;

use Laminas\Db\TableGateway\TableGateway;
use GenericTools\Model\UtilsTraits\JoinBuilder;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use GenericTools;

class ManageTemplatesLettersTable
{
    use GenericTools\Model\baseTable;

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
}
