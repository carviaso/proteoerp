<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
//notaentrega
class Snte extends Controller {
	var $mModulo='SNTE';
	var $titp='Notas de Entrega';
	var $tits='Notas de Entrega';
	var $url ='ventas/snte/';

	function Snte(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'SNTE', $ventana=0 );
		$this->vnega  = trim(strtoupper($this->datasis->traevalor('VENTANEGATIVA')));
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 900, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){
		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$param['grids'][] = $grid1->deploy();

		$readyLayout = $grid->readyLayout2( 220, 192, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );


		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'imprimir'  ,'img'=>'assets/default/images/print.png',  'alt' => 'Reimprimir', 'label'=>'Reimprimir documento'));
		$grid->wbotonadd(array('id'=>'bffact' , 'img'=>'images/star.png'                ,'alt' => 'Facturar'  , 'label'=>'Facturar'));

		$WestPanel = $grid->deploywestp();
		//Panel Central y Sur
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );


		$adic = array(
			array('id'=>'ffact' , 'title'=>'Convertir en factura'),
			array('id'=>'fedita', 'title'=>'Agregar/Editar registro'),
			array('id'=>'fborra', 'title'=>'Eliminar registro'),
			array('id'=>'fshow' , 'title'=>'Mostrar registro')
		);

		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('SNTE', 'JQ');
		$param['otros']        = $this->datasis->otros('SNTE', 'JQ');
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		$param['readyLayout']  = $readyLayout;
		$param['centerpanel']  = $centerpanel;

		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0, $grid1 ){
		$bodyscript ='<script type="text/javascript">';

		$bodyscript .= '
		function snteadd() {
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function snteedit() {
				var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
				if (id)	{
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					if ( ret.status == "PE" ) {
						$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
							$("#fedita").html(data);
							$("#fedita").dialog( "open" );
						});
					} else {
						$.prompt("<h1>Orden no modificable, esta cerrada o en back order");
					}
				} else {
					$.prompt("<h1>Por favor Seleccione un Registro</h1>");
				}
		};';

		$bodyscript .= '
		function snteshow() {
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
		function sntedel() {
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						try{
							var json = JSON.parse(data);
							if(json.status == "A"){
								apprise("Registro eliminado");
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
							}else{
								apprise("Registro no se puede eliminado");
							}
						}catch(e){
							$("#fborra").html(data);
							$("#fborra").dialog("open");
						}
					});
				}
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		};';

		$bodyscript .= '
		function bloqueo() {
			$("#addlink").hide();
			$(\'a[onclick^="del_sitems"]\').hide();
			$(\'input[id^="cana_"]\').attr("readonly","readonly");
			$(\'input[id^="codigoa_"]\').attr("readonly","readonly");
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
		jQuery("#bffact").click(function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				$.post("'.site_url('ventas/sfac/creafromsnte/N').'/"+ret.numero+"/create",
				function(data){
					$("#ffact").html(data);
					$("#ffact").dialog( "open" );
					bloqueo();
				});
			} else { $.prompt("<h1>Por favor Seleccione una nota de entrega</h1>");}
		});';

		$bodyscript .='
		jQuery("#imprimir").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){

				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				var btns= { };

				if(ret.factura != null && ret.factura != false){
					btns={ "Con precios": "N","Sin precios":"S", Factura: "F" };
				}else{
					btns={ "Con precios": "N","Sin precios":"S"};
				}

				$.prompt("<h2>Qu&eacute; modalidad desea imprimir?</h2>",{
					buttons: btns,
					submit: function(e,v,m,f){
						if(v=="F"){
							window.open(\''.site_url($this->url.'sfacprint').'/\'+ret.factura, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
						}else if(v=="S"){
							window.open(\''.site_url('formatos/ver/SNTE').'/\'+id+"/S", \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
						}else{
							window.open(\''.site_url('formatos/ver/SNTE').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
						}
					}
				});

			}else{
				$.prompt("<h1>Por favor Seleccione un Movimiento</h1>");
			}
		});';

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 550, width: 750, modal: true,
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/SNTE').'/\'+res.id+\'/id\'').';
									return true;
								} else {
									apprise(json.mensaje);
								}
							}catch(e){
								$("#fedita").html(r);
								bloqueo();
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

		//Convierte Factura
		$bodyscript .= '
			$("#ffact").dialog({
				autoOpen: false, height: 600, width: 800, modal: true,
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
									if ( json.status == "A" ) {
										if ( json.manual == "N" ) {
											$( "#ffact" ).dialog( "close" );
											jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
											window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+json.pk.id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
											return true;
										}else{
											$.post("'.site_url($this->url.'dataedit/S/create').'",
											function(data){
												$("#ffact").html(data);
											})
											window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+json.pk.id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
											return true;
										}
									}else{
										apprise(json.mensaje);
									}
								}catch(e){
									$("#ffact").html(r);
								}
							}
						});
					},
					"Cancelar": function() {
						$("#ffact").html("");
						$( this ).dialog( "close" );
						$("#newapi'.$grid0.'").trigger("reloadGrid");
					}
				},
				close: function() {
					$("#ffact").html("");
				}
			});';

		$bodyscript .= '	});';
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


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 45,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 1 }',
			'cellattr'      => 'function(rowId, tv, aData, cm, rdata){
				var tips = "";
				if(aData.tipo !== undefined){
					if(aData.tipo=="C"){
						tips = "Cerrada";
					}else if(aData.tipo=="E"){
						tips = "Entregada";
					}else if(aData.tipo=="A"){
						tips = "Anulada";
					}else{
						tips = aData.referen;
					}
				}
				return \'title="\'+tips+\'"\';
			}'
		));


		$grid->addField('numero');
		$grid->label('N&uacute;mero');
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
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('vende');
		$grid->label('Vendedor');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('factura');
		$grid->label('Factura');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('cod_cli');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'align'         => "'center'",
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


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));

		$grid->addField('stotal');
		$grid->label('Subtotal');
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

		$grid->addField('impuesto');
		$grid->label('Impuesto');
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

		$grid->addField('gtotal');
		$grid->label('Total');
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

