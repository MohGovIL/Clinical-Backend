<?php

/**
 * @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 */


namespace FhirAPI\Model;

class FhirQuestionnaire
{
    //form fhir_questionnaire
    public $id;
    public $name;
    public $directory;
    public $state;
    public $aco_spec;
    //form questionnaires_schemas table
    public $form_tables;
    public $column_types;
    public $questions;
    public $linkIds;  //qid at questionnaires_schemas

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->directory = (!empty($data['directory'])) ? $data['directory'] : null;
        $this->state = (!empty($data['state'])) ? $data['state'] : null;
        $this->aco_spec = (!empty($data['aco_spec'])) ? $data['aco_spec'] : null;

        $this->form_tables = (!empty($data['form_tables'])) ? $data['form_tables'] : null;
        $this->column_types = (!empty($data['column_types'])) ? $data['column_types'] : null;
        $this->questions = (!empty($data['questions'])) ? $data['questions'] : null;
        $this->linkIds = (!empty($data['linkIds'])) ? $data['linkIds'] : null;
    }
}
