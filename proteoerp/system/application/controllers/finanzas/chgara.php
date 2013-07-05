<?php
class Chgara extends Controller {
	var $mModulo='CHGARA';
	var $titp='Cheques en Garantia';
	var $tits='Cheques en Garantia';
	var $url ='finanzas/chgara/';

	function Chgara(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_nombre( $modulo, $ventana=0 );
	}

	function index(){
		$this->instalar();
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

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		$grid->wbotonadd(array("id"=>"listado",   "img"=>"assets/default/images/print.png",          "alt" => 'Listado',               "label"=>"Listado"));
		$grid->wbotonadd(array("id"=>"depositar", "img"=>"assets/default/images/cheque.png",         "alt" => 'Enviar a Depositar',    "label"=>"Enviar a Depositar"));
		$grid->wbotonadd(array("id"=>"cobrados",  "img"=>"assets/default/images/monedas.png",        "alt" => 'Cheques Cobrados',      "label"=>"Cheques Cobrados"));
		$grid->wbotonadd(array("id"=>"devueltos", "img"=>"assets/default/images/process-stop32.png", "alt" => 'Cheques Devueltos',     "label"=>"Cheques Devueltos"));
		$grid->wbotonadd(array("id"=>"pagar",     "img"=>"images/face-smile.png",                    "alt" => 'Apliar Pago a Cliente', "label"=>"Apliar Pago a Cliente"));
		$grid->wbotonadd(array("id"=>"cerrar",    "img"=>"images/face-cool.png",                     "alt" => 'Pagar Manualmente',     "label"=>"Pago Manual"));
		$grid->wbotonadd(array("id"=>"anular",    "img"=>"images/face-devilish.png",                 "alt" => 'Cheque Incobrable',     "label"=>"Incobrable"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array("id"=>"forma1", "title"=>"Recepcion de Depositos")
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '
		function fstatus(el, val, opts){
			var meco=\'<div><img src="'.base_url().'images/S.gif" width="20" height="18" border="0" /></div>\';
			if ( el == "E" ){
				meco=\'<div><img src="'.base_url().'assets/default/images/cheque.png" width="20" height="18" border="0" /></div>\';
			} else if (el == "C") {
				meco=\'<div><img src="'.base_url().'assets/default/images/monedas.png" width="20" height="18" border="0" /></div>\';
			} else if (el == "D") {
				meco=\'<div><img src="'.base_url().'images/N.gif" width="20" height="20" border="0" /></div>\';
			} else if (el == "A") {
				meco=\'<div><img src="'.base_url().'images/face-smile.png" width="20" height="20" border="0" /></div>\';
			} else if (el == "0") {
				meco=\'<div><img src="'.base_url().'images/face-devilish.png" width="20" height="20" border="0" /></div>\';
			} else if (el == "B") {
				meco=\'<div><img src="'.base_url().'images/face-cool.png" width="20" height="20" border="0" /></div>\';
			}
			return meco;
		}';

		$param['WestPanel']  = $WestPanel;
		$param['funciones']  = $funciones;
		//$param['EastPanel']  = $EastPanel;
		$param['SouthPanel'] = $SouthPanel;
		$param['listados']   = $this->datasis->listados('CHGARA', 'JQ');
		$param['otros']      = $this->datasis->otros('CHGARA', 'JQ');
		$param['temas']      = array('proteo','darkness','anexos1');
		$param['bodyscript'] = $bodyscript;
		$param['tabs']       = false;
		$param['encabeza']   = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}


	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0 ){
		$mSQL  = "SELECT codbanc, CONCAT(codbanc, ' ', trim(banco),' ', numcuent) banco ";
		$mSQL .= "FROM banc WHERE activo='S'  AND tbanco<>'CAJ' ";
		$mSQL .= "ORDER BY (tbanco='CAJ'), codbanc ";
		$obanc = $this->datasis->llenaopciones($mSQL, false,  'cuenta' );
		$obanc = str_replace('"',"'", $obanc);

		$bodyscript = '<script type="text/javascript">'."\n";

		$bodyscript .= 'var grid = jQuery("#newapi'.$grid0.'");';

		$bodyscript .= '
		jQuery("#listado").click( function(){
			window.open(\''.site_url('reportes/ver/CHGARA').'/\', \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
		});
		';

		// Envia a Depositar, marca el cheque status='E'
		$bodyscript .= '
		$( "#depositar" ).click(function() {
			var grid = jQuery("#newapi'.$grid0.'");
			var s = grid.getGridParam(\'selarrrow\');
			if(s.length){
				meco = sumamonto(0);
				$.prompt( "<h1>Enviar a Depositar ?</h1>", {
					buttons: { Guardar: true, Cancelar: false },
					submit: function(e,v,m,f){
						if (v){
							$.get("'.site_url('finanzas/chgara/chenvia').'/"+meco,
							function(data){
								apprise("<h1>"+data+"</h1>");
								grid.trigger("reloadGrid");
							});
						}
					}
				});
			} else {
				$.prompt("<h1>Seleccione los Cheques</h1>");
			}
		});
		';


		// Marca como Cobrado el cheque status='C' y guarda el Nro Dep, banco y fecha
		$bodyscript .= '
		$( "#cobrados" ).click(function() {
			var grid = jQuery("#newapi'.$grid0.'");
			var s = grid.getGridParam(\'selarrrow\');
			if(s.length){
				meco = sumamonto(0);
				$.prompt( "<h1>Marcar como Cobrado?</h1>Marca solo los cheques que fueron previamente Enviados al Cobro<br/>Cuenta Bancaria: '.$obanc.'<br>Fecha del deposito: <br/> <input type=\'text\' id=\'mfecha\' name=\'mfecha\' value=\''.date('d-m-Y').'\' maxlengh=\'10\' size=\'10\' ><br/>Numero de Deposito:<br/><input type=\'text\' id=\'numdep\' name=\'numdep\' value=\'\'><br/>", {
					buttons: { Guardar: true, Cancelar: false },
					submit: function(e,v,m,f){
						if (v){
							mfecha = f.mfecha.substr(6,4)+f.mfecha.substr(3,2)+f.mfecha.substr(0,2);
							if (f.numdep == ""){
								alert("Debe colocar el Nro de deposito!!!");					
							} else {
								$.get("'.site_url('finanzas/chgara/chcobrados').'/"+meco+"/"+f.numdep+"/"+f.cuenta+"/"+mfecha,
								function(data){
									apprise("<h1>"+data+"</h1>");
									grid.trigger("reloadGrid");
								});
							}
						}
					}
				});
				$("#mfecha").datepicker({dateFormat:"dd-mm-yy"});
			} else {
				$.prompt("<h1>Seleccione los Cheques</h1>");
			}
		});
		';

		// Vuelve a marcar como Pendiente el cheque status='P'
		$bodyscript .= '
		$( "#devueltos" ).click(function() {
			var grid = jQuery("#newapi'.$grid0.'");
			var s = grid.getGridParam(\'selarrrow\');
			if(s.length){
				meco = sumamonto(0);
				$.prompt( "<h1>Marcar los cheques Devueltos ?</h1>Marca solo los cheques que fueron previamente Enviados al Cobro", {
					buttons: { Guardar: true, Cancelar: false },
					submit: function(e,v,m,f){
						if (v){
							$.get("'.site_url('finanzas/chgara/chdevueltos').'/"+meco,
							function(data){
								apprise("<h1>"+data+"</h1>");
								grid.trigger("reloadGrid");
							});
						}
					}
				});
			} else {
				$.prompt("<h1>Seleccione los Cheques</h1>");
			}
		});
		';

		// Marca como Cobrado el cheque status='C' y guarda el Nro Dep, banco y fecha
		$bodyscript .= '
		$( "#cerrar" ).click(function() {
			var grid = jQuery("#newapi'.$grid0.'");
			var s = grid.getGridParam(\'selarrrow\');
			if(s.length == 1){
				entirerow = grid.jqGrid(\'getRowData\',s[0]);
				if ( entirerow["status"].search("moneda") < 0 ){
					$.prompt("<h1>Cheque no cobrado o ya aplicado</h1>Seleccione uno que este depositado y cobrado");
				} else {
					$.prompt( "<h1>Registrar pago y deposito manualmente?</h1>Cerrar cheque cobrado por carga manual? ", {
						buttons: { Guardar: true, Cancelar: false },
						submit: function(e,v,m,f){
							if (v){
								$.get("'.site_url('finanzas/chgara/chcerrar').'/"+entirerow["id"],
								function(data){
									apprise("<h1>"+data+"</h1>");
									grid.trigger("reloadGrid");
								});
							}
						}
					});
				}
			} else {
				$.prompt("<h1>Seleccione un solo Cheque</h1>");
			}
		});
		';

		// Marca como Cobrado el cheque status='C' y guarda el Nro Dep, banco y fecha
		$bodyscript .= '
		$( "#anular" ).click(function() {
			var grid = jQuery("#newapi'.$grid0.'");
			var s = grid.getGridParam(\'selarrrow\');
			if(s.length == 1){
				entirerow = grid.jqGrid(\'getRowData\',s[0]);
				if ( entirerow["status"].search("S.gif") < 0 ){
					$.prompt("<h1>Cheque no esta pendiente</h1>Seleccione otro");
				} else {
					$.prompt( "<h1>Anular Cheque en garantia?</h1>Anular este cheque por Incobrable? ", {
						buttons: { Guardar: true, Cancelar: false },
						submit: function(e,v,m,f){
							if (v){
								$.get("'.site_url('finanzas/chgara/chanular').'/"+entirerow["id"],
								function(data){
									apprise("<h1>"+data+"</h1>");
									grid.trigger("reloadGrid");
								});
							}
						}
					});
				}
			} else {
				$.prompt("<h1>Seleccione un solo Cheque</h1>");
			}
		});';


		// Marca como Genera dep en banco y pago a cliente
		$bodyscript .= '
		$( "#pagar" ).click(function() {
			var grid = jQuery("#newapi'.$grid0.'");
			var s = grid.getGridParam(\'selarrrow\');
			var id = 0;
			var entirerow;
			if(s.length == 1){
				entirerow = grid.jqGrid(\'getRowData\',s[0]);
				id = entirerow["id"];
				var ret   = $("#newapi'. $grid0.'").getRowData(id);  
				mId = id;
				$.post("'.site_url('finanzas/chgara/formapaga').'/"+id, function(data){
					$("#forma1").html(data);
				});
				if ( entirerow["status"].search("moneda") < 0 ) {
					$.prompt("<h1>Cheque no cobrado o ya aplicado</h1>Seleccione uno que este depositado y cobrado");
				} else {
					$( "#forma1" ).dialog( "open" );
				}	
			} else {
				$.prompt("<h1>Por favor Seleccione un Deposito</h1>");
			}
		});';


		// Marca como Cobrado el cheque status='C' y guarda el Nro Dep, banco y fecha
		$bodyscript .= '
		$( "#forma1" ).dialog({
			autoOpen: false,
			height: 470,
			width: 550,
			modal: true,
			buttons: {
				"Aplicar Pago": function() {
					var bValid = true;
					//allFields.removeClass( "ui-state-error" );
					if ( bValid ) {
						$.ajax({
							type: "POST",
							dataType: "html",
							url:"'.site_url("finanzas/chgara/chpagar").'",
							async: false,
							data: $("#pagaforma").serialize(),
							success: function(r,s,x){
								var res = $.parseJSON(r);
								if ( res.status == "E"){
									apprise("<h2>Error:</h2> <h1>"+res.mensaje+"</h1>");
								} else {
									apprise("<h1>"+res.mensaje+"</h1>");
									grid.trigger("reloadGrid");
									$( "#forma1" ).dialog( "close" );
									return [true, a ];
								}
							}
						});
					}
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
		';

		// Marca como Cobrado el cheque status='C' y guarda el Nro Dep, banco y fecha
		$bodyscript .= '
		function sumamonto(rowId){ 
			var grid = jQuery("#newapi'.$grid0.'"); 
			var s; 
			var total = 0; 
			var rowcells=new Array();
			var entirerow;
			var hoy   = new Date();
			var fecha ;
			var meco = "";

			if ( rowId > 0 ) {
				entirerow = grid.jqGrid(\'getRowData\',rowId);
				fecha = new Date(entirerow["fecha"].split("-").join("/"))
				if ( hoy < fecha ){
					apprise( "<h1>Cheque no vencido</h1>" );
				} 
			}

			s = grid.getGridParam(\'selarrrow\'); 
			$("#totaldep").html("");
			if(s.length)
			{
				for(var i=0;i<s.length;i++)
				{
					entirerow = grid.jqGrid(\'getRowData\',s[i]);
					fecha = new Date(entirerow["fecha"].split("-").join("/"))
					if ( hoy >= fecha ){
						total += Number(entirerow["monto"]);
						meco = meco+entirerow["id"]+"-";
					} else {
						if ( rowId == 0 ) {
							grid.resetSelection(s[i]);
						}
					}
				}
				total = Math.round(total*100)/100;
				$("#totaldep").html("Bs. "+nformat(total,2));
				$("#montoform").html("Monto: "+nformat(total,2));
				montotal = total;
			}
			return meco;
		};
		$(function(){$(".inputnum").numeric(".");});
		';

		$bodyscript .= "\n</script>\n";

		return $bodyscript;
	}


	//*********************************************
	// Guarda los que se enviaron a depositar
	//*********************************************
	function chenvia(){
		$ids = $this->uri->segment(4);
		$ids = str_replace("-",",", $ids);
		$ids = substr($ids,0,-1);
		$mSQL = "UPDATE chgara SET status='E', enviado=curdate() WHERE id IN ($ids) AND status='P' ";
		if ($this->db->query($mSQL)) 
			echo "Cheques enviados ";
		else
			echo "Error Guardando Cambios";
	}

	//*********************************************
	// Guarda los que se enviaron a depositar
	//*********************************************
	function chcobrados(){
		$ids     = $this->uri->segment(4);
		$numdep  = $this->uri->segment(5);
		$codbanc = $this->uri->segment(6);
		$fecha   = $this->uri->segment(7);
		
		$ids = str_replace("-",",", $ids);
		$ids = substr($ids,0,-1);
		$mSQL = "UPDATE chgara SET status='C', deposito=?, codbanc=?, fdeposito=? WHERE id IN ($ids) AND status='E' ";
		$this->db->query($mSQL, array($numdep, $codbanc, $fecha));
		echo "Cheques marcados como Cobrados ".$fecha;
	}

	//*********************************************
	// Guarda los que se enviaron a depositar
	//*********************************************
	function chdevueltos(){
		$ids = $this->uri->segment(4);
		$ids = str_replace("-",",", $ids);
		$ids = substr($ids,0,-1);
		$mSQL = "UPDATE chgara SET status='P', deposito='DEVUELTO' WHERE id IN ($ids) AND status='E' ";
		$this->db->query($mSQL);
		echo "Cheques marcados como Devueltos ";
	}

	//*********************************************
	// Anula los cheques incobrables
	//*********************************************
	function chanular(){
		$ids = $this->uri->segment(4);
		$mSQL = "UPDATE chgara SET status='0', deposito='INCOBRABLE' WHERE id IN ($ids) AND status='P' ";
		$this->db->query($mSQL);
		echo "Cheques marcados como Incobrables ";
	}

	//*********************************************
	// Cierra los cheques que se depositan manualmente
	//*********************************************
	function chcerrar(){
		$ids = $this->uri->segment(4);
		$mSQL = "UPDATE chgara SET status='B', deposito='MANUAL' WHERE id IN ($ids) AND status='C' ";
		$this->db->query($mSQL);
		echo "Cheques marcados como cobrados manualmente";
	}


	//*********************************************
	// Guarda los que se enviaron a depositar
	//*********************************************
	function chpagar(){
		$id       = $this->input->get_post('fid');
		$reg      = $this->datasis->damereg("SELECT * FROM chgara WHERE id=$id");
		$monto    = $this->input->get_post('fmonto');
		$efectos  = substr(trim($this->input->get_post('fsele')),0,-1);
		$caja     = '';
		$codbanc  = '';
		$mensaje  = "";
		$envia    = '00';
		$data = array();
		
		$chmonto  = $reg["monto"];
		$cod_cli  = $reg["cod_cli"];
		$deposito = $reg["deposito"];
		$codbanc  = $reg["codbanc"];
		$fecha    = $reg["fecha"];

		if ( strlen($efectos) == 0 ) {
			echo '{"status":"E","numero":"$deposito","mensaje":"Seleccione algun efecto!! "}';
			return;
		}

		$saldo  = $this->datasis->dameval("SELECT SUM(monto-abonos) saldo FROM smov WHERE id IN ( $efectos ) " );
		$factor = $this->datasis->dameval("SELECT SUM(impuesto)/sum(monto) factor FROM smov WHERE id IN ( $efectos ) " );

		$obser  = $this->datasis->dameval("SELECT GROUP_CONCAT(CONCAT(tipo_doc, numero)) efecto FROM smov WHERE id IN ( $efectos ) " );
		$regcli = $this->datasis->damereg("SELECT nombre FROM scli WHERE cliente=".$this->db->escape($cod_cli));

		if ( $chmonto > $saldo){
			echo '{"status":"E","numero":"$deposito","mensaje":"Monto del cheque ('.$chmonto.') es mayor que el saldo deudor ('.$saldo.') , debe generar un Anticipo o seleccionar mas efectos"}';
			return;
		}

		// CREA EL ABONO
		$xnumero   = str_pad($this->datasis->prox_sql("nabcli"),  8, '0', STR_PAD_LEFT);
		$mcontrol  = str_pad($this->datasis->prox_sql("nsmov"),   8, '0', STR_PAD_LEFT);
		$transac   = str_pad($this->datasis->prox_sql("ntransa"), 8, '0', STR_PAD_LEFT);
		$xningreso = str_pad($this->datasis->prox_sql('ningreso'),8, '0', STR_PAD_LEFT);

		$data = array();
		$data['cod_cli']  = $cod_cli; 
		$data['nombre']   = $regcli['nombre']; 
		$data['tipo_doc'] = 'AB';
		$data['numero']   = $xnumero; 
		$data['fecha']    = $fecha; 
		$data['monto']    = $chmonto; 

	
		$data['impuesto'] = $chmonto*$factor;
		$data['vence']    = $fecha;
		//$data['tipo_ref'] = $efecto['tipo_doc'];
		//$data['num_ref']  = $efecto['numero'];
		$data['observa1'] = "PAGA: ".$obser;   //$efecto['tipo_doc'].$efecto['numero'];
		$data['observa2'] =  "";
		$data['banco']    = $codbanc; 
		$data['fecha_op'] = $fecha; 
		$data['num_op']   = $deposito; 
		$data['tipo_op']  =  'DE'; 
		$data['reten']    = 0;
		$data['ppago']    = 0;
		$data['cambio']   = 0;
		$data['mora']     = 0;
		$data['control']  = $mcontrol;
		$data['codigo']   =  '';
		$data['descrip']  = '';
		$data['reteiva']  =  0;
		$data['nroriva']  = '';
		$data['emiriva']  = '';
		$data['ningreso'] = $xningreso;

		$data['usuario']  = $this->secu->usuario();
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$data['transac']  = $transac;
		
		$this->db->insert('smov',$data);

		// FORMA DE PAGO
		$data = array();
		$mcajero = $this->datasis->dameval("SELECT cajero FROM usuario WHERE us_codigo=".$this->db->escape($this->secu->usuario()));

		$data['tipo_doc']  = 'AB';
		$data['numero']    = $xnumero;
		$data['tipo']      = 'DE';
		$data['monto']     = $chmonto;
		$data['num_ref']   = $deposito;
		$data['clave']     = '';
		$data['fecha']     = $fecha;
		$data['banco']     = $codbanc;
		$data['cambio']    = 0;
		$data['f_factura'] = $fecha;
		$data['cod_cli']   = $cod_cli;

		$data['vendedor']  = $mcajero;
		$data['cobrador']  = $mcajero;

		$data['usuario']   = $this->secu->usuario();
		$data['estampa']   = date('Ymd');
		$data['hora']      = date('H:i:s');
		$data['transac']   = $transac;

		$this->db->insert('sfpa',$data);

		// CARGA A EL BANCO
		$this->datasis->actusal($codbanc, $fecha, $chmonto);
               
		$msql = "SELECT numcuent, banco, moneda, saldo FROM banc WHERE codbanc=".$this->db->escape($codbanc);
		$banreg = $this->datasis->damereg($msql);

		$data = array();
		$data['codbanc']   = $codbanc;
		$data['numcuent']  = $banreg['numcuent'];
		$data['banco']     = $banreg['banco'];
		$data['moneda']    = $banreg['moneda'];
		$data['saldo']     = $banreg['saldo'];
		$data['fecha']     = $fecha;   //mpago[i,5] })  se jode la contabilidad si la fecha no es igual
		$data['benefi']    = '';
		$data['tipo_op']   = 'DE';
		$data['numero']    = str_pad($deposito,12,'0',STR_PAD_LEFT);
		$data['monto']     = $chmonto;
		$data['clipro']    = 'C';
		$data['codcp']     = $cod_cli;
		$data['nombre']    = $regcli['nombre'];
		$data['concepto']  = 'INGRESO POR COBRANZA ';
		$data['concep2']   = 
		$data['concep3']   = 
		$data['status']    = 'P';
		$data['bruto']     = $chmonto;
		$data['negreso']   = $xningreso;

		$data['usuario']   = $this->secu->usuario();
		$data['estampa']   = date('Ymd');
		$data['hora']      = date('H:i:s');
		$data['transac']   = $transac;

		$this->db->insert('bmov',$data);

		$query  = $this->db->query("SELECT tipo_doc, numero, monto, impuesto, abonos, id, fecha FROM smov WHERE id IN ( $efectos ) ORDER BY fecha desc, monto desc" );
		$resta = $chmonto;
		foreach( $query->result_array() as $efecto ) {
		
			if ( $efecto['monto']-$efecto['abonos'] > $resta )
				$abonar = $resta;
			else
				$abonar = $efecto['monto']-$efecto['abonos'];
				
			$resta = $resta - $abonar;

			//Detalle en itccli
			$data = array();
			$data['numccli']  = $xnumero;
			$data['tipoccli'] = 'AB';
			$data['cod_cli']  = $cod_cli;
			$data['numero']   = $efecto['numero'];
			$data['tipo_doc'] = $efecto['tipo_doc'];
			$data['fecha']    = $fecha;
			$data['monto']    = $efecto['monto'];
			$data['abono']    = $abonar;
			$data['reten']    = 0;
			$data['ppago']    = 0;
			$data['cambio']   = 0;
			$data['mora']     = 0;
			$data['reteiva']  = '';

			$data['usuario']    = $this->secu->usuario();
			$data['estampa']    = date('Ymd');
			$data['hora']       = date('H:i:s');
			$data['transac']    = $transac;

			$this->db->insert('itccli',$data);

			//Actualiza la Factura
			$mSQL = "UPDATE smov SET abonos=abonos+? WHERE id = ?";
			$this->db->query($mSQL,	array( $abonar, $efecto['id'] ));
			if ($resta <=0 ) break;
		}
		$mSQL = "UPDATE chgara SET status='A', transac='$transac' WHERE id=$id  ";
		$this->db->simple_query($mSQL);
		echo '{"status":"G","numero":"$deposito","mensaje":"Deposito Aplicado a CxC "}';

	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "true";

		$grid  = new $this->jqdatagrid;

		$link  = site_url('ajax/buscascli');
		$afterhtml = '<div id=\"aaaaaa\">Nombre <strong>"+ui.item.nombre+" </strong>RIF/CI <strong>"+ui.item.rifci+" </strong><br>Direccion <strong>"+ui.item.direc+"</strong></div>';
		$auto = $grid->autocomplete( $link, 'cod_cli', 'aaaaa', $afterhtml, '', '\'#editmod\'+gridId1.substring(1)' );

		$grid->addField('status');
		$grid->label('Status');
		$grid->params(array(
			'search'        => 'true',
			'align'         => "'center'",
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
			'formatter'     => 'fstatus'
		));


		$grid->addField('cod_cli');
		$grid->label('Cliente');
		$grid->params(array(
				'width'       => 50,
				'editable'    => $editar,
				'edittype'    => "'text'",
				'editrules'   => '{ edithidden:true, required:true }',
				'editoptions' => '{'.$auto.'}'
			)
		);

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
				'width'       => 160,
				'editable'    => 'false',
				'edittype'    => "'text'",
			)
		);

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

		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 16 }',
		));

		$grid->addField('monto');
		$grid->label('Monto');
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

		$grid->addField('banco');
		$grid->label('Banco');
		$grid->params(array(
			'align'         => "'center'",
			'width'         => 40,
			'editable'      => $editar,
			'edittype'      => "'select'",
			'editrules'     => '{ edithidden:true, required:true }',
			'editoptions'   => '{ dataUrl: "'.base_url().'ajax/ddbanco"}',
			'stype'         => "'text'",
		));

		$grid->addField('cuentach');
		$grid->label('Cuenta Bancaria');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 170,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 22 }',
		));

		$grid->addField('vendedor');
		$grid->label('Vendedor');
		$grid->params(array(
			'width'         => 40,
			'hidden'        => 'true',
			'editable'      => 'true',
			'edittype'      => "'select'",
			'editrules'     => '{ edithidden:true, required:true }',
			'editoptions'   => '{ dataUrl: "'.base_url().'ajax/ddvende"}',
		));

		$grid->addField('observa');
		$grid->label('Observacion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:30, maxlength: 250 }',
		));

		$grid->addField('enviado');
		$grid->label('Enviado Cobro');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}'
		));

		$grid->addField('codbanc');
		$grid->label('Cuenta');
		$grid->params(array(
			'align'         => "'center'",
			'width'         => 40,
			'editable'      => 'false',
			'edittype'      => "'text'",
		));

		$grid->addField('fdeposito');
		$grid->label('F Deposito');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha Deposito" }'
		));


		$grid->addField('deposito');
		$grid->label('Deposito');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 120,
			'edittype'      => "'text'",
		));

		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));

		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Estampa" }'
		));

		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));

		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
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
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('385');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');
		$grid->setMultiSelect(true);

		$grid->setonSelectRow('sumamonto');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:350, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:350, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(false);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

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
		$grid = $this->jqdatagrid;
		$join = array(array('table'=>'scli', 'join'=>'chgara.cod_cli=scli.cliente', 'fields'=>array('nombre')));

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneSqlWhere('chgara', $join);

		$response   = $grid->getData('chgara', $join , array(), false, $mWHERE, 'status desc,fecha' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
		//print_r($mWHERE);
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
		$check  = 0;
		$status = 'P';

		if ( $id > 0 )
			$status = $this->datasis->dameval("SELECT status FROM chgara WHERE id=$id");

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$data['status']    = 'P';
				$data['usuario']   = $this->secu->usuario();
				$data['estampa']   = date('Ymd');
				$data['hora']      = date('H:i:s');
				$this->db->insert('chgara', $data);
				echo "Registro Agregado";
				logusu('CHGARA',"Registro  INCLUIDO");
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			// Solo modifica los Cheques pendientes
			if ( $status == 'P'){			$this->db->where('id', $id);
				$this->db->update('chgara', $data);
				logusu('CHGARA',"Registro $id MODIFICADO");
				echo "Registro Modificado";
			} else
			echo "Cheque no puede modificarse, no esta pendiente";
			

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM chgara WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM chgara WHERE id=$id ");
				logusu('CHGARA',"Registro $id  ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	// forma de cierre de deposito
	function formapaga(){
		$id  = $this->uri->segment($this->uri->total_segments());
		$reg = $this->datasis->damereg("SELECT * FROM chgara WHERE id=$id");

		$salida = '
<script type="text/javascript">
	jQuery("#aceptados").jqGrid({
		datatype: "local",
		height: 190,
		colNames:["id","Tipo","Numero","Fecha", "Saldo"],
		colModel:[
			{name:"id",       index:"id",       width:10, hidden:true},
			{name:"tipo_doc", index:"tipo_doc", width:40},
			{name:"numero",   index:"numero",   width:90},
			{name:"fecha",    index:"fecha",    width:90},
			{name:"saldo",    index:"saldo",    width:80, align:"right"},
		],
		multiselect: true,
		onSelectRow: sumadepo,
		onSelectAll: sumadepo
	});

	
	var mcheques = [
';
		$mSQL = "SELECT id, tipo_doc, numero, fecha, monto-abonos saldo FROM smov WHERE tipo_doc='FC' AND monto>abonos AND cod_cli='".$reg['cod_cli']."' ORDER BY fecha";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0 ){
			foreach( $query->result() as $row ){
				$salida .= '{id:"'.     $row->id.      '",';
				$salida .= 'tipo_doc:"'.$row->tipo_doc.'",';
				$salida .= 'numero:"'.  $row->numero.  '",';
				$salida .= 'fecha:"'.   $row->fecha.   '",';
				$salida .= 'saldo:"'.   $row->saldo.   '" },';
			}
		}
		$salida .= '
	];
	for(var i=0;i<=mcheques.length;i++) jQuery("#aceptados").jqGrid(\'addRowData\',i+1,mcheques[i]);
	
	$("#ffecha").datepicker({dateFormat:"dd/mm/yy"});

	function sumadepo()
        { 
		var grid = jQuery("#aceptados");
		var s;
		var total = 0;
		var meco = "";
		var rowcells=new Array();
		s = grid.getGridParam(\'selarrrow\'); 
		$("#fsele").html("");
		if(s.length)
		{
			for(var i=0; i<s.length; i++)
			{
				var entirerow = grid.jqGrid(\'getRowData\',s[i]);
				total += Number(entirerow["saldo"]);
				meco = meco+entirerow["id"]+",";
			}
			total = Math.round(total*100)/100;	
			$("#grantotal").html(nformat(total,2));
			$("input#fsele").val(meco);
			$("input#fmonto").val(total);
			montotal = total;
		} else {
			total = 0;
			$("#grantotal").html(" "+nformat(total,2));
			$("input#fsele").val("");
			$("input#fmonto").val(total);
			montotal = total;	
		}
	};
</script>

	<p class="validateTips"></p>
	<h1 style="text-align:center">Aplicacion de Garantia Nro. '.$reg['numero'].'</h1>
	<p style="text-align:center;font-size:12px;">Fecha: '.$reg['fecha'].' Banco: '.$reg['banco'].'</p>
	<form id="pagaforma">	
	<input id="fmonto"   name="fmonto"   type="hidden">
	<input id="fsele"    name="fsele"    type="hidden">
	<input id="fnumbcaj" name="fnumbcaj" type="hidden" value="'.$reg['numero'].'">
	<input id="fid"      name="fid"      type="hidden" value="'.$id.'">
	<input id="ftipo"    name="ftipo"    type="hidden" value="C">
	</form>
	<br>
	<center><table id="aceptados"><table></center>
	<table width="80%">
	<td>Monto Cobrado: <div style="font-size:20px;font-weight:bold">'.nformat($reg['monto']).'</div></td><td>
	Aplicar:<div id="grantotal" style="font-size:20px;font-weight:bold">0.00</div>
	</td></table>
	';
		echo $salida;
	}

	function instalar(){
		if (!$this->db->table_exists('chgara')) {
			$mSQL='
				CREATE TABLE `chgara` (
					`cod_cli`    VARCHAR(5)    NULL DEFAULT NULL,
					`fecha`      DATE          NULL DEFAULT NULL,
					`numero`     VARCHAR(16)   NULL DEFAULT NULL,
					`cuentach`   VARCHAR(22)   NULL DEFAULT NULL,
					`banco`      CHAR(3)       NULL DEFAULT NULL,
					`monto`      DECIMAL(18,2) NULL DEFAULT NULL,
					`vendedor`   VARCHAR(5)    NULL DEFAULT NULL,
					`observa`    VARCHAR(250)  NULL DEFAULT NULL,
					`status`     CHAR(1)       NULL DEFAULT NULL,
					`usuario`    VARCHAR(12)   NULL DEFAULT NULL,
					`estampa`    DATE          NULL DEFAULT NULL,
					`hora`       VARCHAR(8)    NULL DEFAULT NULL,
					`modificado` TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					`deposito`   CHAR(12)      NULL DEFAULT NULL,
					`enviado`    DATE          NULL DEFAULT NULL,
					`codbanc`    CHAR(2)       NULL DEFAULT NULL,
					`fdeposito`  DATE          NULL DEFAULT NULL,
					`transac`    VARCHAR(8)    NULL DEFAULT NULL,
					`id`         INT(11)   NOT NULL AUTO_INCREMENT,
					PRIMARY KEY (`id`),
					INDEX `modificado` (`modificado`),
					INDEX `fecha` (`fecha`)
				)
				COLLATE="latin1_swedish_ci"
				ENGINE=MyISAM;';
				
			$this->db->query($mSQL);
		}
		if ( !$this->datasis->iscampo('chgara','enviado') ) {
			$this->db->query('ALTER TABLE chgara ADD COLUMN enviado DATE NULL AFTER deposito');
		};
		if ( !$this->datasis->iscampo('chgara','codbanc') ) {
			$this->db->query('ALTER TABLE chgara ADD COLUMN codbanc CHAR(2) NULL AFTER enviado');
		};
		if ( !$this->datasis->iscampo('chgara','fdeposito') ) {
			$this->db->query('ALTER TABLE chgara ADD COLUMN fdeposito DATE NULL AFTER codbanc');
		};
		if ( !$this->datasis->iscampo('chgara','transac') ) {
			$this->db->query('ALTER TABLE chgara ADD COLUMN transac VARCHAR(8) NULL AFTER fdeposito');
		};
		if ( $this->datasis->isindice('bmov','idunico') ){
			$this->db->query('ALTER TABLE bmov DROP INDEX idunico, ADD INDEX principal (codbanc, tipo_op, numero)');
		}


	}

}
?>
