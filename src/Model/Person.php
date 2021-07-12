<?php


namespace App\Model;


class Person
{
    /**
     * @var int|null
     */
    public ?int $entity_id;

    /**
     * @var string|null
     */
    public ?string $firstname;

    /**
     * @var string|null
     */
    public ?string $lastname;

    /**
     * @var string|null
     */
    public ?string $email;

    /**
     * @var string|null
     */
    public ?string $position;

    /**
     * @var int|null
     */
    public ?int $shares_amount;

    /**
     * @var int|null
     */
    public ?int $start_date;

    /**
     * @var int|null
     */
    public ?int $parent_id;

    /**
     * Person constructor.
     * @param int|null $entityId
     * @param string|null $firstname
     * @param string|null $lastname
     * @param string|null $email
     * @param string|null $position
     * @param int|null $shares_amount
     * @param int|null $startDate
     * @param int|null $parentId
     */
    public function __construct(
        ?int $entityId = null,
        ?string $firstname = null,
        ?string $lastname = null,
        ?string $email = null,
        ?string $position = null,
        ?int $shares_amount = null,
        ?int $startDate = null,
        ?int $parentId = null
    ) {
        $this->entity_id = $entityId;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->position = $position;
        $this->shares_amount = $shares_amount;
        $this->start_date = $startDate;
        $this->parent_id = $parentId;
    }

    /**
     * @return string
     */
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
     * @return array
     */
    public function toArray(): array
    {
        return [
            'entity_id' => $this->entity_id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'position' => $this->position,
            'shares_amount' => $this->shares_amount,
            'start_date' => $this->start_date,
            'parent_id' => $this->parent_id
        ];
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        return $this->entity_id;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    /**
     * @return string|null
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * @return int|null
     */
    public function getSharesAmount(): ?int
    {
        return $this->shares_amount;
    }

    /**
     * @return int|null
     */
    public function getStartDate(): ?int
    {
        return $this->start_date;
    }

    /**
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): Person
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param int|null $entity_id
     * @return $this
     */
    public function setEntityId(?int $entity_id): Person
    {
        $this->entity_id = $entity_id;
        return $this;
    }

    /**
     * @param string|null $firstname
     * @return $this
     */
    public function setFirstname(?string $firstname): Person
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @param string|null $lastname
     * @return $this
     */
    public function setLastname(?string $lastname): Person
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @param int|null $parent_id
     * @return $this
     */
    public function setParentId(?int $parent_id): Person
    {
        $this->parent_id = $parent_id;
        return $this;
    }

    /**
     * @param string|null $position
     * @return $this
     */
    public function setPosition(?string $position): Person
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @param int|null $shares_amount
     * @return $this
     */
    public function setSharesAmount(?int $shares_amount): Person
    {
        $this->shares_amount = $shares_amount;
        return $this;
    }

    /**
     * @param int|null $start_date
     * @return $this
     */
    public function setStartDate(?int $start_date): Person
    {
        $this->start_date = $start_date;
        return $this;
    }
}