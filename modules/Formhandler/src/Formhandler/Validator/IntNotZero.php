<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 9/4/16
 * Time: 4:38 PM
 */

namespace Formhandler\Validator;
use Laminas\Validator\AbstractValidator;

class IntNotZero extends AbstractValidator
{
    const DOUBLEDIGIT = 'ISAFUTUREDATE';

    protected $messageTemplates = array(
        self::DOUBLEDIGIT => 'Must contain numeric value upper than 0',
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
            $this->error(self::DOUBLEDIGIT);
            return false;
        }

        if (ctype_digit($age)==FALSE){
            $this->error(self::DOUBLEDIGIT);
            return false;
        }

        $intAge=(int)$age;

        if ($intAge <= 0 ){
            $this->error(self::DOUBLEDIGIT);
            return false;
        }

        return true;
    }
}

?>
