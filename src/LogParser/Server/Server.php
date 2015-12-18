<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-18 15:50:01
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-18 20:40:10
 */

namespace LogParser\Server;

class Server {
  protected $host;
  protected $username;
  protected $keyfile;
  protected $ssh_options;
  protected $output;
  protected $error;

  public function __construct($host, $username, $keyfile, $ssh_options) {
    $this->host = $host;
    $this->username = $username;
    $this->ssh_options = $ssh_options;
    if($keyfile[0] != '/')
      $this->keyfile = __DIR__ . "/../../../" . $keyfile;
    else
      $this->keyfile = $keyfile;
  }

  private function connectionString() {
    $connection_string = $this->username . "@" . $this->host;
    $connection_string .= " -i " . $this->keyfile;
    foreach ($this->ssh_options as $option_name => $option_value) {
      $connection_string .= " -o " . $option_name . "=" . $option_value;
    }
    return $connection_string;
  }

  public function execCommand($command) {
    $cmd = "ssh " . $this->connectionString() . " " . $command . " 2>&1";
    $this->output['command'] = $cmd;
    exec($cmd, $this->output, $this->error);
    if($this->error)
      throw new \Exception ("\nError sshing: ".print_r($this->output, true));

    return $this->output;
  }
}