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
  <li class='active'><a href='#'>Часы работы (не трогать)</a></li>
  <?php $_count = (int)pg_fetch_row(pg_query($_SESSION['psql'], 'select count(K.id) from komission K where K.parent='.$_SESSION['currentUser']->kid.';'))[0];
    if($_count > 0)
      echo '<li><a href=\'predik.php\'>Вознаграждение ИК</a></li>';
  ?>
  <li><a href='reports.php'><b>Отчеты</b></a></li>
</ul>

<?php
  $komission = pg_query($_SESSION['psql'], 'select U.id, U.fio, P.name from users U, post P, pu PU where U.id=PU.uid and PU.pid=P.id and P.type=0 and (U.isstaffer is null or U.isstaffer=0) and U.kid='.$_SESSION['currentUser']->kid.' order by P.id, U.fio;');
  $header = '';
  $uids = array();
  while($k = pg_fetch_row($komission)) {
    $uids[] = (int)$k[0];
    $header .= '<th>'.str_replace(' ', '<br>', $k[1]).'</th>';
  }
  $calendar = pg_query($_SESSION['psql'], 'select doy from calendar where isholy > 0 and year='.date('Y').';');
  $cal = array();
  while($row = pg_fetch_row($calendar)) {
    $cal[] = (int)$row[0];
  }
  $schedules = pg_query($_SESSION['psql'], 'select U.id, S.day, S.start, S.finish from schedule S, users U where S.vid='.$vid.' and S.uid=U.id and U.kid='.$_SESSION['currentUser']->kid.';');
  $schedule = array();
  while($s = pg_fetch_row($schedules)) {
    $schedule[] = array('uid' => (int)$s[0], 'day' => (int)$s[1], 'start' => (int)$s[2], 'finish' => (int)$s[3]);
  }
  $dates = pg_fetch_row(pg_query($_SESSION['psql'], 'select first, last from kv where vid='.$vid.' and kid='.$_SESSION['currentUser']->kid.';'));
?>

<div class='tab-content'>
  <div class='tab-pane active container-fluid'>
    <table class='table table-striped table-bordered table-condensed'>
      <tr><th style='min-width:100px;'>День</th><?=$header ?></tr>
      <?php
      setlocale(LC_ALL, array('ru_RU', 'ru_RU.utf-8'));
      $firstInYear = strtotime('-1 day', strtotime(date('Y').'-01-01'));
      $first = (int)$dates[0];
      $last = (int)$dates[1];
      $curDay = $first;

      while($curDay < $last + 1) {
        $isHoly = in_array($curDay, $cal);
        echo '<tr'.($isHoly ? ' style=\'background-color:pink;\'' : '').' doy=\''.$curDay.'\'><td>'.strftime('%02d %b %Y г.', strtotime('+'.$curDay.' day', $firstInYear)).'</td>';

        for($u = 0; $u < count($uids); ++$u) {
          $isDayAdded = false;
          for($s = 0; $s < count($schedule); ++$s) {
            if($uids[$u] === $schedule[$s]['uid'] && $curDay === $schedule[$s]['day']) {
              echo '<td uid=\''.$uids[$u].'\'><div '.($isHoly ? 'style=\'background-color:pink;\' ' : '').'class=\'btn btn-small\' onclick=\'change(this);\'>'.(sprintf('%02d', $schedule[$s]['start']).':00 - '.sprintf('%02d', $schedule[$s]['finish']).':00').'</div></td>';
              $isDayAdded = true;
            }
          }
          if(!$isDayAdded) {
            echo '<td uid=\''.$uids[$u].'\'><div '.($isHoly ? 'style=\'background-color:pink;\' ' : '').'class=\'btn btn-small\' onclick=\'change(this);\'>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>';
          }
        }
        echo '</tr>'.PHP_EOL;
        $curDay++;
      }
      ?>
    </table>
  </div>
</div>


