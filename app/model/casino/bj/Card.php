<?php
namespace app\model\casino\bj;

class Card
{
    public $suit;
    public $number;
    
    public function __construct($suit, $number) {
        $this->suit = $suit;
        $this->number = intval($number);
    }
    
    public function getAsBjNumber()
    {
        if ($this->number > 9) return 10;
        return $this->number;
    }
    
    public function __toString() {
        return "".$this->getAsBjNumber();
    }
}