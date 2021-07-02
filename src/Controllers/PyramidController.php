<?php


namespace App\Controllers;


use App\Db\Config;
use App\Db\Connection;
use App\Model\Position;
use App\Model\PositionInterface;
use App\Model\Repository\PersonRepository;
use Exception;
use Faker\Factory;
use App\Model\Person;
use JetBrains\PhpStorm\Pure;
use PDO;

class PyramidController
{
    const PATH_PRESIDENT_DATA_JSON = '../presidentData.json';
    const AMOUNT_OF_PYRAMID_MEMBERS = 100;

    /**
     * @var PersonRepository
     */
    protected PersonRepository $personRepository;

    /**
     * @var Position
     */
    protected Position $position;

    public function __construct()
    {
        echo 'here';//messed up with Connection and Config. I have to new Conf then new Conn
        $this->personRepository = new PersonRepository();
        $this->position         = new Position();
        echo 'pyr constr';
    }

    /**
     * @param int $i
     * @param array $existedUsersId
     * @param $faker
     * @return Person
     */
    protected function createOnePerson(int $i, array $existedUsersId, $faker): Person
    {
        return new Person(
            $i,
            $faker->name,
            $faker->lastName,
            $faker->email,
            Position::NOVICE,
            rand(1, 500),
            rand(1273449600, time() - 86400),
            $existedUsersId[array_rand($existedUsersId, 1)]
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
        foreach ($persons as $per)
        {
            if ($per->getParentId() == $person->getParentId())
            {
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
        $parentPerson     = function ($parentId) use ($persons)
        {
            foreach ($persons as $person)
            {
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
        $amountOfAffiliatesToBeManager = 0;
        $enoughAffToBeManager          = 3;
        foreach ($persons as $per)
        {
            //var_dump($person->getEntityId());
            if ($per->getParentId() == $person->getEntityId())
            {
                $amountOfAffiliatesToBeManager++;
            }
        }

        $isAffiliatesOldEnough = true; // 6 months = 15638400 secs
        if ($amountOfAffiliatesToBeManager >= $enoughAffToBeManager) {// check for the first requirement and go on if it's kk
            $amountOfStocksOfParent                     = $person->getSharesAmount();
            $amountOfStocksOfParentAffiliates           = 0;
            $amountOfStocksOfParentAffiliatesAffiliates = 0;
            foreach ($persons as $per) //affiliates of parent
            {
                if ($per->getParentId() == $person->getEntityId()) {
                    if ($per->getSharesAmount() < time() - 15638400) {
                        $amountOfStocksOfParentAffiliates += $per->getSharesAmount();

                        foreach ($persons as $pers)
                        {
                            if ($pers->getParentId() == $per->getEntityId()) {
                                $amountOfStocksOfParentAffiliatesAffiliates += $pers->getSharesAmount();
                            }
                        }
                    } else
                        $isAffiliatesOldEnough = false;
                    if (!$isAffiliatesOldEnough)
                        break;
                }
                if (!$isAffiliatesOldEnough)
                    break;
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
        $maxStocks                  = 0;
        foreach ($persons as $per)
        {
            if ($per->getParentId() == 1 && $per->getSharesAmount() > $maxStocks) {
                $maxStocks                  = $per->getSharesAmount();
                $idOfTheFutureVicePresident = $per->getEntityId();
            }
        }

        return $idOfTheFutureVicePresident - 1;
    }

    /**
     * @param Person[] $persons
     * @return Person[]
     */
    public function checkForTheRightPosition(array $persons): array
    {
        $this->personRepository->deleteAllUsers();
        $i = 1;
        do {
            $person = $persons[$i];

            //step 1 - for being manager
            if ($this->isItEnoughAffiliatesToBeManager($persons, $person)) {
                $person->setPosition(Position::MANAGER);
            }
            $i++;
        } while ($i < self::AMOUNT_OF_PYRAMID_MEMBERS);

        return $persons;
    }

    /**
     * @return Person[]
     * @throws Exception
     */
    public function regenerateDB(): array
    {
        $this->personRepository->deleteAllUsers();
        return [0 => $this->createThePresident()];
    }

    /**
     * @param array $persons
     * @return bool
     */
    public function areDisallowedMistakesInDB(array $persons): bool//work with db and array //about members
    {
        $parentPerson = function ($parentId) use ($persons)
        {
            foreach ($persons as $person)
            {
                if ($person->entity_id == $parentId) {
                    return $person;
                }
            }
            return null;
        };

        foreach ($persons as $person) //date
        {
            if ($person->getPosition() != Position::PRESIDENT) {
                if (is_null($parentPerson($person->getParentId())) ||
                    $person->getStartDate() < $parentPerson($person->getParentId())->getStartDate() ) {//parent && date
                    echo '<br><br>+ Some members have incorrect parents or date. DB regenerated';
                    return true;
                }
            }
        }
        return false;
    }

    public function checkForAllowedMistakesInDB(array $persons)//work with db and array
    {
        //position
        //amount_of_shares


        return $persons;
    }

    /**
     * @param array $persons
     * @return array
     * @throws Exception
     */
    public function checkOrFixTheDataFromDB(array $persons): array
    {
        /**
         * priority : 1
         * Check if the president is existing
         */
        if (empty($persons) || !$this->areThePresidentDataCorrect())
        {
            echo '<br><br><br>+ we re-create the table (no president or incorrect data)<br>';
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
         * Disallowed mistakes : Date
         */
         if ($this->areDisallowedMistakesInDB($persons)) {
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
        $presidentDataFromJson = $this->getPresidentDataFromJson();
        $presidentDataFromDB   = $this->personRepository->getPresident();
        if ($presidentDataFromDB == null) {
            return false;
        }
        if (
            //$presidentDataFromDB->getEntityId()       == $presidentDataFromJson[0]['entity_id'] &&
            $presidentDataFromDB->getFirstname()        == $presidentDataFromJson[0]['firstname'] &&
            $presidentDataFromDB->getLastname()         == $presidentDataFromJson[0]['lastname'] &&
            $presidentDataFromDB->getEmail()            == $presidentDataFromJson[0]['email'] &&
            $presidentDataFromDB->getPosition()         == $presidentDataFromJson[0]['position'] &&
            $presidentDataFromDB->getSharesAmount()     == $presidentDataFromJson[0]['shares_amount'] &&
            $presidentDataFromDB->getStartDate()        == $presidentDataFromJson[0]['start_date'] &&
            $presidentDataFromDB->getParentId()         == $presidentDataFromJson[0]['parent_id']
        )
        {
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
            throw new Exception('Specified path doesnt exist');
        }
        $content = file_get_contents(self::PATH_PRESIDENT_DATA_JSON);
        if (!$content) {
            throw new Exception('There is file but couldnt be read');
        }
        $presidentData[] = json_decode($content, true);
        if (json_last_error() > 0) {
            throw new Exception('There was error while decoding: ' . json_last_error_msg());
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
            throw new Exception('Incorrect email structure');
        }

        /**
         * Check for position in_array
         * Is it existed position?
         */
        if (!in_array($person->getPosition(), $this->position->getPositions())) {
        throw new Exception('Not existed position');
        }

        /**
         * Check for shares_amount
         * Is it correct type and appropriate value?
         */
        if ($person->getSharesAmount() < 0 || $person->getSharesAmount() > 500) {  //unsigned in db make!
            if ($person->getPosition() !== PositionInterface::PRESIDENT && $person->getSharesAmount() > 500) {
                throw new Exception('Not number or minus value');
            }
        }
    }

    /**
     *
     */
    public function showParticipants() : void
    {
        $persons = $this->personRepository->getAllUsers();
        foreach ($persons as $person)
        {
            echo $person;
        }
    }

    /**
     * @param array $persons
     * @return array
     */
    public function getUsersId(array $persons): array
    {
        //var_dump($persons);
        $existedUsersId = [];
        foreach ($persons as $person)
        {
            $existedUsersId[] = $person->entity_id;
        }
        return $existedUsersId;
    }

    /**
     * @throws Exception
     */
    public function createPyramid() : void
    {
        /**
         * Get participants from DB and Check for data
         */
        $persons                            = $this->checkOrFixTheDataFromDB($this->personRepository->getAllUsers());
        $amountOfAlreadyExistedParticipants = count($persons);
        $faker                              = Factory::create();
        $idOfCurrentLastUser                = $persons[array_key_last($persons)]->getEntityId();

        while ($amountOfAlreadyExistedParticipants < self::AMOUNT_OF_PYRAMID_MEMBERS)
        {
            /**
             * 1 step - create person
             * we r workin' with interim array. IT IS MA MAIN PROBLEM. NEED TO SYNC DATA BTWN DB AND ARRAY
             */
            $person = $this->createOnePerson(
                $idOfCurrentLastUser + 1,
                array_column($persons, 'entity_id'),
                $faker
            );

            $this->checkForTheCorrectTypeOfTheData($person);

            /**
             * 2 step - check for affiliates amount
             */
            $maxAmountOfAffiliates           = $this->checkForAffiliatesAmount($persons, $person);
            $allowedAmountForBeingAffiliates = 3;

            /**
             * 3 step - check start_date
             */
            $isRightDateStart = $this->checkForDateToBeAffiliate($persons, $person);

            /**
             * Here we make a decision about Create person or not
             */
            if ($this->doWeAcceptYouToBeAffiliate(
                $maxAmountOfAffiliates,
                $allowedAmountForBeingAffiliates,
                $isRightDateStart
            )) {
                $persons[] = $person;
                $idOfCurrentLastUser++;
                $amountOfAlreadyExistedParticipants++;
            }
        }
        /**
         * Can u be a manager? Check
         * HERE WE DELETE THE WHOLE DATA IN DB AND USE ONLY ARRAY
         */
        $persons = $this->checkForTheRightPosition($persons);

        /**
         * Define Vice-president
         */
        $persons[$this->whatIsTheIdOfTheFuturePresident($persons)]->setPosition(Position::VICE_PRESIDENT);
        foreach ($persons as $person)
        {
            $this->personRepository->save($person);
        }

        include '../public/Participants.php';
        $this->personRepository->deleteAllUsers();
        $this->personRepository->createPresident($this->getPresidentDataFromJson());
        //$this->personRepository->deleteAllUsersExceptThePresident($this->personRepository->getAmountOfUsers());
    }
}
