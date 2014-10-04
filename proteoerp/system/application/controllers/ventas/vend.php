<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/validaciones.php');
class Vend extends Controller {
	var $mModulo = 'VEND';
	var $titp    = 'Vendedores';
	var $tits    = 'Vendedores';
	var $url     = 'ventas/vend/';

	function Vend(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'VEND', $ventana=0 );
	}

	function index(){
		$this->instalar();
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$grid->wbotonadd(array('id'=>'bgrupo',   'img'=>'images/star.png',     'alt' => 'Gestionar grupos', 'tema'=>'anexos', 'label'=>'Grupos'));
		$grid->wbotonadd(array('id'=>'bzona' ,   'img'=>'images/arrow_up.png' ,   'alt' => 'Asignar zona', 'tema'=>'anexos', 'label'=>'Asignar a Zona'));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro'),
			array('id'=>'fgrupo',  'title'=>'Gestionar Grupos')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('VEND', 'JQ');
		$param['otros']       = $this->datasis->otros('VEND', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';
		$ngrid = '#newapi'.$grid0;

		$bodyscript .= $this->jqdatagrid->bsshow('vend', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'vend', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'vend', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('vend', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '400', '450' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '250', '500' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

		$bodyscript .= '
		$("#bgrupo").click(function(){
			$.post("'.site_url($this->url.'grvdform').'",
			function(data){
				$("#fgrupo").html(data);
				$("#fgrupo").dialog("open");
			});
		});';

		$bodyscript .= '
		$("#fgrupo").dialog({
			autoOpen: false, height: 400, width: 320, modal: true,
			close: function() {
				$("#fgrupo").html("");
			}
		});';

		$bodyscript .= '
		$("#bzona").click(function(){
			var id     = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("'.$ngrid.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'asignazona').'/"+ret.vendedor, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		});';

		$bodyscript .= '});';


		$bodyscript .= '</script>';
		return $bodyscript;
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('vendedor');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));

/*
		$grid->addField('clave');
		$grid->label('Clave');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));
*/

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('direc1');
		$grid->label('Direcci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:35, maxlength: 35 }',
		));


		$grid->addField('direc2');
		$grid->label('Direcci&oacute;n 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:35, maxlength: 35 }',
		));


		$grid->addField('telefono');
		$grid->label('Tel&eacute;fono');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 130,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:13, maxlength: 13 }',
		));


		$grid->addField('comive');
		$grid->label('Comi.Venta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('comicob');
		$grid->label('Comi.Cobrado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('recargo');
		$grid->label('Recargo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('almacen');
		$grid->label('Almac&eacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('grupo');
		$grid->label('Grupo');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('340');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('VEND','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('VEND','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('VEND','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('VEND','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: vendadd, editfunc: vendedit, delfunc: venddel, viewfunc: vendshow');

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

	/**
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('vend');

		$response   = $grid->getData('vend', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$mcodp  = '??????';
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			echo 'Deshabilitado';
		}elseif($oper == 'edit'){
			echo 'Deshabilitado';
		}elseif($oper == 'del'){
			echo 'Deshabilitado';
		}
	}

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});';

		$edit = new DataEdit('', 'vend');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert', '_pre_insert' );
		$edit->pre_process( 'update', '_pre_update' );
		$edit->pre_process( 'delete', '_pre_delete' );

		$edit->vendedor = new inputField('C&oacute;digo', 'vendedor');
		$edit->vendedor->size=5;
		$edit->vendedor->maxlength=5;
		$edit->vendedor->rule = 'trim|required|callback_chexiste|alpha_numeric';
		$edit->vendedor->mode ='autohide';

		$edit->grupo = new dropdownField('Grupo', 'grupo');
		$edit->grupo->option('','Seleccionar');
		$edit->grupo->options('SELECT id, nombre FROM grvd ORDER BY nombre');
		$edit->grupo->style='width:180px';
		$edit->grupo->rule ='required';

		$edit->tipo = new dropdownField('Tipo', 'tipo');
		$edit->tipo->option('','Seleccionar');
		$edit->tipo->options(array('V'=> 'Vendedor','C'=>'Cobrador', 'A'=>'Vendedor y Cobrador','I'=>'Inactivo'));
		$edit->tipo->style='width:180px';
		$edit->tipo->rule ='required';

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->rule = 'trim|strtoupper|required';
		$edit->nombre->size=40;
		$edit->nombre->maxlength=35;

		$edit->direc1 = new inputField('Direcci&oacute;n', 'direc1');
		$edit->direc1->size=40;
		$edit->direc1->rule='trim';
		$edit->direc1->maxlength=35;

		$edit->direc2 = new inputField("", "direc2");
		$edit->direc2->size=40;
		$edit->direc2->rule='trim';
		$edit->direc2->maxlength=35;

		$edit->telefono = new inputField('Tel&eacute;fono', 'telefono');
		$edit->telefono->size=16;
		$edit->telefono->maxlength=13;
		$edit->telefono->rule = 'trim';

		$edit->almacen = new dropdownField('Almac&eacute;n', 'almacen');
		$edit->almacen->option('','Seleccionar');
		$edit->almacen->options("SELECT ubica, ubides FROM caub WHERE gasto='N' ORDER BY ubides");
		$edit->almacen->style='width:150px';

		$edit->clave = new inputField('Clave','clave');
		$edit->clave->size=7;
		$edit->clave->rule='trim';
		$edit->clave->maxlength=5;
		$edit->clave->type='password';
		$edit->clave->when =array('create','modify');

		$edit->comive  = new inputField("% por ventas ", "comive");
		$edit->comive->size=7;
		$edit->comive->maxlength=5;
		$edit->comive->css_class='inputnum';
		$edit->comive->rule='trim|numeric';
		$edit->comive->group='Comisiones';

		$edit->comicob = new inputField('% por cobranzas', 'comicob');
		$edit->comicob->size=7;
		$edit->comicob->maxlength=5;
		$edit->comicob->css_class='inputnum';
		$edit->comicob->rule='trim|numeric';
		$edit->comicob->group='Comisiones';

		//$edit->buttons('modify', 'save', 'undo', 'delete', 'back');

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

	function _pre_insert($do){
		$do->error_message_ar['pre_ins']='';
		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='';
		return true;
	}

	function _pre_delete($do) {
		$codigo=$do->get('vendedor');
		$dbcodigo=$this->db->escape($codigo);
		$check = $this->datasis->dameval("SELECT count(*) FROM sfac WHERE vd=${dbcodigo}");
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Vendedor relacionado con una o mas facturas no puede ser eliminado';
			return False;
		}else	{
			return True;
		}
	}

	function _post_insert($do){
		$codigo=$do->get('vendedor');
		$tipo=$do->get('tipo');
		$nombre=$do->get('nombre');
		logusu('vend',"CODIGO ${codigo} NOMBRE ${nombre} TIPO ${tipo} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('vendedor');
		$tipo=$do->get('tipo');
		$nombre=$do->get('nombre');
		logusu('vend',"CODIGO ${codigo} NOMBRE ${nombre} TIPO ${tipo} MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('vendedor');
		$nombre=$do->get('nombre');
		$tipo=$do->get('tipo');
		logusu('vend',"CODIGO ${codigo} NOMBRE ${nombre} TIPO ${tipo} ELIMINADO");
	}

	function chexiste($codigo){
		$codigo  =$this->input->post('vendedor');
		$dbcodigo=$this->db->escape($codigo);
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM vend WHERE vendedor=${dbcodigo}");
		if ($check > 0){
			$nombre=$this->datasis->dameval("SELECT nombre FROM vend WHERE vendedor=${dbcodigo}");
			$this->validation->set_message('chexiste',"El codigo ${codigo} ya existe para el vendedor ${nombre}");
			return false;
		}else {
			return true;
		}
	}

	//******************************************************************
	// Forma de Grupos vend
	//
	function grvdform(){
		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'hidden'      => 'true',
			'align'       => "'center'",
			'width'       => 20,
			'editable'    => 'false',
			'editoptions' => '{readonly:true,size:10}'
			)
		);

		$grid->addField('nombre');
		$grid->label('Nombre del grupo');
		$grid->params(array(
			'width'     => 180,
			'editable'  => 'true',
			'edittype'  => "'text'",
			'editrules' => '{required:true}'
			)
		);

		$grid->showpager(true);
		$grid->setViewRecords(false);
		$grid->setWidth('300');
		$grid->setHeight('280');

		$grid->setUrlget(site_url($this->url.'getgdata/'));
		$grid->setUrlput(site_url($this->url.'setgdata/'));

		$mgrid = $grid->deploy();

		$msalida  = '<script type="text/javascript">'."\n";
		$msalida .= '
		$("#newapi'.$mgrid['gridname'].'").jqGrid({
			ajaxGridOptions : {type:"POST"}
			,jsonReader : { root:"data", repeatitems: false }
			'.$mgrid['table'].'
			,scroll: true
			,pgtext: null, pgbuttons: false, rowList:[]
		})
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'navGrid\',  "#pnewapi'.$mgrid['gridname'].'",{edit:false, add:false, del:true, search: false});
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'inlineNav\',"#pnewapi'.$mgrid['gridname'].'");
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'filterToolbar\');
		';

		$msalida .= '</script>';
		$msalida .= '<id class="anexos"><table id="newapi'.$mgrid['gridname'].'"></table>';
		$msalida .= '<div   id="pnewapi'.$mgrid['gridname'].'"></div></div>';
		echo $msalida;
	}

	function getgdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('grvd');

		$response   = $grid->getData('grvd', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}


	function asignazona($vd){
		$this->rapyd->load('dataform');
		$dbvd = $this->db->escape($vd);

		$form = new DataForm($this->url."asignazona/${vd}/process");
		//$form->script($script);

		$row = $this->datasis->damerow("SELECT nombre FROM vend WHERE vendedor=${dbvd}");
		if(!empty($row)){
			$htmltabla="<table width='100%' style='background-color:#FBEC88;text-align:center;font-size:12px'>
				<tr>
					<td>Vendedor:</td>
					<td><b>(".htmlspecialchars($row['nombre']).")</b></td>
				</tr>
			</table>";

			$form->tablafo = new containerField('tablafo',$htmltabla);

			$form->tipo = new dropdownField('Tipo', 'tipo');
			$form->tipo->option('V','Vendedor');
			$form->tipo->option('C','Cobrador');
			$form->tipo->rule  = 'required|enum[C,V]';
			$form->tipo->style = 'width:166px';

			$form->zona = new dropdownField('Zona asignada', 'zona');
			$form->zona->rule = 'trim|required';
			$form->zona->option('','Seleccionar');
			$form->zona->options('SELECT TRIM(codigo) AS codigo, CONCAT(codigo," ", nombre) nombre FROM zona ORDER BY nombre');
			$form->zona->style = 'width:166px';
			$form->zona->rule  = 'required';

			$form->build_form();

			if($form->on_success()){
				$tipo   = $form->tipo->newValue;
				$dbzona = $this->db->escape($form->zona->newValue);
				if($tipo=='C'){
					$opt='cobrador';
				}else{
					$opt='vendedor';
				}

				$mSQL="UPDATE scli SET ${opt}=${dbvd} WHERE zona=${dbzona}";
				$ban=$this->db->simple_query($mSQL);
				if($ban){
					$rt=array(
							'status' =>'A',
							'mensaje'=>'Zona asignada.',
							'pk'     =>null
						);
				}else{
					$rt=array(
							'status' =>'B',
							'mensaje'=>'No se pudo asignar la zona al vendedor',
							'pk'     =>null
						);
				}
				echo json_encode($rt);
			}else{
				echo $form->output;
			}
		}else{
			echo 'Registro inexistente';
		}
	}

	function instalar(){
		if(!$this->db->table_exists('vend')){
			$mSQL="CREATE TABLE `vend` (
			  `vendedor` varchar(5) NOT NULL DEFAULT '',
			  `clave` varchar(5) DEFAULT NULL,
			  `nombre` varchar(30) DEFAULT NULL,
			  `direc1` varchar(35) DEFAULT NULL,
			  `direc2` varchar(35) DEFAULT NULL,
			  `telefono` varchar(13) DEFAULT NULL,
			  `comive` decimal(5,2) DEFAULT NULL,
			  `comicob` decimal(5,2) DEFAULT NULL,
			  `recargo` decimal(5,2) DEFAULT NULL,
			  `tipo` char(1) DEFAULT NULL,
			  `almacen` varchar(4) DEFAULT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `grupo` INT(11) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('vend');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE vend DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE vend ADD UNIQUE INDEX vendedor (vendedor)');
			$this->db->simple_query('ALTER TABLE vend ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if(!in_array('grupo',$campos)){
			$this->db->simple_query('ALTER TABLE `vend`	ADD COLUMN `grupo` INT(11) NOT NULL DEFAULT \'1\' AFTER `id`');
		}

		if(!$this->db->table_exists('grvd')){
			$mSQL="CREATE TABLE `grvd` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`nombre` VARCHAR(100) NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
				)
			COMMENT='Grupo de vendedores'
			ENGINE=MyISAM;";
			$this->db->simple_query($mSQL);
			$mSQL="INSERT INTO `grvd` (`nombre`) VALUES ('UNICO')";
			$this->db->simple_query($mSQL);
		}
	}
}
