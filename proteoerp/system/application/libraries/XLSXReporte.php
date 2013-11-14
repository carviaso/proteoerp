<?php
class XLSXReporte {
	var $fcount=0;
	var $DBquery;
	var $DBfieldsName;
	var $DBfieldsType;
	var $DBfieldsMax_lengt;
	var $workbook;
	var $worksheet;
	var $fname;
	var $cols;
	var $ccols;
	var $crows;
	var $Titulo;
	var $Acumulador=array();
	var $SubTitulo;
	var $SobreTabla;
	var $tituHeader;
	var $tituSubHeader=array();
	var $centrar=array();
	var $wstring=array('string','char',252,253,254);
	var $wnumber=array('real','int','decimal',1,2,3,4,5,8,9,16,246);
	var $wdate  =array('date',7,10,11,12);
	var $fc=5;
	var $cc=0;
	var $ii=0;
	var $fi=0;
	var $totalizar=array();
	var $ctotalizar;
	var $grupo=array();
	var $cgrupo;
	var $colspos;
	//var $cgrupos=array();
	var $dRep=TRUE;
	var $grupoLabel;
	var $colum=0;
	var $rows=array();
	var $fCols=array();

	function XLSXReporte($mSQL=''){
		$this->ci = & get_instance();
		$this->ccols=0;
		if(!empty($mSQL)){
			$this->DBquery  = $this->ci->db->query($mSQL);
			$data=$this->DBquery->field_data();
			foreach ($data as $field){
				$this->DBfieldsName[]                 =$field->name;
				$this->DBfieldsType[$field->name]     =$field->type;
				$this->DBfieldsMax_lengt[$field->name]=$field->max_length;
			}
		}

		$this->ci->load->library('PHPExcel');

		$this->ci->phpexcel->getProperties()->setCreator('ProteoERP')
			->setLastModifiedBy('')
			->setTitle($this->Titulo)
			->setSubject('')
			->setDescription($this->Titulo.' '.$this->SubTitulo)
			->setKeywords('ProteoERP')
			->setCategory('');
	}

	function tcols(){
		$this->dRep=false;
		foreach ($this->DBfieldsName as $row){
			$this->AddCol($row,20,$row);
		}
		//$this->grupo=$this->grupos;
		//$this->cgrupo=TRUE;
	}

	function AddCol($DBnom,$width=-1,$TInom ,$align='L',$size=''){
		//Add a column to the table
		if (in_array($DBnom, $this->DBfieldsName)){
			if(is_array($TInom)) $TInom=implode(' ',$TInom);
			$this->colspos[$DBnom]=$this->ccols;
			$this->cols[]=array('titulo'=>$TInom,'campo'=>$DBnom);
			$this->centrar[]='';
			$this->ccols++;

			$a=$this->colum;
			//$this->worksheet->set_column($a,$a, $width);
			$this->colum++;
		}
	}

