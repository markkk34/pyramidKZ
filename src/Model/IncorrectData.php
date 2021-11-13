<?php


namespace App\Model;


use App\Model\Repository\PersonRepository;
use Exception;
use Throwable;

class IncorrectData extends Exception
{
    /**
     * IncorrectData constructor.
     * @param PersonRepository $personRepository
     * @param array $PresidentData
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        PersonRepository $personRepository,
        array $PresidentData,
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    ) {
        $personRepository->deleteAllUsers();
        $personRepository->createPresident($PresidentData);
        parent::__construct($message, $code, $previous);
    }
}