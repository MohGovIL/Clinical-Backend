<?php

/**
 * @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 */


namespace FhirAPI\Model;

class FhirValidationSettings
{
    //for fhir_validation_settings
    public $id;
    public $fhir_element;
    public $filed_name;
    public $request_action;
    public $validation;
    public $type;
    public $active;
    public $validation_param;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->fhir_element = (!empty($data['fhir_element'])) ? $data['fhir_element'] : null;
        $this->filed_name = (!empty($data['filed_name'])) ? $data['filed_name'] : null;
        $this->request_action = (!empty($data['request_action'])) ? $data['request_action'] : null;
        $this->validation = (!empty($data['validation'])) ? $data['validation'] : null;
        $this->type = (!empty($data['type'])) ? $data['type'] : null;
        $this->active = (!empty($data['active'])) ? $data['active'] : null;
        $this->validation_param = (!empty($data['validation_param'])) ? $data['validation_param'] : null;

    }

}
