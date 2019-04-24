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
    protected $status=200;

    /**
     * @var array  头信息
     */
    protected $headers=[];

    /**
     * @var string 返回体
     */
    protected $body;
    /**
     * @var bool 是否发送
     */
    protected $isSent=false;


}