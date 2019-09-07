<?php
session_start();
if(isset($_SESSION['psql'])) {
  if(!is_int($_SESSION['psql']))
    pg_close($_SESSION['psql']);
  unset($_SESSION['psql']);
}

if(isset($_SESSION['currentUser']))
  unset($_SESSION['currentUser']);
session_destroy();
header("Location: /");
die();
?>
