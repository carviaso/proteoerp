<?php
class Grpr extends Controller {
	var $mModulo='GRPR';
	var $titp='Grupo de Proveedores';
	var $tits='Grupo de Proveedores';
	var $url ='compras/grpr/';

	function Grpr(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'GRPR', $ventana=0 );
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
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita', 'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' , 'title'=>'Mostrar Registro'),
			array('id'=>'fborra', 'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		//$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('GRPR', 'JQ');
		$param['otros']       = $this->datasis->otros('GRPR', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('grpr', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'grpr', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'grpr', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('grpr', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '250', '500' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '250', '500' );
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

		$grid  = new $this->jqdatagrid;

		$grid->addField('grupo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('gr_desc');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:25, maxlength: 25 }',
		));


		$grid->addField('cuenta');
		$grid->label('Cuenta Contable');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
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
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('GRPR','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('GRPR','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('GRPR','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('GRPR','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: grpradd, editfunc: grpredit, delfunc: grprdel, viewfunc: grprshow');

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
		$mWHERE = $grid->geneTopWhere('grpr');

		$response   = $grid->getData('grpr', array(array()), array(), false, $mWHERE );
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
		$link=site_url($this->url.'ultimo');

		$script='
		function ultimo(){
			$.ajax({
				url: "'.$link.'",
				success: function(msg){
				  alert( "El ultimo codigo ingresado fue: " + msg );
				}
			});
		}

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

		$edit = new DataEdit('', 'grpr');
		$edit->on_save_redirect=false;
		$edit->script($script,'modify');
		$edit->script($script,'create');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );

		$lgrup='<a href="javascript:ultimo();" title="Consultar &uacute;ltimo grupo ingresado" onclick="">Consultar &uacute;ltimo grupo</a>';
		$edit->grupo = new inputField('Grupo', 'grupo');
		$edit->grupo->mode ='autohide';
		$edit->grupo->size=7;
		$edit->grupo->maxlength =4;
		$edit->grupo->rule = 'trim|required|strtoupper|callback_chexiste|alpha_numeric';
		$edit->grupo->append($lgrup);

		$edit->gr_desc = new inputField('Descripci&oacute;n','gr_desc');
		$edit->gr_desc->size=35;
		$edit->gr_desc->maxlength =25;
		$edit->gr_desc->rule = 'trim|strtoupper|required';

		$edit->cuenta = new inputField('Cta. Contable','cuenta');
		$edit->cuenta->size=18;
		$edit->cuenta->maxlength =15;
		$edit->cuenta->rule ='trim|existecpla';

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
		$grupo  =$this->db->escape($do->data['grupo']);
		$dbgrupo=$this->db->escape($grupo);
		$resulta=$this->datasis->dameval("SELECT COUNT(*) AS cana FROM sprv WHERE grupo=${dbgrupo}");
		if ($resulta==0){
			return true;
		}else{
			$do->error_message_ar['pre_del']="No se puede borrar el registro ya que hay proveedores que pertenecen a este grupo";
			return false;
		}
	}

	function _post_insert($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('gr_desc');
		logusu('grpr',"GRUPO ${codigo} NOMBRE ${nombre} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('gr_desc');
		logusu('grpr',"GRUPO ${codigo} NOMBRE ${nombre} MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('gr_desc');
		logusu('grpr',"GRUPO ${codigo} NOMBRE ${nombre} ELIMINADO");
	}

	function chexiste($codigo){
		$codigo  =$this->input->post('grupo');
		$dbcodigo=$this->db->escape($codigo);
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM grpr WHERE grupo=${dbcodigo}");
		if ($check > 0){
			$nombre=$this->datasis->dameval("SELECT gr_desc FROM grpr WHERE grupo=${dbcodigo}");
			$this->validation->set_message('chexiste',"El codigo ${codigo} ya existe para el grupo ${nombre}");
			return false;
		}else {
			return true;
		}
	}

	function ultimo(){
		$consulgrupo=$this->datasis->dameval('SELECT MAX(grupo) FROM grpr');
		echo $consulgrupo;
	}

	function instalar(){
		//if (!$this->db->table_exists('grpr')) {
		//	$mSQL="CREATE TABLE `grpr` (
		//	  `grupo` varchar(4) NOT NULL DEFAULT '',
		//	  `gr_desc` varchar(25) DEFAULT NULL,
		//	  `cuenta` varchar(15) DEFAULT NULL,
		//	  `id` int(11) NOT NULL AUTO_INCREMENT,
		//	  PRIMARY KEY (`id`),
		//	  UNIQUE KEY `grupo` (`grupo`)
		//	) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1";
		//	$this->db->simple_query($mSQL);
		//}

		$campos=$this->db->list_fields('grpr');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE grpr DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE grpr ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE grpr ADD UNIQUE INDEX grupo (grupo)');
		}
	}
}
