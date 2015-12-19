<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-18 15:14:33
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-18 18:43:19
 */
namespace LogParser\Console;
use Symfony\Component\Console\Application;
use LogParser\Console\Command;

class LogParserApplication extends Application {
  public function __construct() {
    parent::__construct('LogParser para Chaordic, por Ian Garcez');

    $this->addCommands(array(
        new Command\TestCommand(),
        new Command\CreateDummyLogsCommand()
      ));
  }
}