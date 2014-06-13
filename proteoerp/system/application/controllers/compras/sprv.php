<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Sprv extends Controller {
	var $genesal = true;
	var $mModulo='SPRV';
	var $titp='Proveedores';
	var $tits='Proveedores';
	var $url ='compras/sprv/';

	function Sprv(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'SPRV', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 750, 580, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//***************************
	function jqdatag(){
		$consulrif=trim($this->datasis->traevalor('CONSULRIF'));

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'edocta',   'img'=>'images/pdf_logo.gif' ,  'alt' => 'Estado de cuenta' , 'label'=>'Estado de Cuenta'));
		$grid->wbotonadd(array('id'=>'pagweb',   'img'=>'images/html_icon.gif',  'alt' => 'P&aacute;gina Web', 'label'=>'P&aacute;gina Web'));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '
		function consulrif1(campo){
			vrif=$("#"+campo).val();
			if(vrif.length==0){
				alert("Debe introducir primero un RIF");
			}else{
				vrif=vrif.toUpperCase();
				$("#"+campo).val(vrif);
				window.open("'.$consulrif.'"+"?p_rif="+vrif,"CONSULRIF","height=350,width=410");
			}
		}
		';

		// Abre sitio Web
		$funciones .= '
		function iraurl(){
			vurl=$("#url").val();
			if(vrif.length==0){
				alert("Debe introducir primero un URL");
			}else{
				vurl=vurl.toLowerCase();
				window.open("http://"+vurl,"PROVEEDOR","height=600,width=800");
			}
		}';

		// Fusionar Proveedor
		$funciones .= '
		function fusionar(){
			var yurl = "";
			var id = jQuery("#newapi'.$param['grids'][0]['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var mnuevo = "";
				var ret = jQuery("#newapi'.$param['grids'][0]['gridname'].'").jqGrid(\'getRowData\',id);
				var mviejo = ret.proveed;
				$.prompt("<h1>Cambiar Codigo</h1>Proveedor: <b>"+ret.nombre+"</b><br>Codigo Actual: <b>"+ret.proveed+"</b><br><br>Codigo Nuevo <input type=\'text\' id=\'codnuevo\' name=\'mcodigo\' size=\'6\' maxlength=\'5\' >",{
					buttons: { Cambiar:true, Salir:false},
					callback: function(e,v,m,f){
						mnuevo = f.mcodigo;
						if (v) {
							yurl = encodeURIComponent(mnuevo);
							$.ajax({
								url: "'.site_url('compras/sprv/sprvexiste').'",
								global: false,
								type: "POST",
								data: ({ codigo : encodeURIComponent(mnuevo) }),
								dataType: "text",
								async: false,
								success: function(sino) {
									sprvcambia(sino, mviejo, mnuevo, ret.nombre);
								},
								error: function(h,t,e) { apprise("Error..codigo="+yurl+" ",e) }
							});
						}
					}
				});
			} else
				$.prompt("<h1>Por favor Seleccione un Poveedor</h1>");
		};

		function sprvcambia( sino, mviejo, mnuevo, nviejo ) {
			var aprueba = false;
			if (sino.substring(0,1)=="S"){
				apprise("<h1>FUSIONAR: Ya existe el proveedor</h1><h2 style=\"background: #ffdddd;text-align:center;\">("+mnuevo+") "+sino.substring(1)+"</h2><p style=\"font-size:130%\">Si prosigue se eliminara el proveedor ("+mviejo+") "+nviejo+"<br>y los movimientos seran agregados a ("+mnuevo+") </"+"p> <p style=\"align:center;font-size:150%\">Desea <strong>Fusionarlos?</"+"strong></"+"p>",
					{ "confirm":true, "textCancel":"Salir", "textOk":"Proseguir"},
					function(v){
						if (v) {
							sprvfusdef(mnuevo, mviejo)
							jQuery(gridId1).trigger("reloadGrid");
						}
					}
				);
			} else {
				apprise("<h1>Sustitur Codigo actual</h1> <center><h2 style=\"background: #ddeedd\">"+mviejo+" por "+mnuevo+"</"+"h2></"+"center> <p style=\"font-size:130%\">Al cambiar de codigo del proveedor, todos los movimientos y estadisticas <br>se cambiaran correspondientemente.</"+"p> ",
					{ "confirm":true, "textCancel":"Salir", "textOk":"Proseguir"},
					function(v){
						if (v) {
							sprvfusdef(mnuevo, mviejo);
							jQuery(gridId1).trigger("reloadGrid");
						}
					}
				)
			}
		};

		function sprvfusdef(mnuevo, mviejo){
			$.ajax({
				url: "'.site_url('compras/sprv/sprvfusion').'",
				global: false,
				type: "POST",
				data: ({mviejo: encodeURIComponent(mviejo),
					mnuevo: encodeURIComponent(mnuevo) }),
				dataType: "text",
				async: false,
				success: function(sino) {
					alert("Cambio finalizado "+sino,"Finalizado Exitosamente")
				},
				error: function(h,t,e) {alert("Error..","Finalizado con Error" )}
			});
		};
		';


		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['funciones']   = $funciones;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('SPRV', 'JQ');
		$param['otros']       = $this->datasis->otros('SPRV', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}


	// Revisa si existe el codigo
	function sprvexiste(){
		$cliente  = rawurldecode($this->input->post('codigo'));
		$dbcliente= $this->db->escape($cliente);
		$existe   = $this->datasis->dameval('SELECT COUNT(*) AS cana FROM sprv WHERE proveed='.$dbcliente);
		$devo     = 'N ';
		if ($existe > 0 ) {
			$devo  ='S';
			$devo .= $this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$dbcliente);
		}
		echo $devo;
	}


	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">'."\n";
		$ngrid = '#newapi'.$grid0;

		$bodyscript .= $this->jqdatagrid->bsshow('sprv', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'sprv', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'sprv', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('sprv', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '380', '680' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '190', '360' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '200', '300' );

/*
		$bodyscript .= '
		jQuery("#edocta").click( function(){
			var id = $("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("'.$ngrid.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('reportes/ver/SPRMECU/SPRM/').'/\'+ret.proveed').';
			} else { $.prompt("<h1>Por favor Seleccione un Proveedor</h1>");}
		});';

		// Pagina Web
		$bodyscript .= '
		jQuery("#pagweb").click( function(){
			var id     = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret  = $("'.$ngrid.'").getRowData(id);
				if ( ret.url.length > 10 )
					window.open(ret.url);
			} else {
				$.prompt("<h1>Por favor Seleccione un Proveedor</h1>");
			}
		});';

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 380, width: 720, modal: true,
			buttons: {
			"Guardar": function() {
				var murl = $("#df1").attr("action");
				$.ajax({
					type: "POST", dataType: "html", async: false,
					url: murl,
					data: $("#df1").serialize(),
					success: function(r,s,x){
						try{
							var json = JSON.parse(r);
							if (json.status == "A"){
								$("#fedita").dialog( "close" );
								grid.trigger("reloadGrid");
								$.prompt("<h1>Registro Guardado</h1>",{
									submit: function(e,v,m,f){
										setTimeout(function(){ $("'.$ngrid.'").jqGrid(\'setSelection\',json.pk.id);}, 500);
									}}
								);
								idactual = json.pk.id;
								return true;
							} else {
								$.prompt("Error: "+json.mensaje);
							}
						} catch(e){
							$("#fedita").html(r);
						}
					}
				})
			},
			"Cancelar": function(){
				$("#fedita").html("");
				$(this).dialog("close");
			},
			"SENIAT":   function(){ consulrif("rifci"); },
			"URL":   function() { iraurl(); },
			},
			close: function(){
				$("#fedita").html("");
			}
		});';
*/

		$bodyscript .= '
		jQuery("#edocta").click( function(){
			var id = $("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("'.$ngrid.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('reportes/ver/SPRMECU/SPRM/').'/\'+ret.proveed').';
			} else { $.prompt("<h1>Por favor Seleccione un Proveedor</h1>");}
		});';

		// Pagina Web
		$bodyscript .= '
		jQuery("#pagweb").click( function(){
			var id     = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret  = $("'.$ngrid.'").getRowData(id);
				if ( ret.url.length > 10 )
					window.open(ret.url);
			} else {
				$.prompt("<h1>Por favor Seleccione un Proveedor</h1>");
			}
		});';

		$bodyscript .= '});';
		$bodyscript .= '</script>';
		return $bodyscript;
	}


	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i       = 1;
		$linea   = 1;
		$editar  = 'false';

		$mSQL  = 'SELECT grupo, CONCAT(grupo, \' \', gr_desc) descrip FROM grpr ORDER BY grupo';
		$agrupo= $this->datasis->llenajqselect($mSQL, false );

		$mSQL  = 'SELECT cod_banc, nomb_banc nombre FROM tban ORDER BY nomb_banc';
		$banco = $this->datasis->llenajqselect($mSQL, true );

		$link   = site_url('ajax/buscacpla');

		$grid  = new $this->jqdatagrid;

		$grid->addField('proveed');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'));


		$grid->addField('grupo');
		$grid->label('Grupo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$agrupo.',  style:"width:250px"}',
			'stype'         => "'text'",
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('rif');
		$grid->label('RIF');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 12 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('nomfis');
		$grid->label('Raz&oacute;n social');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 80 }',
			'formoptions'   => '{ label:"Razon Social", rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('tipo');
		$grid->label('Persona');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'select'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{value: {"1":"Juridico Domiciliado","2":"Natural Residente","3":"Juridico no Domiciliado","4":"Natural no Residente", "5":"Excluido del LC", "0":"Inactivo" }, style:"width:180px" }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));


		$grid->addField('contacto');
		$grid->label('Contacto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));


/*
		$grid->addField('gr_desc');
		$grid->label('Gr_desc');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 25 }',
		));
*/

		$linea = $linea + 1;
		$grid->addField('tiva');
		$grid->label('Cont.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{value: {"N":"Nacional","I":"Internacional","O":"Otros" }, style:"width:120px" }',
			'formoptions'   => '{ label:"Contribuyente", rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('direc1');
		$grid->label('Direcci&oacute;n 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('reteiva');
		$grid->label('% Ret.IVA');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 60,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));


		$grid->addField('direc2');
		$grid->label('Direcci&oacute;n 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));


		$linea = $linea + 1;
		$grid->addField('codigo');
		$grid->label('C&oacute;d.Prov.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
			'formoptions'   => '{ label:"Cod. en el Prov.", rowpos:'.$linea.', colpos:1 }'
		));


		$grid->addField('direc3');
		$grid->label('Direcci&oacute;n 3');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('telefono');
		$grid->label('Tel&eacute;fono');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'editrules'     => '{ required:false}',
			'edittype'      => "'textarea'",
			'editoptions'   => "{rows:2, cols:28}",
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));


		$grid->addField('cuenta');
		$grid->label('Cta.Contable');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 90,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{'.$grid->autocomplete($link, 'cuenta','cucucu','<div id=\"cucucu\"><b>"+ui.item.descrip+"</b></div>').'}',
		));

		$grid->addField('canticipo');
		$grid->label('Cta.Anticipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 90,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{'.$grid->autocomplete($link, 'cuenta','cucucu','<div id=\"cucucu\"><b>"+ui.item.descrip+"</b></div>').'}',
		));

		$linea = $linea + 1;
		$grid->addField('email');
		$grid->label('Email');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'textarea'",
			'editrules'     => '{ required:false}',
			'editoptions'   => "{rows:2, cols:28}",
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('url');
		$grid->label('Url');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));



		$linea = $linea + 1;
		$grid->addField('prefpago');
		$grid->label('Preferencia');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{ value: { "T":"Transferencia", "C":"Cobro en caja","D":"Deposito"},  style:"width:180px"}',
			'stype'         => "'text'"
		));



		$linea = $linea + 1;
		$grid->addField('banco1');
		$grid->label('Banco1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$banco.',  style:"width:180px"}',
			'stype'         => "'text'"
		));

		$grid->addField('cuenta1');
		$grid->label('Cuenta1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 25 }',
			'formoptions'   => '{ label:"Nro. Cuenta 1" }'
		));

		$linea = $linea + 1;
		$grid->addField('banco2');
		$grid->label('Banco2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$banco.',  style:"width:180px"}',
		));

		$grid->addField('cuenta2');
		$grid->label('Cuenta2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 25 }',
			'formoptions'   => '{ label:"Nro. Cuenta 2" }'
		));

