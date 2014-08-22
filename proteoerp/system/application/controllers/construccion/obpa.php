<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
class Obpa extends Controller {
	var $mModulo = 'OBPA';
	var $titp    = 'PARTIDAS DE OBRAS';
	var $tits    = 'PARTIDAS DE OBRAS';
	var $url     = 'construccion/obpa/';

	function Obpa(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'OBPA', $ventana=0 );
	}

	function index(){
		if ( !$this->datasis->iscampo('obpa','id') ) {
			$this->db->query('ALTER TABLE obpa DROP PRIMARY KEY');
			$this->db->query('ALTER TABLE obpa ADD UNIQUE INDEX codigo (codigo)');
			$this->db->query('ALTER TABLE obpa ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
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
		$grid->wbotonadd(array("id"=>"ggrupo",   "img"=>"images/pdf_logo.gif",  "alt" => "Grupos", "label"=>"Grupos"));
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
		$param['listados']    = $this->datasis->listados('OBPA', 'JQ');
		$param['otros']       = $this->datasis->otros('OBPA', 'JQ');
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

		// Usos de las Areas
		$bodyscript .= '
		$("#ggrupo").click(function(){
			$.post("'.site_url('construccion/obpa/grupoform').'",
			function(data){
				$("#fshow").html(data);
				$("#fshow").dialog( { title:"GRUPOS", width: 320, height: 400, modal: true } );
				$("#fshow").dialog( "open" );
			});
		});';

		$bodyscript .= $this->jqdatagrid->bsshow('obpa', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'obpa', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'obpa', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('obpa', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '250', '420' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

		$bodyscript .= '});';

		$bodyscript .= '</script>';

		return $bodyscript;
	}

	//******************************************************************
	// Forma de Grupos
	//
	function grupoform(){
		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('ID');
		$grid->params(array(
			'hidden'      => 'true',
			'align'       => "'center'",
			'width'       => 20,
			'editable'    => 'false',
			'editoptions' => '{readonly:true,size:10}'
			)
		);

		$grid->addField('grupo');
		$grid->label('Grupo');
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
		$grid->setHeight('240');

		$grid->setUrlget(site_url('construccion/obpa/grupogetdata/'));
		$grid->setUrlput(site_url('construccion/obpa/gruposetdata/'));

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

		$msalida .= "\n</script>\n";
		$msalida .= '<id class="anexos"><table id="newapi'.$mgrid['gridname'].'"></table>';
		$msalida .= '<div   id="pnewapi'.$mgrid['gridname'].'"></div></div>';

		echo $msalida;

	}

	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function grupogetdata(){
		$grid     = $this->jqdatagrid;
		$mWHERE   = $grid->geneTopWhere('obgp');
		$response = $grid->getData('obgp', array(array()), array(), false, $mWHERE, 'grupo' );

		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion del Grid o Tabla
	//
	function gruposetData( $mid = 0 ){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		$id = str_replace('jqg','',$id);
		unset($data['oper']);
		unset($data['id']);

		// ver si puede borrar
		if ($oper == 'del') {
			$grid     = $this->jqdatagrid;
			$response = $grid->operations('obgp','id');
			echo 'Registro Borrado!!!';
		} elseif($oper == 'edit') {
			$id       = $this->input->post('id');
			$grid     = $this->jqdatagrid;
			$response = $grid->operations('obgp','id');
			echo 'Registro Borrado!!!';


		} elseif($oper == 'add'){
			if(false == empty($data)){
				$this->db->insert('obgp', $data);
				echo "Registro Agregado";
				logusu('OBGP',"Registro INCLUIDO");
			} else
			echo "Fallo Agregado!!!";
		}
	}


	//******************************************************************
	// Definicion del Grid o Tabla 
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('descrip');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('grupo');
		$grid->label('Grupo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));

		$grid->addField('nomgrup');
		$grid->label('Nomgrup');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));

		$grid->addField('comision');
		$grid->label('Comision');
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

		$grid->addField('gasto');
		$grid->label('Gasto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
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
		$grid->setAdd(    $this->datasis->sidapuede('OBPA','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('OBPA','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('OBPA','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('OBPA','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: obpaadd, editfunc: obpaedit, delfunc: obpadel, viewfunc: obpashow");

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
		$mWHERE = $grid->geneTopWhere('obpa');

		$response   = $grid->getData('obpa', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM obpa WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('obpa', $data);
					echo "Registro Agregado";

					logusu('OBPA',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM obpa WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM obpa WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE obpa SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("obpa", $data);
				logusu('OBPA',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('obpa', $data);
				logusu('OBPA',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM obpa WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM obpa WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM obpa WHERE id=$id ");
				logusu('OBPA',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	// Edicion 

	function dataedit(){
		$this->rapyd->load('dataedit');

		$script = '
		$(function() {
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit('', 'obpa');

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
		$edit->codigo->size =6;
		$edit->codigo->maxlength =4;

		$edit->descrip = new inputField('Descripcion','descrip');
		$edit->descrip->rule='';
		$edit->descrip->size =42;
		$edit->descrip->maxlength =40;

		$edit->grupo = new dropdownField('Grupo','grupo');
		$edit->grupo->options('SELECT id, grupo FROM obgp ORDER BY grupo');
		$edit->grupo->rule = 'required';
		$edit->grupo->style='width:250px;';

		/*
		$edit->nomgrup = new inputField('Nomgrup','nomgrup');
		$edit->nomgrup->rule='';
		$edit->nomgrup->size =32;
		$edit->nomgrup->maxlength =30;
		$edit->nomgrup->readonly = true;
		*/
		
		$edit->comision = new inputField('Comision','comision');
		$edit->comision->rule='numeric';
		$edit->comision->css_class='inputnum';
		$edit->comision->size =7;
		$edit->comision->maxlength =5;

		$mSQL="SELECT codigo, CONCAT_WS('-',TRIM(descrip),TRIM(codigo)) AS descrip FROM mgas ORDER BY descrip";
		$edit->gasto = new dropdownField('Gasto','gasto');
		$edit->gasto->option('','Seleccionar');
		//$edit->gasto->rule= 'condi_required|callback_chisidb';
		$edit->gasto->options($mSQL);
		$edit->gasto->style ='width:300px;';
/*
		$edit->gasto = new inputField('Gasto','gasto');
		$edit->gasto->rule='';
		$edit->gasto->size =8;
		$edit->gasto->maxlength =6;
*/

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
		return true;
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		$mSQL = 'UPDATE obpa a JOIN obgp b ON a.grupo=b.id SET a.nomgrup=b.grupo';
		$this->db->query($mSQL);
		logusu($do->table,"Creo $this->tits $primary ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		$mSQL = 'UPDATE obpa a JOIN obgp b ON a.grupo=b.id SET a.nomgrup=b.grupo';
		$this->db->query($mSQL);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		if (!$this->db->table_exists('obpa')) {
			$mSQL="CREATE TABLE `obpa` (
			  codigo    char(4)      NOT NULL DEFAULT '',
			  descrip   varchar(40)  DEFAULT NULL,
			  grupo     int(11)      DEFAULT NULL,
			  comision  decimal(5,2) DEFAULT NULL,
			  nomgrup   varchar(30)  DEFAULT NULL,
			  gasto     varchar(6)   DEFAULT NULL,
			  id        int(11)      NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (id),
			  UNIQUE KEY codigo (codigo)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->query($mSQL);
		}
	}
}

?>
