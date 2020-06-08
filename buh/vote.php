<?php
require_once $_SERVER['DOCUMENT_ROOT']."/user.php";
session_start();
if (!isset($_SESSION['currentUser']) || !in_array(6, $_SESSION['currentUser']->post)) {
  if(isset($_SESSION['psql']) && !is_int($_SESSION['psql']) && !$_SESSION['psql'])
    pg_close($_SESSION['psql']);
  unset($_SESSION['psql']);
  unset($_SESSION['currentUser']);
  session_destroy();
  header("Location: /");
  die();
} else {
  if(!isset($_GET['id'])) {
    header("Location: /buh/");
    die();
  } else {
    include_once $_SERVER['DOCUMENT_ROOT']."/header.php";
    $vid = $_GET['id'];
    $vote = pg_query($_SESSION['psql'], 'select V.id, V.name, V.day from vote V where V.id='.$vid);
    if(pg_num_rows($vote)===0) {
      header('Location: /buh/');
      die();
    } else {
      $_SESSION['vid'] = $vid;
    }
    $v = pg_fetch_row($vote);
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
            <?php if(in_array(8, $_SESSION['currentUser']->post))
  echo '<li><a href="/test/reports.php">Отчеты</a></li>';
?>
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
  <li class='active'><a href='#'>Состав комиссии</a></li>
  <li><a href='work.php'>Часы работы (не трогать)</a></li>
  <li><a href='start.php'>Начало работы</a></li>
  <li><a href='finish.php'>Окончание работы</a></li>
  <?php $_count = (int)pg_fetch_row(pg_query($_SESSION['psql'], 'select count(K.id) from komission K where K.parent='.$_SESSION['currentUser']->kid.';'))[0];
    if($_count > 0)
      echo '<li><a href=\'predik.php\'>Вознаграждение ИК</a></li>';
  ?>
  <li><a href='reports.php'><b>Отчеты</b></a></li>
</ul>

<div class='tab-content'>
  <div class='tab-pane active container-fluid'>
    <table class='table table-striped table-bordered table-condensed'>
      <tr><th>№ п/п</th><th>Фамилия Имя Отчество</th><th>Должность</th><th>Лицевой счёт</th><th>Ведомственный коэффициент</th></tr>
        <?php
          $komission = pg_query($_SESSION['psql'], 'select U.id, U.fio, P.name, U.facial, coalesce((select R.coef from rate R where R.uid=U.id and R.vid='.$vid.'), 0.0) from users U, post P, pu PU where U.id=PU.uid and PU.pid=P.id and P.type=0 and (U.isstaffer is null or U.isstaffer=0) and U.kid='.$_SESSION['currentUser']->kid.' order by P.id, U.fio;');
          $num = 1;
          while($k = pg_fetch_row($komission)) {
            $facial = $k[3];
            if(!is_numeric($facial))
              $facial = '<input onchange="changeTblData(\'f;'.$k[0].';\'+this.value);" placeholder=\'40817810XXXXXXXXXXXX\' type=\'text\' style=\'width: 100%; border: 0; background-color: inherit;\'/>';
            echo '<tr><td>'.($num++).'</td><td>'.$k[1].'</td><td>'.$k[2].'</td><td>'.$facial.'</td><td>'.
              (strpos($k[2], 'Пред') !== FALSE ? '-' :
              '<input onchange="changeTblData(\'r;'.$k[0].';\'+Number.parseFloat(this.value).toFixed(8));" type=\'number\' min=\'0.00000000\' max=\'2.00000000\' step=\'0.00000001\' value=\''.sprintf("%1\$.8f", $k[4]).'\' style=\'width: 100%; border: 0; background-color: inherit;\'/>')
              .'</td></tr>';
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

</main>

<?php } include_once $_SERVER['DOCUMENT_ROOT']."/footer.php"; } ?>
