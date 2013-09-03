<?php
namespace app\model\casino;

abstract class Casino
{
    protected $coin = null;
    
    public function setCoin($num) {$this->coin = $num;}
    public function getCoin() {return $this->coin;}
}