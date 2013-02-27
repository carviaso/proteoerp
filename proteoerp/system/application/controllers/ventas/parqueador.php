<?php
require_once(BASEPATH.'application/controllers/ventas/sfac.php');
class parqueador extends Sfac {

	var $titp='Parqueador';
	var $tits='Cobro de servicios por estacionamiento';
	var $url ='ventas/parqueador/';

	function parqueador(){
		parent::Sfac();
		//$this->load->library('rapyd');
		//$this->datasis->modulo_id(216,1);
	}

	function index(){
		$this->datasis->creaintramenu( $data = array('modulo'=>'149','titulo'=>'Parqueador','mensaje'=>'Parqueador','panel'=>'TRANSACCIONES','ejecutar'=>'ventas/parqueador','target'=>'popu','visible'=>'S','pertenece'=>'2','ancho'=>900,'alto'=>600));
		parent::index();
	}

	//Ventana principal de facturacion
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 220, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'boton1'   ,'img'=>'assets/default/images/print.png','alt' => 'Reimprimir'    ,'label'=>'Reimprimir Documento'));
		$grid->wbotonadd(array('id'=>'precierre','img'=>'images/dinero.png'              ,'alt' => 'Cierre de Caja','label'=>'Cierre de Caja'));
		$fiscal=$this->datasis->traevalor('IMPFISCAL','Indica si se usa o no impresoras fiscales, esto activa opcion para cierre X y Z');
		if($fiscal=='S'){
			$grid->wbotonadd(array('id'=>'bcierrex','img'=>'assets/default/images/print.png', 'alt' => 'Imprimir Cierre X','label' => 'Cierre X'));
			$grid->wbotonadd(array('id'=>'bcierrez','img'=>'assets/default/images/print.png', 'alt' => 'Imprimir Cierre Z','label' => 'Cierre Z'));
		}

		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'fedita' , 'title'=>'Agregar/Editar Registro'),
			array('id'=>'scliexp', 'title'=>'Ficha de Cliente' ),
			array('id'=>'fshow'  , 'title'=>'Mostrar registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['jquerys']      = array('plugins/jquery.ui.timepicker.addon.js','i18n/grid.locale-es.js');
		$param['WestPanel']    = $WestPanel;
		$param['script']       = script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('SFAC', 'JQ');
		$param['otros']        = $this->datasis->otros('SFAC', 'JQ');
		$param['centerpanel']  = $centerpanel;
		//$param['funciones']    = $funciones;
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		$param['tamano']       = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	//
	//Funciones de los Botones
	//
	function bodyscript( $grid0, $grid1 ){
		$bodyscript = '<script type="text/javascript">';

		$bodyscript .= '
		function sfacadd() {
			$.post("'.site_url($this->url.'datapar/create').'",
			function(data){
				$("#fimpser").html("");
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function sfacshow() {
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				$.post("'.site_url($this->url.'dataedit/show').'/"+id,
					function(data){
						$("#fshow").html(data);
						$("#fshow").dialog( "open" );
					});
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		};';

		$bodyscript .= '
		function sfacedit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fborra").html("");
					$("#fimpser").html("");
					$("#fedita").html(data);
					$("#fedita").dialog({ buttons: { Ok: function() { $( this ).dialog( "close" ); } } });
					$("#fedita").dialog( "open" );
				});
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function sfacdel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				if(confirm(" Seguro desea anular el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						$("#fedita").html("");
						$("#fimpser").html("");
						$("#fborra").html(data);
						$("#fborra").dialog( "open" );
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '$(function() { ';

		$bodyscript .= '
			jQuery("#boton1").click( function(){
				var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
				if (id)	{
					//alert(Math.ceil((screen.availHeight))+\'x\'+Math.ceil((screen.availWidth)));
					var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
					window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
				} else { $.prompt("<h1>Por favor Seleccione una Factura</h1>");}
			});';

		$bodyscript .= '
			jQuery("#boton2").click( function(){
				window.open(\''.site_url('ventas/sfac/dataedit/create').'\', \'_blank\', \'width=900,height=700,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
			});';

		$fiscal=$this->datasis->traevalor('IMPFISCAL','Indica si se usa o no impresoras fiscales, esto activa opcion para cierre X y Z');
		if($fiscal=='S'){
			$bodyscript .= '
			jQuery("#bcierrex").click( function(){
				window.open(\''.site_url('formatos/descargartxt/CIERREX').'\', \'_blank\', \'width=300,height=300,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
			});';

			$bodyscript .= '
			jQuery("#bcierrez").click( function(){
				window.open(\''.site_url('formatos/descargartxt/CIERREZ').'\', \'_blank\', \'width=300,height=300,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
			});';
		}

		//Precierre
		$bodyscript .= '
			jQuery("#precierre").click( function(){
				//$.prompt("<h1>Seguro que desea hacer cierre?</h1>")
				window.open(\''.site_url('ventas/rcaj/precierre/99/').'/'.$this->secu->getcajero().'\', \'_blank\', \'width=900,height=700,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
			});';

		//Prepara Pago o Abono
		$bodyscript .= '
			$("#cobroser").click(function() {
				$.post("'.site_url('ventas/sfac/fcobroser').'", function(data){
					$("#fcobroser").html(data);
				});
				$( "#fcobroser" ).dialog( "open" );
			});';

		$bodyscript .= '
			$("#imptxt").click(function(){
				var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
				if (id)	{
					$.post("'.site_url('ventas/sfac/dataprintser/modify').'/"+id, function(data){
						$("#fimpser").html(data);
					});
					$("#fimpser").dialog( "open" );
				}else{
					$.prompt("<h1>Por favor Seleccione un Registro</h1>");
				}
			});';

		$bodyscript .= '
			$("#fimpser").dialog({
				autoOpen: false, height: 420, width: 400, modal: true,
				buttons: {
					"Guardar": function() {
						var bValid = true;
						var murl = $("#df1").attr("action");
						$.ajax({
							type: "POST",
							dataType: "html",
							async: false,
							url: murl,
							data: $("#df1").serialize(),
							success: function(r,s,x){
								try{
									var json = JSON.parse(r);
									if (json.status == "A"){
										$("#fimpser").dialog( "close" );
										jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
										return true;
									} else {
										apprise(json.mensaje);
									}
								}catch(e){
									$("#fimpser").html(r);
								}
							}
						})},
					"Imprimir": function() {
							var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
							location.href="'.site_url('formatos/descargartxt/FACTSER').'/"+id;
					},
					"Cancelar": function() {
						$("#fimpser").html("");
						$( this ).dialog( "close" );
					}
				},
				close: function() {
					$("#fimpser").html("");
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

		//Agregar Factura
		$bodyscript .= '
			$("#fedita").dialog({
				autoOpen: false, height: 300, width: 400, modal: true,
				buttons: {
					"Guardar": function() {
						var bValid = true;
						var murl = $("#df1").attr("action");
						//allFields.removeClass( "ui-state-error" );
						$.ajax({
							type: "POST",
							dataType: "html",
							async: false,
							url: murl,
							data: $("#df1").serialize(),
							success: function(r,s,x){
								try{
									var json = JSON.parse(r);
									if ( json.status == "A" ) {
										$( "#fedita" ).dialog( "close" );
										sfacadd();
										jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
										window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+json.pk.id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
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
				}
			});';

		$bodyscript .= '
			$("#fcobroser").dialog({
				autoOpen: false, height: 430, width: 540, modal: true,
				buttons: {
					"Guardar": function() {
						$.post("'.site_url('ventas/mensualidad/servxmes/insert').'", { cod_cli: $("#fcliente").val(),cana_0: $("#fmespaga").val(),tipo_0: $("#fcodigo").val(),num_ref_0: $("#fcomprob").val(),preca_0: $("#ftarifa").val(),fnombre: $("#fnombre").val(),utribu: $("#utribu").val()},
							function(data) {
								if( data.substr(0,14) == "Venta Guardada"){
									$("#fcobroser").dialog( "close" );
									jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
									//apprise(data);
									$("#fcobroser").html("");
									$.post("'.site_url('ventas/sfac/dataprintser/modify').'/"+data.substr(15,10), function(data){
										$("#fimpser").html(data);
									});
									$("#fimpser").dialog( "open" );
									return true;
								}else{
									apprise("<div style=\"font-size:16px;font-weight:bold;background:red;color:white\">Error:</div> <h1>"+data);
								}
							}
						);
					},
					Cancel: function() {
						$("#fcobroser").html("");
						$( this ).dialog( "close" );
					}
				},
				close: function() {
					$("#fcobroser").html("");
				}
			});';

		$bodyscript .= '});';

		$bodyscript .= "\n</script>\n";

		return $bodyscript;
	}

	function datapar(){
		$this->rapyd->load('dataform');
		$this->genesal = false;

		$script = '
		function totaliza(){
			var preca  = Number($("#preca_0").val());
			var thora  = $("#horaentrada").val();
			var t = thora.split(":");
			var d = new Date();

			var hentra = Number(t[0]);
			var mentra = Number(t[1]);

			var hactual = d.getHours();
			var mactual = d.getMinutes();

			var cana = hactual-hentra;

			var dminu = mactual- mentra;
			if(dminu > 15){
				cana = cana+0.5;
			}else if(dminu > 30){
				cana = cana+1;
			}

			$("#cana_0").val(cana);
			$("#total").text(nformat(cana*preca,2));
		}

		function tarifa(){
			 buscapreca();
			 totaliza();
		}

		function buscapreca(){
			var codigo = $("#codigoa_0").val();
			var tarifa   = $.ajax({ type: "POST", url: "'.site_url('ajax/buscaprecio1').'/", data: {q : codigo} ,async: false }).responseText;
			$("#preca_0").val(tarifa);
		}

		$(function() {
			$("#horaentrada").timepicker({
				timeFormat: "hh:mm",
				onClose: function(dateText, inst){
					totaliza();
				},
				onSelect: function(selectedDateTime){
					tarifa();
				}
			});
			tarifa();
		});
		';

		$form = new DataForm($this->url.'datapar/insert');
		$form->script($script);

		$form->placa = new inputField('Placa', 'placa');
		$form->placa->rule = 'trim|required|strtoupper|max_length[20]|callback_chplaca';
		$form->placa->size      = 10;
		$form->placa->maxlength = 7;

		$form->horaentrada = new inputField('Hora de entrada','horaentrada');
		$form->horaentrada->rule      = 'required|hora';
		$form->horaentrada->size      = 10;
		$form->horaentrada->maxlength = 5;

		$form->cana = new inputField('Cantidad','cana_0');
		$form->cana->rule      = 'required|mayorcero';
		$form->cana->size      = 10;
		$form->cana->onkeyup   = 'tarifa()';
		$form->cana->maxlength = 5;

		$form->codigo = new dropdownField('Tipo veh&iacute;culo', 'codigoa_0');
		//$form->codigo->option('','Seleccionar');
		$form->codigo->options('SELECT codigo,CONCAT_WS("-",descrip,precio1) AS val FROM sinv WHERE clave LIKE "TARIFA%" AND tipo="Servicio"');
		$form->codigo->style    = 'width:140px;';
		$form->codigo->onchange = 'tarifa()';
		$form->codigo->rule     = 'required';

		$form->preca = new inputField('Tarifa', 'preca_0');
		$form->preca->rule = 'required|mayorcero';
		$form->preca->size      = 10;
		//$form->preca->
		$form->preca->maxlength = 7;

		$form->total = new freeField('Monto a pagar', 'total','<span style="font-size:2em;" id="total">0,00</span>');

		$form->build_form();

		if ($form->on_success()){
			$monto  = 0;
			$cana   = $form->cana->newValue;
			$preca  = $form->preca->newValue;
			$codigo = $form->codigo->newValue;
			$sinvr  = $this->datasis->damereg("SELECT descrip,iva,tipo FROM sinv WHERE codigo = ".$this->db->escape($codigo));

			//Encabezado
			$_POST['pfac']        = '';
			$_POST['fecha']       = date('d/m/Y');
			$_POST['cajero']      = $this->secu->getcajero();
			$_POST['vd']          = $this->secu->getvendedor();
			$_POST['almacen']     = $this->secu->getalmacen();
			$_POST['tipo_doc']    = 'F';
			$_POST['factura']     = '';
			$_POST['cod_cli']     = 'CONTA';
			$_POST['sclitipo']    = '1';
			$_POST['nombre']      = 'CLIENTE CONTADO';
			$_POST['rifci']       = 'V000000';
			$_POST['direc']       = '';

			//Items
			$id  = 0;
			$tota= $cana*$preca;
			$ind = 'codigoa_'.$id;  $_POST[$ind] = $codigo;
			$ind = 'desca_'.$id;    $_POST[$ind] = $sinvr['descrip'];
			$ind = 'cana_'.$id;     $_POST[$ind] = $cana;
			$ind = 'preca_'.$id;    $_POST[$ind] = $preca;
			$ind = 'tota_'.$id;     $_POST[$ind] = $tota;
			$ind = 'precio1_'.$id;  $_POST[$ind] = 0;
			$ind = 'precio2_'.$id;  $_POST[$ind] = 0;
			$ind = 'precio3_'.$id;  $_POST[$ind] = 0;
			$ind = 'precio4_'.$id;  $_POST[$ind] = 0;
			$ind = 'itiva_'.$id;    $_POST[$ind] = round($sinvr['iva'],2);
			$ind = 'sinvpeso_'.$id; $_POST[$ind] = 0;
			$ind = 'sinvtipo_'.$id; $_POST[$ind] = $sinvr['tipo'];
			$ind = 'detalle_'.$id;  $_POST[$ind] = 'ESTACIONAMIENTO PLACA '.$form->placa->newValue;
			$monto += $tota;

			//Forma de pago
			$_POST['tipo_0']       = 'EF';
			$_POST['sfpafecha_0']  = '';
			$_POST['num_ref_0']    = '';
			$_POST['banco_0']      = '';
			$_POST['monto_0']      = $monto*(1+($sinvr['iva']/100)) ;

			if($monto<=0){
				$rt=array(
					'status' =>'B',
					'mensaje'=>'Monto cero',
					'pk'     =>null
				);
				echo json_encode($rt);
			}

			ob_start();
				parent::dataedit();
				$rt = ob_get_contents();
			@ob_end_clean();

			echo $rt;
		}else{
			echo $form->output;
		}

		if ($form->on_error()){

		}
	}

	//Busca la data en el Servidor por json
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('sfac');
		$mWHERE[] = array('', 'fecha', date('Ymd'), '' );
		$mWHERE[] = array('', 'usuario', $this->session->userdata('usuario'),'');

		$response   = $grid->getData('sfac', array(array()), array(), false, $mWHERE, 'id', 'desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	function chplaca($placa){
		if (preg_match("/^[A-Za-z0-9\-]+$/", $placa)>0){
			return true;
		}else{
			$this->validation->set_message('chplaca', "El dato introducido en el campo <b>%s</b> no parece valido");
			return false;
		}
	}

}
