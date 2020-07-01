<?php

/**
 * Date: 29/01/20
 *  @author Eyal Wolanowski <eyalvo@matrix.co.il>
 */

namespace ClinikalAPI\Model;


class ListOptions
{
    public $list_id;
    public $option_id; // service_type OR  reason_code
    public $title;
    public $seq;
    public $mapping;
    public $notes;
    public $activity;

    public function exchangeArray($data)
    {
        $this->list_id = (!empty($data['list_id'])) ? $data['list_id'] : null;
        $this->option_id = (!empty($data['option_id'])) ? $data['option_id'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->seq = (!empty($data['seq'])) ? $data['seq'] : null;
        $this->mapping = (!empty($data['mapping'])) ? $data['mapping'] : null;
        $this->notes = (!empty($data['notes'])) ? $data['notes'] : null;
        $this->activity = (!empty($data['activity'])) ? $data['activity'] : null;
    }
}
