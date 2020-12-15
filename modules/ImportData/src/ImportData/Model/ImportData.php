<?php

namespace ImportData\Model;

use Laminas\InputFilter\InputFilter;;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

class ImportData
{

    public $id;
    public $external_name;
    public $clinikal_name;
    public $static_name;
    public $source;
    public $update_at;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->external_name = (!empty($data['external_name'])) ? $data['external_name'] : null;
        $this->clinikal_name = (!empty($data['clinikal_name'])) ? $data['clinikal_name'] : null;
        $this->static_name = (!empty($data['static_name'])) ? $data['static_name'] : null;
        $this->source = (!empty($data['source'])) ? $data['source'] : null;
        $this->update_at = (!empty($data['update_at'])) ? $data['update_at'] : null;
    }
}
