<?php
  $colCount = count($komission);
  $colWidth = (int)((15580 - 1000) / $colCount);// (FullWidth - Col0)
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

  $ikName = str_replace('ая', 'ой', $ikName);
  $ikName = str_replace('комиссия', 'комиссии', $ikName);

  $header = '<w:tbl><w:tblPr><w:tblStyle w:val="a3"/><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideH w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideV w:val="none" w:sz="0" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="04A0" w:firstRow="1" w:lastRow="0" w:firstColumn="1" w:lastColumn="0" w:noHBand="0" w:noVBand="1"/></w:tblPr><w:tblGrid><w:gridCol w:w="7791"/><w:gridCol w:w="7792"/></w:tblGrid><w:tr w:rsidR="0043566C" w:rsidTr="0043566C"><w:tc><w:tcPr><w:tcW w:w="7791" w:type="dxa"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:bookmarkStart w:id="0" w:name="_GoBack"/><w:bookmarkEnd w:id="0"/><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>УТВЕРЖДАЮ</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr>
            <w:t>Председатель '.$ikName.'</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr>
            <w:t>_______________ {PREDFIO}</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="7792" w:type="dxa"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p></w:tc></w:tr></w:tbl><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>СВЕДЕНИЯ</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr>
            <w:t>о фактически отработанном времени членами '.$ikName.'</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>с правом решающего голоса, отработавшими в комиссии не на постоянной (штатной) основе</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>на {VOTE}</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>за {PERIOD1}</w:t></w:r></w:p>';
            //{PERIOD1}
  $footer = '<w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p><w:tbl><w:tblPr><w:tblStyle w:val="a3"/><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideH w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideV w:val="none" w:sz="0" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="04A0" w:firstRow="1" w:lastRow="0" w:firstColumn="1" w:lastColumn="0" w:noHBand="0" w:noVBand="1"/></w:tblPr><w:tblGrid><w:gridCol w:w="9142"/><w:gridCol w:w="6525"/></w:tblGrid><w:tr w:rsidR="00F67922" w:rsidTr="00F67922"><w:tc><w:tcPr><w:tcW w:w="7833" w:type="dxa"/></w:tcPr><w:tbl><w:tblPr><w:tblStyle w:val="a3"/><w:tblW w:w="8926" w:type="dxa"/><w:tblBorders><w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideH w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideV w:val="none" w:sz="0" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="04A0" w:firstRow="1" w:lastRow="0" w:firstColumn="1" w:lastColumn="0" w:noHBand="0" w:noVBand="1"/></w:tblPr><w:tblGrid><w:gridCol w:w="4673"/><w:gridCol w:w="1985"/><w:gridCol w:w="2268"/></w:tblGrid><w:tr w:rsidR="00F67922" w:rsidTr="00F67922"><w:tc><w:tcPr><w:tcW w:w="4673" w:type="dxa"/>
            </w:tcPr><w:p w:rsidR="00F67922" w:rsidRDefault="00F67922" w:rsidP="00F67922"><w:pPr><w:spacing w:line="360" w:lineRule="auto"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>Заместитель председателя избирательной комиссии</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1985" w:type="dxa"/></w:tcPr><w:p w:rsidR="00F67922" w:rsidRDefault="00F67922" w:rsidP="00F67922"><w:pPr><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>_________________</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2268" w:type="dxa"/></w:tcPr><w:p w:rsidR="00F67922" w:rsidRDefault="00F67922" w:rsidP="00F67922"><w:pPr><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:proofErr w:type="spellStart"/><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>{ZAMFIO}</w:t></w:r><w:proofErr w:type="spellEnd"/></w:p></w:tc></w:tr><w:tr w:rsidR="00F67922" w:rsidTr="00F67922"><w:tc><w:tcPr><w:tcW w:w="4673" w:type="dxa"/></w:tcPr><w:p w:rsidR="00F67922" w:rsidRDefault="00F67922" w:rsidP="00F67922"><w:pPr><w:spacing w:line="360" w:lineRule="auto"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:bookmarkStart w:id="0" w:name="_GoBack"/>
            <w:bookmarkEnd w:id="0"/><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>МП</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1985" w:type="dxa"/></w:tcPr><w:p w:rsidR="00F67922" w:rsidRPr="00F67922" w:rsidRDefault="00F67922" w:rsidP="00F67922"><w:pPr><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2268" w:type="dxa"/></w:tcPr><w:p w:rsidR="00F67922" w:rsidRDefault="00F67922" w:rsidP="00F67922"><w:pPr><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p></w:tc></w:tr><w:tr w:rsidR="00F67922" w:rsidTr="00F67922"><w:tc><w:tcPr><w:tcW w:w="4673" w:type="dxa"/></w:tcPr><w:p w:rsidR="00F67922" w:rsidRDefault="00F67922" w:rsidP="00F67922"><w:pPr><w:spacing w:line="360" w:lineRule="auto"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>Секретарь избирательной комиссии</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1985" w:type="dxa"/></w:tcPr><w:p w:rsidR="00F67922" w:rsidRDefault="00F67922" w:rsidP="00F67922"><w:pPr><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>_________________</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2268" w:type="dxa"/></w:tcPr>
            <w:p w:rsidR="00F67922" w:rsidRDefault="00F67922" w:rsidP="00F67922"><w:pPr><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:proofErr w:type="spellStart"/><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>{SECRETARYFIO}</w:t></w:r><w:proofErr w:type="spellEnd"/></w:p></w:tc></w:tr><w:tr w:rsidR="00F67922" w:rsidTr="00F67922"><w:tc><w:tcPr><w:tcW w:w="4673" w:type="dxa"/></w:tcPr><w:p w:rsidR="00F67922" w:rsidRDefault="00F67922" w:rsidP="0043566C"><w:pPr><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>«___» _____________ 20___ г.</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1985" w:type="dxa"/></w:tcPr><w:p w:rsidR="00F67922" w:rsidRDefault="00F67922" w:rsidP="0043566C"><w:pPr><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2268" w:type="dxa"/></w:tcPr><w:p w:rsidR="00F67922" w:rsidRDefault="00F67922" w:rsidP="0043566C"><w:pPr><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p></w:tc></w:tr></w:tbl><w:p w:rsidR="00F67922" w:rsidRDefault="00F67922" w:rsidP="0043566C"><w:pPr><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="7834" w:type="dxa"/></w:tcPr>
            <w:p w:rsidR="00F67922" w:rsidRDefault="00F67922" w:rsidP="0043566C"><w:pPr><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p></w:tc></w:tr></w:tbl><w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:spacing w:after="0"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr></w:p><w:sectPr w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidSect="0043566C"><w:pgSz w:w="16838" w:h="11906" w:orient="landscape"/><w:pgMar w:top="568" w:right="678" w:bottom="568" w:left="709" w:header="708" w:footer="708" w:gutter="0"/><w:cols w:space="708"/><w:docGrid w:linePitch="360"/></w:sectPr>';

  $table = '<w:tbl><w:tblPr><w:tblStyle w:val="a3"/><w:tblW w:w="15580" w:type="auto"/><w:tblInd w:w="-5" w:type="dxa"/><w:tblLook w:val="04A0" /></w:tblPr><w:tblGrid><w:gridCol w:w="1560"/>'.str_repeat('<w:gridCol w:w="'.$colWidth.'"/>', $colCount).'</w:tblGrid><w:tr w:rsidR="0043566C" w:rsidTr="0043566C"><w:trPr><w:cantSplit/><w:trHeight w:val="1134"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="1560" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>Число</w:t></w:r></w:p>
            </w:tc>'.$defRowFio.'</w:tr>{DAY_HOURS}<w:tr w:rsidR="0043566C" w:rsidTr="0043566C"><w:tc><w:tcPr><w:tcW w:w="1560" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="0043566C"><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>Отработано часов, всего</w:t></w:r></w:p>
            </w:tc>{SUM_HOURS}</w:tr><w:tr w:rsidR="0043566C" w:rsidTr="0043566C"><w:tc><w:tcPr><w:tcW w:w="1560" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>из них:</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>1. Для выплаты компенсации</w:t></w:r></w:p>
            </w:tc>'.$defZeroCols.'</w:tr><w:tr w:rsidR="0043566C" w:rsidTr="0043566C"><w:tc><w:tcPr><w:tcW w:w="1560" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>2. Для дополнительной оплаты труда (вознаграждения), всего</w:t></w:r></w:p>
            </w:tc>{SUM_HOURS}</w:tr><w:tr w:rsidR="0043566C" w:rsidTr="0043566C"><w:tc><w:tcPr><w:tcW w:w="1560" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>в том числе:</w:t></w:r></w:p><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>в ночное время</w:t></w:r></w:p>
            </w:tc>{SUM_NIGHT_HOURS}</w:tr><w:tr w:rsidR="0043566C" w:rsidTr="0043566C"><w:tc><w:tcPr><w:tcW w:w="1560" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>в выходные и нерабочие праздничные дни</w:t></w:r></w:p>
            </w:tc>{SUM_HOLY_HOURS}</w:tr><w:tr w:rsidR="0043566C" w:rsidTr="0043566C"><w:tc><w:tcPr><w:tcW w:w="1560" w:type="auto"/></w:tcPr><w:p w:rsidR="0043566C" w:rsidRPr="0043566C" w:rsidRDefault="0043566C" w:rsidP="0043566C"><w:pPr><w:ind w:left="0" w:right="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>Подпись члена комиссии об ознакомлении</w:t></w:r></w:p>
            </w:tc>'.$defEmptyCols.'</w:tr></w:tbl>';

  $curDay = $first;
  $curMonth = (int)strftime('%m', strtotime('+'.$curDay.' day', $firstInYear));
  $rowDayHours = '';

  $header1 = str_replace('{PERIOD1}', $monthsNom[$curMonth].' '.strftime('%Y', strtotime('+'.$curDay.' day', $firstInYear)), $header);
  $data1 = $header1;





  while($curDay < $last + 1) {
      if($curMonth !== (int)strftime('%m', strtotime('+'.$curDay.' day', $firstInYear))) {//on change month

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

              $sumHours[$key]['hours'] = 0;
              $sumHours[$key]['night'] = 0;
              $sumHours[$key]['holy'] = 0;
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

          $curMonth = (int)strftime('%m', strtotime('+'.$curDay.' day', $firstInYear));
          $header1 = str_replace('{PERIOD1}', $monthsNom[$curMonth].' '.strftime('%Y', strtotime('+'.$curDay.' day', $firstInYear)), $header);

          $table1 = str_replace('{DAY_HOURS}', $rowDayHours, $table);
          $table1 = str_replace('{SUM_HOURS}', $rowSumHours, $table1);
          $table1 = str_replace('{SUM_NIGHT_HOURS}', $rowSumNightHours, $table1);
          $table1 = str_replace('{SUM_HOLY_HOURS}', $rowSumHolyHours, $table1);

          $data1 .= $table1.$footer.$pageBreak.$header1;

          $rowDayHours = '';
          $rowSumHours = '';

      }


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
  
    


  $table1 = str_replace('{DAY_HOURS}', $rowDayHours, $table);
  $table1 = str_replace('{SUM_HOURS}', $rowSumHours, $table1);
  $table1 = str_replace('{SUM_NIGHT_HOURS}', $rowSumNightHours, $table1);
  $table1 = str_replace('{SUM_HOLY_HOURS}', $rowSumHolyHours, $table1);
  
  $data1 .= $table1.$footer;
  $content = str_replace('{DATA}', $data1, $content);
?>