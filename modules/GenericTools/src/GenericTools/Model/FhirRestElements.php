<?php
/**
 * Date: 29/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class  FHIR REST BUILDING TYPES TABLE EXCHANGE
 */
namespace GenericTools\Model;

class FhirRestElements
{
    /*
     *
     * */
    public $id;
    public $name;
    public $active;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->name = (!empty($data['$name'])) ? $data['$name'] : null;
        $this->active = (!empty($data['$active'])) ? $data['$active'] : null;
    }
}
