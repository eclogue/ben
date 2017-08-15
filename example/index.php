<?php
/**
 * @license MIT
 * @copyright Copyright (c) 2017
 * @author: bugbear
 * @date: 2017/7/13
 * @time: 下午7:42
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use Ben\Config;

Config::load(dirname(__DIR__) . '/example');


$all = Config::all();

var_dump($all);