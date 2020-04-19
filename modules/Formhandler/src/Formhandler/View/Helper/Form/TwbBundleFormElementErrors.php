<?php
namespace Formhandler\View\Helper\Form;

use Zend\Form\View\Helper\FormElementErrors;

class TwbBundleFormElementErrors extends FormElementErrors
{
    protected $attributes = array(
        'class' => 'help-block'
    );
}
