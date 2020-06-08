<?php
  $vid = (int)$_GET['v'];
  $uid = (int)$_GET['u'];
  $doy = (int)$_GET['d'];
  $psql = pg_pconnect('host=localhost port=5432 dbname=gas user=iklo password=48s000');
  $res = pg_query($psql, 'select S.start, S.finish from schedule S where S.vid!='.$vid.' and S.day='.$doy.' and S.uid='.$uid.';');
  $out = array();
  while($r = pg_fetch_row($res)) {
    $out[] = array('s' => (int)$r[0], 'f' => (int)$r[1]);
  }
  echo json_encode($out);
?>
