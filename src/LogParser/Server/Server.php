<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-18 15:50:01
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-19 12:18:46
 */

namespace LogParser\Server;

use \phpseclib\Net\SSH2;
use \phpseclib\Crypt\RSA;

class Server {
  protected $host;
  protected $username;
  protected $keyfile;
  protected $log_path;
  protected $ssh_options;
  protected $output;
  protected $error;
  private $ssh;
  private $key;

  public function __construct($host, $username, $keyfile, $log_path ,$ssh_options) {
    $this->ssh = new SSH2($host);
    $this->key = new RSA();
    $this->key->loadKey(file_get_contents($keyfile));
    $this->host = $host;
    $this->username = $username;
    $this->ssh_options = $ssh_options;
    $this->log_path = $log_path;
    if($keyfile[0] != '/')
      $this->keyfile = __DIR__ . "/../../../" . $keyfile;
    else
      $this->keyfile = $keyfile;

    $this->connect();
  }

  private function connect() {
    if(!$this->ssh->login($this->username, $this->key))
      throw new Exception("login failed");
  }

  public function getLogPath() {
    return $this->log_path;
  }

  private function connectionString() {
    $connection_string = $this->username . "@" . $this->host;
    $connection_string .= " -i " . $this->keyfile;
    foreach ($this->ssh_options as $option_name => $option_value) {
      $connection_string .= " -o " . $option_name . "=" . $option_value;
    }
    return $connection_string;
  }

  private function output($str) {
    return $str;
  }

  public function execCommand($command) {
    return $this->ssh->exec($command);
    // $cmd = "ssh " . $this->connectionString() . " " . $command . " 2>&1";
    // $this->output['command'] = $cmd;
    // exec($cmd, $this->output, $this->error);
    // if($this->error)
    //   throw new \Exception ("\nError sshing: ".print_r($this->output, true));

    // return $this->output;
  }
}