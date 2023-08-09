<?php
namespace Ctzjj\CurlImpersonate;

use RuntimeException;

class Impersonate {

    const CHROME_99 = 'chrome99';

    const CHROME_100 = 'chrome100';

    const CHROME_101 = 'chrome101';

    const CHROME_104 = 'chrome104';

    const CHROME_107 = 'chrome107';

    const CHROME_110 = 'chrome110';

    const CHROME_99_ANDROID = 'chrome99_android';

    const CHROME_EDGE_99 = 'edge99';

    const CHROME_EDGE_101 = 'edge101';

    const FIREFOX_91_ESR = 'ff91esr';

    const FIREFOX_95 = 'ff95';

    const FIREFOX_98 = 'ff98';

    const FIREFOX_100 = 'ff100';

    const FIREFOX_102 = 'ff102';

    const FIREFOX_109 = 'ff109';

    const SAFARI_15_3 = 'safari15_3';

    const SAFARI_15_5 = 'safari15_5';


    public static function getCurlInstance($impersonate = self::CHROME_110) : Curl {
        $browser = self::getBrowser($impersonate);
        $pathInfo = self::getLibFilePathInfo($browser);
        return new Curl($impersonate, $pathInfo['libCurlPath'], $pathInfo['libWritePath']);
    }

    /**
     * @see https://github.com/lwthiker/curl-impersonate/blob/main/browsers.json
     * @param $impersonate
     * @return string
     */
    public static function getBrowser($impersonate) : string {
        $chromeImpersonateList = [
            self::CHROME_99,
            self::CHROME_100,
            self::CHROME_101,
            self::CHROME_104,
            self::CHROME_107,
            self::CHROME_110,
            self::CHROME_99_ANDROID,
            self::CHROME_EDGE_99,
            self::CHROME_EDGE_101,
            self::SAFARI_15_3,
            self::SAFARI_15_5
        ];
        return in_array($impersonate, $chromeImpersonateList, true) ? 'chrome': 'ff';
    }

    /**
     * @param String $browser
     * @return array
     */
    private static function getLibFilePathInfo(String $browser) {
        if (PHP_INT_SIZE !== 8) {
            throw new RuntimeException('Unsupported 32-bit system, please compile the lib files by yourself.');
        }
        $dir = __DIR__;
        $libCurlPath = '';
        $libWritePath = '';
        if (PHP_OS === 'Linux') {
            $libCurlPath = $dir . '/lib/libcurl-impersonate-' . $browser . '.x86_64.so';
            $libWritePath = $dir . '/lib/write.x86_64.so';
            if(php_uname('m') == 'arm64'){
                $libCurlPath = $dir . '/lib/libcurl-impersonate-' . $browser . '.aarch64.so';
                $libWritePath = $dir . '/lib/write.aarch64.so';
            }
        }
        if (PHP_OS === 'WINNT') {
            $libCurlPath = $dir . '/lib/libcurl-impersonate.x86_64.dll';
            $libWritePath = $dir . '/lib/write.x86_64.dll';
        }
        if (PHP_OS === 'Darwin') {
            $libCurlPath = $dir . '/lib/libcurl-impersonate-' . $browser . '.x86_64.dylib';
            $libWritePath = $dir . '/lib/write.x86_64.dylib';
            //mac m1 m2 arm64
            if(php_uname('m') == 'arm64'){
                $libCurlPath = $dir . '/lib/libcurl-impersonate-' . $browser . '.aarch64.dylib';
                $libWritePath = $dir . '/lib/write.aarch64.dylib';
            }
        }

        if (!file_exists($libCurlPath)) {
            throw new RuntimeException('Unsupported system, please compile <libcurl-impersonate> by yourself.');
        }
        if (!file_exists($libWritePath)) {
            throw new RuntimeException('Unsupported system, please compile <write.c> by yourself.');
        }
        return [ 'libCurlPath' => realpath($libCurlPath), 'libWritePath' => $libWritePath];

    }
}