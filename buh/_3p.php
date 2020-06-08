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

$rows = '';
$i = 1;
$sum_7 = 0.0;
$sum_8 = 0.0;
$sum_9 = 0.0;
$sum_11 = 0.0;
$sum_12 = 0.0;
$sum_13 = 0.0;
$sum_14 = 0.0;

$predFullFio = '';
foreach($data as &$d) {
    if($d['isstaffer'] === 1)
        continue;
    $haveFacial = $d['facial'] > 0;
    $tww = pg_fetch_row(pg_query($_SESSION['psql'], 'select (((select coalesce(sum(S1.finish-S1.start),0)-coalesce(sum(case when S1.start < 6 then (case when S1.finish < 7 then S1.finish - S1.start else 6 - S1.start end) when S1.finish>22 then (case when S1.start > 21 then S1.finish - S1.start else S1.finish - 22 end) end),0)
                                                    from schedule S1 where S1.vid=S.vid and S1.uid=S.uid and S1.day in (select doy from calendar where isholy=1)))) as holy, (select coalesce(sum(case when S2.start < 6 then (case when S2.finish < 7 then S2.finish - S2.start else 6 - S2.start end) when S2.finish>22 then (case when S2.start > 21 then S2.finish - S2.start else S2.finish - 22 end) end), 0) from schedule S2 where (S2.start < 6 or S2.finish > 22) and S2.vid=S.vid and S2.uid=S.uid) as night
                                                    from schedule S where S.vid='.$vid.' and S.uid='.$d['uid'].' group by S.vid, S.uid;'));//Двойная оплата
    $twice = (int)$tww[0] + (int)$tww[1];//Праздничные + ночные
    $once = $d['hours'] - $twice;//Одинарная оплата (кол-во часов)

    $coef = $d['coef'];
    $summ = ($once + $twice * 2) * $d['price'];//Размер оплаты за часы
    $plus = $summ * $coef;//Вознаграждение

    $coef = (string)$coef;//sprintf("%.0".(strlen($coef) + 1)."F", $coef);//FORMAT coef
    if(strlen($coef)===3) {
        $coef .= '0';
    } else if(strlen($coef)===1) {
        $coef .= ',00';
    }

    $sum_7 += $once;
    $sum_8 += $twice;
    $sum_9 += $summ;
    $sum_11 += $plus;
    $sum_12 += ($summ + $plus);
    if($haveFacial)
        $sum_13 += ($plus);
    else
        $sum_14 += ($plus);

    $cell1 = '<w:tc><w:tcPr><w:tcW w:w="221" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-142" w:right="-89"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.$i.'</w:t></w:r></w:p></w:tc>';
    $cell2 = '<w:tc><w:tcPr><w:tcW w:w="600" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-127" w:right="-94"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.$d['post'].'</w:t></w:r></w:p></w:tc>';
    $cell3 = '<w:tc><w:tcPr><w:tcW w:w="840" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-67"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.' '.'</w:t></w:r></w:p></w:tc>';
    $cell4 = '<w:tc><w:tcPr><w:tcW w:w="897" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-41" w:right="-7"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.' '.'</w:t></w:r></w:p></w:tc>';
    $cell5 = '<w:tc><w:tcPr><w:tcW w:w="749" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-65" w:right="-142"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.' '.'</w:t></w:r></w:p></w:tc>';
    $cell6 = '<w:tc><w:tcPr><w:tcW w:w="939" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-147" w:right="-74"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.$d['price'].'</w:t></w:r></w:p></w:tc>';
    $cell7 = '<w:tc><w:tcPr><w:tcW w:w="973" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.$once.'</w:t></w:r></w:p></w:tc>';
    $cell8 = '<w:tc><w:tcPr><w:tcW w:w="982" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-114" w:right="-57"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.$twice.'</w:t></w:r></w:p></w:tc>';
    $cell9 = '<w:tc><w:tcPr><w:tcW w:w="1011" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-103" w:right="-34"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.sprintf("%.02f", $summ).'</w:t></w:r></w:p></w:tc>';
    $cell10 = '<w:tc><w:tcPr><w:tcW w:w="896" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-38" w:right="-133"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.$coef.'</w:t></w:r></w:p></w:tc>';
    $cell11 = '<w:tc><w:tcPr><w:tcW w:w="449" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-52" w:right="-86"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.sprintf("%.02f", $plus).'</w:t></w:r></w:p></w:tc>';
    $cell12 = '<w:tc><w:tcPr><w:tcW w:w="636" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-69" w:right="-68"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.sprintf("%.02f", $summ + $plus).'</w:t></w:r></w:p></w:tc>';
    $cell13 = '<w:tc><w:tcPr><w:tcW w:w="861" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-44" w:right="-75"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.($haveFacial ? sprintf("%.02f", $plus) : ' ').'</w:t></w:r></w:p></w:tc>';
    $cell14 = '<w:tc><w:tcPr><w:tcW w:w="511" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-39" w:right="-47"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.($haveFacial ? ' ' : sprintf("%.02f", $plus)).'</w:t></w:r></w:p></w:tc>';
    $cell15 = '<w:tc><w:tcPr><w:tcW w:w="584" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-87" w:right="-131"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.' '.'</w:t></w:r></w:p></w:tc>';
    $cell16 = '<w:tc><w:tcPr><w:tcW w:w="519" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-142" w:right="-89"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.$d['fio'].'</w:t></w:r></w:p></w:tc>';

    $row = $cell1.$cell2.$cell3.$cell4.$cell5.$cell6.$cell7.$cell8.$cell9.$cell10.$cell11.$cell12.$cell13.$cell14.$cell15.$cell16;
    $rows .= '<w:tr wsp:rsidR="00DB0A3B" wsp:rsidRPr="003D601A" wsp:rsidTr="003D601A">'.$row.'</w:tr>';
    $i++;
}
$cell1 = '<w:tc><w:tcPr><w:tcW w:w="221" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-142" w:right="-89"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.' '.'</w:t></w:r></w:p></w:tc>';
$cell2 = '<w:tc><w:tcPr><w:tcW w:w="600" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-127" w:right="-94"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.' '.'</w:t></w:r></w:p></w:tc>';
$cell3 = '<w:tc><w:tcPr><w:tcW w:w="840" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-67"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.' '.'</w:t></w:r></w:p></w:tc>';
$cell4 = '<w:tc><w:tcPr><w:tcW w:w="897" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-41" w:right="-7"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.' '.'</w:t></w:r></w:p></w:tc>';
$cell5 = '<w:tc><w:tcPr><w:tcW w:w="749" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-65" w:right="-142"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.'0,00'.'</w:t></w:r></w:p></w:tc>';
$cell6 = '<w:tc><w:tcPr><w:tcW w:w="939" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-147" w:right="-74"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.' '.'</w:t></w:r></w:p></w:tc>';
$cell7 = '<w:tc><w:tcPr><w:tcW w:w="973" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.$sum_7.'</w:t></w:r></w:p></w:tc>';
$cell8 = '<w:tc><w:tcPr><w:tcW w:w="982" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-114" w:right="-57"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.$sum_8.'</w:t></w:r></w:p></w:tc>';
$cell9 = '<w:tc><w:tcPr><w:tcW w:w="1011" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-103" w:right="-34"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.sprintf("%.02f", $sum_9).'</w:t></w:r></w:p></w:tc>';
$cell10 = '<w:tc><w:tcPr><w:tcW w:w="896" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-38" w:right="-133"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.' '.'</w:t></w:r></w:p></w:tc>';
$cell11 = '<w:tc><w:tcPr><w:tcW w:w="449" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-52" w:right="-86"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.sprintf("%.02f", $sum_11).'</w:t></w:r></w:p></w:tc>';
$cell12 = '<w:tc><w:tcPr><w:tcW w:w="636" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-69" w:right="-68"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.sprintf("%.02f", $sum_12).'</w:t></w:r></w:p></w:tc>';
$cell13 = '<w:tc><w:tcPr><w:tcW w:w="861" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-44" w:right="-75"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.sprintf("%.02f", $sum_13).'</w:t></w:r></w:p></w:tc>';
$cell14 = '<w:tc><w:tcPr><w:tcW w:w="511" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-39" w:right="-47"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.sprintf("%.02f", $sum_14).'</w:t></w:r></w:p></w:tc>';
$cell15 = '<w:tc><w:tcPr><w:tcW w:w="584" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-87" w:right="-131"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.' '.'</w:t></w:r></w:p></w:tc>';
$cell16 = '<w:tc><w:tcPr><w:tcW w:w="519" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="007E6A48" wsp:rsidRPr="003D601A" wsp:rsidRDefault="007E6A48" wsp:rsidP="003D601A"><w:pPr><w:spacing w:after="0" w:line="240" w:line-rule="auto"/><w:ind w:left="-142" w:right="-89"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr></w:pPr><w:r wsp:rsidRPr="003D601A"><w:rPr><w:rFonts w:ascii="Times New Roman" w:fareast="Calibri" w:h-ansi="Times New Roman"/><wx:font wx:val="Times New Roman"/><w:sz w:val="14"/><w:sz-cs w:val="14"/></w:rPr><w:t>'.' '.'</w:t></w:r></w:p></w:tc>';
$row = $cell1.$cell2.$cell3.$cell4.$cell5.$cell6.$cell7.$cell8.$cell9.$cell10.$cell11.$cell12.$cell13.$cell14.$cell15.$cell16;
$rows .= '<w:tr wsp:rsidR="00DB0A3B" wsp:rsidRPr="003D601A" wsp:rsidTr="003D601A">'.$row.'</w:tr>';

