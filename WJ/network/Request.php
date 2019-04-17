<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: wangjian
 * Date: 2019/4/17
 * Time: 21:42
 */

namespace WJ\network;

use WJ\util\Collection;

class Request
{
    public $url;

    public $base;

    public $method;

    public $referrer;

    public $ip;

    public $ajax;

    public $scheme;

    public $userAgent;

    public $type;

    public $length;

    public $query;

    public $data;

    public $cookies;

    public $files;

    public $accept;

    public $proxyIp;


    public function __construct($config = [])
    {
        if(empty($config)){
            $config=[

            ];
        }

    }

    /**
     * 获取一个变量来自$_SERVER ,如果没有使用默认值$default
     *
     * @author wangjian
     * @param string $name
     * @param string $default
     * @return string
     */
    public static function getVar(string $name, $default = '')
    {
        return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
    }

}