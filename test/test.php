<?php
require_once '../vendor/autoload.php';

use Ctzjj\CurlImpersonate\Constant\CurlInfo;
use Ctzjj\CurlImpersonate\Constant\CurlOpt;
use Ctzjj\CurlImpersonate\Curl;
use Ctzjj\CurlImpersonate\Impersonate;



/**
 * @var Curl
 */
$curl = Impersonate::getCurlInstance(Impersonate::CHROME_110);

//$url = "https://tls.browserleaks.com/json";
$url = "https://www.baidu.com";
//$url = "http://curl.test";
//$url = 'https://ascii2d.net';


var_dump($curl->curlVersion()['ssl_version']);
echo PHP_EOL;

$curl->curlSetOpt(CurlOpt::CURLOPT_TIMEOUT, 5);
$curl->curlSetOpt(CurlOpt::CURLOPT_URL, $url);
$curl->curlSetOpt(CurlOpt::CURLOPT_CERTINFO, 1);
$curl->curlSetOpt(CurlOpt::CURLOPT_SSL_VERIFYPEER, 0);
$curl->curlSetOpt(CurlOpt::CURLOPT_HEADER, true);
$curl->curlSetOpt(CurlOpt::CURLOPT_RETURNTRANSFER, true);
//$curl->curlSetOpt(CurlOpt::CURLOPT_HTTPHEADER, ['x-requested-with:AJAX-32134214392fqr321df2r3=============']);

//$curl->curlSetOpt(CurlOpt::CURLOPT_PROXYTYPE, 'http');
//$curl->curlSetOpt(CurlOpt::CURLOPT_PROXY, '127.0.0.1');
//$curl->curlSetOpt(CurlOpt::CURLOPT_PROXYPORT, 6800);

$ret = $curl->curlExec();
//var_dump($curl->curlGetInfo($ch, CurlInfo::CURLINFO_CERTINFO));


//var_dump(curl_getinfo($ch2, CURLINFO_CERTINFO));

//echo  $curl->curlGetInfo($ch, CurlInfo::CURLINFO_CONNECT_TIME);
//
//$headerSize = $curl->curlGetInfo(CurlInfo::CURLINFO_HEADER_SIZE);
//echo substr($ret, 0 , $headerSize);
echo PHP_EOL;
//print_r($curl->curlGetInfo());
$curl->curlClose();
echo PHP_EOL;
echo $ret;

echo $curl->curlError();

//$ch = curl_init();
//curl_setopt($ch, CURLOPT_URL, $url);
//curl_setopt($ch,CURLOPT_CERTINFO, 1);
//curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 0);
//curl_setopt($ch,CURLOPT_HEADER, true);
//curl_exec($ch);
