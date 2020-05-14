<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 9/4/16
 * Time: 4:38 PM
 */

namespace Formhandler\Validator;
use Laminas\Validator\AbstractValidator;

class Time extends AbstractValidator
{
    const ISATIME = 'ISATIME';

    protected $messageTemplates = array(
        self::ISATIME => 'Time must be in a time format',
    );

    public function __construct(array $options = array())
    {
        parent::__construct($options);
    }
    public function isValid($value)
    {
        $this->setValue($value);

        if (strlen($value) >= 4 && !preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $value)) {
            $this->error(self::ISATIME);
            return false;
        }

        return true;
    }
}


?>
