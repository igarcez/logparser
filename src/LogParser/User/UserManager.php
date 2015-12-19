<?php
/**
 * @Author: Ian Garcez <ian@onespace.com.br>
 * @Date:   2015-12-19 13:10:27
 * @Last Modified by:   Ian Garcez
 * @Last Modified time: 2015-12-19 14:19:56
 */

namespace LogParser\User;
use LogParser\JsonDB\JsonTable;

class UserManager {
  private $users;
  private $db;

  const USER_FILE_LOCATION = "/../../../data/users.json";

  public function __construct() {
    $this->db = new JsonTable(__DIR__ . self::USER_FILE_LOCATION, true);
    $this->users = array();
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
    var_dump($this->users);
    foreach ($this->users as $id => $cluster) {
      $this->db->insert(array('id'=> $id, 'cluster' => $cluster));
    }
  }
}