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
    if(isset($_POST['act']) && $_POST['act'] === 'rm') {
      $uid = intval($_POST['uid']);
      pg_query($_SESSION['psql'], 'delete from users where id='.$uid.'; delete from pu where uid='.$uid.'; delete from rate where uid='.$uid.'; delete from result where uid='.$uid.'; delete from schedule where uid='.$uid.';');
      exit(200);
    } else if(isset($_POST['act']) && $_POST['act'] === 'upd') {
      $uid = intval($_POST['uid']);
      $type = intval($_POST['t']);
      $pid = intval($_POST['pid']);
      $query = 'delete from pu where uid='.$uid.' and pid=(select pid from pu U, post P where U.uid='.$uid.' and P.id=U.pid and P.type='.$type.');';
      if($pid > 0) {
        $query .= 'insert into pu(uid,pid) values ('.$uid.', (select P.id from post P where P.type='.$type.' and P.id='.$pid.'));';
      }
      pg_query($_SESSION['psql'], $query);
      exit(200);
    } else if(isset($_POST['act']) && $_POST['act'] === 'staff') {
      $uid = intval($_POST['uid']);
      $isStaffer = $_POST['staff'];
      pg_query($_SESSION['psql'], 'update users set isstaffer='.($isStaffer === 'true' ? '1': '0').' where id='.$uid.';');
      exit(200);
    } else if(isset($_POST['act']) && $_POST['act'] === 'fio') {
      $uid = intval($_POST['uid']);
      $fio = urldecode($_POST['fio']);
      pg_query($_SESSION['psql'], 'update users set fio=\''.$fio.'\' where id='.$uid.';');
      exit(200);
    }

    $cyr = [
      'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
      'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
      'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
      'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
    ];
    $lat = [
      'a','b','v','g','d','e','e','zh','z','i','j','k','l','m','n','o','p',
      'r','s','t','u','f','h','c','ch','sh','sch','a','y','i','e','yu','ya',
      'a','b','v','g','d','e','e','zh','z','i','j','k','l','m','n','o','p',
      'r','s','t','u','f','h','c','ch','sh','sch','a','y','i','e','yu','ya'
    ];
    
    $fio = htmlspecialchars($_POST['fio']);
    $kid = $_SESSION['currentKid'];
    $pid = (int)$_POST['pid'];
    $pid2 = (int)$_POST['pid2'];
    $_fio = explode(' ', $fio);
    $login = str_replace($cyr, $lat, $_fio[0].mb_substr($_fio[1], 0, 1).mb_substr($_fio[2], 0, 1));
    $password = (int)((int)explode(' ', microtime())[1] * explode(' ', microtime())[0]);
    $facial = htmlspecialchars($_POST['facial']);
    $id = pg_fetch_row(pg_query($_SESSION['psql'], 'insert into users(fio,login,password,facial,kid) values (\''.$fio.'\', \''.$login.'\', \''.$password.'\', \''.$facial.'\', '.$kid.') returning id;'))[0];
    if($pid > 0)
      pg_query($_SESSION['psql'], 'insert into pu(uid,pid) values ('.$id.', '.$pid.');');
    if($pid2 > 0)
      pg_query($_SESSION['psql'], 'insert into pu(uid,pid) values ('.$id.', '.$pid2.');');
  }

  $posts = pg_query($_SESSION['psql'], 'select P.id, P.name from post P where P.type=0;');
  $posts2 = pg_query($_SESSION['psql'], 'select P.id, P.name from post P where P.type=1;');

  $p1 = array();
  $p2 = array();
  while($p = pg_fetch_row($posts))
    $p1[] = array('id' => (int)$p[0], 'name' => $p[1]);
  while($p = pg_fetch_row($posts2))
    $p2[] = array('id' => (int)$p[0], 'name' => $p[1]);

  $users = pg_query($_SESSION['psql'], 'select U.id, U.fio, coalesce((select P.name from post P, users U1, pu PU1 where P.type=0 and P.id=PU1.pid and PU1.uid=U1.id and U1.id=U.id), \'-\'), '.
    'U.login, U.facial, coalesce((select P1.name from post P1, users U1, pu PU1 where P1.type=1 and P1.id=PU1.pid and PU1.uid=U1.id and U1.id=U.id), \'-\'), U.password, U.isstaffer '.
    'from users U, komission K, pu PU where PU.uid=U.id and U.kid=K.id and K.id='.$_SESSION['currentKid'].' group by 1,2,4,5,7,K.number order by K.number, U.fio');
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
  <li><a href='komission.php'>Комиссии</a></li>
  <li class='active'><a href='#'>Пользователи</a></li>
  <li><a href='vote.php'>Голосования</a></li>
  <li><a href='test.php'>Тесты</a></li>
