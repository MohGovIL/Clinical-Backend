<?php
/**
 * Created by PhpStorm.
 * User: drorgo
 * Date: 3/18/19
 * Time: 8:44 AM
 */

namespace ReportTool\Model;


interface ReportCreatorInterface
{
    CONST GROUPS_AGE_1 = " BETWEEN 0 AND 6 ";
    CONST GROUPS_AGE_2 = " BETWEEN 7 AND 17 ";
    CONST GROUPS_AGE_3 = " BETWEEN 15 AND 17 ";
    CONST GROUPS_AGE_4 = " BETWEEN 18 AND 20 ";
    CONST GROUPS_AGE_5 = " BETWEEN 21 AND 30 ";
    CONST GROUPS_AGE_6 = " BETWEEN 31 AND 40 ";
    CONST GROUPS_AGE_7 = " BETWEEN 41 AND 50 ";
    CONST GROUPS_AGE_8 = " > 50";

    CONST GROUPS_AGE_RESULT_1 = " 0-6 ";
    CONST GROUPS_AGE_RESULT_2 = " 7-17 ";
    CONST GROUPS_AGE_RESULT_3 = " 15-17 ";
    CONST GROUPS_AGE_RESULT_4 = " 18-20 ";
    CONST GROUPS_AGE_RESULT_5 = " 21-30 ";
    CONST GROUPS_AGE_RESULT_6 = " 31-40 ";
    CONST GROUPS_AGE_RESULT_7 = " 41-50 ";
    CONST GROUPS_AGE_RESULT_8 = " > 50";


    public function CreateReportSql($params,$paging);
    public function DoCount($params);
}