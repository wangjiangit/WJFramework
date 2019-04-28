<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: wangjian
 * Date: 2019/4/24
 * Time: 10:34
 */

namespace WJ\network;


class Response
{
    /**
     * @var int  状态码
     */
    protected $status = 200;

    /**
     * @var array  头信息
     */
    protected $headers = [];

    /**
     * @var string 返回体
     */
    protected $body;

    /**
     * @var bool 是否发送
     */
    protected $isSent = false;

    /**
     * @var array HTTP 状态码描述
     */
    public static $codes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',

        226 => 'IM Used',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',

        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',

        426 => 'Upgrade Required',

        428 => 'Precondition Required',
        429 => 'Too Many Requests',

        431 => 'Request Header Fields Too Large',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',

        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    ];

    /**
     * 设置响应状态码
     *
     * @param int $code 状态码
     * @return object|int $this实例
     * @throws \Exception 异常
     */
    public function status(int $code = null)
    {
        if ($code === null) {
            return $this->status;
        }

        if (array_key_exists($code, self::$codes)) {
            $this->status = $code;
        } else {
            throw new \Exception('Invalid status code.');
        }

        return $this;
    }

    /**
     * 添加头信息到响应
     *
     * @param string|array $name 头名或头名和头值数组
     * @param string $value 头值
     * @return $this
     * @author wangjian
     */
    public function header($name, string $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->headers[$k] = $v;
            }
        } else {
            $this->headers[$name] = $value;
        }

        return $this;
    }

    /**
     * 返回头信息
     *
     * @return array
     * @author wangjian
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * 写入内容到响应体
     *
     * @param string $body
     * @return $this
     * @author wangjian
     */
    public function write(string $body)
    {
        $this->body .= $body;

        return $this;
    }

    /**
     * 清空响应体
     *
     * @return $this
     * @author wangjian
     */
    public function clear()
    {
        $this->status = 200;
        $this->headers = [];
        $this->body = '';

        return $this;
    }

    /**
     * 设置HTTP缓存
     *
     * @param $expires
     * @return $this
     * @author wangjian
     */
    public function cache($expires)
    {
        if (false == $expires) {
            $this->headers['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
            $this->headers['Cache-Control'] = [
                'no-store, no-cache, must-revalidate',
                'post-check=0, pre-check=0',
                'max-age=0'
            ];
            $this->headers['Pragma'] = 'no-cache';
        } else {
            $expires = is_int($expires) ? $expires : strtotime($expires);
            $this->headers['Expires'] = gmdate('D, d M Y H:i:s', $expires);
            $this->headers['Cache-Control'] = 'max-age=' . ($expires - time());
            if (isset($this->headers['Pragma']) && ('no-cache' == $this->headers['Pragma'])) {
                unset($this->headers['Pragma']);
            }
        }

        return $this;
    }

    /**
     * 获取响应体长度
     *
     * @author wangjian
     * @return int
     */
    public function getContentLength()
    {
        return extension_loaded('mbstring') ? mb_strlen($this->body, 'latin1') : strlen($this->body);
    }

    /**
     * 响应是否发送
     *
     * @author wangjian
     * @return bool
     */
    public function sent()
    {
        return $this->isSent;
    }

    /**
     * 发送头
     *
     * @author wangjian
     * @return $this
     */
    public function sendHeaders()
    {
        // 发送状态码头
        if (false !== strpos(PHP_SAPI, 'cgi')) {  // PHP_SAPI 等价于 php_sapi_name()
            header(sprintf('Status: %d %s', $this->status, self::$codes[$this->status]), true);
        } else {
            header(
                sprintf(
                    '%s %d %s',
                    (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1'),
                    $this->status,
                    self::$codes[$this->status]),
                true,
                $this->status
            );
        }

        // 发送其他的信息头
        foreach ($this->headers as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    header($key . ': ' . $val, false);
                }

            } else {
                header($key . ':' . $value);
            }
        }

        // 发送 Content-length 头
        $length = $this->getContentLength();
        if ($length > 0) {
            header('Content-Length:' . $length);
        }

        return $this;
    }

    /**
     * 发送响应
     *
     * @author wangjian
     */
    public function send()
    {
        if (ob_get_length() > 0) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            $this->sendHeaders();
        }

        echo $this->body;

        $this->isSent = true;
    }


}