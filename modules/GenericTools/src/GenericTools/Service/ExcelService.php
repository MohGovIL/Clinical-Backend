<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 07/04/19
 * Time: 16:18
 */

namespace GenericTools\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Interop\Container\ContainerInterface;

class ExcelService
{

    private $spreadsheet;
    private $hasColumnsRow = false;
    private $fileName = 'export.xlsx';

    public function __construct(ContainerInterface $container)
    {
        $this->spreadsheet = new Spreadsheet();
    }

    /**
     * Add title for excel tab
     * @param $title
     */
    public function setTabTitle($title)
    {
        $this->spreadsheet->getActiveSheet()->setTitle($title);
    }

    /**
     * Add row with columns in the top of sheet and bold the row
     * @param array $columns
     */
    public function setColumnsNames(array $columns)
    {
        $this->spreadsheet->getActiveSheet()->fromArray($columns, null, 'A1');
        $from ='A1';
        $to = 'Z1';
        $this->spreadsheet->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold( true );
        $this->hasColumnsRow = true;
    }

    /**
     * Insert array into excel
     * @param array $data
     */
    public function setData(array $data)
    {
        $cellStart = $this->hasColumnsRow ? 'A2' : 'A1';
        $this->spreadsheet->getActiveSheet()->fromArray($data, null, $cellStart);
    }

    /**
     * Change file name
     * @param $fileName
     */
    public function fileName($fileName)
    {
        $this->fileName = $fileName.'.xlsx';
    }

    /**
     * Download xlsx file
     */
    public function downloadFile()
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $this->fileName.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = new Xlsx($this->spreadsheet);
        $writer->save('php://output');
        exit;
    }

}