/*
		$grid->addField('dir_cli');
		$grid->label('Dir_cli');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));

		$grid->addField('dir_cl1');
		$grid->label('Dir_cl1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));
*/

		$grid->addField('orden');
		$grid->label('Orden');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));

		$grid->addField('observa');
		$grid->label('Observaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 105 }',
		));

		$grid->addField('cajero');
		$grid->label('Cajero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));

		$grid->addField('fechafac');
		$grid->label('Fecha Fac.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
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
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));


		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 4 }',
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
		));

		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		/*$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));*/

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('190');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('function(id){
				if (id){
					var ret = $("#titulos").getRowData(id);
					jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
					$.ajax({
						url: "'.base_url().$this->url.'tabla/"+id,
						success: function(msg){
							$("#ladicional").html(msg);
						}
					});
				}
			},
			afterInsertRow:
			function( rid, aData, rowe){
				if(aData.tipo == "E"){
					$(this).jqGrid( "setCell", rid, "tipo","",{color:"#000000", background:"#DCFFB5" });
				}else if(aData.tipo == "A"){
					//$(this).jqGrid( "setRowData", rid, "tipo",{color:"#000000", background:"#C90623" });
					$(this).jqGrid( "setCell", rid, "tipo","",{color:"#000000", background:"#C90623" });
				}
			}
		');


		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setEdit(false);
		$grid->setAdd(   $this->datasis->sidapuede('SNTE','INCLUIR%' ));
		$grid->setDelete($this->datasis->sidapuede('SNTE','BORR_REG%'));
		$grid->setSearch($this->datasis->sidapuede('SNTE','BUSQUEDA%'));


		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');
		$grid->setBarOptions('addfunc: snteadd, editfunc: snteedit, delfunc: sntedel, viewfunc: snteshow');


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
		$mWHERE = $grid->geneTopWhere('snte');

		$response   = $grid->getData('snte', array(array()), array(), false, $mWHERE, 'id', 'desc' );
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
		$mcodp  = "id";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			/*
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM snte WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('snte', $data);
					echo "Registro Agregado";

					logusu('SNTE',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";
			*/

		} elseif($oper == 'edit') {
			unset($data[$mcodp]);
			// Solo puede cambiar si empieza por _
			$mFactura = $this->datasis->dameval("SELECT factura FROM snte WHERE id=$id");
			$mnumero  = $this->datasis->dameval("SELECT numero  FROM snte WHERE id=$id");
			if ( $mFactura[0] == "_" ){
				$this->db->where("id", $id);
				$this->db->update('snte', $data);
				logusu('SNTE',"Nro de Factura Cambiado ".$mFactura." en la orden ".$numero." MODIFICADO");
				echo "Orden ".$mnumero." Cambiada";
			} else
				echo "Orden No Cambiada";


		}elseif($oper == 'del'){
			echo 'Deshabilitado';
		}
	}


	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

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


		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 15 }',
		));


		$grid->addField('desca');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 28 }',
		));


		$grid->addField('cana');
		$grid->label('Cantidad');
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


		$grid->addField('iva');
		$grid->label('Iva');
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
		$grid->addField('mostrado');
		$grid->label('Mostrado');
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

		$grid->addField('entregado');
		$grid->label('Entregado');
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


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));


		/*$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));*/


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


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('160');
		//$grid->setTitle($this->titp);
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(false);
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
		if ($id == false ){
			$id = $this->datasis->dameval('SELECT MAX(id) AS id FROM snte');
		}
		if(empty($id)) return '';
		$dbid = intval($id);

		$numero   = $this->datasis->dameval("SELECT numero FROM snte WHERE id=${dbid}");
		$dbnumero = $this->db->escape($numero);

		$orderby= '';
		$sidx=$this->input->post('sidx');
		if($sidx){
			$campos = $this->db->list_fields('itsnte');
			if(in_array($sidx,$campos)){
				$sidx = trim($sidx);
				$sord   = $this->input->post('sord');
				$orderby="ORDER BY `${sidx}` ".(($sord=='asc')? 'ASC':'DESC');
			}
		}

		$grid     = $this->jqdatagrid;
		$mSQL     = "SELECT * FROM itsnte WHERE numero=${dbnumero} ${orderby}";
		$response = $grid->getDataSimple($mSQL);

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
		$mcodp  = '??????';
		$check  = 0;
