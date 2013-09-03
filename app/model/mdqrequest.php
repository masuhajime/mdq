<?php
use Guzzle\Http\Client;
use Guzzle\Plugin\Cookie\Cookie;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\FileCookieJar;

class MdqRequest {
    
    private static $cookies = null;
    
    public static function getCookieInstance()
    {
        if (is_null(static::$cookies)) {
            $cookie_file_name = __DIR__."/cookies/hajisuke";
            static::$cookies = new FileCookieJar($cookie_file_name);
        }
        return static::$cookies;
    }
    
    public static function getClient()
    {
        $client = new Client('https://member.jp.square-enix.com/');
        $client->addSubscriber(new CookiePlugin(static::getCookieInstance()));
        $client->getConfig()->set('request.params', array(
            'redirect.strict' => true,
            'redirect.disable' => true// mdq内ではredirectはoffにする
        ));
        return $client;
    }
    
    public static function setCookie($response)
    {
        $setcookie = $response->getSetCookie();
        list($name, $value) = explode('=', $setcookie);
        $cookies = static::getCookieInstance();
        $cookie = new Cookie(array(
            'name' => $name,
            'value' => $value,
            'domain' => 'mdq.member.jp.square-enix.com',
            'path' => '/'
        ));
        $cookies->add($cookie);
        //var_dump(static::$cookies);
    }
}