<?php

namespace app\model\casino\bj\strategy;

use app\helper\Logger;

abstract class Strategy
{
    const STRATEGY_STAND = 0;
    const STRATEGY_HIT = 1;
    const STRATEGY_DD = 2;
    
    public static function think(\app\model\casino\bj\BlackJack $bj)
    {
        return self::STRATEGY_STAND;
    }
}