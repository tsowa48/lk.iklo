<?php
  if(!isset($_SESSION['psql'])) {
    header('Location: /test/reports.php');
    die();
  }
  echo '<div class=\'panel panel-primary modal-dialog modal-lg\'>';
  echo '<div class=\'panel-heading\' style=\'font-size:15px;text-align:center;\'>';
  if(isset($_GET['tid'])) {
    $tid = (int)$_GET['tid'];
    $t = pg_fetch_row(pg_query($_SESSION['psql'], 'select T.text, V.name from test T, vote V where V.year='.date('Y').' and V.id=T.vid and T.id='.$tid.';'));
    echo $t[0].'<br>('.$t[1].')</div>';
    $result = pg_query($_SESSION['psql'], 'select U.id, U.fio, K.name from klass G, komission K, pu PU, post P, users U left join result R on R.uid=U.id and R.tid='.$tid.' where R.uid is null and G.tid='.$tid.' and U.kid=K.id and U.id=PU.uid and P.id=PU.pid and ((G.kid=K.id and P.type=0) or G.uid=U.id or G.pid=PU.pid);');
    if(pg_num_rows($result) > 0) {
      echo '<table class=\'table table-bordered table-condensed list-group\'>';
      echo '<tr><th>Ф.И.О.</th><th>Комиссия</th></tr>';
      while($row = pg_fetch_row($result)) {
        echo '<tr><td>'.$row[1].'</td><td>'.$row[2].'</td></tr>';
      }
      echo '</table>';
    } else {
      echo '<div class=\'list-group\'><div class=\'list-group-item text-center\'>Список пуст</div></div>';
    }
  } else { // Список тем тестов
    $tests = pg_query($_SESSION['psql'], 'select T.id, T.text, V.name from test T, vote V, klass K where V.year='.date('Y').' and T.vid=V.id and K.tid=T.id and (V.day+'.$daysAfterVote.') > '.(date('z') + 1).' group by 1,2,3 order by V.name, T.text;');
    echo '<b>Тема для отчета:</b></div>';
    echo '<div class=\'list-group\'>';
    while($t = pg_fetch_row($tests)) {
      echo '<a href=\'?id='.$rid.'&tid='.$t[0].'\' class=\'list-group-item list-group-item-action\'>'.$t[1].'<br><label class=\'label label-primary\'>'.$t[2].'</label></a>';
    }
    echo '</div>';
  }
  echo '</div>';
?>
