<?php require_once(BASEPATH.'application/controllers/validaciones.php');
//retenciones
class Rete extends Controller {
	var $mModulo='RETE';
	var $titp='Codigos Retenciones de ISLR';
	var $tits='Codigos Retenciones de ISLR';
	var $url ='finanzas/rete/';

	function Rete(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'RETE', $ventana=0 );
	}

	function index(){
		$this->instalar();
		//$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
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
		$param['listados']    = $this->datasis->listados('RETE', 'JQ');
		$param['otros']       = $this->datasis->otros('RETE', 'JQ');
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
		$ngrid      = '#newapi'.$grid0;

		$bodyscript .= $this->jqdatagrid->bsshow('rete', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'rete', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'rete', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('rete', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '350', '500' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '350', '500' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

		$bodyscript .= '});';
		$bodyscript .= '</script>';
		return $bodyscript;
	}

	//******************************************************************
	// Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';
		$link  = site_url('ajax/buscacpla');

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 4 }',
		));

		$grid->addField('activida');
		$grid->label('Actividad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 45 }',
		));

		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'false',
			'editable'      => $editar,
			'width'         => 40,
			'editrules'     => '{ required:true}',
			'edittype'      => "'select'",
			'search'        => 'true',
			'editoptions'   => '{value: {"JD":"Juridico Domiciliado", "JN":"Juridico No Domiciliado", "NR":"Natural Residente","NN":"Natural No Residente"} }'

		));

		$grid->addField('base1');
		$grid->label('Base');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 70,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('tari1');
		$grid->label('%Retenci&oacute;n');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 70,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('pama1');
		$grid->label('Exenci&oacute;n');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 70,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('cuenta');
		$grid->label('Cta. Contable');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editoptions' => '{'.$grid->autocomplete($link, 'cuenta','cucucu','<div id=\"cucucu\"><b>"+ui.item.descrip+"</b></div>').'}',
		));

		$grid->addField('concepto');
		$grid->label('Concepto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));


		$grid->addField('tipodesc');
		$grid->label('Persona');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'false',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
		));


		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 60,
			'editable' => 'false',
			'search'   => 'false',
			'hidden'   => 'true'
		));

		$grid->setGrouping('tipodesc');

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('370');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		$grid->setOndblClickRow('');
		$grid->setAdd(    $this->datasis->sidapuede('RETE','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('RETE','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('RETE','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('RETE','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: reteadd, editfunc: reteedit, delfunc: retedel, viewfunc: reteshow');

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

	/*******************************************************************
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('rete');
		$mSQL = "
		SELECT codigo, activida, base1, tari1, pama1, tipo, cuenta, concepto, id,
		CONCAT(tipo,' ',if(tipo='JD','Juridico Domiciliado',
		  if(tipo='JN','Juridico No Domiciliado',
		    if(tipo='NR','Natural Domiciliado','Natural No Domiciliado')
		  )
		)) tipodesc FROM rete ORDER BY tipo, codigo
		";


		$response   = $grid->getData('view_rete', array(array()), array(), false, $mWHERE, 'tipo, codigo' );
		//$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$qformato=$this->datasis->formato_cpla();

		$mCPLA=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'cuenta'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
			);

		$bcpla =$this->datasis->modbus($mCPLA);

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
			$("#cuenta").autocomplete({
				delay: 600,
				autoFocus: true,
				source: function( req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscacpla').'",
						type: "POST",
						dataType: "json",
						data: {"q":req.term},
						success:
							function(data){
								var sugiere = [];
								$.each(data,
									function(i, val){
										sugiere.push( val );
									}
								);
								add(sugiere);
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$("#cuenta").val(ui.item.codigo);
				}
			});
		});';

		$edit = new DataEdit('', 'rete');
		$edit->on_save_redirect=false;
		$edit->script($script, 'create');
		$edit->script($script, 'modify');

		$edit->pre_process( 'delete','_pre_delete' );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo = new inputField('C&oacute;digo', 'codigo');
		$edit->codigo->mode='autohide';
		$edit->codigo->size =7;
		$edit->codigo->maxlength=4;
		$edit->codigo->rule ='required|callback_chexiste';

		$edit->activida = new inputField('Actividad', 'activida');
		$edit->activida->size =40;
		$edit->activida->maxlength=45;
		$edit->activida->rule= 'strtoupper|required';

		$edit->tipo =  new dropdownField('Tipo', 'tipo');
		$edit->tipo->option('JD','Juridico Domiciliado');
		$edit->tipo->option('JN','Juridico No Domiciliado');
		$edit->tipo->option('NN','Natural No Residente');
		$edit->tipo->option('NR','Natural Residente');
		$edit->tipo->style='width:150px';
		$edit->tipo->rule='required|enum[JD,JN,NN,NR]';

		$edit->base1 = new inputField('Base Imponible', 'base1');
		$edit->base1->size =13;
		$edit->base1->maxlength=9;
		$edit->base1->css_class='inputnum';
		$edit->base1->rule='numeric';

		$edit->tari1 =new inputField('Porcentaje de Retenci&oacute;n', 'tari1');
		$edit->tari1->size =13;
		$edit->tari1->maxlength=10;
		$edit->tari1->css_class='inputnum';
		$edit->tari1->rule='numeric|porcent';

		$edit->pama1 = new inputField('Para pagos mayores a', 'pama1');
		$edit->pama1->size =13;
		$edit->pama1->maxlength=13;
		$edit->pama1->css_class='inputnum';
		$edit->pama1->rule='numeric';

		$edit->concepto = new inputField('C&oacute;digo Concepto', 'concepto');
		$edit->concepto->size =5;
		$edit->concepto->maxlength=5;

		$edit->cuenta = new inputField('Contable', 'cuenta');
		$edit->cuenta->rule='trim|existecpla';
		$edit->cuenta->append($bcpla);
		$edit->cuenta->size=15;
		$edit->cuenta->maxlength =15;

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

	function _pre_delete($do){
		$codigo=$this->db->escape($do->get('codigo'));
		$check =  $this->datasis->dameval("SELECT COUNT(*) AS val FROM gser   WHERE creten     = ${codigo}");
		$check += $this->datasis->dameval("SELECT COUNT(*) AS val FROM gereten WHERE codigorete= ${codigo}");

		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Concepto con movimientos, no puede ser Borrado';
			return false;
		}
		return true;
	}

	function _post_insert($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('activida');
		logusu('rete',"RETENCION ${codigo} ${nombre} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('activida');
		logusu('rete',"RETENCION ${codigo} ${nombre} MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('activida');
		logusu('rete',"RETENCION ${codigo} ${nombre}  ELIMINADO ");
	}

	function chexiste($codigo){
		$codigo  =$this->input->post('codigo');
		$dbcodigo=$this->db->escape($codigo);
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM rete WHERE codigo=${dbcodigo}");
		if ($check > 0){
			$activida=$this->datasis->dameval("SELECT activida FROM rete WHERE codigo=${dbcodigo}");
			$this->validation->set_message('chexiste',"La retencion ${codigo} ya existe para la actividad ${activida}");
			return false;
		}else {
			return true;
		}
	 }


	function instalar(){
		$campos=$this->db->list_fields('rete');

		if(!in_array('concepto',$campos)){
			$this->db->simple_query('ALTER TABLE rete ADD COLUMN concepto VARCHAR(10) NULL ');
		}

		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE rete DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE rete ADD UNIQUE INDEX codigo (codigo)');
			$this->db->query('ALTER TABLE rete ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if(!in_array('ut',$campos)){
			$mSQL="ALTER TABLE rete ADD COLUMN ut DECIMAL(12,2) NULL DEFAULT NULL";
			$this->db->query($mSQL);
		}

		// Crea el View
		if (!$this->db->table_exists('view_rete')) {
			$mSQL = "
			CREATE ALGORITHM = UNDEFINED
			VIEW `view_rete` AS SELECT codigo, activida, base1, tari1, pama1, tipo, cuenta, concepto, id, CONCAT(tipo,' ',if(tipo='JD','Juridico Domiciliado',
			if(tipo='JN','Juridico No Domiciliado',	if(tipo='NR','Natural Domiciliado','Natural No Domiciliado')))) tipodesc
			FROM rete ;
			";
			$this->db->query($mSQL);
		}

	}
}

?>
