<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
//require_once(BASEPATH.'application/controllers/formams.php');
class Edcont extends Controller {
	var $mModulo = 'EDCONT';
	var $titp    = 'CONTRATOS DE VENTA';
	var $tits    = 'CONTRATOS DE VENTA';
	var $url     = 'construccion/edcont/';

	function Edcont(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'EDCONT', $ventana=0 );
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
		$param['listados']    = $this->datasis->listados('EDCONT', 'JQ');
		$param['otros']       = $this->datasis->otros('EDCONT', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('edcont', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'edcont', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'edcont', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('edcont', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '500', '750' );
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


		$grid->addField('id_edres');
		$grid->label('Id_edres');
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


		$grid->addField('numero_edres');
		$grid->label('Numero_edres');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
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


		$grid->addField('cliente');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
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


		$grid->addField('inmueble');
		$grid->label('Inmueble');
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


		$grid->addField('inicial');
		$grid->label('Inicial');
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


		$grid->addField('financiable');
		$grid->label('Financiable');
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


		$grid->addField('firma');
		$grid->label('Firma');
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


		$grid->addField('precioxmt2');
		$grid->label('Precioxmt2');
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


		$grid->addField('mt2');
		$grid->label('Mt2');
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


		$grid->addField('monto');
		$grid->label('Monto');
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


		$grid->addField('notas');
		$grid->label('Notas');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
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
		$grid->setAdd(    $this->datasis->sidapuede('EDCONT','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('EDCONT','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('EDCONT','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('EDCONT','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: edcontadd, editfunc: edcontedit, delfunc: edcontdel, viewfunc: edcontshow");

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
		$mWHERE = $grid->geneTopWhere('edcont');

		$response   = $grid->getData('edcont', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM edcont WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('edcont', $data);
					echo "Registro Agregado";

					logusu('EDCONT',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM edcont WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM edcont WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE edcont SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("edcont", $data);
				logusu('EDCONT',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('edcont', $data);
				logusu('EDCONT',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM edcont WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM edcont WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM edcont WHERE id=$id ");
				logusu('EDCONT',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}


	//******************************************************************
	// Edicion 
	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');

		$do = new DataObject('edcont');
		$do->pointer('scli' ,'scli.cliente=edcont.cliente','scli.tipo AS sclitipo, scli.nombre AS nombre, dire11 AS direc, scli.rifci AS rifci','left');
		//$do->pointer('edres' ,'edres.id=edcont.id_edres','scli.tipo AS sclitipo, scli.nombre AS edresnumero','left');
		$do->rel_one_to_many('itedcont', 'itedcont', array('id'=>'id_edcont'));
		$do->order_rel_one_to_many('itedcont','id');

		$edit = new DataDetails('', $do);
/*
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
			$(".inputonlynum").numeric();
		});
		';
		$edit->script($script,'modify');
		$edit->script($script,'create');
*/
		$edit->on_save_redirect=false;

		$id=$edit->get_from_dataobjetct('id');
		if($id!==false){
			$action = "javascript:window.location='".site_url($this->url.'formato/'.$id.'/contrato.xml')."'";
			$edit->button('btn_formato', 'Descargar formato', $action,'TR');
		}

		$status=$edit->get_from_dataobjetct('status');
		if($status == 'P'){
			$action = "javascript:window.location='".site_url($this->url.'actualizar/'.$id)."'";
			$edit->button('btn_actuali', 'Actualizar', $action,'TR');
		}

		//$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert');
		$edit->pre_process( 'update','_pre_update');
		$edit->pre_process( 'delete','_pre_delete');

		$edit->id_edres = new inputField('Id_edres','id_edres');
		$edit->id_edres->rule = 'max_length[11]|integer';
		$edit->id_edres->css_class='inputonlynum';
		$edit->id_edres->size =13;
		$edit->id_edres->maxlength =11;

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule='max_length[8]';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;

		$edit->status = new dropdownField('Estatus','status');
		$edit->status->option('P','Pendiente');
		$edit->status->option('A','Aprobado');
		$edit->status->style='width:180px;';
		$edit->status->rule='max_length[1]';
		$edit->status->when=array('show');

		$edit->numero_edres = new inputField('Reservaci&oacute;n','numero_edres');
		$edit->numero_edres->rule='max_length[8]';
		$edit->numero_edres->size =10;
		$edit->numero_edres->maxlength =8;

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->size =10;
		$edit->numero->maxlength =8;
		$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->insertValue=date('Y-m-d');
		$edit->fecha->maxlength =8;
		$edit->fecha->calendar=false;

		$edit->cliente = new inputField('Cliente','cliente');
		$edit->cliente->rule='max_length[5]|required';
		$edit->cliente->size =7;
		$edit->cliente->maxlength =5;

		$edit->sclitipo = new hiddenField('', 'sclitipo');
		$edit->sclitipo->db_name     = 'sclitipo';
		$edit->sclitipo->pointer     = true;
		$edit->sclitipo->insertValue = 1;

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->size = 25;
		$edit->nombre->maxlength=40;
		$edit->nombre->readonly =true;
		$edit->nombre->autocomplete=false;
		$edit->nombre->rule= 'required';
		$edit->nombre->type ='inputhidden';
		$edit->nombre->pointer=true;

		$edit->rifci   = new inputField('RIF/CI','rifci');
		$edit->rifci->autocomplete=false;
		$edit->rifci->readonly =true;
		$edit->rifci->size = 15;
		$edit->rifci->type ='inputhidden';
		$edit->rifci->pointer=true;

		$edit->direc = new inputField('Direcci&oacute;n','direc');
		$edit->direc->readonly =true;
		$edit->direc->size = 40;
		$edit->direc->type ='inputhidden';
		$edit->direc->pointer=true;

		$edit->edificacion = new dropdownField('Edificaci&oacute;n','edificacion');
		$edit->edificacion->option('','Seleccionar');
		$edit->edificacion->options('SELECT id,TRIM(nombre) AS nombre FROM `edif` ORDER BY nombre');
		$edit->edificacion->style='width:150px;';
		$edit->edificacion->rule='max_length[11]|required';

		$edit->inmueble = new dropdownField('Inmueble','inmueble');
		$edit->inmueble->option('','Seleccionar');
		$edif=$edit->getval('edificacion');
		if($edif!==false){
			$dbedif=$this->db->escape($edif);
			$edit->inmueble->option('','Seleccionar');
			$edit->inmueble->options("SELECT id,TRIM(descripcion) AS nombre FROM `edinmue` WHERE status='D' AND edificacion=$dbedif ORDER BY descripcion");
		}else{
			$edit->inmueble->option('','Seleccione una edificacion');
		}
		$edit->inmueble->style='width:150px;';
		$edit->inmueble->rule='max_length[11]|required';

		$edit->reserva = new inputField('Reserva','reserva');
		$edit->reserva->rule='max_length[17]|numeric';
		$edit->reserva->css_class='inputnum';
		$edit->reserva->size =10;
		$edit->reserva->maxlength =17;

		$edit->precioxmt2 = new inputField('Precio m2','precioxmt2');
		$edit->precioxmt2->rule='max_length[17]|numeric|mayorcero|required';
		$edit->precioxmt2->css_class='inputnum';
		$edit->precioxmt2->size =10;
		$edit->precioxmt2->maxlength =17;
		$edit->precioxmt2->showformat ='decimal';

		$edit->mt2 = new inputField('&Aacute;rea m2','mt2');
		$edit->mt2->rule='max_length[17]|numeric|mayorcero|required';
		$edit->mt2->css_class='inputnum';
		$edit->mt2->size =7;
		$edit->mt2->maxlength =17;
		$edit->mt2->showformat ='decimal';

		$edit->uso = new dropdownField('Uso','uso');
		$edit->uso->option('','Seleccionar');
		$edit->uso->options('SELECT id,uso FROM `eduso` ORDER BY uso');
		$edit->uso->style='width:180px;';
		$edit->uso->rule='required';

		$edit->inicial = new inputField('Inicial','inicial');
		$edit->inicial->rule='max_length[17]|numeric|mayorcero';
		$edit->inicial->css_class='inputnum';
		$edit->inicial->size =19;
		$edit->inicial->maxlength =17;
		$edit->inicial->showformat ='decimal';

		$edit->financiable = new inputField('Monto financiable','financiable');
		$edit->financiable->rule='max_length[17]|numeric';
		$edit->financiable->css_class='inputnum';
		$edit->financiable->size =19;
		$edit->financiable->maxlength =17;
		$edit->financiable->showformat ='decimal';

		$edit->firma = new inputField('Pago final (firma)','firma');
		$edit->firma->rule='max_length[17]|numeric|mayorcero';
		$edit->firma->css_class='inputnum';
		$edit->firma->size =19;
		$edit->firma->type ='inputhidden';
		$edit->firma->maxlength =17;
		$edit->firma->showformat ='decimal';

		$edit->monto = new inputField('Monto total','monto');
		$edit->monto->rule='max_length[17]|numeric|mayorcero';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =19;
		$edit->monto->type ='inputhidden';
		$edit->monto->maxlength =17;
		$edit->monto->showformat ='decimal';

		$edit->notas = new textareaField('Notas','notas');
		$edit->notas->rule='max_length[8]';
		$edit->notas->cols = 70;
		$edit->notas->rows = 4;

		//*******************************
		// Inicio del detalle
		//*******************************
		$edit->it_vencimiento = new dateField('Vencimiento <#o#>','it_vencimiento_<#i#>');
		$edit->it_vencimiento->rule='chfecha|required';
		$edit->it_vencimiento->size =10;
		$edit->it_vencimiento->insertValue =date('Y-m-d');
		$edit->it_vencimiento->db_name ='vencimiento';
		$edit->it_vencimiento->rel_id  ='itedcont';
		$edit->it_vencimiento->maxlength =8;
		$edit->it_vencimiento->calendar=false;

		$edit->it_especial = new dropdownField('Especial <#o#>','it_especial_<#i#>');
		$edit->it_especial->rule    = 'max_length[1]|enum[S,N]';
		$edit->it_especial->db_name = 'especial';
		$edit->it_especial->rel_id  = 'itedcont';
		$edit->it_especial->style='width:80px;';
		$edit->it_especial->option('N','Normal');
		$edit->it_especial->option('S','Especial');

		$edit->it_monto = new inputField('Monto <#o#>','it_monto_<#i#>');
		$edit->it_monto->rule='max_length[10]|numeric';
		$edit->it_monto->db_name   ='monto';
		$edit->it_monto->rel_id    ='itedcont';
		//$edit->it_monto->on_keyup  = 'totagiro()';
		//$edit->it_monto->on_keyup  ='distrib()';
		$edit->it_monto->css_class ='inputnum';
		$edit->it_monto->size      =12;
		$edit->it_monto->maxlength =10;
		$edit->it_monto->showformat ='decimal';
		//******************************
		// Fin del detalle
		//******************************

		if($status != 'A'){
			$edit->buttons('modify', 'delete','add_rel');
			//$edit->buttons('modify', 'save', 'undo', 'delete','add_rel');
		}
		//$edit->buttons('back', 'add');
		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form']  =&  $edit;
			$data['content']  =  $this->load->view('view_edcont', $conten, false);

			//$data['content'] = $this->load->view('view_edcont', $conten,true);
			//echo $edit->output;
		}

/*
		$conten['form']     =& $edit;
		$data['content'] = $this->load->view('view_edcont', $conten,true);
		$data['title']   = heading($this->tits);
		$data['head']    = $this->rapyd->get_head();
		$data['head']   .= script('jquery.js');
		$data['head']   .= script('jquery-ui.js');
		$data['head']   .= script('plugins/jquery.numeric.pack.js');
		$data['head']   .= script('plugins/jquery.floatnumber.js');
		$data['head']   .= script('plugins/jquery.meiomask.js');
		$data['head']   .= phpscript('nformat.js');
		$data['head']   .= style('redmond/jquery-ui-1.8.1.custom.css');
		$this->load->view('view_ventanas', $data);
*/

	}

	function letracambio($id,$numero='1'){

		$sel = array('a.*','b.numero','b.fecha'
		,'CONCAT_WS(" ",TRIM(c.dire11),TRIM(c.dire12)) AS direc'
		,'c.telefono','TRIM(c.nombre) AS nombre','c.ciudad1');
		$this->db->select($sel);
		$this->db->where('a.id',$id);
		$this->db->from('itedcont AS a');
		$this->db->join('edcont AS b','a.id_edcont=b.id');
		$this->db->join('scli AS c','c.cliente=b.cliente');
		$query = $this->db->get();

		if ($query->num_rows() > 0){
			$row    = $query->row();
			$this->load->plugin('numletra');
			$this->db->where('id_edcont',$row->id_edcont);
			$this->db->from('itedcont');
			$cana=$this->db->count_all_results();

			$data['ncontrato'] = $row->numero;
			$data['fecha']     = dbdate_to_human($row->fecha);
			$data['fcontrato'] = dbdate_to_human($row->fecha);
			$data['monto']     = nformat($row->monto);
			$data['montolet']  = strtoupper(numletra($row->monto));
			$data['vence']     = dbdate_to_human($row->vencimiento);
			$data['numero']    = $numero;
			$data['cana']      = $cana;
			$data['direc']     = $row->direc.' '.$row->ciudad1;
			$data['nombre']    = $row->nombre;
			$data['telf']      = $row->telefono;

			formams::_msxml('letra',$data);
		}
	}

	function formato($id){
		$this->load->plugin('numletra');
		$this->load->helper('date');
		$this->load->helper('fecha');

		$sel=array('a.id','a.id_edres','a.numero_edres','a.numero','a.status','a.fecha'
		,'a.cliente','a.edificacion','a.inmueble','a.inicial'
		,'a.financiable','a.firma','a.precioxmt2','a.mt2','a.monto','a.notas','e.uso'
		,'CONCAT_WS(" ",TRIM(b.dire11),TRIM(b.dire12)) AS direc','b.ciudad','b.rifci'
		,'b.telefono','TRIM(b.nombre) AS nombre'
		,'c.codigo AS lcodigo','f.descripcion AS ubica'
		);
		$this->db->select($sel);
		$this->db->from('edcont AS a');
		$this->db->join('scli AS b'     ,'a.cliente=b.cliente');
		$this->db->join('edinmue AS c'  ,'c.id=a.inmueble');
		$this->db->join('eduso AS e'    ,'e.id=a.uso');
		$this->db->join('edifubica AS f','c.ubicacion=f.id');
		$this->db->where('a.id',$id);

		$query = $this->db->get();

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$this->db->where('id_edcont',$id);
				$this->db->from('itedcont');
				$cuotas=$this->db->count_all_results();

				$estado='';
				$data=array();
				$empresa=true;
				if(!$empresa){
					$data['opcionante'] ="el ciudadano: ".utf8_encode($row->nombre).", venezolano, mayor de edad,  titular de la Cédula de Identidad No. ".$row->rifci.",  Médico, domiciliado en la ciudad de ".utf8_encode($row->ciudad).", Estado ".$estado." y hábil;";
				}else{
					$data['opcionante'] =utf8_encode($row->nombre)." domiciliada en la ciudad de ".utf8_encode($row->ciudad).", Estado ".$estado.", debidamente inscrita por ante el Registro Mercantil Cuarto de la Circunscripción Judicial del Estado Zulia, en fecha 18 de Abril de 2006, bajo el No. 04, Tomo 31-A; con Registro de Información Fiscal No. ".$row->rifci;
				}
				$data['opcionante']='opcionante';
				$timespan=mysql_to_unix($row->fecha);

				$data['dialet']         = date('d',$timespan);
				$data['meslet']         = mesletra(date('m',$timespan));
				$data['anio']           = date('Y',$timespan);
				$data['lcodigo']        = $row->lcodigo;
				$data['ubicacion']      = $row->ubica;
				$data['preciomt2let']   = strtoupper(numletra($row->precioxmt2));
				$data['preciomt2']      = nformat($row->precioxmt2);
				$data['financiable']    = nformat($row->financiable);;
				$data['financiablelet'] = strtoupper(numletra($row->inicial+$row->financiable));;
				$data['cuotas']         = $cuotas;
				$data['cuotaslet']      = strtoupper(numletra($cuotas));
				$data['moncuotaslet']   = '';
				$data['moncuotas']      = '';
				$data['venceprimera']   = '';
				$data['inifinanlet']    = strtoupper(numletra($row->inicial));
				$data['inifinan']       = nformat($row->inicial+$row->financiable);
				$data['iniciallet']     = strtoupper(numletra($row->inicial+$row->financiable));
				$data['inicial']        = nformat($row->inicial);
				$data['firmalet']       = strtoupper(numletra($row->firma));
				$data['firma']          = nformat($row->firma);
				$data['numero']         = $row->numero;
				$data['fecha']          = dbdate_to_human($row->fecha);
				$data['monto']          = nformat($row->monto);
				$data['montolet']       = strtoupper(numletra($row->monto));
				$data['area']           = $row->mt2;
				$data['arealet']        = strtoupper(numletra($row->mt2));
				$data['uso']            = $row->uso;

				formams::_msxml('contrato',$data);
			}
		}
	}

	function actualizar($id){
		$url=$this->url.'dataedit/show/'.$id;
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence($url, $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ?$persistence['back_uri'] : $url;

		$sel=array('a.*','b.nombre','c.descripcion AS local');
		$this->db->select($sel);
		$this->db->where('a.id',$id);
		$this->db->from('edcont AS a');
		$this->db->join('scli AS b','a.cliente=b.cliente');
		$this->db->join('edinmue AS c'  ,'c.id=a.inmueble');
		$query = $this->db->get();

		if ($query->num_rows() > 0){
			$estampa=date('Y-m-d');
			$hora   =date('H:i:s');
			$transac=$this->datasis->fprox_numero('ntransa');
			$usuario=$this->secu->usuario();
			$row    = $query->row();

			$mnumnd = $this->datasis->fprox_numero('ndcli');

			if($row->financiable>0){
				$transac2= $this->datasis->fprox_numero('ntransa');
				$mnumnc  = $this->datasis->fprox_numero('nccli');
				$data=array();
				$data['cod_cli']    = $row->cliente;
				$data['nombre']     = $row->nombre;
				$data['tipo_doc']   = 'NC';
				$data['numero']     = $mnumnc;
				$data['fecha']      = $row->fecha;
				$data['monto']      = $row->financiable;
				$data['impuesto']   = 0;
				$data['abonos']     = $row->financiable;
				$data['vence']      = $row->fecha;
				$data['tipo_ref']   = 'PV';
				$data['num_ref']    = $row->numero;
				$data['observa1']   = 'PREVENTA '.$row->local;
				$data['estampa']    = $estampa;
				$data['hora']       = $hora;
				$data['transac']    = $transac2;
				$data['usuario']    = $usuario;
				$data['codigo']     = 'NOCON';
				$data['descrip']    = 'NOTA DE CONTABILIDAD';

				$mSQL = $this->db->insert_string('smov', $data);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'edcont'); }

				$sel=array('a.*');
				$this->db->select($sel);
				$this->db->where('a.id_edcont',$id);
				$this->db->from('itedcont AS a');
				$itquery = $this->db->get();

				foreach ($itquery->result() as $rrow){
					$mnumnd = $this->datasis->fprox_numero('ngicli');
					$data=array();
					$data['cod_cli']    = $row->cliente;
					$data['nombre']     = $row->nombre;
					$data['tipo_doc']   = 'GI';
					$data['numero']     = $mnumnd;
					$data['fecha']      = $row->fecha;
					$data['monto']      = $rrow->monto;
					$data['impuesto']   = 0;
					$data['abonos']     = 0;
					$data['vence']      = $rrow->vencimiento;
					$data['tipo_ref']   = 'PV';
					$data['num_ref']    = $row->numero;
					$data['observa1']   = 'PREVENTA '.$row->local;
					$data['estampa']    = $estampa;
					$data['hora']       = $hora;
					$data['transac']    = $transac2;
					$data['usuario']    = $usuario;
					$data['codigo']     = 'NOCON';
					$data['descrip']    = 'NOTA DE CONTABILIDAD';

					$mSQL = $this->db->insert_string('smov', $data);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'edcont'); }
					$vencimiento= $rrow->vencimiento;
				}
			}

			$data=array();
			$data['cod_cli']    = $row->cliente;
			$data['nombre']     = $row->nombre;
			$data['tipo_doc']   = 'ND';
			$data['numero']     = $mnumnd;
			$data['fecha']      = $row->fecha;
			$data['monto']      = $row->monto;
			$data['impuesto']   = 0;
			$data['abonos']     = $row->financiable;
			$data['vence']      = (isset($vencimiento))? $vencimiento : $row->fecha;
			$data['tipo_ref']   = 'PV';
			$data['num_ref']    = $row->numero;
			$data['observa1']   = 'PREVENTA '.$row->local;
			$data['estampa']    = $estampa;
			$data['hora']       = $hora;
			$data['transac']    = $transac;
			$data['usuario']    = $usuario;
			$data['codigo']     = 'NOCON';
			$data['descrip']    = 'NOTA DE CONTABILIDAD';

			$mSQL = $this->db->insert_string('smov', $data);
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'edcont'); }

			//Aplica la NC a la ND
			if($row->financiable>0){
				$data=array();
				$data['numccli']    = $mnumnd ;
				$data['tipoccli']   = 'ND';
				$data['cod_cli']    = $row->cliente;
				$data['tipo_doc']   = 'NC';
				$data['numero']     = $mnumnc;
				$data['fecha']      = $row->fecha;
				$data['monto']      = $row->monto;
				$data['abono']      = $row->financiable;
				$data['ppago']      = 0;
				$data['reten']      = 0;
				$data['cambio']     = 0;
				$data['mora']       = 0;
				$data['transac']    = $transac;
				$data['estampa']    = $estampa;
				$data['hora']       = $hora;
				$data['usuario']    = $usuario;
				$data['reteiva']    = 0;
				$data['nroriva']    = '';
				$data['emiriva']    = '';
				$data['recriva']    = '';

				$mSQL = $this->db->insert_string('itccli', $data);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'edcont');}
			}

