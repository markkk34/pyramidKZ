<?php

use App\php\data\Person;
use App\php\data\Position;
use Faker\Factory;

require_once '../vendor/autoload.php';
echo 'Hello';
$pdo = null;
try
{
    echo 0;
    //very important: we set         |   mysql here except localhost
    $pdo = new PDO("mysql:host=mysql;port=3306;dbname=pyramid;charset=utf8", "root", "11235813");
}
catch (PDOException $e)
{
    echo $e->getMessage();
}

if ($pdo != null)
{
    $faker = Factory::create();
    $persons = [];
    $persons[] = new Person(1, 'Mike', 'Patterson', 'mike@gmail.com',
        'president', 10000, 1273449600, 0);

    $i = 2;
    $position = new Position();
    do
    {
        /*$res = $pdo->query("select * from person");
        $row = $res->fetch();
        $i++;*/
        //1 step - create
        $person = new Person($i, $faker->name, $faker->lastName, $faker->email,
            Position::NOVICE, rand(1, 500), rand(1273449600, time() - 86400), rand(1, $i - 1)); //1273449600, $i); //we skip start_date
        //$person->setStartDate(rand(1273449600, time() - 86400)); //in the day 86400 sec

        //2 step - check for affiliates amount
        $maxAmountOfAffiliates = 0;
        $allowedAmountForBeingAffiliates = 3; //3 fellows r allowed
        foreach ($persons as $per)
        {
            if ($per->getParentId() == $person->getParentId())
            {
                $maxAmountOfAffiliates++;
            }
        }

        //3 step - check start_date
        $isRightDateStart = false;
        $parentPerson = null; //fix . we can use $i
        foreach ($persons as $pers)
        {
            if ($pers->getEntityId() == $person->getParentId())
            {
                $parentPerson = $pers;
                break;
            }
        }
        if ($person->getStartDate() > $parentPerson->getStartDate())
            $isRightDateStart = true;

        if ($maxAmountOfAffiliates <= $allowedAmountForBeingAffiliates) //1st step varification
        {
            if ($isRightDateStart) //2nd step varif
            {
                $persons[] = $person; //this and below will work if evrth is kk
                $i++; //remember about incr I !!!
            }
        }
    } while ($i < 101);

    //var_dump($persons);

    $i = 2; //but our array starts from 0. so   i = 2    =    array = 1  //cause we dont touch the president
    do
    {
        $person = $persons[$i - 1]; // i - 1   is our fellow beginning from second fellow
        //step 1 - for being manager
        $amountOfAffiliatesToBeManager = 0;
        $enoughAffToBeManager = 3;
        foreach ($persons as $per)
        {
            if ($per->getParentId() == $person->getEntityId())
                $amountOfAffiliatesToBeManager++;
        }

        $isAffiliatesOldEnough = true; // 6 months = 15638400 secs
        if ($amountOfAffiliatesToBeManager >= $enoughAffToBeManager)// check for the first requirement and go on if it's kk
        {
            $amountOfStocksOfParent = $person->getSharesAmount();
            $amountOfStocksOfParentAffiliates = 0;
            $amountOfStocksOfParentAffiliatesAffiliates = 0;
            foreach ($persons as $per) //affiliates of parent
            {
                if ($per->getParentId() == $person->getEntityId())
                {
                    if ($per->getSharesAmount() < time() - 15638400)
                    {
                        $amountOfStocksOfParentAffiliates += $per->getSharesAmount();

                        foreach ($persons as $pers)
                        {
                            if ($pers->getParentId() == $per->getEntityId())
                            {
                                $amountOfStocksOfParentAffiliatesAffiliates += $pers->getSharesAmount();
                            }
                        }
                    }
                    else
                        $isAffiliatesOldEnough = false;
                    if (!$isAffiliatesOldEnough)
                        break;
                }
                if (!$isAffiliatesOldEnough)
                    break;
            }
            $amountOfStocksOfParent += $amountOfStocksOfParentAffiliates / 2 + $amountOfStocksOfParentAffiliatesAffiliates / 3;

            if ($amountOfStocksOfParent > 1000 && $isAffiliatesOldEnough)
                $person->setPosition(Position::MANAGER);
        }

        $i++;
    } while ($i < 101);

    $maxStocks = 0;
    $idOfTheFutureVicePresident = 0;
    foreach ($persons as $per)
    {
        if ($per->getParentId() == 1 && $per->getSharesAmount() > $maxStocks)
        {
            $maxStocks = $per->getSharesAmount();
            $idOfTheFutureVicePresident = $per->getEntityId();
        }
    }
    $persons[$idOfTheFutureVicePresident - 1]->setPosition(Position::VICE_PRESIDENT);

    echo '<br>';

    echo '<table border="1">';
    echo '<tr><td>id</td><td>firstname</td><td>lastname</td><td>email</td><td>position</td><td>shares_amount</td><td>start_date</td><td>parent_id</td></tr>';
    foreach ($persons as $person)
    {
        echo $person;
    }
    echo '</table>';
}
else echo 'problem';

