<?php

namespace Ben;

use Dotenv\Dotenv;
use InvalidArgumentException;
use Dotenv\Environment\DotenvFactory;
use Dotenv\Environment\Adapter\EnvConstAdapter;
use Dotenv\Environment\Adapter\PutenvAdapter;

class Config
{
    private static $config = [];

    protected static $default = 'default.php';

    /**
     * static instance
     *
     * @var null
     */
    private static $instance = null;


    private static $env = 'development';

    protected function __construct()
    {
    }

    /**
     * @param string $env
     */
    public static function loadEnv(string $dir, bool $overload = true)
    {
        $dotenv = Dotenv::create($dir, null);
        if ($overload) {
            $dotenv->overload();
        } else {
            $dotenv->safeLoad();
        }

        $env = new Env();
        $env->load();
        $environment = getenv('env');
        if ($environment) {
            static::$env = $environment;
        }

        var_dump($dotenv->getEnvironmentVariableNames());

        return true;
    }


    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        $instance = static::singleton();
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
        $instance = static::singleton();

        return $instance->getItem($key, $default);
    }

    /**
     * Set value
     *
     * @param mixed $key
     * @param mixed $value
     */
    protected static function set($key, $value = null)
    {
        $instance = static::singleton();

        return $instance->setItem($key, $value);
    }

    /**
     * @return static
     */
    public static function singleton()
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /*
     * @param string $key
     * @param mixed $value
     * @return mixed
     * */
    protected function getItem(string $key, $default = null)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('Ben\Config::get item must be string');
        }

        if (strpos($key, '.')) {
            $indexes = explode('.', $key);
            $temp = '';
            foreach ($indexes as $key => $index) {
                if ($key == 0) {
                    if (!isset(static::$config[$index])) {
                        return $default;
                    }

                    $temp = static::$config[$index];
                    continue;
                }

                if (!isset($temp[$index])) {
                    return $default;
                }

                $temp = $temp[$index];
            }

            return $temp;
        }

        if (isset(static::$config[$key])) {
            return static::$config[$key];
        }

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
            static::$config = array_merge_recursive(static::$config, $key);
            return true;
        }

        if (strpos($key, '.')) {
            $indexes = explode('.', $key);
            $ptr = null; // empty pointer
            $len = count($indexes);
            foreach ($indexes as $key => $index) {
                if ($key === 0) {
                    if (!isset(static::$config[$index])) {
                        static::$config[$index] = [];
                    }
                    $ptr = &static::$config[$index];
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

        static::$config[$key] = $val;

        return true;
    }


    /**
     * get all item
     *
     * @return array
     */
    public static function all()
    {
        return static::$config;
    }

    /**
     * load config from path
     *
     * @param string $path
     */
    public static function load($path, $default = 'default.php')
    {
        $path = rtrim($path, '/');
        $file = $path . '/' . $default;
        $config = [];
        if (file_exists($file)) {
            $data = include $file;
            if (is_array($data) && !empty($data)) {
                $config = $data;
            }
        }

        $envDir = $config['env']['dir'] ?? __DIR__;
        $overload = $config['env']['overload'] ?? false;
        self::loadEnv($envDir, $overload);
        $file = $path . '/' . static::$env . '.php';
        if (file_exists($file)) {
            $data = include $file;
            if (is_array($data) && !empty($data)) {
                $config = array_merge($config, $data);
            }
        }

        if (!isset($config[Env::$envname])) {
            $config[Env::$envname] = static::$env;
        }

        static::set($config);
    }

}
