<?php

namespace Ben;

class Env
{
    protected $argv = [
        'env' => 'e',
        'version' => 'v',
        'help' => 'h',
    ];



    public function __construct($params = [])
    {
        $this->argv = array_merge($this->argv, $params);
    }

    /**
     * parse params and compose opt
     *
     * @return array
     */
    private function parseOpt()
    {
        $opt = '';
        $longOpt = [];
        foreach ($this->argv as $long => $short) {
            if (!is_numeric($long)) {
                $longOpt[] = $long . ':';
            }
            $opt .= $short . ':';
        }

        return [$opt, $longOpt];
    }

    /**
     * get env from argv
     */
    public function load()
    {
        list($opt, $longOpt) = $this->parseOpt();
        $arguments = getopt($opt, $longOpt);
        foreach ($arguments as $opt => $value) {
            $env = 'env=' . $value;
            putenv($env);
        }
    }

    /**
     * add env
     *
     * @param string | array $setting
     */
    public function add($setting)
    {
        if (is_array($setting)) {
            foreach ($setting as $key => $value) {
                $env = $key . '=' . $value;
                putenv($env);
            }
        } else {
            putenv($setting);
        }
    }

    /**
     * remove env variable
     *
     * @param string $name
     */
    public function remove($name) {
        putenv($name);
    }
}