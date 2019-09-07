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
  <li><a href='finish.php'>Окончание работы</a></li>
  <li class='active'><a href='#'>Вознаграждение ИК</a></li>
  <li><a href='reports.php'><b>Отчеты</b></a></li>
</ul>

<div class='tab-content'>
  <div class='tab-pane active container'>
    <table class='table table-striped table-bordered table-condensed'>
      <tr><th>№ п/п</th><th>Фамилия Имя Отчество</th><th>Комиссия</th><th>Вознаграждение</th><th>Коэффициент</th></tr>
        <?php 
          $result = pg_query($_SESSION['psql'], 'select U.id, U.fio, K.name, B.price, coalesce((select R.coef from rate R where R.uid=PU.uid and R.vid=B.vid), 0.0), coalesce((select sum(S.finish-S.start) from schedule S where S.uid=PU.uid and S.vid=B.vid), 0.0), U.isstaffer, U.facial from bet B, pu PU, users U, post P, komission K where U.id=PU.uid and P.id=PU.pid and B.kid=U.kid and B.pid=PU.pid and B.kid=K.id and P.name=\'Председатель\' and K.parent='.$_SESSION['currentUser']->kid.' and B.vid='.$vid.' order by K.number;');
          $num = 1;
          while($row = pg_fetch_row($result)) {
            //$data[] = array('uid' => (int)$row[0], 'fio' => $row[1], 'komission' => $row[2], 'price' => (double)$row[3], 'coef' => (double)$row[4],
            //'hours' => (int)$row[5], 'isstaffer' => (int)$row[6], 'facial' => $row[7]);


            $tww = pg_fetch_row(pg_query($_SESSION['psql'], 'select (((select coalesce(sum(S1.finish-S1.start),0)-coalesce(sum(case when S1.start < 6 then (case when S1.finish < 7 then S1.finish - S1.start else 6 - S1.start end) when S1.finish>22 then (case when S1.start > 21 then S1.finish - S1.start else S1.finish - 22 end) end),0)
                                                    from schedule S1 where S1.vid=S.vid and S1.uid=S.uid and S1.day in (select doy from calendar where isholy=1)))) as holy, (select coalesce(sum(case when S2.start < 6 then (case when S2.finish < 7 then S2.finish - S2.start else 6 - S2.start end) when S2.finish>22 then (case when S2.start > 21 then S2.finish - S2.start else S2.finish - 22 end) end), 0) from schedule S2 where (S2.start < 6 or S2.finish > 22) and S2.vid=S.vid and S2.uid=S.uid) as night
                                                    from schedule S where S.vid='.$vid.' and S.uid='.(int)$row[0].' group by S.vid, S.uid;'));//Двойная оплата
            $twice = (int)$tww[0] + (int)$tww[1];//Праздничные + ночные
            $once = (int)$row[5] - $twice;//Одинарная оплата (кол-во часов)

            //$coef = $d['coef'];
            $summ = ($once + $twice * 2) * (double)$row[3];//Размер оплаты за часы

            echo '<tr><td>'.($num++).'</td><td>'.$row[1].'</td><td>'.$row[2].
                 '</td><td>'.sprintf("%1\$.2f", $summ).'</td><td style=\'padding:0px;\'><input onchange="changeTblData(\'r;'.$row[0].';\'+Number.parseFloat(this.value).toFixed(8));"'.
                 ' type=\'number\' min=\'0.00000000\' max=\'2.00000000\' step=\'0.00000001\' value=\''.sprintf("%1\$.8f", (double)$row[4]).'\' style=\'width: 100%; border: 0; background-color: inherit;\'/></td></tr>';
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
