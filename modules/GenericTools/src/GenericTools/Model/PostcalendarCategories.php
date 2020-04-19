<?php

namespace GenericTools\Model;

class PostcalendarCategories
{

      public $pc_catid ;
      public $pc_catname ;
      public $pc_catcolor ;
      public $pc_catdesc ;
      public $pc_recurrtype ;
      public $pc_enddate ;
      public $pc_recurrspec ;
      public $pc_recurrfreq ;
      public $pc_duration  ;
      public $pc_end_date_flag ;
      public $pc_end_date_type ;
      public $pc_end_date_freq ;
      public $pc_end_all_day ;
      public $pc_dailylimit ;
      public $pc_cattype ;
      public $pc_active ;
      public $pc_seq ;
      public $aco_spec ;
      public $pc_constant_id ;

    public function exchangeArray($data)
    {
        $this->pc_catid     = (!empty($data['pc_catid'])) ? $data['pc_catid'] : null;
        $this->pc_catname     = (!empty($data['pc_catname'])) ? $data['pc_catname'] : null;
        $this->pc_catcolor     = (!empty($data['pc_catcolor'])) ? $data['pc_catcolor'] : null;
        $this->pc_catdesc     = (!empty($data['pc_catdesc'])) ? $data['pc_catdesc'] : null;
        $this->pc_recurrtype     = (!empty($data['pc_recurrtype'])) ? $data['pc_recurrtype'] : null;
        $this->pc_enddate     = (!empty($data['pc_enddate'])) ? $data['pc_enddate'] : null;
        $this->pc_recurrspec     = (!empty($data['pc_recurrspec'])) ? $data['pc_recurrspec'] : null;
        $this->pc_recurrfreq     = (!empty($data['pc_recurrfreq'])) ? $data['pc_recurrfreq'] : null;
        $this->pc_duration     = (!empty($data['pc_duration'])) ? $data['pc_duration'] : null;
        $this->pc_end_date_flag     = (!empty($data['pc_end_date_flag'])) ? $data['pc_end_date_flag'] : null;
        $this->pc_end_date_type     = (!empty($data['pc_end_date_type'])) ? $data['pc_end_date_type'] : null;
        $this->pc_end_date_freq     = (!empty($data['pc_end_date_freq'])) ? $data['pc_end_date_freq'] : null;
        $this->pc_end_all_day     = (!empty($data['pc_end_all_day'])) ? $data['pc_end_all_day'] : null;
        $this->pc_dailylimit     = (!empty($data['pc_dailylimit'])) ? $data['pc_dailylimit'] : null;
        $this->pc_cattype     = (!empty($data['pc_cattype'])) ? $data['pc_cattype'] : null;
        $this->pc_active     = (!empty($data['pc_active'])) ? $data['pc_active'] : null;
        $this->pc_seq     = (!empty($data['pc_seq'])) ? $data['pc_seq'] : null;
        $this->aco_spec     = (!empty($data['aco_spec'])) ? $data['aco_spec'] : null;
        $this->pc_constant_id     = (!empty($data['pc_constant_id'])) ? $data['pc_constant_id'] : null;
    }
}




