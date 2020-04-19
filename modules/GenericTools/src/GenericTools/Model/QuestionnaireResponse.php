<?php
/**
* Date: 05/03/20
* @author  Eyal Wolanowski <eyalvo@matrix.co.il>
*/

namespace GenericTools\Model;


class QuestionnaireResponse
{


    public $id;
    public $form_name;
    public $encounter;
    public $subject;
    public $subject_type;
    public $create_date;
    public $update_date;
    public $create_by;
    public $update_by;
    public $source;
    public $source_type;
    public $status;

    public $questionnaire_id;




    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->form_name = (!empty($data['form_name'])) ? $data['form_name'] : null;
        $this->encounter = (!empty($data['encounter'])) ? $data['encounter'] : null;
        $this->subject = (!empty($data['subject'])) ? $data['subject'] : null;
        $this->subject_type = (!empty($data['subject_type'])) ? $data['subject_type'] : null;
        $this->create_date = (!empty($data['create_date'])) ? $data['create_date'] : null;
        $this->update_date = (!empty($data['update_date'])) ? $data['update_date'] : null;
        $this->create_by = (!empty($data['create_by'])) ? $data['create_by'] : null;
        $this->update_by = (!empty($data['update_by'])) ? $data['update_by'] : null;
        $this->source = (!empty($data['source'])) ? $data['source'] : null;
        $this->source_type = (!empty($data['source_type'])) ? $data['source_type'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;

        $this->questionnaire_id = (!empty($data['questionnaire_id'])) ? $data['questionnaire_id'] : null;

    }
}

