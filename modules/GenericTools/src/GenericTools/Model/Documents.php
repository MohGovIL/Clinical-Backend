<?php


namespace GenericTools\Model;


class Documents
{
    public $id;
    public $type;
    public $date;
    public $url;
    public $mimeType;
    public $owner;
    public $pid;
    public $couchDocId;
    public $couchRevId;
    public $storageMethod;
    public $encounterId;
    public $categoryId; // joined from categories_to_documents table
    public $categoryName; // joined from categories table

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->type = (!empty($data['type'])) ? $data['type'] : null;
        $this->date = (!empty($data['date'])) ? $data['date'] : null;
        $this->url = (!empty($data['url'])) ? $data['url'] : null;
        $this->mimeType = (!empty($data['mimetype'])) ? $data['mimetype'] : null;
        $this->owner = (!empty($data['owner'])) ? $data['owner'] : null;
        $this->pid = (!empty($data['foreign_id'])) ? $data['foreign_id'] : null;
        $this->couchDocId = (!empty($data['couch_docid'])) ? $data['couch_docid'] : null;
        $this->couchRevId = (!empty($data['couch_revid'])) ? $data['couch_revid'] : null;
        $this->storageMethod = (!empty($data['storagemethod'])) ? $data['storagemethod'] : null;
        $this->encounterId = (!empty($data['encounter_id'])) ? $data['encounter_id'] : null;
        $this->categoryId = (!empty($data['category_id'])) ? $data['category_id'] : null;
        $this->categoryName = (!empty($data['name'])) ? $data['name'] : null;
    }
}
