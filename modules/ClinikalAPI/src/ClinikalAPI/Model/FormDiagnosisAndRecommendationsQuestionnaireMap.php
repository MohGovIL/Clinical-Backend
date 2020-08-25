<?php

/**
 * Date: 25/09/2020
 *  @author Dror Golan <drorgo@matrix.co.il>
 */

namespace ClinikalAPI\Model;


class FormDiagnosisAndRecommendationsQuestionnaireMap
{

    public  $id,
            $encounter,
            $form_id,
            $question_id,
            $answer;



    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->encounter = (!empty($data['encounter'])) ? $data['encounter'] : null;
        $this->form_id = (!empty($data['form_id'])) ? $data['form_id'] : null;
        $this->question_id = (!empty($data['question_id'])) ? $data['question_id'] : null;
        $this->answer = (!empty($data['answer'])) ? $data['answer'] : null;
    }
}
