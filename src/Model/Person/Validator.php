<?php


namespace App\Model\Person;

use App\Model\Person\Validator\ValidatorInterface;
use App\Model\Person;

class Validator
{
    /**
     * @var array
     */
    protected array $validators;

    /**
     * @param Person[] $persons
     * @return array
     */
    public function validate(array $persons): array
    {
        $results = [];
        foreach ($this->validators as $validator) {
            if ($validator instanceof ValidatorInterface) {
                $results[] = $validator->validate($persons);
            }
        }
        return $results;
    }

    /**
     * @param ValidatorInterface $validator
     */
    public function addValidator(ValidatorInterface $validator)
    {
        $this->validators[] = $validator;
    }
}
