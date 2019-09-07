<?php
  $uid = (int)$_GET['u'];
  $doy = (int)$_GET['d'];
  $vid = (int)$_GET['v'];
  $start = (int)$_GET['s'];
  $finish = (int)$_GET['f'];

  $query = 'update schedule set start='.$start.', finish='.$finish.' where uid='.$uid.' and vid='.$vid.' and day='.$doy.';'.
           'insert into schedule(uid, vid, day, start,finish) select '.$uid.','.$vid.','.$doy.','.$start.','.$finish.
           ' where not exists (select 1 from schedule where uid='.$uid.' and vid='.$vid.' and day='.$doy.');';
  $psql = pg_pconnect('host=localhost port=5432 dbname=gas user=iklo password=48s000');
  pg_query($psql, $query);
?>