/*
		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM itsnte WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('itsnte', $data);
					echo "Registro Agregado";

					logusu('ITSNTE',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM itsnte WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM itsnte WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE itsnte SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("itsnte", $data);
				logusu('ITSNTE',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('itsnte', $data);
				logusu('ITSNTE',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
		$meco = $this->datasis->dameval("SELECT $mcodp FROM itsnte WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM itsnte WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM itsnte WHERE id=$id ");
				logusu('ITSNTE',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};\
*/
	}


	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');

		$modbus=array(
		'tabla'   =>'sinv',
		'columnas'=>array(
				'codigo'  =>'C&oacute;digo',
				'descrip' =>'Descripci&oacute;n',
				'precio1' =>'Precio 1',
				'precio2' =>'Precio 2',
				'precio3' =>'Precio 3',
				'existen' =>'Existencia',
				),
		'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'descrip'),
		'retornar'=>array(
			'codigo' =>'codigo_<#i#>',
			'descrip'=>'desca_<#i#>',
			'base1'  =>'precio1_<#i#>',
			'base2'  =>'precio2_<#i#>',
			'base3'  =>'precio3_<#i#>',
			'base4'  =>'precio4_<#i#>',
			'iva'    =>'itiva_<#i#>',
			'peso'   =>'sinvpeso_<#i#>',
			'tipo'   =>'sinvtipo_<#i#>',
		),
		'p_uri'=>array(4=>'<#i#>'),
		'where'   => '`activo` = "S" AND `tipo` = "Articulo"',
		'script'  => array('post_modbus_sinv(<#i#>)'),
		'titulo'  =>'Buscar Articulo');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>');

		$mSCLId=array(
		'tabla'   =>'scli',
		'columnas'=>array(
			'cliente' =>'C&oacute;digo Cliente',
			'nombre'=>'Nombre',
			'cirepre'=>'Rif/Cedula',
			'dire11'=>'Direcci&oacute;n',
			'tipo'=>'Tipo'),
		'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'cod_cli','nombre'=>'nombre',
						  'dire11' =>'dir_cli','tipo'  =>'sclitipo'),
		'titulo'  =>'Buscar Cliente',
		'script'  => array('post_modbus_scli()'));
		$btnc =$this->datasis->modbus($mSCLId);

		$do = new DataObject('snte');
		$do->rel_one_to_many('itsnte', 'itsnte', 'numero');
		$do->pointer('scli' ,'scli.cliente=snte.cod_cli','scli.tipo AS sclitipo','left');
		$do->rel_pointer('itsnte','sinv','itsnte.codigo=sinv.codigo','sinv.descrip AS sinvdescrip, sinv.base1 AS sinvprecio1, sinv.base2 AS sinvprecio2, sinv.base3 AS sinvprecio3, sinv.base4 AS sinvprecio4, sinv.iva AS sinviva, sinv.peso AS sinvpeso,sinv.tipo AS sinvtipo');

		$edit = new DataDetails('Nota de entrega', $do);
		$edit->on_save_redirect=false;
		$edit->set_rel_title('itsnte','Producto <#o#>');

		$edit->pre_process( 'insert','_pre_insert');
		$edit->pre_process( 'update','_pre_update');
		$edit->pre_process( 'delete','_pre_delete');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'required';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->calendar = false;
		$edit->fecha->size = 10;

		$edit->vende = new  dropdownField ('Vendedor', 'vende');
		$edit->vende->options('SELECT vendedor, CONCAT(vendedor,\' \',nombre) nombre FROM vend ORDER BY vendedor');
		$edit->vende->style='width:200px;';
		$edit->vende->size = 5;

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->mode='autohide';
		$edit->numero->maxlength=8;
		$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->peso = new inputField('Peso', 'peso');
		$edit->peso->css_class = 'inputnum';
		$edit->peso->readonly  = true;
		$edit->peso->type      = 'inputhidden' ;
		$edit->peso->size      = 10;

		$edit->cliente = new inputField('Cliente','cod_cli');
		$edit->cliente->size = 6;
		//$edit->cliente->maxlength=5;
		$edit->cliente->rule = 'required';
		$edit->cliente->append($btnc);

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->size = 25;
		$edit->nombre->type='inputhidden';
		$edit->nombre->maxlength=40;
		$edit->nombre->autocomplete=false;

		$edit->factura = new inputField('Factura', 'factura');
		$edit->factura->size = 10;
		$edit->factura->when=array('show');

		$edit->almacen = new  dropdownField ('Almac&eacute;n', 'almacen');
		$edit->almacen->options('SELECT ubica, CONCAT(ubica,\' \',ubides) nombre FROM caub ORDER BY ubica');
		$edit->almacen->rule = 'required';
		$edit->almacen->style= 'width:200px;';
		$edit->almacen->size = 5;

		$edit->orden = new inputField('Orden', 'orden');
		$edit->orden->size = 10;

		$edit->observa = new inputField('Observaci&oacute;n', 'observa');
		$edit->observa->size = 37;

		$edit->dir_cli = new inputField('Direcci&oacute;n','dir_cli');
		$edit->dir_cli->type='inputhidden';
		$edit->dir_cli->size = 37;

		//$edit->dir_cl1 = new inputField(' ','dir_cl1');
		//$edit->dir_cl1->size = 55;

		//Para saber que precio se le va a dar al cliente
		$edit->sclitipo = new hiddenField('', 'sclitipo');
		$edit->sclitipo->db_name     = 'sclitipo';
		$edit->sclitipo->pointer     = true;
		$edit->sclitipo->insertValue = 1;

		//Campos para el detalle
		$edit->codigo = new inputField('C&oacute;digo <#o#>', 'codigo_<#i#>');
		$edit->codigo->size     = 8;
		$edit->codigo->db_name  = 'codigo';
		$edit->codigo->rel_id   = 'itsnte';
		$edit->codigo->rule     = 'required';
		$edit->codigo->append($btn);
		$edit->codigo->style    = 'width:80%';

		$edit->desca = new inputField('Descripci&oacute;n <#o#>', 'desca_<#i#>');
		$edit->desca->size=36;
		$edit->desca->db_name='desca';
		$edit->desca->maxlength=40;
		$edit->desca->readonly  = true;
		$edit->desca->rel_id='itsnte';
		$edit->desca->style ='width:98%';

		$edit->cana = new inputField('Cantidad <#o#>', 'cana_<#i#>');
		$edit->cana->db_name  = 'cana';
		$edit->cana->css_class= 'inputnum';
		$edit->cana->rel_id   = 'itsnte';
		$edit->cana->maxlength= 10;
		$edit->cana->size     = 6;
		$edit->cana->rule     = 'required|positive|callback_chcananeg[<#i#>]';
		$edit->cana->autocomplete=false;
		$edit->cana->onkeyup  ='importe(<#i#>)';
		$edit->cana->style    = 'width:98%';

		$edit->precio = new inputField('Precio <#o#>', 'precio_<#i#>');
		$edit->precio->db_name   = 'precio';
		$edit->precio->css_class = 'inputnum';
		$edit->precio->rel_id    = 'itsnte';
		$edit->precio->size      = 10;
		$edit->precio->rule      = 'required|positive|callback_chpreca[<#i#>]';
		$edit->precio->readonly  = true;
		$edit->precio->style    = 'width:98%';

		$edit->importe = new inputField('Importe <#o#>', 'importe_<#i#>');
		$edit->importe->db_name='importe';
		$edit->importe->size=10;
		$edit->importe->css_class='inputnum';
		$edit->importe->rel_id   ='itsnte';
		$edit->importe->style    ='width:98%';
		$edit->importe->type     ='inputhidden';

		for($i=1;$i<=4;$i++){
			$obj='precio'.$i;
			$edit->$obj = new hiddenField('Precio <#o#>', $obj.'_<#i#>');
			$edit->$obj->db_name   = 'sinv'.$obj;
			$edit->$obj->rel_id    = 'itsnte';
			$edit->$obj->pointer   = true;
		}
		$edit->itiva = new hiddenField('', 'itiva_<#i#>');
		$edit->itiva->db_name  = 'iva';
		$edit->itiva->rel_id   = 'itsnte';

		$edit->sinvpeso = new hiddenField('', 'sinvpeso_<#i#>');
		$edit->sinvpeso->db_name   = 'sinvpeso';
		$edit->sinvpeso->rel_id    = 'itsnte';
		$edit->sinvpeso->pointer   = true;

		$edit->sinvtipo = new hiddenField('', 'sinvtipo_<#i#>');
		$edit->sinvtipo->db_name   = 'sinvtipo';
		$edit->sinvtipo->rel_id    = 'itsnte';
		$edit->sinvtipo->pointer   = true;
		//fin de campos para detalle

		$edit->impuesto  = new hiddenField('Impuesto', 'impuesto');
		$edit->impuesto->size = 20;
		$edit->impuesto->css_class='inputnum';

		$edit->stotal  = new hiddenField('Sub.Total', 'stotal');
		$edit->stotal->size = 20;
		$edit->stotal->css_class='inputnum';

		$edit->gtotal  = new hiddenField('Total', 'gtotal');
		$edit->gtotal->size = 20;
		$edit->gtotal->css_class='inputnum';

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

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
			$conten['form']  =&  $edit;
			$this->load->view('view_snte', $conten);
		}
	}

	function tabla() {
		$id = isset($_REQUEST['id'])  ? $_REQUEST['id']   :  0;
		$id = intval($id);
		$cliente  = $this->datasis->dameval("SELECT cod_cli FROM snte WHERE id='$id'");
		$dbcliente= $this->db->escape($cliente);
		$mSQL = "SELECT cod_cli, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos FROM smov WHERE cod_cli=${dbcliente} AND abonos<>monto AND tipo_doc<>'AB' ORDER BY fecha ";
		$query = $this->db->query($mSQL);
		$salida = '';
		$saldo = 0;
		if ( $query->num_rows() > 0 ){
			$salida = "<br><table width='100%' border=1>";
			$salida .= "<tr bgcolor='#e7e3e7'><td colspan=3>Movimiento en Cuentas X Cobrar</td></tr>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";

			foreach ($query->result_array() as $row){
				$salida .= "<tr>";
				$salida .= "<td>".$row['tipo_doc']."</td>";
				$salida .= "<td>".$row['numero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']-$row['abonos']).   "</td>";
				$salida .= "</tr>";
				if ( $row['tipo_doc'] == 'FC' or $row['tipo_doc'] == 'ND' or $row['tipo_doc'] == 'GI' )
					$saldo += $row['monto']-$row['abonos'];
				else
					$saldo -= $row['monto']-$row['abonos'];
			}
			$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'>Saldo : ".nformat($saldo). "</td></tr>";
			$salida .= "</table>";
		}
		$query->free_result();

		echo $salida;
	}

	function sntefactura(){
		$factura = $this->uri->segment($this->uri->total_segments());
		$numero  = $this->uri->segment($this->uri->total_segments()-1);
		$cod_cli = $this->datasis->dameval("SELECT cod_cli FROM snte WHERE numero='$numero'");
		$fecha   = $this->datasis->dameval("SELECT fecha FROM snte WHERE numero='$numero'");

		//revisa si elimina el nro
		if ($factura == 0) {
			$this->db->simple_query("UPDATE snte SET factura='', fechafac=0 WHERE numero='$numero'");
			logusu('SNTE',"Quita Nro. Factura $numero");
			echo "Nro de Factura eliminado";
		} else {
			if ($this->datasis->dameval("SELECT COUNT(*) FROM sfac WHERE tipo_doc='F' AND numero='$factura' AND cod_cli='$cod_cli'")==1)
			{
				$fechafac=$this->datasis->dameval("SELECT fecha FROM sfac WHERE tipo_doc='F' AND numero='$factura' AND cod_cli='$cod_cli'");
				$this->db->simple_query("UPDATE snte SET factura='$factura', fechafac=$fechafac WHERE numero='$numero'");
				logusu('SNTE',"Cambia Nro. Factura $numero -> $factura ");
				echo "Nro de Factura Cambiado ";
			} else {
				echo "Esa Factura no corresponde ";
			}
		}
	}

	function _pre_insert($do){
		$numero  = $this->datasis->fprox_numero('nsnte');
		$transac = $this->datasis->fprox_numero('ntransa');
		$fecha   = $do->get('fecha');
		$vende   = $do->get('vende');
		$usuario = $do->get('usuario');
		$estampa = $do->get('estampa');
		$hora    = $do->get('hora');

		$iva=$stotal=0;
		$cana=$do->count_rel('itsnte');
		for($i=0;$i<$cana;$i++){
			$itcana    = $do->get_rel('itsnte','cana',$i);
			$itprecio  = $do->get_rel('itsnte','precio',$i);
			$itiva     = $do->get_rel('itsnte','iva',$i);
			$itimporte = $itprecio*$itcana;
			$iiva      = $itimporte*($itiva/100);

			$do->set_rel('itsnte','importe'  ,$itimporte,$i);
			$do->set_rel('itsnte','mostrado' ,$itimporte+$iiva,$i);

			$iva    +=$iiva ;
			$stotal +=$itimporte;
		}
		$gtotal=$stotal+$iva;
		$do->set('tipo'     ,'E');
		$do->set('transac' ,$transac);
		$do->set('stotal'  ,round($stotal,2));
		$do->set('gtotal'  ,round($gtotal,2));
		$do->set('impuesto',round($iva   ,2));
		$do->set('numero'  ,$numero);

		return true;
	}

	function _post_insert($do){
		$numero   = $do->get('numero');
		$almacen  = $do->get('almacen');
		$fecha    = $do->get('fecha');
		$cod_cli  = $do->get('cod_cli');
		$nombre   = $do->get('nombre');
		$monto    = $do->get('gtotal');
		$estampa  = $do->get('estampa');
		$hora     = $do->get('hora');
		$transac  = $do->get('transac');
		$usuario  = $do->get('usuario');

		$dbnumero = $this->db->escape($numero);
		$dbalmacen= $this->db->escape($almacen);

		$cana=$do->count_rel('itsnte');
		for($i=0;$i<$cana;$i++){
			$itcodigo = $do->get_rel('itsnte','codigo',$i);
			$itcana   = $do->get_rel('itsnte','cana'  ,$i);

			$this->datasis->sinvcarga($itcodigo, $almacen, $itcana);
		}

		$codigo=$do->get('numero');

		$sntend = $this->datasis->traevalor('SNTEND','Crea la cuenta por cobrar para las notas de entrega');
		if($sntend=='S'){
			$mnumnd = $this->datasis->fprox_numero('ndcli');
			$data=array();
			$data['cod_cli']    = $cod_cli;
			$data['nombre']     = $nombre;
			$data['tipo_doc']   = 'ND';
			$data['numero']     = $mnumnd;
			$data['fecha']      = $fecha;
			$data['monto']      = $monto;
			$data['impuesto']   = 0;
			$data['abonos']     = 0;
			$data['vence']      = $fecha;
			$data['tipo_ref']   = 'NE';
			$data['num_ref']    = $numero;
			$data['observa1']   = 'NOTA DE ENTREGA POR COBRAR';
			$data['estampa']    = $estampa;
			$data['hora']       = $hora;
			$data['transac']    = $transac;
			$data['usuario']    = $usuario;
			$data['codigo']     = 'NOCON';
			$data['descrip']    = 'NOTA DE CONTABILIDAD';
			$data['nroriva']    = '';
			$data['emiriva']    = '';

			$mSQL = $this->db->insert_string('smov', $data);
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'snte'); }
		}

		logusu('snte',"Nota entrega ${numero} CREADO");
	}

	//Chequea si puede o no vender negativo
	function chcananeg($val,$i){
		$almacen = $this->input->post('almacen');

		if($this->vnega=='N'){
			$codigo   = $this->input->post('codigo_'.$i);
			$dbcodigo = $this->db->escape($codigo);
			$dbalmacen= $this->db->escape($almacen);
			$tipo     = trim($this->datasis->dameval("SELECT tipo FROM sinv WHERE codigo=${dbcodigo}"));
			if($tipo[0]=='S') return true;

			$mSQL    = "SELECT SUM(a.existen) AS cana FROM itsinv AS a JOIN caub AS b ON a.alma=b.ubica AND b.tipo='S' WHERE a.codigo=${dbcodigo} AND b.ubica=${dbalmacen}";
			$existen = floatval($this->datasis->dameval($mSQL));
			$val     = floatval($val);
			if($val>$existen){
				$this->validation->set_message('chcananeg', 'El art&iacute;culo '.$codigo.' no tiene cantidad suficiente ('.nformat($existen).')');
				return false;
			}
		}
		return true;
	}

	function chpreca($preca,$ind){
		$codigo  = $this->input->post('codigo_'.$ind);
		$precio4 = $this->datasis->dameval('SELECT base4 FROM sinv WHERE codigo='.$this->db->escape($codigo));
		if($precio4<0) $precio4=0;

		if($preca<$precio4){
			$this->validation->set_message('chpreca', 'El art&iacute;culo '.$codigo.' debe contener un precio de al menos '.nformat($precio4));
			return false;
		}else{
			return true;
		}
	}

	function _pre_delete($do){
		$tipo     = $do->get('tipo');
		$transac  = $do->get('transac');
		$numero   = $do->get('numero');
		$almacen  = $do->get('almacen');
		$dbtransac= $this->db->escape($transac);
		$dbnumero = $this->db->escape($numero);
		$dbalmacen= $this->db->escape($almacen);

		if($tipo=='C'){
			$do->error_message_ar['pre_del']='La nota de entrega esta cerrada, no se puede anular';
			return false;
		}

		if($tipo=='A'){
			$do->error_message_ar['pre_del']='La nota de entrega ya fue anulada en otro momento';
			return false;
		}

		if($tipo=='E'){

			$abonos = $this->datasis->dameval('SELECT abonos FROM smov WHERE transac='.$dbtransac);
			if($abonos > 0){
				$do->error_message_ar['pre_del']='La nota de debito relacionada a esta nota esta abonada, debe primero reversar el pago.';
				return false;
			}

			$mSQL='UPDATE sinv
			JOIN itsnte ON sinv.codigo=itsnte.codigo
			SET sinv.existen=sinv.existen+itsnte.cana
			WHERE itsnte.numero='.$dbnumero;
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'snte'); }

			$mSQL='UPDATE itsinv
			JOIN itsnte ON itsinv.codigo=itsnte.codigo
			SET itsinv.existen=itsinv.existen+itsnte.cana
			WHERE itsnte.numero='.$dbnumero.' AND itsinv.alma='.$dbalmacen;
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'snte'); }

			$mSQL ="UPDATE snte SET tipo='A' WHERE numero = ${dbnumero}";
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'snte'); }

			$do->error_message_ar['pre_del']='La nota de entrega fue anulada';
			return false;
		}

		$do->error_message_ar['pre_del']='Accion no valida';
		return false;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='No se puede cambiar una nota de entrega';
		return false;
	}

	function _post_delete($do){
		$codigo=$do->get('numero');
		logusu('snte',"Nota Entrega ${codigo} ELIMINADO");
	}

	function sclibu(){
		$numero = $this->uri->segment(4);
		$id = $this->datasis->dameval("SELECT b.id FROM snte a JOIN scli b ON a.cod_cli=b.cliente WHERE numero='$numero'");
		redirect('ventas/scli/dataedit/show/'.$id);
	}

	function sfacprint($factura){
		$dbnumero=$this->db->escape($factura);
		$mSQL='SELECT a.id FROM sfac AS a WHERE a.tipo_doc="F" AND a.numero='.$dbnumero;
		$sfac_id=$this->datasis->dameval($mSQL);
		if(!empty($sfac_id)){
			redirect('ventas/sfac/dataprint/modify/'.$sfac_id);
		}else{
			echo 'Factura no encontrada';
		}
	}

	function instalar(){
		if(!$this->datasis->iscampo('snte','id')){
			$this->db->simple_query('ALTER TABLE snte DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE snte ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE snte ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}
	}

}
