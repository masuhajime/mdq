<?php

namespace app\model\request;
use Guzzle\Plugin\Cookie\Cookie;

abstract class RequestBase
{
    public static function send($request)
    {
        $response = $request->send();
        
        $setcookie = $response->getSetCookie();
        list($name, $value) = explode('=', $setcookie);
        $cookies = CookieManager::getCookieInstance();
        $cookie = new Cookie(array(
            'name' => $name,
            'value' => $value,
            'domain' => 'mdq.member.jp.square-enix.com',
            'path' => '/'
        ));
        $cookies->add($cookie);
        
        return $response;
    }
}