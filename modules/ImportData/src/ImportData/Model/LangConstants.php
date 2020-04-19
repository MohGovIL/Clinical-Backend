<?php

namespace ImportData\Model;

class LangConstants
{
    public $cons_id;
    public $constant_name;


    public function exchangeArray($data)
    {
        $this->cons_id = (!empty($data['cons_id'])) ? $data['cons_id'] : null;
        $this->constant_name = (!empty($data['constant_name'])) ? $data['constant_name'] : null;

    }
}