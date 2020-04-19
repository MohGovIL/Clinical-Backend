<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 17/05/18
 * Time: 13:15
 */

namespace GenericTools\Model;


class Facility
{
    public $id;
    public $name;
    public $phone;
    public $street;
    public $city;
    public $state;
    public $email;
    public $postal_code;
    public $facility_code;
    public $attn;
    public $fax;
    public $info;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->phone = (!empty($data['phone'])) ? $data['phone'] : null;
        $this->street = (!empty($data['street'])) ? $data['street'] : null;
        $this->city = (!empty($data['city'])) ? $data['city'] : null;
        $this->state = (!empty($data['state'])) ? $data['state'] : null;
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        $this->postal_code = (!empty($data['postal_code'])) ? $data['postal_code'] : null;
        $this->facility_code = (!empty($data['facility_code'])) ? $data['facility_code'] : null;
        $this->attn = (!empty($data['attn'])) ? $data['attn'] : null;
        $this->fax = (!empty($data['fax'])) ? $data['fax'] : null;
        $this->info = (!empty($data['info'])) ? $data['info'] : null;
    }
}
