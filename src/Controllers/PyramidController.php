<?php

namespace App\Controllers;

use App\Model\Config\Reader;
use App\Model\IncorrectData;
use App\Model\Person\Validator;
use App\Model\Position;
use App\Model\PositionInterface;
use App\Model\Repository\PersonRepository;
use Exception;
use Faker\Factory;
use App\Model\Person;
use JetBrains\PhpStorm\Pure;

class PyramidController
{
    const PATH_PRESIDENT_DATA_JSON = '../presidentData.json';
    const AMOUNT_OF_PYRAMID_MEMBERS = 100;
    const ALLOWED_AMOUNT_OF_AFFILIATES = 4;
    const ALLOWED_AMOUNT_OF_SHARES = 500;
    const SECURE_EXIT_PARENT_ID = 50;

    /**
     * @var Validator
     */
    protected Validator $validator;

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

    /**
     * @var array
     */
    protected array $dataFromPresidentJson;

    protected Reader $reader;

    public function __construct()
    {
        $this->personRepository = new PersonRepository($this->logger = new Logger());
        $this->reader = new Reader();
        $this->position = new Position();
        $this->validator = new Validator();
        $this->validator->addValidator(new Validator\PresidentValidator());
    }

    /**
     * @param int $i
     * @param $faker
     * @param int $parentId
     * @param int $dateOfStart
     * @return Person
     */
    protected function createOnePerson(
        int $i,
        $faker,
        int $parentId,
        int $dateOfStart
    ): Person
    {
        return new Person(
            $i,
            $faker->name,
            $faker->lastName,
            $faker->email,
            Position::NOVICE,
            rand(1, 500),
            $dateOfStart,
            $parentId
        );
    }

    /**
     * @return Person
     */
    protected function createThePresident(): Person
    {
        $person = new Person(
            1,
            'Mike',
            'Patterson',
            'mike@gmail.com',
            'president',
            10000,
            1273449600,
            0
        );
        $this->personRepository->save($person);
        return $person;
    }

    /**
     * @param array $persons
     * @param Person $person
     * @return int
     */
    protected function checkForAffiliatesAmount(array $persons, Person $person): int
    {
        $maxAmountOfAffiliates = 0;
        foreach ($persons as $per) {
            if ($per->getParentId() == $person->getParentId()) {
                $maxAmountOfAffiliates++;
            }
        }

        return $maxAmountOfAffiliates;
    }

    /**
     * @param array $persons
     * @param Person $person
     * @return bool
     */
    protected function checkForDateToBeAffiliate(array $persons, Person $person): bool
    {
        $isRightDateStart = false;
        $parentPerson = function ($parentId) use ($persons)
        {
            foreach ($persons as $person) {
                if ($person->entity_id == $parentId) {
                    return $person->getStartDate();
                }
            }
            return null;
        };

        if ($person->getStartDate() > $parentPerson($person->parent_id) || !is_null($parentPerson($person->parent_id))) {
            $isRightDateStart = true;
        }

        return $isRightDateStart;
    }

