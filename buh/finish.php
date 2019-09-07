<?php
require_once $_SERVER['DOCUMENT_ROOT']."/user.php";
session_start();
if (!isset($_SESSION['currentUser']) || !in_array(6, $_SESSION['currentUser']->post)) {
  if(!isset($_SESSION['psql']) || !$_SESSION['psql'])
    pg_close($_SESSION['psql']);
  session_destroy();
  header("Location: /");
  die();
} else if(!isset($_SESSION['vid'])) {
  header("Location: /buh/");
  die();
} else {
  include_once $_SERVER['DOCUMENT_ROOT']."/header.php";
  $vid = $_SESSION['vid'];
  $v = '';
  $vote = pg_query($_SESSION['psql'], 'select V.id, V.name, V.day from vote V where V.id='.$vid);
  if(pg_num_rows($vote)===0) {
    header('Location: /buh/');
    die();
  } else {
    $v = pg_fetch_row($vote);
  }
  $_count = (int)pg_fetch_row(pg_query($_SESSION['psql'], 'select count(K.id) from komission K where K.parent='.$_SESSION['currentUser']->kid.';'))[0];
?>
<body class='container-fluid'>

<nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <a href='/lk.php' class="navbar-brand">ЛК ИК</a>
      </div>
      <ul class='nav navbar-nav'>
        <li class='dropdown'>
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
        <li class='active'><a href='#'>Бухгалтерия</a></li>
        <?php
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

  <main class='container-fluid'>
  <a style='text-decoration: none;' href='/buh/' title='Выбрать другие выборы'> 
    <div class='panel-footer' style='font-size: 15px; text-align: center; background: pink;'><?=$v[1] ?></div>
  </a>

<ul class='nav nav-tabs'>
  <li><a href='vote.php?id=<?=$vid ?>'>Состав комиссии</a></li>
  <li><a href='work.php'>Часы работы (не трогать)</a></li>
  <li><a href='start.php'>Начало работы</a></li>
  <li class='active'><a href='#'>Окончание работы</a></li>
  <?php if($_count > 0)
      echo '<li><a href=\'predik.php\'>Вознаграждение ИК</a></li>';
  ?>
  <li><a href='reports.php'><b>Отчеты</b></a></li>
</ul>

<?php
  $komission = pg_query($_SESSION['psql'], 'select U.id, U.fio, P.name from users U, post P, pu PU where U.id=PU.uid and PU.pid=P.id and P.type=0 and (U.isstaffer is null or U.isstaffer=0) and U.kid='.$_SESSION['currentUser']->kid.' order by P.id, U.fio;');
  $header = '';
  $uids = array();
  while($k = pg_fetch_row($komission)) {
    $uids[] = (int)$k[0];
    $header .= '<th>'.$k[1].'</th>';
  }
  $calendar = pg_query($_SESSION['psql'], 'select doy from calendar where isholy > 0;');
  $cal = array();
  while($row = pg_fetch_row($calendar)) {
    $cal[] = (int)$row[0];
  }
  $schedules = pg_query($_SESSION['psql'], 'select uid, day, start, finish from schedule where vid='.$vid.';');
  $schedule = array();
  while($s = pg_fetch_row($schedules)) {
    $schedule[] = array('uid' => (int)$s[0], 'day' => (int)$s[1], 'start' => (int)$s[2], 'finish' => (int)$s[3]);
  }
  $dates = pg_fetch_row(pg_query($_SESSION['psql'], 'select first, last from kv where vid='.$vid.' and kid='.$_SESSION['currentUser']->kid.';'));
?>





<div class='tab-content'>
  <div class='tab-pane active container-fluid'>
    <table class='table table-striped table-bordered table-condensed'>
      <tr><th style='min-width:160px;'>День</th><?=$header ?></tr>
      <?php
      setlocale(LC_ALL, array('ru_RU', 'ru_RU.utf-8'));
      $firstInYear = strtotime('-1 day', strtotime(date('Y').'-01-01'));
      $first = (int)$dates[0];
      $last = (int)$dates[1];
      $curDay = $first;
      while($curDay < $last + 1) {
        $isHoly = in_array($curDay, $cal);
        echo '<tr'.($isHoly ? ' style=\'background-color:pink;\'' : '').'><td>'.strftime('%02d %b %Y г.', strtotime('+'.$curDay.' day', $firstInYear)).'</td>';
        for($u = 0; $u < count($uids); ++$u) {
          $isDayAdded = false;
          for($s = 0; $s < count($schedule); ++$s) {
            if($uids[$u] === $schedule[$s]['uid'] && $curDay === $schedule[$s]['day']) {
              echo '<td style=\'padding:0px;\'><select onchange="changeTblData(\'s;'.$uids[$u].';'.$curDay.';\'+this.options[this.selectedIndex].value);" class=\'form-control\''.
                    ($isHoly ? ' style=\'background-color:pink;\'' : '').'>';
              echo '<option value=\'0\' selected disabled></option>';
              for($h = $schedule[$s]['start'] + 1; $h < ((int)$v[2] === $curDay ? 25 : 23); ++$h) {
                echo '<option '.($schedule[$s]['finish'] === $h ? 'selected ' : '').'value=\''.$h.'\'>до '.($h===24 ? '00': $h).':00</option>';
              }
              echo '</select></td>';
              $isDayAdded = true;
            }
          }
          if(!$isDayAdded) {//пустые дни заполнение
            echo '<td></td>';
          }
        }
        echo '</tr>'.PHP_EOL;
        $curDay++;
      }
      ?>
    </table>
  </div>
</div>

<script type="text/javascript">
  function changeTblData(x) {
    $.ajax({
      url: 'ajax/changeSchedule.php',
      data: 'X=' + x + '&vid=<?=$vid ?>'
    });
  }
  </script>

<?php include_once $_SERVER['DOCUMENT_ROOT']."/footer.php"; } ?>
