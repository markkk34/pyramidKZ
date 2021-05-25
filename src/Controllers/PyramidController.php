<?php


namespace App\Controllers;


use App\Db\Config;
use App\Db\Connection;
use App\Model\Position;
use App\Model\Repository\PersonRepository;
use Exception;
use Faker\Factory;
use App\Model\Person;
use JetBrains\PhpStorm\Pure;
use PDO;

class PyramidController
{
    /**
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * @var Connection
     */
    protected Connection $connection;

    /**
     * @var PersonRepository
     */
    protected PersonRepository $personRepository;

    public function __construct()
    {
        echo 'here';//messed up with Connection and Config. I have to new Conf then new Conn
        $this->connection = new Connection(new Config());
        $this->pdo = $this->connection->getConnection();
        $this->personRepository = new PersonRepository();
        echo 'pyr constr and pdo';
    }

    /**
     * @param int $i
     * @param $faker
     * @return Person
     */
    protected function createOnePerson(int $i, $faker): Person
    {
        return new Person(
            $i,
            $faker->name,
            $faker->lastName,
            $faker->email,
            Position::NOVICE,
            rand(1, 500),
            rand(1273449600, time() - 86400),
            rand(1, $i - 1)
        );
    }

    /**
     * @return Person
     */
    protected function createThePresident(): Person
    {
        $query = 'alter table person auto_increment = 1'; //need to get rid of
        $this->pdo->exec($query);
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
        $this->personRepository->save($this->pdo, $person);
        return $person;
    }

    /**
     * @param $persons
     * @param $person
     * @return int
     */
    protected function checkForAffiliatesAmount($persons, $person): int
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
     * @param $persons
     * @param $person
     * @return bool
     */
    protected function checkForDateToBeAffiliate($persons, $person): bool
    {
        $isRightDateStart = false;
        $parentPerson = $persons[$person->getParentId() - 1];

        if ($person->getStartDate() > $parentPerson->getStartDate())
            $isRightDateStart = true;

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
            if ($isRightDateStart) { //2nd step varif
               return true;
            }
        }

        return false;
    }

    /**
     * @param $persons
     * @param $person
     * @return bool
     */
    protected function isItEnoughAffiliatesToBeManager($persons, $person) : bool
    {
        $amountOfAffiliatesToBeManager = 0;
        $enoughAffToBeManager = 3;
        foreach ($persons as $per)
        {
            if ($per->getParentId() == $person->getEntityId())
                $amountOfAffiliatesToBeManager++;
        }

        $isAffiliatesOldEnough = true; // 6 months = 15638400 secs
        if ($amountOfAffiliatesToBeManager >= $enoughAffToBeManager) {// check for the first requirement and go on if it's kk
            $amountOfStocksOfParent = $person->getSharesAmount();
            $amountOfStocksOfParentAffiliates = 0;
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
     * @param $persons
     * @return int
     */
    public function whatIsTheIdOfTheFuturePresident($persons) : int
    {
        $idOfTheFutureVicePresident = 1;
        $maxStocks = 0;
        foreach ($persons as $per)
        {
            if ($per->getParentId() == 1 && $per->getSharesAmount() > $maxStocks) {
                $maxStocks = $per->getSharesAmount();
                $idOfTheFutureVicePresident = $per->getEntityId();
            }
        }

        return $idOfTheFutureVicePresident - 1;
    }

    /**
     * @throws Exception
     * @return int
     */
    public function checkDatabaseForAppropriateDataAndHowMuchParticipants(): int
    {
        $amountOfUsers = $this->personRepository->getAmountOfUsers($this->pdo);
        $i = 1;
        while ($i <= $amountOfUsers)
        {
            $person = $this->personRepository->getUser($i, $this->pdo);
            $this->checkForTheCorrectTypeOfTheData($person);
            $i++;
        }

        return $i;
    }

    /**
     * @throws Exception
     */
    public function checkForTheCorrectTypeOfTheData(Person $person)
    {
        /**
         * Email check
         * Is it email structure?
         */
        if (
            strpos($person->getEmail(), '@') == false ||
            strpos($person->getEmail(), '.') == false
        ) {
            throw new Exception('Incorrect email structure');
        }

        /**
         * Check for position in_array
         * Is it existed position?
         */
        if ($person->getPosition() == Position::VICE_PRESIDENT) {
        } elseif ($person->getPosition() == Position::MANAGER) {
        } elseif ($person->getPosition() == Position::NOVICE) {
        } elseif ($person->getPosition() == Position::PRESIDENT) {
        } else {
            throw new Exception('Not existed position');
        }

        /**
         * Check for shares_amount
         * Is it correct type and appropriate value?
         */
        if ($person->getSharesAmount() < 0) {  //unsigned in db make!
            throw new Exception('Not number or minus value');
        }


    }

    /**
     * @throws Exception
     */
    public function createPyramid()
    {
        /**
         * Check for data in db and how much participants do we have
         * amount - 1 = real amount
         */
        $amountOfAlreadyExistedParticipants = $this->checkDatabaseForAppropriateDataAndHowMuchParticipants();

        $faker = Factory::create();
        $persons = [];
        if ($amountOfAlreadyExistedParticipants == 1) // if no president
        {
            $persons[] = $this->createThePresident(); //in db too
            $amountOfAlreadyExistedParticipants++;
        } else {  //if we have members  //we dont check if the 1st is the president (cope later, now just believe)
            //read members from db
            for ($j = 0; $j < $amountOfAlreadyExistedParticipants - 1; $j++)
            {
                $persons[] = $this->personRepository->getUser($j + 1, $this->pdo);
            }
        }

        $i = $amountOfAlreadyExistedParticipants;
        do
        {
            //1 step - create
            $person = $this->createOnePerson($i, $faker);

            $this->checkForTheCorrectTypeOfTheData($person);

            //2 step - check for affiliates amount
            $maxAmountOfAffiliates = $this->checkForAffiliatesAmount($persons, $person);
            $allowedAmountForBeingAffiliates = 3; //3 fellows r allowed

            //3 step - check start_date
            $isRightDateStart = $this->checkForDateToBeAffiliate($persons, $person);

            if ($this->doWeAcceptYouToBeAffiliate(
                $maxAmountOfAffiliates,
                $allowedAmountForBeingAffiliates,
                $isRightDateStart
            )) {
                $persons[] = $person;
                $i++;
            }
        } while ($i < 101);

        $i = 2; //but our array starts from 0. so   i = 2    =    array = 1  //cause we dont touch the president
        do {
            $person = $persons[$i - 1];

            //step 1 - for being manager
            if ($this->isItEnoughAffiliatesToBeManager($persons, $person))
                $person->setPosition(Position::MANAGER);
            $i++;
        } while ($i < 101);

        $persons[$this->whatIsTheIdOfTheFuturePresident($persons)]->setPosition(Position::VICE_PRESIDENT);

        foreach ($persons as $person)
        {
            if ($person->getEntityId() == 1)//cause we already have Mike
                continue;
            $this->personRepository->save($this->pdo, $person);
        }
        $this->personRepository->deleteAllUsersExceptThePresident($this->pdo, $this->personRepository->getAmountOfUsers($this->pdo));

        include '../public/Participants.php';
    }

}
