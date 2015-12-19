<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-18 15:50:01
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-19 12:34:46
 */

namespace LogParser\Server;

use \phpseclib\Net\SSH2;
use \phpseclib\Crypt\RSA;

class Server {
  private $host;
  private $username;
  private $keyfile;
  private $log_path;
  private $ssh;
  private $key;

  public function __construct($host, $username, $keyfile, $log_path) {
    $this->init($host, $username, $keyfile, $log_path);
    $this->connect();
  }

  private function init($host, $username, $keyfile, $log_path) {
    $this->host = $host;
    $this->username = $username;
    $this->log_path = $log_path;
    if($keyfile[0] != '/')
      $this->keyfile = __DIR__ . "/../../../" . $keyfile;
    else
      $this->keyfile = $keyfile;
  }

  private function connect() {
    $this->ssh = new SSH2($this->host);
    $this->key = new RSA();
    $this->key->loadKey(file_get_contents($this->keyfile));
    if(!$this->ssh->login($this->username, $this->key))
      throw new Exception("login failed");
  }

  public function getLogPath() {
    return $this->log_path;
  }

  private function output($str) {
    return $str;
  }

  public function execCommand($command) {
    return $this->ssh->exec($command);
  }
}