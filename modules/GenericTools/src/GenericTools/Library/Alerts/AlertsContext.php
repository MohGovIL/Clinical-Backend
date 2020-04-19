<?php
/**
 * Created by Dror Golan 24/11/2019
 */


namespace GenericTools\Library\Alerts;
/**
 * Class AlertsContext
 * @package GenericTools\Library\Alerts
 */
class AlertsContext
{

    private $strategy = NULL;

    /**
     * CreateLetterContext constructor.
     * @param ICreateLetter $strategy
     */
    public function __construct(IRule $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function Execute($params = null)
    {
       //check the rules
        return $this->strategy->isMatch();
    }


}
