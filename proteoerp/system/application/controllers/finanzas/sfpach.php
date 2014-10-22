<?php
class sfpach extends Controller {
	var $titp='Arreglo y Deposito de Cheques ';
	var $tits='Arreglo y Deposito de Cheques';
	var $url ='finanzas/sfpach/';

	function sfpach(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('A00',1);
		//$this->instalar();
	}

	function index(){
		//redirect($this->url.'filteredgrid');
		 if ( !$this->datasis->iscampo('sfpa','id') ) {
			$this->db->simple_query('ALTER TABLE sfpa ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		if ( !$this->datasis->iscampo('sfpa','deposito') ) {
			$this->db->query('ALTER TABLE sfpa ADD COLUMN deposito CHAR(12) NULL DEFAULT NULL ');
		};
		if ( !$this->datasis->iscampo('sfpa','cuentach') ) {
			$this->db->query('ALTER TABLE sfpa ADD COLUMN cuentach CHAR(22) NULL DEFAULT NULL ');
		};
		if ( !$this->datasis->iscampo('bcaj','codbanc') ) {
			$this->db->query('ALTER TABLE bcaj ADD COLUMN codbanc CHAR(2) NULL DEFAULT NULL ');
		};
		$this->db->query("INSERT IGNORE INTO banc SET codbanc='00', tbanco='CAJ', proxch='000000000000', saldo=0, numcuent='CAJA TRANSITO', banco='CAJA TRANSITO', activo='S', tipocta='K'");
		redirect($this->url.'jqdatag');
	}


	//***************************
	//
	//   Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript($param['grids'][0]['gridname']);

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		$grid->wbotonadd(array("id"=>"depositar", "img"=>"assets/default/images/cheque.png",  "alt" => 'Enviar Cheques',  "label"=>"Enviar a Depositar", 'tema'=>'anexos'));
		$grid->wbotonadd(array("id"=>"efectivo",  "img"=>"assets/default/images/monedas.png", "alt" => 'Enviar Efectivo', "label"=>"Enviar Efectivo",    'tema'=>'anexos'));
		$grid->wbotonadd(array("id"=>"ocultar",   "img"=>"images/delete.png",  "alt" => 'No Depositar',    "label"=>"No Depositar"));
		$WestPanel = $grid->deploywestp();

		$mSQL  = "SELECT codbanc, CONCAT(codbanc, ' ', TRIM(banco), IF(tbanco='CAJ',' ',numcuent) ) banco FROM banc WHERE tbanco='CAJ' AND activo='S' AND codbanc<>'00' ORDER BY codbanc ";
		$cajas = $this->datasis->llenaopciones($mSQL, true, 'envia');
		$efcaja = $this->datasis->llenaopciones($mSQL, true, 'efcaja');

		$mSQL   = "SELECT codbanc, CONCAT(codbanc, ' ', TRIM(banco),' ', IF(tbanco='CAJ',' ',numcuent) ) banco FROM banc WHERE tbanco<>'CAJ' AND activo='S' ORDER BY codbanc ";
		$bancos = $this->datasis->llenaopciones($mSQL, true, 'recibe');
		$efbanco = $this->datasis->llenaopciones($mSQL, true, 'efbanco');

		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'));

		$SouthPanel .= '
		<div id="deposito-form" title="Enviar Deposito en Cheque">
			<p class="validateTips" style="font-size:18px">Indique la caja que envia y la cuenta de banco que recibe.</p>
			<form>
			<fieldset style="border:none;font-size:12px;">
				<label for="caj">Caja</label>
				'.$cajas.'<br><br>
				<label for="banc">Banco</label>
				'.$bancos.'<br><br>
				<div id="montoform" style="font-size:20px;text-align:center"></div>
			</fieldset>
			</form>
		</div>';

		$SouthPanel .= '
		<div id="efectivo-form" title="Enviar Deposito en Efectivo">
			<p class="validateTips" style="font-size:18px">Indique la caja que envia, la cuenta de banco que recibe y el monto.</p>
			<form>
			<fieldset style="border:none;font-size:12px;">
				<label for="caj">Caja</label>
				'.$efcaja.'<br><br>
				<label for="banc">Banco</label>
				'.$efbanco.'<br><br>
				<label for="banc">Monto</label>
				<input class="inputnum" id="efmonto" size="12" type="text" style="text-align:right;">
			</fieldset>
			</form>
		</div>
		';

		$SouthPanel .= '
		<div id="nodeposito-form" title="Marcar Cheques como ya depositados">
			<form>
			<fieldset style="border:none;font-size:12px;">
				<h1>Seguro que desea marcar estos cheques como ya depositados?</h1>
			</fieldset>
			</form>
		</div>';



		$param['WestPanel']   = $WestPanel;
		//$param['funciones']   = $funciones;

		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('SFPACH', 'JQ');
		$param['otros']       = $this->datasis->otros('SFPACH', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$this->load->view('jqgrid/crud2',$param);

	}	


	//*********************************************
	//
	// Funciones de botones en javascript
	//
	function bodyscript($grid){
		$bodyscript = '<script type="text/javascript">';

		$bodyscript .= '
		var montotal = 0;
		function probar( o, n ) {
			if ( o.val().length < 1 ) {
				o.addClass( "ui-state-error" );
				updateTips( "Seleccion un " + n + "." );
				return false;
			} else {
				return true;
			}
		};';

		$bodyscript .= '
		$(function() {
			var 	envia = $( "#envia" ),
			recibe = $( "#recibe" ),
			efcaja  = $( "#efcaja" ),
			efbanco = $( "#efbanco" ),
			efmonto = $( "#efmonto" ),
			allFields = $( [] ).add( envia ).add( recibe ).add(efcaja).add(efbanco).add(efmonto);
			var grid = jQuery("#newapi'.$grid.'");
			//var s = grid.getGridParam(\'selarrrow\'); ';

		$bodyscript .= '
			$( "#depositar" ).click(function() {
				var s = grid.getGridParam(\'selarrrow\'); 
				if(s.length){
					sumamonto();
					$( "#deposito-form" ).dialog( "open" );
				} else {
					$.prompt("<h1>Seleccione uno o mas Cheques</h1>");
				}
			});

			$( "#deposito-form" ).dialog({
				autoOpen: false,
				height: 300,
				width: 420,
				modal: true,
				buttons: {
					"Guardar": function() {
						var bValid = true;
						s = grid.getGridParam(\'selarrrow\'); 
						allFields.removeClass( "ui-state-error" );
						bValid = bValid && probar( envia,  "Caja" );
						bValid = bValid && probar( recibe, "Banco" );
						if ( bValid ) {
		                                        $.ajax({
		                                                type: "POST",
		                                                url:"'.site_url("finanzas/sfpach/depositos").'",
		                                                processData: true,
		                                                data: "envia="+escape(envia.val())+"&recibe="+escape(recibe.val())+"&monto="+escape(montotal)+"&ids="+escape(s),
		                                                success: function(a){
									var res = $.parseJSON(a);
									$.prompt(res.mensaje,
										{ submit: function(e,v,m,f){
											window.open(\''.base_url().'formatos/ver/BANCAJA/\'+res.numero, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
											}
										}
									);
									grid.trigger("reloadGrid");
									sumamonto();
									return [true, a ];
								}
							})
							$( this ).dialog( "close" );
						}
					},
					Cancelar: function() {$( this ).dialog( "close" );}
				},
				close: function() {allFields.val( "" ).removeClass( "ui-state-error" );}
			});
			';


		$bodyscript .= '
			// Enviar deposito en efectivo	
			$( "#efectivo" ).click(function() {
				$( "#efectivo-form" ).dialog( "open" );
			});

			$( "#efectivo-form" ).dialog({
				autoOpen: false,
				height: 300,
				width: 420,
				modal: true,
				buttons: {
					"Guardar": function() {
						var bValid = true;
						allFields.removeClass( "ui-state-error" );
						bValid = bValid && probar( efcaja,  "Caja" );
						bValid = bValid && probar( efbanco, "Banco" );
						bValid = bValid && probar( efmonto, "Monto" );
						if ( bValid ) {
		                                        $.ajax({
		                                                type: "POST",
		                                                url:"'.site_url("finanzas/sfpach/efectivo").'",
		                                                processData: true,
		                                                data: "caja="+escape(efcaja.val())+"&banco="+escape(efbanco.val())+"&monto="+escape(efmonto.val()),
		                                                success: function(a){
									var res = $.parseJSON(a);
									$.prompt(res.mensaje,
										{ submit: function(e,v,m,f){
											window.open(\''.base_url().'formatos/ver/BANCAJA/\'+res.numero, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
											}
										}
									);
									return [true, a ];
									}
							})
							$( this ).dialog( "close" );
						}
					},
					Cancelar: function() {$( this ).dialog( "close" );}
				},
				close: function() {allFields.val( "" ).removeClass( "ui-state-error" );}
			});';


		$bodyscript .= '
			// No Depositar	
			$( "#ocultar" ).click(function() {
				var s = grid.getGridParam(\'selarrrow\'); 
				if(s.length){
					sumamonto();
					$( "#nodeposito-form" ).dialog( "open" );
				} else {
					$.prompt("<h1>Seleccione uno o mas Cheques</h1>");
				}
			});

			$( "#nodeposito-form" ).dialog({
				autoOpen: false,
				height: 200,
				width: 400,
				modal: true,
				buttons: {
					"Guardar": function() {
						var bValid = true;
						s = grid.getGridParam(\'selarrrow\'); 
						allFields.removeClass( "ui-state-error" );
						bValid = bValid && probar( envia,  "Caja" );
						bValid = bValid && probar( recibe, "Banco" );
						if ( bValid ) {
							$.ajax({
								type: "POST",
								url:"'.site_url("finanzas/sfpach/nodeposito").'",
								processData: true,
								data: "envia="+escape(envia.val())+"&recibe="+escape(recibe.val())+"&monto="+escape(montotal)+"&ids="+escape(s),
								success: function(a){
									var res = $.parseJSON(a);
									$.prompt(res.mensaje,
										{ submit: function(e,v,m,f){
											//window.open(\''.base_url().'formatos/ver/BANCAJA/\'+res.numero, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
										}}
									);
									grid.trigger("reloadGrid");
									sumamonto();
									return [true, a ];
								}
							})
							$( this ).dialog( "close" );
						}
					},
					Cancelar: function() {$( this ).dialog( "close" );}
				},
				close: function() {allFields.val( "" ).removeClass( "ui-state-error" );}
			});
			';



		$bodyscript .= '
		});';
		
		$bodyscript .= '
		function sumamonto(){ 
			var grid     = jQuery("#newapi'.$grid.'");
			var total    = 0;
			var rowcells = new Array();
			var s = grid.getGridParam(\'selarrrow\'); 
			$("#ladicional").html("");
			if(s.length)
			{
				for(var i=0;i<s.length;i++)
				{
					var entirerow = grid.jqGrid(\'getRowData\',s[i]);
					total += Number(entirerow["monto"]);
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

	function defgrid( $deployed = false ){
		$i = 1;

		$grid  = new $this->jqdatagrid;

		$grid->addField('status');
		$grid->label('Status');
		$grid->params(array(
				'width'    => 30,
				'align'    => "'center'",
				'editable' => 'false',
				'edittype' => "'text'"
			)
		);

		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
				'align'       => "'center'",
				'width'       => 80,
				'search'      => 'true',
				'editable'    => 'false',
				'edittype'    => "'text'",
				'editrules'   => '{ required:true,date:true}',
				'formoptions' => '{ label:"Fecha" }'
			)
		);

		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
				'align'         => "'center'",
				'width'         => 30,
				'editable'      => 'true',
				'edittype'      => "'select'",
				'editrules'     => '{ required:true }',
				'editoptions'   => '{ dataUrl: "'.base_url().'ajax/ddtarjeta"}',
				'stype'         => "'text'"
			)
		);

		$grid->addField('num_ref');
		$grid->label('Nro. Cheque');
		$grid->params(array(
				'width'       => 90,
				'editable'    => 'true',
				'edittype'    => "'text'",
				'editrules'   => '{required:true}',
				'editoptions' => '{ size:20, maxlength: 12 }',
			)
		);

		$grid->addField('monto');
		$grid->label('Monto');
		$grid->params(array(
				'width'         => 100,
				'editable'      => 'true',
				'align'         => "'right'",
				'edittype'      => "'text'",
				'search'        => 'true',
				'editrules'     => '{ required:true }',
				'editoptions'   => '{ size:10, maxlength: 10 }',
				'formatter'     => "'number'",
				'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
			)
		);

		$grid->addField('cuentach');
		$grid->label('Cta Corriente');
		$grid->params(array(
				'align'       => "'center'",
				'width'       => 150,
				'editable'    => 'true',
				'edittype'    => "'text'",
				'editrules'   => '{required:false}',
				'editoptions' => '{ size:20, maxlength: 20 }',
			)
		);

		$grid->addField('banco');
		$grid->label('Banco');
		$grid->params(array(
				'width'         => 40,
				'hidden'        => 'true',
				'editable'      => 'true',
				'edittype'      => "'select'",
				'editrules'     => '{ edithidden:true, required:true }',
				'editoptions'   => '{ dataUrl: "'.base_url().'ajax/ddbanco"}',
				'stype'         => "'text'",
			)
		);

		$grid->addField('nombanc');
		$grid->label('Nombre del Banco');
		$grid->params(array(		
				'width'         => 140,
				'editable'      => 'false',
				'edittype'      => "'text'",
				'search'        => 'true'
			)
		);


		$grid->addField('nombre');
		$grid->label('Nombre Cliente');
		$grid->params(array(
				'width'    => 180,
				'editable' => 'false',
				'edittype' => "'text'"
			)
		);



		$grid->addField('tipo_doc');
		$grid->label('Doc.');
		$grid->params(array(
				'width'    => 30,
				'align'    => "'center'",
				'editable' => 'false',
				'edittype' => "'text'"
			)
		);

		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
				'align'    => "'center'",
				'width'    => 70,
				'editable' => 'false',
				'edittype' => "'text'"
			)
		);


		$grid->addField('nomcajero');
		$grid->label('Nombre Cajero');
		$grid->params(array(
				'width'         => 120,
				'editable'      => 'false',
				'edittype'      => "'text'"
			)
		);

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
				'align'    => "'center'",
				'frozen'   => 'true',
				'width'    => 60,
				'editable' => 'false',
				'search'   => 'false'
			)
		);

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setMultiSelect(true);
		$grid->setonSelectRow('sumamonto');

		$grid->setFormOptionsE('closeAfterEdit:false, mtype: "POST", width: 420, height:220, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){ if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd: true,  mtype: "POST", width: 400, height:220, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){ if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');

		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		$grid->setAfterShow('function(formid) { alert(formid); }');

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(true);
		$grid->setDelete(false);
		$grid->setSearch(true);
		$grid->setRowNum(20);
            
		$grid->setShrinkToFit('false');

		#export buttons

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
	* Get data result as json
	*/
	function getdata()
	{
		$tabla = 'view_sfpach';
		$filters = $this->input->get_post('filters');
		$mWHERE = array();

		$grid       = $this->jqdatagrid;
		
		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere($tabla);
		$response   = $grid->getData($tabla, array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
		//echo $this->db->last_query();
	}

	/**
	* Put information
	*/
	function setData()
	{
		//$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');

		$data = $_POST;
		unset($data['oper']);
		unset($data['id']);
		unset($data['cajero']);
		
		if($oper == 'add'){
			echo 'De este modulo no se puede Agregado';
			return;

		} elseif($oper == 'edit') {
			//REVISA SI DEBE GENERAR MOVIMIENTO EF
			$tipo_doc = $this->datasis->dameval("SELECT tipo_doc FROM sfpa WHERE id=$id");
			$montoo   = $this->datasis->dameval("SELECT monto FROM sfpa WHERE id=$id");
			if ($tipo_doc == 'CC') {
				$dife = 0;
			} else
				$dife =    $montoo - $data['monto'];
				
			if ( round($dife,2) <> 0 ) {
				$query = $this->db->get_where('sfpa', array('id'=>$id) );
				$row = $query->row_array();
				$row['tipo'] = 'EF';
				$row['monto'] = $dife;
				unset($row['id']);
				$this->db->insert('sfpa', $row);
				logusu('SFPA',"Cambia forma de pago: id=$id  monto=$montoo ");
			} else {
				unset($data['monto']);
			}
			$this->db->where('id', $id);
			$this->db->update('sfpa', $data);
			echo 'Registro Guardado ';
			return;

		} elseif($oper == 'del') {
			//$this->db->simple_query("DELETE FROM sfpa WHERE id=$id ");
			logusu('sfpa',"Cambio de Cheque $id ELIMINADO");
			echo "Registro Eliminado";
			return;
		}
	}

	//******************************************************************
	//
	//  Guarda los deposios pendientes
	//
	function depositos(){
		$envia   = $this->input->get_post('envia');
		$recibe  = $this->input->get_post('recibe');
		$monto   = $this->input->get_post('monto');
		$cheques = $this->input->get_post('ids');
		$fecha   = date('Ymd');
		
		// Revisamos si el monto coincide con la suma
		$mMonto = $this->datasis->dameval("SELECT SUM(monto) FROM sfpa WHERE id IN ( $cheques )");

		if ($monto <> $mMonto) memowrite("Diferencia de monto $monto <> $mMonto");

		$monto = $mMonto;

		$transac = $this->datasis->prox_sql("ntransa",8);

		$i = 0;
		while ( $i == 0){
			$numero  =$this->datasis->prox_sql("nbcaj",8);
			if ($this->datasis->dameval("SELECT count(*) FROM bcaj WHERE numero='".$numero."'") == 0 ){
				$i = 1;
			};
		}

		$numeroe = $this->datasis->banprox($envia);
		$numeror = $this->datasis->banprox('00');
		$data = array();
		
		$data['fecha']      = $fecha;
		$data['numero']     = $numero;

		$data['tipo']       = 'DE';
		$data['tarjeta']    = 0;
		$data['tdebito']    = 0;
		$data['cheques']    = $monto;
		$data['efectivo']   = 0;
		$data['comision']   = 0;
		$data['islr']       = 0;
		$data['monto']      = $monto;
		$data['envia']      = $envia;
		
		$data['bancoe']     = $this->datasis->dameval("SELECT banco FROM banc WHERE codbanc='$envia'");

		$data['tipoe']      = 'ND';
		$data['numeroe']    = $numeroe;

		$data['codbanc']    = $recibe;
		$data['recibe']     = '00';
		$data['bancor']     = 'DEPOSITO EN TRANSITO';
		$data['tipor']      = 'DE';

		$data['numeror']    = $numeror;
		$data['concepto']   = "TRANSITO DESDE CAJA $envia A BANCO $recibe ";
		$data['concep2']    = "CHEQUES";
		$data['status']     = 'P';  // Pendiente/Cerrado/Anulado
		$data['usuario']    = $this->secu->usuario();
		$data['estampa']    = $fecha;
		$data['hora']       = date('H:i:s');
		$data['transac']    = $transac;

		//Guarda en BCAJ
		$this->db->insert('bcaj', $data);
		$this->datasis->actusal( $envia, $fecha, -$monto );
		
		$mSQL = "UPDATE sfpa SET deposito='$numero', status='P' WHERE id IN ($cheques)";
		$this->db->simple_query($mSQL);
	
		//GUARDA EN BMOV LA SALIDA DE CAJA
		$data = array();
		
		$data['codbanc']  = $envia;
		$data['numcuent'] = $this->datasis->dameval("SELECT numcuent FROM banc WHERE codbanc='$envia'");
		$data['banco']    = $this->datasis->dameval("SELECT banco    FROM banc WHERE codbanc='$envia'");
		$data['saldo']    = $this->datasis->dameval("SELECT saldo    FROM banc WHERE codbanc='$envia'");
		$data['tipo_op']  = 'ND';
		$data['numero']   = $numeroe;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'CAJAS';
		$data['nombre']   = 'DEPOSITO DESDE CAJA';
		$data['monto']    = $monto;
		$data['concepto'] = "DEPOSITO DESDE CAJA $envia A BANCO $recibe ";
		$data['concep2']  = "";
		$data['benefi']   = "";
		$data['usuario']  = $this->secu->usuario();
		$data['estampa']  = $fecha;
		$data['hora']     = date('H:i:s');
		$data['transac']  = $transac;
		$this->db->insert('bmov', $data);
		
		//Actualiza saldo en caja de transito
		$this->datasis->actusal('00', $fecha, $monto);

		$data['codbanc']  = '00';
		$data['numcuent'] = $this->datasis->dameval("SELECT numcuent FROM banc WHERE codbanc='$envia'");
		$data['banco']    = $this->datasis->dameval("SELECT banco    FROM banc WHERE codbanc='$envia'");
		$data['saldo']    = $this->datasis->dameval("SELECT saldo    FROM banc WHERE codbanc='$envia'");
		$data['tipo_op']  = 'NC';
		$data['numero']   = $numeror;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'CAJAS';
		$data['nombre']   = 'DEPOSITO DESDE CAJA';
		$data['monto']    = $monto;
		$data['concepto'] = "DEPOSITO DESDE CAJA $envia A BANCO $recibe ";
		$data['concep2']  = "";
		$data['benefi']   = "";
		$data['usuario']  = $this->secu->usuario();
		$data['estampa']  = $fecha;
		$data['hora']     = date('H:i:s');
		$data['transac']  = $transac;
		$this->db->insert('bmov', $data);
		
		logusu('BCAJ',"Deposito de cheques de caja Nro. $numero creada");
		echo "{\"numero\":\"$numero\",\"mensaje\":\"Registro Agregado\"}";
	}


	//******************************************************************
	//
	//  Guarda los deposios pendientes
	//
	function nodeposito(){
		$cheques = $this->input->get_post('ids');
		$fecha   = date('Ymd');
	
		// Revisamos si el monto coincide con la suma
		
		$mSQL = "UPDATE sfpa SET deposito='', status='C' WHERE id IN ($cheques)";
		$this->db->simple_query($mSQL);
		
		logusu('BCAJ',"Cheques marcados para no Depositar");
		echo "{\"numero\":\"\",\"mensaje\":\"Cheques Marcados\"}";
	}


	//******************************************************************
	//
	//  Depositar Efectivo
	//
	function efectivo(){
		// Genera el deposito pendiente
		$envia   = $this->input->get_post('caja');
		$recibe  = $this->input->get_post('banco');
		$monto   = $this->input->get_post('monto');
		$fecha   = date('Ymd');

		$mMonto = $monto;
		
		// Validar
		$check = 0;
		
		if ( $monto <= 0 ) $check = $check + 1 ;
		
		if ( $check == 0 ){
			$transac = $this->datasis->prox_sql("ntransa",8);
			$i = 0;
			while ( $i == 0){
				$numero  =$this->datasis->prox_sql("nbcaj",8);
				if ($this->datasis->dameval("SELECT count(*) FROM bcaj WHERE numero='".$numero."'") == 0 ){
					$i = 1;
				};
			}

			$numeroe = $this->datasis->banprox($envia);
			$numeror = $this->datasis->banprox('00');
			$data = array();
		
			$data['fecha']      = $fecha;
			$data['numero']     = $numero;
	
			$data['tipo']       = 'DE';
			$data['tarjeta']    = 0;
			$data['tdebito']    = 0;
			$data['cheques']    = 0;
			$data['efectivo']   = $monto;
			$data['comision']   = 0;
			$data['islr']       = 0;
			$data['monto']      = $monto;
			$data['envia']      = $envia;
			
			$data['bancoe']     = $this->datasis->dameval("SELECT banco FROM banc WHERE codbanc='$envia'");
	
			$data['tipoe']      = 'ND';
			$data['numeroe']    = $numeroe;
	
			$data['codbanc']    = $recibe;
			$data['recibe']     = '00';
			$data['bancor']     = 'DEPOSITO EN TRANSITO';
			$data['tipor']      = 'DE';
	
			$data['numeror']    = $numeror;
			$data['concepto']   = "TRANSITO DESDE CAJA $envia A BANCO $recibe ";
			$data['concep2']    = "CHEQUES";
			$data['status']     = 'P';  // Pendiente/Cerrado/Anulado
			$data['usuario']    = $this->secu->usuario();
			$data['estampa']    = $fecha;
			$data['hora']       = date('H:i:s');
			$data['transac']    = $transac;

			//Guarda en BCAJ
			$this->db->insert('bcaj', $data);
			$this->datasis->actusal( $envia, $fecha, -$monto );
	
			//GUARDA EN BMOV LA SALIDA DE CAJA
			$data = array();
		
			$data['codbanc']  = $envia;
			$data['numcuent'] = $this->datasis->dameval("SELECT numcuent FROM banc WHERE codbanc='$envia'");
			$data['banco']    = $this->datasis->dameval("SELECT banco    FROM banc WHERE codbanc='$envia'");
			$data['saldo']    = $this->datasis->dameval("SELECT saldo    FROM banc WHERE codbanc='$envia'");
			$data['tipo_op']  = 'ND';
			$data['numero']   = $numeroe;
			$data['fecha']    = $fecha;
			$data['clipro']   = 'O';
			$data['codcp']    = 'CAJAS';
			$data['nombre']   = 'DEPOSITO DESDE CAJA';
			$data['monto']    = $monto;
			$data['concepto'] = "DEPOSITO DESDE CAJA $envia A BANCO $recibe ";
			$data['concep2']  = "";
			$data['benefi']   = "";
			$data['usuario']  = $this->secu->usuario();
			$data['estampa']  = $fecha;
			$data['hora']     = date('H:i:s');
			$data['transac']  = $transac;
			$this->db->insert('bmov', $data);
		
			//Actualiza saldo en caja de transito
			$this->datasis->actusal('00', $fecha, $monto);

			$data['codbanc']  = '00';
			$data['numcuent'] = $this->datasis->dameval("SELECT numcuent FROM banc WHERE codbanc='$envia'");
			$data['banco']    = $this->datasis->dameval("SELECT banco    FROM banc WHERE codbanc='$envia'");
			$data['saldo']    = $this->datasis->dameval("SELECT saldo    FROM banc WHERE codbanc='$envia'");
			$data['tipo_op']  = 'NC';
			$data['numero']   = $numeror;
			$data['fecha']    = $fecha;
			$data['clipro']   = 'O';
			$data['codcp']    = 'CAJAS';
			$data['nombre']   = 'DEPOSITO DESDE CAJA';
			$data['monto']    = $monto;
			$data['concepto'] = "DEPOSITO DESDE CAJA $envia A BANCO $recibe ";
			$data['concep2']  = "";
			$data['benefi']   = "";
			$data['usuario']  = $this->secu->usuario();
			$data['estampa']  = $fecha;
			$data['hora']     = date('H:i:s');
			$data['transac']  = $transac;
			$this->db->insert('bmov', $data);
	
			logusu('BCAJ',"Deposito en efectivo de caja Nro. $numero creada");

			$rt=array(
				'status'  => 'A',
				'mensaje' => 'Deposito Agregado',
				'pk'      => $transac
			);
			echo json_encode($rt);

			//echo "{\"numero\":\"$numero\",\"mensaje\":\"Registro Agregado\"}";
		} else {
			$rt=array(
				'status'  => 'B',
				'mensaje' => 'Error guardando el Deposito',
				'pk'      => $transac
			);
			echo json_encode($rt);
			
			//echo "{\"numero\":\"$numero\",\"mensaje\":\"Error \"}";
		}
	}


	function instalar(){
	
	}

}
