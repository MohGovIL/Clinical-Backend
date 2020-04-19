<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 1/31/17
 * Time: 10:30 AM
 */

namespace ImportData\Model;


class ImportDataLog
{
    public $id;
    public $table;
    public $status;
    public $affected_records;
    public $info;
    public $update_at;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->table = (!empty($data['table'])) ? $data['table'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->affected_records = (!empty($data['affected_records'])) ? $data['affected_records'] : null;
        $this->info = (!empty($data['info'])) ? $data['info'] : null;
        $this->update_at = (!empty($data['update_at'])) ? $data['update_at'] : null;
    }
}