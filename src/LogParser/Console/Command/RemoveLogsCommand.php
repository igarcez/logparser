<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-19 12:28:04
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-19 12:51:06
 */

namespace LogParser\Console\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use LogParser\Server\Server;

class RemoveLogsCommand extends AbstractCommand {
  protected function configure() {
    $this->setName("remove-logs")
         ->setDescription("remove all logs from clusters");
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    foreach ($this->servers as $server) {
      $server_instance = new Server($server->host, $server->user, $server->key_file, $server->log_path);
      $server_instance->execCommand("rm $server->log_path/access.log");
      $output->writeln("log $server->log_path/access.log removed succefully");
    }
  }
}