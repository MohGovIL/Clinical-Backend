<?php
/**
 * Created by Dror Golan 24/11/2019
 */


namespace GenericTools\Library\Alerts;


use GenericTools\Model\Lists;
use SplStack;

/**
 * Class CreateAlerts
 * @package GenericTools\Library\Alerts
 */
class CreateAlerts
{

    private $_rules ;
    private $_directoryToRule;

    /**
     * CreateAlerts constructor.
     * @param $directoryToRule
     */
    public function __construct($directoryToRule)
    {
        $this->_directoryToRule = $directoryToRule;
        $this->_rules = new SplStack();
    }

    /**
     * @param $rule
     * @param null $data
     */
    public function addRules($rule,$data = null){
        $this->_rules->push($rule);
        $this->data = $data;
    }

    /**
     * @return array|string
     */
    public function execute(){
        $message = [];
        $messageReturned = "";
        $this->_rules->rewind();
        while($this->_rules->valid()){
            $createThisClassOfIruleImp =$this->_directoryToRule. $this->_rules->current();
            $rule = new  AlertsContext(new $createThisClassOfIruleImp($this->data));
            $messageReturned =$rule->execute();
            if($messageReturned!=""){
                $message[]=$messageReturned;
            }
            $this->_rules->next();
        }
        return sizeof($message) > 0 ? $message : "";
    }

}
