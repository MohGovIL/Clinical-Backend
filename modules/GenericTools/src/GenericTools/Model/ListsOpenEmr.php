<?php

namespace GenericTools\Model;

class ListsOpenEmr
{
    public $id;
    public $date;
    public $type;
    public $subtype;
    public $title;
    public $begdate;
    public $enddate;
    public $returndate;
    public $occurrence;
    public $classification;
    public $referredby;
    public $extrainfo;
    public $diagnosis;
    public $activity;
    public $comments;
    public $pid;
    public $list_option_id;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->date = (!empty($data['date'])) ? $data['date'] : null;
        $this->type = (!empty($data['type'])) ? $data['type'] : null;
        $this->subtype = (!empty($data['subtype'])) ? $data['subtype'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->begdate = (!empty($data['begdate'])) ? $data['begdate'] : null;
        $this->enddate = (!empty($data['enddate'])) ? $data['enddate'] : null;
        $this->returndate = (!empty($data['returndate'])) ? $data['returndate'] : null;
        $this->occurrence = (!empty($data['occurrence'])) ? $data['occurrence'] : null;
        $this->classification = (!empty($data['classification'])) ? $data['classification'] : null;
        $this->referredby = (!empty($data['referredby'])) ? $data['referredby'] : null;
        $this->extrainfo = (!empty($data['extrainfo'])) ? $data['extrainfo'] : null;
        $this->diagnosis = (!empty($data['diagnosis'])) ? $data['diagnosis'] : null;
        $this->activity = (!empty($data['activity'])) ? $data['activity'] : null;
        $this->comments = (!empty($data['comments'])) ? $data['comments'] : null;
        $this->pid = (!empty($data['pid'])) ? $data['pid'] : null;
        $this->list_option_id = (!empty($data['list_option_id'])) ? $data['list_option_id'] : null;
    }
}