    /**
     * @param $maxAmountOfAffiliates
     * @param $allowedAmountForBeingAffiliates
     * @param $isRightDateStart
     * @return bool
     */
    protected function doWeAcceptYouToBeAffiliate(
        $maxAmountOfAffiliates,
        $allowedAmountForBeingAffiliates,
        $isRightDateStart
    ): bool
    {
        if ($maxAmountOfAffiliates <= $allowedAmountForBeingAffiliates) {//1st step varification
            if ($isRightDateStart) {                                     //2nd step varif
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $persons
     * @param Person $person
     * @return bool
     */
    protected function isItEnoughAffiliatesToBeManager(array $persons, Person $person) : bool
    {
        if ($person->getPosition() == Position::PRESIDENT) {
            return false;
        }

        $amountOfAffiliatesToBeManager = 0;
        $enoughAffToBeManager = 3;
        foreach ($persons as $per) {
            if ($per->getParentId() == $person->getEntityId()) {
                $amountOfAffiliatesToBeManager++;
            }
        }

        $isAffiliatesOldEnough = true; // 6 months = 15638400 secs
        if ($amountOfAffiliatesToBeManager >= $enoughAffToBeManager) {// check for the first requirement and go on if it's kk
            $amountOfStocksOfParent = $person->getSharesAmount();
            $amountOfStocksOfParentAffiliates = 0;
            $amountOfStocksOfParentAffiliatesAffiliates = 0;
            foreach ($persons as $per) { //affiliates of parent
                if ($per->getParentId() == $person->getEntityId()) {
                    if ($per->getSharesAmount() < time() - 15638400) {
                        $amountOfStocksOfParentAffiliates += $per->getSharesAmount();

                        foreach ($persons as $pers) {
                            if ($pers->getParentId() == $per->getEntityId()) {
                                $amountOfStocksOfParentAffiliatesAffiliates += $pers->getSharesAmount();
                            }
                        }
                    } else {
                        $isAffiliatesOldEnough = false;
                    }
                    if (!$isAffiliatesOldEnough) {
                        break;
                    }
                }
                if (!$isAffiliatesOldEnough) {
                    break;
                }
            }
            $amountOfStocksOfParent +=
                $amountOfStocksOfParentAffiliates / 2 +
                $amountOfStocksOfParentAffiliatesAffiliates / 3;

            if ($amountOfStocksOfParent > 1000 && $isAffiliatesOldEnough)
                return true;
        }

        return false;
    }

    /**
     * @param array $persons
     * @return int
     */
    public function whatIsTheIdOfTheFuturePresident(array $persons) : int
    {
        $idOfTheFutureVicePresident = 1;
        $maxStocks = 0;

        foreach ($persons as $per)
        {
            if (
                $per->getParentId() == $this->personRepository->getPresident()->getEntityId() &&
                $per->getSharesAmount() > $maxStocks
            ) {
                $maxStocks = $per->getSharesAmount();
                $idOfTheFutureVicePresident = $per->getEntityId();
            }
        }

        return $idOfTheFutureVicePresident;
    }

    /**
     * @param Person[] $persons
     * @return Person[]
     */
    public function checkForTheRightPosition(array $persons): array
    {
        foreach ($persons as $person) {
            $personForNow = $person;

            //step 1 - for being manager
            if ($this->isItEnoughAffiliatesToBeManager($persons, $personForNow)) {
                $personForNow->setPosition(Position::MANAGER);
            } elseif ($personForNow->getPosition() != Position::PRESIDENT) {
                $personForNow->setPosition(Position::NOVICE);
            }
        }

        return $persons;
    }

    /**
     * @return Person[]
     * @throws Exception
     */
    public function regenerateDB(): array
    {
        $this->logger->info('DB is re-generated');
        $this->personRepository->deleteAllUsers();
        return [1 => $this->createThePresident()];
    }

    /**
     * @param array $persons
     * @return bool
     */
    public function areOddAffiliates(array $persons): bool
    {
        foreach ($persons as $parentPerson) {
            $affiliatesOfTheParent = 0;
            foreach ($persons as $childPerson) {
                if ($childPerson->getEntityId() != $parentPerson->getEntityId() && $parentPerson->getEntityId() == $childPerson->getParentId()) {
                    $affiliatesOfTheParent++;
                }
            }

            if ($affiliatesOfTheParent > self::ALLOWED_AMOUNT_OF_AFFILIATES) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $persons
     * @return bool
     * @throws Exception
     */
    public function areDisallowedMistakesInDB(array $persons): bool//work with db and array
    {
        $this->logger->info(__FUNCTION__ . '() is called');

        $parentPerson = function ($parentId) use ($persons)
        {
            foreach ($persons as $person) {
                if ($person->getEntityId() == $parentId) {
                    return $person;
                }
            }
            return null;
        };

        if ($this->areOddAffiliates($persons)) {
            return false;
        }

        foreach ($persons as $person) { //date
            if ($person->getPosition() != Position::PRESIDENT) {
                if (is_null($parentPerson($person->getParentId())) ||
                    $person->getParentId() == $persons[$this->reader->readJSON(self::PATH_PRESIDENT_DATA_JSON)['entity_id']]->getParentId() ||
                    $person->getStartDate() < $parentPerson($person->getParentId())->getStartDate() ) {//parent && date
                    echo '<br><br>+ Some members have incorrect parents or date. DB regenerated';
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param array $persons
     * @return array
     */
    public function checkForTheAmountOfShares(array $persons): array
    {
        foreach ($persons as $person) {
            if ($person->getPosition() != Position::PRESIDENT && $person->getSharesAmount() > self::ALLOWED_AMOUNT_OF_SHARES) {
                $person->setSharesAmount(rand(1, self::ALLOWED_AMOUNT_OF_SHARES));
            }
        }
        return $persons;
    }

    /**
     * @param array $persons
     * @return array
     */
    public function checkForOddAffiliates(array $persons): array
    {
        foreach ($persons as $parentPerson) {
            $affiliatesOfTheParent = [];
            foreach ($persons as $childPerson) {
                if ($childPerson->getEntityId() != $parentPerson->getEntityId() && $parentPerson->getEntityId() == $childPerson->getParentId()) {
                    $affiliatesOfTheParent[] = $childPerson->getEntityId();
                }
            }

            if (count($affiliatesOfTheParent) > self::ALLOWED_AMOUNT_OF_AFFILIATES) {
                for ($i = count($affiliatesOfTheParent); $i > self::ALLOWED_AMOUNT_OF_AFFILIATES; $i--) {
                    $personsAffiliateAmountId = $this->availableUsersCreate($persons);
                    $persons[$affiliatesOfTheParent[$i - 1]]->setParentId($this->createParentId($personsAffiliateAmountId));
                }
            }
        }
        return $persons;
    }

    /**
     * @param array $persons
     * @return array
     */
    public function checkForAllowedMistakesInDB(array $persons): array//work with db and array
    {
        $this->logger->info(__FUNCTION__ . '() is called');
        /**
         * Check for Position(Check for Amount of Shares)
         */
        return $this->checkForTheRightPosition($this->checkForTheAmountOfShares($persons));//make steps
    }

    /**
     * @throws Exception
     */
    public function checkOrFixTheDataFromDB(array $persons): array
    {
        $this->logger->info(__FUNCTION__ . '() is called');

        /**
         * priority : 1
         * Check if the president is existing
         */
        if (empty($persons) || !$this->areThePresidentDataCorrect())
        {
            $this->logger->warning('we re-create the table (no president or incorrect data)');
            return $this->regenerateDB();
        }

        /**
         * priority : 2
         * Check for correct type of the data
         */
        foreach ($persons as $person)
        {
            $this->checkForTheCorrectTypeOfTheData($person);
        }

        /**
         * priority : 3
         * Check if the conditions are done correctly
         * Allowed mistakes : Position, Stocks
         * Disallowed mistakes : Date, not Existed Parent or more Affiliates than it's allowed, parent_id = 0
         */
         if ($this->areDisallowedMistakesInDB($persons)) {
             $this->logger->warning('Disallowed Mistakes');
             return $this->regenerateDB();
         }

        return $this->checkForAllowedMistakesInDB($persons);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function areThePresidentDataCorrect(): bool
    {
        $this->logger->info(__FUNCTION__ . '() is called');
        $presidentDataFromJson = $this->reader->readJSON(self::PATH_PRESIDENT_DATA_JSON);
        $presidentDataFromDB = $this->personRepository->getPresident();
        if ($presidentDataFromDB == null) {
            return false;
        }
        if ($presidentDataFromDB->toArray() == $presidentDataFromJson) {
            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function getPresidentDataFromJson(): array
    {
        if (!file_exists(self::PATH_PRESIDENT_DATA_JSON)) {
            throw new IncorrectData($this->personRepository, $this->getPresidentDataFromJson(), 'Specified path doesnt exist');
        }
        $content = file_get_contents(self::PATH_PRESIDENT_DATA_JSON);
        if (!$content) {
            throw new IncorrectData($this->personRepository, $this->getPresidentDataFromJson(), 'There is file but couldnt be read');
        }
        $presidentData = json_decode($content, true);
        if (json_last_error() > 0) {
            throw new IncorrectData($this->personRepository, $this->getPresidentDataFromJson(), 'There was error while decoding: ' . json_last_error_msg());
        }

        return $presidentData;
    }

    /**
     * @throws Exception
     */
    public function checkForTheCorrectTypeOfTheData(Person $person) : void
    {

        /**
         * Email check
         * Is it email structure?
         */
        if (!filter_var($person->getEmail(), FILTER_VALIDATE_EMAIL)) {
            throw new IncorrectData($this->personRepository, $this->reader->readJSON(self::PATH_PRESIDENT_DATA_JSON), 'Incorrect email structure');
        }

        /**
         * Check for position in_array
         * Is it existed position?
         */
        if (!in_array($person->getPosition(), $this->position->getPositions())) {
            throw new IncorrectData($this->personRepository, $this->reader->readJSON(self::PATH_PRESIDENT_DATA_JSON), 'Not existed position');
        }

        /**
         * Check for shares_amount
         * Is it correct type and appropriate value?
         */
        if ($person->getSharesAmount() < 0 || $person->getSharesAmount() > 500) {  //unsigned in db make!
            if ($person->getPosition() !== PositionInterface::PRESIDENT && $person->getSharesAmount() > 500) {
                throw new IncorrectData($this->personRepository, $this->reader->readJSON(self::PATH_PRESIDENT_DATA_JSON), 'Not number or negative value');
            }
        }
    }

    public function showParticipants() : void
    {
        $persons = $this->personRepository->getAllUsers();
        foreach ($persons as $person) {
            echo $person;
        }
    }

    public function getParticipants(): array
    {
        return $this->personRepository->getAllUsers();
    }

    /**
     * @param array $persons
     * @return array
     */
    public function getUsersId(array $persons): array
    {
        $existedUsersId = [];
        foreach ($persons as $person) {
            $existedUsersId[] = $person->entity_id;
        }
        return $existedUsersId;
    }

    /**
     * @param array $persons
     * @return array
     */
    public function makeArraySequentInId(array $persons): array
    {
        $personsWithSequentId = [];
        foreach ($persons as $person) {
            $personsWithSequentId[$person->getEntityId()] = $person;
        }
        return $personsWithSequentId;
    }

    /**
     * @param Person[] $persons
     * @return array
     */
    public function availableUsersCreate(array $persons): array
    {
        $availableUsersIds = [];

        foreach ($persons as $person) {
            if (!isset($availableUsersIds[$person->getEntityId()])) {
                $availableUsersIds[$person->getEntityId()] = 0;
            }

            if ($person->getParentId() !== 0) {
                if (!isset($availableUsersIds[$person->getParentId()])) {
                    $availableUsersIds[$person->getParentId()] = 0;
                }
                $availableUsersIds[$person->getParentId()]++;

                if ($availableUsersIds[$person->getParentId()] >= self::ALLOWED_AMOUNT_OF_AFFILIATES) {
                    unset($availableUsersIds[$person->getParentId()]);
                }
            }
        }

        return $availableUsersIds;
    }

    /**
     * @param array $existedParentIds
     * @return int
     */
    public function createParentId(array $existedParentIds): int
    {
        return array_rand($existedParentIds, 1);
        /*$i = 0;
        while ($i < self::SECURE_EXIT_PARENT_ID) {
            $parentId = array_rand($existedParentIds, 1);
            if ($existedParentIds[$parentId] < self::ALLOWED_AMOUNT_OF_AFFILIATES) {
                return $parentId;
            }

            $i++;
        }

        $this->logger->warning('SECURITY_EXIT has worked; ' . __FUNCTION__ . '() is called');
        foreach ($existedParentIds as $key => $amountOfAffiliates) {
            if ($amountOfAffiliates < self::ALLOWED_AMOUNT_OF_AFFILIATES) {
                return $key;
            }
        }
        return 0;*/
    }

    /**
     * @param Person $person
     * @return int
     * @throws IncorrectData
     * @throws Exception
     */
    public function createAffiliateDate(Person $person): int
    {
        if (time() - 86400 - $person->getStartDate() < 0) {
            throw new IncorrectData($this->personRepository, $this->reader->readJSON(self::PATH_PRESIDENT_DATA_JSON),'Affiliate has negative date');
        }
        return rand($person->getStartDate(), time() - 86400);
    }

    /**
     * @param array $existedParentIds
     * @param Person $person
     * @return array
     */
    #[Pure] public function availableUsersUpdate(array $existedParentIds, Person $person): array
    {
        $existedParentIds[$person->getEntityId()] = 0;
        $existedParentIds[$person->getParentId()]++;

        if ($existedParentIds[$person->getParentId()] >= self::ALLOWED_AMOUNT_OF_AFFILIATES) {
            unset($existedParentIds[$person->getParentId()]);
        }

        return $existedParentIds;
    }

    /**
     * @throws Exception
     */
    public function createPyramid() : void
    {
        $this->logger->info('Pyramid Start');

        /**
         * Get participants from DB and Check for data
         */
        $shares_amount = [];
        $persons = $this->checkOrFixTheDataFromDB($this->makeArraySequentInId($this->personRepository->getAllUsers()));
        $amountOfAlreadyExistedParticipants = count($persons);
        $faker = Factory::create();
        $idOfCurrentLastUser = array_key_last($persons);
        $existedParentIds = $this->availableUsersCreate($persons);

        while ($amountOfAlreadyExistedParticipants < self::AMOUNT_OF_PYRAMID_MEMBERS)
        {
            $personId = $this->createParentId($existedParentIds);
            $person = $this->createOnePerson(
                $idOfCurrentLastUser + 1,
                $faker,
                $personId,
                $this->createAffiliateDate($persons[$personId])
            );

            $existedParentIds = $this->availableUsersUpdate($existedParentIds, $person);

            $persons[] = $person;
            $idOfCurrentLastUser++;
            $amountOfAlreadyExistedParticipants++;
        }

        /**
         * Can u be a manager? Check
         */
        $persons = $this->checkForTheRightPosition($persons);

        /**
         * Define Vice-president
         */
        $persons[$this->whatIsTheIdOfTheFuturePresident($persons)]->setPosition(Position::VICE_PRESIDENT);
        $this->personRepository->deleteAllUsers();
        foreach ($persons as $person)
        {
            $this->personRepository->save($person);
        }

        include '../public/Participants.php';
        $this->personRepository->deleteAllUsers();
        $this->personRepository->createPresident($this->reader->readJSON(self::PATH_PRESIDENT_DATA_JSON));
    }
}
