<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
include('common.php');
class gser extends Controller {

	var $mModulo = 'GSER';
	var $titp    = 'Gastos y Egresos';
	var $tits    = 'Gastos y Egresos';
	var $url     = 'finanzas/gser/';
	var $genesal = true;
	var $solo    = false;

	function Gser(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->mcred = '_CR';
		$this->load->library('pi18n');
		$this->datasis->modulo_nombre( 'GSER', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 990, 700, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	//   Layout en la Ventana
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$grid->setHeight('265');
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$grid->setHeight('170');
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 220, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		$WpAdic = "
		<tr><td><div class=\"anexos\"><table id=\"bpos1\"></table></div><div id='pbpos1'></div></td></tr>\n
		<tr><td><div class=\"anexos\">
			<table cellpadding='0' cellspacing='0' style='width:95%;' align='center'>
				<tr>
					<td style='vertical-align:center;border:1px solid #AFAFAF;'><div class='botones'>".img(array('src' =>"assets/default/images/print.png", 'height'=>18, 'alt'=>'Imprimir','title'=>'Imprimir', 'border'=>'0'))."</div></td>
					<td style='vertical-align:top;text-align:center;'><div class='botones'><a style='width:78px;text-align:left;vertical-align:top;' href='#' id='imprimir'>Egreso</a></div></td>
					<td style='vertical-align:top;text-align:center;'><div class='botones'><a style='width:78px;text-align:left;vertical-align:top;text-align:center;' href='#' id='princheque'>Cheque</a></div></td>
				</tr>
				<tr>
					<td style='vertical-align:center;border:1px solid #AFAFAF;'><div class='botones'>".img(array('src' =>"assets/default/images/print.png",  'height'=>18, 'alt'=>'Imprimir', 'title'=>'Imprimir', 'border'=>'0'))."</div></td>
					<td style='vertical-align:top;text-align:center;'><div class='botones'><a style='width:78px;text-align:left;vertical-align:top;' href='#' id='reteprint'>R.I.V.A.</a></div></td>
					<td style='vertical-align:top;text-align:center;'><div class='botones'><a style='width:78px;text-align:left;vertical-align:top;' href='#' id='reteislrprint'>R.I.S.L.R.</a></div></td>
				</tr>
			</table>
			</div>
		</td></tr>\n
		";

		$grid->setWpAdicional($WpAdic);

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'creamga', 'img'=>'images/agrega4.png' , 'alt' => 'Crear concepto de gasto', 'label'=>'Crear concepto de gasto', 'tema'=>'proteo' ));
		$grid->wbotonadd(array('id'=>'creaprv', 'img'=>'images/agrega4.png' , 'alt' => 'Crear proveedor'        , 'label'=>'Crear Proveedor',  'tema'=>'proteo'   ));
		$WestPanel = $grid->deploywestp();


		//Panel Central
		$centerpanel = $grid->centerpanel( $id = "radicional", $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'fgasto', 'title'=>'Agregar/Editar Gasto/Egreso'),
			array('id'=>'fshow' , 'title'=>'Mostrar registro'),
			array('id'=>'fimpri', 'title'=>'Imprimir Gasto/Egreso'),
			array('id'=>'fborra', 'title'=>'Eliminar Registro'),
			array('id'=>'fsprv' , 'title'=>'Agregar Proveedor')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '
		function ltransac(el, val, opts){
			var link=\'<div><a href="#" onclick="tconsulta(\'+"\'"+el+"\'"+\');">\' +el+ \'</a></div>\';
			return link;
		};';

		//$param['EastPanel']  = $EastPanel;

		$param['WestPanel']    = $WestPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('GSER', 'JQ');
		$param['otros']        = $this->datasis->otros('GSER', 'JQ');
		$param['centerpanel']  = $centerpanel;
		$param['funciones']    = $funciones;
		$param['temas']        = array('proteo','darkness','anexos');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;

		//$param['funciones']    = $funciones;

		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	//Funciones de los Botones
	//
	function bodyscript( $grid0, $grid1 ){
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
		function gseradd() {
			$.post("'.site_url('finanzas/gser/solo/create').'",
			function(data){
				$("#fgasto").html(data);
				$("#fgasto").dialog({height: 570, width: 880, title: "Agregar Gasto/Egreso"});
				$( "#fgasto" ).dialog( "open" );
			})
		};';

		$bodyscript .= '
		function gseredit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url('finanzas/gser/solo/modify').'/"+id, function(data){
					$("#fgasto").html(data);
					$("#fgasto").dialog({height: 570, width: 880, title: "Agregar Egreso"});
					$("#fgasto").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Gasto</h1>");}
		};';

