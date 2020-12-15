<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 5/10/17
 * Time: 4:36 PM
 */
namespace Inheritance\Model;

use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

/*
 * this is the basic structure of Model
 * */
class NetworkingDB /*implements InputFilterAwareInterface*/
{

    public $id;
    public $clinic_id;
    public $username;
    public $password;
    public $dbname;
    public $host;
    public $port;
    public $date;


    //must add it for all the properties
    public function exchangeArray($data)
    {
        $this->id  = (!empty($data['id'])) ? $data['id'] : 0;
        $this->clinic_id     = (!empty($data['clinic_id'])) ? $data['clinic_id'] : null;
        $this->username = (!empty($data['username'])) ? $data['username'] : null;
        $this->password = (!empty($data['password'])) ? $data['password'] : null;
        $this->dbname = (!empty($data['dbname'])) ? $data['dbname'] : null;
        $this->host = (!empty($data['host'])) ? $data['host'] : null;
        $this->port = (!empty($data['port'])) ? $data['port'] : null;
        $this->date = (!empty($data['date'])) ? $data['date'] : date('Y-m-d H:i:s');
    }


}
