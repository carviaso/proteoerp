<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
class Procamp extends Controller {
	var $mModulo = 'PROCAMP';
	var $titp    = 'CAMPANAS';
	var $tits    = 'CAMPANAS';
	var $url     = 'eventos/procamp/';

	function Procamp(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'PROCAMP', $ventana=0 );
	}

	function index(){
		$this->datasis->creaintramenu(array('modulo'=>'160','titulo'=>'Campanas','mensaje'=>'Campanas de promocion','panel'=>'PROMOCIONES','ejecutar'=>'eventos/procamp','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) ); 
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	// Layout en la Ventanas
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
		$param['listados']    = $this->datasis->listados('PROCAMP', 'JQ');
		$param['otros']       = $this->datasis->otros('PROCAMP', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('procamp', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'procamp', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'procamp', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('procamp', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '460', '500' );
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
			'hidden'        => 'true',
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->addField('campana');
		$grid->label('Campana');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->addField('activo');
		$grid->label('Activo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('fechai');
		$grid->label('Fechai');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('fechaf');
		$grid->label('Fechaf');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('convoca');
		$grid->label('Convoca');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('objetivo');
		$grid->label('Objetivo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('alcance');
		$grid->label('Alcance');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('descripcion');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('coordced');
		$grid->label('Coordced');
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


		$grid->addField('coordnom');
		$grid->label('Coordnom');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->addField('autorizado');
		$grid->label('Autorizado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:120, maxlength: 120 }',
		));


		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:80, maxlength: 80 }',
		));


		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
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
		$grid->setAdd(    $this->datasis->sidapuede('PROCAMP','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('PROCAMP','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('PROCAMP','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('PROCAMP','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: procampadd, editfunc: procampedit, delfunc: procampdel, viewfunc: procampshow");

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
		$mWHERE = $grid->geneTopWhere('procamp');

		$response   = $grid->getData('procamp', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM procamp WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('procamp', $data);
					echo "Registro Agregado";

					logusu('PROCAMP',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM procamp WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM procamp WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE procamp SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("procamp", $data);
				logusu('PROCAMP',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('procamp', $data);
				logusu('PROCAMP',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM procamp WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM procamp WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM procamp WHERE id=$id ");
				logusu('PROCAMP',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	// Edicion 

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fechai").datepicker({dateFormat:"dd/mm/yy"});
			$("#fechaf").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		// Valida RIF o CI con mensaje
		$script .= $this->datasis->validarif();

		$script .= '
		function rchrifci(value, colname) {
			value.toUpperCase();
			var patt=/((^[VEJG][0-9])|(^[P][A-Z0-9]))/;
			if( !patt.test(value) )
				return [false,"El Rif colocado no es correcto, por favor verifique con el SENIAT."];
			else
				return [true,""];
		};

		$("#coordced").focusout(function(){
			rif=$(this).val().toUpperCase();
			$(this).val(rif);
			if(!chrif(rif)){
				alert("Al parecer la cedula colocada no es correcta, por favor verifique.");
				return true;
			}else{
				$.ajax({
					type: "POST",
					url: "'.site_url('ajax/traerif').'",
					dataType: "json",
					data: {rifci: rif},
					success: function(data){
						if(data.error==0){
							if($("#coordnom").val()==""){
								$("#coordnom").val(data.nombre);
							}
						}
					}
				});
			}
			return true;
		});
		';


		$edit = new DataEdit('', 'procamp');

		$consulrif     = trim($this->datasis->traevalor('CONSULRIF'));

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

		$edit->campana = new inputField('Nombre','campana');
		$edit->campana->rule='';
		$edit->campana->size =50;
		$edit->campana->maxlength =100;

		$edit->activo = new dropdownField('Status','activo');
		$edit->activo->option('S','Activo');
		$edit->activo->option('N','Inactivo');
		$edit->activo->rule='max_length[1]|required';
		$edit->activo->style='width:150px;';

		$edit->fechai = new dateonlyField('Inicio','fechai');
		$edit->fechai->rule='chfecha';
		$edit->fechai->calendar=false;
		$edit->fechai->size =10;
		$edit->fechai->maxlength =8;

		$edit->fechaf = new dateonlyField('Termino','fechaf');
		$edit->fechaf->rule='chfecha';
		$edit->fechaf->calendar=false;
		$edit->fechaf->size =10;
		$edit->fechaf->maxlength =8;

		$edit->convoca = new textareaField('Convocante','convoca');
		$edit->convoca->rule='';
		$edit->convoca->cols = 50;
		$edit->convoca->rows = 2;

		$edit->objetivo = new textareaField('Objetivo','objetivo');
		$edit->objetivo->rule='';
		$edit->objetivo->cols = 50;
		$edit->objetivo->rows = 2;

		$edit->alcance = new textareaField('Alcance','alcance');
		$edit->alcance->rule='';
		$edit->alcance->cols = 50;
		$edit->alcance->rows = 2;

		$edit->descripcion = new textareaField('Descripcion','descripcion');
		$edit->descripcion->rule='';
		$edit->descripcion->cols = 50;
		$edit->descripcion->rows = 2;

		$edit->coordced = new inputField('Cedula','coordced');
		$edit->coordced->rule='trim|strtoupper|callback_chci';
		$edit->coordced->size =13;
		$edit->coordced->maxlength =11;

		$edit->coordnom = new inputField('Coordinador','coordnom');
		$edit->coordnom->rule='';
		$edit->coordnom->size =50;
		$edit->coordnom->maxlength =100;

		$edit->autorizado = new inputField('Autorizados','autorizado');
		$edit->autorizado->rule='';
		$edit->autorizado->size =50;
		$edit->autorizado->maxlength =120;

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));

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
		if (!$this->db->table_exists('procamp')) {
			$mSQL="CREATE TABLE `procamp` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `campana` varchar(100) NOT NULL COMMENT 'Nombre',
			  `activo` char(1) DEFAULT 'S' COMMENT 'Activo S o N',
			  `fechai` date DEFAULT NULL COMMENT 'Inicio',
			  `fechaf` date DEFAULT NULL COMMENT 'Termino',
			  `convoca` text COMMENT 'Organizacion que convoca',
			  `objetivo` text COMMENT 'Objetivos',
			  `alcance` text COMMENT 'Alcance',
			  `descripcion` text,
			  `coordced` int(11) DEFAULT NULL,
			  `coordnom` varchar(100) DEFAULT NULL,
			  `autorizado` varchar(120) DEFAULT NULL COMMENT 'Usuarios Autorizados',
			  `usuario` varchar(80) DEFAULT NULL,
			  `estampa` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='Eventos de captacion'";
			$this->db->query($mSQL);
		}
		//$campos=$this->db->list_fields('procamp');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}

?>