			$this->db->where('id', $id);
			$this->db->update('edcont',array('status' => 'A'));
		}
		redirect($back);
	}

	function _pre_insert($do){
		$rel='itedcont';
		$cana  = $do->count_rel($rel);
		for($i=0;$i < $cana;$i++){
			$itmonto  = $do->get_rel($rel, 'monto', $i);
			if($itmonto<=0){
				$do->rel_rm($rel,$i);
			}
		}

		$numero =$this->datasis->fprox_numero('nedcont');
		$do->set('numero' ,$numero);
		$do->set('status' ,'P');
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
		if (!$this->db->table_exists('edcont')) {
			$mSQL="CREATE TABLE `edcont` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`id_edres` int(11) DEFAULT '0',
				`numero_edres` char(8) DEFAULT NULL,
				`numero` char(8) DEFAULT NULL,
				`fecha` date DEFAULT NULL,
				`cliente` char(5) DEFAULT NULL,
				`edificacion` int(11) DEFAULT '0',
				`inmueble` int(11) DEFAULT '0',
				`inicial` decimal(17,2) DEFAULT '0.00',
				`financiable` decimal(17,2) DEFAULT '0.00',
				`firma` decimal(17,2) DEFAULT '0.00',
				`precioxmt2` decimal(17,2) DEFAULT '0.00',
				`mt2` decimal(17,2) DEFAULT '0.00',
				`monto` decimal(17,2) DEFAULT '0.00',
				`notas` text,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COMMENT='Reserva de Inmuebles'";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('uso', 'edcont')){
			$mSQL="ALTER TABLE `edcont` ADD COLUMN `uso` INT(11) NOT NULL AFTER `notas`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('status', 'edcont')){
			$mSQL="ALTER TABLE `edcont` ADD COLUMN `status` CHAR(1) NOT NULL DEFAULT 'P' AFTER `numero`";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('itedcont')) {
			$mSQL="CREATE TABLE `itedcont` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `id_edcont` int(11) NOT NULL,
			  `vencimiento` date NOT NULL,
			  `monto` decimal(10,2) NOT NULL,
			  PRIMARY KEY (`id`),
			 KEY `id_edcont` (`id_edcont`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('especial', 'itedcont')){
			$mSQL= "ALTER TABLE `itedcont` ADD COLUMN `especial` CHAR(1) NOT NULL DEFAULT 'N' AFTER `id_edcont`";
		}

	}

}
