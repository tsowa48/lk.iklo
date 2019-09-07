<?php
function int1($money, $word) {
    $Low = array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять', 'десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семьнадцать', 'восемьнадцать', 'девятнадцать');
    $Ts = array('', 'десять', 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
    $Hs = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
    if ($money < 20) {
        $word .= $Low[$money];
    } else if ($money < 100) {
        $word .= $Ts[(int)(floor($money / 10.0))] . ' ' . int1($money - (int)(floor($money / 10.0)) * 10, $word);
    } else if ($money < 1000) {
        $word .= $Hs[(int)(floor($money / 100.0))] . ' ' . int1($money - (int)(floor($money / 100.0)) * 100, $word);
    } else if ($money < 1000000) {
        $ths = (int)(floor($money / 1000.0));
        $e = (($ths > 4 && $ths < 20) || ($ths - floor($ths / 10.0) * 10 < 1)) ? '' :($ths - floor($ths / 10.0) * 10 < 2 ? 'а' : ($ths - floor($ths / 10.0) * 10 < 5 ? 'и' : ''));
        $word .= str_replace('дведцать', 'двадцать', str_replace('два', 'две', str_replace('один', 'одна', int1($ths, '')))) . ' тысяч' . $e . ' ' . int1($money - (int)(floor($money / 1000.0)) * 1000, $word);
    } else if ($money < 1000000000) {
        $ths = (int)(floor($money / 1000000.0));
        $e = (($ths - floor($ths / 10.0) * 10 === 1) ? '' : ($ths - floor($ths / 10.0) * 10 < 5 ? 'а' : 'ов'));
        $word .= int1($ths, '') . ' миллион' . $e . ' ' . int1($money - (int)(floor($money / 1000000.0)) * 1000000, $word);
    } else {
        $word = $money;
    }
    return $word;
};
function money2str($money) {
    $rub = intval($money);
    $kop = ($money - $rub) * 100;
    $rub = $rub > 0 ? int1($rub, '') : 'ноль';
    return $rub.' руб. '.sprintf("%02d", $kop).' коп.';
};

$rows = "";
$i = 1;
$full_dop = 0.0;
foreach($data as &$d) {
    if($d['post'] === 'Председатель')
        continue;
    if($d['isstaffer'] === 1)
        continue;
    $tww = pg_fetch_row(pg_query($_SESSION['psql'], 'select (((select coalesce(sum(S1.finish-S1.start),0)-coalesce(sum(case when S1.start < 6 then (case when S1.finish < 7 then S1.finish - S1.start else 6 - S1.start end) when S1.finish>22 then (case when S1.start > 21 then S1.finish - S1.start else S1.finish - 22 end) end),0)
                                                    from schedule S1 where S1.vid=S.vid and S1.uid=S.uid and S1.day in (select doy from calendar where isholy=1)))) as holy, (select coalesce(sum(case when S2.start < 6 then (case when S2.finish < 7 then S2.finish - S2.start else 6 - S2.start end) when S2.finish>22 then (case when S2.start > 21 then S2.finish - S2.start else S2.finish - 22 end) end), 0) from schedule S2 where (S2.start < 6 or S2.finish > 22) and S2.vid=S.vid and S2.uid=S.uid) as night
                                                    from schedule S where S.vid='.$vid.' and S.uid='.$d['uid'].' group by S.vid, S.uid;'));//Двойная оплата
    $twice = (int)$tww[0] + (int)$tww[1];//Праздничные + ночные
    $once = $d['hours'] - $twice;//Одинарная оплата (кол-во часов)

    $summ = ($once + $twice * 2) * $d['price'] * $d['coef'];//Размер оплаты за часы
    ////$plus = $summ * $d['coef'];//Вознаграждение

    $full_dop += $summ;
    ////$full_sum += $plus;

    $row = '<w:tr wsp:rsidR="00792B7A" wsp:rsidRPr="000025C5" wsp:rsidTr="000025C5"><w:trPr><w:jc w:val="center"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="349" w:type="pct"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/></w:tcPr><w:p wsp:rsidR="003A00EF" wsp:rsidRPr="000025C5" wsp:rsidRDefault="003A00EF" wsp:rsidP="000025C5"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="Calibri"/><w:sz w:val="20"/><w:sz-cs w:val="20"/><w:lang w:val="EN-US"/></w:rPr></w:pPr><w:r wsp:rsidRPr="000025C5"><w:rPr><w:rFonts w:cs="Calibri"/><w:sz w:val="20"/><w:sz-cs w:val="20"/><w:lang w:val="EN-US"/></w:rPr><w:t>'.$i.'</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1445" w:type="pct"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/></w:tcPr><w:p wsp:rsidR="003A00EF" wsp:rsidRPr="000025C5" wsp:rsidRDefault="003A00EF" wsp:rsidP="000025C5"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="Calibri"/><w:sz w:val="20"/><w:sz-cs w:val="20"/><w:lang w:val="EN-US"/></w:rPr></w:pPr><w:r wsp:rsidRPr="000025C5"><w:rPr><w:rFonts w:cs="Calibri"/><w:sz w:val="20"/><w:sz-cs w:val="20"/><w:lang w:val="EN-US"/></w:rPr><w:t>'.$d['fio'].'</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1090" w:type="pct"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/></w:tcPr><w:p wsp:rsidR="003A00EF" wsp:rsidRPr="000025C5" wsp:rsidRDefault="003A00EF" wsp:rsidP="000025C5"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="Calibri"/><w:sz w:val="20"/><w:sz-cs w:val="20"/><w:lang w:val="EN-US"/></w:rPr></w:pPr><w:r wsp:rsidRPr="000025C5"><w:rPr><w:rFonts w:cs="Calibri"/><w:sz w:val="20"/><w:sz-cs w:val="20"/><w:lang w:val="EN-US"/></w:rPr><w:t>'.$d['post'].'</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="706" w:type="pct"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/></w:tcPr><w:p wsp:rsidR="003A00EF" wsp:rsidRPr="000025C5" wsp:rsidRDefault="003A00EF" wsp:rsidP="000025C5"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="Calibri"/><w:sz w:val="20"/><w:sz-cs w:val="20"/><w:lang w:val="EN-US"/></w:rPr></w:pPr><w:r wsp:rsidRPr="000025C5"><w:rPr><w:rFonts w:cs="Calibri"/><w:sz w:val="20"/><w:sz-cs w:val="20"/><w:lang w:val="EN-US"/></w:rPr><w:t>'.sprintf("%.02f", $summ).'</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/></w:tcPr><w:p wsp:rsidR="003A00EF" wsp:rsidRPr="000025C5" wsp:rsidRDefault="000025C5" wsp:rsidP="000025C5"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="Calibri"/><w:sz w:val="20"/><w:sz-cs w:val="20"/><w:lang w:val="EN-US"/></w:rPr></w:pPr><w:r wsp:rsidRPr="000025C5"><w:rPr><w:rFonts w:cs="Calibri"/><w:sz w:val="20"/><w:sz-cs w:val="20"/><w:lang w:val="EN-US"/></w:rPr><w:t></w:t></w:r></w:p></w:tc></w:tr>';
    $rows .= $row;
    $i++;
}

