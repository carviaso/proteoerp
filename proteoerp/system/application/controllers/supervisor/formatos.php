<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/validaciones.php');
class formatos extends validaciones {
	var $genesal=true;

	function formatos(){
		parent::Controller();
		$this->load->library('rapyd');
		//$this->datasis->modulo_id(307,1);
	}

	function index(){
		redirect('supervisor/formatos/filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter('Filtro por Menu de Formatos','formatos');

		$filter->nombre = new inputField('Nombre', 'nombre');
		$filter->nombre->db_name='nombre';
		$filter->nombre->size=15;
		$filter->nombre->group = 'UNO';

		$filter->proteo = new inputField('Proteo','proteo');
		$filter->proteo->size=30;
		$filter->proteo->db_name='proteo';
		$filter->proteo->group = 'UNO';

		$filter->reporte = new inputField('Datasis','forma');
		$filter->reporte->size=30;
		$filter->reporte->db_name='forma';
		$filter->reporte->group = 'UNO';

		$filter->harbour = new inputField('Harbour','harbour');
		$filter->harbour->size=30;
		$filter->harbour->db_name='harbour';
		$filter->harbour->group = 'DOS';

		$filter->tcpdf = new inputField('TCPDF','tcpdf');
		$filter->tcpdf->size=30;
		$filter->tcpdf->db_name='tcpdf';
		$filter->tcpdf->group = 'DOS';

		$filter->buttons('reset','search');
		$filter->build('dataformfiltro');

		$uri  = anchor('supervisor/formatos/dataedit/show/<#nombre#>'   ,'<#nombre#>');
		$uri1 = anchor('supervisor/formatos/reporte/modify/<#nombre#>/' ,'Editar');
		$uri2 = anchor('supervisor/formatos/rdatasis/modify/<#nombre#>/','Editar');
		$uri3 = anchor('supervisor/formatos/rharbour/modify/<#nombre#>/','Editar');
		$uri4 = anchor('supervisor/formatos/observa/modify/<#nombre#>/' ,'Editar');
		$uri5 = anchor('supervisor/formatos/rtcpdf/modify/<#nombre#>/'  ,'Editar');
		$uri6 = anchor('supervisor/formatos/rtcpdf2/modify/<#nombre#>/' ,'Editar');
		$uri7 = anchor('supervisor/formatos/txt/modify/<#nombre#>/'     ,'Editar');

		$grid = new DataGrid('Lista de Menu de Formatos');
		$grid->order_by('nombre','asc');
		$grid->per_page = 15;

		$grid->column('Nombre',    $uri);
		$grid->column('Proteo'   ,$uri1);
		$grid->column('DataSIS'  ,$uri2);
		$grid->column('Harbour'  ,$uri3);
		$grid->column('TXT'      ,$uri7);
		$grid->column('TCPDF'    ,$uri5);
		$grid->column('TCPDF2'   ,$uri6);

		$grid->add('supervisor/formatos/dataedit/create');
		$grid->build();
		//echo $grid->db->last_query();

		$url=site_url('supervisor/formatos/cactivo');
		$data['script']='<script type="text/javascript">
		$(document).ready(function() {
			$("form :checkbox").click(function () {
				$.ajax({
					type: "POST",
					url: "'.$url.'",
					data: "codigo="+this.name,
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
		$data['title']   = heading('Menu de Formatos');
		$data['head']    = script('jquery.pack.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function observa($nombre){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Agregar Observaci&oacute;n','formatos');
		$edit->back_url = site_url('supervisor/formatos/filteredgrid');

		$edit->observa= new textareaField('', 'observa');
		$edit->observa->rows =3;
		$edit->observa->cols=70;

		$edit->buttons('modify', 'save', 'undo','back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = heading('Observaciones');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function reporte(){
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Proteo', 'formatos');
		$id=$edit->_dataobject->pk['nombre'];
		$script='
		$("#df1").submit(function(){
			$.post("'.site_url('supervisor/formatos/gajax_proteo/update/'.$id).'", {nombre: "'.$id.'", proteo: $("#proteo").val()},
			function(data){
				alert("Formato guardado" + data);
			},
			"application/x-www-form-urlencoded;charset='.$this->config->item('charset').'");
			return false;
		});

		function guarda(){
			$("#proteo").val(editor.getValue());
			$.post("'.site_url('supervisor/formatos/gajax_proteo/update/'.$id).'", {nombre: "'.$id.'", proteo: $("#proteo").val()},
			function(data){
				alert("Formato guardado" + data);
			},
			"application/x-www-form-urlencoded;charset='.$this->config->item('charset').'");
			return false;
		};

		function fcargar(){
			$.post("'.site_url('supervisor/formatos/cargar/').'", { nombre:"'.$id.'"},
			function(data){
				if (data){ $("#proteo").val(editor.setValue(data)); } else { alert("Archivo vacio");}
			});
			return false;
		};

		function fguardar(){
			$("#proteo").val(editor.getValue());
			$.post("'.site_url('supervisor/formatos/guardar/').'", {nombre: "'.$id.'", proteo: $("#proteo").val()},
			function(data){
				alert(data);
			});
			return false;
		};';

		$edit->script($script,'modify');
		$edit->back_save  =true;
		$edit->back_cancel=true;
		$edit->back_cancel_save=true;
		$edit->back_url = site_url('supervisor/formatos/filteredgrid');

		//$edit->proteo= new htmlField('', 'proteo');
		$edit->proteo= new textareaField('', 'proteo');
		$edit->proteo->rows =30;
		$edit->proteo->cols=130;
		//$edit->proteo->css_class='codepress php linenumbers-on readonly-off';

		//$edit->buttons('modify', 'save', 'undo','back');
		$edit->build();

		//$this->rapyd->jquery[]='$("#proteo").tabby();';
		//$this->rapyd->jquery[]='$("#proteo").linedtextarea();';
		//$this->rapyd->jquery[]='estilo=$("#proteo").attr("style"); $("#proteo").attr("style",estilo+"-moz-tab-size:2 !important; tab-size:2 !important;")';

		if($this->genesal){
			$data['content'] = $edit->output;
			$data['title']   = "$id";
			$data['head']    = $this->rapyd->get_head();
			//$data['head']   .= script('plugins/jquery-linedtextarea.js').script('plugins/jquery.textarea.js').style('jquery-linedtextarea.css');

			$this->load->view('editform', $data);
			//$this->load->view('view_ventanas_sola', $data);
		}else{
			echo $edit->error_string;
		}
	}

	function guardar(){
		$rs = false;
		$nombre=$this->input->post('nombre');
		$proteo=$this->input->post('proteo');
		if($proteo !== false && $nombre !== false){
			if(stripos($this->config->item('charset'), 'UTF')===false){
				$rs = file_put_contents('formrep/formatos/proteo/'.trim($nombre).'.for', $proteo);
			}
		}
		if ($rs)
			echo 'Reporte Guardado';
		else
			echo 'Error al guardar "formrep/formatos/proteo/'.trim($nombre).'.for"';

	}

	function cargar(){
		$nombre=$this->input->post('nombre');
		if($nombre){
			if(file_exists('formrep/formatos/proteo/'.$nombre.'.for')){
				$leer = file_get_contents('formrep/formatos/proteo/'.$nombre.'.for');
				if($leer) echo $leer;
			}elseif(file_exists('formrep/formatos/proteo/'.$nombre.'.FOR') ){
				$leer = file_get_contents('formrep/formatos/proteo/'.$nombre.'.FOR');
				if($leer) echo $leer;
			}
		}
	}



	function txt(){
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Reporte TXT', 'formatos');
		$id=$edit->_dataobject->pk['nombre'];
		$script='$("#df1").submit(function(){
		$.post("'.site_url('supervisor/formatos/gajax_txt/update/'.$id).'", {nombre: "'.$id.'", txt: $("#txt").val()},
			function(data){
				alert("Reporte guardado" + data);
			},
			"application/x-www-form-urlencoded;charset='.$this->config->item('charset').'");
			return false;
		});';

		$edit->script($script,'modify');
		$edit->back_save  =true;
		$edit->back_cancel=true;
		$edit->back_cancel_save=true;
		$edit->back_url = site_url('supervisor/formatos/filteredgrid');

		$edit->txt= new htmlField(' ', 'txt');
		$edit->txt->rows =30;
		$edit->txt->cols=130;

		$edit->buttons('modify', 'save', 'undo','back');
		$edit->build();

		$this->rapyd->jquery[]='$("#txt").tabby();';
		$this->rapyd->jquery[]='$("#txt").linedtextarea();';
		$this->rapyd->jquery[]='estilo=$("#txt").attr("style"); $("#txt").attr("style",estilo+"-moz-tab-size:2 !important; tab-size:2 !important;")';

		if($this->genesal){
			$data['content'] = $edit->output;
			$data['title']   = "<h1>Formato '$id'</h1>";
			$data['head']    = $this->rapyd->get_head();
			$data['head']   .= script('plugins/jquery-linedtextarea.js').script('plugins/jquery.textarea.js').style('jquery-linedtextarea.css');

			$this->load->view('view_ventanas_sola', $data);
		}else{
			echo $edit->error_string;
		}
	}

	function gajax_txt(){
		header('Content-Type: text/html; '.$this->config->item('charset'));
		$this->genesal=false;
		$nombre=$this->input->post('nombre');
		$proteo=$this->input->post('txt');

		if($proteo!==false and $nombre!==false){
			if(stripos($this->config->item('charset'), 'utf')===false){
				$_POST['nombre']=utf8_decode($nombre);
				$_POST['txt']=utf8_decode($proteo);
			}
			$this->txt();
		}
	}

	function gajax_proteo(){
		header('Content-Type: text/html; '.$this->config->item('charset'));
		$this->genesal=false;
		$nombre=$this->input->post('nombre');
		$proteo=$this->input->post('proteo');

		if($proteo!==false and $nombre!==false){
			if(stripos($this->config->item('charset'), 'utf')===false){
				$_POST['nombre']=utf8_decode($nombre);
				$_POST['proteo']=utf8_decode($proteo);
			}
			$this->reporte();
		}
	}

	function rtcpdf(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Editar TCPDF', 'formatos');
		$id=$edit->_dataobject->pk['nombre'];
		$script='$("#df1").submit(function(){
		$.post("'.site_url('supervisor/formatos/gajax_rtcpdf/update/'.$id).'", {nombre: "'.$id.'", tcpdf: $("#tcpdf").val()},
			function(data){
				alert("Reporte guardado" + data);
			},
			"application/x-www-form-urlencoded;charset='.$this->config->item('charset').'");
			return false;
		});';

		$edit->script($script,'modify');
		$edit->back_save  =true;
		$edit->back_cancel=true;
		$edit->back_cancel_save=true;
		$edit->back_url = site_url('supervisor/formatos/filteredgrid');

		$edit->tcpdf= new textareaField('', 'tcpdf');
		$edit->tcpdf->rows =30;
		$edit->tcpdf->cols=130;
		//$edit->tcpdf->css_class='codepress php linenumbers-on readonly-off';
		$edit->tcpdf->when = array('create','modify');

		$edit->ttcpdf = new freeField('','free',$this->phpCode('<?php '.$edit->_dataobject->get('tcpdf').' ?>'));
		$edit->ttcpdf->when = array('show');

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$this->rapyd->jquery[]='$("#tcpdf").tabby();';
		$this->rapyd->jquery[]='$("#tcpdf").linedtextarea();';

		if($this->genesal){
			$data['content'] = $edit->output;
			$data['title']   = '<h1>Reporte TCPDF</h1>';
			$data['head']    = $this->rapyd->get_head();
			$data['head']   .= script('plugins/jquery-linedtextarea.js').script('plugins/jquery.textarea.js').style('jquery-linedtextarea.css');
			//$data['head']   .= script('codepress/codepress.js');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			echo $edit->error_string;
		}
	}

	function gajax_rtcpdf(){
		header('Content-Type: text/html; '.$this->config->item('charset'));
		$this->genesal=false;
		$nombre=$this->input->post('nombre');
		$tcpdf=$this->input->post('tcpdf');

		if($tcpdf!==false and $nombre!==false){
			if(stripos($this->config->item('charset'), 'utf')===false){
				$_POST['nombre']=utf8_decode($nombre);
				$_POST['tcpdf']=utf8_decode($tcpdf);
			}
			$this->rtcpdf();
		}
	}

	function rdatasis(){
		$nombre=$this->uri->segment(5);
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('DataSIS', 'formatos');
		$edit->back_url = site_url('supervisor/formatos/filteredgrid');

		$edit->reporte= new textareaField('', 'forma');
		$edit->reporte->rows =30;
		$edit->reporte->cols=130;
		$edit->reporte->rule = 'callback_eollw';

		$edit->buttons('modify', 'save', 'undo','back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "<h1>Formato '$nombre'</h1>";
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	function rharbour(){
		$nombre=$this->uri->segment(5);
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Harbour', 'formatos');
		$edit->back_url = site_url('supervisor/formatos/filteredgrid');

		$edit->reporte= new textareaField('', 'harbour');
		$edit->reporte->rows =30;
		$edit->reporte->cols=130;
		$edit->reporte->rule = 'callback_eollw';

		$edit->buttons('modify','save','undo','back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "<h1>Formato '$nombre'</h1>";
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Formatos','formatos');
		$edit->back_url = site_url("supervisor/formatos/filteredgrid");

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule= 'strtoupper|required';
		$edit->nombre->size = 20;

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = heading('Agregar Formatos');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}


	function cactivo(){
		$codigo=$this->input->post('codigo');
		if(!empty($codigo)){
			$pk=explode('|',$codigo);
			$dbpk1=$this->db->escape($pk[0]);
			$dbpk2=$this->db->escape($pk[1]);
			$mSQL="UPDATE intrarepo SET activo=IF(activo='S','N','S') WHERE nombre=$dbpk1 AND modulo=$dbpk2";
			echo $this->db->simple_query($mSQL);
		}else{
			echo 0;
		}
	}

	function rtcpdf2(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Editar TCPDF', 'formatos');
		$id=$edit->_dataobject->pk['nombre'];
		$script='$("#df1").submit(function(){
		$.post("'.site_url('supervisor/formatos/gajax_rtcpdf2/update/'.$id).'", {nombre: "'.$id.'", tcpdf2: $("#tcpdf2").val()},
			function(data){
				alert("Reporte guardado" + data);
			},
			"application/x-www-form-urlencoded;charset='.$this->config->item('charset').'");
			return false;
		});';

		$edit->script($script,'modify');
		$edit->back_save  =true;
		$edit->back_cancel=true;
		$edit->back_cancel_save=true;
		$edit->back_url = site_url('supervisor/formatos/filteredgrid');

		$edit->tcpdf2= new textareaField('', 'tcpdf2');
		$edit->tcpdf2->rows =30;
		$edit->tcpdf2->cols=130;
		//$edit->tcpdf2->css_class='codepress php linenumbers-on readonly-off';
		$edit->tcpdf2->when = array('create','modify');

		$edit->ttcpdf = new freeField('','free',$this->phpCode('<?php '.$edit->_dataobject->get('tcpdf2').' ?>'));
		$edit->ttcpdf->when = array('show');

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$this->rapyd->jquery[]='$("#tcpdf2").tabby();';
		$this->rapyd->jquery[]='$("#tcpdf2").linedtextarea();';

		if($this->genesal){
			$data['content'] = $edit->output;
			$data['title']   = heading('Reporte TCPDF');
			$data['head']    = $this->rapyd->get_head();
			$data['head']   .= script('plugins/jquery-linedtextarea.js').script('plugins/jquery.textarea.js').style('jquery-linedtextarea.css');
			//$data['head']   .= script('codepress/codepress.js');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			echo $edit->error_string;
		}
	}

	function gajax_rtcpdf2(){
		header('Content-Type: text/html; '.$this->config->item('charset'));
		$this->genesal=false;
		$nombre=$this->input->post('nombre');
		$tcpdf=$this->input->post('tcpdf2');

		if($tcpdf!==false and $nombre!==false){
			if(stripos($this->config->item('charset'), 'utf')===false){
				$_POST['nombre'] = utf8_decode($nombre);
				$_POST['tcpdf']  = utf8_decode($tcpdf);
			}
			$this->rtcpdf2();
		}
	}

	function _post_insert($do){
		$nombre  =$do->get('nombre');
		$dbnombre=$this->db->escape($nombre);
		$mSQL="INSERT IGNORE INTO `reportes` (nombre) VALUES ($dbnombre)";
		$this->db->simple_query($mSQL);
		logusu('formatos',"CREADO EL REPORTE $nombre");
	}

	function _post_delete($do){
		$nombre=$do->get('nombre');
		$mSQL="DELETE FROM `reportes` WHERE `nombre`='$nombre'";
		$this->db->simple_query($mSQL);
		logusu('formatos',"BORRADO EL REPORTE $nombre");
	}


	function puertos(){
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load('datafilter','datagrid');

		$sel=array('nombre', "SUBSTRING_INDEX(forma,'\r',1) AS puerto");
		$filter = new DataFilter('Filtro por Menu de Formatos');
		$filter->db->from('formatos');
		$filter->db->where('LENGTH(forma) > 1');
		$filter->db->select($sel);

		$filter->nombre = new inputField('Nombre', 'nombre');
		$filter->nombre->db_name='nombre';
		$filter->nombre->size=15;
		$filter->nombre->group = 'UNO';

		$filter->buttons('reset','search');
		$filter->build('dataformfiltro');

		$aropt = array();
		$opts  = array('LPT1','LPT2','LPT3','COM1','COM2','COM3','SPOOL','ARCHIVO');
		foreach($opts as $opt){
			$aropt[]= anchor('supervisor/formatos/cambiapuerto/<#nombre#>/'.$opt,$opt);
		}

		$uri = implode(',',$aropt);

		$grid = new DataGrid('Lista de Menu de Formatos');
		$grid->order_by('nombre','asc');
		$grid->per_page = 30;

		$grid->column('Nombre del reporte', 'nombre');
		$grid->column('Puerto Actual', 'puerto');
		$grid->column('Acci&oacute;n', $uri);

		$grid->build();
		//echo $grid->db->last_query();

		$url=site_url('supervisor/formatos/cactivo');
		/*$data['script']='<script type="text/javascript">
		$(document).ready(function() {
			$("form :checkbox").click(function () {
				$.ajax({
					type: "POST",
					url: "'.$url.'",
					data: "codigo="+this.name,
					success: function(msg){
					if (msg==0)
						alert("Ocurrio un problema");
					}
				});
		}).change();
		});
		</script>';*/
		$data['content'] = '<form>'.$grid->output.'</form>';
		$data['filtro']  = $filter->output;
		$data['title']   = heading('Menu de Formatos');
		$data['head']    = script('jquery.pack.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function cambiapuerto($nombre,$tipo){
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence('supervisor/formatos/puertos', $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ?$persistence['back_uri'] : 'supervisor/formatos/puertos';

		if(preg_match('/^(LPT)|(COM)[0-9]+$/', $tipo) > 0){
			$val = $tipo;
		}elseif($tipo=='SPOOL'){
			$val = "C:\\SPOOL\\${nombre}.TXT";
		}elseif($tipo=='ARCHIVO'){
			$val = "${nombre}.TXT";
		}else{
			redirect($back);
		}

		$dbnombre = $this->db->escape($nombre);
		$formato  = $this->datasis->dameval("SELECT forma FROM formatos WHERE nombre=$dbnombre");
		if(empty($formato)){
			redirect($back);
		}

		$pos      = strpos($formato, "\r");
		$anteri = substr($formato,0,$pos);
		//Valida que esta cambiando un puerto valido
		if(preg_match('/^[a-zA-Z0-9:\\\_.]+$/',$anteri) > 0){

			$nformato = $val.substr($formato,$pos);
			$data     = array('forma' => $nformato);
			$where    = "nombre=$dbnombre";
			$mSQL     = $this->db->update_string('formatos', $data, $where);

			$this->db->simple_query($mSQL);
		}

		redirect($back);
	}

	function instalar(){
		if ($this->db->table_exists('intrarepo')){
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
		if ($this->db->field_exists('tcpdf2', 'formatos')) $this->db->simple_query("ALTER TABLE `formatos`  ADD COLUMN `tcpdf2` TEXT NULL");
	}
}
