<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 9/4/16
 * Time: 4:38 PM
 */

namespace Formhandler\Validator;
use Laminas\Validator\AbstractValidator;

class NUM extends AbstractValidator
{
    const ISNUM = 'TEMP';

    protected $messageTemplates = array(
        self::ISNUM => 'Must be a numeric value',
    );

    public function __construct(array $options = array())
    {
        parent::__construct($options);
    }
    public function isValid($value)
    {
        $this->setValue($value);

        $num=$value;
        if (is_null($num)) {
            return true;
        }

        if (is_numeric($num)==FALSE){
            $this->error(self::ISNUM);
            return false;
        }
        
        return true;
    }
}

?>
