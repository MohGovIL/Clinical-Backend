<?php

namespace GenericTools\Library\CreateLetter;

interface ICreateLetter
{
    public function collectData();
    public function draw($params);
}
