<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 1/25/17
 * Time: 5:54 PM
 */

namespace ImportData\Lists;


class BaseList
{


    protected function createConstantTitle($identify){
        return $this->uniqueListName . '_' . $identify;
    }

}