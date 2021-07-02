<?php


namespace App\Model;


class Position implements PositionInterface
{
    /**
     * @return array
     */
    public function getPositions() : array
    {
        return [self::NOVICE, self::MANAGER, self::VICE_PRESIDENT, self::PRESIDENT];
    }

}