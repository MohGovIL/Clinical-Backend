<?php


namespace GenericTools\Model;


class Registry
{

    public $name;
    public $state;
    public $directory;
    public $id;
    public $sql_run;
    public $unpackaged;
    public $date;
    public $priority;
    public $category;
    public $nickname;
    public $patient_encounter;
    public $therapy_group_encounter;
    public $aco_spec;

    public $form_tables;
    public $column_types;
    public $questions;
    public $linkIds;  //qid at questionnaires_schemas



    public function exchangeArray($data)
    {
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->state = (!empty($data['state'])) ? $data['state'] : null;
        $this->directory = (!empty($data['directory'])) ? $data['directory'] : null;
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->sql_run = (!empty($data['sql_run'])) ? $data['sql_run'] : null;
        $this->unpackaged = (!empty($data['unpackaged'])) ? $data['unpackaged'] : null;
        $this->date = (!empty($data['date'])) ? $data['date'] : null;
        $this->priority = (!empty($data['priority'])) ? $data['priority'] : null;
        $this->category = (!empty($data['category'])) ? $data['category'] : null;
        $this->nickname = (!empty($data['nickname'])) ? $data['nickname'] : null;
        $this->patient_encounter = (!empty($data['patient_encounter'])) ? $data['patient_encounter'] : null;
        $this->therapy_group_encounter = (!empty($data['therapy_group_encounter'])) ? $data['therapy_group_encounter'] : null;
        $this->aco_spec = (!empty($data['aco_spec'])) ? $data['aco_spec'] : null;

        $this->form_tables = (!empty($data['form_tables'])) ? $data['form_tables'] : null;
        $this->column_types = (!empty($data['column_types'])) ? $data['column_types'] : null;
        $this->questions = (!empty($data['questions'])) ? $data['questions'] : null;
        $this->linkIds = (!empty($data['linkIds'])) ? $data['linkIds'] : null;

    }
}
