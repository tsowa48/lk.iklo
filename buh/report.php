<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/user.php';
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
  if(!isset($_SESSION['psql']) || !$_SESSION['psql']) {
    $psql = pg_connect('host=localhost port=5432 dbname=gas user=iklo password=48s000');
    $_SESSION['psql'] = $psql;
  }
  $vid = $_SESSION['vid'];
  $v = '';
  $vote = pg_query($_SESSION['psql'], 'select V.id, V.name, V.day from vote V where V.id='.$vid);
  if(pg_num_rows($vote)===0) {
    header('Location: /buh/');
    die();
  } else {
    $v = pg_fetch_row($vote);
    $voteName = $v[1];
  }
  //$_count = (int)pg_fetch_row(pg_query($_SESSION['psql'], 'select count(K.id) from komission K where K.parent='.$_SESSION['currentUser']->kid.';'))[0];
  
  $id = $_GET['id'];// ID отчета
  $months = array('', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
  $monthsNom = array('', 'январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь');
  $pageBreak = '<w:p w:rsidR="004A5C90" w:rsidRDefault="004A5C90"><w:pPr><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/><w:lang w:val="en-US"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/><w:lang w:val="en-US"/></w:rPr><w:br w:type="page"/></w:r></w:p>';
  $voteName = str_replace('Дополнительные', 'дополнительных', $voteName);
  $voteName = str_replace('выборы', 'выборах', $voteName);
  $voteName = str_replace('Выборы', 'выборах', $voteName);
  setlocale(LC_ALL, array('ru_RU', 'ru_RU.utf-8'));
  $firstInYear = strtotime('-1 day', strtotime(date('Y').'-01-01'));

  $komissions = pg_query($_SESSION['psql'], 'select U.id, U.fio, P.name, U.isstaffer from users U, pu PU, post P where U.id=PU.uid and PU.pid=P.id and P.type=0 and U.kid='.$_SESSION['currentUser']->kid.' order by P.id asc, U.fio asc;');
  $komission = array();
  $predFio = '';
  $zamFio = '';
  $secretaryFio = '';
  while($k = pg_fetch_row($komissions)) {
    $komission[] = array('uid' => (int)$k[0], 'fio' => (string)$k[1], 'post' => (string)$k[2], 'isstaffer' => (int)$k[3]);
    if(strcmp($k[2], 'Председатель') === 0)
      $predFio = explode(' ', $k[1]);
    else if(strcmp($k[2], 'Заместитель председателя') === 0)
      $zamFio = explode(' ', $k[1]);
    else if(strcmp($k[2], 'Секретарь') === 0)
      $secretaryFio = explode(' ', $k[1]);
  }
  $doys = pg_query($_SESSION['psql'], 'select doy from calendar where isholy > 0 and year='.date('Y').' order by doy;');
  $cal = array();
  while($doy = pg_fetch_row($doys)) {
    $cal[] = (int)$doy[0];
  }
  $dates = pg_fetch_row(pg_query($_SESSION['psql'], 'select first, last, K.name from kv, komission K where K.id=kid and vid='.$vid.' and kid='.$_SESSION['currentUser']->kid.';'));
  $first = (int)$dates[0];
  $last = (int)$dates[1];
  $ikName = $dates[2];
  $period = $monthsNom[(int)strftime('%m', strtotime('+'.$first.' day', $firstInYear))].((int)strftime('%Y', strtotime('+'.$first.' day', $firstInYear)) !== (int)strftime('%Y', strtotime('+'.$last.' day', $firstInYear)) ? (int)strftime('%Y', strtotime('+'.$first.' day', $firstInYear)).' года' : '').
            ' – '.$monthsNom[(int)strftime('%m', strtotime('+'.$last.' day', $firstInYear))].' '.(int)strftime('%Y', strtotime('+'.$last.' day', $firstInYear)).' года';
  
  $reportName = 'Отчет';
  $data = array();
  switch($id) {
    case 1:
      $reportName = 'График работы';
      for($i=0; $i<count($komission); ++$i) {
        if($komission[$i]['isstaffer'] === 1)
          unset($komission[$i]);
      }
      $result = pg_query($_SESSION['psql'], 'select S.uid, S.day, (S.finish - S.start) as hours from schedule S, kv KV where KV.vid=S.vid and KV.kid='.$_SESSION['currentUser']->kid.' and S.vid='.$vid.' order by S.day;');
      while($row = pg_fetch_row($result)) {
        $data[] = array('uid' => (int)$row[0], 'day' => (int)$row[1], 'hours' => (int)$row[2]);
      }
      break;
    case 2:
      $reportName = 'Сведения';
      for($i=0; $i<count($komission); ++$i) {
        if($komission[$i]['isstaffer'] === 1)
          unset($komission[$i]);
      }
      $result = pg_query($_SESSION['psql'], 'select S.uid, S.day, S.start, S.finish from schedule S, kv KV where S.finish is not null and KV.vid=S.vid and KV.kid='.$_SESSION['currentUser']->kid.' and S.vid='.$vid.' order by S.day;');
      while($row = pg_fetch_row($result)) {
        $data[] = array('uid' => (int)$row[0], 'day' => (int)$row[1], 'start' => (int)$row[2], 'finish' => (int)$row[3]);
      }
      break;
    case 3:
      $reportName = 'Расчетная ведомость';
      $result = pg_query($_SESSION['psql'], 'select U.id, U.fio, P.name, B.price, coalesce((select R.coef from rate R where R.uid=PU.uid and R.vid=B.vid), 0.0), coalesce((select sum(S.finish-S.start) from schedule S where S.uid=PU.uid and S.vid=B.vid), 0.0), U.isstaffer, U.facial from bet B, pu PU, users U, post P, komission K where U.id=PU.uid and P.id=PU.pid and B.kid=U.kid and B.pid=PU.pid and B.kid=K.id and '.(isset($_GET['t']) && $_GET['t'] === 'pred' ? 'P.name=\'Председатель\' and K.parent=' : 'K.id=').$_SESSION['currentUser']->kid.' and B.vid='.$vid.' order by P.id, U.fio;');
      while($row = pg_fetch_row($result)) {
        $data[] = array('uid' => (int)$row[0], 'fio' => $row[1], 'post' => $row[2], 'price' => (double)$row[3], 'coef' => (double)$row[4], 'hours' => (int)$row[5], 'isstaffer' => (int)$row[6], 'facial' => $row[7]);
      }
      break;
    case 4:
      $reportName = 'Дополнительная оплата';
      if($_GET['t'] === 'award') {
        $reportName .= ' (за активную работу)';
        $result = pg_query($_SESSION['psql'], 'select U.id, U.fio, P.name, B.price, coalesce((select R.coef from rate R where R.uid=PU.uid and R.vid=B.vid), 0.0), coalesce((select sum(S.finish-S.start) from schedule S where S.uid=PU.uid and S.vid=B.vid), 0.0), U.isstaffer from bet B, pu PU, users U, post P where U.id=PU.uid and P.id=PU.pid and B.kid=U.kid and B.pid=PU.pid and B.kid='.$_SESSION['currentUser']->kid.' and B.vid='.$vid.' order by P.id, U.fio;');
        while($row = pg_fetch_row($result)) {
          $data[] = array('uid' => (int)$row[0], 'fio' => $row[1], 'post' => $row[2], 'price' => (double)$row[3], 'coef' => (double)$row[4], 'hours' => (int)$row[5], 'isstaffer' => (int)$row[6]);
        }
      } elseif($_GET['t'] === 'fee') {
        $reportName .= ' (вознаграждение)';
        $result = pg_query($_SESSION['psql'], 'select U.id, U.fio, P.name, B.price, coalesce((select R.coef from rate R where R.uid=PU.uid and R.vid=B.vid), 0.0), coalesce((select sum(S.finish-S.start) from schedule S where S.uid=PU.uid and S.vid=B.vid), 0.0), U.isstaffer from bet B, pu PU, users U, post P where U.id=PU.uid and P.id=PU.pid and B.kid=U.kid and B.pid=PU.pid and B.kid='.$_SESSION['currentUser']->kid.' and B.vid='.$vid.' order by P.id, U.fio;');
        while($row = pg_fetch_row($result)) {
          $data[] = array('uid' => (int)$row[0], 'fio' => $row[1], 'post' => $row[2], 'price' => (double)$row[3], 'coef' => (double)$row[4], 'hours' => (int)$row[5], 'isstaffer' => (int)$row[6]);
        }
      } elseif($_GET['t'] === 'pred') {
        $reportName .= ' (вознаграждение)';
        $result = pg_query($_SESSION['psql'], 'select U.id, U.fio, K.name, B.price, coalesce((select R.coef from rate R where R.uid=PU.uid and R.vid=B.vid), 0.0), coalesce((select sum(S.finish-S.start) from schedule S where S.uid=PU.uid and S.vid=B.vid), 0.0), U.isstaffer from bet B, pu PU, users U, post P, komission K where K.id=B.kid and P.name=\'Председатель\' and U.id=PU.uid and P.id=PU.pid and B.kid=U.kid and B.pid=PU.pid and K.parent='.$_SESSION['currentUser']->kid.' and B.vid='.$vid.' order by P.id, U.fio;');
        while($row = pg_fetch_row($result)) {
          $data[] = array('uid' => (int)$row[0], 'fio' => $row[1], 'komission' => $row[2], 'price' => (double)$row[3], 'coef' => (double)$row[4], 'hours' => (int)$row[5], 'isstaffer' => (int)$row[6]);
        }
      }
      break;
    case 5:
      $reportName = 'Реестр';
      $_count = 0;
      if($_GET['t'] === 'pred')
        $_count = (int)pg_fetch_row(pg_query($_SESSION['psql'], 'select count(K.id) from komission K where K.parent='.$_SESSION['currentUser']->kid.';'))[0];
      $result = pg_query($_SESSION['psql'], 'select U.id, U.fio, U.facial, K.name, U.isstaffer, coalesce((select R.coef from rate R where R.uid=U.id and S.vid=R.vid), 0.0), B.price, sum(S.finish-S.start), P.name from users U, komission K, bet B, schedule S, pu PU, post P where U.kid=K.id and S.vid='.$vid.' and PU.uid=U.id and PU.pid=P.id and P.type=0 and B.pid=P.id and B.kid=U.kid and B.vid=S.vid and S.uid=U.id and ('.($_count > 0 ? 'P.name=\'Председатель\' and K.parent=' : 'K.id=').$_SESSION['currentUser']->kid.') and U.facial!=\'\' group by 1,2,3,4,5,6,7,9 order by K.name asc, U.fio asc;');
      while($row = pg_fetch_row($result)) {
        $tww = pg_fetch_row(pg_query($_SESSION['psql'], 'select (((select coalesce(sum(S1.finish-S1.start),0)-coalesce(sum(case when S1.start < 6 then (case when S1.finish < 7 then S1.finish - S1.start else 6 - S1.start end) when S1.finish>22 then (case when S1.start > 21 then S1.finish - S1.start else S1.finish - 22 end) end),0)
                                                        from schedule S1 where S1.vid=S.vid and S1.uid=S.uid and S1.day in (select doy from calendar where isholy=1 and year='.date('Y').')))) as holy, (select coalesce(sum(case when S2.start < 6 then (case when S2.finish < 7 then S2.finish - S2.start else 6 - S2.start end) when S2.finish>22 then (case when S2.start > 21 then S2.finish - S2.start else S2.finish - 22 end) end), 0) from schedule S2 where (S2.start < 6 or S2.finish > 22) and S2.vid=S.vid and S2.uid=S.uid) as night
                                                        from schedule S where S.vid='.$vid.' and S.uid='.$row[0].' group by S.vid, S.uid;'));//Двойная оплата
        $twice = (int)$tww[0] + (int)$tww[1];//Праздничные + ночные
        $once = (int)$row[7] - $twice;//Одинарная оплата (кол-во часов)
        
        $summ = ($once + $twice * 2) * (double)$row[6];//Размер оплаты за часы
        $plus = $summ * (double)$row[5];//Вознаграждение
        //$summ += $plus;
        $data[] = array('uid' => (int)$row[0], 'fio' => $row[1], 'facial' => $row[2], 'komission' => $row[3], 'payment' => (double)$summ, 'prize' => (double)$plus, 'isstaffer' => (int)$row[4], 'post' => $row[8]);
      }
      break;
    default:
      $id = 0;
      $reportName = 'Отчет';
  }
  if($_GET['f'] === 'doc') {
    $file = $_SERVER['DOCUMENT_ROOT'].'/buh/report/'.$id.(isset($_GET['t']) ? ($_GET['t'] === 'all' ? 'a' : ($_GET['t'] === 'month' ? 'm' : ($_GET['t'] === 'fee' ? 'f' :  ($_GET['t'] === 'pred' ? 'p' : 'w')))) : '').'.xml';// all=a, month=m, fee=f, award=w, pred=p
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$reportName.(isset($_GET['t']) ? ($_GET['t'] === 'all' ? ' (за весь период)' : ($_GET['t'] === 'month' ? ' (по месяцам)' : '')) : '').'.doc"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    $content = file_get_contents($file);

    include_once('_'.$id.(isset($_GET['t']) ? ($_GET['t'] === 'all' ? 'a' : ($_GET['t'] === 'month' ? 'm' : ($_GET['t'] === 'fee' ? 'f' :  ($_GET['t'] === 'pred' ? 'p' : 'w')))) : '').'.php');// all=a, month=m, fee=f, award=w, pred=p

    //$content = str_replace('{IK}', $ikNum, $content);
    $content = str_replace('{VOTE}', $voteName, $content);
    $content = str_replace('{PERIOD}', $period, $content);
    $content = str_replace('{PREDFIO}', $predFio[0].' '.mb_substr($predFio[1], 0, 1).'.'.mb_substr($predFio[2], 0, 1).'.', $content);
    $content = str_replace('{ZAMFIO}', $zamFio[0].' '.mb_substr($zamFio[1], 0, 1).'.'.mb_substr($zamFio[2], 0, 1).'.', $content);
    $content = str_replace('{SECRETARYFIO}', $secretaryFio[0].' '.mb_substr($secretaryFio[1], 0, 1).'.'.mb_substr($secretaryFio[2], 0, 1).'.', $content);

    header('Content-Length: '.strlen($content));
    echo $content;
  } else if($_GET['f'] === 'csv') {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$reportName.(isset($_GET['t']) ? ' ('.$_GET['t'].')' : '').'.csv"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    $content = '';

    include_once('_'.$id.(isset($_GET['t']) ? $_GET['t'] : '').'.php');

    header('Content-Length: '.strlen($content));
    echo $content;
  }
} ?>
