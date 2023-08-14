<?php
namespace Ctzjj\CurlImpersonate;

use Ctzjj\CurlImpersonate\Constant\CurlInfo;
use Ctzjj\CurlImpersonate\Constant\CurlOpt;
use \FFI;
use RuntimeException;

class Curl {

    private FFI $libCurlFFI;

    private FFI $libWriteFFI;

    private FFI\CData $ch;

    private String $impersonate;

    private int $returnType;

    private int $errorNo;

    private bool $isDestroyed;

    private FFI\CData $writeData;

    private array $curlOptSlistPtrs = [];

    public function __construct($impersonate, $libCurlPath, $libWritePath) {
        $this->impersonate = $impersonate;
        $this->returnType = 0;
        $this->errorNo = 0;
        $this->libCurlFFI = $this->getCurlFFI($libCurlPath);
        $this->libWriteFFI = $this->getWriteFFI($libWritePath);
        $this->curlInit();
    }

    /**
     * curlEasyInit
     *
     * @return FFI\CData
     */
    private function curlInit() {
        $this->writeData = $this->libWriteFFI->new("own_write_data");
        $this->ch = $this->libCurlFFI->curl_easy_init();
        if (FFI::isNull($this->ch)) {
            throw new RuntimeException("curl init fail.");
        }
        $this->isDestroyed = false;
        $this->curlImpersonate($this->impersonate, true);
    }

    /**
     * curlEasySetopt
     *
     * @param resource $ch
     * @param int $option
     * @param mixed $value
     * @return bool
     */
    public function curlSetOpt($option, $value) {
        $slisTypeList = [
            CurlOpt::CURLOPT_HTTPHEADER, // This points to a linked list of headers, struct curl_slist kind. This list is also used for RTSP (in spite of its name)
            CurlOpt::CURLOPT_QUOTE, // send linked-list of QUOTE commands
            CurlOpt::CURLOPT_POSTQUOTE, // send linked-list of post-transfer QUOTE commands
            CurlOpt::CURLOPT_TELNETOPTIONS, // This points to a linked list of telnet options
            CurlOpt::CURLOPT_PREQUOTE, // send linked-list of pre-transfer QUOTE commands
            CurlOpt::CURLOPT_HTTP200ALIASES, // Set aliases for HTTP 200 in the HTTP Response header
            CurlOpt::CURLOPT_MAIL_RCPT, // set the list of SMTP mail receiver(s)
            CurlOpt::CURLOPT_RESOLVE, //send linked-list of name:port:address sets
            CurlOpt::CURLOPT_PROXYHEADER, // This points to a linked list of headers used for proxy requests only, struct curl_slist kind
            CurlOpt::CURLOPT_CONNECT_TO, // Linked-list of host:port:connect-to-host:connect-to-port,overrides the URL's host:port (only for the network layer)
        ];
        if (in_array($option, $slisTypeList, true)) {
            $curlSlist = $this->libCurlFFI->new('curl_slist*');
            foreach ($value as $val) {
                $curlSlist = $this->libCurlFFI->curl_slist_append($curlSlist, $val);
            }
            $curlCode = $this->libCurlFFI->curl_easy_setopt($this->ch, $option, $curlSlist);
            $this->curlOptSlistPtrs[] = &$curlSlist;
            return $curlCode;
        }

        if ((CurlOpt::CURLOPT_RETURNTRANSFER === $option) && ($value == true)) {
            $this->returnType = CurlOpt::CURLOPT_RETURNTRANSFER;
            $this->libCurlFFI->curl_easy_setopt($this->ch, CurlOpt::CURLOPT_WRITEDATA, FFI::addr($this->writeData));
            return $this->libCurlFFI->curl_easy_setopt($this->ch, CurlOpt::CURLOPT_WRITEFUNCTION, $this->libWriteFFI->init());
        }

        return $this->libCurlFFI->curl_easy_setopt($this->ch, $option, $value);
    }

