<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/validaciones.php');
class repomenu extends validaciones {
	var $genesal=true;

	function repomenu(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('90B',1);
	}

	function index(){
		redirect('supervisor/repomenu/filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		//$this->rapyd->uri->keep_persistence();

		function llink($nombre,$alternativo,$modulo){
			if(!empty($nombre))
				$uri  = anchor("supervisor/repomenu/dataedit/show/${nombre}/${modulo}",$nombre);
			else
				$uri  = anchor("supervisor/repomenu/dataedit/${alternativo}/create",$alternativo);
			return $uri;
		}

		function ractivo($nombre,$activo,$modulo){
			if(!empty($activo)){
				$bandera= ($activo=='S') ? 1: 0;
				$retorna = form_checkbox("$nombre|$modulo", 'accept', $bandera);
			}else{
				$retorna  = 'NI';
			}
			return $retorna ;
		}

		$sel=array('b.nombre AS alternativo','a.nombre','a.modulo','a.titulo','a.mensaje','a.activo','b.reporte','b.proteo','b.harbour','b.instancias');
		$filter = new DataFilter('Filtro por Menu de Reportes');
		$filter->db->select($sel);
		$filter->db->from('intrarepo AS a');
		$filter->db->join('reportes  AS b','a.nombre=b.nombre','right');

		$filter->nombre = new inputField('Nombre', 'nombre');
		$filter->nombre->db_name='b.nombre';
		$filter->nombre->size=20;
		$filter->nombre->group = 'Uno';

		$filter->modulo = new dropdownField('Modulo','modulo');
		$filter->modulo->db_name = 'a.modulo';
		$filter->modulo->option('','Todos');
		$filter->modulo->options('SELECT modulo,modulo AS value FROM intrarepo GROUP BY modulo');
		$filter->modulo->style='width:130px';
		$filter->modulo->group = 'Uno';

		$filter->titulo = new inputField('T&iacute;tulo','titulo');
		$filter->titulo->db_name = 'a.titulo';
		$filter->titulo->size=30;
		$filter->titulo->group = 'Uno';

		$filter->activo = new dropdownField('Activo','activo');
		$filter->activo->db_name = 'a.activo';
		$filter->activo->option('','Todos');
		$filter->activo->option('S','Si');
		$filter->activo->option('N','No');
		$filter->activo->style='width:80px';
		$filter->activo->group = 'Uno';

		$filter->proteo = new inputField('Contenido Proteo','proteo');
		$filter->proteo->db_name = 'b.proteo';
		$filter->proteo->size=40;
		$filter->proteo->db_name='b.proteo';
		$filter->proteo->group = 'Dos';

		$filter->reporte = new inputField('Contenido Datasis','reporte');
		$filter->reporte->size=40;
		$filter->reporte->db_name='b.reporte';
		$filter->reporte->group = 'Dos';

		$filter->harbourd = new inputField('Contenido Harbourd','harbourd');
		$filter->harbourd->size=40;
		$filter->harbourd->db_name='b.harbour';
		$filter->harbourd->group = 'Dos';

		$filter->mensaje = new inputField('Mensaje','mensaje');
		$filter->mensaje->size=40;
		$filter->mensaje->db_name='a.mensaje';
		$filter->mensaje->group = 'Dos';

		/*$filter->tcpdf = new inputField('Contenido TCPDF','tcpdf');
		$filter->tcpdf->size=40;
		$filter->tcpdf->db_name='b.tcpdf';*/

		$filter->buttons('reset','search');
		$filter->build('dataformfiltro');

		$uri1 = anchor('supervisor/repomenu/reporte/modify/<#alternativo#>/' ,'Editar');
		$uri2 = anchor('supervisor/repomenu/rdatasis/modify/<#alternativo#>/','Editar');
		$uri3 = anchor('supervisor/repomenu/rharbour/modify/<#alternativo#>/','Editar');
		$uri5 = anchor('supervisor/repomenu/rtcpdf/modify/<#alternativo#>/'  ,'Editar');

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$uri4=anchor_popup('reportes/ver/<#alternativo#>/<#modulo#>', 'Probar', $atts);

		$grid = new DataGrid('Lista de Menu de Reportes');
		$grid->use_function('llink','ractivo');
		$grid->order_by('nombre','asc');
		$grid->per_page = 15;

		$grid->column('Nombre'  ,'<llink><#nombre#>|<#alternativo#>|<#modulo#></llink>');
		$grid->column('Modulo'  ,'modulo');
		$grid->column('Titulo'  ,'titulo');
		$grid->column('Mensaje' ,'mensaje');
		$grid->column('Activo'  ,'<ractivo><#alternativo#>|<#activo#>|<#modulo#></ractivo>',"align='center'");
		$grid->column('Proteo'  ,$uri1);
		$grid->column('DataSIS' ,$uri2);
		$grid->column('Harbour' ,$uri3);
		$grid->column_orderby('Inst.','instancias','instancias');
		//$grid->column('TCPDF'   ,$uri5);
		$grid->column('Ejecutar',$uri4);

		$grid->add('supervisor/repomenu/dataedit/create');
		$grid->build();
		//echo $grid->db->last_query();

		$url=site_url('supervisor/repomenu/cactivo');
		$data['script']='<script type="text/javascript">
		$(document).ready(function() {
			$("form :checkbox").click(function () {
				$.ajax({
					type: "POST",
					url: "'.$url.'",
					data: {"codigo" : this.name},
					success: function(msg){
						if (msg==0)
							alert("Ocurrio un problema");
						}
					});
			}).change();
		});
		</script>';
		$data['content'] = '<form>'.$grid->output.'</form>';
		$data['filtro']  = $filter->output;
		$data['title']   = heading('Gestor de Reportes');
		$data['head']    = script('jquery.pack.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($nombre){
		$this->rapyd->load('dataedit');
		//$this->rapyd->uri->keep_persistence();

		$edit = new DataEdit('Menu de Reportes', 'intrarepo');
		$edit->back_url = site_url('supervisor/repomenu/filteredgrid');
		$edit->post_process('insert','_post_insert');
		//$edit->post_process('delete','_post_delete');

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->mode= 'autohide';
		$edit->nombre->rule= 'strtoupper|required';
		$edit->nombre->size = 20;
		if($nombre!='create') $edit->nombre->insertValue = $nombre;

		$edit->modulo = new inputField('M&oacute;dulo','modulo');
		$edit->modulo->size =20;
		$edit->modulo->rule= 'strtoupper|required';

		$edit->titulo=new inputField('T&iacute;tulo','titulo');
		$edit->titulo->size =40;

		$edit->mensaje =new inputField('Mensaje', 'mensaje');
		$edit->mensaje->size = 50;

		$edit->activo = new dropdownField('Activo','activo');
		$edit->activo->option('S','Si');
		$edit->activo->option('N','No');
		$edit->activo->style='width:60px';

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = heading('Repomenu');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	//******************************************************************
	// Edita el Reporte
	//
	function reporte(){
		header('Content-Type: text/html; charset='.$this->config->item('charset'));

		$this->rapyd->load('dataedit');
		$this->rapyd->uri->keep_persistence();
		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$edit = new DataEdit('', 'reportes');
		$id=$edit->_dataobject->pk['nombre'];

		$uri2=anchor_popup('reportes/ver/'.$id, 'Probar reporte', $atts);
		$uri3=anchor_popup('supervisor/mantenimiento/centinelas', 'Centinela', $atts);

		$edit->title(' ');

		$script='
		$("#df1").submit(function(){
			$.post("'.site_url('supervisor/repomenu/gajax_proteo/update/'.$id).'", {nombre: "'.$id.'", proteo: $("#proteo").val()},
				function(data){
					//alert("Reporte guardado" + data);
				}
			);
			return false;
		});

		function fcargar(){
			$.post("'.site_url('supervisor/repomenu/cargar/').'", { nombre:"'.$id.'"},
			function(data){
				if (data){ $("#proteo").val(data); } else { alert("Archivo vacio");}
			});
			return false;
		};

		function fguardar(){
			$.post("'.site_url('supervisor/repomenu/guardar/').'", {nombre: "'.$id.'", proteo: $("#proteo").val()},
			function(data){
				alert(data);
			});
			return false;
		};';

		$edit->script($script,'modify');
		$edit->back_save  = true;
		$edit->back_cancel= true;
		$edit->back_cancel_save=true;
		$edit->back_url = site_url('supervisor/repomenu/filteredgrid');

		$edit->proteo= new textareaField('', 'proteo');
		$edit->proteo->rows =30;
		$edit->proteo->cols =130;
		$edit->proteo->css_class='text-indent:100px;';

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');

		$accion=$this->datasis->jwinopen(site_url('reportes/ver/'.$id."'"));
		$edit->button_status('btn_probar','Probar Reporte',$accion,'TL','modify');

		$accion=$this->datasis->jwinopen(site_url('supervisor/mantenimiento/centinelas'));
		$edit->button_status('btn_centinela','Centinelas',$accion,'TL','modify');
		$edit->button_status('btn_guardar'  ,'Guardar a Archivo'   ,'fguardar()','TL','modify');
		$edit->button_status('btn_cargar'   ,'Cargar desde Archivo','fcargar()' ,'TL','modify');

		$edit->build();

		$this->rapyd->jquery[]='$("#proteo").tabby();';
		$this->rapyd->jquery[]='$("#proteo").linedtextarea();';

		if($this->genesal){
			$data['content'] = $edit->output;
			$data['title']   = heading('Editando Reporte '.$id);
			$data['head']    = $this->rapyd->get_head();
			$data['head']   .= script('plugins/jquery-linedtextarea.js');
			$data['head']   .= script('plugins/jquery.textarea.js');
			$data['head']   .= style('jquery-linedtextarea.css');

			$this->load->view('view_ventanas_sola', $data);
		}else{
			echo $edit->error_string;
		}
	}

	function gajax_proteo(){
		$this->genesal = false;
		$nombre=$this->input->post('nombre');
		$proteo=$this->input->post('proteo');

		if($proteo !== false && $nombre !== false){
			//if(stripos($this->config->item('charset'), 'utf')===false){
			//	$_POST['nombre']=utf8_decode($nombre);
			//	$_POST['proteo']=utf8_decode($proteo);
			//}

			$this->reporte();
		}
	}

	function rtcpdf($status,$nombre){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Editar TCPDF', 'reportes');
		$edit->back_url = site_url('supervisor/repomenu/filteredgrid');

		$edit->tcpdf= new textareaField('', 'tcpdf');
		$edit->tcpdf->rows =30;
		$edit->tcpdf->cols=130;
		$edit->tcpdf->when = array('create','modify');

		$edit->ttcpdf = new freeField('','free',$this->phpCode('<?php '.$edit->_dataobject->get('tcpdf').' ?>'));
		$edit->ttcpdf->when = array('show');

		$edit->buttons('modify','save', 'undo', 'delete', 'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = heading('Reporte TCPDF');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	function rdatasis($status,$nombre){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Reporte DataSIS','reportes');
		$edit->back_url = site_url("supervisor/repomenu/filteredgrid");

		$edit->reporte= new textareaField('', 'reporte');
		$edit->reporte->rows =30;
		$edit->reporte->cols=130;
		$edit->reporte->rule = 'callback_eollw';

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "<h1>Reporte Datasis</h1>";
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	function rharbour($status,$nombre){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Reporte DataSIS', "reportes");
		$edit->back_url = site_url("supervisor/repomenu/filteredgrid");

		$edit->reporte= new textareaField("", "harbour");
		$edit->reporte->rows =30;
		$edit->reporte->cols=130;
		$edit->reporte->rule = 'callback_eollw';

		$edit->buttons('modify','save','undo','delete','back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "<h1>Reporte Harbour</h1>";
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	function cactivo(){
		$codigo=$this->input->post('codigo');
		if(!empty($codigo)){
			$pk=explode('|',$codigo);
			$mSQL="UPDATE intrarepo SET activo=IF(activo='S','N','S') WHERE nombre='$pk[0]' AND modulo='$pk[1]'";
			echo $this->db->simple_query($mSQL);
		}else{
			echo 0;
		}
	}

	function guardar(){
		$rs = false;
		$nombre=$this->input->post('nombre');
		$proteo=$this->input->post('proteo');
		if($proteo !== false && $nombre !== false){
			if(stripos($this->config->item('charset'), 'utf')===false){
				$rs = file_put_contents('formrep/reportes/proteo/'.$nombre.'.rep',$proteo);
			}
		}
		if ($rs)
			echo 'Reporte Guardado';
		else
			echo 'Error al guardar';

	}

	function cargar(){
		$nombre=$this->input->post('nombre');
		if($nombre){
			if(file_exists('formrep/reportes/proteo/'.$nombre.'.rep')){
				$leer = file_get_contents('formrep/reportes/proteo/'.$nombre.'.rep');
				if($leer) echo $leer;
			}elseif(file_exists('formrep/reportes/proteo/'.$nombre.'.REP') ){
				$leer = file_get_contents('formrep/reportes/proteo/'.$nombre.'.REP');
				if($leer) echo $leer;
			}
		}
	}



	function _post_insert($do){
		$nombre=$do->get('nombre');
		$mSQL="INSERT IGNORE INTO `reportes` (nombre) VALUES ('$nombre')";
		$this->db->simple_query($mSQL);
		logusu('REPOMENU',"CREADO EL REPORTE $nombre");
	}

	function _post_delete($do){
		$nombre=$do->get('nombre');
		$mSQL="DELETE FROM `reportes` WHERE `nombre`='$nombre'";
		$this->db->simple_query($mSQL);
		logusu('REPOMENU',"BORRADO EL REPORTE $nombre");
	}

	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `intrarepo` (
		  `nombre` varchar(71) NOT NULL default '',
		  `modulo` varchar(10) NOT NULL default '',
		  `titulo` varchar(20) default NULL,
		  `mensaje` varchar(60) default NULL,
		  `activo` char(1) default 'S',
		  PRIMARY KEY  (`nombre`,`modulo`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		$this->db->simple_query($mSQL);
	}
}
