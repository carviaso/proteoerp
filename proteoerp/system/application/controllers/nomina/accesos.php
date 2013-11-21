<?php require_once(BASEPATH.'application/controllers/validaciones.php');
// pear install Image_Barcode
class Accesos extends validaciones{
	var $_direccion;

	function Accesos(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->db->simple_query('UPDATE pers SET codigo=TRIM(codigo)');
		$this->instalar();
	}

	function index(){
		$this->datasis->modulo_id(709,1);
		redirect('nomina/accesos/filteredgrid');
	}

	function filteredgrid(){
		$protocolo=explode('/',$_SERVER['SERVER_PROTOCOL']);
		$this->_direccion=$protocolo[0].'://'.$_SERVER['SERVER_NAME'].'/fnomina';

		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();
		$atts = array(
		    'width'      => '400',
		    'height'     => '320',
		    'scrollbars' => 'yes',
		    'status'     => 'yes',
		    'resizable'  => 'yes',
		    'screenx'    => '10',
		    'screeny'    => '10'
		);

		$script='$(".inputnum").numeric(".");';

		$filter = new DataFilter("Filtro por C&oacute;digo");

		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->db_name='cacc.codigo';
		$filter->codigo->size = 15;

		$filter->cedula = new inputField("C&eacute;dula","cedula");
		$filter->cedula->db_name='pers.cedula';
		$filter->cedula->size = 15;

		$filter->nombre = new inputField("Nombres","nombre");
		$filter->nombre->db_name='pers.nombre';
		$filter->nombre->size = 25;

		$filter->fechad = new dateonlyField("FECHA Desde-Hasta", "fechad",'d/m/Y');
		$filter->fechad->clause  ="where";
		$filter->fechad->db_name ="fecha";
		$filter->fechad->operator=">=";
		$filter->fechad->dbformat='Ymd';
		$filter->fechad->size    = 15;

		$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechah->clause="where";
		$filter->fechah->db_name="fecha";
		$filter->fechah->operator="<=";
		$filter->fechah->group = "FECHA";
		$filter->fechah->dbformat='Ymd';
		$filter->fechah->size    = 15;
		$filter->fechah->in      = "fechad";

		$filter->horad = new inputField("HORA Desde - Hasta", "horad");
		$filter->horad->clause   = "where";
		$filter->horad->db_name  = "CONCAT(MID(cacc.hora,1,2),MID(cacc.hora,4,2))";
		$filter->horad->operator = ">=";
		//$filter->horad->group    = "HORA";
		$filter->horad->css_class='inputnum';
		$filter->horad->size     = 3;
		$filter->horad->maxlength= 4;

		$filter->horah = new inputField("Hasta", "horahd");
		$filter->horah->clause   = "where";
		$filter->horah->db_name  = "CONCAT(MID(cacc.hora,1,2),MID(cacc.hora,4,2))";
		$filter->horah->operator = "<=";
		$filter->horah->group    = "HORA";
		$filter->horah->css_class='inputnum';
		$filter->horah->size     = 3;
		$filter->horah->maxlength= 4;
		$filter->horah->in       = "horad";
		$filter->horah->append('Inserte la hora en formato HH  MM (18 00)');

		$filter->buttons('reset','search');
		$filter->build();

		//$ima=$this->_direccion.'/<#archivo#>';
		$ima=base_url().'uploads/fnomina/<#archivo#>';

		$furi = site_url('/nomina/accesos/foto/<#archivo#>');
		$uri  = anchor('nomina/accesos/dataedit/show/<#codigo#>/<#fecha#>/<#hora#>','<#codigo#>');
		$grid = new DataGrid('Lista de Control de Accesos');

		$select=array("cacc.codigo","fecha","cacc.hora","cacc.cedula",
		"CONCAT(TRIM(cacc.codigo),DATE_FORMAT(fecha,'-%Y%m%d'),DATE_FORMAT(cacc.hora,'%H%i%s'),'.jpg') AS archivo",
		"CONCAT_WS('-',pers.nacional,pers.cedula) AS ci, nombre, apellido");
		$grid->db->select($select);
		$grid->db->from('cacc');
		$grid->db->join('pers','cacc.codigo = pers.codigo');

		$grid->db->orderby('fecha','desc');
		$grid->db->orderby('hora','desc');

		$grid->per_page = 7;
		$grid->column_orderby('C&oacute;digo',$uri      ,'codigo'  );
		$grid->column_orderby('C&eacute;dula','cedula'  ,'cedula'  );
		$grid->column_orderby('Nombres'      ,'nombre'  ,'nombre'  );
		$grid->column_orderby('Apellidos'    ,'apellido','apellido');
		$grid->column_orderby('Fecha','<dbdate_to_human><#fecha#></dbdate_to_human>','fecha','align="center"');
		$grid->column_orderby('Hora','hora','align="center"');
		$grid->column('Foto',anchor_popup('uploads/fnomina/<#archivo#>',"<img src='$ima' width='100' border='0'/>",$atts),'align="center"');
		$grid->add('nomina/accesos/dataedit/create');
		$grid->build();

		//echo $grid->db->last_query();
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = heading('Control de Accesos');
		$data['head']    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataedit','dataobject');

		$script= '
		$(function() {
			//$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			//$(".inputnum").numeric(".");
			$("#hora").mask("99:99:99");

			$("#codigo").autocomplete({
				delay: 600,
				autoFocus: true,
				source: function(req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscapers').'",
						type: "POST",
						dataType: "json",
						data: {"q":req.term},
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#nombre").val("");
									$("#nombre_val").text("");
								}else{
									$.each(data,
										function(i, val){
											sugiere.push( val );
										}
									);
								}
								add(sugiere);
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$("#codigo").attr("readonly", "readonly");

					$("#nombre").val(ui.item.nombre);
					$("#nombre_val").text(ui.item.nombre);

					setTimeout(function() {  $("#codigo").removeAttr("readonly"); }, 1500);
				}
			});
		});';

