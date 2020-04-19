<?php


namespace GenericTools\Model;


class ValueSets
{
    public $id;
    public $title;
    public $active;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->active = (!empty($data['active'])) ? $data['active'] : null;
    }
}
