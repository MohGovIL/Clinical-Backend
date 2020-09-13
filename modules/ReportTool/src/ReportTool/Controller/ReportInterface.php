<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 14/03/19
 * Time: 11:01
 */

namespace ReportTool\Controller;


interface ReportInterface
{
    public function indexAction();
    public function pdfAction();
    public function excelAction();
    public function getDataAjaxAction();
}
