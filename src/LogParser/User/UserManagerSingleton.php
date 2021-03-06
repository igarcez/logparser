<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-19 13:10:27
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-20 14:57:55
 */

namespace LogParser\User;
use LogParser\JsonDB\JsonTable;

class UserManagerSingleton {
  private $users;
  private $db;
  private static $instance;

  const USER_FILE_LOCATION = "/../../../data/users.json";

  private function __construct() {
    $this->db = new JsonTable(__DIR__ . self::USER_FILE_LOCATION, true);
    $this->users = array();
    if ($this->db->selectAll()) {
      foreach($this->db->selectAll() as $user) {
        $this->users[$user['id']] = $user['cluster'];
      }
    }
  }

  static function getInstance() {
    if(null == self::$instance){
      self::$instance = new UserManagerSingleton();
    }
    return self::$instance;
  }

  public function getUsers() {
    return $this->users;
  }

  public function getCluster($user_id) {
    return $this->users[$user_id];
  }

  public function addUser($id, $cluster) {
    $this->users[$id] = $cluster;
  }

  public function addUsers($users) {
    foreach ($users as $user) {
      $this->users[$user['id']] = $user['cluster'];
    }
  }

  public function removeUser($id) {
    unset($this->users[$id]);
  }

  public function removeUsers($users) {
    foreach ($users as $user) {
      unset($this->users[$user]);
    }
  }

  public function writeUsers() {
    $this->db->deleteAll();
    foreach ($this->users as $id => $cluster) {
      $this->db->insert(array('id'=> $id, 'cluster' => $cluster));
    }
  }
}