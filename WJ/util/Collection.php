<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: wangjian
 * Date: 2019/4/17
 * Time: 14:40
 */

namespace WJ\util;

class Collection implements \ArrayAccess, \Iterator, \Countable
{

    /**
     *  集合数据
     * @var array
     */
    private $_data;

    /**
     * Collection constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->_data = $data;
    }

    /**
     * 获得一个项目
     *
     * @author wangjian
     * @param string $key
     * @return mixed|null
     */
    public function __get(string $key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * 设置一个项目
     *
     * @author wangjian
     * @param string $key
     * @param $value
     */
    public function __set(string $key, $value)
    {
        $this->_data[$key] = $value;
    }

    /**
     * 检查一个项目是否存在
     *
     * @author wangjian
     * @param string $key
     * @return bool
     */
    public function __isset(string $key)
    {
        return isset($this->_data[$key]);
    }

    /**
     * 删除一个项目
     *
     * @author wangjian
     * @param string $key
     */
    public function __unset(string $key)
    {
        unset($this->_data[$key]);
    }

    /**
     * 通过offset得到项目
     *
     * @author wangjian
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }

    /**
     * 在offset设置一个项目
     *
     * @author wangjian
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_data[] = $value;
        } else {
            $this->_data[$offset] = $value;
        }
    }

    /**
     * 检查在offset位置的项目是否存在
     *
     * @author wangjian
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    /**
     * 删除offset位置的项目
     *
     * @author wangjian
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    /**
     * 复位集合
     *
     * @author wangjian
     */
    public function rewind()
    {
        reset($this->_data);
    }

    /**
     * 获得当前指针指向的项目
     *
     * @author wangjian
     * @return mixed
     */
    public function current()
    {
        return current($this->_data);
    }

    /**
     * 获得当前指针指向的键
     *
     * @author wangjian
     * @return mixed|void
     */
    public function key()
    {
        return key($this->_data);
    }

    /**
     * 获得当前指针指向项目的下一个项目
     *
     * @author wangjian
     * @return mixed|void
     */
    public function next()
    {
        return next($this->_data);
    }

    /**
     * 检查是否当前指针指向的键是否有效
     *
     * @author wangjian
     * @return bool
     */
    public function valid()
    {
        $key = key($this->_data);
        return (null !== $key && false !== $key);
    }

    /**
     * 获得集合项目的数量
     *
     * @author wangjian
     * @return int
     */
    public function count()
    {
        return count($this->_data);
    }

    /**
     * 获得集合所有键
     *
     * @author wangjian
     * @return array
     */
    public function keys()
    {
        return array_keys($this->_data);
    }

    /**
     * 获得集合数据
     *
     * @author wangjian
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * 设置集合数据
     *
     * @author wangjian
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->_data = $data;
    }

    /**
     * 删除集合所有项目
     *
     * @author wangjian
     */
    public function clear()
    {
        $this->_data = [];
    }

}