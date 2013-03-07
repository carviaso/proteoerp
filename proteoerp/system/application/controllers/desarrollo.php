<?php
/***********************************************************************
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
*/

class Desarrollo extends Controller{

	function Desarrollo(){
		parent::Controller();
	}

	function index(){

	}

	function camposdb(){
		$db=$this->uri->segment(3);
		if($db===false){
			exit('Debe especificar en la uri la tabla');
		}
		$query = $this->db->query("DESCRIBE $db");

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$str='$data[\''.$row->Field."']";
				$str=str_pad($str,20);
				echo $str."='';\n";
			}
		}
	}

	function lcamposdb(){
		$db =$this->uri->segment(3);
		$pre=$this->uri->segment(4);
		if($pre!==FALSE)
			$ant="$pre.";
		else
			$ant='';
		if($db===false){
			exit('Debe especificar en la uri la tabla');
		}
		$query = $this->db->query("DESCRIBE $db");

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$str=$row->Field.",";
				echo $ant.$str;
			}
		}
	}

	function acamposdb(){
		$db =$this->uri->segment(3);
		$pre=$this->uri->segment(4);
		if($pre!==FALSE)
			$ant="$pre.";
		else
			$ant='';
		if($db===false){
			exit('Debe especificar en la uri la tabla');
		}
		$query = $this->db->query("DESCRIBE $db");

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$str=$row->Field.'","';
				echo $ant.$str;
			}
		}
	}

	function ccamposdb(){
		$db =$this->uri->segment(3);
		$pre=$this->uri->segment(4);
		if($pre!==FALSE)
			$ant="$pre.";
		else
			$ant='';
		if($db===false){
			exit('Debe especificar en la uri la tabla');
		}
		$query = $this->db->query("DESCRIBE $db");

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$str="'$row->Field',";
				echo $ant.$str;
			}
		}
	}

	function genecrud($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');

		$crud ="\n\t".'function dataedit(){'."\n";
		$crud.="\t\t".'$this->rapyd->load(\'dataedit\');'."\n\n";
		$crud.="\t\t".'$edit = new DataEdit($this->tits, \''.$tabla.'\');'."\n\n";
		$crud.="\t\t".'$edit->back_url = site_url($this->url.\'filteredgrid\');'."\n\n";

		$crud.="\t\t".'$edit->post_process(\'insert\',\'_post_insert\');'."\n";
		$crud.="\t\t".'$edit->post_process(\'update\',\'_post_update\');'."\n";
		$crud.="\t\t".'$edit->post_process(\'delete\',\'_post_delete\');'."\n";
		$crud.="\t\t".'$edit->pre_process(\'insert\',\'_pre_insert\');'."\n";
		$crud.="\t\t".'$edit->pre_process(\'update\',\'_pre_update\');'."\n";
		$crud.="\t\t".'$edit->pre_process(\'delete\',\'_pre_delete\');'."\n";

		$crud.="\n";

		//$fields = $this->db->field_data($tabla);
		$mSQL="DESCRIBE $tabla";
		$query = $this->db->query("DESCRIBE $tabla");
		foreach ($query->result() as $field){

			if($field->Field=='usuario'){
				$crud.="\t\t".'$edit->usuario = new autoUpdateField(\'usuario\',$this->session->userdata(\'usuario\'),$this->session->userdata(\'usuario\'));'."\n\n";
			}elseif($field->Field=='estampa'){
				$crud.="\t\t".'$edit->estampa = new autoUpdateField(\'estampa\' ,date(\'Ymd\'), date(\'Ymd\'));'."\n\n";
			}elseif($field->Field=='hora'){
				$crud.="\t\t".'$edit->hora    = new autoUpdateField(\'hora\',date(\'H:i:s\'), date(\'H:i:s\'));'."\n\n";
			}elseif($field->Field=='id'){
				continue;
			}else{
				preg_match('/(?P<tipo>\w+)(\((?P<length>[0-9\,]+)\)){0,1}/', $field->Type, $matches);
				if(isset($matches['length'])){
					$def=explode(',',$matches['length']);
				}else{
					$def[0]=8;
				}

				if(strrpos($field->Type,'date')!==false){
					$input='date';
				}elseif(strrpos($field->Type,'text')!==false){
					$input= 'textarea';
				}else{
					$input='input';
				}

				$crud.="\t\t".'$edit->'.$field->Field.' = new '.$input."Field('".ucfirst($field->Field)."','$field->Field');\n";

				if(preg_match("/decimal/i",$field->Type)){
					$crud.="\t\t".'$edit->'.$field->Field."->rule='max_length[".$def[0]."]|numeric';\n";
					$crud.="\t\t".'$edit->'.$field->Field."->css_class='inputnum';\n";
				}elseif(preg_match("/integer|int/i",$field->Type)){
					$crud.="\t\t".'$edit->'.$field->Field."->rule='max_length[".$def[0]."]|integer';\n";
					$crud.="\t\t".'$edit->'.$field->Field."->css_class='inputonlynum';\n";
				}elseif(preg_match("/date/i",$field->Type)){
					$crud.="\t\t".'$edit->'.$field->Field."->rule='chfecha';\n";
					$crud.="\t\t".'$edit->'.$field->Field."->calendar=false;\n";
				}else{
					$crud.="\t\t".'$edit->'.$field->Field."->rule='max_length[".$def[0]."]';\n";
				}

				if(strrpos($field->Type,'text')===false){
					$crud.="\t\t".'$edit->'.$field->Field.'->size ='.($def[0]+2).";\n";
					$crud.="\t\t".'$edit->'.$field->Field.'->maxlength ='.($def[0]).";\n";
				}else{
					$crud.="\t\t".'$edit->'.$field->Field."->cols = 70;\n";
					$crud.="\t\t".'$edit->'.$field->Field."->rows = 4;\n";
				}
				$crud.="\n";
			}
		}

		$crud.="\t\t".'$edit->buttons(\'modify\', \'save\', \'undo\', \'delete\', \'back\');'."\n";
		$crud.="\t\t".'$edit->build();'."\n\n";

		$crud.="\t\t".'$script= \'<script type="text/javascript" > '."\n";
		$crud.="\t\t".'$(function() {'."\n";

		$crud.="\t\t\t".'$(".inputnum").numeric(".");'."\n";
		$crud.="\t\t\t".'$(".inputonlynum").numeric();'."\n";

		$crud.="\t\t\t".'$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });'."\n";

		$crud.="\t\t".'});'."\n";
		$crud.="\t\t".'</script>\';'."\n\n";

		$crud.="\t\t".'$data[\'content\'] = $edit->output;'."\n";
		$crud.="\t\t".'$data[\'head\']    = $this->rapyd->get_head();'."\n";
		$crud.="\t\t".'$data[\'script\']  = script(\'jquery.js\').script(\'plugins/jquery.numeric.pack.js\').script(\'plugins/jquery.floatnumber.js\');'."\n";
		$crud.="\t\t".'$data[\'script\'] .= $script;'."\n";
		$crud.="\t\t".'$data[\'title\']   = heading($this->tits);'."\n";
		$crud.="\t\t".'$this->load->view(\'view_ventanas\', $data);'."\n\n";
		$crud.="\t".'}'."\n";

		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			return $crud;
		}
	}


	//******************************************************************
	//
	//   Genera Reporte
	//
	//******************************************************************
	function generepo($tabla=null){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');
		$this->genefilter($tabla, true, true );
	}

	//******************************************************************
	//
	//   Genera la seccion de filtro para el Crud
	//
	//
	//******************************************************************
	function genefilter($tabla=null,$s=true, $repo=false ){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');
		$mt1 = "\n\t";
		$mt2 = "\n\t\t";
		$mt3 = "\n\t\t\t";

		if ( $repo ){
			$mt1   = "\n";
			$mt2   = "\n";
			$mt3   = "\n\t";

			$crud  = '$filter = new DataFilter("Filtro", \''.$tabla.'\');';
			$crud .= $mt1.'$filter->attributes=array(\'onsubmit\'=>\'is_loaded()\');'."\n";

		}else{
			$crud  = $mt1.'function filteredgrid(){';
			$crud .= $mt2.'$this->rapyd->load(\'datafilter\',\'datagrid\');'."\n";
			$crud .= $mt2.'$filter = new DataFilter($this->titp, \''.$tabla.'\');'."\n";
		}

		//$fields = $this->db->field_data($tabla);
		$mSQL="DESCRIBE $tabla";
		$query = $this->db->query("DESCRIBE $tabla");
		$key=array();
		foreach ($query->result() as $field){
				if($field->Key=='PRI')$key[]=$field->Field;

				if($field->Field=='id'){
					continue;
				}

				preg_match('/(?P<tipo>\w+)(\((?P<length>[0-9\,]+)\)){0,1}/', $field->Type, $matches);
				if(isset($matches['length'])){
					$def=explode(',',$matches['length']);
				}else{
					$def[0]=8;
				}

				if(strrpos($field->Type,'date')!==false){
					$input='date';
				}elseif(strrpos($field->Type,'text')!==false){
					$input= 'textarea';
				}else{
					$input='input';
				}

				$crud.=$mt2.'$filter->'.$field->Field.' = new '.$input."Field('".ucfirst($field->Field)."','$field->Field');";

				if(preg_match("/decimal|integer/i",$field->Type)){
					$crud.=$mt2.'$filter->'.$field->Field."->rule      ='max_length[".$def[0]."]|numeric';";
					$crud.=$mt2.'$filter->'.$field->Field."->css_class ='inputnum';";
				}elseif(preg_match("/date/i",$field->Type)){
					$crud.=$mt2.'$filter->'.$field->Field."->rule      ='chfecha';";
				}else{
					$crud.=$mt2.'$filter->'.$field->Field."->rule      ='max_length[".$def[0]."]';";
				}

				if(strrpos($field->Type,'text')===false){
					if($def[0]<80){
						$crud.=$mt2.'$filter->'.$field->Field.'->size      ='.($def[0]+2).";";
					}
					$crud.=$mt2.'$filter->'.$field->Field.'->maxlength ='.($def[0]).";";
				}else{
					$crud.=$mt2.'$filter->'.$field->Field."->cols = 70;";
					$crud.=$mt2.'$filter->'.$field->Field."->rows = 4;";
				}
				$crud.="\n";

		}

		if ( $repo ){
			$crud.=$mt1.'$filter->salformat = new radiogroupField("Formato de salida","salformat");';
			$crud.=$mt1.'$filter->salformat->options($this->opciones);';
			$crud.=$mt1.'$filter->salformat->insertValue =\'PDF\';';
			$crud.=$mt1.'$filter->salformat->clause = "";'."\n";

			$crud.=$mt1.'$filter->buttons("search");';
			$crud.=$mt1.'$filter->build();'."\n\n";

			$crud.=$mt1.'if($this->rapyd->uri->is_set("search")){'."\n";
			$crud.=$mt3.'$mSQL=$this->rapyd->db->_compile_select();';
			$crud.=$mt3.'//echo $mSQL;'."\n";

			$crud.=$mt3.'$sobretabla="";';
			$crud.=$mt3.'//if(!empty($filter->?????->newValue))  $sobretabla.=\'??????:  \'.$filter->?????->description;';
			$crud.=$mt3.'//if(!empty($filter->?????->newValue))  $sobretabla.=\'??????:  \'.$filter->?????->description;'."\n";

			$crud.=$mt3.'$pdf = new PDFReporte($mSQL);';
			$crud.=$mt3.'$pdf->setHeadValores(\'TITULO1\');';
			$crud.=$mt3.'$pdf->setSubHeadValores(\'TITULO2\',\'TITULO3\');';
			$crud.=$mt3.'$pdf->setTitulo("Listado para la Tabla '.strtoupper($tabla).'");';
			$crud.=$mt3.'//$pdf->setSubTitulo("Desde la fecha: ".$_POST[\'fechad\']." Hasta ".$_POST[\'fechah\']);';
			$crud.=$mt3.'$pdf->setSobreTabla($sobretabla);';
			$crud.=$mt3.'$pdf->AddPage();';
			$crud.=$mt3.'$pdf->setTableTitu(11,\'Times\');'."\n";

			$c=0;
			foreach ($query->result() as $field){
				$crud.=$mt3.'$pdf->AddCol(\''.$field->Field.'\', 20,\''.ucfirst($field->Field).'\',\'L\',8);';
			}

			$crud.=$mt3.'$pdf->setTotalizar(\'vtotal\',\'contado\',\'credito\',\'anulado\');';
			$crud.=$mt3.'$pdf->Table();'."\n";
			$crud.=$mt3.'$pdf->Output();'."\n";

			$crud.=$mt1.'}else{'."\n";
			$crud.=$mt3.'$data["filtro"] = $filter->output;';
			$crud.=$mt3.'$data["titulo"] = \'&lt;h2 class="mainheader"&gtListado para la Tabla '.strtoupper($tabla).'&lt;h2&gt;\';';
			$crud.=$mt3.'$data["head"] = $this->rapyd->get_head();';
			$crud.=$mt3.'$this->load->view(\'view_freportes\', $data);';
			$crud.="\n}\n";



		} else {
			$crud.="\t\t".'$filter->buttons(\'reset\', \'search\');'."\n";
			$crud.="\t\t".'$filter->build();'."\n\n";


			$a=$b='';
			foreach($key AS $val){
				$a.='<raencode><#'.$val.'#></raencode>';
				$b.='<#'.$val.'#>';
			}
			$crud.="\t\t".'$uri = anchor($this->url.\'dataedit/show/'.$a.'\',\''.$b.'\');'."\n\n";

			$crud.="\t\t".'$grid = new DataGrid(\'\');'."\n";
			$k=implode(',',$key);
			$crud.="\t\t".'$grid->order_by(\''.$k.'\');'."\n";
			$crud.="\t\t".'$grid->per_page = 40;'."\n\n";

			$c=0;
			foreach ($query->result() as $field){
				if($field->Key=='PRI') $key[]=$field->Field;

				$crud.="\t\t".'$grid->column_orderby(\''.ucfirst($field->Field).'\',';
				if($c==0){
					$crud.='$uri';
					$c++;
					$crud.=',\''.$field->Field.'\',\'align="left"\');'."\n";
				}else{
					$crud.='\'';
					if(strrpos($field->Type,'date')!==false){
						$crud.='<dbdate_to_human><#'.$field->Field.'#></dbdate_to_human>';
						$crud.='\',\''.$field->Field.'\',\'align="center"\');'."\n";
					}elseif(strrpos($field->Type,'double')!==false || strrpos($field->Type,'int')!==false || strrpos($field->Type,'decimal')!==false){
						$crud.='<nformat><#'.$field->Field.'#></nformat>';
						$crud.='\',\''.$field->Field.'\',\'align="right"\');'."\n";
					}else{
						$crud.=$field->Field;
						$crud.='\',\''.$field->Field.'\',\'align="left"\');'."\n";
					}
				}
			}


			$crud.="\n";
			$crud.="\t\t".'$grid->add($this->url.\'dataedit/create\');'."\n";
			$crud.="\t\t".'$grid->build();'."\n";
			$crud.="\n";

			$crud.="\t\t".'$data[\'filtro\']  = $filter->output;'."\n";
			$crud.="\t\t".'$data[\'content\'] = $grid->output;'."\n";
			$crud.="\t\t".'$data[\'head\']    = $this->rapyd->get_head().script(\'jquery.js\');'."\n";
			$crud.="\t\t".'$data[\'title\']   = heading($this->titp);'."\n";
			$crud.="\t\t".'$this->load->view(\'view_ventanas\', $data);'."\n\n";
			$crud.="\t".'}'."\n";
		}
		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			//$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		} else {
			return $crud;
		}
	}

	//******************************************************************
	//
	//   Genera la seccion de funciones post del Crud
	//
	//
	//******************************************************************
	function genepost($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');

		$crud="\n";
		$crud.="\t".'function _post_insert($do){'."\n";
		$crud.="\t\t".'$primary =implode(\',\',$do->pk);'."\n";
		$crud.="\t\t".'logusu($do->table,"Creo $this->tits $primary ");'."\n";
		$crud.="\t".'}'."\n\n";
		$crud.="\t".'function _post_update($do){'."\n";
		$crud.="\t\t".'$primary =implode(\',\',$do->pk);'."\n";
		$crud.="\t\t".'logusu($do->table,"Modifico $this->tits $primary ");'."\n";
		$crud.="\t".'}'."\n\n";
		$crud.="\t".'function _post_delete($do){'."\n";
		$crud.="\t\t".'$primary =implode(\',\',$do->pk);'."\n";
		$crud.="\t\t".'logusu($do->table,"Elimino $this->tits $primary ");'."\n";
		$crud.="\t".'}'."\n";

		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			return $crud;
		}
	}

	//******************************************************************
	//
	//   Genera la seccion de funciones PRE del Crud
	//
	//
	//******************************************************************
	function genepre($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');

		$crud="\n";
		$crud.="\t".'function _pre_insert($do){'."\n";
		$crud.="\t\t".'$do->error_message_ar[\'pre_ins\']=\'\';'."\n";
		$crud.="\t\t".'return true;'."\n";
		$crud.="\t".'}'."\n\n";
		$crud.="\t".'function _pre_update($do){'."\n";
		$crud.="\t\t".'$do->error_message_ar[\'pre_upd\']=\'\';'."\n";
		$crud.="\t\t".'return true;'."\n";
		$crud.="\t".'}'."\n\n";
		$crud.="\t".'function _pre_delete($do){'."\n";
		$crud.="\t\t".'$do->error_message_ar[\'pre_del\']=\'\';'."\n";
		$crud.="\t\t".'return false;'."\n";
		$crud.="\t".'}'."\n";

		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			return $crud;
		}
	}

	function geneinstalar($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');
		$row=$this->datasis->damerow("SHOW CREATE TABLE `$tabla`;");
		//Create Table

		$crud="\n";
		$crud.="\t".'function instalar(){'."\n";
		$crud.="\t\t".'if (!$this->db->table_exists(\''.$tabla.'\')) {'."\n";
		$crud.="\t\t\t".'$mSQL="'.str_replace("\n","\n\t\t\t",$row['Create Table']).'";'."\n";
		$crud.="\t\t\t".'$this->db->simple_query($mSQL);'."\n";
		$crud.="\t\t".'}'."\n";
		$crud.="\t\t".'//$campos=$this->db->list_fields(\''.$tabla.'\');'."\n";
		$crud.="\t\t".'//if(!in_array(\'<#campo#>\',$campos)){ }'."\n";
		$crud.="\t".'}'."\n";

		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			return $crud;
		}
	}

	function genehead($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');

		$crud="\n";
		$crud.='<?php'."\n";
		$crud.="class $tabla extends Controller {"."\n";
		$crud.="\t".'var $titp=\'Titulo Principal\';'."\n";
		$crud.="\t".'var $tits=\'Sub-titulo\';'."\n";
		$crud.="\t".'var $url =\''.$tabla.'/\';'."\n\n";
		$crud.="\t"."function $tabla(){"."\n";
		$crud.="\t\t".'parent::Controller();'."\n";
		$crud.="\t\t".'$this->load->library(\'rapyd\');'."\n";
		$crud.="\t\t".'//$this->datasis->modulo_id(216,1);'."\n";
		$crud.="\t\t".'$this->instalar();'."\n";
		$crud.="\t".'}'."\n\n";
		$crud.="\t".'function index(){'."\n";
		$crud.="\t\t".'redirect($this->url.\'filteredgrid\');'."\n";
		$crud.="\t".'}'."\n\n";

		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			return $crud;
		}
	}

	function genefoot($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');

		$crud="\n";
		$crud.='}'."\n";
		$crud.='?>';

		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			return $crud;
		}
	}

	function genetodo($tabla=null,$s=true){
		$crud='';
		$crud.=$this->genehead($tabla    ,false);
		$crud.=$this->genefilter($tabla  ,false);
		$crud.=$this->genecrud($tabla    ,false);
		$crud.=$this->genepre($tabla     ,false);
		$crud.=$this->genepost($tabla    ,false);
		$crud.=$this->geneinstalar($tabla,false);
		$crud.=$this->genefoot($tabla    ,false);

		$crud=htmlentities($crud);

		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			return $crud;
		}
	}


	// Genera las columnas para Extjs
	function extjs(){
		$db =$this->uri->segment(3);
		if($db===false){
			exit('Debe especificar en la uri la tabla');
		}
		$query = $this->db->query("DESCRIBE $db");
		$i = 0;
		if ($query->num_rows() > 0){
			$fields  = '';
			$columna = '';
			$campos  = '';
			foreach ($query->result() as $row){
				if ($i == 0 ){
					$str="'".$row->Field."'";
					$i = 1;
				} else {
					$str=",'".$row->Field."'";
				}
				$fields .= $str;

				$str = "{ header: ".str_pad("'".$row->Field."'",20).",  width: 60, sortable: true,  dataIndex: ".str_pad("'".$row->Field."'",20).", field: ";

				if ( $row->Type == 'date' or $row->Type == 'timestamp' ) {
					$str .= "{ type: 'date'       }, filter: { type: 'date'    }";
				} elseif ( $row->Type == 'date' or $row->Type == 'timestamp' ) {
					$str = "{ type: 'date'       }, filter: { type: 'date'    }";
				} elseif ( substr($row->Type,0,7) == 'decimal' or substr($row->Type,0,3) == 'int'  ) {
					$str .= "{ type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')";
				} else {
					$str .= "{ type: 'textfield'  }, filter: { type: 'string'  }";
				}
				$columna .= $str."},<br>";


				$str = "{ fieldLabel: ".$row->Field.",  name: ".$row->Field.", width:100, labelWidth:60, ";

				if ( $row->Type == 'date' or $row->Type == 'timestamp' ) {
					$str .= "{ type: 'date'       }, filter: { type: 'date'    }";
				} elseif ( $row->Type == 'date' or $row->Type == 'timestamp' ) {
					$str = "xtype: 'datefield', format: 'd/m/Y', submitFormat: 'Y-m-d' ";
				} elseif ( substr($row->Type,0,7) == 'decimal' or substr($row->Type,0,3) == 'int'  ) {
					$str .= "xtype: 'numberfield', , hideTrigger: true, fieldStyle: 'text-align: right',  renderer : Ext.util.Format.numberRenderer('0,000.00')";
				} else {
					$str .= "xtype: 'textfield' ";
				}
				$campos .= $str."},<br>";

			}
			echo "$fields<br>";
			echo "<br>$columna";
			echo "<br>$campos";
		}
	}

	//******************************************************************
	//
	//  Genera Crud para jqGrid
	//
	//******************************************************************
	function jqgrid(){
		$db = $this->uri->segment(3);
		if($db===false){
			exit('Debe especificar en la uri la tabla y el directorio "/tabla/directorio"');
		}
		$contro =$this->uri->segment(4);
		if($contro===false){
			$contro = 'CONTROLADOR';
		}

		$query = $this->db->query("DESCRIBE $db");
		$i = 0;
		if ($query->num_rows() > 0){
			$fields  = '';
			$columna = '<?php'."\n";
			$param   = '';
			$campos  = '';
			$str = '';
			$tab1 = $this->mtab(1);
			$tab2 = $this->mtab(2);
			$tab3 = $this->mtab(3);
			$tab4 = $this->mtab(4);
			$tab5 = $this->mtab(5);
			$tab6 = $this->mtab(6);
			$tab7 = $this->mtab(7);
			$tab8 = $this->mtab(8);

			$str .= $this->jqgridclase($db, $contro);

			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'//Layout en la Ventana'."\n";
			$str .= $tab1.'//'."\n";
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'function jqdatag(){'."\n\n";
			$str .= $tab2.'$grid = $this->defgrid();'."\n";
			$str .= $tab2.'$param[\'grids\'][] = $grid->deploy();'."\n\n";

			$str .= $tab2."//Funciones que ejecutan los botones\n";
			$str .= $tab2.'$bodyscript = $this->bodyscript( $param[\'grids\'][0][\'gridname\']);'."\n\n";

			$str .= $tab2.'//Botones Panel Izq'."\n";
			$str .= $tab2.'//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));'."\n";
			$str .= $tab2.'$WestPanel = $grid->deploywestp();'."\n\n";

			$str .= $tab2.'$adic = array('."\n";
			$str .= $tab3.'array(\'id\'=>\'fedita\',  \'title\'=>\'Agregar/Editar Registro\'),'."\n";
			$str .= $tab3.'array(\'id\'=>\'fshow\' ,  \'title\'=>\'Mostrar Registro\'),'."\n";
			$str .= $tab3.'array(\'id\'=>\'fborra\',  \'title\'=>\'Eliminar Registro\')'."\n";
			$str .= $tab2.');'."\n";
			$str .= $tab2.'$SouthPanel = $grid->SouthPanel($this->datasis->traevalor(\'TITULO1\'), $adic);'."\n\n";

			//$str .= $tab2.'$SouthPanel = $grid->SouthPanel($this->datasis->traevalor("TITULO1"));'."\n\n";

			$str .= $tab2.'$param[\'WestPanel\']   = $WestPanel;'."\n";
			$str .= $tab2.'//$param[\'EastPanel\'] = $EastPanel;'."\n";
			$str .= $tab2.'$param[\'SouthPanel\']  = $SouthPanel;'."\n";
			$str .= $tab2.'$param[\'listados\']    = $this->datasis->listados(\''.strtoupper($db).'\', \'JQ\');'."\n";
			$str .= $tab2.'$param[\'otros\']       = $this->datasis->otros(\''.strtoupper($db).'\', \'JQ\');'."\n";
			$str .= $tab2.'$param[\'temas\']       = array(\'proteo\',\'darkness\',\'anexos1\');'."\n";
			//$str .= $tab2.'$param[\'anexos\']    = \'anexos1\';'."\n";
			$str .= $tab2.'$param[\'bodyscript\']  = $bodyscript;'."\n";
			$str .= $tab2.'$param[\'tabs\']        = false;'."\n";
			$str .= $tab2.'$param[\'encabeza\']    = $this->titp;'."\n";
			$str .= $tab2.'$param[\'tamano\']      = $this->datasis->getintramenu( substr($this->url,0,-1) );'."\n";

			$str .= $tab2.'$this->load->view(\'jqgrid/crud2\',$param);'."\n";
			$str .= $tab1.'}'."\n\n";

			//**************************************
			//  Funcion de Java del Body
			//
			//
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'//Funciones de los Botones'."\n";
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'function bodyscript( $grid0 ){'."\n";
			$str .= $tab2.'$bodyscript = \'';
			$str .= $tab2.'&lt;script type="text/javascript"&gt;\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'add(){'."\n";
			$str .= $tab3.'$.post("\'.site_url($this->url'.'.\'dataedit/create\').\'",'."\n";
			$str .= $tab3.'function(data){'."\n";
			$str .= $tab4.'$("#fedita").html(data);'."\n";
			$str .= $tab4.'$("#fedita").dialog( "open" );'."\n";
			$str .= $tab3.'})'."\n";
			$str .= $tab2.'};\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'edit(){'."\n";
			$str .= $tab3.'var id     = jQuery("#newapi\'.$grid0.\'").jqGrid(\\\'getGridParam\\\',\\\'selrow\\\');'."\n";
			$str .= $tab3.'if(id){'."\n";
			$str .= $tab4.'var ret    = $("#newapi\'.$grid0.\'").getRowData(id);'."\n";
			$str .= $tab4.'mId = id;'."\n";
			$str .= $tab4.'$.post("\'.site_url($this->url'.'.\'dataedit/modify\').\'/"+id, function(data){'."\n";
			$str .= $tab5.'$("#fedita").html(data);'."\n";
			$str .= $tab5.'$("#fedita").dialog( "open" );'."\n";
			$str .= $tab4.'});'."\n";
			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'$.prompt("&lt;h1&gt;Por favor Seleccione un Registro&lt;/h1&gt;");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'show(){'."\n";
			$str .= $tab3.'var id     = jQuery("#newapi\'.$grid0.\'").jqGrid(\\\'getGridParam\\\',\\\'selrow\\\');'."\n";
			$str .= $tab3.'if(id){'."\n";
			$str .= $tab4.'var ret    = $("#newapi\'.$grid0.\'").getRowData(id);'."\n";
			$str .= $tab4.'mId = id;'."\n";
			$str .= $tab4.'$.post("\'.site_url($this->url'.'.\'dataedit/show\').\'/"+id, function(data){'."\n";
			$str .= $tab5.'$("#fshow").html(data);'."\n";
			$str .= $tab5.'$("#fshow").dialog( "open" );'."\n";
			$str .= $tab4.'});'."\n";
			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'$.prompt("&lt;h1&gt;Por favor Seleccione un Registro&lt;/h1&gt;");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'del() {'."\n";
			$str .= $tab3.'var id = jQuery("#newapi\'.$grid0.\'").jqGrid(\\\'getGridParam\\\',\\\'selrow\\\');'."\n";
			$str .= $tab3.'if(id){'."\n";
			$str .= $tab3.'	if(confirm(" Seguro desea eliminar el registro?")){'."\n";
			$str .= $tab3.'		var ret    = $("#newapi\'.$grid0.\'").getRowData(id);'."\n";
			$str .= $tab3.'		mId = id;'."\n";
			$str .= $tab3.'		$.post("\'.site_url($this->url.\'dataedit/do_delete\').\'/"+id, function(data){'."\n";
			$str .= $tab3.'			try{'."\n";
			$str .= $tab3.'				var json = JSON.parse(data);'."\n";
			$str .= $tab3.'				if (json.status == "A"){'."\n";
			$str .= $tab3.'					apprise("Registro eliminado");'."\n";
			$str .= $tab3.'					jQuery("#newapi\'.$grid0.\'").trigger("reloadGrid");'."\n";
			$str .= $tab3.'				}else{'."\n";
			$str .= $tab3.'					apprise("Registro no se puede eliminado");'."\n";
			$str .= $tab3.'				}'."\n";
			$str .= $tab3.'			}catch(e){'."\n";
			$str .= $tab3.'				$("#fborra").html(data);'."\n";
			$str .= $tab3.'				$("#fborra").dialog( "open" );'."\n";
			$str .= $tab3.'			}'."\n";
			$str .= $tab3.'		});'."\n";
			$str .= $tab3.'	}'."\n";
			$str .= $tab3.'}else{'."\n";
			$str .= $tab3.'	$.prompt("<h1>Por favor Seleccione un Registro</h1>");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};\';'."\n";


			$str .= $tab2.'//Wraper de javascript'."\n";
			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$(function(){'."\n";
			$str .= $tab3.'$("#dialog:ui-dialog").dialog( "destroy" );'."\n";
			$str .= $tab3.'var mId = 0;'."\n";
			$str .= $tab3.'var montotal = 0;'."\n";
			$str .= $tab3.'var ffecha = $("#ffecha");'."\n";
			$str .= $tab3.'var grid = jQuery("#newapi\'.$grid0.\'");'."\n";
			$str .= $tab3.'var s;'."\n";
			$str .= $tab3.'var allFields = $( [] ).add( ffecha );'."\n";
			$str .= $tab3.'var tips = $( ".validateTips" );'."\n";
			$str .= $tab3.'s = grid.getGridParam(\\\'selarrrow\\\');'."\n";
			$str .= $tab3.'\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$("#fedita").dialog({'."\n";
			$str .= $tab3.'autoOpen: false, height: 500, width: 700, modal: true,'."\n";
			$str .= $tab3.'buttons: {'."\n";
			$str .= $tab4.'"Guardar": function() {'."\n";
			$str .= $tab5.'var bValid = true;'."\n";
			$str .= $tab5.'var murl = $("#df1").attr("action");'."\n";
			$str .= $tab5.'allFields.removeClass( "ui-state-error" );'."\n";
			$str .= $tab5.'$.ajax({'."\n";
			$str .= $tab6.'type: "POST", dataType: "html", async: false,'."\n";
			$str .= $tab6.'url: murl,'."\n";
			$str .= $tab6.'data: $("#df1").serialize(),'."\n";
			$str .= $tab6.'success: function(r,s,x){'."\n";

			$str .= $tab7.'try{'."\n";
			$str .= $tab8.'var json = JSON.parse(r);'."\n";
			$str .= $tab8.'if (json.status == "A"){'."\n";
			$str .= $tab8.'	apprise("Registro Guardado");'."\n";
			$str .= $tab8.'	$( "#fedita" ).dialog( "close" );'."\n";
			$str .= $tab8.'	grid.trigger("reloadGrid");'."\n";
			$str .= $tab8.'	\'.$this->datasis->jwinopen(site_url(\'formatos/ver/'.strtoupper($db).'\').\'/\\\'+res.id+\\\'/id\\\'\').\';'."\n";
			$str .= $tab8.'	return true;'."\n";
			$str .= $tab8.'} else {'."\n";
			$str .= $tab8.'	apprise(json.mensaje);'."\n";
			$str .= $tab8.'}'."\n";
			$str .= $tab7.'}catch(e){'."\n";
			$str .= $tab7.'	$("#fedita").html(r);'."\n";
			$str .= $tab7.'}'."\n";

			//$str .= $tab6.'if ( r.length == 0 ) {'."\n";
			//$str .= $tab7.'apprise("Registro Guardado");'."\n";
			//$str .= $tab7.'$( "#fedita" ).dialog( "close" );'."\n";
			//$str .= $tab7.'grid.trigger("reloadGrid");'."\n";
			//$str .= $tab7.'\'.$this->datasis->jwinopen(site_url(\'formatos/ver/'.strtoupper($db).'\').\'/\\\'+res.id+\\\'/id\\\'\').\';'."\n";
			//$str .= $tab7.'return true;'."\n";
			//$str .= $tab6.'} else { '."\n";
			//$str .= $tab7.'$("#fedita").html(r);'."\n";
			//$str .= $tab6.'}'."\n";

			$str .= $tab6.'}'."\n";
			//$str .= $tab4.'}'."\n";
			$str .= $tab5.'})'."\n";
			$str .= $tab4.'},'."\n";
			$str .= $tab4.'"Cancelar": function() {'."\n";
			$str .= $tab5.'$("#fedita").html("");'."\n";
			$str .= $tab5.'$( this ).dialog( "close" );'."\n";
			$str .= $tab4.'}'."\n";
			$str .= $tab3.'},'."\n";
			$str .= $tab3.'close: function() {'."\n";
			$str .= $tab4.'$("#fedita").html("");'."\n";
			$str .= $tab4.'allFields.val( "" ).removeClass( "ui-state-error" );'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'});\';'."\n\n";
			//$str .= $tab2.'});'."\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$("#fshow").dialog({'."\n";
			$str .= $tab3.'autoOpen: false, height: 500, width: 700, modal: true,'."\n";
			$str .= $tab3.'buttons: {'."\n";
			$str .= $tab4.'"Aceptar": function() {'."\n";
			$str .= $tab5.'$("#fshow").html("");'."\n";
			$str .= $tab5.'$( this ).dialog( "close" );'."\n";
			$str .= $tab4.'},'."\n";
			$str .= $tab3.'},'."\n";
			$str .= $tab3.'close: function() {'."\n";
			$str .= $tab4.'$("#fshow").html("");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'});\''.";\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$("#fborra").dialog({'."\n";
			$str .= $tab3.'autoOpen: false, height: 300, width: 400, modal: true,'."\n";
			$str .= $tab3.'buttons: {'."\n";
			$str .= $tab4.'"Aceptar": function() {'."\n";
			$str .= $tab5.'$("#fborra").html("");'."\n";
			$str .= $tab5.'jQuery("#newapi\'.$grid0.\'").trigger("reloadGrid");'."\n";
			$str .= $tab5.'$( this ).dialog( "close" );'."\n";
			$str .= $tab4.'},'."\n";
			$str .= $tab3.'},'."\n";
			$str .= $tab3.'close: function() {'."\n";
			$str .= $tab4.'jQuery("#newapi\'.$grid0.\'").trigger("reloadGrid");'."\n";
			$str .= $tab4.'$("#fborra").html("");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'});\''.";\n\n";

			$str .= $tab2.'$bodyscript .= \'});\'."\n";'."\n\n";

			$str .= $tab2.'$bodyscript .= "\n&lt;/script&gt;\n";'."\n";
			$str .= $tab2.'$bodyscript .= "";'."\n";
			$str .= $tab2.'return $bodyscript;'."\n";
			$str .= $tab1."}\n\n";

			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'//Definicion del Grid y la Forma'."\n";
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'function defgrid( $deployed = false ){'."\n";
			$str .= $tab2.'$i      = 1;'."\n";
			$str .= $tab2.'$editar = "false";'."\n\n";
			$str .= $tab2.'$grid  = new $this->jqdatagrid;'."\n\n";
			$columna .= $str;
			$str = '';

			foreach ($query->result() as $row){
				if ( $row->Field == 'id') {
					$str   = $tab2.'$grid->addField(\'id\');'."\n";
					$str  .= $tab2.'$grid->label(\'Id\');'."\n";
					$str  .= $tab2.'$grid->params(array('."\n";
					$str  .= $tab3.'\'align\'         => "\'center\'",'."\n";
					$str  .= $tab3.'\'frozen\'        => \'true\','."\n";
					$str  .= $tab3.'\'width\'         => 40,'."\n";
					$str  .= $tab3.'\'editable\'      => \'false\','."\n";
					$str  .= $tab3.'\'search\'        => \'false\''."\n";
				} else {
					$str  = $tab2.'$grid->addField(\''.$row->Field.'\');'."\n";
					$str .= $tab2.'$grid->label(\''.ucfirst($row->Field).'\');'."\n";

					$str .= $tab2.'$grid->params(array('."\n";
					$str .= $tab3.'\'search\'        => \'true\','."\n";
					$str .= $tab3.'\'editable\'      => $editar,'."\n";

					if ( $row->Type == 'date' or $row->Type == 'timestamp' ) {
						$str .= $tab3.'\'width\'         => 80,'."\n";
						$str .= $tab3.'\'align\'         => "\'center\'",'."\n";
						$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
						$str .= $tab3.'\'editrules\'     => \'{ required:true,date:true}\','."\n";
						$str .= $tab3.'\'formoptions\'   => \'{ label:"Fecha" }\''."\n";

					} elseif ( substr($row->Type,0,7) == 'decimal' or substr($row->Type,0,3) == 'int'  ) {
						$str .= $tab3.'\'align\'         => "\'right\'",'."\n";
						$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
						$str .= $tab3.'\'width\'         => 100,'."\n";
						$str .= $tab3.'\'editrules\'     => \'{ required:true }\','."\n";
						$str .= $tab3.'\'editoptions\'   => \'{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }\','."\n";
						$str .= $tab3.'\'formatter\'     => "\'number\'",'."\n";
						if (substr($row->Type,0,3) == 'int'){
							$str .= $tab3.'\'formatoptions\' => \'{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }\''."\n";
						} else {
							$str .= $tab3.'\'formatoptions\' => \'{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }\''."\n";
						}

					} elseif ( substr($row->Type,0,7) == 'varchar' or substr($row->Type,0,4) == 'char'  ) {
						$long = str_replace(array('varchar(','char(',')'),"", $row->Type)*10;
						$maxlong = $long/10;
						if ( $long > 200 ) $long = 200;
						if ( $long < 40 ) $long = 40;

						$str .= $tab3.'\'width\'         => '.$long.','."\n";
						$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
						$str .= $tab3.'\'editrules\'     => \'{ required:true}\','."\n";
						$str .= $tab3.'\'editoptions\'   => \'{ size:'.$maxlong.', maxlength: '.$maxlong.' }\','."\n";

					} elseif ( $row->Type == 'text' ) {
						$long = 250;
						$str .= $tab3.'\'width\'         => '.$long.','."\n";
						$str .= $tab3.'\'edittype\'      => "\'textarea\'",'."\n";
						$str .= $tab3.'\'editoptions\'   => "\'{rows:2, cols:60}\'",'."\n";

						//$str .= $tab3.'\'formoptions\'   => "\'{rows:"2", cols:"60"}\'",'."\n";


					} else {
						$str .= $tab3.'\'width\'         => 140,'."\n";
						$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
					}
				}
				$str .= $tab2.'));'."\n\n";
				$columna .= $str."\n";
			}

			$str  = $tab2.'$grid->showpager(true);'."\n";
			$str .= $tab2.'$grid->setWidth(\'\');'."\n";
			$str .= $tab2.'$grid->setHeight(\'290\');'."\n";
			$str .= $tab2.'$grid->setTitle($this->titp);'."\n";
			$str .= $tab2.'$grid->setfilterToolbar(true);'."\n";
			$str .= $tab2.'$grid->setToolbar(\'false\', \'"top"\');'."\n\n";

			$str .= $tab2.'$grid->setFormOptionsE(\'closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} \');'."\n";
			$str .= $tab2.'$grid->setFormOptionsA(\'closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} \');'."\n";

			//$str .= $tab2.'$grid->setAfterSubmit("$.prompt(\'Respuesta:\'+a.responseText); return [true, a ];");'."\n\n";

			$str .= $tab2.'$grid->setAfterSubmit("$(\'#respuesta\').html(\'&lt;span style='."\\'font-weight:bold; color:red;\\'&gt;'+a.responseText+'&lt;/span&gt;'); return [true, a ];".'");'."\n\n";


			$str .= $tab2.'#show/hide navigations buttons'."\n";
			$str .= $tab2.'$grid->setAdd(    $this->datasis->sidapuede(\''.strtoupper($db).'\',\'INCLUIR%\' ));'."\n";
			$str .= $tab2.'$grid->setEdit(   $this->datasis->sidapuede(\''.strtoupper($db).'\',\'MODIFICA%\'));'."\n";
			$str .= $tab2.'$grid->setDelete( $this->datasis->sidapuede(\''.strtoupper($db).'\',\'BORR_REG%\'));'."\n";
			$str .= $tab2.'$grid->setSearch( $this->datasis->sidapuede(\''.strtoupper($db).'\',\'BUSQUEDA%\'));'."\n";
			$str .= $tab2.'$grid->setRowNum(30);'."\n";

			$str .= $tab2.'$grid->setShrinkToFit(\'false\');'."\n\n";

			$str .= $tab2.'$grid->setBarOptions("addfunc: '.strtolower($db).'add, editfunc: '.strtolower($db).'edit, delfunc: '.strtolower($db).'del, viewfunc: '.strtolower($db).'show");'."\n\n";

			$str .= $tab2.'#Set url'."\n";
			$str .= $tab2.'$grid->setUrlput(site_url($this->url.\'setdata/\'));'."\n\n";

			$str .= $tab2.'#GET url'."\n";
			$str .= $tab2.'$grid->setUrlget(site_url($this->url.\'getdata/\'));'."\n\n";

			$str .= $tab2.'if ($deployed) {'."\n";
			$str .= $tab2.'	return $grid->deploy();'."\n";
			$str .= $tab2.'} else {'."\n";
			$str .= $tab2.'	return $grid;'."\n";
			$str .= $tab2.'}'."\n";
			$str .= $tab1.'}'."\n\n";

			$str .= $tab1.'/**'."\n";
			$str .= $tab1.'* Busca la data en el Servidor por json'."\n";
			$str .= $tab1.'*/'."\n";
			$str .= $tab1.'function getdata(){'."\n";

			//$str .= $tab2.'$filters = $this->input->get_post(\'filters\');'."\n";

			$str .= $tab2.'$grid       = $this->jqdatagrid;'."\n\n";

			$str .= $tab2.'// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO'."\n";
			$str .= $tab2.'$mWHERE = $grid->geneTopWhere(\''.$db.'\');'."\n\n";

			$str .= $tab2.'$response   = $grid->getData(\''.$db.'\', array(array()), array(), false, $mWHERE );'."\n";
			$str .= $tab2.'$rs = $grid->jsonresult( $response);'."\n";
			$str .= $tab2.'echo $rs;'."\n";

			$str .= $tab1.'}'."\n\n";

			$str .= $tab1.'/**'."\n";
			$str .= $tab1.'* Guarda la Informacion'."\n";
			$str .= $tab1.'*/'."\n";
			$str .= $tab1.'function setData(){'."\n";
			$str .= $tab2.'$this->load->library(\'jqdatagrid\');'."\n";
			$str .= $tab2.'$oper   = $this->input->post(\'oper\');'."\n";
			$str .= $tab2.'$id     = $this->input->post(\'id\');'."\n";
			$str .= $tab2.'$data   = $_POST;'."\n";
			$str .= $tab2.'$mcodp  = "??????";'."\n";
			$str .= $tab2.'$check  = 0;'."\n\n";

			$str .= $tab2.'unset($data[\'oper\']);'."\n";
			$str .= $tab2.'unset($data[\'id\']);'."\n";

			$str .= $tab2.'if($oper == \'add\'){'."\n";
			$str .= $tab3.'if(false == empty($data)){'."\n";
			$str .= $tab4.'$check = $this->datasis->dameval("SELECT count(*) FROM '.$db.' WHERE $mcodp=".$this->db->escape($data[$mcodp]));'."\n";
			$str .= $tab4.'if ( $check == 0 ){'."\n";
			$str .= $tab5.'$this->db->insert(\''.$db.'\', $data);'."\n";
			$str .= $tab5.'echo "Registro Agregado";'."\n\n";
			$str .= $tab5.'logusu(\''.strtoupper($db).'\',"Registro ????? INCLUIDO");'."\n";
			$str .= $tab4.'} else'."\n";
			$str .= $tab5.'echo "Ya existe un registro con ese $mcodp";'."\n";

			$str .= $tab3.'} else'."\n";
			//$str .= $tab2.'echo \'\';'."\n";
			$str .= $tab4.'echo "Fallo Agregado!!!";'."\n\n";

			$str .= $tab2.'} elseif($oper == \'edit\') {'."\n";
			$str .= $tab3.'$nuevo  = $data[$mcodp];'."\n";
			$str .= $tab3.'$anterior = $this->datasis->dameval("SELECT $mcodp FROM '.$db.' WHERE id=$id");'."\n";
			$str .= $tab3.'if ( $nuevo <> $anterior ){'."\n";
			$str .= $tab4.'//si no son iguales borra el que existe y cambia'."\n";
			$str .= $tab4.'$this->db->query("DELETE FROM '.$db.' WHERE $mcodp=?", array($mcodp));'."\n";
			$str .= $tab4.'$this->db->query("UPDATE '.$db.' SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));'."\n";
			$str .= $tab4.'$this->db->where("id", $id);'."\n";
			$str .= $tab4.'$this->db->update("'.$db.'", $data);'."\n";
			$str .= $tab4.'logusu(\''.strtoupper($db).'\',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");'."\n";
			$str .= $tab4.'echo "Grupo Cambiado/Fusionado en clientes";'."\n";

			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'unset($data[$mcodp]);'."\n";
			$str .= $tab4.'$this->db->where("id", $id);'."\n";
			$str .= $tab4.'$this->db->update(\''.$db.'\', $data);'."\n";
			$str .= $tab4.'logusu(\''.strtoupper($db).'\',"Grupo de Cliente  ".$nuevo." MODIFICADO");'."\n";
			$str .= $tab4.'echo "$mcodp Modificado";'."\n";
			$str .= $tab3.'}'."\n\n";

			$str .= $tab2.'} elseif($oper == \'del\') {'."\n";
			$str .= $tab3.'$meco = $this->datasis->dameval("SELECT $mcodp FROM '.$db.' WHERE id=$id");'."\n";

			$str .= $tab3.'//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM '.$db.' WHERE id=\'$id\' ");'."\n";
			$str .= $tab3.'if ($check > 0){'."\n";
			$str .= $tab4.'echo " El registro no puede ser eliminado; tiene movimiento ";'."\n";
			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'$this->db->simple_query("DELETE FROM '.$db.' WHERE id=$id ");'."\n";
			$str .= $tab4.'logusu(\''.strtoupper($db).'\',"Registro ????? ELIMINADO");'."\n";
			$str .= $tab4.'echo "Registro Eliminado";'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};'."\n";
			$str .= $tab1.'}'."\n";

			$str .= $this->genecrudjq($db, false);

			$str  .= $this->genepre($db,  false);
			$str  .= $this->genepost($db, false);
			$str  .= $this->geneinstalar($db, false);

			$str .= '}'."\n";

			$columna .= $str."\n";

			$data['programa']    = $columna.'?>';
			$data['bd']          = $db;
			$data['controlador'] = $contro;
			$this->load->view('editorcm', $data);

		}
	}


	//******************************************************************
	//
	//  Genera Crud Maestro Detalle para jqGrid
	//
	//******************************************************************
	function jqgridmd(){
		$db = $this->uri->segment(3);
		if($db===false){
			exit('Debe especificar en la uri la tabla Maestro "/maestro/detalle/directorio"');
		}

		$dbit = $this->uri->segment(4);
		if($db===false){
			exit('Debe especificar en la uri la tabla Detalle "/maestro/detalle/directorio"');
		}

		$contro =$this->uri->segment(5);
		if($contro===false){
			$contro = 'CONTROLADOR';
		}

		$query = $this->db->query("DESCRIBE $db");
		$i = 0;
		if ($query->num_rows() > 0){
			$fields  = '';
			$columna = '<pre>';
			$param   = '';
			$campos  = '';
			$str = '';
			$tab1 = $this->mtab(1);
			$tab2 = $this->mtab(2);
			$tab3 = $this->mtab(3);
			$tab4 = $this->mtab(4);
			$tab5 = $this->mtab(5);
			$tab6 = $this->mtab(6);
			$tab7 = $this->mtab(7);
			$tab8 = $this->mtab(8);

			$str .= $this->jqgridclase($db, $contro);


			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'//Layout en la Ventana'."\n";
			$str .= $tab1.'//'."\n";
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'function jqdatag(){'."\n\n";

			$str .= $tab2.'$grid = $this->defgrid();'."\n";
			$str .= $tab2.'$grid->setHeight(\'185\');'."\n";
			$str .= $tab2.'$param[\'grids\'][] = $grid->deploy();'."\n\n";

			$str .= $tab2.'$grid1   = $this->defgridit();'."\n";
			$str .= $tab2.'$grid1->setHeight(\'190\');'."\n";
			$str .= $tab2.'$param[\'grids\'][] = $grid1->deploy();'."\n\n";

			$str .= $tab2.'// Configura los Paneles'."\n";
			$str .= $tab2.'$readyLayout = $grid->readyLayout2( 212, 220, $param[\'grids\'][0][\'gridname\'],$param[\'grids\'][1][\'gridname\']);'."\n\n";

			$str .= $tab2.'//Funciones que ejecutan los botones'."\n";
			$str .= $tab2.'$bodyscript = $this->bodyscript( $param[\'grids\'][0][\'gridname\'], $param[\'grids\'][1][\'gridname\'] );'."\n\n";

			$str .= $tab2.'//Botones Panel Izq'."\n";
			$str .= $tab2.'$grid->wbotonadd(array("id"=>"imprime",  "img"=>"assets/default/images/print.png","alt" => \'Reimprimir\', "label"=>"Reimprimir Documento"));'."\n";
			$str .= $tab2.'$WestPanel = $grid->deploywestp();'."\n\n";

			$str .= $tab2.'//Panel Central'."\n";
			$str .= $tab2.'$centerpanel = $grid->centerpanel( $id = "radicional", $param[\'grids\'][0][\'gridname\'], $param[\'grids\'][1][\'gridname\'] );'."\n\n";

			$str .= $tab2.'$adic = array('."\n";
			$str .= $tab3.'array(\'id\'=>\'fedita\',  \'title\'=>\'Agregar/Editar Registro\'),'."\n";
			$str .= $tab3.'array(\'id\'=>\'fshow\' ,  \'title\'=>\'Mostrar Registro\'),'."\n";
			$str .= $tab3.'array(\'id\'=>\'fborra\',  \'title\'=>\'Eliminar Registro\')'."\n";
			$str .= $tab2.');'."\n";

			$str .= $tab2.'$SouthPanel = $grid->SouthPanel($this->datasis->traevalor(\'TITULO1\'), $adic);'."\n\n";

			$str .= $tab2.'$param[\'WestPanel\']    = $WestPanel;'."\n";
			$str .= $tab2.'$param[\'script\']       = script(\'plugins/jquery.ui.autocomplete.autoSelectOne.js\');'."\n";
			$str .= $tab2.'$param[\'readyLayout\']  = $readyLayout;'."\n";
			$str .= $tab2.'$param[\'SouthPanel\']   = $SouthPanel;'."\n";
			$str .= $tab2.'$param[\'listados\']     = $this->datasis->listados(\''.strtoupper($db).'\', \'JQ\');'."\n";
			$str .= $tab2.'$param[\'otros\']        = $this->datasis->otros(\''.strtoupper($db).'\', \'JQ\');'."\n";
			$str .= $tab2.'$param[\'centerpanel\']  = $centerpanel;'."\n";
			$str .= $tab2.'$param[\'temas\']        = array(\'proteo\',\'darkness\',\'anexos1\');'."\n";
			$str .= $tab2.'$param[\'bodyscript\']   = $bodyscript;'."\n";
			$str .= $tab2.'$param[\'tabs\']         = false;'."\n";
			$str .= $tab2.'$param[\'encabeza\']     = $this->titp;'."\n";
			$str .= $tab2.'$param[\'tamano\']       = $this->datasis->getintramenu( substr($this->url,0,-1) );'."\n";

			$str .= $tab2.'$this->load->view(\'jqgrid/crud2\',$param);'."\n\n";
			$str .= $tab1.'}'."\n\n";



			//**************************************
			//  Funcion de Java del Body
			//
			//
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'//Funciones de los Botones'."\n";
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'function bodyscript( $grid0, $grid1 ){'."\n";
			$str .= $tab2.'$bodyscript = \'';
			$str .= $tab2.'&lt;script type="text/javascript"&gt;\';'."\n\n";


			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'add(){'."\n";
			$str .= $tab3.'$.post("\'.site_url($this->url'.'.\'dataedit/create\').\'",'."\n";
			$str .= $tab3.'function(data){'."\n";
			$str .= $tab4.'$("#fedita").html(data);'."\n";
			$str .= $tab4.'$("#fedita").dialog( "open" );'."\n";
			$str .= $tab3.'})'."\n";
			$str .= $tab2.'};\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'edit(){'."\n";
			$str .= $tab3.'var id     = jQuery("#newapi\'.$grid0.\'").jqGrid(\\\'getGridParam\\\',\\\'selrow\\\');'."\n";
			$str .= $tab3.'if(id){'."\n";
			$str .= $tab4.'var ret    = $("#newapi\'.$grid0.\'").getRowData(id);'."\n";
			$str .= $tab4.'mId = id;'."\n";
			$str .= $tab4.'$.post("\'.site_url($this->url'.'.\'dataedit/modify\').\'/"+id, function(data){'."\n";
			$str .= $tab5.'$("#fedita").html(data);'."\n";
			$str .= $tab5.'$("#fedita").dialog( "open" );'."\n";
			$str .= $tab4.'});'."\n";
			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'$.prompt("&lt;h1&gt;Por favor Seleccione un Registro&lt;/h1&gt;");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'show(){'."\n";
			$str .= $tab3.'var id     = jQuery("#newapi\'.$grid0.\'").jqGrid(\\\'getGridParam\\\',\\\'selrow\\\');'."\n";
			$str .= $tab3.'if(id){'."\n";
			$str .= $tab4.'var ret    = $("#newapi\'.$grid0.\'").getRowData(id);'."\n";
			$str .= $tab4.'mId = id;'."\n";
			$str .= $tab4.'$.post("\'.site_url($this->url'.'.\'dataedit/show\').\'/"+id, function(data){'."\n";
			$str .= $tab5.'$("#fshow").html(data);'."\n";
			$str .= $tab5.'$("#fshow").dialog( "open" );'."\n";
			$str .= $tab4.'});'."\n";
			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'$.prompt("&lt;h1&gt;Por favor Seleccione un Registro&lt;/h1&gt;");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'del() {'."\n";
			$str .= $tab3.'var id = jQuery("#newapi\'.$grid0.\'").jqGrid(\\\'getGridParam\\\',\\\'selrow\\\');'."\n";
			$str .= $tab3.'if(id){'."\n";
			$str .= $tab3.'	if(confirm(" Seguro desea eliminar el registro?")){'."\n";
			$str .= $tab3.'		var ret    = $("#newapi\'.$grid0.\'").getRowData(id);'."\n";
			$str .= $tab3.'		mId = id;'."\n";
			$str .= $tab3.'		$.post("\'.site_url($this->url.\'dataedit/do_delete\').\'/"+id, function(data){'."\n";
			$str .= $tab3.'			try{'."\n";
			$str .= $tab3.'				var json = JSON.parse(data);'."\n";
			$str .= $tab3.'				if (json.status == "A"){'."\n";
			$str .= $tab3.'					apprise("Registro eliminado");'."\n";
			$str .= $tab3.'					jQuery("#newapi\'.$grid0.\'").trigger("reloadGrid");'."\n";
			$str .= $tab3.'				}else{'."\n";
			$str .= $tab3.'					apprise("Registro no se puede eliminado");'."\n";
			$str .= $tab3.'				}'."\n";
			$str .= $tab3.'			}catch(e){'."\n";
			$str .= $tab3.'				$("#fborra").html(data);'."\n";
			$str .= $tab3.'				$("#fborra").dialog( "open" );'."\n";
			$str .= $tab3.'			}'."\n";
			$str .= $tab3.'		});'."\n";
			$str .= $tab3.'	}'."\n";
			$str .= $tab3.'}else{'."\n";
			$str .= $tab3.'	$.prompt("&lt;h1&gt;Por favor Seleccione un Registro&lt;/h1&gt;");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};\';'."\n";


			$str .= $tab2.'//Wraper de javascript'."\n";
			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$(function() {'."\n";
			$str .= $tab3.'$("#dialog:ui-dialog").dialog( "destroy" );'."\n";
			$str .= $tab3.'var mId = 0;'."\n";
			$str .= $tab3.'var montotal = 0;'."\n";
			$str .= $tab3.'var ffecha = $("#ffecha");'."\n";
			$str .= $tab3.'var grid = jQuery("#newapi\'.$grid0.\'");'."\n";
			$str .= $tab3.'var s;'."\n";
			$str .= $tab3.'var allFields = $( [] ).add( ffecha );'."\n";
			$str .= $tab3.'var tips = $( ".validateTips" );'."\n";
			$str .= $tab3.'s = grid.getGridParam(\\\'selarrrow\\\');'."\n";
			$str .= $tab3.'\';'."\n\n";
			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$("#fedita").dialog({'."\n";
			$str .= $tab3.'autoOpen: false, height: 500, width: 700, modal: true,'."\n";
			$str .= $tab3.'buttons: {'."\n";
			$str .= $tab4.'"Guardar": function() {'."\n";
			$str .= $tab5.'var bValid = true;'."\n";
			$str .= $tab5.'var murl = $("#df1").attr("action");'."\n";
			$str .= $tab5.'allFields.removeClass( "ui-state-error" );'."\n";
			$str .= $tab5.'$.ajax({'."\n";
			$str .= $tab6.'type: "POST", dataType: "html", async: false,'."\n";
			$str .= $tab6.'url: murl,'."\n";
			$str .= $tab6.'data: $("#df1").serialize(),'."\n";
			$str .= $tab6.'success: function(r,s,x){'."\n";

			$str .= $tab7.'try{'."\n";
			$str .= $tab8.'var json = JSON.parse(r);'."\n";
			$str .= $tab8.'if (json.status == "A"){'."\n";
			$str .= $tab8.'	apprise("Registro Guardado");'."\n";
			$str .= $tab8.'	$( "#fedita" ).dialog( "close" );'."\n";
			$str .= $tab8.'	grid.trigger("reloadGrid");'."\n";
			$str .= $tab8.'	\'.$this->datasis->jwinopen(site_url(\'formatos/ver/'.strtoupper($db).'\').\'/\\\'+res.id+\\\'/id\\\'\').\';'."\n";
			$str .= $tab8.'	return true;'."\n";
			$str .= $tab8.'} else {'."\n";
			$str .= $tab8.'	apprise(json.mensaje);'."\n";
			$str .= $tab8.'}'."\n";
			$str .= $tab7.'}catch(e){'."\n";
			$str .= $tab7.'	$("#fedita").html(r);'."\n";
			$str .= $tab7.'}'."\n";

			//$str .= $tab6.'if ( r.length == 0 ) {'."\n";
			//$str .= $tab7.'apprise("Registro Guardado");'."\n";
			//$str .= $tab7.'$( "#fedita" ).dialog( "close" );'."\n";
			//$str .= $tab7.'grid.trigger("reloadGrid");'."\n";
			//$str .= $tab7.'\'.$this->datasis->jwinopen(site_url(\'formatos/ver/'.strtoupper($db).'\').\'/\\\'+res.id+\\\'/id\\\'\').\';'."\n";
			//$str .= $tab7.'return true;'."\n";
			//$str .= $tab6.'} else { '."\n";
			//$str .= $tab7.'$("#fedita").html(r);'."\n";
			//$str .= $tab6.'}'."\n";

			$str .= $tab6.'}'."\n";
			//$str .= $tab4.'}'."\n";
			$str .= $tab5.'})'."\n";
			$str .= $tab4.'},'."\n";
			$str .= $tab4.'"Cancelar": function() {'."\n";
			$str .= $tab5.'$("#fedita").html("");'."\n";
			$str .= $tab5.'$( this ).dialog( "close" );'."\n";
			$str .= $tab4.'}'."\n";
			$str .= $tab3.'},'."\n";
			$str .= $tab3.'close: function() {'."\n";
			$str .= $tab4.'$("#fedita").html("");'."\n";
			$str .= $tab4.'allFields.val( "" ).removeClass( "ui-state-error" );'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'});\';'."\n\n";
			//$str .= $tab2.'});'."\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$("#fshow").dialog({'."\n";
			$str .= $tab3.'autoOpen: false, height: 500, width: 700, modal: true,'."\n";
			$str .= $tab3.'buttons: {'."\n";
			$str .= $tab4.'"Aceptar": function() {'."\n";
			$str .= $tab5.'$("#fshow").html("");'."\n";
			$str .= $tab5.'$( this ).dialog( "close" );'."\n";
			$str .= $tab4.'},'."\n";
			$str .= $tab3.'},'."\n";
			$str .= $tab3.'close: function() {'."\n";
			$str .= $tab4.'$("#fshow").html("");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'});\''.";\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$("#fborra").dialog({'."\n";
			$str .= $tab3.'autoOpen: false, height: 300, width: 400, modal: true,'."\n";
			$str .= $tab3.'buttons: {'."\n";
			$str .= $tab4.'"Aceptar": function() {'."\n";
			$str .= $tab5.'$("#fborra").html("");'."\n";
			$str .= $tab5.'jQuery("#newapi\'.$grid0.\'").trigger("reloadGrid");'."\n";
			$str .= $tab5.'$( this ).dialog( "close" );'."\n";
			$str .= $tab4.'},'."\n";
			$str .= $tab3.'},'."\n";
			$str .= $tab3.'close: function() {'."\n";
			$str .= $tab4.'jQuery("#newapi\'.$grid0.\'").trigger("reloadGrid");'."\n";
			$str .= $tab4.'$("#fborra").html("");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'});\''.";\n\n";
			$str .= $tab2.'$bodyscript .= \'});\'."\n";'."\n\n";

			$str .= $tab2.'$bodyscript .= "\n&lt;/script&gt;\n";'."\n";
			$str .= $tab2.'$bodyscript .= "";'."\n";
			$str .= $tab2.'return $bodyscript;'."\n";
			$str .= $tab1."}\n\n";

			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'//Definicion del Grid y la Forma'."\n";
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'function defgrid( $deployed = false ){'."\n";
			$str .= $tab2.'$i      = 1;'."\n";
			$str .= $tab2.'$editar = "false";'."\n\n";
			$str .= $tab2.'$grid  = new $this->jqdatagrid;'."\n\n";
			$columna .= $str;
			$str = '';

			$columna .= $this->jqgridcol($db);

			$str  = $tab2.'$grid->showpager(true);'."\n";
			$str .= $tab2.'$grid->setWidth(\'\');'."\n";
			$str .= $tab2.'$grid->setHeight(\'290\');'."\n";
			$str .= $tab2.'$grid->setTitle($this->titp);'."\n";
			$str .= $tab2.'$grid->setfilterToolbar(true);'."\n";
			$str .= $tab2.'$grid->setToolbar(\'false\', \'"top"\');'."\n\n";

			$str .= $tab2.'$grid->setOnSelectRow(\''."\n";
			$str .= $tab3.'function(id){'."\n";
			$str .= $tab4.'if (id){'."\n";
			$str .= $tab5.'jQuery(gridId2).jqGrid("setGridParam",{url:"\'.site_url($this->url.\'getdatait/\').\'/"+id+"/", page:1});'."\n";
			$str .= $tab5.'jQuery(gridId2).trigger("reloadGrid");'."\n";
			$str .= $tab4.'}'."\n";
			$str .= $tab3.'}\''."\n";
			$str .= $tab2.');'."\n";

			$str .= $tab2.'$grid->setFormOptionsE(\'closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} \');'."\n";
			$str .= $tab2.'$grid->setFormOptionsA(\'closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} \');'."\n";

			$str .= $tab2.'$grid->setAfterSubmit("$(\'#respuesta\').html(\'&lt;span style='."\\'font-weight:bold; color:red;\\'&gt;'+a.responseText+'&lt;/span&gt;'); return [true, a ];".'");'."\n\n";

			$str .= $tab2.'#show/hide navigations buttons'."\n";
			$str .= $tab2.'$grid->setAdd(    $this->datasis->sidapuede(\''.strtoupper($db).'\',\'INCLUIR%\' ));'."\n";
			$str .= $tab2.'$grid->setEdit(   $this->datasis->sidapuede(\''.strtoupper($db).'\',\'MODIFICA%\'));'."\n";
			$str .= $tab2.'$grid->setDelete( $this->datasis->sidapuede(\''.strtoupper($db).'\',\'BORR_REG%\'));'."\n";
			$str .= $tab2.'$grid->setSearch( $this->datasis->sidapuede(\''.strtoupper($db).'\',\'BUSQUEDA%\'));'."\n";
			$str .= $tab2.'$grid->setRowNum(30);'."\n";

			$str .= $tab2.'$grid->setShrinkToFit(\'false\');'."\n\n";

			$str .= $tab2.'$grid->setBarOptions("addfunc: '.strtolower($db).'add, editfunc: '.strtolower($db).'edit, delfunc: '.strtolower($db).'del, viewfunc: '.strtolower($db).'show");'."\n\n";


			$str .= $tab2.'#Set url'."\n";
			$str .= $tab2.'$grid->setUrlput(site_url($this->url.\'setdata/\'));'."\n\n";

			$str .= $tab2.'#GET url'."\n";
			$str .= $tab2.'$grid->setUrlget(site_url($this->url.\'getdata/\'));'."\n\n";

			$str .= $tab2.'if ($deployed) {'."\n";
			$str .= $tab2.'	return $grid->deploy();'."\n";
			$str .= $tab2.'} else {'."\n";
			$str .= $tab2.'	return $grid;'."\n";
			$str .= $tab2.'}'."\n";
			$str .= $tab1.'}'."\n\n";

			$str .= $tab1.'/**'."\n";
			$str .= $tab1.'* Busca la data en el Servidor por json'."\n";
			$str .= $tab1.'*/'."\n";
			$str .= $tab1.'function getdata()'."\n";
			$str .= $tab1.'{'."\n";

			$str .= $tab2.'$grid       = $this->jqdatagrid;'."\n\n";

			$str .= $tab2.'// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO'."\n";
			$str .= $tab2.'$mWHERE = $grid->geneTopWhere(\''.$db.'\');'."\n\n";

			$str .= $tab2.'$response   = $grid->getData(\''.$db.'\', array(array()), array(), false, $mWHERE, \'id\',\'desc\' );'."\n";
			$str .= $tab2.'$rs = $grid->jsonresult( $response);'."\n";
			$str .= $tab2.'echo $rs;'."\n";

			$str .= $tab1.'}'."\n\n";

			$str .= $tab1.'/**'."\n";
			$str .= $tab1.'* Guarda la Informacion'."\n";
			$str .= $tab1.'*/'."\n";
			$str .= $tab1.'function setData()'."\n";
			$str .= $tab1.'{'."\n";
			$str .= $tab2.'$this->load->library(\'jqdatagrid\');'."\n";
			$str .= $tab2.'$oper   = $this->input->post(\'oper\');'."\n";
			$str .= $tab2.'$id     = $this->input->post(\'id\');'."\n";
			$str .= $tab2.'$data   = $_POST;'."\n";
			$str .= $tab2.'$mcodp  = "??????";'."\n";
			$str .= $tab2.'$check  = 0;'."\n\n";

			$str .= $tab2.'unset($data[\'oper\']);'."\n";
			$str .= $tab2.'unset($data[\'id\']);'."\n";

			$str .= $tab2.'if($oper == \'add\'){'."\n";
			$str .= $tab3.'if(false == empty($data)){'."\n";
			$str .= $tab4.'$check = $this->datasis->dameval("SELECT count(*) FROM '.$db.' WHERE $mcodp=".$this->db->escape($data[$mcodp]));'."\n";
			$str .= $tab4.'if ( $check == 0 ){'."\n";
			$str .= $tab5.'$this->db->insert(\''.$db.'\', $data);'."\n";
			$str .= $tab5.'echo "Registro Agregado";'."\n\n";
			$str .= $tab5.'logusu(\''.strtoupper($db).'\',"Registro ????? INCLUIDO");'."\n";
			$str .= $tab4.'} else'."\n";
			$str .= $tab5.'echo "Ya existe un registro con ese $mcodp";'."\n";

			$str .= $tab3.'} else'."\n";
			//$str .= $tab2.'echo \'\';'."\n";
			$str .= $tab4.'echo "Fallo Agregado!!!";'."\n\n";

			$str .= $tab2.'} elseif($oper == \'edit\') {'."\n";
			$str .= $tab3.'$nuevo  = $data[$mcodp];'."\n";
			$str .= $tab3.'$anterior = $this->datasis->dameval("SELECT $mcodp FROM '.$db.' WHERE id=$id");'."\n";
			$str .= $tab3.'if ( $nuevo <> $anterior ){'."\n";
			$str .= $tab4.'//si no son iguales borra el que existe y cambia'."\n";
			$str .= $tab4.'$this->db->query("DELETE FROM '.$db.' WHERE $mcodp=?", array($mcodp));'."\n";
			$str .= $tab4.'$this->db->query("UPDATE '.$db.' SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));'."\n";
			$str .= $tab4.'$this->db->where("id", $id);'."\n";
			$str .= $tab4.'$this->db->update("'.$db.'", $data);'."\n";
			$str .= $tab4.'logusu(\''.strtoupper($db).'\',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");'."\n";
			$str .= $tab4.'echo "Grupo Cambiado/Fusionado en clientes";'."\n";

			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'unset($data[$mcodp]);'."\n";
			$str .= $tab4.'$this->db->where("id", $id);'."\n";
			$str .= $tab4.'$this->db->update(\''.$db.'\', $data);'."\n";
			$str .= $tab4.'logusu(\''.strtoupper($db).'\',"Grupo de Cliente  ".$nuevo." MODIFICADO");'."\n";
			$str .= $tab4.'echo "$mcodp Modificado";'."\n";
			$str .= $tab3.'}'."\n\n";

			$str .= $tab2.'} elseif($oper == \'del\') {'."\n";
			$str .= $tab3.'$meco = $this->datasis->dameval("SELECT $mcodp FROM '.$db.' WHERE id=$id");'."\n";

			$str .= $tab3.'//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM '.$db.' WHERE id=\'$id\' ");'."\n";
			$str .= $tab3.'if ($check > 0){'."\n";
			$str .= $tab4.'echo " El registro no puede ser eliminado; tiene movimiento ";'."\n";
			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'$this->db->simple_query("DELETE FROM '.$db.' WHERE id=$id ");'."\n";
			$str .= $tab4.'logusu(\''.strtoupper($db).'\',"Registro ????? ELIMINADO");'."\n";
			$str .= $tab4.'echo "Registro Eliminado";'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};'."\n";
			$str .= $tab1.'}'."\n\n";


			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'//Definicion del Grid y la Forma'."\n";
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'function defgridit( $deployed = false ){'."\n";
			$str .= $tab2.'$i      = 1;'."\n";
			$str .= $tab2.'$editar = "false";'."\n\n";
			$str .= $tab2.'$grid  = new $this->jqdatagrid;'."\n\n";
			$columna .= $str;
			$str = '';

			$columna .= $this->jqgridcol($dbit);

			$str  = $tab2.'$grid->showpager(true);'."\n";
			$str  = $tab2.'$grid->setWidth("");'."\n";
			$str  = $tab2.'$grid->setHeight(\'190\');'."\n";
			$str  = $tab2.'$grid->setfilterToolbar(false);'."\n";
			$str  = $tab2.'$grid->setToolbar(\'false\', \'"top"\');'."\n";

			$str  = $tab2.'#show/hide navigations buttons'."\n";
			$str  = $tab2.'$grid->setAdd(false);'."\n";
			$str  = $tab2.'$grid->setEdit(false);'."\n";
			$str  = $tab2.'$grid->setDelete(false);'."\n";
			$str  = $tab2.'$grid->setSearch(true);'."\n";
			$str  = $tab2.'$grid->setRowNum(30);'."\n";
			$str  = $tab2.'$grid->setShrinkToFit(\'false\');'."\n";


			$str .= $tab2.'#Set url'."\n";
			$str .= $tab2.'$grid->setUrlput(site_url($this->url.\'setdatait/\'));'."\n\n";

			$str .= $tab2.'#GET url'."\n";
			$str .= $tab2.'$grid->setUrlget(site_url($this->url.\'getdatait/\'));'."\n\n";

			$str .= $tab2.'if ($deployed) {'."\n";
			$str .= $tab2.'	return $grid->deploy();'."\n";
			$str .= $tab2.'} else {'."\n";
			$str .= $tab2.'	return $grid;'."\n";
			$str .= $tab2.'}'."\n";
			$str .= $tab1.'}'."\n\n";

			$str .= $tab1.'/**'."\n";
			$str .= $tab1.'* Busca la data en el Servidor por json'."\n";
			$str .= $tab1.'*/'."\n";
			$str .= $tab1.'function getdatait( $id = 0 )'."\n";
			$str .= $tab1.'{'."\n";

			$str .= $tab2.'if ($id === 0 ){'."\n";
			$str .= $tab3.'$id = $this->datasis->dameval("SELECT MAX(id) FROM '.$db.'");'."\n";
			$str .= $tab2.'}'."\n";
			$str .= $tab2.'if(empty($id)) return "";'."\n";
			$str .= $tab2.'$numero   = $this->datasis->dameval("SELECT numero FROM '.$db.' WHERE id=$id");'."\n";

			$str .= $tab2.'$grid    = $this->jqdatagrid;'."\n";
			$str .= $tab2.'$mSQL    = "SELECT * FROM '.$dbit.' WHERE numero=\'$numero\' ";'."\n";
			$str .= $tab2.'$response   = $grid->getDataSimple($mSQL);'."\n";
			$str .= $tab2.'$rs = $grid->jsonresult( $response);'."\n";
			$str .= $tab2.'echo $rs;'."\n";

			$str .= $tab1.'}'."\n\n";

			$str .= $tab1.'/**'."\n";
			$str .= $tab1.'* Guarda la Informacion'."\n";
			$str .= $tab1.'*/'."\n";
			$str .= $tab1.'function setDatait()'."\n";
			$str .= $tab1.'{'."\n";
			$str .= $tab1.'}'."\n\n";


			$str .= $tab1.'//***********************************'."\n";
			$str .= $tab1.'// DataEdit  '."\n";
			$str .= $tab1.'//***********************************'."\n";

			$str .= $this->genecrudjq($db, false);

			$str .= $this->genepre( $db, false);
			$str .= $this->genepost($db, false);
			$str .= $this->geneinstalar($db, false);

			$str .= '}'."\n";

			$columna .= $str."\n";

			echo $columna."</pre>";

		}

	}


	//********************************
	// Genera la clase
	//********************************
	function jqgridclase($db, $contro){
		$tab1 = $this->mtab(1);
		$tab2 = $this->mtab(2);
		$tab3 = $this->mtab(3);

		$str  = '';
		$str .= 'class '.ucfirst($db).' extends Controller {'."\n";
		$str .= $tab1.'var $mModulo = \''.strtoupper($db).'\';'."\n";
		$str .= $tab1.'var $titp    = \'Modulo '.strtoupper($db).'\';'."\n";
		$str .= $tab1.'var $tits    = \'Modulo '.strtoupper($db).'\';'."\n";
		$str .= $tab1.'var $url     = \''.$contro.'/'.$db.'/\';'."\n\n";

		$str .= $tab1.'function '.ucfirst($db).'(){'."\n";
		$str .= $tab2.'parent::Controller();'."\n";
		$str .= $tab2.'$this->load->library(\'rapyd\');'."\n";
		$str .= $tab2.'$this->load->library(\'jqdatagrid\');'."\n";
		$str .= $tab2.'$this->datasis->modulo_nombre( \''.strtoupper($db).'\', $ventana=0 );'."\n";
		$str .= $tab1.'}'."\n\n";

		$str .= $tab1.'function index(){'."\n";
		$str .= $tab2.'/*if ( !$this->datasis->iscampo(\''.$db.'\',\'id\') ) {'."\n";
		$str .= $tab3.'$this->db->simple_query(\'ALTER TABLE '.$db.' DROP PRIMARY KEY\');'."\n";
		$str .= $tab3.'$this->db->simple_query(\'ALTER TABLE '.$db.' ADD UNIQUE INDEX numero (numero)\');'."\n";
		$str .= $tab3.'$this->db->simple_query(\'ALTER TABLE '.$db.' ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)\');'."\n";
		$str .= $tab2.'};*/'."\n";

		$str .= $tab2.'//$this->datasis->creaintramenu(array(\'modulo\'=>\'000\',\'titulo\'=>\'<#titulo#>\',\'mensaje\'=>\'<#mensaje#>\',\'panel\'=>\'<#panal#>\',\'ejecutar\'=>\'<#ejecuta#>\',\'target\'=>\'popu\',\'visible\'=>\'S\',\'pertenece\'=>\'<#pertenece#>\',\'ancho\'=>900,\'alto\'=>600));';

		$str .= $tab2.'$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );'."\n";
		$str .= $tab2.'redirect($this->url.\'jqdatag\');'."\n";
		$str .= $tab1.'}'."\n\n";

		return $str;

	}


	//************************************
	//
	//Genera las Columnas
	//
	function jqgridcol($db){
		$tab1 = $this->mtab(1);
		$tab2 = $this->mtab(2);
		$tab3 = $this->mtab(3);
		$tab4 = $this->mtab(4);
		$tab5 = $this->mtab(5);
		$tab6 = $this->mtab(6);
		$tab7 = $this->mtab(7);
		$tab8 = $this->mtab(8);

		$query = $this->db->query("DESCRIBE $db");
		$columna = '';
		$str     = '';
		foreach ($query->result() as $row){
			if ( $row->Field == 'id') {
				$str   = $tab2.'$grid->addField(\'id\');'."\n";
				$str  .= $tab2.'$grid->label(\'Id\');'."\n";
				$str  .= $tab2.'$grid->params(array('."\n";
				$str  .= $tab3.'\'align\'         => "\'center\'",'."\n";
				$str  .= $tab3.'\'frozen\'        => \'true\','."\n";
				$str  .= $tab3.'\'width\'         => 40,'."\n";
				$str  .= $tab3.'\'editable\'      => \'false\','."\n";
				$str  .= $tab3.'\'search\'        => \'false\''."\n";
			} else {
				$str  = $tab2.'$grid->addField(\''.$row->Field.'\');'."\n";
				$str .= $tab2.'$grid->label(\''.ucfirst($row->Field).'\');'."\n";

				$str .= $tab2.'$grid->params(array('."\n";
				$str .= $tab3.'\'search\'        => \'true\','."\n";
				$str .= $tab3.'\'editable\'      => $editar,'."\n";

				if ( $row->Type == 'date' or $row->Type == 'timestamp' ) {
					$str .= $tab3.'\'width\'         => 80,'."\n";
					$str .= $tab3.'\'align\'         => "\'center\'",'."\n";
					$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
					$str .= $tab3.'\'editrules\'     => \'{ required:true,date:true}\','."\n";
					$str .= $tab3.'\'formoptions\'   => \'{ label:"Fecha" }\''."\n";

				} elseif ( substr($row->Type,0,7) == 'decimal' or substr($row->Type,0,3) == 'int'  ) {
					$str .= $tab3.'\'align\'         => "\'right\'",'."\n";
					$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
					$str .= $tab3.'\'width\'         => 100,'."\n";
					$str .= $tab3.'\'editrules\'     => \'{ required:true }\','."\n";
					$str .= $tab3.'\'editoptions\'   => \'{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }\','."\n";
					$str .= $tab3.'\'formatter\'     => "\'number\'",'."\n";
					if (substr($row->Type,0,3) == 'int'){
						$str .= $tab3.'\'formatoptions\' => \'{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }\''."\n";
					} else {
						$str .= $tab3.'\'formatoptions\' => \'{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }\''."\n";
					}

				} elseif ( substr($row->Type,0,7) == 'varchar' or substr($row->Type,0,4) == 'char'  ) {
					$long = str_replace(array('varchar(','char(',')'),"", $row->Type)*10;
					$maxlong = $long/10;
					if ( $long > 200 ) $long = 200;
					if ( $long < 40 ) $long = 40;

					$str .= $tab3.'\'width\'         => '.$long.','."\n";
					$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
					$str .= $tab3.'\'editrules\'     => \'{ required:true}\','."\n";
					$str .= $tab3.'\'editoptions\'   => \'{ size:'.$maxlong.', maxlength: '.$maxlong.' }\','."\n";

				} elseif ( $row->Type == 'text' ) {
					$long = 250;
					$str .= $tab3.'\'width\'         => '.$long.','."\n";
					$str .= $tab3.'\'edittype\'      => "\'textarea\'",'."\n";
					$str .= $tab3.'\'editoptions\'   => "\'{rows:2, cols:60}\'",'."\n";

				} else {
					$str .= $tab3.'\'width\'         => 140,'."\n";
					$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
				}
			}
			$str .= $tab2.'));'."\n\n";
			$columna .= $str."\n";
		}
		return $columna;
	}


	// Genera un jqgrid simple a partir de una tabla
	function jqgridsimple(){
		$tabla = $this->uri->segment(3);
		if($tabla===false){
			exit('Debe especificar en la uri la tabla y el directorio "/tabla/controlador/directorio/id"');
		}

		$contro =$this->uri->segment(4);
		if($contro===false){
			exit('Debe especificar en la uri la tabla y el directorio "/tabla/controlador/directorio/id"');
		}

		$directo =$this->uri->segment(5);
		if($directo===false){
			exit('Debe especificar en la uri la tabla y el directorio "/tabla/controlador/directorio/id"');
		}
		$id =$this->uri->segment(6);
		if($id==false){
			exit('Debe especificar en la uri la tabla y el directorio "/tabla/controlador/directorio/id"');
		}
		$str = $this->datasis->jqgridsimplegene($tabla, $contro, $directo, $id);
		echo "<pre>".$str."</pre>";

	}


	function mtab($n = 1){ return str_repeat("\t",$n); }

	//******************************************************************
	// Gener Crud
	function genecrudjq($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla)))
			show_error('Tabla no existe o faltan parametros');

		$crud ="\n\t".'function dataedit(){'."\n";
		$crud.="\t\t".'$this->rapyd->load(\'dataedit\');'."\n";

		$crud.="\t\t".'$script= \''."\n";
		$crud.="\t\t".'$(function() {'."\n";
		$crud.="\t\t\t".'$("#fecha").datepicker({dateFormat:"dd/mm/yy"});'."\n";
		$crud.="\t\t".'});'."\n";
		$crud.="\t\t".'\';'."\n\n";

		$crud.="\t\t".'$edit = new DataEdit($this->tits, \''.$tabla.'\');'."\n\n";
		$crud.="\t\t".'$edit->script($script,\'modify\');'."\n";
		$crud.="\t\t".'$edit->script($script,\'create\');'."\n";
		$crud.="\t\t".'$edit->on_save_redirect=false;'."\n\n";
		$crud.="\t\t".'$edit->back_url = site_url($this->url.\'filteredgrid\');'."\n\n";

		$crud.="\t\t".'$edit->script($script,\'create\');'."\n\n";
		$crud.="\t\t".'$edit->script($script,\'modify\');'."\n\n";

		$crud.="\t\t".'$edit->post_process(\'insert\',\'_post_insert\');'."\n";
		$crud.="\t\t".'$edit->post_process(\'update\',\'_post_update\');'."\n";
		$crud.="\t\t".'$edit->post_process(\'delete\',\'_post_delete\');'."\n";
		$crud.="\t\t".'$edit->pre_process(\'insert\', \'_pre_insert\' );'."\n";
		$crud.="\t\t".'$edit->pre_process(\'update\', \'_pre_update\' );'."\n";
		$crud.="\t\t".'$edit->pre_process(\'delete\', \'_pre_delete\' );'."\n";

		$crud.="\n";

		$crud.="\t\t".'$script= \' '."\n";
		$crud.="\t\t".'$(function() {'."\n";
		$crud.="\t\t\t".'$("#fecha").datepicker({dateFormat:"dd/mm/yy"});'."\n";
		$crud.="\t\t".'});';
		$crud.="\t\t".'\';'."\n";

		$crud.="\t\t".'$edit->script($script,\'create\');'."\n";
		$crud.="\t\t".'$edit->script($script,\'modify\');'."\n";
		$crud.="\n";

		$mSQL="DESCRIBE $tabla";
		$query = $this->db->query("DESCRIBE $tabla");
		foreach ($query->result() as $field){

			if($field->Field=='usuario'){
				$crud.="\t\t".'$edit->usuario = new autoUpdateField(\'usuario\',$this->session->userdata(\'usuario\'),$this->session->userdata(\'usuario\'));'."\n\n";
			}elseif($field->Field=='estampa'){
				$crud.="\t\t".'$edit->estampa = new autoUpdateField(\'estampa\' ,date(\'Ymd\'), date(\'Ymd\'));'."\n\n";
			}elseif($field->Field=='hora'){
				$crud.="\t\t".'$edit->hora    = new autoUpdateField(\'hora\',date(\'H:i:s\'), date(\'H:i:s\'));'."\n\n";
			}elseif($field->Field=='id'){
				continue;
			}else{
				preg_match('/(?P<tipo>\w+)(\((?P<length>[0-9\,]+)\)){0,1}/', $field->Type, $matches);
				if(isset($matches['length'])){
					$def=explode(',',$matches['length']);
				}else{
					$def[0]=8;
				}

				if(strrpos($field->Type,'date')!==false){
					$input='dateonly';
				}elseif(strrpos($field->Type,'text')!==false){
					$input= 'textarea';
				}else{
					$input='input';
				}

				$crud.="\t\t".'$edit->'.$field->Field.' = new '.$input."Field('".ucfirst($field->Field)."','$field->Field');\n";

				if(preg_match("/decimal/i",$field->Type)){
					$crud.="\t\t".'$edit->'.$field->Field."->rule='numeric';\n";
					$crud.="\t\t".'$edit->'.$field->Field."->css_class='inputnum';\n";

				}elseif(preg_match("/integer|int/i",$field->Type)){
					$crud.="\t\t".'$edit->'.$field->Field."->rule='integer';\n";
					$crud.="\t\t".'$edit->'.$field->Field."->css_class='inputonlynum';\n";

				}elseif(preg_match("/date/i",$field->Type)){
					$crud.="\t\t".'$edit->'.$field->Field."->rule='chfecha';\n";

				}else{
					$crud.="\t\t".'$edit->'.$field->Field."->rule='';\n";
				}

				if(strrpos($field->Type,'text')===false){
					$crud.="\t\t".'$edit->'.$field->Field.'->size ='.($def[0]+2).";\n";
					$crud.="\t\t".'$edit->'.$field->Field.'->maxlength ='.($def[0]).";\n";
				}else{
					$crud.="\t\t".'$edit->'.$field->Field."->cols = 70;\n";
					$crud.="\t\t".'$edit->'.$field->Field."->rows = 4;\n";
				}
				$crud.="\n";
			}
		}

		$crud.="\t\t".'$edit->build();'."\n\n";

		$crud.="\t\t".'if($edit->on_success()){'."\n";
		$crud.="\t\t".'	$rt=array('."\n";
		$crud.="\t\t".'		\'status\' =>\'A\','."\n";
		$crud.="\t\t".'		\'mensaje\'=>\'Registro guardado\','."\n";
		$crud.="\t\t".'		\'pk\'     =>$edit->_dataobject->pk'."\n";
		$crud.="\t\t".'	);'."\n";
		$crud.="\t\t".'	echo json_encode($rt);'."\n";
		$crud.="\t\t".'}else{'."\n";
		$crud.="\t\t".'	echo $edit->output;'."\n";
		$crud.="\t\t".'}'."\n";

		$crud.="\t".'}'."\n";

		if($s){
			$data['programa'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('editorcm', $data);
			//$this->load->view('jqgrid/ventanajq', $data);
		}else{
			return $crud;
		}
	}

	//******************************************************************
	//    Genera el View a partir de la Tabla
	//******************************************************************
	function geneviewjq($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla)))
			show_error('Tabla no existe o faltan parametros');

		$crud  ="\t".'<?php'."\n";
		$crud .="\t".'echo $form_scripts;'."\n";
		$crud .="\t".'echo $form_begin;'."\n\n";
		$crud .="\t".'if(isset($form->error_string)) echo \'<div class="alert">\'.$form->error_string.\'</div>\';'."\n";
		$crud .="\t".'if($form->_status <> \'show\'){ ?>'."\n\n";
		$crud .="\t".'<script language="javascript" type="text/javascript">'."\n";
		$crud .="\t".'</script>'."\n";
		$crud .="\t".'<?php } ?>'."\n\n";
		$crud .="\t".'<fieldset  style=\'border: 1px outset #FEB404;background: #FFFCE8;\'>'."\n";
		$crud .="\t".'<table width=\'100%\'>'."\n";

		$mSQL ="DESCRIBE $tabla";
		$query = $this->db->query("DESCRIBE $tabla");
		foreach ($query->result() as $field){
			$crud .="\t".'	<tr>'."\n";
			$crud .="\t".'		<td class="littletablerowth"><?php echo $form->'.$field->Field.'->label;  ?></td>'."\n";
			$crud .="\t".'		<td class="littletablerow"  ><?php echo $form->'.$field->Field.'->output; ?></td>'."\n";
			$crud .="\t".'	</tr>'."\n";
		}

		$crud .="\t".'</table>'."\n";
		$crud .="\t".'</fieldset>'."\n";
		$crud .="\t".'<?php echo $form_end; ?>'."\n";

		echo '<html><body><pre>'.htmlentities( $crud).'</pre></body></html>';

	}


	function editor(){
			$this->load->view('editorcm');
	}

	function jqguarda(){
		$code   = $this->input->post('code');
		$db     = $this->input->post('bd');
		$contro = $this->input->post('contro');
		file_put_contents('system/application/controllers/'.$contro.'/'.$db.'.php',$code);
		redirect($this->url.'desarrollo/jqcargar/'.$db.'/'.$contro);
	}

	function jqcargar(){
		$db = $this->uri->segment(3);
		if($db===false){
			exit('Debe especificar en la uri la tabla y el directorio "/tabla/directorio"');
		}
		$contro =$this->uri->segment(4);
		if($contro===false){
			$contro = '';
		}
		if ( $contro == '' )
			$leer = file_get_contents('system/application/controllers/'.$db.'.php');
		else
			$leer = file_get_contents('system/application/controllers/'.$contro.'/'.$db.'.php');

		$data['programa']    = $leer;
		$data['bd']          = $db;
		$data['controlador'] = $contro;
		$this->load->view('editorcm', $data);

	}

	function ccc(){
		print_r($this->datasis->controladores());
	}


}
