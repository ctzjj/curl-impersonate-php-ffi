<?php
require_once '../vendor/autoload.php';

use Ctzjj\CurlImpersonate\Constant\CurlInfo;
use Ctzjj\CurlImpersonate\Constant\CurlOpt;
use Ctzjj\CurlImpersonate\Impersonate;



/**
 * @var \Ctzjj\CurlImpersonate\Curl
 */
$curl = Impersonate::getCurlInstance(Impersonate::CHROME_110);

$url = "https://tls.browserleaks.com/json";
//$url = "https://www.baidu.com";

$ch = $curl->curlInit();


var_dump($curl->curlVersion()['ssl_version']);
echo PHP_EOL;

//$curl->curlSetOpt($ch, CurlOpt::CURLOPT_TIMEOUT, 5);
$curl->curlSetOpt($ch, CurlOpt::CURLOPT_URL, $url);
$curl->curlSetOpt($ch, CurlOpt::CURLOPT_CERTINFO, 1);
$curl->curlSetOpt($ch, CurlOpt::CURLOPT_SSL_VERIFYPEER, 0);
$curl->curlSetOpt($ch, CurlOpt::CURLOPT_HEADER, true);

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

