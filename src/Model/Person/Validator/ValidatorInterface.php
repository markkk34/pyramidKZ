<?php

namespace App\Model\Person\Validator;

use App\Model\Person;
use App\Model\Person\Validator\ValidationResultInterface;

interface ValidatorInterface
{
    /**
     * @param Person[] $persons
     * @return \App\Model\Person\Validator\ValidationResultInterface
     */
    public function validate(array $persons): ValidationResultInterface;
}
