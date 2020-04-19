<?php

namespace GenericTools\Model;

class User
{
    public $id;
    public $username;
    public $fname;
    public $mname;
    public $lname;
    public $active;
    public $title;
    public $state_license_number;
    public $facility_id;
    public $federaltaxid;

    public function exchangeArray($data)
    {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->username = (!empty($data['username'])) ? $data['username'] : null;
        $this->fname = (!empty($data['fname'])) ? $data['fname'] : null;
        $this->mname = (!empty($data['mname'])) ? $data['mname'] : null;
        $this->lname  = (!empty($data['lname'])) ? $data['lname'] : null;
        $this->active  = (!empty($data['active'])) ? $data['active'] : null;
        $this->title  = (!empty($data['title'])) ? $data['title'] : null;
        $this->state_license_number  = (!empty($data['state_license_number'])) ? $data['state_license_number'] : null;
        $this->facility_id  = (!empty($data['facility_id'])) ? $data['facility_id'] : null;
        $this->federaltaxid = (!empty($data['federaltaxid'])) ? $data['federaltaxid'] : null;
    }


}
