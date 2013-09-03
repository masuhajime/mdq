<?php
namespace app\model\request;

use Guzzle\Http\Client;
use Guzzle\Plugin\Cookie\Cookie;
use Guzzle\Plugin\Cookie\CookiePlugin;
use app\helper\Logger;

class RequestMdq extends RequestBase
{
    
    private static function getClient()
    {
        $client = new Client('https://mdq.member.jp.square-enix.com/');
        $client->addSubscriber(new CookiePlugin(CookieManager::getCookieInstance()));
        $client->getConfig()->set('request.params', array(
            'redirect.strict' => true,
            'redirect.disable' => true// mdq内ではredirectはoffにする
        ));
        $client->setUserAgent('Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A403 Safari/8536.25');
        return $client;
    }
    
    public static function getLoginedClient()
    {
        Logger::info('get login client', __LINE__, __FILE__);
        $client = self::getClient();
        $request = $client->get('http://mdq.member.jp.square-enix.com/start.php');
        self::send($request);
        //Request::setCookie($r);
        return $client;
    }
}