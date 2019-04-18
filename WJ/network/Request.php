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
    /**
     * @var string 请求的URL
     */
    public $url;

    /**
     * @var  string 基目录
     */
    public $base;

    /**
     * @var string 请求方法
     */
    public $method;

    /**
     * @var string referrer url
     */
    public $referrer;

    /**
     * @var  string IP
     */
    public $ip;

    /**
     * @var  bool 是否ajax
     */
    public $ajax;

    /**
     * @var  string 协议
     */
    public $scheme;

    /**
     * @var string  客户端user agent
     */
    public $userAgent;

    /**
     * @var string 内容MIME类型
     */
    public $type;

    /**
     * @var int  请求体大小
     */
    public $length;

    /**
     * @var  \WJ\util\Collection 查询字符串
     */
    public $query;

    /**
     * @var \WJ\util\Collection 请求原始体
     */
    public $data;

    /**
     * @var \WJ\util\Collection COOKIE
     */
    public $cookies;

    /**
     * @var \WJ\util\Collection FILES
     */
    public $files;

    /**
     * @var string  客户端ACCEPT
     */
    public $accept;

    /**
     * @var string 代理IP
     */
    public $proxyIp;

    /**
     * 构造属性
     * Request constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config = [
                'url' => str_replace('@', '%40', self::getVar('REQUEST_URI', '/')),
                'base' => str_replace(array('\\', ' '), array('/', '%20'), dirname(self::getVar('SCRIPT_NAME'))),
                'method' => self::getMethod(),
                'referrer' => self::getVar('HTTP_REFERER'),
                'ip' => self::getVar('REMOTE_ADDR'),
                'ajax' => self::getVar('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest',
                'scheme' => self::getVar('SERVER_PROTOCOL', 'HTTP/1.1'),
                'userAgent' => self::getVar('HTTP_USER_AGENT'),
                'type' => self::getVar('CONTENT_TYPE'),
                'length' => self::getVar('CONTENT_LENGTH', 0),
                'query' => new Collection($_GET),
                'data' => new Collection($_POST),
                'cookies' => new Collection($_COOKIE),
                'files' => new Collection($_FILES),
                'secure' => self::getVar('HTTPS', 'off') != 'off',
                'accept' => self::getVar('HTTP_ACCEPT'),
                'proxyIp' => self::getProxyIpAddress()
            ];
        }
        $this->init($config);
    }

    /**
     * 初始化请求属性
     *
     * @author wangjian
     * @param array $properties
     */
    public function init($properties = array())
    {

        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }


        if ($this->base != '/' && strlen($this->base) > 0 && strpos($this->url, $this->base) === 0) {
            $this->url = substr($this->url, strlen($this->base));
        }

        if (empty($this->url)) {
            $this->url = '/';
        } else {
            $_GET += self::parseQuery($this->url);

            $this->query->setData($_GET);
        }


        if (strpos($this->type, 'application/json') === 0) {
            $body = $this->getBody();
            if ($body != '') {
                $data = json_decode($body, true);
                if ($data != null) {
                    $this->data->setData($data);
                }
            }
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

    /**
     * 获取请求方法
     *
     * @author wangjian
     * @return string
     */
    public static function getMethod()
    {
        $method = self::getVar('REQUEST_METHOD', 'GET');

        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        } elseif (isset($_REQUEST['_method'])) {
            $method = $_REQUEST['_method'];
        }

        return strtoupper($method);
    }

    /**
     * 获取代理IP
     *
     * @author wangjian
     * @return string
     */
    public static function getProxyIpAddress()
    {
        static $forwarded = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED'
        );

        $flags = \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE;

        foreach ($forwarded as $key) {
            if (array_key_exists($key, $_SERVER)) {
                sscanf($_SERVER[$key], '%[^,]', $ip);
                if (filter_var($ip, \FILTER_VALIDATE_IP, $flags) !== false) {
                    return $ip;
                }
            }
        }

        return '';
    }

    /**
     * 解析查询字符串
     *
     * @author wangjian
     * @param string $url
     * @return array
     */
    public static function parseQuery(string $url)
    {
        $params = [];
        $args = parse_url($url);
        if (isset($args['query'])) {
            parse_str($args['query'], $params);
        }

        return $params;
    }

    /**
     * 获取原始body体
     *
     * @author wangjian
     * @return false|string
     */
    public static function getBody()
    {
        static $body;
        if (!is_null($body)) {
            return $body;
        }
        $method = self::getMethod();
        if ('POST' == $method || 'PUT' == $method || 'PATCH' == $method) {
            $body = file_get_contents('php://input');
        }

        return $body;
    }

}