    public function curlExec() {
        $int = $this->libCurlFFI->curl_easy_perform($this->ch);

        // free opt slist
        foreach ($this->curlOptSlistPtrs as &$ptr) {
            $this->libCurlFFI->curl_slist_free_all($ptr);
        }
        $this->curlOptSlistPtrs = [];

        if ($int !== CurlOpt::CURLOPT_OK) {
            $this->errorNo = $int;
            // FFI::free(FFI::addr($this->writeData));
            return false;
        }

        if (CurlOpt::CURLOPT_RETURNTRANSFER === $this->returnType) {
            $result =  FFI::string($this->writeData->buf, $this->writeData->size);
            // FFI::free(FFI::addr($this->writeData));
            return $result;
        }
        // TODO
//        if (CurlOpt::CURLOPT_FILE === $this->returnType) {
//
//        }
        return true;
    }

    public function curlErrorNo() {
        return $this->errorNo;
    }

    public function curlError() {
        if ($this->errorNo == 0) {
            return '';
        }
        $ret =  $this->libCurlFFI->curl_easy_strerror($this->errorNo);
        return FFI::string($ret);
    }

    public function curlClose() {
        if ($this->isDestroyed) {
            return ;
        }
        $this->libCurlFFI->curl_easy_cleanup($this->ch);
        $this->isDestroyed = true;
    }

