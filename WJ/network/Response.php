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
     * @author wangjian
     * @param string|array $name 头名或头名和头值数组
     * @param string $value 头值
     * @return $this
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
     * @author wangjian
     * @return array
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * 写入内容到响应体
     *
     * @author wangjian
     * @param string $body
     * @return $this
     */
    public function write(string $body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * 清空响应体
     *
     * @author wangjian
     * @return $this
     */
    public function clear()
    {
        $this->status=200;
        $this->headers=[];
        $this->body='';

        return $this;
    }


}