<?php

namespace ReportTool\Model;

/**
 * Created by PhpStorm.
 * User: drorgo
 * Date: 3/18/19
 * Time: 8:44 AM
 */

class ProcedureCall
{


    public function CreateReportSql($filters,$paging)
    {

        $facility = !empty($filters['facility']) ? ',' . $filters['facility'] . ',' : 0;
        //$facility=0;
        $fromDate = DateToYYYYMMDD($filters['from_date']);
        $toDate = DateToYYYYMMDD($filters['until_date']);
        $destinations = $filters['destinations'] !== 'no_divided_dest' ? 1 : 0;
        $groups = $filters['groups'] !== 'without' ? 1 : 0;
        //todo - fix all parameters
        $status = 0;
        $age = 0;

        $sql = "CALL PatientSumByDestGroup('$facility','$fromDate', '$toDate',$destinations,$groups,$status,$age)";

        return $sql;
    }

    public function DoCount($params){


        $sql = $this->CreateReportSql($params,false);

        return $sql;
    }
}
