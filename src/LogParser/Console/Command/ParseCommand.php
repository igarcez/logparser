<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-19 14:25:46
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-20 15:31:09
 */

namespace LogParser\Console\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use LogParser\Server\Server;

class ParseCommand extends AbstractCommand {
  private $server_instances;
  private $output;
  protected function configure() {
    $this->server_instances = array();
    $this->setName("parse")
         ->setDescription("parse clusters after logs");
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    //TODO change to threads here
    $this->initializeServerInstances();
    $this->output = $output;
    foreach ($this->server_instances as $server) {
      foreach($this->getLinesByUsers($this->parseUserIdLines($server)) as $user_id => $array_of_lines) {
        $this->writeLinesToServer($user_id, $array_of_lines);
      }
    }
  }

  private function initializeServerInstances() {
    foreach ($this->servers as $server) {
      $server_instance = new Server($server->host, $server->user, $server->key_file, $server->log_path);
      $this->server_instances[$server->host] = $server_instance;
    }
  }

  private function parseUserIdLines($server_instance) {
    $log_path = $server_instance->getLogPath();
    return $server_instance->execCommand("cat $log_path/access.log | grep userid");
  }

  private function getLinesByUsers($lines) {
    $lines = explode("\n", $lines);
    $users = $this->user_manager->getUsers();
    $lines_by_user = array();
    foreach ($lines as $line) {
      // check if user exist on database, if it does, dont add it
      preg_match('/userid=([A-z0-9\-]*)"/', $line, $matches);
      $user_id = $matches[1];
      if(!isset($users[$user_id])){
        $array_keys = array_keys($this->servers_array);
        $this->user_manager->addUser($user_id, $this->servers_array[$array_keys[rand(0, count($this->servers_array) - 1 )]]['host']);
        $users = $this->user_manager->getUsers();
      }
      $lines_by_user[$user_id][] = $line;
    }

    $this->user_manager->writeUsers();
    return $lines_by_user;
  }

  private function writeLinesToServer($user_id, $lines) {
    $this->createUserLogFile($user_id);
    $cluster = $this->user_manager->getCluster($user_id);
    $server = $this->server_instances[$cluster];
    $log_path = $server->getLogPath();
    $user_file = "$log_path/users/$user_id";
    $first_line = $server->execCommand("sed -n 1p $user_file");

    $this->writeToFile($server, $lines, $first_line, $user_file);
  }

  private function extractLineDate($line) {
    preg_match('/([0-9]{2}\/[A-z]{3}\/[0-9]{4}\:[0-9]{2}\:[0-9]{2}\:[0-9]{2})/', $line, $matches);
    return $matches[1];
  }

  private function createUserLogFile($user_id) {
    $cluster = $this->user_manager->getCluster($user_id);
    $server = $this->server_instances[$cluster];
    $log_path = $server->getLogPath();
    return $server->execCommand("mkdir -p $log_path/users/ && touch $log_path/users/$user_id");
  }

  private function writeTofile($server, $lines, $first_line, $user_file) {
    foreach($lines as $line){
      if(!$first_line)
        $this->writeFirstLineToServer($server, $line, $user_file);
      else
        $this->writeLineToServer($server, $line, $user_file, $first_line);
      $server_host = $server->getHost();
      $this->output->writeln("added line $line to $server_host");
    }
  }

  private function writeFirstLineToServer($server, $line, $user_file) {
    $server->execCommand("echo '$line' >> $user_file");
  }

  private function writeLineToServer($server, $line, $user_file, $first_line) {
    if (!empty($server->execCommand("cat $user_file | fgrep '$line'"))) return;
    $line_date = $this->extractLineDate($line);
    $line_date = str_replace('/', '\/', $line_date);
    $total_of_lines = $server->execCommand("cat -n $user_file | tail -n1 | awk '{print $1;}'");
    $last_entry_line_number = $server->execCommand("cat -n $user_file | awk '$5<\"[$line_date\"' | tail -n1 | awk '{print $1;}'");
    if($total_of_lines == $last_entry_line_number)
      $this->writeFirstLineToServer($server, $line, $user_file);
    else
      $server->execCommand("sed -i '$last_entry_line_numberi $line' $user_file");
  }

}

// class ChildThread extends \Thread {

// }