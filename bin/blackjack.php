<?php
require_once __DIR__.'/../app/bootstrap.php';

use app\model\request\RequestMdq;
use app\helper\Logger;
use app\model\casino\bj\BlackJack;
use app\model\casino\bj\strategy\BasicStrategy;

Logger::info('blackjack start', __LINE__, __FILE__);

$client = RequestMdq::getLoginedClient();
$bj = BlackJack::status($client);
$starting_coin = null;
$strategy_history = array();
$last_game = null;

while(1) {
    sleep(2);
    switch ($bj->getStatus()) {
        case BlackJack::STATUS_TOP:
            if (is_null($starting_coin)) {
                $starting_coin = $bj->getCoin();
            }
            $strategy_history = array();
            Logger::info("COIN:".$starting_coin." -> ".$bj->getCoin()." (diff:".($bj->getCoin()-$starting_coin).") ". $last_game);
            $last_game = '';
            $bj = BlackJack::start($client, $bj->getCC());
            break;
        case BlackJack::STATUS_GAME:
            //Logger::info($bj."", __LINE__, __FILE__);
            switch (BasicStrategy::think($bj)) {
                case BasicStrategy::STRATEGY_HIT: 
                    //Logger::info("HIT", __LINE__, __FILE__);
                    $strategy_history[] = 'HIT';
                    $bj = BlackJack::hit($client);
                    break;
                case BasicStrategy::STRATEGY_STAND: 
                    //Logger::info("STAND", __LINE__, __FILE__);
                    $strategy_history[] = 'STAND';
                    $bj = BlackJack::stand($client); 
                    break;
                case BasicStrategy::STRATEGY_DD: 
                    //Logger::info("### DDOWN ######", __LINE__, __FILE__);
                    $strategy_history[] = 'DDOWN';
                    $bj = BlackJack::dd($client); 
                    break;
            }
            break;
        case BlackJack::STATUS_GAME_OVER:
            $last_game = ($bj." ".  implode(',', $strategy_history));
            $bj = BlackJack::status($client);
            break;
    }
}

Logger::info($bj."", __LINE__, __FILE__);