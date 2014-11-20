<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Nomina extends Controller {
	var $mModulo = 'NOMI';
	var $titp    = 'NOMINAS GUARDADAS';
	var $tits    = 'NOMINAS GUARDADAS';
	var $url     = 'nomina/nomina/';

	function Nomina(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'NOMI', $ventana=0 );
	}

	function index(){
		if ( !$this->db->table_exists('view_nomina') ) {
			$mSQL = "
			CREATE ALGORITHM = UNDEFINED VIEW view_nomina AS
			SELECT a.contrato, a.trabaja, b.nombre, a.numero, a.frecuencia, a.fecha, a.fechap,
				a.estampa, a.usuario, a.transac,
				SUM(a.valor*(MID(concepto,1,1)<>'9' AND valor>0 AND concepto<>'PRES')) asigna,
				SUM(a.valor*(MID(concepto,1,1)<>'9' AND valor<0 AND concepto<>'PRES')) deduc,
				SUM(a.valor*(MID(concepto,1,1)<>'9')) total,
				SUM(a.valor*(concepto='PRES')) presta,
				SUM(a.valor*(MID(concepto,1,1)='9')) patronal
			FROM nomina a join noco b on a.contrato = b.codigo
			GROUP BY a.numero";
			$this->db->query($mSQL);
		}

		if ( !$this->datasis->iscampo('nomina','id') ) {
			$this->db->simple_query('ALTER TABLE nomina DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE nomina ADD INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE nomina ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 900, 600, substr($this->url,0,-1) );
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
		$grid->wbotonadd(array('id'=>'regene' , 'img'=>'images/repara.png',  'alt' => 'Regenerar Nomina', 'label'=>'Regenerar Nomina', 'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'imprime', 'img'=>'assets/default/images/print.png','alt' => 'Imprimir recibos', 'label'=>'Imprimir Prenomina'));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '
		function ltransac(el, val, opts){
			var meco=\'<div><a href="#" onclick="tconsulta(\'+"\'"+el+"\'"+\');">\' +el+ \'</a></div>\';
			return meco;
		};
		';

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('NOMI', 'JQ');
		$param['otros']       = $this->datasis->otros('NOMI', 'JQ');
		$param['funciones']   = $funciones;
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

		$bodyscript .= '
		function tconsulta(transac){
			if (transac)	{
				window.open(\''.site_url('contabilidad/casi/localizador/transac/procesar').'/\'+transac, \'_blank\', \'width=800, height=600, scrollbars=yes, status=yes, resizable=yes,screenx=((screen.availHeight/2)-300), screeny=((screen.availWidth/2)-400)\');
			} else {
				$.prompt("<h1>Transaccion invalida</h1>");
			}
		};';

		$bodyscript .= '
		function nominaadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function nominaedit(){
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
		function nominashow(){
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
		$("#regene").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				var mrege =
				{
					state0: {
						html: "<h1>Regenerar Nomina "+ret.numero+"</h1>Util para asegurar que todos los movimientos relacionados estan efectivamente cargados en el sistema administrativo.",
						buttons: { Regenerar: true, Cancelar: false },
						submit: function(e,v,m,f){
							if (v) {
								mId = id;
								$.post("'.site_url($this->url.'nomirege').'/"+ret.numero, function(data){
									try{
										var json = JSON.parse(data);
										if (json.status == "A"){
											$.prompt.getStateContent(\'state1\').find(\'#in_prome2\').text(json.mensaje);
											$.prompt.goToState(\'state1\');
											jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
										}else{
											$.prompt.getStateContent(\'state1\').find(\'#in_prome2\').text(json.mensaje);
											$.prompt.goToState(\'state1\');
										}
									}catch(e){
										$("#fborra").html(data);
										$("#fborra").dialog( "open" );
									}
								});
								return false;
							}
						}
					},
					state1: {
						html: "<h1>Resultado</h1><span id=\'in_prome2\'></span>",
						focus: 1,
						buttons: { Ok:true }
					}
				};

				$.prompt(mrege);
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		});';

		$bodyscript .= '
		function nominadel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				var mrever =
				{
					state0: {
						html: "<h1>Reverar Pre-Nomina "+ret.numero+"</h1>",
						buttons: { Reversar: true, Cancelar: false },
						submit: function(e,v,m,f){
							if (v) {
								mId = id;
								$.post("'.site_url($this->url.'nomirev').'/"+ret.numero, function(data){
									try{
										var json = JSON.parse(data);
										if (json.status == "A"){
											$.prompt.getStateContent(\'state1\').find(\'#in_prome2\').text(json.mensaje);
											$.prompt.goToState(\'state1\');
											jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
										}else{
											$.prompt.getStateContent(\'state1\').find(\'#in_prome2\').text(json.mensaje);
											$.prompt.goToState(\'state1\');
											apprise("Registro no se puede eliminado");
										}
									}catch(e){
										$("#fborra").html(data);
										$("#fborra").dialog( "open" );
									}
								});
								return false;
							}
						}
					},
					state1: {
						html: "<h1>Resultado</h1><span id=\'in_prome2\'></span>",
						focus: 1,
						buttons: { Ok:true }
					}
				};

				$.prompt(mrever);
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};
		';

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
		jQuery("#imprime").click( function(){
			window.open(\''.site_url('formatos/descargar/RECIBO/').'/\', \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes\');
		});';

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
									'.$this->datasis->jwinopen(site_url('formatos/ver/NOMINA').'/\'+res.id+\'/id\'').';
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

		$bodyscript .= '});';
		$bodyscript .= '</script>';

		return $bodyscript;
	}

	//******************************************************************
	// Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
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

		$grid->addField('contrato');
		$grid->label('Contrato');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));

		$grid->addField('frecuencia');
		$grid->label('Frecuencia');
		$grid->params(array(
			'align'         => '"center"',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
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


		$grid->addField('asigna');
		$grid->label('Asigna');
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


		$grid->addField('deduc');
		$grid->label('Deduc');
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


		$grid->addField('presta');
		$grid->label('Presta');
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


		$grid->addField('patronal');
		$grid->label('Patronal');
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

		$grid->addField('fechap');
		$grid->label('Fechap');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('trabaja');
		$grid->label('Trabaja');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
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


		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));


		$grid->addField('transac');
		$grid->label('Transac');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
			'formatter'     => 'ltransac'
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
		$grid->setAdd(    false ); //$this->datasis->sidapuede('NOMI','INCLUIR%' ));
		$grid->setEdit(   false ); //$this->datasis->sidapuede('NOMI','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('NOMI','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('NOMI','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: nominaadd, editfunc: nominaedit, delfunc: nominadel, viewfunc: nominashow");

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

	/*******************************************************************
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('view_nomina');

		$response   = $grid->getData('view_nomina', array(array()), array(), false, $mWHERE, 'numero', 'DESC' );
		$rs = $grid->jsonresult( $response);

		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion
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
			echo 'Deshabilitado';
		}elseif($oper == 'edit'){
			echo 'Deshabilitado';
		}elseif($oper == 'del'){
			echo 'Deshabilitado';
		}
	}

	//******************************************************************
	//  REVERSAR NOMINA
	//
	function nomirev( $nomina = 0 ) {
		if ( $nomina == 0)
			$nomina = $this->uri->segment($this->uri->total_segments());

		if ( $nomina == 0) {
			$rt=array(
				'status' =>'B',
				'mensaje'=>'Numero de nomina invalido',
				'pk'     => $nomina
			);
			echo json_encode($rt);
			return true;
		}

		if ($this->datasis->dameval('SELECT COUNT(*) FROM nomina WHERE numero="'.$nomina.'"') == 0 ) {
			$rt=array(
				'status' =>'B',
				'mensaje'=>'NO EXISTE NINGUNA NOMINA CON ESE NUMERO!',
				'pk'     => $nomina
			);
			echo json_encode($rt);
			return true;
		}

		if ($this->datasis->dameval("SELECT COUNT(*) FROM nomina") == 0 ){
			$rt=array(
				'status' =>'B',
				'mensaje'=>'No hay ninguna Nomina Generada; genere una primero',
				'pk'     => $nomina
			);
			echo json_encode($rt);
			return true;
		}

		$mSQL  = "SELECT fecha, fechap, contrato, trabaja, transac FROM nomina WHERE numero=".$nomina." LIMIT 1";
		$mreg  = $this->datasis->damereg($mSQL);

		$fecha    = $mreg['fecha'];
		$fechap   = $mreg['fechap'];
		$contrato = $mreg['contrato'];
		$trabaja  = $mreg['trabaja'];
		$transac  = $mreg['transac'];
		$gsernum  = '';

		$mSQL = "SELECT b.tipo FROM prenom a JOIN noco b ON a.contrato=b.codigo LIMIT 1";
		$frec = $this->datasis->dameval($mSQL);

		// VERIFICA SI NO ESTA PAGADA
		$mSQL = "SELECT abonos FROM sprm WHERE transac='".$transac."' AND tipo_doc IN ('ND') ";
		if ($this->datasis->dameval($mSQL) > 0) {
			echo "NOMINA TIENE CANCELACIONES;NO SE PUEDE ELIMINAR ";
			return true;
		}

		// ELIMINA POR TRANSACCION GENERAR ITEMS
		$this->db->query("DELETE FROM gser   WHERE transac='".$transac."'");
		$this->db->query("DELETE FROM gitser WHERE transac='".$transac."'");

		// GENERA CXP
		$this->db->query("DELETE FROM sprm WHERE transac='".$transac."'");

		// PRESTAMOS
		$mSQL = "SELECT * FROM smov WHERE num_ref=".$nomina." AND transac='".$transac."' ";
		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){

				$COD_CLI  = $row->cod_cli;
				$TIPO_DOC = $row->tipo_doc;
				$NUMERO   = $row->numero;
				$FECHA    = $row->fecha;
				$MONTO    = $row->monto;

				$mSQL= "DELETE FROM smov
						WHERE cod_cli='".$COD_CLI."' AND
							tipo_doc='".$TIPO_DOC."' AND fecha='".$FECHA."'
							AND numero='".$NUMERO."' AND transac='".$transac."' ";
				$this->db->query($mSQL);

				// DESCARGA EL MOVIMIENTO EN ITCCLI
				$mSQL= "SELECT abono, tipo_doc, numero, cod_cli, fecha FROM itccli
						WHERE numccli='".$NUMERO."' AND tipoccli='".$TIPO_DOC."'
						AND cod_cli='".$COD_CLI."' AND transac='".$transac."' ";
				$mREG = $this->datasis->damereg($mSQL);

				$mSQL= "DELETE FROM itccli
						WHERE numccli='".$NUMERO."' AND tipoccli='".$TIPO_DOC."'
						AND cod_cli='".$COD_CLI."' AND transac='".$transac."' ";
				$this->db->query($mSQL);

				// ACTUALIZA EL DOCUMENTO ORIGEN
				$mSQL = "UPDATE smov SET abonos=abonos-".$mREG['abono']."
				WHERE tipo_doc='".$mREG['tipo_doc']."' AND
				numero='".$mREG['numero']."' AND
				cod_cli='".$mREG['cod_cli']."' AND fecha='".$mREG['fecha']."'";

				$this->db->query($mSQL);
			}
		}

		$this->nomprenom($nomina);

		$mSQL  = "DELETE FROM nomina WHERE numero='".$nomina."'";
		$this->db->query($mSQL);

		logusu('NOMI',"NOMINA $nomina REVERSADA");

		$rt=array(
			'status' =>'A',
			'mensaje'=>'Nomina Reversada',
			'pk'     => $nomina
		);
		echo json_encode($rt);


	}

	//***************************************
	//
	//  DEVUELVE LA NOMINA A PRENOMINA
	//
	function nomprenom( $nomina) {

		$mreg = $this->datasis->damereg("SELECT fecha, fechap, contrato, trabaja, frecuencia FROM nomina WHERE numero='".$nomina."' LIMIT 1");

		$fecha    = $mreg['fecha'];
		$fechap   = $mreg['fechap'];
		$contrato = $mreg['contrato'];
		$trabaja  = $mreg['trabaja'];
		$frec     = $mreg['frecuencia'];

		$dbcontrato= $this->db->escape($contrato);
		$dbtrabaja = $this->db->escape($trabaja);
		$dbnomina  = $this->db->escape($nomina);
		$dbfecha   = $this->db->escape($fecha);
		$dbfechap  = $this->db->escape($fechap);
		$dbfrec    = $this->db->escape($frec);

		// MANDA LA NOMINA DE REGRESO A PRENOM
		$mSQL = "TRUNCATE prenom ";
		$this->db->query($mSQL);

		$mSQL ="INSERT IGNORE INTO prenom (contrato, codigo, nombre, concepto, tipo, descrip, grupo, formula, monto, fecha, valor,fechap, trabaja )
			SELECT contrato, codigo, nombre, concepto, tipo, descrip, grupo, formula, monto, fecha, valor, fechap, trabaja
			FROM nomina
			WHERE numero=${dbnomina} AND concepto<>'PRES' ";
		$this->db->query($mSQL);

		$mSQL = "INSERT IGNORE INTO prenom (contrato, codigo, nombre, concepto, grupo, tipo, descrip, formula, monto, fecha, fechap )
			SELECT ${dbcontrato}, b.codigo, CONCAT(RTRIM(b.apellido),'/',b.nombre) nombre,
			a.concepto, c.grupo, a.tipo, a.descrip, a.formula, 0, ${dbfecha}, ${dbfechap}
			FROM asig a JOIN pers b ON a.codigo=b.codigo
			JOIN conc c ON a.concepto=c.concepto
			WHERE b.tipo=${dbfrec} AND b.contrato=${dbcontrato} AND b.status='A' ";
		$this->db->query($mSQL);

		$mSQL = "INSERT IGNORE INTO prenom (contrato, codigo,nombre, concepto, grupo, tipo, descrip, formula, monto, fecha, fechap )
			SELECT ${dbcontrato}, b.codigo, CONCAT(RTRIM(b.apellido),'/',b.nombre) nombre,
				a.concepto, a.grupo, a.tipo, a.descrip, a.formula, 0, ${dbfecha}, ${dbfecha}
			FROM conc a JOIN itnoco c ON a.concepto=c.concepto
			JOIN pers b ON b.contrato=c.codigo
			WHERE c.codigo=${dbcontrato} AND b.status='A' ";
		$this->db->query($mSQL);

		$this->db->query("UPDATE prenom SET trabaja=${dbtrabaja}");

		$this->load->library('pnomina');
		$this->pnomina->creapretab();
		$this->pnomina->llenapretab();
	}



	//******************************************************************
	//  REGENERA LA NOMINA SIN BORRARLA
	//
	function nomirege( $nomina ) {

		$mNOMI = $this->datasis->dameval("SELECT ctaac FROM conc WHERE formula='XSUELDO' ");
		$dbnomina  = $this->db->escape($nomina);

		//COLOCAR + LAS NOMINAS
		$mSQL = "UPDATE nomina SET valor=ABS(valor) WHERE MID(concepto,1,1)='9' ";
		$this->db->query($mSQL);

		$mSQL = "SELECT COUNT(*) FROM nomina WHERE numero=${dbnomina}";
		if($this->datasis->dameval($mSQL) == 0){
			$rt=array(
				'status' =>'B',
				'mensaje'=>'NO EXISTE NINGUNA NOMINA CON EL NUMERO '.$nomina,
				'pk'     => $nomina
			);
			echo json_encode($rt);
			return true;
		}

		$mreg = $this->datasis->damereg("SELECT transac, fecha FROM nomina WHERE numero=${dbnomina}");
		$mTRANSAC = $mreg['transac'];
		$FECHA    = $mreg['fecha'];

		$mSQL = "DELETE FROM gitser WHERE transac='".$mTRANSAC."' ";
		$this->db->query($mSQL);

		$mSQL= "INSERT INTO gitser (fecha, numero, proveed, codigo, descrip, precio, iva, importe, unidades, fraccion, almacen, departa, sucursal, usuario, estampa, transac)
				SELECT a.fechap fecha, a.numero, b.ctaac proveed, b.ctade codigo,   CONCAT(RTRIM(b.descrip),' ',d.depadesc) descrip, SUM(a.valor), 0, SUM(a.valor), 0, 0, '', d.enlace, c.sucursal, a.usuario, a.estampa, a.transac
				FROM nomina a JOIN conc b ON a.concepto=b.concepto JOIN pers c ON a.codigo=c.codigo JOIN depa d ON c.depto=d.departa
				WHERE valor<>0 AND tipod='G' AND a.numero=${dbnomina} GROUP BY ctade, d.enlace ";
		$this->db->query($mSQL);

		//GENERA EL ENCABEZADO DE GSER
		$mSQL = "DELETE FROM gser WHERE  transac='".$mTRANSAC."' ";
		$this->db->query($mSQL);

		$mSQL= "INSERT INTO gser (fecha, numero, proveed, nombre, vence, totpre,  totiva, totbruto, reten, totneto, codb1, tipo1, cheque1, monto1, credito, anticipo, orden, tipo_doc, usuario, estampa, transac)
				SELECT a.fechap fecha, a.numero numero, b.ctaac proveed, d.nombre, a.fechap vence, SUM(a.valor) totpre, 0 totiva, SUM(a.valor) totbruto, 0 reten, SUM(a.valor) totneto, '' codb1, '' tipo1, '' cheque1,
					(
						SELECT ABS(SUM(bbb.monto)) FROM (SELECT sum(d.valor) monto FROM nomina d JOIN conc e ON d.concepto=e.concepto JOIN pers f ON d.codigo=f.codigo WHERE d.valor<>0 AND e.tipod!='G' AND d.numero=${dbnomina}
						UNION ALL
						SELECT SUM(valor) monto FROM nomina g WHERE g.numero=${dbnomina} AND g.concepto='PRES' ) bbb)*(b.ctaac='".$mNOMI."'
					) monto1,
					sum(a.valor)+(
						SELECT SUM(valor) FROM (SELECT sum(valor) valor FROM nomina a JOIN conc b ON a.concepto=b.concepto JOIN pers c ON a.codigo=c.codigo WHERE valor<>0 AND tipod!='G' AND a.numero='".$nomina."'
						UNION ALL
						SELECT SUM(valor) FROM nomina a WHERE a.numero=${dbnomina} AND concepto='PRES' ) aaa)*(b.ctaac='".$mNOMI."') credito,
					0 anticipo, '', 'GA', a.usuario, a.estampa, a.transac FROM nomina a JOIN conc b ON a.concepto=b.concepto
				JOIN pers c ON a.codigo=c.codigo JOIN sprv d ON ctaac=d.proveed
				WHERE valor<>0 AND tipod='G' AND a.numero=${dbnomina} GROUP BY ctaac ";
		$this->db->query($mSQL);

		//Borras los que empiezan por N
		$mSQL = "DELETE FROM sprm WHERE transac='".$mTRANSAC."' AND numero='N".substr($nomina,1,7)."' ";
		$this->db->query($mSQL);

		// GENERA LAS ND EN PROVEEDORES SPRM
		// debe revisar cuales esta
		$mSQL= "SELECT b.ctaac, a.fechap fecha, sum(valor) valor, d.nombre, b.descrip
				FROM nomina a JOIN conc b ON a.concepto=b.concepto
				JOIN pers c ON a.codigo=c.codigo
				JOIN sprv d ON b.ctaac=d.proveed
				WHERE a.valor<>0 AND b.tipod='P' AND b.tipoa='P' AND a.numero='".$nomina."'
				GROUP BY b.ctaac ";

		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){

				$data = array();
				$data["cod_prv"]  = $row->ctaac;
				$data["nombre"]   = $row->nombre;
				$data["tipo_doc"] = 'ND';
				$data["fecha"]    = $row->fecha;
				$data["monto"]    = abs($row->valor);
				$data["vence"]    = $row->fecha;
				$data["tipo_ref"] = 'GA';
				$data["num_ref"]  = $nomina;
				$data["observa1"] = $row->descrip;
				$data["observa2"] = 'NOMINA';
				$data["reteiva"]  = 0;
				$data["codigo"]   = 'NOCON';
				$data["descrip"]  = 'NOMINA';
				$data["impuesto"] = 0;

				$mSQL = "SELECT COUNT(*) FROM sprm WHERE transac='".$mTRANSAC."' AND cod_prv='".$row->ctaac."' AND tipo_doc='ND' AND fecha='".$row->fecha."' AND numero<>'".$nomina."'";
				if ( $this->datasis->dameval($mSQL) == 0 ){
					//SI NO ESTA
					$mCONTROL = $this->datasis->fprox_numero("nsprm");
					$mNOTADEB = $this->datasis->fprox_numero("num_nd");

					$data["numero"]  = $mNOTADEB;
					$data["abonos"]  = 0;
					$data["control"] = $mCONTROL;

					$this->db->insert('sprm', $data);
				} else {
					$mSQL = "SELECT numero, abonos, control, id FROM sprm WHERE transac='".$mTRANSAC."' AND cod_prv='".$row->ctaac."' AND tipo_doc='ND' AND fecha='".$row->fecha."'";
					$mREG = $this->datasis->damereg($mSQL);
					$data["numero"]  = $mREG['numero'];
					$data["abonos"]  = $mREG['abonos'];
					$data["control"] = $mREG['control'];

					$this->db->update('sprm', $data, "id=".$mREG['id']);
				}
				$this->db->query($mSQL);
			}
		}

		$mSQL = "DELETE FROM sprm WHERE transac='".$mTRANSAC."' AND numero='".$nomina."'";
		$this->db->query($mSQL);

		$mSQL= "INSERT IGNORE INTO sprm (tipo_doc, fecha, numero, cod_prv, nombre, vence, monto, impuesto, tipo_ref, num_ref, codigo, descrip, usuario, estampa, transac, observa1 )
				SELECT 'ND' tipo_doc, fecha, '".$nomina."'  numero, proveed, nombre, vence, credito, 0, 'GA', '' ,'NOCON', 'NOMINA', usuario, estampa, transac, 'NOMINA '
				FROM gser WHERE tipo_doc='GA' AND numero='".$nomina."' ";
		$this->db->query($mSQL);

		// PRESTAMOS
		$mSQL= "SELECT
					c.cod_cli, c.nombre, c.tipo_doc, c.numero, c.fecha, a.fechap,
					a.codigo, b.cuota, a.valor, a.estampa, a.usuario, a.hora, a.transac,
					IF(c.monto-c.abonos-b.cuota>0,b.cuota,c.monto-c.abonos) cuotac, c.monto
				FROM nomina a
				JOIN pres   b ON a.codigo=b.codigo
				JOIN smov   c ON b.cod_cli=c.cod_cli AND b.tipo_doc=c.tipo_doc AND b.numero=c.numero
				AND c.monto<>c.abonos
				WHERE a.concepto='PRES' AND a.numero='".$nomina."' AND b.cuota = abs(a.valor)";

		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				//Busca si ya existe
				$mSQL = "SELECT COUNT(*) FROM smov WHERE cod_cli='".$row->cod_cli."' AND tipo_doc='NC' AND transac='".$mTRANSAC."' AND monto=".abs($row->valor);

				if ( $this->datasis->dameval($mSQL) == 0 ) {
					$mCONTROL = $this->datasis->fprox_numero("nsmov");
					$mNOTACRE = $this->datasis->fprox_numero("nccli");

					$data = array();
					$data["cod_cli"]  = $row->cod_cli;
					$data["nombre"]   = $row->nombre;
					$data["tipo_doc"] = 'NC';
					$data["numero"]   = $mNOTACRE;
					$data["fecha"]    = $row->fechap;
					$data["monto"]    = abs($row->valor);
					$data["impuesto"] = 0;
					$data["vence"]    = $row->fechap;
					$data["abonos"]   = abs($row->valor);
					$data["tipo_ref"] = 'GA';
					$data["num_ref"]  = $nomina;
					$data["observa1"] = 'PAGO A '.$row->tipo_doc.$row->numero;
					$data["observa2"] = 'POR DESCUENTO DE NOMINA';
					$data["control"]  = $mCONTROL;
					$data["codigo"]   = 'NOCON';
					$data["descrip"]  = 'NOMINA';
					$data["transac"]  = $mTRANSAC;
					$data["estampa"]  = $row->estampa;
					$data["hora"]     = $row->hora;
					$data["usuario"]  = $row->usuario;
					$this->db->insert('smov', $data );

					// ACTUALIZA EL DOCUMENTO ORIGEN
					$mSQL = "
						UPDATE smov SET abonos=abonos+".abs($row->valor)."
						WHERE tipo_doc='$row->tipo_doc' AND numero='".$row->numero."'
						AND cod_cli='".$row->cod_cli."' AND fecha=".$row->fecha." LIMIT 1";
					$this->db->query($mSQL);

					// CARGA EL MOVIMIENTO EN ITCCLI
					$data = array();
					$data["numccli"]  = $mNOTACRE;
					$data["tipoccli"] = 'NC';
					$data["cod_cli"]  = $row->cod_cli;
					$data["tipo_doc"] = $row->tipo_doc;
					$data["numero"]   = $row->numero;
					$data["fecha"]    = $row->fecha;
					$data["monto"]    = $row->monto;
					$data["abono"]    = abs($row->valor);
					$data["transac"]  = $mTRANSAC;
					$data["estampa"]  = $row->estampa;
					$data["hora"]     = $row->hora;
					$data["usuario"]  = $row->usuario;
					$this->db->insert('itccli', $data );
				}
			}
		}

		logusu('NOMI',"NOMINA $nomina REGENERADA");

		$rt=array(
			'status' =>'A',
			'mensaje'=>'NOMINA REGENERADA '.$nomina,
			'pk'     => $nomina
		);
		echo json_encode($rt);


	}


/*
	function dataedit(){
		$this->rapyd->load("dataedit");
		$edit = new DataEdit("clientes", "nomina");
		$edit->back_url = site_url("nomina/nomina/filteredgrid");
		//$edit->script($script, "create");
		//$edit->script($script, "modify");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';

		$edit->numero =  new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->maxlength=8;
		$edit->numero->size =10;
		$edit->numero->rule ="required";

		$edit->frecuencia = new dropdownField("Tipo de N&oacute;mina", "frecuencia");
		$edit->frecuencia->option("","");
		$edit->frecuencia->options(array("Q"=> "Quincenal","M"=>"Mensual","S"=>"Semanal"));
		$edit->frecuencia->style = "width:100px;";

		$edit->contrato = new dropdownField("Contrato", "contrato");
		$edit->contrato->option("","");
		$edit->contrato->options('SELECT codigo, nombre FROM noco');
		$edit->contrato->style = "width:300px;";

		$edit->depto = new dropdownField("Departamento", "depto");
		$edit->depto->option("","");
		$edit->depto->options('SELECT departa,descrip FROM depa');
		$edit->depto->style = "width:200px;";

		$edit->codigo = new dropdownField("C&oacute;digo", "codigo");
		//$edit->codigo->_dataobject->db_name="trim(codigo)";
		$edit->codigo->option("","");
		$edit->codigo->options("SELECT codigo,concat(trim(apellido),' ',trim(nombre)) nombre FROM pers ORDER BY apellido");
		$edit->codigo->style = "width:100px;";
		$edit->codigo->mode="autohide";

		$edit->nombre =  new inputField("Nombre", "nombre");
		$edit->nombre->mode="autohide";
		$edit->nombre->maxlength=30;
		$edit->nombre->size=40;

		$edit->concepto = new dropdownField("Concepto", "concepto");
		$edit->concepto->option("","");
		$edit->concepto->options('SELECT concepto,descrip FROM conc ORDER BY descrip');
		$edit->concepto->style = "width:200px;";

		$edit->tipo =  new inputField("Tipo","tipo");
		$edit->tipo->option("A","A");
		$edit->tipo->option("D","D");
		$edit->tipo->mode="autohide";
		$edit->tipo->style = "width:50px;";

		$edit->descrip =  new inputField("T. Descripci&oacute;n", "descrip");
		$edit->descrip->mode="autohide";
		$edit->descrip->maxlength=35;
		$edit->descrip->size =45;

		$edit->grupo =  new inputField("Grupo", "grupo");
		$edit->grupo->maxlength=4;
		$edit->grupo->size =6;

		$edit->formula =  new inputField("Formula", "formula");
		$edit->formula->maxlength=120;
		$edit->formula->size =80;

		$edit->monto = new inputField("Monto","monto");
		$edit->monto->size =17;
		$edit->monto->maxlength=14;
		$edit->monto->css_class='inputnum';
		$edit->monto->rule='numeric';

		$edit->fecha =  new DateonlyField("fecha", "fecha","d/m/Y");
		$edit->fecha->size = 12;

		$edit->cuota =  new inputField("Cuota", "cuota");
		$edit->cuota->maxlength=11;
		$edit->cuota->size =13;
		$edit->cuota->css_class='inputnum';
		$edit->cuota->rule='integer';

		$edit->cuotat =  new inputField("Cuota Total", "cuotat");
		$edit->cuotat->maxlength=11;
		$edit->cuotat->size =13;
		$edit->cuotat->css_class='inputnum';
		$edit->cuotat->rule='integer';

		$edit->valor =  new inputField("Valor", "valor");
		$edit->valor->maxlength=17;
		$edit->valor->size =20;
		$edit->valor->css_class='inputnum';
		$edit->valor->rule='numeric';

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "<h1>Nomina</h1>";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function grid() {
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 50;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : '[{"property":"fecha","direction":"DESC"}]';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;

		$where = $this->datasis->extjsfiltro($filters);

		$this->db->_protect_identifiers=false;
		$this->db->select('a.numero, a.fecha, a.contrato, sum(a.valor*(a.valor>0)) asigna, ABS(sum(a.valor*(a.valor<0))) deduc, b.nombre noconom');

		$this->db->from('nomina a');
		$this->db->join('noco b', 'a.contrato=b.codigo');
		$this->db->groupby('a.numero');
		if (strlen($where)>1){$this->db->where($where);	}

		$sort = json_decode($sort, true);
		for ($i=0;$i<count($sort);$i++) {
			$this->db->order_by($sort[$i]['property'],$sort[$i]['direction']);
		}

		$this->db->limit($limit, $start);
		$query = $this->db->get();
		$results =  $this->datasis->dameval("SELECT COUNT(*) FROM (SELECT numero FROM nomina GROUP BY numero) aaa");
		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}

	function gridtraba(){
		$nomina   = isset($_REQUEST['nomina'])  ? $_REQUEST['nomina']   :  0;
		if ($nomina == 0 ) $nomina = $this->datasis->dameval("SELECT MAX(numero) FROM nomina")  ;
		$mSQL = "SELECT codigo, nombre, sum(valor*(valor>0)*(MID(concepto,1,1)<>9)) asigna,  sum(valor*(valor<0)*(MID(concepto,1,1)<>9)) deduc, sum(valor*(MID(concepto,1,1)<>9)) saldo FROM nomina WHERE numero='$nomina' GROUP BY codigo";
		$query = $this->db->query($mSQL);
		$results =  $this->datasis->dameval("SELECT COUNT(*) FROM (SELECT codigo FROM nomina WHERE numero='$nomina' GROUP BY codigo) aaa");
		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}

	function gridconc(){
		$nomina   = isset($_REQUEST['nomina'])  ? $_REQUEST['nomina']   :  0;
		$codigo   = isset($_REQUEST['codigo'])  ? $_REQUEST['codigo']   :  0;
		if ($nomina == 0 ) $nomina = $this->datasis->dameval("SELECT MAX(numero) FROM nomina")  ;
		if ($codigo == 0 ) $codigo = $this->datasis->dameval("SELECT MIN(codigo) FROM nomina WHERE numero='$nomina'")  ;
		$mSQL = "SELECT concepto, descrip, valor*(valor>0) asigna, valor*(valor<0) deduc FROM nomina WHERE numero='$nomina' AND trim(codigo)='$codigo' ORDER BY concepto ";
		$query = $this->db->query($mSQL);
		$results =  $this->datasis->dameval("SELECT COUNT(*) FROM (SELECT codigo FROM nomina WHERE numero='$nomina' AND codigo='$codigo' GROUP BY concepto) aaa");
		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}



//****************************************************************
//
//
//
//****************************************************************
	function nomiextjs(){
		$encabeza='<table width="100%" bgcolor="#2067B5"><tr><td align="left" width="100px"><img src="'.base_url().'assets/default/css/templete_01.jpg" width="120"></td><td align="center"><h1 style="font-size: 20px; color: rgb(255, 255, 255);" onclick="history.back()">NOMINAS GUARDADAS</h1></td><td align="right" width="100px"><img src="'.base_url().'assets/default/images/cerrar.png" alt="Cerrar Ventana" title="Cerrar Ventana" onclick="parent.window.close()" width="25"></td></tr></table>';

		$listados= $this->datasis->listados('nomi');
		$otros=$this->datasis->otros('nomi', 'nomina/nomina');

		$script = "
<script type=\"text/javascript\">
var BASE_URL   = '".base_url()."';
var BASE_PATH  = '".base_url()."';
var BASE_ICONS = '".base_url()."assets/icons/';
var BASE_UX    = '".base_url()."assets/js/ext/ux';

Ext.Loader.setConfig({ enabled: true });
Ext.Loader.setPath('Ext.ux', BASE_UX);

var urlApp = '".base_url()."';
var numeroactual = '';

Ext.require([
	'Ext.grid.*',
	'Ext.ux.grid.FiltersFeature',
	'Ext.data.*',
	'Ext.util.*',
	'Ext.tree.*',
	'Ext.state.*',
	'Ext.form.*',
	'Ext.window.MessageBox',
	'Ext.tip.*',
	'Ext.ux.CheckColumn',
	'Ext.toolbar.Paging',
	'Ext.dd.*'
]);

var mxs = ((screen.availWidth/2)-400);
var mys = ((screen.availHeight/2)-300);

var tipos = new Ext.data.SimpleStore({
    fields: ['abre', 'todo'],
    data : [ ['Q','Quincenal'],['S','Semanal'],['B','Bisemanal'],['M','Mensual'],['O','Otros'] ]
});

//Column Model
var NomiCol =
	[
		{ header: 'Numero',       width:  60, sortable: true,  dataIndex: 'numero',   field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Fecha',        width:  70, sortable: true,  dataIndex: 'fecha',    field: { type: 'datefield' }, filter: { type: 'date' }},
		{ header: 'Contrato',     width:  70, sortable: true,  dataIndex: 'contrato', field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Asignaciones', width:  80, sortable: true,  dataIndex: 'asigna',   field: { type: 'textfield' }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'Deducciones',  width:  80, sortable: true,  dataIndex: 'deduc',    field: { type: 'textfield' }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'Nombre',       width: 250, sortable: true,  dataIndex: 'noconom',  field: { type: 'textfield' }, filter: { type: 'string' }}
	];

//Column Model
var TrabaCol =
	[
		{ header: 'Codigo',     width:  50, sortable: true,  dataIndex: 'codigo',   field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Nombre',     width: 170, sortable: true,  dataIndex: 'nombre',   field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Asignacion', width:  80, sortable: true,  dataIndex: 'asigna',   field: { type: 'textfield' }, filter: { type: 'string' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'Deduccion',  width:  60, sortable: true,  dataIndex: 'deduc',    field: { type: 'textfield' }, filter: { type: 'string' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'Saldo',      width:  80, sortable: true,  dataIndex: 'saldo',    field: { type: 'textfield' }, filter: { type: 'string' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')}
	];

//Column Model
var ConcCol =
	[
		{ header: 'Conc.',       width:  40, sortable: true,  dataIndex: 'concepto', field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Descripcion', width: 150, sortable: true,  dataIndex: 'descrip',  field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Asignacion',  width:  80, sortable: true,  dataIndex: 'asigna',   field: { type: 'textfield' }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'Deduccion',   width:  60, sortable: true,  dataIndex: 'deduc',    field: { type: 'textfield' }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')}
	];

var nomina = '';
Ext.onReady(function(){
	/////////////////////////////////////////////////
	// Define los data model
	// Contratos
	Ext.define('Nomi', {
		extend: 'Ext.data.Model',
		fields: ['numero', 'fecha','contrato','noconom','asigna','deduc'],
		proxy: {
			type: 'ajax',
			noCache: false,
			api: {
				read   : urlApp + 'nomina/nomina/grid',
				method: 'POST'
			},
			reader: {
				type: 'json',
				root: 'data',
				successProperty: 'success',
				messageProperty: 'message',
				totalProperty: 'results'
			}
		}
	});

	//////////////////////////////////////////////////////////
	// create the Data Store
	var storeNomi = Ext.create('Ext.data.Store', {
		model: 'Nomi',
		autoLoad: false,
		autoSync: true,
		method: 'POST'
	});

	/////////////////////////////////////////////////
	// Define los data model
	// Contratos
	Ext.define('Traba', {
		extend: 'Ext.data.Model',
		fields: ['codigo', 'nombre', 'saldo', 'asigna', 'deduc' ],
		proxy: {
			type: 'ajax',
			noCache: false,
			api: {
				read   : urlApp + 'nomina/nomina/gridtraba',
				method: 'POST'
			},
			reader: {
				type: 'json',
				root: 'data',
				successProperty: 'success',
				messageProperty: 'message',
				totalProperty: 'results'
			}
		}
	});

	//////////////////////////////////////////////////////////
	// create the Data Store
	var storeTraba = Ext.create('Ext.data.Store', {
		model: 'Traba',
		autoLoad: false,
		autoSync: true,
		method: 'POST'
	});

	//////////////////////////////////////////////////////////////////
	// create the grid and specify what field you want
	// to use for the editor at each column.
	var gridTraba = Ext.create('Ext.grid.Panel', {
		width: '100%',
		height: '100%',
		store: storeTraba,
		title: 'Trabajadores',
		iconCls: 'icon-grid',
		frame: true,
		columns: TrabaCol
	});

	/////////////////////////////////////////////////
	// Define los data model
	// Contratos
	Ext.define('Conc', {
		extend: 'Ext.data.Model',
		fields: ['concepto', 'descrip', 'asigna', 'deduc' ],
		proxy: {
			type: 'ajax',
			noCache: false,
			api: {
				read   : urlApp + 'nomina/nomina/gridconc',
				method: 'POST'
			},
			reader: {
				type: 'json',
				root: 'data',
				successProperty: 'success',
				messageProperty: 'message',
				totalProperty: 'results'
			}
		}
	});

	//////////////////////////////////////////////////////////
	// create the Data Store
	var storeConc = Ext.create('Ext.data.Store', {
		model: 'Conc',
		autoLoad: false,
		autoSync: true,
		method: 'POST'
	});


	//////////////////////////////////////////////////////////////////
	// create the grid and specify what field you want
	// to use for the editor at each column.
	var gridConc = Ext.create('Ext.grid.Panel', {
		width: '100%',
		height: '100%',
		store: storeConc,
		title: 'Conceptos',
		iconCls: 'icon-grid',
		frame: true,
		columns: ConcCol
	});

	//////////////////////////////////////////////////////////////////
	// create the grid and specify what field you want
	// to use for the editor at each column.
	var gridNomi = Ext.create('Ext.grid.Panel', {
		width: '100%',
		height: '100%',
		store: storeNomi,
		title: 'Nominas Guardadas',
		iconCls: 'icon-grid',
		frame: false,
		columns: NomiCol,
		features: [ { ftype: 'filters', encode: 'json', local: false } ],
		dockedItems: [{
			xtype: 'toolbar',
			items: [{iconCls: 'icon-delete', text: 'Eliminar',  disabled: true, itemId: 'delete', scope: this, handler: this.onDeleteClick } ]
		}],
		// paging bar on the bottom
		bbar: Ext.create('Ext.PagingToolbar', {
			store: storeNomi,
			displayInfo: false,
			displayMsg: 'Pag No. {0} - Reg. {1} de {2}',
			emptyMsg: 'No se encontraron Registros.'
		}),
		onSelectChange: function(selModel, selections){	down('#delete').setDisabled(selections.length === 0); },
		onDeleteClick: function() {
			var selection = this.getView().getSelectionModel().getSelection()[0];
			Ext.MessageBox.show({
				title: 'Confirme',
				msg: 'Esta seguro?',
				buttons: Ext.MessageBox.YESNO,
				fn: function(btn){
					if (btn == 'yes') {
						if (selection) {
							//storeNomi.remove(selection);
						}
						storeNomi.load();
					}
				},
				icon: Ext.MessageBox.QUESTION
			});
		},
	});

	// Al cambiar seleccion de Nomina
	gridNomi.getSelectionModel().on('selectionchange', function(sm, selectedRecord) {
		if (selectedRecord.length) {
			gridNomi.down('#delete').setDisabled(selectedRecord.length === 0);
			nomina = selectedRecord[0].data.numero;
			gridTraba.setTitle(nomina+' '+selectedRecord[0].data.noconom);
			storeTraba.load({ params: { nomina: nomina }});
			storeConc.load({ params: { nomina: nomina }});
		}
	});

	// update panel body on selection change
	gridTraba.getSelectionModel().on('selectionchange', function(sm, selectedRecord) {
		if (selectedRecord.length) {
			storeConc.load({ params: { nomina: nomina, codigo: selectedRecord[0].data.codigo }});
			gridConc.setTitle(selectedRecord[0].data.nombre);
		}
	});


//////************ MENU DE ADICIONALES /////////////////
".$listados."

//////************ FIN DE ADICIONALES /////////////////

	var viewport = new Ext.Viewport({id:'simplevp', layout:'border', border:false, items:[{region: 'north',preventHeader: true,height: 40,minHeight: 40,html: '".$encabeza."'},{region:'west',width:190,border:false,autoScroll:true,title:'Lista de Opciones',	collapsible:true,split:true,collapseMode:'mini',layoutConfig:{animate:true},layout: 'accordion',items: [{title:'Listados',border:false,layout: 'fit',items: gridListado},{title:'Otras Funciones',border:false,layout: 'fit',html: '".$otros."'}]},{region:'east',	id: 'este',width:340,items: gridConc,border:false,preventHeader: true,collapsible:true	},{cls: 'irm-column irm-center-column irm-master-detail', region: 'center', title:  'center-title', layout: 'border', preventHeader: true, border: false, items: [{itemId: 'viewport-center-master',cls: 'irm-master',region: 'center',items: gridNomi},{itemId: 'viewport-center-detail',preventHeader: true,region: 'south',height: '40%',split: true, title: 'center-detail-title',margins: '0 0 0 0',items: gridTraba}]}]});
	storeNomi.load();
	storeTraba.load();
	storeConc.load();
});

</script>
";
		return $script;

	}

	function _post_insert($do){
		$codigo=$do->get('numero');
		$nombre=$do->get('nombre');
		logusu('nomina',"NOMINA $codigo NOMBRE  $nombre CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('numero');
		$nombre=$do->get('nombre');
		logusu('nomina',"NOMINA $codigo NOMBRE  $nombre  MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('numero');
		$nombre=$do->get('nombre');
		logusu('nomina',"NOMINA $codigo NOMBRE  $nombre  ELIMINADO ");
	}

	function chexiste($codigo){
		$codigo=$this->input->post('codigo');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE codigo='$codigo'");
		if ($check > 0){
			$nombre=$this->datasis->dameval("SELECT nombre FROM pers WHERE codigo='$codigo'");
			$this->validation->set_message('chexiste',"Personal con el codigo $codigo nombre $nombre ya existe");
			return FALSE;
		}else {
  		return TRUE;
		}
	}

Vacaciones
Periodo correspondiente al Ano 2011
Dias habiles vacaciones 15 + anos de servicio hasta 30
Dias de Descanso 6
Dias Feriados    1

Paga sobre la ultima nomina

sueldo integral * 15+anos
sueldo integral * descanso
sueldo integral * feriados




dias


*/
}
