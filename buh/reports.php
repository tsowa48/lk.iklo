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
  <li><a href='vote.php?id=<?=$vid ?>'>Состав комиссии</a></li>
  <li><a href='work.php'>Часы работы (не трогать)</a></li>
  <li><a href='start.php'>Начало работы</a></li>
  <li><a href='finish.php'>Окончание работы</a></li><?php
  if($_count > 0)
    echo '<li><a href=\'predik.php\'>Вознаграждение ИК</a></li>';
  ?><li class='active'><a href='#'><b>Отчеты</b></a></li>
</ul>

<div class='tab-content'>
<div class='tab-pane active container'><style> #tblReports td{ vertical-align:middle;} #tblReports th {text-align:center;}</style>
  <table class='table table-bordered table-condensed' id='tblReports'>
    <tr><th>Название отчета</th><th>Тип отчета</th><th>Формат</th></tr>
    <tr>
      <td rowspan='2'>1&nbsp;График работы</td>
            <td>За весь период</td>
            <td>
              <a href='report.php?id=1&t=all&f=doc' class='btn btn-primary btn-small'><i class='glyphicon glyphicon-save'></i> Word</a>
            </td>
          </tr>
          <tr>
            <td>По месяцам</td>
            <td>
              <a href='report.php?id=1&t=month&f=doc' class='btn btn-primary btn-small'><i class='glyphicon glyphicon-save'></i> Word</a>
            </td>
          </tr>
          <tr>
            <td rowspan='2'>2&nbsp;Сведения</td>
            <td>За весь период</td>
            <td>
              <a href='report.php?id=2&t=all&f=doc' class='btn btn-primary btn-small'><i class='glyphicon glyphicon-save'></i> Word</a>
            </td>
          </tr>
          <tr>
            <td>По месяцам</td>
            <td>
              <a href='report.php?id=2&t=month&f=doc' class='btn btn-primary btn-small'><i class='glyphicon glyphicon-save'></i> Word</a>
            </td>
          </tr>
          <tr>
            <td <?php echo ($_count > 0 ? 'rowspan=\'2\'' : ''); ?> >3&nbsp;Расчетная ведомость</td>
            <td>Вознаграждение</td>
            <td>
              <a href='report.php?id=3&f=doc' class='btn btn-primary btn-small'><i class='glyphicon glyphicon-save'></i> Word</a>
            </td>
          </tr>
          <?php if ($_count > 0) { ?>
            <tr>
            <td>За активную работу председателей</td>
            <td>
            <a href='report.php?id=3&t=pred&f=doc' class='btn btn-primary btn-small'><i class='glyphicon glyphicon-save'></i> Word</a>
            </td>
            </tr>
          <?php }?>
          <tr>
            <td rowspan='<?=($_count > 0 ? '3' : '2') ?>'>4&nbsp;Дополнительная оплата</td>
            <td>Вознаграждение</td>
            <td>
              <a href='report.php?id=4&t=fee&f=doc' class='btn btn-primary btn-small'><i class='glyphicon glyphicon-save'></i> Word</a>
            </td>
          </tr>
          <tr>
            <td>За активную работу</td>
            <td>
              <a href='report.php?id=4&t=award&f=doc' class='btn btn-primary btn-small'><i class='glyphicon glyphicon-save'></i> Word</a>
            </td>
          </tr>
          <?php if ($_count > 0) { ?>
          <tr>
            <td>За активную работу председателей</td>
            <td>
              <a href='report.php?id=4&t=pred&f=doc' class='btn btn-primary btn-small'><i class='glyphicon glyphicon-save'></i> Word</a>
            </td>
          </tr>
          <?php }?>
          <tr>
            <td rowspan='<?=($_count > 0 ? '3' : '2') ?>'>5&nbsp;Реестр</td>
            <td>Сбербанк</td>
            <td>
              <a href='report.php?id=5&t=sber&f=csv' class='btn btn-success btn-small'><i class='glyphicon glyphicon-save'></i> Excel</a>
            </td>
          </tr>
          <?php if ($_count > 0) { ?>
          <tr>
            <td>Сбербанк (Премия председателей)</td>
            <td>
              <a href='report.php?id=5&t=pred&f=csv' class='btn btn-success btn-small'><i class='glyphicon glyphicon-save'></i> Excel</a>
            </td>
          </tr>
          <?php }?>
          <tr>
            <td>Избирательная комиссия</td>
            <td>
              <a href='report.php?id=5&t=ik&f=csv' class='btn btn-success btn-small'><i class='glyphicon glyphicon-save'></i> Excel</a>
            </td>
          </tr>
        </table>
      </div>
</div>
<?php include_once $_SERVER['DOCUMENT_ROOT']."/footer.php"; } ?>