		$do = new DataObject('cacc');
		$do->pointer('pers' ,'cacc.codigo=pers.codigo','pers.nombre AS persnombre','left');

		$edit = new DataEdit('Accesos', 'cacc');
		$edit->script($script, 'create');
		$edit->script($script, 'modify');
		$edit->back_url = site_url('nomina/accesos/filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->post_process('insert','_pre_insert');
		$edit->post_process('update','_pre_update');

		$pers=array(
			'tabla'   =>'pers',
			'columnas'=>array(
				'codigo'  =>'Codigo',
				'cedula'  =>'Cedula',
				'nombre'  =>'Nombre',
				'apellido' =>'Apellido'
			),
			'filtro'  =>array('codigo'=>'C&oacute;digo','cedula'=>'Cedula'),
			'retornar'=>array('codigo'=>'codigo'),
			'titulo'  =>'Buscar Personal'
		);

		$boton=$this->datasis->modbus($pers);

		$edit->codigo = new inputField('C&oacute;digo del trabajador', 'codigo');
		$edit->codigo->rule='trim';
		$edit->codigo->mode='autohide';
		$edit->codigo->maxlength = 15;
		$edit->codigo->size = 15;
		$edit->codigo->append($boton);
		$edit->codigo->rule = 'required|callback_chexiste';

		$edit->persnombre = new inputField('Nombre', 'persnombre');
		$edit->persnombre->pointer='true';
		$edit->persnombre->in = 'codigo';
		$edit->persnombre->db_name = 'persnombre';
		$edit->persnombre->type = 'inputhidden';

		/*$edit->nacional = new dropdownField('Nacionalidad', 'nacional');
		$edit->nacional->style = "width:110px;";
		$edit->nacional->option("V","Venezolano");
		$edit->nacional->option("E","Extranjero");*/

		$edit->fecha    = new DateonlyField('Fecha','fecha');
		$edit->fecha->mode='autohide';
		$edit->fecha->size =12;
		$edit->fecha->rule ='required';
		$edit->fecha->insertValue=date('Y-m-d');

		$edit->hora  = new inputField('Hora', 'hora');
		$edit->hora->maxlength=8;
		$edit->hora->size=10;
		$edit->hora->mode='autohide';
		$edit->hora->rule='required|callback_chhora';
		$edit->hora->insertValue=date('H:i:s');
		$edit->hora->append('hh:mm:ss');

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = heading('Control de Accesos');
		$data['head']    = $this->rapyd->get_head();
		$data['head']   .= script('jquery.js');
		$data['head']   .= script('jquery-ui.js');
		$data['head']   .= script('plugins/jquery.maskedinput.min.js');
		$data['head']   .= style('redmond/jquery-ui.css');
		$this->load->view('view_ventanas', $data);
	}

	function cerberus(){
		$this->load->helper('string');
		$this->load->library('path');
		$path=new Path();
		$path->setPath($this->config->item('uploads_dir'));
		$path->append('/fnomina');
		$upload_path =reduce_double_slashes('../'.$path->getPath().'/');

		$query = $this->db->query('SELECT id,sid,ip,usr,pwd FROM cerberus WHERE activo="S"');
		foreach ($query->result() as $row){
			$id =$row->sid;
			$ip =$row->ip;
			$usr=$row->usr;
			$pwd=$row->pwd;

			$link = mysql_connect($ip,$usr,$pwd) or die('Maquina fuera de linea');
			mysql_select_db('datasis') or die('Base de datos no seleccionable');
			$mSQL="SELECT id,codigo, nacional, cedula,fecha,hora,foto FROM cer_cacc WHERE id>$id ORDER BY id";
			$result   = mysql_query($mSQL,$link);
			$num_rows = mysql_num_rows($result);
			if ($num_rows > 0){
				while ($data = mysql_fetch_assoc($result)) {
					$fnombre=$data['codigo'].'-'.preg_replace('/[^0-9]+/','', $data['fecha'].$data['hora']).'.jpg';
					//echo $fnombre.br();
					file_put_contents($upload_path.$fnombre,$data['foto']);
					$sid=$data['id'];
					unset($data['foto']);
					unset($data['id']);
					$sql = $this->db->insert_string('cacc', $data);
					$ban=$this->db->simple_query($sql);
					if(!$ban){ memowrite($sql,'accesos'); }
				}
				$data=array('sid'=>$sid);
				$sql = $this->db->update_string('cerberus', $data,'id='.$row->id);
				$ban=$this->db->simple_query($sql);
				if(!$ban){ memowrite($sql,'accesos'); }
			}
		}
	}

