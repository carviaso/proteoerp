<?php
/**
  ProteoERP
 
  @autor    Andres Hocevar
  @license  GNU GPL v3
*/

class Desarrollo extends Controller{

	function Desarrollo(){
		parent::Controller();
	}

	function index(){

		$styles  = "\n<!-- Estilos -->\n";
		$styles .= style('rapyd.css');
		$styles .= style('ventanas.css');
		$styles .= style('themes/proteo/proteo.css');
		$styles .= style("themes/ui.jqgrid.css");
		$styles .= style("themes/ui.multiselect.css");
		$styles .= style('layout1.css');


		$styles .= '
<style type="text/css">
	p {font-size:1em; margin: 1ex 0;}
	p.buttons {text-align:center;line-height:2.5em;}
	button {line-height: normal;}
	.hidden {display: none;}
	ul {z-index:100000;margin:1ex 0;padding:0;list-style:none;cursor:pointer;border:1px solid Black;width:15ex;position:	relative;}
	ul li {background-color: #EEE;padding: 0.15em 1em 0.3em 5px;}
	ul ul {display:none;position:absolute;width:100%;left:-1px;bottom:0;margin:0;margin-bottom: 1.55em;}
	.ui-layout-north ul ul {bottom:auto;margin:0;margin-top:1.45em;}
	ul ul li { padding: 3px 1em 3px 5px; }
	ul ul li:hover { background-color: #FF9; }
	ul li:hover ul { display:block; background-color: #EEE; }

	#feedback { font-size: 0.8em; }
	#tablas .ui-selecting { background: #FECA40; }
	#tablas .ui-selected { background: #F39814; color: white; }
	#tablas { list-style-type: none; margin: 0; padding: 0; width: 90%; }
	#tablas li { margin: 1px; padding: 0em; font-size: 0.8em; height: 14px; }
</style>
</style>
';

		$title = "
<div id='encabe'>
<table width='98%'>
	<tr>
		<td>".heading('Herramientas de Desarrollo')."</td>
		<td align='right' width='40'>".image('cerrar.png','Cerrar Ventana',array('onclick'=>'window.close()','height'=>'20'))."</td>
	</tr>
</table>
</div>
";


		$script  = "\n<!-- JQUERY -->\n";
		$script .= script('jquery-min.js');
		$script .= script('jquery-migrate-min.js');
		$script .= script('jquery-ui.custom.min.js');

		$script .= script("jquery.layout.js");
		$script .= script("i18n/grid.locale-sp.js");

		$script .= script("ui.multiselect.js");
		$script .= script("jquery.jqGrid.min.js");
		$script .= script("jquery.tablednd.js");
		$script .= script("jquery.contextmenu.js");

		$script .= '
<script type="text/javascript">
';

		$script .= '
	// set EVERY state here so will undo ALL layout changes
	// used by the Reset State button: myLayout.loadState( stateResetSettings )
	var stateResetSettings = {
		north__size:		"auto"
	,	north__initClosed:	false
	,	north__initHidden:	false
	,	south__size:		"auto"
	,	south__initClosed:	false
	,	south__initHidden:	false
	,	west__size:			200
	,	west__initClosed:	false
	,	west__initHidden:	false
	,	east__size:			300
	,	east__initClosed:	false
	,	east__initHidden:	false
	};

	var myLayout;

	$(document).ready(function () {

		// this layout could be created with NO OPTIONS - but showing some here just as a sample...
		// myLayout = $("body").layout(); -- syntax with No Options

		myLayout = $("body").layout({

		//	reference only - these options are NOT required because "true" is the default
			closable: true,	resizable:	true, slidable:	true, livePaneResizing:	true
		//	some resizing/toggling settings
		,	north__slidable: false, north__togglerLength_closed: "100%", north__spacing_closed:	20
		,	south__resizable:false,	south__spacing_open:0
		,	south__spacing_closed:20
		//	some pane-size settings
		,	west__minSize: 100, east__size: 300, east__minSize: 200, east__maxSize: .5, center__minWidth: 100
		//	some pane animation settings
		,	west__animatePaneSizing: false,	west__fxSpeed_size:	"fast",	west__fxSpeed_open: 1000
		,	west__fxSettings_open:{ easing: "easeOutBounce" },	west__fxName_close:"none"
		//	enable showOverflow on west-pane so CSS popups will overlap north pane
		//,	west__showOverflowOnHover:	true
		,	stateManagement__enabled:true, showDebugMessages: true
		});
 	});

	$(function() {
		$("#tablas").selectable({
			selected: function( event, ui ) {
				if ( $("#tabla1").val() == "" ) 
					$("#tabla1").val(ui.selected.id);
				else 
					$("#tabla2").val(ui.selected.id);
			}
		});
	});

	function camposdb() { 
		$.post("'.site_url('desarrollo/camposdb')."/".'"+$("#tabla1").val(),
		function(data){
			$("#resultado").html("");
			$("#resultado").html(data);
		});
	};
	
	function lcamposdb () { 
		$.post("'.site_url('desarrollo/lcamposdb')."/".'"+$("#tabla1").val(),
		function(data){
			$("#resultado").html("");
			$("#resultado").html(data);
		});
	};

	function ccamposdb () { 
		$.post("'.site_url('desarrollo/ccamposdb')."/".'"+$("#tabla1").val(),
		function(data){
			
			$("#resultado").html(data);
		});
	};

	function jqgrid () { 
		window.open(\''.site_url('desarrollo/jqgrid').'/\'+$("#tabla1").val()+"/"+$("#modulo").val(), \'_blank\', \'width=900, height=700, scrollbars=yes, status=yes, resizable=yes,screenx=((screen.availHeight/2)-350), screeny=((screen.availWidth/2)-450)\');
	};

	function jqgridmd () { 
		window.open(\''.site_url('desarrollo/jqgridmd').'/\'+$("#tabla1").val()+"/"+$("#tabla2").val()+"/"+$("#modulo").val(), \'_blank\', \'width=900, height=700, scrollbars=yes, status=yes, resizable=yes,screenx=((screen.availHeight/2)-350), screeny=((screen.availWidth/2)-450)\');
	};


</script>
';

// ENCABEZADO
$tabla = '
<div class="ui-layout-north" onmouseover="myLayout.allowOverflow(\'north\')" onmouseout="myLayout.resetOverflow(this)">
<table width="100%" bgcolor="#2067B5">
	<tr>
		<td align="left" width="80px"><img src="'.base_url().'assets/default/css/templete_01.jpg" width="120"></td><td align="center"><h1 style="font-size: 20px; color: rgb(255, 255, 255);" onclick="history.back()">HERRAMIENTAS DE DESARROLLO</h1></td><td align="left" width="100px" nowrap="nowrap"><font style="color:#FFFFFF;font-size:12px">Usuario: '.$this->secu->usuario().'<br/>'.$this->secu->getnombre().'</font></td><td align="right" width="28px"></td>
	</tr>
</table>
</div>
';

// IZQUIERDO
$tabla .= '
<div class="ui-layout-west">
	Tablas Disponibles:
	<ol id="tablas">
';
	//Trae las tablas
	$query = $this->db->query("SHOW TABLE STATUS");
	foreach ($query->result_array() as $field){
		if ( substr($field['Name'],0,4) != 'b2b_'
			&& $field['Name'] != 'ModBusqueda' 
			&& $field['Name'] != 'accdirecto' 
			&& $field['Name'] != 'bitacora' 
			&& substr($field['Name'],0,4) != 'crm_' 
			&& substr($field['Name'],0,4) != 'gpt_' 
			&& $field['Name'] != 'data_sesion' 
			&& $field['Name'] != 'chat' 
			&& $field['Name'] != 'costos' 
			&& $field['Name'] != 'cerberus' 
			&& $field['Name'] != 'contadores'
			&& $field['Name'] != 'ejecutasql'
			&& $field['Name'] != 'formatos'
			&& $field['Name'] != 'reportes'
			&& $field['Name'] != 'graficos'
			&& $field['Name'] != 'impor_data'
			&& $field['Name'] != 'pantallas'   
			&& $field['Name'] != 'i18n' 
			&& $field['Name'] != 'logusu' 
			&& $field['Name'] != 'intramenu' 
			&& $field['Name'] != 'internet' 
			&& $field['Name'] != 'intrarepo' 
			&& $field['Name'] != 'intermenu' 
			&& $field['Name'] != 'intrasida' 
			&& $field['Name'] != 'tmenus' 
			&& $field['Name'] != 'usuario' 
			&& $field['Name'] != 'sida' 

		)
		$tabla .= "\t<li class='ui-widget-content' id='".$field['Name']."' > ".$field['Name'].'</li>';
	}

$tabla .= '
	</ol>
</div>
';

// INFERIOR
$tabla .= '
<div class="ui-layout-south">
';

$tabla .= $this->datasis->traevalor('TITULO1');

$tabla .= '
</div>
';

// DERECHA
$tabla .= '
<div class="ui-layout-east">
</div>
';

// CENTRO
$tabla .= '
<div class="ui-layout-center">
	<table width="100%" bgcolor="#58ACFA">
		<tr>
			<td>Tabla Maestra</td>
			<td><input id="tabla1" type="text" value="" ></td>
			<td><button id="camposdb"  onclick="camposdb()" >Arreglo de Campos</button></td>
			<td><button id="lcamposdb" onclick="lcamposdb()">Lista de Campos</button></td>
		</tr><tr>
			<td>Tabla Detalle </td>
			<td><input id="tabla2" type="text" value="" ></td>
			<td><button id="ccamposdb" onclick="ccamposdb()">Lista con comillas</button></td>
		</tr><tr>
			<td>Modulo</td>
			<td><select id="modulo">
				<option value="">Elija uno</option>
';

$dirs = array_filter(glob('system/application/controllers/*'), 'is_dir');
foreach ( $dirs as $field ){
	$modulo = str_replace('system/application/controllers/','',$field);
	$tabla .= "\t\t\t\t<option value='".$modulo."'>".$modulo.'</option>'."\n";
}
$tabla .= '
			</select></td>
			<td><button id="jqgrid"   onclick="jqgrid()"  >Generar Maestro</button></td>
			<td><button id="jqgridmd" onclick="jqgridmd()">Maestro Detalle</button></td>
		</tr>
	</table>
	<br>
	<div style="background:#EAEAEA" id="resultado"></div>
</div>
';

		$data['content'] = $tabla;
		$data['title']   = $title;
		$data['head']    = $styles;
		$data['head']   .= $script;

		$this->load->view('view_ventanas_lite',$data);

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
		$crud.="\t\t".'$edit = new DataEdit(\'\', \''.$tabla.'\');'."\n\n";
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
	function generepo($tabla=null){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');
		$this->genefilter($tabla, true, true );
	}

	//******************************************************************
	//
	//   Genera la seccion de filtro para el Crud
	//
	function genefilter( $tabla=null, $s=true, $repo=false ){
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
	//   Genera la seccion de funciones post del Crud
	//
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
		$crud.="\t\t\t".'$this->db->query($mSQL);'."\n";
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
	// Genera Crud para jqGrid
	//
	function jqgrid(){
		$db = $this->uri->segment(3);
		if($db===false){
			exit('Debe especificar en la uri la tabla y el directorio "/tabla/directorio"');
		}
		$contro =$this->uri->segment(4);
		if($contro===false){
			$contro = '';
		}

		// Programa
		$path = 'system/application/controllers/';
		//if ( is_file($path.$contro.'/'.$db.'.php') )
			//$columna = file_get_contents('system/application/controllers/'.$contro.'/'.$db.'.php');
		//else
			$columna = $this->programa($db,$contro);

		// Vistas
		$path = 'system/application/views/';
		//if ( is_file($path.'view_'.$db.'.php') )
		//	$vista = file_get_contents('system/application/views/view_'.$db.'.php');
		//else
			$vista = $this->vista($db);

		// Reportes
		$reporte = $this->genefilter( $db, $s=false, $repo=false );

		$data['programa'] = $columna.'?>';
		$data['vista']    = $vista;
		$data['reporte']  = $reporte;

		$data['bd']       = $db;
		$data['vbd']      = "view_".$db;
		$data['rbd']      = $db;

		$data['controlador'] = $contro;
		$this->load->view('editorcm', $data);

	}

	//******************************************************************
	//
	//
	function programa( $db, $contro ){
		$query = $this->db->query("DESCRIBE $db");
		$i = 0;
		if ($query->num_rows() > 0){
			$fields   = '';
			$columna  = '<?php'."\n";
			$columna .= "/**\n";
 			$columna .= "* ProteoERP\n";
 			$columna .= "*\n";
 			$columna .= "* @autor    Andres Hocevar\n";
 			$columna .= "* @license  GNU GPL v3\n";
			$columna .= "*/\n";

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

			$str .= $tab1.'//******************************************************************'."\n";
			$str .= $tab1.'// Layout en la Ventana'."\n";
			$str .= $tab1.'//'."\n";

			$str .= $tab1.'function jqdatag(){'."\n\n";
			$str .= $tab2.'$grid = $this->defgrid();'."\n";
			$str .= $tab2.'$param[\'grids\'][] = $grid->deploy();'."\n\n";

			$str .= $tab2."//Funciones que ejecutan los botones\n";
			$str .= $tab2.'$bodyscript = $this->bodyscript( $param[\'grids\'][0][\'gridname\']);'."\n\n";

			$str .= $tab2.'//Botones Panel Izq'."\n";
			$str .= $tab2.'//$grid->wbotonadd(array("id"=>"funcion",   "img"=>"images/engrana.png",  "alt" => "Formato PDF", "label"=>"Ejemplo"));'."\n";
			$str .= $tab2.'$WestPanel = $grid->deploywestp();'."\n\n";

			$str .= $tab2.'$adic = array('."\n";
			$str .= $tab3.'array(\'id\'=>\'fedita\',  \'title\'=>\'Agregar/Editar Registro\'),'."\n";
			$str .= $tab3.'array(\'id\'=>\'fshow\' ,  \'title\'=>\'Mostrar Registro\'),'."\n";
			$str .= $tab3.'array(\'id\'=>\'fborra\',  \'title\'=>\'Eliminar Registro\')'."\n";
			$str .= $tab2.');'."\n";
			$str .= $tab2.'$SouthPanel = $grid->SouthPanel($this->datasis->traevalor(\'TITULO1\'), $adic);'."\n\n";

			$str .= $tab2.'$param[\'WestPanel\']   = $WestPanel;'."\n";
			$str .= $tab2.'//$param[\'EastPanel\'] = $EastPanel;'."\n";
			$str .= $tab2.'$param[\'SouthPanel\']  = $SouthPanel;'."\n";
			$str .= $tab2.'$param[\'listados\']    = $this->datasis->listados(\''.strtoupper($db).'\', \'JQ\');'."\n";
			$str .= $tab2.'$param[\'otros\']       = $this->datasis->otros(\''.strtoupper($db).'\', \'JQ\');'."\n";
			$str .= $tab2.'$param[\'temas\']       = array(\'proteo\',\'darkness\',\'anexos1\');'."\n";
			$str .= $tab2.'$param[\'bodyscript\']  = $bodyscript;'."\n";
			$str .= $tab2.'$param[\'tabs\']        = false;'."\n";
			$str .= $tab2.'$param[\'encabeza\']    = $this->titp;'."\n";
			$str .= $tab2.'$param[\'tamano\']      = $this->datasis->getintramenu( substr($this->url,0,-1) );'."\n";

			$str .= $tab2.'$this->load->view(\'jqgrid/crud2\',$param);'."\n";
			$str .= $tab1.'}'."\n\n";

			//**************************************
			//  Funcion de Java del Body
			//
			$str .= $tab1.'//******************************************************************'."\n";
			$str .= $tab1.'// Funciones de los Botones'."\n";
			$str .= $tab1.'//'."\n";
			$str .= $tab1.'function bodyscript( $grid0 ){'."\n";

			$str .= $tab2.'$bodyscript = \'<script type="text/javascript">\';'."\n";
			$str .= $tab2.'$ngrid = \'#newapi\'.$grid0;'."\n\n";

			$str .= $tab2.'$bodyscript .= $this->jqdatagrid->bsshow(\''.strtolower($db).'\', $ngrid, $this->url );'."\n";
			$str .= $tab2.'$bodyscript .= $this->jqdatagrid->bsadd( \''.strtolower($db).'\', $this->url );'."\n";
			$str .= $tab2.'$bodyscript .= $this->jqdatagrid->bsdel( \''.strtolower($db).'\', $ngrid, $this->url );'."\n";
			$str .= $tab2.'$bodyscript .= $this->jqdatagrid->bsedit(\''.strtolower($db).'\', $ngrid, $this->url );'."\n\n";

			$str .= $tab2.'//Wraper de javascript'."\n";
			$str .= $tab2.'$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);'."\n\n";

			$str .= $tab2.'$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, \'300\', \'400\' );'."\n";
			$str .= $tab2.'$bodyscript .= $this->jqdatagrid->bsfshow( \'300\', \'400\' );'."\n";
			$str .= $tab2.'$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, \'300\', \'400\' );'."\n\n";

			$str .= $tab2.'$bodyscript .= \'});\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \'</script>\';'."\n\n";


			$str .= $tab2.'return $bodyscript;'."\n";
			$str .= $tab1."}\n\n";


			$str .= $tab1.'//******************************************************************'."\n";
			$str .= $tab1.'// Definicion del Grid o Tabla '."\n";
			$str .= $tab1.'//'."\n";
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

			$str .= $tab2.'$grid->setAfterSubmit("$(\'#respuesta\').html(\'&lt;span style='."\\'font-weight:bold; color:red;\\'&gt;'+a.responseText+'&lt;/span&gt;'); return [true, a ];".'");'."\n\n";
			$str .= $tab2.'$grid->setOndblClickRow(\'\');';

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

			$str .= $tab1.'//******************************************************************'."\n";
			$str .= $tab1.'// Busca la data en el Servidor por json'."\n";
			$str .= $tab1.'//'."\n";
			$str .= $tab1.'function getdata(){'."\n";
			$str .= $tab2.'$grid       = $this->jqdatagrid;'."\n\n";
			$str .= $tab2.'// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO'."\n";
			$str .= $tab2.'$mWHERE = $grid->geneTopWhere(\''.$db.'\');'."\n\n";
			$str .= $tab2.'$response   = $grid->getData(\''.$db.'\', array(array()), array(), false, $mWHERE );'."\n";
			$str .= $tab2.'$rs = $grid->jsonresult( $response);'."\n";
			$str .= $tab2.'echo $rs;'."\n";
			$str .= $tab1.'}'."\n\n";

			$str .= $tab1.'//******************************************************************'."\n";
			$str .= $tab1.'// Guarda la Informacion del Grid o Tabla'."\n";
			$str .= $tab1.'//'."\n";
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
			$str .= $tab4.'$this->db->query("DELETE FROM '.$db.' WHERE id=$id ");'."\n";
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
			return $columna;

		}
	}


	//******************************************************************
	//  Genera Crud Maestro Detalle para jqGrid
	//
	function jqgridmd(){
		$db = $this->uri->segment(3);
		if($db===false){
			exit('Debe especificar en la uri la tabla Maestro "/maestro/detalle/directorio"');
		}

		$dbit = $this->uri->segment(4);
		if($dbit===false){
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
			$str .= $tab8.'	\'.$this->datasis->jwinopen(site_url(\'formatos/ver/'.strtoupper($db).'\').\'/\\\'+json.pk.id+\\\'/id\\\'\').\';'."\n";
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
			$str .= $tab4.'$this->db->query("DELETE FROM '.$db.' WHERE id=$id ");'."\n";
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

			$str .= $this->genecrudjqmd($db, $dbit, false);

			$str .= $this->genepre( $db, false);
			$str .= $this->genepost($db, false);

			$str .= $this->geneinstalar($db, false);
			$str .= $this->geneinstalar($dbit, false);

			$str .= '}'."\n";

			$columna .= $str."\n";

			// Genera view
			$columna .= "\n\n".'//******************************************************************'."\n";
			$columna .= '// View'."\n";
			$columna .= "\n/*";
			$columna .= $this->geneviewjqmd($db, $dbit,true);
			$columna .= "*/\n";

			echo $columna."</pre>";

		}
	}


	//******************************************************************
	// Genera la clase
	//
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
		$str .= $tab3.'$this->db->query(\'ALTER TABLE '.$db.' DROP PRIMARY KEY\');'."\n";
		$str .= $tab3.'$this->db->query(\'ALTER TABLE '.$db.' ADD UNIQUE INDEX numero (numero)\');'."\n";
		$str .= $tab3.'$this->db->query(\'ALTER TABLE '.$db.' ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)\');'."\n";
		$str .= $tab2.'};*/'."\n";

		$str .= $tab2.'//$this->datasis->creaintramenu(array(\'modulo\'=>\'000\',\'titulo\'=>\'<#titulo#>\',\'mensaje\'=>\'<#mensaje#>\',\'panel\'=>\'<#panal#>\',\'ejecutar\'=>\'<#ejecuta#>\',\'target\'=>\'popu\',\'visible\'=>\'S\',\'pertenece\'=>\'<#pertenece#>\',\'ancho\'=>900,\'alto\'=>600));'."\n";

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
	// Generar Crud
	//
	function genecrudjq($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla)))
			show_error('Tabla no existe o faltan parametros');


		$crud ="\n\t".'//******************************************************************'."\n";
		$crud.="\t".  '// Edicion '."\n";

		$crud.="\n\t".'function dataedit(){'."\n";
		$crud.="\t\t".'$this->rapyd->load(\'dataedit\');'."\n";

		$crud.="\t\t".'$script= \''."\n";
		$crud.="\t\t".'$(function() {'."\n";
		$crud.="\t\t\t".'$("#fecha").datepicker({dateFormat:"dd/mm/yy"});'."\n";
		$crud.="\t\t\t".'$(".inputnum").numeric(".");'."\n";
		$crud.="\t\t".'});'."\n";
		$crud.="\t\t".'\';'."\n\n";

		$crud.="\t\t".'$edit = new DataEdit(\'\', \''.$tabla.'\');'."\n\n";
		$crud.="\t\t".'$edit->script($script,\'modify\');'."\n";
		$crud.="\t\t".'$edit->script($script,\'create\');'."\n";
		$crud.="\t\t".'$edit->on_save_redirect=false;'."\n\n";
		$crud.="\t\t".'$edit->back_url = site_url($this->url.\'filteredgrid\');'."\n\n";

		$crud.="\t\t".'$edit->post_process(\'insert\',\'_post_insert\');'."\n";
		$crud.="\t\t".'$edit->post_process(\'update\',\'_post_update\');'."\n";
		$crud.="\t\t".'$edit->post_process(\'delete\',\'_post_delete\');'."\n";
		$crud.="\t\t".'$edit->pre_process(\'insert\', \'_pre_insert\' );'."\n";
		$crud.="\t\t".'$edit->pre_process(\'update\', \'_pre_update\' );'."\n";
		$crud.="\t\t".'$edit->pre_process(\'delete\', \'_pre_delete\' );'."\n";
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
					$crud.="\t\t".'$edit->'.$field->Field."->calendar=false;\n";

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
	// Generar Crud para md
	//
	function genecrudjqmd($tabla=null, $tablait=null, $s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla)))
			show_error('Tabla no existe o faltan parametros');

		$crud ="\n\t".'function dataedit(){'."\n";
		$crud.="\t\t".'$this->rapyd->load(\'dataobject\',\'datadetails\');'."\n";

		$crud.="\t\t".'$script= \''."\n";
		$crud.="\t\t".'$(function() {'."\n";
		$crud.="\t\t\t".'$("#fecha").datepicker({dateFormat:"dd/mm/yy"});'."\n";
		$crud.="\t\t\t".'$(".inputnum").numeric(".");'."\n";
		$crud.="\t\t".'});'."\n";
		$crud.="\t\t".'\';'."\n\n";

		$crud.="\t\t".'$do = new DataObject(\''.$tabla.'\');'."\n\n";
		$crud.="\t\t".'$do->rel_one_to_many(\''.$tablait.'\',\''.$tablait.'\',\'numero\');'."\n";

		$crud.="\t\t".'$edit = new DataDetails($this->tits, $do );'."\n\n";
		$crud.="\t\t".'$edit->script($script,\'modify\');'."\n";
		$crud.="\t\t".'$edit->script($script,\'create\');'."\n";
		$crud.="\t\t".'$edit->on_save_redirect=false;'."\n\n";
		$crud.="\t\t".'$edit->back_url = site_url($this->url.\'filteredgrid\');'."\n\n";

		$crud.="\t\t".'$edit->post_process(\'insert\',\'_post_insert\');'."\n";
		$crud.="\t\t".'$edit->post_process(\'update\',\'_post_update\');'."\n";
		$crud.="\t\t".'$edit->post_process(\'delete\',\'_post_delete\');'."\n";
		$crud.="\t\t".'$edit->pre_process(\'insert\', \'_pre_insert\' );'."\n";
		$crud.="\t\t".'$edit->pre_process(\'update\', \'_pre_update\' );'."\n";
		$crud.="\t\t".'$edit->pre_process(\'delete\', \'_pre_delete\' );'."\n";
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
					$crud.="\t\t".'$edit->'.$field->Field."->calendar=false;\n";

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

		$crud.="\n\t\t".'//******************************************************************'."\n";
		$crud.="\t\t".  '// Detalle '."\n";

		$mSQL="DESCRIBE $tablait";
		$query = $this->db->query($mSQL);
		foreach ($query->result() as $field){

			if($field->Field=='usuario'){
				//$crud.="\t\t".'$edit->usuario = new autoUpdateField(\'usuario\',$this->session->userdata(\'usuario\'),$this->session->userdata(\'usuario\'));'."\n\n";
			}elseif($field->Field=='estampa'){
				//$crud.="\t\t".'$edit->estampa = new autoUpdateField(\'estampa\' ,date(\'Ymd\'), date(\'Ymd\'));'."\n\n";
			}elseif($field->Field=='hora'){
				//$crud.="\t\t".'$edit->hora    = new autoUpdateField(\'hora\',date(\'H:i:s\'), date(\'H:i:s\'));'."\n\n";
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

				$crud.="\t\t".'$edit->'.$field->Field.' = new '.$input."Field('".ucfirst($field->Field)."','".$field->Field."_<#i#>');\n";

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
				$crud.="\t\t".'$edit->'.$field->Field."->rel_id ='$tablait';\n";

				$crud.="\n";
			}
		}

		$crud .="\t\t".'//******************************************************************'."\n\n";

		$crud.="\t\t".'$edit->buttons(\'add_rel\');'."\n\n";
		$crud.="\t\t".'$edit->build();'."\n\n";

		$crud.="\t\t".'if($edit->on_success()){'."\n";
		$crud.="\t\t".'	$rt=array('."\n";
		$crud.="\t\t".'		\'status\' =>\'A\','."\n";
		$crud.="\t\t".'		\'mensaje\'=>\'Registro guardado\','."\n";
		$crud.="\t\t".'		\'pk\'     =>$edit->_dataobject->pk'."\n";
		$crud.="\t\t".'	);'."\n";
		$crud.="\t\t".'	echo json_encode($rt);'."\n";
		$crud.="\t\t".'}else{'."\n";


		$crud.="\t\t\t".'$conten[\'form\']  =& $edit;'."\n";
		$crud.="\t\t\t".'$this->load->view(\'view_'.$tabla.'\', $conten);'."\n";

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
	// Genera el View a partir de la Tabla
	//
	function geneviewjq( $tabla=null, $s=true ){
		if (empty($tabla) OR (!$this->db->table_exists($tabla)))
			show_error('Tabla no existe o faltan parametros');

		$crud = $this->vista($tabla);
		echo '<html><body><pre>'.htmlentities( $crud).'</pre></body></html>';

	}

	//******************************************************************
	// Genera vistas
	//
	function vista( $tabla ){
		$crud  = '<?php'."\n";
		$crud .= "/**\n";
		$crud .= "* ProteoERP\n";
		$crud .= "*\n";
		$crud .= "* @autor    Andres Hocevar\n";
		$crud .= "* @license  GNU GPL v3\n";
		$crud .= "*/\n";
		$crud .= 'echo $form_scripts;'."\n";
		$crud .= 'echo $form_begin;'."\n\n";
		$crud .= 'if(isset($form->error_string)) echo \'<div class="alert">\'.$form->error_string.\'</div>\';'."\n";
		$crud .= 'if($form->_status <> \'show\'){ ?>'."\n\n";
		$crud .= '<script language="javascript" type="text/javascript">'."\n";
		$crud .= '</script>'."\n";
		$crud .= '<?php } ?>'."\n\n";
		$crud .= '<fieldset  style=\'border: 1px outset #FEB404;background: #FFFCE8;\'>'."\n";
		$crud .= '<table width=\'100%\'>'."\n";

		$mSQL ="DESCRIBE $tabla";
		$query = $this->db->query("DESCRIBE $tabla");
		foreach ($query->result() as $field){
			$crud .= '	<tr>'."\n";
			$crud .= '		<td class="littletablerowth"><?php echo $form->'.$field->Field.'->label;  ?></td>'."\n";
			$crud .= '		<td class="littletablerow"  ><?php echo $form->'.$field->Field.'->output; ?></td>'."\n";
			$crud .= '	</tr>'."\n";
		}

		$crud .= '</table>'."\n";
		$crud .= '</fieldset>'."\n";
		$crud .= '<?php echo $form_end; ?>'."\n";

		return $crud;
	}


	//******************************************************************
	//    Genera el View a partir de la Tabla
	//******************************************************************
	function geneviewjqmd($tabla=null, $tablait=null, $s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla)))
			show_error('Tabla no existe o faltan parametros');

		$crud  ='<?php'."\n";
		$crud .= "/**\n";
		$crud .= "* ProteoERP\n";
		$crud .= "*\n";
		$crud .= "* @autor    Andres Hocevar\n";
		$crud .= "* @license  GNU GPL v3\n";
		$crud .= "*/\n";

		$crud .= 'if ($form->_status==\'delete\' || $form->_action==\'delete\' || $form->_status==\'unknow_record\'){'."\n";
		$crud .= "\t".'echo $form->output;'."\n";
		$crud .= '} else {'."\n";

		$crud .= "\t".'$html=\'<tr id="tr_itstra_<#i#>">\';'."\n";
		$crud .= "\t".'$campos=$form->template_details(\''.$tablait.'\');'."\n";
		$crud .= "\t".'foreach($campos as $nom=>$nan){'."\n";
		$crud .= "\t\t".'$pivot=$nan[\'field\'];'."\n";
		$crud .= "\t\t".'$align = (strpos($pivot,\'inputnum\')) ? \'align="right"\' : \'\';'."\n";
		$crud .= "\t\t".'$html.=\'<td class="littletablerow" \'.$align.\'>\'.$pivot.\'</td>\';'."\n";
		$crud .= "\t".'}'."\n";
		$crud .= '}'."\n";

		$crud .= 'if($form->_status!=\'show\') {'."\n";
		$crud .= "\t".'$html.=\'<td class="littletablerow"><a href=# onclick=\\\'del_'.$tablait.'(<#i#>);return false;\\\'>\'.img(\'images/delete.jpg\').\'</a></td>\';'."\n";
		$crud .= '}'."\n";

		$crud .= '$html.=\'</tr>\';'."\n";

		$crud .= '$campos=$form->js_escape($html);'."\n";
		$crud .= 'if(isset($form->error_string)) echo \'<div class="alert">\'.$form->error_string.\'</div>\';'."\n";
		$crud .= 'echo $form_begin;'."\n";
		$crud .= 'if($form->_status!=\'show\'){'."\n";
		$crud .= '?>'."\n\n";

		$crud .= '<script language="javascript" type="text/javascript">'."\n";
		$crud .= 'itstra_cont=<?php echo $form->max_rel_count[\''.$tablait.'\'] ?>;'."\n";

		$crud .= '$(function(){'."\n";

		$crud .= '	$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });'."\n";
		$crud .= '	$(".inputnum").numeric(".");'."\n";
		$crud .= '	for(var i=0;i < <?php echo $form->max_rel_count[\''.$tablait.'\']; ?>;i++){'."\n";
		$crud .= '		autocod(i.toString());'."\n";
		$crud .= '	}'."\n";

		$crud .= '	$(\'input[name^="cantidad_"]\').keypress(function(e) {'."\n";
		$crud .= '		if(e.keyCode == 13) {'."\n";
		$crud .= '		    add_'.$tablait.'();'."\n";
		$crud .= '			return false;'."\n";
		$crud .= '		}'."\n";
		$crud .= '	});'."\n";
		$crud .= '});'."\n";

		$crud .= 'function post_modbus(id){'."\n";
		$crud .= '	//var id      = i.toString();'."\n";
		$crud .= '	var descrip = $(\'#descrip_\'+id).val();'."\n";
		$crud .= '	$(\'#descrip_\'+id+\'_val\').text(descrip);'."\n";
		$crud .= '	$(\'#cantidad_\'+id).focus();'."\n";
		$crud .= '}'."\n";

		$crud .= '//Agrega el autocomplete'."\n";
		$crud .= 'function autocod(id){'."\n";
		$crud .= '	$(\'#codigo_\'+id).autocomplete({'."\n";
		$crud .= '		source: function( req, add){'."\n";
		$crud .= '			$.ajax({'."\n";
		$crud .= '				url:  "<?php echo site_url(\'ajax/buscasinvart\'); ?>",'."\n";
		$crud .= '				type: \'POST\','."\n";
		$crud .= '				dataType: \'json\','."\n";
		$crud .= '				data: "q="+req.term,'."\n";
		$crud .= '				success:'."\n";
		$crud .= '					function(data){'."\n";
		$crud .= '						var sugiere = [];'."\n";
		$crud .= '						$.each(data,'."\n";
		$crud .= '							function(i, val){'."\n";
		$crud .= '								sugiere.push( val );'."\n";
		$crud .= '							}'."\n";
		$crud .= '						);'."\n";
		$crud .= '						add(sugiere);'."\n";
		$crud .= '					},'."\n";
		$crud .= '			})'."\n";
		$crud .= '		},'."\n";
		$crud .= '		minLength: 2,'."\n";
		$crud .= '		select: function( event, ui ) {'."\n";
		$crud .= '			$(\'#codigo_\'+id).attr(\'readonly\',\'readonly\');'."\n";

		$crud .= '			$(\'#codigo_\'+id).val(ui.item.codigo);'."\n";
		$crud .= '			$(\'#descrip_\'+id).val(ui.item.descrip);'."\n";
		$crud .= '			post_modbus(id);'."\n";

		$crud .= '			setTimeout(function(){ $(\'#codigo_\'+id).removeAttr(\'readonly\'); }, 1500);'."\n";
		$crud .= '		}'."\n";
		$crud .= '	});'."\n";
		$crud .= '}'."\n\n";

		$crud .= 'function add_'.$tablait.'(){'."\n";
		$crud .= '	var htm = <?php echo $campos; ?>;'."\n";
		$crud .= '	can = '.$tablait.'_cont.toString();'."\n";
		$crud .= '	con = ('.$tablait.'_cont+1).toString();'."\n";
		$crud .= '	htm = htm.replace(/<#i#>/g,can);'."\n";
		$crud .= '	htm = htm.replace(/<#o#>/g,con);'."\n";
		$crud .= '	$("#__UTPL__").before(htm);'."\n";
		$crud .= '	$("#cantidad_"+can).numeric(".");'."\n";
		$crud .= '	$("#codigo_"+can).focus();'."\n";
		$crud .= '	autocod(can);'."\n";
		$crud .= '	$("#cantidad_"+can).keypress(function(e) {'."\n";
		$crud .= '		if(e.keyCode == 13) {'."\n";
		$crud .= '		    add_'.$tablait.'();'."\n";
		$crud .= '			return false;'."\n";
		$crud .= '		}'."\n";
		$crud .= '	});'."\n";

		$crud .= '	itstra_cont='.$tablait.'_cont+1;'."\n";
		$crud .= '}'."\n";

		$crud .= 'function del_'.$tablait.'(id){'."\n";
		$crud .= '	id = id.toString();'."\n";
		$crud .= '	$(\'#tr_'.$tablait.'_\'+id).remove();'."\n";
		$crud .= '}'."\n";
		$crud .= '</script>'."\n";
		$crud .= '<?php } ?>'."\n";


		$crud .="\t".'<fieldset  style=\'border: 1px outset #FEB404;background: #FFFCE8;\'>'."\n";
		$crud .="\t".'<table width=\'100%\'>'."\n";

		$mSQL ="DESCRIBE $tabla";
		$query = $this->db->query($mSQL);
		foreach ($query->result() as $field){
			$crud .="\t".'	<tr>'."\n";
			$crud .="\t".'		<td class="littletablerowth"><?php echo $form->'.$field->Field.'->label;  ?></td>'."\n";
			$crud .="\t".'		<td class="littletablerow"  ><?php echo $form->'.$field->Field.'->output; ?></td>'."\n";
			$crud .="\t".'	</tr>'."\n";
		}

		$mSQL ="DESCRIBE $tablait";
		$query = $this->db->query($mSQL);

		$crud .= "\t\t".'<tr><td>&nbsp;</td></tr>'."\n";
		$crud .= "\t\t".'<tr>'."\n";
		$crud .= "\t\t\t".'<td>'."\n";
		$crud .= "\t\t\t".'<div style=\'overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:250px\'>'."\n";
		$crud .= "\t\t\t".'<table width=\'100%\'>'."\n";
		$crud .= "\t\t\t\t".'<tr>'."\n";

		foreach ($query->result() as $field){
			$crud .= "\t\t\t\t\t".'<td bgcolor=\'#7098D0\' width="80">'.$field->Field.'</td>'."\n";
		}

		$crud .= "\t\t\t\t".'</tr>'."\n";
		$crud .= "\t\t\t\t".'<?php for($i=0;$i<$form->max_rel_count[\''.$tablait.'\'];$i++) {'."\n";

		$i = 1;
		foreach ($query->result() as $field){
			$crud .= "\t\t\t\t\t".'$obj'.$i.' = "'.$field->Field.'_$i";'."\n";
			$i++;
		}
		$crud .= "\n\t\t\t\t".'?>'."\n";

		$crud .= "\n\t\t\t\t".'<tr id=\'tr_'.$tablait.'_<?php echo $i; ?>\'>'."\n";

		$i = 1;
		foreach ($query->result() as $field){
			$crud .= "\t\t\t\t\t".'<td class="littletablerow"><?php echo $form->$obj'.$i.'->output ?></td>'."\n";
			$i++;
		}

		$crud .= "\t\t\t\t\t".'<?php if($form->_status!=\'show\') {?>'."\n";
		$crud .= "\t\t\t\t\t".'	<td class="littletablerow"><a href="#" onclick=\'del_'.$tablait.'(<?php echo $i; ?>);return false;\'><?php echo img("images/delete.jpg"); ?></a></td>'."\n";
		$crud .= "\t\t\t\t\t".'<?php } ?>'."\n";

		$crud .= "\t\t\t\t".'</tr>'."\n";
		$crud .= "\t\t\t\t".'<?php } ?>'."\n";
		$crud .= "\t\t\t\t".'<tr id=\'__UTPL__\'>'."\n";

		foreach ($query->result() as $field){
			$crud .= "\t\t\t\t\t".'<td class="littletablefooterb" align="right">&nbsp;</td>'."\n";
		}

		$crud .= "\t\t\t\t\t".'<?php if($form->_status!=\'show\') {?>'."\n";
		$crud .= "\t\t\t\t\t".'<td class="littletablefooterb" align="right">&nbsp;</td>'."\n";
		$crud .= "\t\t\t\t\t".'<?php } ?>'."\n";

		$crud .= "\t\t\t\t".'</tr>'."\n";
		$crud .= "\t\t\t".'</table>'."\n";
		$crud .= "\t\t\t".'</div>'."\n";
		$crud .= "\t\t\t".'</td>'."\n";
		$crud .= "\t\t".'</tr>'."\n";

		$crud .="\t".'</table>'."\n";
		$crud .="\t".'</fieldset>'."\n";

		$crud .='<?php echo $form_end; ?>'."\n";

		return '<html><body><pre>'.htmlentities( $crud).'</pre></body></html>';

	}


