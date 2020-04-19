<?php


namespace GenericTools\Model;


class EventCodeReasonMap
{
    public $event_id;
    public $option_id;

    public function exchangeArray($data)
    {
        $this->event_id = (!empty($data['event_id'])) ? $data['event_id'] : null;
        $this->title = (!empty($data['option_id'])) ? $data['option_id'] : null;
    }
}
