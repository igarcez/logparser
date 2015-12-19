<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-18 15:15:53
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-19 12:34:53
 */
namespace LogParser\Console\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use LogParser\Server\Server;
class TestCommand extends AbstractCommand {
  protected function configure() {
    $this->setName("test")
         ->setDescription("test command");
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    foreach ($this->servers as $server) {
      $server = new Server($server->host, $server->user, $server->key_file, $server->log_path);
      $path = $server->getLogPath();
      $output->writeln($server->execCommand("cat $path/access.log"));
    }
  }
}