	function Header(){
		$this->ii = 6;

		$this->ci->phpexcel->setActiveSheetIndex(0)
			->setCellValue('A1', $this->utf8(implode(' ',$this->tituHeader)))
			->setCellValue('A2', $this->utf8(implode(' ',$this->tituSubHeader)))
			->setCellValue('A3', $this->utf8($this->Titulo ))
			->setCellValue('A4', $this->utf8($this->SubTitulo))
			->setCellValue('A5', $this->utf8($this->SobreTabla));

		$this->ci->phpexcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
		$this->ci->phpexcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(7);
		$this->ci->phpexcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(14);
		$this->ci->phpexcel->getActiveSheet()->getStyle('A3:A4')
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$lastcol=PHPExcel_Cell::stringFromColumnIndex($this->colum-1);
		for($i=1;$i<=5;$i++){
			$this->ci->phpexcel->getActiveSheet()->mergeCells("A${i}:${lastcol}${i}");
		}
		$this->lastcol=$lastcol;
		$encab='A6:'.$lastcol.'6';
		$this->ci->phpexcel->getActiveSheet()->freezePane('A7');
		$this->ci->phpexcel->getActiveSheet()->setAutoFilter($encab);
		$this->ci->phpexcel->getActiveSheet()->getStyle($encab)->applyFromArray(
			array(
				'font' => array(
					'bold' => true
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'borders' => array(
					'button'     => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'fill' => array(
					'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation'   => 90,
					'startcolor' => array(
						'argb' => 'FFA0A0A0'
					),
					'endcolor'   => array(
						'argb' => 'FFFFFFFF'
					)
				)
			)
		);

		foreach(range('A',PHPExcel_Cell::stringFromColumnIndex($this->colum-1)) as $columnID) {
			$this->ci->phpexcel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}

	}

	function Table() {
		if($this->dRep)
			$this->Header();//Encabezado
		//------------campos tabla-------------------------------
		foreach($this->cols AS $cl=>$cols){
			$this->ci->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($cl,$this->ii,$cols['titulo']);
		}
		$this->ii=$this->ii+2;
		//----------fin campos tabla-----------------------------
		//----------inicializa valores-----------------------------
		if($this->ctotalizar){
			foreach($this->cols  as $i=>$fila ){
				$gtotal[$fila['campo']]= 0;
			}
			$rgtotal=$gtotal;
		}

		//----------fin inicializa valores--------------------------
		//**--inicio data set, recorre fila a fila --------------------------
		foreach($this->DBquery->result_array() as $row){
			//------se recorre por columnas para calculo de totales y escritura de datos----------------------------
			foreach($this->cols AS $o=>$cols){
				$campo=$cols['campo'];
				$nf=$row;
				if (preg_match("/^__cC[0-9]+$/", $campo)>0){
					$sal=$this->_parsePattern($this->fCols[$campo]);
					$val=$this->fCols[$campo];
					if (count($sal)>0){
						foreach($sal as $pasa){
							if(!is_numeric($nf[$pasa])) $nf[$pasa]=0;
							$val=str_replace('<#'.$pasa.'#>',$nf[$pasa],$val);
						}
						$col='$val='.$val.';';
						eval($col);
						$row[$campo]=$val;
					}
				}

				if ($this->ctotalizar){
					if (in_array($campo,$this->totalizar)){
						$gtotal[$campo] +=$row[$campo];
						if($this->cgrupo){
							for($u=0;$u<count($this->grupo);$u++){
								$stotal[$u][$campo]+=$row[$campo];
								$rstotal[$u][$campo] =$stotal[$u][$campo];
							}
						}
						$rgtotal[$campo]=$gtotal[$campo];
						//if (in_array($campo, $this->Acumulador)) $row[$campo]=$stotal[$u-1][$campo];
						if (in_array($campo, $this->Acumulador)){
							if($this->cgrupo)
								$row[$campo]=$stotal[0][$campo];
							else
								$row[$campo]=$gtotal[$campo];
						}
					}else{
						$total[$campo]=$gtotal[$campo]=$rtotal[$campo]=$rgtotal[$campo]=' ';
						for($u=0;$u<count($this->grupo);$u++){
					 		$stotal[$u][$campo]=$rstotal[$u][$campo]=' ';
					 	}
					}
				}
				//------se escribe los datos----------------------------
				$l=$this->ii;
				$this->selectWrite($l-1, $o,$row[$campo],$campo);
				//------se escribe los datos----------------------------
			}
			$this->ii++;
		}
		////**--fin data set, recorre fila a fila --------------------------
		//
		////--escritura totales finales --------------------------
		//if ($this->ctotalizar){
		//		if ($this->cgrupo){
		//			for($u=0;$u<count($this->grupo);$u++){
		//				foreach($this->cols AS $h=>$cols){
		//					$campo=$cols['campo'];
		//					if(in_array($campo,$this->totalizar))
		//						//--------escritura totales finales--------------
		//						$this->worksheet->write_number($this->ii-1, $h,$rstotal[$u][$campo],$this->t2);
		//					else
		//						$this->worksheet->write_number($this->ii-1, $h,' ',$this->t2);
		//				}
		//				foreach($this->cols  as $i=>$fila ){
		//					$stotal[$u][$fila['campo']] = 0;
		//				}
		//				$this->ii++;
		//			}
		//
		//		}
		//	//--------escritura TOTAL FINAL--------------
		//	foreach($this->cols AS $h=>$cols){
		//		$campo=$cols['campo'];
		//		if(in_array($campo,$this->totalizar)){
		//			$this->worksheet->write_number($this->ii-1, $h,$rgtotal[$campo],$this->t1);
		//		}else{
		//			$this->worksheet->write_number($this->ii-1, $h,' ',$this->t1);
		//		}
		//	}
		//}
		////--fin escritura totales finales --------------------------
		//if($this->dRep){
		$this->Footer();

	}

	function setType($campo,$tipo){//relleno
		$this->DBfieldsType[$campo]=$tipo;
	}

	function setTitulo($tit='Listado',$size='',$font=''){
		$this->Titulo =$tit;
	}

	function setSubTitulo($tit='',$size='',$font=''){
		if(!empty($tit) ) $this->SubTitulo =$tit;
	}

	function setTableTitu($size='',$font=''){

	}

	function setRow($size='',$font=''){

	}

	function setHead($tituHeader='',$size='',$font=''){
	}

	function setSubHead($tituSubHeader='',$size='',$font=''){
	}

	function setHeadValores($param){
		$CI =& get_instance();
		$data= func_get_args();
		foreach($data as $sale)
			$this->tituHeader[]=$CI->datasis->traevalor($sale);
	}

	function setSubHeadValores($param){
		$CI =& get_instance();
		$data= func_get_args();
		foreach($data as $sale)
			$this->tituSubHeader[]=$CI->datasis->traevalor($sale);
	}

	function setAcumulador($param){
		$data= func_get_args();
		foreach($data as $sale){
			if (in_array($sale, $this->DBfieldsName) OR array_key_exists($sale,$this->fCols)){
				$this->Acumulador[]=$sale;
				if (!in_array($sale, $this->totalizar)){
					$this->totalizar[]=$sale;
					$this->ctotalizar=true;
				}
			}
		}
	}

	function setTotalizar($param){
		$data= func_get_args();

		$i=0;
		foreach($data as $sale){
			if (in_array($sale, $this->DBfieldsName) OR array_key_exists($sale,$this->fCols)){
				$this->totalizar[]=$sale;
				$this->ctotalizar=true;
			}
		}
	}

	function setGrupo($param){
		if(is_array($param)){
			$data=$param;
		}else{
			$data= func_get_args();
		}
		foreach($data as $sale){
			if(in_array($sale, $this->DBfieldsName)){
				$this->AddCol($sale,-1,$sale);
			}
		}
	}

	function setSobreTabla($SobreTabla,$size=8,$font='Arial'){
		$this->SobreTabla=$SobreTabla;
	}

	function setHeadGrupo($label='',$campo='',$font='',$size='',$type=''){
	}

	function setGrupoLabel($label){

	}

	function GroupTableHeader($row,$n=0){
		for($i=$n-1;$i<count($this->grupo);$i++){

			if (!empty($this->grupoLabel[$i])){

				$sal=$this->_parsePattern($this->grupoLabel[$i]);
				if(count($sal)>0){

					$label=$this->grupoLabel[$i];
					foreach($sal as $pasa){

						if($this->DBfieldsType[$pasa]=='date'){
							if(function_exists('dbdate_to_huma')){
								$row[$pasa]=dbdate_to_human($row[$pasa]);
							}
						}
						$label=str_replace('<#'.$pasa.'#>',$row[$pasa],$label);
					}
				}else
					$label=$this->grupoLabel[$i];
			}else{
				$label=$this->grupo[$i].' '.$row[$this->grupo[$i]];

			}
			$linea='A'.$this->ii;
			$arreglo[0]=$label;
			$this->worksheet->write_row($linea, $arreglo , $this->h4);
			$this->ii++;
		}
	}

	function Row($data,$linea=0,$pinta=1) {
	}

	function CalcWidths($width,$align) {
	}

	function add_fila($param){
		$data= func_get_args();
		$fila= array();
		foreach($this->rows as $i=>$key ){
			if(array_key_exists($i,$data))
				$fila[$key]=$data[$i];
			else
				$fila[$key]=' ';
			//$this->worksheet->write($this->ii, $i, $fila[$key]);
		}
		$this->ii++;
	}

	function AddPage(){
	}

	function Footer(){

		foreach($this->totalizar as $tot){
			if(isset($this->colspos[$tot])){
				$pos = PHPExcel_Cell::stringFromColumnIndex($this->colspos[$tot]);
				$this->ci->phpexcel->setActiveSheetIndex(0)->setCellValue($pos.($this->ii-1), "=SUM(${pos}7:${pos}".($this->ii-2).")");
			}
		}

		$this->ci->phpexcel->setActiveSheetIndex(0)
			->setCellValue('A'.($this->ii+2), $this->utf8($this->Titulo.' :: Sistema ProteoERP'));

		$this->ci->phpexcel->getActiveSheet()->getStyle('A'.($this->ii+2))->getFont()->setSize(8);
		$this->ci->phpexcel->getActiveSheet()->getStyle('A'.($this->ii+2))
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->ci->phpexcel->getActiveSheet()->mergeCells('A'.($this->ii+2).':'.$this->lastcol.($this->ii+2));
	}

	function Output(){

		$nomb='';//ucwords($this->Titulo);
		if(empty($nomb)){
			$nomb=date('d-m-Y');
		}
		$fname=preg_replace('/[\/:*?"<>| ]/','',$nomb);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$fname.'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($this->ci->phpexcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	function _parsePattern($pattern){
		$template = $pattern;
		$parsedcount = 0;
		$salida=array();
		while (strpos($template,'#>')>0) {
			$parsedcount++;
			$parsedfield = substr($template,strpos($template,"<#")+2,strpos($template,"#>")-strpos($template,"<#")-2);
			$salida[]=$parsedfield;
			$template = str_replace("<#".$parsedfield ."#>","",$template);
		}
		return $salida;
	}

	function selectWrite($f,$c,$campo,$dbcampo){
		if(isset($this->DBfieldsType[$dbcampo])){
			$tipo=$this->DBfieldsType[$dbcampo];
		}else{
			$tipo='';
		}

		if(in_array($tipo,$this->wnumber)){
			$xtypo = PHPExcel_Cell_DataType::TYPE_NUMERIC;
			$this->ci->phpexcel->setActiveSheetIndex(0)->setCellValueExplicitByColumnAndRow($c,$f,$campo,$xtypo);
		}elseif(in_array($tipo,$this->wstring)){
			$campo=trim($campo);
			$xtypo = PHPExcel_Cell_DataType::TYPE_STRING;
			$this->ci->phpexcel->setActiveSheetIndex(0)->setCellValueExplicitByColumnAndRow($c,$f,$this->utf8($campo),$xtypo);
		}elseif(in_array($tipo,$this->wdate)){
			//$campo= new DateTime($campo);
			//$xtypo=PHPExcel_Cell_DataType::TYPE_DATE;
			//$this->ci->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($c,$f,PHPExcel_Shared_Date::PHPToExcel($campo));
			$xtypo = PHPExcel_Cell_DataType::TYPE_STRING;
			if(function_exists('dbdate_to_human')){
				$campo=dbdate_to_human($campo);
			}
			$this->ci->phpexcel->setActiveSheetIndex(0)->setCellValueExplicitByColumnAndRow($c,$f,$campo,$xtypo);
		}else{
			if(preg_match('@(^[1-9][0-9]*(\.[0-9]+)?$)|(^0?\.[0-9]+$)@i',$campo)>0){
				$xtypo = PHPExcel_Cell_DataType::TYPE_NUMERIC;
				$this->ci->phpexcel->setActiveSheetIndex(0)->setCellValueExplicitByColumnAndRow($c,$f,$campo,$xtypo);
			}else{
				$xtypo = PHPExcel_Cell_DataType::TYPE_STRING;
				$campo=trim($campo);
				$this->ci->phpexcel->setActiveSheetIndex(0)->setCellValueExplicitByColumnAndRow($c,$f,$this->utf8($campo),$xtypo);
			}
		}
	}

	function grupoCambio($bache,$row){
		return false;
	}

	function AddCof($field=-1,$width=-1,$caption='',$align='L', $tipo=''){//$fontsize=11
		if(is_array($caption))
			$caption=implode(' ',$caption);
		//Add a column to the table
		if($field!=-1){
			$correcto=false;
			$sal=$this->_parsePattern($field);

			if(count($sal)>0){
				$correcto=true;
				foreach($sal as $pasa){
					if (!in_array($pasa, $this->DBfieldsName)){
						$correcto=false;
					}
				}
			}
			if ($correcto){
				$nname='__cC'.$this->fcount;
				$this->cols[]=array( 'campo'=>$nname, 'titulo'=>$caption,'tipo'=>$tipo);//,'w'=>$width, 'a'=>$align,'s'=>$fontsize
				$this->rows[]=$nname;
				$this->fCols[$nname]=$field;
				$this->fcount++;
				//$this->setType($nname,'real');
			}
		}
	}

	function utf8($val){
		if($this->ci->db->char_set=='latin1'){
			return utf8_encode($val);
		}
		return $val;
	}
}

//class PDFReporte extends XLSXReporte{
//	function PDFReporte($mSQL=''){
//		$this->XLSXReporte($mSQL);
//	}
//}
