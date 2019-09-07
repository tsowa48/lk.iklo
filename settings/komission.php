<?php
require_once $_SERVER['DOCUMENT_ROOT']."/user.php";
session_start();
if (!isset($_SESSION['currentUser']) || !in_array(8, $_SESSION['currentUser']->post)) {
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
      //pg_query($_SESSION['psql'], 'delete from komission where id='.$kid.'; delete from users where kid='.$kid.';');// <----- TODO: delete from all tables kid & uid
      exit(200);
    }
    $name = htmlspecialchars($_POST['name']);
    $number = (int)$_POST['number'];
    pg_query($_SESSION['psql'], 'insert into komission(parent,name,number) values ('.$_SESSION['currentKid'].', \''.$name.'\', '.$number.')');
  } else if($_SERVER['REQUEST_METHOD'] === 'GET') {
    if(isset($_GET['kid']) && is_numeric($_GET['kid']))
      $kid = (int)$_GET['kid'];
    else
      $kid = $_SESSION['currentUser']->kid;
    $_SESSION['currentKid'] = $kid;
  }

  $komissions = pg_query($_SESSION['psql'], 'select K.id, K.name, K.number from komission K where K.parent='.$_SESSION['currentKid'].' or K.id='.$_SESSION['currentKid'].' order by K.number;');
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
        <?php
        if(in_array(6, $_SESSION['currentUser']->post))
          echo '<li><a href=\'/buh/\'>Бухгалтерия</a></li>';
        ?>
        <li class='active'><a href='#'>Настройки</a></li>
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

<ul class='nav nav-tabs'>
  <li class='active'><a href='#'>Комиссии</a></li>
  <li><a href='users.php'>Пользователи</a></li>
  <li><a href='vote.php'>Голосования</a></li>
  <li><a href='test.php'>Тесты</a></li>
</ul>
<div class='tab-content'>
  <div class='tab-pane active container'>
    <nav>
      <form method='post' action='#' class='form-inline'>
        <input type='text' class='form-control input-sm' name='name' placeholder='Название' style='width:40%;' required/>
        <input type='text' class='form-control input-sm' name='number' placeholder='Номер' required />
        <input type='submit' class='btn btn-small btn-success' value='Добавить'/>
      </form>
    </nav>
    <main>
      <table class='table table-bordered table-condensed table-striped'>
        <tr><th>Название</th><th>Номер</th><th><a href='?' class='btn btn-small btn-success'><i class='glyphicon glyphicon-home'></i></a></th></tr>
        <?php while($k = pg_fetch_row($komissions)) {
          echo '<tr style=\'cursor:pointer;\' onclick=\'changeKid(this);\''.($_SESSION['currentKid'] === (int)$k[0] ? 'class=\'danger\' ':'').'kid=\''.$k[0].'\'><td>'.$k[1].'</td><td>'.$k[2].'</td><td><div class=\'btn btn-small btn-danger\' onclick=\'removeKomission(this)\'>X</div></td></tr>';
        } ?>
      </table>
    </main>
    <script type='text/javascript'>
      function removeKomission(t) {
        $.ajax({
          url: 'komission.php',
          data: 'act=rm&kid=' + $(t).parent().parent().attr('kid'),
          type: 'post',
          success: function(d, s, x) {
            $(t).parent().parent().remove();
          }
        });
      }

      function changeKid(t) {
        location.href = '?kid=' + $(t).attr('kid');
      }
    </script>
  </div>
</div>

<?php } include_once $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>