/*
		$grid->addField('canticipo');
		$grid->label('Canticipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 15 }',
		));
*/


		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => "'false'",
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
			'editable'      => "'false'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('310');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow(' function(id){
			if (id){
				var ret = jQuery(gridId1).jqGrid(\'getRowData\',id);
				$(gridId1).jqGrid("setCaption", ret.nombre);
				$.ajax({
					url: "'.site_url($this->url).'/resumen/"+id,
					success: function(msg){
						msg += "<img src=\''.site_url($this->url.'vcard').'/'.'"+id+"\' alt=\'vCard\' height=\'150\' width=\'150\'> ";
						$("#ladicional").html(msg);
					}
				});
			}
		}');

		$grid->setFormOptionsE('
			closeAfterEdit:true, mtype: "POST", width: 720, height:410, closeOnEscape: true, top: 50, left:20, recreateForm:true,
			afterSubmit: function(a,b){
				if (a.responseText.length > 0) $.prompt(a.responseText);
					return [true, a ];
			},
			beforeShowForm: function(frm){
					$(\'#proveed\').attr(\'readonly\',\'readonly\');
					$(\'<a href="#">SENIAT<span class="ui-icon ui-icon-disk"></span></a>\').click(function(){
						consulrif("rif");
					}).addClass("fm-button ui-state-default ui-corner-all fm-button-icon-left").prependTo("#Act_Buttons>td.EditButton");
				},
			afterShowForm: function(frm){$("select").selectmenu({style:"popup"});}'
		);

		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 720, height:410, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},
			beforeShowForm: function(frm){
					$(\'<a href="#">SENIAT<span class="ui-icon ui-icon-disk"></span></a>\').click(function(){
						consulrif("rif");
					}).addClass("fm-button ui-state-default ui-corner-all fm-button-icon-left").prependTo("#Act_Buttons>td.EditButton");
				},
			afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} '
		);

		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');


		$grid->setBarOptions('addfunc: sprvadd, editfunc: sprvedit, delfunc: sprvdel, viewfunc: sprvshow');

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
		$mWHERE = $grid->geneTopWhere('sprv');

		$response   = $grid->getData('sprv', array(array()), array(), false, $mWHERE );
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
		$mcodp  = 'proveed';
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);

		if($oper == 'add'){

		}elseif($oper == 'edit'){
			$proveed  = $this->datasis->dameval("SELECT proveed FROM sprv WHERE id=${id}");
			if ( isset($data['proveed']) ) unset($data['proveed']);
			$this->db->where('id', $id);
			$this->db->update('sprv', $data);
			logusu('SPRV','Proveedor  '.$proveed.' MODIFICADO');
			echo 'Proveedor Modificado';

		}elseif($oper == 'del'){
			$codigo  = $this->datasis->dameval("SELECT ${mcodp} FROM sprv WHERE id=${id}");
			$dbcodigo= $this->db->escape($codigo);
			$check =  $this->datasis->dameval("SELECT COUNT(*) AS cana FROM sprm WHERE cod_prv=${dbcodigo}");
			$check += $this->datasis->dameval("SELECT COUNT(*) AS cana FROM scst WHERE proveed=${dbcodigo}");
			$check += $this->datasis->dameval("SELECT COUNT(*) AS cana FROM gser WHERE proveed=${dbcodigo}");
			$check += $this->datasis->dameval("SELECT COUNT(*) AS cana FROM ords WHERE proveed=${dbcodigo}");
			$check += $this->datasis->dameval("SELECT COUNT(*) AS cana FROM bmov WHERE clipro='P' AND codcp=${dbcodigo}");
			if($check > 0){
				echo 'El registro no puede ser eliminado; tiene movimiento ';
			}else{
				$this->db->simple_query('DELETE FROM sprv WHERE proveed='.$dbcodigo);
				logusu('SPRV','Proveedor '.$codigo.' ELIMINADO');
				echo 'Proveedor Eliminado';
			}
		};
	}


	//Resumen rapido
	function resumen() {
		$id  = intval($this->uri->segment($this->uri->total_segments()));
		$dbid= $this->db->escape($id);
		$row = $this->datasis->damereg("SELECT proveed FROM sprv WHERE id=${dbid}");
		if(empty($row))
		return;
		$proveed  = $row['proveed'];
		$salida = '';

		$saldo  = $this->datasis->dameval("SELECT SUM(monto*IF(tipo_doc IN ('FC','ND','GI'),1,-1)) saldo FROM sprm WHERE cod_prv=".$this->db->escape($proveed));

		$salida  = '<table width="90%" cellspacing="0" align="center">';
		if($saldo > 0)
			$salida .= "<tr style='background-color:#8BB381;font-size:14px;' align='right'><td><b>Saldo: </b> </td><td align='left'><b>".nformat($saldo)."</b></td></tr>\n";
		elseif($saldo < 0)
			$salida .= "<tr style='background-color:#C11B17;font-size:14px;color:white;' align='right'><td><b>Saldo: </b> </td><td align='left'><b>".nformat($saldo)."</b></td></tr>\n";
		else
			$salida .= "<tr style='background-color:#8BB381;font-size:14px;' align='right'><td><b>Saldo: </b> </td><td align='left'><b>0.00</b></td></tr>\n";

		$salida .= "</table>\n";

		echo $salida;
	}

	//******************************************************************
	//     DATAEDIT
	//
	function dataedit(){
		$this->rapyd->load('dataedit');

		$mSCLId=array(
			'tabla'   =>'scli',
			'columnas'=>array(
			'cliente' =>'C&oacute;digo Cliente',
			'nombre'  =>'Nombre',
			'contacto'=>'Contacto',
			'nomfis'  =>'Nom. Fiscal'),
			'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
			'retornar'=>array('cliente'=>'cliente','nomfis'=>'nomfis'),
			'titulo'  =>'Buscar Cliente');

		$qformato=$this->qformato=$this->datasis->formato_cpla();

		$mCPLA=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'cuenta'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
			);

		$mANTI=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'canticipo'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
			);


		$bsclid =$this->datasis->modbus($mSCLId);
		$bcpla  =$this->datasis->modbus($mCPLA);
		$banti  =$this->datasis->modbus($mANTI,'bamti');

		$smenu['link']=barra_menu('131');
		$consulrif=$this->datasis->traevalor('CONSULRIF');
		$link=site_url('compras/sprv/uproveed');


		$edit = new DataEdit('', 'sprv');
		$edit->on_save_redirect=false;
		$script ='
			$(function() {
				$("#tr_gr_desc").hide();
				$("#grupo").change(function(){grupo();}).change();
				$(".inputnum").numeric(".");
				$("#banco1").change(function () { acuenta(); }).change();
				$("#banco2").change(function () { acuenta(); }).change();

				$("#rif").focusout(function(){
					rif = $(this).val().toUpperCase();
					$(this).val(rif);
					patt = /[EJPGV][0-9]{4,10} */g;
					if(patt.test(rif)){
						if(!chrif(rif)){
							alert("Al parecer el RIF colocado no es correcto, por favor verifique con el SENIAT.");
						}else{
							$.ajax({
								type: "POST",
								url: "'.site_url('ajax/traerif').'",
								dataType: "json",
								data: {rifci: rif},
								success: function(data){
									if(data.error==0){
										if($("#nombre").val()==""){
											$("#nombre").val(data.nombre);
										}
										if($("#nomfis").val()==""){
											$("#nomfis").val(data.nombre);
										}
										$("#reteiva").val(data.tasa);
									}
								}
							});

						//Chequea si esta repetido
						$.ajax({
							type: "POST",
							url: "'.site_url('ajax/rifrep/P').'",
							dataType: "json",
							data: {rifci: rif, codigo: '.json_encode($edit->_dataobject->get('proveed')).'},
							success: function(data){
								if(data.rt){
									$.prompt(data.msj,{
										buttons: { Continuar: true },
										focus: 1,
										submit:function(e,v,m,f){

											$("#nombre").focus();
										}
									});
								}
							}
						});
						//Fin del chequeo repetido

						}
					} else {
						alert("El RIF o Cedula introducida no es correcta, por favor verifique e intente de nuevo.");
						return false;
					}
				});
			});
			'.$this->datasis->validarif().'
			function grupo(){
				t=$("#grupo").val();
				a=$("#grupo :selected").text();
				$("#gr_desc").val(a);
			}
			function acuenta(){
				for(i=1;i<=2;i++){
					vbanco=$("#banco"+i).val();
					if(vbanco.length>0){
						$("#tr_cuenta"+i).show();
					}else{
						$("#cuenta"+i).val("");
						$("#tr_cuenta"+i).hide();
					}
				}
			}
			function anomfis(){
					vtiva=$("#tiva").val();
					if(vtiva=="C" || vtiva=="E" || vtiva=="R"){
						$("#tr_nomfis").show();
						$("#tr_riff").show();
					}else{
						$("#nomfis").val("");
						$("#rif").val("");
						$("#tr_nomfis").hide();
						$("#tr_rif").hide();
					}
			}

			function consulrif(){
					vrif=$("#rif").val();
					if(vrif.length==0){
						alert("Debe introducir primero un RIF");
					}else{
						vrif=vrif.toUpperCase();
						$("#rif").val(vrif);
						window.open("'.$consulrif.'"+"?p_rif="+vrif,"CONSULRIF","height=350,width=410");
					}
			}
			function ultimo(){
				$.ajax({
					url: "'.$link.'",
					success: function(msg){
					alert( "El ultimo codigo ingresado fue: " + msg );
					}
				});
			}

			function iraurl(){
				vurl=$("#url").val();
				if(vurl.length==0){
					alert("Debe introducir primero un URL");
				}else{
					vurl=vurl.toLowerCase();
					window.open(vurl);
				}
			}';

		$edit->script($script, 'create');
		$edit->script($script, 'modify');
		$edit->pre_process( 'delete','_pre_delete' );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$lproveed='<a href="javascript:ultimo();" title="Ultimo codigo ingresado" onclick="">Ultimo</a>';
		$edit->proveed  = new inputField('C&oacute;digo', 'proveed');
		$edit->proveed->rule = 'trim|required|callback_chexiste|alpha_dash_slash';
		$edit->proveed->mode = 'autohide';
		$edit->proveed->size = 6;
		$edit->proveed->maxlength =5;
		$edit->proveed->append($lproveed);
		$edit->proveed->title = 'Codigo del Proveedor';

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->rule = 'trim|strtoupper|required';
		$edit->nombre->size = 42;
		$edit->nombre->maxlength =40;
		$edit->nombre->title = 'Nombre del Proveedor';

		//$lriffis='<a href="javascript:consulrif();" title="Consultar RIF en el SENIAT" onclick="" style="color:red;font-size:9px;border:none;">SENIAT</a>';
		$edit->rif =  new inputField('RIF', 'rif');
		$edit->rif->rule = 'trim|strtoupper|required|callback_chsprvrif';
		//$edit->rif->append($lriffis);
		$edit->rif->maxlength=13;
		$edit->rif->size =12;
		$edit->rif->title = 'RIF o Cedula del Proveedor';

		$edit->contacto = new inputField('Contacto', 'contacto');
		$edit->contacto->size =42;
		$edit->contacto->rule ='trim';
		$edit->contacto->maxlength =40;
		$edit->contacto->title = 'Nombre de la persona con quien hablan o son atendidos en el proveedor';

		$edit->nomfis = new textareaField('Raz&oacute;n Social', 'nomfis');
		$edit->nomfis->rule = 'trim';
		$edit->nomfis->cols = 40;
		$edit->nomfis->rows =  2;
		$edit->nomfis->maxlength =200;
		$edit->nomfis->style = 'width:170;';
		$edit->nomfis->title = 'Nombre como aparecen en el registro';

		$edit->grupo = new dropdownField('Grupo', 'grupo');
		$edit->grupo->option('','Seleccionar');
		$edit->grupo->options("SELECT grupo,gr_desc,grupo FROM grpr ORDER BY gr_desc");
		$edit->grupo->style = 'width:190px';
		$edit->grupo->group = 'Datos del Proveedor';
		$edit->grupo->title = 'Grupo de Proveedores';

		$edit->gr_desc = new inputField('gr_desc', 'gr_desc');

		$edit->tipo = new dropdownField('Persona', 'tipo');
		$edit->tipo->option('','Seleccionar');
		$edit->tipo->options(array('1'=> 'Jur&iacute;dico Domiciliado','2'=>'Residente', '3'=>'Jur&iacute;dico No Domiciliado','4'=>'No Residente','5'=>'Excluido del Libro de Compras','0'=>'Inactivo'));
		$edit->tipo->style = 'width:190px';
		$edit->tipo->rule  = 'required';
		$edit->tipo->group = 'Datos del Proveedor';
		$edit->tipo->title = 'Tipo de persona Jur&iacute;dico, Natural, etc';

		$edit->tiva  = new dropdownField('Or&iacute;gen', 'tiva');
		$edit->tiva->options(array('N'=>'Nacional','I'=>'Internacional','O'=>'Otros'));
		$edit->tiva->style='width:190px;';

		$edit->direc1 = new textareaField('Direcci&oacute;n','direc1');
		$edit->direc1->rule = 'trim';
		$edit->direc1->cols = 43;
		$edit->direc1->rows =  2;
		$edit->direc1->maxlength =200;
		$edit->direc1->style = 'width:170;';
		$edit->direc1->title = 'Nombre como aparecen en el registro';

		$edit->estado = new dropdownField('Estado','estado');
		$edit->estado->style='width:170px;';
		$edit->estado->option('','Seleccione un Estado');
		$edit->estado->options('SELECT codigo, entidad FROM estado ORDER BY entidad');

		$edit->pais = new inputField('Pa&iacute;s','pais');
		$edit->pais->rule = 'trim';
		$edit->pais->size = 30;
		$edit->pais->maxlength = 30;
