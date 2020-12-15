<?php

namespace PrintComponents;

/**
 * Components for print in the clinikal modules
 * */
class PrintComponents
{

    public function MohHeader()
    {

    }

    public function table($columns, $data)
    {

        $htmlTable = "<table class='table print-table'>";
        /*thead*/
        $htmlTable.='<thead><tr>';
        foreach ($columns as $key=>$name) {
            $htmlTable.='<th>'.$name.'</th>';
        }
        $htmlTable.='</tr></thead>';
        /*tbody*/
        $htmlTable.= '<tbody>';
        $rows = array();
        foreach ($data as $row) {
            $cells = array();
            foreach ($row as $cell) {
                $cells[] = "<td>{$cell}</td>";
            }
            $rows[] = "<tr>" . implode('', $cells) . "</tr>";
        }
        $htmlTable .= implode('', $rows);
        $htmlTable .= "</tbody></table>";

        return $htmlTable;
    }
}