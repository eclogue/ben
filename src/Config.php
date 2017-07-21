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
     * Get value by key
     *
     * @param string $key
     * @param mixed $default
     */
    public static function get($key, $default = null)
    {
        $instance = self::singleton();
        $instance->getItem($key, $default);
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
        $instance->setItem($key, $value);
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
    protected function setItem($key, $val = null)
    {
        if (is_array($key) && $val === null) {
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
