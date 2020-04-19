<?php

namespace Inheritance\Form;

use Zend\Form\Form;
use Application\Listener\Listener;

/**
 * Class BaseForm
 * @package Inheritance\Form
 */
class BaseForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct($name);

        //load translation class
        //now the translate load in the base controller and base form
        $this->translate = new Listener();
    }


    /**
     * Adding bootstrap form structure
     * @param $formRow
     * @return string
     */
    public function formBootstrapRow($formRow)
    {
        $output = '<div class="form-group">';
        $output .= $formRow;
        $output .= '</div>';

        return $output;
    }
}