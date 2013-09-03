<?php
namespace app\model\casino\bj\strategy;

use app\helper\Logger;

define('S', 0);
define('H', 1);
define('D', 2);

class BasicStrategy extends Strategy
{
    const STRATEGY_STAND = 0;
    const STRATEGY_HIT = 1;
    const STRATEGY_DD = 2;
    
    
    // 参考: http://kasinoru-ru.com/01/0103.html
    // under 8(include 8) must be hit
    // over 17(include 17) must be hit
    private static $strategy_hard = array(
        // Dealer up card
        //    array(2,3,4,5,6,7,8,9,10,A),
        9  => array(H,D,D,D,D,H,H,H,H,H),
        10 => array(D,D,D,D,D,D,D,D,H,H),
        11 => array(D,D,D,D,D,D,D,D,D,H),
        12 => array(H,H,S,S,S,H,H,H,H,H),
        13 => array(S,S,S,S,S,H,H,H,H,H),
        14 => array(S,S,S,S,S,H,H,H,H,H),
        15 => array(S,S,S,S,S,H,H,H,H,H),
        16 => array(S,S,S,S,S,H,H,H,H,H),
    );
    private static $strategy_soft = array(
        //    array(2,3,4,5,6,7,8,9,10,A),
        12 => array(H,H,S,S,S,H,H,H,H,H),
        13 => array(H,H,H,D,D,H,H,H,H,H),
        14 => array(H,H,H,D,D,H,H,H,H,H),
        15 => array(H,H,D,D,D,H,H,H,H,H),
        16 => array(H,H,D,D,D,H,H,H,H,H),
        17 => array(H,D,D,D,D,H,H,H,H,H),
        18 => array(S,D,D,D,D,S,S,H,H,H),
    );
    
    public static function think(\app\model\casino\bj\BlackJack $bj)
    {
        $player = $bj->getPlayer();
        $dealer = $bj->getDelaer();
        $p_score = $player->getScore();
        $dealer_up_card = $dealer->getUpCard();
        if ($p_score >= 19) {
            return self::STRATEGY_STAND;
        }
        if (!$player->isSoftHand()) {
            // hard hand
            $p_score = $player->getScore();
            if ($p_score <= 8) {
                return self::STRATEGY_HIT;
            } else if ($p_score >= 17) {
                return self::STRATEGY_STAND;
            }
            $dealer_array_num = self::getDealerStrategyArrayNum($dealer_up_card);
            return self::$strategy_hard[$p_score][$dealer_array_num];
        }
        // soft hand
        if ($p_score <= 11 || 19 <= $p_score) {
            throw new \RuntimeException("BasicStrategy algorithm error");
        }
        $dealer_array_num = self::getDealerStrategyArrayNum($dealer_up_card);
        return self::$strategy_soft[$p_score][$dealer_array_num];
    }
    
    private static function getDealerStrategyArrayNum($number)
    {
        if ($number > 9) {
            return 8;
        }
        if ($number == 1) {
            return 9;
        } 
        return $number - 2;
    }
}
