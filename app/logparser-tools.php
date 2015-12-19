<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-18 15:08:21
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-19 11:52:18
 */
set_time_limit(0);
date_default_timezone_set("America/Sao_Paulo");
$autoloader = require __DIR__ . '/../src/composer_autoloader.php';
if (!$autoloader()) {
    die(
      'É preciso instalar as dependências, execute:' . PHP_EOL .
      'curl -sS https://getcomposer.org/installer | php' . PHP_EOL .
      'php composer.phar install' . PHP_EOL
    );
}
return new LogParser\Console\LogParserApplication();