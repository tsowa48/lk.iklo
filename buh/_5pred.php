<?php
foreach($data as &$d) {
  if($d['isstaffer'] === 1)
    continue;
  $FIO = explode(' ', $d['fio']);
  $FIO = $FIO[0].';'.$FIO[1].';'.$FIO[2];

  $FACIAL = $d['facial'];
  $FACIAL = ($FACIAL === 0 ? '' : $FACIAL);

  $AWARD = sprintf("%.02f", $d['prize']);

  $content .= $FACIAL.';'.$FIO.';'.$AWARD.PHP_EOL;
}
$content = mb_convert_encoding($content, 'windows-1251');
?>
