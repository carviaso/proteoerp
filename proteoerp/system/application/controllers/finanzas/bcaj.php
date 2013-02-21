<?php
class Bcaj extends Controller {
	var $titp='Depositos Transferencias y Remesas';
	var $tits='Depositos Transferencias y Remesas';
	var $url ='finanzas/bcaj/';

	function bcaj(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->helper('file');
		$this->load->library('jqdatagrid');
		if ( read_file('./system/application/config/datasis.php')) {
			$this->config->load('datasis');
		} else {
			$this->config->set_item('cajas', array('cobranzas'=>'99','efectivo'=>'99', 'valores'=>'99', 'tarjetas'=>'99', 'gastos'=>'99'));
		}
		$this->guitipo=array('DE'=>'Deposito','TR'=>'Transferencia');
		//$this->datasis->modulo_id('51D',1);
		$this->bcajnumero='';
	}

	function index(){
		if ( !$this->datasis->iscampo('bcaj','id') ) {
			$this->db->simple_query('ALTER TABLE bcaj DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE bcaj ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE bcaj ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		redirect($this->url.'jqdatag');
	}


	//******************************************************************
	//Layout en la Ventana
	//
	function jqdatag(){
		if (!$this->datasis->sidapuede('BCAJ', 'TODOS')) {
			redirect('/bienvenido/noautorizado');
		}

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 200, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );


		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		$WestPanel = '
<div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content">
<div class="otros">

<table id="west-grid" align="center">
	<tr><td>
		<div class="anexos"><table id="listados"></table></div></td>
	</tr><tr>
		<td><table id="otros"></table></td>
	</tr>
</table>

<table id="west-grid" align="center">
	<tr>
		<td><div class="tema1"><a style="width:90px" href="#" id="impPdf">Imprimir '.img(array('src' => 'images/pdf_logo.gif', 'alt' => 'Formato PDF',  'title' => 'Formato PDF', 'border'=>'0')).'</a></div></td>
		<td><div class="tema1"><a style="width:90px" href="#" id="impHtml">Ver en '.img(array('src' => 'images/html_icon.gif', 'alt' => 'Formato HTML',  'title' => 'Formato HTML', 'border'=>'0')).'</a></div></td>
	</tr><tr>
		<td colspan="2"><div class="tema1"><a style="width:190px" href="#" id="cerrardpt">Cerrar Deposito '.img(array('src' => 'images/candado.jpg', 'alt' => 'Eliminar',  'title' => 'Eliminar', 'border'=>'0')).'</a></div></td>
	</tr><tr>
		<td colspan="2">&nbsp;</td>
	</tr><tr>
		<td colspan="2">&nbsp;</td>
	</tr><tr>
		<td colspan="2"><div class="tema1"><a style="width:190px" href="#" id="borrar">Eliminar Movimiento '.img(array('src' => 'images/delete.jpg', 'alt' => 'Eliminar',  'title' => 'Eliminar', 'border'=>'0')).'</a></div></td>
	</tr><tr>
		<td colspan="2">&nbsp;</td>
	</tr><tr>
		<td colspan="2">&nbsp;</td>
	</tr><tr>
		<td colspan="2">&nbsp;</td>
	</tr><tr>
		<td colspan="2">&nbsp;</td>
	</tr><tr>
		<td colspan="2">&nbsp;</td>
	</tr><tr>
		<td colspan="2">&nbsp;</td>
	</tr><tr>
		<td colspan="2">&nbsp;</td>
	</tr><tr>
		<td colspan="2">&nbsp;</td>
	</tr><tr>
		<td colspan="2">&nbsp;</td>
	</tr><tr>
		<td colspan="2">&nbsp;<br><br></td>
	</tr><tr>
		<td colspan="2"><div class="tema1"><a style="width:190px" href="#" id="chdevo">Cheque Devuelto '.img(array('src' => 'images/delete.jpg', 'alt' => 'Cheque devuelto',  'title' => 'Cheque devuelto', 'border'=>'0')).'</a></div></td>
	</tr>
</table>
</div>
</div> <!-- #LeftPane -->
';


		//Panel Central
		$centerpanel = $grid->centerpanel( $id = "radicional", $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
		array("id"=>"forma1",  "title"=>"Agregar/Editar Registro")
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);


		$funciones = '';

		$param['WestPanel']    = $WestPanel;
		$param['readyLayout']  = $readyLayout;

		//$param['EastPanel']  = $EastPanel;
		$param['listados']     = $this->datasis->listados('BCAJ', 'JQ');
		$param['otros']        = $this->datasis->otros('BCAJ', 'JQ');
		//$param['funciones']  = $funciones;

		$param['centerpanel']  = $centerpanel;
		$param['SouthPanel']   = $SouthPanel;
		$param['temas']        = array('proteo','darkness','anexos1');
		
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		
		$this->load->view('jqgrid/crud2',$param);
	}


	//******************************************************************
	//Funciones de los Botones
	//******************************************************************
	function bodyscript( $grid0, $grid1 ){
		$bodyscript = '<script type="text/javascript">';

		//Imprime a PDF
		$bodyscript .= '
		jQuery("#impPdf").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.base_url().'formatos/ver/BANCAJA/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});
		';

		//Imprime a HTML
		$bodyscript .= '
		jQuery("#impHtml").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.base_url().'formatos/verhtml/BANCAJA/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});
		';

		//Imprime a HTML
		$bodyscript .= '
		jQuery("#chdevo").click( function(){
			var id = jQuery("#newapi'.$grid1.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid1.'").jqGrid(\'getRowData\',id);
				$.prompt(
					"<h1>Cheque Devuelto </h1><table width=\"100%\"><tr><td>Numero: "+ret.num_ref+"</td><td>Fecha: "+ret.fecha+"</td></tr><tr><td>Banco: "+ret.banco+"</td><td>Monto: "+ret.monto+"</td></tr></table>",
					{
						buttons: { "Devolver":true, "Cancelar": false }, focus: 1,
						callback: function(e,v,m,f){
							if (v == true) {
								$.get("'.base_url().'finanzas/prmo/prmochdev/"+id,
								function(data){ alert(data); });
							}
						}
					}
				);
			} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});
		';
/*
		//Imprime a HTML
		$bodyscript .= '
		function probar( o, n ) {
			if ( o.val().length < 1 ) {
				o.addClass( "ui-state-error" );
				updateTips( "Seleccion un " + n + "." );
				return false;
			} else {
				return true;
			}
		};
		';
*/

		//Imprime a HTML
		$bodyscript .= '
		$(function() {
			$("#dialog:ui-dialog").dialog( "destroy" );
			var mId = 0;
			var montotal = 0;
			var fnumero = $("#fnumero");
			var ffecha = $("#ffecha");
			var grid = jQuery("#newapi'.$grid0.'");
			var s;
			var allFields = $( [] ).add( fnumero ).add( ffecha );

			var tips = $( ".validateTips" );

			s = grid.getGridParam(\'selarrrow\'); 
			$( "input:submit, a, button", ".otros" ).button();

			$("#cerrardpt").click(function() {
				var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
				if (id)	{
					var ret    = $("#newapi'. $grid0.'").getRowData(id);  
					mId = id;
					$.post("'.base_url().'finanzas/bcaj/formacierre/"+id, function(data){
						$("#forma1").html(data);
					});
					if ( ret["status"] == "P" ){
						$( "#forma1" ).dialog( "open" );
					} else {
						$.prompt("<h1>Movimiento no esta Pendiente</h1>");
					}
				} else { $.prompt("<h1>Por favor Seleccione un Deposito</h1>");}
			});

			$("#borrar").click(function() {
				var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
				var m = "";
				if (id)	{
					$.prompt( "Eliminar Registro? ",{
							buttons: { Borrar:true, Cancelar:false},
							callback: function(e,v,m,f){
								if (v == true) {
									$.get("'.base_url().$this->url.'bcajborra/"+id,
									function(data){
										alert(data);
									});
								}
							}
						}
					);
				} else { $.prompt("<h2>Por favor Seleccione un Movimiento</h2>");}
			});

			var fnombre = $("#name" ),
				email = $( "#email" ),
				password = $( "#password" ),
				allFields = $( [] ).add( name ).add( email ).add( password ),
				tips = $( ".validateTips" );

			$( "#forma1").dialog({
				autoOpen: false,
				height: 470,
				width: 550,
				modal: true,
				buttons: {
					"Cerrar Deposito": function() {
						var bValid = true;
						allFields.removeClass( "ui-state-error" );
						if ( bValid ) {
							$.ajax({
								type: "POST",
								dataType: "html",
								url:"'.site_url("finanzas/bcaj/cerrardpt").'",
								async: false,
								data: $("#cierreforma").serialize(),
								success: function(r,s,x){
									var res = $.parseJSON(r);
									if ( res.status == "E"){
										alert("Error: "+res.mensaje);
									} else {
										alert(res.mensaje);
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
				},
				close: function() {
					allFields.val( "" ).removeClass( "ui-state-error" );
				}
			});
		});
		';


		$bodyscript .= "\n</script>\n";
		return $bodyscript;
	}



	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 60,
			'editable' => 'false',
			'search'   => 'false'
		));

		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"DE":"Deposito","TR":"Transferencia","RM":"Remesas" }, readonly: "readonly" }',
			'formoptions'   => '{ rowpos:"1", colpos: "1" }'
		));

		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true }',
			'formoptions'   => '{ label:"Fecha" }',
			//'formatoptions' => '{formatter:\'date\'}',
			'formatter'     => "'date'",
		));

		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 70,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:"10", readonly: "readonly" }',
			'formoptions'   => '{ rowpos:"1", colpos: "2" }'

		));

		$grid->addField('status');
		$grid->label('Status');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
			'editoptions' => '{ readonly: "readonly" }'
		));

		$grid->addField('monto');
		$grid->label('Total');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, readonly: "readonly" }',
			'formatter'     => "'number'",
			'formatoptions' => '{ decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }',
			'formoptions'   => '{ rowpos:"6", colpos: "2" }'
		));

		$grid->addField('envia');
		$grid->label('Envia');
		$grid->params(array(
			'align'    => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));

		$grid->addField('bancoe');
		$grid->label('Banco/Caja Envia');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
		));


		$grid->addField('tipoe');
		$grid->label('Tipo');
		$grid->params(array(
			'align'    => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('numeroe');
		$grid->label('Numero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
		));


		$grid->addField('recibe');
		$grid->label('Recibe');
		$grid->params(array(
			'align'    => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('bancor');
		$grid->label('Banco/Caja Recibe');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 140,
			'edittype'      => "'text'",
		));

		$grid->addField('tipor');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
		));

		$grid->addField('numeror');
		$grid->label('Numero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 100,
			'edittype'      => "'text'",
		));

		$grid->addField('tarjeta');
		$grid->label('Monto T.C.');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'true',
			'align'         => "'right'",
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, readonly: "readonly" }',
			'formatter'     => "'number'",
			'formatoptions' => '{ decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }',
			'formoptions'   => '{ rowpos:"2", colpos: "1" }'
		));

		$grid->addField('tdebito');
		$grid->label('Monto T.D.');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, readonly: "readonly" }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }',
			'formoptions'   => '{ rowpos:"2", colpos: "2" }'
		));

		$grid->addField('cheques');
		$grid->label('Monto CH');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10 }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }',
			'formoptions'   => '{ rowpos:"3", colpos: "1" }'
		));

		$grid->addField('efectivo');
		$grid->label('Efectivo');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10 }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }',
			'formoptions'   => '{ rowpos:"3", colpos: "2" }'
		));

		$grid->addField('comision');
		$grid->label('Comision');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 90,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, readonly: "readonly" }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }',
			'formoptions'   => '{ rowpos:"4", colpos: "1" }'
		));

		$grid->addField('islr');
		$grid->label('ISLR');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 90,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, readonly: "readonly" }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }',
			'formoptions'   => '{ rowpos:"4", colpos: "2" }'
		));

		$grid->addField('concepto');
		$grid->label('Concepto 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 140,
			'edittype'      => "'text'",
		));

		$grid->addField('concep2');
		$grid->label('Concepto 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 140,
			'edittype'      => "'text'",
		));

		$grid->addField('benefi');
		$grid->label('Beneficiario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 140,
			'edittype'      => "'text'",
		));

		$grid->addField('Boleta');
		$grid->label('boleta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'edittype'      => "'text'",
		));

		$grid->addField('precinto');
		$grid->label('Precinto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'edittype'      => "'text'",
		));

		$grid->addField('comprob');
		$grid->label('Comprob');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 60,
			'edittype'      => "'text'",
		));

		$grid->addField('totcant');
		$grid->label('Cantidad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 60,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10 }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('transac');
		$grid->label('Transac');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'edittype'      => "'text'",
		));

		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 60,
			'edittype'      => "'text'",
		));

		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'false',
			'align'         => "'center'",
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
		));

		$grid->addField('deldia');
		$grid->label('Dia Cierre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('modificado');
		$grid->label('modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('190');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');
		$grid->setOndblClickRow('');


		$grid->setOnSelectRow(' function(id){
				if (id){
					var ret = $("#titulos").getRowData(id);
					jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
				}
			}'
		);

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 500, height:250, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 500, height:250, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) {var res = $.parseJSON(a.responseText);
					$.prompt(res.mensaje,{
						submit: function(e,v,m,f){
							window.open(\''.base_url().'formatos/ver/BCAJ/\'+res.id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
					}
					});
					return [true, a ];}}
					');

		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(false);
		$grid->setDelete(true);
		$grid->setSearch(true);
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
		//$filters = $this->input->get_post('filters');
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('bcaj');

		$response   = $grid->getData('bcaj', array(array()), array(), false, $mWHERE, 'id', 'desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
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

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				//$this->db->insert('caub', $data);
			}
			echo "No se puede agregar";

		} elseif($oper == 'edit') {
			if ( $data['islr']+$data['comision'] == 0 ){
				$data['monto'] = $data['cheques']+$data['efectivo']+$data['tarjeta']+$data['tdebito']-$data['comision']-$data['islr'];
				unset($data['comision']);
				unset($data['islr']);
				unset($data['numero']);
				unset($data['tarjeta']);
				unset($data['tdebito']);
				unset($data['tipo']);
				$this->db->where('id', $id);
				$this->db->update('bcaj', $data);

				$transac = $this->datasis->dameval("SELECT transac FROM bcaj WHERE id=$id ");
				$monto   = $this->datasis->dameval("SELECT monto   FROM bcaj WHERE id=$id ");
				$envia   = $this->datasis->dameval("SELECT envia   FROM bcaj WHERE id=$id ");
				$recibe  = $this->datasis->dameval("SELECT recibe  FROM bcaj WHERE id=$id ");
				$fecha   = $this->datasis->dameval("SELECT fecha   FROM bcaj WHERE id=$id ");
				$this->datasis->actusal($envia,  $fecha,  $monto);
				$this->datasis->actusal($recibe, $fecha, -$monto);
				
				$this->datasis->actusal($envia,  $fecha, -$data['monto']);
				$this->datasis->actusal($recibe, $fecha,  $data['monto']);

				$mSQL = "UPDATE bmov SET monto=".$data['monto']." WHERE transac='".$transac."'";
				$this->db->simple_query($mSQL);
				echo 'Registro Modificado';
			} else {
				echo 'No se puede modificar si tiene comision o ISLR';
			}
			

		} elseif($oper == 'del') {
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM itsinv WHERE alma='$codigo' AND existen>0");
			if ($check > 0){
				echo " El almacen no fuede ser eliminado; tiene movimiento ";
			} else {
				//$this->db->simple_query("DELETE FROM caub WHERE id=$id ");
				logusu('BCAJ',"Almacen $codigo ELIMINADO");
				echo "{ success: true, message: 'Registro Eliminado'}";
			}
		};
	}


	//************************************************
	//
	//Definicion del Grid de las Formas de Pago
	//
	//************************************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

