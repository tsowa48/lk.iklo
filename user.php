<?php
class User {
  public $id;
  public $fio;
  public $kid;
  public $post;//array

  function __construct($_id = 0, $_fio = '-', $_kid = -1, $_post) {
    $this->id = (int)$_id;
    $this->fio = (string)$_fio;
    $this->kid = (int)$_kid;
    $this->post = $_post;
  }

  static function login($login, $password) {
    $result = pg_query($_SESSION['psql'], 'select U.id, U.fio, U.kid from users U where U.login=\''.$login.'\' and U.password=\''.$password.'\';');
    if(pg_num_rows($result) !== 1)
      return NULL;
    $row = pg_fetch_row($result);
    $posts = array();
    $res = pg_query($_SESSION['psql'], 'select P.id from post P, pu PU where PU.pid=P.id and PU.uid='.$row[0].';');
    while($r = pg_fetch_row($res)) {
      $posts[] = (int)$r[0];
    }

    return new User($row[0], $row[1], $row[2], $posts);
  }
}

?>
