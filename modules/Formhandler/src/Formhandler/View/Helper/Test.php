<?php

/**
 * Created by PhpStorm.
 * User: drorgo
 * Date: 15/08/16
 * Time: 9:34 AM
 */

namespace Formhandler\View\Helper;
use Zend\View\Helper\AbstractHelper;


class Test extends AbstractHelper
{
    public function __invoke($s)
    {
        // TODO: Implement __invoke() method.
        return '<div class=\"input-group\">
                <span class=\"input-group-addon\" id=\"basic-addon1\">@</span>
                <input type=\"text\" class=\"form-control\" placeholder=\"'.$s.'\" aria-describedby=\"basic-addon1\">
                </div>
                ';
    }
}