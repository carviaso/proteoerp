<?php
class Pretab extends Controller {
	var $mModulo = 'PRETAB';
	var $titp    = 'PRENOMINA';
	var $tits    = 'PRENOMINA';
	var $url     = 'nomina/pretab/';

	function Pretab(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'PRETAB', $ventana=0 );
	}

	function index(){
		if ( !$this->datasis->iscampo('pretab','id') ) {
			$this->db->simple_query('ALTER TABLE pretab DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE pretab ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE pretab ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		$this->datasis->creaintramenu(array('modulo'=>'716','titulo'=>'Prenomina','mensaje'=>'Prenomina','panel'=>'TRANSACCIONES','ejecutar'=>'nomina/pretab','target'=>'popu','visible'=>'S','pertenece'=>'7','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 900, 600, substr($this->url,0,-1) );
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
		$grid->wbotonadd(array('id'=>'genepre',   'img'=>'images/star.png',  'alt' => 'Genera Prenomina', 'label'=>'Genera Prenomina'));
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
		$param['listados']    = $this->datasis->listados('PRETAB', 'JQ');
		$param['otros']       = $this->datasis->otros('PRETAB', 'JQ');
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

		// Prepara Prenomina
		$bodyscript .= '
		$("#genepre").click( function() {
			$.post("'.base_url().'nomina/prenom/",
			function(data){
				$("#fedita").dialog( {height: 230, width: 450, title: "Cruce Cliente Cliente"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});
		';



		$bodyscript .= '
		function pretabadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function pretabedit(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function pretabshow(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/show').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function pretabdel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						try{
							var json = JSON.parse(data);
							if (json.status == "A"){
								apprise("Registro eliminado");
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
							}else{
								apprise("Registro no se puede eliminado");
							}
						}catch(e){
							$("#fborra").html(data);
							$("#fborra").dialog( "open" );
						}
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';
		//Wraper de javascript
		$bodyscript .= '
		$(function(){
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/PRETAB').'/\'+res.id+\'/id\'').';
									return true;
								} else {
									apprise(json.mensaje);
								}
							}catch(e){
								$("#fedita").html(r);
							}
						}
					})
				},
				"Cancelar": function() {
					$("#fedita").html("");
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				$("#fedita").html("");
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});';

		$bodyscript .= '
		$("#fshow").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fshow").html("");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				$("#fshow").html("");
			}
		});';

		$bodyscript .= '
		$("#fborra").dialog({
			autoOpen: false, height: 300, width: 400, modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fborra").html("");
					jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
				$("#fborra").html("");
			}
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

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('frec');
		$grid->label('Frec');
		$grid->params(array(
			'search'        => 'true',
			'hidden'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('total');
		$grid->label('Total');
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

		$query = $this->db->query("DESCRIBE pretab");
		$i = 0;
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				if ( substr($row->Field,0,1) == 'c' && $row->Field != 'codigo' ) {

					$etiq = $this->datasis->dameval("SELECT CONCAT(TRIM(encab1), ' ', encab2 ) encabeza FROM conc WHERE concepto=".$this->db->escape(substr($row->Field,1,4)));
					$grid->addField($row->Field);
					$grid->label($etiq);
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
				}
			}
		}

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
		$grid->setAdd(    $this->datasis->sidapuede('PRETAB','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('PRETAB','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('PRETAB','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('PRETAB','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: pretabadd, editfunc: pretabedit, delfunc: pretabdel, viewfunc: pretabshow");

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
		$mWHERE = $grid->geneTopWhere('pretab');

		$response   = $grid->getData('pretab', array(array()), array(), false, $mWHERE );
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
		$mcodp  = "??????";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM pretab WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('pretab', $data);
					echo "Registro Agregado";

					logusu('PRETAB',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM pretab WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM pretab WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE pretab SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("pretab", $data);
				logusu('PRETAB',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('pretab', $data);
				logusu('PRETAB',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM pretab WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM pretab WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM pretab WHERE id=$id ");
				logusu('PRETAB',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
		});
		';

		$edit = new DataEdit($this->tits, 'pretab');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->script($script,'create');

		$edit->script($script,'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
		});		';
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->rule='';
		$edit->codigo->size =17;
		$edit->codigo->maxlength =15;

		$edit->frec = new inputField('Frec','frec');
		$edit->frec->rule='';
		$edit->frec->size =3;
		$edit->frec->maxlength =1;

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='';
		$edit->nombre->size =32;
		$edit->nombre->maxlength =30;

		$edit->total = new inputField('Total','total');
		$edit->total->rule='numeric';
		$edit->total->css_class='inputnum';
		$edit->total->size =19;
		$edit->total->maxlength =17;


		$query = $this->db->query("DESCRIBE pretab");
		$i = 0;
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				if ( substr($row->Field,0,1) == 'c' && $row->Field != 'codigo' ) {

					$obj = $row->Field;

					$edit->$obj = new inputField('C010', $obj);
					$edit->$obj->rule='numeric';
					$edit->$obj->css_class='inputnum';
					$edit->$obj->size =19;
					$edit->$obj->maxlength =17;
				}
			}
		}


/*
		$edit->c010 = new inputField('C010','c010');
		$edit->c010->rule='numeric';
		$edit->c010->css_class='inputnum';
		$edit->c010->size =19;
		$edit->c010->maxlength =17;

		$edit->c018 = new inputField('C018','c018');
		$edit->c018->rule='numeric';
		$edit->c018->css_class='inputnum';
		$edit->c018->size =19;
		$edit->c018->maxlength =17;

		$edit->c030 = new inputField('C030','c030');
		$edit->c030->rule='numeric';
		$edit->c030->css_class='inputnum';
		$edit->c030->size =19;
		$edit->c030->maxlength =17;

		$edit->c060 = new inputField('C060','c060');
		$edit->c060->rule='numeric';
		$edit->c060->css_class='inputnum';
		$edit->c060->size =19;
		$edit->c060->maxlength =17;

		$edit->c070 = new inputField('C070','c070');
		$edit->c070->rule='numeric';
		$edit->c070->css_class='inputnum';
		$edit->c070->size =19;
		$edit->c070->maxlength =17;

		$edit->c080 = new inputField('C080','c080');
		$edit->c080->rule='numeric';
		$edit->c080->css_class='inputnum';
		$edit->c080->size =19;
		$edit->c080->maxlength =17;

		$edit->c090 = new inputField('C090','c090');
		$edit->c090->rule='numeric';
		$edit->c090->css_class='inputnum';
		$edit->c090->size =19;
		$edit->c090->maxlength =17;

		$edit->c102 = new inputField('C102','c102');
		$edit->c102->rule='numeric';
		$edit->c102->css_class='inputnum';
		$edit->c102->size =19;
		$edit->c102->maxlength =17;

		$edit->c110 = new inputField('C110','c110');
		$edit->c110->rule='numeric';
		$edit->c110->css_class='inputnum';
		$edit->c110->size =19;
		$edit->c110->maxlength =17;

		$edit->c120 = new inputField('C120','c120');
		$edit->c120->rule='numeric';
		$edit->c120->css_class='inputnum';
		$edit->c120->size =19;
		$edit->c120->maxlength =17;

		$edit->c125 = new inputField('C125','c125');
		$edit->c125->rule='numeric';
		$edit->c125->css_class='inputnum';
		$edit->c125->size =19;
		$edit->c125->maxlength =17;

		$edit->c130 = new inputField('C130','c130');
		$edit->c130->rule='numeric';
		$edit->c130->css_class='inputnum';
		$edit->c130->size =19;
		$edit->c130->maxlength =17;

		$edit->c195 = new inputField('C195','c195');
		$edit->c195->rule='numeric';
		$edit->c195->css_class='inputnum';
		$edit->c195->size =19;
		$edit->c195->maxlength =17;

		$edit->c330 = new inputField('C330','c330');
		$edit->c330->rule='numeric';
		$edit->c330->css_class='inputnum';
		$edit->c330->size =19;
		$edit->c330->maxlength =17;

		$edit->c340 = new inputField('C340','c340');
		$edit->c340->rule='numeric';
		$edit->c340->css_class='inputnum';
		$edit->c340->size =19;
		$edit->c340->maxlength =17;

		$edit->c600 = new inputField('C600','c600');
		$edit->c600->rule='numeric';
		$edit->c600->css_class='inputnum';
		$edit->c600->size =19;
		$edit->c600->maxlength =17;

		$edit->c610 = new inputField('C610','c610');
		$edit->c610->rule='numeric';
		$edit->c610->css_class='inputnum';
		$edit->c610->size =19;
		$edit->c610->maxlength =17;

		$edit->c620 = new inputField('C620','c620');
		$edit->c620->rule='numeric';
		$edit->c620->css_class='inputnum';
		$edit->c620->size =19;
		$edit->c620->maxlength =17;

		$edit->c650 = new inputField('C650','c650');
		$edit->c650->rule='numeric';
		$edit->c650->css_class='inputnum';
		$edit->c650->size =19;
		$edit->c650->maxlength =17;

		$edit->c690 = new inputField('C690','c690');
		$edit->c690->rule='numeric';
		$edit->c690->css_class='inputnum';
		$edit->c690->size =19;
		$edit->c690->maxlength =17;

		$edit->c900 = new inputField('C900','c900');
		$edit->c900->rule='numeric';
		$edit->c900->css_class='inputnum';
		$edit->c900->size =19;
		$edit->c900->maxlength =17;

		$edit->c910 = new inputField('C910','c910');
		$edit->c910->rule='numeric';
		$edit->c910->css_class='inputnum';
		$edit->c910->size =19;
		$edit->c910->maxlength =17;

		$edit->c920 = new inputField('C920','c920');
		$edit->c920->rule='numeric';
		$edit->c920->css_class='inputnum';
		$edit->c920->size =19;
		$edit->c920->maxlength =17;

		$edit->c930 = new inputField('C930','c930');
		$edit->c930->rule='numeric';
		$edit->c930->css_class='inputnum';
		$edit->c930->size =19;
		$edit->c930->maxlength =17;
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
		if (!$this->db->table_exists('pretab')) {
			$mSQL="CREATE TABLE `pretab` (
			  `codigo` char(15) NOT NULL DEFAULT '',
			  `frec` char(1) DEFAULT NULL,
			  `fecha` date DEFAULT NULL,
			  `nombre` char(30) DEFAULT NULL,
			  `total` decimal(17,2) DEFAULT '0.00',
			  `c010` decimal(17,2) DEFAULT '0.00',
			  `c018` decimal(17,2) DEFAULT '0.00',
			  `c030` decimal(17,2) DEFAULT '0.00',
			  `c060` decimal(17,2) DEFAULT '0.00',
			  `c070` decimal(17,2) DEFAULT '0.00',
			  `c080` decimal(17,2) DEFAULT '0.00',
			  `c090` decimal(17,2) DEFAULT '0.00',
			  `c102` decimal(17,2) DEFAULT '0.00',
			  `c110` decimal(17,2) DEFAULT '0.00',
			  `c120` decimal(17,2) DEFAULT '0.00',
			  `c125` decimal(17,2) DEFAULT '0.00',
			  `c130` decimal(17,2) DEFAULT '0.00',
			  `c195` decimal(17,2) DEFAULT '0.00',
			  `c330` decimal(17,2) DEFAULT '0.00',
			  `c340` decimal(17,2) DEFAULT '0.00',
			  `c600` decimal(17,2) DEFAULT '0.00',
			  `c610` decimal(17,2) DEFAULT '0.00',
			  `c620` decimal(17,2) DEFAULT '0.00',
			  `c650` decimal(17,2) DEFAULT '0.00',
			  `c690` decimal(17,2) DEFAULT '0.00',
			  `c900` decimal(17,2) DEFAULT '0.00',
			  `c910` decimal(17,2) DEFAULT '0.00',
			  `c920` decimal(17,2) DEFAULT '0.00',
			  `c930` decimal(17,2) DEFAULT '0.00',
			  PRIMARY KEY (`codigo`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('pretab');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}

?>
