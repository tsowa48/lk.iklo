<?php
    //i;[UID];[DAY];[nVALUE]
    //s;[UID];[DAY];[nVALUE]
    //c;[UID];[fVALUE]
    //s - график работы
    //i - сведения
    //c - коэффициент
    $x = explode(';', $_GET['X']);
    $vid = $_GET['vid'];
    $query = '';
    switch ($x[0]) {
      case 's':
        //schedule(uid, vid, day, start, hours)
        $query = 'update schedule set finish='.$x[3].' where uid='.$x[1].' and vid='.$vid.' and day='.$x[2].';';
        break;
      case 'i':
        //schedule(uid, vid, day, hours, start)
        if((int)$x[3] < 0) {
          $query = 'delete from schedule where vid='.$vid.' and uid='.$x[1].' and day='.$x[2].';';
        } else {
          $query = 'update schedule set start='.$x[3].', finish=NULL where uid='.$x[1].' and vid='.$vid.' and day='.$x[2].';'.
                   'insert into schedule(uid, vid, day, start) select '.$x[1].','.$vid.','.$x[2].','.$x[3].
                   ' where not exists (select 1 from schedule where uid='.$x[1].' and vid='.$vid.' and day='.$x[2].');';
        }
        break;
      case 'r':
        //rate(uid, vid, coef)
        $query = 'update rate set coef='.str_replace(',', '.', sprintf("%1\$.8f", $x[2])).' where uid='.$x[1].' and vid='.$vid.';'.
                 'insert into rate(uid,vid,coef) select '.$x[1].','.$vid.','.str_replace(',', '.', sprintf("%1\$.8f", $x[2])).
                 ' where not exists (select 1 from rate where vid='.$vid.' and uid='.$x[1].');';
        break;
      case 'f':
        $query = 'update users set facial=\''.substr($x[2], 0, 20).'\' where id='.$x[1].';';
        break;
      default:
        return;
    }
    $psql = pg_connect('host=localhost port=5432 dbname=gas user=iklo password=48s000');
    pg_query($psql, $query);
    pg_close($psql);
    unset($psql);
?>
