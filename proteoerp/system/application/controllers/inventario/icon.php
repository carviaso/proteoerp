<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Icon extends Controller {
	var $mModulo = 'ICON';
	var $titp    = 'Conceptos de Ajuste';
	var $tits    = 'Conceptos de Ajuste';
	var $url     = 'inventario/icon/';

	function Icon(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'ICON', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->creaintramenu(array('modulo'=>'327','titulo'=>'Conceptos de Ajuste','mensaje'=>'Conceptos de Ajuste','panel'=>'REGISTROS','ejecutar'=>'/inventario/icon','target'=>'popu','visible'=>'S','pertenece'=>'3','ancho'=>900,'alto'=>600));
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
		$grid->wbotonadd(array("id"=>"fingreso", "img"=>"images/agrega4.png", "alt" => "Concepto de Ingreso", "label"=>"Concepto de Ingreso",'tema'=>'anexos'));
		$grid->wbotonadd(array("id"=>"fegreso",  "img"=>"images/agrega4.png", "alt" => "Concepto de Egreso",  "label"=>"Concepto de Egreso", 'tema'=>'anexos'));
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
		$param['listados']    = $this->datasis->listados('ICON', 'JQ');
		$param['otros']       = $this->datasis->otros('ICON', 'JQ');
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
		$("#fingreso").click( function(){
			$.post("'.site_url($this->url.'deingreso/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})

		});';


		$bodyscript .= '
		$("#fegreso").click( function(){
			$.post("'.site_url($this->url.'deegreso/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});';


		$bodyscript .= '
		function iconadd(tipo){
			alert("Use los Botones para Agregar");
		};';

		$bodyscript .= '
		function iconedit(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				if ( ret.tipo == "I" ){
					$.post("'.site_url($this->url.'deingreso/modify').'/"+id, function(data){
						$("#fedita").html(data);
						$("#fedita").dialog( "open" );
					});
				} else {
					$.post("'.site_url($this->url.'deegreso/modify').'/"+id, function(data){
						$("#fedita").html(data);
						$("#fedita").dialog( "open" );
					});
				}
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function iconshow(){
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
		function icondel() {
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
			autoOpen: false, height: 300, width: 450, modal: true,
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/ICON').'/\'+res.id+\'/id\'').';
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

		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));


		$grid->addField('concepto');
		$grid->label('Concepto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
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


		$grid->addField('gastode');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('ingreso');
		$grid->label('Ingreso');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('ingresod');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
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


/*
		$grid->addField('depto');
		$grid->label('Depto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));
*/




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
		$grid->setAdd( false ); //  $this->datasis->sidapuede('ICON','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('ICON','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('ICON','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('ICON','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: iconadd, editfunc: iconedit, delfunc: icondel, viewfunc: iconshow");

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
		$mWHERE = $grid->geneTopWhere('icon');

		$response   = $grid->getData('icon', array(array()), array(), false, $mWHERE );
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
		$mcodp  = "codigo";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM icon WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('icon', $data);
					echo "Registro Agregado";

					logusu('ICON',"Registro codigo INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM icon WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM icon WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE icon SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("icon", $data);
				logusu('ICON',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('icon', $data);
				logusu('ICON',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM icon WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM icon WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM icon WHERE id=$id ");
				logusu('ICON',"Registro codigo ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function deingreso(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'icon');

		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$script = "
		$('#ingreso').autocomplete({
			source: function( req, add){
				$.ajax({
					url:  '".site_url('ajax/autobotr')."',
					type: 'POST',
					dataType: 'json',
					data: 'q='+encodeURIComponent(req.term),
					success:
						function(data){
							var sugiere = [];
							if(data.length==0){
								$('#ingreso').val('');
								$('#ingresod').val('');
							}else{
								$.each(data,function(i, val){sugiere.push( val );});
							}
							add(sugiere);
						},
				})
			},
			minLength: 1,
			select: function( event, ui ) {
				$('#ingreso').attr('readonly', 'readonly');
				$('#ingreso').val(ui.item.codigo);
				$('#ingresod').val(ui.item.descrip);
				setTimeout(function() { $('#ingreso').removeAttr('readonly'); }, 1500);
			}
		});
		";

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->rule        = '';
		$edit->codigo->size        =  8;
		$edit->codigo->maxlength   =  6;

		$edit->concepto = new inputField('Concepto','concepto');
		$edit->concepto->rule      = '';
		$edit->concepto->size      = 32;
		$edit->concepto->maxlength = 30;

		$edit->ingreso = new inputField('Ingreso','ingreso');
		$edit->ingreso->rule       = '';
		$edit->ingreso->size       =  7;
		$edit->ingreso->maxlength  = 20;

		$edit->ingresod = new inputField('Descripcion','ingresod');
		$edit->ingresod->rule      = '';
		$edit->ingresod->size      = 32;
		$edit->ingresod->maxlength = 30;
		$edit->ingresod->readonly  = true;

		$edit->tipo = new hiddenField('Tipo','tipo');
		$edit->tipo->insertValue   = 'I';

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form'] =&  $edit;
			$this->load->view('view_icon', $conten);
		}
	}

	function deegreso(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'icon');

		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$script = "
		$('#gasto').autocomplete({
			source: function( req, add){
				$.ajax({
					url:  '".site_url('ajax/automgas')."',
					type: 'POST',
					dataType: 'json',
					data: 'q='+encodeURIComponent(req.term),
					success:
						function(data){
							var sugiere = [];

							if(data.length==0){
								$('#gasto').val('');
								$('#gastode').val('');
							}else{
								$.each(data,
									function(i, val){
										sugiere.push( val );
									}
								);
							}
							add(sugiere);
						},
				})
			},
			minLength: 1,
			select: function( event, ui ) {
				$('#gasto').attr('readonly', 'readonly');
				$('#gasto').val(ui.item.codigo);
				$('#gastode').val(ui.item.descrip);
				setTimeout(function() {  $('#gasto').removeAttr('readonly'); }, 1500);
			}
		});
		";

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->rule        = '';
		$edit->codigo->size        =  8;
		$edit->codigo->maxlength   =  6;

		$edit->concepto = new inputField('Concepto','concepto');
		$edit->concepto->rule      = '';
		$edit->concepto->size      = 32;
		$edit->concepto->maxlength = 30;

		$edit->gasto = new inputField('Gasto','gasto');
		$edit->gasto->rule         = '';
		$edit->gasto->size         =  8;
		$edit->gasto->maxlength    = 20;

		$edit->gastode = new inputField('Descripcion','gastode');
		$edit->gastode->rule       = '';
		$edit->gastode->size       = 32;
		$edit->gastode->maxlength  = 30;
		$edit->gastode->readonly   = true;

		$edit->tipo = new hiddenField('Tipo','tipo');
		$edit->tipo->insertValue   = 'E';

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form'] =&  $edit;
			$this->load->view('view_icon', $conten);
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
		if (!$this->db->table_exists('icon')) {
			$mSQL="CREATE TABLE `icon` (
				codigo   char( 6) DEFAULT NULL,
				concepto char(30) DEFAULT NULL,
				gasto    char( 6) DEFAULT NULL,
				gastode  char(30) DEFAULT NULL,
				ingreso  char( 5) DEFAULT NULL,
				ingresod char(30) DEFAULT NULL,
				depto    char( 2) DEFAULT NULL,
				tipo     char( 1) DEFAULT 'E',
				id       INT( 11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('icon');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE icon DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE icon ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE icon ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}
	}
}
