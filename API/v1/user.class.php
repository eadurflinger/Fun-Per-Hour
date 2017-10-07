<?php
class User {
  protected $uid = null, $loggedIn = false;
  public function __construct($id=null) {
    if($id) $this->loggedIn = true;
    $this->uid = $id;
 }
  public function __get($name) {
    return $this->$name;
  }
}
?>
