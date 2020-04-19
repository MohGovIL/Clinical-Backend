<?php

namespace ImportData\Model;

class Codes
{
    public $code_text;
    public $code_text_short;
    public $code;
    public $code_type;
    public $modifier;
    public $units;
    public $fee;
    public $superbill;
    public $related_code;
    public $taxrates;
    public $cyp_factor;
    public $active;
    public $reportable;
    public $financial_reporting;



    public function exchangeArray($data)
    {
        $this->code_text = (!empty($data['code_text'])) ? $data['code_text'] : null;
        $this->code_text_short = (!empty($data['code_text_short'])) ? $data['code_text_short'] : '';
        $this->code = (!empty($data['code'])) ? $data['code'] : '';
        $this->code_type = (!empty($data['code_type'])) ? $data['code_type'] : '';
        $this->modifier = (!empty($data['modifier'])) ? $data['modifier'] : '';
        $this->units = (!empty($data['units'])) ? $data['units'] : '0';
        $this->fee = (!empty($data['fee'])) ? $data['fee'] : '0.00';
        $this->superbill = (!empty($data['superbill'])) ? $data['superbill'] : '';
        $this->related_code = (!empty($data['related_code'])) ? $data['related_code'] : '';
        $this->taxrates = (!empty($data['taxrates'])) ? $data['taxrates'] : '';
        $this->cyp_factor = (!empty($data['cyp_factor'])) ? $data['cyp_factor'] : '0';
        $this->active = (!empty($data['active'])) ? $data['active'] : '0';
        $this->reportable = (!empty($data['reportable'])) ? $data['reportable'] : '0';
        $this->financial_reporting = (!empty($data['financial_reporting'])) ? $data['financial_reporting'] : '0';


    }
}

