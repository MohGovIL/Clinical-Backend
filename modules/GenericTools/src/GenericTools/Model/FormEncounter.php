<?php
/**
 * Created by PhpStorm.
 * User: Yuriy Gershem
 * Date: 30/01/20
 * Time: 13:45
 */
namespace GenericTools\Model;

class FormEncounter
{
    public $id;
    public $date;
    public $reason;
    public $facility;
    public $facility_id;
    public $pid;
    public $encounter;
    public $onset_date;
    public $sensitivity;
    public $billing_note;
    public $pc_catid;
    public $last_level_billed;
    public $last_level_closed;
    public $last_stmt_date;
    public $stmt_count;
    public $provider_id;
    public $supervisor_id;
    public $invoice_refno;
    public $referral_source;
    public $billing_facility;
    public $external_id;
    public $pos_code;
    public $parent_encounter_id;
    public $status;
    public $eid;
    public $priority;
    public $service_type;
    public $service_type_title;
    public $reason_code_title;
    public $reason_code;
    public $service_type_seq;
    public $escort_id;
    public $arrival_way;
    public $reason_codes_details;
    public $secondary_status;
    public $status_update_date;




    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->date = (!empty($data['date'])) ? $data['date'] : null;
        $this->reason = (!empty($data['reason'])) ? $data['reason'] : null;
        $this->facility = (!empty($data['facility'])) ? $data['facility'] : null;
        $this->facility_id = (!empty($data['facility_id'])) ? $data['facility_id'] : null;
        $this->pid = (!empty($data['pid'])) ? $data['pid'] : null;
        $this->encounter = (!empty($data['encounter'])) ? $data['encounter'] : null;
        $this->onset_date = (!empty($data['onset_date'])) ? $data['onset_date'] : null;
        $this->sensitivity = (!empty($data['sensitivity'])) ? $data['sensitivity'] : null;
        $this->billing_note = (!empty($data['billing_note'])) ? $data['billing_note'] : null;
        $this->pc_catid = (!empty($data['pc_catid'])) ? $data['pc_catid'] : null;
        $this->last_level_billed = (!empty($data['last_level_billed'])) ? $data['last_level_billed'] : null;
        $this->last_level_closed = (!empty($data['last_level_closed'])) ? $data['last_level_closed'] : null;
        $this->last_stmt_date = (!empty($data['last_stmt_date'])) ? $data['last_stmt_date'] : null;
        $this->stmt_count = (!empty($data['stmt_count'])) ? $data['stmt_count'] : null;
        $this->provider_id = (!empty($data['provider_id'])) ? $data['provider_id'] : null;
        $this->supervisor_id = (!empty($data['supervisor_id'])) ? $data['supervisor_id'] : null;
        $this->invoice_refno = (!empty($data['invoice_refno'])) ? $data['invoice_refno'] : null;
        $this->referral_source = (!empty($data['referral_source'])) ? $data['referral_source'] : null;
        $this->billing_facility = (!empty($data['billing_facility'])) ? $data['billing_facility'] : null;
        $this->external_id = (!empty($data['external_id'])) ? $data['external_id'] : null;
        $this->pos_code = (!empty($data['pos_code'])) ? $data['pos_code'] : null;
        $this->parent_encounter_id = (!empty($data['parent_encounter_id'])) ? $data['parent_encounter_id'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->eid = (!empty($data['eid'])) ? $data['eid'] : null;
        $this->priority = (!is_null($data['priority'])) ? $data['priority'] : 0;
        $this->service_type_seq = (!empty($data['service_type_seq'])) ? $data['service_type_seq'] : null;
        $this->service_type = (!empty($data['service_type'])) ? $data['service_type'] : null;
        $this->service_type_title = (!empty($data['service_type_title'])) ? $data['service_type_title'] : null;
        $this->reason_code = (!empty($data['reason_code'])) ? $data['reason_code'] : null;
        $this->reason_code_title = (!empty($data['reason_code_title'])) ? $data['reason_code_title'] : null;
        $this->escort_id = (!empty($data['escort_id'])) ? $data['escort_id'] : null;
        $this->arrival_way = (!empty($data['arrival_way'])) ? $data['arrival_way'] : null;
        $this->reason_codes_details = (!empty($data['reason_codes_details'])) ? $data['reason_codes_details'] : null;
        $this->secondary_status = (!empty($data['secondary_status'])) ? $data['secondary_status'] : null;
        $this->status_update_date = (!empty($data['status_update_date'])) ? $data['status_update_date'] : null;




    }
}
