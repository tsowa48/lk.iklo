<?php
$SUMM = 0.0;
foreach($data as &$d) {
  if($d['isstaffer'] === 1)
    continue;

  $FACIAL = $d['facial'];
  $FACIAL = ($FACIAL === 0 ? '' : $FACIAL);
  $sum = $d['payment'] + $d['prize'];
  $SUMM += $sum;
  $AWARD = sprintf("%.02f", $sum);
  $content .= $d['fio'].'; '.$FACIAL.';'.$AWARD.PHP_EOL;
}
$content .= 'ИТОГО;;'.$SUMM.PHP_EOL;
$content = mb_convert_encoding($content, 'windows-1251');
?>