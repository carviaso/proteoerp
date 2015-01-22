<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
include('common.php');
class Gserchi extends Controller {
	var $mModulo = 'GSERCHI';
	var $titp    = 'Caja chica';
	var $tits    = 'Caja chica';
	var $url     = 'finanzas/gserchi/';

	function Gserchi(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'GSERCHI', $ventana=0 );
		$this->mcred = '_CR';
		$this->idgser= 0;
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		$this->datasis->creaintramenu( $data = array('modulo'=>'52B','titulo'=>'Caja Chica','mensaje'=>'Caja Chica','panel'=>'GASTOS','ejecutar'=>'finanzas/gserchi','target'=>'popu','visible'=>'S','pertenece'=>'5','ancho'=>900,'alto'=>600));
		redirect($this->url.'jqdatag');
	}

	function _gserchipros($codbanc,$cargo,$codprv,$benefi,$numeroch=null){
			$dbcodprv = $this->db->escape($codprv);
			$nombre   = $this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$dbcodprv);
			$fecha    = date('Y-m-d');
			$numeroch = str_pad($numeroch, 12, '0', STR_PAD_LEFT);
			$sp_fecha = str_replace('-','',$fecha);
			$dbcodbanc= $this->db->escape($codbanc);
			$error    = 0;
			$cr       = $this->mcred; //Marca para el credito
			$estampa  = date('Y-m-d');
			$hora     = date('H:i:s');

			if($cargo!=$cr){
				$databan  = common::_traebandata($codbanc);
				$datacar  = common::_traebandata($cargo);
				if(!empty($datacar)){
					$tipo  = $datacar['tbanco'];
					$moneda= $datacar['moneda'];
				}else{
					return false;
				}
			}else{
				$tipo = $moneda = '';
			}

			$mSQL='SELECT codbanc,fechafac,numfac,nfiscal,rif,proveedor,codigo,descrip,
			  moneda,montasa,tasa,monredu,reducida,monadic,sobretasa,exento,importe,sucursal,departa,usuario,estampa,hora
			FROM gserchi WHERE ngasto IS NULL AND aceptado="S" AND codbanc='.$dbcodbanc;

			$query = $this->db->query($mSQL);
			if($query->num_rows() > 0){
				$transac  = $this->datasis->fprox_numero('ntransa');
				$numero   = $this->datasis->fprox_numero('ngser');

				$montasa=$monredu=$monadic=$tasa=$reducida=$sobretasa=$exento=$totpre=$totiva=0;
				foreach($query->result() as $row){

					$data = array();
					$data['fecha']      = $fecha;
					$data['numero']     = $numero;
					$data['proveed']    = $codprv;
					$data['codigo']     = $row->codigo;
					$data['descrip']    = $row->descrip;
					$data['precio']     = $row->montasa+$row->monredu+$row->monadic+$row->exento;
					$data['iva']        = $row->tasa+$row->reducida+$row->sobretasa;
					$data['importe']    = $data['precio']+$data['iva'];
					$data['unidades']   = 1;
					$data['fraccion']   = 0;
					$data['almacen']    = '';
					$data['sucursal']   = $row->sucursal;
					$data['departa']    = $row->departa ;
					$data['transac']    = $transac;
					$data['usuario']    = $this->session->userdata('usuario');
					$data['estampa']    = $estampa;
					$data['hora']       = $hora;
					$data['huerfano']   = '';
					$data['rif']        = $row->rif      ;
					$data['proveedor']  = $row->proveedor;
					$data['numfac']     = $row->numfac   ;
					$data['fechafac']   = $row->fechafac ;
					$data['nfiscal']    = $row->nfiscal  ;
					$data['feprox']     = '';
					$data['dacum']      = '';
					$data['residual']   = '';
					$data['vidau']      = '';
					$data['montasa']    = $row->montasa  ;
					$data['monredu']    = $row->monredu  ;
					$data['monadic']    = $row->monadic  ;
					$data['tasa']       = $row->tasa     ;
					$data['reducida']   = $row->reducida ;
					$data['sobretasa']  = $row->sobretasa;
					$data['exento']     = $row->exento   ;
					$data['reteica']    = 0;
					//$data['idgser']     = '';

					$sql=$this->db->insert_string('gitser', $data);
					$ban=$this->db->simple_query($sql);
					if($ban==false){ memowrite($sql,'gserchi'); $error++;}

					$montasa  +=$row->montasa  ;
					$monredu  +=$row->monredu  ;
					$monadic  +=$row->monadic  ;
					$tasa     +=$row->tasa     ;
					$reducida +=$row->reducida ;
					$sobretasa+=$row->sobretasa;
					$exento   +=$row->exento   ;
				}
				$totpre = $montasa+$monredu+$monadic+$exento;
				$totiva = $tasa+$reducida+$sobretasa;
				$totneto= $totpre+$totiva;

				if($cargo==$cr){ //si el cargo va a credito
					$nombre  = $this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$this->db->escape($codprv));
					$tipo1   = '';
					$credito = $totneto;
					$causado = $this->datasis->fprox_numero('ncausado');
					$control = $this->datasis->fprox_numero('nsprm');

					$data=array();
					$data['cod_prv']    = $codprv;
					$data['nombre']     = $nombre;
					$data['tipo_doc']   = 'FC';
					$data['numero']     = $numero;
					$data['fecha']      = $fecha;
					$data['monto']      = $totneto;
					$data['impuesto']   = $totiva ;
					$data['abonos']     = 0;
					$data['vence']      = $fecha;
					$data['observa1']   = 'REPOSICION DE CAJA CHICA '.$codbanc;
					$data['reten']      = 0;
					$data['transac']    = $transac;
					$data['estampa']    = $estampa;
					$data['hora']       = $hora;
					$data['usuario']    = $this->session->userdata('usuario');
					$data['reteiva']    = 0;
					$data['montasa']    = $montasa;
					$data['monredu']    = $monredu;
					$data['monadic']    = $monadic;
					$data['tasa']       = $tasa;
					$data['reducida']   = $reducida;
					$data['sobretasa']  = $sobretasa;
					$data['exento']     = $exento;
					$data['causado']    = $causado;
					$data['control']    = $control;

					//$data['tipo_ref']   = '';
					//$data['num_ref']    = '';
					//$data['observa2']   = '';
					//$data['banco']      = '';
					//$data['tipo_op']    = '';
					//$data['comprob']    = '';
					//$data['numche']     = '';
					//$data['codigo']     = '';
					//$data['descrip']    = '';
					//$data['ppago']      = '';
					//$data['nppago']     = '';
					//$data['nreten']     = '';
					//$data['mora']       = '';
					//$data['posdata']    = '';
					//$data['benefi']     = '';
					//$data['control']    = '';
					//$data['cambio']     = '';
					//$data['pmora']      = '';
					//$data['nfiscal']    = '';
					//$data['fecdoc']     = '';
					//$data['afecta']     = '';
					//$data['fecapl']     = '';
					//$data['serie']      = '';
					//$data['depto']      = '';
					//$data['negreso']    = '';
					//$data['ndebito']    = '';

					$sql=$this->db->insert_string('sprm', $data);
					$ban=$this->db->simple_query($sql);
					if($ban==false){ memowrite($sql,'gserchi'); $error++;}
					$cargo   = '';
					$cheque  = '';
					$negreso = '';
				}else{
					$tipo1  = ($tipo=='CAJ')? 'D': 'C';
					$cheque = ($tipo=='CAJ')? $this->datasis->banprox($codbanc): $numeroch ;
					$negreso= $this->datasis->fprox_numero('negreso');
					$credito= 0;
					$causado='';

					$data=array();
					$data['codbanc']    = $cargo;
					$data['moneda']     = $moneda;
					$data['numcuent']   = $datacar['numcuent'];
					$data['banco']      = $datacar['banco'];
					$data['saldo']      = $datacar['saldo'];
					$data['tipo_op']    = ($tipo=='CAJ') ? 'ND': 'CH';
					$data['numero']     = $cheque;
					$data['fecha']      = $fecha;
					$data['clipro']     = 'P';
					$data['codcp']      = $codprv;
					$data['nombre']     = $nombre;
					$data['monto']      = $totneto;
					$data['concepto']   = 'REPOSICION DE CAJA CHICA '.$codbanc;
					//$data['concep2']    = '';
					//$data['concep3']    = '';
					//$data['documen']    = '';
					//$data['comprob']    = '';
					//$data['status']     = '';
					//$data['cuenta']     = '';
					//$data['enlace']     = '';
					//$data['bruto']      = '';
					//$data['comision']   = '';
					//$data['impuesto']   = '';
					//$data['registro']   = '';
					//$data['concilia']   = '';
					$data['benefi']     = $benefi;
					$data['posdata']    = '';
					$data['abanco']     = '';
					$data['liable']     = ($tipo=='CAJ') ? 'S': 'N';;
					$data['transac']    = $transac;
					$data['usuario']    = $this->session->userdata('usuario');
					$data['estampa']    = $estampa;
					$data['hora']       = $hora;
					$data['anulado']    = 'N';
					$data['susti']      = '';
					$data['negreso']    = $negreso;
					//$data['ndebito']    = '';
					//$data['ncausado']   = '';
					//$data['ncredito']   = '';

					$sql=$this->db->insert_string('bmov', $data);
					$ban=$this->db->simple_query($sql);
					if($ban==false){ memowrite($sql,'gserchi'); $error++;}

					$this->datasis->actusal($cargo, $sp_fecha, (-1)*$totneto);
				}

				$data = array();
				$data['fecha']      = $fecha;
				$data['numero']     = $numero;
				$data['proveed']    = $codprv;
				$data['nombre']     = $nombre;
				$data['vence']      = $fecha;
				$data['totpre']     = $totpre;
				$data['totiva']     = $totiva;
				$data['totbruto']   = $totneto;
				$data['reten']      = 0;
				$data['totneto']    = $totneto;//totneto=totbruto-reten
				$data['codb1']      = $cargo;
				$data['tipo1']      = $tipo1;
				$data['cheque1']    = $cheque;
				$data['credito']    = $credito;
				$data['tipo_doc']   = 'FC';
				$data['orden']      = '';
				$data['anticipo']   = 0;
				$data['benefi']     = $benefi;
				$data['mdolar']     = '';
				$data['usuario']    = $this->session->userdata('usuario');
				$data['estampa']    = $estampa;
				$data['hora']       = $hora;
				$data['transac']    = $transac;
				$data['preten']     = '';
				$data['creten']     = '';
				$data['breten']     = '';
				$data['huerfano']   = '';
				$data['reteiva']    = 0;
				$data['nfiscal']    = '';
				$data['afecta']     = '';
				$data['fafecta']    = '';
				$data['ffactura']   = '';
				$data['cajachi']    = 'S';
				$data['montasa']    = $montasa;
				$data['monredu']    = $monredu;
				$data['monadic']    = $monadic;
				$data['tasa']       = $tasa;
				$data['reducida']   = $reducida;
				$data['sobretasa']  = $sobretasa;
				$data['exento']     = $exento;
				$data['compra']     = '';
				$data['serie']      = '';
				$data['reteica']    = 0;
				$data['retesimple'] = 0;
				$data['negreso']    = $negreso;
				$data['ncausado']   = $causado;
				$data['tipo_or']    = '';
				$data['monto1']     = $totneto;
				//$data['comprob1']   = '';

				//$data['codb2']      = '';
				//$data['tipo2']      = '';
				//$data['cheque2']    = '';
				//$data['comprob2']   = '';
				//$data['monto2']     = '';
				//$data['codb3']      = '';
				//$data['tipo3']      = '';
				//$data['cheque3']    = '';
				//$data['comprob3']   = '';
				//$data['monto3']     = '';

				$sql=$this->db->insert_string('gser', $data);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'gserchi'); $error++;}
				$idgser=$this->db->insert_id();

				$data = array('idgser' => $idgser);
				$dbfecha  = $this->db->escape($fecha);
				$dbnumero = $this->db->escape($numero);
				$dbcodprv = $this->db->escape($codprv);
				$where = "fecha=${dbfecha} AND proveed=${dbcodprv} AND  numero=${dbnumero}";
				$mSQL = $this->db->update_string('gitser', $data, $where);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'gserchi'); $error++; }

				$data = array('ngasto' => $numero);
				$where = "ngasto IS NULL AND  codbanc=${dbcodbanc} AND aceptado='S'";
				$mSQL = $this->db->update_string('gserchi', $data, $where);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'gserchi'); $error++; }
				$this->idgser=$idgser;
			}
		logusu('gserchi',"Genero gasto de caja chica ${numero}");
		return ($error==0)? true : false;
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
		//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$grid->wbotonadd(array('id'=>'imprime','img'=>'assets/default/images/print.png','alt' => 'Reimprimir caja chica', 'label'=>'Imprimir caja chica'));
		$grid->wbotonadd(array('id'=>'baprov' ,'img'=>'images/arrow_up.png', 'alt' => 'Aprobar o rechazar gasto para el pago' ,'label'=>'Aprobar/Rechazar' ));
		$grid->wbotonadd(array('id'=>'brepon' ,'img'=>'images/star.png'    , 'alt' => 'Reposici&oacute;n de caja Chica' ,'label'=>'Reponer Caja Chica' ));
		$WestPanel = $grid->deploywestp();

		$adic = array(
		array('id'=>'fedita',  'title'=>'Agregar/Editar factura de caja chica'),
		array('id'=>'frepon',  'title'=>'Reposici&oacute;n de caja chica'),
		array('id'=>'fborra',  'title'=>'Borar factura de caja chica')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('GSERCHI', 'JQ');
		$param['otros']       = $this->datasis->otros('GSERCHI', 'JQ');
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
		function gserchiadd() {
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function gserchidel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var act  = jQuery("#newapi'.$grid0.'").jqGrid (\'getCell\', id, \'aceptado\');
				if(act!="S"){
					if(confirm(" Seguro desea eliminar el registro?")){
						var ret    = $("#newapi'.$grid0.'").getRowData(id);
						mId = id;
						$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(r){
							try{
								var json = JSON.parse(r);
								if (json.status == "A"){
									$.prompt("Registro Eliminado");
									jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
									return true;
								} else {
									$.prompt(json.mensaje);
								}
							}catch(e){
								$("#fborra").html(r);
								$("#fborra").dialog("open");
							}
						});
					}
				}else{
					$.prompt("<h1>No se puede modificar un registro que fue aceptado</h1>");
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function gserchiedit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var act  = jQuery("#newapi'.$grid0.'").jqGrid (\'getCell\', id, \'aceptado\');
				var ret  = $("#newapi'.$grid0.'").getRowData(id);
				if(act!="S"){
					mId = id;
					$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
						$("#fedita").html(data);
						$("#fedita").dialog( "open" );
					});
				}else{
					$.prompt("<h1>No se puede modificar un registro que fue aceptado</h1>");
				}
			} else {
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
		jQuery("#brepon").click( function(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				caja = jQuery("#newapi'.$grid0.'").jqGrid (\'getCell\', id, \'codbanc\');
				$.post("'.site_url($this->url.'gserchipros').'/'.'"+caja, function(data){
					$("#frepon").html(data);
				});
				$( "#frepon").dialog( "open" );
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		});';

		$bodyscript .= '
		jQuery("#imprime").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('formatos/ver/GSERCHI').'/\'+id+\'/id\'').';
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		});';

		$bodyscript .= '
		jQuery("#baprov").click( function(){
			var grid = jQuery("#newapi'.$grid0.'");
			var rowcells=new Array();
			var s = grid.getGridParam(\'selarrrow\');
			$("#ladicional").html("");
			if(s.length){
				for(var i=0;i<s.length;i++){
					var entirerow = grid.jqGrid(\'getRowData\',s[i]);

					$.post("'.site_url('finanzas/gser/gserchiajax').'",{ id: entirerow["id"]},
						function(data){
							if(data=="1"){
								grid.trigger("reloadGrid");
								return true;
							}else{
								alert("Hubo un error, comuniquese con soporte tecnico: "+data);
								return false;
							}
						}
					);
				}
			}
		});';

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
			buttons: {
				"Guardar": function(){
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
									$.prompt("Registro Guardado");
									$( "#fedita" ).dialog( "close" );
									grid.trigger("reloadGrid");
									return true;
								} else {
									$.prompt(json.mensaje);
								}
							}catch(e){
								$("#fedita").html(r);
							}
						}
					})
				},
				"Cancelar": function() {
					$(this).dialog("close");
					$("#fedita").html("");
				}
			},
			close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
				$("#fedita").html("");
			}
		});';

		$bodyscript .= '
		$("#frepon").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
			buttons: {
				"Guardar": function(){
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
								if(json.status == "A"){
									$.prompt("Pago procesado");
									$( "#frepon" ).dialog( "close" );
									grid.trigger("reloadGrid");
									'.$this->datasis->jwinopen(site_url('formatos/ver/GSER').'/\'+json.pk.id+\'/id\'').';
									return true;
								}else{
									$.prompt(json.mensaje);
								}
							}catch(e){
								$("#frepon").html(r);
							}
						}
					})
				},
				"Cancelar": function() {
					$(this).dialog("close");
					$("#frepon").html("");
				}
			},
			close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
				$("#frepon").html("");
			}
		});';

		$bodyscript .= '
		$("#fborra").dialog({
			autoOpen: false, height: 300, width: 300, modal: true,
			buttons: {
				"Aceptar": function() {
					$( this ).dialog( "close" );
					grid.trigger("reloadGrid");
				}
			},
			close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
		});';

		$bodyscript .= '});'."\n";

		$bodyscript .= '
		function sumamonto(){
			var grid = jQuery("#newapi'.$grid0.'");
			var s;
			var total = 0;
			var rowcells=new Array();
			s = grid.getGridParam(\'selarrrow\');
			$("#ladicional").html("");
			if(s.length){
				for(var i=0;i<s.length;i++){
					var entirerow = grid.jqGrid(\'getRowData\',s[i]);
					total += Number(entirerow["importe"]);
				}
				total = Math.round(total*100)/100;
				$("#ladicional").html("<span style=\"font-size:20px;text-align:center;\" >Bs. "+nformat(total,2)+"</span>");
				$("#montoform").html("Monto: "+nformat(total,2));
				montotal = total;
			}
		};';

		$bodyscript .= '</script>';
		return $bodyscript;
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('aceptado');
		$grid->label('Aprobado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 55,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
			'cellattr'      => 'function(rowId, tv, aData, cm, rdata){
				var tips = "";
				if(aData.aceptado == "S"){
					tips = "Gasto Aceptado";
				}else if(aData.aceptado == "N"){
					tips = "Gasto Rechazado";
				}else{
					tips = "Gasto nuevo";
				}
				return \'title="\'+tips+\'"\';
			}'
		));

		$grid->addField('codbanc');
		$grid->label('Caja');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('fechafac');
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


		$grid->addField('numfac');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('nfiscal');
		$grid->label('N.F&iacute;scal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));


		$grid->addField('rif');
		$grid->label('Rif');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 130,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:13, maxlength: 13 }',
		));


		$grid->addField('proveedor');
		$grid->label('Proveedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));

		$grid->addField('importe');
		$grid->label('Importe');
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

		//$grid->addField('moneda');
		//$grid->label('Moneda');
		//$grid->params(array(
		//	'search'        => 'true',
		//	'editable'      => $editar,
		//	'width'         => 40,
		//	'edittype'      => "'text'",
		//	'editrules'     => '{ required:true}',
		//	'editoptions'   => '{ size:2, maxlength: 2 }',
		//));

		$grid->addField('montasa');
		$grid->label('Base G.');
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

		$grid->addField('tasa');
		$grid->label('Impuesto G.');
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

		$grid->addField('monredu');
		$grid->label('Base R.');
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

		$grid->addField('reducida');
		$grid->label('Impuesto R.');
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

		$grid->addField('monadic');
		$grid->label('Base A.');
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

		$grid->addField('sobretasa');
		$grid->label('Impuesto A.');
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

		$grid->addField('exento');
		$grid->label('Exento');
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

		$grid->addField('sucursal');
		$grid->label('Sucursal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));

		$grid->addField('departa');
		$grid->label('Departamento');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));

		//$grid->addField('ngasto');
		//$grid->label('Ngasto');
		//$grid->params(array(
		//	'search'        => 'true',
		//	'editable'      => $editar,
		//	'width'         => 80,
		//	'edittype'      => "'text'",
		//	'editrules'     => '{ required:true}',
		//	'editoptions'   => '{ size:8, maxlength: 8 }',
		//));

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

		//$grid->setGrouping('ngasto');

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');
		//$grid->setGrouping('codbanc');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('GSERCHI','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('GSERCHI','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('GSERCHI','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('GSERCHI','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');
		$grid->setMultiSelect(true);
		//$grid->setonSelectRow('sumamonto');
		$grid->setOnSelectRow('
			sumamonto,
			afterInsertRow:
			function( rid, aData, rowe){
				if(aData.aceptado=="S"){
					$(this).jqGrid( "setCell", rid, "aceptado","", {color:"#FFFFFF", background:"#166D05" });
				}else if(aData.aceptado=="N"){
					$(this).jqGrid( "setCell", rid, "aceptado","", {color:"#FFFFFF", background:"#FF2C14" });
				}else{
					$(this).jqGrid( "setCell", rid, "aceptado","", {color:"#FFFFFF", background:"#FFE205" });
				}
			}
		');

		$grid->setBarOptions('addfunc: gserchiadd, editfunc: gserchiedit,delfunc: gserchidel');
		$grid->setOndblClickRow('');

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

	function cierregserchi(){
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();

		$uri  = anchor('finanzas/gser/gserchipros/<#codbanc#>','<#codbanc#>');

		$grid = new DataGrid('');
		$select=array('MAX(fechafac) AS fdesde',
					  'MIN(fechafac) AS fhasta',
					  'SUM(tasa+sobretasa+reducida) AS totiva',
					  'SUM(montasa+monadic+monredu+tasa+sobretasa+reducida+exento) AS total',
					  'TRIM(codbanc) AS codbanc',
					  'COUNT(*) AS cana');
		$grid->db->select($select);
		$grid->db->from('gserchi');
		$grid->db->where('ngasto IS NULL');
		$grid->db->where('aceptado','S');
		$grid->db->groupby('codbanc');

		$grid->order_by('codbanc','desc');
		$grid->per_page = 15;
		$grid->column_orderby('Caja',$uri,'codbanc');
		$grid->column('N.facturas','cana','align=\'center\'');
		$grid->column_orderby('Fecha inicial','<dbdate_to_human><#fdesde#></dbdate_to_human>','fdesde','align=\'center\'');
		$grid->column_orderby('Fecha final'  ,'<dbdate_to_human><#fhasta#></dbdate_to_human>','fdesde','align=\'center\'');
		$grid->column_orderby('IVA'   ,'<nformat><#totiva#></nformat>'  ,'totiva' ,'align=\'right\'');
		$grid->column_orderby('Monto' ,'<nformat><#total#></nformat>' ,'total','align=\'right\'');

		$action = "javascript:window.location='".site_url('finanzas/gser/agregar')."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');
		$grid->build();
		//echo $grid->db->last_query();

		echo $grid->output;
	}


	//Convierte los gastos en caja chica
	function gserchipros($codbanc=null){
		if(empty($codbanc)) show_error('Faltan parametros');
		$dbcodbanc=$this->db->escape($codbanc);
		$mSQL='SELECT COUNT(*) AS cana, SUM(exento+montasa+monadic+monredu+tasa+sobretasa+reducida) AS monto FROM gserchi WHERE ngasto IS NULL AND aceptado="S" AND codbanc='.$dbcodbanc;
		$r   =$this->datasis->damerow($mSQL);
		if($r['cana']==0){
			echo heading("Caja ${codbanc} no tiene gastos aprobados, debe primero aprobar algun gasto y luego si puede reponerla");
			return false;
		}

		$mSQL="SELECT a.codprv, b.nombre FROM banc AS a JOIN sprv AS b ON a.codprv=b.proveed WHERE a.codbanc=${dbcodbanc}";
		$query = $this->db->query($mSQL);
		if($query->num_rows()>0){
			$row    = $query->row();
			$nombre = $row->nombre;
			$codprv = $row->codprv;
		}else{
			$nombre =$codprv = '';
		}

		$sql='SELECT TRIM(a.codbanc) AS codbanc,tbanco FROM banc AS a';
		$query = $this->db->query($sql);
		$comis=array();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$ind='_'.$row->codbanc;
				$comis[$ind]['tbanco']=$row->tbanco;
			}
		}
		$json_comis=json_encode($comis);

		$this->rapyd->load('dataform','datagrid2');

		$modbus=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'Codigo Proveedor',
				'nombre'  =>'Nombre',
				'rif'     =>'RIF'),
			'filtro'  =>array('proveed'=>'Codigo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'codprv','nombre'=>'nombre'),
			'titulo'  =>'Buscar Proveedor',
			'script'  =>array('post_modbus()')
		);
		$bsprv=$this->datasis->modbus($modbus);

		$script='var comis = '.$json_comis.';

		$(document).ready(function() {
			desactivacampo("");
			$("#codprv").autocomplete({
				source: function( req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscasprv').'",
						type: "POST",
						dataType: "json",
						data: {"q":req.term},
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#nombre").val("");
									$("#nombre_val").text("");
									$("#codprv").val("");
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
				minLength: 2,
				select: function( event, ui ) {
					$("#codprv").attr("readonly", "readonly");

					$("#nombre").val(ui.item.nombre);
					$("#nombre_val").text(ui.item.nombre);
					$("#codprv").val(ui.item.proveed);

					setTimeout(function(){ $("#codprv").removeAttr("readonly"); }, 1500);
				}
			});

			desactivacampo($("#cargo").val());
		});

		function post_modbus(){
			nombre=$("#nombre").val();
			$("#nombre_val").text(nombre);
		}

		function desactivacampo(codb1){
			if(codb1.length>0 && codb1!="'.$this->mcred.'"){
				eval("tbanco=comis._"+codb1+".tbanco;"  );
				if(tbanco=="CAJ"){
					$("#cheque").attr("disabled","disabled");
					$("#benefi").attr("disabled","disabled");
				}else{
					$("#cheque").removeAttr("disabled");
					$("#benefi").removeAttr("disabled");
				}
			}else{
				$("#cheque").attr("disabled","disabled");
				$("#benefi").attr("disabled","disabled");
			}
		}';

		$pcchi=$this->datasis->damerow("SELECT proveed, nombre FROM sprv WHERE nombre LIKE '%CAJA%CHICA%' LIMIT 1");

		$form = new DataForm($this->url.'gserchipros/'.$codbanc.'/process');
		$form->title("N&uacute;mero de facturas aceptadas $r[cana], monto total <b>".nformat($r['monto']).'</b> para la caja '.$codbanc);
		$form->script($script);

		$form->codprv = new inputField('Proveedor', 'codprv');
		$form->codprv->rule='required';
		$form->codprv->insertValue=$codprv;
		$form->codprv->size=8;
		$form->codprv->append($bsprv);
		$form->codprv->insertValue=(empty($pcchi))? '' : $pcchi['proveed'];

		$form->nombre = new inputField('Nombre', 'nombre');
		$form->nombre->rule='required';
		$form->nombre->insertValue=$nombre;
		$form->nombre->in = 'codprv';
		$form->nombre->type='inputhidden';
		$form->nombre->insertValue=(empty($pcchi))? '' : $pcchi['nombre'];

		$dbcodban=$this->db->escape($codbanc);
		$form->cargo = new dropdownField('Reponer desde','cargo');
		$form->cargo->option('','Seleccionar');
		$form->cargo->options("SELECT codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE activo='S' AND tipocta<>'Q' AND codbanc<>$dbcodban ORDER BY codbanc");
		$form->cargo->option($this->mcred,'CREDITO');
		$form->cargo->onchange='desactivacampo(this.value)';
		$form->cargo->rule='max_length[5]|required';

		$form->cheque = new inputField('Cheque Numero', 'cheque');
		$form->cheque->rule='condi_required|callback_chobligaban';
		//$form->cheque->append('Aplica si es un banco');
		$form->cheque->group='Aplica si repone desde un Banco';
		$form->cheque->size=12;

		$form->benefi = new inputField('Beneficiario', 'benefi');
		$form->benefi->insertValue=$nombre;
		$form->benefi->rule='condi_required|callback_chobligaban|strtoupper';
		//$form->benefi->append('Aplica si es un banco');
		$form->benefi->group=$form->cheque->group;

		$form->build_form();

		$grid = new DataGrid2("Lista de facturas aceptadas para pagar de la caja ${codbanc}",'gserchi');
		$select=array('exento + montasa + monadic + monredu + tasa + sobretasa + reducida AS totneto','descrip',
					  'tasa + sobretasa + reducida AS totiva','proveedor','fechafac','numfac','codbanc' );
		$grid->totalizar('totneto','totiva');
		$grid->db->select($select);
		$grid->db->where('aceptado','S');
		$grid->db->where('ngasto IS NULL');
		$grid->db->where('codbanc',$codbanc);

		$grid->order_by('fechafac','desc');
		$grid->per_page = 15;
		//$grid->column('Caja','codbanc');
		$grid->column('N&uacute;mero','numfac');
		$grid->column('Fecha' ,'<dbdate_to_human><#fechafac#></dbdate_to_human>','align=\'center\'');
		$grid->column('Proveedor','proveedor');
		$grid->column('Descripci&oacute;n','descrip');
		$grid->column('IVA'   ,'<nformat><#totiva#></nformat>'  ,'align=\'right\'');
		$grid->column('Monto' ,'<b><nformat><#totneto#></nformat></b>' ,'align=\'right\'');

		//$grid->add('finanzas/gser/datagserchi/create','Agregar nueva factura');
		$grid->build();

		if($form->on_success()){
			$codprv  = $form->codprv->newValue;
			$cargo   = $form->cargo->newValue;
			$nombre  = $form->nombre->newValue;
			$benefi  = $form->benefi->newValue;
			$cheque  = $form->cheque->newValue;

			$rt=$this->_gserchipros($codbanc,$cargo,$codprv,$benefi,$cheque);

			if($rt){
				$rt=array(
					'status' => 'A',
					'mensaje'=> 'Registro guardado',
					'pk'     => array('id'=>$this->idgser)
				);
				echo json_encode($rt);
			}else{
				$rt=array(
					'status' => 'B',
					'mensaje'=> 'No se pudo guardar',
					'pk'     => ''
				);
				echo json_encode($rt);
			}
		}else{
			echo $form->output.$grid->output;
		}

	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE  = $grid->geneTopWhere('gserchi');
		$mWHERE[]= array('','ngasto IS NULL','');
		$response   = $grid->getData('gserchi', array(array()), array(), false, $mWHERE, 'codbanc' );
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
		$mcodp  = '??????';
		$check  = 0;

		//unset($data['oper']);
		//unset($data['id']);
		//if($oper == 'add'){
		//	if(false == empty($data)){
		//		$check = $this->datasis->dameval("SELECT count(*) FROM gserchi WHERE $mcodp=".$this->db->escape($data[$mcodp]));
		//		if ( $check == 0 ){
		//			$this->db->insert('gserchi', $data);
		//			echo "Registro Agregado";
        //
		//			logusu('gserchi',"Registro ????? INCLUIDO");
		//		} else
		//			echo "Ya existe un registro con ese $mcodp";
		//	} else
		//		echo "Fallo Agregado!!!";
        //
		//} elseif($oper == 'edit') {
		//	$nuevo  = $data[$mcodp];
		//	$anterior = $this->datasis->dameval("SELECT $mcodp FROM gserchi WHERE id=$id");
		//	if ( $nuevo <> $anterior ){
		//		//si no son iguales borra el que existe y cambia
		//		$this->db->query("DELETE FROM gserchi WHERE $mcodp=?", array($mcodp));
		//		$this->db->query("UPDATE gserchi SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
		//		$this->db->where("id", $id);
		//		$this->db->update("gserchi", $data);
		//		logusu('gserchi',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
		//		echo "Grupo Cambiado/Fusionado en clientes";
		//	} else {
		//		unset($data[$mcodp]);
		//		$this->db->where("id", $id);
		//		$this->db->update('gserchi', $data);
		//		logusu('gserchi',"Caja chica  ".$nuevo." MODIFICADO");
		//		echo "$mcodp Modificado";
		//	}
        //
		//} elseif($oper == 'del') {
		//	$meco = $this->datasis->dameval("SELECT $mcodp FROM gserchi WHERE id=$id");
		//	//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM gserchi WHERE id='$id' ");
		//	if ($check > 0){
		//		echo " El registro no puede ser eliminado; tiene movimiento ";
		//	} else {
		//		$this->db->simple_query("DELETE FROM gserchi WHERE id=$id ");
		//		logusu('GSERCHI',"Registro ????? ELIMINADO");
		//		echo "Registro Eliminado";
		//	}
		//};
	}

	function gserchiajax(){
		$id   = $this->input->post('id');
		$dbid = $this->db->escape($id);
		$rt='0';
		if($id!==false){
			$mSQL="UPDATE gserchi SET aceptado=IF(aceptado='S','N','S') WHERE id=$dbid AND ngasto IS NULL";
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){
				$rt='0';
			}else{
				$sta    = $this->datasis->dameval("SELECT aceptado FROM gserchi WHERE id=$dbid");
				$estado = ($sta=='S')?'ACEPTADO':'RECHAZADO';
				logusu('GSERCHI',"Factura cchi $id $estado");
				$rt='1';
			}
		}
		echo $rt;
	}

	function dataedit(){
		$this->rapyd->load('dataedit');
		$mgas=array(
			'tabla'   => 'mgas',
			'columnas'=> array('codigo' =>'Codigo','descrip'=>'Descripcion','tipo'=>'Tipo'),
			'filtro'  => array('descrip'=>'Descripcion'),
			'retornar'=> array('codigo' =>'codigo','descrip'=>'descrip'),
			'titulo'  => 'Buscar enlace administrativo');
		$bcodigo=$this->datasis->modbus($mgas);

		$ivas=$this->datasis->ivaplica();

		$tasa      = $ivas['tasa']/100;
		$redutasa  = $ivas['redutasa']/100;
		$sobretasa = $ivas['sobretasa']/100;

		$consulrif=$this->datasis->traevalor('CONSULRIF');
		$url=site_url('ajax/ajaxsprv');
		$script="
		function consulrif(){
			vrif=$('#rif').val();
			if(vrif.length==0){
				alert('Debe introducir primero un RIF');
			}else{
				vrif=vrif.toUpperCase();
				$('#rif').val(vrif);
				window.open('${consulrif}'+'?p_rif='+vrif,'CONSULRIF','height=350,width=410');
			}
		}

		function poneiva(tipo){
			if(tipo==1){
				ptasa = $redutasa;
				campo = 'reducida';
				monto = 'monredu';
			} else if (tipo==3){
				ptasa = $sobretasa;
				campo = 'sobretasa';
				monto = 'monadic'
			} else {
				ptasa = $tasa;
				campo = 'tasa';
				monto = 'montasa';
			}
			if($('#'+monto).val().length>0)  base=parseFloat($('#'+monto).val());   else  base  =0;
			$('#'+campo).val(roundNumber(base*ptasa,2));
			totaliza();
		}

		function totaliza(){
			if($('#montasa').val().length>0)   montasa  =parseFloat($('#montasa').val());   else  montasa  =0;
			if($('#tasa').val().length>0)      tasa     =parseFloat($('#tasa').val());      else  tasa     =0;
			if($('#monredu').val().length>0)   monredu  =parseFloat($('#monredu').val());   else  monredu  =0;
			if($('#reducida').val().length>0)  reducida =parseFloat($('#reducida').val());  else  reducida =0;
			if($('#monadic').val().length>0)   monadic  =parseFloat($('#monadic').val());   else  monadic  =0;
			if($('#sobretasa').val().length>0) sobretasa=parseFloat($('#sobretasa').val()); else  sobretasa=0;
			if($('#exento').val().length>0)    exento   =parseFloat($('#exento').val());    else  exento   =0;

			total=roundNumber(montasa+tasa+monredu+reducida+monadic+sobretasa+exento,2);
			$('#importe').val(total);
			$('#importe_val').text(nformat(total,2));
		}

		$(function(){
			$('#codigo').autocomplete({
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
									$('#codigo').val('');
									$('#descrip').val('');
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
					$('#codigo').attr('readonly', 'readonly');

					$('#codigo').val(ui.item.codigo);
					$('#descrip').val(ui.item.descrip);
					setTimeout(function() {  $('#codigo').removeAttr('readonly'); }, 1500);
				}
			});

			$('#fechafac').datepicker({ dateFormat: 'dd/mm/yy' });
			$('#importe_val').css('font-size','2em');
			$('#importe_val').css('font-weight','bold');

			$('.inputnum').numeric('.');
			$('#exento'   ).bind('keyup',function() { totaliza(); });
			$('#montasa'  ).bind('keyup',function() { poneiva(2); });
			$('#tasa'     ).bind('keyup',function() { totaliza(); });
			$('#monredu'  ).bind('keyup',function() { poneiva(1); });
			$('#reducida' ).bind('keyup',function() { totaliza(); });
			$('#monadic'  ).bind('keyup',function() { poneiva(3); });
			$('#sobretasa').bind('keyup',function() { totaliza(); });

			$('#rif').focusout(function(){
				rif=$('#rif').val().toUpperCase();
				$('#rif').val(rif);
				if(rif.length > 0){
					$.post('$url', { rif: rif },function(data){
						$('#proveedor').val(data);
					});
				}else{
					alert('Debe introducir un rif');
				}
			});

			totaliza();
		});";

		$edit = new DataEdit('', 'gserchi');
		$edit->back_url = site_url('finanzas/gser/gserchi');
		$edit->on_save_redirect=false;
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert' ,'_pre_insert');
		$edit->pre_process('update' ,'_pre_update');
		$edit->pre_process('delete' ,'_pre_delete');

		$edit->codbanc = new dropdownField('Caja','codbanc');
		$edit->codbanc->option('','Seleccionar');
		$edit->codbanc->options("SELECT TRIM(codbanc) AS codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE tbanco IN ('CAJ','CHI') AND codbanc!='00' AND tipocta='Q' ORDER BY codbanc");
		$edit->codbanc->rule='max_length[2]|required';
		$edit->codbanc->style = 'width:180px';

		$edit->fechafac = new dateField('Fecha','fechafac');
		$edit->fechafac->rule='max_length[10]|required';
		$edit->fechafac->size =12;
		$edit->fechafac->insertValue=date('Y-m-d');
		$edit->fechafac->maxlength =10;
		$edit->fechafac->calendar=false;

		$edit->numfac = new inputField('Factura','numfac');
		$edit->numfac->rule='max_length[8]|required';
		$edit->numfac->size =12;
		$edit->numfac->maxlength =20;
		$edit->numfac->autocomplete =false;

		$edit->nfiscal = new inputField('Control fiscal','nfiscal');
		$edit->nfiscal->rule='max_length[12]|required';
		$edit->nfiscal->size =14;
		$edit->nfiscal->maxlength =20;
		$edit->nfiscal->autocomplete =false;

		//$lriffis='<a href="javascript:consulrif();" title="Consultar RIF en el SENIAT" onclick="">Consultar RIF en el SENIAT</a>';
		$edit->rif = new inputField('RIF','rif');
		$edit->rif->rule='max_length[13]|required';
		$edit->rif->size =13;
		$edit->rif->maxlength =13;
		$edit->rif->group='Datos del proveedor';
		//$edit->rif->append(HTML::button('traesprv', 'SENIAT', '', 'button', 'button'));
		//$edit->rif->append($lriffis);

		$edit->proveedor = new inputField('Nombre','proveedor');
		$edit->proveedor->rule='max_length[40]|strtoupper';
		$edit->proveedor->size =40;
		$edit->proveedor->group='Datos del proveedor';
		$edit->proveedor->maxlength =40;

		$edit->codigo = new inputField('Gasto','codigo');
		$edit->codigo->rule ='max_length[6]|required';
		$edit->codigo->size =6;
		$edit->codigo->maxlength =8;
		$edit->codigo->append($bcodigo);

		$edit->descrip = new inputField('Descripci&oacute;n','descrip');
		$edit->descrip->rule='max_length[50]|strtoupper';
		$edit->descrip->size =50;
		$edit->descrip->maxlength =50;

		$alicuota=$this->datasis->ivaplica(date('Y-m-d'));

		$arr=array(
			'exento'   =>'Monto <b>Exento</b>|Monto exento',
			'montasa'  =>'Base Tasa G. '.htmlnformat($ivas['tasa']).'%|Base imponible',
			'tasa'     =>'IVA G. '.htmlnformat($ivas['tasa']).'%|Monto del IVA',
			'monredu'  =>'Base Tasa R. '.htmlnformat($ivas['redutasa']).'%|Base imponible',
			'reducida' =>'IVA R. '.htmlnformat($ivas['redutasa']).'%|Monto del IVA',
			'monadic'  =>'Base Tasa A. '.htmlnformat($ivas['sobretasa']).'%|Base imponible',
			'sobretasa'=>'IVA A. '.htmlnformat($ivas['sobretasa']).'%|Monto del IVA',
			'importe'  =>'Importe total');

		foreach($arr AS $obj=>$label){
			$pos = strrpos($label, '|');
			if($pos!==false){
				$piv=explode('|',$label);
				$label=$piv[0];
				$grupo=$piv[0];
			}else{
				$grupo='';
			}
			$edit->$obj = new inputField($label,$obj);
			$edit->$obj->rule='max_length[17]|numeric';
			$edit->$obj->css_class='inputnum';
			$edit->$obj->insertValue =0;
			$edit->$obj->size =12;
			$edit->$obj->maxlength =12;
			//$edit->$obj->group=$grupo;
			$edit->$obj->autocomplete=false;
		}
		$edit->$obj->readonly=true;

		$edit->tasa->rule     ='condi_required|max_length[17]|callback_chtasa';
		$edit->reducida->rule ='condi_required|max_length[17]|callback_chreducida';
		$edit->sobretasa->rule='condi_required|max_length[17]|callback_chsobretasa';
		$edit->importe->rule  ='max_length[17]|numeric|positive';
		$edit->importe->type  ='inputhidden';
		$edit->importe->label ='<b style="font-size:2em">Total</b>';
		$edit->importe->showformat ='decimal';

		$edit->sucursal = new dropdownField('Sucursal','sucursal');
		$edit->sucursal->options('SELECT codigo,sucursal FROM sucu ORDER BY sucursal');
		$edit->sucursal->rule ='max_length[2]|required';
		$edit->sucursal->style= "width:300px";

		$edit->departa = new dropdownField('Departamento','departa');
		$edit->departa->options("SELECT TRIM(depto) AS codigo, CONCAT_WS('-',depto,TRIM(descrip)) AS label FROM dpto WHERE tipo='G' ORDER BY depto");
		$edit->departa->rule ='max_length[2]';
		$edit->departa->style= "width:300px";

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->estampa = new autoUpdateField('estampa' ,date('YmD'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:m:s'), date('H:m:s'));

		//$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);

			echo json_encode($rt);
		}else{
			$conten['form']  =&  $edit;
			$data['content'] = $this->load->view('view_gserchi', $conten);

			//$data['script'] = $script;
			//$this->load->view('view_gserchi', $data);

			//echo $edit->output;
		}

	}

	function _pre_insert($do){
		$rif   =$do->get('rif');
		$dbrif = $this->db->escape($rif);
		$nombre=$do->get('proveedor');
		$fecha =date('Y-m-d');
		$csprv  =intval($this->datasis->dameval('SELECT COUNT(*) AS cana FROM sprv WHERE rif='.$dbrif));
		$csprv +=intval($this->datasis->dameval('SELECT COUNT(*) AS cana FROM provoca WHERE rif='.$dbrif));
		if($csprv==0){
			$mSQL ='INSERT IGNORE INTO provoca (rif,nombre,fecha) VALUES ('.$dbrif.','.$this->db->escape($nombre).','.$this->db->escape($fecha).')';
			$this->db->simple_query($mSQL);
		}

		$total  = 0;
		$total += $do->get('exento')   ;
		$total += $do->get('montasa')  ;
		$total += $do->get('tasa')     ;
		$total += $do->get('monredu')  ;
		$total += $do->get('reducida') ;
		$total += $do->get('monadic')  ;
		$total += $do->get('sobretasa');

		if($total>0){
			$do->set('importe',$total);
			return true;
		}else{
			$do->error_message_ar['pre_ins'] = $do->error_message_ar['pre_upd'] = 'No se puede guardar un gasto con monto cero';
			return false;
		}
	}

	function chobligaban($val){
		$ban=$this->input->post('cargo');
		if($ban==$this->mcred) return true;
		$tipo=common::_traetipo($ban);
		if($tipo!='CAJ'){
			if(empty($val)){
				$this->validation->set_message('chobligaban', 'El campo %s es obligatorio cuando el cargo es a un banco');
				return false;
			}
		}
		return true;
	}

	function _pre_update($do){
		$aceptado = $do->get('aceptado');
		if($aceptado!='S'){
			return true;
		}else{
			$do->error_message_ar['pre_upd'] = 'No se puede modificar un gasto aceptado';
			return false;
		}
		$rt=$this->_pre_insert($do);
		return $rt;
	}

	function _pre_delete($do){
		$aceptado = $do->get('aceptado');
		if($aceptado=='S'){
			$do->error_message_ar['pre_del'] = 'No se puede eliminar un gasto aprobado';
			return false;
		}else{
			return true;
		}
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		$numero  = $do->get('numero');
		logusu($do->table,"Creo factura caja chica numero ${numero} id $primary ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		$numero  = $do->get('numero');
		logusu($do->table,"Modifico factura caja chica numero ${numero} $primary ");
	}

	function _post_delete($do){
		$primary = implode(',',$do->pk);
		$numero  = $do->get('numfac');
		logusu($do->table,"Elimino factura caja chica numero ${numero} $primary ");
	}

	function vista(){

	}

	function instalar(){
		if (!$this->db->table_exists('gserchi')) {
			$mSQL="CREATE TABLE `gserchi` (
			  `codbanc` varchar(5) NOT NULL DEFAULT '',
			  `fechafac` date DEFAULT NULL,
			  `numfac` varchar(8) DEFAULT NULL,
			  `nfiscal` varchar(12) DEFAULT NULL,
			  `rif` varchar(13) DEFAULT NULL,
			  `proveedor` varchar(40) DEFAULT NULL,
			  `codigo` varchar(6) DEFAULT NULL,
			  `descrip` varchar(50) DEFAULT NULL,
			  `moneda` char(2) DEFAULT NULL,
			  `montasa` decimal(17,2) DEFAULT '0.00',
			  `tasa` decimal(17,2) DEFAULT NULL,
			  `monredu` decimal(17,2) DEFAULT '0.00',
			  `reducida` decimal(17,2) DEFAULT NULL,
			  `monadic` decimal(17,2) DEFAULT '0.00',
			  `sobretasa` decimal(17,2) DEFAULT NULL,
			  `exento` decimal(17,2) DEFAULT '0.00',
			  `importe` decimal(12,2) DEFAULT NULL,
			  `sucursal` char(2) DEFAULT NULL,
			  `departa` char(2) DEFAULT NULL,
			  `ngasto` varchar(8) DEFAULT NULL,
			  `usuario` varchar(12) DEFAULT NULL,
			  `estampa` date DEFAULT NULL,
			  `hora` varchar(8) DEFAULT NULL,
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `aceptado` char(1) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('gserchi');
		//if(!in_array('<#campo#>',$campos)){ }
	}

}
