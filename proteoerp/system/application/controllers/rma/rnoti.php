<?php
class Rnoti extends Controller {
	var $mModulo = 'RNOTI';
	var $titp    = 'RMA Notificacion/Recepcion';
	var $tits    = 'RMA Notificacion/Recepcion';
	var $url     = 'rma/rnoti/';

	function Rnoti(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'RNOTI', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->creaintramenu(array('modulo'=>'350','titulo'=>'Notificacion y Recepcion','mensaje'=>'Notificacion y Recepcion','panel'=>'RMA','ejecutar'=>'rma/rnoti','target'=>'popu','visible'=>'S','pertenece'=>'3','ancho'=>900,'alto'=>600));
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
		$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
		array("id"=>"fedita",  "title"=>"Agregar/Editar Registro")
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('RNOTI', 'JQ');
		$param['otros']       = $this->datasis->otros('RNOTI', 'JQ');
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
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function rnotiadd() {
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function rnotiedit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		};';

		//Wraper de javascript
		$bodyscript .= '
		$(function() {
			$("#dialog:ui-dialog").dialog( "destroy" );
			var mId = 0;
			var montotal = 0;
			var ffecha = $("#ffecha");
			var grid = jQuery("#newapi'.$grid0.'");
			var s;
			var allFields = $( [] ).add( ffecha );
			var tips = $( ".validateTips" );
			s = grid.getGridParam(\'selarrrow\');
			';

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
			buttons: {
			"Guardar": function() {
				var bValid = true;
				var murl = $("#df1").attr("action");
				allFields.removeClass( "ui-state-error" );
				$.ajax({
					type: "POST", dataType: "html", async: false,
					url: murl,
					data: $("#df1").serialize(),
					success: function(r,s,x){
						try{
							var json = JSON.parse(r);
							if (json.status == "A"){
								apprise("Registro Guardado");
								$( "#fedita" ).dialog( "close" );
								grid.trigger("reloadGrid");
								'.$this->datasis->jwinopen(site_url('formatos/ver/RNOTI').'/\'+res.id+\'/id\'').';
								return true;
							} else {
								apprise(json.mensaje);
							}
						}catch(e){
							$("#fedita").html(r);
						}
					}
			})},
			"Cancelar": function() { $( this ).dialog( "close" ); }
			},
			close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
		});';
		$bodyscript .= '});'."\n";

		$bodyscript .= "\n</script>\n";
		$bodyscript .= "";
		return $bodyscript;
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
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


		$grid->addField('codprod');
		$grid->label('Codigo producto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('serial');
		$grid->label('Serial');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:35, maxlength: 35 }',
		));


		$grid->addField('descprod');
		$grid->label('Descripcion producto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:45, maxlength: 45 }',
		));


		$grid->addField('codcliente');
		$grid->label('Cod. Cliente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('nomcliente');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:45, maxlength: 45 }',
		));


		$grid->addField('fechafact');
		$grid->label('Fecha Venta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('numfact');
		$grid->label('Factura');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('reporte');
		$grid->label('Reporte');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('garantia');
		$grid->label('Garantia');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('falla');
		$grid->label('Falla');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('estado');
		$grid->label('Estado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('observacion');
		$grid->label('Observacion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('diagnostico');
		$grid->label('Diagnostico');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));

		$grid->addField('fechadiag');
		$grid->label('Fecha Diag');
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

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('RNOTI','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('RNOTI','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('RNOTI','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('RNOTI','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("\t\taddfunc: rnotiadd,\n\t\teditfunc: rnotiedit");

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
	function getdata()
	{
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('rnoti');

		$response   = $grid->getData('rnoti', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData()
	{
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
				$check = $this->datasis->dameval("SELECT count(*) FROM rnoti WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('rnoti', $data);
					echo "Registro Agregado";

					logusu('RNOTI',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM rnoti WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM rnoti WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE rnoti SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("rnoti", $data);
				logusu('RNOTI',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('rnoti', $data);
				logusu('RNOTI',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM rnoti WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM rnoti WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM rnoti WHERE id=$id ");
				logusu('RNOTI',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'rnoti');

		$edit->on_save_redirect=false;

		$scli=array(
		'tabla'   =>'scli',
		'columnas'=>array(
			'cliente' =>'C&oacute;digo Cliente',
			'nombre'  =>'Nombre',
			'contacto'=>'Contacto'),
		'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'codcliente','nombre'=>'nomcliente'),
		'titulo'  =>'Buscar Cliente');
		
		$cboton=$this->datasis->modbus($scli);

		$sinv=array(
		'tabla'   =>'sinv',
		'columnas'=>array(
			'codigo'  =>'Codigo',
			'descrip' =>'Descripcion',
			'barras'  =>'Barras'),
		'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripcion'),
		'retornar'=>array('codigo'=>'codprod',      'descrip'=>'descprod'),
		'titulo'  =>'Buscar Inventario');
		
		$sboton=$this->datasis->modbus($sinv);


		$edit->back_url = site_url($this->url.'filteredgrid');
		$script= ' 
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$("#fechadiag").datepicker({dateFormat:"dd/mm/yy"});
			$("#fechafact").datepicker({dateFormat:"dd/mm/yy"});
		});';
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert');
		$edit->pre_process( 'update','_pre_update');
		$edit->pre_process( 'delete','_pre_delete');

		$edit->codcliente = new inputField('Cod. Cliente','codcliente');
		$edit->codcliente->size =7;
		$edit->codcliente->maxlength =5;
		$edit->codcliente->append($cboton); 

		$edit->nomcliente = new inputField('Nombre cliente','nomcliente');
		$edit->nomcliente->rule='';
		$edit->nomcliente->size =47;
		$edit->nomcliente->maxlength =45;

/*
		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size = 10;
		$edit->fecha->rule ='required';
		$edit->fecha->calendar=false;
		$edit->fecha->rule='chfecha';
		$edit->fecha->maxlength =8;
*/

		$edit->serial = new inputField('Serial','serial');
		$edit->serial->rule='';
		$edit->serial->size =37;
		$edit->serial->maxlength =35;

		$edit->codprod = new inputField('Codigo producto','codprod');
		$edit->codprod->rule='';
		$edit->codprod->size =17;
		$edit->codprod->maxlength =15;
		$edit->codprod->append($sboton); 

		$edit->descprod = new inputField('Descripcion producto','descprod');
		$edit->descprod->size =47;
		$edit->descprod->maxlength =45;

		$edit->fechafact = new dateField('Fecha venta','fechafact');
		$edit->fechafact->rule='chfecha';
		$edit->fechafact->size =10;
		$edit->fechafact->maxlength =8;
		$edit->fechafact->calendar=false;

		$edit->numfact = new inputField('Factura','numfact');
		$edit->numfact->rule='max_length[8]';
		$edit->numfact->size =10;
		$edit->numfact->maxlength =8;

		$edit->reporte = new inputField('Reporte','reporte');
		$edit->reporte->rule='max_length[20]';
		$edit->reporte->size =22;
		$edit->reporte->maxlength =20;

		$edit->garantia = new inputField('Garantia','garantia');
		$edit->garantia->rule='';
		$edit->garantia->size =4;
		$edit->garantia->maxlength =2;

		$edit->falla = new textareaField('Falla','falla');
		$edit->falla->rule='';
		$edit->falla->cols = 70;
		$edit->falla->rows = 4;
/*
		$edit->observacion = new textareaField('Observacion','observacion');
		$edit->observacion->rule='';
		$edit->observacion->cols = 70;
		$edit->observacion->rows = 4;

		$edit->estado = new inputField('Estado','estado');
		$edit->estado->rule='max_length[20]';
		$edit->estado->size =22;
		$edit->estado->maxlength =20;

		$edit->diagnostico = new textareaField('Diagnostico','diagnostico');;
		$edit->diagnostico->cols = 70;
		$edit->diagnostico->rows = 4;

		$edit->fechadiag = new dateField('Fecha diagnostico','fechadiag');
		$edit->fechadiag->rule='chfecha';
		$edit->fechadiag->size =10;
		$edit->fechadiag->maxlength =8;
		$edit->fechadiag->calendar=false;
*/
		$edit->build();

		$script= '<script type="text/javascript" > 
		$(function() {
			$(".inputnum").numeric(".");
			$(".inputonlynum").numeric();
		});
		</script>';
		
		$script= '<script type="text/javascript" > 
		$(function() {
			$("#fecha").datepicker({   dateFormat: "dd/mm/yy" });
		});
		</script>';

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
		$mSQL = "
		CREATE TABLE IF NOT EXISTS `rnoti` (
			id          INT(11) NOT NULL AUTO_INCREMENT,
			fecha       DATE        NULL DEFAULT NULL,
			codprod     VARCHAR(15) NULL DEFAULT NULL COMMENT 'codigo o modelo del producto',
			serial      VARCHAR(35) NULL DEFAULT NULL COMMENT 'serial del producto',
			descprod    VARCHAR(45) NULL DEFAULT NULL COMMENT 'auto descripcion del producto',
			codcliente  VARCHAR(5)  NULL DEFAULT NULL COMMENT 'auto codigo del cliente',
			nomcliente  VARCHAR(45) NULL DEFAULT NULL COMMENT 'automatico nombre del cliente',
			fechafact   DATE        NULL DEFAULT NULL COMMENT 'automatico fecha de venta',
			numfact     VARCHAR(8)  NULL DEFAULT NULL COMMENT 'automatico numero de factura de venta',
			reporte     VARCHAR(20) NULL DEFAULT NULL COMMENT 'opcional numero de reporte del proveedor',
			garantia    CHAR(2)     NULL DEFAULT NULL COMMENT 'automatico',
			falla       TEXT        NULL              COMMENT 'observacion, descripcion de la falla ',
			estado      CHAR(20)    NULL DEFAULT 'NOTIFICADO' COMMENT ' RECIBIDO, REVISADO, ESPERA, REPARADO',
			observacion TEXT        NULL              COMMENT 'observacion, descripcion del producto',
			diagnostico TEXT        NULL              COMMENT 'diagnostico del producto',
			fechadiag   DATE        NULL              DEFAULT NULL COMMENT 'fecha diagnostico del producto',
			PRIMARY KEY (`id`)
		)
		COMMENT='notificacion o reporte de mercancia defectuosa'
		COLLATE='latin1_swedish_ci'
		ENGINE=MyISAM;";
		$this->db->query($mSQL);
	

	}
}
?>