<div class='clock' style='display:none; background:rgb(243,243,243); border:1px solid grey;'>
  <style> table * {font-size:12px;} .clock table {width:100%;} .clock  td { cursor: pointer; border: 1px solid grey; padding: 2px; border-condensed: border-condensed;}
  .clock td:hover:not(.dis) {
    background: rgb(200,200,200);
  }
  .hour {
    background: rgb(70,165,70);
  }
  .dis {
    background: red;
  }
  </style>
  <table id='tblClock'></table>
  <div style='background:rgb(243,243,243);padding:2px;'>
    <span id='time' style='font-size:11px;'>xx:xx - xx:xx</span>
    <div class='btn btn-small btn-success' onclick='save();' style='padding: 2px;font-size:11px'>Сохранить</div>
  </div>
</div>


<script type="text/javascript">
  function change(t) {
    var doy = $(t).parent().parent().attr('doy');
    var uid = $(t).parent().attr('uid');
    $('.hour').removeClass('hour');
    $('#time').text('xx:xx - xx:xx');
    $('#time').removeAttr('start');
    $('#time').removeAttr('finish');
    $.ajax({
      url: 'ajax/getSh.php',
      data: 'u='+uid+'&d='+doy+'&v=<?=$vid ?>',
      //async: false,
      success: function(d, s, j) {
        d = JSON.parse(d);
        var jsize = d.length;
        var k = [];
        var tbl = '<tr>';
        for(var q=0; q<24; ++q) {
          var isExist = false;
          for(var g=0;g < jsize;++g) {
            if(q >= d[g]['s'] && q < d[g]['f']) {
              isExist = true;
              break;
            }
          }
          tbl += ('<td' + (isExist ? ' class=\'dis\'' : ' onclick=\'setHour(this);\')') + '>' + q + '</td>') + ((q+1)%6===0 ? '</tr>' : '');
        }
        $('#tblClock').html(tbl);
        $('.clock').attr('uid', uid);
        $('.clock').attr('doy', doy);
        $('.clock').attr('vid', <?=$vid ?>);
        $('.clock').css({top: $(t).position().top + $(t).height() + 4, left: $(t).position().left, position:'absolute'});
        $('.clock').show();
      }
    });
  }

  function setHour(t) {
    $(t).toggleClass('hour');
    var L = $('.hour').length;
    if(L > 2) {
      $('.hour').removeClass('hour');
      $('#time').text('xx:xx - xx:xx');
      $('#time').removeAttr('start');
      $('#time').removeAttr('finish');
      return;
    }
    if(L < 3 && L > 0) {
      var h0 = parseInt($('.hour').get(0).innerHTML);
      var h1 = (L === 2 ? parseInt($('.hour').get(1).innerHTML) : h0) + 1;
      if(h1 < h0) {
        var _h = h0;
        h0 = h1;
        h1 = _h;
      }
      $('#time').text((h0<10?'0':'') + h0 + ':00 - '+ (h1<10?'0':'') + h1 + ':00');
      $('#time').attr('start', h0);
      $('#time').attr('finish', h1);
    }
  }

  function save() {
    var uid=$('.clock').attr('uid');
    var doy=$('.clock').attr('doy');
    var vid=$('.clock').attr('vid');
    var start=$('#time').attr('start');
    var finish=$('#time').attr('finish');

    //alert('uid=' + uid + ', doy=' + doy + ', vid=' + vid + ', s=' + start + ', f=' + finish);
    $.ajax({
      url: 'ajax/saveSh.php',
      data: 'u='+uid+'&d='+doy+'&v='+vid+'&s='+start+'&f='+finish,
      success: function(d,s,j) {
        var elem = document.elementFromPoint($('.clock').position().left + 6 - window.pageXOffset, $('.clock').position().top - 6 - window.pageYOffset);
        elem.innerHTML =((start<10?'0':'') + start + ':00 - '+ (finish<10?'0':'') + finish + ':00');
        $('.clock').hide();
      }
    });
  }
  </script>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/footer.php'; } ?>
