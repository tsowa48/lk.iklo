<?php
require_once $_SERVER['DOCUMENT_ROOT']."/user.php";
session_start();
if (!isset($_SESSION['currentUser'])) {
  if(isset($_SESSION['psql']) && !is_int($_SESSION['psql']) && !$_SESSION['psql'])
    pg_close($_SESSION['psql']);
  unset($_SESSION['psql']);
  unset($_SESSION['currentUser']);
  session_destroy();
  header("Location: /");
  die();
} else {
  include_once $_SERVER['DOCUMENT_ROOT'].'/header.php';

  if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if($_POST['act'] === 'rm') {
      $kid = (int)$_POST['kid'];
      pg_query($_SESSION['psql'], 'delete from komission where id='.$kid.';');
      exit(200);
    }
    $name = htmlspecialchars($_POST['name']);
    $number = (int)$_POST['number'];
    pg_query($_SESSION['psql'], 'insert into komission(parent,name,number) values ('.$_SESSION['currentUser']->kid.', \''.$name.'\', '.$number.')');
  }

  $komissions = pg_query($_SESSION['psql'], 'select K.id, K.name, K.number from komission K where K.parent='.$_SESSION['currentUser']->kid.' or K.id='.$_SESSION['currentUser']->kid.' order by K.number;');
?>

<body class='container-fluid'>

  <nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <div class="navbar-brand">ЛК ИК</div>
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
    <div class='panel panel-primary'>
      <div class='panel-heading text-center'><b>Основная информация</b></div>
      <div class='panel-body'>
        <?php $user = pg_fetch_row(pg_query($_SESSION['psql'], 'select U.id, U.fio, U.login, U.password, U.facial, array_to_string(array(select P.name from post P, pu PU where P.id=PU.pid and PU.uid=U.id), \', \'), K.name from users U, komission K where K.id=U.kid and U.id='.$_SESSION['currentUser']->id.';')); ?>
        <div class='input-group'>
          <span class='input-group-addon'>Фамилия Имя Отчество:</span>
          <input type='text' class='form-control' value='<?=$user[1] ?>'/>
        </div>
        <br>
        <div class='input-group'>
          <span class='input-group-addon'>Логин:</span>
          <input type='text' class='form-control' value='<?=$user[2] ?>'/>
        </div>
        <br>
        <div class='input-group'>
          <span class='input-group-addon'>Пароль:</span>
          <input type='text' class='form-control' value='******'/><? //$user[3] ?>
        </div>
        <br>
        <div class='input-group'>
          <span class='input-group-addon'>Лицевой счёт:</span>
          <input type='text' class='form-control' placeholder='40817810XXXXXXXXXXXX' value='<?=is_numeric($user[4]) ? $user[4] : '' ?>'/>
        </div>
        <br>
        <div class='input-group'>
          <span class='input-group-addon'>Должность:</span>
          <input type='text' class='form-control' disabled value='<?=$user[5] ?>'/>
        </div>
        <br>
        <div class='input-group'>
          <span class='input-group-addon'>Комиссия:</span>
          <input type='text' class='form-control' disabled value='<?=$user[6] ?>'/>
        </div>
      </div>
      <!--div class='panel-footer'>
        <input type='submit' class='btn btn-small btn-success right' value='Сохранить изменения'/>
      </div-->
    </div>
  </main>

<?php } include_once $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>
