<?php

namespace GenericTools\Model;

class Patients
{
    public $id;
    public $fname;
    public $lname;
    public $mname;
    public $ss;
    public $DOB;
    public $pid;
    public $updated_by;
    public $updated_date;
    public $updated_source;
    public $phone_cell;
    public $mh_house_no;
    public $street;
    public $city;
    public $country_code;
    public $email;
    public $postal_code;
    public $mh_pobox;
    public $mh_type_id;
    public $mh_english_name;
    public $deceased_date;
    public $mh_insurance_organiz;


    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->fname = (!empty($data['fname'])) ? $data['fname'] : null;
        $this->lname = (!empty($data['lname'])) ? $data['lname'] : null;
        $this->mname = (!empty($data['mname'])) ? $data['mname'] : null;
        $this->ss = (!empty($data['ss'])) ? $data['ss'] : null;
        $this->DOB = (!empty($data['DOB'])) ? $data['DOB'] : null;
        $this->pid = (!empty($data['pid'])) ? $data['pid'] : null;
        $this->updated_by = (!empty($data['updated_by'])) ? $data['updated_by'] : null;
        $this->updated_date = (!empty($data['updated_date'])) ? $data['updated_date'] : null;
        $this->updated_source = (!empty($data['updated_source'])) ? $data['updated_source'] : null;
        $this->phone_cell = (!empty($data['phone_cell'])) ? $data['phone_cell'] : null;
        $this->street = (!empty($data['street'])) ? $data['street'] : null;
        $this->city = (!empty($data['city'])) ? $data['city'] : null;
        $this->country_code = (!empty($data['country_code'])) ? $data['country_code'] : null;
        $this->mh_house_no = (!empty($data['mh_house_no'])) ? $data['mh_house_no'] : null;
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        $this->postal_code = (!empty($data['postal_code'])) ? $data['postal_code'] : null;
        $this->mh_pobox = (!empty($data['mh_pobox'])) ? $data['mh_pobox'] : null;
        $this->mh_type_id = (!empty($data['mh_type_id'])) ? $data['mh_type_id'] : null;
        $this->sex = (!empty($data['sex'])) ? $data['sex'] : null;
        $this->mh_english_name = (!empty($data['mh_english_name'])) ? $data['mh_english_name'] : null;
        $this->mh_english_name = (!empty($data['mh_english_name'])) ? $data['mh_english_name'] : null;
        $this->deceased_date = (!empty($data['deceased_date'])) ? $data['deceased_date'] : null;
        $this->mh_insurance_organiz = (!empty($data['mh_insurance_organiz'])) ? $data['mh_insurance_organiz'] : null;
    }
}
