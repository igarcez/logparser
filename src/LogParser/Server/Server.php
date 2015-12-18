<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-18 15:50:01
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-18 16:32:20
 */

namespace LogParser\Server;

class Server {
  protected $host;
  protected $username;
  protected $keyfile;
  protected $ssh_options;
  protected $output;
  protected $error;

  public function __construct($host, $username, $keyfile) {
    $this->host = $host;
    $this->username = $username;
    $this->keyfile = $keyfile;
    $this->ssh_options = array(
      'UserKnownHostsFile' => '/dev/null',
      'StrictHostKeyChecking' => 'no',
      'PasswordAuthentication' => 'no',
      'IdentitiesOnly' => 'yes'
    );
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