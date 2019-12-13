<?php
namespace App\Traits;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Storage;
use Auth;

trait GridTrait {

    public function isServerActive($ip, $port){
        $url = '';
        //$url = 'http://'.$ip.':'.$port.'/wd/hub/status';
        $client = new Client();
        try {
            $res = $client->request('GET', $url, ['connect_timeout' => 0.5]);
            return true;
        } catch (RequestException $e) {
            return false;
        }
    }

    public function getBrowser($pid){
        
    }

}
