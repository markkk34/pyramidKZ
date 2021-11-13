<?php

namespace App\Model\Person\Validator;

use App\Controllers\Logger;
use App\Model\Position;
use App\Model\Person;
use App\Model\Repository\PersonRepository;
use Exception;

class PresidentValidator implements ValidatorInterface
{
    const PATH_PRESIDENT_DATA_JSON = '../../presidentData.json';//
    /**
     * @var PersonRepository
     */
    protected PersonRepository $personRepository;

    /**
     * @var Position
     */
    protected Position $position;

    /**
     * @var Logger
     */
    protected Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger();
        $this->personRepository = new PersonRepository($this->logger);
        $this->position = new Position();
    }

    /**
     * @param Person[] $persons
     * @return ValidationResultInterface
     * @throws Exception
     */
    public function validate(array $persons): ValidationResultInterface
    {
        $presidentDataFromJson = $this->getPresidentDataFromJson();
        $presidentId = $presidentDataFromJson['entity_id'];
        if (isset($persons[$presidentId])) {
            if ($persons[$presidentId]->toArray() == $presidentDataFromJson) {

            } else {

            }
        } else {

        }
        if (empty($persons) || !$this->areThePresidentDataCorrect())
        {
            $this->logger->warning('we re-create the table (no president or incorrect data)');
            return $this->regenerateDB();
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function areThePresidentDataCorrect(): bool
    {
        $this->logger->info(__FUNCTION__ . '() is called');
        $presidentDataFromJson = $this->getPresidentDataFromJson();
        $presidentDataFromDB = $this->personRepository->getPresident();
        if ($presidentDataFromDB == null) {
            return false;
        }
        if (
            $presidentDataFromDB->toArray() == $presidentDataFromJson
        ) {
            return true;
        }

        return false;
    }

    public function getPresidentDataFromJson(): array
    {
        try {
            if (!file_exists(self::PATH_PRESIDENT_DATA_JSON)) {
                throw new IncorrectData($this->personRepository, $this->getPresidentDataFromJson(), 'Specified path doesnt exist');
            }
            $content = file_get_contents(self::PATH_PRESIDENT_DATA_JSON);
            if (!$content) {
                throw new IncorrectData($this->personRepository, $this->getPresidentDataFromJson(), 'There is file but couldnt be read');
            }
            $presidentData[] = json_decode($content, true);
            if (json_last_error() > 0) {
                throw new IncorrectData($this->personRepository, $this->getPresidentDataFromJson(), 'There was error while decoding: ' . json_last_error_msg());
            }
        } catch (IncorrectData $incorrectData) {
            echo $incorrectData->getMessage();
            die('We cannot work further if JSON is not working');
        }

        return $presidentData[0];
    }
}