    public function curlGetInfo($option = null) {

        $cTypeMap = [
            CurlInfo::CURLINFO_EFFECTIVE_URL => ['type' => 'string', 'key' => 'url'],
            CurlInfo::CURLINFO_CONTENT_TYPE => ['type' => 'string', 'key' => 'content_type'],
            CurlInfo::CURLINFO_HTTP_CODE => ['type' => 'long', 'key' => 'http_code'],
            CurlInfo::CURLINFO_HEADER_SIZE => ['type' => 'long', 'key' => 'header_size'],
            CurlInfo::CURLINFO_REQUEST_SIZE => ['type' => 'long', 'key' => 'request_size'],
            CurlInfo::CURLINFO_FILETIME => ['type' => 'long', 'key' => 'filetime'],
            CurlInfo::CURLINFO_SSL_VERIFYRESULT => ['type' => 'long', 'key' => 'ssl_verify_result'],
            CurlInfo::CURLINFO_REDIRECT_COUNT => ['type' => 'long', 'key' => 'redirect_count'],
            CurlInfo::CURLINFO_TOTAL_TIME => ['type' => 'double', 'key' => 'total_time'],
            CurlInfo::CURLINFO_NAMELOOKUP_TIME => ['type' => 'double', 'key' => 'namelookup_time'],
            CurlInfo::CURLINFO_CONNECT_TIME => ['type' => 'double', 'key' => 'connect_time'],
            CurlInfo::CURLINFO_PRETRANSFER_TIME => ['type' => 'double', 'key' => 'pretransfer_time'],
            CurlInfo::CURLINFO_SIZE_UPLOAD => ['type' => 'double', 'key' => 'size_upload'],
            CurlInfo::CURLINFO_SIZE_DOWNLOAD => ['type' => 'double', 'key' => 'size_download'],
            CurlInfo::CURLINFO_SPEED_DOWNLOAD => ['type' => 'double', 'key' => 'speed_download'],
            CurlInfo::CURLINFO_SPEED_UPLOAD => ['type' => 'double', 'key' => 'speed_upload'],
            CurlInfo::CURLINFO_CONTENT_LENGTH_DOWNLOAD => ['type' => 'double', 'key' => 'download_content_length'],
            CurlInfo::CURLINFO_CONTENT_LENGTH_UPLOAD => ['type' => 'double', 'key' => 'upload_content_length'],
            CurlInfo::CURLINFO_STARTTRANSFER_TIME => ['type' => 'double', 'key' => 'starttransfer_time'],
            CurlInfo::CURLINFO_REDIRECT_TIME => ['type' => 'double', 'key' => 'redirect_time'],
            CurlInfo::CURLINFO_REDIRECT_URL => ['type' => 'string', 'key' => 'redirect_url'],
            CurlInfo::CURLINFO_PRIMARY_IP => ['type' => 'string', 'key' => 'primary_ip'],
            CurlInfo::CURLINFO_CERTINFO => ['type' => 'curl_certinfo', 'key' => 'certinfo'],
            CurlInfo::CURLINFO_PRIMARY_PORT => ['type' => 'long', 'key' => 'primary_port'],
            CurlInfo::CURLINFO_LOCAL_IP => ['type' => 'string', 'key' => 'local_ip'],
            CurlInfo::CURLINFO_LOCAL_PORT => ['type' => 'long', 'key' => 'local_port'],
            CurlInfo::CURLINFO_HTTP_VERSION => ['type' => 'long', 'key' => 'http_version'],
            CurlInfo::CURLINFO_PROTOCOL => ['type' => 'long', 'key' => 'protocol'],
            CurlInfo::CURLINFO_PROXY_SSL_VERIFYRESULT => ['type' => 'long', 'key' => 'ssl_verifyresult'],
            CurlInfo::CURLINFO_SCHEME => ['type' => 'string', 'key' => 'scheme'],
            CurlInfo::CURLINFO_APPCONNECT_TIME_T => ['type' => 'long', 'key' => 'appconnect_time_us'],
            CurlInfo::CURLINFO_CONNECT_TIME_T => ['type' => 'long', 'key' => 'connect_time_us'],
            CurlInfo::CURLINFO_NAMELOOKUP_TIME_T => ['type' => 'long', 'key' => 'namelookup_time_us'],
            CurlInfo::CURLINFO_PRETRANSFER_TIME_T => ['type' => 'long', 'key' => 'pretransfer_time_us'],
            CurlInfo::CURLINFO_REDIRECT_TIME_T => ['type' => 'long', 'key' => 'redirect_time_us'],
            CurlInfo::CURLINFO_STARTTRANSFER_TIME_T => ['type' => 'long', 'key' => 'starttransfer_time_us'],
            CurlInfo::CURLINFO_TOTAL_TIME_T => ['type' => 'long', 'key' => 'total_time_us'],
        ];

        if (null === $option) {
            $info = [];
            foreach ($cTypeMap as $curlInfo => $dataInfo) {
                $info[$dataInfo['key']] = $this->getCurlInfoValue($curlInfo, $dataInfo['type']);
            }
            return $info;
        }

        if (!isset($cTypeMap[$option])) {
            return false;
        }

        $dataInfo = $cTypeMap[$option];
        return $this->getCurlInfoValue($option, $dataInfo['type']);
    }

    public function curlReset() {
        return $this->libCurlFFI->curl_easy_reset($this->ch);
    }

    public function curlVersion() {
        $info = [];
        $data = $this->libCurlFFI->curl_version_info(7);
        if (FFI::isNull($data)) {
            return false;
        }
        $cData = $data[0];
        $info['version_number'] = $cData->version_num;
        $info['age'] = $cData->age;
        $info['features'] = $cData->features;
        $info['ssl_version_number'] = $cData->ssl_version_num;
        $info['version'] = $cData->version;
        $info['host'] = $cData->host;
        $info['ssl_version'] = $cData->ssl_version;
        $info['libz_version'] = $cData->libz_version;
        $i = 0;
        $protocols = [];
        while (1) {
            $data = $cData->protocols[$i];
            if (null === $data) {
                break;
            }
            $protocols[$i] = $data;
            $i += 1;
        }
        $info['protocols'] = $protocols;
        if ($cData->age >= 1) {
            $info['ares'] = $cData->ares;
            $info['ares_num'] = $cData->ares_num;
        }
        if ($cData->age >= 2) {
            $info['libidn'] = $cData->libidn;
	    }
        if ($cData->age >= 3) {
            $info['iconv_ver_num'] = $cData->iconv_ver_num;
            $info['libssh_version'] = $cData->libssh_version;
	    }
        if ($cData->age >= 4) {
            $info['brotli_ver_num'] = $cData->brotli_ver_num;
            $info['brotli_version'] = $cData->brotli_version;
	    }
        return $info;
    }

