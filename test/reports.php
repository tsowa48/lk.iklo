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

<nav class='navbar navbar-inverse'>
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

<?php if( !isset($_GET['id'])) { ?>
  <div class='list-group'>
    <a href='?id=1' class='list-group-item list-group-item-action text-center list-group-item-success'>Сводный отчет по теме</a>
    <a href='?id=2' class='list-group-item list-group-item-action text-center list-group-item-danger'>Не прошедшие тестирование по теме</a>
    <a href='?id=3' class='list-group-item list-group-item-action text-center list-group-item-warning'>Не приступившие к тестированию</a>
  </div>

<?php } else {
  $rid = (int)$_GET['id'];
  $daysAfterVote = 30;//Сколько дней показывать тест после дня голосования
  echo '<style> td, th {font-size: 15px;}</style>';

  include_once $_SERVER['DOCUMENT_ROOT'].'/test/reports/r'.$rid.'.php';

} ?>
  </main>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/footer.php'; } ?>