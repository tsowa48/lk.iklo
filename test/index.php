<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/user.php';
session_start();
if (!isset($_SESSION['currentUser']) || $_SESSION['currentUser'] == NULL) {
  pg_close($_SESSION['psql']);
  session_destroy();
  header('Location: /');
  die();
} else {
  include_once $_SERVER['DOCUMENT_ROOT'].'/header.php';
?>

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
    <div class='list-group'>
    <?php
    $tests = pg_query($_SESSION['psql'], 'select T.id, T.text, T.passball, coalesce((select R.ball from result R where R.uid='.$_SESSION['currentUser']->id.' and R.tid=T.id), -9999.0), V.name from test T, klass G, vote V where V.year='.date('Y').' and T.vid=V.id and T.id=G.tid and (G.kid='.$_SESSION['currentUser']->kid.' or G.uid='.$_SESSION['currentUser']->id.' or G.pid in ('.implode(',', $_SESSION['currentUser']->post).'));');
    if(pg_num_rows($tests)>0){
      while($test = pg_fetch_row($tests)) {
        if($test[3] < $test[2]) {
          echo '<a href=\'test.php?tid='.$test[0].'\' class=\'list-group-item list-group-item-action list-group-item-'.($test[3] < -999.0 ? 'default' : 'danger').'\'>'.$test[1].'<br><label class=\'label label-primary\'>'.$test[4].'</label></a>';
        }
      }
    } else
      echo '<p align="center" class="list-group-item list-group-item-success">Вы успешно сдали все тесты</p>';
  ?>
    </div>
  </main>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/footer.php'; } ?>
