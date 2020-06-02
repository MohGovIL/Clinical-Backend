<?php
/**
 * Created by PhpStorm.
 * User: shaharzi
 * Date: 04/09/16
 * Time: 21:13
 */


namespace Inheritance\Model;

use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

/*
 * this is the basic structure of Model
 * */
class Networking /*implements InputFilterAwareInterface*/
{
    //properties of all columns (from sql table) that we want

    public $id;
    public $clinic_name;
    public $clinic_id;
    public $type;
    public $valid;
    public $visible;
    public $parent;
    public $url;
    public $server_ip;
    public $files_location;
    public $version;



    //must add it for all the properties
    public function exchangeArray($data)
    {
        $this->id  = (!empty($data['id'])) ? $data['id'] : 0;
        $this->clinic_name     = (!empty($data['clinic_name'])) ? $data['clinic_name'] : null;
        $this->clinic_id = (!empty($data['clinic_id'])) ? $data['clinic_id'] : null;
        $this->type = (!empty($data['type'])) ? $data['type'] : null;
        $this->valid = (!empty($data['valid'])) ? $data['valid'] : 1;
        $this->visible = (!empty($data['visible'])) ? $data['visible'] : 1;
        $this->parent = (!empty($data['parent'])) ? $data['parent'] : null;
        $this->url = (!empty($data['url'])) ? $data['url'] : null;
        $this->server_ip = (!empty($data['server_ip'])) ? $data['server_ip'] : null;
        $this->files_location = (!empty($data['files_location'])) ? $data['files_location'] : null;
        $this->version = (!empty($data['version'])) ? $data['version'] : null;
    }


}
