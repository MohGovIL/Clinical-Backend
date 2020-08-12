<?php

/**
 * @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 */


namespace FhirAPI\Model;

class FhirServiceRequest
{
    //for fhir_service_request
    public $id;
    public $category;
    public $encounter;
    public $reason_code;
    public $patient;
    public $instruction_code;
    public $order_detail_code;
    public $order_detail_system;
    public $patient_instruction;
    public $requester;
    public $authored_on;
    public $status;
    public $intent;
    public $note;
    public $performer;
    public $occurrence_datetime;
    public $reason_reference_doc_id;


    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->category = (!empty($data['category'])) ? $data['category'] : null;
        $this->encounter = (!empty($data['encounter'])) ? $data['encounter'] : null;
        $this->reason_code = (!empty($data['reason_code'])) ? $data['reason_code'] : null;
        $this->patient = (!empty($data['patient'])) ? $data['patient'] : null;
        $this->instruction_code = (!empty($data['instruction_code'])) ? $data['instruction_code'] : null;
        $this->order_detail_code = (!empty($data['order_detail_code'])) ? $data['order_detail_code'] : null;
        $this->order_detail_system = (!empty($data['order_detail_system'])) ? $data['order_detail_system'] : null;
        $this->patient_instruction = (!empty($data['patient_instruction'])) ? $data['patient_instruction'] : null;
        $this->requester = (!empty($data['requester'])) ? $data['requester'] : null;
        $this->authored_on = (!empty($data['authored_on'])) ? $data['authored_on'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->intent = (!empty($data['intent'])) ? $data['intent'] : null;
        $this->note = (!empty($data['note'])) ? $data['note'] : null;
        $this->performer = (!empty($data['performer'])) ? $data['performer'] : null;
        $this->occurrence_datetime = (!empty($data['occurrence_datetime'])) ? $data['occurrence_datetime'] : null;
        $this->reason_reference_doc_id = (!empty($data['reason_reference_doc_id'])) ? $data['reason_reference_doc_id'] : null;
    }
}
