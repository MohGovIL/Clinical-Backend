<?php
/**
 * Created by PhpStorm.
 * User: drorgo
 * Date: 3/18/19
 * Time: 9:09 AM
 */

namespace ReportTool\Model;

class ContextCreator
{

    /**
     * @var Strategy The Context maintains a reference to one of the Strategy
     * objects. The Context does not know the concrete class of a strategy. It
     * should work with all strategies via the Strategy interface.
     */
    private $strategy;

    /**
     * Usually, the Context accepts a strategy through the constructor, but also
     * provides a setter to change it at runtime.
     * @param ReportCreatorInterface $strategy
     */
    public function __construct($strategy)
    {
        $className="ReportTool\\Model\\".$strategy;
        $this->strategy = new $className;
    }

    /**
     * Usually, the Context allows replacing a Strategy object at runtime.
     */
    public function setStrategy($strategy)
    {
        $this->strategy = new $strategy;
    }

    /**
     * The Context delegates some work to the Strategy object instead of
     * implementing multiple versions of the algorithm on its own.
     */
    public function DoSomeBusinessLogic($type,$params)
    {
        $result = null;
        $rowAnswer=array();
        switch($type){

            case "count":
                $result =  $this->strategy->DoCount($params);
                $res = sqlStatement("SELECT COUNT(*) rows_count FROM (".$result." ) a ");
                break;
            case "default":
                $result = $this->strategy->CreateReportSql($params,true);
                $res = sqlStatement($result);

                break;

        }


        while(  $row = sqlFetchArray($res)) {
            foreach($row as $col){
                //todo - translation to working with first version of procedure - need fix.
               // $col=xlt($col);
            }
            $rowAnswer[] =$row;
        }

        return $rowAnswer;

    }


}