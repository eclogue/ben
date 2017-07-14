<?php

namespace Ben;

use InvalidArgumentException;

class Config
{
    private static $_config = [];

    protected static $default = 'default.php';

    /**
     * self instance
     *
     * @var null
     */
    private static $_instance = null;

    /**
     * default env variable
     * @var string
     */
    private $_env = 'develop';


    public function __construct($opt = [])
    {
        $env = new Env($opt);
        $env->load();
        $environment = getenv('env');
        if ($environment) {
            $this->_env = $environment;
        }
    }


    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        $instance = self::singleton();
        if (is_callable([$instance, $method])) {
            return call_user_func_array([$instance, $method], $arguments);
        } else {
            throw new InvalidArgumentException('Call invalid method ' . $method . 'of Ben');
        }

    }

    /**
     * @return static
     */
    public static function singleton($opt =[])
    {
        if (self::$_instance === null) {
            self::$_instance = new static($opt);
        }

        return self::$_instance;
    }

    /*
     * @param mixed $key
     * @param mixed $value
     * @return mixed
     * */
    protected function get($key, $default = '')
    {
        if (strpos($key, '.')) {
            $indexes = explode('.', $key);
            $temp = '';
            foreach ($indexes as $key => $index) {
                if ($key == 0) {
                    if (!isset(self::$_config[$index])) {
                        return $default;
                    }
                    $temp = self::$_config[$index];
                    continue;
                }
                if (!isset($temp[$index])) {
                    return $default;
                }
                $temp = $temp[$index];
            }

            return $temp;
        }
        if (isset(self::$_config[$key]))
            return self::$_config[$key];

        return $default;
    }


    /**
     * @param string $key
     * @param mixed $val
     * @return mixed
     */
    protected function set($key, $val = '')
    {
        if (is_array($key) && $val === '') {
            self::$_config = array_merge_recursive(self::$_config, $key);
            return true;
        }
        if (strpos($key, '.')) {
            $indexes = explode('.', $key);
            $ptr = null; // empty pointer
            $len = count($indexes);
            foreach ($indexes as $key => $index) {
                if ($key === 0) {
                    if (!isset(self::$_config[$index])) {
                        self::$_config[$index] = [];
                    }
                    $ptr = &self::$_config[$index];
                    continue;
                }
                if ($key === $len - 1) {
                    $ptr[$index] = $val;
                    break;
                }
                if (!isset($ptr[$index])) {
                    $ptr[$index] = [];
                }

                $ptr = &$ptr[$index];
            }

            return true;
        }

        self::$_config[$key] = $val;

        return true;
    }


    /**
     * get all item
     *
     * @return array
     */
    protected function all()
    {
        return self::$_config;
    }

    /**
     * load config from path
     *
     * @param string $path
     */
    protected function load($path)
    {
        $default = static::$default;
        $path = rtrim($path, '/');
        $file = $path . '/' . $default;
        $config = [];
        if (file_exists($file)) {
            $data = include $file;
            if (is_array($data) && !empty($data)) {
                $config = $data;
            }
        }
        $file = $path . '/' . $this->_env . '.php';
        if (file_exists($file)) {
            $data = include $file;
            if (is_array($data) && !empty($data)) {
                $config = array_merge($config, $data);
            }
        }

        $this->set($config);
    }

}
