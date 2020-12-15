<?php

namespace ReportTool\Form;

use Zend\Form\Form,
 Zend\Form\Element;
use GenericTools\Controller\GenericToolsController;

/**
 */
class FiltersForm extends Form {

    public function __construct(array $lists = array())
    {
        parent::__construct('filtersForm');

    }

}
