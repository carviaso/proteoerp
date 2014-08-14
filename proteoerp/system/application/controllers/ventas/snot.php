<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Snot extends Controller {
	var $mModulo = 'SNOT';
	var $titp    = 'Notas de despacho';
	var $tits    = 'Notas de despacho';
	var $url     = 'ventas/snot/';

	function Snot(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'SNOT', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 900, 650, substr($this->url,0,-1) );
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

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 220, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );


		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'impri',  'img'=>'assets/default/images/print.png','alt' => 'Reimprimir', 'label'=>'Reimprimir Documento'));
		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar NOTAS DE DESPACHO'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')

		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']    = $WestPanel;
		$param['script']       = '';
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']    = $this->datasis->listados('SNOT', 'JQ');
		$param['otros']       = $this->datasis->otros('SNOT', 'JQ');
		$param['centerpanel']  = $centerpanel;
		//$param['funciones']    = $funciones;
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
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function snotadd() {
			$.post("'.site_url('ventas/snot/dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function snotedit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url('ventas/snot/dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		};';

		$bodyscript .= '
		function snotshow(){
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
		function snotdel() {
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
		jQuery("#impri").click(function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('formatos/ver/SNOT').'/\'+id+\'/id\'').';
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		});';

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
									'.$this->datasis->jwinopen(site_url('formatos/ver/SNOT').'/\'+json.pk.id+\'/id\'').';
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

		$bodyscript .= '});';
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


		$grid->addField('factura');
		$grid->label('Factura');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('cod_cli');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
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

		$grid->addField('fechafa');
		$grid->label('Fecha.Fac');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
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


		$grid->addField('cajero');
		$grid->label('Cajero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
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


		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
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


		$grid->addField('transac');
		$grid->label('Transaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('observa1');
		$grid->label('Observaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('observa2');
		$grid->label('Observaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
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


		//$grid->addField('almaorg');
		//$grid->label('Alma.Org');
		//$grid->params(array(
		//	'search'        => 'true',
		//	'editable'      => $editar,
		//	'width'         => 40,
		//	'edittype'      => "'text'",
		//	'editrules'     => '{ required:true}',
		//	'editoptions'   => '{ size:4, maxlength: 4 }',
		//));


		//$grid->addField('almades');
		//$grid->label('Alma.Des');
		//$grid->params(array(
		//	'search'        => 'true',
		//	'editable'      => $editar,
		//	'width'         => 40,
		//	'edittype'      => "'text'",
		//	'editrules'     => '{ required:true}',
		//	'editoptions'   => '{ size:4, maxlength: 4 }',
		//));


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
		$grid->setHeight('200');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
				if (id){
					jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
				}
			}'
		);


		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('SNOT','1' ));
		$grid->setEdit(   false );
		$grid->setDelete( $this->datasis->sidapuede('SNOT','5'));
		$grid->setSearch( $this->datasis->sidapuede('SNOT','6'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: snotadd, editfunc: snotedit, delfunc: snotdel, viewfunc: snotshow');

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
		$mWHERE = $grid->geneTopWhere('snot');

		$response   = $grid->getData('snot', array(array()), array(), false, $mWHERE , 'id', 'desc');
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
/*
		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM snot WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('snot', $data);
					echo "Registro Agregado";

					logusu('SNOT',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM snot WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM snot WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE snot SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("snot", $data);
				logusu('SNOT',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('snot', $data);
				logusu('SNOT',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM snot WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM snot WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM snot WHERE id=$id ");
				logusu('SNOT',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
		*/
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
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('cant');
		$grid->label('Cant.');
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


		$grid->addField('saldo');
		$grid->label('Saldo');
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


		$grid->addField('entrega');
		$grid->label('Entrega');
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


		$grid->addField('factura');
		$grid->label('Factura');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
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


		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
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


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('190');
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(false);
		$grid->setDelete(false);
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
	function getdatait( $id = 0 ){
		if($id == 0){
			$id = $this->datasis->dameval('SELECT MAX(id) FROM snot');
		}
		if(empty($id)) return '';
		$dbid   = $this->db->escape($id);
		$numero = $this->datasis->dameval('SELECT numero FROM snot WHERE id='.$dbid);
		$dbnumero= $this->db->escape($numero);

		$orderby= '';
		$sidx=$this->input->post('sidx');
		if($sidx){
			$campos = $this->db->list_fields('itsnot');
			if(in_array($sidx,$campos)){
				$sidx = trim($sidx);
				$sord   = $this->input->post('sord');
				$orderby="ORDER BY `${sidx}` ".(($sord=='asc')? 'ASC':'DESC');
			}
		}

		$grid    = $this->jqdatagrid;
		$mSQL    = 'SELECT * FROM itsnot WHERE numero='.$dbnumero.' '.$orderby;
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setDatait(){
	}

	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');

		$modbusSinv=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo'  =>'C&oacute;digo',
				'descrip' =>'Descripci&oacute;n',
				),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array(
				'codigo' =>'codigo_<#i#>',
				'descrip'=>'descrip_<#i#>',
			),
			'p_uri'   => array(4=>'<#i#>'),
			'titulo'  => 'Buscar Articulo',
			'where'   => '`activo` = "S" AND `tipo` = "Articulo"'
		);
		//$boton=$this->datasis->p_modbus($modbusSinv,'<#i#>');

		$modbus=array(
			'tabla'   =>'sfac',
			'columnas'=>array(
				'numero'  =>'N&uacute;mero',
				'fecha'   =>'Fecha',
				'cod_cli' =>'Cliente',
				'rifci'   =>'Rif',
				'nombre'  =>'Nombre',
				'tipo_doc'=>'Tipo',
				),
			'filtro'  =>array(
				'numero' =>'N&uacute;mero',
				'cod_cli'=>'Cliente',
				'rifci'  =>'Rif',
				'nombre' =>'Nombre'),
			'where'=>'tipo_doc = "F" and mid(numero,1,1) <> "_"',
			'retornar'=>array(
				'numero' =>'factura',
				'fecha'  =>'fechafa',
				'cod_cli'=>'cod_cli',
				'nombre' =>'nombre'
			),
			'titulo'  => 'Buscar Factura',
		);
		//$btn=$this->datasis->modbus($modbus);

		$do = new DataObject('snot');
		$do->rel_one_to_many('itsnot', 'itsnot', 'numero');
		$do->rel_pointer('itsnot','sinv','itsnot.codigo=sinv.codigo','sinv.descrip AS sinvdescrip','sinv.ultimo AS sinvultimo');

		$edit = new DataDetails('Nota de Entrega', $do);
		$edit->on_save_redirect=false;
		$edit->set_rel_title('itsnot','Producto <#o#>');

		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$("#fechafa").datepicker({dateFormat:"dd/mm/yy"});
		});		';
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->pre_process( 'insert','_pre_insert');
		$edit->pre_process( 'delete','_pre_delete');
		$edit->pre_process( 'update','_pre_update');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'required|chfecha';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->calendar=false;
		$edit->fecha->size = 10;

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->mode='autohide';
		$edit->numero->maxlength=8;
		$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->tipo = new  dropdownField('Tipo', 'tipo');
		$edit->tipo->option('E','Entrega');
		$edit->tipo->option('D','Devoluci&oacute;n');
		$edit->tipo->style='width:140px;';
		$edit->tipo->size = 5;
		$edit->tipo->rule='required|enum[D,E]';
		$edit->tipo->insertValue='E';

		$edit->fechafa = new DateonlyField('Fecha Factura', 'fechafa','d/m/Y');
		$edit->fechafa->rule = 'required|chfecha';
		$edit->fechafa->mode = 'autohide';
		$edit->fechafa->calendar=false;
		$edit->fechafa->size = 10;
		$edit->fechafa->type = 'inputhidden';

		$edit->factura = new inputField('Factura', 'factura');
		$edit->factura->size = 10;
		$edit->factura->mode='autohide';
		$edit->factura->maxlength=8;
		$edit->factura->rule='required|existefac|callback_chsnte';
		//$edit->factura->append($btn);

		$edit->peso = new inputField('Peso', 'peso');
		$edit->peso->css_class = 'inputnum';
		$edit->peso->readonly  = true;
		$edit->peso->size      = 10;
		$edit->peso->type = 'inputhidden';

		$edit->cliente = new inputField('Cliente','cod_cli');
		$edit->cliente->size = 6;
		$edit->cliente->maxlength=5;
		$edit->cliente->type = 'inputhidden';
		$edit->cliente->rule = 'required|callback_chsclifa';

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->size = 25;
		$edit->nombre->maxlength=40;
		$edit->nombre->autocomplete=false;
		$edit->nombre->rule= 'required';
		$edit->nombre->type = 'inputhidden';

		$edit->observa1 = new inputField('Observaciones', 'observ1');
		$edit->observa1->size      = 40;
		$edit->observa1->maxlength = 80;

		//**************************
		//  Campos para el detalle
		//**************************
		$edit->codigo = new inputField('C&oacute;digo', 'codigo_<#i#>');
		$edit->codigo->size     = 12;
		$edit->codigo->db_name  = 'codigo';
		$edit->codigo->readonly = true;
		$edit->codigo->rel_id   = 'itsnot';
		$edit->codigo->rule     = 'required|existesinv|callback_chrepetidos';
		$edit->codigo->type     = 'inputhidden';
		//$edit->codigo->append($boton);

		$edit->descrip = new inputField('Descripci&oacute;n', 'descrip_<#i#>');
		$edit->descrip->size=36;
		$edit->descrip->db_name='descrip';
		$edit->descrip->maxlength=50;
		$edit->descrip->readonly  = true;
		$edit->descrip->rel_id='itsnot';
		$edit->descrip->type = 'inputhidden';

		$edit->cant = new inputField('Cantidad', 'cant_<#i#>');
		$edit->cant->db_name   = 'cant';
		$edit->cant->css_class = 'inputnum';
		$edit->cant->rel_id    = 'itsnot';
		$edit->cant->maxlength = 10;
		$edit->cant->size      = 6;
		$edit->cant->rule      = 'required|positive|numeric';
		$edit->cant->type      = 'inputhidden';
		$edit->cant->showformat= 'decimal';
		$edit->cant->autocomplete=false;

		$edit->saldo = new inputField('Saldo', 'saldo_<#i#>');
		$edit->saldo->db_name   = 'saldo';
		$edit->saldo->css_class = 'inputnum';
		$edit->saldo->rel_id    = 'itsnot';
		$edit->saldo->maxlength = 10;
		$edit->saldo->size      = 6;
		$edit->saldo->showformat= 'decimal';
		$edit->saldo->rule      = 'required|positive|numeric';
		$edit->saldo->type      = 'inputhidden';
		$edit->saldo->autocomplete=false;

		$edit->entrega = new inputField('Entrega', 'entrega_<#i#>');
		$edit->entrega->db_name   = 'entrega';
		$edit->entrega->css_class = 'inputnum';
		$edit->entrega->rel_id    = 'itsnot';
		$edit->entrega->maxlength = 10;
		$edit->entrega->size      = 6;
		$edit->entrega->rule      = 'positive|numeric|callback_chitems[<#i#>]';
		$edit->entrega->showformat='decimal';
		$edit->entrega->autocomplete=false;

		//$edit->itfactura = new inputField('Factura <#o#>', 'itfactura_<#i#>');
		//$edit->itfactura->size     = 12;
		//$edit->itfactura->db_name  = 'factura';
		//$edit->itfactura->readonly = true;
		//$edit->itfactura->rel_id   = 'itsnot';
		//**************************
		//fin de campos para detalle
		//**************************

		$edit->hora    = new autoUpdateField('hora'   ,date('Hi') , date('Hi'));
		$edit->estampa = new autoUpdateField('estampa',date('Ymd'), date('Ymd'));
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		//$edit->buttons('save', 'undo', 'delete', 'back','add_rel');
		//$edit->buttons('add_rel');
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
			$data['content'] = $this->load->view('view_snot', $conten);
		}
	}

	function chsclifa($scli){
		$factura  = $this->input->post('factura');
		$dbfactura= $this->db->escape($factura);
		$mSQL='SELECT TRIM(cod_cli) AS cliente FROM sfac WHERE tipo_doc="F" AND numero='.$dbfactura;
		$cliente=$this->datasis->dameval($mSQL);
		if($scli==$cliente){
			return true;
		}

		$this->validation->set_message('chsclifa', 'El cliente '.$scli.' no pertenece a la factura selecionada.');
		return false;
	}

	function chfechafa($fecha){
		//falta implementar
		return true;
		$factura = $this->input->post('factura');
		$dbfactura= $this->db->escape($factura);
		$mSQL='SELECT fecha AS cliente FROM sfac WHERE tipo_doc="F" AND numero='.$dbfactura;
		$fechafa=$this->datasis->dameval($mSQL);
		if($fecha==$fechafa){
			return true;
		}

		$this->validation->set_message('chfechafa', 'La fecha '.$fecha.' no pertenece a la factura selecionada.');
		return false;
	}

	function chitems($val,$i){
		$factura = $this->input->post('factura');
		$codigo  = $this->input->post('codigo_'.$i);
		$tipo    = $this->input->post('tipo');
		if($tipo!='D'){
			$dbcodigo =$this->db->escape($codigo );
			$dbfactura=$this->db->escape($factura);

			$facturado= $this->datasis->dameval("SELECT SUM(cana) AS cana FROM sitems WHERE tipoa='F' AND numa=${dbfactura} AND codigoa=${dbcodigo}");
			$dexpress = $this->datasis->dameval("SELECT SUM(cana) AS cana FROM sitems WHERE tipoa='F' AND numa=${dbfactura} AND codigoa=${dbcodigo} AND  despacha='S'");
			$entregado= $this->datasis->dameval("SELECT SUM(IF(b.tipo='D',-1,1)*a.entrega) AS cana FROM itsnot AS a JOIN snot AS b ON a.numero=b.numero WHERE b.factura=${dbfactura} AND a.codigo=${dbcodigo}");
			$devuelto = $this->datasis->dameval("SELECT SUM(a.cana) AS cana FROM sitems AS a JOIN sfac AS b ON a.numa=b.numero AND a.tipoa=b.tipo_doc WHERE b.tipo_doc='D' AND b.factura=${dbfactura} AND a.codigoa=${dbcodigo}");

			if(empty($facturado)){
				$this->validation->set_message('chitems', 'El articulo '.$codigo.' no pertenece a la factura '.$factura.'.');
				return false;
			}

			if(empty($dexpress)) { $dexpress=0;  }
			if(empty($entregado)){ $entregado=0; }
			if(empty($devuelto)) { $devuelto =0; }
			if(empty($val))      { $val=0;       }

			$saldo    = $facturado-$entregado-$devuelto-$dexpress;
			if($val > $saldo){
				$this->validation->set_message('chitems', 'Esta intentado entregar mas unidades de articulo '.$codigo.' de las que corresponde ( Max. '.($saldo).').');
				return false;
			}
		}
		//$this->validation->set_message('chitems',"$facturado-$entregado-$devuelto");
		return true;
	}

	function chsnte($factura){
		$dbfactura= $this->db->escape($factura);
		$cana=$this->datasis->dameval('SELECT COUNT(*) AS val FROM snte WHERE factura='.$dbfactura);
		if(empty($cana)){
			return true;
		}
		$this->validation->set_message('chsnte', 'La factura que esta intentando despachar tiene una nota de entrega, por lo tanto se asume que ya fue despachada.');
		return false;

	}

	function chrepetidos($cod){
		if(!isset($this->chrepetidos)) $this->chrepetidos=array();
		if(array_search($cod, $this->chrepetidos)===false){
			$this->chrepetidos[]=$cod;
			return true;
		}else{
			$this->validation->set_message('chrepetidos', 'El art&iacute;culo '.$cod.' esta repetido');
			return false;
		}
	}

	function _pre_insert($do){
		$factura  = $do->get('factura');

		$canar= 0;
		$cana = $do->count_rel('itsnot');
		for($i = 0;$i < $cana;$i++){
			$codigoa   = $do->get_rel('itsnot', 'codigo' , $i);
			$itcant    = $do->get_rel('itsnot', 'cant'   , $i);
			$itentrega = $do->get_rel('itsnot', 'entrega', $i);

			if(empty($itentrega)){
				$do->rel_rm('itsnot',$i);
				continue;
			}
			$canar++;
			$do->set_rel('itsnot', 'factura' , $factura, $i);
		}
		if($canar<=0){
			$do->error_message_ar['pre_ins']='Debe tener al menos un articulo para despachar.';
			return false;
		}
		$numero = $this->datasis->fprox_numero('nsnot');
		$do->set('numero',$numero);

		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='No se puede modificar las notas de despacho, debe anularla.';
		return false;
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='No se puede eliminar una nota de despacho.';
		return false;
	}

	function _post_insert($do){
		$codigo=$do->get('numero');
		logusu('snot',"Nota Entrega ${codigo} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('numero');
		logusu('snot',"Nota Entrega ${codigo} MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('numero');
		logusu('snot',"Nota Entrga ${codigo} ELIMINADA");
	}

	function grid(){
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 50;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : '';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;

		$where = $this->datasis->extjsfiltro($filters,'snot');

		$this->db->_protect_identifiers=false;
		$this->db->select('*');
		$this->db->from('snot');

		if (strlen($where)>1){
			$this->db->where($where);
		}

		if ( $sort == '') $this->db->order_by( 'numero', 'desc' );

		$sort = json_decode($sort, true);
		for ($i=0;$i<count($sort);$i++) {
			$this->db->order_by($sort[$i]['property'],$sort[$i]['direction']);
		}
		$sql = $this->db->_compile_select($this->db->_count_string . $this->db->_protect_identifiers('numrows'));
		$results = $this->datasis->dameval($sql);
		$this->db->limit($limit, $start);
		$query = $this->db->get();
		$arr = $this->datasis->codificautf8($query->result_array());

		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}


	function tabla() {
		$id   = isset($_REQUEST['id'])  ? $_REQUEST['id']   :  0;
		$cliente = $this->datasis->dameval("SELECT cod_cli FROM snot WHERE id='$id'");
		$mSQL = "SELECT cod_cli, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos FROM smov WHERE cod_cli='$cliente' AND abonos<>monto AND tipo_doc<>'AB' ORDER BY fecha ";
		$query = $this->db->query($mSQL);
		$salida = '';
		$saldo = 0;
		if ( $query->num_rows() > 0 ){
			$salida = "<br><table width='100%' border=1>";
			$salida .= "<tr bgcolor='#e7e3e7'><td colspan=3>Movimiento en Cuentas X Cobrar</td></tr>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";

			foreach ($query->result_array() as $row)
			{
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

	function griditsnot(){
		$numero   = isset($_REQUEST['numero'])  ? $_REQUEST['numero']   :  0;
		if ($numero == 0 ) $numero = $this->datasis->dameval("SELECT MAX(numero) FROM snot")  ;

		$mSQL = "SELECT * FROM itsnot a JOIN sinv b ON a.codigo=b.codigo WHERE a.numero='$numero' ORDER BY a.codigo";
		$query = $this->db->query($mSQL);
		$results =  0;
		$arr = array();
		foreach ($query->result_array() as $row)
		{
			$meco = array();
			foreach( $row as $idd=>$campo ) {
				$meco[$idd] = utf8_encode($campo);
			}
			$arr[] = $meco;
		}
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}

	function sclibu(){
		$numero = $this->uri->segment(4);
		$id = $this->datasis->dameval("SELECT b.id FROM snot a JOIN scli b ON a.cod_cli=b.cliente WHERE numero='$numero'");
		redirect('ventas/scli/dataedit/show/'.$id);
	}

	function instalar(){
		$campos=$this->db->list_fields('snot');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE snot DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE snot ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE snot ADD UNIQUE INDEX numero (numero)');
		}
	}
}
