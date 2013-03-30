<?php
class Bconci extends Controller {
	var $mModulo = 'BCONCI';
	var $titp    = 'Conciliaci&oacute;n Bancaria';
	var $tits    = 'Conciliaci&oacute;n Bancaria';
	var $url     = 'finanzas/bconci/';

	function Bconci(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'BCONCI', $ventana=0 );
	}

	function index(){
		/*if ( !$this->datasis->iscampo('bconci','id') ) {
			$this->db->simple_query('ALTER TABLE bconci DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE bconci ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE bconci ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};*/
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
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
		$param['listados']    = $this->datasis->listados('BCONCI', 'JQ');
		$param['otros']       = $this->datasis->otros('BCONCI', 'JQ');
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
		function bconciadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function bconciedit(){
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
		function bconcishow(){
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
		function bconcidel() {
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/BCONCI').'/\'+res.id+\'/id\'').';
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


		$grid->addField('codbanc');
		$grid->label('Codbanc');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('numcuent');
		$grid->label('Numcuent');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 180,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:18, maxlength: 18 }',
		));


		$grid->addField('banco');
		$grid->label('Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('saldoi');
		$grid->label('Saldoi');
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


		$grid->addField('saldof');
		$grid->label('Saldof');
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


		$grid->addField('deposito');
		$grid->label('Deposito');
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


		$grid->addField('credito');
		$grid->label('Credito');
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


		$grid->addField('cheque');
		$grid->label('Cheque');
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


		$grid->addField('debito');
		$grid->label('Debito');
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


		$grid->addField('status');
		$grid->label('Status');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
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


		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
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

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('BCONCI','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('BCONCI','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('BCONCI','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('BCONCI','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: bconciadd, editfunc: bconciedit, delfunc: bconcidel, viewfunc: bconcishow");

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
		$mWHERE = $grid->geneTopWhere('bconci');

		$response   = $grid->getData('bconci', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM bconci WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('bconci', $data);
					echo "Registro Agregado";

					logusu('BCONCI',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM bconci WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM bconci WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE bconci SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("bconci", $data);
				logusu('BCONCI',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('bconci', $data);
				logusu('BCONCI',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM bconci WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM bconci WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM bconci WHERE id=$id ");
				logusu('BCONCI',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'bconci');
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;
		$edit->fecha->insertValue=date('Y-m-d',mktime(0, 0, 0, date('n'),0));
		$edit->fecha->calendar=false;

		$edit->codbanc = new dropdownField('Banco','codbanc');
		$edit->codbanc->style= 'width:480px';
		$edit->codbanc->rule = 'required';
		$edit->codbanc->option('','Seleccionar');
		$edit->codbanc->options("SELECT TRIM(codbanc) AS codbanc,CONCAT_WS('-',codbanc,banco,numcuent) AS desca FROM banc WHERE tbanco<>'CAJ'");

		$edit->saldoi = new inputField('Saldo Inicial','saldoi');
		$edit->saldoi->rule='numeric';
		$edit->saldoi->insertValue='0.0';
		$edit->saldoi->css_class='inputnum';
		$edit->saldoi->size =20;
		$edit->saldoi->maxlength =18;

		$edit->saldof = new inputField('Saldo Final','saldof');
		$edit->saldof->insertValue='0.0';
		$edit->saldof->rule='numeric';
		$edit->saldof->css_class='inputnum';
		$edit->saldof->size =20;
		$edit->saldof->maxlength =18;

		$edit->deposito = new inputField('Dep&oacute;sito','deposito');
		$edit->deposito->rule='numeric';
		$edit->deposito->insertValue='0.0';
		$edit->deposito->css_class='inputnum';
		$edit->deposito->size =20;
		$edit->deposito->maxlength =18;

		$edit->credito = new inputField('Cr&eacute;dito','credito');
		$edit->credito->rule='numeric';
		$edit->credito->insertValue='0.0';
		$edit->credito->css_class='inputnum';
		$edit->credito->size =20;
		$edit->credito->maxlength =18;

		$edit->cheque = new inputField('Cheque','cheque');
		$edit->cheque->rule='numeric';
		$edit->cheque->insertValue='0.0';
		$edit->cheque->css_class='inputnum';
		$edit->cheque->size =20;
		$edit->cheque->maxlength =18;

		$edit->debito = new inputField('D&eacute;bito','debito');
		$edit->debito->rule='numeric';
		$edit->debito->insertValue='0.0';
		$edit->debito->css_class='inputnum';
		$edit->debito->size =20;
		$edit->debito->maxlength =18;

		$edit->status = new inputField('Estatus','status');
		$edit->status->rule='';
		$edit->status->size =3;
		$edit->status->maxlength =1;

		$edit->usuario = new autoUpdateField('usuario',$this->secu->usuario(),$this->secu->usuario());
		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
			return true;
		}

		if($edit->on_error()){
			$rt=array(
				'status' =>'B',
				'mensaje'=>preg_replace('/<[^>]*>/', '', $edit->error_string),
				'pk'     =>null,
			);
			echo json_encode($rt);
			$act = false;
			return true;
		}

		if($edit->on_show()){
			$conten['form'] =&  $edit;
			$this->load->view('view_bconci', $conten);
		}
	}

	function _pre_insert($do){
		$codbanc  = $do->get('codbanc');
		$fecha    = $do->get('fecha');
		$dbfecha  = $this->db->escape($fecha);
		$dbcodbanc= $this->db->escape($codbanc);


		$ant = intval($this->datasis->dameval('SELECT COUNT(*) FROM bconci WHERE codbanc='.$dbcodbanc.' AND fecha='.$dbfecha));
		if($ant>0){
			$do->error_message_ar['pre_ins']='Ya existe una conciliacion con esa fecha para el mismo banco.';
			return false;
		}


		$row = $this->datasis->damerow('SELECT numcuent,banco FROM banc WHERE codbanc='.$dbcodbanc);
		if(!empty($row)){
			$do->set('numcuent',$row['numcuent']);
			$do->set('banco'   ,$row['banco']);
		}else{
			$do->error_message_ar['pre_ins']='Banco no valido';
			return false;
		}

		$cana=0;
		$this->mSQLs=array();
		foreach($_POST as $ind=>$val){
			if (preg_match("/^itid_[0-9]+$/", $ind) && $val>0) {
				$dbval=$this->db->escape($val);
				$this->mSQLs[] = "UPDATE bmov SET concilia=${dbfecha} WHERE id=${dbval}";
				$cana++;
			}
		}

		if($cana==0){
			$do->error_message_ar['pre_ins']='Necesita seleccionar al menos un efecto.';
			return false;
		}

		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='No se puede editar una conciliaci&oacute;n, debe eliminarla y volverla a hace.';
		return true;
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='';
		return true;
	}

	function _post_insert($do){
		$fecha = $do->get('fecha');
		foreach($this->mSQLs AS $mSQL){
			$this->db->simple_query($mSQL);
		}

		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary fecha ${fecha}");
	}

	function _post_update($do){
		$fecha = $do->get('fecha');
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary fecha ${fecha}");
	}

	function _post_delete($do){
		$fecha  = $do->get('fecha');
		$dbfecha= $this->db->escape($fecha);

		$mSQL= 'UPDATE bmov SET concilia=\'0000-00-00\' WHERE concilia='.$dbfecha;
		$this->db->simple_query($mSQL);

		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits ${primary} fecha ${fecha}");
	}

	function instalar(){
		if (!$this->db->table_exists('bconci')) {
			$mSQL="CREATE TABLE `bconci` (
			  `fecha` date DEFAULT NULL,
			  `codbanc` char(2) DEFAULT NULL,
			  `numcuent` varchar(18) DEFAULT NULL,
			  `banco` varchar(30) DEFAULT NULL,
			  `saldoi` decimal(18,2) DEFAULT NULL,
			  `saldof` decimal(18,2) DEFAULT NULL,
			  `deposito` decimal(18,2) DEFAULT NULL,
			  `credito` decimal(18,2) DEFAULT NULL,
			  `cheque` decimal(18,2) DEFAULT NULL,
			  `debito` decimal(18,2) DEFAULT NULL,
			  `status` char(1) DEFAULT NULL,
			  `usuario` varchar(4) DEFAULT NULL,
			  `estampa` date DEFAULT NULL,
			  `hora` varchar(8) DEFAULT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('bconci');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}
