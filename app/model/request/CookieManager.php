<?php
namespace app\model\request;

use Guzzle\Plugin\Cookie\CookieJar\FileCookieJar;

class CookieManager {
    
    private static $cookies = null;
    
    public static function getCookieInstance()
    {
        if (is_null(static::$cookies)) {
            // TODO: ここを外から名称を与えられるようにする
            $cookie_file_name = __DIR__."/../../../cookies/hajisuke";
            static::$cookies = new FileCookieJar($cookie_file_name);
        }
        return static::$cookies;
    }
}
