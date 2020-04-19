<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 9/4/16
 * Time: 4:38 PM
 */

namespace Formhandler\Validator;
use Zend\Validator\AbstractValidator;

class  NoPastDate  extends AbstractValidator
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

        $dateFromNow   =$value;
        if (is_null($dateFromNow)) {
            $this->error(self::ISAFUTUREDATE);
            return false;
        }

        if ($GLOBALS['date_display_format'] == 2){
            $pickedDate= date("d/m/Y");
        } else {
            $pickedDate= date("Y-m-d");
        }


        if ($pickedDate==FALSE){
            $this->error(self::ISADATE);
            return false;
        }
        // dd/mm/yyyy format
        if ($GLOBALS['date_display_format'] == 2){
            $nowDate= date("d/m/Y") ;
        } else {
            $nowDate= date("Y-m-d") ;
        }


        if ($nowDate>$pickedDate) {
            $this->error(self::ISAFUTUREDATE);
            return false;
        }

        return true;
    }
}


?>
