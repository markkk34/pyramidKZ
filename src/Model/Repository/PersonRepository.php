<?php

namespace App\Model\Repository;

use App\Controllers\Logger;
use App\Model\Config\DbConfig\Config;
use App\Model\Config\DbConfig\Connection;
use App\Model\Person;
use PDO;

class PersonRepository
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
     * @var Logger
     */
    protected Logger $logger;

    /**
     * PersonRepository constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->connection = new Connection(new Config());
        $this->pdo = $this->connection->getConnection();
        echo '  pdo works in rep<br>';
    }

    /**
     * @param Person $person
     */
    public function save(Person $person) : void
    {
        $stmt = $this->pdo->prepare('insert into person (entity_id, firstname, lastname, email, position, shares_amount, start_date, parent_id)
                    values(?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $person->getEntityId(),
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
     *
     */
    public function deleteAllUsers() : void
    {
        $this->pdo->exec(
            'delete from person;
            alter table person auto_increment = 1;'
        );
    }

    /**
     * @param int $id
     * @return Person|null
     */
    public function getUser(int $id): ?Person
    {
        $stmt  = $this->pdo->prepare('select * from person where entity_id = ?');
        $stmt->execute([$id]);
        $data  = $stmt->fetchAll();
        return isset($data[0]['entity_id']) ? new Person(
            $id,
            $data[0]['firstname'],
            $data[0]['lastname'],
            $data[0]['email'],
            $data[0]['position'],
            $data[0]['shares_amount'],
            $data[0]['start_date'],
            $data[0]['parent_id']
        ) : null;
    }

    /**
     * @return array
     */
    public function getAllUsers(): array
    {
        $resultSet = $this->pdo->query('select * from person');
        return $resultSet->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Person::class);
    }

    /**
     * @return Person|null
     */
    public function getPresident(): ?Person
    {
        $result = $this->pdo->query('select * from person where position = "president"');
        if ($row = $result->fetch()) {
            return new Person(
                $row['entity_id'],
                $row['firstname'],
                $row['lastname'],
                $row['email'],
                $row['position'],
                $row['shares_amount'],
                $row['start_date'],
                $row['parent_id']
            );
        } else {
            return null;
        }
    }

    /**
     * @return int
     */
    public function getAmountOfUsers(): int
    {
        $stmt = $this->pdo->query('select count(*) from person');
        return $stmt->fetch()['count(*)'];
    }

    /**
     * @return array
     */
    public function getAllUsersId(): array
    {
        return $this->pdo->query('select entity_id from person')
            ->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @param array $presidentData
     */
    public function createPresident(array $presidentData) : void
    {
        $stmt = $this->pdo->prepare(
            'insert into person (firstname, lastname, email, position, shares_amount, start_date, parent_id)
                    values(?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
                           $presidentData['firstname'],
                           $presidentData['lastname'],
                           $presidentData['email'],
                           $presidentData['position'],
                           $presidentData['shares_amount'],
                           $presidentData['start_date'],
                           $presidentData['parent_id']

                       ]);
    }

}
