<?php
class Tbrutas extends Controller {
	var $mModulo = 'TBRUTAS';
	var $titp    = 'RUTAS';
	var $tits    = 'RUTAS';
	var $url     = 'pasajes/tbrutas/';

	function Tbrutas(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'TBRUTAS', $ventana=0 );
	}

	function index(){
		$this->datasis->creaintramenu(array('modulo'=>'166','titulo'=>'Rutas','mensaje'=>'Rutas','panel'=>'PASAJES','ejecutar'=>'pasajes/tbrutas','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$grid->setHeight('160');
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$grid1->setHeight('130');
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 190, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = "radicional", $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

/*
		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);
*/

		//Botones Panel Izq
		$grid->wbotonadd(array("id"=>"agregad",   "img"=>"images/pdf_logo.gif",  "alt" => "Agrega Destino",   "label"=>"Agrega Destino", "tema"=>'anexos'));
		$grid->wbotonadd(array("id"=>"modifid",   "img"=>"images/pdf_logo.gif",  "alt" => "Modifica Destino", "label"=>"Modifica Destino", "tema"=>'anexos'));
		$grid->wbotonadd(array("id"=>"elimind",   "img"=>"images/pdf_logo.gif",  "alt" => "Elimina Destino",  "label"=>"Elimina Destino", "tema"=>'anexos'));

		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['readyLayout'] = $readyLayout;
		$param['SouthPanel']  = $SouthPanel;
		$param['centerpanel'] = $centerpanel;
		$param['listados']    = $this->datasis->listados('TBRUTAS', 'JQ');
		$param['otros']       = $this->datasis->otros('TBRUTAS', 'JQ');
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
	function bodyscript( $grid0, $grid1 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function tbrutasadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function tbrutasedit(){
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
		function tbrutasshow(){
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
		function tbrutasdel() {
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

/*
		$bodyscript .= '
		var mcome1 = "<h1>Generar Pre-Nomina</h1>"+
			"<center><p>Seleccione el Contrato:</p>"+"'.$noco.'</center><br>"+
			"<table align=\'center\'>"+
			"<tr><td>Fecha de Corte: </td><td><input id=\'mfechac\' name=\'mfechac\' size=\'10\' class=\'input\' value=\''.date('d/m/Y').'\'></td></tr>"+
			"<tr><td>Fecha de Pago:  </td><td><input id=\'mfechap\' name=\'mfechap\' size=\'10\' class=\'input\' value=\''.date('d/m/Y').'\'></td></tr>"+
			"</table>"
		;
		var mprepanom = 
		{
			state0: {
				html: mcome1,
				buttons: { Generar: true, Cancelar: false },
				submit: function(e,v,m,f){
					mnuevo = f.mcodigo;
					if (v) {
						$.post("'.site_url('nomina/prenom/geneprenom').'/", { contrato: f.mcontrato, fechac: f.mfechac, fechap: f.mfechac }, 
							function(data){
								$.prompt.getStateContent(\'state1\').find(\'#in_prome2\').text(data);
								$.prompt.goToState(\'state1\');
								$("#newapi'.$grid0.'").trigger("reloadGrid");
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
		$("#genepre").click( function() 
		{
			$.prompt(mprepanom);
			$("#mfechac").datepicker({dateFormat:"dd/mm/yy"});
			$("#mfechap").datepicker({dateFormat:"dd/mm/yy"});
		});
		';
*/

		$noco = $this->datasis->llenaopciones("SELECT codofi, CONCAT(codofi,' ', desofi) FROM tbofici ORDER BY codofi", false, 'moficina');
		$noco = str_replace('"',"'",$noco);


		//
		$bodyscript .= '
		jQuery("#modifid").click( function(){
			var id = jQuery("#newapi'.$grid1.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid1.'").jqGrid(\'getRowData\',id);
				$.prompt(mprepanom);
			} else { $.prompt("<h1>Por favor Seleccione un Destino</h1>");}
		});';

		//Agrgaga destinos
		$bodyscript .= '
		jQuery("#agregad").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				var mcome1 = "<h1>Destinos</h1>"+
					"<table align=\'center\'>"+
					"<tr><td>Oficina:</tdtd><td colspan=\'3\'>"+"'.$noco.'</td></tr>"+
					"<tr><td>Hora: </td><td><input               id=\'mhora\'    name=\'mhora\'    size=\'6\' class=\'input\' value=\'\'></td>"+
					"<td align=\'right\'>Mostrar:  </td><td align=\'left\'><input type=\'checkbox\' id=\'mmostrar\' name=\'mmostrar\' class=\'input\' value=\'S\'></td></tr>"+
					"</table>";
				var mprepanom = 
				{
					state0: {
						html: mcome1,
						buttons: { Generar: true, Cancelar: false },
						submit: function(e,v,m,f){
							moficina = f.moficina;
							if (v) {
								$.post("'.site_url('pasajes/tbrutas/destiadd').'/", { oficina: f.moficina, hora: f.mhora, mostrar: f.mmostrar, mid: id }, 
									function(data){
										$.prompt.getStateContent(\'state1\').find(\'#in_prome2\').text(data);
										$.prompt.goToState(\'state1\');
										$("#newapi'.$grid1.'").trigger("reloadGrid");
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
				$.prompt(mprepanom);
				$("#mhora").mask("99:99 a");

			} else { $.prompt("<h1>Por favor Seleccione un Destino</h1>");}
		});';


/*
				$.post("'.site_url('finanzas/mgas/dataedit/create').'",
				function(data){
					$("#fgasto").html(data);
					$("#fgasto").dialog({height: 400, width: 700, title: "Agregar Gasto"});
					$("#fgasto").dialog( "open" );
				});
*/


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
			autoOpen: false, height: 300, width: 500, modal: true,
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/TBRUTAS').'/\'+res.id+\'/id\'').';
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
	//
	//
	function destiadd(){
		$moficina = $this->input->post('oficina');
		$mhora    = $this->input->post('hora');
		$mostrar  = $this->input->post('mostrar');
		$mid   = $this->input->post('mid'); 
		
		
		echo "$moficina $mhora $mostrar $mid";
		
	}


	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;


		$grid->addField('codrut');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));


		$grid->addField('horsal');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));


		$grid->addField('tipuni');
		$grid->label('Bus');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'align'         => "'center'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('origen');
		$grid->label('Origen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 155,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->addField('destino');
		$grid->label('Destino Final');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 155,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));

		$grid->addField('tipserv');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
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
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');
		
		$grid->setOnSelectRow('
			function(id){
				if (id){
					jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
					$.ajax({
						url: "'.base_url().$this->url.'tabla/"+id,
						success: function(msg){
							$("#ladicional").html(msg);
						}
					});
				}
			}'
		);

		

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('TBRUTAS','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('TBRUTAS','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('TBRUTAS','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('TBRUTAS','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: tbrutasadd, editfunc: tbrutasedit, delfunc: tbrutasdel, viewfunc: tbrutasshow");

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
		$mWHERE = $grid->geneTopWhere('tbrutas');

		$response   = $grid->getData('tbrutas', array(array()), array(), false, $mWHERE, 'codrut' );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM tbrutas WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('tbrutas', $data);
					echo "Registro Agregado";

					logusu('TBRUTAS',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM tbrutas WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM tbrutas WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE tbrutas SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("tbrutas", $data);
				logusu('TBRUTAS',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('tbrutas', $data);
				logusu('TBRUTAS',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM tbrutas WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM tbrutas WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM tbrutas WHERE id=$id ");
				logusu('TBRUTAS',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codrut');
		$grid->label('Codrut');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('codofiorg');
		$grid->label('Origen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('codofides');
		$grid->label('Oficina');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));

		$grid->addField('desofi');
		$grid->label('Destino');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'left'",
			'edittype'      => "'text'",
			'width'         => 240,
		));


		$grid->addField('orden');
		$grid->label('Orden');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'width'         => 60,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));


		$grid->addField('mostrar');
		$grid->label('Ver');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'center'",
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
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


		$grid->showpager(false);
		$grid->setWidth('');
		$grid->setHeight('290');
		//$grid->setTitle($this->titp);
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    true ); //$this->datasis->sidapuede('TBDESTINOS','INCLUIR%' ));
		$grid->setEdit(   true ); //$this->datasis->sidapuede('TBDESTINOS','MODIFICA%'));
		$grid->setDelete( true ); //$this->datasis->sidapuede('TBDESTINOS','BORR_REG%'));
		$grid->setSearch( false); //$this->datasis->sidapuede('TBDESTINOS','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: tbdestinosadd, editfunc: tbdestinosedit, delfunc: tbdestinosdel, viewfunc: tbdestinosshow");

		#Set url
		$grid->setUrlput(site_url($this->url.'setdatait/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdatait/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdatait(){
		$id = $this->uri->segment(4);
		if ($id === false ){
			$id = $this->datasis->dameval("SELECT id FROM tbrutas ORDER BY codrut LIMIT 1");
		}
		if(empty($id)) return '';
		$dbid = $this->db->escape($id);

		$row = $this->datasis->damerow('SELECT codrut FROM tbrutas WHERE id='.$dbid);

		$codrut = $this->db->escape($row['codrut']);
		//$numero  = $this->db->escape($row['numero']);
		//$fecha   = $this->db->escape($row['fecha']);
		//$transac = $this->db->escape($row['transac']);

		$grid       = $this->jqdatagrid;
		$mSQL    = "SELECT a.*, b.desofi FROM tbdestinos a JOIN tbofici b ON a.codofides=b.codofi WHERE codrut=${codrut} AND codofiorg=MID(codrut,1,2) ORDER BY orden";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
/*
		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('tbdestinos');

		$response   = $grid->getData('tbdestinos', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
*/
	}

	/**
	* Guarda la Informacion
	*/
	function setDatait(){
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
				$check = $this->datasis->dameval("SELECT count(*) FROM tbdestinos WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('tbdestinos', $data);
					echo "Registro Agregado";

					logusu('TBDESTINOS',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM tbdestinos WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM tbdestinos WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE tbdestinos SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("tbdestinos", $data);
				logusu('TBDESTINOS',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('tbdestinos', $data);
				logusu('TBDESTINOS',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM tbdestinos WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM tbdestinos WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM tbdestinos WHERE id=$id ");
				logusu('TBDESTINOS',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}



	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$("#horsal").mask("99:99a");
		});
		';

		$edit = new DataEdit( '', 'tbrutas');

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

		$edit->codrut = new inputField('Codigo','codrut');
		$edit->codrut->rule='';
		$edit->codrut->size =8;
		$edit->codrut->maxlength =6;

		$edit->horsal = new inputField('Hora','horsal');
		$edit->horsal->rule='';
		$edit->horsal->size =8;
		$edit->horsal->maxlength =6;

		$edit->tipuni = new dropdownField('Unidad','tipuni');
		$edit->tipuni->rule='required';
		$edit->tipuni->options('SELECT tipbus, CONCAT(tipbus, " ", desbus) nombre FROM tbmodbus ORDER BY tipbus');

		$edit->origen = new inputField('Origen','origen');
		$edit->origen->rule='';
		$edit->origen->size =30;
		$edit->origen->maxlength =100;

		$edit->destino = new inputField('Destino','destino');
		$edit->destino->rule='';
		$edit->destino->size =30;
		$edit->destino->maxlength =100;

		$edit->tipserv = new dropdownField('Tipo','tipserv');
		$edit->tipserv->rule='required';
		$edit->tipserv->options('SELECT tipserv, CONCAT(tipserv, " ", desserv) nombre FROM tbtipserv ORDER BY tipserv');

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
		if (!$this->db->table_exists('tbrutas')) {
			$mSQL="CREATE TABLE `tbrutas` (
			  `tipserv` varchar(2) DEFAULT '',
			  `codrut` varchar(6) NOT NULL DEFAULT '',
			  `horsal` varchar(6) DEFAULT '',
			  `tipuni` varchar(5) DEFAULT '',
			  `origen` varchar(100) DEFAULT '',
			  `destino` varchar(100) DEFAULT '',
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codrut` (`codrut`)
			) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('tbrutas');
		//if(!in_array('<#campo#>',$campos)){ }
	}

	function tabla() {
		$id = $this->uri->segment($this->uri->total_segments());
/*
		$transac = $this->datasis->dameval("SELECT transac FROM gser WHERE id='$id'");
		$mSQL = "SELECT cod_prv, MID(CONCAT(TRIM(cod_prv),' ',nombre),1,25) nombre, tipo_doc, numero, monto, abonos FROM sprm WHERE transac='$transac' ORDER BY cod_prv ";
		$query = $this->db->query($mSQL);
		$codprv = 'XXXXXXXXXXXXXXXX';
		$salida = '';
		$saldo = 0;
		if ( $query->num_rows() > 0 ){
			$salida = "<br><table width='100%' border=1>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";

			foreach ($query->result_array() as $row)
			{
				if ( $codprv != $row['cod_prv']){
					$codprv = $row['cod_prv'];
					$salida .= "<tr bgcolor='#c7d3c7'>";
					$salida .= "<td colspan=4>".trim($row['nombre']). "</td>";
					$salida .= "</tr>";
				}
				if ( $row['tipo_doc'] == 'FC' ) {
					$saldo = $row['monto']-$row['abonos'];
				}
				$salida .= "<tr>";
				$salida .= "<td>".$row['tipo_doc']."</td>";
				$salida .= "<td>".$row['numero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
				$salida .= "</tr>";
			}
			$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'>Saldo : ".nformat($saldo). "</td></tr>";
			$salida .= "</table>";
		}

		$mSQL = "SELECT codbanc, banco, tipo_op tipo_doc, numero, monto FROM bmov WHERE transac='$transac' ORDER BY codbanc ";
		$query = $this->db->query($mSQL);
		$salida .= "\n";
		if ( $query->num_rows() > 0 ){
			$salida .= "<br><table width='100%' border=1>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>Banco</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row)
			{
				$salida .= "<tr>";
				$salida .= "<td>".$row['codbanc']."</td>";
				$salida .= "<td>".$row['banco'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto'])."</td>";
				$salida .= "</tr>";
			}
			$salida .= "</table>";
		}
		echo $salida;
*/
	
	}
}
?>
