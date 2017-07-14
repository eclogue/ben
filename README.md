# ben
Ben is the name of my old buffalo.This project is designed to manage the configuration file.

#usage
  `use Ben\Config;`

  - `Config::set($key, $val = '')`  

    `$key` string or array. if $key use `.` connection, it will be considered as
    get value from multi-dimensional arrays by index. For example:
    ```
    Config::set('test.foo'); // look like $config['test']['foo'];
    ```
    if $key is array it will be merged

  - `Config::get($key)` 
    
    `$key` string. suport `.` search multi-dimensional arrays. For example:
    `Config::get('test.foo')`

  - load configure file from path


  ```
  Config::load('./config/develop.php');
  ```
  support auto load by command line arguments
  ```
  // set path
  Config::load('./config');
  // run script
  php demo.php --env staging  
  ```