/*
		$grid->addField('tipo_doc');
		$grid->label('Tipo_doc');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
		));


		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));
*/

		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
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


		$grid->addField('banco');
		$grid->label('Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 3 }',
		));


		$grid->addField('num_ref');
		$grid->label('Num_ref');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
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


/*
		$grid->addField('clave');
		$grid->label('Clave');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));
*/

/*
		$grid->addField('f_factura');
		$grid->label('F_factura');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

*/
		$grid->addField('cod_cli');
		$grid->label('Cod_cli');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('vendedor');
		$grid->label('Vendedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));

/*
		$grid->addField('cobrador');
		$grid->label('Cobrador');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));
*/

		$grid->addField('status');
		$grid->label('Status');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));

/*
		$grid->addField('cobro');
		$grid->label('Cobro');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('cambio');
		$grid->label('Cambio');
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


		$grid->addField('almacen');
		$grid->label('Almacen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 4 }',
		));


		$grid->addField('transac');
		$grid->label('Transac');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));


		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
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
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));


		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('cierre');
		$grid->label('Cierre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));
*/

		$grid->addField('deposito');
		$grid->label('Deposito');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
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

		$grid->addField('cuentach');
		$grid->label('Cuentach');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 22 }',
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('150');
		$grid->setTitle('Detalle del Deposito');
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');
		$grid->setOndblClickRow('');

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(false);
		$grid->setDelete(false);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

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
	function getdatait()
	{
		$id = $this->uri->segment(4);
		if ($id == false ){
			$id = $this->datasis->dameval("SELECT MAX(id) FROM bcaj");
		}
		$deposito = $this->datasis->dameval("SELECT numero FROM bcaj WHERE id=$id");
		$grid     = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM sfpa WHERE deposito='$deposito' ";
		
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setDataIt()
	{
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$this->db->insert('sfpa', $data);
				echo "Registro Agregado";

				logusu('SFPA',"Registro ????? INCLUIDO");
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			//unset($data['ubica']);
			$this->db->where('id', $id);
			$this->db->update('sfpa', $data);
			logusu('SFPA',"Registro ????? MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sfpa WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				//$this->db->simple_query("DELETE FROM sfpa WHERE id=$id ");
				logusu('SFPA',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}


//****************************************************



	/*********************************
	 *
	 * cierra el deposito
	 *
	 */
	function cerrardpt(){
		// Genera el deposito pendiente
		$deposito = $this->input->get_post('fdeposito'); //Nro deposito
		$fecha    = $this->input->get_post('ffecha'); 
		$id       = $this->input->get_post('fid');
		$tipo     = $this->input->get_post('ftipo');
		$monto    = $this->input->get_post('fmonto');
		$cheques  = substr(trim($this->input->get_post('fsele')),0,-1);
		$numbcaj  = $this->input->get_post('fnumbcaj'); //Nro de Bcaj
		$caja     = '';
		$codbanc  = '';
		$mensaje  = "";
		$envia    = '00';
	
		$reg = $this->datasis->damereg("SELECT * FROM bcaj WHERE id=$id ");
	
		// Revisamos
		$check = 0;
		if ($id <= 0) {
			$check    = 1;
			$mensaje .= "ID en 0 ";
		} else
			$codbanc  = $reg['codbanc'];

		$caja   = $reg['envia'];
		$recibe = $codbanc;

		if ( $reg['status'] <> 'P' ) {
			$check += 1 ;
			$mensaje .= "Deposito no Pendiente ";
		}

		if ( empty($deposito) ){
			$check += 1;
			$mensaje .= "Falta: Numero de Deposito ";
		}

		if (empty($monto)){
			$check += 1;
			$mensaje .= 'Debe colocar el monto ';
		} else {
			if ( $monto <= 0 ){
				$check +1;
				$mensaje .= 'El monto debe ser > 0 ';
			}
		}

		if (empty($codbanc)){
			$check += 1;
			$mensaje .= 'No esta definido el banco receptor ';
		}

		if ( $tipo == "C" ) {
			if ( $check == 0 )
			{
				// Revisamos si el monto coincide con la suma
				$fecha = substr($fecha,-4,4).substr($fecha,3,2).substr($fecha,0,2);
	
				$mMdepo = $this->datasis->dameval("SELECT SUM(monto) FROM sfpa WHERE id IN ( $cheques )");
				$monto  = $this->datasis->dameval("SELECT SUM(monto) FROM sfpa WHERE deposito='$numbcaj'");
				$bancoo = 
			
				// GUARDA EN BCAJ
				$numeroe = $this->datasis->banprox('00');
				$numeror = $this->datasis->banprox($codbanc);
				$transac = $this->datasis->prox_sql("ntransa",8);

				//Busca Proximo Numero
				$i = 0;
					while ( $i == 0){
					$numero  = $this->datasis->prox_sql("nbcaj",8);
					if ($this->datasis->dameval("SELECT count(*) FROM bcaj WHERE numero='".$numero."'") == 0 ){
						$i = 1;
					};
				}

				$data = array();
				$data['fecha']      = $fecha;
				$data['numero']     = $numero;
				$data['tipo']       = 'DE';
				$data['tarjeta']    = 0;
				$data['tdebito']    = 0;
				$data['cheques']    = $mMdepo; //$monto;
				$data['efectivo']   = 0;
				$data['comision']   = 0;
				$data['islr']       = 0;
				$data['monto']      = $mMdepo; //$monto;
				$data['envia']      = '00';
				$data['bancoe']     = 'DEPOSITO EN TRANSITO';
				$data['tipoe']      = 'ND';
				$data['numeroe']    = $numeroe;
				$data['codbanc']    = $caja;  //de donde vino
				$data['recibe']     = $codbanc;
				$data['bancor']     = $this->datasis->dameval("SELECT banco FROM banc WHERE codbanc='$codbanc'");
				$data['tipor']      = 'DE';
				$data['numeror']    = $numeror;
				$data['concepto']   = "CIERRE DE DEPOSITO DE TRANSITO A BANCO $codbanc ";
				$data['concep2']    = "CHEQUES";
				$data['status']     = 'C';  // Pendiente/Cerrado/Anulado
				$data['usuario']    = $this->secu->usuario();
				$data['estampa']    = $fecha;
				$data['hora']       = date('H:i:s');
				$data['comprob']    = $numbcaj;
				$data['transac']    = $transac;

				//Guarda en BCAJ
				$this->db->insert('bcaj', $data);
				$this->datasis->actusal( '00', $fecha, -$mMdepo );
	
				$mSQL = "UPDATE sfpa SET deposito='$numero', status='C' WHERE id IN ($cheques)";
				$this->db->simple_query($mSQL);
	
				//GUARDA EN BMOV LA SALIDA DE CAJA
				$data = array();
		
				$data['codbanc']  = '00';
				$data['numcuent'] = $this->datasis->dameval("SELECT numcuent FROM banc WHERE codbanc='00'");
				$data['banco']    = $this->datasis->dameval("SELECT banco    FROM banc WHERE codbanc='00'");
				$data['saldo']    = $this->datasis->dameval("SELECT saldo    FROM banc WHERE codbanc='00'");
				$data['tipo_op']  = 'ND';
				$data['numero']   = $numeroe;
				$data['fecha']    = $fecha;
				$data['clipro']   = 'O';
				$data['codcp']    = 'CAJAS';
				$data['nombre']   = 'DEPOSITO DESDE CAJA';
				$data['monto']    = $monto; // Saca el monto completo
				$data['concepto'] = "CIERRE DEPOSITO EN TRANSITO BANCO $codbanc ";
				$data['concep2']  = "";
				$data['benefi']   = "";
				$data['usuario']  = $this->secu->usuario();
				$data['estampa']  = $fecha;
				$data['hora']     = date('H:i:s');
				$data['transac']  = $transac;
				$this->db->insert('bmov', $data);
		
				//Actualiza saldo en caja de transito
				$this->datasis->actusal($codbanc, $fecha, $mMdepo);

				$data['codbanc']  = $codbanc;
				$data['numcuent'] = $this->datasis->dameval("SELECT numcuent FROM banc WHERE codbanc='$codbanc'");
				$data['banco']    = $this->datasis->dameval("SELECT banco    FROM banc WHERE codbanc='$codbanc'");
				$data['saldo']    = $this->datasis->dameval("SELECT saldo    FROM banc WHERE codbanc='$codbanc'");
				$data['tipo_op']  = 'NC';
				$data['numero']   = $numeror;
				$data['fecha']    = $fecha;
				$data['clipro']   = 'O';
				$data['codcp']    = 'CAJAS';
				$data['nombre']   = 'DEPOSITO DESDE CAJA';
				$data['monto']    = $mMdepo;
				$data['concepto'] = "DEPOSITO CONCILIADO BANCO $recibe ";
				$data['concep2']  = "";
				$data['benefi']   = "";
				$data['usuario']  = $this->secu->usuario();
				$data['estampa']  = $fecha;
				$data['hora']     = date('H:i:s');
				$data['transac']  = $transac;
				$this->db->insert('bmov', $data);
	
				//Si los montos son diferentes genera la devolucion
				///devuelve las cheques no depositados
				if ($monto > $mMdepo){
					//Actualiza saldo en caja de transito
					$numeror = $this->datasis->banprox($caja);
					$this->datasis->actusal($caja, $fecha, $monto-$mMdepo);
					$data['codbanc']  = $caja;
					$data['numcuent'] = $this->datasis->dameval("SELECT numcuent FROM banc WHERE codbanc='$codbanc'");
					$data['banco']    = $this->datasis->dameval("SELECT banco    FROM banc WHERE codbanc='$codbanc'");
					$data['saldo']    = $this->datasis->dameval("SELECT saldo    FROM banc WHERE codbanc='$codbanc'");
					$data['tipo_op']  = 'NC';
					$data['numero']   = $numeror;
					$data['fecha']    = $fecha;
					$data['clipro']   = 'O';
					$data['codcp']    = 'CAJAS';
					$data['nombre']   = 'CHEQUES NO DEPOSITADOS';
					$data['monto']    = $monto-$mMdepo;
					$data['concepto'] = "CHEQUES RECHAZADOS DEPOSITO (00 => $caja) ";
					$data['concep2']  = "";
					$data['benefi']   = "";
					$data['usuario']  = $this->secu->usuario();
					$data['estampa']  = $fecha;
					$data['hora']     = date('H:i:s');
					$data['transac']  = $transac;
					$this->db->insert('bmov', $data);
				
					$mSQL = "UPDATE sfpa SET status='' WHERE  status='P' AND deposito='$numbcaj'";
					$this->db->simple_query($mSQL);

					$numero  = $this->datasis->prox_sql("nbcaj",8);
					$data = array();
					$data['fecha']      = $fecha;
					$data['numero']     = $numero;
					$data['tipo']       = 'DE';
					$data['tarjeta']    = 0;
					$data['tdebito']    = 0;
					$data['cheques']    = $monto-$mMdepo;
					$data['efectivo']   = 0;
					$data['comision']   = 0;
					$data['islr']       = 0;
					$data['monto']      = $monto-$mMdepo;
					$data['envia']      = '00';
					$data['bancoe']     = 'DEPOSITO EN TRANSITO';
					$data['tipoe']      = 'ND';
					$data['numeroe']    = $numeroe;
					$data['codbanc']    = $caja;  //de donde vino
					$data['recibe']     = $caja;
					$data['bancor']     = $this->datasis->dameval("SELECT banco FROM banc WHERE codbanc='$caja'");
					$data['tipor']      = 'DE';
					$data['numeror']    = $numeror;
					$data['concepto']   = "CUEQUES RECHAZADOS DEPOSITO BANCO $codbanc ";
					$data['concep2']    = "CHEQUES";
					$data['status']     = 'C';  // Pendiente/Cerrado/Anulado
					$data['usuario']    = $this->secu->usuario();
					$data['comprob']    = $numbcaj;
					$data['estampa']    = $fecha;
					$data['hora']       = date('H:i:s');
					$data['transac']    = $transac;
					//Guarda en BCAJ
					$this->db->insert('bcaj', $data);
					$this->datasis->actusal( '00', $fecha, -$mMdepo );
				}

				//cierra el deposito incial
				$mSQL = "UPDATE bcaj SET status='C' WHERE numero='$numbcaj'";
				$this->db->simple_query($mSQL);

				logusu('BCAJ',"Cierre de Deposito de cheques de caja Nro. $numero creada");

				echo '{"status":"G","numero":"$numero","mensaje":"Deposito Cerrado '.$numero.'"}';
			} else {
				echo '{"status":"E","numero":"'.$id.'","mensaje":"'.$mensaje.'"}';
			}
		} elseif ( $tipo == "E") {
			if ( $check == 0 )
			{
				// Revisamos si el monto coincide con la suma
				$fecha = substr($fecha,-4,4).substr($fecha,3,2).substr($fecha,0,2);
	
				$mMdepo = $reg['monto'];
				$monto  = $reg['monto'];
				$bancoo = 
			
				// GUARDA EN BCAJ
				$numeroe = $this->datasis->banprox('00');
				$numeror = $this->datasis->banprox($codbanc);
				$transac = $this->datasis->prox_sql("ntransa",8);

				//Busca Proximo Numero
				$i = 0;
					while ( $i == 0){
					$numero  = $this->datasis->prox_sql("nbcaj",8);
					if ($this->datasis->dameval("SELECT count(*) FROM bcaj WHERE numero='".$numero."'") == 0 ){
						$i = 1;
					};
				}

				$data = array();
				$data['fecha']      = $fecha;
				$data['numero']     = $numero;
				$data['tipo']       = 'DE';
				$data['tarjeta']    = 0;
				$data['tdebito']    = 0;
				$data['cheques']    = 0; //$monto;
				$data['efectivo']   = $mMdepo;
				$data['comision']   = 0;
				$data['islr']       = 0;
				$data['monto']      = $mMdepo; //$monto;
				$data['envia']      = '00';
				$data['bancoe']     = 'DEPOSITO EN TRANSITO';
				$data['tipoe']      = 'ND';
				$data['numeroe']    = $numeroe;
				$data['codbanc']    = $caja;  //de donde vino
				$data['recibe']     = $codbanc;
				$data['bancor']     = $this->datasis->dameval("SELECT banco FROM banc WHERE codbanc='$codbanc'");
				$data['tipor']      = 'DE';
				$data['numeror']    = $numeror;
				$data['concepto']   = "CIERRE DE DEPOSITO DE TRANSITO A BANCO $codbanc ";
				$data['concep2']    = "CHEQUES";
				$data['status']     = 'C';  // Pendiente/Cerrado/Anulado
				$data['usuario']    = $this->secu->usuario();
				$data['estampa']    = $fecha;
				$data['hora']       = date('H:i:s');
				$data['comprob']    = $numbcaj;
				$data['transac']    = $transac;

				//Guarda en BCAJ
				$this->db->insert('bcaj', $data);
				$this->datasis->actusal( '00', $fecha, -$mMdepo );
	
				$mSQL = "UPDATE sfpa SET deposito='$numero', status='C' WHERE id IN ($cheques)";
				$this->db->simple_query($mSQL);
	
				//GUARDA EN BMOV LA SALIDA DE CAJA
				$data = array();
		
				$data['codbanc']  = '00';
				$data['numcuent'] = $this->datasis->dameval("SELECT numcuent FROM banc WHERE codbanc='00'");
				$data['banco']    = $this->datasis->dameval("SELECT banco    FROM banc WHERE codbanc='00'");
				$data['saldo']    = $this->datasis->dameval("SELECT saldo    FROM banc WHERE codbanc='00'");
				$data['tipo_op']  = 'ND';
				$data['numero']   = $numeroe;
				$data['fecha']    = $fecha;
				$data['clipro']   = 'O';
				$data['codcp']    = 'CAJAS';
				$data['nombre']   = 'DEPOSITO DESDE CAJA';
				$data['monto']    = $monto; // Saca el monto completo
				$data['concepto'] = "CIERRE DEPOSITO EN TRANSITO BANCO $codbanc ";
				$data['concep2']  = "";
				$data['benefi']   = "";
				$data['usuario']  = $this->secu->usuario();
				$data['estampa']  = $fecha;
				$data['hora']     = date('H:i:s');
				$data['transac']  = $transac;
				$this->db->insert('bmov', $data);
		
				//Actualiza saldo en caja de transito
				$this->datasis->actusal($codbanc, $fecha, $mMdepo);

				$data['codbanc']  = $codbanc;
				$data['numcuent'] = $this->datasis->dameval("SELECT numcuent FROM banc WHERE codbanc='$codbanc'");
				$data['banco']    = $this->datasis->dameval("SELECT banco    FROM banc WHERE codbanc='$codbanc'");
				$data['saldo']    = $this->datasis->dameval("SELECT saldo    FROM banc WHERE codbanc='$codbanc'");
				$data['tipo_op']  = 'NC';
				$data['numero']   = $numeror;
				$data['fecha']    = $fecha;
				$data['clipro']   = 'O';
				$data['codcp']    = 'CAJAS';
				$data['nombre']   = 'DEPOSITO DESDE CAJA';
				$data['monto']    = $mMdepo;
				$data['concepto'] = "DEPOSITO CONCILIADO BANCO $recibe ";
				$data['concep2']  = "";
				$data['benefi']   = "";
				$data['usuario']  = $this->secu->usuario();
				$data['estampa']  = $fecha;
				$data['hora']     = date('H:i:s');
				$data['transac']  = $transac;
				$this->db->insert('bmov', $data);

				//cierra el deposito incial
				$mSQL = "UPDATE bcaj SET status='C' WHERE numero='$numbcaj'";
				$this->db->simple_query($mSQL);

				logusu('BCAJ',"Cierre de Deposito en Efectivo Nro. $numero creada");

				echo '{"status":"G","numero":"$numero","mensaje":"Deposito Cerrado '.$numero.'"}';
			} else {
				echo '{"status":"E","numero":"'.$id.'","mensaje":"'.$mensaje.'"}';
			}
		}
		

	}


	/*********************************
	*
	* Elimina Movimiento
	*
	*/
	function bcajborra(){
		$id = $this->uri->segment($this->uri->total_segments());
		
		$transac = $this->datasis->dameval("SELECT transac FROM bcaj WHERE id=$id");
		$status  = $this->datasis->dameval("SELECT status  FROM bcaj WHERE id=$id");
		$numero  = $this->datasis->dameval("SELECT numero  FROM bcaj WHERE id=$id");

		if ( $this->datasis->dameval("SELECT COUNT(*) FROM bmov WHERE transac='$transac' ") > 0 ){
			$mSQL = "SELECT codbanc, fecha, monto*if(tipo_op IN ('CH','ND'),1,-1) monto FROM bmov WHERE transac='$transac'";
			$query = $this->db->query($mSQL);
			if ( $query->num_rows() > 0 ) {
				foreach( $query->result() as $row ) {
					$this->datasis->actusal($row->codbanc, $row->fecha, $row->monto);
				}
			}
		}

		$this->db->query("DELETE FROM bcaj   WHERE transac=?", array($transac));
		$this->db->query("DELETE FROM bmov   WHERE transac=?", array($transac));
		$this->db->query("DELETE FROM gser   WHERE transac=?", array($transac));
		$this->db->query("DELETE FROM smov   WHERE transac=?", array($transac));
		$this->db->query("DELETE FROM itccli WHERE transac=?", array($transac));
		$this->db->query("DELETE FROM gitser WHERE transac=?", array($transac));

		// Cambia el estaus de los cheques
		$this->db->query("UPDATE sfpa SET status='' WHERE deposito='".$numero."'");

		// LIBERA LOS CHEQUES SI ES DE
		$this->db->simple_query("UPDATE sfpa SET status='' AND deposito='' WHERE deposito=?", array($numero));
		logusu('BCAJ',"MOVIMIENTO DE CAJA $numero Transaccion $transac ELIMINADO");
		echo "Movimiento de Caja Eliminado ";
	}


	/*********************************
	 *
	 * Elimina Movimiento
	 *
	 */
	function bcajdevo(){
		$id = $this->uri->segment($this->uri->total_segments());
		
		$transac = $this->datasis->dameval("SELECT transac FROM sfpa WHERE id=$id");
		$status  = $this->datasis->dameval("SELECT status  FROM sfpa WHERE id=$id");
		$numero  = $this->datasis->dameval("SELECT numero  FROM sfpa WHERE id=$id");
/*
		if ( $this->datasis->dameval("SELECT COUNT(*) FROM bmov WHERE transac='$transac' ") > 0 ){
			$mSQL = "SELECT codbanc, fecha, monto*if(tipo_op IN ('CH','ND'),1,-1) monto FROM bmov WHERE transac='$transac'";
			$query = $this->db->query($mSQL);
			if ( $query->num_rows() > 0 ) {
				foreach( $query->result() as $row ) {
					$this->datasis->actusal($row->codbanc, $row->fecha, $row->monto);
				}
			}
		}

		$this->db->simple_query("DELETE FROM bcaj   WHERE transac=?", array($transac));
		$this->db->simple_query("DELETE FROM bmov   WHERE transac=?", array($transac));
		$this->db->simple_query("DELETE FROM gser   WHERE transac=?", array($transac));
		$this->db->simple_query("DELETE FROM smov   WHERE transac=?", array($transac));
		$this->db->simple_query("DELETE FROM itccli WHERE transac=?", array($transac));
		$this->db->simple_query("DELETE FROM gitser WHERE transac=?", array($transac));

		// LIBERA LOS CHEQUES SI ES DE
		$this->db->simple_query("UPDATE sfpa SET status='' AND deposito='' WHERE deposito=?", array($numero));
		logusu('BCAJ',"MOVIMIENTO DE CAJA $numero ELIMINADO");
*/
		echo "Cheque Devuelto ";
	}



///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
	function filteredgrid(){

		$this->rapyd->load('datafilter','datagrid');
		$smenu['link']=barra_menu('51D');

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$filter = new DataFilter('Filtro','bcaj');
		$filter->fecha = new dateonlyField('Fecha','fecha');
		$filter->fecha->size=10;
		$filter->fecha->operator='=';

		$filter->numero = new inputField('N&uacute;mero', 'numero');
		$filter->numero->size=20;

		//$filter->nombre = new inputField('Nombre', 'nombre');
		//$filter->nombre->size=40;

		//$filter->banco = new dropdownField('Banco', 'codbanc');
		//$filter->banco->option('','');
		//$filter->banco->options('SELECT codbanc,banco FROM banc where tbanco<>\'CAJ\' ORDER BY codbanc');

		$filter->buttons('reset','search');
		$filter->build();

		$uri = anchor('finanzas/bcaj/dataedit/show/<#numero#>','<#numero#>');
		$uri1 = anchor('formatos/ver/BANCAJA/<#numero#>','<#numero#>');

		$grid = new DataGrid('Lista');
		$grid->order_by('numero','desc');
		$grid->per_page = 15;

		$uri2 = anchor_popup('finanzas/bcaj/formato/<#numero#>/<#tipo#>', img(array('src'=>'images/pdf_logo.gif','border'=>'0','alt'=>'PDF'))  ,$atts);
		//$uri2 .= "&nbsp;".anchor('finanzas/gser/mgserdataedit/modify/<#id#>',img(array('src'=>'images/pdf_logo.gif','border'=>'0','alt'=>'PDF')));

		$grid->column_orderby('N&uacute;mero',$uri,'numero');
		$grid->column_orderby('Fecha'        ,'<dbdate_to_human><#fecha#></dbdate_to_human>','fecha');
		$grid->column_orderby('Env&iacute;a' ,'<#envia#>-<#bancoe#>','bancoe');
		$grid->column_orderby('Recibe'       ,'<#recibe#>-<#bancor#>','bancor');
		$grid->column_orderby('Monto'        ,'<nformat><#monto#></nformat>' ,'monto','align=right');
		$grid->column_orderby('Concepto'     ,'concepto','concepto');
		$grid->column('Vista',$uri2,"align='center'");

		$grid->add('finanzas/bcaj/agregar');
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);

		$data['title']   = '<h1>Movimientos de Caja</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataedit');
		$edit = new DataEdit('Deposito en caja', 'bcaj');
		$edit->back_url = site_url('finanzas/bcaj/index');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'chfecha|required';

		$edit->tipo = new dropdownField('Tipo', 'tipo');
		$edit->tipo->options($this->guitipo);
		$edit->tipo->rule = 'required';
		$edit->tipo->style = 'width:180px';

		$edit->envia = new dropdownField('Envia','envia');
		$edit->envia->option('','Seleccionar');

		$edit->recibe = new dropdownField('Recibe','recibe');
		$edit->recibe->option('','Seleccionar');

		//Poner los campos que faltan

		$edit->buttons('modify', 'save', 'undo', 'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = '<h1>Depositos,transferencias y remesas</h1>';
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}
*/

	function formato($numero){
		$formato=$this->_formato($numero);
		$url='formatos/ver/'.$formato.'/'.$numero;
		redirect($url);
	}


	function agregar(){
		$data['content'] = '<table align="center">';

		$data['content'].= '<tr><td><img src="'.base_url().'images/dinero.jpg'.'" height="100px"></td><td bgcolor="#ddeedd">';
		$data['content'].= '<p>'.anchor('finanzas/bcaj/depositoefe'  ,'Deposito de efectivo: ');
		$data['content'].= 'Esta opci&oacute;n se utiliza para depositar lo recaudado en efectivo desde
		                    las cajas para los bancos, debe tener a mano el n&uacute;mero del deposito.</p>';

		$data['content'].= '</td></tr><tr><td><img src="'.base_url().'images/tarjetas.jpg'.'" height="100px"></td><td>';
		$data['content'].= '<p>'.anchor('finanzas/bcaj/depositotar'  ,'Deposito de tarjetas: ');
		$data['content'].= 'Para registrar lo recaudado mediante tarjetas electr&oacute;nicas (Cr&eacute;dito, Debito, Cesta Ticket)
		                    seg&uacute;n los valores impresos en los cierres diarios de los puntos de venta electr&oacute;nicos.</p>';

		$data['content'].= '</td></tr><tr><td><img src="'.base_url().'images/transfer.jpg'.'" height="100px" ></td><td bgcolor="#ddeedd">';
		$data['content'].= '<p>'.anchor('finanzas/bcaj/transferencia','Transferencias: ');
		$data['content'].= 'Puede hacer transferencias entre cajas o entre cuentas bancarias, las que correspondan a
		                    cuentas bancarias pueden realizarce mediante cheque-deposito (manual) o NC-ND por transferencia
		                    electr&oacute;nica, en cualquier caso debe tener los n&uacute;meros de documentos correspondientes.</p>';

		$data['content'].= '</td></tr><tr><td><img src="'.base_url().'images/caja_activa.gif'.'" height="100px" ></td><td>';
		$data['content'].= '<p>'.anchor('finanzas/bcaj/autotranfer','Transferencia de Cierre de Caja: ');
		$data['content'].= 'Si por pol&iacute;tica de la empresa se quiere descargar la caja de recaudaci&oacute;n todos los d&iacute;as, esta
		                    opci&oacute;n facilita el proceso ya que puede hacer varias transferencias en una sola operaci&oacute;n, por lo
		                    que se recomienda hacerla despues de cerrar todas la cajas.</p>';

		$data['content'].= '</td></tr><tr><td><img src="'.base_url().'images/blindado.gif'.'" height="60px" ></td><td bgcolor="#ddeedd">';
		$data['content'].= '<p>'.anchor('finanzas/bcaj/remesa','Remesas: ');
		$data['content'].= 'Cuando se entrega la relaci&oacute;n de cesta tickets a la empresa de valores de parte del Banco.</p>';

		$data['content'].= '</td></tr><tr><td colspan=2 align="center">'.anchor('finanzas/bcaj/index'        ,'Regresar').br();
		$data['content'].= '</td></tr></table>'.br();

		$data['title']   = heading('Selecciona la operaci&oacute;n que desea realizar');
		$data['head']    = $this->rapyd->get_head();  //.phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function remesa(){
		$this->rapyd->load('dataform');

		$edit = new DataForm('finanzas/bcaj/depositoefe/process');
		$edit->title('Remesa de Valores');

		$edit->numero = new inputField2('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->mode='autohide';
		$edit->numero->maxlength=8;
		$edit->numero->readonly=TRUE;

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size = 10;
		$edit->fecha->rule = 'chfecha|required';

		$edit->envia = new dropdownField('Caja','envia');
		$edit->envia->option('','Seleccionar');
		$desca='CONCAT_WS(\'-\',codbanc,banco) AS desca';
		$edit->envia->options( "SELECT TRIM(codbanc) AS codbanc,$desca FROM banc WHERE tbanco='CAJ'");
		$edit->envia->rule   = 'required';
		$edit->envia->style  = 'width:180px';

		$edit->boleta = new inputField('Nro de Boleta', 'boleta');
		$edit->boleta->rule='required';
		$edit->boleta->size=20;

		$edit->precinto = new inputField('Nro de Precinto', 'precinto');
		$edit->precinto->rule='required';
		$edit->precinto->size=20;

		$edit->comprob = new inputField('Comp. de Servicio', 'comprob');
		$edit->comprob->rule='required';
		$edit->comprob->size=20;

		$script='
			function totaliza(){
				if($("#efectivo").val().length>0) efectivo=parseFloat($("#efectivo").val()); else efectivo=0;
				if($("#cheques").val().length>0)  cheques =parseFloat($("#cheques").val());  else cheques =0;
				monto   =efectivo+cheques;
				$("#monto").val(roundNumber(monto,2));
			}';

		$script='';

		//$this->rapyd->jquery[]='$("#cheques,#efectivo").bind("keyup",function() { totaliza(); });';
		//$edit->script($script);

		$obj = 'monto';
		$edit->$obj = new inputField("Monto Bruto: ", $obj);
		$edit->$obj->css_class='inputnum';
		$edit->$obj->rule='trim|numeric';
		$edit->$obj->maxlength =15;
		$edit->$obj->size = 20;
		$edit->$obj->group = 'Montos';
		$edit->$obj->autocomplete=false;


		//$edit->$obj->readonly=true;
		//$edit->recibe->style = 'width:180px';


		$back_url = site_url('finanzas/bcaj/agregar');
		$edit->button('btn_undo', 'Regresar', "javascript:window.location='${back_url}'", 'TR');

		$edit->submit('btnsubmit','Guardar');
		$edit->build_form();

		//**********************
		//  Guarda el efecto
		//**********************
		if ($edit->on_success()){
			$fecha   = $edit->fecha->newValue;
			$envia   = $edit->envia->newValue;
			$recibe  = $edit->recibe->newValue;
			$numeror = $edit->numeror->newValue;
			$efectivo= $edit->efectivo->newValue;
			$cheque  = $edit->cheques->newValue;

			$rt=$this->_transferendepefe($fecha,$efectivo,$cheque,$envia,$recibe,$numeror);
			if($rt){
				redirect('finanzas/bcaj/listo/n/'.$this->bcajnumero);
			}else{
				redirect('finanzas/bcaj/listo/s');
			}
		}

		$data['content'] = $edit->output;
		$data['title']   = heading('Deposito');
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function transferencia(){
		$this->rapyd->load('dataform');

		$edit = new DataForm('finanzas/bcaj/transferencia/process');
		$edit->title='Deposito en caja';
		$link  = site_url('finanzas/bcaj/get_trrecibe');
		$script='
		function get_trrecibe(){
			if($("#envia").val().length>0){
				$.post("'.$link.'",{ envia: $("#envia").val()}, function(data){
					//alert(data);
					$("#recibe").html(data);
				});
			}
		}';
		$edit->script($script);

		$edit->back_url = site_url('finanzas/bcaj/index');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'chfecha|required';

		$edit->envia = new dropdownField('Envia y N&uacute;mero','envia');
		$edit->envia->option('','Seleccionar');

		$edit->numeroe = new inputField('N&uacute;mero de envio', 'numeroe');
		$edit->numeroe->in='envia';
		$edit->numeroe->rule='condi_required|callback_chnumeroe';
		$edit->numeroe->size=20;
		$edit->numeroe->append('Solo si el que env&iacute;a es un banco');


		$env=$this->input->post('envia');
		$edit->recibe = new dropdownField('Recibe y N&uacute;mero','recibe');
		$edit->recibe->option('','Seleccionar');
		if($env!==false){
			$tipo  = $this->_traetipo($env);
			$ww    = ($tipo=='CAJ') ? 'AND tbanco="CAJ"' : '';
			$desca = 'CONCAT_WS(\'-\',codbanc,banco) AS desca';
			$edit->recibe->options("SELECT codbanc,$desca FROM banc WHERE codbanc<>".$this->db->escape($env)." $ww ORDER BY banco");
		}

		$edit->numeror = new inputField('N&uacute;mero de envio', 'numeror');
		$edit->numeror->in='recibe';
		$edit->numeror->rule='condi_required|callback_chnumeror';
		$edit->numeror->size=20;
		$edit->numeror->append('Solo si el que recibe es un banco');

		$desca='CONCAT_WS(\'-\',codbanc,banco) AS desca';
		$edit->envia->options("SELECT codbanc,$desca FROM banc ORDER BY banco");
		$edit->envia->onchange = 'get_trrecibe();';
		$edit->envia->rule     = 'required';

		$codigo=$this->input->post('envia');
		if($codigo!==false){
			$tipo= $this->_traetipo($codigo);
			$ww=($tipo=='CAJ') ? 'tbanco="CAJ"' : 'tbanco<>"CAJ"';
			$edit->recibe->options("SELECT codbanc,$desca FROM banc WHERE $ww AND codbanc<>".$this->db->escape($codigo)." ORDER BY banco");
		}else{
			$edit->recibe->option('','Seleccione una caja de envio');
		}
		$edit->recibe->rule  = 'required';

		$edit->tipoe = new dropdownField('Documento de emisi&oacute;n','tipoe');
		$edit->tipoe->option('ND','Nota de debito');
		$edit->tipoe->option('CH','Cheque');
		$edit->tipoe->rule='condi_required|callback_chtipoe';
		$edit->tipoe->style  = 'width:180px';

		$edit->moneda = new dropdownField('Moneda','moneda');
		$edit->moneda->options('SELECT moneda,descrip FROM mone ORDER BY descrip');
		$edit->moneda->style  = 'width:180px';

		$edit->monto = new inputField('Monto', 'monto');
		$edit->monto->css_class='inputnum';
		$edit->monto->rule='trim|numeric|required';
		$edit->monto->maxlength =15;
		$edit->monto->size = 20;
		$edit->monto->autocomplete=false;

		$edit->envia->rule   = 'required';
		$edit->envia->style  = 'width:180px';
		$edit->recibe->style = 'width:180px';

		$back_url = site_url('finanzas/bcaj/agregar');
		$edit->button('btn_undo', 'Regresar', "javascript:window.location='${back_url}'", 'BL');

		$edit->submit('btnsubmit','Guardar');
		$edit->build_form();

		if ($edit->on_success()){
			$fecha  = $edit->fecha->newValue;
			$monto  = $edit->monto->newValue;
			$envia  = $edit->envia->newValue;
			$recibe = $edit->recibe->newValue;
			$numeror= $edit->numeror->newValue;
			$numeroe= $edit->numeroe->newValue;
			$tipoe  = $edit->tipoe->newValue;
			$moneda = $edit->moneda->newValue;
			$rt=$this->_transferencaj($fecha,$monto,$envia,$recibe,false,$numeror,$numeroe,$tipoe,$moneda);
			if($rt){
				redirect('/finanzas/bcaj/listo/n/'.$this->bcajnumero);
			}else{
				redirect('/finanzas/bcaj/listo/s');
			}
		}

		$this->rapyd->jquery[]='$(".inputnum").numeric(".");';
		//$this->rapyd->jquery[]='get_trrecibe();';
		$data['content'] = $edit->output;
		$data['title']   = heading('Transferencias');
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function depositoefe(){
		$this->rapyd->load('dataform');

		$edit = new DataForm('finanzas/bcaj/depositoefe/process');
		$edit->title('Deposito de efectivo');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size = 10;
		$edit->fecha->rule = 'chfecha|required';

		$edit->envia = new dropdownField('Envia','envia');
		$edit->envia->option('','Seleccionar');

		$edit->recibe = new dropdownField('Recibe','recibe');
		$edit->recibe->option('','Seleccionar');

		$edit->numeror = new inputField('N&uacute;mero de deposito', 'numeror');
		$edit->numeror->rule='required';
		$edit->numeror->size=20;

		$desca='CONCAT_WS(\'-\',codbanc,banco) AS desca';
		$script='
			function totaliza(){
				if($("#efectivo").val().length>0) efectivo=parseFloat($("#efectivo").val()); else efectivo=0;
				if($("#cheques").val().length>0)  cheques =parseFloat($("#cheques").val());  else cheques =0;
				monto   =efectivo+cheques;
				$("#monto").val(roundNumber(monto,2));
			}

			function get_cheques(){
				$.post("'.site_url('finanzas/bcaj/chequelist').'",{ recibe: $("#recibe").val()}, function(data) {
					$("#chequelist").html(data);
				});
			}';

		$this->rapyd->jquery[]='$("#cheques,#efectivo").bind("keyup",function() { totaliza(); });';
		//$this->rapyd->jquery[]='$("#recibe").bind("change",function() { get_cheques(); });';
		$edit->script($script);

		$edit->envia->options( "SELECT TRIM(codbanc) AS codbanc,$desca FROM banc WHERE tbanco='CAJ'");

		$edit->recibe->options("SELECT TRIM(codbanc) AS codbanc,$desca FROM banc WHERE tbanco<>'CAJ'");
		$edit->recibe->rule='callback_chtr|required';

		$campos=array(
				'cheques' =>'Cheques',
				'efectivo'=>'Efectivo',
				'monto'   =>'Monto total');

		foreach($campos AS $obj=>$titulo){
			$edit->$obj = new inputField($titulo, $obj);
			$edit->$obj->css_class='inputnum';
			$edit->$obj->rule='trim|numeric';
			$edit->$obj->maxlength =15;
			$edit->$obj->size = 20;
			$edit->$obj->group = 'Montos';
			$edit->$obj->autocomplete=false;
		}
		$edit->$obj->readonly=true;

		$edit->envia->rule   = 'required';
		$edit->envia->style  = 'width:180px';
		$edit->recibe->style .= 'width:180px';

		//$edit->container = new containerField('','<div id=\'chequelist\'>Lista de cheques</div>');
		//$edit->container->group = 'Montos';

		$back_url = site_url('finanzas/bcaj/agregar');
		$edit->button('btn_undo', 'Regresar', "javascript:window.location='${back_url}'", 'TR');

		$edit->submit('btnsubmit','Guardar');
		$edit->build_form();

		//**********************
		//  Guarda el efecto
		//**********************
		if ($edit->on_success()){
			$fecha   = $edit->fecha->newValue;
			$envia   = $edit->envia->newValue;
			$recibe  = $edit->recibe->newValue;
			$numeror = $edit->numeror->newValue;
			$efectivo= $edit->efectivo->newValue;
			$cheque  = $edit->cheques->newValue;

			$rt=$this->_transferendepefe($fecha,$efectivo,$cheque,$envia,$recibe,$numeror);
			if($rt){
				redirect('finanzas/bcaj/listo/n/'.$this->bcajnumero);
			}else{
				redirect('finanzas/bcaj/listo/s');
			}
		}

		$data['content'] = $edit->output;
		$data['title']   = heading('Deposito');
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function chequelist(){
		$recibe=$this->input->post('recibe');
		if(!empty($recibe)){
			$dbrecibe=$this->db->escape($recibe);

			$this->db->select(array('a.id','a.tipo_doc','a.numero','a.monto','a.tipo','b.nomb_banc','c.nombre'));
			$this->db->from('sfpa AS a');
			$this->db->join('tban AS b','a.banco=cod_banc');
			$this->db->join('scli AS c','a.cod_cli=c.cliente');
			$this->db->join('banc AS d',"a.banco=d.tbanco AND d.codbanc=$dbrecibe",'left');
			$this->db->where('a.tipo','CH');
			$this->db->where('status IS NULL');
			$this->db->order_by('d.codbanc IS NULL');
			$this->db->order_by('a.banco');
			$query = $this->db->get();
			//echo $this->db->last_query();

			echo '<table>';
			foreach ($query->result() as $row){
				echo '<tr>';
				echo '<td>'.form_checkbox('CC['.$row->id.']', 'accept').'</td>';
				echo '<td>'.$row->tipo_doc.$row->numero.'</td>';
				echo "<td>$row->nombre</td>";
				echo "<td>$row->nomb_banc</td>";
				echo '<td align=\'right\'>'.nformat($row->monto).'</td>';
				echo '</tr>';
			}
			echo '</table>';
		}else{
			echo 'Debe seleccionar un receptor.';
		}

	}

	function depositotar(){
		$this->rapyd->load('dataform');

		$edit = new DataForm('finanzas/bcaj/depositotar/process');
		$edit->title('Deposito en caja de tarjetas');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size = 10;
		$edit->fecha->rule = 'chfecha|required';

		$edit->envia = new dropdownField('Envia','envia');
		$edit->envia->option('','Seleccionar');

		$edit->recibe = new dropdownField('Recibe','recibe');
		$edit->recibe->option('','Seleccionar');

		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option('NC','Nota de credito');
		$edit->tipo->option('DE','Deposito');

		$edit->numero = new inputField('N&uacute;mero de deposito', 'numero');
		$edit->numero->rule='required';
		$edit->numero->size=20;

		$desca='CONCAT_WS(\'-\',codbanc,banco) AS desca';
		$sql='SELECT TRIM(a.codbanc) AS codbanc,b.comitc, b.comitd, b.impuesto FROM banc AS a JOIN tban AS b ON a.tbanco=b.cod_banc AND b.cod_banc<>\'CAJ\'';
		$query = $this->db->query($sql);
		$comis=array();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$ind='_'.$row->codbanc;
				$comis[$ind]['comitc']  =$row->comitc;
				$comis[$ind]['comitd']  =$row->comitd;
				$comis[$ind]['impuesto']=$row->impuesto;
			}
		}
		$json_comis=json_encode($comis);
		$script='
			comis=eval('.$json_comis.');
			function calcomis(){
				if($("#recibe").val().length>0){
					tasa='.$this->datasis->traevalor('tasa').';
					banco="_"+$("#recibe").val();
					eval("td=comis."+banco+".comitd;"  );
					eval("tc=comis."+banco+".comitc;"  );
					eval("im=comis."+banco+".impuesto;");

					if($("#tarjeta").val().length>0)  tarjeta=parseFloat($("#tarjeta").val());   else tarjeta =0;
					if($("#tdebito").val().length>0)  tdebito =parseFloat($("#tdebito").val());  else tdebito =0;

					islr    =tarjeta*10/(100+tasa);
					islr    =islr*(im/10);
					comision=tarjeta*(tc/100)+tdebito*(td/100);
					monto   =tarjeta+tdebito-comision-islr;

					$("#monto").val(roundNumber(monto,2));
					$("#comision").val(roundNumber(comision,2));
					$("#islr").val(roundNumber(islr,2));
				}
			}

			function totaliza(){
				if($("#tarjeta").val().length>0)  tarjeta =parseFloat($("#tarjeta").val());  else tarjeta =0;
				if($("#tdebito").val().length>0)  tdebito =parseFloat($("#tdebito").val());  else tdebito =0;
				if($("#comision").val().length>0) comision=parseFloat($("#comision").val()); else comision=0;
				if($("#islr").val().length>0)     islr    =parseFloat($("#islr").val());     else     islr=0;
				monto   =tarjeta+tdebito-comision-islr;
				$("#monto").val(roundNumber(monto,2));
			}';

		$this->rapyd->jquery[]='$("#tarjeta,#tdebito").bind("keyup",function() { calcomis(); });';
		$this->rapyd->jquery[]='$("#comision,#islr").bind("keyup",function() { totaliza(); });';
		$this->rapyd->jquery[]='$("#recibe").change(function() { calcomis(); });';
		$edit->script($script);

		$edit->envia->options( "SELECT TRIM(codbanc) AS codbanc,$desca FROM banc WHERE tbanco='CAJ'");

		$edit->recibe->options("SELECT TRIM(codbanc) AS codbanc,$desca FROM banc WHERE tbanco<>'CAJ'");
		$edit->recibe->rule='callback_chtr|required';

		$campos=array(
				'tarjeta' =>'Tarjeta de Cr&eacute;dito',
				'tdebito' =>'Tarjeta de D&eacute;bito',
				'comision'=>'Comisi&oacute;n',
				'islr'    =>'I.S.L.R.',
				'monto'   =>'Monto total');
		foreach($campos AS $obj=>$titulo){
			$edit->$obj = new inputField($titulo, $obj);
			$edit->$obj->css_class='inputnum';
			$edit->$obj->rule='trim|numeric';
			$edit->$obj->maxlength =15;
			$edit->$obj->size = 20;
			$edit->$obj->group = 'Montos';
			$edit->$obj->autocomplete=false;
		}
		$edit->$obj->readonly=true;

		$edit->envia->rule   = 'required';
		$edit->envia->style  = 'width:180px';
		$edit->recibe->style = 'width:180px';

		$back_url = site_url('finanzas/bcaj/agregar');
		$edit->button('btn_undo', 'Regresar', "javascript:window.location='${back_url}'", 'TR');

		$edit->submit('btnsubmit','Guardar');
		$edit->build_form();

		//**********************
		//  Guarda el efecto
		//**********************
		if ($edit->on_success()){
			$fecha   =$edit->fecha->newValue;
			$envia   =$edit->envia->newValue;
			$recibe  =$edit->recibe->newValue;
			$tarjeta =$edit->tarjeta->newValue;
			$tdebito =$edit->tdebito->newValue;
			$comision=$edit->comision->newValue;
			$islr    =$edit->islr->newValue;
			$numeror =$edit->numero->newValue;
			$tipo    =$edit->tipo->newValue;

			$rt=$this->_transferendeptar($fecha,$tarjeta,$tdebito,$comision,$islr,$envia,$recibe,$numeror,$tipo);
			if($rt){
				redirect('/finanzas/bcaj/listo/n/'.$this->bcajnumero);
			}else{
				redirect('/finanzas/bcaj/listo/s');
			}
		}

		$data['content'] = $edit->output;
		$data['title']   = heading('Deposito');
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}


	//Auto transferencia
	function autotranfer(){
		$this->rapyd->load('dataform');
		$edit = new DataForm('finanzas/bcaj/autotranfer/process');
		$edit->title='Transferencia automatica entre cajas';

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'chfecha|required';
		$edit->fecha->dbformat='Y-m-d';
		$edit->fecha->size=10;

		$back_url=site_url('finanzas/bcaj/agregar');
		$edit->button('btn_undo','Regresar',"javascript:window.location='$back_url'",'BL');
		$edit->submit('btnsubmit','Siguiente');
		$edit->build_form();
		if ($edit->on_success()){
			$fecha=$edit->fecha->newValue;
			redirect('finanzas/bcaj/autotranfer2/'.$fecha);
		}

		$data['content'] = $edit->output;
		$data['title']   = heading('Conciliaci&oacute;n de cierre');
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function autotranfer2($fecha=null){
		//***************************
		$this->cajas=$this->config->item('cajas');
		foreach($this->cajas AS $inv=>$val){
			$codban=$this->db->escape($val);
			$cana=$this->datasis->dameval("SELECT COUNT(*) AS cana FROM banc WHERE codbanc=$codban");
			if($cana==0){
				show_error('La caja '.$val.' no esta registrada en el sistema, debe registrarla por el modulo de bancos o ajustar la configuracion en config/datasis.php');
			}
		}
		//***************************

		$this->rapyd->load('dataform');
		$this->load->library('validation');
		$val=$this->validation->chfecha($fecha,'Y-m-d');
		if($val){
			$montosis=$this->_montoautotranf($this->cajas['cobranzas'],$fecha);
			if($montosis>0){

				$script='
					function totaliza(){
						if($("#tarjeta").val().length>0)  tarjeta =parseFloat($("#tarjeta").val());  else tarjeta =0;
						if($("#efectivo").val().length>0) efectivo=parseFloat($("#efectivo").val()); else efectivo=0;
						if($("#gastos").val().length>0)   gastos  =parseFloat($("#gastos").val());   else gastos  =0;
						if($("#valores").val().length>0)  valores =parseFloat($("#valores").val());  else valores =0;
						monto=tarjeta+gastos+efectivo+valores;
						$("#monto").val(roundNumber(monto,2));
					}';

				$edit = new DataForm("finanzas/bcaj/autotranfer2/$fecha/process");
				$edit->title='Transferencia automatica entre cajas';
				$edit->script($script);

				//$edit->back_url = site_url('finanzas/bcaj/index');


				$campos=array(
					'efectivo'=>'Efectivo caja: '.$this->cajas['efectivo'],
					'tarjeta' =>'Tarjeta de D&eacute;bito y Cr&eacute;dito caja: '.$this->cajas['tarjetas'],
					'gastos'  =>'Gastos por Justificar caja: '.$this->cajas['gastos'],
					'valores' =>'Valores, Cesta Tickes y Cheques caja: '.$this->cajas['valores'],
					'monto'   =>'Monto total');

				foreach($campos AS $obj=>$titulo){
					$edit->$obj = new inputField($titulo, $obj);
					$edit->$obj->css_class='inputnum';
					$edit->$obj->rule='trim|numeric';
					$edit->$obj->maxlength =15;
					$edit->$obj->size = 20;
					$edit->$obj->group = 'Montos';
					$edit->$obj->autocomplete=false;
				}
				$edit->$obj->rule='trim|numeric|callback_chtotal|required';
				$edit->$obj->readonly=true;

				$back_url=site_url('finanzas/bcaj/index');
				$edit->button('btn_undo','Regresar',"javascript:window.location='$back_url'",'BL');
				$edit->submit('btnsubmit','Guardar');
				$edit->build_form();

				$salida  = 'El monto total a tranferir para la fecha <b id="ffecha">'.dbdate_to_human($fecha).'</b> debe ser de: <b id="mmonto">'.nformat($montosis).'</b>';
				if ($edit->on_success()){
					//$fecha=$edit->fecha->newValue;
					foreach($campos AS $obj=>$titulo){
						$$obj=$edit->$obj->newValue;
					}
					if( round($montosis,2) == round($efectivo+$tarjeta+$gastos+$valores,2)) {
						$rt=$this->_autotranfer($fecha,$efectivo,$tarjeta,$gastos,$valores);
						if($rt){
							redirect('/finanzas/bcaj/listo/n/'.$this->bcajnumero);
						}else{
							redirect('/finanzas/bcaj/listo/s');
						}
					}else{
						$edit->error_string='El monto total a transferir debe ser de :<b>'.nformat($montosis).'</b>, faltan '.nformat($montosis-$efectivo-$tarjeta-$gastos-$valores);
						$edit->build_form();
						//$salida .= $edit->output;
					}
				}
				$salida .= $edit->output;

				$url=site_url('finanzas/bcaj/ajaxmonto');
				$this->rapyd->jquery[]='$(".inputnum").numeric(".");';
				$this->rapyd->jquery[]='$(".inputnum").bind("keyup",function() { totaliza(); });';
				$this->rapyd->jquery[]='$("td").removeAttr("style");';
				$this->rapyd->jquery[]='$("input[name=\'traesaldo\']").click(function() {
					fecha=$("#fecha").val();
					if(fecha.length > 0){
						$.post("'.$url.'", { fecha: $("#fecha").val() },
							function(data){
								$("#mmonto").html(nformat(data));
								$("#ffecha").html($("#fecha").val());
								$(".alert").hide("slow");
							});
					}else{
						alert("Debe introducir una fecha");
					}
					});';

			}else{
				$dbfecha=$this->db->escape($fecha);
				$mSQL = "SELECT COUNT(*) AS cana FROM bcaj WHERE concep2='AUTOTRANFER' AND fecha=$dbfecha";
				$cana = $this->datasis->dameval($mSQL);
				if($cana>0){
					$salida = 'Ya fue hecha una tranferencias para la fecha dada, si desea puede reversarla haciendo click '.anchor('finanzas/bcaj/reverautotranfer/'.$fecha,'aqui').' ';
					$salida.= ' o puede '.anchor('finanzas/bcaj/index','regresar').' al inicio.';
				}else{
					$salida = 'No hay monto disponible para transferir '.anchor('finanzas/bcaj/autotranfer','Regresar');
				}
			}
		}else{
			show_error('Falta el parametro fecha');
		}

		$data['content'] = $salida;
		$data['title']   = '<h1>Conciliaci&oacute;n de cierre </h1>';
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function ajaxmonto(){
		$fecha=$this->input->post('fecha');
		if($fecha!==false){
			$fecha=human_to_dbdate($fecha);
			$monto=$this->_montoautotranf($this->cajas['cobranzas'],$fecha);
		}else{
			$monto=0;
		}
		echo $monto;
	}

	//Metodo que reversa las tranferencias automaticas
	function reverautotranfer($fecha){
		$this->load->library('validation');
		$val  = $this->validation->chfecha($fecha,'Y-m-d');
		$error= 0;
		if($val){
			$rt=$this->_reverautotranfer($fecha);
			if($rt)
				redirect('finanzas/bcaj/listo/n');
			else
				redirect('finanzas/bcaj/listo/s');
		}
	}

	function _reverautotranfer($fecha){
		$dbfecha=$this->db->escape($fecha);
		$sp_fecha= str_replace('-','',$fecha);
		$mSQL="SELECT transac,monto,envia,recibe FROM bcaj WHERE fecha=$dbfecha AND concep2='AUTOTRANFER'";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$transac=$this->db->escape($row->transac);
				$sql="DELETE FROM bmov WHERE transac=$transac";
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'bcaj'); $error++; }

				$monto=$row->monto;
				$sql='CALL sp_actusal('.$this->db->escape($row->envia).",'$sp_fecha',$monto)";
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'bcaj'); $error++; }

				$sql='CALL sp_actusal('.$this->db->escape($row->recibe).",'$sp_fecha',-$monto)";
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'bcaj'); $error++; }
			}
		}
		$sql="DELETE FROM bcaj WHERE fecha=$dbfecha AND concep2='AUTOTRANFER'";
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }
		return ($error==0)? true : false;
	}

	function _autotranfer($fecha,$efectivo=0,$tarjeta=0,$gastos=0,$valores=0){
		//$cajas=$this->config->item('cajas');
		$envia=$this->cajas['cobranzas'];
		$arr=array(
			'efectivo'=>$this->cajas['efectivo'],
			'tarjeta' =>$this->cajas['tarjetas'],
			'gastos'  =>$this->cajas['gastos'],
			'valores' =>$this->cajas['valores']
		);
		$rt=true;
		foreach($arr as $monto=>$recibe){
			if(!$this->_transferencaj($fecha,$$monto,$envia,$recibe,true))
				$rt=false;
		}
		return $rt;
	}


	function _transferencaj($fecha,$monto,$envia,$recibe,$auto=false,$numeror=null,$numeroe=null,$tipoe='ND',$moneda='Bs'){
		if($monto<=0) return true;
		$numero  = $this->datasis->fprox_numero('nbcaj');
		$transac = $this->datasis->fprox_numero('ntransa');
		$_numeroe= $this->datasis->banprox($envia);
		$_numeror= $this->datasis->banprox($recibe);
		$numeroe = ($_numeroe===false)? str_pad($numeroe, 12, '0', STR_PAD_LEFT): $_numeroe;
		$numeror = ($_numeror===false)? str_pad($numeror, 12, '0', STR_PAD_LEFT): $_numeror;
		$sp_fecha= str_replace('-','',$fecha);
		$tipor   = ($tipoe=='ND') ? 'NC': 'DE';
		$error  = 0;
		$this->bcajnumero=$numero;

		$mSQL='SELECT codbanc,numcuent,tbanco,banco,saldo FROM banc WHERE codbanc IN ('.$this->db->escape($envia).','.$this->db->escape($recibe).')';
		$query = $this->db->query($mSQL);
		$infbanc=array();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$infbanc[$row->codbanc]['numcuent']=$row->numcuent;
				$infbanc[$row->codbanc]['tbanco']  =$row->tbanco;
				$infbanc[$row->codbanc]['banco']   =$row->banco;
				$infbanc[$row->codbanc]['saldo']   =$row->banco;
			}
		}

		$data=array(
			'tipo'    => 'TR',
			'fecha'   => $fecha,
			'numero'  => $numero,
			'transac' => $transac,
			'usuario' => $this->session->userdata('usuario'),
			'envia'   => $envia,
			'tipoe'   => $tipoe,
			'numeroe' => $numeroe,
			'bancoe'  => $infbanc[$envia]['banco'],
			'recibe'  => $recibe,
			'tipor'   => $tipor,
			'numeror' => $numeror,
			'bancor'  => $infbanc[$recibe]['banco'],
			'concepto'=> 'TRANSFERENCIA ENTRE CAJA '.$envia.' A '.$recibe,
			'concep2' => ($auto)? 'AUTOTRANFER' : '',
			'benefi'  => '',
			'boleta'  => '',
			'precinto'=> '',
			'comprob' => '',
			'totcant' => '',
			'status'  => '',
			'estampa' => date('Ymd'),
			'hora'    => date('H:i:s'),
			'deldia'  => $fecha,
			'tarjeta' => 0,
			'tdebito' => 0,
			'cheques' => 0,
			'efectivo'=> $monto,
			'comision'=> 0,
			'islr'    => 0,
			'monto'   => $monto,
		);
		$sql=$this->db->insert_string('bcaj', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		//Crea el egreso en el banco
		$mSQL='CALL sp_actusal('.$this->db->escape($envia).",'$sp_fecha',-$monto)";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		$data=array();
		$data['codbanc']  = $envia;
		$data['numcuent'] = $infbanc[$envia]['numcuent'];
		$data['banco']    = $infbanc[$envia]['banco'];
		$data['saldo']    = $infbanc[$envia]['saldo'];
		$data['tipo_op']  = $tipoe;
		$data['numero']   = $numeroe;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'TRANS';
		$data['monto']    = $monto;
		$data['concepto'] = 'TRANSFERENCIAS ENTRE CAJA '.$envia.' A '.$recibe;
		$data['concep2']  = '';
		$data['transac']  = $transac;
		$data['usuario']  = $this->session->userdata('usuario');
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$data['benefi']   = '-';
		$data['moneda']   = $moneda;
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		//Crea el ingreso la otra caja
		$mSQL='CALL sp_actusal('.$this->db->escape($recibe).",'$sp_fecha',$monto)";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		$data=array();
		$data['codbanc']  = $recibe;
		$data['numcuent'] = $infbanc[$recibe]['numcuent'];
		$data['banco']    = $infbanc[$recibe]['banco'];
		$data['saldo']    = $infbanc[$recibe]['saldo'];
		$data['tipo_op']  = $tipor;
		$data['numero']   = $numeror;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'TRANS';
		$data['monto']    = $monto;
		$data['concepto'] = 'TRANSFERENCIAS ENTRE CAJA '.$envia.' A '.$recibe;
		$data['concep2']  = '';
		$data['transac']  = $transac;
		$data['usuario']  = $this->session->userdata('usuario');
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$data['benefi']   = '-';
		$data['moneda']   = $moneda;
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }
		logusu('bcaj',"Transferencia de caja $numero creada");

		return ($error==0) ? true : false;
	}

	function _transferendepefe($fecha, $efectivo, $cheque, $envia, $recibe, $numeror, $moneda='Bs'){
		$monto=$efectivo+$cheque;
		if($monto<=0) return true;
		$numero = $this->datasis->fprox_numero('nbcaj');
		$transac= $this->datasis->fprox_numero('ntransa');
		$numeroe= $this->datasis->banprox($envia);
		$numeroe = str_pad($numeroe, 12, '0', STR_PAD_LEFT);
		$usuario = $this->secu->usuario();
		$estampa = date('Y-m-d');
		$hora    = date('H:i:s');
		$benefi  = trim($this->datasis->traevalor('TITULO1'));
		//$numeror = ($_numeror===false)? str_pad($numeror, 12, '0', STR_PAD_LEFT): $_numeror;


		//$numeror= $this->datasis->banprox($recibe);
		$sp_fecha= str_replace('-','',$fecha);
		$error  = 0;
		$this->bcajnumero=$numero;

		$mSQL='SELECT codbanc,numcuent,tbanco,banco,saldo FROM banc WHERE codbanc IN ('.$this->db->escape($envia).','.$this->db->escape($recibe).')';
		$query = $this->db->query($mSQL);
		$infbanc=array();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$infbanc[$row->codbanc]['numcuent']=$row->numcuent;
				$infbanc[$row->codbanc]['tbanco']  =$row->tbanco;
				$infbanc[$row->codbanc]['banco']   =$row->banco;
				$infbanc[$row->codbanc]['saldo']   =$row->saldo;
			}
		}

		$data=array(
			'tipo'    => 'DE',
			'fecha'   => $fecha,
			'numero'  => $numero,
			'transac' => $transac,
			'usuario' => $usuario,
			'envia'   => $envia,
			'tipoe'   => 'ND',
			'numeroe' => $numeroe,
			'bancoe'  => $infbanc[$envia]['banco'],
			'recibe'  => $recibe,
			'tipor'   => 'DE',
			'numeror' => $numeror,
			'bancor'  => $infbanc[$recibe]['banco'],
			'concepto'=> 'DEPOSITO DE CAJA '.$envia.' A BANCO '.$recibe,
			'concep2' => ($auto)? 'AUTOTRANFER' : '',
			'benefi'  => $benefi,
			'boleta'  => '',
			'precinto'=> '',
			'comprob' => '',
			'totcant' => '',
			'status'  => '',
			'estampa' => $estampa,
			'hora'    => $hora,
			'deldia'  => $fecha,
			'tarjeta' => 0,
			'tdebito' => 0,
			'cheques' => $cheque,
			'efectivo'=> $efectivo,
			'comision'=> 0,
			'islr'    => 0,
			'monto'   => $monto,
		);

		$sql=$this->db->insert_string('bcaj', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		//Crea el egreso en el banco
		$mSQL='CALL sp_actusal('.$this->db->escape($envia).",'$sp_fecha',-$monto)";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		$data=array();
		$data['codbanc']  = $envia;
		$data['numcuent'] = $infbanc[$envia]['numcuent'];
		$data['banco']    = $infbanc[$envia]['banco'];
		$data['saldo']    = $infbanc[$envia]['saldo'];
		$data['tipo_op']  = 'ND';
		$data['numero']   = $numeroe;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'CAJAS';
		$data['nombre']   = 'DEPOSITO DESDE CAJA';
		$data['monto']    = $monto;
		$data['concepto'] = 'DEPOSITO DE CAJA '.$envia.' A BANCO '.$recibe;
		$data['concep2']  = '';
		$data['transac']  = $transac;
		$data['usuario']  = $usuario;
		$data['estampa']  = $estampa;
		$data['hora']     = $hora;
		$data['benefi']   = '-';
		$data['comprob']  = $numero;
		$data['moneda']   = $moneda;

		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		//Crea el ingreso la otra caja
		$mSQL='CALL sp_actusal('.$this->db->escape($recibe).",'$sp_fecha',$monto)";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		$data=array();
		$data['codbanc']  = $recibe;
		$data['numcuent'] = $infbanc[$recibe]['numcuent'];
		$data['banco']    = $infbanc[$recibe]['banco'];
		$data['saldo']    = $infbanc[$recibe]['saldo'];
		$data['tipo_op']  = 'DE';
		$data['numero']   = $numeror;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['bruto']    = 0;
		$data['comision'] = 0;
		$data['impuesto'] = 0;
		$data['codcp']    = 'CAJAS';
		$data['nombre']   = 'DEPOSITO DESDE CAJA';
		$data['monto']    = $monto;
		$data['concepto'] = 'DEPOSITO DE CAJA '.$envia.' A BANCO '.$recibe;
		$data['concep2']  = '';
		$data['transac']  = $transac;
		$data['usuario']  = $usuario;
		$data['estampa']  = $estampa;
		$data['hora']     = $hora;
		$data['benefi']   = '-';
		$data['documen']  = $numero;
		$data['comprob']  = $numero;
		$data['moneda']   = $moneda;
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }
		logusu("Transferencia de caja $numero creada");
		return ($error==0) ? true : false;
	}

	function _transferendeptar($fecha,$tarjeta,$tdebito,$comision,$islr,$envia,$recibe,$numeror,$tipo,$moneda='Bs'){
		$monto=$tarjeta+$tdebito;
		if($monto<=0) return true;
		$numero  = $this->datasis->fprox_numero('nbcaj');
		$transac = $this->datasis->fprox_numero('ntransa');
		$numeroe = $this->datasis->banprox($envia);
		$dbrecibe= $this->db->escape($recibe);
		$sp_fecha= str_replace('-','',$fecha);
		$usuario = $this->secu->usuario();
		$estampa = date('Y-m-d');
		$hora    = date('H:i:s');
		$error   = 0;
		$this->bcajnumero=$numero;

		$mSQL="SELECT a.tipotra ,a.formaca FROM tban AS a JOIN banc AS b ON a.cod_banc=b.tbanco WHERE a.cod_banc=$dbrecibe";
		$parr=$this->datasis->damerow($mSQL);
		$formaca=(empty($parr['formaca']) OR $parr['formaca']=='NETA')? 'NETA': 'BRUTA';

		$mSQL='SELECT codbanc,numcuent,tbanco,banco,saldo,codprv,gastocom,depto FROM banc WHERE codbanc IN ('.$this->db->escape($envia).','.$this->db->escape($recibe).')';
		$query = $this->db->query($mSQL);
		$infbanc=array();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$infbanc[$row->codbanc]['numcuent' ]=$row->numcuent;
				$infbanc[$row->codbanc]['tbanco']   =$row->tbanco;
				$infbanc[$row->codbanc]['banco']    =$row->banco;
				$infbanc[$row->codbanc]['saldo']    =$row->banco;
				$infbanc[$row->codbanc]['codprv']   =$row->codprv;
				$infbanc[$row->codbanc]['gastocom'] =$row->gastocom;
				$infbanc[$row->codbanc]['depto']    =$row->depto;
			}
		}

		$data=array(
			'tipo'    => 'DE',
			'fecha'   => $fecha,
			'numero'  => $numero,
			'transac' => $transac,
			'envia'   => $envia,
			'tipoe'   => 'ND',
			'numeroe' => $numeroe,
			'bancoe'  => $infbanc[$envia]['banco'],
			'recibe'  => $recibe,
			'tipor'   => $tipo,
			'numeror' => $numeror,
			'bancor'  => $infbanc[$recibe]['banco'],
			'concepto'=> 'DEP/TARJETAS DE CAJA '.$envia.' A BANCO '.$recibe,
			'concep2' => '',
			'benefi'  => $this->datasis->traevalor('TITULO1'),
			'boleta'  => '',
			'precinto'=> '',
			'comprob' => '',
			'totcant' => 0,
			'status'  => '',
			'usuario' => $usuario,
			'estampa' => $estampa,
			'hora'    => $hora,
			'deldia'  => $fecha,
			'tarjeta' => $tarjeta,
			'tdebito' => $tdebito,
			'cheques' => 0,
			'efectivo'=> 0,
			'comision'=> $comision,
			'islr'    => $islr,
			'monto'   => $tarjeta+$tdebito-$comision-$islr,
		);
		$sql=$this->db->insert_string('bcaj', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		//Crea el egreso en el banco
		$data=array();
		$data['codbanc']  = $envia;
		$data['numcuent'] = $infbanc[$envia]['numcuent'];
		$data['banco']    = $infbanc[$envia]['banco'];
		$data['saldo']    = $infbanc[$envia]['saldo'];
		$data['tipo_op']  = 'ND';
		$data['numero']   = $numeroe;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'CAJAS';
		$data['nombre']   = 'DEPOSITO DESDE CAJA';
		$data['monto']    = $tarjeta+$tdebito;
		$data['concepto'] = 'DEP/TARJETAS DE CAJA '.$envia.' A BANCO '.$recibe;
		$data['concep2']  = '';
		$data['comprob']  = $numero;
		$data['transac']  = $transac;
		$data['usuario']  = $usuario;
		$data['estampa']  = $estampa;
		$data['hora']     = $hora;
		$data['moneda']   = $moneda;
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }
		$mSQL='CALL sp_actusal('.$this->db->escape($envia).",'$sp_fecha',-$data[monto])";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		//Crea el ingreso la otra caja

		$data=array();
		$data['codbanc']  = $recibe;
		$data['numcuent'] = $infbanc[$recibe]['numcuent'];
		$data['banco']    = $infbanc[$recibe]['banco'];
		$data['saldo']    = $infbanc[$recibe]['saldo'];
		$data['tipo_op']  = 'NC';
		$data['numero']   = $numeror;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'CAJAS';
		$data['comision'] = $comision;
		$data['impuesto'] = $islr;
		$data['monto']    = ($formaca=='NETA')?  $tarjeta+$tdebito-$islr-$comision : $tarjeta+$tdebito ;
		$data['nombre']   = 'DEPOSITO DESDE CAJA';
		$data['concepto'] = 'DEP/TARJETAS DE CAJA '.$envia.' A BANCO '.$recibe;;
		$data['concep2']  = '';
		$data['bruto']    = $tarjeta;
		$data['comprob']  = $numero;
		$data['documen']  = $numero;
		$data['transac']  = $transac;
		$data['usuario']  = $usuario;
		$data['estampa']  = $estampa;
		$data['hora']     = $hora;
		$data['moneda']   = $moneda;
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		$mSQL='CALL sp_actusal('.$this->db->escape($recibe).",'$sp_fecha',$data[monto])";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		if($comision>0){
			if($formaca=='BRUTA'){
				$data=array();
				$data['codbanc']  = $recibe;
				$data['numcuent'] = $infbanc[$recibe]['numcuent'];
				$data['banco']    = $infbanc[$recibe]['banco'];
				$data['saldo']    = $infbanc[$recibe]['saldo'];
				$data['tipo_op']  = 'ND';
				$data['numero']   = 'C'.substr($numeror,1);
				$data['fecha']    = $fecha;
				$data['clipro']   = 'O';
				$data['codcp']    = 'CAJAS';
				$data['comision'] = $comision;
				$data['impuesto'] = $islr;
				$data['monto']    = $comision;
				$data['nombre']   = 'COMISION POR TC/TD';
				$data['concepto'] = 'COMISION POR TC/TD';
				$data['concep2']  = '';
				$data['bruto']    = $tarjeta;
				$data['comprob']  = $numero;
				$data['documen']  = $numero;
				$data['transac']  = $transac;
				$data['usuario']  = $usuario;
				$data['estampa']  = $estampa;
				$data['hora']     = $hora;
				$data['moneda']   = $moneda;
				$sql=$this->db->insert_string('bmov', $data);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'bcaj'); $error++; }
			}

			$data=array();
			$data['fecha']    = $fecha;
			$data['numero']   = 'CTC'.substr($numero,-5);
			$data['proveed']  = $infbanc[$recibe]['codprv'];
			$data['nombre']   = $this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$this->db->escape($infbanc[$recibe]['codprv']));
			$data['vence']    = $fecha;
			$data['totpre']   = $comision;
			$data['totiva']   = 0;
			$data['totbruto'] = $comision;
			$data['reten']    = 0;
			$data['totneto']  = $comision;
			$data['codb1']    = $envia;
			$data['cheque1']  = $numeroe;
			$data['tipo1']    = 'D';
			$data['monto1']   = $comision;
			$data['codb2']    = '';
			$data['tipo2']    = '';
			$data['cheque2']  = '';
			$data['comprob2'] = '';
			$data['monto2']   = 0;
			$data['codb3']    = '';
			$data['tipo3']    = '';
			$data['cheque3']  = '';
			$data['comprob3'] = '';
			$data['monto3']   = 0;
			$data['credito']  = 0;
			$data['anticipo'] = 0;
			$data['orden']    = '';
			$data['tipo_doc'] = 'FC';
			$data['transac']  = $transac;
			$data['usuario']  = $usuario;
			$data['estampa']  = $estampa;
			$data['hora']     = $hora;
			$sql=$this->db->insert_string('gser', $data);
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'bcaj'); $error++; }

			$data=array();
			$data['fecha']    = $fecha;
			$data['numero']   = 'CTC'.substr($numero,-5);
			$data['proveed']  = $infbanc[$recibe]['codprv'];
			$data['codigo']   = $infbanc[$recibe]['gastocom'];
			$data['descrip']  = 'COMISION POR TARJETAS '.$infbanc[$recibe]['banco'];
			$data['precio']   = $comision;
			$data['iva']      = 0;
			$data['importe']  = $comision;
			$data['unidades'] = 0;
			$data['fraccion'] = 0;
			$data['almacen']  = '';
			$data['departa']  = $infbanc[$recibe]['depto'];
			$data['sucursal'] = ' ';
			$data['transac']  = $transac;
			$data['usuario']  = $usuario;
			$data['estampa']  = $estampa;
			$data['hora']     = $hora;
			$sql=$this->db->insert_string('gitser', $data);
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'bcaj'); $error++; }
		}

		if($islr>0){
			if($formaca=='BRUTA'){
				$data=array();
				$data['codbanc']  = $recibe;
				$data['numcuent'] = $infbanc[$recibe]['numcuent'];
				$data['banco']    = $infbanc[$recibe]['banco'];
				$data['saldo']    = $infbanc[$recibe]['saldo'];
				$data['tipo_op']  = 'ND';
				$data['numero']   = 'R'.substr($numeror,1);
				$data['fecha']    = $fecha;
				$data['clipro']   = 'O';
				$data['codcp']    = 'CAJAS';
				$data['comision'] = $comision;
				$data['impuesto'] = $islr;
				$data['monto']    = $islr;
				$data['nombre']   = 'RETENCION DE ISLR POR TC';
				$data['concepto'] = 'RETENCION DE ISLR POR TC';
				$data['concep2']  = '';
				$data['bruto']    = $tarjeta;
				$data['comprob']  = $numero;
				$data['documen']  = $numero;
				$data['transac']  = $transac;
				$data['usuario']  = $usuario;
				$data['estampa']  = $estampa;
				$data['hora']     = $hora;
				$data['moneda']   = $moneda;
				$sql=$this->db->insert_string('bmov', $data);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'bcaj'); $error++; }
			}
			$nccli = $this->datasis->fprox_numero('nccli');
			$nsmov = $this->datasis->fprox_numero('nsmov');
			$ff    = str_replace('-','',$fecha);
			$udia  = days_in_month(substr($ff,0,4),substr($ff,4,2));

			$data=array();
			$data['cod_cli']  = 'RETED';
			$data['nombre']   = 'RETENCION I.S.L.R. TDC/BANCOS';
			$data['tipo_doc'] = 'ND';
			$data['numero']   = $nccli;
			$data['fecha']    = $fecha;
			$data['monto']    = $islr;
			$data['impuesto'] = 0;
			$data['vence']    = substr($ff,0,6).$udia;
			$data['tipo_ref'] = 'DC';
			$data['num_ref']  = '';
			$data['observa1'] = 'RET/ISLR TC POR DEP '.$infbanc[$recibe]['banco'];
			$data['observa2'] = '';
			$data['control']  = $nsmov;
			$data['transac']  = $transac;
			$data['usuario']  = $usuario;
			$data['estampa']  = $estampa;
			$data['hora']     = $hora;
			$sql=$this->db->insert_string('smov', $data);
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'bcaj'); $error++; }
		}

		logusu('bcaj',"Transferencia de caja $numero creada");
		return ($error==0) ? true : false;
	}


	//Metodo para las tranferencias por deposito
	function _transferendep($fecha,$tarjeta,$tdebito,$cheque,$efectivo,$comision,$islr,$envia,$recibe,$moneda='Bs'){
		if($monto<=0) return true;
		$numero  = $this->datasis->fprox_numero('nbcaj');
		$transac = $this->datasis->fprox_numero('ntransa');
		$numeroe = $this->datasis->banprox($envia);
		$numeror = $this->datasis->banprox($recibe);
		$sp_fecha= str_replace('-','',$fecha);
		$error   = 0;
		$this->bcajnumero=$numero;

		$mSQL='SELECT codbanc,numcuent,tbanco,banco,saldo FROM banc WHERE codbanc IN ('.$this->db->escape($envia).','.$this->db->escape($recibe).')';
		$query = $this->db->query($mSQL);
		$infbanc=array();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$infbanc[$row->codbanc]['numcuent']=$row->numcuent;
				$infbanc[$row->codbanc]['tbanco']  =$row->tbanco;
				$infbanc[$row->codbanc]['banco']   =$row->banco;
				$infbanc[$row->codbanc]['saldo']   =$row->banco;
			}
		}

		$monto = $tarjeta+$tdebito+$cheques-$comision-$islr;
		$data=array(
			'tipo'    => 'DE',
			'fecha'   => $fecha,
			'numero'  => $numero,
			'transac' => $transac,
			'usuario' => $this->session->userdata('usuario'),
			'envia'   => $edit->envia->newValue,
			'tipoe'   => 'ND',
			'numeroe' => $numeroe,
			'bancoe'  => $infbanc[$envia]['banco'],
			'recibe'  => $edit->recibe->newValue,
			'tipor'   => 'DE',
			'numeror' => $numeror,
			'bancor'  => $infbanc[$recibe]['banco'],
			'concepto'=> 'DEPOSITO ENTRE '.$envia.' A '.$recibe,
			'concep2' => '',
			'benefi'  => '',
			'boleta'  => '',
			'precinto'=> '',
			'comprob' => '',
			'totcant' => '',
			'status'  => '',
			'deldia'  => $fecha,
			'tarjeta' => $edit->tarjeta->newValue,
			'tdebito' => $edit->tdebito->newValue,
			'cheques' => $edit->cheques->newValue,
			'efectivo'=> $edit->efectivo->newValue,
			'comision'=> $edit->comision->newValue,
			'islr'    => $edit->islr->newValue,
			'monto'   => $monto,
			'estampa' => date('Ymd'),
			'hora'    => date('H:i:s'),
		);

		$sql=$this->db->insert_string('bcaj', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		//Crea el egreso en el banco
		$mSQL='CALL sp_actusal('.$this->db->escape($envia).",'$sp_fecha',-$monto)";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		$data=array();
		$data['codbanc']  = $envia;
		$data['numcuent'] = $infbanc[$envia]['numcuent'];
		$data['banco']    = $infbanc[$envia]['banco'];
		$data['saldo']    = $infbanc[$envia]['saldo'];
		$data['tipo_op']  = 'ND';
		$data['numero']   = $numeroe;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'CAJAS';
		$data['monto']    = $monto;
		$data['concepto'] = 'DEPOSITO ENTRE CAJA '.$envia.' A '.$recibe;
		$data['concep2']  = '';
		$data['transac']  = $transac;
		$data['usuario']  = $this->session->userdata('usuario');
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$data['benefi']   = '-';
		$data['moneda']   = $moneda;
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		//Crea el ingreso la otra caja
		$mSQL='CALL sp_actusal('.$this->db->escape($recibe).",'$sp_fecha',$monto)";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		$data=array();
		$data['codbanc']  = $recibe;
		$data['numcuent'] = $infbanc[$recibe]['numcuent'];
		$data['banco']    = $infbanc[$recibe]['banco'];
		$data['saldo']    = $infbanc[$recibe]['saldo'];
		$data['tipo_op']  = 'DE';
		$data['numero']   = $numeror;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'CAJAS';
		$data['monto']    = $monto;
		$data['concepto'] = 'DEPOSITO ENTRE CAJA '.$envia.' A '.$recibe;
		$data['concep2']  = '';
		$data['transac']  = $transac;
		$data['usuario']  = $this->session->userdata('usuario');
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$data['benefi']   = '-';
		$data['moneda']   = $moneda;
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }
		return ($error==0) ? true : false;
	}

	function _montoautotranf($caja,$fecha){
		$dbfecha=$this->db->escape($fecha);
		$dbcaja =$this->db->escape($caja);
		$mSQL="SELECT SUM(if(tipo_op IN ('NC','DE'),1,-1)*monto) AS monto FROM bmov WHERE codbanc=$dbcaja AND fecha=$dbfecha AND anulado='N'";
		$monto=$this->datasis->dameval($mSQL);
		return (empty($monto))? 0 : $monto;
	}

	function chnumeror($numero){
		$dbcodban=$this->db->escape($this->input->post('recibe'));
		$tipo=$this->datasis->dameval("SELECT tbanco FROM banc WHERE codbanc=$dbcodban");

		if($tipo!='CAJ' && empty($numero)){
			$this->validation->set_message('chnumeror', 'Cuando el que recibe es un banco es obligatorio el n&uacute;mero de deposito');
			return false;
		}else{
			return true;
		}
	}

	function chnumeroe($numero){
		$dbcodban=$this->db->escape($this->input->post('envia'));
		$tipo=$this->datasis->dameval("SELECT tbanco FROM banc WHERE codbanc=$dbcodban");

		if($tipo!='CAJ' && empty($numero)){
			$this->validation->set_message('chnumeroe', 'Cuando el que env&iacute;a es un banco es obligatorio el n&uacute;mero de deposito');
			return false;
		}else{
			return true;
		}
	}

	function chtipoe($tipoe){
		$eenvia = $this->input->post('envia');
		$envia  = $this->_traetipo($eenvia);

		if($envia=='CAJ' && $tipoe!='ND'){
			$this->validation->set_message('chtipoe', 'Cuando el que env&iacute;a es una caja la emisi&oacute;n debe ser por nota de d&eacute;bito');
			return false;
		}else{
			return true;
		}
	}

	function chtotal($monto){
		$monto =0;
		$monto+=floatval($this->input->post('efectivo'));
		$monto+=floatval($this->input->post('tarjeta' ));
		$monto+=floatval($this->input->post('gastos'  ));
		$monto+=floatval($this->input->post('valores' ));

		if($monto>0){
			return true;
		}else{
			$this->validation->set_message('chtotal', 'No puede guardar una transferencia en 0');
			return false;
		}
	}

	//Transferencia entre cajas
	function tranferencaj(){
		$this->rapyd->load('dataform');
		$desca='CONCAT_WS(\'-\',codbanc,banco) AS desca';

		$edit = new DataForm('finanzas/bcaj/tranferencaj/process');
		$edit->title='Transferencia entre cajas';

		$edit->back_url = site_url('finanzas/bcaj/index');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'chfecha|required';
		$edit->fecha->dbformat='Y-m-d';
		$edit->fecha->size=10;

		$edit->envia = new dropdownField('Envia','envia');
		$edit->envia->option('','Seleccionar');
		$edit->envia->options("SELECT  codbanc,$desca FROM banc WHERE tbanco='CAJ'");
		$edit->envia->style = 'width:180px';
		$edit->envia->rule  = 'required';

		$edit->recibe = new dropdownField('Recibe','recibe');
		$edit->recibe->option('','Seleccionar');
		$edit->recibe->options("SELECT  codbanc,$desca FROM banc WHERE tbanco='CAJ'");
		$edit->recibe->style = 'width:180px';
		$edit->recibe->rule  = 'required';

		$edit->monto = new inputField('Monto', 'monto');
		$edit->monto->css_class='inputnum';
		$edit->monto->rule='trim|numeric|required';
		$edit->monto->maxlength =15;
		$edit->monto->size = 20;
		$edit->monto->autocomplete=false;

		$edit->submit('btnsubmit','Guardar');
		$edit->build_form();
		$salida=$edit->output;

		if ($edit->on_success()){
			$fecha  = $edit->fecha->newValue;
			$monto  = $edit->monto->newValue;
			$envia  = $edit->envia->newValue;
			$recibe = $edit->recibe->newValue;
			$rt=$this->_transferencaj($fecha,$monto,$envia,$recibe);
			if($rt){
				redirect('finanzas/bcaj/listo/n/'.$this->bcajnumero);
			}else{
				redirect('finanzas/bcaj/listo/s');
			}
		}

		$data=array();
		$this->rapyd->jquery[]='$(".inputnum").numeric(".");';
		$data['content'] = $salida;
		$data['title']   = '<h1>Transferencias entre cajas</h1>';
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function _formato($numero){
		$dbnumero=$this->db->escape($numero);
		$mSQL  = "SELECT a.tipo,a.tipoe,a.tipor,TRIM(b.tbanco) AS envia, TRIM(c.tbanco) AS recibe FROM bcaj AS a JOIN banc AS b ON a.envia=b.codbanc JOIN banc AS c ON a.recibe=c.codbanc WHERE a.numero = $dbnumero";

		$query = $this->db->query($mSQL);
		$row   = $query->first_row();
		if ($query->num_rows() > 0){
			if($row->tipo=='DE'){
				$formato='BANCAJA';
			}elseif($row->tipo=='TR' && $row->recibe=='CAJ' && $row->envia=='CAJ'){
				$formato='BTRANCJ';
			}elseif($row->recibe!='CAJ' && $row->envia!='CAJ' && $row->tipoe=='ND' && $row->tipor=='NC'){
				$formato='BTRANND';
			}elseif($row->recibe!='CAJ' && $row->envia!='CAJ' && $row->tipoe=='CH' && $row->tipor=='DE'){
				$formato='BTRANCH';
			}
			return $formato;
		}
		return '';
	}

	function _imprimir($numero,$tipo){
		//Deposito BANCAJA
		//Transferencia entre cajas BTRANCJ
		//Transferencia con ND BTRANND
		//Transferencia con cheque BTRANCH
		$formato=$this->_formato($numero);
		return (!empty($formato))? site_url('formatos/'.$tipo.'/'.$formato.'/'.$numero) : '';
	}

	function listo($error, $numero=null){
		if($error=='n'){
			$data['content'] = 'Transacci&oacute;n completada ';
			if(!empty($numero)){
				$url=$this->_imprimir($numero,'ver');
				//$data['content'] .= ', puede <a href="#" onclick="fordi.print();">imprimirla</a>';
				$data['content'] .= ' '.anchor('finanzas/bcaj/agregar','Regresar');
				$data['content'] .= br()."<iframe name='fordi' src ='$url' width='10' height='10' style='display:none;'><p>Tu navegador no soporta iframes.</p></iframe>";
			}else{
				$data['content'] .= anchor('finanzas/bcaj/index','Regresar');
			}
		}else{
			$data['content'] = 'Lo siento pero hubo alg&uacute;n error en la transacci&oacute;n, se genero un centinela '.anchor('finanzas/bcaj/index','Regresar');
		}
		$data['title']   = '<h1>Transferencias entre cajas</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function get_trrecibe(){
		$codigo=$this->input->post('envia');
		echo "<option value=''>Seleccionar</option>";

		if($codigo!==false){
			$tipo= $this->_traetipo($codigo);

			if(!empty($tipo)){
				$ww=($tipo=='CAJ') ? 'AND tbanco="CAJ"' : '';
				$desca='CONCAT_WS(\'-\',codbanc,banco) AS desca';
				$mSQL=$this->db->query("SELECT codbanc,$desca FROM banc WHERE codbanc<>".$this->db->escape($codigo)." $ww ORDER BY banco");
				if($mSQL){
					foreach($mSQL->result() AS $fila )
						echo "<option value='".$fila->codbanc."'>".$fila->desca."</option>";
				}
			}
		}
	}

	function _traetipo($codigo){
		$sql='SELECT tbanco FROM banc WHERE codbanc='.$this->db->escape($codigo);
		return $this->datasis->dameval($sql);
	}

	// forma de cierre de deposito
	function formacierre(){
		$id  = $this->uri->segment($this->uri->total_segments());
		$reg = $this->datasis->damereg("SELECT a.numero, a.fecha, a.monto, a.codbanc, a.envia, b.banco, a.efectivo, a.cheques FROM bcaj a JOIN banc b ON a.codbanc=b.codbanc WHERE a.id=$id");

		if ( $reg['cheques'] > 0 ) {

		$salida = '
<script type="text/javascript">
	jQuery("#aceptados").jqGrid({
		datatype: "local",
		height: 190,
		colNames:["id","Banco","Numero","Cuenta", "Monto"],
		colModel:[
			{name:"id",     index:"id",     width:10, hidden:true},
			{name:"banco",  index:"banco",  width:40},
			{name:"numero", index:"numero", width:90},
			{name:"cuenta", index:"cuenta", width:150},
			{name:"monto",  index:"monto",  width:80, align:"right"},
		],
		multiselect: true,
		onSelectRow: sumadepo,
		onSelectAll: sumadepo
	});

	
	var mcheques = [
';
		$mSQL = "SELECT id, banco, num_ref, cuentach, monto FROM sfpa WHERE deposito='".$reg['numero']."'";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0 ){
			foreach( $query->result() as $row ){
				$salida .= '{id:"'.$row->id.'",';
				$salida .= 'banco:"'.$row->banco.'",';
				$salida .= 'numero:"'.$row->num_ref.'",';
				$salida .= 'cuenta:"'.$row->cuentach.'",';
				$salida .= 'monto:"'.$row->monto.'"},';
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
				total += Number(entirerow["monto"]);
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
	<h1 style="text-align:center">Cierre de Deposito Nro. '.$reg['numero'].'</h1>
	<p style="text-align:center;font-size:12px;">Fecha: '.$reg['fecha'].' Banco: '.$reg['codbanc'].' '.$reg['banco'].'</p>
	<form id="cierreforma">	
	<table width="80%" align="center"><tr>
		<td  class="CaptionTD" align="right">Numero</td>
		<td><input type="text" name="fdeposito" id="fdeposito" class="text ui-widget-content ui-corner-all" maxlengh="12" size="12" value="" /></td>
		<td  class="CaptionTD"  align="right">Fecha</td>
		<td>&nbsp;<input name="ffecha" id="ffecha" type="text" value="'.date('d/m/Y').'" maxlengh="10" size="10"  /></td>
	</tr></table>
	<input id="fmonto"   name="fmonto"   type="hidden">
	<input id="fsele"    name="fsele"    type="hidden">
	<input id="fnumbcaj" name="fnumbcaj" type="hidden" value="'.$reg['numero'].'">
	<input id="fid"      name="fid"      type="hidden" value="'.$id.'">
	<input id="ftipo"    name="ftipo"    type="hidden" value="C">
	</form>
	<br>
	<center><table id="aceptados"><table></center>
	<table width="80%">
	<td>Monto en Transito: <div style="font-size:20px;font-weight:bold">'.nformat($reg['monto']).'</div></td><td>
	Depositado:<div id="grantotal" style="font-size:20px;font-weight:bold">0.00</div>
	</td></table>
	
	';
	
		} elseif ( $reg['efectivo'] > 0) {
//////////////////////////////////////////////////////////////////
//           DEPOSITOS EFECTIVO
//
		$salida = '
<script type="text/javascript">
	$("#ffecha").datepicker({dateFormat:"dd/mm/yy"});
</script>
	<p class="validateTips"></p>
	<h1 style="text-align:center">Cierre de Deposito Nro. '.$reg['numero'].'</h1>
	<p style="text-align:center;font-size:12px;">Fecha: '.$reg['fecha'].' Banco: '.$reg['codbanc'].' '.$reg['banco'].'</p>
	<form id="cierreforma">	
	<table width="80%" align="center">
	<tr>
		<td  class="CaptionTD" align="right">Numero</td>
		<td><input type="text" name="fdeposito" id="fdeposito" class="text ui-widget-content ui-corner-all" maxlengh="12" size="12" value="" /></td>
		<td  class="CaptionTD"  align="right">Fecha</td>
		<td>&nbsp;<input name="ffecha" id="ffecha" type="text" value="'.date('d/m/Y').'" maxlengh="10" size="10"  /></td>
	</tr>
	</table>
	<input id="fmonto"   name="fmonto"   type="hidden" value="'.$reg['monto'].'">
	<input id="fnumbcaj" name="fnumbcaj" type="hidden" value="'.$reg['numero'].'">
	<input id="fsele"    name="fsele"    type="hidden" valus="">
	<input id="fid"      name="fid"      type="hidden" value="'.$id.'">
	<input id="ftipo"    name="ftipo"    type="hidden" value="E">
	</form>
	<br>
	<table width="80%">
		<tr>
		<td align="right">Monto en Efectivo: </td><td style="font-size:20px;font-weight:bold">'.nformat($reg['monto']).'</td>
		</tr>
	</table>
	
	';

		}
		echo $salida;

	}
	
	
}

?>
