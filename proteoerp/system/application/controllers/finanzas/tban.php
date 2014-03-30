<?php
class Tban extends Controller {
	var $mModulo = 'TBAN';
	var $titp    = 'Instituciones Bancarias';
	var $tits    = 'Instituciones Bancarias';
	var $url     = 'finanzas/tban/';

	function Tban(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'TBAN', $ventana=0 );
	}

	function index(){
		$this->instalar();
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
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

		$funciones = '
		function factivo(el, val, opts){
			var meco=\'<div><img src="'.base_url().'images/S.gif" width="20" height="18" border="0" /></div>\';
			if ( el == "N" ){
				meco=\'<div><img src="'.base_url().'images/N.gif" width="20" height="20" border="0" /></div>\';
			}
			return meco;
		}
		';


		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['funciones']   = $funciones;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('TBAN', 'JQ');
		$param['otros']       = $this->datasis->otros('TBAN', 'JQ');
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
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function tbanadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function tbanedit(){
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
		function tbanshow(){
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
		function tbandel() {
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
			autoOpen: false, height: 430, width: 550, modal: true,
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/TBAN').'/\'+res.id+\'/id\'').';
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

	//******************************************************************
	// Definicion del Grid o Tabla
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('activo');
		$grid->label('*');
		$grid->params(array(
			'align'        => '"center"',
			'search'        => 'false',
			'editable'      => $editar,
			'width'         => 20,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
			'formatter'     => 'factivo'
		));


		$grid->addField('cod_banc');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:3, maxlength: 3 }',
		));


		$grid->addField('nomb_banc');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));

		$grid->addField('rif');
		$grid->label('RIF');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

		$grid->addField('url');
		$grid->label('Sitio Web (Url)');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('debito');
		$grid->label('I.D.B. %');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 90,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('comitc');
		$grid->label('Com. TC%');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 90,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('comitd');
		$grid->label('Com. TD%');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 90,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('impuesto');
		$grid->label('I.S.L.R. %');
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


		$grid->addField('tipotra');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));

		$grid->addField('formaca');
		$grid->label('Calculo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));

		$grid->addField('formato');
		$grid->label('Formato de Cheque');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
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
		$grid->setAdd(    $this->datasis->sidapuede('TBAN','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('TBAN','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('TBAN','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('TBAN','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: tbanadd, editfunc: tbanedit, delfunc: tbandel, viewfunc: tbanshow");

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
		$mWHERE = $grid->geneTopWhere('tban');

		$response   = $grid->getData('tban', array(array()), array(), false, $mWHERE, 'cod_banc' );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM tban WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('tban', $data);
					echo "Registro Agregado";

					logusu('TBAN',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM tban WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM tban WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE tban SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("tban", $data);
				logusu('TBAN',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('tban', $data);
				logusu('TBAN',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM tban WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM tban WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM tban WHERE id=$id ");
				logusu('TBAN',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	// Editar
	//
	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});

		$("#rif").blur(function(){
			rif = $(this).val().toUpperCase();
			$(this).val(rif);
			patt = /[EJPGV][0-9]{4,9} */g;
			if(!patt.test(rif)){
				alert("El RIF o Cedula introducida no es correcta, por favor verifique e intente de nuevo.");
				$("#rif").trigger("focus");
				return false;
			}
		});
		';

		$edit = new DataEdit('', 'tban');

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

		$edit->cod_banc = new inputField('Codigo','cod_banc');
		$edit->cod_banc->rule      = '';
		$edit->cod_banc->size      = 5;
		$edit->cod_banc->maxlength = 3;
		$edit->cod_banc->mode      ='autohide';

		$edit->nomb_banc = new inputField('Nombre','nomb_banc');
		$edit->nomb_banc->rule='';
		$edit->nomb_banc->size =32;
		$edit->nomb_banc->maxlength =30;

		$edit->rif = new inputField('RIF','rif');
		$edit->rif->rule='';
		$edit->rif->size =17;
		$edit->rif->maxlength =15;

		$edit->activo = new dropdownField('Activo', 'activo');
		$edit->activo->style ='width:50px;';
		$edit->activo->rule='required|enum[S,N]';
		$edit->activo->options(array('S'=>'Si','N'=>'No' ));

		$edit->tipotra = new dropdownField('Transaccion','tipotra');
		$edit->tipotra->options(array('NC'=>'NOTA DE CREDITO','DE'=>'DEPOSITO' ));

		$edit->formaca = new dropdownField('Forma de Calculo','formaca');
		$edit->formaca->options(array('NETO'=>'NETO','BRUTO'=>'BRUTO' ));

		$edit->url = new inputField('Sitio Web','url');
		$edit->url->rule='';
		$edit->url->size =40;
		$edit->url->maxlength =40;

		$edit->debito = new inputField('I.D.B. %','debito');
		$edit->debito->rule='numeric';
		$edit->debito->css_class='inputnum';
		$edit->debito->size =8;
		$edit->debito->maxlength =6;
		$edit->debito->insertValue = "0.00";

		$edit->comitc = new inputField('Comision TC%','comitc');
		$edit->comitc->rule='numeric';
		$edit->comitc->css_class='inputnum';
		$edit->comitc->size =8;
		$edit->comitc->maxlength =6;
		$edit->comitc->insertValue = "0.00";

		$edit->comitd = new inputField('Comision TD%','comitd');
		$edit->comitd->rule='numeric';
		$edit->comitd->css_class='inputnum';
		$edit->comitd->size =8;
		$edit->comitd->maxlength =6;
		$edit->comitd->insertValue = "0.00";

		$edit->impuesto = new inputField('I.S.L.R. %','impuesto');
		$edit->impuesto->rule='numeric';
		$edit->impuesto->css_class='inputnum';
		$edit->impuesto->size =8;
		$edit->impuesto->maxlength =6;
		$edit->impuesto->insertValue = "0.00";

		$edit->formato = new inputField('Formato de Cheque','formato');
		$edit->formato->rule='';
		$edit->formato->size =40;
		$edit->formato->maxlength =40;

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
		$id        = $do->get('id');
		$cod_banc  = $this->datasis->dameval("SELECT cod_banc FROM tban WHERE id=${id}");
		$dbcod_banc= $this->db->escape($cod_banc);
		$check     = intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM banc WHERE tbanco=${dbcod_banc}"));
		$check    += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM sfpa WHERE  banco=${dbcod_banc}"));
		if($check == 0) {
			return true;
		}else{
			$do->error_message_ar['pre_del']='Existen bancos o pagos asociados al banco';
			return false;
		}
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
		return true;
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
		return true;
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
		return true;
	}

	function instalar(){
		if (!$this->db->table_exists('tban')) {
			$mSQL="CREATE TABLE `tban` (
			  `cod_banc` char(3) NOT NULL DEFAULT '',
			  `nomb_banc` varchar(30) DEFAULT NULL,
			  `url` varchar(30) DEFAULT NULL,
			  `debito` decimal(6,2) DEFAULT NULL,
			  `comitc` decimal(6,2) DEFAULT NULL,
			  `comitd` decimal(6,2) DEFAULT NULL,
			  `impuesto` decimal(6,2) DEFAULT NULL,
			  `tipotra` char(2) NOT NULL DEFAULT 'NC',
			  `formaca` varchar(6) DEFAULT 'NETA',
			  `formato` varchar(50) DEFAULT NULL COMMENT 'Formato de cheque',
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `cod_banc` (`cod_banc`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('tban');
		if(!in_array('formato',$campos)){
			$mSQL="ALTER TABLE tban ADD COLUMN formato VARCHAR(50) NULL DEFAULT NULL COMMENT 'Formato de cheque' AFTER formaca;";
			$this->db->query($mSQL);
			$mSQL="UPDATE tban SET formato=CONCAT('CHEQUE',cod_banc) WHERE formato IS NULL";
			$this->db->query($mSQL);
		}

		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE tban DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE tban DROP INDEX cod_banc');

			$this->db->query('ALTER TABLE tban ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->query('ALTER TABLE tban ADD UNIQUE INDEX cod_banc (cod_banc)');
		}

		if(!in_array('rif',$campos)){
			$mSQL="ALTER TABLE tban ADD COLUMN rif VARCHAR(15) NULL DEFAULT NULL COMMENT 'RIF del Banco' AFTER nomb_banc";
			$this->db->query($mSQL);
		}

		if(!in_array('activo',$campos)){
			$mSQL="ALTER TABLE `tban` ADD COLUMN activo CHAR(1) NULL DEFAULT 'S' COMMENT 'Activo' AFTER formato";
			$this->db->query($mSQL);
			$mSQL="UPDATE tban SET activo='S' WHERE activo<>'N' ";
			$this->db->query($mSQL);
		}
	}
}
?>
