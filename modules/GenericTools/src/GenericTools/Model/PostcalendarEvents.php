<?php

namespace GenericTools\Model;

class PostcalendarEvents
{
    public $pc_eid;
    public $pc_apptstatus;
    public $pc_catid;
    public $pc_title;
    public $pc_time;
    public $pc_hometext;
    public $pc_startTime;
    public $pc_eventDate;
    public $pc_endDate;
    public $pc_duration;
    public $pc_priority;
    public $pc_service_type;
    public $pc_healthcare_service_id;

    /*
    public $reason_ids;
    public $reason_titles;
    public $reason_sequences;
    public $service_title;
    public $service_seq;
    */



    public function exchangeArray($data)
    {
        $this->pc_eid = (!empty($data['pc_eid'])) ? $data['pc_eid'] : null;
        $this->pc_apptstatus = (!empty($data['pc_apptstatus'])) ? $data['pc_apptstatus'] : null;
        $this->pc_catid = (!empty($data['pc_catid'])) ? $data['pc_catid'] : null;
        $this->pc_title = (!empty($data['pc_title'])) ? $data['pc_title'] : null;
        $this->pc_time = (!empty($data['pc_time'])) ? $data['pc_time'] : null;
        $this->pc_hometext = (!empty($data['pc_hometext'])) ? $data['pc_hometext'] : null;
        $this->pc_startTime = (!empty($data['pc_startTime'])) ? $data['pc_startTime'] : null;
        $this->pc_eventDate = (!empty($data['pc_eventDate'])) ? $data['pc_eventDate'] : null;
        $this->pc_endDate = (!empty($data['pc_endDate'])) ? $data['pc_endDate'] : null;
        $this->pc_duration = (!empty($data['pc_duration'])) ? $data['pc_duration'] : null;
        $this->pc_priority = (!empty($data['pc_priority'])) ? $data['pc_priority'] : null;
        $this->pc_service_type = (!empty($data['pc_service_type'])) ? $data['pc_service_type'] : null;
        $this->pc_healthcare_service_id = (!empty($data['pc_healthcare_service_id'])) ? $data['pc_healthcare_service_id'] : null;




    }
}