</ul>

<div class='tab-content'>
  <div class='tab-pane active container-fluid'>
    <nav>
      <form method='post' action='#' class='form-inline'>
      <input type='text' class='form-control input-sm' name='fio' placeholder='ФИО' style='width:25%;' required/>
      <select class='form-control input-sm' name='pid'><option value='0'></option>
      <?php foreach($p1 as $key => $value) {
        echo '<option value=\''.$p1[$key]['id'].'\'>'.$p1[$key]['name'].'</option>';
      } ?> </select>
      <select class='form-control input-sm' name='pid2'><option value='0'></option>
      <?php foreach($p2 as $key => $value) {
        echo '<option value=\''.$p2[$key]['id'].'\'>'.$p2[$key]['name'].'</option>';
      } ?> </select>
      <input type='text' class='form-control input-sm' name='facial' style='width:20%;' placeholder='Лицевой счёт'/>
      <input type='submit' class='btn btn-small btn-success' value='Добавить'/>
      </form>
    </nav>
    <main>
      <table class='table table-bordered table-condensed table-striped'>
        <tr><th>ФИО</th><th>Должность</th><th>Должность 2</th><th>Логин</th><th>Пароль</th><th>Лицевой счёт</th><th>В штате</th><th></th></tr>
        <?php while($user = pg_fetch_row($users)) {
          echo '<tr uid=\''.$user[0].'\'><td onkeyup=\'changefio(this,event);\' contenteditable=\'true\'>'.$user[1].'</td><td style=\'padding:0;\'><select onchange=\'updateUser(this,0);\' class=\'form-control input-sm\' ><option value=\'0\' selected></option>';
          foreach($p1 as $key => $value) {
            echo '<option '.(strcmp($user[2], $p1[$key]['name'])===0 ? 'selected ' : '').'value=\''.$p1[$key]['id'].'\'>'.$p1[$key]['name'].'</option>';
          }
          echo '</select></td>';
          if(strcmp($user[5], 'Администратор')===0) {
            echo '<td>'.$user[5].'</td>';
          } else {
            echo '<td style=\'padding:0;\'><select onchange=\'updateUser(this,1);\' class=\'form-control input-sm\'><option value=\'0\' selected></option>';
            foreach($p2 as $key => $value) {
              echo '<option '.(strcmp($user[5], $p2[$key]['name'])===0 ? 'selected ' : '').'value=\''.$p2[$key]['id'].'\'>'.$p2[$key]['name'].'</option>';
            }
            echo '</select></td>';
          }//(is_numeric($user[4]) ? 'указан' : '')
          echo '<td>'.$user[3].'</td><td>'.(strcmp($user[5], 'Администратор') === 0 ? '' : $user[6]).'</td><td>'.$user[4].'</td><td style=\'padding:0px;\'><input class=\'form-control input-sm\' onchange=\'setStaffer(this);\' type=\'checkbox\' name=\'isstaffer\' '.(is_numeric($user[7]) ? 'checked' : '').'/></td><td style=\'padding:0;\'><div class=\'btn btn-small btn-danger\' onclick=\'removeUser(this);\'>X</div></td></tr>';
        } ?>
      </table>
    </main>
    <script type='text/javascript'>
      function removeUser(t) {
        $.ajax({
          url: 'users.php',
          data: 'act=rm&uid=' + $(t).parent().parent().attr('uid'),
          type: 'post',
          success: function(d, s, x) {
            $(t).parent().parent().remove();
          }
        });
      }

      function updateUser(p, t) {
        $.ajax({
          url: 'users.php',
          data: 'act=upd&t=' + t + '&uid=' + $(p).parent().parent().attr('uid') + '&pid=' + p.options[p.selectedIndex].value,
          type: 'post'
        });
      }

      function setStaffer(t) {
        $.ajax({
          url: 'users.php',
          data: 'act=staff&uid=' + $(t).parent().parent().attr('uid') + '&staff=' + t.checked,
          type: 'post'
        });
      }

      function changefio(t,e) {
        //console.log(e);
        if(e.keyCode < 32)
          return;
        $.ajax({
          url: 'users.php',
          data: 'act=fio&uid=' + $(t).parent().attr('uid') + '&fio=' + encodeURI(t.innerHTML),
          type: 'post'
        });
      }
    </script>
  </div>
</div>

<?php } include_once $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>
