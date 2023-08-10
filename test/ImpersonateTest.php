<?php

namespace test;

use Ctzjj\CurlImpersonate\Curl;
use Ctzjj\CurlImpersonate\Impersonate;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ImpersonateTest extends TestCase {

    public function testBrowserChrome() {
        $this->assertTrue('chrome' === Impersonate::getBrowser(Impersonate::CHROME_110));
        $this->assertTrue('chrome' === Impersonate::getBrowser(Impersonate::CHROME_107));
        $this->assertTrue('chrome' === Impersonate::getBrowser(Impersonate::CHROME_104));
        $this->assertTrue('chrome' === Impersonate::getBrowser(Impersonate::CHROME_101));
        $this->assertTrue('chrome' === Impersonate::getBrowser(Impersonate::CHROME_100));
        $this->assertTrue('chrome' === Impersonate::getBrowser(Impersonate::CHROME_99));
        $this->assertTrue('chrome' === Impersonate::getBrowser(Impersonate::CHROME_99_ANDROID));
        $this->assertTrue('chrome' === Impersonate::getBrowser(Impersonate::CHROME_EDGE_101));
        $this->assertTrue('chrome' === Impersonate::getBrowser(Impersonate::CHROME_EDGE_99));
        $this->assertTrue('chrome' === Impersonate::getBrowser(Impersonate::SAFARI_15_3));
        $this->assertTrue('chrome' === Impersonate::getBrowser(Impersonate::SAFARI_15_5));
    }

    public function testBrowserFF() {
        $this->assertTrue('ff' === Impersonate::getBrowser(Impersonate::FIREFOX_109));
        $this->assertTrue('ff' === Impersonate::getBrowser(Impersonate::FIREFOX_102));
        $this->assertTrue('ff' === Impersonate::getBrowser(Impersonate::FIREFOX_100));
        $this->assertTrue('ff' === Impersonate::getBrowser(Impersonate::FIREFOX_91_ESR));
        $this->assertTrue('ff' === Impersonate::getBrowser(Impersonate::FIREFOX_98));
        $this->assertTrue('ff' === Impersonate::getBrowser(Impersonate::FIREFOX_95));
    }

    public function testBrowserNoFoundDefault() {

        $this->assertTrue('chrome' === Impersonate::getBrowser('foobar'));
        $this->assertTrue('chrome' === Impersonate::getBrowser(3));

        $this->expectException(\TypeError::class);
        Impersonate::getBrowser(['foobar']);
    }

    public function testGetCurlInstance() {
        $this->assertInstanceOf(Curl::class, Impersonate::getCurlInstance(Impersonate::CHROME_110));

        $this->assertInstanceOf(Curl::class, Impersonate::getCurlInstance(Impersonate::FIREFOX_95));
    }

}