<?php


namespace App\Model\Repository;


use App\Model\Person;
use PDO;

class PersonRepository
{
    /**
     * @param PDO $pdo
     * @param Person $person
     */
    public function save(PDO $pdo, Person $person)
    {
        $query = 'insert into person (firstname, lastname, email, position, shares_amount, start_date, parent_id)
                    values(?, ?, ?, ?, ?, ?, ?)';
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $person->getFirstname(),
            $person->getLastname(),
            $person->getEmail(),
            $person->getPosition(),
            $person->getSharesAmount(),
            $person->getStartDate(),
            $person->getParentId()
        ]);
    }

    /**
     * @param PDO $pdo
     */
    public function deleteAllUsersExceptThePresident(PDO $pdo, $amountOfUsers)
    {
        $query = '
                delete from person where entity_id between 2 and 1000;
                alter table person auto_increment = 2;
                ';
        $pdo->exec($query);
    }

    /**
     * @param int $id
     * @param PDO $pdo
     * @return Person
     */
    public function getUser(int $id, PDO $pdo): Person
    {
        $query = 'select * from person where entity_id = ?';
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        $data = $stmt->fetchAll();
        return new Person(
            $id,
            $data[0]['firstname'],
            $data[0]['lastname'],
            $data[0]['email'],
            $data[0]['position'],
            $data[0]['shares_amount'],
            $data[0]['start_date'],
            $data[0]['parent_id']
        );
    }

    /**
     * @param PDO $pdo
     * @return int
     */
    public function getAmountOfUsers(PDO $pdo): int
    {
        $query = 'select count(*) from person';
        $stmt = $pdo->query($query);
        return $stmt->fetch()['count(*)'];
    }


}
