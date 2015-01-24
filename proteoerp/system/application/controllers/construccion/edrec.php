<?php
class Edrec extends Controller {
	var $mModulo = 'EDREC';
	var $titp    = 'RECIBOS DE COBRO';
	var $tits    = 'RECIBOS DE COBRO';
	var $url     = 'construccion/edrec/';

	function Edrec(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'EDREC', $ventana=0 );
	}

	function index(){
		$this->datasis->creaintramenu(array('modulo'=>'A07','titulo'=>'Recibos de Cobro','mensaje'=>'Recibos de Cobro','panel'=>'CONDOMINIO','ejecutar'=>'construccion/edrec','target'=>'popu','visible'=>'S','pertenece'=>'A','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		$this->instalar();
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	// Layout en la Ventana
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$grid->setHeight('185');
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$grid1->setHeight('190');
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 220, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Botones Panel Izq
		$grid->wbotonadd(array("id"=>"imprime",   "img"=>"assets/default/images/print.png","alt" => 'Reimprimir', "label"=>"Imprimir Recibo"));
		$grid->wbotonadd(array("id"=>"edtraegas", "img"=>"images/engrana.png",  "alt" => "Traer Gastos", "label"=>"Traer Gastos"));
		$grid->wbotonadd(array("id"=>"generec",   "img"=>"images/engrana.png",  "alt" => 'Generar Recibos', "label"=>"Generar Recibos"));
		$grid->wbotonadd(array("id"=>"genecobro", "img"=>"images/engrana.png",  "alt" => 'Enviar Recibos al cobro', "label"=>"Recibos al cobro"));

		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = "radicional", $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']    = $WestPanel;
		$param['script']       = script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('EDREC', 'JQ');
		$param['otros']        = $this->datasis->otros('EDREC', 'JQ');
		$param['centerpanel']  = $centerpanel;
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		$param['tamano']       = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);

	}

	//******************************************************************
	// Funciones de los Botones
	//
	function bodyscript( $grid0, $grid1 ){
		$bodyscript = '		<script type="text/javascript">';

		$anomes = $this->datasis->dameval('SELECT MAX(anomes) FROM edrec');
		$ano = substr($anomes,0,4);
		$mes = substr($anomes,4,2);
		
		// Pide Fecha
		$ano0 = date('Y',mktime(0,0,0,date('m'),date('d'),date('Y')+1));

		$mano  = '<select id=\'mano\' name=\'mano\'>';
		$mano .= '<option '.($ano==$ano0   ? 'selected' : '').' value=\''.$ano0.    '\'>'.$ano0    .'</option>';
		$mano .= '<option '.($ano==$ano0-1 ? 'selected' : '').' value=\''.($ano0-1).'\'>'.($ano0-1).'</option>';
		$mano .= '<option '.($ano==$ano0-2 ? 'selected' : '').' value=\''.($ano0-2).'\'>'.($ano0-2).'</option>';
		$mano .= '<option '.($ano==$ano0-3 ? 'selected' : '').' value=\''.($ano0-3).'\'>'.($ano0-3).'</option>';
		$mano .= '</select>';

		$mmes  = '<select id=\'mmes\' name=\'mmes\'>';
		for ( $i = 1; $i < 13; $i++ ){
			$m = str_pad($i,2,'0', STR_PAD_LEFT);
			$mmes .= '<option '.($mes==$m ? 'selected':'').' value=\''.$m.'\'>'.$m.'</option>';
		}		
		$mmes .= '</select>';
		$mes  = '<select id=\'mmes\' name=\'mmes\'><option value=\'01\'>01</option><option value=\'02\'>02</option><option value=\'03\'>03</option><option value=\'04\'>04</option><option value=\'05\'>05</option><option value=\'06\'>06</option><option value=\'07\'>07</option><option value=\'08\'>08</option><option value=\'09\'>09</option><option value=\'10\'>10</option><option value=\'11\'>11</option><option value=\'12\'>12</option></select>';

		$bodyscript .= '
		$("#generec").click(function(){
			var mgene = {
			state0: {
				html:"<h1>Generar Recibos: </h1><br/><center>Fecha: '.$mano.'&nbsp; Mes: '.$mmes.'</center><br/>",
				buttons: { Cancelar: false, Aceptar: true },
				focus: 1,
				submit:function(e,v,m,f){
					if(v){
						e.preventDefault();
						$.ajax({
							url: \''.site_url('construccion/edrec/generec').'\',
							global: false,
							type: "POST",
							data: ({ anomes : encodeURIComponent(f.mano+f.mmes) }),
							dataType: "text",
							async: false,
							success: function(sino) {
								if (sino.substring(0,1)=="S"){
									$.prompt.goToState("state1");
								} else {
									$.prompt.close();
								}
							},
							error: function(h,t,e) { alert("Error.. ",e) }
						});
						return false;
					}
				}
			},
			state1: {
				html:"Was that awesome or what!?",
				buttons: { Back: -1, Exit: 0 },
				focus: 1,
				submit:function(e,v,m,f){
					e.preventDefault();
					if(v==0)
						$.prompt.close();
					else if(v==-1)
						$.prompt.goToState("state0");
				}
			}
			};
			$.prompt(mgene);
		});';

		$bodyscript .= '
		$("#genecobro").click(function(){
			var mgene = {
			state0: {
				html:"<h1>Recibos al Cobro: </h1><br/><center>Fecha: '.$mano.'&nbsp; Mes: '.$mmes.'</center><br/>",
				buttons: { Cancelar: false, Aceptar: true },
				focus: 1,
				submit:function(e,v,m,f){
					if(v){
						e.preventDefault();
						$.ajax({
							url: \''.site_url('construccion/edrec/genecobro').'\',
							global: false,
							type: "POST",
							data: ({ anomes : encodeURIComponent(f.mano+f.mmes) }),
							dataType: "text",
							async: false,
							success: function(sino) {
								if (sino.substring(0,1)=="S"){
									$.prompt.goToState("state1");
								} else {
									$.prompt.close();
								}
							},
							error: function(h,t,e) { alert("Error.. ",e) }
						});
						return false;
					}
				}
			},
			state1: {
				html:"Was that awesome or what!?",
				buttons: { Back: -1, Exit: 0 },
				focus: 1,
				submit:function(e,v,m,f){
					e.preventDefault();
					if(v==0)
						$.prompt.close();
					else if(v==-1)
						$.prompt.goToState("state0");
				}
			}
			};
			$.prompt(mgene);
		});';

		$bodyscript .= '
		$("#edtraegas").click(function(){
			var meco = "";
			var mgene = {
			state0: {
				html:"<h1>Traer Gastos: </h1><br/><center>Fecha: '.$mano.'&nbsp; Mes: '.$mes.'</center><br/>",
				buttons: { Cancelar: false, Aceptar: true },
				focus: 1,
				submit:function(e,v,m,f){
					if(v){
						e.preventDefault();
						$.ajax({
							url: \''.site_url('construccion/edgasto/edtraegas').'\',
							global: false,
							type: "POST",
							data: ({ anomes : encodeURIComponent(f.mano+f.mmes) }),
							dataType: "text",
							async: false,
							success: function(sino) {
								meco = " sino="+sino;
								if (sino.substring(0,1)=="S"){
									$.prompt.goToState("state1");
								} else {
									$.prompt.close();
								}
							},
							error: function(h,t,e) { alert("Error.. ",e) }
						});
						return false;
					}
				}
			},
			state1: {
				html:"Gastos transferidos!"+meco,
				buttons: { Regresar: -1, Salir: 0 },
				focus: 1,
				submit:function(e,v,m,f){
					e.preventDefault();
					if(v==0)
						$.prompt.close();
					else if(v==-1)
						$.prompt.goToState("state0");
				}
			}
			};
			$.prompt(mgene);
		});';


		$bodyscript .= '
		$("#imprime").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('formatos/ver/EDREC').'/\'+id').';
			} else { $.prompt("<h1>Por favor Seleccione un Recibo</h1>");}
		});';

		$bodyscript .= '
		function edrecadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function edrecedit(){
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
		function edrecshow(){
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
		function edrecdel() {
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
			autoOpen: false, height: 500, width: 750, modal: true,
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/EDREC').'/\'+json.pk.id+\'/id\'').';
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
	// Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

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
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			//'editrules'     => '{ required:true,date:true}',
			'formatter'     => "'date'",
			'sorttype'      => "'date'",
			'datefmt'       => "'d/m/Y'",
			'formoptions'   => '{ label:"Fecha", srcformat: "Y-m-d", newformat: "d-m-Y" }',
		));

		$grid->addField('vence');
		$grid->label('Vence');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formatter'     => "'date'",
			'sorttype'      => "'date'",
			'datefmt'       => "'d/m/Y'",
			'formoptions'   => '{ label:"Fecha", srcformat: "Y-m-d", newformat: "d-m-Y" }'
		));

		$grid->addField('cod_cli');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));

		$grid->addField('inmueble');
		$grid->label('Inmueble');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'width'         => 50,
			'editrules'     => '{ required:true }',
		));

		$grid->addField('total');
		$grid->label('Total');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('alicuota');
		$grid->label('Alicuota');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 10 }'
		));

		$grid->addField('cuota');
		$grid->label('Cuota');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
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

		$grid->addField('anomes');
		$grid->label('Mes');
		$grid->params(array(
			'search'        => 'true',
			'align'         => "'center'",
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'"
		));

		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));

		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
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
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
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

		$grid->setOnSelectRow('
			function(id){
				if (id){
					jQuery(gridId2).jqGrid("setGridParam",{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
				}
			}'
		);
		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('EDREC','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('EDREC','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('EDREC','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('EDREC','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: edrecadd, editfunc: edrecedit, delfunc: edrecdel, viewfunc: edrecshow");

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
	function getdata()
	{
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('edrec');

		$response   = $grid->getData('edrec', array(array()), array(), false, $mWHERE, 'id','desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/*******************************************************************
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
				$check = $this->datasis->dameval("SELECT count(*) FROM edrec WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('edrec', $data);
					echo "Registro Agregado";

					logusu('EDREC',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM edrec WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM edrec WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE edrec SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("edrec", $data);
				logusu('EDREC',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('edrec', $data);
				logusu('EDREC',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM edrec WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM edrec WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM edrec WHERE id=$id ");
				logusu('EDREC',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	//Definicion del Grid y la Forma
	//
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));

		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

		$grid->addField('detalle');
		$grid->label('Detalle');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:200, maxlength: 200 }',
		));

		$grid->addField('total');
		$grid->label('Total');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('alicuota');
		$grid->label('Alicuota');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 10 }'
		));

		$grid->addField('cuota');
		$grid->label('Cuota');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
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

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'hidden'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));

		$grid->addField('id_edrc');
		$grid->label('Id_edrc');
		$grid->params(array(
			'hidden'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));

		$grid->setShrinkToFit('false');
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

	/*******************************************************************
	* Busca la data en el Servidor por json
	*/
	function getdatait( $id = 0 )
	{
		if ($id === 0 ){
			$id = $this->datasis->dameval("SELECT MAX(id) FROM edrec");
		}
		if(empty($id)) return "";
		$numero   = $this->datasis->dameval("SELECT numero FROM edrec WHERE id=$id");
		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM editrec WHERE numero='$numero' ";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/*******************************************************************
	* Guarda la Informacion
	*/
	function setDatait()
	{
	}

	//******************************************************************
	// DataEdit  
	//
	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$("#vence").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$do = new DataObject('edrec');
		$do->rel_one_to_many('editrec','editrec','numero');
		//$do->rel_one_to_many('itpfac', 'itpfac', array('numero' => 'numa'));


		$edit = new DataDetails($this->tits, $do );

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		//$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$edit->numero = new inputField('Numero','numero');
		$edit->numero->rule='';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->calendar=false;
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;
		$edit->fecha->insertValue = date('Y-m-d');

		$edit->vence = new dateonlyField('Vence','vence');
		$edit->vence->rule='chfecha';
		$edit->vence->calendar=false;
		$edit->vence->size =10;
		$edit->vence->maxlength =8;
		$edit->vence->insertValue = date('Y-m-d');

		$edit->cod_cli = new inputField('Cliente','cod_cli');
		$edit->cod_cli->rule='';
		$edit->cod_cli->size =7;
		$edit->cod_cli->maxlength =5;

		$edit->inmueble = new dropdownField('Inmueble','inmueble');
		$edit->inmueble->style='width:550px;';
		$edit->inmueble->options('SELECT a.codigo, CONCAT(a.codigo," ", a.descripcion, " ", b.nombre) descrip FROM edinmue a JOIN scli b ON a.ocupante=b.cliente ORDER BY a.codigo ');

		$edit->total = new inputField('Total','total');
		$edit->total->rule='numeric';
		$edit->total->css_class='inputnum';
		$edit->total->size =14;
		$edit->total->maxlength =12;

		$edit->alicuota = new inputField('Alicuota','alicuota');
		$edit->alicuota->rule='numeric';
		$edit->alicuota->css_class='inputnum';
		$edit->alicuota->size =14;
		$edit->alicuota->maxlength =12;

		$edit->cuota = new inputField('Cuota','cuota');
		$edit->cuota->rule='numeric';
		$edit->cuota->css_class='inputnum';
		$edit->cuota->size =14;
		$edit->cuota->maxlength =17;

		$edit->status = new dropdownField('Status','status');
		$edit->status->style='width:100px;';
		$edit->status->option('P' ,'Pendiente');
		$edit->status->option('F' ,'Facturado');

		$edit->observa = new textareaField('Observacion','observa');
		$edit->observa->rule='';
		$edit->observa->cols = 40;
		$edit->observa->rows = 2;

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));

		//**************************************************************
		// Detalle 

		$edit->tipo = new dropdownField('Tipo','tipo_<#i#>');
		$edit->tipo->style='width:120px;';
		$edit->tipo->options('SELECT depto, CONCAT(depto," ", descrip) descrip FROM dpto WHERE tipo="G" AND depto <> "GP" UNION ALL SELECT codbanc, CONCAT(codbanc," ",banco) banco FROM banc WHERE tbanco="FO" AND activo="S"');
		$edit->tipo->db_name   = 'tipo';
		$edit->tipo->rel_id    = 'editrec';

		$edit->codigo = new inputField('Codigo','codigo_<#i#>');
		$edit->codigo->size     = 7;
		$edit->codigo->rule      = 'required';
		$edit->codigo->db_name   = 'codigo';
		$edit->codigo->rel_id    = 'editrec';

		$edit->detalle = new inputField('Detalle','detalle_<#i#>');
		$edit->detalle->rule      =  '';
		$edit->detalle->size      =  35;
		$edit->detalle->maxlength = 200;
		$edit->detalle->db_name   = 'detalle';
		$edit->detalle->rel_id    = 'editrec';

		$edit->totald = new inputField('Total','total_<#i#>');
		$edit->totald->rule       = 'numeric';
		$edit->totald->css_class  = 'inputnum';
		$edit->totald->size       = 12;
		$edit->totald->maxlength  = 12;
		$edit->totald->db_name    = 'total';
		$edit->totald->rel_id     = 'editrec';
		$edit->totald->onkeyup='cuotatot(<#i#>)';

		$edit->alicuotad = new inputField('Alicuota','alicuota_<#i#>');
		$edit->alicuotad->rule        = 'numeric';
		$edit->alicuotad->css_class   = 'inputnum';
		$edit->alicuotad->size        = 12;
		$edit->alicuotad->insertValue = 1;
		$edit->alicuotad->maxlength   = 12;
		$edit->alicuotad->db_name     = 'alicuota';
		$edit->alicuotad->rel_id      = 'editrec';
		$edit->alicuotad->onkeyup='cuotatot(<#i#>)';

		$edit->cuotad = new inputField('Cuota','cuota_<#i#>');
		$edit->cuotad->rule      = 'numeric';
		$edit->cuotad->css_class = 'inputnum';
		$edit->cuotad->size      = 10;
		$edit->cuotad->maxlength = 12;
		$edit->cuotad->db_name   = 'cuota';
		$edit->cuotad->rel_id    = 'editrec';

		//**************************************************************

		$edit->buttons('add_rel');
		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form']  =& $edit;
			$this->load->view('view_edrec', $conten);
		}
	}

	function _pre_insert($do){
		$numero=$this->datasis->fprox_numero('nedrec');
		$do->set('numero', $numero);
		$do->set('origen', 'M');
		$inmueble = $this->db->escape($do->get('inmueble'));
		$cod_cli = $this->datasis->dameval('SELECT ocupante FROM edinmue WHERE codigo='.$inmueble);
		$do->set('cod_cli', $cod_cli);
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
		$numero  = $this->db->escape($do->get('numero'));
		$usuario = $this->db->escape($this->session->userdata('usuario'));
		$mSQL = 'UPDATE editrec SET estampa=curdate(), hora=curtime(), usuario='.$usuario.', id_edrc='.$primary.' WHERE numero='.$numero;
		$this->db->query($mSQL);
		logusu('edrec',"Recibo ".$do->get('numero'));
		return true;
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		$numero  = $this->db->escape($do->get('numero'));
		$usuario = $this->db->escape($this->session->userdata('usuario'));
		$mSQL = 'UPDATE editrec SET estampa=curdate(), hora=curtime(), usuario='.$usuario.', id_edrc='.$primary.' WHERE numero='.$numero;
		logusu('edrec',"Modifico $this->tits $primary ");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	//******************************************************************
	// Eliminar Recibos de Cobro
	//
	function borrarec( $anomes = 0){
		if ( $anomes == 0 ) $anomes = $this->input->post('anomes');
		if ( $anomes <= 0  ) {
			echo 'Error en la Fecha ('.$anomes.')';
			return false;
		}
		$dbanomes = $this->db->escape($anomes);

		//Busca si ya se facturaron
		$mSQL = "SELECT count(*) FROM edrec WHERE anomes=".$dbanomes;
		if ( $this->datasis->dameval($mSQL) > 0 ){
			$mSQL = "DELETE FROM edrec WHERE anomes=".$dbanomes;
			$this->db->query($mSQL);
			echo "Recibos Eliminados";
			return true;
		}
	}

	//******************************************************************
	// Genera Recibos de Cobro
	//
	function generec( $anomes = 0){
		if ( $anomes == 0 ) $anomes = $this->input->post('anomes');
		if ( $anomes <= 0  ) {
			echo 'Error en la Fecha ('.$anomes.')';
			return false;
		}
		$dbanomes = $this->db->escape($anomes);

		//Busca si ya se facturaron
		$mSQL = "SELECT count(*) FROM edrec WHERE anomes=".$dbanomes;
		if ( $this->datasis->dameval($mSQL) > 0 ){
			echo "Fecha ya facturada";
			return false;
		}

		$tasa = $this->datasis->traevalor('CONDOADM','COMISION DE GASTOS ADMINISTRATIVOS');
		if ($tasa == '') $tasa = 10; 

		// Calculas las alicuotas por tipo
		$mSQL = "
		SELECT aplicacion indice, sum(alicuota) valor FROM (
			SELECT a.aplicacion, 
				(SELECT bb.alicuota FROM edalicuota bb WHERE a.id=bb.inmueble AND EXTRACT(YEAR_MONTH FROM bb.fecha)<=${dbanomes} ORDER BY bb.fecha DESC LIMIT 1 ) alicuota
			FROM edinmue a ) aa
		WHERE aa.aplicacion IS NOT NULL AND aa.aplicacion NOT IN ('CO','')";
		$query = $this->db->query($mSQL);

		$malit = array();
		$malit['CO'] = 0;
		if ($query->num_rows() > 0){
			foreach ( $query->result() as $row ) {
				$malit[$row->indice] = $row->valor;
				$malit['CO'] = $malit['CO'] + $row->valor;
			}
		}

		// GRUPO DE GASTOS
		$GRUPO = array();
		$mSQL = "SELECT grupo indice FROM grga";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0) {
			foreach ( $query->result() as $row ){ $GRUPO[$row->indice] = 0; }
		}
		$GRUPOORG = $GRUPO;

		//Genera los recibos
		$mSQL = "
			SELECT '000001' numero, CURDATE() fecha, CURDATE() + INTERVAL 5 DAY vence, cliente cod_cli, inmueble, total, alicuota, cuota, 'P' status, 'Recibo' observa, '321' usuario, CURDATE() estampa, CURTIME() hora, 0 transac, 0 id, grupo, area, depto
			FROM (
				SELECT d.id, a.aplicacion, d.codigo inmueble, b.codigo, b.descrip, sum(a.total) total, mm.alicuota, ROUND(mm.alicuota*sum(a.total)/100,2) cuota, e.cliente, e.nombre, a.detalle, f.descrip aplidesc, b.grupo, d.area, d.aplicacion depto
				FROM edgasto   a
				JOIN mgas      b ON a.partida = b.id
				JOIN edinmue   d
				LEFT JOIN scli e ON IF(d.ocupante='' OR d.ocupante IS NULL, d.propietario,d.ocupante) = e.cliente
				JOIN dpto f ON a.aplicacion=f.depto
				JOIN ( 
					SELECT aa.id,  aa.codigo, (SELECT bb.alicuota FROM edalicuota bb 
					WHERE aa.id=bb.inmueble AND EXTRACT(YEAR_MONTH FROM bb.fecha)<=${dbanomes} ORDER BY bb.fecha DESC LIMIT 1 ) alicuota
					FROM edinmue aa) mm ON d.id = mm.id
				WHERE EXTRACT(YEAR_MONTH FROM a.causado)=${dbanomes}
			GROUP BY d.codigo ) aa
			HAVING alicuota >0
		";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			foreach( $query->result() as  $row ) {
				$numero = $this->datasis->fprox_numero('nedrec');
				$fecha  = date('Ymd'); 
				$inmueble = $this->db->escape($row->inmueble);
				$AREA = $row->area;
				$data = array();
				$data['numero']   = $numero;
				$data['fecha']    = $fecha;
				$data['vence']    = date('Ymd',mktime(0,0,0, date('m'), date('d')+5,date('Y') ));
				$data['cod_cli']  = $row->cod_cli;
				$data['inmueble'] = $row->inmueble;
				$data['total']    = $row->total;
				$data['alicuota'] = $row->alicuota;
				$data['cuota']    = $row->cuota;
				$data['status']   = 'P';
				$data['observa']  = 'Recibo';
				$data['usuario']  = $this->session->userdata('usuario');
				$data['estampa']  = date('Ymd');
				$data['hora']     = date('h:m:s');
				$data['anomes']   = $anomes;
				$this->db->insert('edrec',$data);
				$id = $this->db->insert_id();
				// Agrega el detalle
				$mSQL = "
				SELECT '000001' numero, tipo, codigo, detalle, total, alicuota, cuota, curdate() fecha, '321' usuario, curdate() estampa, curtime() hora, 0 transac, 0 id, 0 id_edrc, grupo
				FROM (
					SELECT d.id, a.aplicacion tipo, d.codigo inmueble, b.codigo, b.descrip, sum(a.total) total, mm.alicuota, ROUND(mm.alicuota*sum(a.total)/100,2) cuota, e.cliente, e.nombre, a.detalle, f.descrip aplidesc, b.grupo
					FROM edgasto a
					JOIN mgas    b ON a.partida = b.id
					JOIN edinmue d
					LEFT JOIN scli    e ON IF(d.ocupante='' OR d.ocupante IS NULL, d.propietario,d.ocupante) = e.cliente
					JOIN dpto f ON a.aplicacion=f.depto
					JOIN ( 
						SELECT aa.id,  aa.codigo, (SELECT bb.alicuota FROM edalicuota bb 
						WHERE aa.id=bb.inmueble AND EXTRACT(YEAR_MONTH FROM bb.fecha)<=${anomes} ORDER BY bb.fecha DESC LIMIT 1 ) alicuota
						FROM edinmue aa) mm ON d.id = mm.id
					WHERE EXTRACT(YEAR_MONTH FROM a.causado)=${anomes} AND d.codigo = ${inmueble} AND (a.aplicacion='CO' OR a.aplicacion=d.aplicacion)
				GROUP BY a.aplicacion, d.codigo, a.partida ) aa
				";

				// Reinicia Contadores
				$GRUPO = $GRUPORG;
				$monto = 0;
				$query1 = $this->db->query($mSQL);
				foreach( $query1->result() as  $row1 ) {
					$data1 = array();
					$data1['numero']   = $numero;
					$data1['tipo']     = $row1->tipo;
					$data1['codigo']   = $row1->codigo;
					$data1['detalle']  = $row1->detalle;
					$data1['total']    = $row1->total;
					$data1['alicuota'] = $row1->alicuota;
					if ( $row1->tipo == 'CO'){
						$data1['cuota'] = $row1->cuota;
					} else {
						$data1['cuota'] = round($row1->total*($row1->alicuota/$malit[$row1->tipo]),2);
					}
					$data1['fecha']    = $row1->fecha;
					$data1['usuario']  = $this->session->userdata('usuario');
					$data1['estampa']  = date('Ymd');
					$data1['hora']     = date('h:m:s');
					$data1['id_edrc']  = $id;
					$this->db->insert('editrec',$data1);
					$monto = $monto + $data1['cuota'];
					$GRUPO[$row1->grupo] = $GRUPO[$row1->grupo]+$data1['cuota'];
				}
				$data1 = array();
				$data1['numero']   = $numero;
				$data1['tipo']     = 'Z1';
				$data1['codigo']   = 'COMADM';
				$data1['detalle']  = 'COMISION POR SERVICIOS ADMINISTRATIVOS';
				$data1['total']    = $monto;
				$data1['alicuota'] = 0;
				$data1['cuota']    = round($monto*$tasa/100,2);
				$data1['fecha']    = $fecha;
				$data1['usuario']  = $this->session->userdata('usuario');
				$data1['estampa']  = date('Ymd');
				$data1['hora']     = date('h:m:s');
				$data1['id_edrc']  = $id;
				$this->db->insert('editrec',$data1);
				$monto = $monto + $data1['cuota'];

				// Agrega los Fondos
				$mSQL = "SELECT codbanc, banco, formula, depto, id FROM banc WHERE tbanco = 'FO' AND activo='S' ";
				$query1 = $this->db->query($mSQL);
				$UT     = $this->datasis->utri($anomes.'01');
				foreach( $query1->result() as  $row2 ) {
					memowrite('$cuota='.$row2->formula.' GR='.$GRUPO['0000'], $row2->codbanc );
					if ( $row2->depto != 'CO' ){  
						if ($row->depto != $row2->depto ) continue;
					}
					eval('$cuota='.$row2->formula.';');
					if ( $cuota <> 0 ){
						$data1 = array();
						$data1['numero']   = $numero;
						$data1['tipo']     = 'FO';
						$data1['codigo']   = $row2->codbanc;
						$data1['detalle']  = $row2->banco;
						$data1['total']    = $monto;
						$data1['alicuota'] = 0;
						$data1['cuota']    = round($cuota,2);
						$data1['fecha']    = $fecha;
						$data1['usuario']  = $this->session->userdata('usuario');
						$data1['estampa']  = date('Ymd');
						$data1['hora']     = date('h:m:s');
						$data1['id_edrc']  = $id;
						$this->db->insert('editrec',$data1);
					}
				}
			}
			$mSQL = 'UPDATE edrec a SET a.cuota = (SELECT SUM(b.cuota) FROM editrec b WHERE a.numero = b.numero)';
			$this->db->query($mSQL);

			echo "Si se Guardaron";
		}
	}

	//******************************************************************
	// Genera Recibos de Cobro
	//
	function genecobro( $anomes = 0){
		if ( $anomes == 0 ) $anomes = $this->input->post('anomes');
		if ( $anomes <= 0  ) {
			echo 'Error en la fecha ('.$anomes.')';
			return false;
		}
		$dbanomes = $this->db->escape($anomes);

		$tasa = $this->datasis->traevalor('CONDOADM','COMISION DE GASTOS ADMINISTRATIVOS');
		if ($tasa == '') $tasa = 10; 

		//Genera los recibos
		$mSQL = "
			SELECT 
				a.cod_cli, b.nombre, 'ND' tipo_doc, CONCAT('RC',MID(a.numero,3,6)) numero,
				a.fecha, a.cuota monto, 0 impuesto, 0 abonos, a.vence, 'RC' tipo_ref, 
				a.numero num_ref, 'RECIBO DE CONDOMINIO ${anomes}' observa1, a.id,
				CONCAT('CORRESPONDIENTE AL MES ',MID(a.anomes,5,2),'-',MID(a.anomes,1,4)) observa2,
				a.usuario,a.estampa, a.hora,a.transac, 'NOCON' codigo, 0 montasa, 0 monredu, 
				0 monadic, 0 tasa, 0 reducida, 0 sobretasa, 0 exento
			FROM edrec a JOIN scli b ON a.cod_cli=b.cliente
			WHERE a.status = 'P' AND ( a.anomes <= ".$dbanomes." OR a.anomes IS NULL)";
		
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			foreach( $query->result() as  $row ) {
				$transac   = $this->datasis->fprox_numero('ntransa');
				$fecha     = date('Ymd'); 
				$data = array();
				$data['cod_cli']   = $row->cod_cli;
				$data['nombre']    = $row->nombre;
				$data['tipo_doc']  = $row->tipo_doc;
				$data['numero']    = $row->numero;
				$data['fecha']     = $row->fecha;
				$data['monto']     = $row->monto;
				$data['impuesto']  = $row->impuesto;
				$data['abonos']    = $row->abonos;
				$data['vence']     = $row->vence;
				$data['tipo_ref']  = $row->tipo_ref;
				$data['num_ref']   = $row->num_ref;
				$data['observa1']  = $row->observa1;
				$data['observa2']  = $row->observa2;
				$data['usuario']   = $row->usuario;
				$data['estampa']   = date('h:m:s');
				$data['hora']      = date('h:m:s');
				$data['transac']   = $transac;
				$data['codigo']    = $row->codigo;
				$data['montasa']   = $row->montasa;
				$data['monredu']   = $row->monredu;
				$data['monadic']   = $row->monadic;
				$data['tasa']      = $row->tasa;
				$data['reducida']  = $row->reducida;
				$data['sobretasa'] = $row->sobretasa;
				$data['exento']    = $row->exento;
				$this->db->insert('smov',$data);

				// Actualiza edrec
				$data = array();
				$data['transac'] = $transac;
				$data['status']  = 'F';
				$this->db->where("id", $row->id);
				$this->db->update('edrec',$data);
			}
			echo "Si se Guardaron";
		}
	}

	function instalar(){
		if (!$this->db->table_exists('edrec')) {
			$mSQL="
			CREATE TABLE edrec (
				numero    VARCHAR(8) NOT NULL DEFAULT '',
				fecha     DATE NOT NULL  DEFAULT '0000-00-00',
				vence     DATE           DEFAULT NULL,
				cod_cli   VARCHAR(5)     DEFAULT NULL,
				inmueble  INT(11)        DEFAULT NULL,
				total     DECIMAL(12,2)  DEFAULT NULL,
				alicuota  DECIMAL(12,10) DEFAULT NULL,
				cuota     DECIMAL(17,2)  DEFAULT NULL,
				status    CHAR(1)        DEFAULT NULL,
				observa   TEXT,
				origen    CHAR(1)        DEFAULT 'A' COMMENT 'Automatico o Manual',
				usuario   VARCHAR(12)    DEFAULT NULL,
				estampa   DATE           DEFAULT NULL,
				hora      VARCHAR(5)     DEFAULT NULL,
				transac   VARCHAR(8)     DEFAULT NULL,
				id        INT(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (id),
			  UNIQUE KEY numero (numero),
			  KEY fecha (fecha),
			  KEY cliente (cod_cli)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC";
			$this->db->query($mSQL);
		}
		$campos=$this->db->list_fields('edrec');
		if(!in_array('origen',$campos)) $this->db->query("ALTER TABLE edrec ADD COLUMN origen CHAR(1) NULL DEFAULT 'A' COMMENT 'Automatico o Manual' AFTER observa");


		if (!$this->db->table_exists('editrec')) {
			$mSQL="
			CREATE TABLE editrec (
				numero   VARCHAR(8)     DEFAULT NULL,
				tipo     VARCHAR(8)     DEFAULT NULL,
				codigo   VARCHAR(15)    DEFAULT NULL,
				detalle  VARCHAR(200)   DEFAULT NULL,
				total    DECIMAL(12,2)  DEFAULT NULL,
				alicuota DECIMAL(12,10) DEFAULT NULL,
				cuota    DECIMAL(12,2)  DEFAULT NULL,
				fecha    DATE           DEFAULT NULL,
				usuario  VARCHAR(12)    DEFAULT NULL,
				estampa  DATE           DEFAULT NULL,
				hora     VARCHAR(5)     DEFAULT NULL,
				transac  VARCHAR(8)     DEFAULT NULL,
				id       INT(11)        unsigned NOT NULL AUTO_INCREMENT,
				id_edrc  INT(11)        DEFAULT NULL,
			PRIMARY KEY (id),
			KEY numero  (numero),
			KEY codigo  (codigo),
			KEY transac (transac)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC";
			$this->db->query($mSQL);
		}
	}
}

