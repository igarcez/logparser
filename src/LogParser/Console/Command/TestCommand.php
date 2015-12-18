<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-18 15:15:53
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-18 16:32:38
 */
namespace LogParser\Console\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use LogParser\Server\Server;
class TestCommand extends Command {
  protected function configure() {
    $this->setName("test")
         ->setDescription("test command");
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $server = new Server('192.168.0.42', 'vagrant', '/Users/ian/Development/Chaordic/LogParser/Servers/.vagrant/machines/static1/virtualbox/private_key');
    $output->writeln($server->execCommand('ls'));
  }
}