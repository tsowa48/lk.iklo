<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/user.php';
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
      if($_POST['action'] === 'addTest') {
        $testName = $_POST['testTheme'];
        $vid = (int)$_POST['vid'];
        $testKlass = $_POST['testKlass'];

        $questions = array();
        $answers = array();
        $answerText = array();
        foreach ($_POST as $key => $value) {
          if(substr($key, 0, 2) === 'q_')
            $questions[] = array((int)explode('_', $key)[1], $value);
          if(substr($key, 0, 2) === 'a_')
            $answers[] = array((int)explode('_', $key)[1], (int)$value);
          if(substr($key, 0, 2) === 't_')
            $answerText[] = array((int)explode('_', $key)[1], array((int)explode('_', $key)[2], $value));
        }
        $minBall = count($questions) * 0.6;
        $tid = pg_fetch_row(pg_query($_SESSION['psql'], 'insert into test(text, vid, passball) values (\''.$testName.'\', '.$vid.', '.$minBall.') returning id;'))[0];
        $query = '';
        foreach($testKlass as $grp) {
          $query .= 'insert into klass(tid,'.substr($grp, 0, 1).'id) values ('.$tid.', '.substr($grp, 1).');';
        }
        pg_query($_SESSION['psql'], $query);
        $query = '';
        foreach ($questions as $qIdText) {
          $qInternalId = $qIdText[0];
          $qText = $qIdText[1];
          $correctAnswersPerQuestion = 0;
          foreach ($answers as $a) {
            if($a[0] === $qInternalId)
              $correctAnswersPerQuestion++;
          }
          $qid = pg_fetch_row(pg_query($_SESSION['psql'], 'insert into question(tid, text, type) values ('.$tid.',\''.$qText.'\', '.($correctAnswersPerQuestion > 1 ? '2': '1').') returning id;'))[0];

          foreach ($answerText as $at) {
            if($at[0] === $qInternalId) {
              $aId = $at[1][0];//answerId
              $aText = $at[1][1];//answerText
              $isCorrect = false;
              foreach ($answers as $a) {
                if($a[0] === $qInternalId && $a[1] === $aId)
                  $isCorrect = true;
              }
              $query .= 'insert into answer(qid,text,ball) values ('.$qid.',\''.$aText.'\','.($isCorrect ? sprintf("%.1f", 1.0 / $correctAnswersPerQuestion) : ($correctAnswersPerQuestion > 1 ? sprintf("-%.1f", 1.0 / $correctAnswersPerQuestion) : '0')).');';
            }
          }
        }
        pg_query($_SESSION['psql'], $query);
      }
  }
?>

<body class='container-fluid'>

<nav class='navbar navbar-inverse'>
    <div class='container-fluid'>
      <div class='navbar-header'>
        <a href='/lk.php' class='navbar-brand'>ЛК ИК</a>
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
  <li><a href='vote.php'>Голосования</a></li>
  <li class='active'><a href='#'>Тесты</a></li>
</ul>

