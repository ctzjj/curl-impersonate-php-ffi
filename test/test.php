<?php
require_once '../vendor/autoload.php';

use Ctzjj\CurlImpersonate\Constant\CurlInfo;
use Ctzjj\CurlImpersonate\Constant\CurlOpt;
use Ctzjj\CurlImpersonate\Curl;
use Ctzjj\CurlImpersonate\Impersonate;



/**
 * @var Curl
 */
$curl = Impersonate::getCurlInstance(Impersonate::FIREFOX_98);

$url = "https://tls.browserleaks.com/json";
//$url = "https://www.baidu.com";
//$url = "http://curl.test";

$ch = $curl->curlInit();


var_dump($curl->curlVersion()['ssl_version']);
echo PHP_EOL;

//$curl->curlSetOpt($ch, CurlOpt::CURLOPT_TIMEOUT, 5);
$curl->curlSetOpt($ch, CurlOpt::CURLOPT_URL, $url);
$curl->curlSetOpt($ch, CurlOpt::CURLOPT_CERTINFO, 1);
$curl->curlSetOpt($ch, CurlOpt::CURLOPT_SSL_VERIFYPEER, 0);
$curl->curlSetOpt($ch, CurlOpt::CURLOPT_HEADER, true);
$curl->curlSetOpt($ch, CurlOpt::CURLOPT_HTTPHEADER, ['x-requested-with:AJAX-32134214392fqr321df2r3=============']);

$ret = $curl->curlExec($ch);
//var_dump($curl->curlGetInfo($ch, CurlInfo::CURLINFO_CERTINFO));


//var_dump(curl_getinfo($ch2, CURLINFO_CERTINFO));

//echo  $curl->curlGetInfo($ch, CurlInfo::CURLINFO_CONNECT_TIME);
//
echo $ret;
$headerSize = $curl->curlGetInfo($ch, CurlInfo::CURLINFO_HEADER_SIZE);
echo PHP_EOL;
echo $curl->curlGetInfo($ch, CurlInfo::CURLINFO_SSL_ENGINES);
$curl->curlClose($ch);
echo PHP_EOL;
echo substr($ret, 0 , $headerSize);

