<?php

/**
 * Date: 02/06/2020
 *  @author Dror Golan <drorgo@matrix.co.il>
 */


namespace ClinikalAPI\Model;


class ManageTemplatesLetters
{

    public $id;
    public $letter_name;
    public $letter_class;
    public $letter_class_action;
    public $active;
    public $letter_post_json;



    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->letter_name = (!empty($data['letter_name'])) ? $data['letter_name'] : null;
        $this->letter_class = (!empty($data['letter_class'])) ? $data['letter_class'] : null;
        $this->letter_class_action = (!empty($data['letter_class_action'])) ? $data['letter_class_action'] : null;
        $this->active = (!empty($data['active'])) ? $data['active'] : null;
        $this->letter_post_json = (!empty($data['letter_post_json'])) ? $data['letter_post_json'] : null;

    }
}


