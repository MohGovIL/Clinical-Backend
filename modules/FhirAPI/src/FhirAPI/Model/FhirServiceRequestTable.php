<?php

/**
 * @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 */

namespace FhirAPI\Model;

use Laminas\Db\TableGateway\TableGateway;
use GenericTools;
use GenericTools\Model\UtilsTraits\JoinBuilder;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;

class FhirServiceRequestTable
{
    protected $tableGateway;

    use GenericTools\Model\baseTable;
    use JoinBuilder;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->join = array();
    }
}
