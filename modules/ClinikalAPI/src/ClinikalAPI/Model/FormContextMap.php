<?php

/**
 * Date: 29/01/20
 *  @author Eyal Wolanowski <eyalvo@matrix.co.il>
 */

namespace ClinikalAPI\Model;


class FormContextMap
{

    public $form_id;
    public $context_type; // service_type OR  reason_code
    public $context_id;

    //registry fields for join
    public $name;
    public $state;
    public $unpackaged;
    public $sql_run;
    public $priority;
    public $category;
    public $directory;
    public $nickname;
    public $aco_spec;
    public $component_name;


    public function exchangeArray($data)
    {
        $this->form_id = (!empty($data['form_id'])) ? $data['form_id'] : null;
        $this->context_type = (!empty($data['context_type'])) ? $data['context_type'] : null;
        $this->context_id = (!empty($data['context_id'])) ? $data['context_id'] : null;

        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->state = (!empty($data['state'])) ? $data['state'] : null;
        $this->unpackaged = (!empty($data['unpackaged'])) ? $data['unpackaged'] : null;
        $this->sql_run = (!empty($data['sql_run'])) ? $data['sql_run'] : null;
        $this->priority = (!empty($data['priority'])) ? $data['priority'] : null;
        $this->category = (!empty($data['category'])) ? $data['category'] : null;
        $this->directory = (!empty($data['directory'])) ? $data['directory'] : null;
        $this->nickname = (!empty($data['nickname'])) ? $data['nickname'] : null;
        $this->aco_spec = (!empty($data['aco_spec'])) ? $data['aco_spec'] : null;
        $this->component_name = (!empty($data['component_name'])) ? $data['component_name'] : null;

    }
}
