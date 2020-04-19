<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 1/26/17
 * Time: 4:18 PM
 */

namespace ImportData\Model;


class LangDefinitions
{
    public $def_id;
    public $cons_id;
    public $lang_id;
    public $definition;


    public function exchangeArray($data)
    {
        $this->def_id = (!empty($data['def_id'])) ? $data['def_id'] : null;
        $this->cons_id = (!empty($data['cons_id'])) ? $data['cons_id'] : null;
        $this->lang_id = (!empty($data['lang_id'])) ? $data['lang_id'] : null;
        $this->definition = (!empty($data['definition'])) ? $data['definition'] : null;

    }
}