	function editor(){
			$this->load->view('editorcm');
	}

	function jqguarda(){
		$code   = $this->input->post('code');
		$db     = $this->input->post('bd');
		$contro = $this->input->post('contro');
		file_put_contents('system/application/controllers/'.$contro.'/'.$db.'.php',$code);
		//redirect(base_url.'desarrollo/jqcargar/'.$db.'/'.$contro);
		echo 'Guardado';
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


	function menu(){


		$arbol = '<?xml version=\'1.0\' encoding="utf-8"?>
<rows>
    <page>1</page>
    <total>1</total>
    <records>1</records>
    <row><cell>1</cell><cell>Listas de Campos</cell><cell></cell><cell>0</cell><cell>1</cell><cell>10</cell><cell>false</cell><cell>false</cell></row>

    <row><cell>2</cell><cell>En arreglo $data</cell><cell>'.site_url('desarrollo/camposdb').'</cell><cell>1</cell><cell>2</cell><cell>3</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>3</cell><cell>Separados x ,   </cell><cell>jsonex.html   </cell><cell>1</cell><cell>4</cell><cell>5</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>4</cell><cell>Separado x ","  </cell><cell>loadoncex.html</cell><cell>1</cell><cell>6</cell><cell>7</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>5</cell><cell>Separado x \',\'</cell><cell>localex.html  </cell><cell>1</cell><cell>8</cell><cell>9</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>6</cell><cell>Manipulating</cell><cell></cell><cell>0</cell><cell>11</cell><cell>18</cell><cell>false</cell><cell>false</cell></row>

    <row><cell>7</cell><cell>Grid Data  </cell><cell>manipex.html</cell><cell>1</cell><cell>12</cell><cell>13</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>8</cell><cell>Get Methods</cell><cell>getex.html  </cell><cell>1</cell><cell>14</cell><cell>15</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>9</cell><cell>Set Methods</cell><cell>setex.html  </cell><cell>1</cell><cell>16</cell><cell>17</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>10</cell><cell>Advanced       </cell><cell></cell><cell>0</cell><cell>19</cell><cell>32</cell><cell>false</cell><cell>false</cell></row>

    <row><cell>11</cell><cell>Multi Select   </cell><cell>multiex.html     </cell><cell>1</cell><cell>20</cell><cell>21</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>12</cell><cell>Master Detail  </cell><cell>masterex.html    </cell><cell>1</cell><cell>22</cell><cell>23</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>13</cell><cell>Subgrid        </cell><cell>subgrid.html     </cell><cell>1</cell><cell>24</cell><cell>25</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>14</cell><cell>Grid as Subgrid</cell><cell>subgrid_grid.html</cell><cell>1</cell><cell>26</cell><cell>27</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>15</cell><cell>Resizing       </cell><cell>resizeex.html    </cell><cell>1</cell><cell>28</cell><cell>28</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>16</cell><cell>Search Big Sets</cell><cell>bigset.html      </cell><cell>1</cell><cell>30</cell><cell>31</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>17</cell><cell>New since beta 3.0</cell><cell></cell><cell>0</cell><cell>33</cell><cell>44</cell><cell>false</cell><cell>false</cell></row>

    <row><cell>18</cell><cell>Custom Multi Select</cell><cell>cmultiex.html </cell><cell>1</cell><cell>34</cell><cell>35</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>19</cell><cell>Subgrid with JSON  </cell><cell>jsubgrid.html </cell><cell>1</cell><cell>36</cell><cell>37</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>20</cell><cell>After Load Callback</cell><cell>loadcml.html  </cell><cell>1</cell><cell>38</cell><cell>39</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>21</cell><cell>Resizable Columns  </cell><cell>resizecol.html</cell><cell>1</cell><cell>40</cell><cell>41</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>22</cell><cell>Hide/Show Columns  </cell><cell>hideex.html   </cell><cell>1</cell><cell>42</cell><cell>43</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>23</cell><cell>Row Editing (new)</cell><cell></cell><cell>0</cell><cell>45</cell><cell>58</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>24</cell><cell>Basic Example</cell><cell>rowedex1.html</cell><cell>1</cell><cell>46</cell><cell>47</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>25</cell><cell>Custom Edit</cell><cell>rowedex2.html</cell><cell>1</cell><cell>48</cell><cell>49</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>26</cell><cell>Using Events</cell><cell>rowedex3.html</cell><cell>1</cell><cell>50</cell><cell>51</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>27</cell><cell>Full Control</cell><cell>rowedex4.html</cell><cell>1</cell><cell>52</cell><cell>53</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>28</cell><cell>Input types</cell><cell>rowedex5.html</cell><cell>1</cell><cell>54</cell><cell>55</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>135</cell><cell>Inline Navigator (new)</cell><cell>43rowedex.html</cell><cell>1</cell><cell>56</cell><cell>57</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>29</cell><cell>Data Mapping</cell><cell></cell><cell>0</cell><cell>59</cell><cell>66</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>30</cell><cell>XML Mapping</cell><cell>xmlmap.html</cell><cell>1</cell><cell>60</cell><cell>61</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>31</cell><cell>JSON Mapping</cell><cell>jsonmap.html</cell><cell>1</cell><cell>62</cell><cell>63</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>32</cell><cell>Data Optimization</cell><cell>jsonopt.html</cell><cell>1</cell><cell>64</cell><cell>65</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>33</cell><cell>Integrations</cell><cell></cell><cell>0</cell><cell>67</cell><cell>70</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>34</cell><cell>UI Datepicker</cell><cell>calendar.html</cell><cell>1</cell><cell>68</cell><cell>69</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>35</cell><cell>Live Data Manipulation</cell><cell></cell><cell>0</cell><cell>70</cell><cell>81</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>36</cell><cell>Searching Data</cell><cell>searching.html</cell><cell>1</cell><cell>71</cell><cell>72</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>37</cell><cell>Edit row</cell><cell>editing.html</cell><cell>1</cell><cell>73</cell><cell>74</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>38</cell><cell>Add row</cell><cell>adding.html</cell><cell>1</cell><cell>75</cell><cell>76</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>39</cell><cell>Delete row</cell><cell>deleting.html</cell><cell>1</cell><cell>77</cell><cell>78</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>40</cell><cell>Navigator</cell><cell>navgrid.html</cell><cell>1</cell><cell>79</cell><cell>80</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>41</cell><cell>New in version 3.1</cell><cell></cell><cell>0</cell><cell>81</cell><cell>90</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>42</cell><cell>Toolbars and userdata</cell><cell>toolbar.html</cell><cell>1</cell><cell>82</cell><cell>83</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>43</cell><cell>New Methods</cell><cell>methods.html</cell><cell>1</cell><cell>84</cell><cell>85</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>44</cell><cell>Post Data</cell><cell>postdata.html</cell><cell>1</cell><cell>86</cell><cell>87</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>45</cell><cell>Common Params</cell><cell>defparams.html</cell><cell>1</cell><cell>88</cell><cell>89</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>46</cell><cell>New in version 3.2</cell><cell></cell><cell>0</cell><cell>91</cell><cell>106</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>47</cell><cell>New Methods 3.2</cell><cell>methods32.html</cell><cell>1</cell><cell>92</cell><cell>93</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>48</cell><cell>Initial hidden grid</cell><cell>hiddengrid.html</cell><cell>1</cell><cell>94</cell><cell>95</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>49</cell><cell>After Insert Row event</cell><cell>afterinsrow.html</cell><cell>1</cell><cell>96</cell><cell>97</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>50</cell><cell>Controling server errors</cell><cell>loaderror.html</cell><cell>1</cell><cell>98</cell><cell>99</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>51</cell><cell>Hide/Show columns</cell><cell>hideshow.html</cell><cell>1</cell><cell>100</cell><cell>101</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>52</cell><cell>Custom Button and Forms</cell><cell>custbutt.html</cell><cell>1</cell><cell>102</cell><cell>103</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>53</cell><cell>Client Validation</cell><cell>csvalid.html</cell><cell>1</cell><cell>104</cell><cell>105</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>54</cell><cell>New in version 3.3</cell><cell></cell><cell>0</cell><cell>107</cell><cell>126</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>55</cell><cell>Dynamic height and width</cell><cell>gridwidth.html</cell><cell>1</cell><cell>108</cell><cell>109</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>56</cell><cell>Tree Grid</cell><cell>treegrid.html</cell><cell>1</cell><cell>110</cell><cell>111</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>57</cell><cell>Cell Editing</cell><cell>celledit.html</cell><cell>1</cell><cell>112</cell><cell>113</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>58</cell><cell>Visible Columns</cell><cell>setcolumns.html</cell><cell>1</cell><cell>114</cell><cell>115</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>59</cell><cell>HTML Table to Grid</cell><cell>tbltogrid.html</cell><cell>1</cell><cell>116</cell><cell>117</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>60</cell><cell>Multiple Toolbar Search</cell><cell>search1.html</cell><cell>1</cell><cell>118</cell><cell>119</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>61</cell><cell>Multiple Form Search</cell><cell>search2.html</cell><cell>1</cell><cell>120</cell><cell>121</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>62</cell><cell>Data type as function</cell><cell>datatype.html</cell><cell>1</cell><cell>122</cell><cell>123</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>63</cell><cell>Row Drag and Drop</cell><cell>tablednd.html</cell><cell>1</cell><cell>124</cell><cell>125</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>64</cell><cell>New in version 3.4</cell><cell></cell><cell>0</cell><cell>127</cell><cell>140</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>65</cell><cell>Formater</cell><cell>formatter.html</cell><cell>1</cell><cell>128</cell><cell>129</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>66</cell><cell>Custom Formater</cell><cell>custfrm.html</cell><cell>1</cell><cell>130</cell><cell>131</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>67</cell><cell>Import Configuration from XML</cell><cell>xmlimp.html</cell><cell>1</cell><cell>132</cell><cell>133</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>68</cell><cell>Autoloading data when scroll</cell><cell>scrgrid.html</cell><cell>1</cell><cell>134</cell><cell>135</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>69</cell><cell>Scroll with dynamic row select</cell><cell>navgrid2.html</cell><cell>1</cell><cell>136</cell><cell>137</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>70</cell><cell>Tree Grid Adjacency model</cell><cell>treegrid2.html</cell><cell>1</cell><cell>138</cell><cell>139</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>71</cell><cell>New in version 3.5</cell><cell></cell><cell>0</cell><cell>141</cell><cell>160</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>72</cell><cell>Autowidth and row numbering</cell><cell>autowidth.html</cell><cell>1</cell><cell>142</cell><cell>143</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>73</cell><cell>Grid view mode</cell><cell>speed.html</cell><cell>1</cell><cell>144</cell><cell>145</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>74</cell><cell>Integrated Search Toolbar</cell><cell>search3.html</cell><cell>1</cell><cell>146</cell><cell>147</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>75</cell><cell>Advanced Searching</cell><cell>search4.html</cell><cell>1</cell><cell>148</cell><cell>149</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>76</cell><cell>Form Improvements</cell><cell>navgrid3.html</cell><cell>1</cell><cell>150</cell><cell>151</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>77</cell><cell>TreeGrid real world example</cell><cell>treegridadv.html</cell><cell>1</cell><cell>152</cell><cell>153</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>78</cell><cell>Form Navigation</cell><cell>navgrid4.html</cell><cell>1</cell><cell>154</cell><cell>155</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>79</cell><cell>Summary Footer Row</cell><cell>summary.html</cell><cell>1</cell><cell>156</cell><cell>157</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>80</cell><cell>View sortable columns</cell><cell>sortcols.html</cell><cell>1</cell><cell>158</cell><cell>159</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>81</cell><cell>New in version 3.6</cell><cell></cell><cell>0</cell><cell>161</cell><cell>186</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>82</cell><cell>New API</cell><cell>36newapi.html</cell><cell>1</cell><cell>162</cell><cell>163</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>83</cell><cell>RTL Support</cell><cell>36rtl.html</cell><cell>1</cell><cell>164</cell><cell>165</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>84</cell><cell>Column Reordering</cell><cell>36colreorder.html</cell><cell>1</cell><cell>166</cell><cell>167</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>85</cell><cell>Column Chooser</cell><cell>36columnchoice.html</cell><cell>1</cell><cell>168</cell><cell>169</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>86</cell><cell>Custom Validation</cell><cell>36custvalid.html</cell><cell>1</cell><cell>170</cell><cell>171</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>87</cell><cell>Create Custom input element</cell><cell>36custinput.html</cell><cell>1</cell><cell>172</cell><cell>173</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>88</cell><cell>Ajax Improvements</cell><cell>36ajaxing.html</cell><cell>1</cell><cell>174</cell><cell>175</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>89</cell><cell>True scrolling Rows</cell><cell>36scrolling.html</cell><cell>1</cell><cell>176</cell><cell>177</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>90</cell><cell>Sortable Rows</cell><cell>36sortrows.html</cell><cell>1</cell><cell>178</cell><cell>179</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>91</cell><cell>Drag and Drop Rows</cell><cell>36draganddrop.html</cell><cell>1</cell><cell>180</cell><cell>181</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>92</cell><cell>Resizing Grid</cell><cell>36resize.html</cell><cell>1</cell><cell>182</cell><cell>183</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>93</cell><cell>New in version 3.7</cell><cell></cell><cell>0</cell><cell>185</cell><cell>200</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>95</cell><cell>Load array data at once</cell><cell>37array.html</cell><cell>1</cell><cell>186</cell><cell>187</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>96</cell><cell>Load at once from server</cell><cell>37server.html</cell><cell>1</cell><cell>188</cell><cell>189</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>97</cell><cell>Single search</cell><cell>37single.html</cell><cell>1</cell><cell>190</cell><cell>191</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>98</cell><cell>Multiple search</cell><cell>37multiple.html</cell><cell>1</cell><cell>192</cell><cell>193</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>99</cell><cell>Virtual scrolling</cell><cell>37scroll.html</cell><cell>1</cell><cell>194</cell><cell>195</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>100</cell><cell>Tooolbar search</cell><cell>37toolbar.html</cell><cell>1</cell><cell>196</cell><cell>197</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>101</cell><cell>Add/edit/delete on local data</cell><cell>37crud.html</cell><cell>1</cell><cell>198</cell><cell>199</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>102</cell><cell>Grouping</cell><cell></cell><cell>0</cell><cell>201</cell><cell>229</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>103</cell><cell>Simple grouping with array data</cell><cell>38array.html</cell><cell>1</cell><cell>202</cell><cell>203</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>104</cell><cell>Hide grouping column</cell><cell>38array2.html</cell><cell>1</cell><cell>204</cell><cell>205</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>105</cell><cell>Grouped header row config</cell><cell>38array3.html</cell><cell>1</cell><cell>206</cell><cell>207</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>106</cell><cell>RTL Support</cell><cell>38array4.html</cell><cell>1</cell><cell>208</cell><cell>209</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>107</cell><cell>Grouping row(s) collapsed</cell><cell>38array5.html</cell><cell>1</cell><cell>210</cell><cell>211</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>108</cell><cell>Summary Footers</cell><cell>38array6.html</cell><cell>1</cell><cell>212</cell><cell>213</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>109</cell><cell>Remote Data (sorted)</cell><cell>38remote1.html</cell><cell>1</cell><cell>214</cell><cell>215</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>110</cell><cell>Remote Data (sorted with grandtotals)</cell><cell>38remote2.html</cell><cell>1</cell><cell>216</cell><cell>217</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>111</cell><cell>Dynamically change grouping</cell><cell>38remote4.html</cell><cell>1</cell><cell>218</cell><cell>219</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>112</cell><cell>View Summary Row on Collapse</cell><cell>38remote5.html</cell><cell>1</cell><cell>220</cell><cell>221</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>113</cell><cell>Multi Group all level sums (new)</cell><cell>44remote1.html</cell><cell>1</cell><cell>222</cell><cell>223</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>114</cell><cell>Multi Group one level sum  (new)</cell><cell>44remote2.html</cell><cell>1</cell><cell>224</cell><cell>225</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>115</cell><cell>Multi Group Show sums on header(new)</cell><cell>44remote3.html</cell><cell>1</cell><cell>226</cell><cell>227</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>119</cell><cell>Functionality</cell><cell></cell><cell>0</cell><cell>230</cell><cell>241</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>120</cell><cell>Data colspan</cell><cell>40colspan.html</cell><cell>1</cell><cell>231</cell><cell>232</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>121</cell><cell>Keyboard navigation</cell><cell>40keyboard.html</cell><cell>1</cell><cell>233</cell><cell>234</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>122</cell><cell>Column model templates</cell><cell>40cmtmpl.html</cell><cell>1</cell><cell>235</cell><cell>236</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>123</cell><cell>Add tree node </cell><cell>40addnode.html</cell><cell>1</cell><cell>237</cell><cell>238</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>124</cell><cell>Formatter actions </cell><cell>40frmactions.html</cell><cell>1</cell><cell>239</cell><cell>240</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>131</cell><cell>Searching</cell><cell></cell><cell>0</cell><cell>260</cell><cell>270</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>132</cell><cell>Complex search </cell><cell>40grpsearch.html</cell><cell>1</cell><cell>261</cell><cell>262</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>133</cell><cell>Show query in search </cell><cell>40grpsearch1.html</cell><cell>1</cell><cell>263</cell><cell>264</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>134</cell><cell>Validation in serach </cell><cell>40grpsearch2.html</cell><cell>1</cell><cell>265</cell><cell>266</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>135</cell><cell>Search Templates </cell><cell>40grpsearch3.html</cell><cell>1</cell><cell>267</cell><cell>268</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>140</cell><cell>Hierarchy</cell><cell></cell><cell>0</cell><cell>280</cell><cell>290</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>141</cell><cell>Custom Icons </cell><cell>40subgrid1.html</cell><cell>1</cell><cell>281</cell><cell>282</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>142</cell><cell>Expand all Rows on load </cell><cell>40subgrid2.html</cell><cell>1</cell><cell>283</cell><cell>284</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>143</cell><cell>Load subgrid data only once</cell><cell>40subgrid3.html</cell><cell>1</cell><cell>285</cell><cell>286</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>150</cell><cell>Frozen Cols.Group Header(new)</cell><cell></cell><cell>0</cell><cell>290</cell><cell>300</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>151</cell><cell>Group Header - no colspan style </cell><cell>43groupnc.html</cell><cell>1</cell><cell>291</cell><cell>292</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>152</cell><cell>Group Header - with colspan style </cell><cell>43groupwc.html</cell><cell>1</cell><cell>293</cell><cell>294</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>153</cell><cell>Frozen column</cell><cell>43frozen1.html</cell><cell>1</cell><cell>295</cell><cell>296</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>156</cell><cell>Frozen column with group header</cell><cell>43frozen2.html</cell><cell>1</cell><cell>297</cell><cell>298</cell><cell>true</cell><cell>true</cell></row>

</rows>';

		echo $arbol;
	}
}
