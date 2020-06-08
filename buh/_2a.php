<?php
    $colCount = count($komission);
    $colWidth = (int)((15580 - 1000) / $colCount);// (FullWidth - Col0)
    $defColCount = str_repeat('<w:gridCol w:w="'.$colWidth.'"/>', $colCount);
    $defEmptyCols = str_repeat('<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/></w:tcPr>'.
                               '<w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/>'.
                               '<w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p></w:tc>', $colCount);
    $defZeroCols = str_repeat('<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/></w:tcPr>'.
                               '<w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/>'.
                               '<w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r>'.
                               '<w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>0</w:t></w:r></w:p></w:tc>', $colCount);                           
    $defRowFio = '';
    $sumHours = array();
    foreach($komission as &$part) {
      $defRowFio .= '<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/>'.($colCount > 12 ? '<w:textDirection w:val="btLr"/>' : '').'</w:tcPr>';
      $FIO = explode(' ', $part['fio']);
      $sumHours[] = array('uid' => (int)$part['uid'], 'hours' => 0, 'night' => 0, 'holy' => 0);
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
      $rowDayHours .= '<w:tr w:rsidR="0043566C" w:rsidTr="0043566C">';
      $holyDay = '';
      if(in_array($curDay, $cal)) {
        $holyDay = ' (П)';
        if((int)strftime('%u', strtotime('+'.$curDay.' day', $firstInYear)) === 6)
          $holyDay = ' (С)';
        else if((int)strftime('%u', strtotime('+'.$curDay.' day', $firstInYear)) === 7)
          $holyDay = ' (В)';
      }
      $rowDayHours .= '<w:tc><w:tcPr><w:tcW w:w="1000" w:type="auto"/></w:tcPr>'.
                      '<w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/>'.
                      '<w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr>'.
                      '</w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr>'.
                      '<w:t>'.strftime('%d', strtotime('+'.$curDay.' day', $firstInYear)).' '.$months[(int)strftime('%m', strtotime('+'.$curDay.' day', $firstInYear))].$holyDay.'</w:t></w:r></w:p></w:tc>';//Add DAY column
      foreach($komission as &$part) {
        $finish = 0;
        $start = 0;
        foreach($data as &$d) {
          if($d['uid'] === $part['uid'] && $d['day'] === $curDay) {
            $start = (int)$d['start'];
            $finish = (int)$d['finish'];
            foreach($sumHours as $key => $value) {
              if($sumHours[$key]['uid'] === $d['uid']) {
                $sumHours[$key]['hours'] += ($finish - $start);
                $night = 0;

                if($start < 6 || $finish > 22) {
                  $night = ($start < 6 ? (($finish < 7 ? $finish : 6) - $start) : 
                           ($finish - ($start > 21 ? $start : 22)));
                  
                           //$night = ($finish > 22 ? ($finish - ($start > 21 ? $start : 22)) : -1);
                  $sumHours[$key]['night'] += $night;
                }
                if(in_array($curDay, $cal)) {
                  $sumHours[$key]['holy'] += (($finish - $start) - $night);//Add Holy hours without $nightHours
                }
              }
            }
          }
        }
        $currentHour = ' ';// NOT USE FOR CALC !!!!!!!!!!!!!!!!!!
        if($finish === 0) {
          $start = ' ';
          $currentHour = ' ';
        } else {
          $start = sprintf('%02u', $start).':00-'.sprintf('%02u', $finish).':00';
          $currentHour = ($finish - $start).'ч (Д)';
        }
        
        $rowDayHours .= '<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr>'.
                        '<w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/>'.
                        '<w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/>'.
                        '</w:rPr><w:t>'.$start.'</w:t></w:r></w:p>';//Add start & end hours for every $komission
        $rowDayHours .= '<w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr>'.
                        '<w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/>'.
                        '<w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/>'.
                        '</w:rPr><w:t>'.$currentHour.'</w:t></w:r></w:p></w:tc>';//Add hours for every $komission
      }
      $rowDayHours .= '</w:tr>';
      $curDay++;
    }
    $rowSumHours = '';
    $rowSumNightHours = '';
    $rowSumHolyHours = '';
    foreach($komission as &$part) {
      $hours = 0;
      $nightHours = 0;
      $holyHours = 0;
      foreach($sumHours as $key => $value) {
        if($sumHours[$key]['uid'] === $part['uid']) {
          $hours = $sumHours[$key]['hours'];
          $nightHours = $sumHours[$key]['night'];
          $holyHours = $sumHours[$key]['holy'];
          break;
        }
      }
      $rowSumHours .= '<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C">'.
                      '<w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr>'.
                      '<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>'.$hours.'</w:t></w:r></w:p></w:tc>';
                      $rowSumNightHours .= '<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C">'.
                      '<w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr>'.
                      '<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>'.$nightHours.'</w:t></w:r></w:p></w:tc>';
                      $rowSumHolyHours .= '<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C">'.
                      '<w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr>'.
                      '<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>'.$holyHours.'</w:t></w:r></w:p></w:tc>';
    }

    $ikName = str_replace('ая', 'ой', $ikName);
    $ikName = str_replace('комиссия', 'комиссии', $ikName);

    $content = str_replace('{DEFCOLCOUNT}', $defColCount, $content);
    $content = str_replace('{DEFROWFIO}', $defRowFio, $content);
    $content = str_replace('{DAY_HOURS}', $rowDayHours, $content);
    $content = str_replace('{SUM_HOURS}', $rowSumHours, $content);
    $content = str_replace('{DEFEMPTYCOLS}', $defEmptyCols, $content);
    $content = str_replace('{DEFZEROCOLS}', $defZeroCols, $content);
    $content = str_replace('{HEADER_LINE_1}', $ikName, $content);
    $content = str_replace('{SUM_NIGHT_HOURS}', $rowSumNightHours, $content);
    $content = str_replace('{SUM_HOLY_HOURS}', $rowSumHolyHours, $content);
    ?>