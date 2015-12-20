<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-18 16:37:31
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-20 16:41:32
 */

namespace LogParser\Console\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use LogParser\Server\Server;
class CreateDummyLogsCommand extends AbstractCommand {
  private $users;
  private $limit;

  protected function configure() {
    $this->users = 5;
    $this->limit = 1000;
    $this->setName("create-logs")
         ->setDescription("Create and populate logs on clusters")
         ->setDefinition(array(
            new InputOption('users', 'u', InputOption::VALUE_OPTIONAL, 'number of user ids to create', $start),
            new InputOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'limit number of log  entries on each cluster', $this->limit),
            new InputOption('old-users', null, InputOption::VALUE_NONE, 'use existing users')
          ));

  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->users = intval($input->getOption('users'));
    $this->limit = intval($input->getOption('limit'));

    $old_users = $this->user_manager->getUsers();

    if ($this->users < 1 || $this->limit < 1)
      throw new \InvalidArgumentException("users and limit need to be bigger than 1", 1);

    //TODO implement visual progress
    if($input->getOption('old-users') && count($old_users) && count($old_users) >= $this->users)
      $users_ids = $this->extractOldUsersIds($old_users, $this->users);
    else
      $users_ids = $this->generateUsersIds($this->users);


    $files = array("/meme.jpg", "/lolcats.jpg", "/other.jpg");

    $time = 100000;

    foreach ($this->servers as $server) {
      $server_instance = new Server($server->host, $server->user, $server->key_file, $server->log_path);
      for ($i=0; $i < $this->limit; $i++) {
        $time += rand(0,1000);
        $server_instance->execCommand("mkdir -p $server->log_path");
        $server_instance->execCommand("touch $server->log_path/access.log");
        $log_line = $this->generateLogLine($time, $users_ids, $files);
        $log_path = $server->log_path;
        $server_instance->execCommand("echo '$log_line' >> $log_path/access.log");
        $output->writeln("creating line: $log_line on server: $log_path/access.log");
      }
      $output->writeln("log for " . $server->host . " created!");
    }
  }

  private function generateLogLine($time, $users_ids, $files) {
    $log_line = "177.126.180.83 - - [" . date("d/M/Y:H:i:s", $time) . "]";
    $log_line .= " \"GET " . $files[rand(0, (count($files) - 1 ))] . " HTTP/1.1\" 200 2148 \"-\"";
    $log_line .= " \"userid=" . $users_ids[rand(0, (count($users_ids) - 1 ))] . "\"";
    return $log_line;
  }

  private function extractOldUsersIds($users_list, $number) {
    $users_ids = array();
    $keys = array_keys($users_list);
    for ($i=0; $i < $this->users; $i++) {
      $users_ids[] = $keys[$i];
    }
    return $users_ids;
  }

  private function generateUsersIds($users) {
    $users_ids = array();
    for ($i=0; $i < $this->users; $i++) {
      $users_ids[] = $this->generateUserId();
    }
    return $users_ids;
  }

  private function generateUserId() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $characters_length = strlen($characters);
    $random_string = '';
    for ($i = 0; $i < 31; $i++) {
      if($i == 8 || $i == 13 || $i == 17 || $i == 22){
        $random_string .= "-";
      }
      $random_string .= $characters[rand(0, $characters_length - 1)];
    }
    return $random_string;
  }
}