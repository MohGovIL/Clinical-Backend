<?php
/**
 * @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * base class for form mechanism
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Forms;


use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;



 class GenericFormHandler
{

    public $formHandler;

    public function __construct($params)
    {
        $this->formHandler=new BaseForm($params);
    }


    public function getFormHandler(){

        return $this->formHandler;
    }

}
