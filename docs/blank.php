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
            <li><a href='/test/reports.php'>Отчеты</a></li>
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
    
      foreach ($files1 as $key => $value) {
        if (is_file($dir.'/'.$value))  {
          //$mime = mime_content_type($dir.'/'.$value);
          $z = zip_open($dir.'/'.$value);
          while ($zip_entry = zip_read($z)) {
            $name = zip_entry_name($zip_entry);
            if($name === 'docProps/core.xml') {// docProps/app.xml
              zip_entry_open($z, $zip_entry, "r");
              $buf = (zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));//json_encode
              $title = substr($buf, strpos($buf, '<dc:title>') + 10, strpos($buf, '</dc:title>') - strpos($buf, '<dc:title>') - 10);

              //echo '<a href=\'?doc='.$value.'\' class=\'list-group-item list-group-item-action list-group-item-default\'>'.$mime.'</a>';
              echo '<tr><td>'.$title.'</td><td><a href=\'?doc='.$value.'\' class=\'btn btn-primary btn-small\'><i class=\'glyphicon glyphicon-save\'></i> Word</a></td></tr>';

              zip_entry_close($zip_entry);
              break;
            }
          }
          zip_close($z);
        }
      }
  ?>
    </table>
  </main>

<?php } include_once $_SERVER['DOCUMENT_ROOT'].'/footer.php'; ?>
