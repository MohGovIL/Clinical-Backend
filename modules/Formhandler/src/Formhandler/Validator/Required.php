<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 19/09/16
 * Time: 10:54
 */

namespace Formhandler\Validator;

use Laminas\Validator\AbstractValidator;

class Required extends AbstractValidator
{
    const ISANAGE = 'ISAFUTUREDATE';

    protected $messageTemplates = array(
        self::ISANAGE => 'The field is required.',
    );

    public function __construct(array $options = array())
    {
        parent::__construct($options);
    }
    public function isValid($value)
    {
        if(is_array($value) && sizeof($value)>0)
        {

            foreach($value as $key=>$val)
            {
                if(!isset($val) || $val=null)
                    return false;
            }

            return true;
        }

       if(!is_null($value) && trim($value)!='' )
        return true;

        return false;
    }

    public function getMessages()
    {
        return $this->messageTemplates;
    }
}
