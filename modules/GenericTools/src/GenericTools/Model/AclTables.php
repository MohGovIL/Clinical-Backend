<?php

namespace GenericTools\Model;

use Laminas\Db\Sql\Sql;

class AclTables
{

    public function __construct($dbAdapter)
    {

        $this->sql = new Sql($dbAdapter);
        $this->adapter = $dbAdapter;
    }


    /**
     * select users that belong to certain aro group
     * @param $aro_group
     * @return array
     */
    public function getUsersByAroGroup($aro_group)
    {
        $sql = "select u.* from `users` as u "
            . "left join `gacl_aro` as ga on  ga.`value` = u.`username` "
            . "left join `gacl_groups_aro_map` as ggam on ggam.`aro_id` = ga.`id` "
            . "where ggam.`group_id` = (select `id` from gacl_aro_groups where `value` = ?) order by u.fname asc, u.lname asc; ";
        $statement = $this->adapter->createStatement($sql, array($aro_group));
        $return = $statement->execute();
        $results = array();
        foreach ($return as $row) {
            $results[$row['id']] = $row;
        }
        return (isset($results)) ? $results : array();

    }


    /**
     * select users that belong to certain aro group
     * @param $aro_group
     * @return array
     */
    public function whatIsUserAroGroups($uid) : array
    {
        $sql = "select g.value from `gacl_aro_groups` as g "
            . "left join `gacl_groups_aro_map` as ggam on ggam.`group_id` = g.`id` "
            . "left join `gacl_aro` as ga on  ga.`id` = ggam.`aro_id` "
            . "where ga.`value` = (select `username` from users where `id` = ?)";
        $statement = $this->adapter->createStatement($sql, array($uid));
        $return = $statement->execute();
        $results = array();
        foreach ($return as $row) {
            $results[] = $row['value'];
        }
        return (isset($results)) ? $results : array();

    }


    public function getAcoForThisGroup($uid)
    {
        $sql = "SELECT return_value,GROUP_CONCAT(gam.section_value) sections,GROUP_CONCAT(gam.value) values_acl
					   FROM gacl_aro ga
                       LEFT JOIN gacl_groups_aro_map ggam ON ggam.aro_id = ga.id
                       LEFT JOIN gacl_aro_groups gag ON gag.id = ggam.group_id
                       LEFT JOIN users u ON ga.value = u.username
                       LEFT JOIN gacl_aro_groups_map gagm ON gagm.group_id = ggam.group_id
                       LEFT JOIN gacl_acl gacl ON gacl.id = gagm.acl_id
                       LEFT JOIN gacl_aco_map gam ON gam.acl_id =  gacl.id AND gam.section_value = 'client_app'
                WHERE u.id = ?
                GROUP BY return_value;";

        $statement = $this->adapter->createStatement($sql, array($uid));
        $return = $statement->execute();

        $acoResults = [];
        foreach ($return as $row) {
            $acoResults[] = $row;
        }


        foreach ($acoResults as $values) {
            $type = $values['return_value'];
            $sections  = explode(',',$values['values_acl']);
            foreach ($sections as $key => $val) {
                $aros[$type][] = $val;
            }
         }
        return (isset($aros)) ? $aros : array();
    }

}
