<?php


namespace App\Model;


class Stock
{
    /**
     * @var int|null
     */
    public ?int $shares_amount_id;

    /**
     * @var int|null
     */
    public ?int $entity_id;

    /**
     * @var int|null
     */
    public ?int $shares_amount;

    /**
     * Stock constructor.
     * @param int|null $shares_amount_id
     * @param int|null $entity_id
     * @param int|null $shares_amount
     */
    public function __construct(
        ?int $shares_amount_id = null,
        ?int $entity_id = null,
        ?int $shares_amount = null
    ) {
        $this->shares_amount_id = $shares_amount_id;
        $this->entity_id = $entity_id;
        $this->shares_amount = $shares_amount;
    }

    /**
     * @return int|null
     */
    public function getSharesAmountId(): ?int
    {
        return $this->shares_amount_id;
    }

    /**
     * @param int|null $shares_amount_id
     * @return Stock
     */
    public function setSharesAmountId(?int $shares_amount_id): Stock
    {
        $this->shares_amount_id = $shares_amount_id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        return $this->entity_id;
    }

    /**
     * @param int|null $entity_id
     * @return Stock
     */
    public function setEntityId(?int $entity_id): Stock
    {
        $this->entity_id = $entity_id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSharesAmount(): ?int
    {
        return $this->shares_amount;
    }

    /**
     * @param int|null $shares_amount
     * @return Stock
     */
    public function setSharesAmount(?int $shares_amount): Stock
    {
        $this->shares_amount = $shares_amount;
        return $this;
    }
}
