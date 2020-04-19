<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 9/4/16
 * Time: 4:38 PM
 */

namespace Formhandler\Validator;
use Zend\Validator\AbstractValidator;

class DateFromNow extends AbstractValidator
{
    const ISAFUTUREDATE = 'ISAFUTUREDATE';
    const ISADATE = 'ISADATE';

    protected $messageTemplates = array(
        self::ISAFUTUREDATE => 'Can not pick future date',
        self::ISADATE => 'Date must be in a date format',
    );

    public function __construct(array $options = array())
    {
        parent::__construct($options);
    }
    public function isValid($value)
    {
        $this->setValue($value);

        $pickedDate = $value;
        if (is_null($pickedDate)) {
            $this->error(self::ISAFUTUREDATE);
            return false;
        }

        if ($pickedDate==FALSE){
            $this->error(self::ISADATE);
            return false;
        }

        $pickedDate = DateToYYYYMMDD($pickedDate);

        $nowDate= date("Y-m-d") ;

        if ($nowDate<$pickedDate) {
            $this->error(self::ISAFUTUREDATE);
            return false;
        }
        return true;
    }
}


?>
