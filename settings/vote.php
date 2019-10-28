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
      $vid = (int)$_POST['vid'];
      pg_query($_SESSION['psql'], 'delete from vote where id='.$vid.'; delete from kv where vid='.$vid.'; delete from rate where vid='.$vid.'; delete from bet where vid='.$vid.'; delete from schedule where vid='.$vid.';');
      exit(200);
    }

    $u0 = $_POST['u0'];
    $t0 = $_POST['t0'];
    $u1 = $_POST['u1'];
    $t1 = $_POST['t1'];
    $u2 = $_POST['u2'];
    $t2 = $_POST['t2'];
    $u3 = $_POST['u3'];
    $t3 = $_POST['t3'];

    $duf = '(select extract(doy from date \''.$_POST['duf']. '\'))';
    $dul = '(select extract(doy from date \''.$_POST['dul']. '\'))';
    $dtf = '(select extract(doy from date \''.$_POST['dtf']. '\'))';
    $dtl = '(select extract(doy from date \''.$_POST['dtl']. '\'))';
    $kik = explode(PHP_EOL, iconv('windows-1251', 'utf-8', file_get_contents($_FILES['kik']['tmp_name'])));
    $_size = count($kik);
    $name = str_replace('"', '', explode(';', $kik[0])[0]);
    $day = explode(';', $kik[1])[0];

    $vid = pg_fetch_row(pg_query($_SESSION['psql'], 'insert into vote(name, day, year) values (\''.$name.'\', '.(date('z', strtotime($day)) + 1).', '.date('Y', strtotime($day)).') returning id;'))[0];
    $q = array();
    $_sql = '';
    for($i = 12; $i < $_size; ++$i) {
      $k = explode(';', $kik[$i]);
      if(mb_strlen($k[6]) > 0) { //uik
        $q[] = '((select id from komission where number='.$k[6].'), '.$vid.', '.$duf.', '.$dul.')';
        $_sql .= 'insert into bet(vid,kid,pid,price) values ('.$vid.',(select id from komission where number='.$k[6].'), (select id from post where name=\'Председатель\' and type=0), '.$u0.');';
        $_sql .= 'insert into bet(vid,kid,pid,price) values ('.$vid.',(select id from komission where number='.$k[6].'), (select id from post where name=\'Заместитель председателя\' and type=0), '.$u1.');';
        $_sql .= 'insert into bet(vid,kid,pid,price) values ('.$vid.',(select id from komission where number='.$k[6].'), (select id from post where name=\'Секретарь\' and type=0), '.$u2.');';
        $_sql .= 'insert into bet(vid,kid,pid,price) values ('.$vid.',(select id from komission where number='.$k[6].'), (select id from post where name=\'Член комиссии\' and type=0), '.$u3.');';
      } else if(strpos($k[9],'T') !== false) { //tik
        $tik = explode('T', $k[9])[1];
        $q[] = '((select id from komission where number='.$tik.'), '.$vid.', '.$duf.', '.$dul.')';
        $_sql .= 'insert into bet(vid,kid,pid,price) values ('.$vid.',(select id from komission where number='.$tik.'), (select id from post where name=\'Председатель\' and type=0), '.$t0.');';
        $_sql .= 'insert into bet(vid,kid,pid,price) values ('.$vid.',(select id from komission where number='.$tik.'), (select id from post where name=\'Заместитель председателя\' and type=0), '.$t1.');';
        $_sql .= 'insert into bet(vid,kid,pid,price) values ('.$vid.',(select id from komission where number='.$tik.'), (select id from post where name=\'Секретарь\' and type=0), '.$t2.');';
        $_sql .= 'insert into bet(vid,kid,pid,price) values ('.$vid.',(select id from komission where number='.$tik.'), (select id from post where name=\'Член комиссии\' and type=0), '.$t3.');';
      }
    }
    pg_query($_SESSION['psql'], 'insert into kv(kid, vid, first, last) values '.implode(',', $q).';');
    pg_query($_SESSION['psql'], $_sql);
  }

  $votes = pg_query($_SESSION['psql'], 'select V.id, V.name, (select date \''.date('Y').'-01-01\' + V.day - 1) from vote V, kv KV, komission K where K.id=KV.kid and V.id=KV.vid and V.year='.date('Y').' and K.id='.$_SESSION['currentKid'].';');
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
  <li><a href='users.php'>Пользователи</a></li>
  <li class='active'><a href='#'>Голосования</a></li>
  <li><a href='test.php'>Тесты</a></li>
</ul>

