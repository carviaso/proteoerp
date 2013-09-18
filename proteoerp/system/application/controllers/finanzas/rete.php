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

	//***************************
	//Funciones de los Botones
	//***************************
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

	//***************************
	//Definicion del Grid y la Forma
	//***************************
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
			//'editrules'     => '{ required:true}',
			//'editoptions'   => '{ size:30, maxlength: 15 }',
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

	/**
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

		//$edit->pre_process( 'insert','_pre_insert' );
		//$edit->pre_process( 'update','_pre_update' );
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



/*
 class Rete extends validaciones {

	var $data_type = null;
	var $data = null;

	function rete (){
		parent::Controller();
		$this->load->library('pi18n');
		$this->load->library('rapyd');
	}

	function index(){
		if ( !$this->datasis->iscampo('rete','id') ) {
			$this->db->simple_query('ALTER TABLE rete DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE rete ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE rete ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE rete ADD COLUMN concepto VARCHAR(5) NULL ');
		}

		$this->datasis->modulo_id(515,1);
		if($this->pi18n->pais=='COLOMBIA'){
			redirect('finanzas/retecol/filteredgrid');
		}else{
			//redirect("finanzas/rete/filteredgrid");
			$this->reteextjs();
		}
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter('Filtro por C&oacute;digo', 'rete');
		$filter->codigo = new inputField('C&oacute;digo', 'codigo');
		$filter->codigo->size=15;

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('finanzas/rete/dataedit/show/<#codigo#>','<#codigo#>');

		$grid = new DataGrid("Lista de Retenciones");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;

		$grid->column("C&oacute;digo",$uri);
		$grid->column("Pago de","activida");
		$grid->column("Base Imponible","base1");
		$grid->column("Porcentaje de Retenci&oacute;n","tari1");
		$grid->column("Para pagos mayores a","pama1");
		$grid->column("Tipo","tipo");

		$grid->add("finanzas/rete/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Retenciones</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
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
		});
		';

		$edit = new DataEdit("Retenciones", "rete");
		$edit->back_url = site_url("finanzas/rete/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->mode="autohide";
		$edit->codigo->size =7;
		$edit->codigo->maxlength=4;
		$edit->codigo->rule ="required|callback_chexiste";

		$edit->activida = new inputField("Pago de", "activida");
		$edit->activida->size =55;
		$edit->activida->maxlength=45;
		$edit->activida->rule= "strtoupper|required";

		$edit->tipo =  new dropdownField("Tipo", "tipo");
		$edit->tipo->option("JD","JD");
		$edit->tipo->option("JN","JN");
		$edit->tipo->option("NN","NN");
		$edit->tipo->option("NR","NR");
		$edit->tipo->style='width:60px';

		$edit->base1 = new inputField("Base Imponible", "base1");
		$edit->base1->size =13;
		$edit->base1->maxlength=9;
		$edit->base1->css_class='inputnum';
		$edit->base1->rule='numeric';

		$edit->tari1 =new inputField("Porcentaje de Retenci&oacute;n", "tari1");
		$edit->tari1->size =13;
		$edit->tari1->maxlength=10;
		$edit->tari1->css_class='inputnum';
		$edit->tari1->rule='numeric';

		$edit->pama1 = new inputField("Para pagos mayores a", "pama1");
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

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();


		$smenu['link']=barra_menu('515');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$data['content'] = $edit->output;
		$data['title']   = "<h1>Retenciones</h1>";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	 }

	 function _post_insert($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('activida');
		logusu('rete',"RETENCION $codigo $nombre CREADO");
	 }

	 function _post_update($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('activida');
		logusu('rete',"RETENCION $codigo $nombre MODIFICADO");
	 }

	 function _post_delete($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('activida');
		logusu('rete',"RETENCION $codigo $nombre  ELIMINADO ");
	 }

	 function chexiste($codigo){
		$codigo=$this->input->post('codigo');
		//echo 'aquiii'.$fecha;
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM rete WHERE codigo='$codigo'");
		if ($check > 0){
		 $activida=$this->datasis->dameval("SELECT activida FROM rete WHERE codigo='$codigo'");
		 $this->validation->set_message('chexiste',"La retencion $codigo ya existe para la actividad $activida");
		 return FALSE;
		}else {
		return TRUE;
		}
	 }

	 function instalar(){
		if (!$this->db->field_exists('ut','rete')) {
		 $mSQL="ALTER TABLE rete CHANGE COLUMN tipocol tipocol CHAR(2) NULL DEFAULT '0.0' COLLATE 'utf8_unicode_ci' AFTER cuenta, ADD COLUMN ut DECIMAL(12,2) NULL DEFAULT NULL AFTER tipocol";
		 $this->db->simple_query($mSQL);
		}
	 }


	function grid(){
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 50;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : '[{"property":"tipo","direction":"ASC"},{"property":"codigo","direction":"ASC"}]';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;

		$where = $this->datasis->extjsfiltro($filters);

		$this->db->_protect_identifiers=false;
		$this->db->select("*, IF(tipo='JD','Juridico Domiciliado', IF(tipo='JN','Juridico No Domiciliado', IF(tipo='NR','Natural Residente','Natural No Residente'))) persona");
		$this->db->from('rete');
		if (strlen($where)>1) $this->db->where($where, NULL, FALSE);

		$sort = json_decode($sort, true);
		for ( $i=0; $i<count($sort); $i++ ) {
			$this->db->order_by($sort[$i]['property'],$sort[$i]['direction']);
		}

		$this->db->limit($limit, $start);
		$query = $this->db->get();
		$results = $this->db->count_all('rete');

		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data", results:'. $results.', data:'.json_encode($arr).'}';
	}

	function crear(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos   = $data['data'];
		$codigo = $campos['codigo'];

		if ( !empty($codigo) ) {
			unset($campos['id']);
			unset($campos['persona']);
			// Revisa si existe ya ese contrato
			if ($this->datasis->dameval("SELECT COUNT(*) FROM rete WHERE codigo='$codigo'") == 0)
			{
				$mSQL = $this->db->insert_string("rete", $campos );
				$this->db->simple_query($mSQL);
				logusu('rete',"RETENCION $codigo CREADO");
				echo "{ success: true, message: 'Retencion Agregada'}";
			} else {
				echo "{ success: false, message: 'Ya existe una retencion con ese codigo!!'}";
			}

		} else {
			echo "{ success: false, message: 'Falta el campo codigo!!'}";
		}
	}

	function modificar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];

		$codigo = $campos['codigo'];
		unset($campos['codigo']);
		unset($campos['id']);
		unset($campos['persona']);

		$mSQL = $this->db->update_string("rete", $campos,"id=".$data['data']['id'] );
		$this->db->simple_query($mSQL);
		logusu('rete',"RETENCION $codigo ID ".$data['data']['id']." MODIFICADO");
		echo "{ success: true, message: 'Retencion Modificada -> ".$data['data']['codigo']."'}";
	}

	function eliminar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];

		$codigo = $campos['codigo'];
		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM gser WHERE creten='$codigo'");

		if ($check > 0){
			echo "{ success: false, message: 'rete no puede ser Borrada'}";
		} else {
			$this->db->simple_query("DELETE FROM rete WHERE codigo='$codigo'");
			logusu('rete',"rete $codigo ELIMINADO");
			echo "{ success: true, message: 'rete Eliminada'}";
		}
	}




//****************************************************************8
//
//
//
//****************************************************************8
	function reteextjs(){
		$encabeza='RETENCION';
		$listados= $this->datasis->listados('rete');
		$otros=$this->datasis->otros('rete', 'finanzas/rete');

		$urlajax = 'finanzas/rete/';
		$variables = "var mcuenta='';";

		$tipos="['JD', 'Juridico Domiciliado'],['JN','Juridico No Domiciliado'],['NR','Natural Residente'],['NN','Natural No Res.']";


		$funciones = "";

		$valida = "
		{ type: 'length', field: 'codigo',   min: 1 },
		{ type: 'length', field: 'activida', min: 1 }
		";

		$columnas = "
		{ header: 'codigo',     width:  50, sortable: true, dataIndex: 'codigo',   field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Actividad',  width: 300, sortable: true, dataIndex: 'activida', field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Base Imp.',  width:  50, sortable: true, dataIndex: 'base1',    field: { type: 'numeric'   }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00') },
		{ header: 'Ret.%',      width:  50, sortable: true, dataIndex: 'tari1',    field: { type: 'numeric'   }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00') },
		{ header: 'Exencion',   width:  70, sortable: true, dataIndex: 'pama1',    field: { type: 'numeric'   }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00') },
		{ header: 'Cuenta',     width:  80, sortable: true, dataIndex: 'cuenta',   field: { type: 'textfield' }, filter: { type: 'string'  }},
		{ header: 'Concepto',   width:  80, sortable: true, dataIndex: 'concepto', field: { type: 'textfield' }, filter: { type: 'string'  }},
	";

		$campos = "'id', 'codigo','activida','base1','tari1','pama1','tipo','cuenta', 'persona','concepto'";

		$camposforma = "
							{
							xtype:'fieldset',
							frame: false,
							border: false,
							labelAlign: 'right',
							defaults: { xtype:'fieldset', labelWidth:70 },
							style:'padding:4px',
							layout: 'column',
							items: [
									{ xtype: 'textfield',   fieldLabel: 'Codigo',     name: 'codigo',   width:110, allowBlank: false, id: 'codigo' },
									{ xtype: 'combo',       fieldLabel: 'Tipo',       name: 'tipo',     width:270, store: [".$tipos."], labelWidth:70},
									{ xtype: 'textfield',   fieldLabel: 'Actividad',  name: 'activida', width:400, allowBlank: false },
									{ xtype: 'combo',       fieldLabel: 'C.Contable', name: 'cuenta',   width:400, store: cplaStore, id: 'cuenta', mode: 'remote', hideTrigger: true, typeAhead: true, forceSelection: true, valueField: 'item', displayField: 'valor'},
									{ xtype: 'textfield',   fieldLabel: 'Concepto',   name: 'concepto', width:120, allowBlank: true },
								]
							},{
							xtype:'fieldset',
							title: 'Valores',
							frame: false,
							border: false,
							labelAlign: 'right',
							defaults: { xtype:'fieldset', labelWidth:170 },
							style:'padding:4px',
							layout: 'column',
							items: [
									{ xtype: 'numberfield', fieldLabel: 'Base Imponible',  name: 'base1', width:260, hideTrigger: true, fieldStyle: 'text-align: right',  renderer : Ext.util.Format.numberRenderer('0,000.00') },
									{ xtype: 'numberfield', fieldLabel: 'Retencion %',     name: 'tari1', width:260, hideTrigger: true, fieldStyle: 'text-align: right',  renderer : Ext.util.Format.numberRenderer('0,000.00') },
									{ xtype: 'numberfield', fieldLabel: 'Pagos mayores a', name: 'pama1', width:260, hideTrigger: true, fieldStyle: 'text-align: right',  renderer : Ext.util.Format.numberRenderer('0,000.00') },
								]
							}
		";

		$titulow = 'Formas de Pago';

		$winwidget = "
				closable: false,
				closeAction: 'destroy',
				width: 450,
				height: 340,
				resizable: false,
				modal: true,
				items: [writeForm],
				listeners: {
					beforeshow: function() {
						var form = this.down('writerform').getForm();
						this.activeRecord = registro;
						if (registro) {
							mcuenta  = registro.data.cuenta;
							cplaStore.proxy.extraParams.cuenta  = mcuenta  ;
							cplaStore.load({ params: { 'cuenta': registro.data.cuenta,  'origen': 'beforeform' } });
							form.loadRecord(registro);
							form.findField('codigo').setReadOnly(true);
						} else {
							form.findField('codigo').setReadOnly(false);
						}
					}
				}
";

		$stores = "
var cplaStore = new Ext.data.Store({
	fields: [ 'item', 'valor'],
	autoLoad: false,
	autoSync: false,
	pageSize: 50,
	pruneModifiedRecords: true,
	totalProperty: 'results',
	proxy: {
		type: 'ajax',
		url : urlApp + 'contabilidad/cpla/cplabusca',
		extraParams: {  'cuenta': mcuenta, 'origen': 'store' },
		reader: {type: 'json',totalProperty: 'results',root: 'data'}
	},
	method: 'POST'
});
		";

		$features = "features: [{ ftype: 'grouping', groupHeaderTpl: '{name}' },{ ftype: 'filters', encode: 'json', local: false }],";

		$agrupar = "		remoteSort: true,
		groupField: 'persona',";

		$data['listados']    = $listados;
		$data['otros']       = $otros;
		$data['encabeza']    = $encabeza;
		$data['urlajax']     = $urlajax;
		$data['variables']   = $variables;
		$data['funciones']   = $funciones;
		$data['valida']      = $valida;
		$data['columnas']    = $columnas;
		$data['campos']      = $campos;
		$data['stores']      = $stores;
		$data['camposforma'] = $camposforma;
		$data['titulow']     = $titulow;
		$data['winwidget']   = $winwidget;
		$data['features']    = $features;
		$data['agrupar']     = $agrupar;

		$data['title']  = heading('Retenciones');
		$this->load->view('extjs/extjsven',$data);

	}

}
*/
?>