/*
		$edit->direc1 = new inputField('Direcci&oacute;n','direc1');
		$edit->direc1->size =42;
		$edit->direc1->rule ='trim';
		$edit->direc1->maxlength =40;
*/
		$edit->direc2 = new inputField(' ','direc2');
		$edit->direc2->size =42;
		$edit->direc2->rule ='trim';
		$edit->direc2->maxlength =40;

		$edit->direc3 = new inputField(' ','direc3');
		$edit->direc3->size =42;
		$edit->direc3->rule ='trim';
		$edit->direc3->maxlength =40;

		$edit->telefono = new textareaField('Tel&eacute;fono', 'telefono');
		$edit->telefono->rule = 'trim';
		$edit->telefono->cols = 26;
		$edit->telefono->rows =  2;
		$edit->telefono->maxlength =200;

		$edit->email  = new inputField('Email', 'email');
		$edit->email->rule = 'trim|valid_email';
		$edit->email->size =29;
		$edit->email->maxlength =30;
		$edit->email->title = 'Correo electr&oacute;nico';

		$edit->url = new inputField('Sitio Web', 'url');
		$edit->url->group = 'Datos del Proveedor';
		$edit->url->rule  = 'trim';
		$edit->url->size  = 28;
		$edit->url->maxlength =40;
		$edit->url->title = 'P&aacute;gina Web del Proveedor';

		$atts = array(
				'width'     =>'800',
				'height'    =>'600',
				'scrollbars'=>'yes',
				'status'    =>'yes',
				'resizable' =>'yes',
				'screenx'   =>'5',
				'screeny'   =>'5');
		$lcli=anchor_popup('/ventas/scli/dataedit/create',image('list_plus.png','Agregar',array('border'=>'0')),$atts);

		$edit->observa  = new inputField('Observaci&oacute;n', 'observa');
		$edit->observa->group = 'Datos del Proveedor';
		$edit->observa->rule  = 'trim';
		$edit->observa->size  = 41;

		$edit->banco1 = new dropdownField('Cuenta en bco. (1)', 'banco1');
		$edit->banco1->option('','Ninguno');
		$edit->banco1->options('SELECT cod_banc,nomb_banc FROM tban ORDER BY nomb_banc');

		$edit->banco1->group = 'Cuentas Bancarias';
		$edit->banco1->style ='width:140px;';

		$edit->cuenta1 = new inputField('&nbsp;&nbsp;N&uacute;mero (1)','cuenta1');
		$edit->cuenta1->size = 22;
		$edit->cuenta1->rule = 'trim';
		$edit->cuenta1->maxlength = 25;
		$edit->cuenta1->group = "Cuentas Bancarias";

		$edit->banco2 = new dropdownField('Cuenta en bco. (2)', 'banco2');
		$edit->banco2->option('','Ninguno');
		$edit->banco2->options('SELECT cod_banc,nomb_banc FROM tban ORDER BY nomb_banc');
		$edit->banco2->group = 'Cuentas Bancarias';
		$edit->banco2->style='width:140px;';

		$edit->cuenta2 = new inputField('&nbsp;&nbsp;N&uacute;mero (2)','cuenta2');
		$edit->cuenta2->size = 22;
		$edit->cuenta2->rule = 'trim';
		$edit->cuenta2->maxlength = 25;
		$edit->cuenta2->group = "Cuentas Bancarias";

		$edit->cliente  = new inputField('Como Cliente', 'cliente');
		$edit->cliente->size =7;
		$edit->cliente->rule ='trim';
		$edit->cliente->readonly=true;
		$edit->cliente->append($bsclid);
		$edit->cliente->title = 'C&oacute;digo como cliente para hacer cruces de cuenta';

		$edit->prefpago = new dropdownField('Preferencia de pago','prefpago');
		$edit->prefpago->option('T','Transferencia');
		$edit->prefpago->option('C','Cobro en caja');
		$edit->prefpago->option('D','Deposito');
		$edit->prefpago->group = 'Cuentas Bancarias';
		$edit->prefpago->style = 'width:140px;';

		$edit->codigo  = new inputField('Cod. en Prov', 'codigo');
		$edit->codigo->size  = 15;
		$edit->codigo->rule  = 'trim';
		$edit->codigo->title = 'C&oacute;digo en el sistema del proveedor';

		$edit->cuenta = new inputField('Contable', 'cuenta');
		$edit->cuenta->rule='trim|existecpla';
		$edit->cuenta->size =15;
		$edit->cuenta->maxlength =15;
		$edit->cuenta->append($bcpla);
		$edit->cuenta->title = 'C&oacute;digo en el plan de cuentas contable';

		$edit->canticipo = new inputField('Anticipo', 'canticipo');
		$edit->canticipo->rule='trim|existecpla';
		$edit->canticipo->size =15;
		$edit->canticipo->maxlength =15;
		$edit->canticipo->append($banti);
		$edit->canticipo->title = 'C&oacute;digo en el plan de cuentas contable para cargar anticipos si es diferente';

		$edit->reteiva  = new inputField('Retenci&oacute;n','reteiva');
		$edit->reteiva->size = 6;
		$edit->reteiva->css_class='inputnum';
		$edit->reteiva->insertValue='75.00';
		$edit->reteiva->append('%');

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' => 'A',
				'mensaje'=> 'Registro guardado',
				'pk'     => $edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form']  =&  $edit;
			$data['content']  =  $this->load->view('view_sprv', $conten);
		}
	}

	function _pre_delete($do) {
		$codigo  = $do->get('proveed');
		$dbcodigo= $this->db->escape($codigo);
		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sprm WHERE cod_prv=${dbcodigo}");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM scst WHERE proveed=${dbcodigo}");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM gser WHERE proveed=${dbcodigo}");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM ords WHERE proveed=${dbcodigo}");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM bmov WHERE clipro='P' AND codcp=${dbcodigo}");
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Cliente con Movimiento no puede ser Borrado';
			return false;
		}
		return true;
	}

	function _post_insert($do){
		$codigo=$do->get('proveed');
		$nombre=$do->get('nombre');
		logusu('sprv',"PROVEEDOR ${codigo} NOMBRE ${nombre} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('proveed');
		$nombre=$do->get('nombre');
		logusu('sprv',"PROVEEDOR ${codigo} NOMBRE ${nombre} MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('proveed');
		$nombre=$do->get('nombre');
		logusu('sprv',"PROVEEDOR ${codigo} NOMBRE ${nombre} ELIMINADO");
	}

	function chsprvrif($rif){
		$tiva=$this->input->post('tiva');
		if($tiva=='O' || $tiva=='I'){
			return true;
		}
		if (preg_match("/(^[VEJG][0-9]{9}[[:blank:]]*$)|(^[[:blank:]]*$)/", $rif)>0){
			return true;
		}else {
			$this->validation->set_message('chsprvrif', "El campo <b>%s</b> debe tener el siguiente formato V=Venezolano(a), G=Gobierno, J=Juridico Como primer caracter seguido del n&uacute;mero de documento. Ej: V123456789, J123456789");
			return false;
		}
	}

	function chexiste($codigo){
		$dbcodigo= $this->db->escape($codigo);
		$rif=$this->input->post('rif');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM sprv WHERE proveed=${dbcodigo}");
		if ($check > 0){
			$nombre=$this->datasis->dameval("SELECT nombre FROM sprv WHERE proveed=${dbcodigo}");
			$this->validation->set_message('chexiste',"El codigo ${codigo} ya existe para el proveedor ${nombre}");
			return false;
		//}elseif(strlen($rif)>0){
		//	$check=$this->datasis->dameval("SELECT COUNT(*) FROM sprv WHERE rif='$rif'");
		//	if ($check > 0){
		//		$nombre=$this->datasis->dameval("SELECT nombre FROM sprv WHERE rif='$rif'");
		//		$this->validation->set_message('chexiste',"El rif $rif ya existe para el proveedor $nombre");
		//		return false;
		//	}else {
		//		return true;
		//	}
		}else{
			return true;
		}
	}

	function _pre_insert($do){
		$do->set('registrado',date('Y-m-d'));
		return true;
	}

	function rifdupli(){
		$rt=array('status'=>'A','msj'=>'');
		$rif  =$do->get('rif');
		$dbrif=$this->db->escape($rif);
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM sprv WHERE rif=${dbrif}");
		if($check > 0){
			$rt=array('status'=>'B','msj'=>'Ya existe '.$check.' proveedor con el mismo numero de rif');
		}
		echo json_encode($rt);
	}


	function update(){
		$mSQL=$this->db->query('UPDATE sprv SET reteiva=75 WHERE reteiva<>100');
	}

	function uproveed(){
		$consulproveed=$this->datasis->dameval('SELECT MAX(proveed) FROM sprv');
		echo $consulproveed;
	}

	function consulta(){
		$this->load->helper('openflash');
		$this->rapyd->load('datagrid');
		$fields = $this->db->field_data('sprv');
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

		$mCodigo = $this->datasis->dameval("SELECT proveed FROM sprv WHERE id=".$claves['id']."");

		$grid = new DataGrid('Ventas por Mes');
		$grid->db->_protect_identifiers=false;
		$grid->db->select( array('a.tipo_doc','a.fecha', 'a.numero', 'a.monto', 'a.abonos', 'a.monto-a.abonos saldo' ) );
		$grid->db->from('sprm a');
		$grid->db->where('a.cod_prv', $mCodigo );
		$grid->db->where('a.monto <> a.abonos');
		$grid->db->where('a.tipo_doc IN ("FC","ND","GI") ' );
		$grid->db->orderby('a.fecha');

		$grid->column('Fecha'  ,'fecha' );
		$grid->column('Tipo'   ,'tipo_doc','align="CENTER"');
		$grid->column('Numero' ,'numero','align="LEFT"');
		$grid->column('Monto'  ,'<nformat><#monto#></nformat>' ,'align="RIGHT"');
		$grid->column('Abonos' ,'<nformat><#abonos#></nformat>','align="RIGHT"');
		$grid->column('Saldo'  ,'<nformat><#saldo#></nformat>' ,'align="RIGHT"');
		$grid->build();

		$nombre = $this->datasis->dameval("SELECT nombre FROM sprv WHERE id=".$claves['id']." ");

		$data['content'] = $grid->output;
		$data["head"]     = script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$data['title']    = '<h1>Consulta de Proveedor</h1>';
		$data["subtitle"] = "
			<div align='center' style='border: 2px outset #EFEFEF;background: #EFEFEF;font-size:18px'>
				<a href='javascript:javascript:history.go(-1)'>(".$mCodigo.") ".$nombre."</a>
			</div>";
		$this->load->view('view_ventanas', $data);
	}

	function vcard($id_sprv) {
		$dbid = $this->db->escape($id_sprv);
		$sprv = $this->datasis->damerow("SELECT contacto, nombre, telefono, direc1 dire11 FROM sprv WHERE id=${dbid}");

		if ( !empty($sprv) ) {
			$this->load->library('Qr');
			$contacto=trim($sprv['contacto']);
			$nombre  =trim($sprv['nombre']);
			$telf1   =trim($sprv['telefono']);
			$telf2   ='';
			$direc   =trim($sprv['dire11']);
			if(!empty($contacto)){
				$empresa=$nombre;
				$nombre =$contacto;
			}else{
				$empresa='';
			}
			$text = "BEGIN:VCARD\n";
			$text.= "VERSION:2.1\n";
			$text.= "N:$nombre\n";
			$text.= "FN:$nombre\n";
			if(!empty($empresa)) $text.= "ORG:$empresa\n";
			//$text.= "TITLE:$cargo\n";
			if(!empty($telf1)) $text.= "TEL;WORK;VOICE:$telf1\n";
			//if(!empty($telf2)) $text.= "TEL;WORK;VOICE:$telf2\n";
			$text.= "ADR;WORK:$direc\n";
			$text.= "END:VCARD";
			$this->qr->imgcode($text);
		}
	}

	//******************************************************************
	// Fusionar
	//
	function sprvfusion(){
		$mviejo    = strtoupper($_REQUEST['mviejo']);
		$mnuevo    = strtoupper($_REQUEST['mnuevo']);

		//ELIMINAR DE SCLI
		$mYaEsta = $this->datasis->dameval("SELECT count(*) FROM sprv WHERE proveed=".$this->db->escape($mnuevo));

		if ( $mYaEsta > 0 )
			$this->db->query("DELETE FROM sprv WHERE proveed=".$this->db->escape($mviejo));
		else
			$this->db->query("UPDATE sprv SET proveed=".$this->db->escape($mnuevo)." WHERE proveed=".$this->db->escape($mviejo));

		// SPRM
		$mSQL = "UPDATE sprm SET cod_prv=".$this->db->escape($mnuevo)." WHERE cod_prv=".$this->db->escape($mviejo);
		$this->db->simple_query($mSQL);

		// APAN
		$mSQL = "UPDATE apan SET clipro=".$this->db->escape($mnuevo)." WHERE clipro=".$this->db->escape($mviejo)." AND tipo='P' ";
		$this->db->simple_query($mSQL);

		//APAN
		$mSQL = "UPDATE apan SET reinte=".$this->db->escape($mnuevo)." WHERE reinte=".$this->db->escape($mviejo)." AND tipo='C' ";

		// ITPPRO
		$mSQL = "UPDATE itppro SET cod_prv=".$this->db->escape($mnuevo)." WHERE cod_prv=".$this->db->escape($mviejo);
		$this->db->simple_query($mSQL);

		// BMOV CLIPRO='P'  CODCP
		$mSQL = "UPDATE bmov SET codcp=".$this->db->escape($mnuevo)." WHERE codcp=".$this->db->escape($mviejo)." AND clipro='P'";
		$this->db->simple_query($mSQL);

		// SCST
		$mSQL = "UPDATE scst SET proveed=".$this->db->escape($mnuevo)." WHERE proveed=".$this->db->escape($mviejo);
		$this->db->simple_query($mSQL);

		// ITSCST
		$mSQL = "UPDATE itscst SET proveed=".$this->db->escape($mnuevo)." WHERE proveed=".$this->db->escape($mviejo);
		$this->db->simple_query($mSQL);

		// ORDS
		$mSQL = "UPDATE ords SET proveed=".$this->db->escape($mnuevo)." WHERE proveed=".$this->db->escape($mviejo);
		$this->db->simple_query($mSQL);

		// ITORDS
		$mSQL = "UPDATE itords SET proveed=".$this->db->escape($mnuevo)." WHERE proveed=".$this->db->escape($mviejo);
		$this->db->simple_query($mSQL);

		// ORDC
		$mSQL = "UPDATE ordc SET proveed=".$this->db->escape($mnuevo)." WHERE proveed=".$this->db->escape($mviejo);
		$this->db->simple_query($mSQL);

		// ITORDS
		$mSQL = "UPDATE itordc SET proveed=".$this->db->escape($mnuevo)." WHERE proveed=".$this->db->escape($mviejo);
		$this->db->simple_query($mSQL);

		// GSER
		$mSQL = "UPDATE gser SET proveed=".$this->db->escape($mnuevo)." WHERE proveed=".$this->db->escape($mviejo);
		$this->db->simple_query($mSQL);

		// GITSER
		$mSQL = "UPDATE gitser SET proveed=".$this->db->escape($mnuevo)." WHERE proveed=".$this->db->escape($mviejo);
		$this->db->simple_query($mSQL);

		// CRUC
		$mSQL = "UPDATE cruc SET proveed=".$this->db->escape($mnuevo)." WHERE proveed=".$this->db->escape($mviejo)." AND MID(tipo,1,1)='P'";
		$this->db->simple_query($mSQL);

		// CRUC
		$mSQL = "UPDATE cruc SET cliente=".$this->db->escape($mnuevo)." WHERE cliente=".$this->db->escape($mviejo)." AND MID(tipo,3,1)='P'";
		$this->db->simple_query($mSQL);

		// PRMO
		$mSQL = "UPDATE prmo SET clipro=".$this->db->escape($mnuevo)." WHERE clipro=".$this->db->escape($mviejo)." AND tipop NOT IN ('1','3','6')";
		$this->db->simple_query($mSQL);

		// RIVA
		$mSQL = "UPDATE riva SET clipro=".$this->db->escape($mnuevo)." WHERE clipro=".$this->db->escape($mviejo);
		$this->db->simple_query($mSQL);

		// LVACA
		if ( $this->datasis->istabla('lvaca')){
			$mSQL = "UPDATE lvaca SET codprv=".$this->db->escape($mnuevo)." WHERE codprv=".$this->db->escape($mviejo);
			$this->db->simple_query($mSQL);
		}

		// LRUTA
		if ( $this->datasis->istabla('lruta')){
			$mSQL = "UPDATE lruta SET codprv=".$this->db->escape($mnuevo)." WHERE codprv=".$this->db->escape($mviejo);
			$this->db->simple_query($mSQL);
		}

		// LPAGO
		if ( $this->datasis->istabla('lpago')){
			$mSQL = "UPDATE lpago SET proveed=".$this->db->escape($mnuevo)." WHERE proveed=".$this->db->escape($mviejo);
			$this->db->simple_query($mSQL);
		}

		// LREDU
		if ( $this->datasis->istabla('lgasto')){
			$mSQL = "UPDATE lgasto SET proveed=".$this->db->escape($mnuevo)." WHERE proveed=".$this->db->escape($mviejo);
			$this->db->simple_query($mSQL);
		}
	}
	function instalar(){
		$campos=$this->db->list_fields('sprv');
		if (!in_array('id',$campos)){
			$this->db->query('ALTER TABLE sprv DROP PRIMARY KEY');
			$this->db->query('ALTER TABLE sprv ADD UNIQUE INDEX proveed (proveed)');
			$this->db->query('ALTER TABLE sprv ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if(!in_array('copre'      ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN copre     VARCHAR(11)  NULL DEFAULT NULL   AFTER cuenta');
		if(!in_array('ocompra'    ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN ocompra   CHAR(1)      NULL DEFAULT NULL   AFTER copre');
		if(!in_array('dcredito'   ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN dcredito  DECIMAL(3,0) NULL DEFAULT "0"    AFTER ocompra');
		if(!in_array('despacho'   ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN despacho  DECIMAL(3,0) NULL DEFAULT NULL   AFTER dcredito');
		if(!in_array('visita'     ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN visita    VARCHAR(9)   NULL DEFAULT NULL   AFTER despacho');
		if(!in_array('cate'       ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN cate      VARCHAR(20)  NULL DEFAULT NULL   AFTER visita');
		if(!in_array('reteiva'    ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN reteiva   DECIMAL(7,2) NULL DEFAULT "0.00" AFTER cate');
		if(!in_array('ncorto'     ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN ncorto    VARCHAR(20)  NULL DEFAULT NULL   AFTER nombre');
		if(!in_array('prefpago'   ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN prefpago  CHAR(1)      NULL DEFAULT "T"    COMMENT "Preferencia de pago, Transferencia, Deposito, Caja" AFTER reteiva');
		if(!in_array('canticipo'  ,$campos)) $this->db->query("ALTER TABLE sprv ADD COLUMN canticipo VARCHAR(15)  NULL DEFAULT NULL   COMMENT 'Cuenta contable de Anticipo'                        AFTER cuenta");
		if(!in_array('estado'     ,$campos)) $this->db->query("ALTER TABLE sprv ADD COLUMN estado      INT(11) NULL DEFAULT 0 COMMENT 'Estados o Entidades'");
		if(!in_array('aniversario',$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN aniversario DATE NULL DEFAULT NULL COMMENT "Fecha de Aniversario"');
		if(!in_array('registrado' ,$campos)) $this->db->query('ALTER TABLE `sprv` ADD COLUMN `registrado` DATE NULL DEFAULT NULL AFTER `aniversario`');

		$this->db->query('ALTER TABLE sprv CHANGE COLUMN nomfis   nomfis   VARCHAR(200) DEFAULT NULL NULL');
		$this->db->query('ALTER TABLE sprv CHANGE COLUMN telefono telefono TEXT NULL    DEFAULT NULL');
		$this->db->query('ALTER TABLE sprv CHANGE COLUMN direc1   direc1   TEXT NULL    DEFAULT NULL');
	}
}
