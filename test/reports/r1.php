<?php
  if(!isset($_SESSION['psql'])) {
    header('Location: /test/reports.php');
    die();
  }
  echo '<div class=\'panel panel-primary modal-dialog modal-lg\'>';
  echo '<div class=\'panel-heading\' style=\'font-size:15px;text-align:center;\'>';
  if(isset($_GET['tid'])) {
    $rid = intval($_GET['id']);
    $tid = intval($_GET['tid']);
    $kid = (isset($_GET['kid']) && is_numeric($_GET['kid'])) ? intval($_GET['kid']) : $_SESSION['currentUser']->kid;
    $t = pg_fetch_row(pg_query($_SESSION['psql'], 'select T.text, V.name from test T, vote V where V.year='.date('Y').' and V.id=T.vid and T.id='.$tid.';'));
    echo $t[0].'<br>('.$t[1].')</div>';
    echo '<table class=\'table table-bordered table-striped table-condensed\'>';
    echo '<tr><th>Комиссия</th><th>Кол-во нижестоящих комиссий</th><th>Кол-во членов комиссии</th><th>Кол-во принявших участие</th><th>Кол-во сдавших</th></tr>';
    
    //$curIK = pg_fetch_row(pg_query($_SESSION['psql'], 'select K.id, K.name, (select count(E.id) from komission E where E.parent=K.id), (select count(U.id) from users U, komission E where E.id=U.kid and E.parent=K.id), (select count(R.uid) from result R, users U, komission E where E.parent=K.id and E.id=U.kid and U.id=R.uid and R.tid='.$tid.'), (select count(R.uid) from result R, users U, komission E, test T where E.parent=K.id and E.id=U.kid and U.id=R.uid and R.tid=T.id and R.ball>=T.passball and T.id='.$tid.') from komission K where K.id='.$_SESSION['currentUser']->kid.' group by 1,2 order by K.number;'));
    if($_SESSION['currentUser']->kid !== $kid) {
      $curIK = pg_fetch_row(pg_query($_SESSION['psql'], 'select K.id, K.name, (select count(E.id) from komission E where E.parent=K.id), (select count(U.id) from users U, pu PU, post P where K.id=U.kid and U.id=PU.uid and PU.pid=P.id and P.type=0), (select count(R.uid) from result R, users U where K.id=U.kid and U.id=R.uid and R.tid='.$tid.'), (select count(R.uid) from result R, users U, test T where K.id=U.kid and U.id=R.uid and R.tid=T.id and R.ball>=T.passball and T.id='.$tid.') from komission K where K.id='.$_SESSION['currentUser']->kid.' group by 1,2 order by K.number;'));
      echo '<tr><td style=\'cursor:pointer\' onclick=\'javascript:location.href="?id='.$rid.'&tid='.$tid.'&kid='.$curIK[0].'"\'>'.$curIK[1].'</td><td>'.$curIK[2].'</td><td>'.$curIK[3].'</td><td>'.$curIK[4].'</td><td>'.$curIK[5].'</td></tr>';
    }
    $curIK = pg_fetch_row(pg_query($_SESSION['psql'], 'select K.id, K.name, (select count(E.id) from komission E where E.parent=K.id), (select count(U.id) from users U, pu PU, post P where K.id=U.kid and U.id=PU.uid and PU.pid=P.id and P.type=0), (select count(R.uid) from result R, users U where K.id=U.kid and U.id=R.uid and R.tid='.$tid.'), (select count(R.uid) from result R, users U, test T where K.id=U.kid and U.id=R.uid and R.tid=T.id and R.ball>=T.passball and T.id='.$tid.') from komission K where K.id='.$kid.' group by 1,2 order by K.number;'));
    echo '<tr class=\'info\'><td style=\'cursor:pointer\' onclick=\'javascript:location.href="?id='.$rid.'&tid='.$tid.'&kid='.$curIK[0].'"\'>'.$curIK[1].'</td><td>'.$curIK[2].'</td><td>'.$curIK[3].'</td><td>'.$curIK[4].'</td><td>'.$curIK[5].'</td></tr>';

    //$result = pg_query($_SESSION['psql'], 'select K.id, K.name, (select count(E.id) from komission E where E.parent=K.id), (select count(U.id) from users U, komission E where E.id=U.kid and E.parent=K.id), (select count(R.uid) from result R, users U, komission E where E.parent=K.id and E.id=U.kid and U.id=R.uid and R.tid='.$tid.'), (select count(R.uid) from result R, users U, komission E, test T where E.parent=K.id and E.id=U.kid and U.id=R.uid and R.tid=T.id and R.ball>=T.passball and T.id='.$tid.') from komission K where K.parent='.$_SESSION['currentUser']->kid.' order by K.number;');
    $result = pg_query($_SESSION['psql'], 'select K.id, K.name, (select count(E.id) from komission E where E.parent=K.id), (select count(U.id) from users U, pu PU, post P where K.id=U.kid and U.id=PU.uid and PU.pid=P.id and P.type=0), (select count(R.uid) from result R, users U where K.id=U.kid and U.id=R.uid and R.tid='.$tid.'), (select count(R.uid) from result R, users U, test T where K.id=U.kid and U.id=R.uid and R.tid=T.id and R.ball>=T.passball and T.id='.$tid.') from komission K where K.parent='.$kid.' order by K.number;');
    $allparent = 0;
    $allMembers = 0;
    $allTookPart = 0;
    $allFinish = 0;
    while($row = pg_fetch_row($result)) {
      $allparent += (int)$row[2];
      $allMembers += (int)$row[3];
      $allTookPart += (int)$row[4];
      $allFinish += (int)$row[5];
      echo '<tr'.((int)$row[3]===(int)$row[4] && (int)$row[4]===(int)$row[5] ? ' class=\'success\'' : '').'><td style=\'cursor:pointer\' onclick=\'javascript:location.href="?id='.$rid.'&tid='.$tid.'&kid='.$row[0].'"\'>'.$row[1].'</td><td>'.$row[2].'</td><td>'.$row[3].'</td><td>'.$row[4].'</td><td>'.$row[5].'</td></tr>';
    }
    echo '<tr><th>Итого:</th><th>'.$allparent.'</th><th>'.$allMembers.'</th><th>'.$allTookPart.'</th><th>'.$allFinish.'</th></tr>';
    echo '</table>';
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
