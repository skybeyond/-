<?php
/**
 * Created by PhpStorm.
 * User: beyond
 * Date: 2018/1/3
 * Time: 11:21
 */
define("ROOT_PATH", dirname(__FILE__) . DIRECTORY_SEPARATOR);
define("CACHE_PATH", ROOT_PATH."cache".DIRECTORY_SEPARATOR);
header("Access-Control-Allow-Origin: *");
//$test = file_get_contents('https://img.98754.com/o/hongbao/d.php?callback=jsonp&uu');
//$t = str_replace(array('jsonp','(',')'),'',$test);
//$m = json_decode($t,true);
//print_r($m);

//请求url地址
$appId = 'wx6021957e0995944c';
$appSecret = '25d6177925d5cf1ddfa34e4a9caa465d';

class WX{
    public $appId = 'wx6021957e0995944c';
    private $appSecret = '25d6177925d5cf1ddfa34e4a9caa465d';
    public $noncestr;
    private $cache = '';

    public function __construct()
    {

    }

    public function gettoken(){

        $token = get_cache(CACHE_PATH,"token");
        if($token){
            return $token;
        }else{
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appId."&secret=".$this->appSecret;
            $access = $this->curlget($url);
            $access = json_decode($access,true);
            if($access['expires_in']==7200) {
                set_cache(CACHE_PATH, $access, 7150, 'token');
                return $access;
            }else{
                return '';
            }
        }
    }

    public function getjsapi(){

        $jsapi = get_cache(CACHE_PATH,"jsapi");
        if($jsapi){
            return $jsapi;
        }else{
            $token = $this->gettoken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$token['access_token']."&type=jsapi";
            $access = $this->curlget($url);
            $access = json_decode($access,true);
            if($access['errcode']==0) {
                set_cache(CACHE_PATH, $access, 7150, 'jsapi');
                return $access;
            }else{
                return '';
            }
        }
    }

    private function curlget($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if(!curl_exec($ch)){
            $data='';
        }else{
            $data = curl_multi_getcontent($ch);
        }
        curl_close($ch);
        return $data;
    }
}


function set_cache($cache_name,$data,$expire=7000,$cache_path=''){
    $cache = array(
        'expire'=>time()+$expire,
        'data'=>$data
    );
    $path = $cache_name.$cache_path.DIRECTORY_SEPARATOR;
    if(!is_dir($path)) {
        mkdir($path, 0777, true);
    }
    file_put_contents($path.md5($cache_name).'.cache',serialize($cache));
    return true;
}

/**
 * 获取文件缓存
 * @param $cache_name
 * @param string $cache_path
 * @return array
 */
function get_cache($cache_name,$cache_path=''){
    $path = $cache_name.$cache_path.DIRECTORY_SEPARATOR;
    if(!file_exists($path.md5($cache_name).'.cache')) return array();
    $conent = file_get_contents($path.md5($cache_name).'.cache');
    $cache = unserialize($conent);
    if(isset($cache['expire'])&&$cache['expire']>time()) return $cache['data'];
    return array();
}

$jsonstr = file_get_contents("php://input");
$data = json_decode($jsonstr,true);
$data['type'] = "jsapi";
$wx = new WX();
switch($data['type']){
    case "jsapi":
        $jsapi = $wx->getjsapi();
        if($jsapi&&$jsapi['errcode']==0){
            $str['nonceStr'] = randomCharacter(16);
            $str['jsapi_ticket'] = $jsapi['ticket'];
            $str['timestamp'] = time();
	    $protocol = (!empty($_SERVER[HTTPS]) && $_SERVER[HTTPS] !== off || $_SERVER[SERVER_PORT] == 443) ? "https://" : "http://";
  	    $urls = $protocol.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
            $str['url'] = 'http://vw.netjoy.com/good2/fourth.html';

            //ksort($str);

            //$string = http_build_query($str);
	    //$strings = urldecode($string);
	    //$signature = sha1($string);
	    //print_r($signature);
	    $string = "jsapi_ticket={$jsapi['ticket']}&noncestr={$str['nonceStr']}&timestamp={$str['timestamp']}&url={$str['url']}";
            $signature = sha1($string);
            //print_r($signature);exit;


            $arr['errcode'] = 0;
            $arr['appId'] = $wx->appId;
            $arr['timestamp'] = $str['timestamp'];
            $arr['nonceStr']  = $str['nonceStr'];
            $arr['signature']  = $signature;
	    $arr['url'] = 'http://vw.netjoy.com/good2/fourth.html';
	    $arr['jsapi_ticket'] = $jsapi['ticket'];
	   
	
	   
            echo json_encode($arr);
        }else{
            echo json_encode(array('errcode'=>404,'data'=>''));
        }
}


function randomCharacter($number=6){
    $m = '0123456789abcdefghijkpqrstuvwxyzABCDEFGHIJKPQRSTUVWXYZ';
    $s = str_shuffle($m);
    $str = substr($s,1,$number);
    return $str;
}