<div class='tab-content'>
  <div class='tab-pane active container'>
    <nav>
      <a href='#' class='btn btn-success' data-target='#addTestModal' data-toggle='modal' data-id='1'>Добавить тест</a>
    </nav>
    <main>
  
    <div class='list-group'>
        <?php
        $tests = pg_query($_SESSION['psql'], 'select T.id, T.text, T.passball, V.name from test T, vote V where V.id=T.vid and (V.day + 60)>'.date('z').' and V.year='.date('Y').' order by V.day, T.text;');
        if(pg_num_rows($tests) === 0) {
          echo '<p align=\'center\' class=\'list-group-item\'>Тестов нет</p>';
        } else {
          while($t = pg_fetch_row($tests)) {
            echo '<a href=\'?tid='.$t[0].'\' class=\'list-group-item list-group-item-action\'>'.$t[1].'<br><label class=\'label label-primary\'>'.$t[3].'</label></a>';
          }
        }
        ?>
    </div>

  </div>

  <!--Modal ADD TEST-->
  <div id='addTestModal' class='modal fade' role='dialog'>
          <div class='modal-dialog modal-lg'>
            <form class='panel panel-inverse' action='#' method='post' enctype='multipart/form-data'>
              <input type='hidden' name='action' value='addTest'/>
              <div class='panel-heading'>
                <button type='button' class='close' data-dismiss='modal' style='color:white;'>&times;</button>
                <h5 class='modal-title'>Новый тест</h5>
              </div>
              <div class='panel-body'>
                <div class='input-group'>
                  <span class='input-group-addon'>Тема:</span>
                  <input class='form-control' type='text' name='testTheme' required/>
                </div><br>
                <div class='input-group'>
                  <span class='input-group-addon'>Выборы:</span>
                  <select class='form-control' name='vid' required><option value='0' selected disabled></option>
                  <?php
                    $votes = pg_query($_SESSION['psql'], 'select V.id, V.name from vote V order by V.name;');
                    while($row = pg_fetch_row($votes)) {
                      echo '<option value=\''.$row[0].'\'>'.$row[1].'</option>';
                    }
                  ?></select>
                </div><br>
                <div class='input-group'>
                  <span class='input-group-addon'>Группа:</span>
                  <select class='form-control' name='testKlass[]' size='30' required multiple>
                  <?php
                    $posts = pg_query($_SESSION['psql'], 'select P.id, P.name from post P order by P.id;');
                    while($row = pg_fetch_row($posts)) {
                      echo '<option value=\'p'.$row[0].'\'>'.$row[1].'</option>';
                    }
                    $komissions = pg_query($_SESSION['psql'], 'select K.id, K.name from komission K order by K.name;');
                    while($row = pg_fetch_row($komissions)) {
                      echo '<option value=\'k'.$row[0].'\'>'.$row[1].'</option>';
                    }
                  ?></select>
                </div><br>
                <div class='btn btn-success' onclick='addQuestionInList();' id='btnAddQuestion'>Добавить вопрос</div>
                <hr>
                <div class='list-group' id='questionList'>

                </div>
              </div>
              <div class='panel-footer'>&nbsp;
                <button type='submit' class='btn btn-success' style='float:right;' id='btnSaveTest'>Сохранить тест</button>
              </div>
            </form>

            <script type='text/javascript'>
              var qid = 0;
      function addQuestionInList() {
        var answerCount = prompt('Количество вариантов ответа:', 4);
        if(answerCount===null || answerCount < 2)
          return;
        var x = "";
        for(var i=0;i<answerCount;i++)
          x+= "<br><div class='input-group'><span class='input-group-addon'><input type='checkbox' name='a_" + qid + "_" + i + "' value='" + i + "'/>&nbsp;</span><input class='form-control' type='text' name='t_" + qid + "_" + i + "' placeholder='Вариант ответа №" + (i+1) + "'/></div>";
        $('#questionList').append("<div class='list-group-item'><textarea class='form-control' rows='3' name='q_" + qid + "' placeholder='Текст вопроса №" + (qid+1) + "'></textarea>" + x + "</div>");
        qid++;
      }
<?php if(isset($_GET['tid'])) {
  $_tid = (int)$_GET['tid'];
  //$selectedTest = pg_fetch_row(pg_query($_SESSION['psql'], "select T.theme, T.vote, T.testfor from test T where T.id=".$_tid.";"));
  //$currectTestQuestions = pg_query($_SESSION['psql'], "select Q.id, Q.text, Q.type from question Q where Q.tid=".$_tid.";");
  //$questionList = "";
  //$_qid = 0;
  //while($row = pg_fetch_row($currectTestQuestions)) {
//    $questionAnswers = pg_query($_SESSION['psql'], "select A.id, A.text from answer A where A.qid=".$row[0].";");
    //$answers = "";
    //$i = 0;
    //while($answer = pg_fetch_row($questionAnswers)) {
//      $answers .= "<br><div class='input-group'><span class='input-group-addon'><input type='checkbox' name='a_".$_qid."_".$i."' value='".$i."'/>&nbsp;</span><input class='form-control' type='text' name='t_".$_qid."_".$i."' placeholder='Вариант ответа №".($i+1)."' value='".$answer[1]."'/></div>";
      //$i++;
    //}
    //$questionList .= "<div class='list-group-item'><textarea class='form-control' rows='3' name='q_".$_qid."' placeholder='Текст вопроса №".($_qid+1)."'>".$row[1]."</textarea>".$answers."</div>";
    //$_qid++;
  //}
?>

  ////$(document).ready(function(){
    ////$('#btnAddQuestion').addClass("hidden");
    ////$('#btnSaveTest').addClass("hidden");//DEBUG
    ////$('input[name=testTheme]').val("<?php //echo $selectedTest[0]; ?>");
    ////$('input[name=testVote]').val("<?php //echo $selectedTest[1]; ?>");
    ////$('input[name=testTestFor]').val("<?php //echo $selectedTest[2]; ?>");
    ////$('#questionList').html("<?php //echo $questionList; ?>");
    ////$('#addTestModal').modal({ show: 'true' }); });
<?php  } ?>

      </script>
          </div>
        </div>
        <!--/Modal-->


<?php include_once $_SERVER['DOCUMENT_ROOT'].'/footer.php'; } ?>
