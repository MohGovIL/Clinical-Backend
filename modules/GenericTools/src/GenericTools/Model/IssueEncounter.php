<?php

namespace GenericTools\Model;

class IssueEncounter
{
    public $pid;
    public $list_id;
    public $encounter;
    public $resolved;

    public function exchangeArray($data)
    {
        $this->pid = (!empty($data['pid'])) ? $data['pid'] : null;
        $this->list_id = (!empty($data['list_id'])) ? $data['list_id'] : null;
        $this->encounter = (!empty($data['encounter'])) ? $data['encounter'] : null;
        $this->resolved = (!empty($data['resolved'])) ? $data['resolved'] : null;
    }
}
