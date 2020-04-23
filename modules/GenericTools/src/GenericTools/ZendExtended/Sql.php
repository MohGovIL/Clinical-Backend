<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 12/12/18
 * Time: 13:53
 */

namespace GenericTools\ZendExtended;

use Zend\Db\Sql\Sql as ZendSql;
use Laminas\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Sql\PreparableSqlInterface;

/**
 * Class Sql extend of Zend\Db\Sql\Sql
 * This extend save into log table all the insert/update/delete queries.
 * @package GenericTools\ZendExtended
 */

class Sql extends ZendSql
{
    public function prepareStatementForSqlObject(
        PreparableSqlInterface $sqlObject,
        StatementInterface $statement = null,
        AdapterInterface $adapter = null
    ) {
        //original method
        $returnStatement = parent::prepareStatementForSqlObject($sqlObject, $statement, $adapter);
        $sqlClass = get_class($sqlObject);
        // log only insert/update/delete
        if ($sqlClass !== 'Zend\Db\Sql\Select') {
            $event = explode('\\', $sqlClass);
            $event = strtolower($event[count($event) -1]);
            $table  = $this->getTable();
            $sqlStatement = $this->buildSqlString($sqlObject);
            $category = 'general';
            //category name according table prefix
            $tablesPrefixes = array('moh_vac' => 'vaccines',
                                       'mh_mz' => 'addictions-meziga',
                                       'mh_medicine_distribution' => 'addictions-meziga',
                                       'mh_irregular' => 'addictions-meziga',
                                       'moh_test' => 'addictions-urinetest',
                                       'moh' => 'addiction-generic'
                                      );
            foreach ($tablesPrefixes as $prefix => $title) {
                if (strpos($table, $prefix) !== false ) {
                    $category = $title;
                    break;
                }
            }
            $fullEvent = $table . '-' . $event;
            $group = isset($_SESSION['authGroup']) ?  $_SESSION['authGroup'] : "";
            $user = isset($_SESSION['authUser']) ?  $_SESSION['authUser'] : "";
            $pid = isset($_SESSION['pid']) ?  $_SESSION['pid'] : "";

            $sql = "INSERT INTO `log`(`date`, `event`, `user`, `groupname`, `comments`, `user_notes`, `patient_id`, `log_from`, `category`) VALUES (?, ?, ?, ?, ?,NULL ,?, 'zend-modules', ?)";
            sqlQueryNoLog($sql, array(date('Y-m-d H:i:s'),$fullEvent, $user, $group, $sqlStatement, $pid, $category));
        }

        return $returnStatement;
    }
}
