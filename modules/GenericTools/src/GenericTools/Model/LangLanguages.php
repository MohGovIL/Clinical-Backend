<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 17/05/18
 * Time: 13:15
 */

namespace GenericTools\Model;


class LangLanguages
{
    public $lang_id;
    public $lang_code;
    public $lang_description;
    public $lang_is_rtl;

    public function exchangeArray($data)
    {
        $this->lang_id = (!empty($data['lang_id'])) ? $data['lang_id'] : null;
        $this->lang_code = (!empty($data['lang_code'])) ? $data['lang_code'] : null;
        $this->lang_description = (!empty($data['lang_description'])) ? $data['lang_description'] : null;
        $this->lang_is_rtl = (!empty($data['lang_is_rtl'])) ? $data['lang_is_rtl'] : null;
    }
}