<?php


namespace GenericTools\Model;


class HealthcareServices
{

    public $id;
    public $active;
    public $providedBy;
    public $category;
    public $type;
    public $name;
    public $comment;
    public $extraDetails;
    public $availableTime;
    public $notAvailable;
    public $availabilityExceptions;
    public $providedBy_display;
    public $category_display;
    public $type_display;


    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->active = (!empty($data['active'])) ? $data['active'] : null;
        $this->providedBy = (!empty($data['providedBy'])) ? $data['providedBy'] : null;
        $this->category = (!empty($data['category'])) ? $data['category'] : null;
        $this->type = (!empty($data['type'])) ? $data['type'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->comment = (!empty($data['comment'])) ? $data['comment'] : null;
        $this->extraDetails = (!empty($data['extraDetails'])) ? $data['extraDetails'] : null;
        $this->availableTime = (!empty($data['availableTime'])) ? $data['availableTime'] : null;
        $this->notAvailable = (!empty($data['notAvailable'])) ? $data['notAvailable'] : null;
        $this->availabilityExceptions = (!empty($data['availabilityExceptions'])) ? $data['availabilityExceptions'] : null;
        $this->providedBy_display = (!empty($data['providedBy_display'])) ? $data['providedBy_display'] : null;
        $this->category_display = (!empty($data['category_display'])) ? $data['category_display'] : null;
        $this->type_display = (!empty($data['type_display'])) ? $data['type_display'] : null;

    }
}
