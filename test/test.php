<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/user.php';
session_start();
if (!isset($_SESSION['currentUser']) || $_SESSION['currentUser'] == NULL) {
  pg_close($_SESSION['psql']);
  session_destroy();
  header('Location: /');
  die();
} else {
  include_once $_SERVER['DOCUMENT_ROOT'].'/header.php'; ?>
  
<body class='container-fluid'>

  <nav class="navbar navbar-inverse">
      <div class="container-fluid">
        <div class="navbar-header">
          <a href='/lk.php' class="navbar-brand">ЛК ИК</a>
        </div>
        <ul class='nav navbar-nav'>
          <li class='dropdown active'>
            <a class='dropdown-toggle' data-toggle='dropdown' href='#'>Тестирование&nbsp;<span class='caret'></span></a>
            <ul class='dropdown-menu'>
              <li><a href='/test/'>Тесты</a></li>
              <li><a href='/test/reports.php'>Отчеты</a></li>
            </ul>
          </li>
          <li class='dropdown'>
          <a class='dropdown-toggle' data-toggle='dropdown' href='#'>Документы&nbsp;<span class='caret'></span></a>
          <ul class='dropdown-menu'>
            <li><a href='/docs/blank.php'>Бланки</a></li>

          </ul>
        </li>
          <?php
          if(in_array(6, $_SESSION['currentUser']->post))
            echo '<li><a href=\'/buh/\'>Бухгалтерия</a></li>';
          if(in_array(8, $_SESSION['currentUser']->post))
            echo '<li><a href=\'/settings/\'>Настройки</a></li>';
          ?>
        </ul>
        <ul class='nav navbar-nav navbar-right'>
          <li class='dropdown'>
            <a class='dropdown-toggle' data-toggle='dropdown' href='#'><?=$_SESSION['currentUser']->fio ?>&nbsp;<span class='caret'></span></a>
            <ul class='dropdown-menu'>
              <li><a href='/edit.php'>Редактировать</a></li>
              <li><a href='/logout.php'>Выход</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  
    <main class='container'>

<?php
  if(isset($_GET['tid'])) {
    $tid = (int)$_GET['tid'];
    $uid = $_SESSION['currentUser']->id;

    $isLast = false;

    $questionSize;
    $currentQID;
    $qids = array();

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
      pg_query($_SESSION['psql'], 'delete from result where uid='.$uid.' and tid='.$tid.'; delete from incorrect where uid='.$uid.' and tid='.$tid.';');//clear result
      $test = pg_fetch_row(pg_query($_SESSION['psql'], 'select T.id, T.text, T.passball, T.vid from test T where T.id='.$tid.';'));
      $_SESSION['currentTest'] = $test;
      $result = pg_query($_SESSION['psql'], 'select Q.id from question Q where Q.tid='.$tid.' order by random();');
      while($row = pg_fetch_row($result)) {
        $qids[] = (int)$row[0];
      }
      $_SESSION['currentTestQIDs'] = $qids;
      $_SESSION['currentQuestionIndex'] = 0;
      $currentQID = $qids[0];
    } else if($_SERVER['REQUEST_METHOD'] === 'POST') {
      $answer = array_values($_POST);
      $idx = $_SESSION['currentQuestionIndex'];
      $qids = $_SESSION['currentTestQIDs'];

      $currentBall = 0.0;
      foreach($answer as $a) {
        $ans = (double)pg_fetch_row(pg_query($_SESSION['psql'], 'select A.ball from answer A where A.qid='.$qids[$idx].' and A.id='.$a.';'))[0];
        $currentBall += $ans;
      }
      if($currentBall <= 0.0 || count($answer) < 1)
        pg_query($_SESSION['psql'], 'insert into incorrect(uid,tid,qid) values ('.$uid.', '.$tid.', '.$qids[$idx].');');

      $r1 = (double)pg_fetch_row(pg_query($_SESSION['psql'], 'select coalesce((select ball from result where tid='.$tid.' and uid='.$uid.'), -1000.0);'))[0];
      if($r1 === -1000.0)
        pg_query($_SESSION['psql'], 'insert into result(uid,tid,ball) values ('.$uid.', '.$tid.', '.$currentBall.');');
      else
        pg_query($_SESSION['psql'], 'update result set ball='.($r1 + $currentBall).' where uid='.$uid.' and tid='.$tid.';');

      if($idx + 1 === count($qids))
        $isLast = true;
      else {
        $idx++;
        $_SESSION['currentQuestionIndex'] = $idx;
        $currentQID = $qids[$idx];
      }
    }

    if( !$isLast) {
      $questionSize = count($qids);
      $question = pg_fetch_row(pg_query($_SESSION['psql'], 'select Q.id, Q.text, Q.type from question Q where Q.id='.$currentQID.';'));
      $answers = pg_query($_SESSION['psql'], 'select A.id, A.text from answer A where A.qid='.$currentQID.' order by random();');
      echo '<form class=\'panel panel-primary vertical-center modal-dialog modal-lg\' method=\'POST\'>';
      echo '<div class=\'panel-heading\' style=\'font-size:15px;\'><b>'.$question[1].'</b></div>';
      echo '<div class=\'panel-body\' style=\'padding:15px;\'>';
      $i = 0;
      while($a = pg_fetch_row($answers)) {
        echo '<div class=\''.((int)$question[2] === 1 ? 'radio' : 'checkbox').'\'><label style=\'font-size: 18px;\'><input type=\''.((int)$question[2] === 1 ? 'radio' : 'checkbox').
           '\' name=\''.((int)$question[2] === 1 ? '0' : $i++).'\' value=\''.$a[0].'\'/>&nbsp;'.$a[1].'</label></div><br>';
      }
      echo '</div><div class=\'panel-footer\'>';
      echo 'Вопрос №'.($_SESSION['currentQuestionIndex'] + 1).' из '.$questionSize;
      echo '<div class=\'btn-group\' style=\'float:right\'>';
      if(($_SESSION['currentQuestionIndex'] + 1) === $questionSize)
        echo '<input type=\'submit\' class=\'btn btn-success\' value=\'Завершить тест\'/>';
      else
        echo '<input type=\'submit\' class=\'btn btn-success\' value=\'Далее\'/>';
      echo '</div></div></form>';
    } else { //Результат
    ?>
      <div class='panel panel-success vertical-center modal-dialog modal-lg'>
      <div class='panel-heading' style='font-size:15px;'><b>Результат тестирования</b></div>
      <div class='panel-body' style='font-size:20px;'>
    <?php
      $balls = (double)pg_fetch_row(pg_query($_SESSION['psql'], 'select R.ball from result R where R.tid='.$tid.' and R.uid='.$uid.';'))[0];
      $test = $_SESSION['currentTest'];
      $minBall = (double)$test[2];
      echo 'Тестирование завершено.<br>Ваше количество баллов: <b>'.($balls < 0.0 ? '0' : (int)($balls * 10)).'</b><br>Минимальное необходимое количество баллов: <b>'.(int)($minBall * 10).'</b><br>';
      echo 'Результат: <b style=\'color:'.($balls >= $minBall ? 'lime' : 'red').';\'>Тест '.($balls >= $minBall ? 'сдан' : 'не сдан').'</b>';

      $zz = pg_query($_SESSION['psql'], 'select Q.text from question Q, incorrect I where I.qid=Q.id and I.tid=Q.tid and I.uid='.$uid.' and I.tid='.$tid.';');
      if(pg_num_rows($zz) > 0) {
        echo '<hr><div class=\'panel panel-danger modal-dialog modal-md\'>';
        echo '<div class=\'panel-heading\'><b>Неправильно даны ответы на следущие вопросы:</b></div>';
        echo '<div class=\'list-group\'>';
        while($z = pg_fetch_row($zz)) {
          echo '<div class=\'list-group-item\'>'.$z[0].'</div>';
        }
        echo "</div></div>";
      }

      unset($_SESSION['currentTest']);
      unset($_SESSION['currentTestQIDs']);
      unset($_SESSION['currentQuestionIndex']);
    ?>
    </div>

<?php
    }
} else {
    header('Location: tests.php');
    die();
}
include_once $_SERVER['DOCUMENT_ROOT'].'/footer.php'; } ?>
