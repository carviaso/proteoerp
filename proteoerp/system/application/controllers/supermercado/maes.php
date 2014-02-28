<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Maes extends Controller {
	var $mModulo = 'MAES';
	var $titp    = 'MAESTRO DE INVENTARIO';
	var $tits    = 'MAESTRO DE INVENTARIO';
	var $url     = 'supermercado/maes/';

	function Maes(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'MAES', $ventana=0 );
	}

	function index(){
		if ( !$this->datasis->iscampo('maes','id') ) {
			$this->db->simple_query('ALTER TABLE maes DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE maes ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE maes ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		//$this->datasis->creaintramenu(array('modulo'=>'330','titulo'=>'Inventario','mensaje'=>'Maestro de Inventario','panel'=>'INVENTARIO','ejecutar'=>'inventario/maes','target'=>'popu','visible'=>'S','pertenece'=>'3','ancho'=>900,'alto'=>600));
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

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);


		$param['script']      = script('sinvmaes.js');
		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('MAES', 'JQ');
		$param['otros']       = $this->datasis->otros('MAES', 'JQ');
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
		function maesadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function maesedit(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function maesshow(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/show').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function maesdel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						try{
							var json = JSON.parse(data);
							if (json.status == "A"){
								apprise("Registro eliminado");
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
		$(function(){
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
		$("#fedita").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/MAES').'/\'+res.id+\'/id\'').';
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
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

/*
		$grid->addField('referen');
		$grid->label('Referen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));
*/

		$grid->addField('barras');
		$grid->label('Barras');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('depto');
		$grid->label('Depto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('familia');
		$grid->label('Familia');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('grupo');
		$grid->label('Grupo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));

/*
		$grid->addField('nom_grup');
		$grid->label('Nom_grup');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
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
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));

		$grid->addField('descrip');
		$grid->label('Descrip');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('corta');
		$grid->label('Corta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));

		$grid->addField('unidad');
		$grid->label('Unidad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));

		$grid->addField('presenta');
		$grid->label('Presenta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));

		$grid->addField('mempaq');
		$grid->label('Mempaq');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));

		$grid->addField('dempaq');
		$grid->label('Dempaq');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));

		$grid->addField('empaque');
		$grid->label('Empaque');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:27, maxlength: 27 }',
		));

/*
		$grid->addField('pais');
		$grid->label('Pais');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 160,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:16, maxlength: 16 }',
		));
*/

		$grid->addField('fracxuni');
		$grid->label('Fracxuni');
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


		$grid->addField('alcohol');
		$grid->label('Alcohol');
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


		$grid->addField('tamano');
		$grid->label('Tamano');
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


		$grid->addField('medida');
		$grid->label('Medida');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
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


		$grid->addField('cu_inve');
		$grid->label('Cu_inve');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('moneda');
		$grid->label('Moneda');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('costo');
		$grid->label('Costo');
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


		$grid->addField('costotal');
		$grid->label('Costotal');
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


		$grid->addField('existen');
		$grid->label('Existen');
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


		$grid->addField('fracci');
		$grid->label('Fracci');
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


		$grid->addField('maximo');
		$grid->label('Maximo');
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


		$grid->addField('minimo');
		$grid->label('Minimo');
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


		$grid->addField('susti');
		$grid->label('Clave');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));


		$grid->addField('modifi');
		$grid->label('Modifi');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('volumen');
		$grid->label('Volumen');
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


		$grid->addField('cprv1');
		$grid->label('Cprv1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('nprv1');
		$grid->label('Nprv1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('pprv1');
		$grid->label('Pprv1');
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


		$grid->addField('ultimo');
		$grid->label('Ultimo');
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


		$grid->addField('uprv1');
		$grid->label('Uprv1');
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


		$grid->addField('fprv1');
		$grid->label('Fprv1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('cprv2');
		$grid->label('Cprv2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('nprv2');
		$grid->label('Nprv2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('pprv2');
		$grid->label('Pprv2');
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


		$grid->addField('uprv2');
		$grid->label('Uprv2');
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


		$grid->addField('fprv2');
		$grid->label('Fprv2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('cprv3');
		$grid->label('Cprv3');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('nprv3');
		$grid->label('Nprv3');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('pprv3');
		$grid->label('Pprv3');
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


		$grid->addField('uprv3');
		$grid->label('Uprv3');
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


		$grid->addField('fprv3');
		$grid->label('Fprv3');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('cprv4');
		$grid->label('Cprv4');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('nprv4');
		$grid->label('Nprv4');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('pprv4');
		$grid->label('Pprv4');
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


		$grid->addField('uprv4');
		$grid->label('Uprv4');
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


		$grid->addField('fprv4');
		$grid->label('Fprv4');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('cprv5');
		$grid->label('Cprv5');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));


		$grid->addField('nprv5');
		$grid->label('Nprv5');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('pprv5');
		$grid->label('Pprv5');
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


		$grid->addField('uprv5');
		$grid->label('Uprv5');
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


		$grid->addField('fprv5');
		$grid->label('Fprv5');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('margen1');
		$grid->label('Margen1');
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


		$grid->addField('base1');
		$grid->label('Base1');
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


		$grid->addField('precio1');
		$grid->label('Precio1');
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


		$grid->addField('margen2');
		$grid->label('Margen2');
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


		$grid->addField('base2');
		$grid->label('Base2');
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


		$grid->addField('precio2');
		$grid->label('Precio2');
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


		$grid->addField('margen3');
		$grid->label('Margen3');
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


		$grid->addField('base3');
		$grid->label('Base3');
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


		$grid->addField('precio3');
		$grid->label('Precio3');
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


		$grid->addField('margen4');
		$grid->label('Margen4');
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


		$grid->addField('base4');
		$grid->label('Base4');
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


		$grid->addField('precio4');
		$grid->label('Precio4');
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


		$grid->addField('margen5');
		$grid->label('Margen5');
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


		$grid->addField('base5');
		$grid->label('Base5');
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


		$grid->addField('precio5');
		$grid->label('Precio5');
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


		$grid->addField('can_ven');
		$grid->label('Can_ven');
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


		$grid->addField('margm2');
		$grid->label('Margm2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('basem2');
		$grid->label('Basem2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('precm2');
		$grid->label('Precm2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('margm3');
		$grid->label('Margm3');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('basem3');
		$grid->label('Basem3');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('precm3');
		$grid->label('Precm3');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('oferta');
		$grid->label('Oferta');
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


		$grid->addField('fdesde');
		$grid->label('Fdesde');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('hdesde');
		$grid->label('Hdesde');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('fhasta');
		$grid->label('Fhasta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('hhasta');
		$grid->label('Hhasta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('lleve');
		$grid->label('Lleve');
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


		$grid->addField('pague');
		$grid->label('Pague');
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


		$grid->addField('fecha1');
		$grid->label('Fecha1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('fechav');
		$grid->label('Fechav');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('fechai');
		$grid->label('Fechai');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('dvolum1');
		$grid->label('Dvolum1');
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


		$grid->addField('dvolum2');
		$grid->label('Dvolum2');
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


		$grid->addField('fechau');
		$grid->label('Fechau');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('observa');
		$grid->label('Observa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('conjunto');
		$grid->label('Conjunto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('dollar');
		$grid->label('Dollar');
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


		$grid->addField('fcalc');
		$grid->label('Fcalc');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('redondeo');
		$grid->label('Redondeo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('pprove');
		$grid->label('Pprove');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('marca');
		$grid->label('Marca');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:22, maxlength: 22 }',
		));


		$grid->addField('ordena');
		$grid->label('Ordena');
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


		$grid->addField('implic');
		$grid->label('Implic');
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


		$grid->addField('ulicor');
		$grid->label('Ulicor');
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


		$grid->addField('piso');
		$grid->label('Piso');
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


		$grid->addField('promo');
		$grid->label('Promo');
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


		$grid->addField('almac');
		$grid->label('Almac');
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


		$grid->addField('serial');
		$grid->label('Serial');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('ensambla');
		$grid->label('Ensambla');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('garantia');
		$grid->label('Garantia');
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


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('MAES','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('MAES','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('MAES','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('MAES','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: maesadd, editfunc: maesedit, delfunc: maesdel, viewfunc: maesshow");

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
		$mWHERE = $grid->geneTopWhere('maes');

		$response   = $grid->getData('maes', array(array()), array(), false, $mWHERE, 'codigo' );
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
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM maes WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('maes', $data);
					echo "Registro Agregado";

					logusu('MAES',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM maes WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM maes WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE maes SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("maes", $data);
				logusu('MAES',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('maes', $data);
				logusu('MAES',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM maes WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM maes WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM maes WHERE id=$id ");
				logusu('MAES',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$link  =site_url('inventario/common/add_marc');
		$link4 =site_url('inventario/common/get_marca');
		$link5 =site_url('inventario/common/add_unidad');
		$link6 =site_url('inventario/common/get_unidad');
		$link7 =site_url('inventario/maes/ultimo');
		$link8 =site_url('inventario/maes/sugerir');
		$link9 =site_url('inventario/common/add_depto');
		$link10=site_url('inventario/common/get_depto');
		$link11=site_url('inventario/common/add_familia');
		$link12=site_url('inventario/common/get_familia');
		$link13=site_url('inventario/common/add_grupo');
		$link14=site_url('inventario/common/get_grupo_m');

		$script='
		function dpto_change(){
			$.post("'.$link12.'",
					{ depto:$("#depto").val() },
					function(data){
						$("#familia").html(data);
					}
			)
			$.post("'.$link14.'",{ fami:"", depto:"" },
				function(data){
					$("#grupo").html(data);
				}
			)
		}
		';

		$script .= '
		$(function(){
			$("#depto").change(
				function(){
					dpto_change();
				}
			);

			$("#familia").change(function(){
				$.post("'.$link14.'",
					{ fami:$(this).val(), depto: $("#depto").val() },
					function(data){
						$("#grupo").html(data);
					}
				)
			});

			$("#tdecimal").change(function(){
				var clase;
				if($(this).attr("value")=="S") clase="inputnum"; else clase="inputonlynum";
				$("#exmin").unbind();$("#exmin").removeClass(); $("#exmin").addClass(clase);
				$("#exmax").unbind();$("#exmax").removeClass(); $("#exmax").addClass(clase);
				$("#exord").unbind();$("#exord").removeClass(); $("#exord").addClass(clase);
				$("#exdes").unbind();$("#exdes").removeClass(); $("#exdes").addClass(clase);

				$(".inputnum").numeric(".");
				$(".inputonlynum").numeric("0");
			});

			requeridos(true);
		});
		';

		$script .= '
		function ultimo(){
			$.ajax({
				url: "'.$link7.'",
				success: function(msg){
				  alert( "El &uacute;ltimo c&oacute;digo ingresado fue: " + msg );
				}
			});
		}
		';

		$script .= '
		function sugerir(){
			$.ajax({
				url: "'.$link8.'",
				success: function(msg){
					if(msg){
						$("#codigo").val(msg);
					}
					else{
						alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
					}
				}
			});
		}
		';

/*
		$script .= '
		function get_familias(){
			$.post("'.site_url('supermercado/maes/maesfamilias').'",
				{ depto:$(this).val() },
				function(data){
					$("#familia").html(data);
				}
			)

			$.post("'.site_url('supermercado/maes/maesgrupos').'",
				{ familia: "" },
				function(data){
					$("#grupo").html(data);
				}
			)

			//var url = "'.site_url('supermercado/maes/maesfamilias').'";
			//var pars = "dpto="+$F("depto");
			//var myAjax = new Ajax.Updater("td_familia", url, { method: "post", parameters: pars });
			//var url = "'.site_url('supermercado/maes/maesgrupos').'";
			//var gmyAjax = new Ajax.Updater("td_grupo", url);
		}
		';

		$script .= '
		function get_grupo(){
			$.post("'.site_url('supermercado/maes/maesgrupos').'",
				{ familia:$(this).val() },
					function(data){
						$("#grupo").html(data);
				}
			)

			//var url = "'.site_url('supermercado/maes/maesgrupos').'";
			//var pars = "dpto="+$F("depto")+"&fami="+$F("familia");
			//var myAjax = new Ajax.Updater("td_grupo", url, { method: "post", parameters: pars });
		}

		';


*/
		$edit = new DataEdit('', 'maes');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->script($script,'create');

		$edit->script($script,'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$("#maintabcontainer").tabs();
		});		';
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->rule='';
		$edit->codigo->size =17;
		$edit->codigo->maxlength =15;

		$edit->referen = new inputField('Codigo Alterno','referen');
		$edit->referen->rule='';
		$edit->referen->size =17;
		$edit->referen->maxlength =15;

		$edit->barras = new inputField('Barras','barras');
		$edit->barras->rule='';
		$edit->barras->size =17;
		$edit->barras->maxlength =15;

		$edit->depto = new dropdownField("Departamento", "depto");
		$edit->depto->option("","");
		$edit->depto->options("SELECT depto, CONCAT(descrip,' (',depto,')') descrip FROM dpto WHERE tipo='I' ORDER BY descrip");
		//$edit->depto->onchange = "get_familias();";
		$edit->depto->group = "Datos";
		$edit->depto->style='width:200px;';


		$edit->familia = new dropdownField("Familia","familia");
		$edit->familia->rule ="required";
		//$edit->familia->append($AddLinea);
		//$edit->familia->onchange = "get_grupo();";

		$depto = $edit->getval('depto');
		$edit->familia->style='width:200px;';

		if($depto!==FALSE){
			$edit->familia->option("","");
			$edit->familia->options("SELECT familia, CONCAT( descrip,' (',familia,')') descrip FROM fami WHERE depto='$depto' ORDER BY descrip");
		}else{
			$edit->familia->option("","Seleccione un Departamento primero");
		}

		$edit->grupo = new dropdownField("Grupo", "grupo");
		$edit->grupo->rule="required";
		$familia = $edit->getval('familia');
		//$edit->grupo->options("SELECT grupo, nom_grup FROM grup WHERE familia='$familia' ORDER BY nom_grup");
		$edit->grupo->style='width:200px;';

		if($familia!==FALSE){
			$edit->grupo->option("","");
			$edit->grupo->options("SELECT grupo, CONCAT(nom_grup,' (',grupo,')') descrip FROM grup WHERE familia='$familia' ORDER BY nom_grup");
		}else{
			$edit->grupo->option("","Seleccione un Departamento primero");
		}


		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->style='width:120px;';
		$edit->tipo->option("Articulo","Art&iacute;culo" );
		$edit->tipo->option("Servicio","Servicio");
		$edit->tipo->option("Descartar","Descartar");
		$edit->tipo->option("Consumo","Consumo");
		$edit->tipo->option("Fraccion","Fracci&oacute;n");
		$edit->tipo->option("Lote","Lote");

		$edit->unidad = new inputField('Unidad','unidad');
		$edit->unidad->rule='';
		$edit->unidad->size =10;
		$edit->unidad->maxlength =8;

		$edit->presenta = new inputField('Presenta','presenta');
		$edit->presenta->rule='';
		$edit->presenta->size =10;
		$edit->presenta->maxlength =8;

		$edit->mempaq = new inputField('Mempaq','mempaq');
		$edit->mempaq->rule='';
		$edit->mempaq->size =10;
		$edit->mempaq->maxlength =8;

		$edit->dempaq = new inputField('Dempaq','dempaq');
		$edit->dempaq->rule='';
		$edit->dempaq->size =10;
		$edit->dempaq->maxlength =8;

		$edit->empaque = new inputField('Empaque','empaque');
		$edit->empaque->rule='';
		$edit->empaque->size =29;
		$edit->empaque->maxlength =27;

		$edit->pais = new inputField('Pais','pais');
		$edit->pais->rule='';
		$edit->pais->size =18;
		$edit->pais->maxlength =16;

		$edit->fracxuni = new inputField('Fracxuni','fracxuni');
		$edit->fracxuni->rule='integer';
		$edit->fracxuni->css_class='inputonlynum';
		$edit->fracxuni->size =13;
		$edit->fracxuni->maxlength =11;

		$edit->alcohol = new inputField('Alcohol','alcohol');
		$edit->alcohol->rule='integer';
		$edit->alcohol->css_class='inputonlynum';
		$edit->alcohol->size =13;
		$edit->alcohol->maxlength =11;

		$edit->tamano = new inputField('Tamano','tamano');
		$edit->tamano->rule='integer';
		$edit->tamano->css_class='inputonlynum';
		$edit->tamano->size =13;
		$edit->tamano->maxlength =11;

		$edit->medida = new inputField('Medida','medida');
		$edit->medida->rule='';
		$edit->medida->size =6;
		$edit->medida->maxlength =4;

		$ivas=$this->datasis->ivaplica();
		$edit->iva = new dropdownField('IVA %', 'iva');
		foreach($ivas as $tasa=>$ivamonto){
			$edit->iva->option($ivamonto,nformat($ivamonto));
		}
		$edit->iva->style='width:100px;';
		$edit->iva->insertValue = $ivas['tasa'];
		$edit->iva->onchange='calculos(\'S\');';


		$edit->descrip = new inputField('Descrip','descrip');
		$edit->descrip->rule='';
		$edit->descrip->size =42;
		$edit->descrip->maxlength =40;

		$edit->corta = new inputField('Corta','corta');
		$edit->corta->rule='';
		$edit->corta->size =22;
		$edit->corta->maxlength =20;

		$edit->cu_inve = new inputField('Codigo de Caja','cu_inve');
		$edit->cu_inve->rule='';
		$edit->cu_inve->size =17;
		$edit->cu_inve->maxlength =15;

		$edit->moneda = new inputField('Moneda','moneda');
		$edit->moneda->rule='';
		$edit->moneda->size =4;
		$edit->moneda->maxlength =2;

		$edit->costo = new inputField('Costo','costo');
		$edit->costo->rule='numeric';
		$edit->costo->css_class='inputnum';
		$edit->costo->size =15;
		$edit->costo->maxlength =17;

		$edit->existen = new inputField('Existen','existen');
		$edit->existen->rule='integer';
		$edit->existen->css_class='inputonlynum';
		$edit->existen->size =10;
		$edit->existen->maxlength =11;

		$edit->fracci = new inputField('Fracci','fracci');
		$edit->fracci->rule='integer';
		$edit->fracci->css_class='inputonlynum';
		$edit->fracci->size =10;
		$edit->fracci->maxlength =11;

		$edit->maximo = new inputField('Maximo','maximo');
		$edit->maximo->rule='integer';
		$edit->maximo->css_class='inputonlynum';
		$edit->maximo->size =10;
		$edit->maximo->maxlength =11;

		$edit->minimo = new inputField('Minimo','minimo');
		$edit->minimo->rule='integer';
		$edit->minimo->css_class='inputonlynum';
		$edit->minimo->size =10;
		$edit->minimo->maxlength =11;

		$edit->susti = new inputField('Clave','susti');
		$edit->susti->rule='';
		$edit->susti->size =12;
		$edit->susti->maxlength =10;

		$edit->modifi = new inputField('Modifi','modifi');
		$edit->modifi->rule='';
		$edit->modifi->size =3;
		$edit->modifi->maxlength =1;

		$edit->volumen = new inputField('Volumen','volumen');
		$edit->volumen->rule='numeric';
		$edit->volumen->css_class='inputnum';
		$edit->volumen->size =12;
		$edit->volumen->maxlength =12;

		$edit->peso = new inputField('Peso','peso');
		$edit->peso->rule='numeric';
		$edit->peso->css_class='inputnum';
		$edit->peso->size =14;
		$edit->peso->maxlength =12;

		$edit->cprv1 = new inputField('Cprv1','cprv1');
		$edit->cprv1->rule='';
		$edit->cprv1->size =7;
		$edit->cprv1->maxlength =5;

		$edit->nprv1 = new inputField('Nprv1','nprv1');
		$edit->nprv1->rule='';
		$edit->nprv1->size =32;
		$edit->nprv1->maxlength =30;

		$edit->pprv1 = new inputField('Pprv1','pprv1');
		$edit->pprv1->rule='numeric';
		$edit->pprv1->css_class='inputnum';
		$edit->pprv1->size =19;
		$edit->pprv1->maxlength =17;

		$edit->ultimo = new inputField('Ultimo','ultimo');
		$edit->ultimo->rule='numeric';
		$edit->ultimo->css_class='inputnum';
		$edit->ultimo->size =15;
		$edit->ultimo->maxlength =17;

		$edit->uprv1 = new inputField('Uprv1','uprv1');
		$edit->uprv1->rule='numeric';
		$edit->uprv1->css_class='inputnum';
		$edit->uprv1->size =19;
		$edit->uprv1->maxlength =17;

		$edit->fprv1 = new dateonlyField('Fprv1','fprv1');
		$edit->fprv1->rule='chfecha';
		$edit->fprv1->size =10;
		$edit->fprv1->maxlength =8;

		$edit->cprv2 = new inputField('Cprv2','cprv2');
		$edit->cprv2->rule='';
		$edit->cprv2->size =7;
		$edit->cprv2->maxlength =5;

		$edit->nprv2 = new inputField('Nprv2','nprv2');
		$edit->nprv2->rule='';
		$edit->nprv2->size =32;
		$edit->nprv2->maxlength =30;

		$edit->pprv2 = new inputField('Pprv2','pprv2');
		$edit->pprv2->rule='numeric';
		$edit->pprv2->css_class='inputnum';
		$edit->pprv2->size =19;
		$edit->pprv2->maxlength =17;

		$edit->uprv2 = new inputField('Uprv2','uprv2');
		$edit->uprv2->rule='numeric';
		$edit->uprv2->css_class='inputnum';
		$edit->uprv2->size =19;
		$edit->uprv2->maxlength =17;

		$edit->fprv2 = new dateonlyField('Fprv2','fprv2');
		$edit->fprv2->rule='chfecha';
		$edit->fprv2->size =10;
		$edit->fprv2->maxlength =8;

		$edit->cprv3 = new inputField('Cprv3','cprv3');
		$edit->cprv3->rule='';
		$edit->cprv3->size =7;
		$edit->cprv3->maxlength =5;

		$edit->nprv3 = new inputField('Nprv3','nprv3');
		$edit->nprv3->rule='';
		$edit->nprv3->size =32;
		$edit->nprv3->maxlength =30;

		$edit->pprv3 = new inputField('Pprv3','pprv3');
		$edit->pprv3->rule='numeric';
		$edit->pprv3->css_class='inputnum';
		$edit->pprv3->size =19;
		$edit->pprv3->maxlength =17;

		$edit->uprv3 = new inputField('Uprv3','uprv3');
		$edit->uprv3->rule='numeric';
		$edit->uprv3->css_class='inputnum';
		$edit->uprv3->size =19;
		$edit->uprv3->maxlength =17;

		$edit->fprv3 = new dateonlyField('Fprv3','fprv3');
		$edit->fprv3->rule='chfecha';
		$edit->fprv3->size =10;
		$edit->fprv3->maxlength =8;

		$edit->cprv4 = new inputField('Cprv4','cprv4');
		$edit->cprv4->rule='';
		$edit->cprv4->size =7;
		$edit->cprv4->maxlength =5;

		$edit->nprv4 = new inputField('Nprv4','nprv4');
		$edit->nprv4->rule='';
		$edit->nprv4->size =32;
		$edit->nprv4->maxlength =30;

		$edit->pprv4 = new inputField('Pprv4','pprv4');
		$edit->pprv4->rule='numeric';
		$edit->pprv4->css_class='inputnum';
		$edit->pprv4->size =19;
		$edit->pprv4->maxlength =17;

		$edit->uprv4 = new inputField('Uprv4','uprv4');
		$edit->uprv4->rule='numeric';
		$edit->uprv4->css_class='inputnum';
		$edit->uprv4->size =19;
		$edit->uprv4->maxlength =17;

		$edit->fprv4 = new dateonlyField('Fprv4','fprv4');
		$edit->fprv4->rule='chfecha';
		$edit->fprv4->size =10;
		$edit->fprv4->maxlength =8;

		$edit->cprv5 = new inputField('Cprv5','cprv5');
		$edit->cprv5->rule='';
		$edit->cprv5->size =8;
		$edit->cprv5->maxlength =6;

		$edit->nprv5 = new inputField('Nprv5','nprv5');
		$edit->nprv5->rule='';
		$edit->nprv5->size =32;
		$edit->nprv5->maxlength =30;

		$edit->pprv5 = new inputField('Pprv5','pprv5');
		$edit->pprv5->rule='numeric';
		$edit->pprv5->css_class='inputnum';
		$edit->pprv5->size =19;
		$edit->pprv5->maxlength =17;

		$edit->uprv5 = new inputField('Uprv5','uprv5');
		$edit->uprv5->rule='numeric';
		$edit->uprv5->css_class='inputnum';
		$edit->uprv5->size =19;
		$edit->uprv5->maxlength =17;

		$edit->fprv5 = new dateonlyField('Fprv5','fprv5');
		$edit->fprv5->rule='chfecha';
		$edit->fprv5->size =10;
		$edit->fprv5->maxlength =8;

		$edit->margen1 = new inputField('Margen1','margen1');
		$edit->margen1->rule='numeric';
		$edit->margen1->css_class='inputnum';
		$edit->margen1->size =9;
		$edit->margen1->maxlength =7;

		$edit->base1 = new inputField('Base1','base1');
		$edit->base1->rule='numeric';
		$edit->base1->css_class='inputnum';
		$edit->base1->size =19;
		$edit->base1->maxlength =17;

		$edit->precio1 = new inputField('Precio1','precio1');
		$edit->precio1->rule='numeric';
		$edit->precio1->css_class='inputnum';
		$edit->precio1->size =19;
		$edit->precio1->maxlength =17;

		$edit->margen2 = new inputField('Margen2','margen2');
		$edit->margen2->rule='numeric';
		$edit->margen2->css_class='inputnum';
		$edit->margen2->size =9;
		$edit->margen2->maxlength =7;

		$edit->base2 = new inputField('Base2','base2');
		$edit->base2->rule='numeric';
		$edit->base2->css_class='inputnum';
		$edit->base2->size =19;
		$edit->base2->maxlength =17;

		$edit->precio2 = new inputField('Precio2','precio2');
		$edit->precio2->rule='numeric';
		$edit->precio2->css_class='inputnum';
		$edit->precio2->size =19;
		$edit->precio2->maxlength =17;

		$edit->margen3 = new inputField('Margen3','margen3');
		$edit->margen3->rule='numeric';
		$edit->margen3->css_class='inputnum';
		$edit->margen3->size =9;
		$edit->margen3->maxlength =7;

		$edit->base3 = new inputField('Base3','base3');
		$edit->base3->rule='numeric';
		$edit->base3->css_class='inputnum';
		$edit->base3->size =19;
		$edit->base3->maxlength =17;

		$edit->precio3 = new inputField('Precio3','precio3');
		$edit->precio3->rule='numeric';
		$edit->precio3->css_class='inputnum';
		$edit->precio3->size =19;
		$edit->precio3->maxlength =17;

		$edit->margen4 = new inputField('Margen4','margen4');
		$edit->margen4->rule='numeric';
		$edit->margen4->css_class='inputnum';
		$edit->margen4->size =9;
		$edit->margen4->maxlength =7;

		$edit->base4 = new inputField('Base4','base4');
		$edit->base4->rule='numeric';
		$edit->base4->css_class='inputnum';
		$edit->base4->size =19;
		$edit->base4->maxlength =17;

		$edit->precio4 = new inputField('Precio4','precio4');
		$edit->precio4->rule='numeric';
		$edit->precio4->css_class='inputnum';
		$edit->precio4->size =19;
		$edit->precio4->maxlength =17;

		$edit->margen5 = new inputField('Margen5','margen5');
		$edit->margen5->rule='numeric';
		$edit->margen5->css_class='inputnum';
		$edit->margen5->size =9;
		$edit->margen5->maxlength =7;

		$edit->base5 = new inputField('Base5','base5');
		$edit->base5->rule='numeric';
		$edit->base5->css_class='inputnum';
		$edit->base5->size =19;
		$edit->base5->maxlength =17;

		$edit->precio5 = new inputField('Precio5','precio5');
		$edit->precio5->rule='numeric';
		$edit->precio5->css_class='inputnum';
		$edit->precio5->size =19;
		$edit->precio5->maxlength =17;

		$edit->can_ven = new inputField('Can_ven','can_ven');
		$edit->can_ven->rule='integer';
		$edit->can_ven->css_class='inputonlynum';
		$edit->can_ven->size =13;
		$edit->can_ven->maxlength =11;

		$edit->margm2 = new inputField('Margm2','margm2');
		$edit->margm2->rule='';
		$edit->margm2->size =10;
		$edit->margm2->maxlength =8;

		$edit->basem2 = new inputField('Basem2','basem2');
		$edit->basem2->rule='';
		$edit->basem2->size =10;
		$edit->basem2->maxlength =8;

		$edit->precm2 = new inputField('Precm2','precm2');
		$edit->precm2->rule='';
		$edit->precm2->size =10;
		$edit->precm2->maxlength =8;

		$edit->margm3 = new inputField('Margm3','margm3');
		$edit->margm3->rule='';
		$edit->margm3->size =10;
		$edit->margm3->maxlength =8;

		$edit->basem3 = new inputField('Basem3','basem3');
		$edit->basem3->rule='';
		$edit->basem3->size =10;
		$edit->basem3->maxlength =8;

		$edit->precm3 = new inputField('Precm3','precm3');
		$edit->precm3->rule='';
		$edit->precm3->size =10;
		$edit->precm3->maxlength =8;

		$edit->oferta = new inputField('Oferta','oferta');
		$edit->oferta->rule='numeric';
		$edit->oferta->css_class='inputnum';
		$edit->oferta->size =17;
		$edit->oferta->maxlength =15;

		$edit->fdesde = new dateonlyField('Fdesde','fdesde');
		$edit->fdesde->rule='chfecha';
		$edit->fdesde->size =10;
		$edit->fdesde->maxlength =8;

		$edit->hdesde = new inputField('Hdesde','hdesde');
		$edit->hdesde->rule='';
		$edit->hdesde->size =7;
		$edit->hdesde->maxlength =5;

		$edit->fhasta = new dateonlyField('Fhasta','fhasta');
		$edit->fhasta->rule='chfecha';
		$edit->fhasta->size =10;
		$edit->fhasta->maxlength =8;

		$edit->hhasta = new inputField('Hhasta','hhasta');
		$edit->hhasta->rule='';
		$edit->hhasta->size =7;
		$edit->hhasta->maxlength =5;

		$edit->lleve = new inputField('Lleve','lleve');
		$edit->lleve->rule='integer';
		$edit->lleve->css_class='inputonlynum';
		$edit->lleve->size =13;
		$edit->lleve->maxlength =11;

		$edit->pague = new inputField('Pague','pague');
		$edit->pague->rule='integer';
		$edit->pague->css_class='inputonlynum';
		$edit->pague->size =13;
		$edit->pague->maxlength =11;

		$edit->fecha1 = new dateonlyField('Fecha1','fecha1');
		$edit->fecha1->rule='chfecha';
		$edit->fecha1->size =10;
		$edit->fecha1->maxlength =8;

		$edit->fechav = new dateonlyField('Fechav','fechav');
		$edit->fechav->rule='chfecha';
		$edit->fechav->size =10;
		$edit->fechav->maxlength =8;

		$edit->fechai = new dateonlyField('Fechai','fechai');
		$edit->fechai->rule='chfecha';
		$edit->fechai->size =10;
		$edit->fechai->maxlength =8;

		$edit->dvolum1 = new inputField('Dvolum1','dvolum1');
		$edit->dvolum1->rule='integer';
		$edit->dvolum1->css_class='inputonlynum';
		$edit->dvolum1->size =13;
		$edit->dvolum1->maxlength =11;

		$edit->dvolum2 = new inputField('Dvolum2','dvolum2');
		$edit->dvolum2->rule='integer';
		$edit->dvolum2->css_class='inputonlynum';
		$edit->dvolum2->size =13;
		$edit->dvolum2->maxlength =11;

		$edit->fechau = new dateonlyField('Fechau','fechau');
		$edit->fechau->rule='chfecha';
		$edit->fechau->size =10;
		$edit->fechau->maxlength =8;

		$edit->observa = new textareaField('Observa','observa');
		$edit->observa->rule='';
		$edit->observa->cols = 70;
		$edit->observa->rows = 4;

		$edit->conjunto = new inputField('Conjunto','conjunto');
		$edit->conjunto->rule='';
		$edit->conjunto->size =7;
		$edit->conjunto->maxlength =5;

		$edit->dollar = new inputField('Dollar','dollar');
		$edit->dollar->rule='numeric';
		$edit->dollar->css_class='inputnum';
		$edit->dollar->size =12;
		$edit->dollar->maxlength =10;

		$edit->fcalc = new inputField('Fcalc','fcalc');
		$edit->fcalc->rule='';
		$edit->fcalc->size =3;
		$edit->fcalc->maxlength =1;

		$edit->redondeo = new dropdownField("Redondear", "redondeo");
		$edit->redondeo->style='width:150px;';
		$edit->redondeo->option("NO","No");
		$edit->redondeo->option("P0","Precio Decimales");
		$edit->redondeo->option("P1","Precio Unidades" );
		$edit->redondeo->option("P2","Precio Decenas"  );
		$edit->redondeo->option("B0","Base Decimales"  );
		$edit->redondeo->option("B1","Base Unidades"   );
		$edit->redondeo->option("B2","Base Decenas"    );
		$edit->redondeo->onchange = "redonde('M');";
		$edit->redondeo->group = "Costos";

		$edit->pprove = new inputField('Pprove','pprove');
		$edit->pprove->rule='';
		$edit->pprove->size =7;
		$edit->pprove->maxlength =5;

		//$AddMarca='<a href="javascript:add_marca();" title="Haz clic para Agregar una marca nueva">Agregar Marca</a>';
		$edit->marca = new dropdownField("Marca", "marca");
		$edit->marca->style='width:180px;';
		$edit->marca->option("","");
		$edit->marca->options("SELECT marca as codigo, marca FROM marc ORDER BY marca");
		//$edit->marca->append($AddMarca);

		$edit->ordena = new inputField('Ordena','ordena');
		$edit->ordena->rule='integer';
		$edit->ordena->css_class='inputonlynum';
		$edit->ordena->size =13;
		$edit->ordena->maxlength =11;

		$edit->implic = new inputField('Implic','implic');
		$edit->implic->rule='numeric';
		$edit->implic->css_class='inputnum';
		$edit->implic->size =8;
		$edit->implic->maxlength =6;

		$edit->ulicor = new inputField('Ulicor','ulicor');
		$edit->ulicor->rule='numeric';
		$edit->ulicor->css_class='inputnum';
		$edit->ulicor->size =17;
		$edit->ulicor->maxlength =15;

		$edit->piso = new inputField('Piso','piso');
		$edit->piso->rule='integer';
		$edit->piso->css_class='inputonlynum';
		$edit->piso->size =13;
		$edit->piso->maxlength =11;

		$edit->promo = new inputField('Promo','promo');
		$edit->promo->rule='integer';
		$edit->promo->css_class='inputonlynum';
		$edit->promo->size =13;
		$edit->promo->maxlength =11;

		$edit->almac = new inputField('Almac','almac');
		$edit->almac->rule='integer';
		$edit->almac->css_class='inputonlynum';
		$edit->almac->size =13;
		$edit->almac->maxlength =11;

		$edit->serial = new inputField('Serial','serial');
		$edit->serial->rule='';
		$edit->serial->size =3;
		$edit->serial->maxlength =1;

		$edit->ensambla = new dropdownField("Ensamblado", "ensambla");
		$edit->ensambla->style='width:60px;';
		$edit->ensambla->option("N","No" );
		$edit->ensambla->option("S","Si" );
		$edit->ensambla->group = "Datos";

		$edit->garantia = new inputField('Garantia','garantia');
		$edit->garantia->rule='integer';
		$edit->garantia->css_class='inputonlynum';
		$edit->garantia->size =13;
		$edit->garantia->maxlength =11;

		$codigo=$edit->_dataobject->get("codigo");
		$edit->almacenes = new containerField('almacenes',$this->_detalle($codigo));
		$edit->almacenes->when = array("show","modify");


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
			$this->load->view('view_maes', $conten);
		}
	}

	function _pre_insert($do){
		$do->error_message_ar['pre_ins']='';
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
		logusu($do->table,"Creo $this->tits $primary ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		if (!$this->db->table_exists('maes')) {
			$mSQL="CREATE TABLE `maes` (
			  `codigo` varchar(15) NOT NULL DEFAULT '',
			  `referen` varchar(15) DEFAULT NULL,
			  `barras` varchar(15) DEFAULT NULL,
			  `depto` varchar(4) DEFAULT NULL,
			  `familia` varchar(4) DEFAULT NULL,
			  `grupo` varchar(4) DEFAULT NULL,
			  `nom_grup` varchar(20) DEFAULT NULL,
			  `tipo` char(1) DEFAULT NULL,
			  `unidad` varchar(8) DEFAULT NULL,
			  `presenta` varchar(8) DEFAULT NULL,
			  `mempaq` varchar(8) DEFAULT NULL,
			  `dempaq` varchar(8) DEFAULT NULL,
			  `empaque` varchar(27) DEFAULT NULL,
			  `pais` varchar(16) DEFAULT NULL,
			  `fracxuni` int(11) DEFAULT NULL,
			  `alcohol` int(11) DEFAULT NULL,
			  `tamano` int(11) DEFAULT '0',
			  `medida` varchar(4) DEFAULT NULL,
			  `iva` decimal(8,2) NOT NULL DEFAULT '0.00',
			  `descrip` varchar(40) DEFAULT NULL,
			  `corta` varchar(20) DEFAULT NULL,
			  `cu_inve` varchar(15) DEFAULT NULL,
			  `moneda` char(2) DEFAULT NULL,
			  `costo` decimal(17,2) NOT NULL DEFAULT '0.00',
			  `costotal` decimal(17,2) NOT NULL DEFAULT '0.00',
			  `existen` int(11) DEFAULT '0',
			  `fracci` int(11) DEFAULT '0',
			  `maximo` int(11) DEFAULT '0',
			  `minimo` int(11) DEFAULT '0',
			  `susti` varchar(10) DEFAULT NULL,
			  `modifi` char(1) DEFAULT NULL,
			  `volumen` decimal(12,2) NOT NULL DEFAULT '0.00',
			  `peso` decimal(12,2) NOT NULL DEFAULT '0.00',
			  `cprv1` varchar(5) DEFAULT NULL,
			  `nprv1` varchar(30) DEFAULT NULL,
			  `pprv1` decimal(17,2) DEFAULT '0.00',
			  `ultimo` decimal(17,2) NOT NULL DEFAULT '0.00',
			  `uprv1` decimal(17,2) DEFAULT '0.00',
			  `fprv1` date DEFAULT NULL,
			  `cprv2` varchar(5) DEFAULT NULL,
			  `nprv2` varchar(30) DEFAULT NULL,
			  `pprv2` decimal(17,2) DEFAULT '0.00',
			  `uprv2` decimal(17,2) DEFAULT '0.00',
			  `fprv2` date DEFAULT NULL,
			  `cprv3` varchar(5) DEFAULT NULL,
			  `nprv3` varchar(30) DEFAULT NULL,
			  `pprv3` decimal(17,2) DEFAULT '0.00',
			  `uprv3` decimal(17,2) DEFAULT '0.00',
			  `fprv3` date DEFAULT NULL,
			  `cprv4` varchar(5) DEFAULT NULL,
			  `nprv4` varchar(30) DEFAULT NULL,
			  `pprv4` decimal(17,2) DEFAULT '0.00',
			  `uprv4` decimal(17,2) DEFAULT '0.00',
			  `fprv4` date DEFAULT NULL,
			  `cprv5` varchar(6) DEFAULT NULL,
			  `nprv5` varchar(30) DEFAULT NULL,
			  `pprv5` decimal(17,2) DEFAULT '0.00',
			  `uprv5` decimal(17,2) DEFAULT '0.00',
			  `fprv5` date DEFAULT NULL,
			  `margen1` decimal(7,2) DEFAULT '0.00',
			  `base1` decimal(17,2) DEFAULT '0.00',
			  `precio1` decimal(17,2) DEFAULT '0.00',
			  `margen2` decimal(7,2) DEFAULT '0.00',
			  `base2` decimal(17,2) DEFAULT '0.00',
			  `precio2` decimal(17,2) DEFAULT '0.00',
			  `margen3` decimal(7,2) DEFAULT '0.00',
			  `base3` decimal(17,2) DEFAULT '0.00',
			  `precio3` decimal(17,2) DEFAULT '0.00',
			  `margen4` decimal(7,2) DEFAULT '0.00',
			  `base4` decimal(17,2) DEFAULT '0.00',
			  `precio4` decimal(17,2) DEFAULT '0.00',
			  `margen5` decimal(7,2) DEFAULT '0.00',
			  `base5` decimal(17,2) DEFAULT '0.00',
			  `precio5` decimal(17,2) DEFAULT '0.00',
			  `can_ven` int(11) DEFAULT NULL,
			  `margm2` double DEFAULT NULL,
			  `basem2` double DEFAULT NULL,
			  `precm2` double DEFAULT NULL,
			  `margm3` double DEFAULT NULL,
			  `basem3` double DEFAULT NULL,
			  `precm3` double DEFAULT NULL,
			  `oferta` decimal(15,2) NOT NULL DEFAULT '0.00',
			  `fdesde` date DEFAULT NULL,
			  `hdesde` varchar(5) DEFAULT NULL,
			  `fhasta` date DEFAULT NULL,
			  `hhasta` varchar(5) DEFAULT NULL,
			  `lleve` int(11) DEFAULT NULL,
			  `pague` int(11) DEFAULT NULL,
			  `fecha1` date DEFAULT NULL,
			  `fechav` date DEFAULT NULL,
			  `fechai` date DEFAULT NULL,
			  `dvolum1` int(11) DEFAULT NULL,
			  `dvolum2` int(11) DEFAULT NULL,
			  `fechau` date DEFAULT NULL,
			  `observa` mediumtext,
			  `conjunto` varchar(5) DEFAULT NULL,
			  `dollar` decimal(10,2) DEFAULT '0.00',
			  `fcalc` char(1) DEFAULT 'U',
			  `redondeo` char(2) DEFAULT 'P0',
			  `pprove` varchar(5) DEFAULT NULL,
			  `marca` varchar(22) DEFAULT NULL,
			  `ordena` int(11) unsigned NOT NULL DEFAULT '0',
			  `implic` decimal(6,2) NOT NULL DEFAULT '0.00',
			  `ulicor` decimal(15,2) NOT NULL DEFAULT '0.00',
			  `piso` int(11) unsigned DEFAULT NULL,
			  `promo` int(11) unsigned DEFAULT '0',
			  `almac` int(11) unsigned DEFAULT '0',
			  `serial` char(1) DEFAULT 'N',
			  `ensambla` char(1) DEFAULT 'N',
			  `garantia` int(11) DEFAULT '0',
			  PRIMARY KEY (`codigo`),
			  KEY `descripcion` (`descrip`),
			  KEY `referencia` (`referen`),
			  KEY `barras` (`barras`),
			  KEY `caja` (`cu_inve`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('maes');
		//if(!in_array('<#campo#>',$campos)){ }
	}



/*

class maes extends Controller {

	function maes(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		//$this->datasis->modulo_id(309,1);
		redirect("supermercado/maes/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load("datafilter2","datagrid");

		rapydlib("prototype");
		$ajax_onchange = '
			  function get_familias(){
			    var url = "'.site_url('supermercado/maes/maesfamilias').'";
			    var pars = "dpto="+$F("depto");
			    var myAjax = new Ajax.Updater("td_familia", url, { method: "post", parameters: pars });

			    var url = "'.site_url('supermercado/maes/maesgrupos').'";
			    var gmyAjax = new Ajax.Updater("td_grupo", url);
			  }

			  function get_grupo(){
			    var url = "'.site_url('supermercado/maes/maesgrupos').'";
			    var pars = "dpto="+$F("depto")+"&fami="+$F("familia");
			    var myAjax = new Ajax.Updater("td_grupo", url, { method: "post", parameters: pars });
			  }';

		$filter = new DataFilter2("Filtro por Producto");
		$select=array("a.descrip as descripcion","a.tipo","a.marca","a.codigo","a.familia","a.grupo","a.depto","b.nom_grup AS nom_grup","c.descrip AS nom_fami","d.descrip AS nom_depto");
		$filter->db->select($select);
		$filter->db->from("maes AS a");
		$filter->db->join("grup AS b","a.grupo=b.grupo");
		$filter->db->join("fami AS c","a.familia=c.familia");
		$filter->db->join("dpto AS d","c.depto=d.depto");
		$filter->db->groupby("a.codigo");
		$filter->script($ajax_onchange);

		$filter->codigo = new inputField("C&oacute;digo", "a.codigo");
		$filter->codigo->size=20;
		$filter->codigo->maxlength=15;

		$filter->tipo = new dropdownField("Tipo", "a.tipo");
		$filter->tipo->option("","" );
		$filter->tipo->option("I","supermercado" );
		$filter->tipo->option("L","Licores"    );
		$filter->tipo->option("P","Por peso"   );
		$filter->tipo->option("K","Desposte"   );
		$filter->tipo->option("C","Combo"      );
		$filter->tipo->option("F","Farmaco"    );
		$filter->tipo->option("S","Servicio"   );
		$filter->tipo->option("R","Receta"     );
		$filter->tipo->option("D","Desactivado");
		$filter->tipo->style='width:110px;';

		$filter->marca = new dropdownField("Marca", "a.marca");
		$filter->marca->option("","");
		$filter->marca->options("SELECT marca as codigo, marca FROM marc ORDER BY marca");
		$filter->marca->style='width:180px;';

		$filter->dpto = new dropdownField("Departamento", "depto");
		$filter->dpto->db_name="a.depto";
		$filter->dpto->option("","");
		$filter->dpto->options("SELECT depto,descrip FROM dpto WHERE tipo='I' ORDER BY descrip");
		$filter->dpto->onchange = "get_familias();";

		$filter->familia = new dropdownField("Familia", "familia");
		$filter->familia->db_name="a.familia";
		$filter->familia->option("","Seleccione un departamento");
		$filter->familia->onchange = "get_grupo();";

		$filter->grupo = new dropdownField("Grupo", "grupo");
		$filter->grupo->db_name="a.familia";
		$filter->grupo->option("","Seleccione una familia");

		$filter->buttons("reset","search");
		$filter->build();

		$grid = new DataGrid("Lista de Art&iacute;culos");
		$grid->order_by("codigo","asc");
		$grid->per_page = 15;

		$link=anchor('/supermercado/maes/dataedit/show/<#codigo#>','<#codigo#>');
		$uri_2 = anchor('supermercado/maes/dataedit/create/<#codigo#>','Duplicar');

		$grid->column("c&oacute;digo",$link);
		$grid->column("Departamento","nom_depto");
		$grid->column("Familia","nom_fami");
		$grid->column("Grupo","nom_grup");
		$grid->column("Descripcion","descripcion");
		$grid->column("Duplicar",$uri_2     ,"align='center'");

		$grid->add("supermercado/maes/dataedit/create");
		$grid->build();

		$data["crud"] = $filter->output . $grid->output;
		$data["titulo"] = 'Lista de Art&iacute;culos';

		$content["content"]   = $this->load->view('rapyd/crud', $data, true);
		$content["rapyd_head"] = $this->rapyd->get_head();
		$content["code"] = '';
		$content["lista"] = "
			<h3>Editar o Agregar</h3>
			<div>Con esta pantalla se puede editar o agregar datos a los Departamentos del M&oacute;dulo de supermercado</div>
			<div class='line'></div>
			<a href='#' onclick='window.close()'>Cerrar</a>
			<div class='line'></div>\n<br><br><br>\n";
		$this->load->view('rapyd/tmpsolo', $content);
	}

	function dataedit1($status='',$id='' ) {
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load('dataedit2','dataobject');

		$link  =site_url('supermercado/common/add_marc');
		$link4 =site_url('supermercado/common/get_marca');
		$link5 =site_url('supermercado/common/add_unidad');
		$link6 =site_url('supermercado/common/get_unidad');
		$link7 =site_url('supermercado/maes/ultimo');
		$link8 =site_url('supermercado/maes/sugerir');
		$link9 =site_url('supermercado/common/add_depto');
		$link10=site_url('supermercado/common/get_depto');
		$link11=site_url('supermercado/common/add_familia');
		$link12=site_url('supermercado/common/get_familia');
		$link13=site_url('supermercado/common/add_grupo');
		$link14=site_url('supermercado/common/get_grupo');

		$script='
		function dpto_change(){
			$.post("'.$link12.'",{ depto:$("#depto").val() },function(data){$("#familia").html(data);})
			$.post("'.$link14.'",{ familia:"" },function(data){$("#grupo").html(data);})
		}
		$(function(){
			$("#depto").change(function(){dpto_change(); });
			$("#familia").change(function(){ $.post("'.$link14.'",{ familia:$(this).val() },function(data){$("#grupo").html(data);}) });

			$("#tdecimal").change(function(){
				var clase;
				if($(this).attr("value")=="S") clase="inputnum"; else clase="inputonlynum";
				$("#exmin").unbind();$("#exmin").removeClass(); $("#exmin").addClass(clase);
				$("#exmax").unbind();$("#exmax").removeClass(); $("#exmax").addClass(clase);
				$("#exord").unbind();$("#exord").removeClass(); $("#exord").addClass(clase);
				$("#exdes").unbind();$("#exdes").removeClass(); $("#exdes").addClass(clase);

				$(".inputnum").numeric(".");
				$(".inputonlynum").numeric("0");
			});

			requeridos(true);
		});

		function ultimo(){
			$.ajax({
				url: "'.$link7.'",
				success: function(msg){
				  alert( "El &uacute;ltimo c&oacute;digo ingresado fue: " + msg );
				}
			});
		}

		function sugerir(){
			$.ajax({
				url: "'.$link8.'",
				success: function(msg){
					if(msg){
						$("#codigo").val(msg);
					}
					else{
						alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
					}
				}
			});
		}

		function add_marca(){
			marca=prompt("Introduza el nombre de la MARCA a agregar");
			if(marca==null){
			}else{
				$.ajax({
				 type: "POST",
				 processData:false,
					url: "'.$link.'",
					data: "valor="+marca,
					success: function(msg){
						if(msg=="s.i"){
							marca=marca.substr(0,30);
							$.post("'.$link4.'",{ x:"" },function(data){$("#marca").html(data);$("#marca").val(marca);})
						}
						else{
							alert("Disculpe. En este momento no se ha podido agregar la marca, por favor intente mas tarde");
						}
					}
				});
			}
		}

		function add_unidad(){
			unidad=prompt("Introduza el nombre de la UNIDAD a agregar");
			if(unidad==null){
			}else{
				$.ajax({
				 type: "POST",
				 processData:false,
					url: "'.$link5.'",
					data: "valor="+unidad,
					success: function(msg){
						if(msg=="s.i"){
							unidad=unidad.substr(0,8);
							$.post("'.$link6.'",{ x:"" },function(data){$("#unidad").html(data);$("#unidad").val(unidad);})
						}
						else{
							alert("Disculpe. En este momento no se ha podido agregar la unidad, por favor intente mas tarde");
						}
					}
				});
			}
		}

		function add_depto(){
			depto=prompt("Introduza el nombre del DEPARTAMENTO a agregar");
			if(depto==null){
			}else{
				$.ajax({
				 type: "POST",
				 processData:false,
					url: "'.$link9.'",
					data: "valor="+depto,
					success: function(msg){
						if(msg=="Y.a-Existe"){
							alert("Ya existe un Departamento con esa Descripcion");
						}
						else{
							if(msg=="N.o-SeAgrego"){
								alert("Disculpe. En este momento no se ha podido agregar el departamento, por favor intente mas tarde");
							}else{
								$.post("'.$link10.'",{ x:"" },function(data){$("#depto").html(data);$("#depto").val(msg);})
							}
						}
					}
				});
			}
		}

		function add_linea(){
			deptoval=$("#depto").val();
			if(deptoval==""){
				alert("Debe seleccionar un Departamento al cual agregar la familia");
			}else{
				familia=prompt("Introduza el nombre de la familia a agregar al DEPARTAMENTO seleccionado");
				if(familia==null){
				}else{
					$.ajax({
					 type: "POST",
					 processData:false,
						url: "'.$link11.'",
						data: "valor="+familia+"&&valor2="+deptoval,
						success: function(msg){
							if(msg=="Y.a-Existe"){
								alert("Ya existe una familia con esa Descripcion");
							}
							else{
								if(msg=="N.o-SeAgrego"){
									alert("Disculpe. En este momento no se ha podido agregar la familia, por favor intente mas tarde");
								}else{
									$.post("'.$link12.'",{ depto:deptoval },function(data){$("#familia").html(data);$("#familia").val(msg);})
								}
							}
						}
					});
				}
			}
		}

		function add_grupo(){
			lineaval=$("#familia").val();
			deptoval=$("#depto").val();
			if(lineaval==""){
				alert("Debe seleccionar una familia a la cual agregar el departamento");
			}else{
				grupo=prompt("Introduza el nombre del GRUPO a agregar a la familia seleccionada");
				if(grupo==null){
				}else{
					$.ajax({
					 type: "POST",
					 processData:false,
						url: "'.$link13.'",
						data: "valor="+grupo+"&&valor2="+lineaval+"&&valor3="+deptoval,
						success: function(msg){
							if(msg=="Y.a-Existe"){
								alert("Ya existe una familia con esa Descripcion");
							}
							else{
								if(msg=="N.o-SeAgrego"){
									alert("Disculpe. En este momento no se ha podido agregar la familia, por favor intente mas tarde");
								}else{
									$.post("'.$link14.'",{ familia:lineaval },function(data){$("#grupo").html(data);$("#grupo").val(msg);})
								}
							}
						}
					});
				}
			}
		}';

		$do = new DataObject("maes");
		if($status=="create" && !empty($id)){
			$do->load($id);
			$do->set('codigo', '');
		}

		$edit = new DataEdit2("Maestro de supermercado", $do);
		$edit->script($script,"create");
		$edit->script($script,"modify");
		$edit->back_url = site_url("supermercado/maes/filteredgrid");

		$ultimo='<a href="javascript:ultimo();" title="Consultar ultimo c&oacute;digo ingresado"> Consultar ultimo c&oacute;digo</a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->size=20;
		$edit->codigo->maxlength=15;
		$edit->codigo->rule = "trim|required|strtoupper|callback_chexiste";
		$edit->codigo->mode="autohide";
		$edit->codigo->append($sugerir);
		$edit->codigo->append($ultimo);
		$edit->codigo->group = "Datos";

		$AddMarca='<a href="javascript:add_marca();" title="Haz clic para Agregar una marca nueva">Agregar Marca</a>';
		$edit->marca = new dropdownField("Marca", "marca");
		$edit->marca->style='width:180px;';
		$edit->marca->option("","");
		$edit->marca->options("SELECT marca as codigo, marca FROM marc ORDER BY marca");
		$edit->marca->append($AddMarca);

		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->style='width:180px;';
		$edit->tipo->option("Articulo","Art&iacute;culo" );
		$edit->tipo->option("Servicio","Servicio");
		$edit->tipo->option("Descartar","Descartar");
		$edit->tipo->option("Consumo","Consumo");
		$edit->tipo->option("Fraccion","Fracci&oacute;n");
		$edit->tipo->option("Lote","Lote");
		$edit->tipo->group = "Datos";

		$AddDepto='<a href="javascript:add_depto();" title="Haz clic para Agregar un nuevo Departamento">Agregar Departamento</a>';
		$edit->depto = new dropdownField("Departamento", "depto");
		$edit->depto->rule ="required";
		//$edit->depto->onchange = "get_linea();";
		$edit->depto->option("","Seleccione un Departamento");
		$edit->depto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");
		$edit->depto->append($AddDepto);
		$edit->depto->group = "Datos";

		$AddLinea='<a href="javascript:add_linea();" title="Haz clic para Agregar una nueva familia;">Agregar familia</a>';
		$edit->familia = new dropdownField("Familia","familia");
		$edit->familia->rule ="required";
		$edit->familia->append($AddLinea);
		$depto=$edit->getval('depto');
		if($depto!==FALSE){
			$edit->familia->options("SELECT familia, descrip FROM fami WHERE depto='$depto' ORDER BY descrip");
		}else{
			$edit->familia->option("","Seleccione un Departamento primero");
		}
		$edit->familia->group = "Datos";

		$AddGrupo='<a href="javascript:add_grupo();" title="Haz clic para Agregar un nuevo Grupo;">Agregar Grupo</a>';
		$edit->grupo = new dropdownField("Grupo", "grupo");
		$edit->grupo->rule="required";
		$edit->grupo->append($AddGrupo);
		$familia=$edit->getval('familia');
		if($familia!==FALSE){
			$edit->grupo->options("SELECT grupo, nom_grup FROM grup WHERE familia='$familia' ORDER BY nom_grup");
		}else{
			$edit->grupo->option("","Seleccione un Departamento primero");
		}
		$edit->grupo->group = "Datos";

		$edit->barras = new inputField("C&oacute;digo Barras", "barras");
		$edit->barras->size=20;
		$edit->barras->maxlength=15;
		$edit->barras->rule = "trim";
		$edit->barras->group = "Datos";

		$edit->referen = new inputField("S.N.M.", "referen");
		$edit->referen->size=17;
		$edit->referen->maxlength=15;
		$edit->referen->group = "Datos";

		$edit->barras = new inputField("Barras", "barras");
		$edit->barras->size=17;
		$edit->barras->maxlength=15;
		$edit->barras->group = "Datos";

		$edit->cu_inve = new inputField("Caja", "cu_inve");
		$edit->cu_inve->size=17;
		$edit->cu_inve->maxlength=15;
		$edit->cu_inve->group = "Datos";

		$edit->ensambla = new dropdownField("Ensamblado", "ensambla");
		$edit->ensambla->style='width:60px;';
		$edit->ensambla->option("N","No" );
		$edit->ensambla->option("S","Si" );
		$edit->ensambla->group = "Datos";

		$edit->empaque = new inputField("Des/Epq", "empaque");
		$edit->empaque->size=30;
		$edit->empaque->maxlength=27;
		$edit->empaque->group = "Datos";

		$edit->descrip = new inputField("Larga","descrip");
		$edit->descrip->size=48;
		$edit->descrip->maxlength=40;
		$edit->descrip->rule = "required";
		$edit->descrip->group = "Descripci&oacute;nes";

		$edit->corta = new inputField("Corta", "corta");
		$edit->corta->size=28;
		$edit->corta->maxlength=20;
		$edit->corta->group = "Descripci&oacute;nes";

		$edit->susti = new inputField("Clave", "susti");
		$edit->susti->size=15;
		$edit->susti->maxlength=10;
		$edit->susti->group = "Descripci&oacute;nes";

		$edit->serial = new dropdownField("Serializar", "serial");
		$edit->serial->style='width:60px;';
		$edit->serial->option("N","No" );
		$edit->serial->option("S","Si" );
		$edit->serial->when =array("show");
		$edit->serial->group = "Existencias";

		$edit->minimo = new inputField("Existencia Minima", "minimo");
		$edit->minimo->size=15;
		$edit->minimo->maxlength=11;
		$edit->minimo->when =array("show");
		$edit->minimo->group = "Existencias";
		$edit->minimo->rule='numeric|callback_positivo|trim';

		$edit->maximo = new inputField("Existencia Maxima", "maximo");
		$edit->maximo->size=15;
		$edit->maximo->maxlength=11;
		$edit->maximo->when =array("show");
		$edit->maximo->group = "Existencias";
		$edit->maximo->rule='numeric|callback_positivo|trim';

		$edit->ordena = new inputField("Existencia Ordenada", "ordena");
		$edit->ordena->size=15;
		$edit->ordena->maxlength=11;
		$edit->ordena->when =array("show");
		$edit->ordena->group = "Existencias";
		$edit->ordena->rule='numeric|callback_positivo|trim';

		$edit->alcohol = new inputField("Licor G/I", "alcohol");
		$edit->alcohol->size=15;
		$edit->alcohol->maxlength=11;
		$edit->alcohol->group = "Licores";

		$edit->implic = new inputField("Impuesto por alcohol", "implic");
		$edit->implic->size=8;
		$edit->implic->maxlength=6;
		$edit->implic->group = "Licores";

		$edit->tamano = new inputField("Tama&ntilde;o", "tamano");
		$edit->tamano->size=15;
		$edit->tamano->maxlength=11;
		$edit->tamano->when =array("show");
		$edit->tamano->group = "Licores";

		$edit->medida = new inputField("Medida", "medida");
		$edit->medida->size=15;
		$edit->medida->maxlength=11;
		$edit->medida->when =array("show");
		$edit->medida->group = "Licores";

		$edit->conjunto = new inputField("Conjunto de Articulo", "conjunto");
		$edit->conjunto->size=8;
		$edit->conjunto->maxlength=8;
		$edit->conjunto->group = "Licores";

		$edit->ultimo = new inputField("Ultimo", "ultimo");
		$edit->ultimo->css_class='inputnum';
		$edit->ultimo->size=21;
		$edit->ultimo->maxlength=17;
		$edit->ultimo->group = "Costos";

		$edit->iva = new inputField("Iva", "iva");
		$edit->iva->css_class='inputnum';
		$edit->iva->onchange = "calculos('M');";
		$edit->iva->size=10;
		$edit->iva->maxlength=8;
		$edit->iva->group = "Costos";

		$edit->costo = new inputField("Promedio", "costo");
		$edit->costo->css_class='inputnum';
		$edit->costo->onchange = "calculos(costo);";
		$edit->costo->size=21;
		$edit->costo->maxlength=17;
		$edit->costo->group = "Costos";

		$edit->fcalc = new dropdownField("Base C&aacute;lculo", "fcalc");
		$edit->fcalc->style='width:150px;';
		$edit->fcalc->option("U","Ultimo" );
		$edit->fcalc->option("P","Promedio" );
		$edit->fcalc->onchange = "calculos('M');";
		$edit->fcalc->group = "Costos";

		$edit->redondeo = new dropdownField("Redondear", "redondeo");
		$edit->redondeo->style='width:150px;';
		$edit->redondeo->option("NO","No");
		$edit->redondeo->option("P0","Precio Decimales");
		$edit->redondeo->option("P1","Precio Unidades" );
		$edit->redondeo->option("P2","Precio Decenas"  );
		$edit->redondeo->option("B0","Base Decimales"  );
		$edit->redondeo->option("B1","Base Unidades"   );
		$edit->redondeo->option("B2","Base Decenas"    );
		$edit->redondeo->onchange = "redonde('M');";
		$edit->redondeo->group = "Costos";

		$edit->fracxuni = new inputField("Presenta", "fracxuni");
		$edit->fracxuni->size=5;
		$edit->fracxuni->maxlength=11;
		$edit->fracxuni->group = "Costos";
		$edit->fracxuni->rule='numeric|callback_positivo|trim';

		$AddUnidad='<a href="javascript:add_unidad();" title="Haz clic para Agregar una unidad nueva">Agregar Unidad</a>';
		$edit->dempaq = new dropdownField("Unidad", "dempaq");
		$edit->dempaq->style='width:110x;';
		$edit->dempaq->options("SELECT presenta label, presenta FROM mpre ORDER BY presenta");
		$edit->dempaq->in="fracxuni";
		$edit->dempaq->append($AddUnidad);

		$edit->mempaq = new dropdownField("Unidad", "mempaq");
		$edit->mempaq->style='width:110x;';
		$edit->mempaq->options("SELECT presenta label, presenta FROM mpre ORDER BY presenta");
		$edit->mempaq->in="fracxuni";
		$edit->mempaq->append($AddUnidad);

		for($i=1;$i<=5;$i++){
			$objeto="margen$i";
			$edit->$objeto = new inputField("Margen $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->maxlength=6;
			$edit->$objeto->onchange = "calculos('I');";
			$edit->$objeto->rule="required";
			$edit->$objeto->group = "Precios";


			$objeto="Ebase$i";
			$edit->$objeto = new freeField("","","Precio $i");
			$edit->$objeto->in="margen$i";

			$objeto="base$i";
			$edit->$objeto = new inputField("Base $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->maxlength=13;
			$edit->$objeto->in="margen$i";
			$edit->$objeto->onchange = "cambiobase('I');";
			$edit->$objeto->rule="required";

			$objeto="Eprecio$i";
			$edit->$objeto = new freeField("","","Precio + I.V.A. $i");
			$edit->$objeto->in="margen$i";

			$objeto="precio$i";
			$edit->$objeto = new inputField("Margen $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->maxlength=6;
			$edit->$objeto->in="margen$i";
			$edit->$objeto->onchange = "cambioprecio('I');";
			$edit->$objeto->rule="required";
		}


		$codigo=$edit->_dataobject->get("codigo");
		$edit->almacenes = new containerField('almacenes',$this->_detalle($codigo));
		$edit->almacenes->when = array("show","modify");

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "<h1>Maestro de supermercado</h1>";
		$data["head"]    =
		   script("jquery.pack.js").
		   script("plugins/jquery.numeric.pack.js").
		   script("plugins/jquery.floatnumber.js").
		   script("sinvmaes.js").
		   $this->rapyd->get_head();

		$this->load->view('view_ventanas', $data);
	}
	function dataeditsan() {
		$this->rapyd->load('dataedit');
		//rapydlib("prototype");

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';

		$ajax_onchange = '
			  function get_familias(){
			    var url = "'.site_url('supermercado/maes/maesfamilias').'";
			    var pars = "dpto="+$F("depto");
			    var myAjax = new Ajax.Updater("td_familia", url, { method: "post", parameters: pars });

			    var url = "'.site_url('supermercado/maes/maesgrupos').'";
			    var gmyAjax = new Ajax.Updater("td_grupo", url);
			  }

			  function get_grupo(){
			    var url = "'.site_url('supermercado/maes/maesgrupos').'";
			    var pars = "dpto="+$F("depto")+"&fami="+$F("familia");
			    var myAjax = new Ajax.Updater("td_grupo", url, { method: "post", parameters: pars });
			  }';


		$edit = new DataEdit("Maestro de Supermercado", "maes");
		$edit->script($ajax_onchange);
		$edit->script($ajax_onchange,"modify");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		$edit->back_url = site_url("supermercado/maes/filteredgrid");

		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->size=20;
		$edit->codigo->maxlength=15;
		$edit->codigo->rule = "required";
		$edit->codigo->mode="autohide";
		$edit->codigo->group = "Datos";

		$edit->marca = new dropdownField("Marca", "marca");
		$edit->marca->style='width:110px;';
		$edit->marca->option("","");
		$edit->marca->options("SELECT marca as codigo, marca FROM marc ORDER BY marca");
		$edit->marca->group = "Datos";

		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->style='width:110px;';
		$edit->tipo->option("I","Inventario" );
		$edit->tipo->option("L","Licores"    );
		$edit->tipo->option("P","Por peso"   );
		$edit->tipo->option("K","Desposte"   );
		$edit->tipo->option("C","Combo"      );
		$edit->tipo->option("F","Farmaco"    );
		$edit->tipo->option("S","Servicio"   );
		$edit->tipo->option("R","Receta"     );
		$edit->tipo->option("D","Desactivado");
		$edit->tipo->group = "Datos";

		$edit->dpto = new dropdownField("Departamento", "depto");
		$edit->dpto->option("","");
		$edit->dpto->options("SELECT depto,descrip FROM dpto WHERE tipo='I' ORDER BY descrip");
		$edit->dpto->onchange = "get_familias();";
		$edit->dpto->group = "Datos";

		$edit->familia = new dropdownField("Familia", "familia");
		$edit->familia->onchange = "get_grupo();";
		$edit->familia->group = "Datos";

		$edit->grupo = new dropdownField("Grupo", "grupo");
		$edit->grupo->group = "Datos";

		$edit->referen = new inputField("S.N.M.", "referen");
		$edit->referen->size=17;
		$edit->referen->maxlength=15;
		$edit->referen->group = "Datos";

		$edit->barras = new inputField("Barras", "barras");
		$edit->barras->size=17;
		$edit->barras->maxlength=15;
		$edit->barras->group = "Datos";

		$edit->cu_inve = new inputField("Caja", "cu_inve");
		$edit->cu_inve->size=17;
		$edit->cu_inve->maxlength=15;
		$edit->cu_inve->group = "Datos";

		$edit->ensambla = new dropdownField("Ensamblado", "ensambla");
		$edit->ensambla->style='width:60px;';
		$edit->ensambla->option("N","No" );
		$edit->ensambla->option("S","Si" );
		$edit->ensambla->group = "Datos";

		$edit->empaque = new inputField("Des/Epq", "empaque");
		$edit->empaque->size=30;
		$edit->empaque->maxlength=27;
		$edit->empaque->group = "Datos";

		$edit->descrip = new inputField("Larga","descrip");
		$edit->descrip->size=48;
		$edit->descrip->maxlength=40;
		$edit->descrip->rule = "required";
		$edit->descrip->group = "Descripci&oacute;nes";

		$edit->corta = new inputField("Corta", "corta");
		$edit->corta->size=28;
		$edit->corta->maxlength=20;
		$edit->corta->group = "Descripci&oacute;nes";

		$edit->susti = new inputField("Clave", "susti");
		$edit->susti->size=15;
		$edit->susti->maxlength=10;
		$edit->susti->group = "Descripci&oacute;nes";

		$edit->serial = new dropdownField("Serializar", "serial");
		$edit->serial->style='width:60px;';
		$edit->serial->option("N","No" );
		$edit->serial->option("S","Si" );
		$edit->serial->when =array("show");
		$edit->serial->group = "Existencias";

		$edit->minimo = new inputField("Existencia Minima", "minimo");
		$edit->minimo->size=15;
		$edit->minimo->maxlength=11;
		$edit->minimo->when =array("show");
		$edit->minimo->group = "Existencias";

		$edit->maximo = new inputField("Existencia Maxima", "maximo");
		$edit->maximo->size=15;
		$edit->maximo->maxlength=11;
		$edit->maximo->when =array("show");
		$edit->maximo->group = "Existencias";

		$edit->ordena = new inputField("Existencia Ordenada", "ordena");
		$edit->ordena->size=15;
		$edit->ordena->maxlength=11;
		$edit->ordena->when =array("show");
		$edit->ordena->group = "Existencias";

		$edit->alcohol = new inputField("Licor G/I", "alcohol");
		$edit->alcohol->size=15;
		$edit->alcohol->maxlength=11;
		$edit->alcohol->group = "Licores";

		$edit->implic = new inputField("Impuesto por alcohol", "implic");
		$edit->implic->size=8;
		$edit->implic->maxlength=6;
		$edit->implic->group = "Licores";

		$edit->tamano = new inputField("Tama&ntilde;o", "tamano");
		$edit->tamano->size=15;
		$edit->tamano->maxlength=11;
		$edit->tamano->when =array("show");
		$edit->tamano->group = "Licores";

		$edit->medida = new inputField("Medida", "medida");
		$edit->medida->size=15;
		$edit->medida->maxlength=11;
		$edit->medida->when =array("show");
		$edit->medida->group = "Licores";

		$edit->conjunto = new inputField("Conjunto de Articulo", "conjunto");
		$edit->conjunto->size=8;
		$edit->conjunto->maxlength=8;
		$edit->conjunto->group = "Licores";

		$edit->ultimo = new inputField("Ultimo", "ultimo");
		$edit->ultimo->css_class='inputnum';
		$edit->ultimo->size=21;
		$edit->ultimo->maxlength=17;
		$edit->ultimo->group = "Costos";

		$edit->iva = new inputField("Iva", "iva");
		$edit->iva->css_class='inputnum';
		$edit->iva->onchange = "calculos('M');";
		$edit->iva->size=10;
		$edit->iva->maxlength=8;
		$edit->iva->group = "Costos";

		$edit->costo = new inputField("Promedio", "costo");
    $edit->costo->css_class='inputnum';
		$edit->costo->onchange = "calculos(costo);";
		$edit->costo->size=21;
		$edit->costo->maxlength=17;
		$edit->costo->group = "Costos";

		$edit->fcalc = new dropdownField("Base C&aacute;lculo", "fcalc");
		$edit->fcalc->style='width:150px;';
		$edit->fcalc->option("U","Ultimo" );
		$edit->fcalc->option("P","Promedio" );
		$edit->fcalc->onchange = "calculos('M');";
		$edit->fcalc->group = "Costos";

		$edit->redondeo = new dropdownField("Redondear", "redondeo");
		$edit->redondeo->style='width:150px;';
		$edit->redondeo->option("NO","No");
		$edit->redondeo->option("P0","Precio Decimales");
		$edit->redondeo->option("P1","Precio Unidades" );
		$edit->redondeo->option("P2","Precio Decenas"  );
		$edit->redondeo->option("B0","Base Decimales"  );
		$edit->redondeo->option("B1","Base Unidades"   );
		$edit->redondeo->option("B2","Base Decenas"    );
    $edit->redondeo->onchange = "redonde('M');";
	  $edit->redondeo->group = "Costos";

		$edit->fracxuni = new inputField("Presenta", "fracxuni");
		$edit->fracxuni->size=5;
		$edit->fracxuni->maxlength=11;
		$edit->fracxuni->group = "Costos";

		$edit->dempaq = new dropdownField("Unidad", "dempaq");
		$edit->dempaq->style='width:110x;';
		$edit->dempaq->options("SELECT presenta label, presenta FROM mpre ORDER BY presenta");
		$edit->dempaq->in="fracxuni";

		$edit->mempaq = new dropdownField("Unidad", "mempaq");
		$edit->mempaq->style='width:110x;';
		$edit->mempaq->options("SELECT presenta label, presenta FROM mpre ORDER BY presenta");
		$edit->mempaq->in="fracxuni";

		for($i=1;$i<=5;$i++){
			$objeto="margen$i";
			$edit->$objeto = new inputField("Margen $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->maxlength=6;
			$edit->$objeto->onchange = "calculos('I');";
			$edit->$objeto->rule="required";
			$edit->$objeto->group = "Precios";


			$objeto="Ebase$i";
			$edit->$objeto = new freeField("","","Precio $i");
			$edit->$objeto->in="margen$i";

			$objeto="base$i";
			$edit->$objeto = new inputField("Base $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->maxlength=13;
			$edit->$objeto->in="margen$i";
			$edit->$objeto->onchange = "cambiobase('I');";
			$edit->$objeto->rule="required";

			$objeto="Eprecio$i";
			$edit->$objeto = new freeField("","","Precio + I.V.A. $i");
			$edit->$objeto->in="margen$i";

			$objeto="precio$i";
			$edit->$objeto = new inputField("Margen $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->maxlength=6;
			$edit->$objeto->in="margen$i";
			$edit->$objeto->onchange = "cambioprecio('I');";
			$edit->$objeto->rule="required";
		}

		$codigo=$edit->_dataobject->get("codigo");
		$edit->almacenes = new containerField('almacenes',$this->_detalle($codigo));
		$edit->almacenes->when = array("show","modify");
		$edit->almacenes->group = "Precios";


		if($this->rapyd->uri->is_set("modify") or $this->rapyd->uri->is_set("show")){
			$codigo =$edit->_dataobject->get("codigo");
			$depto  =$edit->_dataobject->get("depto");
			$familia=$edit->_dataobject->get("familia");

			$edit->familia->options("SELECT familia,descrip FROM fami WHERE depto = '$depto' ORDER BY descrip");
			//$edit->grupo->options("SELECT grupo, nom_grup FROM grup WHERE depto='$depto' AND familia='$familia'");
		}else{
			$edit->familia->option("","Seleccione un departamento");
			$edit->grupo->option("","Seleccione una familia");
		}
		//$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->buttons("modify", "save", "undo", "back");
		$edit->build();

		//echo $edit->codigo->value;
		$data['content'] = $edit->output;
		//$data['content'] = $this->load->view('view_maes', $conten,true);
		//$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").script("tabber.js").script("prototype.js").script("sinvmaes.js").$this->rapyd->get_head();
		$data["head"]      = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$data['title']   = '<h1>Maestro de Supermercado</h1>';
		$this->load->view('view_ventanas', $data);
	}
	function dataedit() {
		$this->rapyd->load('dataedit');
		//rapydlib("prototype");

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';

		$ajax_onchange = '
			  function get_familias(){
			    var url = "'.site_url('supermercado/maes/maesfamilias').'";
			    var pars = "dpto="+$F("depto");
			    var myAjax = new Ajax.Updater("td_familia", url, { method: "post", parameters: pars });

			    var url = "'.site_url('supermercado/maes/maesgrupos').'";
			    var gmyAjax = new Ajax.Updater("td_grupo", url);
			  }

			  function get_grupo(){
			    var url = "'.site_url('supermercado/maes/maesgrupos').'";
			    var pars = "dpto="+$F("depto")+"&fami="+$F("familia");
			    var myAjax = new Ajax.Updater("td_grupo", url, { method: "post", parameters: pars });
			  }';


		$edit = new DataEdit("Maestro de Supermercado", "maes");
		$edit->script($ajax_onchange);
		$edit->script($ajax_onchange,"modify");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		$edit->back_url = site_url("supermercado/maes/filteredgrid");

		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->size=20;
		$edit->codigo->maxlength=15;
		$edit->codigo->rule = "required";
		$edit->codigo->mode="autohide";
		$edit->codigo->group = "Datos";

		$edit->marca = new dropdownField("Marca", "marca");
		$edit->marca->style='width:110px;';
		$edit->marca->option("","");
		$edit->marca->options("SELECT marca as codigo, marca FROM marc ORDER BY marca");
		$edit->marca->group = "Datos";

		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->style='width:110px;';
		$edit->tipo->option("I","Inventario" );
		$edit->tipo->option("L","Licores"    );
		$edit->tipo->option("P","Por peso"   );
		$edit->tipo->option("K","Desposte"   );
		$edit->tipo->option("C","Combo"      );
		$edit->tipo->option("F","Farmaco"    );
		$edit->tipo->option("S","Servicio"   );
		$edit->tipo->option("R","Receta"     );
		$edit->tipo->option("D","Desactivado");
		$edit->tipo->group = "Datos";

		$edit->dpto = new dropdownField("Departamento", "depto");
		$edit->dpto->option("","");
		$edit->dpto->options("SELECT depto,descrip FROM dpto WHERE tipo='I' ORDER BY descrip");
		$edit->dpto->onchange = "get_familias();";
		$edit->dpto->group = "Datos";

		$edit->familia = new dropdownField("Familia", "familia");
		$edit->familia->onchange = "get_grupo();";
		$edit->familia->group = "Datos";

		$edit->grupo = new dropdownField("Grupo", "grupo");
		$edit->grupo->group = "Datos";

		$edit->referen = new inputField("S.N.M.", "referen");
		$edit->referen->size=17;
		$edit->referen->maxlength=15;
		$edit->referen->group = "Datos";

		$edit->barras = new inputField("Barras", "barras");
		$edit->barras->size=17;
		$edit->barras->maxlength=15;
		$edit->barras->group = "Datos";

		$edit->cu_inve = new inputField("Caja", "cu_inve");
		$edit->cu_inve->size=17;
		$edit->cu_inve->maxlength=15;
		$edit->cu_inve->group = "Datos";

		$edit->ensambla = new dropdownField("Ensamblado", "ensambla");
		$edit->ensambla->style='width:60px;';
		$edit->ensambla->option("N","No" );
		$edit->ensambla->option("S","Si" );
		$edit->ensambla->group = "Datos";

		$edit->empaque = new inputField("Des/Epq", "empaque");
		$edit->empaque->size=30;
		$edit->empaque->maxlength=27;
		$edit->empaque->group = "Datos";

		$edit->descrip = new inputField("Larga","descrip");
		$edit->descrip->size=48;
		$edit->descrip->maxlength=40;
		$edit->descrip->rule = "required";
		$edit->descrip->group = "Descripci&oacute;nes";

		$edit->corta = new inputField("Corta", "corta");
		$edit->corta->size=28;
		$edit->corta->maxlength=20;
		$edit->corta->group = "Descripci&oacute;nes";

		$edit->susti = new inputField("Clave", "susti");
		$edit->susti->size=15;
		$edit->susti->maxlength=10;
		$edit->susti->group = "Descripci&oacute;nes";

		$edit->serial = new dropdownField("Serializar", "serial");
		$edit->serial->style='width:60px;';
		$edit->serial->option("N","No" );
		$edit->serial->option("S","Si" );
		$edit->serial->when =array("show");
		$edit->serial->group = "Existencias";

		$edit->minimo = new inputField("Existencia Minima", "minimo");
		$edit->minimo->size=15;
		$edit->minimo->maxlength=11;
		$edit->minimo->when =array("show");
		$edit->minimo->group = "Existencias";

		$edit->maximo = new inputField("Existencia Maxima", "maximo");
		$edit->maximo->size=15;
		$edit->maximo->maxlength=11;
		$edit->maximo->when =array("show");
		$edit->maximo->group = "Existencias";

		$edit->ordena = new inputField("Existencia Ordenada", "ordena");
		$edit->ordena->size=15;
		$edit->ordena->maxlength=11;
		$edit->ordena->when =array("show");
		$edit->ordena->group = "Existencias";

		$edit->alcohol = new inputField("Licor G/I", "alcohol");
		$edit->alcohol->size=15;
		$edit->alcohol->maxlength=11;
		$edit->alcohol->group = "Licores";

		$edit->implic = new inputField("Impuesto por alcohol", "implic");
		$edit->implic->size=8;
		$edit->implic->maxlength=6;
		$edit->implic->group = "Licores";

		$edit->tamano = new inputField("Tama&ntilde;o", "tamano");
		$edit->tamano->size=15;
		$edit->tamano->maxlength=11;
		$edit->tamano->when =array("show");
		$edit->tamano->group = "Licores";

		$edit->medida = new inputField("Medida", "medida");
		$edit->medida->size=15;
		$edit->medida->maxlength=11;
		$edit->medida->when =array("show");
		$edit->medida->group = "Licores";

		$edit->conjunto = new inputField("Conjunto de Articulo", "conjunto");
		$edit->conjunto->size=8;
		$edit->conjunto->maxlength=8;
		$edit->conjunto->group = "Licores";

		$edit->ultimo = new inputField("Ultimo", "ultimo");
		$edit->ultimo->css_class='inputnum';
		$edit->ultimo->size=21;
		$edit->ultimo->maxlength=17;
		$edit->ultimo->group = "Costos";

		$edit->iva = new inputField("Iva", "iva");
		$edit->iva->css_class='inputnum';
		$edit->iva->onchange = "calculos('M');";
		$edit->iva->size=10;
		$edit->iva->maxlength=8;
		$edit->iva->group = "Costos";

		$edit->costo = new inputField("Promedio", "costo");
    $edit->costo->css_class='inputnum';
		$edit->costo->onchange = "calculos(costo);";
		$edit->costo->size=21;
		$edit->costo->maxlength=17;
		$edit->costo->group = "Costos";

		$edit->fcalc = new dropdownField("Base C&aacute;lculo", "fcalc");
		$edit->fcalc->style='width:150px;';
		$edit->fcalc->option("U","Ultimo" );
		$edit->fcalc->option("P","Promedio" );
		$edit->fcalc->onchange = "calculos('M');";
		$edit->fcalc->group = "Costos";

		$edit->redondeo = new dropdownField("Redondear", "redondeo");
		$edit->redondeo->style='width:150px;';
		$edit->redondeo->option("NO","No");
		$edit->redondeo->option("P0","Precio Decimales");
		$edit->redondeo->option("P1","Precio Unidades" );
		$edit->redondeo->option("P2","Precio Decenas"  );
		$edit->redondeo->option("B0","Base Decimales"  );
		$edit->redondeo->option("B1","Base Unidades"   );
		$edit->redondeo->option("B2","Base Decenas"    );
    $edit->redondeo->onchange = "redonde('M');";
	  $edit->redondeo->group = "Costos";

		$edit->fracxuni = new inputField("Presenta", "fracxuni");
		$edit->fracxuni->size=5;
		$edit->fracxuni->maxlength=11;
		$edit->fracxuni->group = "Costos";

		$edit->dempaq = new dropdownField("Unidad", "dempaq");
		$edit->dempaq->style='width:110x;';
		$edit->dempaq->options("SELECT presenta label, presenta FROM mpre ORDER BY presenta");
		$edit->dempaq->in="fracxuni";

		$edit->mempaq = new dropdownField("Unidad", "mempaq");
		$edit->mempaq->style='width:110x;';
		$edit->mempaq->options("SELECT presenta label, presenta FROM mpre ORDER BY presenta");
		$edit->mempaq->in="fracxuni";

		for($i=1;$i<=5;$i++){
			$objeto="margen$i";
			$edit->$objeto = new inputField("Margen $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->maxlength=6;
			$edit->$objeto->onchange = "calculos('I');";
			$edit->$objeto->rule="required";
			$edit->$objeto->group = "Precios";


			$objeto="Ebase$i";
			$edit->$objeto = new freeField("","","Precio $i");
			$edit->$objeto->in="margen$i";

			$objeto="base$i";
			$edit->$objeto = new inputField("Base $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->maxlength=13;
			$edit->$objeto->in="margen$i";
			$edit->$objeto->onchange = "cambiobase('I');";
			$edit->$objeto->rule="required";

			$objeto="Eprecio$i";
			$edit->$objeto = new freeField("","","Precio + I.V.A. $i");
			$edit->$objeto->in="margen$i";

			$objeto="precio$i";
			$edit->$objeto = new inputField("Margen $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->maxlength=6;
			$edit->$objeto->in="margen$i";
			$edit->$objeto->onchange = "cambioprecio('I');";
			$edit->$objeto->rule="required";
		}

		$codigo=$edit->_dataobject->get("codigo");
		$edit->almacenes = new containerField('almacenes',$this->_detalle($codigo));
		$edit->almacenes->when = array("show","modify");
		$edit->almacenes->group = "Precios";


		if($this->rapyd->uri->is_set("modify") or $this->rapyd->uri->is_set("show")){
			$codigo =$edit->_dataobject->get("codigo");
			$depto  =$edit->_dataobject->get("depto");
			$familia=$edit->_dataobject->get("familia");

			$edit->familia->options("SELECT familia,descrip FROM fami WHERE depto = '$depto' ORDER BY descrip");
			//$edit->grupo->options("SELECT grupo, nom_grup FROM grup WHERE depto='$depto' AND familia='$familia'");
		}else{
			$edit->familia->option("","Seleccione un departamento");
			$edit->grupo->option("","Seleccione una familia");
		}
		//$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->buttons("modify", "save", "undo", "back");
		$edit->build();

		//echo $edit->codigo->value;
		$data['content'] = $edit->output;
		//$data['content'] = $this->load->view('view_maes', $conten,true);
		//$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").script("tabber.js").script("prototype.js").script("sinvmaes.js").$this->rapyd->get_head();
		$data["head"]      = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$data['title']   = '<h1>Maestro de Supermercado</h1>';
		$this->load->view('view_ventanas', $data);
	}
*/

	//******************************************************************
	//  Detalle de Almacenes
	//
	function _detalle($codigo){
  	$salida='hola';
  	if(!empty($codigo)){
  		$this->rapyd->load('dataedit','datagrid');

			$grid = new DataGrid('');

			$grid->db->select=array("ubica","locali","cantidad","fraccion");
			$grid->db->from('ubic');
			$grid->db->where('codigo',$codigo);

			$grid->column("Almacen"          ,"ubica" );
			$grid->column("Ubicaci&oacute;n" ,"locali");
			$grid->column("Cantidad"         ,"cantidad",'align="RIGHT"');
			$grid->column("Fracci&oacute;n"  ,"fraccion",'align="RIGHT"');

			$grid->build();
			$salida=$grid->output;
		}
		return $salida;
	}

/*
	function sug($tabla=''){
		if($tabla=='dpto'){
			$valor=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN dpto ON LPAD(depto,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND depto IS NULL LIMIT 1");
		}elseif($tabla=='line'){
				$valor=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN line ON LPAD(linea,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND linea IS NULL LIMIT 1");
		}elseif($tabla=='grup'){
				$valor=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN grup ON LPAD(grupo,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND grupo IS NULL LIMIT 1");
		}
		return $valor;
	}

	function ultimo(){
		$ultimo=$this->datasis->dameval("SELECT codigo FROM maes ORDER BY codigo DESC LIMIT 1");
		echo $ultimo;
	}

	function sugerir(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN maes ON LPAD(codigo,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND codigo IS NULL LIMIT 1");
		echo $ultimo;
	}

	function chexiste($codigo){
		//$codigo=$this->input->post('codigo');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM maes WHERE codigo='$codigo'");
		if ($check > 0){
			$descrip=$this->datasis->dameval("SELECT descrip FROM maes WHERE codigo='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el producto $descrip");
			return FALSE;
		}else {
		 return TRUE;
		}
	}

	function chexiste2($alterno){
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM maes WHERE alterno='$alterno'");
		if ($check > 0){
			$descrip=$this->datasis->dameval("SELECT descrip FROM maes WHERE alterno='$alterno'");
			$this->validation->set_message('chexiste',"El codigo alterno $alterno ya existe para el producto $descrip");
			return FALSE;
		}else {
			return TRUE;
		}
	}*/

	function maesfamilias(){
		$this->rapyd->load("fields");
		$where = "";
		$sql = "SELECT familia, descrip FROM fami ";
		$linea = new dropdownField("Familia", "familia");
		$dpto = $this->input->post('dpto');

		if ($dpto){
			$where = "WHERE depto = ".$this->db->escape($dpto);
			$sql = "SELECT familia, descrip FROM fami $where ORDER BY descrip";
			$linea->option("","");
			$linea->options($sql);
		}else{
			 $linea->option("","Seleccione Un Departamento");
		}
		$linea->status   = "modify";
		$linea->onchange = "get_grupo();";
		$linea->build();
		echo $linea->output;
	}

	function maesgrupos(){
		$this->rapyd->load("fields");
		$where = "";
		$fami=$this->input->post('fami');
		$dpto=$this->input->post('dpto');

		$grupo = new dropdownField("Grupo", "grupo");
		if ($fami AND $dpto AND !(empty($fami) OR empty($dpto))) {
			$where .= "WHERE depto = ".$this->db->escape($dpto);
			$where .= "AND familia = ".$this->db->escape($fami);
			$sql = "SELECT grupo, nom_grup FROM grup $where";
			$grupo->option("","");
			$grupo->options($sql);
		}else{
			$grupo->option("","Seleccione una familia");
		}
		$grupo->status = "modify";
		$grupo->build();
		echo $grupo->output;
	}

/*
	function instalar(){
		$mSQL='ALTER TABLE `maes` DROP PRIMARY KEY';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `maes` ADD UNIQUE `codigo` (`codigo`)';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE maes ADD id INT AUTO_INCREMENT PRIMARY KEY';
		$this->db->simple_query($mSQL);

		$mSQL="CREATE TABLE IF NOT EXISTS `sinvcombo` (
		`combo` char(15) NOT NULL,
		`codigo` char(15) NOT NULL default '',
		`descrip` char(30) default NULL,
		`cantidad` decimal(10,3) default NULL,
		`precio` decimal(15,2) default NULL,
		`transac` char(8) default NULL,
		`estampa` date default NULL,
		`hora` char(8) default NULL,
		`usuario` char(12) default NULL,
		`costo` decimal(17,2) default '0.00',
		PRIMARY KEY  (`combo`,`codigo`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		$this->db->simple_query($mSQL);
	}
*/
}
