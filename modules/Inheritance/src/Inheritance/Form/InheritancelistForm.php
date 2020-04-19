<?php

namespace Inheritance\Form;

use Inheritance\Form\BaseForm;

/**
 * Class PumpsForm
 * @package Inheritance\Form
 */
class InheritanceForm extends BaseForm
{


    /**
     * PumpsForm constructor.
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct('Inheritance');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'company_name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'company_name',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => $this->translate->z_xlt('Company name'),
            ),
        ));
        $this->add(array(
            'name' => 'model',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'model',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => $this->translate->z_xlt('Model'),
            ),
        ));
        $this->add(array(
            'name' => 'serial_no',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'serial_no',
                'class' => 'form-control',
                'readonly' => 'readonly'
            ),
            'options' => array(
                'label' => $this->translate->z_xlt('Serial no'),
            ),
        ));
        $this->add(array(
           'name' => 'round_dir',
           'type' => 'select',
           'attributes' => array(
             'id' =>  'round_dir',
             'class' => 'form-control'
           ),
           'options' => array(
                'label' => $this->translate->z_xlt('Round direction'),
                 'value_options' => self::$round_dir_values
           )
        ));
        $this->add(array(
            'name' => 'speed',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'speed',
                'class' => 'form-control',
                //todo default value - here or in JS after getting the pump settings?
                'value' => '80'
            ),
            'options' => array(
                'label' => $this->translate->z_xlt('Speed'),
            ),
        ));
        $this->add(array(
            'name' => 'pipe_diameter',
            'type' => 'select',
            'attributes' => array(
                'id' =>  'pipe_diameter',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => $this->translate->z_xlt('Pipe diameter'),
                'value_options' => self::$pipe_diameter_values
            )
        ));
        //unit weight is MG in case another value is added or changed please implement the logic in the configuration file
        $this->add(array(
            'name' => 'weight_unit',
            'type' => 'select',
            'attributes' => array(
                'id' =>  'weight_unit',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => $this->translate->z_xlt('Weight unit'),
                'value_options' => self::$weight_unit
            )
        ));
        $this->add(array(
            'name' => 'save',
            'type' => 'button',
            'attributes' => array(
                'id' => 'save',
                'class' => 'btn btn-default',
                'disabled' => 'disabled'
            ),
            'options' => array(
                'label' => $this->translate->z_xlt('Save'),
            )
        ));
    }

}