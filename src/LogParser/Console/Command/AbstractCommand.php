<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-18 16:47:48
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-18 17:26:33
 */

namespace LogParser\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command {
  protected $servers;

  const SERVER_CONFIG = '/../../../../configs/server.json';

  public function __construct() {
    parent::__construct();
    $this->servers = json_decode(file_get_contents($this->getServerConfigFile()))->servers;
    if (!$this->servers)
      throw new \Exception("Servers not found, tried to load from: " . $this->getServerConfigFile(), 1);

  }

  private function getServerConfigFile () {
    return __DIR__ . self::SERVER_CONFIG;
  }
}