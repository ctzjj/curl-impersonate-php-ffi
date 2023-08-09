<?php
namespace  Ctzjj\CurlImpersonate\Constant;

/**
 * https://github.com/curl/curl/blob/master/include/curl/curl.h
 * Class CurlMultiOpt
 * @package Ctzjj\CurlImpersonate\Constant
 */
class CurlMultiOpt {
    const CURLMOPT_SOCKETFUNCTION = 20000 + 1;
    const CURLMOPT_SOCKETDATA = 10000 + 2;
    const CURLMOPT_PIPELINING = 0 + 3;
    const CURLMOPT_TIMERFUNCTION = 20000 + 4;
    const CURLMOPT_TIMERDATA = 10000 + 5;
    const CURLMOPT_MAXCONNECTS = 0 + 6;
    const CURLMOPT_MAX_HOST_CONNECTIONS = 0 + 7;
    const CURLMOPT_MAX_PIPELINE_LENGTH = 0 + 8;
    const CURLMOPT_CONTENT_LENGTH_PENALTY_SIZE = 30000 + 9;
    const CURLMOPT_CHUNK_LENGTH_PENALTY_SIZE = 30000 + 10;
    const CURLMOPT_PIPELINING_SITE_BL = 10000 + 11;
    const CURLMOPT_PIPELINING_SERVER_BL = 10000 + 12;
    const CURLMOPT_MAX_TOTAL_CONNECTIONS = 0 + 13;
    const CURLMOPT_PUSHFUNCTION = 20000 + 14;
    const CURLMOPT_PUSHDATA = 10000 + 15;
    const CURLMOPT_MAX_CONCURRENT_STREAMS = 0 + 16;
}