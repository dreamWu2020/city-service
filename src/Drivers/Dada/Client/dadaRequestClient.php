<?php
namespace CityService\Drivers\Dada\Client;

use CityService\Drivers\Dada\Response;
use GuzzleHttp\Client;

/**
 * 达达接口请求客户端
 */

class DadaRequestClient
{

    /**
     * http request timeout;
     */
    private $httpTimeout = 5;

    /**
     * 配置项
     */
    private $config;

    /**
     * 接口类
     */
    private $api;

    /**
     * 构造函数
     */
    public function __construct($config, $api)
    {
        $this->config = $config;
        $this->api = $api;
    }

    /**
     * 请求调用api
     * @return bool
     */
    public function makeRequest()
    {
        $reqParams = $this->bulidRequestParams();
        $resp = $this->getHttpRequestWithPost(json_encode($reqParams));
        return $this->parseResponseData($resp);
    }

    /**
     * 构造请求数据
     * data:业务参数，json字符串
     */
    public function bulidRequestParams()
    {
        $config = $this->getConfig();
        $api = $this->getApi();

        $requestParams = array();
        $requestParams['app_key'] = $config->getAppKey();
        $requestParams['body'] = $api->getBusinessParams();
        $requestParams['format'] = $config->getFormat();
        $requestParams['v'] = $config->getV();
        $requestParams['source_id'] = $config->getSourceId();
        $requestParams['timestamp'] = time();
        $requestParams['signature'] = $this->_sign($requestParams);
        return $requestParams;
    }

    /**
     * 签名生成signature
     */
    public function _sign($data)
    {

        $config = $this->getConfig();

        //1.升序排序
        ksort($data);

        //2.字符串拼接
        $args = "";
        foreach ($data as $key => $value) {
            $args .= $key . $value;
        }
        $args = $config->app_secret . $args . $config->app_secret;
        //3.MD5签名,转为大写
        $sign = strtoupper(md5($args));

        return $sign;
    }

    /**
     * 发送请求,POST
     * @param $url 指定URL完整路径地址
     * @param $data 请求的数据
     */
    public function getHttpRequestWithPost($data)
    {

        $config = $this->config;
        $api = $this->api;
        $url = $config->getHost() . $api->getUrl();

        try {
            $client = new Client([
                'timeout' => 30,
            ]);

            $body = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => $data,
            ])->getBody();
            return $body;
        } catch (GuzzleException $e) {
            throw new HttpException($e->getMessage());
        }

        // json
        // $headers = array(
        //     'Content-Type: application/json',
        // );
        // $curl = curl_init($url);
        // curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_HEADER, false);
        // curl_setopt($curl, CURLOPT_POST, true);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        // curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        // $resp = curl_exec($curl);
        // // var_dump( curl_error($curl) );//如果在执行curl的过程中出现异常，可以打开此开关查看异常内容。
        // $info = curl_getinfo($curl);
        // curl_close($curl);
        // if (isset($info['http_code']) && $info['http_code'] == 200) {
        //     return $resp;
        // }
        // return '';
    }

    /**
     * 解析响应数据
     * @param $arr返回的数据
     * 响应数据格式：{"status":"success","result":{},"code":0,"msg":"成功"}
     */
    public function parseResponseData($arr)
    {
        // $resp = new DadaResponse();
        // if (empty($arr)) {
        //     $resp->setStatus(DadaConstant::FAIL);
        //     $resp->setMsg(DadaConstant::FAIL_MSG);
        //     $resp->setCode(DadaConstant::FAIL_CODE);
        // }else{
        //     $data = json_decode($arr, true);
        //     $resp->setStatus($data['status']);
        //     $resp->setMsg($data['msg']);
        //     $resp->setCode($data['code']);
        //     $resp->setResult($data['result']);
        // }

        // return $resp;

        return new Response(json_decode((string) $arr, true));
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getApi()
    {
        return $this->api;
    }
}
