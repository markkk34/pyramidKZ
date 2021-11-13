<?php

namespace App\Model;

use App\Model\PositionInterface;

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
