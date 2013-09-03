<?php

namespace app\model\casino\bj;

use app\helper\Logger;

class BlackJack extends \app\model\casino\Casino
{
    const STATUS_TOP = 0;
    const STATUS_GAME = 1;
    const STATUS_GAME_OVER = 2;
    const URL = 'http://mdq.member.jp.square-enix.com/casino.php?BLACKJACK=1';
    
    private $game_status = null;
    private $cc = null;// BJ key
    private $dealer = null;
    private $player = null;
    
    private function __construct($status)
    {
        $this->game_status = $status;
    }
    
    public static function start($client, $cc)
    {
        $request = $client->post(self::URL.'&cc='.$cc, null, array(
            'COIN' => 2,
            'BET' => '決　定',
        ));
        $result = $request->send();
        return self::parseBody($result->getBody());
    }
    public static function hit($client)
    {
        $request = $client->post(self::URL, null, array(
            'HIT' => 'ヒット'
        ));
        $result = $request->send();
        return self::parseBody($result->getBody());
    }
    public static function dd($client)
    {
        $request = $client->post(self::URL, null, array(
            'DDOWN' => 'ダブルダウン'
        ));
        $result = $request->send();
        return self::parseBody($result->getBody());
    }
    public static function stand($client)
    {
        $request = $client->post(self::URL, null, array(
            'STAND' => 'スタンド'
        ));
        $result = $request->send();
        return self::parseBody($result->getBody());
    }
    /**
     * @return \app\model\casino\bj\BlackJack
     */
    public static function status($client)
    {
        $r = $client->get(self::URL)->send();
        return self::parseBody($r->getBody());
    }
    
    private static function parseBody($body)
    {
        $status = self::getStatusFromBody($body);
        $bj = new self($status);
        
        switch ($status) {
            case self::STATUS_TOP:
                    $bj->setCoin(self::getCoins($body));
                    $bj->setCC(self::getCCFromHtml($body));
                    break;
            case self::STATUS_GAME:
            case self::STATUS_GAME_OVER:
                    list($dealer, $player) = self::getPlayers($body);
                    $bj->setDelaer($dealer);
                    $bj->setPlayer($player);
                    break;
        }
        
        return $bj;
    }
    
    private static function getStatusFromBody($body)
    {
        if (0 !== preg_match('/カジノコイン所持数 \[\d+ 枚\]/', $body)) {
            return self::STATUS_TOP;
        }
        if (0 !== preg_match('/リトライ/', $body)) {
            return self::STATUS_GAME_OVER;
        }
        if (0 !== preg_match('/DEALER MUST DRAW TO 16, STAND ON ALL 17s/', $body)) {
            return self::STATUS_GAME;
        }
        throw new \RuntimeException("Unknown BlackJack Status");
    }
    
    private static function getCoins($body)
    {
        $m = array();
        if (0 !== preg_match('/カジノコイン所持数 \[(\d+) 枚\]/', $body, $m)) {
            return intval($m[1]);
        }
        throw new \RuntimeException("Invalid body");
    }
    
    private static function getPlayers($body)
    {
        $p = strpos($body, 'DEALER MUST DRAW TO 16, STAND ON ALL 17s');
        $body_dealer = substr($body, 0, $p);
        $dealer_cards = self::getCards($body_dealer);
        $dealer_score = self::getScore($body_dealer, true);
        
        $body_player = substr($body, $p);
        $player_cards = self::getCards($body_player);
        $player_score = self::getScore($body_player, false);
        
        $delaer = new Player($dealer_score, $dealer_cards);
        $player = new Player($player_score, $player_cards);
        
        return array($delaer, $player);
    }
    
    private static function getCards($body)
    {
        $m = array();
        if (0 === preg_match_all('/tc\/tc(\d+)\-(\d+).png/', $body, $m, PREG_SET_ORDER)) {
            throw new \RuntimeException("cant not found cards");            
        }
        $cards = array();
        foreach ($m as $card) {
            $suit = $card[1];
            $number = $card[2];
            $cards[] = new Card($suit, $number);
        }
        return $cards;
    }
    
    private static function getScore($body, $is_dealer)
    {
        $m = array();
        if ($is_dealer) {
            if (0 !== preg_match('/DEALER : <span style="font-size:150%">(\d+)<\/span>/', $body, $m)) {
                return intval($m[1]);
            }
            throw new \RuntimeException("dealer score not found");
        } else {
            if (0 !== preg_match('/PLAYER : <span style="font-size:150%">(\d+)<\/span>/', $body, $m)) {
                return intval($m[1]);
            }
            throw new \RuntimeException("player score not found");
        }
    }
    
    private static function getCCFromHtml($body)
    {
        $m = array();
        if (0 !== preg_match('/BLACKJACK=1&cc=([abcdef0-9]+)"/', $body, $m)) {
            return $m[1];
        }
        throw new \RuntimeException("Invalid cc");
    }
    
    public function __toString()
    {
        return $this->game_status.' Dealer: '.sprintf("%2s", $this->dealer).' Player: '.sprintf("%2s", $this->player);
    }

    public function setDelaer(Player $p) {$this->dealer = $p;}
    public function setPlayer(Player $p) {$this->player = $p;}
    /**
     * @return \app\model\casino\bj\Player
     */
    public function getDelaer() {return $this->dealer;}
    /**
     * @return \app\model\casino\bj\Player
     */
    public function getPlayer() {return $this->player;}
    public function getStatus() {return $this->game_status;}
    public function getCC() {return $this->cc;}
    public function setCC($cc) {$this->cc = $cc;}
}
