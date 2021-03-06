<?php
/**
 * Created by PhpStorm.
 * User: yzx
 * Date: 2017-12-14
 * Time: 10:23
 * http://api.fanyi.baidu.com/api/trans/product/index 百度翻译平台
 */

namespace App\Handlers;


use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;

class SlugTranslateHandler
{
    public function translate($text)
    {
        //实例化 HTTP 客户端
        $http = new Client();
        // 初始化配置信息
        $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid = config('services.baidu_translate.appid');
        $key = config('services.baidu_translate.key');
        $salt = time();

        //如果没有配置百度翻译，使用拼音
        if (empty($appid) || empty($key)) {
            return $this->pinyin($text);
        }

        // 根据文档，生成 sign
        // http://api.fanyi.baidu.com/api/trans/product/apidoc
        // appid+q+salt+密钥 的MD5值
        $sign = md5($appid . $text . $salt . $key);
        $query = http_build_query([
            'q'     =>  $text,
            'from'  =>  'zh',
            'to'    =>  'en',
            'appid' =>  $appid,
            'salt'  =>  $salt,
            'sign'  =>  $sign
        ]);


        //发送http get请求
        $response = $http->get($api.$query);
        $result = json_decode($response->getBody(),true);

        /**
         * 结果成功如下：
         array:3 [▼
            "from" => "zh"
            "to" => "en"
            "trans_result" => array:1 [▼
                0 => array:2 [▼
                    "src" => "XSS 安全漏洞"
                    "dst" => "XSS security vulnerability"
                ]
            ]
        ]
         */
        if (isset($result['trans_result'][0]['dst'])) {
            return str_slug($result['trans_result'][0]['dst']);
        } else {
            return $this->pinyin($text);
        }


    }

    public function pinyin($text)
    {
        return str_slug(app(Pinyin::class)->permalink($text));
    }

}





















