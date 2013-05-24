<?php require_once(BASEPATH.'application/controllers/validaciones.php');
class Grga extends Controller {
	var $mModulo='GRGA';
	var $titp='Grupo de Gastos';
	var $tits='Grupo de Gastos';
	var $url ='finanzas/grga/';

	function Grga(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'GRGA', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 680, 450, substr($this->url,0,-1) );
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

		//$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('GRGA', 'JQ');
		$param['otros']       = $this->datasis->otros('GRGA', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';
		$link  = site_url('ajax/buscacpla');

		$grid  = new $this->jqdatagrid;

		$grid->addField('grupo');
		$grid->label('Grupo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 4 }',
		));


		$grid->addField('nom_grup');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 25 }',
		));


		$grid->addField('cu_inve');
		$grid->label('Cta. Contable');
		$grid->params(array(
			'width'         => 120,
			'frozen'        => 'true',
			'editable'      => 'true',
			'edittype'      => "'text'",
			'editoptions'   => '{'.$grid->autocomplete($link, 'cu_inve','cucucu','<div id=\"cucucu\"><b>"+ui.item.descrip+"</b></div>').'}',
			'search'        => 'true'
		));

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 60,
			'editable' => $editar,
			'search'   => 'false',
			'hidden'   => 'true'
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('235');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 400, height:180, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 400, height:180, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		$grid->setOndblClickRow('');
		$grid->setAdd(    $this->datasis->sidapuede('GRGA','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('GRGA','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('GRGA','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('GRGA','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: grgaadd, editfunc: grgaedit, delfunc: grgadel, viewfunc: grgashow");

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

	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';
		$ngrid      = '#newapi'.$grid0;

		$bodyscript .= $this->jqdatagrid->bsshow('grga', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'grga', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'grga', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('grga', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '200', '450' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '200', '450' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '200', '400' );

		$bodyscript .= '});';
		$bodyscript .= '</script>';
		return $bodyscript;
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('grga');

		$response   = $grid->getData('grga', array(array()), array(), false, $mWHERE, 'grupo' );
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
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$grupo = $this->input->post('grupo');
				$this->db->insert('grga', $data);
				echo "Registro Agregado";

				logusu('GRGA',"Registro ".$grupo." INCLUIDO");
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$grupo = $this->input->post('grupo');
			unset($data['grupo']);
			$this->db->where('id', $id);
			$this->db->update('grga', $data);
			logusu('GRGA',"Registro ".$grupo." MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			$grupo = $this->input->post('grupo');
			$check = $this->datasis->dameval("SELECT COUNT(*) FROM mgas WHERE grupo='$grupo'");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM grga WHERE id=$id ");
				logusu('GRGA',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$script='
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
			$("#cu_inve").autocomplete({
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

		$edit = new DataEdit('', 'grga');
		$edit->on_save_redirect=false;
		$edit->script($script,'modify');
		$edit->script($script,'create');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->grupo =     new inputField('C&oacute;digo', 'grupo');
		$edit->grupo->mode="autohide";
		$edit->grupo->size = 6;
		$edit->grupo->rule = 'trim|strtoupper|required|callback_chexiste|alpha_numeric';
		$edit->grupo->maxlength=5;

		$edit->nom_grup =  new inputField('Nombre del Grupo', 'nom_grup');
		$edit->nom_grup->size = 35;
		$edit->nom_grup->rule = 'trim|strtoupper|required';
		$edit->nom_grup->maxlength=25;

		$edit->cu_inve =   new inputField('Cuenta Contable', 'cu_inve');
		$edit->cu_inve->size = 18;
		$edit->cu_inve->maxlength=15;
		$edit->cu_inve->rule = 'trim|existecpla';

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
		$resulta=$this->datasis->dameval("SELECT COUNT(*) AS cana FROM mgas WHERE grupo=${dbgrupo}");
		if ($resulta==0){
			return true;
		}else{
			$do->error_message_ar['pre_del']="No se puede borrar el registro ya que existen conceptos de gastos relacionados a este grupo";
			return false;
		}
	}

	function _post_insert($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('nom_grup');
		logusu('grga',"GRUPO DE GASTOS ${codigo} NOMBRE  ${nombre} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('nom_grup');
		logusu('grga',"GRUPO DE GASTOS ${codigo} NOMBRE  ${nombre}  MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('nom_grup');
		logusu('grga',"GRUPO DE GASTOS ${codigo} NOMBRE  ${nombre}  ELIMINADO ");
	}

	function chexiste($codigo){
		$codigo  =$this->input->post('grupo');
		$dbcodigo=$this->db->escape($codigo);
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM grga WHERE grupo=${dbcodigo}");
		if ($check > 0){
			$grupo=$this->datasis->dameval("SELECT nom_grup FROM grga WHERE grupo=${dbcodigo}");
			$this->validation->set_message('chexiste',"El codigo ${codigo} ya existe para el grupo ${grupo}");
			return false;
		}else {
			return true;
		}
	}

	function instalar(){
		$campos=$this->db->list_fields('grga');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE grga DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE grga ADD UNIQUE INDEX grupo (grupo)');
			$this->db->simple_query('ALTER TABLE grga ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}
	}

}