<div class='tab-content'>
  <div class='tab-pane active container'>
    <nav>
      <a class='btn btn-success btn-small' data-toggle='modal' data-target='#addVoteModal'>Добавить выборы</a>
      <a class='btn btn-warning btn-small' href='cal.php'>Обновить календарь</a>
    </nav>
    <main>
      <table class='table table-bordered table-condensed table-striped'>
        <tr><th>Название</th><th>Дата голосования</th></tr>
        <?php while($v = pg_fetch_row($votes)) {
          echo '<tr vid=\''.$v[0].'\'><td>'.$v[1].'</td><td>'.$v[2].'</td></tr>';//<td><div class=\'btn btn-small btn-danger\' onclick=\'removeVote(this)\'>X</div></td>
        } ?>
      </table>
    </main>
    <script type='text/javascript'>
      function removeVote(t) {
        $.ajax({
          url: 'vote.php',
          data: 'act=rm&vid=' + $(t).parent().parent().attr('vid'),
          type: 'post',
          success: function(d, s, x) {
            $(t).parent().parent().remove();
          }
        });
      }
    </script>
  </div>
</div>




<div class='modal fade' id='addVoteModal' role='dialog'>
    <div class='modal-dialog' style='border: 1px solid #222;'>

      <form class='modal-content' action='#' method='POST' enctype='multipart/form-data'>
        <div class='modal-header inverse' style='padding:10px;'>
          <button style='color:white;' type='button' class='close' data-dismiss='modal'>&times;</button>
          <h5 class='modal-title'>Добавление новых выборов</h5>
        </div>
        <div class='modal-body' style='padding:5px;'>
          <input type='hidden' name='action' value='addVote'>
          <div class='input-group'>
            <span class='input-group-addon'>Отчет КИК (csv)</span>
            <!--input type='hidden' name='MAX_FILE_SIZE' value='1000000' /-->
            <input type='file' class='form-control' name='kik' accept='.csv' required/>
          </div>
          <br>

          <table class='table table-bordered table-condensed'>
            <tr><th></th><th colspan='3' style='text-align:center;background:#eee'>Ставка (руб/час):</th></tr>
            <tr>
              <th>Должность</th>
              <th>УИК</th>
              <th>ТИК</th>
              <th>ИКСРФ</th>
            </tr>
            <tr>
              <td>Председатель</td>
              <td><input class='form-control' type='number' name='u0' value='0' min='0' step='1' style='width:100%;margin:0;'/></td>
              <td><input class='form-control' type='number' name='t0' value='0' min='0' step='1' style='width:100%;margin:0;'/></td>
              <td><input class='form-control' type='number' name='i0' value='0' min='0' step='1' style='width:100%;margin:0;'/></td>
            </tr>
            <tr>
              <td>Заместитель</td>
              <td><input class='form-control' type='number' name='u1' value='0' min='0' step='1' style='width:100%;margin:0;'/></td>
              <td><input class='form-control' type='number' name='t1' value='0' min='0' step='1' style='width:100%;margin:0;'/></td>
              <td><input class='form-control' type='number' name='i1' value='0' min='0' step='1' style='width:100%;margin:0;'/></td>
            </tr>
            <tr>
              <td>Секретарь</td>
              <td><input class='form-control' type='number' name='u2' value='0' min='0' step='1' style='width:100%;margin:0;'/></td>
              <td><input class='form-control' type='number' name='t2' value='0' min='0' step='1' style='width:100%;margin:0;'/></td>
              <td><input class='form-control' type='number' name='i2' value='0' min='0' step='1' style='width:100%;margin:0;'/></td>
            </tr>
            <tr>
              <td>Член комиссии</td>
              <td><input class='form-control' type='number' name='u3' value='0' min='0' step='1' style='width:100%;margin:0;'/></td>
              <td><input class='form-control' type='number' name='t3' value='0' min='0' step='1' style='width:100%;margin:0;'/></td>
              <td><input class='form-control' type='number' name='i3' value='0' min='0' step='1' style='width:100%;margin:0;'/></td>
            </tr>
          </table>
          
          <div class='input-group'>
            <span class='input-group-addon'>Начало работы УИК:<br><br>Конец работы УИК:</span>
            <input type='date' class='form-control' name='duf' required/>
            <input type='date' class='form-control' name='dul' required/>
          </div>
          <div class='input-group'>
            <span class='input-group-addon'>Начало работы ТИК:<br><br>Конец работы ТИК:</span>
            <input type='date' class='form-control' name='dtf' required/>
            <input type='date' class='form-control' name='dtl' required/>
          </div>
          <div class='input-group'>
            <span class='input-group-addon'>Начало работы ИКСРФ:<br><br>Конец работы ИКСРФ:</span>
            <input type='date' class='form-control' name='dif'/>
            <input type='date' class='form-control' name='dil'/>
          </div>

        </div>
        <div class='modal-footer' style='padding:5px;'>
          <input type='submit' class='btn btn-success btn-small' value='Добавить'/>
        </div>
      </form>
    </div>
  </div>


<?php } include_once $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>
