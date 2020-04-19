<?php

namespace ImportData\Model;

class Lists
{
    public $list_id;
    public $option_id;
    public $title;
    public $activity;
    public $notes;




    public function exchangeArray($data)
    {
        $this->list_id = (!empty($data['list_id'])) ? $data['list_id'] : null;
        $this->option_id = (!empty($data['option_id'])) ? $data['option_id'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->activity = (!empty($data['activity'])) ? $data['activity'] : 0;
        $this->notes = (!empty($data['notes'])) ? $data['notes'] : 0;

    }
}