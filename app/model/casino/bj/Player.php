<?php

namespace app\model\casino\bj;

class Player
{
    private $cards = array();
    private $score = 0;
    
    public function __construct($score, $cards) {
        $this->cards = $cards;
        $this->score = $score;
    }
    
    public function getScore(){return $this->score;}
    
    public function __toString()
    {
        $card_infos = array();
        foreach ($this->cards as $card) {
            $card_infos[] = '['.$card.']';
        }
        return "{$this->score}, cards:". implode(',', $card_infos);
    }
    
    public function isSoftHand()
    {
        foreach ($this->cards as $card) {
            if ($card->number == 1) {
                // Aが手札にあり、かつ単純な数字の合計が
                // 10以下の場合
                return 11 > $this->sum_simple();
            }
        }
        return false;
    }
    
    public function sum_simple()
    {
        $t = 0;
        foreach ($this->cards as $card)
        {
            $t += $card->getAsBjNumber();
        }
        return $t;
    }
    
    public function getUpCard()
    {
        $enable_upcards = ((0 === $this->cards[0]->getAsBjNumber()) xor (0 === $this->cards[1]->getAsBjNumber()));
        if (!$enable_upcards) {
            throw new \RuntimeException("invalid upcards");
        }
        if (0 < $this->cards[0]->getAsBjNumber()) 
                return $this->cards[0]->getAsBjNumber();
        return $this->cards[1]->getAsBjNumber();
    }
}
