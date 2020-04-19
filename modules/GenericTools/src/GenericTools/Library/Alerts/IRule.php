<?php
/**
 * Created by Dror Golan 24/11/2019
 */


namespace   GenericTools\Library\Alerts;
/**
 * Interface IRule
 * @package GenericTools\Library\Alerts
 */
interface IRule
{
    public function isMatch();
    public function bindRulestoCheck();
    public function bindElementsToArray();
}
