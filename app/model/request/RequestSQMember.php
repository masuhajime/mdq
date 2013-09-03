<?php
namespace app\model\request;

use Guzzle\Http\Client;
use Guzzle\Plugin\Cookie\CookiePlugin;

// TODO: まだ動作確認してません

class RequestSQMember extends RequestBase
{
    
    public static function getClient()
    {
        $client = new Client('https://member.jp.square-enix.com/');
        $client->addSubscriber(new CookiePlugin(CookieManager::getCookieInstance()));
        $client->getConfig()->set('request.params', array(
            'redirect.strict' => true,
        ));
        return $client;
    }
    
    // この関数ではredirectをonにする
    public function loginSquareMenbers() {
        $client = Request::getClient();
        // Send the request with no cookies and parse the returned cookies
        $request = $client->post('https://member.jp.square-enix.com/login/try/');
        $request->addPostFields(array(
            'email' => '',
            'passwd' => '',
            'store' => 'on',
            'loginChallenge' => 'try',
            'next' => '',
            'http' => '//member.jp.square-enix.com/',
            'mypage' => '',
            // ticをログイン画面にアクセスしてhtmlから取得してくる
            // https://member.jp.square-enix.com/login/
            'tic' => '',
            'tic2' => ''
        ));
        $response = $request->send();
        //echo $response->getBody();
    }
}