		$bodyscript .= '
		function gsershow() {
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				$.post("'.site_url($this->url.'dataedit/show').'/"+id,
					function(data){
						$("#fshow").html(data);
						$("#fshow").dialog( "open" );
					});
			} else {
				$.prompt("<h1>Por favor Seleccione un gasto</h1>");
			}
		};';

		$bodyscript .= '
		function gserdel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						try{
							var json = JSON.parse(data);
							if (json.status == "A"){
								apprise("Gasto eliminado");
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
		jQuery("#princheque").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url($this->url.'/impcheque').'/\'+id, \'_blank\', \'width=300,height=400,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-200), screeny=((screen.availWidth/2)-150)\');
			} else { $.prompt("<h1>Por favor Seleccione una Egreso</h1>");}
		});';

		//Agrgaga mgas
		$bodyscript .= '
		jQuery("#creamga").click(
			function(){
				$.post("'.site_url('finanzas/mgas/dataedit/create').'",
				function(data){
					$("#fsprv").html(data);
					$("#fsprv").dialog({height: 500, width: 700, title: "Agregar Gasto"});
					$("#fsprv").dialog( "open" );
				});
		});';


		// Agregar Proveedor
		$bodyscript .= '
		jQuery("#creaprv").click(
			function(){
			$.post("'.site_url('compras/sprv/dataedit/create').'",
			function(data){
				$("#fsprv").html(data);
				$("#fsprv").dialog({height: 500, width: 720, title: "Agregar Proveedor"});
				$("#fsprv").dialog( "open" );
			})
		});';

		$bodyscript .= '
		jQuery("#agregar").click( function(){
			window.open(\''.site_url('finanzas/gser/dataedit/create').'\', \'_blank\', \'width=900,height=700,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
		});';

		$bodyscript .= '
		jQuery("#modifica").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url('finanzas/gser/dataedit/modify').'/\'+id, \'_blank\', \'width=900,height=700,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
			} else { $.prompt("<h1>Por favor Seleccione un Gasto</h1>");}
		});';


		$bodyscript.= '
		jQuery("#imprimir").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url('formatos/ver/GSER/').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
			} else { $.prompt("<h1>Por favor Seleccione una gasto</h1>");}
		});';

		//Imprimir retencion
		$bodyscript .= '
		jQuery("#reteprint").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				if(Number(ret.reteiva) > 0){
					window.open(\''.site_url($this->url.'printrete').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
				}else{
					$.prompt("<h1>El gasto seleccionado no tiene retenci&oacute;n de iva</h1>");
				}
			} else {
				$.prompt("<h1>Por favor Seleccione un gasto</h1>");
			}
		});';

		//Imprime retencion islr
		$bodyscript .= '
		jQuery("#reteislrprint").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id){
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				if(Number(ret.reten) > 0){
					window.open(\''.site_url('formatos/ver/GSERRT/').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
				}else{
					$.prompt("<h1>El gasto seleccionado no tiene retenci&oacute;n ISLR</h1>");
				}
			} else { $.prompt("<h1>Por favor Seleccione un gasto</h1>");}
		});';

		$bodyscript .= '
		jQuery("#agregar").click( function(){
			window.open(\''.site_url('finanzas/gser/dataedit/create').'\', \'_blank\', \'width=900,height=700,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
		});';


		$bodyscript .= '
			$("#fshow").dialog({
				autoOpen: false, height: 500, width: 900, modal: true,
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
			$("#fgasto").dialog({
				autoOpen: false, height: 450, width: 900, modal: true,
				buttons: {
					"Guardar": function() {
						var bValid = true;
						var murl = $("#df1").attr("action");
						allFields.removeClass( "ui-state-error" );
						if ( bValid ) {
							$.ajax({
								type: "POST", dataType: "html", async: false,
								url: murl,
								data: $("#df1").serialize(),
								success: function(r,s,x){
									try{
										var json = JSON.parse(r);
										if (json.status == "A"){
											$("#fgasto").dialog( "close" );
											grid.trigger("reloadGrid");
											'.$this->datasis->jwinopen(site_url('formatos/ver/GSER').'/\'+json.pk.id+\'/id\'').';
											return true;
										}else{
											apprise(json.mensaje);
										}
									}catch(e){
										$("#fgasto").html(r);
									}

								}
							});
						}
					},
					Cancelar: function() {
						$(this).dialog( "close" );
						$("#fgasto").html("");
					}
				},
				close: function() {
					allFields.val("").removeClass( "ui-state-error" );
					$("#fgasto").html("");
				}
			});';

		$bodyscript .= '
			$("#fsprv").dialog({
				autoOpen: false, height: 450, width: 900, modal: true,
				buttons: {
					"Guardar": function() {
						var bValid = true;
						var murl = $("#df1").attr("action");
						allFields.removeClass( "ui-state-error" );
						if ( bValid ) {
							$.ajax({
								type: "POST", dataType: "html", async: false,
								url: murl,
								data: $("#df1").serialize(),
								success: function(r,s,x){
									try{
										var json = JSON.parse(r);
										if (json.status == "A"){
											$("#fsprv").dialog( "close" );
											grid.trigger("reloadGrid");
											return true;
										}else{
											apprise(json.mensaje);
										}
									}catch(e){
										$("#fsprv").html(r);
									}

								}
							});
						}
					},
					Cancelar: function() {
						$(this).dialog( "close" );
						$("#fsprv").html("");
					}
				},
				close: function() {
					allFields.val("").removeClass( "ui-state-error" );
					$("#fsprv").html("");
				}
			});';

		$bodyscript .= '
			$("#fimpri").dialog({
				autoOpen: false, height: 590, width: 950, modal: true,
				buttons: {
					"R. IVA": function() {


					},
					"R. ISLR": function() {


					},
					"Cancelar": function() {
						$(this).dialog( "close" );
						$("#fimpri").html("");
					}
				},
				close: function() {
					allFields.val("").removeClass( "ui-state-error" );
					$("#fimpri").html("");
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

		$grid->addField('tipo_doc');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'center'",
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
		));

		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));

		$grid->addField('proveed');
		$grid->label('Prov.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));

		$grid->addField('nombre');
		$grid->label('Nombre del Proveedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));

		$grid->addField('vence');
		$grid->label('Vence');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('totpre');
		$grid->label('Base');
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

		$grid->addField('totiva');
		$grid->label('IVA');
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


		$grid->addField('totbruto');
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


		$grid->addField('reten');
		$grid->label('R.ISLR');
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

		$grid->addField('reteiva');
		$grid->label('R.IVA');
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

		$grid->addField('totneto');
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

		$grid->addField('tipo1');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));

		$grid->addField('codb1');
		$grid->label('Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
		));


		$grid->addField('cheque1');
		$grid->label('N.Cheque');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));

		$grid->addField('comprob1');
		$grid->label('Comprobante');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 6 }',
		));

		$grid->addField('credito');
		$grid->label('Cr&eacute;dito');
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


		$grid->addField('orden');
		$grid->label('Orden');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));


		$grid->addField('anticipo');
		$grid->label('Anticipo');
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


		$grid->addField('benefi');
		$grid->label('Beneficiario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
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


		$grid->addField('transac');
		$grid->label('Transaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
			'formatter'     => 'ltransac'
		));


		$grid->addField('preten');
		$grid->label('% Retenci&oacute;n');
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


		$grid->addField('creten');
		$grid->label('Cod.Retenci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 4 }',
		));


		$grid->addField('breten');
		$grid->label('Base.Retenci&oacute;n');
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


		$grid->addField('nfiscal');
		$grid->label('N.Fiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
			'formoptions'   => '{ label:"Nro Fiscal" }'
		));

		$grid->addField('cajachi');
		$grid->label('C.Chica');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'center'",
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));

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


		$grid->addField('tasa');
		$grid->label('Tasa G.');
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
		$grid->label('Tasa R.');
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
		$grid->label('Tasa A.');
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

		$grid->addField('serie');
		$grid->label('Serie');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 30 }',
			'formoptions'   => '{ label:"Nro. de Factura" }'
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


		$grid->addField('retesimple');
		$grid->label('Rete.Simple');
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


		$grid->addField('negreso');
		$grid->label('N.Egreso');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));


		$grid->addField('ncausado');
		$grid->label('N.Causado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
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


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('260');
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
			},afterInsertRow:
			function( rid, aData, rowe){
				if ( aData.tipo_doc == "XX" ){
					$(this).jqGrid( "setCell", rid, "tipo_doc","", {color:"#FFFFFF", background:"#C90623" });
				}
			}'
		);

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 400, height:250, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 400, height:250, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('GSER','INCLUIR%' ));
		$grid->setEdit(   false);
		$grid->setDelete( $this->datasis->sidapuede('GSER','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('GSER','BUSQUEDA%'));

		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		//$grid->setBarOptions("\t\taddfunc: gseradd,\n\t\teditfunc: gseredit");
		$grid->setBarOptions('addfunc: gseradd, viewfunc: gsershow, delfunc: gserdel');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdata/'));

		if($deployed){
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
		$mWHERE = $grid->geneTopWhere('gser');

		$response   = $grid->getData('gser', array(array()), array(), false, $mWHERE, 'id', 'desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = intval($this->input->post('id'));
		$data   = $_POST;

		if($id>0){
			if(isset($data['oper'])) unset($data['oper']);
			if(isset($data['id']))   unset($data['id']);

			if($oper == 'add'){
				echo 'Deshabilitado.';
				return false;
			}elseif($oper == 'edit'){
				if(!$this->datasis->sidapuede('GSER','INCLUIR%')){
					echo 'No tiene acceso a modificar';
					return false;
				}

				$posibles=array('nfiscal','serie');
				foreach($data as $ind=>$val){
					if(!in_array($ind,$posibles)){
						echo 'Campo no permitido ('.$ind.')';
						return false;
					}
				}

				$dbid=$this->db->escape($id);

				$row = $this->datasis->damerow("SELECT fecha,tipo_doc, numero, proveed,transac,cajachi FROM gser WHERE id=${dbid}");
				if(!empty($row)){
					$data['numero'] = substr($data['serie'],-8);
					$transac = $row['transac'];
					if($row['cajachi']=='S'){
						echo 'No se puede modificar un gasto de cajachica';
						return false;
					}

					if($row['tipo_doc']!='FC'){
						echo 'Solo se le pueden cambiar los valores a las facturas';
						return false;
					}

					if($data['numero'] != $row['numero']){
						//Chequea si puede cambiar los valores
						$this->db->from('gser');
						$this->db->where('id <>'   ,$id);
						$this->db->where('fecha'   ,$row['fecha']);
						$this->db->where('tipo_doc',$row['tipo_doc']);
						$this->db->where('numero'  ,$data['numero']);
						$this->db->where('proveed' ,$row['proveed']);
						$cana = $this->db->count_all_results();
						if(!empty($cana)){
							echo 'Ya existe un registro con el mismo numero.';
							return false;
						}
					}

					//Cambia el gasto
					$this->db->where('id'   ,$id);
					$this->db->update('gser',$data);

					//Cambia el detalle
					$this->db->where('idgser' ,$id);
					$this->db->update('gitser',array('numero'=>$data['numero']));

					if($data['numero'] != $row['numero']){
						//Cambia la retencion ISLR
						$this->db->where('idd'     ,$id);
						$this->db->where('origen'  ,'GSER');
						$this->db->update('gereten',array('numero'=>$data['numero']));

						//Cambia las aplicaciones
						$this->db->where('numero'  ,$row['numero']);
						$this->db->where('tipo_doc',$row['tipo_doc']);
						$this->db->where('cod_prv' ,$row['proveed']);
						$this->db->update('itppro' ,array('numero'=>$data['numero']));
					}

					//Cambia la retencion de IVA
					$this->db->where('transac',$transac);
					$this->db->update('riva'  ,array('numero'=>$data['numero'],'nfiscal'=>$data['nfiscal']));

					//Cambia la CxC
					$this->db->where('transac' ,$transac);
					$this->db->where('tipo_doc',$row['tipo_doc']);
					$this->db->where('fecha'   ,$row['fecha']);
					$this->db->where('cod_prv' ,$row['proveed']);
					$this->db->update('sprm'   ,array('numero'=>$data['numero'],'nfiscal'=>$data['nfiscal']));

					logusu('GSER','Gasto/Egreso '.$row['fecha'].'-'.$row['tipo_doc'].'-'.$row['numero'].'-'.$row['proveed'].' MODIFICADO');
					echo 'Gasto Modificado';
					return true;
				}else{
					echo 'Registro no encontrado';
					return false;
				}

			} elseif($oper == 'del') {
				echo 'Deshabilitado.';
				return false;
			}
		}else{
			echo 'Id no valido';
			return false;
		}
	}


	//******************************************************************
	//  Definicion del Grid y la Forma
	//
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));


		$grid->addField('proveed');
		$grid->label('Proveedor');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{'.$grid->autocomplete(site_url('ajax/automgas'), 'codigo','ncodigo','<div id=\"ncodigo\"><b>"+ui.item.label+"</b></div>').', size:10}',
			//'editoptions'   => '{ size:30, maxlength: 6 }',
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 50 }',
		));


		$grid->addField('precio');
		$grid->label('Precio');
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


		$grid->addField('iva');
		$grid->label('IVA');
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

		$grid->addField('departa');
		$grid->label('Departamento');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
		));


		$grid->addField('feprox');
		$grid->label('Feprox');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


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


		$grid->addField('tasa');
		$grid->label('Tasa G.');
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
		$grid->label('Tasa R.');
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
		$grid->label('Tasa A.');
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

		$grid->addField('R. ICA');
		$grid->label('Reteica');
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

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('170');
		//$grid->setTitle($this->titp);
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(30);
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

	/**
	* Busca la data en el Servidor por json
	*/
	function getdatait(){
		$id = $this->uri->segment(4);
		if ($id === false ){
			$id = $this->datasis->dameval('SELECT MAX(id) AS id FROM gser');
		}
		if(empty($id)) return '';
		$dbid = $this->db->escape(intval($id));

		$row = $this->datasis->damerow('SELECT proveed,numero,fecha,transac FROM gser WHERE id='.$dbid);

		if(!empty($row)){
			$proveed = $this->db->escape($row['proveed']);
			$numero  = $this->db->escape($row['numero']);
			$fecha   = $this->db->escape($row['fecha']);
			$transac = $this->db->escape($row['transac']);
		}else{
			return '';
		}

		$orderby= '';
		$sidx=$this->input->post('sidx');
		if($sidx){
			$campos = $this->db->list_fields('gitser');
			if(in_array($sidx,$campos)){
				$sidx = trim($sidx);
				$sord   = $this->input->post('sord');
				$orderby="ORDER BY `${sidx}` ".(($sord=='asc')? 'ASC':'DESC');
			}
		}

		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM gitser WHERE numero=${numero} AND proveed={$proveed} AND fecha=${fecha} AND transac=${transac} ${orderby}";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setDatait(){
		$this->load->library('jqdatagrid');
		$oper = $this->input->post('oper');
		$id   = intval($this->input->post('id'));
		$data = $_POST;

		if($id>0){
			if(isset($data['oper'])) unset($data['oper']);
			if(isset($data['id']))   unset($data['id']);

			if($oper == 'add'){
				echo 'Deshabilitado';
			}elseif($oper == 'edit'){
				if(!$this->datasis->sidapuede('GSER','MODIFICA%')){
					echo 'No tiene acceso a modificar';
					return false;
				}

				$posibles=array('descrip','codigo');
				foreach($data as $ind=>$val){
					if(!in_array($ind,$posibles)){
						echo 'Campo no permitido ('.$ind.')';
						return false;
					}
				}
				if(isset($data['codigo'])){
					$dbcodigo = $this->db->escape($data['codigo']);
					$cana = $this->datasis->dameval("SELECT COUNT(*) AS cana FROM mgas WHERE codigo=${dbcodigo}");
					if($cana==0){
						echo 'Codigo no valido';
						return false;
					}
				}

				$this->db->where('id', $id);
				$this->db->update('gitser', $data);
				logusu('GITSER','Item Modificado '.$id.' MODIFICADO');
				echo "${id} Modificado";
				return true;

			} elseif($oper == 'del') {
				echo 'No esta previsto eliminar items individuales';
				return false;
			}
		}else{
			echo 'Id no valido';
			return false;
		}
	}

	function solo(){
		$this->solo = true;
		$id = $this->uri->segment($this->uri->total_segments());

		//Creando Gasto
		if($id == 'create'){
			$this->dataedit();
		}elseif($id == 'insert'){
			$this->genesal = false;
			$rt = $this->dataedit();
			$rt = str_replace("\n",'<br>',$rt);

			$arr_rt=array('status' =>'','id'=>'','mensaje'=>'');
			if($rt == 'Gasto Guardado'){
				$status='E';
			}else{
				$status='E';
			}
			if(strlen($rt) > 0){
				$arr_rt['status'] = $status;
				$arr_rt['id']     = $id;
				$arr_rt['mensaje']= $rt;
				echo json_encode($arr_rt);
			}
		}elseif($id == 'process'){
			$control = $this->uri->segment($this->uri->total_segments()-1);
			$rt = $this->actualizar($control);
			$rt = str_replace("\n",'<br>',$rt);
			if(strlen($rt[1]) > 0){
				$p = ($rt[0] === false)? 'E' : 'A';
			}
			$arr_rt['status'] = $p;
			$arr_rt['id']     = $control;
			$arr_rt['mensaje']= heading($rt[1]);
			echo json_encode($arr_rt);
		}else{
			$modo = $this->uri->segment($this->uri->total_segments()-1);

			if($modo == 'update') $this->genesal = false;
			$rt = $this->dataedit();

			$rt = str_replace("\n",'<br>',$rt);
			if($rt == 'Gasto Guardado'){
				$status='A';
			}else{
				$status='E';
			}
			if(strlen($rt)>0){
				$arr_rt['status'] = $status;
				$arr_rt['id']     = $id;
				$arr_rt['mensaje']= $rt;
				echo json_encode($arr_rt);
			}
		}
	}

	function gserserie(){
		$serie = $this->uri->segment($this->uri->total_segments());
		$id    = intval($this->uri->segment($this->uri->total_segments()-1));
		if(!empty($serie) && $id>0){
			$dbserie = $this->db->escape($serie);
			$this->db->simple_query("UPDATE gser SET serie=${dbserie} WHERE id=${id}");
			echo ' con exito ';
		}else{
			echo ' NO se guardo ';
		}
		logusu('GSER',"Cambia Nro. Serie ${id} ->  ${serie}");
	}

	function gserfiscal(){
		$serie = $this->uri->segment($this->uri->total_segments());
		$id    = intval($this->uri->segment($this->uri->total_segments()-1));
		if(!empty($serie) && $id>0){
			$dbserie = $this->db->escape($serie);
			$this->db->simple_query("UPDATE gser SET nfiscal=${dbserie} WHERE id=${id}");
			echo " con exito ";
		}else{
			echo " NO se guardo ";
		}
		logusu('GSER',"Cambia Nro. Fiscal ${id} ->  ${serie}");
	}


	function impcheque($id_gser){
		$dbid=$this->db->escape($id_gser);
		$fila=$this->datasis->damerow('SELECT a.codb1,a.cheque1,a.benefi,a.nombre,monto1 ,tipo1 FROM gser AS a JOIN sprv AS b ON a.proveed=b.proveed WHERE a.id='.$dbid);
		$fila['benefi'] = trim($fila['benefi']);
		$fila['nombre'] = trim($fila['nombre']);

		$banco = Common::_traetipo($fila['codb1']);
		$tipo  = trim($fila['tipo1']);

		if($banco!='CAJ' && $tipo=='C'){
			$this->load->library('cheques');
			$nombre = (empty($fila['benefi']))? $fila['nombre']: $fila['benefi'];
			$monto  = $fila['monto1'];
			$fecha  = date('Y-m-d');
			$banco  = $banco;
			$this->cheques->genera($nombre,$monto,$banco,$fecha,true);
		}else{
			if($tipo=='D'){
				echo 'Egreso fue pagado por transferencia bancaria';
			}else{
				echo 'Egreso no fue pagado con cheque de banco';
			}
		}
	}

	function agregar(){
		$data['content'] = '<div align="center" id="maso" >';

		$data['content'].= '<div class="box" style="width:240px;background-color: #F9F7F9;">'.br();
		$data['content'].= '<a href="'.base_url().'finanzas/gser/gserchi"><img border=0 src="'.base_url().'images/cajachica.gif'.'" height="80px"></a>'.br();
		$data['content'].= '<p>Incluir gastos pagados con dinero de caja chica para ser relacionados al cierre y/o reposicion</p>'.br();
		//$data['content'].= anchor('finanzas/gser/gserchi'  ,'Gastos de Caja Chica').br();
		$data['content'].= '</div>'.br();

		$data['content'].= '<div  class="box" style="width:240px;background-color: #F9F7F9;" class="box">'.br();
		$data['content'].= '<a href="'.base_url().'finanzas/gser/cierregserchi">';
		$data['content'].= '<img border=0 src="'.base_url().'images/rendicion.jpg'.'" height="90px"></a>'.br();
		$data['content'].= '<p>Reposicion de caja con las facturas ingresadas</p>'.br();
		//$data['content'].= anchor('finanzas/gser/cierregserchi'  ,'Cerrar Caja Chica').br();
		$data['content'].= '</div>'.br();

		$data['content'].= '</div><center>';

		$data['title']   = heading('Agregar Gastos');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas_masonry', $data);
	}

	//Para Caja chica
	function gserchi(){
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter('Filtro de gastos de cajas chicas','gserchi');
		$select=array('numfac','fechafac','proveedor',
		'tasa + sobretasa + reducida AS totiva',
		'exento + montasa + monadic + monredu AS totneto',
		'exento + montasa + monadic + monredu + tasa + sobretasa + reducida AS total',
		'ngasto');
		$filter->db->select($select);

		$filter->codbanc = new dropdownField('Codigo de la caja','codbanc');
		$filter->codbanc->option('','Todos');
		$filter->codbanc->options("SELECT codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE tbanco='CAJ' ORDER BY codbanc");

		$filter->fechad = new dateonlyField('Fecha desde', 'fechad','d/m/Y');
		$filter->fechah = new dateonlyField('Fecha hasta', 'fechah','d/m/Y');
		$filter->fechad->clause  = $filter->fechah->clause ='where';
		$filter->fechad->db_name = $filter->fechah->db_name='fechafac';
		$filter->fechah->size=$filter->fechad->size=10;
		$filter->fechad->operator='>=';
		$filter->fechah->operator='<=';

		$filter->numero = new inputField('Numero', 'numfac');

		$filter->proveed = new inputField('Proveedor', 'proveedor');
		//$filter->proveed->append($boton);
		$filter->proveed->db_name = 'proveedor';

		$filter->aceptado = new dropdownField('Aceptados','aceptado');
		$filter->aceptado->option('' ,'Todos');
		$filter->aceptado->option('S','Aceptados');
		$filter->aceptado->option('N','No aceptados');
		$filter->aceptado->style = 'width:120px';

		$action = "javascript:window.location='".site_url('finanzas/gser/agregar')."'";
		$filter->button('btn_regresa', 'Regresar', $action, 'TR');

		$filter->buttons('reset','search');
		$filter->build();

		$uri  = anchor('finanzas/gser/datagserchi/show/<#id#>','<#numfac#>');

		function checker($id,$conci,$ngasto){
			if(empty($ngasto)){
				if($conci=='S'){
					return form_checkbox('nn'.$id,$id,true);
				}else{
					return form_checkbox('nn'.$id,$id,false);
				}
			}else{
				return $ngasto;
			}
		}

		$grid = new DataGrid();
		$grid->use_function('checker');
		$grid->order_by('numfac','desc');
		$grid->per_page = 15;
		$grid->column_orderby('Caja','codbanc','caja');
		$grid->column_orderby('Numero',$uri,'numfac');
		$grid->column_orderby('Fecha' ,'<dbdate_to_human><#fechafac#></dbdate_to_human>','fechafac','align=\'center\'');
		$grid->column_orderby('Proveedor','proveedor','proveedor');
		$grid->column_orderby('IVA'   ,'totiva'    ,'totiva'  ,'align=\'right\'');
		$grid->column_orderby('Monto' ,'totneto'   ,'totneto' ,'align=\'right\'');
		$grid->column_orderby('Aceptado','<checker><#id#>|<#aceptado#>|<#ngasto#></checker>','aceptado','align=\'center\'');

		$grid->add('finanzas/gser/datagserchi/create','Agregar nueva factura');
		$grid->build();
		//echo $grid->db->last_query();

		$this->rapyd->jquery[]='$(":checkbox").change(function(){
			name=$(this).attr("name");
			$.post("'.site_url('finanzas/gser/gserchiajax').'",{ id: $(this).val()},
			function(data){
					if(data=="1"){
					return true;
				}else{
					$("input[name=\'"+name+"\']").removeAttr("checked");
					alert("Hubo un error, comuniquese con soporte tecnico: "+data);
					return false;
				}
			});
		});';

		$data['content'] = $filter->output.$grid->output;
		$data['head']    = script('jquery.js');
		$data['head']   .= $this->rapyd->get_head();
		$data['title']   = heading('Agregar/Modificar facturas de Caja Chica');
		$this->load->view('view_ventanas', $data);
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
				$rt='1';
			}
		}
		echo $rt;
	}

	function datagserchi(){
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

		$script = "
		function consulrif(){
			vrif=$('#rif').val();
			if(vrif.length==0){
				alert('Debe introducir primero un RIF');
			}else{
				vrif=vrif.toUpperCase();
				$('#rif').val(vrif);
				window.open('$consulrif'+'?p_rif='+vrif,'CONSULRIF','height=350,width=410');
			}
		}
		";

		$script .= "
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
		";

		$script .= "
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
			$('#importe_val').text(nformat(total));
		}
		";

		$script .= "
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
		";

		$script .= "
		$('#importe_val').css('font-size','2em');
		$('#importe_val').css('font-weight','bold');
		";

		$edit = new DataEdit('Gastos de caja chica', 'gserchi');
		$edit->back_url = site_url('finanzas/gser/gserchi');
		$edit->script($script,'create');
		$edit->script($script,'modify');
		$edit->pre_process('insert' ,'_pre_gserchi');
		$edit->pre_process('update' ,'_pre_gserchi');

		$edit->codbanc = new dropdownField('Codigo de la caja','codbanc');
		$edit->codbanc->option('','Seleccionar');
		$edit->codbanc->options("SELECT codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE tbanco='CAJ' AND activo='S' ORDER BY codbanc");
		$edit->codbanc->rule='max_length[5]|required';

		$edit->fechafac = new dateField('Fecha de la factura','fechafac');
		$edit->fechafac->rule='max_length[10]|required';
		$edit->fechafac->size =12;
		$edit->fechafac->insertValue=date('Y-m-d');
		$edit->fechafac->maxlength =10;

		$edit->numfac = new inputField('Numero de la factura','numfac');
		$edit->numfac->rule='max_length[8]|required';
		$edit->numfac->size =10;
		$edit->numfac->maxlength =8;
		$edit->numfac->autocomplete =false;

		$edit->nfiscal = new inputField('Control fiscal','nfiscal');
		$edit->nfiscal->rule='max_length[12]|required';
		$edit->nfiscal->size =14;
		$edit->nfiscal->maxlength =12;
		$edit->nfiscal->autocomplete =false;

		$lriffis='<a href="javascript:consulrif();" title="Consultar RIF en el SENIAT" onclick="">Consultar RIF en el SENIAT</a>';
		$edit->rif = new inputField('RIF','rif');
		$edit->rif->rule='max_length[13]|required';
		$edit->rif->size =13;
		$edit->rif->maxlength =13;
		$edit->rif->group='Datos del proveedor';
		$edit->rif->append(HTML::button('traesprv', 'Consultar Proveedor', '', 'button', 'button'));
		$edit->rif->append($lriffis);

		$edit->proveedor = new inputField('Nombre del proveedor','proveedor');
		$edit->proveedor->rule='max_length[40]|strtoupper';
		$edit->proveedor->size =40;
		$edit->proveedor->group='Datos del proveedor';
		$edit->proveedor->maxlength =40;

		$edit->codigo = new inputField('Codigo del gasto','codigo');
		$edit->codigo->rule ='max_length[6]|required';
		$edit->codigo->size =6;
		$edit->codigo->maxlength =8;
		$edit->codigo->append($bcodigo);

		$edit->descrip = new inputField('Descripcion','descrip');
		$edit->descrip->rule='max_length[50]|strtoupper';
		$edit->descrip->size =50;
		$edit->descrip->maxlength =50;

		$alicuota=$this->datasis->ivaplica(date('Y-m-d'));

		$arr=array(
			'exento'   =>'Monto <b>Exento</b>|Base exenta',
			'montasa'  =>'Montos con Alicuota <b>general</b> '.  $ivas['tasa'].'%|Base imponible',
			'tasa'     =>'Montos con Alicuota <b>general</b> '.  $ivas['tasa'].'%|Monto del IVA',
			'monredu'  =>'Montos con Alicuota <b>reducida</b> '. $ivas['redutasa'].'%|Base imponible',
			'reducida' =>'Montos con Alicuota <b>reducida</b> '. $ivas['redutasa'].'%|Monto del IVA',
			'monadic'  =>'Montos con Alicuota <b>adicional</b> '.$ivas['sobretasa'].'%|Base imponible',
			'sobretasa'=>'Montos con Alicuota <b>adicional</b> '.$ivas['sobretasa'].'%|Monto del IVA',
			'importe'  =>'Importe total');

		foreach($arr as $obj=>$label){
			$pos = strrpos($label, '|');
			if($pos!==false){
				$piv=explode('|',$label);
				$label=$piv[1];
				$grupo=$piv[0];
			}else{
				$grupo='';
			}

			$edit->$obj = new inputField($label,$obj);
			$edit->$obj->rule='max_length[17]|numeric';
			$edit->$obj->css_class='inputnum';
			$edit->$obj->insertValue =0;
			$edit->$obj->size =17;
			$edit->$obj->maxlength =17;
			$edit->$obj->group=$grupo;
			$edit->$obj->autocomplete=false;
		}
		$edit->$obj->readonly=true;

		$edit->tasa->rule     ='condi_required|max_length[17]|callback_chtasa';
		$edit->reducida->rule ='condi_required|max_length[17]|callback_chreducida';
		$edit->sobretasa->rule='condi_required|max_length[17]|callback_chsobretasa';
		$edit->importe->rule  ='max_length[17]|numeric|positive';
		$edit->importe->type  ='inputhidden';
		$edit->importe->label ='<b style="font-size:2em">Total</b>';

		$edit->sucursal = new dropdownField('Sucursal','sucursal');
		$edit->sucursal->options('SELECT codigo, sucursal FROM sucu ORDER BY sucursal');
		$edit->sucursal->rule='max_length[2]|required';

		$edit->departa = new dropdownField('Departamento','departa');
		$edit->departa->options("SELECT codigo, CONCAT_WS('-',codigo,departam) AS label FROM dept ORDER BY codigo");
		$edit->departa->rule='max_length[2]';

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:m:s'), date('H:m:s'));

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$url=site_url('finanzas/gser/ajaxsprv');
		//$this->rapyd->jquery[]='$(".inputnum").bind("keyup",function() { totaliza(); })';
		$this->rapyd->jquery[]='$(".inputnum").numeric(".");';
		$this->rapyd->jquery[]='$("#exento"   ).bind("keyup",function() { totaliza(); })';
		$this->rapyd->jquery[]='$("#montasa"  ).bind("keyup",function() { poneiva(2); })';
		$this->rapyd->jquery[]='$("#tasa"     ).bind("keyup",function() { totaliza(); })';
		$this->rapyd->jquery[]='$("#monredu"  ).bind("keyup",function() { poneiva(1); })';
		$this->rapyd->jquery[]='$("#reducida" ).bind("keyup",function() { totaliza(); })';
		$this->rapyd->jquery[]='$("#monadic"  ).bind("keyup",function() { poneiva(3); })';
		$this->rapyd->jquery[]='$("#sobretasa").bind("keyup",function() { totaliza(); })';

		$this->rapyd->jquery[]='$("input[name=\'traesprv\']").click(function() {
			rif=$("#rif").val();
			if(rif.length > 0){
				$.post("'.$url.'", { rif: rif },function(data){
					$("#proveedor").val(data);
				});
			}else{
				alert("Debe introducir un rif");
			}
		});';

		$data['content'] = $edit->output;
		$data['title']   = heading('Agregar/Modificar facturas de Caja Chica');
		$data['head']    = $this->rapyd->get_head();
		$data['head']   .= phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function _pre_gserchi($do){
		$rif   =$do->get('rif');
		$dbrif = $this->db->escape($rif);
		$nombre=$do->get('proveedor');
		$fecha =date('Y-m-d');
		$csprv =$this->datasis->dameval('SELECT COUNT(*) FROM sprv WHERE rif='.$dbrif);
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

	//Para Caja chica
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

		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Cajas pendientes por cerrar');
		$this->load->view('view_ventanas', $data);
	}

	//Convierte los gastos en caja chica
	function gserchipros($codbanc=null){
		if(empty($codbanc)) show_error('Faltan parametros');
		$dbcodbanc=$this->db->escape($codbanc);
		$mSQL='SELECT COUNT(*) AS cana, SUM(exento+montasa+monadic+monredu+tasa+sobretasa+reducida) AS monto FROM gserchi WHERE ngasto IS NULL AND aceptado="S" AND codbanc='.$dbcodbanc;
		$r   =$this->datasis->damerow($mSQL);
		if($r['cana']==0) show_error('Caja sin gastos');

		$mSQL="SELECT a.codprv, b.nombre FROM banc AS a JOIN sprv AS b ON a.codprv=b.proveed WHERE a.codbanc=$dbcodbanc";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
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
				$comis[$ind]['tbanco']  =$row->tbanco;
			}
		}
		$json_comis=json_encode($comis);

		$this->rapyd->load('dataform','datagrid');

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
						data: "q="+req.term,
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

		$form = new DataForm('finanzas/gser/gserchipros/'.$codbanc.'/process');
		$form->title("Numero de facturas aceptadas $r[cana], monto total <b>".nformat($r['monto']).'</b>');
		$form->script($script);

		$form->codprv = new inputField('Proveedor', 'codprv');
		$form->codprv->rule='required';
		$form->codprv->insertValue=$codprv;
		$form->codprv->size=8;
		$form->codprv->append($bsprv);

		$form->nombre = new inputField('Nombre', 'nombre');
		$form->nombre->rule='required';
		$form->nombre->insertValue=$nombre;
		$form->nombre->in = 'codprv';
		$form->nombre->type='inputhidden';

		$form->cargo = new dropdownField('Con cargo a','cargo');
		$form->cargo->option($this->mcred,'Credito');
		$form->cargo->options("SELECT codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE activo='S' ORDER BY codbanc");
		$form->cargo->onchange='desactivacampo(this.value)';
		$form->cargo->rule='max_length[5]|required';

		$form->cheque = new inputField('Numero de cheque', 'cheque');
		$form->cheque->rule='condi_required|callback_chobligaban';
		$form->cheque->append('Aplica  solo si el cargo es a un banco');

		$form->benefi = new inputField('Beneficiario', 'benefi');
		$form->benefi->insertValue=$nombre;
		$form->benefi->rule='condi_required|callback_chobligaban';
		$form->benefi->append('Aplica  solo si el cargo es a un banco');

		$action = "javascript:window.location='".site_url('finanzas/gser/cierregserchi/'.$codbanc)."'";
		$form->button('btn_regresa', 'Regresar', $action, 'BR');

		$form->submit('btnsubmit','Procesar');
		$form->build_form();

		$grid = new DataGrid('Lista de Gastos','gserchi');
		$select=array('exento + montasa + monadic + monredu + tasa + sobretasa + reducida AS totneto',
					  'tasa + sobretasa + reducida AS totiva','proveedor','fechafac','numfac','codbanc' );
		$grid->db->select($select);
		$grid->db->where('aceptado','S');
		$grid->db->where('ngasto IS NULL');
		$grid->db->where('codbanc',$codbanc);

		$grid->order_by('numfac','desc');
		$grid->per_page = 15;
		$grid->column('Caja','codbanc');
		$grid->column('Numero','numfac');
		$grid->column('Fecha' ,'<dbdate_to_human><#fechafac#></dbdate_to_human>','align=\'center\'');
		$grid->column('Proveedor','proveedor');
		$grid->column('IVA'   ,'totiva'    ,'align=\'right\'');
		$grid->column('Monto' ,'totneto'   ,'align=\'right\'');

		//$grid->add('finanzas/gser/datagserchi/create','Agregar nueva factura');
		$grid->build();

		if($form->on_success()){
			$codprv  = $form->codprv->newValue;
			$cargo   = $form->cargo->newValue;
			$nombre  = $form->nombre->newValue;
			$benefi  = $form->benefi->newValue;
			$cheque  = $form->cheque->newValue;

			$rt=$this->_gserchipros($codbanc,$cargo,$codprv,$benefi,$cheque);
			//var_dump($rt);
			if($rt){
				redirect('finanzas/gser/listo/n');
			}else{
				redirect('finanzas/gser/listo/s');
			}
		}

		$data['content'] = $form->output.$grid->output;
		$data['title']   = heading('Reposicion de caja chica '.$codbanc);
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['head']   .= script('jquery-ui.js');
		$data['head']   .= style('redmond/jquery-ui-1.8.1.custom.css');
		$data['head']   .= phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function chnumero($val){
		$tipo_doc = $this->input->post('tipo_doc');
		if($tipo_doc<>'ND' && empty($val)){
			$this->validation->set_message('chnumero', 'El campo %s es obligatorio.');
			return false;
		}
		return true;
	}

	function chtipoe($tipoe){
		$eenvia = $this->input->post('codb1');
		if(!empty($eenvia)){
			$envia  = common::_traetipo($eenvia);

			if($envia=='CAJ' && $tipoe!='D'){
				$this->validation->set_message('chtipoe', 'Cuando el gasto se carga a una caja el %s debe ser nota de debito.');
				return false;
			}elseif($envia!='CAJ' && empty($tipoe)){
				$this->validation->set_message('chtipoe', 'Cuando el gasto se carga a un banco el %s es obligatorio.');
				return false;
			}else{
				return true;
			}
		}else{
			return true;
		}
	}

	function chcodb($codb1){
		$monto1=$this->input->post('monto1');
		if($monto1>0 && empty($codb1)){
			$this->validation->set_message('chcodb', 'El campo %s es obligatorio cuando se paga un monto al contado');
			return false;
		}
	}

	function chobligaban($val){
		$ban=$this->input->post('codb1');
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

	//Chequea que el iva retenido sea 100, 0, 75%
	function chreteiva($monto){
		$totiva = round($this->input->post('totiva'),2);
		$monto  = round($monto,2);

		if($monto==0.00 || $monto==$totiva || round($totiva*0.75,2)==$monto){
			return true;
		}else{
			$this->validation->set_message('chreteiva', 'El campo %s tiene que ser 0, 75 o 100 del monto del iva');
			return false;
		}
	}

	function chobliganumero($val){
		return $this->_chobliganumero($val,'cargo','chobliganumero');
	}

	function chobliganumerog($val){
		return $this->_chobliganumero($val,'codb1','chobliganumerog');
	}

	function _chobliganumero($val,$campo,$func){
		$ban=$this->input->post($campo);
		if(empty($ban)) return true;
		$tipo=common::_traetipo($ban);
		if($tipo!='CAJ'){
			if(empty($val)){
				$this->validation->set_message($func, 'El campo %s es obligatorio cuando el cargo es a un banco');
				return false;
			}
		}
		return true;
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

		$databan  = common::_traebandata($codbanc);
		$datacar  = common::_traebandata($cargo);
		if(!is_null($datacar)){
			$tipo  = $datacar['tbanco'];
			$moneda= $datacar['moneda'];
		}

		$mSQL='SELECT codbanc,fechafac,numfac,nfiscal,rif,proveedor,codigo,descrip,
		  moneda,montasa,tasa,monredu,reducida,monadic,sobretasa,exento,importe,sucursal,departa,usuario,estampa,hora
		FROM gserchi WHERE ngasto IS NULL AND aceptado="S" AND codbanc='.$dbcodbanc;

		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$transac  = $this->datasis->fprox_numero('ntransa');
			$numero   = $this->datasis->fprox_numero('ngser');
			$cheque   = ($tipo=='CAJ')? $this->datasis->banprox($codbanc): $numeroch ;

			$montasa=$monredu=$monadic=$tasa=$reducida=$sobretasa=$exento=$totpre=$totiva=0;
			foreach ($query->result() as $row){
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
				$data['estampa']    = date('Y-m-d');
				$data['hora']       = date('H:i:s');
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
				if($ban==false){ memowrite($sql,'gser'); $error++;}

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

				$data=array();
				$data['cod_prv']    = $codprv;
				$data['nombre']     = $nombre;
				$data['tipo_doc']   = 'FC';
				$data['numero']     = $numero ;
				$data['fecha']      = $fecha ;
				$data['monto']      = $totneto;
				$data['impuesto']   = $totiva ;
				$data['abonos']     = 0;
				$data['vence']      = $fecha;
				$data['observa1']   = 'REPOSICION DE CAJA CHICA '.$codbanc;

				$data['transac']    = $transac;
				$data['estampa']    = date('Y-m-d');
				$data['hora']       = date('H:i:s');
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

				$sql=$this->db->insert_string('sprm', $data);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'gser'); $error++;}
				$cargo   = '';
				$cheque  = '';
				$negreso = '';
			}else{
				$ttipo  = $datacar['tbanco'];
				$tipo1  = ($ttipo=='CAJ') ? 'D': 'C';
				$negreso= $this->datasis->fprox_numero('negreso');
				$credito= 0;
				$causado='';

				$data=array();
				$data['codbanc']    = $cargo;
				$data['moneda']     = $moneda;
				$data['numcuent']   = $datacar['numcuent'];
				$data['banco']      = $datacar['banco'];
				$data['saldo']      = $datacar['saldo'];
				$data['tipo_op']    = ($ttipo=='CAJ') ? 'ND': 'CH';
				$data['numero']     = $cheque;
				$data['fecha']      = $fecha;
				$data['clipro']     = 'P';
				$data['codcp']      = $codprv;
				$data['nombre']     = $nombre;
				$data['monto']      = $totneto;
				$data['concepto']   = 'REPOSICION DE CAJA CHICA '.$codbanc;
				$data['benefi']     = $benefi;
				$data['posdata']    = '';
				$data['abanco']     = '';
				$data['liable']     = ($ttipo=='CAJ') ? 'S': 'N';;
				$data['transac']    = $transac;
				$data['usuario']    = $this->session->userdata('usuario');
				$data['estampa']    = date('Y-m-d');
				$data['hora']       = date('H:i:s');
				$data['anulado']    = 'N';
				$data['susti']      = '';
				$data['negreso']    = $negreso;

				$sql=$this->db->insert_string('bmov', $data);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'gser'); $error++;}

				$this->datasis->actusal($cargo,$sp_fecha,(-1)*$totneto);
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
			$data['estampa']    = date('Y-m-d');
			$data['hora']       = date('H:i:s');
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

			$sql=$this->db->insert_string('gser', $data);
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'gser'); $error++;}
			$idgser=$this->db->insert_id();

			$data = array('idgser' => $idgser);
			$dbfecha  = $this->db->escape($fecha);
			$dbnumero = $this->db->escape($numero);
			$dbcodprv = $this->db->escape($codprv);
			$where = "fecha=$dbfecha AND proveed=$dbcodprv AND  numero=$dbnumero";
			$mSQL = $this->db->update_string('gitser', $data, $where);
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'gser'); $error++; }

			$data = array('ngasto' => $numero);
			$where = "ngasto IS NULL AND  codbanc=$dbcodbanc";
			$mSQL = $this->db->update_string('gserchi', $data, $where);
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'gser'); $error++; }
		}
		return ($error==0)? true : false;
	}

	//******************************************************************
	//Crea la retencion
	//
	function _gserrete($fecha,$tipo,$fechafac,$numero,$nfiscal,$afecta,$clipro,$montasa,$monredu,$monadic,$tasa,$reducida,$sobretasa,$exento,$reiva,$transac){
		$nrocomp=$this->datasis->fprox_numero('niva');
		$sp_fecha= str_replace('-','',$fecha);
		$row     = $this->datasis->damerow('SELECT nombre,rif FROM sprv WHERE proveed='.$this->db->escape($clipro));
		$totpre  = $montasa+$monredu+$monadic+$exento;
		$totiva  = $tasa+$reducida+$sobretasa;
		$totneto = $totpre+$totiva;
		$error   = 0;

		$data['nrocomp']    = $nrocomp;
		$data['emision']    = $fecha;
		$data['periodo']    = substr($sp_fecha,0,6);
		$data['tipo_doc']   = $tipo;
		$data['fecha']      = $fechafac;
		$data['numero']     = $numero;
		$data['nfiscal']    = $nfiscal;
		$data['afecta']     = $afecta;
		$data['clipro']     = $clipro;
		$data['nombre']     = $row['nombre'];
		$data['rif']        = $row['rif'];
		$data['exento']     = $exento;
		$data['tasa']       = ($montasa>0)? round($tasa*100/$montasa,2) : 0;
		$data['general']    = $montasa;
		$data['geneimpu']   = $tasa;
		$data['tasaadic']   = ($monadic>0)? round($sobretasa*100/$monadic,2) : 0;
		$data['adicional']  = $monadic;
		$data['adicimpu']   = $sobretasa;
		$data['tasaredu']   = ($monredu>0)? round($reducida*100/$monredu,2) : 0;
		$data['reducida']   = $monredu;
		$data['reduimpu']   = $reducida;
		$data['stotal']     = $totpre;
		$data['impuesto']   = $totiva;
		$data['gtotal']     = $totneto;
		$data['reiva']      = $reiva;
		$data['transac']    = $transac;
		$data['estampa']    = date('Y-m-d');
		$data['hora']       = date('H:i:s');
		$data['usuario']    = $this->session->userdata('usuario');

		$sql=$this->db->insert_string('riva', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'gser'); $error++;}

		return ($error==0)? true : false;
	}

	function _rm_gserrete($transac){
		$dbtransac = $this->db->escape($transac);
		$mSQL      = 'UPDATE riva SET transac = CONCAT("_",MID(transac,2)) WHERE transac='.$dbtransac;
		$this->db->simple_query($mSQL);
	}

	//Crea la cuenta por pagar en caso de que el gasto sea a credito
	function _gsersprm($codbanc,$codprv,$numero,$fecha,$montasa,$monredu,$monadic,$tasa,$reducida,$sobretasa,$exento,$causado,$transac,$abono=0){
		$nombre  = $this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$this->db->escape($codprv));
		$totpre = $montasa+$monredu+$monadic+$exento;
		$totiva = $tasa+$reducida+$sobretasa;
		$totneto= $totpre+$totiva;
		$error  = 0;

		$data=array();
		$data['cod_prv']    = $codprv;
		$data['nombre']     = $nombre;
		$data['tipo_doc']   = 'FC';
		$data['numero']     = $numero ;
		$data['fecha']      = $fecha ;
		$data['monto']      = $totneto;
		$data['impuesto']   = $totiva ;
		$data['abonos']     = $abono;
		$data['vence']      = $fecha;
		$data['observa1']   = 'EGRESO NRO. '.$numero.' PROVEEDOR '.$nombre;
		$data['transac']    = $transac;
		$data['estampa']    = date('Y-m-d');
		$data['hora']       = date('H:i:s');
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

		$sql=$this->db->insert_string('sprm', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'gser'); $error++;}

		return ($error==0)? true : false;
	}

	//Reversa la transaccion en sprm
	function _rm_gsersprm($transac){
		$this->db->delete('sprm', array('transac' => $transac));
	}

	//genera el movimiento de banco cuando el pago es al contado
	function _bmovgser($codbanc,$codprv,$cargo,$negreso,$cheque,$fecha,$totneto,$benefi,$transac,$tipo_op,$msj=''){
		$nombre  = $this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$this->db->escape($codprv));
		$datacar = common::_traebandata($cargo);
		$sp_fecha = str_replace('-','',$fecha);
		$ttipo   = $datacar['tbanco'];
		$error   = 0;

		$data=array();
		$data['codbanc']    = $cargo;
		$data['moneda']     = $datacar['moneda'];
		$data['numcuent']   = $datacar['numcuent'];
		$data['banco']      = $datacar['banco'];
		$data['saldo']      = $datacar['saldo'];
		$data['tipo_op']    = ($ttipo=='CAJ') ? 'ND': $tipo_op;
		$data['numero']     = str_pad($cheque, 12, '0', STR_PAD_LEFT);
		$data['fecha']      = $fecha;
		$data['clipro']     = 'P';
		$data['codcp']      = $codprv;
		$data['nombre']     = $nombre;
		$data['monto']      = $totneto;
		$data['concepto']   = $msj;
		$data['benefi']     = $benefi;
		$data['posdata']    = '';
		$data['abanco']     = '';
		$data['liable']     = ($ttipo=='CAJ') ? 'N': 'S';
		$data['transac']    = $transac;
		$data['usuario']    = $this->session->userdata('usuario');
		$data['estampa']    = date('Y-m-d');
		$data['hora']       = date('H:i:s');
		$data['anulado']    = 'N';
		$data['susti']      = '';
		$data['negreso']    = $negreso;
		//$data['ndebito']    = '';
		//$data['ncausado']   = '';
		//$data['ncredito']   = '';

		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'gser'); $error++;}

		$this->datasis->actusal($cargo,$sp_fecha,(-1)*$totneto);
		//$sql='CALL sp_actusal('.$this->db->escape($cargo).",'$sp_fecha',-$totneto)";
		//$ban=$this->db->simple_query($sql);
		//if($ban==false){ memowrite($sql,'gser'); $error++; }

		return ($error==0)? true : false;
	}

	function _rm_bmovgser($transac){
		$sel=array('codbanc','tipo_op','numero','monto');
		$this->db->select($sel);
		$this->db->from('bmov');
		$this->db->where('transac',$transac);

		$query = $this->db->get();
		foreach ($query->result() as $row){

			$this->db->where('codbanc', $row->codbanc);
			$this->db->where('tipo_op', $row->tipo_op);
			$this->db->where('numero' , $row->numero);
			$this->db->where('transac', $transac);

			if($row->tipo_op=='CH'){
				$data = array('anulado' => 'S');

				$this->db->update('bmov', $data);
			}else{
				$this->db->delete('bmov');
			}

			$cargo   =$row->codbanc;
			$totneto =$row->monto;
			$sp_fecha=date('Ymd');

			$this->datasis->actusal($cargo,$sp_fecha,$totneto);
			//$sql='CALL sp_actusal('.$this->db->escape($cargo).",'$sp_fecha',$totneto)";
			//$ban=$this->db->simple_query($sql);
			//if($ban==false){ memowrite($sql,'gser');}
		}
	}

	function ajaxsprv(){
		$rif=$this->input->post('rif');
		if($rif!==false){
			$dbrif=$this->db->escape($rif);
			$nombre=$this->datasis->dameval("SELECT nombre FROM provoca WHERE rif=$dbrif");
			if(empty($nombre))
				$nombre=$this->datasis->dameval("SELECT nombre FROM sprv WHERE rif=$dbrif");
			echo $nombre;
		}
	}

	function listo($error,$numero=null){
		if($error=='n'){
			$data['content'] = 'Transaccion completada ';
			if(!empty($numero)){
				$url='formatos/verhtml/';

				$data['content'] .= ', puede <a href="#" onclick="fordi.print();">imprimirla</a>';
				$data['content'] .= ' o '.anchor('finanzas/gser/index','Regresar');
				$data['content'] .= "<iframe name='fordi' src ='$url' width='100%' height='450'><p>Tu navegador no soporta iframes.</p></iframe>";
			}else{
				$data['content'] .= anchor('finanzas/gser/index','Regresar');
			}
		}else{
			$data['content'] = 'Lo siento pero hubo algun error en la transaccion, se genero un centinela '.anchor('finanzas/gser/index','Regresar');
		}
		$data['title']   = heading('Transferencias entre cajas');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');
		$tipo_rete=$this->datasis->traevalor('CONTRIBUYENTE');
		$rif      =$this->datasis->traevalor('RIF');

		$fields = $this->db->field_data('gser');
		$url_pk = $this->uri->segment_array();
		$coun=0; $pk=array();
		foreach ($fields as $field){
			if($field->primary_key==1){
				$coun++;
				$pk[]=$field->name;
			}
		}
		$values=array_slice($url_pk,-$coun);
		$claves=array_combine (array_reverse($pk) ,$values );

		$query="UPDATE gitser AS a
			JOIN gser AS b on a.numero=b.numero and a.fecha = b.fecha and a.proveed = b.proveed
			SET a.idgser=b.id
			WHERE a.id=".$claves['id']." ";
		$this->db->simple_query($query);

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=> array('proveed' =>'Coodigo', 'nombre'=>'Nombre', 'rif'=>'Rif'),
			'filtro'  => array('proveed' =>'Codigo',  'nombre'=>'Nombre'),
			'retornar'=> array('proveed' =>'proveed', 'nombre'=>'nombre','tipo'=>'sprvtipo','reteiva'=>'sprvreteiva'),
			'script'  => array('post_sprv_modbus()'),
			'titulo'  => 'Buscar Proveedor');
		$bSPRV = $this->datasis->modbus($mSPRV);

		$do = new DataObject('gser');
		$do->pointer('sprv' ,'sprv.proveed=gser.proveed','sprv.tipo AS sprvtipo, sprv.reteiva AS sprvreteiva','left');
		$do->rel_one_to_many('gitser' ,'gitser' ,array('id'=>'idgser'));
		$do->rel_one_to_many('gereten','gereten',array('id'=>'idd'));
		$do->where_rel_one_to_many('gereten',array('gereten.origen','GSER'));
		//$do->rel_pointer('rete','rete','gereten.codigorete=rete.codigo','rete.pama1 AS retepama1');

		$edit = new DataDetails('Gastos', $do);
		if ( $edit->_status == 'show' ) {
			$edit->back_url = site_url('finanzas/gser/filteredgrid');
		} else {
			$edit->back_url = site_url('finanzas/gser/agregar');
		}

		$edit->set_rel_title('gitser','Gasto <#o#>');

		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->tipo_doc =  new dropdownField('Documento', 'tipo_doc');
		$edit->tipo_doc->style='width:70px';
		$edit->tipo_doc->option('FC','Factura');
		$edit->tipo_doc->option('ND','N. Debito');
		if($edit->_status=='show'){
			$edit->tipo_doc->option('XX','Anulado');
			$edit->tipo_doc->option('AD','Amortizacion');
			$edit->tipo_doc->option('GA','Gasto de Nomina');
		}

		$edit->ffactura = new DateonlyField('Fecha', 'ffactura','d/m/Y');
		$edit->ffactura->insertValue = date('Y-m-d');
		$edit->ffactura->size = 12;
		$edit->ffactura->rule = 'required';
		$edit->ffactura->calendar = false;

		$edit->fecha = new DateonlyField('Registro', 'fecha');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size = 12;
		$edit->fecha->rule = 'required';
		$edit->fecha->calendar = false;

		$edit->vence = new DateonlyField('Vence', 'vence','d/m/Y');
		$edit->vence->insertValue = date('Y-m-d');
		$edit->vence->size = 12;
		$edit->vence->calendar = false;

		$edit->compra = new inputField('Doc.Asociado','compra');
		$edit->compra->rule='max_length[8]';
		$edit->compra->size =10;
		$edit->compra->maxlength =8;

		$edit->numero = new inputField('Documento Nro.', 'serie');
		$edit->numero->size = 10;
		$edit->numero->maxlength=12;
		$edit->numero->autocomplete=false;
		$edit->numero->rule='condi_required|callback_chnumero';

		$edit->proveed = new inputField('Proveedor','proveed');
		$edit->proveed->size = 6;
		$edit->proveed->append($bSPRV);
		$edit->proveed->rule= 'required';

		$edit->nfiscal  = new inputField('Control Fiscal', 'nfiscal');
		$edit->nfiscal->size = 10;
		$edit->nfiscal->autocomplete=false;
		$edit->nfiscal->maxlength=20;

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->size = 30;
		$edit->nombre->maxlength=40;
		$edit->nombre->type='inputhidden';
		$edit->nombre->rule= 'required';

		$edit->sprvtipo = new hiddenField('','sprvtipo');
		$edit->sprvtipo->db_name = 'sclitipo';
		$edit->sprvtipo->pointer = true;

		$edit->sprvreteiva = new hiddenField('','sprvreteiva');
		$edit->sprvreteiva->db_name = 'sprvreteiva';
		$edit->sprvreteiva->insertValue=($tipo_rete=='ESPECIAL' && strtoupper($rif[0])!='V') ? '75':'0';
		$edit->sprvreteiva->pointer = true;

		$edit->totpre  = new inputField('Sub.Total', 'totpre');
		$edit->totpre->size = 10;
		$edit->totpre->css_class='inputnum';
		$edit->totpre->readonly = true;
		$edit->totpre->showformat ='decimal';
		$edit->totpre->type='inputhidden';

		$edit->totbruto= new inputField('Total', 'totbruto');
		$edit->totbruto->size = 10;
		$edit->totbruto->css_class='inputnum';
		$edit->totbruto->showformat ='decimal';
		$edit->totbruto->type='inputhidden';

		$edit->totiva = new inputField('Total IVA', 'totiva');
		$edit->totiva->css_class ='inputnum';
		$edit->totiva->size      = 10;
		$edit->totiva->showformat ='decimal';
		$edit->totiva->type='inputhidden';

		$edit->reteica = new inputField('Ret. ICA', 'reteica');
		$edit->reteica->css_class = 'inputnum';
		$edit->reteica->when      = array('show');
		$edit->reteica->size      = 10;
		$edit->reteica->showformat ='decimal';

		$edit->retesimple = new inputField('Ret', 'retesimple');
		$edit->retesimple->css_class = 'inputnum';
		$edit->retesimple->when      = array('show');
		$edit->retesimple->size      = 10;
		$edit->retesimple->showformat ='decimal';

		$edit->codb1 = new dropdownField('Caja/Banco','codb1');
		$edit->codb1->option('','Ninguno');
		$edit->codb1->options("SELECT TRIM(codbanc) AS ind, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE activo='S' ORDER BY codbanc");
		$edit->codb1->rule  = 'max_length[5]|callback_chcodb|condi_required';
		$edit->codb1->style = 'width:120px';
		$edit->codb1->onchange="esbancaja(this.value)";

		$edit->fondo = new dropdownField('Fondo','fondo');
		$edit->fondo->option('','Ninguno');
		$edit->fondo->options("SELECT TRIM(codbanc) AS ind, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE activo='S' AND tbanco='FO' ORDER BY codbanc");
		$edit->fondo->style = 'width:160px';

		$edit->tipo1 =  new dropdownField('Tipo', 'tipo1');
		$edit->tipo1->option('' ,'Ninguno');
		$edit->tipo1->option('C','Cheque');
		$edit->tipo1->option('D','N.Debito');
		$edit->tipo1->rule ='condi_required|callback_chtipoe';
		$edit->tipo1->style='width:70px';

		$edit->cheque1 = new inputField('N&uacute;mero','cheque1');
		$edit->cheque1->rule = 'condi_required|callback_chobliganumerog';
		$edit->cheque1->size = 12;
		$edit->cheque1->maxlength=20;

		$edit->benefi = new inputField('Beneficiario','benefi');
		$edit->benefi->size = 39;
		$edit->benefi->maxlength=40;

		$edit->monto1= new inputField('Contado', 'monto1');
		$edit->monto1->rule = 'numeric|positive';
		$edit->monto1->size = 10;
		$edit->monto1->css_class='inputnum';
		$edit->monto1->onkeyup='contado()';
		$edit->monto1->rule = 'condi_required|callback_chmontocontado|positive';
		$edit->monto1->autocomplete=false;
		$edit->monto1->showformat ='decimal';

		$edit->credito= new inputField('Monto a Cr&eacute;dito', 'credito');
		$edit->credito->rule = 'numeric|positive';
		$edit->credito->size = 10;
		$edit->credito->showformat ='decimal';
		$edit->credito->css_class='inputnum';
		$edit->credito->onkeyup='ccredito()';
		$edit->credito->autocomplete=false;
		//$edit->credito->readonly=true;

		$edit->reten = new inputField('Ret. ISLR','reten');
		$edit->reten->rule = 'numeric|positive';
		$edit->reten->size = 10;
		$edit->reten->maxlength=10;
		$edit->reten->css_class='inputnum';
		//$edit->reten->when=array('show');
		$edit->reten->showformat ='decimal';
		$edit->reten->type='inputhidden';

		$edit->reteiva = new inputField('Ret de IVA','reteiva');
		$edit->reteiva->rule = 'numeric|positive';
		$edit->reteiva->size = 10;
		$edit->reteiva->maxlength=10;
		$edit->reteiva->rule = 'callback_chreteiva';
		$edit->reteiva->onchange ='totalizar()';
		$edit->reteiva->css_class='inputnum';
		$edit->reteiva->showformat ='decimal';
		$edit->reteiva->autocomplete=false;
		//$edit->reteiva->onkeyup="reteiva()";

		$edit->checkbox = new checkboxField("C.N.D.", "cnd", "S","N");
		$edit->checkbox->insertValue = "y";

		$edit->reteica = new inputField('Ret. ICA','reteica');
		$edit->reteica->size = 10;
		$edit->reteica->maxlength=10;
		//$edit->reteica->rule = 'callback_chreteiva';
		$edit->reteica->css_class='inputnum';
		$edit->reteica->when=array('show');

		$edit->totneto = new inputField('Monto Neto','totneto');
		$edit->totneto->rule = 'numeric|positive';
		$edit->totneto->size = 10;
		$edit->totneto->maxlength=10;
		$edit->totneto->css_class='inputnum';
		$edit->totneto->readonly=true;
		$edit->totneto->showformat ='decimal';
		//$edit->totneto->type='inputhidden';

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));

		//**************************************************************
		//   Campos para el detalle 1
		//
		$edit->codigo = new inputField('Codigo <#o#>', 'codigo_<#i#>');
		$edit->codigo->size=7;
		$edit->codigo->db_name='codigo';
		//$edit->codigo->append($btn);
		$edit->codigo->rule  = 'required';
		$edit->codigo->rel_id= 'gitser';

		$edit->descrip = new inputField('Descripcion <#o#>', 'descrip_<#i#>');
		$edit->descrip->size=40;
		$edit->descrip->db_name='descrip';
		$edit->descrip->maxlength=50;
		$edit->descrip->rel_id='gitser';

		$edit->precio = new inputField('Precio <#o#>', 'precio_<#i#>');
		$edit->precio->db_name='precio';
		$edit->precio->css_class='inputnum';
		$edit->precio->size=10;
		$edit->precio->rule='required|positive';
		$edit->precio->rel_id='gitser';
		$edit->precio->autocomplete=false;
		$edit->precio->onkeyup='importe(<#i#>)';
		$edit->precio->showformat ='decimal';

		$ivas=$this->datasis->ivaplica();
		$edit->tasaiva =  new dropdownField('IVA <#o#>', 'tasaiva_<#i#>');
		$edit->tasaiva->option($ivas['tasa']     ,$ivas['tasa'].'%');
		$edit->tasaiva->option($ivas['redutasa'] ,$ivas['redutasa'].'%');
		$edit->tasaiva->option($ivas['sobretasa'],$ivas['sobretasa'].'%');
		$edit->tasaiva->option('0','0.00%');
		$edit->tasaiva->db_name='tasaiva';
		$edit->tasaiva->rule='positive';
		$edit->tasaiva->style="30px";
		$edit->tasaiva->rel_id   ='gitser';
		$edit->tasaiva->onchange ='importe(<#i#>)';

		$edit->iva = new inputField('importe <#o#>', 'iva_<#i#>');
		$edit->iva->db_name='iva';
		$edit->iva->css_class='inputnum';
		$edit->iva->rel_id   ='gitser';
		$edit->iva->size=8;
		$edit->iva->rule='positive|callback_chretiva';
		$edit->iva->onkeyup='valida(<#i#>)';
		$edit->iva->showformat ='decimal';
		$edit->iva->type='inputhidden';

		$edit->importe = new inputField('importe <#o#>', 'importe_<#i#>');
		$edit->importe->db_name='importe';
		$edit->importe->css_class='inputnum';
		$edit->importe->rel_id   ='gitser';
		$edit->importe->size=10;
		$edit->importe->onkeyup='valida(<#i#>)';
		$edit->importe->showformat ='decimal';
		$edit->importe->type='inputhidden';

		$edit->departa =  new dropdownField('Departamento <#o#>', 'departa_<#i#>');
		$edit->departa->option('','Seleccionar');
		$edit->departa->options("SELECT TRIM(depto) AS codigo, CONCAT_WS('-',depto,TRIM(descrip)) AS label FROM dpto WHERE tipo IN ('G','A') ORDER BY depto");
		$edit->departa->db_name='departa';
		$edit->departa->rule='required';
		$edit->departa->style = 'width:100px';
		$edit->departa->rel_id   ='gitser';
		$edit->departa->onchange="gdeparta(this.value)";

		$edit->sucursal =  new dropdownField('Sucursal <#o#>', 'sucursal_<#i#>');
		$edit->sucursal->options("SELECT codigo,codigo AS sucursal FROM sucu ORDER BY codigo");
		$edit->sucursal->db_name='sucursal';
		$edit->sucursal->rule='required';
		$edit->sucursal->style = 'width:40px';
		$edit->sucursal->title ='Sucursal';

		$edit->sucursal->rel_id   ='gitser';
		$edit->sucursal->onchange="gsucursal(this.value)";
		//================= Fin de campos para detalle =================


		//**************************************************************
		//   Campos para el detalle reten
		//
		$edit->itorigen = new autoUpdateField('origen','GSER','GSER');
		$edit->itorigen->rel_id ='gereten';

		$edit->codigorete = new dropdownField('','codigorete_<#i#>');
		$edit->codigorete->option('','Seleccionar');
		$edit->codigorete->options('SELECT TRIM(codigo) AS codigo,TRIM(CONCAT_WS("-",tipo,codigo,activida)) AS activida FROM rete ORDER BY tipo,codigo');
		$edit->codigorete->db_name='codigorete';
		$edit->codigorete->rule   ='max_length[4]';
		$edit->codigorete->style  ='width: 300px';
		$edit->codigorete->rel_id ='gereten';
		$edit->codigorete->onchange='post_codigoreteselec(<#i#>,this.value)';

		$edit->base = new inputField('base','base_<#i#>');
		$edit->base->db_name='base';
		$edit->base->rule='max_length[10]|numeric|positive';
		$edit->base->css_class='inputnum';
		$edit->base->size =12;
		$edit->base->autocomplete=false;
		$edit->base->rel_id    ='gereten';
		$edit->base->maxlength =10;
		$edit->base->onkeyup   ='importerete(<#i#>)';
		$edit->base->showformat ='decimal';

		$edit->porcen = new inputField('porcen','porcen_<#i#>');
		$edit->porcen->db_name='porcen';
		$edit->porcen->rule='max_length[5]|numeric|positive';
		$edit->porcen->css_class='inputnum';
		$edit->porcen->size =7;
		$edit->porcen->rel_id    ='gereten';
		$edit->porcen->readonly  = true;
		$edit->porcen->maxlength =5;
		$edit->porcen->showformat ='decimal';
		$edit->porcen->type='inputhidden';

		$edit->monto = new inputField('monto','monto_<#i#>');
		$edit->monto->db_name='monto';
		$edit->monto->rule='max_length[10]|numeric|positive';
		$edit->monto->css_class='inputnum';
		$edit->monto->rel_id    ='gereten';
		$edit->monto->size =12;
		$edit->monto->readonly  = true;
		$edit->monto->maxlength =8;
		$edit->monto->showformat ='decimal';
		$edit->monto->type='inputhidden';
		//*****************************
		//Fin de campos para detalle
		//*****************************

		$edit->buttons('add_rel');

		$edit->on_save_redirect=false;
		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			if($this->genesal){
				$conten['form']  =& $edit;
				$conten['solo']  = $this->solo;
				$data['content'] = $this->load->view('view_gser', $conten);
			}else{
				$rt=array(
					'status' =>'B',
					'mensaje'=> html_entity_decode( $edit->error_string),
					'pk'     =>''
				);
				echo json_encode($rt);
			}
		}
	}

	//Calcula las retenciones para enviarlas por ajax
	function calcularete(){
		$post    = $_POST;

		if(!isset($post['proveed'])){
			return;
		}
		$proveed = $post['proveed'];

		$data=array();
		foreach($post as $ind=>$val){
			$subject = 'abcdef';
			$pattern = '/^def/';
			if(preg_match('/^codigo_(?P<id>\d+)+/',$ind, $match)>0){
				$id=$match['id'];
				$pivot=array('codigo'=>$post['codigo_'.$id] , 'monto'=>$post['precio_'.$id]);
				$data[]=$pivot;
			}
		}

		$tiposprv= $this->datasis->dameval('SELECT tipo FROM sprv WHERE proveed='.$this->db->escape($proveed));
		$campo   = ($tiposprv=='1')? 'retej' : 'reten';

		$greten=$pgreten=array();
		foreach($data as $cgas){
			$codigo = $cgas['codigo'];
			$precio = $cgas['monto'];
			$mmsql="SELECT b.codigo ,a.descrip, b.base1,b.tari1,b.activida,b.pama1
						FROM mgas AS a
						LEFT JOIN rete AS b ON a.${campo}=b.codigo
					WHERE a.codigo=".$this->db->escape($codigo)." LIMIT 1";

			$fila=$this->datasis->damerow($mmsql);
			if(!empty($fila['pama1'])){
				if($precio>=$fila['base1']){
					$itbase= $precio*($fila['base1']/100);
					$itret = $itbase*($fila['tari1']/100);
					if($itret>0){
						$gind=$fila['codigo'];
						if(isset($pgreten[$gind])){
							$pgreten[$gind][0]+= $itbase;
							$pgreten[$gind][2]+= $itret;
						}else{
							$pgreten[$gind]= array($itbase,$fila['tari1'],$itbase*$fila['tari1']/100);
						}

					}
				}
			}
		}
		$pivot=array();
		if(count($pgreten)>0){
			foreach($pgreten as $ind=>$vals){
				$pivot['codigo']= $ind;
				$pivot['base']  = round($vals[0],2);
				$pivot['porcen']= round($vals[1],2);
				$pivot['monto'] = round($vals[2],2);
				$greten[]=$pivot;
			}

		}
		echo json_encode($greten);
	}

	function mgserdataedit(){
		$this->rapyd->load('dataedit');
		$this->rapyd->uri->keep_persistence();

		$sprv=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'Codigo Proveedor',
			'nombre'=>'Nombre',
			'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'Codigo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed','nombre'=>'nombre'),
			'titulo'  =>'Buscar Proveedor');

		$bsprv=$this->datasis->modbus($sprv);

		$edit = new DataEdit('Correccion','gser');
		//$edit->back_save  =true;
		//$edit->back_cancel=true;
		$edit->back_cancel_save=true;
		$edit->pre_process( 'create','_pre_mgsercreate' );
		$edit->pre_process( 'update','_pre_mgserupdate' );
		$edit->post_process('update','_post_mgserupdate');
		$edit->back_url = 'finanzas/gser';

		$edit->fecha = new dateonlyField('Fecha Recepcion', 'fecha');
		$edit->fecha->size = 10;
		$edit->fecha->rule= 'required';

		$edit->ffactura = new dateonlyField('Fecha Documento', 'ffactura');
		$edit->ffactura->size = 10;
		$edit->ffactura->rule= 'required';

		$edit->vence = new dateonlyField('Fecha Vencimiento', 'vence');
		$edit->vence->size = 10;
		$edit->vence->rule= 'required';

		$edit->serie = new inputField('Numero', 'serie');
		$edit->serie->size = 20;
		$edit->serie->rule= 'required|trim';
		$edit->serie->maxlength=20;

		$edit->nfiscal = new inputField('Control Fiscal', 'nfiscal');
		$edit->nfiscal->size = 20;
		$edit->nfiscal->rule= 'required|max_length[12]|trim';
		$edit->nfiscal->maxlength=20;

		$edit->proveed = new inputField('Codigo', 'proveed');
		$edit->proveed->size =8;
		$edit->proveed->maxlength=5;
		$edit->proveed->append($bsprv);
		$edit->proveed->rule = 'required|trim';
		//$edit->proveed->group='Datos Proveedor';

		$edit->nombre = new inputField('Nombre ', 'nombre');
		$edit->nombre->size =  50;
		$edit->nombre->maxlength=40;
		$edit->nombre->readonly = true;
		$edit->nombre->rule= 'required';
		//$edit->nombre->group='Datos Proveedor';

		$edit->codb1 = new inputField('Codigo del banco', 'codb1');
		$edit->codb1->mode='autohide';
		$edit->codb1->group='Datos finacieros';

		$edit->tipo1 = new dropdownField('Tipo de operacion', 'tipo1');
		$edit->tipo1->option('N','Nota de debito');
		$edit->tipo1->option('C','Cheque');
		$edit->tipo1->mode='autohide';
		$edit->tipo1->group='Datos finacieros';

		$edit->cheque1 = new inputField('Numero', 'cheque1');
		$edit->cheque1->mode='autohide';
		$edit->cheque1->group='Datos finacieros';

		$edit->totpre = new inputField('Monto neto', 'totpre');
		$edit->totpre->mode='autohide';
		$edit->totpre->group='Montos';

		$edit->totiva = new inputField('Impuesto', 'totiva');
		$edit->totiva->mode='autohide';
		$edit->totiva->group='Montos';

		$edit->credito = new inputField('Monto a Credito', 'credito');
		$edit->credito->mode='autohide';
		$edit->credito->group='Montos';

		$edit->totbruto = new inputField('Monto total', 'totbruto');
		$edit->totbruto->mode='autohide';
		$edit->totbruto->group='Montos';

		$edit->buttons('save');
		$edit->build();

		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_gsermgser', $conten,true);

		//$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Correccion de Egresos');
		$this->load->view('view_ventanas', $data);
	}

	function _pre_mgserupdate($do){
		$serie   = $do->get('serie');
		$nnumero = substr($serie,-8);
		$do->set('numero',$nnumero);
	}

	function _post_mgserupdate($do) {
		$fecha     = $this->db->escape($do->get('fecha'));
		$vence     = $this->db->escape($do->get('vence'));
		$proveed   = $this->db->escape($do->get('proveed'));
		$nombre    = $this->db->escape($do->get('nombre'));
		$transac   = $do->get('transac');
		$dbtransac = $this->db->escape($transac);
		$numero    = $this->db->escape($do->get('numero'));

		$update="UPDATE gser SET serie=$numero WHERE transac=$dbtransac";
		$this->db->query($update);

		$update2="UPDATE gitser SET fecha=$fecha, proveed=$proveed,numero=$numero WHERE transac=$dbtransac";
		$this->db->query($update2);

		//MODIFICA SPRM
		$update3="UPDATE sprm SET fecha=$fecha,vence=$vence, numero=$numero, cod_prv=$proveed,nombre=$nombre WHERE tipo_doc='FC' AND transac=$dbtransac";
		$this->db->query($update3);

		//MODIFICA BMOV
		$update4="UPDATE bmov SET fecha=$fecha, numero=$numero, codcp=$proveed,nombre=$nombre WHERE clipro='P' AND transac=$dbtransac";
		$this->db->query($update4);

		//MODIFICA RIVA
		$update5="UPDATE riva SET fecha=$fecha, numero=$numero,clipro=$proveed,nombre=$nombre WHERE transac=$dbtransac";
		$this->db->query($update5);

		logusu('GSER',"Gasto $numero CAMBIADO");
		return true;
	}

	function _pre_mgsercreate($do){
		return false;
	}

	function _pre_insert($do){
		$fecha   = $do->get('fecha');
		$usuario = $do->get('usuario');
		$proveed = $do->get('proveed');
		$ffecha  = $do->get('ffactura');
		$codb1   = $do->get('codb1');
		$tipo1   = $do->get('tipo1');
		$benefi  = $do->get('benefi');
		$nombre  = $do->get('nombre');
		$nfiscal = $do->get('nfiscal');
		$tipo_doc= $do->get('tipo_doc');
		$monto1  = $do->get('monto1');
		$serie   = $do->get('serie');

		if(empty($serie) && $tipo_doc='ND'){
			$serie = $this->datasis->fprox_numero('num_nd');
			$do->set('serie',$serie);
		}

		$numero  = substr($serie,-8);
		$do->set('numero',$numero);

		$rivaex  = $this->input->post('_rivaex');
		if(empty($monto1)){
			$monto1  = 0;
		}else{
			$monto1 = floatval($monto1);
		}
		//$cheque1= $do->get('cheque1');
		$_tipo=common::_traetipo($codb1);

		if(empty($benefi) && $tipo1=='C'){
			$do->set('benefi',$nombre);
		}

		if(empty($nfiscal)){
			$do->set('nfiscal',$numero);
		}

		if($_tipo=='CAJ'){
			$nn=$this->datasis->banprox($codb1);
			$do->set('cheque1',$nn);
		}else{
			if(!empty($codb1)){
				$cheque=$do->get('cheque1');
				$cheque=str_pad($cheque, 12, '0', STR_PAD_LEFT);
				$do->set('cheque1',$cheque);
			}
		}

		$mSQL='SELECT COUNT(*) AS cana FROM gser WHERE proveed='.$this->db->escape($proveed).' AND numero='.$this->db->escape($numero).' AND fecha='.$this->db->escape($fecha).' AND tipo_doc='.$this->db->escape($tipo_doc);
		$ca=$this->datasis->dameval($mSQL);
		if($ca>0){
			$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert']='Al parecer ya esta registrado un gasto con la misma fecha de recepcion, numero y proveedor.';
			return false;
		}

		//Totalizamos la retenciones (exepto la de iva)
		$retemonto=$rete_cana_vacio=$retebase=0;
		$rete_cana=$do->count_rel('gereten');
		for($i=0;$i<$rete_cana;$i++){
			$codigorete = $do->get_rel('gereten','codigorete',$i);
			if(!empty($codigorete)){
				$importe = floatval($do->get_rel('gereten','base'  ,$i));
				$monto   = floatval($do->get_rel('gereten','monto' ,$i));
				$porcen  = $do->get_rel('gereten','porcen',$i);

				$do->set_rel('gereten','numero'  ,$serie  ,$i);
				$do->set_rel('gereten','origen'  ,'GSER'  ,$i);
				$retemonto += $monto;
				$retebase  += $importe;
			}else{
				$rete_cana_vacio++;
			}
		}

		$do->set('reten',$retemonto);
		if($rete_cana_vacio==$rete_cana){
			$do->unset_rel('gereten'); //si no hay retencion elimina la relacion
		}elseif($rete_cana-$rete_cana_vacio == 1){
			//Cuando la retencion es una sola
			$do->set('creten',$codigorete);
			$do->set('breten',$importe   );
			$do->set('preten',$porcen    );
		}
		$retemonto=round($retemonto,2);
		//Fin de las retenciones exepto iva

		$ivat=$subt=$total=0;
		$tasa=$reducida=$sobretasa=$montasa=$monredu=$monadic=$exento=0;
		$con=$this->db->query("SELECT tasa,redutasa,sobretasa FROM civa ORDER BY fecha desc LIMIT 1");
		$t=$con->row('tasa');$rt=$con->row('redutasa');$st=$con->row('sobretasa');
		$cana=$do->count_rel('gitser');

		for($i=0;$i<$cana;$i++){
			$codigo = $do->get_rel('gitser','codigo' ,$i);
			$auxt   = floatval($do->get_rel('gitser','tasaiva',$i));
			$precio = floatval($do->get_rel('gitser','precio' ,$i));
			$iva    = $precio*($auxt/100);

			$importe=round($iva+$precio,2);
			$total+=$importe;
			$ivat +=$iva;
			$subt +=$precio;

			$do->set_rel('gitser','iva'    ,round($iva,2),$i);
			$do->set_rel('gitser','importe',$importe,$i);
		}
		$ivat=round($ivat,2);

		if($retebase>$subt){
			$do->error_message_ar['pre_ins'] ='El monto base de la retencion es no puede ser mayor que la base de la factura';
			return false;
		}

		//Calcula la retencion del iva si aplica
		$rif      = $this->datasis->traevalor('RIF');
		$contribu = $this->datasis->traevalor('CONTRIBUYENTE');
		if($contribu=='ESPECIAL' && $rif!='V' &&  $rivaex!='S'){
			$prete=floatval($this->datasis->dameval('SELECT reteiva FROM sprv WHERE proveed='.$this->db->escape($proveed)));
			if(empty($prete)) $prete=75;
			$reteiva=$ivat*$prete/100;
		}else{
			$reteiva=0;
		}
		$reteiva=round($reteiva,2);
		$do->set('reteiva', $reteiva);
		//Fin del calculo de la retencion de iva

		//Chequea que el monto retenido no sea mayor a la base del gasto
		if($retemonto+$reteiva>$subt){
			$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert']='Opps!! no se puede cargar un gasto cuyas retenciones sean mayores a la base del mismo.';
			return false;
		}

		//Calcula los totales
		$atotneto = floatval($do->get('totneto'));
		$totneto=$total-$retemonto-$reteiva;
		$do->set('totpre'  ,$subt );
		$do->set('totbruto',$total);
		$do->set('totiva'  ,$ivat );

		//Ajuste por problemas de decimas
		if(($atotneto-$totneto)!=0){
			$do->set('totneto' ,$totneto);
			if($atotneto==$monto1){
				$do->set('monto1'  ,$totneto);
				$monto1=$totneto;
			}
		}
		//Fin del ajuste por decimas
		$do->set('credito' ,$totneto-$monto1);

		//Calcula la tasa particulares
		$trans=$this->datasis->fprox_numero('ntransa');
		$do->set('transac',$trans);
		for($i=0;$i<$cana;$i++){
			$auxt   = $do->get_rel('gitser','tasaiva',$i);
			$precio = $do->get_rel('gitser','precio' ,$i);
			$iva    = $do->get_rel('gitser','iva'    ,$i);
			if($auxt-$t==0) {
				$tasa   +=$iva;
				$montasa+=$precio;
				$do->set_rel('gitser','tasa'     ,$iva   ,$i);
				$do->set_rel('gitser','montasa'  ,$precio,$i);
			}elseif($auxt-$rt==0) {
				$reducida+=$iva;
				$monredu +=$precio;
				$do->set_rel('gitser','reducida' ,$iva   ,$i);
				$do->set_rel('gitser','monredu'  ,$precio,$i);
			}elseif($auxt-$st==0) {
				$sobretasa+=$iva;
				$monadic  +=$precio;
				$do->set_rel('gitser','sobretasa',$iva   ,$i);
				$do->set_rel('gitser','monadic'  ,$precio,$i);
			}else{
				$exento+=$precio;
				$do->set_rel('gitser','exento'   ,$precio,$i);
			}

			$do->set_rel('gitser','fecha'   ,$fecha  ,$i);
			$do->set_rel('gitser','numero'  ,$numero ,$i);
			$do->set_rel('gitser','transac' ,$trans  ,$i);
			$do->set_rel('gitser','usuario' ,$usuario,$i);
			$do->set_rel('gitser','proveed' ,$proveed,$i);
			$do->set_rel('gitser','fechafac',$ffecha ,$i);

			$do->rel_rm_field('gitser','tasaiva',$i);//elimina el campo comodin
		}

		$do->set('tasa'     ,$tasa     );
		$do->set('montasa'  ,$montasa  );
		$do->set('reducida' ,$reducida );
		$do->set('monredu'  ,$monredu  );
		$do->set('sobretasa',$sobretasa);
		$do->set('monadic'  ,$monadic  );
		$do->set('exento'   ,$exento   );

		$rete_cana=$do->count_rel('gereten');
		for($i=0;$i<$rete_cana;$i++){
			$codigorete = $do->get_rel('gereten','codigorete',$i);
			if(!empty($codigorete)){
				$do->set_rel('gereten','transac' ,$trans,$i);
			}
		}

		if ($monto1>0){
			$negreso  = $this->datasis->fprox_numero('negreso');
			$ncausado = '';
		}else{
			$ncausado = $this->datasis->fprox_numero('ncausado');
			$negreso  = '';
		}
		$do->set('negreso' ,$negreso );
		$do->set('ncausado',$ncausado);

		return true;
	}

	function _post_insert($do){
		$codbanc  = $do->get('codb1');
		$codprv   = $do->get('proveed');
		$numero   = $do->get('numero');
		$fecha    = $do->get('fecha');
		$fechafac = $do->get('ffactura');
		$montasa  = $do->get('montasa');
		$monredu  = $do->get('monredu');
		$monadic  = $do->get('monadic');
		$tasa     = $do->get('tasa');
		$reducida = $do->get('reducida');
		$sobretasa= $do->get('sobretasa');
		$exento   = $do->get('exento');
		$causado  = $do->get('ncausado');
		$negreso  = $do->get('negreso');
		$transac  = $do->get('transac');
		$cheque   = $do->get('cheque1');
		$monto1   = $do->get('monto1');
		$tipo1    = $do->get('tipo1');
		$codb1    = $do->get('codb1');
		$reiva    = $do->get('reteiva');
		$reten    = $do->get('reten');
		$nfiscal  = $do->get('nfiscal');
		$tipo     = $do->get('tipo_doc');
		$afecta   = $do->get('afecta');
		$estampa  = $do->get('estampa');
		$usuario  = $do->get('usuario');
		$hora     = $do->get('hora');
		$nombre   = $do->get('nombre');
		$totiva   = $do->get('totiva');
		$credito  = $do->get('credito');

		$totbruto = $do->get('totbruto');
		$totneto  = $do->get('totneto');
		$totcred  = round($totneto-$monto1,2);


		//Crea el movimiento para la retencion ISLR
		if($reten>0){
			$mnsprm  = $this->datasis->fprox_numero('num_nd');
			$control = $this->datasis->fprox_numero('nsprm');

			$data=array();
			$data['cod_prv']    = 'RETEN';
			$data['nombre']     = 'RETENCIONES POR ENTERAR';
			$data['tipo_doc']   = 'ND';
			$data['numero']     = $mnsprm;
			$data['fecha']      = $fecha;
			$data['monto']      = $reten;
			$data['impuesto']   = 0;
			$data['abonos']     = 0;
			$data['vence']      = $fecha;
			$data['tipo_ref']   = $tipo;
			$data['num_ref']    = $numero;
			$data['observa1']   = 'RET/I.S.L.R. CAUSADA EN';
			$data['observa2']   = 'FACTURA # '.$numero.' DE FECHA '.$fechafac;
			$data['transac']    = $transac;
			$data['estampa']    = $estampa;
			$data['hora']       = $hora;
			$data['usuario']    = $usuario;
			$data['reteiva']    = 0;
			$data['montasa']    = 0;
			$data['monredu']    = 0;
			$data['monadic']    = 0;
			$data['tasa']       = 0;
			$data['reducida']   = 0;
			$data['sobretasa']  = 0;
			$data['exento']     = 0;
			$data['control']    = $control;
			$data['codigo']     = 'NOCON';
			$data['descrip']    = 'NOTA DE CONTABILIDAD';

			$sql=$this->db->insert_string('sprm', $data);
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'gser');}
		}
		//Fin de la retencion ISLR

		//Crea el movimiento para la retencion de IVA
		if($reiva>0){
			$mnsprm  = $this->datasis->fprox_numero('num_nd');
			$control = $this->datasis->fprox_numero('nsprm');

			$data=array();
			$data['cod_prv']    = 'REIVA';
			$data['nombre']     = 'RETENCIONES POR ENTERAR';
			$data['tipo_doc']   = 'ND';
			$data['numero']     = $mnsprm;
			$data['fecha']      = $fecha;
			$data['monto']      = $reiva;
			$data['impuesto']   = 0;
			$data['abonos']     = 0;
			$data['vence']      = $fecha;
			$data['tipo_ref']   = $tipo;
			$data['num_ref']    = $numero;
			$data['observa1']   = 'RET/IVA CAUSADA EN';
			$data['observa2']   = 'FACTURA # '.$numero.' DE FECHA '.$fechafac;
			$data['transac']    = $transac;
			$data['estampa']    = $estampa;
			$data['hora']       = $hora;
			$data['usuario']    = $usuario;
			$data['reteiva']    = 0;
			$data['montasa']    = 0;
			$data['monredu']    = 0;
			$data['monadic']    = 0;
			$data['tasa']       = 0;
			$data['reducida']   = 0;
			$data['sobretasa']  = 0;
			$data['exento']     = 0;
			$data['control']    = $control;
			$data['codigo']     = 'NOCON';
			$data['descrip']    = 'NOTA DE CONTABILIDAD';

			$sql=$this->db->insert_string('sprm', $data);
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'gser'); }
			$this->_gserrete($fecha,$tipo,$fechafac,$numero,$nfiscal,$afecta,$codprv,$montasa,$monredu,$monadic,$tasa,$reducida,$sobretasa,$exento,$reiva,$transac);
		}
		//Fin de la retencion de IVA

		//Crea el movimiento en banco del monto al contado
		if($monto1 > 0.00){
			$benefi=$do->get('benefi');
			$msj = "EGRESO AL CONTADO SEGUN FACTURA ${numero}";
			if($tipo1=='D'){
				$tipo_op='ND';
			}else{
				$tipo_op='CH';
			}
			$this->_bmovgser($codbanc,$codprv,$codbanc,$negreso,$cheque,$fecha,$monto1,$benefi,$transac,$tipo_op,$msj);
		}
		//Fin del movimiento en el banco


		//Crea la cuenta por pagar si es necesario
		if($totcred > 0.0){
			$causado = $this->datasis->fprox_numero('ncausado');
			$control = $this->datasis->fprox_numero('nsprm');

			$data=array();
			$data['cod_prv']    = $codprv;
			$data['nombre']     = $nombre;
			$data['tipo_doc']   = $tipo;
			$data['numero']     = $numero ;
			$data['fecha']      = $fecha ;
			$data['monto']      = $totbruto;
			$data['impuesto']   = $totiva ;
			$data['abonos']     = $monto1+$reten+$reiva;
			$data['vence']      = $fecha;
			$data['observa1']   = '';
			$data['transac']    = $transac;
			$data['estampa']    = $estampa;
			$data['hora']       = $hora;
			$data['usuario']    = $usuario;
			$data['reteiva']    = $reiva;
			$data['reten']      = $reten;
			$data['montasa']    = $montasa;
			$data['monredu']    = $monredu;
			$data['monadic']    = $monadic;
			$data['tasa']       = $tasa;
			$data['reducida']   = $reducida;
			$data['sobretasa']  = $sobretasa;
			$data['exento']     = $exento;
			$data['causado']    = $causado;
			$data['control']    = $control;

			$sql=$this->db->insert_string('sprm', $data);
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'gser');}

			//Crea el abono si lo tiene
			if($monto1 > 0){
				$absprm  = $this->datasis->fprox_numero('num_ab');
				$control = $this->datasis->fprox_numero('nsprm');

				$data=array();
				$data['cod_prv']    = $codprv;
				$data['nombre']     = $nombre;
				$data['tipo_doc']   = 'AB';
				$data['numero']     = $absprm;
				$data['fecha']      = $fecha ;
				$data['monto']      = $monto1;
				$data['impuesto']   = 0 ;
				$data['abonos']     = $monto1;
				$data['vence']      = $fecha;
				$data['observa1']   = 'ABONA A '.$tipo.$numero;
				$data['transac']    = $transac;
				$data['estampa']    = $estampa;
				$data['hora']       = $hora;
				$data['usuario']    = $usuario;
				$data['banco']      = $codb1;
				$data['tipo_op']    = ($tipo1=='C')? 'CH' :'DE';
				$data['numche']     = $cheque;
				$data['reteiva']    = 0;
				$data['montasa']    = 0;
				$data['monredu']    = 0;
				$data['monadic']    = 0;
				$data['tasa']       = 0;
				$data['reducida']   = 0;
				$data['sobretasa']  = 0;
				$data['exento']     = 0;
				$data['control']    = $control;

				$sql=$this->db->insert_string('sprm', $data);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'gser');}

				//Aplica el AB a la FC
				$itppro=array();
				$itppro['numppro']    = $absprm;
				$itppro['tipoppro']   = 'AB';
				$itppro['cod_prv']    = $codprv;
				$itppro['tipo_doc']   = 'FC';
				$itppro['numero']     = $numero;
				$itppro['fecha']      = $fecha;
				$itppro['monto']      = $monto1;
				$itppro['abono']      = $monto1;
				$itppro['ppago']      = 0;
				$itppro['reten']      = 0;
				$itppro['cambio']     = 0;
				$itppro['mora']       = 0;
				$itppro['transac']    = $transac;
				$itppro['estampa']    = $estampa;
				$itppro['hora']       = $hora;
				$itppro['usuario']    = $usuario;
				$itppro['preten']     = 0;
				$itppro['creten']     = 0;
				$itppro['breten']     = 0;
				$itppro['reteiva']    = 0;
				$mSQL = $this->db->insert_string('itppro', $itppro);
				$ban=$this->db->simple_query($mSQL);
				if(!$ban){ memowrite($mSQL,'gser'); $error++;}
			}
			//Fin de la creacion del abono

			//Si tiene retencion de IVA
			if($reiva>0){
				$ncsprm = $this->datasis->fprox_numero('num_nc');
				$data=array();
				$data['cod_prv']    = $codprv;
				$data['nombre']     = $nombre;
				$data['tipo_doc']   = 'NC';
				$data['numero']     = $ncsprm ;
				$data['fecha']      = $fecha ;
				$data['monto']      = $reiva;
				$data['impuesto']   = 0;
				$data['tipo_ref']   = $tipo;
				$data['num_ref']    = $numero;
				$data['abonos']     = $reiva;
				$data['vence']      = $fecha;
				$data['observa1']   = 'RET/IVA CAUSADA EN';
				$data['observa2']   = 'FACTURA # '.$numero.' DE FECHA '.$fechafac;
				$data['transac']    = $transac;
				$data['estampa']    = $estampa;
				$data['hora']       = $hora;
				$data['usuario']    = $usuario;
				$data['reteiva']    = 0;
				$data['montasa']    = 0;
				$data['monredu']    = 0;
				$data['monadic']    = 0;
				$data['tasa']       = 0;
				$data['reducida']   = 0;
				$data['sobretasa']  = 0;
				$data['exento']     = 0;
				$data['control']    = $control;

				$sql=$this->db->insert_string('sprm', $data);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'gser');}

				//Aplica la NC a la FC
				$itppro=array();
				$itppro['numppro']    = $ncsprm;
				$itppro['tipoppro']   = 'NC';
				$itppro['cod_prv']    = $codprv;
				$itppro['tipo_doc']   = 'FC';
				$itppro['numero']     = $numero;
				$itppro['fecha']      = $fecha;
				$itppro['monto']      = $reiva;
				$itppro['abono']      = $reiva;
				$itppro['ppago']      = 0;
				$itppro['reten']      = 0;
				$itppro['cambio']     = 0;
				$itppro['mora']       = 0;
				$itppro['transac']    = $transac;
				$itppro['estampa']    = $estampa;
				$itppro['hora']       = $hora;
				$itppro['usuario']    = $usuario;
				$itppro['preten']     = 0;
				$itppro['creten']     = 0;
				$itppro['breten']     = 0;
				$itppro['reteiva']    = 0;
				$mSQL = $this->db->insert_string('itppro', $itppro);
				$ban=$this->db->simple_query($mSQL);
				if(!$ban){ memowrite($mSQL,'gser'); $error++;}
			}
			//Fin de la retencion de IVA

			//Si tiene ISLR
			if($reten>0){
				$ncsprm = $this->datasis->fprox_numero('num_nc');
				$data=array();
				$data['cod_prv']    = $codprv;
				$data['nombre']     = $nombre;
				$data['tipo_doc']   = 'NC';
				$data['numero']     = $ncsprm ;
				$data['fecha']      = $fecha ;
				$data['monto']      = $reten;
				$data['impuesto']   = 0;
				$data['tipo_ref']   = $tipo;
				$data['num_ref']    = $numero;
				$data['abonos']     = $reten;
				$data['vence']      = $fecha;
				$data['observa1']   = 'RET/I.S.L.R. CAUSADA EN';
				$data['observa2']   = 'FACTURA # '.$numero.' DE FECHA '.$fechafac;
				$data['transac']    = $transac;
				$data['estampa']    = $estampa;
				$data['hora']       = $hora;
				$data['usuario']    = $usuario;
				$data['reteiva']    = 0;
				$data['montasa']    = 0;
				$data['monredu']    = 0;
				$data['monadic']    = 0;
				$data['tasa']       = 0;
				$data['reducida']   = 0;
				$data['sobretasa']  = 0;
				$data['exento']     = 0;
				$data['control']    = $control;

				$sql=$this->db->insert_string('sprm', $data);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'gser');}

				//Aplica la NC a la FC
				$itppro=array();
				$itppro['numppro']    = $ncsprm;
				$itppro['tipoppro']   = 'NC';
				$itppro['cod_prv']    = $codprv;
				$itppro['tipo_doc']   = 'FC';
				$itppro['numero']     = $numero;
				$itppro['fecha']      = $fecha;
				$itppro['monto']      = $reten;
				$itppro['abono']      = $reten;
				$itppro['ppago']      = 0;
				$itppro['reten']      = 0;
				$itppro['cambio']     = 0;
				$itppro['mora']       = 0;
				$itppro['transac']    = $transac;
				$itppro['estampa']    = $estampa;
				$itppro['hora']       = $hora;
				$itppro['usuario']    = $usuario;
				$itppro['preten']     = 0;
				$itppro['creten']     = 0;
				$itppro['breten']     = 0;
				$itppro['reteiva']    = 0;
				$mSQL = $this->db->insert_string('itppro', $itppro);
				$ban=$this->db->simple_query($mSQL);
				if(!$ban){ memowrite($mSQL,'gser'); $error++;}
			}
			//Fin de la retencion ISLR
		}
		//Fin de la cuenta por pagar

		logusu('gser',"Gasto ${numero} ${codprv} ${fecha} CREADO");
		return true;
	}

	function _pre_delete($do){
		$transac  = $do->get('transac');
		$tipo_doc = $do->get('tipo_doc');
		$cod_prv  = $do->get('proveed');
		$numero   = $do->get('numero');
		$fecha    = $do->get('fecha');
		$id       = $do->get('id');

		if($tipo_doc=='XX'){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='El gasto ya fue anulado.';
			return false;
		}

		if($tipo_doc=='AD'){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='El gasto pertenece a una amotrizacion, solo se puede eliminar reversandola.';
			return false;
		}

		if($tipo_doc=='GA'){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='El gasto pertenece a una nomina, solo se puede eliminar reversandola.';
			return false;
		}

		$this->db->select(array('cod_prv','tipo_doc','monto','abonos'));
		$this->db->from('sprm');
		$this->db->where('transac',$transac);
		$query = $this->db->get();

		$verif=true;
		$abs=$fcs=0;
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				if($row->tipo_doc=='NC' || $row->tipo_doc=='AB'){
					$abs+=$row->monto;
				}
				if($row->tipo_doc=='ND' && $row->abonos!=0 && $row->cod_prv!=$cod_prv){
					$verif=false;
					break;
				}
				if($row->tipo_doc==$tipo_doc && $row->cod_prv==$cod_prv){
					$fcs=$row->abonos;
				}
			}
			if(round($fcs-$abs,2)!=0.00){
				$verif=false;
			}
		}

		if(!$verif){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='No se puede anular el gasto por tener abonos, eliminelos antes de continuar.';
			return false;
		}

		$this->db->delete('sprm'  , array('transac' => $transac));
		$this->db->delete('itppro', array('transac' => $transac));
		$this->_rm_gserrete($transac);
		$this->_rm_bmovgser($transac);

		$dbcod_prv = $this->db->escape($cod_prv);
		$dbnumero  = $this->db->escape($numero);
		$dbfecha   = $this->db->escape($fecha);
		$dbtransac = $this->db->escape($transac);
		//Borra los articulos
		$mSQL="DELETE FROM gitser WHERE numero=${dbnumero} AND fecha=${dbfecha} AND proveed=${dbcod_prv} AND transac=${dbtransac}";
		$this->db->simple_query($mSQL);

		//Borra las retenciones islr
		$mSQL='DELETE FROM gereten WHERE origen=\'GSER\' AND idd='.$id;
		$this->db->simple_query($mSQL);

		//Anula la retencion de IVA
		if($this->datasis->dameval("SELECT COUNT(*) FROM riva WHERE transac=${dbtransac}") > 0){
			$mTRANULA = '_'.$this->datasis->fprox_numero('rivanula',7);
			$this->db->simple_query("UPDATE riva SET transac='${mTRANULA}' WHERE transac=${dbtransac}");
		}
		//Fin de la anulacion de iva


		//Revisa si fue anulado previamente para borrar el registro y evitar registro duplicado
		$query     = $this->db->query("SELECT id, transac FROM gser WHERE tipo_doc='XX' AND fecha=${dbfecha} AND numero=${dbnumero} AND proveed=${dbcod_prv}");
		foreach($query->result() as $row){
			$dbitid     = $this->db->escape($row->id);
			$dbittransac= $this->db->escape($row->transac);

			$mSQL='DELETE FROM gser WHERE id='.$dbitid;
			$this->db->simple_query($mSQL);

			$mSQL="DELETE FROM gitser WHERE numero=${dbnumero} AND fecha=${dbfecha} AND proveed=${dbcod_prv} AND transac=${dbittransac}";
			$this->db->simple_query($mSQL);
		}
		//Fin de la eliminacion del registro

		$this->db->where( 'id'  , $id);
		$this->db->update('gser', array('tipo_doc'=>'XX'));

		$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Gasto Anulado.';
		logusu('gser',"Gasto ${numero} fecha ${fecha} proveed ${cod_prv} Anulado");
		return false;
	}

	function _pre_update($do){
		return false;
	}

	function _post_update($do){
		$codigo=$do->get('numero');
		logusu('gser',"Gasto ${codigo} Modificado");
	}

	function _post_delete($do){
		$codigo=$do->get('numero');
		logusu('gser',"Gasto ${codigo} ELIMINADO");
	}

	//Chequea que exista un monto cuando se seleccion un banco/caja
	function chmontocontado($val){
		$codb1=$this->input->post('codb1');
		if(!empty($codb1)){
			if($val<=0){
				$this->validation->set_message('chmontocontado', 'El campo %s no puede ser menor o igual a cero si selecciono una caja o banco');
				return false;
			}
		}
		return true;
	}

	function chtasa($monto){
		$iva   = $this->input->post('montasa');
		$iva   = (empty($iva))?   0: $iva  ;
		$monto = (empty($monto))? 0: $monto;
		if(!is_numeric($monto)){
			$this->validation->set_message('chtasa', 'El campo %s general debe contener n&uacute;meros.');
			return false;
		}

		if($monto>0 && $iva>0){
			return true;
		}elseif($monto==0 && $iva==0){
			return true;
		}else{
			$this->validation->set_message('chtasa', 'Si la base general es mayor que cero debe generar impuesto');
			return false;
		}
	}

	function chreducida($monto){
		$iva=$this->input->post('monredu');
		$iva   = (empty($iva))?   0: $iva  ;
		$monto = (empty($monto))? 0: $monto;
		if(!is_numeric($monto)){
			$this->validation->set_message('chreducida', 'El campo %s reducida debe contener numeros.');
			return false;
		}

		if($monto>0 && $iva>0){
			return true;
		}elseif($monto==0 && $iva==0){
			return true;
		}else{
			$this->validation->set_message('chreducida', 'Si la base reducida es mayor que cero debe generar impuesto');
			return false;
		}
	}

	function chsobretasa($monto){
		$iva=$this->input->post('monadic');
		$iva   = (empty($iva))?   0: $iva  ;
		$monto = (empty($monto))? 0: $monto;
		if(!is_numeric($monto)){
			$this->validation->set_message('chsobretasa', 'El campo %s adicional debe contener numeros.');
			return false;
		}

		if($monto>0 && $iva>0){
			return true;
		}elseif($monto==0 && $iva==0){
			return true;
		}else{
			$this->validation->set_message('chsobretasa', 'Si la base adicional es mayor que cero debe generar impuesto');
			return false;
		}
	}

	function printrete($id_gser){
		$sel=array('b.id');
		$this->db->select($sel);
		$this->db->from('gser AS a');
		$this->db->join('riva AS b','a.transac=b.transac');
		$this->db->where('a.id' , $id_gser);
		$mSQL_1 = $this->db->get();

		if ($mSQL_1->num_rows() == 0){ show_error('Retención no encontrada');}

		$row = $mSQL_1->row();
		$id  = $row->id;
		redirect("formatos/ver/RIVA/${id}");
	}

	function sprvbu(){
		$control  = $this->uri->segment(4);
		$dbcontrol= $this->db->escape($control);
		$id = $this->datasis->dameval("SELECT b.id FROM gser a JOIN sprv b ON a.proveed=b.proveed WHERE control=${dbcontrol}");
		redirect('compras/sprv/dataedit/show/'.$id);
	}

	function tabla(){
		$id       = $this->uri->segment($this->uri->total_segments());
		$dbid     = $this->db->escape($id);
		$transac  = $this->datasis->dameval("SELECT transac FROM gser WHERE id=${dbid}");
		$dbtransac= $this->db->escape($transac );
		$mSQL = "SELECT cod_prv, MID(CONCAT(TRIM(cod_prv),' ',nombre),1,25) nombre, tipo_doc, numero, monto, abonos FROM sprm WHERE transac=${dbtransac} ORDER BY cod_prv";
		$query = $this->db->query($mSQL);
		$codprv = 'XXXXXXXXXXXXXXXX';
		$salida = '';
		$saldo = 0;
		if ( $query->num_rows() > 0 ){
			$salida  = '<br><table width=\'100%\' border=\'1\'>';
			$salida .= '<tr bgcolor=\'#e7e3e7\'><td>Tp</td><td align=\'center\'>Numero</td><td align=\'center\'>Monto</td></tr>';

			foreach ($query->result_array() as $row){
				if($codprv != $row['cod_prv']){
					$codprv = $row['cod_prv'];
					$salida .= '<tr bgcolor=\'#c7d3c7\'>';
					$salida .= '<td colspan=\'4\'>'.trim($row['nombre']).'</td>';
					$salida .= '</tr>';
				}
				if($row['tipo_doc']=='FC'){
					$saldo = $row['monto']-$row['abonos'];
				}
				$salida .= '<tr>';
				$salida .= '<td>'.$row['tipo_doc'].'</td>';
				$salida .= '<td>'.$row['numero'].  '</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
				$salida .= '</tr>';
			}
			$salida .= '<tr bgcolor=\'#d7c3c7\'><td colspan=\'4\' align=\'center\'>Saldo : '.nformat($saldo).'</td></tr>';
			$salida .= '</table>';
		}

		$mSQL = "SELECT codbanc, banco, tipo_op tipo_doc, numero, monto FROM bmov WHERE transac=${dbtransac} ORDER BY codbanc";
		$query = $this->db->query($mSQL);
		$salida .= "\n";
		if ( $query->num_rows() > 0 ){
			$salida .= '<br><table width=\'100%\' border=\'1\'>';
			$salida .= '<tr bgcolor=\'#e7e3e7\'><td>Tp</td><td align=\'center\'>Banco</td><td align=\'center\'>Monto</td></tr>';
			foreach ($query->result_array() as $row){
				$salida .= '<tr>';
				$salida .= '<td>'.$row['codbanc'].'</td>';
				$salida .= '<td>'.$row['banco'].  '</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
				$salida .= '</tr>';
			}
			$salida .= '</table>';
		}
		echo $salida;
	}

	function instalar(){
		$campos=$this->db->list_fields('gser');
		if(!in_array('reteica',   $campos)) $this->db->query("ALTER TABLE gser ADD COLUMN reteica    DECIMAL(12,2) NULL DEFAULT NULL");
		if(!in_array('retesimple',$campos)) $this->db->query("ALTER TABLE gser ADD COLUMN retesimple DECIMAL(12,2) NULL DEFAULT NULL");
		if(!in_array('negreso',   $campos)) $this->db->query("ALTER TABLE gser ADD COLUMN negreso    CHAR(8)    NULL DEFAULT NULL");
		if(!in_array('ncausado',  $campos)) $this->db->query("ALTER TABLE gser ADD COLUMN ncausado   VARCHAR(8) NULL DEFAULT NULL");
		if(!in_array('fondo',     $campos)) $this->db->query("ALTER TABLE gser ADD COLUMN fondo      CHAR(2)    NULL DEFAULT NULL");
		if(!in_array('cnd',       $campos)) $this->db->query("ALTER TABLE gser ADD COLUMN cnd        CHAR(1)    NULL DEFAULT NULL");

		if(!in_array('id',$campos)){
			$query="ALTER TABLE `gser` DROP PRIMARY KEY";
			$this->db->query($query);
			$query="ALTER TABLE `gser` ADD UNIQUE INDEX `gser` (`fecha`, `numero`, `proveed`)";
			$this->db->query($query);
			$query="ALTER TABLE `gser` ADD COLUMN `id` INT(15) UNSIGNED NULL AUTO_INCREMENT,  ADD PRIMARY KEY (`id`)";
			$this->db->query($query);
			$query="UPDATE gitser AS a
				JOIN gser AS b on a.numero=b.numero and a.fecha = b.fecha and a.proveed = b.proveed
				SET a.idgser=b.id  WHERE a.idgser IS NULL";
			$this->db->query($query);
		}

		$itcampos=$this->db->list_fields('gitser');
		if(!in_array('id',$itcampos)){
			$query="ALTER TABLE gitser ADD COLUMN `id` INT(15) UNSIGNED NULL AUTO_INCREMENT,  ADD PRIMARY KEY (`id`);";
			$this->db->query($query);
		}

		if(!in_array('idgser',$itcampos)){
			$query="ALTER TABLE gitser ADD COLUMN idgser INT(15) UNSIGNED NOT NULL DEFAULT '0' AFTER `id`, ADD INDEX `idgser` (`idgser`)";
			$this->db->query($query);
		}

		$query="UPDATE gitser AS a
			JOIN gser AS b on a.numero=b.numero AND a.fecha = b.fecha AND a.proveed = b.proveed
			SET a.idgser=b.id WHERE a.idgser IS NULL OR a.idgser=0";
		$this->db->query($query);

		if(!$this->db->table_exists('gereten')){
			$mSQL="CREATE TABLE `gereten` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`idd` INT(11) NULL DEFAULT NULL,
				`origen` CHAR(4) NULL DEFAULT NULL,
				`numero` VARCHAR(25) NULL DEFAULT NULL,
				`codigorete` VARCHAR(4) NULL DEFAULT NULL,
				`actividad` VARCHAR(45) NULL DEFAULT NULL,
				`base` DECIMAL(10,2) NULL DEFAULT NULL,
				`porcen` DECIMAL(5,2) NULL DEFAULT NULL,
				`monto` DECIMAL(10,2) NULL DEFAULT NULL,
				`transac` VARCHAR(8) NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `transac` (`transac`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->query($mSQL);
		}
		$gcampos=$this->db->list_fields('gereten');
		if(!in_array('transac',$gcampos)){
			$query="ALTER TABLE `gereten` ADD COLUMN `transac` VARCHAR(8) NULL DEFAULT NULL AFTER `monto`";
			$this->db->query($query);
			$query="ALTER TABLE `gereten` ADD INDEX `transac` (`transac`)";
			$this->db->query($query);
		}

		if (!$this->db->table_exists('gserchi')) {
			$query="CREATE TABLE IF NOT EXISTS `gserchi` (
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
				`usuario` varchar(12) DEFAULT NULL,
				`estampa` date DEFAULT NULL,
				`hora` varchar(8) DEFAULT NULL,
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC";
			$this->db->query($query);
		}

		$gcampos=$this->db->list_fields('gserchi');
		if(!in_array('ngasto',  $gcampos)) $this->db->query("ALTER TABLE gserchi ADD COLUMN ngasto   VARCHAR(8) NULL DEFAULT NULL AFTER departa");
		if(!in_array('aceptado',$gcampos)) $this->db->query("ALTER TABLE gserchi ADD COLUMN aceptado CHAR(1)    NULL DEFAULT NULL AFTER ngasto");
		if(!in_array('cnd',     $gcampos)) $this->db->query("ALTER TABLE gserchi ADD COLUMN cnd      CHAR(1)    NULL DEFAULT NULL AFTER aceptado");

		if (!$this->db->table_exists('rica')) {
			$query="CREATE TABLE rica (
				codigo CHAR(5)      NOT  NULL,
				activi CHAR(14)     NULL DEFAULT NULL,
				aplica CHAR(100)    NULL DEFAULT NULL,
				tasa   DECIMAL(8,2) NULL DEFAULT NULL,
				PRIMARY KEY (codigo)
				) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC";
			$this->db->query($query);
		}
	}
}
