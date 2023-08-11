<?php
namespace test;

use Ctzjj\CurlImpersonate\Impersonate;
use PHPUnit\Framework\TestCase;

class CurlTest extends TestCase {

    public function testCurlChrome() {
        $curl = Impersonate::getCurlInstance(Impersonate::CHROME_110);
        $this->assertTrue("BoringSSL" === $curl->curlVersion()['ssl_version']);
    }

    public function testCurlFirefox() {
        $curl = Impersonate::getCurlInstance(Impersonate::FIREFOX_98);
        if (PHP_OS === 'WINNT') {
            $this->assertTrue("BoringSSL" === $curl->curlVersion()['ssl_version']);
        } else {
            $this->assertTrue("NSS/3.87" === $curl->curlVersion()['ssl_version']);
        }
    }

    public function testCurlImpersonateFF() {
        $curl = Impersonate::getCurlInstance(Impersonate::FIREFOX_98);
        if (PHP_OS === 'WINNT') {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(0 === $curl->curlImpersonate(Impersonate::FIREFOX_109, 1));
        }

        $this->expectException(\RuntimeException::class);
        $curl->curlImpersonate(Impersonate::CHROME_100, 1);
    }

    public function testCurlImpersonateChrome() {
        $curl = Impersonate::getCurlInstance(Impersonate::CHROME_100);
        $this->assertTrue(0 === $curl->curlImpersonate(Impersonate::CHROME_110, 1));

        $this->expectException(\RuntimeException::class);
        $curl->curlImpersonate(Impersonate::FIREFOX_109, 1);
    }

    public function testCurlGetInfo() {
        $curl = Impersonate::getCurlInstance(Impersonate::CHROME_100);
        $info = $curl->curlGetInfo();
        $keys = array (
            0 => 'url',
            1 => 'content_type',
            2 => 'http_code',
            3 => 'header_size',
            4 => 'request_size',
            5 => 'filetime',
            6 => 'ssl_verify_result',
            7 => 'redirect_count',
            8 => 'total_time',
            9 => 'namelookup_time',
            10 => 'connect_time',
            11 => 'pretransfer_time',
            12 => 'size_upload',
            13 => 'size_download',
            14 => 'speed_download',
            15 => 'speed_upload',
            16 => 'download_content_length',
            17 => 'upload_content_length',
            18 => 'starttransfer_time',
            19 => 'redirect_time',
            20 => 'redirect_url',
            21 => 'primary_ip',
            22 => 'certinfo',
            23 => 'primary_port',
            24 => 'local_ip',
            25 => 'local_port',
            26 => 'http_version',
            27 => 'protocol',
            28 => 'ssl_verifyresult',
            29 => 'scheme',
            30 => 'appconnect_time_us',
            31 => 'connect_time_us',
            32 => 'namelookup_time_us',
            33 => 'pretransfer_time_us',
            34 => 'redirect_time_us',
            35 => 'starttransfer_time_us',
            36 => 'total_time_us',
        );

        foreach ($keys as $k) {
            $this->assertTrue(array_key_exists($k, $info));
        }
    }

    public function testCurlVersion() {
        $curl = Impersonate::getCurlInstance(Impersonate::CHROME_100);
        $info = $curl->curlVersion();
        $keys = array (
            0 => 'version_number',
            1 => 'age',
            2 => 'features',
            3 => 'ssl_version_number',
            4 => 'version',
            5 => 'host',
            6 => 'ssl_version',
            7 => 'libz_version',
            8 => 'protocols',
            9 => 'ares',
            10 => 'ares_num',
            11 => 'libidn',
            12 => 'iconv_ver_num',
            13 => 'libssh_version',
            14 => 'brotli_ver_num',
            15 => 'brotli_version',
        );
        foreach ($keys as $k) {
            $this->assertTrue(array_key_exists($k, $info));
        }
    }

}