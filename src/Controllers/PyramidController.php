<?php


namespace App\Controllers;


use App\Models\Position;
use Faker\Factory;
use App\Models\Person;
use JetBrains\PhpStorm\Pure;
use PDO;
use PDOException;

class PyramidController
{
    /**
     * @var PDO
     */
    protected PDO $pdo;

    public function __construct()
    {
        try {
            $this->pdo = connectionToDB();
            echo 'pyr constr and pdo';
        }
        catch (PDOException $exception) {
            echo 'ERROR' . $exception->getMessage();
        }
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
    #[Pure] protected function createThePresident(): Person
    {
        return new Person(
            1,
            'Mike',
            'Patterson',
            'mike@gmail.com',
            'president',
            10000,
            1273449600,
            0
        );
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
    protected function doWeAcceptYouToBeAffiliate($maxAmountOfAffiliates, $allowedAmountForBeingAffiliates, $isRightDateStart): bool
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
            $amountOfStocksOfParent += $amountOfStocksOfParentAffiliates / 2 + $amountOfStocksOfParentAffiliatesAffiliates / 3;

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

    public function createPyramid()
    {
        $faker = Factory::create();
        $persons = [];
        $persons[] = $this->createThePresident();

        $i = 2;
        do
        {
            //1 step - create
            $person = $this->createOnePerson($i, $faker);

            //2 step - check for affiliates amount
            $maxAmountOfAffiliates = $this->checkForAffiliatesAmount($persons, $person);
            $allowedAmountForBeingAffiliates = 3; //3 fellows r allowed

            //3 step - check start_date
            $isRightDateStart = $this->checkForDateToBeAffiliate($persons, $person);

            if ($this->doWeAcceptYouToBeAffiliate($maxAmountOfAffiliates, $allowedAmountForBeingAffiliates, $isRightDateStart)) {
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

        include '../public/Participants.php';
    }

}


/*$res = $pdo->query("select * from person");
          $row = $res->fetch();
          $i++;*/
