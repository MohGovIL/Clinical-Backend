<?php
/**
 * User: Eyal Wolanowski eyalvo@matrix.co.il
 * Date: 17/05/20
 * Time: 16:58
 */

namespace ClinikalAPI\Model;


class ClinikalPatientTrackingChanges
{

    public $facility_id;
    public $update_date;


    public function exchangeArray($data)
    {
        $this->facility_id = (!empty($data['facility_id'])) ? $data['facility_id'] : null;
        $this->update_date = (!empty($data['update_date'])) ? $data['update_date'] : null;

    }

}
