<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 9/4/16
 * Time: 4:38 PM
 */

namespace Formhandler\Validator;
use Zend\Validator\AbstractValidator;



class  EncounterUntilToday  extends AbstractValidator
{
    const EMPTYDATE = 'EMPTYDATE';
    const FALSEDATE = 'FALSEDATE';

    protected $messageTemplates = array(
        self::EMPTYDATE => 'Can not pick empty date',
        self::FALSEDATE => 'Date is not in range',
    );

    public function __construct(array $options = array())
    {
        parent::__construct($options);
    }

    public function isValid($value)
    {
        $this->setValue($value);

        $pickDate   =$value;
        if (is_null($pickDate)) {
            $this->error(self::EMPTYDATE);
            return false;
        }

        $pickDate=DateToYYYYMMDD($pickDate);
        $today=date('Y-m-d');


        if ($pickDate>$today) {
            $this->error(self::FALSEDATE);
            return false;
        }

        $encounterId=$_SESSION["encounter"];
        $sql="SELECT date FROM form_encounter WHERE encounter=?";
        $res = sqlStatement($sql, array($encounterId));
        $row = sqlFetchArray($res);
        $encounterDate= substr($row['date'],0,10);

        if ($pickDate<$encounterDate) {
            $this->error(self::FALSEDATE);
            return false;
        }


        return true;
    }
}


?>
