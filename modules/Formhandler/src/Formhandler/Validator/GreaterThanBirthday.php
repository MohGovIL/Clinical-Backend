<?php

namespace Formhandler\Validator;
use Laminas\Validator\AbstractValidator;


class GreaterThanBirthday extends AbstractValidator
{
    public function __construct(array $options = array())
    {
        parent::__construct($options);
    }
    public function isValid($value)
    {
        // Don't need server side validation
        return true;
    }
}
