<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/validaciones.php');
class Grcl extends Controller {
	var $mModulo='GRCL';
	var $titp='Grupo de Clientes';
	var $tits='Grupo de Clientes';
	var $url ='ventas/grcl/';

	function Grcl(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'GRCL', $ventana=0 );
	}

	function index(){
		$this->instalar();
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 680, 500, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	//Layout en la Ventana
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
		$param['listados']    = $this->datasis->listados('GRCL', 'JQ');
		$param['otros']       = $this->datasis->otros('GRCL', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	//Funciones de los Botones
	//
	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';
		$ngrid = '#newapi'.$grid0;

		$bodyscript .= $this->jqdatagrid->bsshow('grcl', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'grcl', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'grcl', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('grcl', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '200', '370' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '190', '360' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '200', '300' );

		$bodyscript .= '});';

		$bodyscript .= '</script>';
		return $bodyscript;
	}

	//******************************************************************
	//Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'true';
		$link   = site_url('ajax/buscacpla');

		$grid  = new $this->jqdatagrid;

		$grid->addField('grupo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 4 }',
		));

		$grid->addField('gr_desc');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 25 }',
		));

		$grid->addField('clase');
		$grid->label('Clase');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{value: {"C":"Clientes","O":"Otros","I":"Internos" }, style:"width:100px" }'
		));

		$grid->addField('cuenta');
		$grid->label('Cuenta');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{'.$grid->autocomplete($link, 'cuenta','cucucu','<div id=\"cucucu\"><b>"+ui.item.descrip+"</b></div>').'}',
		));

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'hidden'        => 'true',
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('235');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true,mtype: "POST",width: 420,height:200,closeOnEscape: true,top: 50,left:20,recreateForm:true,afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});}');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 420, height:200, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('GRCL','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('GRCL','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('GRCL','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('GRCL','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: grcladd, editfunc: grcledit, delfunc: grcldel, viewfunc: grclshow');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdata/'));


		$grid->setOnSelectRow('
			function(id){
				$.post("'.site_url($this->url.'canascli').'/"+encodeURIComponent(id),
				function(data){
					var cana=Number(data);
					var msj ="";
					if(cana>1){
						msj = "<b>"+data+"</b> clientes asociados";
					}else if(cana==1){
						msj = "<b>"+data+"</b> cliente asociado";
					}else{
						msj = "Ning&uacute;n cliente asociado";
					}
					$("#ladicional").html("<p style=\'text-align:center;\'>"+msj+"</p>");
				});
			}');

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
		$mWHERE = $grid->geneTopWhere('grcl');

		$response   = $grid->getData('grcl', array(array()), array(), false, $mWHERE, 'grupo' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/*******************************************************************
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
				$check = $this->datasis->dameval("SELECT count(*) FROM grcl WHERE grupo=".$this->db->escape($data['grupo']));
				if($check == 0){
					$this->db->insert('grcl', $data);
					echo 'Registro Agregado';
					logusu('GRCL','Grupo de Cliente '.$data['grupo'].' INCLUIDO');
				}else{
					echo 'Ya existe un grupo con ese Codigo';
				}
			}else{
				echo 'Fallo Agregado!!!';
			}
		}elseif($oper == 'edit') {
			$grupo  = $data['grupo'];
			$grupov = $this->datasis->dameval("SELECT grupo FROM grcl WHERE id=${id}");
			if($grupo <> $grupov){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM grcl WHERE grupo=?", array($grupo));
				$this->db->query("UPDATE scli SET grupo=? WHERE grupo=?", array( $grupo, $grupov ));
				$this->db->where('id', $id);
				$this->db->update('grcl', $data);
				logusu('GRCL',"Grupo Cambiado/Fusionado Nuevo:${grupo} Anterior: ${grupov} MODIFICADO");
				echo 'Grupo Cambiado/Fusionado en clientes';
			}else{
				unset($data['grupo']);
				$this->db->where('id', $id);
				$this->db->update('grcl', $data);
				logusu('GRCL',"Grupo de Cliente ${grupo} MODIFICADO");
				echo 'Grupo Modificado';
			}

		}elseif($oper == 'del'){
			$grupo = $this->datasis->dameval("SELECT grupo FROM grcl WHERE id=${id}");
			$check = $this->datasis->dameval("SELECT COUNT(*) AS cana FROM scli WHERE grupo=".$this->db->escape($grupo));
			if($check > 0){
				echo ' El grupo no puede ser eliminado; tiene clientes asociados ';
			}else{
				$this->db->simple_query("DELETE FROM grcl WHERE id=${id}");
				logusu('GRCL','Grupo de Cliente '.$grupo.' ELIMINADO');
				echo 'Grupo Eliminado';
			}
		}
	}

	//******************************************************************
	//
	//
	function dataedit(){
		$this->rapyd->load('dataedit');

		$qformato=$this->qformato=$this->datasis->formato_cpla();
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

		$edit = new DataEdit('', 'grcl');
		$edit->on_save_redirect=false;
		$edit->script($script,'modify');
		$edit->script($script,'create');

		$edit->pre_process( 'delete','_pre_delete' );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->grupo = new inputField('C&oacute;digo', 'grupo');
		$edit->grupo->mode ='autohide';
		$edit->grupo->rule ='trim|required|max_length[4]|callback_chexiste|alpha_numeric';
		$edit->grupo->size =5;
		$edit->grupo->maxlength =4;

		$edit->clase = new dropdownField('Clase', 'clase');
		$edit->clase->option('','Seleccionar');
		$edit->clase->options(array('C'=> 'Cliente','O'=>'Otros','I'=>'Internos'));
		$edit->clase->rule ='required';
		$edit->clase->style='width:100px;';

		$edit->gr_desc = new inputField('Descripci&oacute;n', 'gr_desc');
		$edit->gr_desc->size =30;
		$edit->gr_desc->maxlength =25;
		$edit->gr_desc->rule= 'required|strtoupper';

		$edit->cuenta = new inputField('Cta. Contable', 'cuenta');
		$edit->cuenta->rule= 'existecpla';
		$edit->cuenta->size =20;
		$edit->cuenta->maxlength =15;
		$edit->cuenta->append($bcpla);

		//$edit->buttons("modify", "save", "undo", "delete", "back");
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

	function _pre_delete($do) {
		$grupo  =$do->get('grupo');
		$dbgrupo=$this->db->escape($grupo);
		$check = intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM scli WHERE grupo=${dbgrupo}"));
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Grupo con clientes asociados, no puede ser Borrado';
			return false;
		}else	{
			return true;
		}
	}

	function _post_insert($do){
		$codigo=$do->get('grupo');
		logusu('grcl',"GRUPO ${codigo} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('grupo');
		logusu('grcl',"GRUPO ${codigo} MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('grupo');
		logusu('grcl',"GRUPO ${codigo} ELIMINADO");
	}

	function chexiste($codigo){
		$codigo  =$this->input->post('grupo');
		$dbcodigo=$this->db->escape($codigo);
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM grcl WHERE grupo=${dbcodigo}");
		if ($check > 0){
			$grupo=$this->datasis->dameval("SELECT gr_desc FROM grcl WHERE grupo=${dbcodigo}");
			$this->validation->set_message('chexiste',"El codigo ${codigo} ya existe para el grupo ${grupo}");
			return false;
		}else {
			return true;
		}
	}

	function canascli($id=null){
		$id=intval($id);
		if($id>0){
			$grupo   = $this->datasis->dameval('SELECT grupo FROM grcl WHERE id='.$id);
			$dbgrupo = $this->db->escape($grupo);
			$cana    = intval($this->datasis->dameval('SELECT COUNT(*) AS cana FROM scli WHERE grupo='.$dbgrupo));
			echo $cana;
		}
	}

	function instalar(){
		$campos=$this->db->list_fields('grcl');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE grcl DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE grcl ADD UNIQUE INDEX grupo (grupo)');
			$this->db->simple_query('ALTER TABLE grcl ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');

		}
	}
}
