<?php

namespace ImportData\Lists;


interface InterfaceList{

    /**
     * set into $EDMdata property the EMD data (per row)
     * @param $data
     * @return mixed
     */
    public function __construct($data);

    /**
     * convert the key to clinikal database keys and set it into $clinikalData
     * @return mixed
     */
    public function convertKeys();

    /**
     * return new instance from Model folder  that match to ModelTable with the right data
     * (for example - if it's save into list options table this method return 'Lists' model that match to save in the ListsTable Model)
     * @return mixed
     */
    public function getTableObject();

    /**
     * return array with english constant and the hebrew translation
     * if there isn't english translation the 'id' is be the english constant
     * @return mixed
     */
    public function getTranslation();

}