	function lbarras(){
		$sal='';
		$attr = array('style'=> 'margin:7px');
		$query = $this->db->query('SELECT TRIM(carnet) AS carnet, CONCAT_WS(TRIM(nombre),TRIM(apellido)) AS nombre FROM pers WHERE status="A" AND LENGTH(TRIM(carnet))>0');
		foreach ($query->result() as $row){
			$attr['src']='nomina/accesos/barras/'.$row->carnet;
			$attr['alt']=$row->nombre;
			$sal .= ' '.img($attr);
		}
		$data['content'] = $sal;
		$data['title']   = heading('Listado de barras del personal');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	function barras($carnet){
		error_reporting(0);
		require_once 'Image/Barcode.php';
		Image_Barcode::draw($carnet, 'code128', 'png');
	}

	function foto(){
		//$archivo=$this->uri->segment(4);
		//if (isset($archivo) and file_exists("/usr/samba/fnomina/$archivo")){
		//	Header("Content-type: image/jpeg");
		//	Header("Pragma: No-cache");
		//	readfile("/usr/samba/fnomina/$archivo");
		//}
		//Header("Content-type: image/gif");
		//Header("Pragma: No-cache");
		//$dir=dirname($_SERVER["SCRIPT_FILENAME"]);
		//readfile("$dir/images/ndisp.gif");
	}

	function traecedula($codigo){
		$this->db->escape($codigo);
		return $this->datasis->dameval("SELECT cedula FROM pers WHERE codigo=$codigo");
	}

	function traenacional($codigo){
		$this->db->escape($codigo);
		return $this->datasis->dameval("SELECT nacional FROM pers WHERE codigo=$codigo");
	}

	function _pre_insert($do){
		$codigo=$do->get('codigo');
		$do->set('cedula'  ,$this->traecedula($codigo));
		$do->set('nacional',$this->traenacional($codigo));
		$do->set('manual','S');
	}

	function _pre_update($do){
		$codigo=$do->get('codigo');
		$do->set('cedula',$this->traecedula($codigo));
		$do->set('nacional',$this->traenacional($codigo));
	}

	function _post_insert($do){
		$codigo=$do->get('codigo');
		logusu('cacc',"ACCESO PARA $codigo CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('codigo');
		logusu('cacc',"ACCESO PARA $codigo MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('codigo');
		logusu('cacc',"ACCESO PARA $codigo ELIMINADO ");
	}

	function chexiste($codigo){
		$codigo=$this->input->post('codigo');
		$fecha=human_to_dbdate($this->input->post('fecha'));
		$hora=$this->input->post('hora');

		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM cacc WHERE codigo='$codigo' AND fecha='$fecha' AND hora='$hora'");
		if ($chek > 0){
			$nombre=$this->datasis->dameval("SELECT cedula FROM cacc WHERE codigo='$codigo' AND fecha='$fecha' AND hora='$hora'");
			$this->validation->set_message('chexiste',"Acceso para $nombre CODIGO $codigo FECHA $fecha HORA $hora ya existe");
			return false;
		}else {
			return true;
		}
	}

	function instalar(){
		if (!$this->db->field_exists('manual', 'cacc')){
			$query="ALTER TABLE `cacc`  ADD COLUMN `manual` CHAR(1) NOT NULL DEFAULT 'N' AFTER `hora`";
			$this->db->simple_query($query);
		}

		if (!$this->db->table_exists('cerberus')){
			$mSQL="CREATE TABLE `cerberus` (
			`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`ids` INT(10) UNSIGNED NOT NULL DEFAULT '0',
			`ip` VARCHAR(32) NOT NULL COLLATE 'utf8_unicode_ci',
			`usr` VARCHAR(32) NOT NULL COLLATE 'utf8_unicode_ci',
			`pwd` VARCHAR(32) NOT NULL COLLATE 'utf8_unicode_ci',
			`sid` INT(11) UNSIGNED NOT NULL DEFAULT '0',
			`activo` CHAR(1) NOT NULL DEFAULT 'S',
			PRIMARY KEY (`id`)
			)
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			$this->db->simple_query($mSQL);
		}
	}
}
