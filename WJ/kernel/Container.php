<?php
declare(strict_types=1);

namespace WJ\kernel;
/**
 * Created by PhpStorm.
 * User: wangjian
 * Date: 2019/4/14
 * Time: 8:58
 */
class Container
{

    protected $classes = [];

    protected $instances = [];

    protected static $dirs = [];

    /**
     * 注册一个类
     *
     * @param string $name 注册名
     * @param string|callable $class 类名或一个返回实例化的类
     * @param array $params 类初始化参数
     * @param callable|null $callback 对象实例化后去调用的回调
     * @author wangjian
     */
    public function register(string $name, $class, array $params = [], callable $callback = null)
    {
        unset($this->instances[$name]);
        $this->classes[$name] = [$class, $params, $callback];
    }

    /**
     * 注销一个类
     *
     * @param string $name
     * @author wangjian
     */
    public function unregister(string $name)
    {
        unset($this->classes[$name]);
    }

    /**
     * 获取类的一个实例
     *
     * @param string $name 实例名
     * @return object|null  类实例
     * @author wangjian
     */
    public function getInstance(string $name)
    {

        return isset($this->instances[$name]) ? $this->instances[$name] : null;
    }

    /**
     * 获取一个类的实例
     *
     * @param $class
     * @param array $params
     * @return mixed|object
     * @throws \ReflectionException
     * @author wangjian
     */
    public function newInstance($class, array $params = [])
    {
        if (is_callable($class)) {
            return call_user_func_array($class, $params);
        }

        switch (count($params)) {
            case 0:
                return new $class();
            case 1:
                return new $class($params[0]);
            case 2:
                return new $class($params[0], $params[1]);
            case 3:
                return new $class($params[0], $params[1], $params[2]);
            case 4:
                return new $class($params[0], $params[1], $params[2], $params[3]);
            case 5:
                return new $class($params[0], $params[1], $params[2], $params[3], $params[4]);
            default:
                try {
                    $refClass = new \ReflectionClass($class);
                    return $refClass->newInstanceArgs($params);
                } catch (\ReflectionException $e) {
                    throw new \ReflectionException("不能实例化{$class}", 0, $e);
                }
        }
    }

    /**
     *  获取一个类的信息
     *
     * @param string $name
     * @return mixed|null
     * @author wangjian
     */
    public function get(string $name)
    {
        return isset($this->classes[$name]) ? $this->classes[$name] : null;
    }

    /**
     * 重置对象到初始化状态
     *
     * @author wangjian
     */
    public function reset()
    {
        $this->classes = [];
        $this->instances = [];
    }

    /**
     * 装载一个注册类的实例
     *
     * @param string $name
     * @param bool $shared
     * @return mixed|object|null
     * @throws \ReflectionException
     * @author wangjian
     */
    public function load(string $name, bool $shared = true)
    {
        $object = null;
        if (isset($this->classes[$name])) {
            list($class, $params, $callback) = $this->classes[$name];
            $existBool = isset($this->instances[$name]);

            if ($shared) {
                $object = ($existBool) ? $this->getInstance() : $this->newInstance($class, $params);
                if (!$existBool) {
                    $this->instances[$name] = $object;
                }
            } else {
                $object = $this->newInstance($class, $params);
            }

            if ($callback && (!$shared || !$existBool)) {
                $refObjectArray = [&$object];
                call_user_func_array($callback, $refObjectArray);
            }

        }

        return $object;
    }


}