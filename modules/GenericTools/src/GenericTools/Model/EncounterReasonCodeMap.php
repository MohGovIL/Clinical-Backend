<?php


namespace GenericTools\Model;


class EncounterReasonCodeMap
{
    public $eid;
    public $reason_code;

    public function exchangeArray($data)
    {
        $this->eid = (!empty($data['eid'])) ? $data['eid'] : null;
        $this->reason_code = (!empty($data['reason_code'])) ? $data['reason_code'] : null;
    }
}
