<?php

namespace GenericTools\Library\CreateLetter;

use Interop\Container\ContainerInterface;

class CreateLetterContext
{
    private $strategy = NULL;

    /**
     * CreateLetterContext constructor.
     * @param ICreateLetter $strategy
     */
    public function __construct(ICreateLetter $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function doRender($params)
    {
        //Stage 2 : Create binary PDF string
        return $this->strategy->draw($params);
    }

}
