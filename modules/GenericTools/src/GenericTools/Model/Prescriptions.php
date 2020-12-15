<?php


namespace GenericTools\Model;


class Prescriptions
{

    public $id;
    public $patient_id;
    public $filled_by_id;
    public $pharmacy_id;
    public $date_added;
    public $date_modified;
    public $provider_id;
    public $encounter;
    public $start_date;
    public $drug;
    public $drug_id;
    public $form;
    public $dosage;
    public $quantity;
    public $size;
    public $unit;
    public $route;
    public $interval;
    public $substitute;
    public $refills;
    public $per_refill;
    public $filled_date;
    public $medication;
    public $note;
    public $active;
    public $datetime;
    public $user;
    public $site;
    public $prescriptionguid;
    public $external_id;
    public $end_date;
    public $indication;



    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->patient_id = (!empty($data['patient_id'])) ? $data['patient_id'] : null;
        $this->filled_by_id = (!empty($data['filled_by_id'])) ? $data['filled_by_id'] : null;
        $this->pharmacy_id = (!empty($data['pharmacy_id'])) ? $data['pharmacy_id'] : null;
        $this->date_added = (!empty($data['date_added'])) ? $data['date_added'] : null;
        $this->date_modified = (!empty($data['date_modified'])) ? $data['date_modified'] : null;
        $this->provider_id = (!empty($data['provider_id'])) ? $data['provider_id'] : null;
        $this->encounter = (!empty($data['encounter'])) ? $data['encounter'] : null;
        $this->start_date = (!empty($data['start_date'])) ? $data['start_date'] : null;
        $this->drug = (!empty($data['drug'])) ? $data['drug'] : null;
        $this->drug_id = (!empty($data['drug_id'])) ? $data['drug_id'] : null;
        $this->form = (!empty($data['form'])) ? $data['form'] : null;
        $this->dosage = (!empty($data['dosage'])) ? $data['dosage'] : null;
        $this->quantity = (!empty($data['quantity'])) ? $data['quantity'] : null;
        $this->size = (!empty($data['size'])) ? $data['size'] : null;
        $this->unit = (!empty($data['unit'])) ? $data['unit'] : null;
        $this->route = (!empty($data['route'])) ? $data['route'] : null;
        $this->interval = (!empty($data['interval'])) ? $data['interval'] : null;
        $this->substitute = (!empty($data['substitute'])) ? $data['substitute'] : null;
        $this->refills = (!empty($data['refills'])) ? $data['refills'] : null;
        $this->per_refill = (!empty($data['per_refill'])) ? $data['per_refill'] : null;
        $this->filled_date = (!empty($data['filled_date'])) ? $data['filled_date'] : null;
        $this->medication = (!empty($data['medication'])) ? $data['medication'] : null;
        $this->note = (!empty($data['note'])) ? $data['note'] : null;
        $this->active = (!empty($data['active'])) ? $data['active'] : null;
        $this->datetime = (!empty($data['datetime'])) ? $data['datetime'] : null;
        $this->user = (!empty($data['user'])) ? $data['user'] : null;
        $this->site = (!empty($data['site'])) ? $data['site'] : null;
        $this->prescriptionguid = (!empty($data['prescriptionguid'])) ? $data['prescriptionguid'] : null;
        $this->external_id = (!empty($data['external_id'])) ? $data['external_id'] : null;
        $this->end_date = (!empty($data['end_date'])) ? $data['end_date'] : null;
        $this->indication = (!empty($data['indication'])) ? $data['indication'] : null;
    }
}