    public function curlImpersonate(string $impersonate, bool $defaultHeaders) {
        $this->compareImpersonateBrowser($impersonate);
        return $this->libCurlFFI->curl_easy_impersonate($this->ch, $impersonate, $defaultHeaders);
    }

    private function getCurlFFI($curlPath) {
        return FFI::cdef(file_get_contents(__DIR__ . '/cdef/libcurl_impersonate.h'), $curlPath);
    }

    private function getWriteFFI($writePath) {
        return FFI::cdef(file_get_contents(__DIR__ . '/cdef/write.h'), $writePath);
    }

    private function compareImpersonateBrowser(string $impersonate) {
        $initBrowser = Impersonate::getBrowser($this->impersonate);
        $changeBrowser = Impersonate::getBrowser($impersonate);
        if ($initBrowser !== $changeBrowser) {
            throw new RuntimeException('The initialized browser type does not match the input browser type. Please reinitialize the corresponding instance.');
        }
    }

    private function getCurlInfoValue($curlInfo, $cType) {
        $string = FFI::new("char*");
        $long = FFI::new("long");
        $double = FFI::new("double");
        if ($cType === 'long') {
            if (CurlOpt::CURLOPT_OK === $this->libCurlFFI->curl_easy_getinfo($this->ch, $curlInfo, FFI::addr($long))) {
                return $long->cdata;
            }
        }

        if ($cType === 'double') {
            if (CurlOpt::CURLOPT_OK === $this->libCurlFFI->curl_easy_getinfo($this->ch, $curlInfo, FFI::addr($double))) {
                return $double->cdata;
            }
        }

        if ($cType === 'string') {
            if (CurlOpt::CURLOPT_OK === $this->libCurlFFI->curl_easy_getinfo($this->ch, $curlInfo, FFI::addr($string))) {
                if (FFI::isNull($string)) {
                    return null;
                }
                return FFI::string($string);
            }
        }
        if ($cType === 'curl_certinfo') {
            $certInfo = $this->libCurlFFI->new('curl_certinfo*');
            if (CurlOpt::CURLOPT_OK === $this->libCurlFFI->curl_easy_getinfo($this->ch, $curlInfo, FFI::addr($certInfo))) {
                if (FFI::isNull($certInfo)) {
                    return [];
                }
                $cData = $certInfo[0];
                $certNum = $cData->num_of_certs->cdata ?? 0;
                $certDataList = [];
                for ($i = 0; $i < $certNum; $i++ ) {
                    $certData = [];
                    $sList = $cData->certinfo[$i];
                    while (1) {
                        if (FFI::isNull($sList->next)) {
                            break;
                        }
                        $str = FFI::string($sList->data);
                        $strArr = explode(':', $str, 2);
                        if (!isset($strArr[1])) {
                            $certData[$strArr[0]] = null;
                            $sList = $sList->next;
                            continue;
                        }
                        $certData[$strArr[0]] = $strArr[1];
                        $sList = $sList->next;
                    }
                    $certDataList[] = $certData;
                }

                return $certDataList;
            }
        }
        return null;
    }

    public function __destruct() {
        foreach ($this->curlOptSlistPtrs as $ptr) {
            $this->libCurlFFI->curl_slist_free_all($ptr);
        }

        if (!$this->isDestroyed) {
            $this->libCurlFFI->curl_easy_cleanup($this->ch);
        }
    }
}