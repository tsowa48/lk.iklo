<?php
    $colCount = count($komission);
    $colWidth = (int)((15580 - 1000) / $colCount);// (FullWidth - Col0)
    $defColCount = str_repeat('<w:gridCol w:w="'.$colWidth.'"/>', $colCount);
    $defEmptyCols = str_repeat('<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/></w:tcPr>'.
                               '<w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/>'.
                               '<w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p></w:tc>', $colCount);
    $defRowFio = '';
    $sumHours = array();
    foreach($komission as &$part) {
      $defRowFio .= '<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/>'.($colCount > 12 ? '<w:textDirection w:val="btLr"/>' : '').'</w:tcPr>';
      $FIO = explode(' ', $part['fio']);
      $sumHours[] = array('uid' => (int)$part['uid'], 'hours' => 0);
      foreach($FIO as &$_fio) {
        $defRowFio .= '<w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C">'.
                      '<w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr>'.
                      '<w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/>'.
                      '<w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr>'.
                      '<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>'.$_fio.'</w:t></w:r></w:p>';
      }
      $defRowFio .= '</w:tc>';
    }
    $curDay = $first;
    $rowDayHours = '';
    while($curDay < $last + 1) {
      $isHoly = in_array($curDay, $cal);
      $rowDayHours .= '<w:tr w:rsidR="0043566C" w:rsidTr="0043566C">';
      $rowDayHours .= '<w:tc><w:tcPr><w:tcW w:w="1000" w:type="auto"/></w:tcPr>'.
                      '<w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/>'.
                      '<w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr>'.
                      '</w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr>'.
                      '<w:t>'.strftime('%02d', strtotime('+'.$curDay.' day', $firstInYear)).' '.$months[(int)strftime('%m', strtotime('+'.$curDay.' day', $firstInYear))].'</w:t></w:r></w:p></w:tc>';//Add DAY column
      foreach($komission as &$part) {
        $currentHour = 0;
        foreach($data as &$d) {
          if($d['uid'] === $part['uid'] && $d['day'] === $curDay) {
            $currentHour = $d['hours'];
            foreach($sumHours as $key => $value)
              if($sumHours[$key]['uid'] === $d['uid'])
                $sumHours[$key]['hours'] += $currentHour;
          }
        }
        if($currentHour === 0) $currentHour = ' ';
        $rowDayHours .= '<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr>'.
                        '<w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/>'.
                        '<w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/>'.
                        '</w:rPr><w:t>'.$currentHour.'</w:t></w:r></w:p></w:tc>';//Add hours for every $komission
      }
      $rowDayHours .= '</w:tr>';
      $curDay++;
    }
    $rowSumHours = '';
    foreach($komission as &$part) {
      $hours = 0;
      foreach($sumHours as $key => $value)
        if($sumHours[$key]['uid'] === $part['uid'])
          $hours = $sumHours[$key]['hours'];
      $rowSumHours .= '<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C">'.
      '<w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr>'.
      '<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>'.$hours.'</w:t></w:r></w:p></w:tc>';
    }
    $headerLine1 = str_replace('ая', 'ой', $ikName);
    $headerLine1 = str_replace('комиссия', 'комиссии', $headerLine1);
    $headerLine2 = '';
    //$headerLine2 = 'избирательного участка №{IK}';

    $content = str_replace('{DEFCOLCOUNT}', $defColCount, $content);
    $content = str_replace('{DEFROWFIO}', $defRowFio, $content);
    $content = str_replace('{DAY_HOURS}', $rowDayHours, $content);
    $content = str_replace('{SUM_HOURS}', $rowSumHours, $content);
    $content = str_replace('{DEFEMPTYCOLS}', $defEmptyCols, $content);
    $content = str_replace('{HEADER_LINE_1}', $headerLine1, $content);
    $content = str_replace('{HEADER_LINE_2}', $headerLine2, $content);
    ?>