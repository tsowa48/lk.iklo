<?php
$psql = pg_connect('host=localhost port=5432 dbname=gas user=iklo password=48s000');
$html = file_get_contents('http://www.consultant.ru/law/ref/calendar/proizvodstvennye/'.date('Y').'/');
$months = explode('<table class="cal">', $html);
$i = 1;
$sql = '';
for($m = 1; $m < 13; ++$m) {
  $months[$m] = substr($months[$m], strpos($months[$m], '<tbody>'), strpos($months[$m], '</tbody>') - strpos($months[$m], '<tbody>'));
  $days = explode('</td>', str_replace('</tr><tr>', '', $months[$m]));
  for($d = 0; $d < count($days) - 1; ++$d) {
    if(strpos($days[$d], 'inactively'))
      continue;
    $sql .= 'insert into calendar(doy,day,isholy,year) values ('.($i).',\''.sprintf('%02d', explode('">', $days[$d])[1]).'.'.sprintf('%02d', $m).'.'.date('Y').'\','.(strpos($days[$d], 'weekend') ? '1' : '0').', '.date('Y').');';
    $i++;
  }
}
pg_query($psql, $sql);
echo 'OK';
pg_close($psql);
unset($psql);
?>
