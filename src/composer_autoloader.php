<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-18 15:09:32
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-18 15:10:34
 */

return function () {
    $files = array(
      __DIR__ . '/../vendor/autoload.php', // stand-alone package
    );
    foreach ($files as $file) {
        if (is_file($file)) {
            require_once $file;
            return true;
        }
    }
    return false;
};