$makeFIO = explode(' ', $_SESSION['currentUser']->fio);
$myPost = pg_fetch_row(pg_query($_SESSION['psql'], 'select P.name, P.type from post P,pu PU where P.id=PU.pid and PU.uid='.$_SESSION['currentUser']->id.' order by P.type asc;'))[0];

$content = str_replace('{ROWS}', $rows, $content);
$content = str_replace('{IK_NAME}', $ikName, $content);
$content = str_replace('{FULL_SUM}', sprintf("%.02f", $full_dop), $content);
$content = str_replace('{ISSUED}', sprintf("%.02f", $full_dop), $content);
$content = str_replace('{NOT_ISSUED}', sprintf("%.02f", 0.00), $content);
$content = str_replace('{DEPOSITED}', sprintf("%.02f", 0.00), $content);
$content = str_replace('{IK_SMALL}', 'комиссии', $content);
$content = str_replace('{DISTRIBUTOR}', '_____________________', $content);//($_SESSION['currentUser']->ik === 0 ? 'председатель УИК' : 'бухгалтер ТИК')
$content = str_replace('{DISTRIBUTOR_FIO}', '_____________________', $content);// ($_SESSION['currentUser']->ik === 0 ? ($predFio[0].' '.mb_substr($predFio[1], 0, 1).'.'.mb_substr($predFio[2], 0, 1).'.') : '_____________________')
$content = str_replace('{MAKE}', mb_strtolower($myPost), $content);//($_SESSION['currentUser']->ik === 0 ? 'председатель комиссии' : 'бухгалтер комиссии')
$content = str_replace('{MAKE_FIO}',  $makeFIO[0].' '.mb_substr($makeFIO[1], 0, 1).'.'.mb_substr($makeFIO[2], 0, 1).'.', $content);//'_____________________'
$content = str_replace('{CHECK}', '_____________________', $content); //($_SESSION['currentUser']->ik === 0 ? 'бухгалтер комиссии' : 'председатель комиссии')
$content = str_replace('{CHECK_FIO}', '_____________________', $content); //($_SESSION['currentUser']->ik === 0 ? '_____________________' : ($predFio[0].' '.mb_substr($predFio[1], 0, 1).'.'.mb_substr($predFio[2], 0, 1).'.'))
$content = str_replace('{SUM_PROPIS}', money2str(sprintf("%.02f", $full_dop)), $content);
?>
