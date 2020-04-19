<?php
/**
 *  @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * base class for form mechanism
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Forms;


use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use GenericTools\Model\FormsGenericHandlerTable;



 class BaseForm implements FormInt
{

     private $container  = null;
     private $adapter  = null;
     private $formQuestionMapping  = null;
     private $encounter  = null;
     private $form_id  = null;



    public function __construct($params)
    {
        $this->container=$params['container'];
        $this->adapter=$params['adapter'];
        $this->formQuestionMapping=$params['formQuestionMapping'];
        $this->encounter=$params['encounter'];
        $this->form_id=$params['form_id'];
    }


    public function getFormAnswers(){

        $formsGenericHandlerTable = $this->container->get(FormsGenericHandlerTable::class);
        $answers=$formsGenericHandlerTable->getFormAnswers($this->formQuestionMapping,$this->encounter,$this->form_id);
        return $answers;
    }

    public function saveFormAnswers($data){

        $encounter=$this->encounter;
        $formId=$this->form_id;
        $formsGenericHandlerTable = $this->container->get(FormsGenericHandlerTable::class);

        foreach ($data['questionsMap'] as $tableName => $questionsIds){

            $records =array();
            foreach ($questionsIds as $index => $questionsType){
                $records[]=array($encounter,$formId,$questionsType,$data['answers'][$questionsType]);
            }
            $formsGenericHandlerTable->insertFormAnswers($tableName,$records);
        }

         return true;
     }

}
