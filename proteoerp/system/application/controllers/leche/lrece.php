<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Lrece extends Controller {
	var $mModulo = 'LRECE';
	var $titp    = 'Recepci&oacute;n de Leche';
	var $tits    = 'Recepci&oacute;n de Leche';
	var $url     = 'leche/lrece/';

	function Lrece(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'LRECE', $ventana=0 );
		$do->table='lrece';
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 900, 600, substr($this->url,0,-1) );
		$this->datasis->creaintramenu( $data = array('modulo'=>'220','titulo'=>'Recepcion de Leche','mensaje'=>'Recepcion de Leche','panel'=>'LECHE','ejecutar'=>'leche/lrece','target'=>'popu','visible'=>'S','pertenece'=>'2','ancho'=>900,'alto'=>600));
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$grid->setHeight('120');
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$grid1->setHeight('180');
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 210, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Panel Derecho
		$WpAdic = "
		<tr><td><div class=\"tema1\"><table id=\"bpos1\"></table></div><div id='pbpos1'></div></td></tr>\n
		<tr><td><div class=\"tema1\">
			<table cellpadding='0' cellspacing='0' style='width:100%;'>
				<tr>
					<td colspan='3' style='text-align:center;border:1px solid #AFAFAF;background:#FEBE25;'><div class='botones'>ANALISIS DE LABORATORIO</div></td>
				</tr>
				<tr>
					<td style='vertical-align:center;border:1px solid #AFAFAF;'><div class='botones'>".img(array('src' =>"images/lab.png",  'height' => 18, 'alt' => 'Imprimir',    'title' => 'Imprimir', 'border'=>'0'))."</div></td>
					<td style='vertical-align:top;text-align:center;'><div class='botones'><a style='width:70px;text-align:left;vertical-align:top;' href='#' id='banalisis'>Analisis</a></div></td>
					<td style='vertical-align:top;'><div class='botones'><a style='width:80px;text-align:left;vertical-align:top;' href='#' id='bvaqueras'>Vaqueras</a></div></td>
				</tr>
			</table>
			</div>
		</td></tr>\n
		";

		$grid->setWpAdicional($WpAdic);

		//Botones Panel Izq
		//$grid->wbotonadd(array('id'=>'imprime',  'img'=>'assets/default/images/print.png','alt' => 'Reimprimir', 'label'=>'Reimprimir Documento'));
		$grid->wbotonadd(array('id'=>'recalcu'  , 'img'=>'images/vaca.png','alt' => 'Recalcular' , 'label'=>'Recalcular'  ));
		$grid->wbotonadd(array('id'=>'bagrega'  , 'img'=>'images/vaca.png','alt' => 'Agrega'     , 'label'=>'Agregar Vaquera'  ));
		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'fedita', 'title'=>'Agregar/Editar Pedido'),
			array('id'=>'fshow' , 'title'=>'Ver Registro'),
			array('id'=>'fborra', 'title'=>'Elimina registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']    = $WestPanel;
		$param['script']       = script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('LRECE', 'JQ');
		$param['otros']        = $this->datasis->otros('LRECE', 'JQ');
		$param['centerpanel']  = $centerpanel;
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		$param['tamano']       = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);

	}

	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0, $grid1 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function lreceshow() {
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				$.post("'.site_url($this->url.'apertura/show').'/"+id,
					function(data){
						$("#fshow").html(data);
						$("#fshow").dialog( "open" );
					});
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		};';

		$bodyscript .= '
		function lrecedel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(r){
						try{
							var json = JSON.parse(r);
							if (json.status == "A"){
								apprise("Registro Eliminado");
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
								return true;
							} else {
								apprise(json.mensaje);
							}
						}catch(e){
							$("#fborra").html(r);
							$("#fborra").dialog( "open" );
						}
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';


		$bodyscript .= '
		function lreceadd() {
			$.post("'.site_url($this->url.'apertura/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog({height: 400, width: 500, title: "Agregar Recepcion de Leche"});
				$("#fedita").dialog( "open" );
			});
		};';

		$bodyscript .= '
		function modanal(id) {
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				$.post("'.site_url('leche/lrece/analisis/modify').'/"+id,
				function(data){
					$("#fedita").html(data);
					$("#fedita").dialog({height: 380, width: 500, title: "Analisis la Recepcion "+id+" Ruta "+ret.ruta+" Nombre "+ret.nombre});
					$("#fedita").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		};';


		$bodyscript .= '
		function lreceedit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'apertura/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog({height: 350, width: 550, title: "Editar Recepcion Nro. "+id+" Ruta "+ret.ruta })
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
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
		jQuery("#imprime").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('formatos/ver/LRECE').'/\'+id+\'/id\'').';
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		});';

		$bodyscript .= '
		jQuery("#bagrega").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');


			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'itvaqueras').'/"+id+"/create", function(data){
					$("#fedita").html(data);
					$("#fedita").dialog({height: 300, width: 550, title: "Editar Recepcion Nro. "+id+" Ruta "+ret.ruta })
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
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
			close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});';

		$bodyscript .= '
		$("#fshow").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
			buttons: {
				"Aceptar": function() {
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				$("#fshow").html("");
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});';


		$bodyscript .= '
		jQuery("#recalcu").click( function(){
			$.post("'.site_url($this->url.'recalcula').'", function(data){
				$.prompt("<h1>Recalculo Concluido</h1>"+data)
			});
		});';


		$bodyscript .= '
		jQuery("#bvaqueras").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				$.post("'.site_url('leche/lrece/vaqueras/modify').'/"+id,
				function(data){
					$("#fedita").html(data);
					$("#fedita").dialog({height: 500, width: 840, title: "Analisis Vaqueras Recepcion "+id+" Ruta "+ret.ruta+" Chofer "+ret.nombre});
					$("#fedita").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		});';

		$bodyscript .= '
		jQuery("#banalisis").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				$.post("'.site_url('leche/lrece/analisis').'/"+id+"/create",
				function(data){
					$("#fedita").html(data);
					$("#fedita").dialog({height: 380, width: 500, title: "Analisis la Recepcion "+id+" Ruta "+ret.ruta+" Nombre "+ret.nombre});
					$("#fedita").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		});';

		$bodyscript .= '
		jQuery("#bcierre").click( function(){
			var id = c
			if (id)	{
				$.post("'.site_url('leche/lrece/cierre/update').'",
				function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		});';

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 400, width: 700, modal: true,
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
								jQuery("#newapi'. $grid1.'").trigger("reloadGrid");
								//'.$this->datasis->jwinopen(site_url('formatos/ver/LRECE').'/\'+res.id+\'/id\'').';
								return true;
							} else {
								apprise(json.mensaje);
							}
						}catch(e){
							$("#fedita").html(r);
						}
					}
			})},
			"Cancelar": function() {
				$( this ).dialog( "close" );
				$("#fedita").html("");
			}
			},
			close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
				$("#fedita").html("");
			}
		});';

		$bodyscript .= '});'."\n";

		$bodyscript .= "\n</script>\n";
		$bodyscript .= "";
		return $bodyscript;
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 45,
			'editable'      => 'false',
			'search'        => 'true'
		));

		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 120,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('fechal');
		$grid->label('Llegada');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha Llegada" }'
		));

		$grid->addField('fechar');
		$grid->label('Recoleccion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha de Recoleccion" }'
		));


		$grid->addField('ruta');
		$grid->label('Ruta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));

		$grid->addField('flete');
		$grid->label('Flete');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));

		$grid->addField('nombre');
		$grid->label('Nombre del Chofer');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:45, maxlength: 45 }',
		));

		$grid->addField('lista');
		$grid->label('Lista');
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

		$grid->addField('lleno');
		$grid->label('Peso Lleno');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 90,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('vacio');
		$grid->label('Peso Vacio');
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

		$grid->addField('neto');
		$grid->label('P.Neto');
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


		$grid->addField('densidad');
		$grid->label('Densidad');
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


		$grid->addField('litros');
		$grid->label('Litros');
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

		$grid->addField('diferen');
		$grid->label('Diferencia');
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
		$grid->addField('animal');
		$grid->label('Animal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));

		$grid->addField('crios');
		$grid->label('Crioscopia');
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

		$grid->addField('h2o');
		$grid->label('Agua %');
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

		$grid->addField('temp');
		$grid->label('Temp.');
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

		$grid->addField('brix');
		$grid->label('Grados Brix');
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

		$grid->addField('grasa');
		$grid->label('Grasa %');
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

		$grid->addField('acidez');
		$grid->label('Acidez');
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

		$grid->addField('cloruros');
		$grid->label('Cloruros');
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

		$grid->addField('dtoagua');
		$grid->label('Dto.Agua');
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
*/
		$grid->addField('transporte');
		$grid->label('Transp.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'int'",
			//'formatter'     => "'number'",
			//'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));

		$grid->addField('lpago');
		$grid->label('R.Pago');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			//'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
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
					var ret = jQuery(gridId1).jqGrid(\'getRowData\',id);
					jQuery(gridId2).jqGrid("setGridParam",{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");

					$.ajax({
						url: "'.base_url().$this->url.'resumen/"+id,
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
		$grid->setAdd(    $this->datasis->sidapuede('LRECE','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('LRECE','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('LRECE','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('LRECE','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: lreceadd,editfunc: lreceedit,delfunc: lrecedel,viewfunc: lreceshow");

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


	//***************************************
	//  Detalle de Analisis
	//
	//***************************************
	function resumen($id){
		$salida = '';
		$nombre = $this->datasis->dameval("SELECT b.nombre FROM lrece a JOIN lruta b ON a.ruta=b.codigo WHERE a.id=$id");
		$query = $this->db->query("SELECT id, observa, round(litros,1) litros FROM lanal WHERE id_lrece=$id");
		if ($query->num_rows() > 0){
			$salida = '<table width="90%" align="center" border="1" cellspacing="0" cellpadding="0" >';
			$salida .= '<tr style="background:#A1AFAF"><th>Analisis</th><th>Litros</th></tr>';
			foreach ($query->result() as $row){
				$salida .= '<tr><td>';
				$salida .= "<a href='#' onclick= 'modanal(".$row->id.")'>";
				$salida .= $row->observa.'</a></td><td align="right">'.$row->litros.'</td></tr>';
			}
			$salida .= '</table>';
		}
		echo '<center>'.$nombre.'</centercenter><br>'. $salida;
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('lrece');

		$response   = $grid->getData('lrece', array(array()), array(), false, $mWHERE, 'id','desc' );
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
			//if(false == empty($data)){
			//	$check = $this->datasis->dameval("SELECT count(*) FROM lrece WHERE $mcodp=".$this->db->escape($data[$mcodp]));
			//	if ( $check == 0 ){
			//		$this->db->insert('lrece', $data);
			//		echo "Registro Agregado";
			//		logusu('LRECE',"Registro ????? INCLUIDO");
			//	} else
			//		echo "Ya existe un registro con ese $mcodp";
			//} else
			//	echo "Fallo Agregado!!!";
		} elseif($oper == 'edit') {
			if($this->datasis->sidapuede('LRECE','CAMBIO DE FECHA%' ) || true){
				$this->db->where('id', $id);
				$this->db->update('lrece', $data);
				logusu('LRECE',"Registro $id MODIFICADO");
				echo "Registro Modificado";
			}else{
				echo "No tiene permisos para modificar el registro";
			}
		} elseif($oper == 'del') {
			//$meco = $this->datasis->dameval("SELECT $mcodp FROM lrece WHERE id=$id");
			////$check =  $this->datasis->dameval("SELECT COUNT(*) FROM lrece WHERE id='$id' ");
			//if ($check > 0){
			//	echo " El registro no puede ser eliminado; tiene movimiento ";
			//} else {
			//	$this->db->simple_query("DELETE FROM lrece WHERE id=$id ");
			//	logusu('LRECE',"Registro ????? ELIMINADO");
			//	echo "Registro Eliminado";
			//}
		};
	}

	//************************************
	//
	//Definicion del Grid y la Forma
	//
	//************************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		//$grid->addField('id');
		//$grid->label('N&uacute;mero');
		//$grid->params(array(
		//	'align'         => "'center'",
		//	'frozen'        => 'true',
		//	'width'         => 40,
		//	'editable'      => 'false',
		//	'search'        => 'false'
		//));

		$grid->addField('vaquera');
		$grid->label('Vaquera');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('nombre');
		$grid->label('Vaquera Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:45, maxlength: 45 }',
		));


		$grid->addField('lista');
		$grid->label('Litros');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 60,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('animal');
		$grid->label('Animal');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('crios');
		$grid->label('Crioscopia');
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


		$grid->addField('h2o');
		$grid->label('Agua %');
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

/*
		$grid->addField('temp');
		$grid->label('Temp.');
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
*/

		$grid->addField('brix');
		$grid->label('Gr.Brix');
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


		$grid->addField('grasa');
		$grid->label('Grasa %');
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


		$grid->addField('acidez');
		$grid->label('Acidez');
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

		$grid->addField('cloruros');
		$grid->label('Cloruros');
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

		$grid->addField('alcohol');
		$grid->label('Alcohol');
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

		$grid->addField('dtoagua');
		$grid->label('Dto.Agua');
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


		//$grid->addField('id_lrece');
		//$grid->label('Id_lrece');
		//$grid->params(array(
		//	'search'        => 'true',
		//	'editable'      => $editar,
		//	'align'         => "'right'",
		//	'edittype'      => "'text'",
		//	'width'         => 100,
		//	'editrules'     => '{ required:true }',
		//	'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
		//	'formatter'     => "'number'",
		//	'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		//));

		$grid->addField('lpago');
		$grid->label('R.Pago');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			//'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));

		$grid->setHeight('170');
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){if (id){var ret = 0;}}
			,cellEdit: true
			,cellsubmit: "remote"
			,cellurl: "'.site_url($this->url.'setdatait/').'"
			,afterSaveCell: function(r,n,v,ir,ic) {
				var id = $(gridId1).jqGrid(\'getGridParam\',\'selrow\');
 				$(gridId1).trigger("reloadGrid");
 				//$(gridId1).loadComplete);
					$(gridId1).setSelection(id,false);
			}
		');


		$grid->setOndblClickRow("");

		$grid->setFormOptionsE('');
		$grid->setFormOptionsA('');
		$grid->setAfterSubmit('');

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(true);
		$grid->setDelete(false);
		$grid->setSearch(false);
		$grid->setRowNum(90);
		$grid->setShrinkToFit('false');


		#Set url
		$grid->setUrlput(site_url($this->url.'setdatait/'));

		#GET url
		//$grid->setUrlget(site_url($this->url.'getdatait/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdatait( $id = 0 ){
		if ($id === 0 ){
			$id = $this->datasis->dameval("SELECT MAX(id) AS id FROM lrece");
		}
		if(empty($id)) return "";
		$grid    = $this->jqdatagrid;
		$mSQL    = 'SELECT * FROM itlrece WHERE id_lrece='.$this->db->escape($id).' ORDER BY nombre';
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setDatait(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$this->db->where('id', $id);
			$this->db->update('itlrece', $data);
			logusu('LRECE',"Registro $id MODIFICADO");
			$idlrece = $this->datasis->dameval("SELECT id_lrece FROM itlrece WHERE id=$id");
			//SUMA AL ENCABEZADO
			$litros = $this->datasis->dameval("SELECT sum(lista) FROM itlrece WHERE id_lrece=$idlrece");
			$this->db->query("UPDATE lrece SET lista=$litros WHERE id=$idlrece");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
				echo " El registro no puede ser eliminado; tiene movimiento ";
		};
	}

	//***********************************
	// DataEdit
	//***********************************
	function apertura(){
		$this->rapyd->load('dataedit');

		$script='
		$(document).ready(function() {
			$("#flete").autocomplete({
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
									$("#proveed").val("");
									$("#proveed_val").text("");
									$("#flete").val("");
								}else{
									$.each(data,
										function(i, val){
											sugiere.push( val );
										});
								}
								add(sugiere);
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$("#flete").attr("readonly", "readonly");
					$("#proveed").val(ui.item.nombre);
					$("#proveed_val").text(ui.item.nombre);
					$("#flete").val(ui.item.proveed);
					setTimeout(function(){ $("#flete").removeAttr("readonly"); }, 1500);
				}
			});
		});';


		$do = new DataObject('lrece');
		$do->pointer('sprv' ,'sprv.proveed=lrece.flete','sprv.nombre AS sprvnombre','left');

		$edit = new DataEdit('', 'lrece');
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->on_save_redirect=false;
		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert', '_post_apertura_insert');
		$edit->post_process('update', '_post_apertura_update');

		$edit->post_process('delete', '_post_apertura_delete');
		$edit->pre_process( 'insert', '_pre_apertura_insert');
		$edit->pre_process( 'update', '_pre_apertura_update');
		$edit->pre_process( 'delete', '_pre_apertura_delete');

		$edit->container = new containerField("alert","<b class='alert'>Cambiar la ruta eliminar&aacute; los analisis por vaqueras en caso de tenerlos<b>");
		$edit->container->when = array('modify');

		$edit->ruta = new dropdownField('Ruta', 'ruta');
		$edit->ruta->option('','Seleccionar');
		$edit->ruta->options('SELECT codigo, CONCAT(nombre," ", codigo) nombre FROM lruta ORDER BY nombre');
		$edit->ruta->rule   = 'trim|required';
		$edit->ruta->style  = 'width:300px';

		$edit->lleno = new inputField('Peso lleno','lleno');
		$edit->lleno->rule       = 'numeric|required|callback_chlleno';
		$edit->lleno->css_class  = 'inputnum';
		$edit->lleno->insertValue= '0';
		$edit->lleno->size       =  9;
		$edit->lleno->maxlength  = 16;
		$edit->lleno->showformat = 'decimal';

		$edit->nombre = new inputField('Chofer','nombre');
		$edit->nombre->rule      = 'strtoupper|required';
		$edit->nombre->size      = 40;
		$edit->nombre->maxlength = 45;

		$edit->flete = new inputField('Flete','flete');
		$edit->flete->size      = 6;
		$edit->flete->maxlength = 5;

		$edit->proveed = new inputField('Proveedor','proveed');
		$edit->proveed->pointer   = true;
		$edit->proveed->size      = 40;
		$edit->proveed->type      = 'inputhidden';
		$edit->proveed->maxlength = 45;
		$edit->proveed->in        = 'flete';

		$edit->fechal = new DateonlyField('Fecha llegada', 'fechal','d/m/Y');
		$edit->fechal->size = 12;
		$edit->fechal->rule='required|chfecha';
		$edit->fechal->insertValue = date('Y-m-d');
		$edit->fechal->calendar = false;

		$edit->fechar = new DateonlyField('Fecha Recolecci&oacute;n', 'fechar','d/m/Y');
		$edit->fechar->size = 12;
		$edit->fechar->rule='required|chfecha';
		$edit->fechar->insertValue = date('Y-m-d');
		$edit->fechar->calendar = false;

		$fechai = $edit->getval('fechal');
		if($fechai !== false){
			$dbfechai=$this->db->escape($fechai);
		}else{
			$dbfechai='CURDATE()';
		}
		$edit->transporte = new dropdownField('Transporte', 'transporte');
		$edit->transporte->option('','Seleccionar');
		$edit->transporte->options("SELECT id, CONCAT(ruta, ' ', nombre) AS val FROM lrece WHERE fechal=${dbfechai} AND MID(ruta,1,1)='G' ORDER BY nombre");
		$edit->transporte->rule  ='condi_required|callback_chtransporte';
		$edit->transporte->style ='width:240px;';

		$edit->vacio = new inputField('Peso vac&iacute;o','vacio');
		$edit->vacio->rule       = 'numeric|required';
		$edit->vacio->css_class  = 'inputnum';
		$edit->vacio->size       = 9;
		$edit->vacio->insertValue= "0";
		$edit->vacio->maxlength  = 16;
		$edit->vacio->onkeyup    = 'calconeto()';
		$edit->vacio->showformat = 'decimal';

		$edit->neto = new inputField('Peso Neto','neto');
		$edit->neto->rule       = 'numeric';
		$edit->neto->css_class  = 'inputnum';
		$edit->neto->type       = 'inputhidden';
		$edit->neto->size       = 9;
		$edit->neto->maxlength  = 7;
		$edit->neto->showformat = 'decimal';

		$edit->densidad = new inputField('Densidad','densidad');
		$edit->densidad->rule      = 'numeric|required';
		$edit->densidad->css_class = 'inputnum';
		$edit->densidad->size      = 7;
		$edit->densidad->maxlength = 10;
		$edit->densidad->onkeyup   = 'calcolitro()';

		$edit->litros = new inputField('Litros','litros');
		$edit->litros->rule       = 'numeric';
		$edit->litros->css_class  = 'inputnum';
		$edit->litros->size       =  9;
		$edit->litros->maxlength  = 16;
		$edit->litros->type       = 'inputhidden';

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form'] =&  $edit;
			$this->load->view('view_lreceap', $conten);
		}
	}

	function chlleno($val){
		$val=floatval($val);
		$ruta=$this->input->post('ruta');
		if(substr($ruta,0,1)=='G' && $val<=0){
			$this->validation->set_message('chlleno', 'El campo %s es obligatorio para la ruta '.$ruta);
			return false;
		}
		return true;
	}

	function chtransporte($val){
		$lleno=floatval($this->input->post('lleno'));
		$ruta =$this->input->post('ruta');
		if(substr($ruta,0,1)=='R' && $lleno==0.00 && empty($val)){
			$this->validation->set_message('chtransporte', 'El campo %s es obligatorio para la ruta '.$ruta);
			return false;
		}
		if(empty($lleno) && empty($val)){
			$this->validation->set_message('chtransporte', 'El campo %s es obligatorio cuando no hay lleno');
			return false;
		}
		return true;
	}


	//Se usa solo para borrar
	function dataedit(){
		$this->rapyd->load('dataedit');
		$edit = new DataEdit('', 'lrece');

		$edit->on_save_redirect=false;

		$edit->post_process('delete', '_post_dataedit_delete');
		$edit->pre_process( 'insert', '_pre_dataedit_insert');
		$edit->pre_process( 'update', '_pre_dataedit_update');
		$edit->pre_process( 'delete', '_pre_dataedit_delete');
		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro eliminado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			//echo $edit->output;
			$conten['form'] =&  $edit;
			$this->load->view('view_lrecean', $conten);
		}
	}

	function _pre_dataedit_insert($do){
		return false;
	}
	function _pre_dataedit_update($do){
		return false;
	}
	function _pre_dataedit_delete($do){
		$fecha  = $do->get('fecha');
		$dbfecha= $this->db->escape($fecha);
		$cana   = $this->datasis->dameval('SELECT COUNT(*) FROM lcierre WHERE fecha='.$dbfecha);

		if($cana>0){
			$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'Ya el d&iacute;a '.dbdate_to_human($fecha).' fue cerrado.';
			return false;
		}
	}

	function _post_dataedit_delete($do){
		$id   = $do->get('id');
		$dbid = $this->db->escape($id);

		$mSQL="DELETE FROM itlrece WHERE id_lrece=${dbid}";
		$this->db->simple_query($mSQL);

		$mSQL="DELETE FROM lanal  WHERE id_lrece=${dbid}";
		$this->db->simple_query($mSQL);

		logusu('lrece','Recepcion $id eliminada');
	}
	//Fin para el borrado


	//****************************************
	//
	//    Analisis de Laboratorio
	//
	function analisis($id_lrece){
		$this->rapyd->load('dataedit');

		$script= '
		$(document).ready(function() {
			$(".inputnum").numeric(".");
		});';

		$script= '';

		$edit = new DataEdit('', 'lanal');

		$edit->script($script,'modify');
		$edit->script($script,'create');

		$edit->on_save_redirect=false;
		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('update','_post_analisis_update');
		$edit->pre_process( 'insert', '_pre_analisis_insert');
		$edit->pre_process( 'update', '_pre_analisis_update');
		$edit->pre_process( 'delete', '_pre_analisis_delete');


		$edit->numero = new inputField('N&uacute;mero','id');
		$edit->numero->rule='max_length[8]';
		$edit->numero->size =9;
		$edit->numero->mode='autohide';
		$edit->numero->maxlength =8;


		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->mode='autohide';
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;
/*
		$edit->ruta = new inputField('Ruta','ruta');
		$edit->ruta->rule='max_length[4]';
		$edit->ruta->mode='autohide';
		$edit->ruta->size =6;
		$edit->ruta->maxlength =4;
*/
		$edit->observa = new inputField('Observaciones','observa');
		//$edit->observa->mode='autohide';
		$edit->observa->insertValue='TQ1';
		$edit->observa->size =40;
		$edit->observa->maxlength =45;
/*
		$edit->lleno = new inputField('Peso lleno','lleno');
		$edit->lleno->rule='max_length[16]|numeric|required';
		$edit->lleno->css_class='inputnum';
		$edit->lleno->size =12;
		$edit->lleno->mode='autohide';
		$edit->lleno->maxlength =16;
		$edit->lleno->showformat = 'decimal';

		$edit->vacio = new inputField('Peso vac&iacute;o','vacio');
		$edit->vacio->rule='max_length[16]|numeric|required';
		$edit->vacio->css_class='inputnum';
		$edit->vacio->size =7;
		$edit->vacio->maxlength =16;
		$edit->vacio->onkeyup = 'calconeto()';
		$edit->vacio->showformat   = 'decimal';

		$edit->neto = new inputField('Peso Neto','neto');
		$edit->neto->rule='numeric';
		$edit->neto->css_class='inputnum';
		$edit->neto->type = 'inputhidden';
		$edit->neto->size=7;
		$edit->neto->maxlength=7;
		$edit->neto->showformat   = 'decimal';

		$edit->densidad = new inputField('Densidad','densidad');
		$edit->densidad->rule='numeric|required';
		$edit->densidad->css_class='inputnum';
		$edit->densidad->size =5;
		$edit->densidad->maxlength =10;
		$edit->densidad->onkeyup = 'calcolitro()';
*/
		$edit->litros = new inputField('Litros','litros');
		$edit->litros->rule='numeric';
		$edit->litros->css_class='inputnum';
		$edit->litros->size =7;
		$edit->litros->insertValue=round($this->datasis->dameval('SELECT SUM(lista) AS lt FROM itlrece WHERE id_lrece='.$this->db->escape($id_lrece)),2);
		$edit->litros->maxlength =16;
		//$edit->litros->type = 'inputhidden';
/*
		$edit->lista = new inputField('Litros lista','lista');
		$edit->lista->rule='max_length[16]|numeric';
		$edit->lista->css_class='inputnum';
		$edit->lista->size =12;
		$edit->lista->maxlength =16;
*/
		$edit->animal = new  dropdownField ('Animal', 'animal');
		$edit->animal->option('M' ,'Mezcla');
		$edit->animal->option('V' ,'Vaca');
		$edit->animal->option('B' ,'Bufala');
		$edit->animal->rule = 'required';
		$edit->animal->style= 'width:100px;';

		$edit->crios = new inputField('Criosc&oacute;pia','crios');
		$edit->crios->rule='max_length[10]|numeric|required';
		$edit->crios->css_class='inputnum';
		$edit->crios->insertValue='536';
		$edit->crios->size =7;
		$edit->crios->maxlength =10;

		$edit->h2o = new inputField('Agua %','h2o');
		$edit->h2o->rule='max_length[10]|numeric|porcent|required';
		$edit->h2o->css_class='inputnum';
		$edit->h2o->size =7;
		$edit->h2o->insertValue='0';
		$edit->h2o->maxlength =10;
		$edit->h2o->onkeyup = 'descuagua()';

		$edit->temp = new inputField('Temperatura','temp');
		$edit->temp->rule='max_length[10]|numeric|required';
		$edit->temp->css_class='inputnum';
		$edit->temp->insertValue='7';
		$edit->temp->size =7;
		$edit->temp->maxlength =10;

		$edit->brix = new inputField('Grados Brix','brix');
		$edit->brix->rule='max_length[10]|numeric|required';
		$edit->brix->css_class='inputnum';
		$edit->brix->insertValue='9.4';
		$edit->brix->size =7;
		$edit->brix->maxlength =10;

		$edit->grasa = new inputField('Grasa %','grasa');
		$edit->grasa->rule='max_length[10]|numeric|porcent|required';
		$edit->grasa->css_class='inputnum';
		$edit->grasa->insertValue='4.2';
		$edit->grasa->size =7;
		$edit->grasa->maxlength =10;

		$edit->acidez = new inputField('Acidez','acidez');
		$edit->acidez->rule='max_length[10]|numeric|required';
		$edit->acidez->css_class='inputnum';
		$edit->acidez->insertValue='16';
		$edit->acidez->size =7;
		$edit->acidez->maxlength =10;

		$edit->cloruros = new inputField('Cloruros','cloruros');
		$edit->cloruros->rule='max_length[10]|numeric|required';
		$edit->cloruros->css_class='inputnum';
		$edit->cloruros->insertValue='200';
		$edit->cloruros->size =7;
		$edit->cloruros->maxlength =10;

		$edit->alcohol = new inputField('Alcohol','alcohol');
		$edit->alcohol->rule='numeric|required';
		$edit->alcohol->insertValue='-1';
		$edit->alcohol->css_class='inputnum';
		$edit->alcohol->size =7;
		$edit->alcohol->maxlength =10;

		$edit->dtoagua = new inputField('Dcto. de Agua','dtoagua');
		$edit->dtoagua->rule='max_length[10]|numeric|required';
		$edit->dtoagua->css_class='inputnum';
		$edit->dtoagua->size =7;
		$edit->dtoagua->maxlength =10;

//		$edit->id_lrece = new inputField('id_lrece','id_lrece');
//		$edit->id_lrece->rule        = 'numeric|required';
//		$edit->id_lrece->insertValue = $this->uri->segment(5);
//		$edit->id_lrece->css_class   = 'inputnum';
//		$edit->id_lrece->size        =  7;
//		$edit->id_lrece->maxlength   = 10;
//		$edit->id_lrece->

		$edit->id_lrece  = new autoUpdateField('id_lrece',$this->uri->segment(4), $this->uri->segment(4) );


		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			//echo $edit->output;
			$conten['form'] =&  $edit;
			$this->load->view('view_lrecean', $conten);
		}
	}

	//****************************************
	//
	// Analisis de Laboratorio de Vaqueras
	//
	function vaqueras($status,$id){

		$dbid=$this->db->escape($id);
		$cana=$this->datasis->dameval("SELECT COUNT(*) AS cana FROM itlrece WHERE id_lrece=$dbid");

		if(empty($cana)){
			$mSQL="SELECT b.codigo, b.nombre, b.id, a.animal  FROM lrece AS a JOIN lvaca AS b ON a.ruta=b.ruta WHERE a.id=$dbid";
			$query = $this->db->query($mSQL);

			if ($query->num_rows() > 0){
				foreach ($query->result() as $row){
					$data=array();
					$data['vaquera']    = $row->codigo;
					$data['nombre']     = $row->nombre;
					$data['densidad']   = 1.0164;
					$data['lista']      = 0;
					$data['animal']     = 'V';
					$data['crios']      = 0;
					$data['h2o']        = 0;
					$data['temp']       = 10;
					$data['brix']       = 0;
					$data['grasa']      = 0;
					$data['acidez']     = 0;
					$data['cloruros']   = 0;
					$data['alcohol']    = 0;
					$data['dtoagua']    = 0;
					$data['id_lvaca']   = $row->id;
					$data['id_lrece']   = $id;

					$sql = $this->db->insert_string('itlrece', $data);
					$this->db->query($sql);
				}
			}else{
				echo 'Ruta no tiene vaqueras';
				exit();
			}
		}

		$this->rapyd->load('dataobject','datadetails');

		$script= '
		$(document).ready(function() {
			$(".inputnum").numeric(".");
		});';

		$do = new DataObject('lrece');
		$do->rel_one_to_many('itlrece', 'itlrece', array('id'=>'id_lrece'));

		$do->pointer('sprv' ,'sprv.proveed=lrece.flete','sprv.nombre AS sprvnombre','left');
		$do->rel_pointer('itlrece','lvaca','lvaca.id=itlrece.id_lvaca','lvaca.nombre AS lvacadescrip,lvaca.codigo AS lvacacodigo','left');
		$do->order_rel_one_to_many('itlrece','id');

		$edit = new DataDetails('Recepci&oacute;n de leche',$do);
		$edit->on_save_redirect=false;
		$edit->script($script,'modify');
		$edit->script($script,'create');

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('update','_post_vaqueras_update');

		$edit->pre_process( 'insert', '_pre_vaqueras_insert');
		$edit->pre_process( 'update', '_pre_vaqueras_update');
		$edit->pre_process( 'delete', '_pre_vaqueras_delete');

		$edit->id = new inputField('N&uacute;mero','id');
		$edit->id->rule='max_length[8]';
		$edit->id->mode = 'autohide';
		$edit->id->size =9;
		$edit->id->maxlength =8;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->mode = 'autohide';
		$edit->fecha->maxlength =8;

		$edit->ruta = new inputField('Ruta','ruta');
		$edit->ruta->rule='max_length[4]';
		$edit->ruta->mode = 'autohide';
		$edit->ruta->size =6;
		$edit->ruta->maxlength =4;

		$edit->flete = new inputField('Flete','flete');
		$edit->flete->rule='max_length[5]';
		//$edit->chofer->mode = 'autohide';
		$edit->flete->size =6;
		$edit->flete->maxlength =5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[45]';
		$edit->nombre->mode = 'autohide';
		$edit->nombre->size =40;
		$edit->nombre->maxlength =45;

		$edit->lleno = new inputField('Lleno','lleno');
		$edit->lleno->rule='max_length[16]|numeric';
		$edit->lleno->mode = 'autohide';
		$edit->lleno->css_class='inputnum';
		$edit->lleno->size =12;
		$edit->lleno->maxlength =16;

		$edit->vacio = new inputField('Vacio','vacio');
		$edit->vacio->rule='max_length[16]|numeric';
		$edit->vacio->mode = 'autohide';
		$edit->vacio->css_class='inputnum';
		$edit->vacio->size =12;
		$edit->vacio->maxlength =16;

		$edit->neto = new inputField('Neto','neto');
		$edit->neto->rule='max_length[16]|numeric';
		$edit->neto->mode = 'autohide';
		$edit->neto->css_class='inputnum';
		$edit->neto->size =12;
		$edit->neto->maxlength =16;

		$edit->densidad = new inputField('Densidad','densidad');
		$edit->densidad->rule='max_length[10]|numeric';
		$edit->densidad->mode = 'autohide';
		$edit->densidad->css_class='inputnum';
		$edit->densidad->size =12;
		$edit->densidad->maxlength =10;

		$edit->lista = new inputField('Lista','lista');
		$edit->lista->rule='max_length[16]|numeric';
		$edit->lista->mode = 'autohide';
		$edit->lista->css_class='inputnum';
		$edit->lista->size =12;
		$edit->lista->maxlength =16;

		//Diferencia neto-lista
		$edit->diferen = new inputField('Diferencia','diferen');
		$edit->diferen->rule='max_length[16]|numeric';
		$edit->diferen->css_class='inputnum';
		$edit->diferen->size =12;
		$edit->diferen->mode = 'autohide';
		$edit->diferen->maxlength =16;

		$edit->animal = new  dropdownField ('Animal', 'animal');
		$edit->animal->option('V' ,'Vaca');
		$edit->animal->option('B' ,'Bufala');
		$edit->animal->rule = 'required';
		$edit->animal->mode = 'autohide';
		$edit->animal->style= 'width:145px;';

		$edit->crios = new inputField('D. Criosc&oacute;pico','crios');
		$edit->crios->rule='max_length[10]|numeric';
		$edit->crios->css_class='inputnum';
		$edit->crios->mode = 'autohide';
		$edit->crios->size =12;
		$edit->crios->maxlength =10;

		$edit->h2o = new inputField('Agua %','h2o');
		$edit->h2o->rule='max_length[10]|numeric';
		$edit->h2o->mode = 'autohide';
		$edit->h2o->css_class='inputnum';
		$edit->h2o->size =12;
		$edit->h2o->maxlength =10;
		$edit->h2o->insertValue="0";

		$edit->temp = new inputField('Temperatura','temp');
		$edit->temp->rule='max_length[10]|numeric';
		$edit->temp->mode = 'autohide';
		$edit->temp->css_class='inputnum';
		$edit->temp->size =12;
		$edit->temp->maxlength =10;

		$edit->brix = new inputField('Grados Brix','brix');
		$edit->brix->rule='max_length[10]|numeric';
		$edit->brix->mode = 'autohide';
		$edit->brix->css_class='inputnum';
		$edit->brix->size =12;
		$edit->brix->maxlength =10;

		$edit->grasa = new inputField('Grasa %','grasa');
		$edit->grasa->rule='max_length[10]|numeric';
		$edit->grasa->mode = 'autohide';
		$edit->grasa->css_class='inputnum';
		$edit->grasa->size =12;
		$edit->grasa->maxlength =10;

		$edit->acidez = new inputField('Acidez','acidez');
		$edit->acidez->rule='max_length[10]|numeric';
		$edit->acidez->mode = 'autohide';
		$edit->acidez->css_class='inputnum';
		$edit->acidez->size =12;
		$edit->acidez->maxlength =10;

		$edit->cloruros = new inputField('Cloruros','cloruros');
		$edit->cloruros->rule='max_length[10]|numeric';
		$edit->cloruros->mode = 'autohide';
		$edit->cloruros->css_class='inputnum';
		$edit->cloruros->size =12;
		$edit->cloruros->maxlength =10;

		$edit->alcohol = new inputField('Alcohol','alcohol');
		$edit->alcohol->rule='numeric|required';
		$edit->alcohol->mode = 'autohide';
		$edit->alcohol->css_class='inputnum';
		$edit->alcohol->size =7;
		$edit->alcohol->maxlength =10;

		$edit->dtoagua = new inputField('Dto. Agua','dtoagua');
		$edit->dtoagua->rule='max_length[10]|numeric';
		$edit->dtoagua->mode = 'autohide';
		$edit->dtoagua->css_class='inputnum';
		$edit->dtoagua->size =12;
		$edit->dtoagua->maxlength =10;
		$edit->dtoagua->mode = 'autohide';

		//Inicio del detalle
		$edit->itid = new hiddenField('','id_<#i#>');
		$edit->itid->rel_id   = 'itlrece';
		$edit->itid->db_name  = 'id';

		$edit->itid_lvaca = new hiddenField('','id_lvaca_<#i#>');
		$edit->itid_lvaca->rel_id   = 'itlrece';
		$edit->itid_lvaca->db_name  = 'id_lvaca';

		$edit->itvaquera = new hiddenField('','vaquera_<#i#>');
		$edit->itvaquera->rel_id   = 'itlrece';
		$edit->itvaquera->db_name  = 'vaquera';

		$edit->itnombre = new hiddenField('','nombre_<#i#>');
		$edit->itnombre->rel_id   = 'itlrece';
		$edit->itnombre->db_name  = 'nombre';

		$edit->itlvacadescrip = new inputField('','lvacadescrip_<#i#>');
		$edit->itlvacadescrip->db_name = 'lvacadescrip';
		$edit->itlvacadescrip->pointer = true;
		$edit->itlvacadescrip->type='inputhidden';
		$edit->itlvacadescrip->rel_id  = 'itlrece';

		$edit->itlvacacodigo = new inputField('','lvacacodigo_<#i#>');
		$edit->itlvacacodigo->db_name = 'lvacacodigo';
		$edit->itlvacacodigo->type='inputhidden';
		$edit->itlvacacodigo->pointer = true;
		$edit->itlvacacodigo->rel_id  = 'itlrece';

		$edit->itanimal = new  dropdownField ('Animal', 'animal_<#i#>');
		$edit->itanimal->db_name  = 'animal';
		$edit->itanimal->rel_id = 'itlrece';
		$edit->itanimal->option(''  ,'Seleccionar');
		$edit->itanimal->option('M' ,'Mezcla');
		$edit->itanimal->option('V' ,'Vaca');
		$edit->itanimal->option('B' ,'Bufala');
		$edit->itanimal->rule = 'required';
		$edit->itanimal->style='width:70px;';

		$edit->itdensidad = new inputField('Densidad','densidad_<#i#>');
		$edit->itdensidad->db_name = 'densidad';
		$edit->itdensidad->rel_id  = 'itlrece';
		$edit->itdensidad->rule='max_length[10]|numeric|required';
		$edit->itdensidad->css_class='inputnum';
		$edit->itdensidad->size =6;
		$edit->itdensidad->maxlength =10;

		$edit->itlista = new hiddenField('Litros lista','lista_<#i#>');
		$edit->itlista->db_name = 'lista';
		$edit->itlista->rel_id  = 'itlrece';
		//$edit->itlista->rule='max_length[16]|numeric|required';
		//$edit->itlista->css_class='inputnum';
		//$edit->itlista->size =6;
		//$edit->itlista->maxlength =16;

		$edit->itcrios = new inputField('Criosc&oacute;pica','crios_<#i#>');
		$edit->itcrios->db_name  = 'crios';
		$edit->itcrios->rel_id = 'itlrece';
		$edit->itcrios->rule='max_length[10]|numeric|required';
		$edit->itcrios->css_class='inputnum';
		$edit->itcrios->size =6;
		//$edit->itcrios->insertValue='536';
		$edit->itcrios->maxlength =10;

		$edit->ith2o = new inputField('Agua %','h2o_<#i#>');
		$edit->ith2o->db_name  = 'h2o';
		$edit->ith2o->rel_id = 'itlrece';
		$edit->ith2o->rule='max_length[10]|numeric|porcent|required';
		$edit->ith2o->css_class='inputnum';
		$edit->ith2o->insertValue='0';
		$edit->ith2o->size =6;
		$edit->ith2o->maxlength =10;

		$edit->ittemp = new inputField('Temperatura','temp_<#i#>');
		$edit->ittemp->db_name  = 'temp';
		$edit->ittemp->rel_id = 'itlrece';
		$edit->ittemp->rule='max_length[10]|numeric|required';
		$edit->ittemp->css_class='inputnum';
		$edit->ittemp->size =6;
		$edit->ittemp->maxlength =10;

		$edit->itbrix = new inputField('Grados Brix','brix_<#i#>');
		$edit->itbrix->db_name  = 'brix';
		$edit->itbrix->rel_id = 'itlrece';
		$edit->itbrix->rule='max_length[10]|numeric|required';
		$edit->itbrix->css_class='inputnum';
		$edit->itbrix->size =6;
		$edit->itbrix->maxlength =10;

		$edit->itgrasa = new inputField('Grasa %','grasa_<#i#>');
		$edit->itgrasa->db_name  = 'grasa';
		$edit->itgrasa->rel_id = 'itlrece';
		$edit->itgrasa->rule='max_length[10]|numeric|porcent|required';
		$edit->itgrasa->css_class='inputnum';
		//$edit->itgrasa->insertValue='4.2';
		$edit->itgrasa->size =6;
		$edit->itgrasa->maxlength =10;

		$edit->itacidez = new inputField('Acidez','acidez_<#i#>');
		$edit->itacidez->db_name  = 'acidez';
		//$edit->itacidez->insertValue='16';
		$edit->itacidez->rel_id = 'itlrece';
		$edit->itacidez->rule='numeric|required';
		$edit->itacidez->css_class='inputnum';
		$edit->itacidez->size =6;
		$edit->itacidez->maxlength =10;

		$edit->itcloruros = new inputField('Cloruros','cloruros_<#i#>');
		$edit->itcloruros->db_name  = 'cloruros';
		$edit->itcloruros->rel_id = 'itlrece';
		$edit->itcloruros->rule='numeric|required';
		$edit->itcloruros->css_class='inputnum';
		//$edit->itcloruros->insertValue='200';
		$edit->itcloruros->size =6;
		$edit->itcloruros->maxlength =10;

		$edit->italcohol = new inputField('Alcohol','alcohol_<#i#>');
		$edit->italcohol->db_name  = 'alcohol';
		$edit->italcohol->rule='numeric|required';
		$edit->italcohol->css_class='inputnum';
		$edit->italcohol->size =4;
		//$edit->italcohol->insertValue='-1';
		$edit->italcohol->rel_id = 'itlrece';
		$edit->italcohol->maxlength =10;

/*
		$edit->itdtoagua = new inputField('Dto. Agua','dtoagua_<#i#>');
		$edit->itdtoagua->db_name  = 'dtoagua';
		$edit->itdtoagua->rel_id = 'itlrece';
		$edit->itdtoagua->rule='numeric|required';
		$edit->itdtoagua->css_class='inputnum';
		$edit->itdtoagua->size =6;
		$edit->itdtoagua->maxlength =10;
*/
		//Fin del detalle

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form'] =&  $edit;
			$this->load->view('view_lrece', $conten);
		}
	}

	function itvaqueras($id_lrece){

		$this->rapyd->load('dataedit');

		$script= "
		$(document).ready(function() {
			$('.inputnum').numeric('.');

			$('#vaquera').autocomplete({
				source: function( req, add){
					$.ajax({
						url:  '".site_url('ajax/buscalvaca')."',
						type: 'POST',
						dataType: 'json',
						data: 'q='+req.term,
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$('#vaquera').val('')
									$('#nombre').val('');
									$('#nombre_val').text('');
									$('#id_lvaca').val('');
								}else{
									$.each(data,
										function(i, val){
											sugiere.push( val );
										}
									);
									add(sugiere);
								}
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$('#vaquera').attr('readonly', 'readonly');
					$('#vaquera').val(ui.item.vaquera);
					$('#nombre').val(ui.item.nombre);
					$('#nombre_val').text(ui.item.nombre);
					$('#id_lvaca').val(ui.item.id_lvaca);

					setTimeout(function() {  $('#vaquera').removeAttr('readonly'); }, 1500);
				}
			});

		});";

		$edit = new DataEdit('Recepci&oacute;n de leche','itlrece');
		$edit->on_save_redirect=false;
		$edit->script($script,'modify');
		$edit->script($script,'create');

		//$edit->post_process('update','_post_vaqueras_update');
		$edit->post_process('insert', '_post_itvaqueras_insert');
		$edit->pre_process( 'insert', '_pre_itvaqueras_insert');
		//$edit->pre_process( 'update', '_pre_vaqueras_update');
		//$edit->pre_process( 'delete', '_pre_vaqueras_delete');

		$edit->vaquera = new inputField('Vaquera','vaquera');
		$edit->vaquera->db_name  = 'vaquera';
		$edit->vaquera->size =8;
		$edit->vaquera->maxlength=5;
		$edit->vaquera->rule = 'required|trim';

		$edit->itid_lvaca = new hiddenField('','id_lvaca');
		$edit->itid_lvaca->db_name  = 'id_lvaca';
		$edit->itid_lvaca->in ='vaquera';

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->db_name  = 'nombre';
		$edit->nombre->size =50;
		$edit->nombre->type='inputhidden';
		$edit->nombre->maxlength=45;
		$edit->nombre->rule = 'required|trim';

		$edit->id_lrece   = new autoUpdateField('id_lrece',$id_lrece,$id_lrece);

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

	//******************************************************************
	// RECALCULA LOS VALORES DE LAS RECEPCIONES
	//******************************************************************
	function recalcula(){
		$mSQL = 'UPDATE lrece a SET lista=(SELECT SUM(lista) FROM itlrece b WHERE a.id=b.id_lrece );';
		$this->db->query($mSQL);
		$mSQL = 'UPDATE lrece SET litros=lleno, neto=lleno, diferen=lleno-lista WHERE vacio=0 AND lleno>0;';
		$this->db->query($mSQL);
		$mSQL = 'UPDATE lrece SET lista=TRUNCATE(lista,0);';
		$this->db->query($mSQL);
		$mSQL = 'UPDATE lrece SET litros=TRUNCATE(ROUND((lleno-vacio)/densidad,2),0), neto=lleno-vacio, diferen=ROUND((lleno-vacio)/densidad,2)-lista
		WHERE vacio>0 AND lleno>0;';
		$this->db->query($mSQL);
		$mSQL = 'UPDATE lrece SET diferen=litros-lista;';
		$this->db->query($mSQL);
		echo "Calculo Concluido";
	}



	//Pre y Pos para la apertura
	function _pre_itvaqueras_insert($do){
		$dbcodigo = $this->db->escape($do->get('vaquera'));
		$id=$this->datasis->dameval('SELECT id FROM lvaca WHERE codigo='.$dbcodigo);

		$do->set('id_lvaca',$id);
	}

	function _post_itvaqueras_insert($do){
		$codigo  = $do->get('vaquera');
		$id_lrece= $do->get('id_lrece');

		logusu($do->table,"Agregada vaquera adicional ${codigo} Recepcion: ${id_lrece}");
	}

	function _post_apertura_insert($do){
		$primary = implode(',',$do->pk);
		// Crea los itrece
		$mSQL="SELECT b.id, b.codigo, b.nombre FROM lrece AS a JOIN lvaca AS b ON a.ruta=b.ruta WHERE a.id=$primary";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$data=array();
				$data['vaquera']   = $row->codigo ;
				$data['nombre']    = $row->nombre ;
				$data['densidad']   = 1.032;
				$data['lista']      = 0;
				$data['animal']     = 'V';
				$data['crios']      = 0;
				$data['h2o']        = 0;
				$data['temp']       = 10;
				$data['brix']       = 0;
				$data['grasa']      = 0;
				$data['acidez']     = 0;
				$data['cloruros']   = 0;
				$data['dtoagua']    = 0;
				$data['id_lvaca']   = $row->id;
				$data['id_lrece']   = $primary;

				$sql = $this->db->insert_string('itlrece', $data);
				$this->db->query($sql);
			}
		}else{
			echo 'Ruta no tiene vaqueras';
		}
		logusu($do->table,"Ingreso recepcion $this->tits $primary ");
	}


	function _post_apertura_update($do){
		$ruta   = $do->get('ruta');
		$id     = $do->get('id');
		$dbruta = $this->db->escape($ruta);

		$mSQL="DELETE itlrece FROM itlrece JOIN lvaca ON itlrece.id_lvaca=lvaca.id WHERE lvaca.ruta != $dbruta AND id_lrece = $id";
		$this->db->query($mSQL);

		$mSQL="SELECT b.id, b.codigo, b.nombre FROM lrece AS a JOIN lvaca AS b ON a.ruta=b.ruta WHERE a.id=$id";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$data=array();
				$data['vaquera']   = $row->codigo ;
				$data['nombre']    = $row->nombre ;
				$data['densidad']   = 1.032;
				$data['lista']      = 0;
				$data['animal']     = 'V';
				$data['crios']      = 0;
				$data['h2o']        = 0;
				$data['temp']       = 10;
				$data['brix']       = 0;
				$data['grasa']      = 0;
				$data['acidez']     = 0;
				$data['cloruros']   = 0;
				$data['dtoagua']    = 0;
				$data['id_lvaca']   = $row->id;
				$data['id_lrece']   = $id;

				$sql = $this->db->insert_string('itlrece', $data);
				$sql = str_replace('INSERT ','INSERT IGNORE ',$sql);
				$this->db->query($sql);
			}
		}else{
			echo 'Ruta no tiene vaqueras';
		}

		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico Apertura $this->tits $primary ");
	}

	function _post_apertura_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino recepcion $this->tits $primary ");
	}

	function _pre_apertura_update($do){
		$lleno    = $do->get('lleno');
		$vacio    = $do->get('vacio');
		$neto     = $lleno-$vacio;
		$densidad = $do->get('densidad');
		$litros   = $neto*$densidad;
		$do->set('litros',$litros);
		$do->set('neto'  ,$neto);

		//$fecha  = $do->get('fecha');
		//$dbfecha= $this->db->escape($fecha);
		//$cana   = $this->datasis->dameval("SELECT COUNT(*) FROM lcierre WHERE fecha=".$dbfecha);
		//if($cana>0){
		//	$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'Ya el d&iacute;a '.dbdate_to_human($fecha).' fue cerrado.';
		//	return false;
		//}

		return true;
	}

	function _pre_apertura_delete($do){
		//$fecha  = $do->get('fecha');
		//$dbfecha= $this->db->escape($fecha);
		//$cana   = $this->datasis->dameval("SELECT COUNT(*) FROM lcierre WHERE fecha=".$dbfecha);
		//if($cana>0){
		//	$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'Ya el d&iacute;a '.dbdate_to_human($fecha).' fue cerrado.';
		//	return false;
		//}

		return true;
	}

	function _pre_apertura_insert($do){
		$do->set('fecha'  ,date('Y-m-d'));
		$do->set('estampa',date('Y-m-d H:i:s'));

		$hoy       = new DateTime();
		$fregistro = new DateTime($do->get('fecha' ));
		$frecolec  = new DateTime($do->get('fechar')); //Vaca
		$fllegada  = new DateTime($do->get('fechal')); //llego transporte

		if($frecolec > $hoy){
			$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'La fecha de llegada no puede mayor que la fecha actual';
			return false;
		}

		if($fllegada  > $hoy){
			$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'La fecha de la llegada no puede mayor que la fecha actual';
			return false;
		}

		if($frecolec > $fllegada){
			$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'La fecha de la recoleccion no puede ser mayor que la fecha de llegada';
			return false;
		}

		$interval = date_diff($frecolec, $fllegada);
		$diffdia  = intval($interval->format('%a'));
		if($diffdia>1){
			$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'La fecha de la recoleccion no puede diferir mas de un dia con la fecha de llegada';
			return false;
		}

		$lleno    = $do->get('lleno');
		$vacio    = $do->get('vacio');
		$neto     = $lleno-$vacio;
		$densidad = $do->get('densidad');
		$litros   = $neto*$densidad;
		if($vacio==0){
			$do->set('litros',$lleno );
		}else{
			$do->set('litros',$litros);
		}
		$do->set('neto'  ,$neto);

		//$fecha  = $do->get('fecha');
		//$dbfecha= $this->db->escape($fecha);
		//$cana   = $this->datasis->dameval("SELECT COUNT(*) FROM lcierre WHERE fecha=".$dbfecha);
		//if($cana>0){
		//	$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'Ya el d&iacute;a '.dbdate_to_human($fecha).' fue cerrado.';
		//	return false;
		//}

		$lleno = floatval($do->get('lleno'));
		if($lleno>0) $do->set('transporte','');

		return true;
	}

	//Pre y Pos para los analisis
	function _post_analisis_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico Analisis $this->tits $primary ");
	}
	function _pre_analisis_insert($do){
		return true;
	}
	function _pre_analisis_delete($do){
		return false;
	}
	function _pre_analisis_update($do){
		//$fecha  = $do->get('fecha');
		//$dbfecha= $this->db->escape($fecha);
		//$cana   = $this->datasis->dameval("SELECT COUNT(*) FROM lcierre WHERE fecha=".$dbfecha);
		//if($cana>0){
		//	$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'Ya el d&iacute;a '.dbdate_to_human($fecha).' fue cerrado.';
		//	return false;
		//}

		$densidad= $do->get('densidad');
		$lleno   = $do->get('lleno');
		$vacio   = $do->get('vacio');
		if($vacio>$lleno){
			$do->error_message_ar['pre_ins']='El peso lleno debe ser mayor al peso vac&iacute;o.';
			return false;
		}
		$peso    = $lleno-$vacio ;
		$litros  = ($densidad>0)? round($peso/$densidad,2) : 0;

		$do->set('litros',$litros);
		$do->set('neto'  ,$peso  );

		$lista = $do->get('lista');
		if($lista > 0){
			$do->set('diferen',$litros-$lista);
		}
		return true;
	}

	//Pre y Pos para las vaqueras
	function _post_vaqueras_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico Vaqueras $this->tits $primary ");
	}
	function _pre_vaqueras_insert($do){
		return false;
	}
	function _pre_vaqueras_delete($do){
		return false;
	}
	function _pre_vaqueras_update($do){
		//$fecha  = $do->get('fecha');
		//$dbfecha= $this->db->escape($fecha);
		//$cana   = $this->datasis->dameval("SELECT COUNT(*) FROM lcierre WHERE fecha=".$dbfecha);
		//if($cana>0){
		//	$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'Ya el d&iacute;a '.dbdate_to_human($fecha).' fue cerrado.';
		//	return false;
		//}

		$lista=0;
		$rel='itlrece';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$lista += $do->get_rel($rel, 'lista', $i);
		}
		$litros = $do->get('litros');
		if($litros > 0){
			$do->set('lista',$lista);
			$do->set('diferen',$litros-$lista);
		}
		return true;
	}

	function instalar(){
		if(!$this->db->table_exists('lrece')){
			$mSQL="CREATE TABLE `lrece` (
				`numero` CHAR(8) NULL DEFAULT NULL,
				`fecha` DATETIME NULL DEFAULT NULL,
				`transporte` INT(11) NULL DEFAULT NULL COMMENT 'Transporte',
				`fechal` DATE NULL DEFAULT NULL COMMENT 'Fecha de Llegada',
				`fechar` DATE NULL DEFAULT NULL COMMENT 'Fecha de Recoleccion',
				`ruta` CHAR(4) NULL DEFAULT NULL COMMENT 'Ruta Grupo de Proveedor',
				`flete` CHAR(5) NULL DEFAULT NULL COMMENT 'Proveedor Flete',
				`nombre` CHAR(45) NULL DEFAULT NULL COMMENT 'Nombre Chofer ',
				`lleno` DECIMAL(16,2) NULL DEFAULT NULL COMMENT 'Peso de la Unidad llena',
				`vacio` DECIMAL(16,2) NULL DEFAULT NULL COMMENT 'Peso de la Unidad Vacia',
				`neto` DECIMAL(16,2) NULL DEFAULT NULL COMMENT 'Neto lleno-vacio',
				`densidad` DECIMAL(10,4) NULL DEFAULT NULL COMMENT 'Densidad',
				`litros` DECIMAL(16,2) NULL DEFAULT NULL COMMENT 'Total Litros neto*densidad',
				`lista` DECIMAL(16,2) NULL DEFAULT NULL COMMENT 'Segun Lista',
				`diferen` DECIMAL(16,3) NULL DEFAULT NULL COMMENT 'Diferencia Neto/Lista',
				`animal` CHAR(1) NULL DEFAULT NULL COMMENT 'Vaca o Bufala',
				`crios` DECIMAL(10,0) NULL DEFAULT NULL COMMENT 'Crioscopia',
				`h2o` DECIMAL(10,2) NULL DEFAULT NULL COMMENT '% de Agua',
				`temp` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Temperatura',
				`brix` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Grados Brix',
				`grasa` DECIMAL(10,3) NULL DEFAULT NULL COMMENT '% Grasa',
				`acidez` DECIMAL(10,0) NULL DEFAULT NULL COMMENT 'Acidez',
				`cloruros` DECIMAL(10,0) NULL DEFAULT NULL COMMENT 'Cloruros',
				`dtoagua` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Dto. Agua',
				`pago` INT(11) NULL DEFAULT '0' COMMENT 'Nro de Pago',
				`montopago` DECIMAL(12,2) NULL DEFAULT '0.00' COMMENT 'Monto del pago',
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `numero` (`numero`),
				INDEX `fecha` (`fecha`),
				INDEX `transporte` (`transporte`)
			)
			COMMENT='Recepcion de Leche'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('montopago', 'lrece')){
			$mSQL="ALTER TABLE `lrece` ADD COLUMN `montopago` DECIMAL(12,2) NULL DEFAULT '0' COMMENT 'Monto del pago' AFTER `pago`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('estampa', 'lrece')){
			$mSQL="ALTER TABLE `lrece` ADD COLUMN `estampa` DATETIME NULL DEFAULT NULL AFTER `montopago`";
			$this->db->simple_query($mSQL);
			$mSQL="UPDATE lrece SET estampa=fecha";
			$this->db->simple_query($mSQL);
			$mSQL="ALTER TABLE `lrece` CHANGE COLUMN `fecha` `fecha` DATE NULL DEFAULT NULL AFTER `numero`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('transporte', 'lrece')){
			$mSQL="ALTER TABLE `lrece` ADD COLUMN `transporte` INT(11) NULL DEFAULT NULL COMMENT 'Transporte' AFTER `fecha`, ADD INDEX `transporte` (`transporte`)";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('fechal', 'lrece')){
			$mSQL="ALTER TABLE `lrece` ADD COLUMN `fechal` DATE NULL DEFAULT NULL COMMENT 'Fecha de Llegada' AFTER `transporte`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('fechar', 'lrece')){
			$mSQL="ALTER TABLE `lrece` ADD COLUMN `fechar` DATE NULL DEFAULT NULL COMMENT 'Fecha de Recoleccion' AFTER `fechal`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('alcohol', 'itlrece')){
			$mSQL = "ALTER TABLE itlrece ADD COLUMN `alcohol` DECIMAL(10,3) NULL DEFAULT NULL COMMENT 'alcohol' AFTER `dtoagua`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('montopago', 'itlrece')){
			$mSQL = "ALTER TABLE `itlrece` ADD COLUMN `montopago` DECIMAL(12,2) NULL DEFAULT '0' COMMENT 'Monto del pago' AFTER `pago`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('alcohol', 'lanal')){
			$mSQL = "ALTER TABLE lanal ADD COLUMN `alcohol` DECIMAL(10,3) NULL DEFAULT NULL COMMENT 'alcohol' AFTER `dtoagua`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('itlrece')){
			$mSQL="CREATE TABLE `itlrece` (
				`vaquera` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Vaquera',
				`nombre` VARCHAR(45) NULL DEFAULT NULL COMMENT 'Productor ',
				`densidad` DECIMAL(10,4) NULL DEFAULT '1.0164' COMMENT 'Densidad',
				`lista` DECIMAL(16,2) NULL DEFAULT NULL COMMENT 'Segun Lista',
				`animal` CHAR(1) NULL DEFAULT 'V' COMMENT 'Vaca o Bufala',
				`crios` DECIMAL(10,0) NULL DEFAULT '0' COMMENT 'Crioscopia',
				`h2o` DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT '% de Agua',
				`temp` DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Temperatura',
				`brix` DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Grados Brix',
				`grasa` DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT '% Grasa',
				`acidez` DECIMAL(10,0) NULL DEFAULT '0' COMMENT 'Acidez',
				`cloruros` DECIMAL(10,0) NULL DEFAULT '0' COMMENT 'Cloruros',
				`dtoagua` DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Dto. Agua',
				`alcohol` DECIMAL(10,0) NULL DEFAULT '0' COMMENT 'alcohol',
				`id_lrece` INT(11) NULL DEFAULT NULL,
				`id_lvaca` INT(11) NULL DEFAULT NULL,
				`activa` CHAR(1) NULL DEFAULT 'A' COMMENT 'Activa Si o No',
				`pago` INT(11) NULL DEFAULT '0' COMMENT 'ID del pago lpago',
				`montopago` DECIMAL(12,2) NULL DEFAULT '0' COMMENT 'Monto del pago',
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `vaquera` (`vaquera`, `id_lrece`),
				INDEX `id_lrece` (`id_lrece`),
				INDEX `id_lvaca` (`id_lvaca`)
			)
			COMMENT='Detalle Recepcion de Leche'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}
	}
}
