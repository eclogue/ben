<?php

namespace Ben;

use InvalidArgumentException;

class Config
{
    private static $config = [];

    protected static $default = 'default.php';

    /**
     * self instance
     *
     * @var null
     */
    private static $_instance = null;


    public $env = 'develop';

    public function __construct()
    {
        $env = new Env();
        $env->load();
        $environment = getenv('env');
        if ($environment) {
            $this->env = $environment;
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
     * Get value by key
     *
     * @param string $key
     * @param mixed $default
     */
    public static function get($key, $default = null)
    {
        $instance = self::singleton();
        return $instance->getItem($key, $default);
    }

    /**
     * Set value
     *
     * @param mixed $key
     * @param mixed $value
     */
    public static function set($key, $value = null)
    {
        $instance = self::singleton();
        return $instance->setItem($key, $value);
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
     * @param string $key
     * @param mixed $value
     * @return mixed
     * */
    protected function getItem($key, $default = null)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('Ben\Config::get item must be string');
        }
        if (strpos($key, '.')) {
            $indexes = explode('.', $key);
            $temp = '';
            foreach ($indexes as $key => $index) {
                if ($key == 0) {
                    if (!isset(self::$config[$index])) {
                        return $default;
                    }
                    $temp = self::$config[$index];
                    continue;
                }
                if (!isset($temp[$index])) {
                    return $default;
                }
                $temp = $temp[$index];
            }

            return $temp;
        }
        if (isset(self::$config[$key]))
            return self::$config[$key];

        return $default;
    }


    /**
     * @param string $key
     * @param mixed $val
     * @return mixed
     */
    protected function setItem($key, $val = null)
    {
        if (is_array($key) && $val === null) {
            self::$config = array_merge_recursive(self::$config, $key);
            return true;
        }
        if (strpos($key, '.')) {
            $indexes = explode('.', $key);
            $ptr = null; // empty pointer
            $len = count($indexes);
            foreach ($indexes as $key => $index) {
                if ($key === 0) {
                    if (!isset(self::$config[$index])) {
                        self::$config[$index] = [];
                    }
                    $ptr = &self::$config[$index];
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

        self::$config[$key] = $val;

        return true;
    }


    /**
     * get all item
     *
     * @return array
     */
    public static function all()
    {
        return self::$config;
    }

    /**
     * load config from path
     *
     * @param string $path
     */
    public static function load($path, $default = 'default.php')
    {
        $instance = self::singleton();
        $path = rtrim($path, '/');
        $file = $path . '/' . $default;
        $config = [];
        if (file_exists($file)) {
            $data = include $file;
            if (is_array($data) && !empty($data)) {
                $config = $data;
            }
        }
        $file = $path . '/' . $instance->env . '.php';
        if (file_exists($file)) {
            $data = include $file;
            if (is_array($data) && !empty($data)) {
                $config = array_merge($config, $data);
            }
        }

        self::set($config);
    }

}
