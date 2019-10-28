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
  include_once $_SERVER['DOCUMENT_ROOT']."/header.php";
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


  <main class='container'>
    <div class='list-group'>
    <?php
      $votes = pg_query($_SESSION['psql'], 'select V.id, V.name, (select date \''.date('Y').'-01-01\' + V.day - 1) from vote V, kv KV, komission K where (V.day + 60)>'.date('z').' and V.year='.date('Y').' and K.id=KV.kid and V.id=KV.vid and K.id='.$_SESSION['currentUser']->kid.';');
      while($v = pg_fetch_row($votes)) {
        echo '<a href=\'vote.php?id='.$v[0].'\' class=\'list-group-item list-group-item-action\'>'.$v[1].' <label class=\'label label-primary\'>'.$v[2].'</label></a>';
      }
    ?>
    </div>
  </main>


<?php include_once $_SERVER['DOCUMENT_ROOT']."/footer.php"; } ?>
