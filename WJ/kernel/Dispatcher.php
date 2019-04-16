<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: wangjian
 * Date: 2019/4/16
 * Time: 22:06
 */

namespace WJ\kernel;


class Dispatcher
{
    /**
     * 映射的事件
     *
     * @var array
     */
    protected $events = [];

    /**
     * 方法过滤
     *
     * @var array
     */
    protected $filters = [];

    /**
     * 分配一个回调给一个事件
     *
     * @param string $name
     * @param callback $callback
     * @author wangjian
     */
    public function set(string $name, callback $callback)
    {
        $this->events[$name] = $callback;
    }

    /**
     * 获取一个事件被分配的回调
     *
     * @param string $name
     * @return mixed|null
     * @author wangjian
     */
    public function get(string $name)
    {
        return isset($this->events[$name]) ? $this->events[$name] : null;
    }

    /**
     * 检查一个事件是否设置回调
     *
     * @param string $name
     * @return bool
     * @author wangjian
     */
    public function has(string $name)
    {
        return isset($this->events[$name]);
    }

    /**
     * 清除一个事件或全部事件回调
     *
     * @param string|null $name
     * @author wangjian
     */
    public function clear(string $name = null)
    {
        if ($name !== null) {
            unset($this->events[$name]);
            unset($this->filters[$name]);
        } else {
            $this->events = [];
            $this->filters = [];
        }
    }

    /**
     * 挂钩一个回调到事件
     *
     * @param string $name
     * @param string $type
     * @param callable $callback
     * @author wangjian
     */
    public function hook(string $name, string $type, callable $callback)
    {
        $this->filters[$name][$type][] = $callback;
    }

    /**
     * 重置一个对象到初始化状态
     *
     * @author wangjian
     */
    public function reset()
    {
        $this->events = [];
        $this->filters = [];
    }

    /**
     * 调用一个方法
     *
     * @param mixed $func
     * @param array $params
     * @return mixed
     * @author wangjian
     */
    public static function invokeMethod($func, array &$params = [])
    {
        list($class, $method) = $func;
        $object = is_object($class);

        switch (count($params)) {
            case 0:
                return ($object) ? $object->$method() : $class::$method();
            case 1:
                return ($object) ? $object->$method($params[0]) : $class::$method($params[0]);
            case 2:
                return ($object) ?
                    $object->$method($params[0], $params[1]) :
                    $object::$method($params[0], $params[1]);
            case 3:
                return ($object) ?
                    $object->$method($params[0], $params[1], $params[2]) :
                    $object::$method($params[0], $params[1], $params[2]);
            case 4:
                return ($object) ?
                    $object->$method($params[0], $params[1], $params[2], $params[3]) :
                    $object::$method($params[0], $params[1], $params[2], $params[3]);
            case 5:
                return ($object) ?
                    $object->$method($params[0], $params[1], $params[2], $params[3], $params[4]) :
                    $object::$method($params[0], $params[1], $params[2], $params[3], $params[4]);
            default:
                return call_user_func_array($func, $params);
        }

    }

    /**
     * 调用一个函数
     *
     * @param $func
     * @param array $params
     * @return mixed
     * @author wangjian
     */
    public static function callFunction($func, array &$params = [])
    {

        if (is_string($func) && strpos($func, '::') !== false) {   // 如果是类的静态方法
            return call_user_func_array($func, $params);
        }

        switch (count($params)) {
            case 0:
                return $func();
            case 1:
                return $func($params[0]);
            case 2:
                return $func($params[0], $params[1]);
            case 3:
                return $func($params[0], $params[1], $params[2]);
            case 4:
                return $func($params[0], $params[1], $params[2], $params[3]);
            case 5:
                return $func($params[0], $params[1], $params[2], $params[3], $params[4]);
            default:
                return call_user_func_array($func, $params);
        }
    }

    /**
     *  执行一个回调
     *
     * @param $callback
     * @param array $params
     * @return mixed
     * @throws \Exception
     * @author wangjian
     */
    public static function execute($callback, array &$params = [])
    {
        if (is_callable($callback)) {
            return is_array($callback) ? self::invokeMethod($callback, $params) : self::callFunction($callback, $params);
        } else {
            throw new \Exception('invalid  callback specified');
        }

    }

    /**
     * 执行一个方法链过滤器
     *
     * @param array $filters
     * @param array $params
     * @param $output
     * @throws \Exception
     * @author wangjian
     */
    public function filter(array $filters, array &$params, &$output)
    {
        $argsArray = [&$params, &$output];
        foreach ($filters as $callback) {
            $continue = $this->execute($callback, $argsArray);
            if (false === $continue) {
                break;
            }
        }
    }

    /**
     * 调度一个事件[过滤前 ，过滤后]
     *
     * @param string $name
     * @param array $params
     * @return string
     * @throws \Exception
     * @author wangjian
     */
    public function run(string $name, array $params = [])
    {
        $outputString = '';

        // 运行pre-filter
        if (!empty($this->filters[$name]['before'])) {
            $this->filter($this->filters[$name]['before'], $params, $outputString);
        }

        // 运行请求的方法
        $outpuString = $this->execute($this->get($name), $params);

        // 运行 post-filters
        if (!empty($this->filters[$name]['after'])) {
            $this->filter($this->filters[$name]['after'], $params, $output);
        }

        return $outputString;
    }


}