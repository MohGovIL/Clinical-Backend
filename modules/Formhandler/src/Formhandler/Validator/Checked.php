<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 9/4/16
 * Time: 4:38 PM
 */

namespace Formhandler\Validator;
use DateTime;
use SqlParser\Context;
use Zend\Validator\AbstractValidator;

class Checked extends AbstractValidator
{
    const ISNUM = 'TEMP';

    protected $messageTemplates = array(
        self::ISNUM => 'Must be checked',
    );

    public function __construct(array $options = array())
    {
        parent::__construct($options);
    }
    public function isValid($value)
    {
        $this->setValue($value);


        if ($value>0) {
            return true;
        }

        if ($value<=0){
            $this->error(self::ISNUM);
            return false;
        }

        return true;
    }
}


?>