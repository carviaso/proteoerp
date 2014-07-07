<?php
class Edinmue extends Controller {
	var $mModulo = 'EDINMUE';
	var $titp    = 'INMUEBLES';
	var $tits    = 'INMUEBLES';
	var $url     = 'construccion/edinmue/';

	function Edinmue(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'EDINMUE', $ventana=0 );
	}

	function index(){
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
		$param['listados']    = $this->datasis->listados('EDINMUE', 'JQ');
		$param['otros']       = $this->datasis->otros('EDINMUE', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	// Funciones de los Botones
	//
	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';
		$ngrid = '#newapi'.$grid0;

		$bodyscript .= $this->jqdatagrid->bsshow('edinmue', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'edinmue', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'edinmue', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('edinmue', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '550', '600' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

		$bodyscript .= '});';

		$bodyscript .= '</script>';

		return $bodyscript;
	}

	//******************************************************************
	// Definicion del Grid o Tabla 
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('descripcion');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->addField('edificacion');
		$grid->label('Edificacion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));


		$grid->addField('uso');
		$grid->label('Uso');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));


		$grid->addField('usoalter');
		$grid->label('Usoalter');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));


		$grid->addField('ubicacion');
		$grid->label('Ubicacion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));


		$grid->addField('caracteristicas');
		$grid->label('Caracteristicas');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('area');
		$grid->label('Area');
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


		$grid->addField('estaciona');
		$grid->label('Estaciona');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));


		$grid->addField('deposito');
		$grid->label('Deposito');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));


		$grid->addField('preciomt2e');
		$grid->label('Preciomt2e');
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


		$grid->addField('preciomt2c');
		$grid->label('Preciomt2c');
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


		$grid->addField('preciomt2a');
		$grid->label('Preciomt2a');
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


		$grid->addField('objeto');
		$grid->label('Objeto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('status');
		$grid->label('Status');
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

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('EDINMUE','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('EDINMUE','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('EDINMUE','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('EDINMUE','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: edinmueadd, editfunc: edinmueedit, delfunc: edinmuedel, viewfunc: edinmueshow");

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
		$mWHERE = $grid->geneTopWhere('edinmue');

		$response   = $grid->getData('edinmue', array(array()), array(), false, $mWHERE );
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
		$mcodp  = "??????";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM edinmue WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('edinmue', $data);
					echo "Registro Agregado";

					logusu('EDINMUE',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM edinmue WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM edinmue WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE edinmue SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("edinmue", $data);
				logusu('EDINMUE',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('edinmue', $data);
				logusu('EDINMUE',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM edinmue WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM edinmue WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM edinmue WHERE id=$id ");
				logusu('EDINMUE',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	// Edicion 

	function dataedit(){
		$this->rapyd->load('dataedit');

		$link1=site_url('construccion/common/get_ubic');
		$script ='
		$(function() {
			$("#edificacion").change(function(){ edif_change(); });
			$(".inputnum").numeric(".");
			$(".inputonlynum").numeric();
		});

		function edif_change(){
			$.post("'.$link1.'",{ edif:$("#edificacion").val() }, function(data){ $("#ubicacion").html(data);})
		}
';


		$edit = new DataEdit('', 'edinmue');
		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		//$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert');
		$edit->pre_process( 'update','_pre_update');
		$edit->pre_process( 'delete','_pre_delete');

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->rule='max_length[15]|unique';
		$edit->codigo->size =17;
		$edit->codigo->maxlength =15;

		$edit->descripcion = new inputField('Descripci&oacute;n','descripcion');
		$edit->descripcion->rule='max_length[100]';
		$edit->descripcion->maxlength =100;

		$edit->objeto = new dropdownField('Objeto','objeto');
		$edit->objeto->option('','Seleccionar');
		$edit->objeto->option('A','Alquiler');
		$edit->objeto->option('V','Venta');
		$edit->objeto->rule='max_length[1]|required';

		$edit->status = new dropdownField('Estatus','status');
		$edit->status->option('D','Disponible');
		$edit->status->option('A','Alquilado');
		$edit->status->option('V','Vendido');
		$edit->status->option('R','Reservado');
		$edit->status->option('O','Otro');
		$edit->status->rule='max_length[11]';

		$edit->edificacion = new dropdownField('Edificaci&oacute;n','edificacion');
		$edit->edificacion->option('','Seleccionar');
		$edit->edificacion->options('SELECT id,TRIM(nombre) AS nombre FROM edif ORDER BY nombre');
		$edit->edificacion->rule='max_length[11]';

		$edit->ubicacion = new dropdownField('Ubicaci&oacute;n','ubicacion');
		$edit->ubicacion->rule='max_length[11]|integer';
		$edif=$edit->getval('edificacion');
		if($edif!==false){
			$dbedif=$this->db->escape($edif);
			$edit->ubicacion->option('','Seleccionar');
			$edit->ubicacion->options("SELECT id,descripcion FROM `edifubica` WHERE id_edif=$dbedif ORDER BY descripcion");
		}else{
			$edit->ubicacion->option('','Seleccione una edificacion');
		}

		$edit->uso = new dropdownField('Uso','uso');
		$edit->uso->option('','Seleccionar');
		$edit->uso->options('SELECT id,uso FROM `eduso` ORDER BY uso');
		$edit->uso->rule='max_length[11]|required';

		$edit->usoalter = new dropdownField('Uso Alternativo','usoalter');
		$edit->usoalter->option('','Seleccionar');
		$edit->usoalter->options('SELECT id,uso FROM `eduso` ORDER BY uso');
		$edit->usoalter->rule='max_length[11]';

		$edit->caracteristicas = new textareaField('Caracter&iacute;sticas','caracteristicas');
		//$edit->caracteristicas->rule='max_length[8]';
		$edit->caracteristicas->cols = 70;
		$edit->caracteristicas->rows = 4;

		$edit->area = new inputField('&Aacute;rea Mt2','area');
		$edit->area->rule='max_length[15]|numeric';
		$edit->area->css_class='inputnum';
		$edit->area->size =10;
		//$edit->area->maxlength =15;

		$edit->estaciona = new inputField('Estacionamiento','estaciona');
		$edit->estaciona->rule='max_length[10]|integer';
		$edit->estaciona->size =10;
		$edit->estaciona->css_class='inputonlynum';
		$edit->estaciona->maxlength =10;

		$edit->deposito = new inputField('Dep&oacute;sito','deposito');
		$edit->deposito->rule='max_length[11]|integer';
		$edit->deposito->size =10;
		$edit->deposito->maxlength =11;
		$edit->deposito->css_class='inputonlynum';

		$edit->preciomt2e = new inputField('Precio x mt2 (Contado)','preciomt2e');
		$edit->preciomt2e->rule='max_length[15]|numeric';
		$edit->preciomt2e->css_class='inputnum';
		$edit->preciomt2e->size =10;
		$edit->preciomt2e->maxlength =15;

		$edit->preciomt2c = new inputField('Precio x mt2 (Cr&eacute;dito)','preciomt2c');
		$edit->preciomt2c->rule='max_length[15]|numeric';
		$edit->preciomt2c->css_class='inputnum';
		$edit->preciomt2c->size =10;
		$edit->preciomt2c->maxlength =15;

		$edit->preciomt2a = new inputField('Precio x mt2 (Alquiler)','preciomt2');
		$edit->preciomt2a->rule='max_length[15]|numeric';
		$edit->preciomt2a->css_class='inputnum';
		$edit->preciomt2a->size =10;
		$edit->preciomt2a->maxlength =15;


		$edit->buttons('modify', 'save', 'undo', 'delete', 'back','add');
		$edit->build();
/*
		$data['content'] = $edit->output;
		$data['script']  = script('jquery.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').$script;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading($this->tits);
		$this->load->view('view_ventanas', $data);
*/

/*
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit($this->tits, 'edinmue');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->rule='';
		$edit->codigo->size =17;
		$edit->codigo->maxlength =15;

		$edit->descripcion = new inputField('Descripcion','descripcion');
		$edit->descripcion->rule='';
		$edit->descripcion->size =102;
		$edit->descripcion->maxlength =100;

		$edit->edificacion = new inputField('Edificacion','edificacion');
		$edit->edificacion->rule='integer';
		$edit->edificacion->css_class='inputonlynum';
		$edit->edificacion->size =13;
		$edit->edificacion->maxlength =11;

		$edit->uso = new inputField('Uso','uso');
		$edit->uso->rule='integer';
		$edit->uso->css_class='inputonlynum';
		$edit->uso->size =13;
		$edit->uso->maxlength =11;

		$edit->usoalter = new inputField('Usoalter','usoalter');
		$edit->usoalter->rule='integer';
		$edit->usoalter->css_class='inputonlynum';
		$edit->usoalter->size =13;
		$edit->usoalter->maxlength =11;

		$edit->ubicacion = new inputField('Ubicacion','ubicacion');
		$edit->ubicacion->rule='integer';
		$edit->ubicacion->css_class='inputonlynum';
		$edit->ubicacion->size =13;
		$edit->ubicacion->maxlength =11;

		$edit->caracteristicas = new textareaField('Caracteristicas','caracteristicas');
		$edit->caracteristicas->rule='';
		$edit->caracteristicas->cols = 70;
		$edit->caracteristicas->rows = 4;

		$edit->area = new inputField('Area','area');
		$edit->area->rule='numeric';
		$edit->area->css_class='inputnum';
		$edit->area->size =17;
		$edit->area->maxlength =15;

		$edit->estaciona = new inputField('Estaciona','estaciona');
		$edit->estaciona->rule='integer';
		$edit->estaciona->css_class='inputonlynum';
		$edit->estaciona->size =12;
		$edit->estaciona->maxlength =10;

		$edit->deposito = new inputField('Deposito','deposito');
		$edit->deposito->rule='integer';
		$edit->deposito->css_class='inputonlynum';
		$edit->deposito->size =13;
		$edit->deposito->maxlength =11;

		$edit->preciomt2e = new inputField('Preciomt2e','preciomt2e');
		$edit->preciomt2e->rule='numeric';
		$edit->preciomt2e->css_class='inputnum';
		$edit->preciomt2e->size =17;
		$edit->preciomt2e->maxlength =15;

		$edit->preciomt2c = new inputField('Preciomt2c','preciomt2c');
		$edit->preciomt2c->rule='numeric';
		$edit->preciomt2c->css_class='inputnum';
		$edit->preciomt2c->size =17;
		$edit->preciomt2c->maxlength =15;

		$edit->preciomt2a = new inputField('Preciomt2a','preciomt2a');
		$edit->preciomt2a->rule='numeric';
		$edit->preciomt2a->css_class='inputnum';
		$edit->preciomt2a->size =17;
		$edit->preciomt2a->maxlength =15;

		$edit->objeto = new inputField('Objeto','objeto');
		$edit->objeto->rule='';
		$edit->objeto->size =3;
		$edit->objeto->maxlength =1;

		$edit->status = new inputField('Status','status');
		$edit->status->rule='';
		$edit->status->size =3;
		$edit->status->maxlength =1;

		$edit->build();
*/
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

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='';
		return false;
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		if (!$this->db->table_exists('edinmue')) {
			$mSQL="CREATE TABLE `edinmue` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `codigo` char(15) DEFAULT NULL,
			  `descripcion` char(100) DEFAULT NULL,
			  `edificacion` int(11) DEFAULT NULL,
			  `uso` int(11) DEFAULT NULL,
			  `usoalter` int(11) DEFAULT NULL,
			  `ubicacion` int(11) DEFAULT NULL,
			  `caracteristicas` text,
			  `area` decimal(15,2) DEFAULT NULL,
			  `estaciona` int(10) DEFAULT NULL,
			  `deposito` int(11) DEFAULT NULL,
			  `preciomt2e` decimal(15,2) DEFAULT NULL,
			  `preciomt2c` decimal(15,2) DEFAULT NULL,
			  `preciomt2a` decimal(15,2) DEFAULT NULL,
			  `objeto` char(1) NOT NULL,
			  `status` char(1) NOT NULL COMMENT 'Alquilado, Vendido, Reservado, Otro',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codigo` (`codigo`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Facilidades '";
			$this->db->query($mSQL);
		}
		//$campos=$this->db->list_fields('edinmue');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}

/*
class edinmue extends Controller {
	var $titp='Inmuebles';
	var $tits='Inmuebles';
	var $url ='construccion/edinmue/';

	function edinmue(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('A03',1);
		$this->instalar();
	}

	function index(){
		redirect($this->url.'filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp);
		$sel=array('a.id','a.codigo','a.descripcion','a.edificacion','c.uso','d.uso AS usoalter','e.descripcion AS ubicacion','a.caracteristicas','a.area','a.estaciona','a.deposito','b.nombre');

		$filter->db->select($sel);
		$filter->db->from('edinmue AS a');
		$filter->db->join('edif  AS b','a.edificacion=b.id');
		$filter->db->join('eduso AS c','a.uso=c.id');
		$filter->db->join('eduso AS d','a.usoalter=d.id','left');
		$filter->db->join('edifubica AS e','a.ubicacion=e.id AND a.edificacion=e.id_edif');

		$filter->codigo = new inputField('C&oacute;digo','codigo');
		$filter->codigo->rule      ='max_length[15]';
		$filter->codigo->size      =17;
		$filter->codigo->maxlength =15;

		$filter->objeto = new dropdownField('Objeto','objeto');
		$filter->objeto->option('','Todos');
		$filter->objeto->option('A','Alquiler');
		$filter->objeto->option('V','Venta');

		$filter->descripcion = new inputField('Descripci&oacute;n','descripcion');
		$filter->descripcion->rule      ='max_length[100]';
		$filter->descripcion->maxlength =100;

		$filter->edificacion = new dropdownField('Edificaci&oacute;n','edificacion');
		$filter->edificacion->option('','Seleccionar');
		$filter->edificacion->options('SELECT id,TRIM(nombre) AS nombre FROM edif ORDER BY nombre');

		$filter->uso = new dropdownField('Uso','uso');
		$filter->uso->option('','Todos');
		$filter->uso->options('SELECT id,uso FROM `eduso` ORDER BY uso');

		$filter->ubicacion = new dropdownField('Ubicaci&oacute;n','ubicacion');
		$filter->ubicacion->option('','Seleccionar');
		$filter->ubicacion->options('SELECT id,descripcion FROM `edifubica` ORDER BY descripcion');

		$filter->status = new dropdownField('Estatus','status');
		$filter->status->option('D','Disponible');
		$filter->status->option('A','Alquilado');
		$filter->status->option('D','Vendido');
		$filter->status->option('R','Reservado');
		$filter->status->option('O','Otro');

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Id',$uri,'id','align="left"');
		$grid->column_orderby('C&oacute;digo','codigo','codigo','align="left"');
		$grid->column_orderby('Descripci&oacute;n','descripcion','descripcion','align="left"');
		$grid->column_orderby('Edificaci&oacute;n','nombre','nombre');
		$grid->column_orderby('Uso','uso','uso');
		$grid->column_orderby('Uso alterno','usoalter','usoalter');
		$grid->column_orderby('Ubicaci&oacute;n','ubicacion','ubicacion');
		$grid->column_orderby('&Aacute;rea'     ,'<nformat><#area#></nformat>','area','align="right"');
		$grid->column_orderby('Estacionamiento' ,'estaciona','estaciona','align="right"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = heading($this->titp);
		$this->load->view('view_ventanas', $data);

	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'edinmue');

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert');
		$edit->pre_process( 'update','_pre_update');
		$edit->pre_process( 'delete','_pre_delete');

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->rule='max_length[15]|unique';
		$edit->codigo->size =17;
		$edit->codigo->maxlength =15;

		$edit->descripcion = new inputField('Descripci&oacute;n','descripcion');
		$edit->descripcion->rule='max_length[100]';
		$edit->descripcion->maxlength =100;

		$edit->objeto = new dropdownField('Objeto','objeto');
		$edit->objeto->option('','Seleccionar');
		$edit->objeto->option('A','Alquiler');
		$edit->objeto->option('V','Venta');
		$edit->objeto->rule='max_length[1]|required';

		$edit->status = new dropdownField('Estatus','status');
		$edit->status->option('D','Disponible');
		$edit->status->option('A','Alquilado');
		$edit->status->option('V','Vendido');
		$edit->status->option('R','Reservado');
		$edit->status->option('O','Otro');
		$edit->status->rule='max_length[11]';

		$edit->edificacion = new dropdownField('Edificaci&oacute;n','edificacion');
		$edit->edificacion->option('','Seleccionar');
		$edit->edificacion->options('SELECT id,TRIM(nombre) AS nombre FROM edif ORDER BY nombre');
		$edit->edificacion->rule='max_length[11]';

		$edit->ubicacion = new dropdownField('Ubicaci&oacute;n','ubicacion');
		$edit->ubicacion->rule='max_length[11]|integer';
		$edif=$edit->getval('edificacion');
		if($edif!==false){
			$dbedif=$this->db->escape($edif);
			$edit->ubicacion->option('','Seleccionar');
			$edit->ubicacion->options("SELECT id,descripcion FROM `edifubica` WHERE id_edif=$dbedif ORDER BY descripcion");
		}else{
			$edit->ubicacion->option('','Seleccione una edificacion');
		}

		$edit->uso = new dropdownField('Uso','uso');
		$edit->uso->option('','Seleccionar');
		$edit->uso->options('SELECT id,uso FROM `eduso` ORDER BY uso');
		$edit->uso->rule='max_length[11]|required';

		$edit->usoalter = new dropdownField('Uso Alternativo','usoalter');
		$edit->usoalter->option('','Seleccionar');
		$edit->usoalter->options('SELECT id,uso FROM `eduso` ORDER BY uso');
		$edit->usoalter->rule='max_length[11]';

		$edit->caracteristicas = new textareaField('Caracter&iacute;sticas','caracteristicas');
		//$edit->caracteristicas->rule='max_length[8]';
		$edit->caracteristicas->cols = 70;
		$edit->caracteristicas->rows = 4;

		$edit->area = new inputField('&Aacute;rea Mt2','area');
		$edit->area->rule='max_length[15]|numeric';
		$edit->area->css_class='inputnum';
		$edit->area->size =10;
		//$edit->area->maxlength =15;

		$edit->estaciona = new inputField('Estacionamiento','estaciona');
		$edit->estaciona->rule='max_length[10]|integer';
		$edit->estaciona->size =10;
		$edit->estaciona->css_class='inputonlynum';
		$edit->estaciona->maxlength =10;

		$edit->deposito = new inputField('Dep&oacute;sito','deposito');
		$edit->deposito->rule='max_length[11]|integer';
		$edit->deposito->size =10;
		$edit->deposito->maxlength =11;
		$edit->deposito->css_class='inputonlynum';

		$edit->preciomt2e = new inputField('Precio x mt2 (Contado)','preciomt2e');
		$edit->preciomt2e->rule='max_length[15]|numeric';
		$edit->preciomt2e->css_class='inputnum';
		$edit->preciomt2e->size =10;
		$edit->preciomt2e->maxlength =15;

		$edit->preciomt2c = new inputField('Precio x mt2 (Cr&eacute;dito)','preciomt2c');
		$edit->preciomt2c->rule='max_length[15]|numeric';
		$edit->preciomt2c->css_class='inputnum';
		$edit->preciomt2c->size =10;
		$edit->preciomt2c->maxlength =15;

		$edit->preciomt2a = new inputField('Precio x mt2 (Alquiler)','preciomt2');
		$edit->preciomt2a->rule='max_length[15]|numeric';
		$edit->preciomt2a->css_class='inputnum';
		$edit->preciomt2a->size =10;
		$edit->preciomt2a->maxlength =15;

		$link1=site_url('construccion/common/get_ubic');
		$script ='<script type="text/javascript" >
		$(function() {
			$("#edificacion").change(function(){ edif_change(); });
			$(".inputnum").numeric(".");
			$(".inputonlynum").numeric();
		});

		function edif_change(){
			$.post("'.$link1.'",{ edif:$("#edificacion").val() }, function(data){ $("#ubicacion").html(data);})
		}

		</script>';

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back','add');
		$edit->build();
		$data['content'] = $edit->output;
		$data['script']  = script('jquery.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').$script;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading($this->tits);
		$this->load->view('view_ventanas', $data);
	}

	function _pre_insert($do){
		return true;
	}

	function _pre_update($do){
		return true;
	}

	function _pre_delete($do){
		return true;
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		if (!$this->db->table_exists('edinmue')) {
			$mSQL="CREATE TABLE `edinmue` (
			  `id` INT(11) NOT NULL AUTO_INCREMENT,
			  `codigo` CHAR(15) NULL DEFAULT NULL,
			  `descripcion` CHAR(100) NULL DEFAULT NULL,
			  `edificacion` INT(11) NULL DEFAULT NULL,
			  `uso` INT(11) NULL DEFAULT NULL,
			  `usoalter` INT(11) NULL DEFAULT NULL,
			  `ubicacion` INT(11) NULL DEFAULT NULL,
			  `caracteristicas` TEXT NULL,
			  `area` DECIMAL(15,2) NULL DEFAULT NULL,
			  `estaciona` INT(10) NULL DEFAULT NULL,
			  `deposito` INT(11) NULL DEFAULT NULL,
			  `preciomt2` DECIMAL(15,2) NULL DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE INDEX `codigo` (`codigo`)
			)
			COMMENT='Inmuebles'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('preciomt2e', 'edinmue')) {
			$mSQL="ALTER TABLE `edinmue`  CHANGE COLUMN `preciomt2` `preciomt2e` DECIMAL(15,2) NULL AFTER `deposito`,  ADD COLUMN `preciomt2c` DECIMAL(15,2) NULL AFTER `preciomt2e`,  ADD COLUMN `preciomt2a` DECIMAL(15,2) NULL AFTER `preciomt2c`";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('objeto', 'edinmue')) {
			$mSQL="ALTER TABLE `edinmue`  ADD COLUMN `objeto` CHAR(1) NOT NULL AFTER `preciomt2a`";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('status', 'edinmue')) {
			$mSQL="ALTER TABLE `edinmue`  ADD COLUMN `status` CHAR(1) NOT NULL COMMENT 'Alquilado, Vendido, Reservado,Disponible, Otro' AFTER `objeto`;";
			$this->db->simple_query($mSQL);
		}

	}

}*/
?>
