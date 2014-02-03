<?php
class Reparto extends Controller {
	var $mModulo = 'REPARTO';
	var $titp    = 'REPARTO AL CLIENTE';
	var $tits    = 'REPARTO AL CLIENTE';
	var $url     = 'ventas/reparto/';

	function Reparto(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'REPARTO', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->creaintramenu(array('modulo'=>'151','titulo'=>'Reparto','mensaje'=>'Reparto a Domicilio','panel'=>'DESPACHO','ejecutar'=>'ventas/reparto','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		$this->db->query('UPDATE sfac SET reparto=0 WHERE reparto IS NULL');
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){
		$grid = $this->defgrid();
		$grid->setHeight('155');
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$grid1->setHeight('160');
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 190, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'imprime', 'img'=>'assets/default/images/print.png','alt' => 'Reimprimir', 'label'=>'Reimprimir Documento'));

		$grid->wbotonadd(array('id'=>'agregaf', 'img'=>'images/databaseadd.png', 'alt'=>'Agregar Factura',      'label'=>'Agregar Facturas',     'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'cargard', 'img'=>'images/camion.png',      'alt'=>'Cargar Vehiculo',      'label'=>'Cargar Vehiculo',      'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'entrega', 'img'=>'images/acuerdo.png',     'alt'=>'Entregado al Cliente', 'label'=>'Entregado al Cliente', 'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'cerrard', 'img'=>'images/candado.png',     'alt'=>'Cerrar Despacho',      'label'=>'Cerrar Despacho',      'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'anulard', 'img'=>'images/delete.png',      'alt'=>'Anular Despacho',      'label'=>'Anular Despacho',      'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'buscafc', 'img'=>'images/delete.png',      'alt'=>'Anular Despacho',      'label'=>'Buscar Faturas',      ));

		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'fedita', 'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' , 'title'=>'Mostrar Registro'),
			array('id'=>'fborra', 'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones  = '$("#agregaf").hide();'."\n";
		$funciones .= '$("#cargard").hide();'."\n";
		$funciones .= '$("#entrega").hide();'."\n";
		$funciones .= '$("#cerrard").hide();'."\n";

		$param['funciones']    = $funciones;
		$param['WestPanel']    = $WestPanel;
		$param['script']       = script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('REPARTO', 'JQ');
		$param['otros']        = $this->datasis->otros('REPARTO', 'JQ');
		$param['centerpanel']  = $centerpanel;
		$param['temas']        = array('proteo','darkness','anexos');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		$param['tamano']       = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);

	}

	//******************************************************************
	//Funciones de los Botones
	//
	function bodyscript( $grid0, $grid1 ){
		$bodyscript = '<script type="text/javascript">';
		$ngrid = '#newapi'.$grid0;

		$bodyscript .= '
		jQuery("#imprime").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('formatos/ver/REPARTO').'/\'+id+\'/id\'').';
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		});';

		$bodyscript .= '
		$("#agregaf").click(function(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'factuforma').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog( { title:"SELECCIONAR FACTURAS", width: 600, height: 450 } );
					$("#fshow").dialog( "open" );
				});

			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		});';

		// Modificar Destino
		$bodyscript .= '
		$("#cargard").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				var mcome1 = "<h1>Fecha de Carga</h1><table align=\'center\'><tr>"+
					"<td>Fecha de Carga: </td>"+
					"<td><input id=\'mfecha\' name=\'mfecha\' size=\'10\'  value=\''.date('d/m/Y').'\'></td></tr></table>";
				var mprepanom =
				{
					state0: {
						html: mcome1,
						buttons: { Guardar: true, Cancelar: false },
						submit: function(e,v,m,f){
							mfecha = f.mfecha;
							if (v) {
								$.post("'.site_url('ventas/reparto/cambiatipo').'/", { fecha: f.mfecha, mid: id, oper: \'carga\' },
									function(data){
										$.prompt.getStateContent(\'state1\').find(\'#in_prome2\').text(data);
										$.prompt.goToState(\'state1\');
										$("#newapi'.$grid0.'").trigger("reloadGrid");
								});
								return false;
							}
						}
					},
					state1: {html: "<h1>Resultado</h1><span id=\'in_prome2\'></span>", focus:1, buttons: { Ok:true }}
				};
				$.prompt(mprepanom);
				$("#mfecha").datepicker({dateFormat:"dd/mm/yy"});

			} else { $.prompt("<h1>Por favor Seleccione un Reparto</h1>");}
		});';

		// Cierre
		$bodyscript .= '
		$("#cerrard").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = $("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				var mcome1 = "<h1>Fecha de Cierre</h1><table align=\'center\'><tr>"+
					"<td>Fecha de Cierre: </td>"+
					"<td><input id=\'mfecha\' name=\'mfecha\' size=\'10\'  value=\''.date('d/m/Y').'\'></td></tr></table>";
				var mprepanom =
				{
					state0: {
						html: mcome1,
						buttons: { Guardar: true, Cancelar: false },
						submit: function(e,v,m,f){
							mfecha = f.mfecha;
							if (v) {
								$.post("'.site_url('ventas/reparto/cambiatipo').'/", { fecha: f.mfecha, mid: id, oper: \'cierre\' },
									function(data){
										$.prompt.getStateContent(\'state1\').find(\'#in_prome3\').text(data);
										$.prompt.goToState(\'state1\');
										$("#newapi'.$grid0.'").trigger("reloadGrid");
								});
								return false;
							}
						}
					},
					state1: {html: "<h1>Resultado</h1><span id=\'in_prome3\'></span>", focus:1, buttons: { Ok:true }}
				};
				$.prompt(mprepanom);
				$("#mfecha").datepicker({dateFormat:"dd/mm/yy"});

			} else { $.prompt("<h1>Por favor Seleccione un Reparto</h1>");}
		});';


		// Cierre
		$bodyscript .= '
		$("#anulard").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = $("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				var mcome1 = "<h1>Eliminar Reparto?</h1><table align=\'center\'><tr>"+
					"<td>Eliminar Reparto y desmarcar las facturas?</td>"+
					"</tr></table>";
				var mprepanom =
				{
					state0: {
						html: mcome1,
						buttons: { Eliminar: true, Cancelar: false },
						submit: function(e,v,m,f){
							if (v) {
								$.post("'.site_url('ventas/reparto/cambiatipo').'/", { mid: id, oper: \'anular\' },
									function(data){
										$.prompt.getStateContent(\'state1\').find(\'#in_prome3\').text(data);
										$.prompt.goToState(\'state1\');
										$("#newapi'.$grid0.'").trigger("reloadGrid");
								});
								return false;
							}
						}
					},
					state1: {html: "<h1>Resultado</h1><span id=\'in_prome3\'></span>", focus:1, buttons: { Ok:true }}
				};
				$.prompt(mprepanom);
				$("#mfecha").datepicker({dateFormat:"dd/mm/yy"});

			} else { $.prompt("<h1>Por favor Seleccione un Reparto</h1>");}
		});';


		// Entrega
		$bodyscript .= '
		$("#entrega").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = $("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				var mcome1 = "<h1>Fecha de Entrega</h1><table align=\'center\'><tr>"+
					"<td>Fecha de Entrega: </td>"+
					"<td><input id=\'mfecha\' name=\'mfecha\' size=\'10\'  value=\''.date('d/m/Y').'\'></td></tr></table>";
				var mprepanom =
				{
					state0: {
						html: mcome1,
						buttons: { Guardar: true, Cancelar: false },
						submit: function(e,v,m,f){
							mfecha = f.mfecha;
							if (v) {
								$.post("'.site_url('ventas/reparto/cambiatipo').'/", { fecha: f.mfecha, mid: id, oper: \'entrega\' },
									function(data){
										$.prompt.getStateContent(\'state1\').find(\'#in_prome3\').text(data);
										$.prompt.goToState(\'state1\');
										$("#newapi'.$grid0.'").trigger("reloadGrid");
								});
								return false;
							}
						}
					},
					state1: {html: "<h1>Resultado</h1><span id=\'in_prome3\'></span>", focus:1, buttons: { Ok:true }}
				};
				$.prompt(mprepanom);
				$("#mfecha").datepicker({dateFormat:"dd/mm/yy"});

			} else { $.prompt("<h1>Por favor Seleccione un Reparto</h1>");}
		});';

		// Agregar
		$bodyscript .= $this->jqdatagrid->bsadd( 'reparto', $this->url );  //Por Defecto

		//Editar
		//$bodyscript .= $this->jqdatagrid->bsedit( 'reparto', $ngrid ,$this->url );  //Por Defecto

		$bodyscript .= '
		function repartoedit(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				if ( ret.tipo == "P"){
					mId = id;
					$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
						$("#fedita").html(data);
						$("#fedita").dialog( "open" );
					});
				} else {
					$.promp("<h1>No puede modificar un Reparto ya Cargado</h1>");
				}
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';


		// Mostrar
		$bodyscript .= $this->jqdatagrid->bsshow( 'reparto', $ngrid, $this->url );  //Por Defecto

		// Borrar
		$bodyscript .= $this->jqdatagrid->bsdel( 'reparto', $ngrid, $this->url );  //Por Defecto

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);  //Por Defecto

		// Dialogo fedita
		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, "300", "550" );  //Por Defecto

		// Dialogo fshow
		$bodyscript .= $this->jqdatagrid->bsfshow( "250", "500" );  //Por Defecto

		// Dialogo fborra
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, "300", "400" );  //Por Defecto

		$bodyscript .= '});';

		$bodyscript .= '</script>';

		return $bodyscript;
	}

	//******************************************************************
	// Cambia Tipo de Reparto
	//
	function cambiatipo(){
		$id    = $this->input->post('mid');
		$fecha = human_to_dbdate($this->input->post('fecha'));
		$oper  = $this->input->post('oper');
		if ( $oper == 'carga' ){
			$tipo = $this->datasis->dameval("SELECT tipo FROM reparto WHERE id=$id");
			if ($tipo == 'P') {
				$this->db->where("id", $id);
				$this->db->update('reparto', array( "tipo" => 'C', "carga" => $fecha ) );
				echo "Guardada ";
			} else
				echo "No esta Pendiente";

		} elseif ( $oper == 'entrega' ){
			$tipo = $this->datasis->dameval("SELECT tipo FROM reparto WHERE id=$id");
			if ($tipo == 'C') {
				$this->db->where("id", $id);
				$this->db->update('reparto', array( "tipo" => 'E', "entregado" => $fecha ) );
				echo "Guardada ";
			} else
				echo "No esta Cargada";

		} elseif ( $oper == 'cierre' ){
			$tipo = $this->datasis->dameval("SELECT tipo FROM reparto WHERE id=$id");
			if ($tipo == 'E') {
				$this->db->where("id", $id);
				$this->db->update('reparto', array( "tipo" => 'F', "retorno" => $fecha ) );
				$entrega = $this->datasis->dameval("SELECT entregado FROM reparto WHERE id=$id");
				$this->db->query("UPDATE sfac SET entregado='".$entrega."' WHERE reparto=$id");
				echo "Guardada ";
			} else
				echo "No esta Entregada";

		} elseif ( $oper == 'anular' ){
			$tipo = $this->datasis->dameval("SELECT tipo FROM reparto WHERE id=$id");
			if ($tipo != 'F') {
				$this->db->where("id", $id);
				$this->db->update('reparto', array( "tipo" => 'A' ) );
				$this->db->query("UPDATE sfac SET entregado='0', reparto=0 WHERE reparto=$id");
				echo "Reparto Eliminado ";
			} else
				echo "Reparto ya finaliado no se puede anular";
		}
	}


	//******************************************************************
	// Formato de la ventana
	//
	function factuforma( $id = 0 ){
		$msalida = '<script type="text/javascript">'."\n";
		$msalida .= 'var mid='.$id.";\n";

		$msalida .= '
		$("#bpos1").jqGrid({
			ajaxGridOptions: { type: "POST"},
			jsonReader: { root: "data", repeatitems: false},
			ondblClickRow: pasa,
			url:\''.site_url($this->url.'facturas').'/\'+mid,
			editurl: \''.site_url($this->url.'facturasw').'/\'+mid,
			datatype: "json",
			rowNum:12,

			height: 280,
			pager: \'#pbpos1\',
			rowList:[],
			toolbar: [false],

			width:  400,
			hiddengrid: false,
			postdata: { tboficiid: "wapi"},
			colNames:[\'id\', \'Numero\',\'Fecha\', \'Cliente\', \'Zona\', \'Rep\', \'Peso\'],
			colModel:[
				{name:\'id\',      index:\'id\',      width: 10, hidden:true},
				{name:\'numero\',  index:\'numero\',  width: 35, editable:false, search: true},
				{name:\'fecha\',   index:\'fecha\',   width: 35, editable:false, search: true, align:\'center\',edittype:\'text\', editoptions: {size: 10, maxlengh: 10, dataInit: function(element) { $(element).datepicker({dateFormat: \'yy-mm-dd\',changeMonth: true,changeYear: true,yearRange: \'1983:2023\'})}, defaultValue:\'2013-05-01\'}, searchoptions: {size: 10, maxlengh: 10, dataInit: function(element) { $(element).datepicker({dateFormat: \'yy-mm-dd\',changeMonth: true,changeYear: true,yearRange: \'1983:2023\'})}}},
				{name:\'cod_cli\', index:\'cod_cli\', width: 20, editable:false, search: true },
				{name:\'zona\',    index:\'zona\',    width: 20, editable:false, search: true, align:\'center\' },
				{name:\'reparto\', index:\'reparto\', width: 20, editable:false, search: true, formatter: fsele },
				{name:\'peso\',    index:\'peso\',    width: 40, editable:false, search: true, editoptions: {size:10,maxlength:10,dataInit:function(elem){$(elem).numeric();}},formatter:\'number\',formatoptions:{decimalSeparator:".",thousandsSeparator:",",decimalPlaces:2}, align:\'right\' }
			],
		});
		$("#bpos1").jqGrid(\'navGrid\',"#pbpos1",{edit:false, add:false, del:false, search: true });
		$("#bpos1").jqGrid(\'filterToolbar\');

		function fsele(el, val, opts){
			var meco=\'<div><img src="'.base_url().'images/circuloverde.png" border="0" /></div>\';
			if ( el == "0" ){
				meco=\'<div>&nbsp;</div>\';
			}
			return meco;
		}


		function pasa(){
			var id = $("#bpos1").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				$.post("'.site_url($this->url.'agregaf').'/"+mid+"/"+id, function(data){
					var json = JSON.parse(data);
					$("#totpeso").html(json.peso);
					$("#totcana").html(json.cana);
					$("#bpos1").trigger("reloadGrid");
				});

			}
		}
		';

		$peso = $this->datasis->dameval("SELECT SUM(peso) FROM sfac WHERE peso IS NOT NULL AND reparto=$id");
		if( !$peso ) $peso = "0.00";

		$cana = $this->datasis->dameval("SELECT COUNT(*) FROM sfac WHERE peso IS NOT NULL AND reparto=$id");
		if( !$cana ) $cana = "0.00";

		$reg  = $this->datasis->damereg("SELECT b.descrip, b.capacidad, b.placa FROM reparto a JOIN flota b ON a.vehiculo=b.codigo WHERE a.id=$id");
		$msalida .= "\n</script>\n";

		$msalida .= "<table width='100%'><tr><td>
		<div class=\"tema1\"><table id=\"bpos1\"></table></div>
		<div id='pbpos1'></div>\n
		</td><td align='center' valign='top'>
		<p style='background:#ABE278;font-size:10pt;text-align:left;'>Para agregar o quitar facturas haga doble click sobre las mismas</p>\n
		<table width='100%' align='center'>
			<tr>
				<td bgcolor='#DFDFDF'>VEHICULO</td>
			</tr>
				<td style='font-size:10pt;font-weight:bold;'>".$reg['descrip']." ".$reg['placa']."</td>
			</tr>
				<td bgcolor='#DFDFDF'>CAPACIDAD Kg.</td>
			</tr>
				<tr><td align='center' style='font-size:14pt;font-weight:bold;'>".$reg['capacidad']."</td>
			</tr>
		</table>
		<br><br>
		<table width='100%' align='center'>
			<tr>
				<td bgcolor='#DFDFDF'>TOTAL SELECCION</td>
			</tr><tr>
				<td align='center' style='font-size:14pt;font-weight:bold;'><div id='totpeso'>".$peso."</div></td>
			</tr>
		</table>
		<br><br>
		<table width='100%' align='center'>
			<tr>
				<td bgcolor='#DFDFDF'>TOTAL FACTURAS</td>
			</tr><tr>
				<td align='center' style='font-size:14pt;font-weight:bold;'><div id='totcana'>".$cana."</div></td>
			</tr>
		</table>
		</td></tr>
		</table>\n";

		echo $msalida;

	}

	//******************************************************************
	// Agrega Factura
	//
	function agregaf($reparto, $factura){
		$dbfactura= $this->db->escape($factura);
		$dbreparto= $this->db->escape($reparto);
		$actual   = $this->datasis->dameval("SELECT reparto FROM sfac WHERE id=${dbfactura}");
		if ( $actual == 0 ) {
			$mSQL = "UPDATE sfac SET reparto=${dbreparto} WHERE id=${dbfactura}";
			$this->db->query($mSQL);
			$msj = 'Factura Agregada';
		} else {
			$mSQL = "UPDATE sfac SET reparto=0 WHERE id=${dbfactura}";
			$this->db->query($mSQL);
			$msj = 'Factura Desmarcada';
		}
		$row = $this->datasis->damereg("SELECT SUM(peso) peso, COUNT(*) cana FROM sfac WHERE peso IS NOT NULL AND reparto=${dbreparto}");

		$peso = $row['peso'];
		$cana = $row['cana'];

		$this->db->where('id',$reparto);
		$this->db->update('reparto',array('peso'=>$row['peso'], 'facturas'=>$row['cana']) );

		echo '{ "mensaje": "'.$msj.'", "peso": "'.$peso.'", "cantidad": "'.$cana.'"  }';

	}

	//******************************************************************
	// Factura
	//
	function facturas( $id = 0 ){
		$this->load->library('jqdatagrid');
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('sfac');
		$mWHERE[] = array('', 'reparto', array($id,'0'), '' );
		$mWHERE[] = array('', 'tipo_doc', 'F', '' );

		$response   = $grid->getData('sfac', array(array()), array('id', 'numero','fecha', 'cod_cli', 'zona', 'reparto', 'peso'), false, $mWHERE, 'id','desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}


	//******************************************************************
	// Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 80,
			'editable'      => 'false',
			'search'        => 'false'
		));

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

		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha",size:12 }'
		));

		$grid->addField('retorno');
		$grid->label('Retorno');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha",size:12 }'
		));

		$grid->addField('chofer');
		$grid->label('Chofer');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));

		$grid->addField('vehiculo');
		$grid->label('Veh&iacute;culo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));

		$grid->addField('peso');
		$grid->label('Peso');
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

		$grid->addField('facturas');
		$grid->label('Facturas');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
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

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
				if (id){
					var ret    = $(gridId1).getRowData(id);
					jQuery(gridId2).jqGrid("setGridParam",{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
					if ( ret.tipo == \'P\' ) {
						$("#agregaf").show();
						$("#cargard").show();
						$("#entrega").hide();
						$("#cerrard").hide();
					} else if ( ret.tipo == \'C\' ) {
						$("#agregaf").hide();
						$("#cargard").hide();
						$("#entrega").show();
						$("#cerrard").hide();
					} else if ( ret.tipo == \'E\' ) {
						$("#agregaf").hide();
						$("#cargard").hide();
						$("#entrega").hide();
						$("#cerrard").show();
					} else if ( ret.tipo == \'F\' || ret.tipo == \'A\' ) {
						$("#agregaf").hide();
						$("#cargard").hide();
						$("#entrega").hide();
						$("#cerrard").hide();
						$("#anulard").hide();
					}
				}
			}'
		);
		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('REPARTO','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('REPARTO','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('REPARTO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('REPARTO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: repartoadd, editfunc: repartoedit, delfunc: repartodel, viewfunc: repartoshow');

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
		$mWHERE = $grid->geneTopWhere('reparto');

		$response   = $grid->getData('reparto', array(array()), array(), false, $mWHERE, 'id','desc' );
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
		$mcodp  = '??????';
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM reparto WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('reparto', $data);
					echo "Registro Agregado";

					logusu('REPARTO',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM reparto WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM reparto WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE reparto SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("reparto", $data);
				logusu('REPARTO',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('reparto', $data);
				logusu('REPARTO',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM reparto WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM reparto WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM reparto WHERE id=$id ");
				logusu('REPARTO',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	//Definicion del Grid y la Forma
	//
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('tipo_doc');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'align'         => "'center'",
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));

		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
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

		$grid->addField('zona');
		$grid->label('Zona');
		$grid->params(array(
			'search'        => 'true',
			'align'         => "'center'",
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));

		$grid->addField('peso');
		$grid->label('Peso');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 70,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('totalg');
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

		$grid->addField('cod_cli');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
			'align'         => "'center'",
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
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

		$grid->addField('vd');
		$grid->label('Vendedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));

		$grid->addField('almacen');
		$grid->label('Almac&eacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
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

		$grid->setOndblClickRow('
			,ondblClickRow: function(id){
				var mid = jQuery(gridId1).jqGrid(\'getGridParam\',\'selrow\');
				if(mid){           // ID del Reparto
					var id = jQuery(gridId2).jqGrid(\'getGridParam\',\'selrow\');
					if (id){       // ID de la factura
						var ret = $(gridId2).getRowData(id);
						if ( ret.tipo == "E"){
							$.prompt(\'Eliminar Factura del reparto?\', {
							buttons: { \'Quitar\': true, \'Cancelar\': false },
							submit: function(e,v,m,f){
								$.post("'.site_url($this->url.'quita').'/"+id);
								$(gridId2).trigger("reloadGrid");
							}})
						} else {
							$.prompt(\'<h2>Solo se pueden Eliminar repartos entregados</h2h2>\');
						}
					}
				} else {
					$.prompt("Seleccione un Reparto");
				}
			}
		');


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


	function quita($id){
		$dbid=$this->db->escape($id);
		$reparto  = $this->datasis->dameval("SELECT reparto FROM sfac    WHERE id=${dbid}");
		$dbreparto= $this->db->escape($reparto);
		$tipo     = $this->datasis->dameval("SELECT tipo    FROM reparto WHERE id=${dbreparto}");
		if($tipo == 'E'){
			$this->db->where('id',$id);
			$this->db->update('sfac',array( 'reparto' => 0 ));
			$this->db->query("UPDATE reparto SET eliminadas=CONCAT_WS('',eliminadas,".$reparto.") WHERE id=${dbreparto}");

			$row = $this->datasis->damereg("SELECT SUM(peso) peso, COUNT(*) cana FROM sfac WHERE peso IS NOT NULL AND reparto=${dbreparto}");
			$peso = $row['peso'];

			$this->db->where('id',$reparto);
			$this->db->update('reparto',array('peso'=>$row['peso'], 'facturas'=>$row['cana']) );

			echo 'Factura retirada';

		}
	}

	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function getdatait( $id = 0 ){
		if ($id === 0 ){
			$id = $this->datasis->dameval("SELECT MAX(id) FROM reparto");
		}
		if(empty($id)) return '';
		$dbid = $this->db->escape($id);
		//$numero   = $this->datasis->dameval("SELECT id FROM reparto WHERE id=$id");
		$grid     = $this->jqdatagrid;
		$mSQL     = "SELECT tipo_doc, numero, fecha, zona, peso, cod_cli, nombre, vd, totalg, almacen, id FROM sfac WHERE reparto=${dbid}";
		$response = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setDatait(){

	}

	//******************************************************************
	// DataEdit
	//
	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$("#retorno").datepicker({dateFormat:"dd/mm/yy"});
		});
		';

		$edit = new DataEdit('', 'reparto');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;
		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );

/*
		$edit->tipo = new dropdownField('Tipo', 'tipo');
		$edit->tipo->option('P','Pendiente');
		$edit->tipo->option('C','Cargado');
		$edit->tipo->option('D','Despachado');
		$edit->tipo->option('F','Finalizado');
		$edit->tipo->option('A','Anulado');
		$edit->tipo->style = 'width:100px;';
		$edit->tipo->rule='required';
*/

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->insertValue = date('Ymd');
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;
		$edit->fecha->calendar=false;

/*
		$edit->retorno = new dateonlyField('Retorno','retorno');
		$edit->retorno->rule='chfecha';
		$edit->retorno->size =10;
		$edit->retorno->maxlength =8;
		$edit->retorno->calendar=false;
*/

		$edit->chofer = new dropdownField('Chofer','chofer');
		$edit->chofer->option('','Seleccionar');
		$edit->chofer->options("SELECT codigo, nombre nombre FROM chofer ORDER BY nombre");
		$edit->chofer->rule='required';
		$edit->chofer->style = 'width:300px;';

		$edit->vehiculo = new dropdownField('Veh&iacute;culo','vehiculo');
		$edit->vehiculo->option('','Seleccionar');
		$edit->vehiculo->options("SELECT codigo, CONCAT_WS(' ',codigo, descrip, capacidad) FROM flota ORDER BY descrip");
		$edit->vehiculo->rule='required';
		$edit->vehiculo->style = 'width:300px;';

		$edit->observa = new textareaField('Observaci&oacute;n','observa');
		$edit->observa->rule='';
		$edit->observa->cols = 40;
		$edit->observa->rows = 4;
/*
		$edit->peso = new inputField('Peso','peso');
		$edit->peso->rule='numeric';
		$edit->peso->css_class='inputnum';
		$edit->peso->size =12;
		$edit->peso->maxlength =10;

		$edit->facturas = new inputField('Facturas','facturas');
		$edit->facturas->rule='integer';
		$edit->facturas->css_class='inputonlynum';
		$edit->facturas->size =13;
		$edit->facturas->maxlength =11;
*/
		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->hora    = new autoUpdateField('hora',   date('H:i:s'), date('H:i:s'));

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
		// Coloca por defecto el tipo
		$do->set('tipo','P');
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
		logusu($do->table,"Creo $this->tits ${primary}");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits ${primary}");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits ${primary}");
	}

	function instalar(){
		if (!$this->db->table_exists('reparto')){
			$mSQL="CREATE TABLE `reparto` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `tipo` char(1) NOT NULL COMMENT 'Tipo Pendiente, Cargado, Despachado, Finalizado, Anulado',
			  `fecha` date DEFAULT NULL COMMENT 'Fecha de Despacho',
			  `retorno` date DEFAULT NULL COMMENT 'Fecha que regresa',
			  `chofer` char(5) DEFAULT NULL COMMENT 'Chofer tabla chofer',
			  `vehiculo` char(10) DEFAULT NULL COMMENT 'Vehiculo => flota',
			  `observa` text,
			  `peso` decimal(10,2) DEFAULT NULL COMMENT 'Peso total',
			  `facturas` int(11) DEFAULT NULL COMMENT 'Nro de Faturas',
			  `estampa` date DEFAULT NULL,
			  `usuario` char(12) DEFAULT NULL,
			  `hora` char(8) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('sfac');
		if(!in_array('reparto',$campos)){
			$mSQL="ALTER TABLE `sfac` ADD COLUMN `reparto` INT(11) NULL DEFAULT '0' AFTER `manual`";
			$this->db->simple_query($mSQL);
		}
	}
}
