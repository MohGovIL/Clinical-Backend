<?php
namespace Formhandler\View\Helper\Form;

use Laminas\Form\View\Helper\FormElementErrors;

class TwbBundleFormElementErrors extends FormElementErrors
{
    protected $attributes = array(
        'class' => 'help-block'
    );
}
