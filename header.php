<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8" />
    <link rel='stylesheet' href='/css/bootstrap.css'/>
    <script src='/js/jquery.min.js'></script>
    <script src='/js/bootstrap.min.js'></script>
	  <title>Личный кабинет</title>
  </head>
  <?php
  if(!isset($_SESSION['psql']) || !$_SESSION['psql']) {
    $psql = pg_connect('host=localhost port=5432 dbname=gas user=iklo password=48s000');
    $_SESSION['psql'] = $psql;
  }
  ?>
