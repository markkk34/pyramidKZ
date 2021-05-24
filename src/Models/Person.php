<?php


namespace App\Models;


class Person
{
    protected int $entity_id;
    protected string $firstname;
    protected string $lastname;
    protected string $email;
    protected string $position;
    protected int $shares_amount;
    protected int $start_date;
    protected int $parent_id;

    public function __construct(int $entity_id,
                                string $firstname,
                                string $lastname,
                                string $email,
                                string $position,
                                int $shares_amount,
                                int $start_date,
                                int $parent_id
    )
    {
        //echo ' constr Person ';
        $this->entity_id = $entity_id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->position = $position;
        $this->shares_amount = $shares_amount;
        $this->start_date = $start_date;
        $this->parent_id = $parent_id;
    }

    public function __toString(): string
    {
        return '<tr><td>'. $this->entity_id . "</td><td>" .
            $this->firstname . "</td><td>" .
            $this->lastname . "</td><td>" .
            $this->email . "</td><td>" .
            $this->position . "</td><td>" .
            $this->shares_amount . "</td><td>" .
            $this->start_date . "</td><td>" .
            $this->parent_id . "</td></tr>";
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->entity_id;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parent_id;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getSharesAmount(): int
    {
        return $this->shares_amount;
    }

    /**
     * @return int
     */
    public function getStartDate(): int
    {
        return $this->start_date;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): Person
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param int $entity_id
     * @return $this
     */
    public function setEntityId(int $entity_id): Person
    {
        $this->entity_id = $entity_id;
        return $this;
    }

    /**
     * @param string $firstname
     * @return $this
     */
    public function setFirstname(string $firstname): Person
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @param string $lastname
     * @return $this
     */
    public function setLastname(string $lastname): Person
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @param int $parent_id
     * @return $this
     */
    public function setParentId(int $parent_id): Person
    {
        $this->parent_id = $parent_id;
        return $this;
    }

    /**
     * @param string $position
     * @return $this
     */
    public function setPosition(string $position): Person
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @param int $shares_amount
     * @return $this
     */
    public function setSharesAmount(int $shares_amount): Person
    {
        $this->shares_amount = $shares_amount;
        return $this;
    }

    /**
     * @param int $start_date
     * @return $this
     */
    public function setStartDate(int $start_date): Person
    {
        $this->start_date = $start_date;
        return $this;
    }
}