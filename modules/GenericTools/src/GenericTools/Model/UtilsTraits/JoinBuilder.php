<?php

namespace GenericTools\Model\UtilsTraits;

use Laminas\Db\Sql\Join;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\TableIdentifier;

trait JoinBuilder
{
    private  $allJoins = array();

    /**
     * @param string|array|TableIdentifier $name A table name on which to join, or a single
     *     element associative array, of the form alias => table, or TableIdentifier instance
     * @param string|Predicate\Expression $on A specification describing the fields to join on.
     * @param string|string[]|int|int[] $columns A single column name, an array
     *     of column names, or (a) specification(s) such as SQL_STAR representing
     *     the columns to join.
     * @param string $type The JOIN type to use; see the JOIN_* constants.
     * @return self Provides a fluent interface
     * @throws Exception\InvalidArgumentException for invalid $name values.
     */
    public function appendJoin($name, $on, $columns = [Select::SQL_STAR], $type = Join::JOIN_INNER)
    {
        $this->allJoins['join_with'][] = $name;
        $this->allJoins['ON'][] = $on;
        $this->allJoins['select'][] = $columns;
        $this->allJoins['join_type'][] = $type;
    }

    public function addGroupForJoin($group)
    {
        $this->allJoins['GROUP'][] = $group;
    }

    public function getJoins()
    {
        return $this->allJoins;
    }
    public function clearAllJoin(){
        $this->allJoins = array();
    }
}
