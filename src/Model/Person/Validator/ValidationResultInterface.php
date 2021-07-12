<?php

namespace App\Model\Person\Validator;

interface ValidationResultInterface
{
    /**
     * @return bool
     */
    public function getResult(): bool;

    /**
     * @return array
     */
    public function getErrors(): array;

    /**
     * @return bool
     */
    public function isRecoverable(): bool;
}
