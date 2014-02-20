<?php
class Mantenimiento extends Controller{

	function Mantenimiento(){
		parent::Controller();
		$this->load->library('rapyd');
		//$this->datasis->modulo_id('900',1);
	}

	function index(){
		$this->datasis->modulo_id('900',1);

		//$contenido  = '<center>'."\n";
		$contenido  = '<div class="mantenidiv" style="font-size:12px;">'."\n";
		$contenido .= '<div class="column">'."\n";

		$contenido .= $this->opciontb(
			'Actualizar Proteo',
			anchor('#',img(array('src'=>'assets/default/images/logo.png','border'=>'0','alt'=>'Actualizar','width'=>'130px')),array('onclick'=>'bobo(\''.base_url().'supervisor/mantenimiento/actualizaproteo\');return false;')),
			'Actualiza a la &uacute;ltima vesi&oacute;n'
		);

		$contenido .= $this->opciontb(
			'Borrar Prefacturas',
			anchor('#',img(array('src'=>'assets/default/images/clean-database.jpeg','border'=>'0','alt'=>'Actualizar')),array('onclick'=>'bobo(\''.base_url().'supervisor/mantenimiento/bprefac\');return false;')),
			'Borrar PreFacturas menores o iguales al d&iacute;a de ayer'
		);

		$contenido .= $this->opciontb(
			'Descargar Puertos',
			anchor('supervisor/mantenimiento/puertosdir',img(array('src'=>'assets/default/images/download.png','border'=>'0','alt'=>'Descargar Puertos'))),
			'Descargar PUERTOS.DIR para DataSIS '.anchor('supervisor/mantenimiento/puertosdir/LPT1','LPT1').', '.anchor('supervisor/mantenimiento/puertosdir/LPT2','LPT2').', ' .anchor('supervisor/mantenimiento/puertosdir/OBJETO','OBJ')
		);

/*
		$contenido .= $this->opciontb(
			'Cambiar Almacen en NE',
			anchor('#',img(array('src'=>'assets/default/images/package.png','border'=>'0','alt'=>'Actualizar')),array('onclick'=>'bobo(\''.base_url().'supervisor/mantenimiento/sntealma\');return false;')),
			'Modifica el almac&eacute;n en las notas de entrega'
		);
*/
		$contenido .= '</div>'."\n";
		$contenido .= '<div class="column">'."\n";

		$contenido .= $this->opciontb(
			'Centinelas',
			anchor('#',img(array('src'=>'assets/default/images/process-stop32.png','border'=>'0','alt'=>'Centinelas','id'=>'centinelas'))),
			'Centinelas o Mesajes del sistema'
		);

		$contenido .= $this->opciontb(
			'Revisa CLientes',
			anchor('#',img(array('src'=>'assets/default/images/clients.png','border'=>'0','alt'=>'Inconsistencias','id'=>'inconsist'))),
			'Incosistencias Clientes'
		);
			//anchor('supervisor/mantenimiento/clinconsis',img(array('src'=>'assets/default/images/clients.png','border'=>'0','alt'=>'Inconsistencia de Clientes'))),

		$contenido .= $this->opciontb(
			'Reportes Duplicados',
			anchor('supervisor/mantenimiento/sfacdif',img(array('src'=>'assets/default/images/report-database.jpeg','border'=>'0','alt'=>'Actualizar'))),
			'Detectar inconsistencias en Facturas'
		);


		$contenido .= '</div>'."\n";
		$contenido .= '<div class="column">'."\n";

		$contenido .= $this->opciontb(
			'Vaciar ModBus',
			anchor('#',img(array('src'=>'assets/default/images/delete-database.jpeg','border'=>'0','alt'=>'Borrar Temporales')),array('onclick'=>'bobo(\''.base_url().'supervisor/mantenimiento/bmodbus\');return false;')),
			'Vaciar tablas Temporales'
		);

		$contenido .= $this->opciontb(
			'Reparar Tablas',
			anchor('#',img(array('src'=>'assets/default/images/repair-database.jpeg','border'=>'0','alt'=>'Reparar')),array('onclick'=>'bobo(\''.base_url().'supervisor/mantenimiento/reparatabla\');return false;')),
			'Reparar Todas las Tablas de la BD'
		);

		$contenido .= $this->opciontb(
			'Recalcualr Inventario',
			anchor('#',img(array('src'=>'assets/default/images/inventario1.png','border'=>'0','alt'=>'Recalcular')),array('onclick'=>'bobo(\''.base_url().'supervisor/mantenimiento/calcosto\');return false;')),
			'Recalcula Inventario'
		);

/*
		$contenido .= $this->opciontb(
			'Mantenimiento de Tablas',
			anchor('supervisor/mantenimiento/tablas',img(array('src'=>'assets/default/images/accept-database.png','border'=>'0','alt'=>'Reparar'))),
			'Mantenimiento de Tablas'
		);

		$contenido .= $this->opciontb(
			'Modificar Contadores',
			anchor('#',img(array('src'=>'assets/default/images/speedometer.png','border'=>'0','alt'=>'Borrar Temporales')),array('onclick'=>'bobo(\''.base_url().'supervisor/mantenimiento/contadores\');return false;')),
			'Cambiar Contadores'
		);
*/
		$contenido .= '</div>'."\n";
		$contenido .= '</div>'."\n";
		//$contenido .= '</center>'."\n";

		$style = '
<style>
.column { width: 255px; float: left; padding-bottom: 100px; }
.portlet { margin: 0 1em 1em 0; }
.portlet-header { margin: 0em; padding-bottom: 4px; padding-left: 0.2em; }
//.portlet-header { margin: 0.3em; padding-bottom: 4px; padding-left: 0.2em; }
.portlet-header .ui-icon { float: right; }
.portlet-content { padding: 0.4em; }
.portlet-content1 { padding: 0.4em; background-color:#C9D4D8; }
.portlet-content2 { padding: 0.4em; }
.ui-sortable-placeholder { border: 1px dotted black; visibility: visible !important; height: 50px !important; }
.ui-sortable-placeholder * { visibility: hidden; }
</style>
';

		$script = '
<script>
$(function() {
	$(".column").sortable({ connectWith: ".column" });
	$(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" ).find(".portlet-header").addClass( "ui-widget-header ui-corner-all").prepend("<span class=\'ui-icon ui-icon-minusthick\'></span>").end().find( ".portlet-content" );
	$( ".portlet-header .ui-icon" ).click(function(){$( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );$( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();});
	$( ".column" ).disableSelection();
})

function bobo(url,mensa){'."
	$.blockUI({message: $('#displayBox'),css:{top:($(window).height()-400)/2+'px',left:($(window).width() -300)/2+'px',width:'400px'}".'});
	$.get(url, function(data){setTimeout($.unblockUI, 2);
	if ( !mensa ){ $.prompt(data); }

	});
	return false;
}

$("#centinelas").click( function() {
	$("#d900").load("supervisor/mantenimiento/centinelas");
})

$("#inconsist").click( function() {
	$("#d900").load("supervisor/mantenimiento/clinconsis");
})


</script>
';

		$contenido .= '<div id="displayBox" style="display:none" ><p>Disculpe por la espera.....</p><img  src="'.base_url().'images/doggydig.gif" width="131px" height="79px"  /></div>';

		$data['content'] = $contenido;

		$data['style']   = style('themes/proteo/proteo.css');
		$data['style']  .= style('impromptu/default.css');
		$data['style']  .= $style;

		$data['script']  = script('jquery-min.js');
		$data['script'] .= script('jquery-migrate-min.js');
		$data['script'] .= script('jquery-ui.custom.min.js');
		$data['script'] .= script('plugins/jquery.blockUI.js');
		$data['script'] .= script('jquery-impromptu.js');
		$data['script'] .= $script;

		$data['head']   = $this->rapyd->get_head();

		$data['title']   = '<h1>Mantenimiento</h1>';
		//$this->load->view('view_ventanas', $data);
		echo $script.$style.$contenido;

	}


	function opciontb( $titulo, $url, $leyenda ){
		$contenido = '
	<div class="portlet">
		<div class="portlet-header">'.$titulo.'</div>
		<div class="portlet-content">
			<table width="100%">
				<tr>
					<td>'.$url.'</td>
					<td>'.$leyenda.'</td>
				</tr>
			</table>
		</div>
	</div>
		';
		return $contenido;
	}

	function reparatabla(){
		$resulta = heading('Tablas con Fallas:');
		$sifallo = false;
		$this->datasis->modulo_id('900',1);
		$this->load->dbutil();
		$tables = $this->db->list_tables();
		foreach ($tables as $table){
			if(preg_match('/^(view_[a-zA-Z]*|sp_[a-zA-Z]*)/i', $table)){
				continue;
			}

			$query = $this->db->query('CHECK TABLE '.$table);
			if($query->num_rows() > 0){
				$row = $query->row();
				if(($row->Msg_text=='OK') || ($row->Msg_text=='Table is already up to date')){
					continue;
				}else{
					if ($row->Msg_type=='error'){
						if(!$this->dbutil->repair_table($table)){
							$sifallo = true;
							$resulta .= $table."\n";
						}
					}
				}
			}
		}
		if ( !$sifallo) $resulta .= "<p>Todas las Tablas se repararon con Exito</p>";
		echo $resulta ;
	}

	function bprefac(){
		$this->datasis->modulo_id('900',1);
		$mSQL="DELETE FROM sitems WHERE MID(numa,1,1)='_' AND fecha<CURDATE()";
		$this->db->simple_query($mSQL);
		$mSQL="DELETE FROM sfac WHERE MID(numero,1,1)='_' AND fecha<CURDATE()";
		$this->db->simple_query($mSQL);
		echo '<h1>Borrado Realizado con Exito</h1>';
	}

	function bmodbus(){
		$this->datasis->modulo_id('900',1);
		$mSQL="TRUNCATE modbus";
		$this->db->simple_query($mSQL);
		echo '<h1>Vaciado de Tablas temporales Realizado</h1>';

	}

	function centinelas(){
		$this->datasis->modulo_id('900',1);
		$this->load->helper('directory');

		$map = directory_map('./system/logs/');
		$lista=array();
		$mensajes = "<table width='100%'>";
		foreach($map AS $file) {
			if($file!='index.html')
				$mensajes .= '<tr><td><a href="#" onclick="elminacenti(\''.$file.'\')">'.img(array('src'=>'images/delete.jpg','border'=>'0','alt'=>'Elimina'))."</a><a href='javascript:void(0)' onclick=\"carga('$file')\" >$file</a></td></tr>\n";
		}
		$mensajes .= "</table>";

		$copy="<br><a href='javascript:void(0)' class='mininegro'  onclick=\"copiar()\" >Copiar texto</a>";
		$tadata = array('name' => 'sql','id'   => 'log','rows' => '20','cols' => '60');

		//$this->table->add_row(ul($lista), '<b id="fnom">Seleccione un archivo de centinela</b><br>'.$form);
		$link=site_url('supervisor/mantenimiento/vercentinela');


		$script ="
<script>
function carga(arch){
	link='$link'+'/'+arch;
	$('#fnom').text(arch);
	$('#log').load(link);
}

function cargamante() {
	$('#d900').load('supervisor/mantenimiento');
}

function elminacenti(cual){
	var url = '".site_url("supervisor/mantenimiento/borracentinela")."/'+cual;
	$.post(
		url,
		function(data){
			$('#d900').load('supervisor/mantenimiento/centinelas');
		}
	);
}

</script>";


		$contenido  = "<div>\n";
		$contenido .= "<table width='100%'>\n";
		$contenido .= "<tr><td>CENTINELAS</td><td align='right'>".image('go-previous.png','Regresar',array('onclick'=>'cargamante()','height'=>'30'))."</td></tr>";

		$contenido .= "<tr>\n";
		$contenido .= "<td style='valign:top;font-size:14px;'>\n";
		$contenido .= $mensajes;
		$contenido .= "</td>\n";
		$contenido .= "<td>\n";
		$contenido .= "<div id='log' style='font-size:12px;width:600px;height:300px;border:1px solid;'></div>\n";

		$contenido .= "</td>\n";
		$contenido .= "</tr>\n";

		$contenido .= "</table>\n";
		$contenido .= "</div>\n";

		echo $script.$contenido;
	}


	function vercentinela($file=NULL){
		$this->datasis->modulo_id('900',1);
		if(empty($file)) return FALSE;
		$this->load->helper('file');
		$string = read_file("./system/logs/$file");
		$string = $string;
		echo $string;
	}

	function borracentinela($file=NULL){
		$this->datasis->modulo_id('900',1);
		if(!empty($file)){
			$this->load->helper('file');
			unlink("./system/logs/$file");
		}
		redirect('supervisor/mantenimiento/centinelas');
	}
	function almainconsis(){

		$this->rapyd->load("datafilter","datagrid");
		$this->datasis->modulo_id('900',1);

		$filter = new DataFilter("Clientes inconsistentes");

		$filter->fechad = new dateonlyField('Desde','fechad');
		$filter->fechah = new dateonlyField('Hasta','fechah');
		$filter->fechad->clause  =$filter->fechah->clause="where";
		$filter->fechad->db_name =$filter->fechah->db_name="fecha";
		$filter->fechad->insertValue = date("Y-m-d");
		$filter->fechah->insertValue = date("Y-m-d");
		$filter->fechad->operator=">=";
		$filter->fechah->operator="<=";

		$filter->buttons("reset","search");
		$filter->build();

		if($this->rapyd->uri->is_set("search") AND $filter->is_valid()){
			$fechah=$filter->fechah->newValue;
			$fechad=$filter->fechad->newValue;

			$alma=$this->datasis->dameval("SELECT a.ubica FROM (`costos` as a) LEFT JOIN `caub` AS b ON `a`.`ubica`=`b`.`ubica` WHERE `b`.`ubica` = 'NULL' AND `origen` = '3I' AND a.fecha >= '$fechad' AND a.fecha <= '$fechah'");

			$uri = anchor('supervisor/mantenimiento/cambioalma/modify/<#tipo_doc#>/<#numero#>','Cambio');

			$grid = new DataGrid('Almacenes inconsistentes');
			$select=array('a.fecha','a.numero','a.cod_cli','a.tipo_doc','a.totalg','a.almacen');
			$grid->db->select($select);
			$grid->db->from('sfac as a');
			$grid->db->where("a.almacen",$alma);
			$grid->db->where("a.fecha >= ",$fechad);
			$grid->db->where("a.fecha <=",$fechah);
			$grid->per_page = 15;

			$grid->column('Fecha'      ,'<dbdate_to_human><#fecha#></dbdate_to_human>' ,'fecha');
			$grid->column('Numero'     ,'numero'  );
			$grid->column('Cliente'    ,'cod_cli' );
			$grid->column('Tipo'       ,'tipo_doc');
			$grid->column('Monto'      ,'totalg'  );
			$grid->column('Almacen'    ,'almacen' );
			$grid->column('Realizar'    ,$uri );

			$grid->build();

			$tabla=$grid->output;
		}else{
			$tabla='';
		}
		echo $filter->output.$tabla;

/*
		$data['content']  = $filter->output.$tabla;
		$data['title']    = "<h1>Almacenes con problemas de incosistencias</h1>";
		$data['head']     = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
*/



	}

	function cambioalma(){
		$this->datasis->modulo_id('900',1);
		$this->rapyd->load("dataedit");
		$edit = new DataEdit("Realizar cambio de almacen","sfac");
		$edit->back_url = site_url("supervisor/mantenimiento/almainconsis");

		$edit->fecha = new DateonlyField("Fecha", "fecha","d/m/Y");
		$edit->fecha->insertValue = date("Y-m-d");
		$edit->fecha->size = 10;
		$edit->fecha->mode="autohide";

		$edit->numero = new inputField("N&uacute;mero", "numero");
		$edit->numero->size = 10;
		$edit->numero->mode="autohide";

		$edit->tipo = new dropdownField("Tipo", "tipo_doc");
		$edit->tipo->option("D","D");
		$edit->tipo->option("F","F");
		$edit->tipo->option("X","X");
		$edit->tipo->mode="autohide";

		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->size = 55;
		$edit->nombre->maxlength=40;
		$edit->nombre->mode="autohide";

		$edit->almacen = new  dropdownField ("Almacen", "almacen");
		$edit->almacen->option("","Todos");
		$edit->almacen->options("SELECT ubica, ubides FROM caub WHERE gasto='N' and invfis='N' ORDER BY ubides");

		$edit->buttons("modify", "save", "undo", "back");
		$edit->build();

		$data['content'] =$edit->output;
		$data['title']   = "Almacen Inconsistente";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function clinconsis(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();
		$this->datasis->modulo_id('900',1);

		$scli=array(
		'tabla'   =>'scli',
		'columnas'=>array(
		'cliente' =>'C&oacute;digo Cliente',
		'nombre'  =>'Nombre',
		'contacto'=>'Contacto'),
		'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'cod_cli'),
		'titulo'  =>'Buscar Cliente');

		$boton=$this->datasis->modbus($scli);

		$filter = new DataFilter('');
		$select=array(
			'a.fecha',
			'a.tipo_doc',
			'a.cod_cli',
			'a.numero',
			'a.nombre',
			'a.monto',
			'(SUM(b.abono)+
			(SELECT COALESCE(SUM(d.monto),0) FROM `itcruc` AS d  JOIN cruc AS e ON d.numero=e.numero WHERE e.tipo LIKE "C%" AND e.proveed=a.cod_cli AND CONCAT(`a`.`tipo_doc`,`a`.`numero`)=`d`.`onumero`)+
			(SELECT COALESCE(SUM(d.monto),0) FROM `itcruc` AS d  JOIN cruc AS e ON d.numero=e.numero WHERE e.tipo LIKE "%C" AND e.cliente=a.cod_cli AND CONCAT(`a`.`tipo_doc`,`a`.`numero`)=`d`.`onumero`))
			AS abonoreal',
			'a.abonos AS inconsist',);

		$filter->db->select($select);
		$filter->db->from('smov AS a');
		$filter->db->join('itccli AS b','a.cod_cli=b.cod_cli AND a.numero=b.numero AND a.tipo_doc=b.tipo_doc');
		$filter->db->groupby('a.cod_cli, a.tipo_doc,a.numero');
		$filter->db->having('abonoreal  <>','inconsist');
		$filter->db->orderby('a.cod_cli','b.numero');

		$filter->fechad = new dateonlyField('Desde','fechad');
		$filter->fechah = new dateonlyField('Hasta','fechah');
		$filter->fechad->clause  =$filter->fechah->clause="where";
		$filter->fechad->db_name =$filter->fechah->db_name="a.fecha";
		$filter->fechad->operator=">=";
		$filter->fechah->operator="<=";

		$filter->cliente = new inputField("Cliente", "cod_cli");
		$filter->cliente->db_name="a.cod_cli";
		$filter->cliente->size = 30;
		$filter->cliente->append($boton);

		$filter->buttons("reset","search");
		$filter->build();

		function descheck($numero,$cod_cli,$tipo_doc,$fecha,$abonoreal){
			$pk=array($numero,$cod_cli,$tipo_doc,$fecha,$abonoreal);
			$str=htmlspecialchars(serialize($pk));
			$data = array(
			  'name'    => 'pk',
			  'value'   => $str,
			  'checked' => FALSE);
			return form_checkbox($data);
		}

		function diff($a,$b){
			return nformat($a-$b);
		}

		$uri1 = anchor('supervisor/mantenimiento/itclinconsis/<str_replace>/|:slach:|<#cod_cli#></str_replace>/<#numero#>/<#tipo_doc#>','<#cod_cli#>');
		$uri2 = anchor('supervisor/mantenimiento/ajustar/<#cod_cli#>','Ajustar Saldo');

		$grid = new DataGrid("Lista de Clientes");
		$grid->use_function('descheck','diff');
		$grid->per_page = 15;
		$grid->use_function('str_replace');

		$grid->column_orderby('Cliente'        ,$uri1    ,'cod_cli');
		$grid->column_orderby('Nombre'         ,'nombre' ,'nombre');
		$grid->column_orderby('Fecha'          ,'<dbdate_to_human><#fecha#></dbdate_to_human>' ,'fecha');
		$grid->column_orderby('N&uacute;mero'  ,'<#tipo_doc#><#numero#>'    ,'numero');
		$grid->column_orderby('Monto'          ,'<nformat><#monto#></nformat>'        ,'monto'     ,"align='right'");
		$grid->column_orderby('Abono Real'     ,'<nformat><#abonoreal#></nformat>'    ,'abonoreal' ,"align='right'");
		$grid->column_orderby('Abono Inconsis.','<nformat><#inconsist#></nformat>'    ,'inconsist' ,"align='right'");
		$grid->column('Faltante'               ,'<diff><#abonoreal#>|<#inconsist#></diff>',"align='right'");
		$grid->column('Ajustar Saldo'          ,'<descheck><#numero#>|<#cod_cli#>|<#tipo_doc#>|<#fecha#>|<#abonoreal#></descheck>',"align=center");

		$grid->build();

		$script='';
		$url=site_url('supervisor/mantenimiento/ajustesaldo');
		$data['script']='<script type="text/javascript">
			$(document).ready(function() {
				$("form :checkbox").click(function () {
				$.ajax({
					  type: "POST",
					  url: "'.$url.'",
					  data: $(this).serialize(),
					  success: function(msg){
					    if(msg==0)
					      alert("No se puedo ajustar el saldo");
					  }
					});
				}).change();
			});
			</script>';

		/*echo $filter->output;
		echo form_open('').$grid->output.form_close().$script;*/

		$data['content']  = $filter->output;
		$data['content'] .= form_open('').$grid->output.form_close().$script;
		$data['title']    = "<h1>Clientes con problemas de incosistencias</h1>";
		$data['head']     = script("jquery.js");
		$data['head']    .= script("plugins/jquery.checkboxes.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function clinconsismasivo(){
		$this->datasis->modulo_id('900',1);
		$mSQL="SELECT
			`a`.`fecha`,
			`a`.`tipo_doc`,
			`a`.`cod_cli`,
			`a`.`numero`,
			sum(b.abono)+(SELECT COALESCE(SUM(d.monto),0) FROM `itcruc` AS d WHERE CONCAT(`a`.`tipo_doc`,`a`.`numero`)=`d`.`onumero`) AS abonoreal,
			`a`.`abonos` AS inconsist
			FROM (`smov` AS a)
			JOIN `itccli` AS b ON `a`.`cod_cli`=`b`.`cod_cli` AND a.numero=b.numero AND a.tipo_doc=b.tipo_doc
			WHERE b.tipo_doc='FC'
			GROUP BY `a`.`cod_cli`, `a`.`tipo_doc`, `a`.`numero` HAVING `abonoreal` <> inconsist LIMIT 300";

		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				if(!$this->_sclisaldo($row->numero,$row->cod_cli,$row->tipo_doc,$row->fecha,$row->abonoreal)){
					echo "No se pudo cambiar ".$row->numero.' '.$row->cod_cli;
				}
			}
		}
	}

	function ajustesaldo(){
		$this->datasis->modulo_id('900',1);
		$pk  = unserialize(htmlspecialchars_decode($this->input->post('pk')));
		if(count($pk)!=5){
			echo 0;
		}else{
			if($this->_sclisaldo($pk[0],$pk[1],$pk[2],$pk[3],$pk[4]))
			echo 1;
			else
			echo 0;
		}
	}

	function _sclisaldo($numero,$cod_cli,$tipo_doc,$fecha,$abono){
		$data = array('abonos' => $abono);

		$where  =' numero='.$this->db->escape($numero);
		$where .=' AND cod_cli ='.$this->db->escape($cod_cli);
		$where .=' AND tipo_doc='.$this->db->escape($tipo_doc);
		$where .=' AND fecha   ='.$this->db->escape($fecha);

		$mSQL = $this->db->update_string('smov', $data, $where);

		return $this->db->simple_query($mSQL);
	}


	function itclinconsis($cliente='',$numero='',$tipo_doc){
		$this->datasis->modulo_id('900',1);
		$this->rapyd->load("datagrid2");
		$this->rapyd->uri->keep_persistence();

		$uri = anchor('supervisor/mantenimiento/clinconsis','Regresar');

		$select=array('numccli','tipoccli','fecha','abono','tipo_doc','cod_cli');
		$grid = new DataGrid2($uri);
		$grid->per_page = 15;
		$grid->db->select($select);
		$grid->db->from('itccli');
		$grid->db->where('cod_cli',$cliente);
		$grid->db->where('tipo_doc',$tipo_doc);
		$grid->db->where('numero',$numero);

		$grid->column('Numero' ,'numccli' );
		$grid->column('Tipo'   ,'tipoccli' );
		$grid->column('Fecha'  ,'<dbdate_to_human><#fecha#></dbdate_to_human>');
		$grid->column('Monto'  ,'<nformat><#abono#></nformat>',"align='right'");

		$grid->totalizar('abono');
		$grid->build();

		$data['content'] = $grid->output;
		$data['title']   = "<h1>Detalle de los Abonos del cliente:$cliente</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function sfacdif(){
		$this->datasis->modulo_id('900',1);
		$this->rapyd->load('dataform','datagrid2');
		$this->rapyd->uri->keep_persistence();

		$edit = new DataForm('supervisor/mantenimiento/sfacdif/process');

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule       = 'chfecha';
		$edit->fecha->dbformat   = 'Ymd';
		$edit->fecha->size       = 10;
		$edit->fecha->insertValue= date('Y-m-d');
		$edit->fecha->maxlength  = 8;

		$edit->submit('btnsubmit','Procesar');
		$edit->build_form();

		$tabla='';
		if($edit->on_success()){
			$dbfecha = $this->db->escape($edit->fecha->newValue);
			$mSQL="CALL `sp_sfacdif`($dbfecha)";
			$query = $this->db->query($mSQL);
			$arr=array();
			foreach ($query->result_array() as $row){
				$arr[]=$row;
			}

			$grid = new DataGrid2('',$arr);
			//$grid->per_page = 15;

			$grid->column('Transac'        ,'transac' );
			$grid->column('Tipo'           ,'<#tipo_doc#><#numsfac#>' );
			$grid->column('Fecha'          ,'<dbdate_to_human><#fecha#></dbdate_to_human>');
			$grid->column('Referencia'     ,'ref' );
			$grid->column('N.fiscal'       ,'nfiscal' );
			$grid->column('N.sfac'         ,'numsfac' );
			$grid->column('N.sfpa'         ,'numsfpa' );
			$grid->column('N.sitems'       ,'numitem' );
			$grid->column('SFAC'           ,'<nformat><#sfac#></nformat>' );
			$grid->column('SFPA'           ,'<nformat><#sfpa#></nformat>' );
			$grid->column('SITEMS'         ,'<nformat><#sitems#></nformat>' );
			$grid->column('Dif.Pag'        ,'<nformat><#dife1#></nformat>' );
			$grid->column('Dif.ite'        ,'<nformat><#dife2#></nformat>' );
			$grid->column('Usuario sfac'   ,'ususfac' );
			$grid->column('Usuario sfpa'   ,'ususfpa' );
			$grid->column('Usuario sitems' ,'usuitem' );

			$grid->totalizar('dife1','dife2');
			$grid->build();
			$tabla=$grid->output;
		}

		$data['content'] = $edit->output.$tabla;
		$data['title']   = heading('Diferencia en facturaci&ocute;1n');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}


	function contadores(){
		$this->datasis->modulo_id('900',1);
		if(!$this->datasis->essuper()) show_404();

		$this->rapyd->load("dataform");
		$edit = new DataForm('supervisor/mantenimiento/contadores/process');

		$edit->numero = new inputField("N&uacute;mero", "numero");
		$edit->numero->rule='required|numeric|max_length[8]';
		$edit->numero->size = 10;
		$edit->numero->maxlength=8;
		$edit->numero->append('El n&uacute;mero que coloque va a ser el pr&oacute;ximo n&uacute;mero que proporcione el contador');

		$edit->container = new containerField("alert","<div class='alert'>Haga uso de este modulo solo si sabe lo que esta haciendo, una cambio mal puede dejar inoperativo el sistema</div>");

		$_POST['confirma']='';
		$edit->confirma = new inputField('Escriba ACEPTO para confirmaci&oacute;n', 'confirma');
		$edit->confirma->rule='callback_confirma';
		$edit->confirma->size = 6;
		$edit->confirma->append('Sencible a las may&uacute;sculas');

		$edit->submit('btnm_submit','Aceptar');

		$edit->build_form();

		$sal='';
		if ($edit->on_success()){
			$num = $edit->numero->newValue;
			$sal=$this->_contadores($num);
		}

		$data['content'] = $edit->output.'<pre>'.$sal.'</pre>';
		$data['title']   = '<h1>Cambio en los contadores</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _contadores($num=null){
		$rt='';
		if(!empty($num) && is_numeric($num)){
			$tables = $this->db->list_tables();

			foreach ($tables as $table){
				$fields = $this->db->list_fields($table);
				if(count($fields)==3){
					if($fields[0]=='numero' && $fields[1]=='usuario' && $fields[2]=='fecha'){
						$mSQL="DELETE FROM `${table}` WHERE numero>=${num}";
						if($this->db->simple_query($mSQL)){
							$mSQL="ALTER TABLE `$table` AUTO_INCREMENT=${num}";
							if (!$this->db->simple_query($mSQL)){
								$rt.= "Error cambiando el contador en ${table} \n";
							}else{
								$rt.= "${table} cambiado \n";
							}
						}
					}
				}
			}
		}
		return $rt;
	}


	function statuscont(){
		$tables = $this->db->list_tables();

		foreach ($tables as $table){
			$fields = $this->db->list_fields($table);
			if(count($fields)==3){
				if($fields[0]=='numero' && $fields[1]=='usuario' && $fields[2]=='fecha'){
					$mSQL="SHOW TABLE STATUS LIKE '${table}'";
					 $query = $this->db->query($mSQL);
					if($query->num_rows() >0){
						$row = $query->row();
						echo '<b>'.$table.'</b>: '.$row->Auto_increment.br();
					}
				}
			}
		}
	}

	function confirma($par){
		if($par=='ACEPTO'){
			return true;
		}
		$this->validation->set_message('confirma', 'Debe escribir ACEPTO en la confirmaci&oacute;n');
		return false;
	}

	function tablas(){
		$this->datasis->modulo_id('900',1);
		$this->rapyd->load("dataform","datatable");
		$tables = $this->db->list_tables();

		$form = new DataForm("supervisor/mantenimiento/tablas/process");
		$form->free = new freeField("Lista de Tablas","free","Chequear|Reparar|Optimizar");
		foreach($tables as $tabla){
			$che="chequea_".$tabla;
			$re="repara_".$tabla;
			$op="optimi_".$tabla;
			$ob1="con_".$tabla;
			$ob2="con2_".$tabla;

			$form->$che = new checkboxField("$tabla", "$che","CHECK TABLE $tabla","no");
			$form->$ob1 = new containerField("","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
			$form->$ob1->in="$che";
			$form->$re = new checkboxField("", "$re","REPAIR TABLE $tabla","no");
			$form->$re->in="$che";
			$form->$ob2 = new containerField("","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
			$form->$ob2->in="$che";
			$form->$op = new checkboxField("", "$op","OPTIMIZE TABLE $tabla","no");
			$form->$op->in="$che";
		}

		$form->submit("btnsubmit","Aceptar");
		$form->build_form();

		if  ($form->on_show() || $form->on_error()) {
			$data["content"] =$form->output;
		}

		if ($form->on_success()){
			$data["content"] = "<h1>Procesos y Consultas generadas</h1><br>";
			$atras=anchor('supervisor/mantenimiento/tablas','Atras');
			//print_R($_POST);
			foreach($_POST as $nom=>$val){


				if($this->db->simple_query($val)){
					$data["content"].= "Se Proceso:".$nom." Con la consulta:(".$val.")<br>";
				}else{
					if($val=="Aceptar") break;
					$data["content"].= "Error en consulta:".$val."<br>";
				}
			}
			$data["content"].=$atras;
		}
		$data['title']   = "Mantenimiento de tablas";
		$data["rapyd_head"] = $this->rapyd->get_head();

		$this->load->view("view_ventanas", $data);
	}


	function sntealma(){
		$this->datasis->modulo_id('900',1);
		$this->rapyd->load("datafilter","datagrid");

		$filter = new DataFilter("Cambio de almac&eacute;n en notas de entrega",'snte');

		$filter->numero = new inputField('N&uacute;mero','numero');
		$filter->numero->size=10;

		$filter->fechad = new dateonlyField('Desde','fechad');
		$filter->fechah = new dateonlyField('Hasta','fechah');
		$filter->fechad->clause  =$filter->fechah->clause="where";
		$filter->fechad->db_name =$filter->fechah->db_name="fecha";
		$filter->fechad->insertValue = date("Y-m-d");
		$filter->fechah->insertValue = date("Y-m-d");
		$filter->fechad->operator=">=";
		$filter->fechah->operator="<=";

		$filter->buttons("reset","search");
		$filter->build();

		if($this->rapyd->uri->is_set("search") AND $filter->is_valid()){

			$uri = anchor('supervisor/mantenimiento/sntecambioalma/modify/<#numero#>','<#almacen#>');

			$grid = new DataGrid('Notas de entrega');
			$grid->per_page = 15;

			$grid->column('Fecha'      ,'<dbdate_to_human><#fecha#></dbdate_to_human>');
			$grid->column('Almac&eacute;n'    ,$uri );
			$grid->column('Numero'     ,'numero'  );
			$grid->column('Cliente'    ,'cod_cli' );
			$grid->column('Nombre'    ,'nombre' );
			$grid->column('Monto'      ,'<nformat><#gtotal#></nformat>'  ,'align="right"');

			$grid->build();

			$tabla=$grid->output;
		}else{
			$tabla='';
		}
		$data['content']  = $filter->output.$tabla;
		$data['title']    = "<h1>Cambio de almac&eacute;n en notas de entrega</h1>";
		$data['head']     = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function sntecambioalma(){
		$this->datasis->modulo_id('900',1);
		$this->rapyd->load('dataedit');
		$edit = new DataEdit('Realizar cambio de almac&eacute;n','snte');
		$edit->back_url = site_url('supervisor/mantenimiento/sntealma');

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->mode='autohide';

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->size = 55;
		$edit->nombre->maxlength=40;
		$edit->nombre->mode='autohide';

		$edit->almacen = new  dropdownField ('Almac&eacute;n', 'almacen');
		$edit->almacen->option('','Todos');
		$edit->almacen->options("SELECT ubica,CONCAT_WS('-',ubica,ubides) AS val FROM caub WHERE gasto='N' and invfis='N' ORDER BY ubides");

		$edit->buttons('modify', 'save', 'undo', 'back');
		$edit->build();

		$data['content'] =$edit->output;
		$data['title']   = '<h1>Cambio de almac&eacute;n en nota de entrega</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function secuencias(){
		$tables = $this->db->list_tables();
		foreach ($tables as $table){
			$fields = $this->db->list_fields($table);
			if(count($fields)==3){
				if($fields[0]=='numero' AND $fields[1]=='usuario' AND $fields[2]=='fecha'){
					$mSQL="SHOW TABLE STATUS LIKE '$table'";
					$query = $this->db->query($mSQL);
					if($query->num_rows() > 0){
						$row = $query->row();
						echo $table.' <b>'.$row->Auto_increment.'</b>'.br();
					}
				}
			}
		}
	}

	function actualizaproteo(){
		session_write_close();
		set_time_limit(3600);
		$responde = '<h1>Resultado de la Actualizacion</h1>';
		if (!extension_loaded('svn')) {
			$responde .= 'La extension svn no esta cargada, debe cargarla para poder usar estas opciones...';
		}else{
			$dir=getcwd();
			$svn=$dir.'/.svn';

			if(!is_writable($svn)){
				$responde .= 'No se tiene permiso al directorio .svn, comuniquese con soporte t&eacute;cnico...';
			}else{

				$antes = $this->datasis->traevalor('SVNVER','Version svn de proteo');
				if(empty($antes)) $aver=0; else $aver=intval($antes);

				$ver =@svn_update($dir);

				if($ver>0){
					if($ver>$aver){
						$responde .= 'Actualizado a la versi&oacute;n: '.$ver;
						$dbver = $this->db->escape($ver);
						$mSQL="UPDATE valores SET valor=${dbver} WHERE nombre='SVNVER'";
						$this->db->simple_query($mSQL);
						//$this->db->simple_query('TRUNCATE modbus');
					}else{
						$responde .= 'Ya estaba la ultima versi&oacute;n instalada '.$aver;
					}
				}else{
					$responde .= 'Hubo problemas con la actualizaci&oacute;n, comuniquese con soporte t&eacute;cnico';
				}
			}
		}
		$host= $this->db->hostname;
		$db  = $this->db->database;
		$pwd = $this->db->password;
		$usr = $this->db->username;
		$file= tempnam('/tmp',$db.'.sql');
		echo $responde;
	}

	function respaldo(){
		$this->datasis->modulo_id('900',1);
		if(!$this->datasis->essuper()) show_404();
		$this->load->library('zip');
		$host= $this->db->hostname;
		$db  = $this->db->database;
		$pwd = $this->db->password;
		$usr = $this->db->username;
		$file= tempnam('/tmp',$db.'.sql');

		if(!empty($pwd)){
			$pwd = "-p ${pwd}";
		}

		$cmd="mysqldump -u ${usr} ${pwd} -h ${host} --opt --routines ${db} > ${file}";
		$sal=exec($cmd);

		$this->zip->read_file($file);
		$this->zip->download($db.'.zip');
		unlink($file);
	}

	function puertosdir($obj=null){
		$this->load->helper('download');

		if (extension_loaded('dbase')) {
			$def = array(
			    array('FORMA'  , 'C',  10),
			    array('PUERTO' , 'C',  60),
			    array('DESCRIP', 'C',  200),
			);
			$temp =tempnam("/tmp", 'puertos');
			$db=dbase_create($temp, $def);
			if ($db){
				$query = $this->db->query('SELECT nombre FROM formatos UNION SELECT nombre FROM reportes');
				if ($query->num_rows() > 0){
					foreach ($query->result() as $row){
						if(is_null($obj)){
							$pivot=array($row->nombre,'C:\\spool\\'.$row->nombre.'.TXT','');
						}else{
							$pivot=array($row->nombre,$obj,'');
						}
						dbase_add_record($db, $pivot);
					}
				}
				dbase_close($db);
			}
			$data = file_get_contents($temp);
			force_download('PUERTOS.DIR', $data);
			unlink($temp);
			//echo "<h1>Archivo listo para descargar</h1><a>PUERTOS.DIR</a>";
		}else{
			echo '<h1>Falla de Sistema</h1>Debe cargar las librerias dbase para poder usar este modulo';
		}
	}

	//Para reconstruir sfac a partir de sfpa, sitems,scli y sinv
	function ressfac(){
		$mSQL="INSERT INTO sfac
		SELECTaa.tipoa,aa.numa,aa.fecha,aa.fecha,aa.vendedor,aa.codigoa,aa.rifci,aa.nombre,aa.dire11,aa.dire12,'' AS orden,'' AS referen,SUM(aa.iva),0 AS inicial,SUM(aa.tota) AS totals,SUM(aa.tota+aa.iva) AS totalg,'' AS status, '' AS observa,'' AS observ1,0 AS devolu,aa.cajero,'0001' AS almacen,0 AS peso,'' AS factura,'' AS pedido,aa.usuario,aa.estampa,aa.hora,aa.transac,'' AS nfiscal,'' AS zona,'' AS ciudad,0 AS comision,'N' AS pagada,'N' AS sepago,
		0  AS dias,
		'' AS fpago,
		0  AS comical,
		SUM(aa.tota*(aa.sinviva=0))  AS exento,
		SUM(aa.iva*(aa.sinviva=12))  AS tasa,
		SUM(aa.iva*(aa.sinviva=8))   AS reducida,
		SUM(aa.iva*(aa.sinviva=22))  AS sobretasa,
		SUM(aa.tota*(aa.sinviva=12)) AS montasa,
		SUM(aa.tota*(aa.sinviva=8))  AS monredu,
		SUM(aa.tota*(aa.sinviva=22)) AS monadic,
		'' AS notcred,'' AS fentrega,'' AS  fpagom,'' AS fdespacha,'' AS udespacha,'' AS numarma,'' AS maqfiscal,null AS id,'' AS dmaqfiscal,'' AS nromanual,'' AS fmanual,'' AS lleva
		FROM
		(SELECT b.*,c.iva AS sinviva,c.peso,e.cliente,e.nombre,e.dire11,e.dire12,e.rifci
		FROM sfac AS a
		RIGHT JOIN sitems AS b ON a.tipo_doc=b.tipoa AND a.numero=b.numa
		JOIN sinv AS c ON b.codigoa=c.codigo
		JOIN sfpa AS d ON b.tipoa=MID(d.tipo_doc,1,1) AND b.numa=d.numero
		JOIN scli AS e ON d.cod_cli=e.cliente WHERE a.numero IS NULL) AS aa
		GROUP BY aa.tipoa,aa.numa";
		echo $mSQL;
	}

	function factraza($numero=null){
		$this->datasis->modulo_id('900',1);
		if(empty($numero)) show_error('Falta n&uacute;mero de factura');
		$this->rapyd->load('datafilter','datagrid');

		$sel=array('b.tipoa','a.fecha','b.codigoa','b.desca','b.despacha','b.udespacha','b.fdespacha','b.cana');
		$grid = new DataGrid('Productos de la factura '.$numero);
		$grid->db->select($sel);
		$grid->db->from('sfac AS a');
		$grid->db->join('sitems AS b', 'a.numero=b.numa AND a.tipo_doc=b.tipoa');
		$grid->db->where('a.numero',$numero);
		$grid->db->order_by('b.codigoa');
		$grid->column('C&oacute;digo','codigoa');
		$grid->column('Descripci&oacute;n','desca'  );
		$grid->column('Despachado' ,'<#despacha#> : <#udespacha#> <dbdate_to_human><#fdespacha#></dbdate_to_human>');
		$grid->column('Fecha'   ,'<dbdate_to_human><#fecha#></dbdate_to_human>');
		$grid->column('Cantidad', 'cana','align="left"');
		$grid->build();

		$sel=array('b.tipoa','b.numa','b.codigoa','b.desca','b.despacha','b.udespacha','b.fdespacha','b.cana');
		$grid2 = new DataGrid('Devoluciones de la factura '.$numero);
		$grid2->db->select($sel);
		$grid2->db->from('sfac AS a');
		$grid2->db->join('sitems AS b', 'a.numero=b.numa AND a.tipo_doc=b.tipoa');
		$grid2->db->where('a.factura',$numero);
		$grid2->db->order_by('b.numa','b.codigoa');
		$grid2->column('Tipo','tipoa');
		$grid2->column('N&uacute;mero','numa');
		$grid2->column('C&oacute;digo','codigoa');
		$grid2->column('Descripci&oacute;n','desca'  );
		//$grid2->column('Despachado' ,'<#despacha#> : <#udespacha#> <dbdate_to_human><#fdespacha#></dbdate_to_human>');
		$grid2->column('Cantidad', 'cana','align="rigth"');
		$grid2->build();

		$sel=array('b.codigo','b.numero','b.descrip','b.entrega','b.saldo','b.cant');
		$grid3 = new DataGrid('Notas de despacho '.$numero);
		$grid3->db->select($sel);
		$grid3->db->from('snot AS a');
		$grid3->db->join('itsnot AS b', 'a.numero=b.numero');
		$grid3->db->where('a.factura',$numero);
		$grid3->db->order_by('b.numero','b.codigo');
		$grid3->column('N&uacute;mero','numero');
		$grid3->column('C&oacute;digo'      ,'codigo');
		$grid3->column('Descripci&oacute;n' ,'descrip'  );
		$grid3->column('Cantidad', 'cant'   ,'align="rigth"');
		$grid3->column('Saldo'   , 'saldo'  ,'align="rigth"');
		$grid3->column('Entrega' , 'entrega','align="rigth"');
		$grid3->build();

		$tabla=$grid->output.br().br().$grid2->output.br().br().$grid3->output;
		$data['content']  = $tabla;
		$data['title']    = heading('Trazas de factura');
		$data['head']     = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function calcosto(){
		$this->db->simple_query("CALL sp_calcopasa()");
		$this->db->simple_query("CALL sp_calcoinv()");
		$this->db->simple_query("CALL sp_calcoestadis()");
		echo '<h1>Recalculo Concluido</h1>';
	}

}
