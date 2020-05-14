<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 1/31/17
 * Time: 10:30 AM
 */

namespace ImportData\Model;

use Laminas\Db\TableGateway\TableGateway;


class ImportDataLogTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function save($data){

        $data = is_object($data) ? get_object_vars($data) : $data;
        $this->tableGateway->insert($data);
    }
}
