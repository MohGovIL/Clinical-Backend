<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 9/4/16
 * Time: 4:38 PM
 */

namespace Formhandler\Validator;
use Zend\Validator\AbstractValidator;

class Age extends AbstractValidator
{
    const ISANAGE = 'ISAFUTUREDATE';

    protected $messageTemplates = array(
        self::ISANAGE => 'Age must be 0-100 numeric value',
    );

    public function __construct(array $options = array())
    {
        parent::__construct($options);
    }
    public function isValid($value)
    {
        $this->setValue($value);

        $age=$value;
        if (is_null($age)) {
            $this->error(self::ISANAGE);
            return false;
        }

        if (ctype_digit($age)==FALSE){
            $this->error(self::ISANAGE);
            return false;
        }

        $intAge=(int)$age;

        if ($intAge <0 || $intAge>100 ){
            $this->error(self::ISANAGE);
            return false;
        }

        return true;
    }
}

?>