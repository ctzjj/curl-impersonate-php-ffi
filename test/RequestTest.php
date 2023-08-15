<?php
namespace test;

use Ctzjj\CurlImpersonate\Constant\CurlOpt;
use Ctzjj\CurlImpersonate\Impersonate;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {

    public function testChromeGet() {
        $curl = Impersonate::getCurlInstance(Impersonate::CHROME_110);
        $url = "http://httpbin.org/get";
        $curl->curlSetOpt(CurlOpt::CURLOPT_URL, $url);
        $curl->curlSetOpt(CurlOpt::CURLOPT_RETURNTRANSFER, true);
        $ret = $curl->curlExec();
        $curl->curlClose();
        $this->assertTrue(stripos($ret, '}') !== false);
    }

    public function testFfGet() {
        $curl = Impersonate::getCurlInstance(Impersonate::FIREFOX_109);
        $url = "http://httpbin.org/get";
        $curl->curlSetOpt(CurlOpt::CURLOPT_URL, $url);
        $curl->curlSetOpt(CurlOpt::CURLOPT_RETURNTRANSFER, true);
        $ret = $curl->curlExec();
        $curl->curlClose();
        $this->assertTrue(stripos($ret, '}') !== false);
    }

    public function testChromePost() {
        $curl = Impersonate::getCurlInstance(Impersonate::CHROME_110);
        $url = "http://httpbin.org/post";
        // application/x-www-form-urlencoded
        $curl->curlSetOpt(CurlOpt::CURLOPT_URL, $url);
        $curl->curlSetOpt(CurlOpt::CURLOPT_RETURNTRANSFER, true);
        $curl->curlSetOpt(CurlOpt::CURLOPT_POST, true);
        $curl->curlSetOpt(CurlOpt::CURLOPT_POSTFIELDS, http_build_query(['foo' => 'bar']));
        $ret = $curl->curlExec();
        $this->assertTrue(stripos($ret, '"foo": "bar"') !== false);
        // application/json
        $curl->curlSetOpt(CurlOpt::CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8']);
        $curl->curlSetOpt(CurlOpt::CURLOPT_POSTFIELDS, json_encode(['foo' => 'bar']));
        $ret = $curl->curlExec();
        $this->assertTrue(stripos($ret, '"foo": "bar"') !== false);
    }

    public function testFfPost() {
        $curl = Impersonate::getCurlInstance(Impersonate::CHROME_110);
        $url = "http://httpbin.org/post";
        // application/x-www-form-urlencoded
        $curl->curlSetOpt(CurlOpt::CURLOPT_URL, $url);
        $curl->curlSetOpt(CurlOpt::CURLOPT_RETURNTRANSFER, true);
        $curl->curlSetOpt(CurlOpt::CURLOPT_POST, true);
        $curl->curlSetOpt(CurlOpt::CURLOPT_POSTFIELDS, http_build_query(['foo' => 'bar']));
        $ret = $curl->curlExec();
        $this->assertTrue(stripos($ret, '"foo": "bar"') !== false);

        // application/json
        $curl->curlSetOpt(CurlOpt::CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8']);
        $curl->curlSetOpt(CurlOpt::CURLOPT_POSTFIELDS, json_encode(['foo' => 'bar']));
        $ret = $curl->curlExec();
        $this->assertTrue(stripos($ret, '"foo": "bar"') !== false);

    }

    public function testReferer() {
        $curl = Impersonate::getCurlInstance(Impersonate::CHROME_110);
        $url = "http://httpbin.org/get";
        $curl->curlSetOpt(CurlOpt::CURLOPT_URL, $url);
        $curl->curlSetOpt(CurlOpt::CURLOPT_RETURNTRANSFER, true);
        $curl->curlSetOpt(CurlOpt::CURLOPT_REFERER, 'https://www.bing.cn');
        $ret = $curl->curlExec();
        $this->assertTrue(stripos($ret, 'www.bing.cn') !== false);

    }

}