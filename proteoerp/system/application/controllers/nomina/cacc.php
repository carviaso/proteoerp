<?php
class Cacc extends Controller {
	var $mModulo = 'CACC';
	var $titp    = 'Modulo de Accesos';
	var $tits    = 'Modulo de Accesos';
	var $url     = 'nomina/cacc/';

	function Cacc(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'CACC', $ventana=0 );
	}

	function index(){
		$this->instalar();
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	// Layout en la Ventana
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('CACC', 'JQ');
		$param['otros']       = $this->datasis->otros('CACC', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['script']      = script('plugins/jquery.maskedinput.min.js');
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	// Funciones de los Botones
	//
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function caccadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function caccedit(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function caccshow(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/show').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function caccdel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						try{
							var json = JSON.parse(data);
							if (json.status == "A"){
								apprise("Registro eliminado");
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
							}else{
								apprise("Registro no se puede eliminado");
							}
						}catch(e){
							$("#fborra").html(data);
							$("#fborra").dialog( "open" );
						}
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';
		//Wraper de javascript
		$bodyscript .= '
		$(function(){
			$("#dialog:ui-dialog").dialog( "destroy" );
			var mId = 0;
			var montotal = 0;
			var ffecha = $("#ffecha");
			var grid = jQuery("#newapi'.$grid0.'");
			var s;
			var allFields = $( [] ).add( ffecha );
			var tips = $( ".validateTips" );
			s = grid.getGridParam(\'selarrrow\');
			';

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 300, width: 500, modal: true,
			buttons: {
				"Guardar": function() {
					var bValid = true;
					var murl = $("#df1").attr("action");
					allFields.removeClass( "ui-state-error" );
					$.ajax({
						type: "POST", dataType: "html", async: false,
						url: murl,
						data: $("#df1").serialize(),
						success: function(r,s,x){
							try{
								var json = JSON.parse(r);
								if (json.status == "A"){
									apprise("Registro Guardado");
									$( "#fedita" ).dialog( "close" );
									grid.trigger("reloadGrid");
									return true;
								} else {
									apprise(json.mensaje);
								}
							}catch(e){
								$("#fedita").html(r);
							}
						}
					})
				},
				"Cancelar": function() {
					$("#fedita").html("");
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				$("#fedita").html("");
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});';

		$bodyscript .= '
		$("#fshow").dialog({
			autoOpen: false, height: 300, width: 500, modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fshow").html("");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				$("#fshow").html("");
			}
		});';

		$bodyscript .= '
		$("#fborra").dialog({
			autoOpen: false, height: 300, width: 400, modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fborra").html("");
					jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
				$("#fborra").html("");
			}
		});';

		$bodyscript .= '});';
		$bodyscript .= '</script>';
		return $bodyscript;
	}

	//******************************************************************
	// Definicion del Grid o Tabla
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;


		$grid->addField('id');
		$grid->label('Foto');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'false',
			'width'         => 100,
			'fixed'         => 'true',
			'formatter'     => 'function (cellvalue){ return "<img width=\'100\' border=\'0\' src=\''.site_url($this->url.'foto').'/"+cellvalue+"/"+cellvalue+".jpg\' alt=\'Entrada del trabajador\' />"; }'
		));

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('nacional');
		$grid->label('Nacional');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('cedula');
		$grid->label('C&eacute;dula');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));


		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('manual');
		$grid->label('Manual');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow(',ondblClickRow: function(id){ caccshow(); return; }');
		$grid->setAdd(    $this->datasis->sidapuede('CACC','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('CACC','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('CACC','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('CACC','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: caccadd, editfunc: caccedit, delfunc: caccdel, viewfunc: caccshow');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdata/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('cacc');

		$response   = $grid->getData('cacc', array(array()), array(), false, $mWHERE , 'id', 'desc');
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion del Grid o Tabla
	//
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			echo 'Deshabilitado';
		}elseif($oper == 'edit') {
			echo 'Deshabilitado';
		}elseif($oper == 'del'){
			echo 'Deshabilitado';
		}
	}

	//******************************************************************
	// Edicion

	function dataedit(){
		$this->rapyd->load('dataedit','dataobject');

		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
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

									$("#nacional").val("");
									$("#nacional_val").text("");

									$("#cedula").val("");
									$("#cedula_val").text("");

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

					$("#nacional").val(ui.item.nacional);
					$("#nacional_val").text(ui.item.nacional);

					$("#cedula").val(ui.item.cedula);
					$("#cedula_val").text(ui.item.cedula);

					setTimeout(function() {  $("#codigo").removeAttr("readonly"); }, 1500);
				}
			});
		});';


		$do = new DataObject('cacc');
		$do->pointer('pers' ,'cacc.codigo=pers.codigo','pers.nombre AS persnombre,pers.nacional AS persnacional,pers.cedula AS perscedula','left');

		$edit = new DataEdit('', $do);
		$edit->script($script,'create');
		$edit->script($script,'modify');
		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );

		$edit->codigo = new inputField('C&oacute;digo del trabajador', 'codigo');
		$edit->codigo->rule='trim';
		$edit->codigo->mode='autohide';
		$edit->codigo->maxlength = 15;
		$edit->codigo->size = 15;
		$edit->codigo->rule = 'required|existepers|callback_chexiste';

		$edit->persnombre = new inputField('Nombre', 'nombre');
		$edit->persnombre->pointer='true';
		$edit->persnombre->db_name = 'persnombre';
		$edit->persnombre->type = 'inputhidden';

		$edit->persnacional = new inputField('C&eacute;dula', 'nacional');
		$edit->persnacional->pointer = 'true';
		$edit->persnacional->db_name = 'persnacional';
		$edit->persnacional->type    = 'inputhidden';

		$edit->perscedula = new inputField('', 'cedula');
		$edit->perscedula->pointer = 'true';
		$edit->perscedula->db_name = 'perscedula';
		$edit->perscedula->type    = 'inputhidden';
		$edit->perscedula->in      = 'persnacional';

		$edit->fecha    = new DateonlyField('Fecha','fecha');
		$edit->fecha->size =12;
		$edit->fecha->rule ='required';
		$edit->fecha->insertValue=date('Y-m-d');

		$edit->hora  = new inputField('Hora', 'hora');
		$edit->hora->maxlength=8;
		$edit->hora->size=10;
		$edit->hora->rule='required|callback_chhora';
		$edit->hora->insertValue=date('H:i:s');
		$edit->hora->append('hh:mm:ss');
		$edit->hora->style = 'font-size: 2.5em;font-weight:bold;';

		$id=$edit->getval('id');
		if($id!==false){
			$id = intval($id);
			$furl=site_url($this->url.'foto/'.$id.'/'.$id.'.jpg');
			$edit->foto = new  containerField('Foto',"<p style='text-align:center'><img border='0' src='${furl}' alt='Entrada del trabajador' /></p>");
			$edit->foto->when=array('show');
		}

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			echo $edit->output;
		}
	}

	function _pre_inserup($do){
		$do->set('manual','S');
		$codigo  = $do->get('codigo');
		$dbcodigo= $this->db->escape($codigo);
		$row=$this->datasis->damerow('SELECT nacional, cedula FROM pers WHERE codigo='.$dbcodigo);
		if(!empty($row)){
			$do->set('cedula'  ,$row['cedula']  );
			$do->set('nacional',$row['nacional']);
		}
	}

	function _pre_insert($do){
		$this->_pre_inserup($do);
		$do->error_message_ar['pre_ins']='';
		return true;
	}

	function _pre_update($do){
		$this->_pre_inserup($do);
		$do->error_message_ar['pre_upd']='';
		return true;
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='';
		return false;
	}

	function _post_insert($do){
		$codigo=$do->get('codigo');
		logusu('cacc',"ACCESO PARA ${codigo} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('codigo');
		logusu('cacc',"ACCESO PARA ${codigo} MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('codigo');
		logusu('cacc',"ACCESO PARA ${codigo} ELIMINADO");
	}

	function chexiste($codigo){
		$codigo= $this->input->post('codigo');
		$fecha = human_to_dbdate($this->input->post('fecha'));
		$hora  = $this->input->post('hora');
		$dbcodigo= $this->db->escape($codigo);
		$dbfecha = $this->db->escape($fecha );
		$dbhora  = $this->db->escape($hora  );

		$chek=$this->datasis->dameval("SELECT COUNT(*) AS cana FROM cacc WHERE codigo=${dbcodigo} AND fecha=${dbfecha} AND hora=${dbhora}");
		if($chek > 0){
			$nombre=$this->datasis->dameval("SELECT cedula FROM cacc WHERE codigo=${dbcodigo} AND fecha=${dbfecha} AND hora=${dbhora}");
			$this->validation->set_message('chexiste',"Acceso para $nombre CODIGO ${codigo} FECHA ${fecha} HORA ${hora} ya existe");
			return false;
		}else {
			return true;
		}
	}

	function foto($id){
		$dbid = intval($id);
		$arch = $this->datasis->dameval("SELECT CONCAT(TRIM(cacc.codigo),DATE_FORMAT(fecha,'-%Y%m%d'),DATE_FORMAT(cacc.hora,'%H%i%s'),'.jpg') AS archivo FROM cacc WHERE id=${dbid}");
		$imag = 'uploads/fnomina/'.$arch;

		if(file_exists($imag)){
			Header("Content-type: image/jpeg");
			Header("Pragma: No-cache");
			readfile($imag);
		}

		Header("Content-type: image/gif");
		Header("Pragma: No-cache");
		$dir=dirname($_SERVER["SCRIPT_FILENAME"]);
		readfile("$dir/images/ndisp.gif");
	}

	function instalar(){
		if (!$this->db->table_exists('cacc')) {
			$mSQL="CREATE TABLE `cacc` (
			  `codigo` varchar(15) NOT NULL DEFAULT '0',
			  `nacional` char(1) DEFAULT '0',
			  `cedula` varchar(10) DEFAULT NULL,
			  `fecha` date NOT NULL DEFAULT '0000-00-00',
			  `hora` time NOT NULL DEFAULT '00:00:00',
			  `manual` char(1) NOT NULL DEFAULT 'N',
			  PRIMARY KEY (`codigo`,`fecha`,`hora`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('cacc');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE `cacc` DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE `cacc` ADD UNIQUE INDEX `unico` (`codigo`, `fecha`, `hora`)');
			$this->db->simple_query('ALTER TABLE `cacc` ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}
	}
}
