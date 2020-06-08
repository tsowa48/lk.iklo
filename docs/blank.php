<?php
function str2rtf($str) {
  $ret = $str;
  $ret = str_replace('А', '\'c0', $ret);
  $ret = str_replace('Б', '\'c1', $ret);
  $ret = str_replace('В', '\'c2', $ret);
  $ret = str_replace('Г', '\'c3', $ret);
  $ret = str_replace('Д', '\'c4', $ret);
  $ret = str_replace('Е', '\'c5', $ret);
  $ret = str_replace('Ё', '\'c5', $ret);//<---
  $ret = str_replace('Ж', '\'c6', $ret);
  $ret = str_replace('З', '\'c7', $ret);
  $ret = str_replace('И', '\'c8', $ret);
  $ret = str_replace('Й', '\'c9', $ret);
  $ret = str_replace('К', '\'ca', $ret);
  $ret = str_replace('Л', '\'cb', $ret);
  $ret = str_replace('М', '\'cc', $ret);
  $ret = str_replace('Н', '\'cd', $ret);
  $ret = str_replace('О', '\'ce', $ret);
  $ret = str_replace('П', '\'cf', $ret);
  $ret = str_replace('Р', '\'d0', $ret);
  $ret = str_replace('С', '\'d1', $ret);
  $ret = str_replace('Т', '\'d2', $ret);
  $ret = str_replace('У', '\'d3', $ret);
  $ret = str_replace('Ф', '\'d4', $ret);
  $ret = str_replace('Х', '\'d5', $ret);
  $ret = str_replace('Ц', '\'d6', $ret);
  $ret = str_replace('Ч', '\'d7', $ret);
  $ret = str_replace('Ш', '\'d8', $ret);
  $ret = str_replace('Щ', '\'d9', $ret);
  $ret = str_replace('Ъ', '\'da', $ret);
  $ret = str_replace('Ы', '\'db', $ret);
  $ret = str_replace('Ь', '\'dc', $ret);
  $ret = str_replace('Э', '\'dd', $ret);
  $ret = str_replace('Ю', '\'de', $ret);
  $ret = str_replace('Я', '\'df', $ret);
  $ret = str_replace('а', '\'e0', $ret);
  $ret = str_replace('б', '\'e1', $ret);
  $ret = str_replace('в', '\'e2', $ret);
  $ret = str_replace('г', '\'e3', $ret);
  $ret = str_replace('д', '\'e4', $ret);
  $ret = str_replace('е', '\'e5', $ret);
  $ret = str_replace('ё', '\'e5', $ret);//<---
  $ret = str_replace('ж', '\'e6', $ret);
  $ret = str_replace('з', '\'e7', $ret);
  $ret = str_replace('и', '\'e8', $ret);
  $ret = str_replace('й', '\'e9', $ret);
  $ret = str_replace('к', '\'ea', $ret);
  $ret = str_replace('л', '\'eb', $ret);
  $ret = str_replace('м', '\'ec', $ret);
  $ret = str_replace('н', '\'ed', $ret);
  $ret = str_replace('о', '\'ee', $ret);
  $ret = str_replace('п', '\'ef', $ret);
  $ret = str_replace('р', '\'f0', $ret);
  $ret = str_replace('с', '\'f1', $ret);
  $ret = str_replace('т', '\'f2', $ret);
  $ret = str_replace('у', '\'f3', $ret);
  $ret = str_replace('ф', '\'f4', $ret);
  $ret = str_replace('х', '\'f5', $ret);
  $ret = str_replace('ц', '\'f6', $ret);
  $ret = str_replace('ч', '\'f7', $ret);
  $ret = str_replace('ш', '\'f8', $ret);
  $ret = str_replace('щ', '\'f9', $ret);
  $ret = str_replace('ъ', '\'fa', $ret);
  $ret = str_replace('ы', '\'fb', $ret);
  $ret = str_replace('ь', '\'fc', $ret);
  $ret = str_replace('э', '\'fd', $ret);
  $ret = str_replace('ю', '\'fe', $ret);
  $ret = str_replace('я', '\'ff', $ret);

  //$ret = str_replace('', '', $ret);
  return $ret;
};

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
  if(isset($_GET['doc'])) {
    $doc = $_GET['doc'];
    $doc = str_replace('/', '', $doc);
    //$doc = str_replace('&#47;', '', $doc);

    $file = $_SERVER['DOCUMENT_ROOT'].'/docs/files/'.$doc;
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$doc.'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    $content = file_get_contents($file);

    //Replace template variables
    $content = str_replace('lk_currentYear', date('Y'), $content);//Текущий год
    $content = str_replace('lk_currentUserFIO', $_SESSION['currentUser']->fio, $content);//ФИО текущего пользователя
    $content = str_replace('lk_currentIK', $_SESSION['currentUser']->ik, $content);//Комиссия текущего пользователя

    $content = str2rtf($content);
    header('Content-Length: '.strlen($content));
    echo $content;
    exit();
  }

  include_once $_SERVER['DOCUMENT_ROOT'].'/header.php';
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
        <li class='dropdown active'>
          <a class='dropdown-toggle' data-toggle='dropdown' href='#'>Документы&nbsp;<span class='caret'></span></a>
          <ul class='dropdown-menu'>
            <li><a href='#'>Бланки</a></li>

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

  <main class='container'><style> #tblReports td{ vertical-align:middle;} #tblReports th {text-align:center;}</style>
    <table class='table table-bordered table-condensed' id='tblReports'>
      <tr><th>Название</th><th>Формат</th></tr>
    <?php
      $dir = $_SERVER['DOCUMENT_ROOT'].'/docs/files/';
      $files1 = array_diff(scandir($dir), array('..', '.'));
    
      foreach ($files1 as $key => $name) {
        if (is_file($dir.'/'.$name))  {
          $title = mb_substr($name, 0, strlen($name) - mb_strrpos($name, '.'));
          ////$mime = mime_content_type($dir.'/'.$name);
          echo '<tr><td>'.$title.'</td><td class=\'col-xs-2\'><a href=\'?doc='.$name.'\' class=\'btn btn-primary btn-small\'><i class=\'glyphicon glyphicon-save\'></i> Word</a></td></tr>';
        }
      }
  ?>
    </table>
  </main>

<?php } include_once $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>
