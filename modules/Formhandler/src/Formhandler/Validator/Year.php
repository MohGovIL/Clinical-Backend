<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 9/4/16
 * Time: 4:38 PM
 */

namespace Formhandler\Validator;
use Laminas\Validator\AbstractValidator;

class Year extends AbstractValidator
{
    const YEAR = 'ISAFUTUREDATE';

    protected $messageTemplates = array(
        self::YEAR => 'Must contain numeric value in the format of YYYY ',
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
            $this->error(self::YEAR);
            return false;
        }

        if (ctype_digit($age)==FALSE){
            $this->error(self::YEAR);
            return false;
        }

        $intAge=(int)$age;

        if ($intAge <0 || $intAge>999 ){
            $this->error(self::YEAR);
            return false;
        }

        return true;
    }
}

?>