//$ikName = str_replace('ая', 'ой', $ikName);
//$ikName = str_replace('комиссия', 'комиссии', $ikName);

$makeFIO = explode(' ', $_SESSION['currentUser']->fio);
$myPost = pg_fetch_row(pg_query($_SESSION['psql'], 'select P.name, P.type from post P,pu PU where P.id=PU.pid and PU.uid='.$_SESSION['currentUser']->id.' order by P.type asc;'))[0];


$content = str_replace('{SUMM_PROPIS}', money2str(sprintf("%.02f", $sum_14)), $content);
$content = str_replace('{IKNAME}', $ikName, $content);
$content = str_replace('{PRED_FIO}', '________________________', $content);//implode(' ', $predFio)
$content = str_replace('{BUH_FIO}', '________________________', $content);
$content = str_replace('{ROWS}', $rows, $content);
$content = str_replace('{OUTED}', sprintf("%.02f", $sum_14), $content);
$content = str_replace('{NOT_OUTED}', ' ', $content);
$content = str_replace('{DEPONED}', ' ', $content);
$content = str_replace('{MAKE}', mb_strtolower($myPost), $content);//бухгалтер
$content = str_replace('{MAKE_FIO}', $makeFIO[0].' '.mb_substr($makeFIO[1], 0, 1).'.'.mb_substr($makeFIO[2], 0, 1).'.', $content);
$content = str_replace('{CHECK}', ' ', $content);
$content = str_replace('{CHECK_FIO}', '________________________', $content);
$content = str_replace('{OUTER}', '_____________________', $content);
$content = str_replace('{OUTER_FIO}', '________________________', $content);
$content = str_replace('{KASSIR_FIO}', '_____________________', $content);
?>