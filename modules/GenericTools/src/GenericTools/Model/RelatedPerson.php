<?php


namespace GenericTools\Model;


class RelatedPerson
{
    public $id;
    public $identifier;
    public $identifier_type;
    public $active;
    public $pid;
    public $relationship;
    public $phone_home;
    public $phone_cell;
    public $email;
    public $gender;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->identifier = (!empty($data['identifier'])) ? $data['identifier'] : null;
        $this->identifier_type = (!empty($data['identifier_type'])) ? $data['identifier_type'] : null;
        $this->active = (!empty($data['active'])) ? $data['active'] : null;
        $this->pid = (!empty($data['pid'])) ? $data['pid'] : null;
        $this->relationship = (!empty($data['relationship'])) ? $data['relationship'] : null;
        $this->phone_home = (!empty($data['phone_home'])) ? $data['phone_home'] : null;
        $this->phone_cell = (!empty($data['phone_cell'])) ? $data['phone_cell'] : null;
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        $this->gender = (!empty($data['gender'])) ? $data['gender'] : null;
    }
}
