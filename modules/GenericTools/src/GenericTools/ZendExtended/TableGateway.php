<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 12/12/18
 * Time: 13:43
 */

namespace GenericTools\ZendExtended;

use Laminas\Db\TableGateway\TableGateway as ZendTableGateway;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSetInterface;
use GenericTools\ZendExtended\Sql;

class TableGateway extends ZendTableGateway
{

    public function __construct(
    $table,
    AdapterInterface $adapter,
    $features = null,
    ResultSetInterface $resultSetPrototype = null,
    Sql $sql = null
    ) {
        parent::__construct($table, $adapter, $features, $resultSetPrototype, new Sql($adapter, $table));
    }
}
