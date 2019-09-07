<?php
  $colCount = count($komission);
  $colWidth = (int)((15580 - 1000) / $colCount);// (FullWidth - Col0)

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
  $ikName = str_replace('ая', 'ой', $ikName);
  $ikName = str_replace('комиссия', 'комиссии', $ikName);
  $header = '<w:tbl><w:tblPr><w:tblStyle w:val="a3"/><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideH w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideV w:val="none" w:sz="0" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="04A0" w:firstRow="1" w:lastRow="0" w:firstColumn="1" w:lastColumn="0" w:noHBand="0" w:noVBand="1"/></w:tblPr><w:tblGrid><w:gridCol w:w="7791"/><w:gridCol w:w="7792"/></w:tblGrid><w:tr w:rsidR="0043566C" w:rsidTr="0043566C"><w:tc><w:tcPr><w:tcW w:w="7791" w:type="dxa"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:bookmarkStart w:id="0" w:name="_GoBack"/><w:bookmarkEnd w:id="0"/><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>Утвержден постановлением</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr>
    <w:t>'.$ikName.'</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr>
    <w:t>'.' от «__» __________ 20__ года № _____</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="7792" w:type="dxa"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p></w:tc></w:tr></w:tbl><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>ГРАФИК РАБОТЫ</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr>
    <w:t>членов '.$ikName.'</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>с правом решающего голоса, работающих в комиссии не на постоянной (штатной) основе</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>на {VOTE}</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>на {PERIOD1}</w:t></w:r></w:p>';
    //{PERIOD1}

  $footer = '<w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p><w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>Секретарь избирательной комиссии _________________ {SECRETARYFIO}</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>«___» _____________ 20___ г.</w:t></w:r></w:p><w:sectPr w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidSect="0043566C"><w:pgSz w:w="16838" w:h="11906" w:orient="landscape"/><w:pgMar w:top="568" w:right="678" w:bottom="568" w:left="709" w:header="708" w:footer="708" w:gutter="0"/><w:cols w:space="708"/><w:docGrid w:linePitch="360"/></w:sectPr>';

  $table = '<w:tbl><w:tblPr><w:tblStyle w:val="a3"/><w:tblW w:w="15580" w:type="auto"/><w:tblInd w:w="-5" w:type="dxa"/><w:tblLook w:val="04A0" /></w:tblPr><w:tblGrid>
    <w:gridCol w:w="1560"/>'.str_repeat('<w:gridCol w:w="'.$colWidth.'"/>', $colCount).'</w:tblGrid><w:tr w:rsidR="0043566C" w:rsidTr="0043566C"><w:trPr><w:cantSplit/><w:trHeight w:val="1134"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="1560" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>Число</w:t></w:r></w:p>
    </w:tc>'.$defRowFio.'</w:tr>{ROWs}<w:tr w:rsidR="0043566C" w:rsidTr="0043566C"><w:tc><w:tcPr><w:tcW w:w="1560" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>Итого</w:t></w:r></w:p>
    </w:tc>{SUM_HOURS}</w:tr><w:tr w:rsidR="0043566C" w:rsidTr="0043566C"><w:tc><w:tcPr><w:tcW w:w="1560" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>Подпись члена комиссии об ознакомлении</w:t></w:r></w:p>
    </w:tc>'.str_repeat('<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p></w:tc>', $colCount).'</w:tr></w:tbl>';

  $curDay = $first;
  $curMonth = (int)strftime('%m', strtotime('+'.$curDay.' day', $firstInYear));
  $rowSumHours = '';//var_dump($sumHours);
  $ROWs = '';
  
  $header1 = str_replace('{PERIOD1}', $monthsNom[$curMonth].' '.strftime('%Y', strtotime('+'.$curDay.' day', $firstInYear)), $header);
  $data1 = $header1;

  while($curDay < $last + 1) {
    if($curMonth !== (int)strftime('%m', strtotime('+'.$curDay.' day', $firstInYear))) {//on change month
      foreach($komission as &$part) {
        foreach($sumHours as $key => $value) {
          if($sumHours[$key]['uid'] === $part['uid']) {
            $hours = $sumHours[$key]['hours'];
            $rowSumHours .= '<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C">'.
                            '<w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr>'.
                            '<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>'.$hours.'</w:t></w:r></w:p></w:tc>';
            $sumHours[$key]['hours'] = 0;
          }
        }
      }

      $curMonth = (int)strftime('%m', strtotime('+'.$curDay.' day', $firstInYear));
      $header1 = str_replace('{PERIOD1}', $monthsNom[$curMonth].' '.strftime('%Y', strtotime('+'.$curDay.' day', $firstInYear)), $header);
      $table1 = str_replace('{ROWs}', $ROWs, $table);
      $table1 = str_replace('{SUM_HOURS}', $rowSumHours, $table1);
      $data1 .= $table1.$footer.$pageBreak.$header1;

      $ROWs = '';
      $rowSumHours = '';
    }



    $ROWs .= '<w:tr w:rsidR="0043566C" w:rsidTr="0043566C"><w:tc><w:tcPr><w:tcW w:w="1000" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr>'.
                    '<w:t>'.strftime('%d', strtotime('+'.$curDay.' day', $firstInYear)).' '.$months[$curMonth].'</w:t></w:r></w:p></w:tc>';//Add DAY column
    foreach($komission as &$part) {
      $currentHour = 0;
      foreach($data as &$d) {
        if($d['uid'] === $part['uid'] && $d['day'] === $curDay) {
          $currentHour = (int)$d['hours'];
          foreach($sumHours as $key => $value)
            if($sumHours[$key]['uid'] === $d['uid'])
              $sumHours[$key]['hours'] += $currentHour;
        }
      }
      if($currentHour === 0) $currentHour = ' ';
      $ROWs .= '<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr>'.
                      '<w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/>'.
                      '<w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/>'.
                      '</w:rPr><w:t>'.$currentHour.'</w:t></w:r></w:p></w:tc>';//Add hours for every $komission
    }
    $ROWs .= '</w:tr>';
    $curDay++;
  }


  foreach($komission as &$part) {
    foreach($sumHours as $key => $value) {
      if($sumHours[$key]['uid'] === $part['uid']) {
        $hours = $sumHours[$key]['hours'];
        $rowSumHours .= '<w:tc><w:tcPr><w:tcW w:w="'.$colWidth.'" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C">'.
                        '<w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr>'.
                        '<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>'.$hours.'</w:t></w:r></w:p></w:tc>';
        $sumHours[$key]['hours'] = 0;
      }
    }
  }

  
  $table1 = str_replace('{ROWs}', $ROWs, $table);
  $table1 = str_replace('{SUM_HOURS}', $rowSumHours, $table1);

  $data1 .= $table1.$footer;

  $content = str_replace('{DATA}', $data1, $content);
?>