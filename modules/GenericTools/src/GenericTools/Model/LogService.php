<?php


namespace GenericTools\Model;


class LogService
{

    public $id;
    public $date;
    public $event;
    public $user;
    public $groupname;
    public $comments;
    public $user_notes;
    public $patient_id;
    public $success;
    public $checksum;
    public $crt_user;
    public $log_from;
    public $menu_item_id;
    public $ccda_doc_id;
    public $category;


    public function exchangeArray($data)
    {

        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->id = (!empty($data['date'])) ? $data['date'] : null;
        $this->id = (!empty($data['event'])) ? $data['event'] : null;
        $this->id = (!empty($data['user'])) ? $data['user'] : null;
        $this->id = (!empty($data['groupname'])) ? $data['groupname'] : null;
        $this->id = (!empty($data['comments'])) ? $data['comments'] : null;
        $this->id = (!empty($data['user_notes'])) ? $data['user_notes'] : null;
        $this->id = (!empty($data['patient_id'])) ? $data['patient_id'] : null;
        $this->id = (!empty($data['success'])) ? $data['success'] : null;
        $this->id = (!empty($data['checksum'])) ? $data['checksum'] : null;
        $this->id = (!empty($data['crt_user'])) ? $data['crt_user'] : null;
        $this->id = (!empty($data['log_from'])) ? $data['log_from'] : null;
        $this->id = (!empty($data['menu_item_id'])) ? $data['menu_item_id'] : null;
        $this->id = (!empty($data['ccda_doc_id'])) ? $data['ccda_doc_id'] : null;
        $this->id = (!empty($data['category'])) ? $data['category'] : null;

    }

}
