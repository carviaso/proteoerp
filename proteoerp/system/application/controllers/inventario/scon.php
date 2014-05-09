<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Scon extends Controller {
	var $mModulo = 'SCON';
	var $titp    = 'Consignación de Inventario';
	var $tits    = 'Consignación de Inventario';
	var $url     = 'inventario/scon/';

	function Scon(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'SCON', $ventana=0 );
	}

	function index(){
		$this->instalar();
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$grid->setHeight('185');
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$grid1->setHeight('190');
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 220, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'imprime',  'img'=>'assets/default/images/print.png','alt' => 'Reimprimir', 'label'=>'Reimprimir Documento'));
		if($this->datasis->sidapuede('SCON','INCLUIR%' )){
			$grid->wbotonadd(array('id'=>'addscli',  'img'=>'images/agrega4.png'             ,'alt' => 'Consignaci&oacute;n a cliente'  , 'label'=>'Consignaci&oacute;n a Cliente'));
			$grid->wbotonadd(array('id'=>'addsprv',  'img'=>'images/agrega4.png'             ,'alt' => 'Consignaci&oacute;n a proveedor', 'label'=>'Consignaci&oacute;n a Proveedor'));
		}

		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']    = $WestPanel;
		$param['script']       = '';
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('SCON', 'JQ');
		$param['otros']        = $this->datasis->otros('SCON', 'JQ');
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
		$bodyscript = '<script type="text/javascript">';

		$bodyscript .= '
		function sconaddscli(){
			$.post("'.site_url($this->url.'dataedit/C/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function sconaddsprv(){
			$.post("'.site_url($this->url.'dataedit/P/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function sconedit(){
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
		function sconshow(){
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
		function scondel() {
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
		jQuery("#imprime").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url('formatos/ver/SCON/').'/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes\');
			} else { $.prompt("<h1>Por favor Seleccione una Registro</h1>");}
		});';

		$bodyscript .= 'jQuery("#addscli").click( function (){ sconaddscli();});';

		$bodyscript .= 'jQuery("#addsprv").click( function (){ sconaddsprv();});';

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
									'.$this->datasis->jwinopen(site_url('formatos/ver/SCON').'/\'+json.pk.id+\'/id\'').';
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


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
			'cellattr'      => 'function(rowId, tv, aData, cm, rdata){
				var tips = "";
				if(aData.tipo !== undefined){
					if(aData.tipo=="C"){
						tips = "Cliente";
					}else if(aData.tipo == "P"){
						tips = "Proveedor";
					}else{
						tips = aData.tipo;
					}
				}
				return \'title="\'+tips+\'"\';
			}'
		));


		$grid->addField('tipod');
		$grid->label('Mov.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
			'cellattr'      => 'function(rowId, tv, aData, cm, rdata){
				var tips = "";
				if(aData.tipod !== undefined){
					if(aData.tipod=="E"){
						tips = "Entrada";
					}else if(aData.tipod == "S"){
						tips = "salida";
					}else{
						tips = aData.tipod;
					}
				}
				return \'title="\'+tips+\'"\';
			}'
		));


		$grid->addField('status');
		$grid->label('Estatus');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('almacen');
		$grid->label('Almacen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('asociado');
		$grid->label('Asociado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('clipro');
		$grid->label('Cli/Pro');
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


		$grid->addField('direc1');
		$grid->label('Direci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('direc2');
		$grid->label('Direci&oacute;n 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('observ1');
		$grid->label('Observaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:33, maxlength: 33 }',
		));


		$grid->addField('observ2');
		$grid->label('Observaci&oacute;n 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:33, maxlength: 33 }',
		));


		$grid->addField('stotal');
		$grid->label('SubTotal');
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


		$grid->addField('impuesto');
		$grid->label('Impuesto');
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


		$grid->addField('gtotal');
		$grid->label('Total');
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


		$grid->addField('origen');
		$grid->label('Origen');
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
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
				if (id){
					jQuery(gridId2).jqGrid("setGridParam",{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
				}
			}'
		);
		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(false);
		$grid->setDelete( $this->datasis->sidapuede('SCON','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('SCON','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: sconaddscli, editfunc: sconedit, delfunc: scondel, viewfunc: sconshow');

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
		$mWHERE = $grid->geneTopWhere('scon');

		$response   = $grid->getData('scon', array(array()), array(), false, $mWHERE, 'id','desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
		//$this->load->library('jqdatagrid');
		//$oper   = $this->input->post('oper');
		//$id     = $this->input->post('id');
		//$data   = $_POST;
		//$mcodp  = "??????";
		//$check  = 0;
        //
		//unset($data['oper']);
		//unset($data['id']);
		//if($oper == 'add'){
		//	if(false == empty($data)){
		//		$check = $this->datasis->dameval("SELECT count(*) FROM scon WHERE $mcodp=".$this->db->escape($data[$mcodp]));
		//		if ( $check == 0 ){
		//			$this->db->insert('scon', $data);
		//			echo "Registro Agregado";
        //
		//			logusu('SCON',"Registro ????? INCLUIDO");
		//		} else
		//			echo "Ya existe un registro con ese $mcodp";
		//	} else
		//		echo "Fallo Agregado!!!";
        //
		//} elseif($oper == 'edit') {
		//	$nuevo  = $data[$mcodp];
		//	$anterior = $this->datasis->dameval("SELECT $mcodp FROM scon WHERE id=$id");
		//	if ( $nuevo <> $anterior ){
		//		//si no son iguales borra el que existe y cambia
		//		$this->db->query("DELETE FROM scon WHERE $mcodp=?", array($mcodp));
		//		$this->db->query("UPDATE scon SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
		//		$this->db->where("id", $id);
		//		$this->db->update("scon", $data);
		//		logusu('SCON',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
		//		echo "Grupo Cambiado/Fusionado en clientes";
		//	} else {
		//		unset($data[$mcodp]);
		//		$this->db->where("id", $id);
		//		$this->db->update('scon', $data);
		//		logusu('SCON',"Grupo de Cliente  ".$nuevo." MODIFICADO");
		//		echo "$mcodp Modificado";
		//	}
        //
		//} elseif($oper == 'del') {
		//	$meco = $this->datasis->dameval("SELECT $mcodp FROM scon WHERE id=$id");
		//	//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM scon WHERE id='$id' ");
		//	if ($check > 0){
		//		echo " El registro no puede ser eliminado; tiene movimiento ";
		//	} else {
		//		$this->db->simple_query("DELETE FROM scon WHERE id=$id ");
		//		logusu('SCON',"Registro ????? ELIMINADO");
		//		echo "Registro Eliminado";
		//	}
		//};
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


		$grid->addField('desca');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
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


		$grid->addField('recibido');
		$grid->label('Recibido');
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


		/*$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));*/


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
		if ($id === 0 ){
			$id = $this->datasis->dameval("SELECT MAX(id) FROM scon");
		}
		if(empty($id)) return '';
		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM itscon WHERE id_scon=${id}";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setDatait(){
	}

	//***********************************
	// DataEdit
	//***********************************

	function dataedit($opttipo){
		$opt_key = array_search($opttipo,array('C','P'));
		if($opt_key===false){
			show_404('');
		}
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
		'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
		'p_uri'   =>array(4=>'<#i#>'),
		'where'   => '`activo` = "S" AND `tipo` = "Articulo"',
		'script'  => array('post_modbus_sinv(<#i#>)'),
		'titulo'  =>'Buscar Art&iacute;culo');

		if($opttipo=='C'){
			$mCLIPRO=array(
			'tabla'   =>'scli',
			'columnas'=>array(
				'cliente' =>'C&oacute;digo Cliente',
				'nombre'=>'Nombre',
				'cirepre'=>'Rif/Cedula',
				'dire11'=>'Direcci&oacute;n',
				'tipo'=>'Tipo'),
			'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
			'retornar'=>array('cliente'=>'clipro','nombre'=>'nombre',
							'dire11'=>'direc1','tipo'=>'cliprotipo'),
			'titulo'  =>'Buscar Cliente',
			'script'  => array('post_modbus_scli()'));

			$modbus['retornar']=array(
				'codigo' =>'codigo_<#i#>',
				'descrip'=>'desca_<#i#>',
				'base1'  =>'precio1_<#i#>',
				'base2'  =>'precio2_<#i#>',
				'base3'  =>'precio3_<#i#>',
				'base4'  =>'precio4_<#i#>',
				'iva'    =>'itiva_<#i#>',
				'peso'   =>'sinvpeso_<#i#>',
				'tipo'   =>'sinvtipo_<#i#>',
			);
		}else{
			$mCLIPRO=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'=>'Nombre',
				'direc1'=>'Direcci&oacute;n',
				'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'clipro','nombre'=>'nombre',
							'direc1'=>'direc1'),
			'titulo'  =>'Buscar Proveedor');

			$modbus['retornar']=array(
				'codigo' =>'codigo_<#i#>',
				'descrip'=>'desca_<#i#>',
				'base1'  =>'precio1_<#i#>',
				'base2'  =>'precio2_<#i#>',
				'base3'  =>'precio3_<#i#>',
				'base4'  =>'precio4_<#i#>',
				'iva'    =>'itiva_<#i#>',
				'peso'   =>'sinvpeso_<#i#>',
				'tipo'   =>'sinvtipo_<#i#>',
			);
		}
		$btnc =$this->datasis->modbus($mCLIPRO);
		$btn  =$this->datasis->p_modbus($modbus,'<#i#>');

		$do = new DataObject('scon');
		$do->rel_one_to_many('itscon', 'itscon', array('id'=>'id_scon'));
		if($opttipo=='C'){
			$do->pointer('scli' ,'scli.cliente=scon.clipro','scli.tipo AS cliprotipo','left');
			$do->rel_pointer('itscon','sinv','itscon.codigo=sinv.codigo','sinv.descrip AS sinvdescrip, sinv.base1 AS sinvprecio1, sinv.base2 AS sinvprecio2, sinv.base3 AS sinvprecio3, sinv.base4 AS sinvprecio4, sinv.iva AS sinviva, sinv.peso AS sinvpeso,sinv.tipo AS sinvtipo');
		}else{
			//$do->pointer('sprv' ,'sprv.proveed=psinv.clipro','"1" AS `cliprotipo`','left');
			$do->rel_pointer('itscon','sinv','itscon.codigo=sinv.codigo','sinv.descrip AS sinvdescrip, sinv.ultimo AS sinvprecio1, sinv.ultimo AS sinvprecio2, sinv.ultimo AS sinvprecio3, sinv.ultimo AS sinvprecio4, sinv.iva AS sinviva, sinv.peso AS sinvpeso,sinv.tipo AS sinvtipo');
		}

		$edit = new DataDetails('Inventario a consignaci&oacute;n', $do);
		$edit->on_save_redirect=false;
		$edit->set_rel_title('itscon','Producto <#o#>');

		$edit->pre_process('insert' ,'_pre_insert');
		$edit->pre_process('update' ,'_pre_update');
		$edit->pre_process('delete' ,'_pre_delete');
		$edit->post_process('insert','_post_insert');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'required';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 12;
		$edit->fecha->calendar=false;

		$edit->tipod = new dropdownField('Tipo de movimiento', 'tipod');
		$edit->tipod->option('E','Entregado');
		$edit->tipod->option('R','Recibido');
		$edit->tipod->rule ='required';
		$edit->tipod->insertValue= ($opttipo=='C') ? 'E' : 'R';
		$edit->tipod->style='width:120px';

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 12;
		$edit->numero->mode='autohide';
		$edit->numero->maxlength=8;
		//$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->peso = new inputField('Peso', 'peso');
		$edit->peso->css_class = 'inputnum';
		$edit->peso->readonly  = true;
		$edit->peso->size      = 10;
		$edit->peso->type ='inputhidden';

		$edit->clipro = new inputField(($opttipo=='C') ? 'Cliente':'Proveedor','clipro');
		$edit->clipro->size = 6;
		$edit->clipro->maxlength=5;
		$edit->clipro->rule = 'required';
		$edit->clipro->append($btnc);

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->size = 25;
		$edit->nombre->maxlength=40;
		$edit->nombre->type ='inputhidden';
		$edit->nombre->autocomplete=false;

		$edit->dir_clipro = new inputField('Direcci&oacute;n','direc1');
		$edit->dir_clipro->size = 37;
		$edit->dir_clipro->type ='inputhidden';

		$edit->asociado = new inputField('Doc. Asociado', 'asociado');
		$edit->asociado->mode='autohide';
		$edit->asociado->size = 10;

		$edit->almacen = new  dropdownField ('Almac&eacute;n', 'almacen');
		$edit->almacen->options('SELECT ubica, CONCAT(ubica,\' \',ubides) nombre FROM caub ORDER BY ubica');
		$edit->almacen->rule = 'required';
		$edit->almacen->style='width:200px;';
		$edit->almacen->size = 5;

		$edit->observ1 = new inputField('Observaci&oacute;n', 'observ1');
		$edit->observ1->size = 37;

		//Para saber que precio se le va a dar al cliente
		$edit->cliprotipo = new hiddenField('', 'cliprotipo');
		$edit->cliprotipo->db_name     = 'cliprotipo';
		$edit->cliprotipo->pointer     = true;
		$edit->cliprotipo->insertValue = 1;

		//Campos para el detalle
		$edit->codigo = new inputField('C&oacute;digo <#o#>', 'codigo_<#i#>');
		$edit->codigo->size     = 12;
		$edit->codigo->db_name  = 'codigo';
		$edit->codigo->onkeyup   = 'OnEnter(event,<#i#>)';
		$edit->codigo->autocomplete=false;
		$edit->codigo->rel_id   = 'itscon';
		$edit->codigo->rule     = 'required';
		$edit->codigo->append($btn);

		$edit->desca = new inputField('Descripci&oacute;n <#o#>', 'desca_<#i#>');
		$edit->desca->size=34;
		$edit->desca->db_name='desca';
		$edit->desca->maxlength=50;
		$edit->desca->readonly  = true;
		$edit->desca->rel_id='itscon';

		$edit->cana = new inputField('Cantidad <#o#>', 'cana_<#i#>');
		$edit->cana->db_name  = 'cana';
		$edit->cana->css_class= 'inputnum';
		$edit->cana->rel_id   = 'itscon';
		$edit->cana->maxlength= 10;
		$edit->cana->size     = 6;
		$edit->cana->rule     = 'required|positive';
		$edit->cana->autocomplete=false;
		$edit->cana->onkeyup  ='importe(<#i#>)';

		$edit->precio = new inputField('Precio <#o#>', 'precio_<#i#>');
		$edit->precio->db_name   = 'precio';
		$edit->precio->css_class = 'inputnum';
		$edit->precio->rel_id    = 'itscon';
		$edit->precio->size      = 10;
		if($opttipo=='C'){
			$edit->precio->rule      = 'required|positive|callback_chpreca[<#i#>]';
		}else{
			$edit->precio->rule      = 'required|positive';
		}
		$edit->precio->readonly  = true;

		$edit->importe = new inputField('Importe <#o#>', 'importe_<#i#>');
		$edit->importe->db_name='importe';
		$edit->importe->size=10;
		$edit->importe->css_class='inputnum';
		$edit->importe->rel_id   ='itscon';

		for($i=1;$i<=4;$i++){
			$obj='precio'.$i;
			$edit->$obj = new hiddenField('Precio <#o#>', $obj.'_<#i#>');
			$edit->$obj->db_name   = 'sinv'.$obj;
			$edit->$obj->rel_id    = 'itscon';
			$edit->$obj->pointer   = true;
		}
		$edit->itiva = new hiddenField('', 'itiva_<#i#>');
		$edit->itiva->db_name  = 'iva';
		$edit->itiva->rel_id   = 'itscon';

		$edit->sinvpeso = new hiddenField('', 'sinvpeso_<#i#>');
		$edit->sinvpeso->db_name   = 'sinvpeso';
		$edit->sinvpeso->rel_id    = 'itscon';
		$edit->sinvpeso->pointer   = true;

		$edit->sinvtipo = new hiddenField('', 'sinvtipo_<#i#>');
		$edit->sinvtipo->db_name   = 'sinvtipo';
		$edit->sinvtipo->rel_id    = 'itscon';
		$edit->sinvtipo->pointer   = true;
		//fin de campos para detalle

		$edit->impuesto  = new inputField('Impuesto', 'impuesto');
		$edit->impuesto->size = 20;
		$edit->impuesto->css_class='inputnum';
		$edit->impuesto->type ='inputhidden';

		$edit->stotal  = new inputField('Sub.Total', 'stotal');
		$edit->stotal->size = 20;
		$edit->stotal->css_class='inputnum';
		$edit->stotal->type ='inputhidden';

		$edit->gtotal  = new inputField('Total', 'gtotal');
		$edit->gtotal->size = 20;
		$edit->gtotal->css_class='inputnum';
		$edit->gtotal->type ='inputhidden';

		$edit->tipo = new autoUpdateField('tipo',$opttipo,$opttipo);

		//$edit->buttons('save', 'undo', 'back','add_rel');
		$edit->build();

		$inven=array();
		if($opttipo=='C'){
			$titulo='Consignaci&oacute;n a Cliente';
			//$query=$this->db->query('SELECT TRIM(codigo) AS codigo ,TRIM(descrip) AS descrip,tipo,base1,base2,base3,base4,iva,peso,precio1,pond FROM sinv WHERE activo=\'S\'');
			$edit->tipo  = new autoUpdateField('tipo','C','C');
		}else{
			$titulo='Consignaci&oacute;n a Proveedor';
			//$query=$this->db->query('SELECT TRIM(codigo) AS codigo ,TRIM(descrip) AS descrip,tipo,ultimo AS base1,ultimo AS base2,ultimo AS base3,ultimo AS base4,iva,peso,precio1,pond FROM sinv WHERE activo=\'S\'');
			$edit->tipo  = new autoUpdateField('tipo','R','R');
		}


		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['opttipo']= $opttipo;
			$conten['form']   = & $edit;
			$this->load->view('view_scon', $conten);
		}

	}

	function _pre_insert($do){
		$tipo=$do->get('tipo');
		if($tipo=='C'){
			$numero = $this->datasis->fprox_numero('nsconc');
		}else{
			$numero = $this->datasis->fprox_numero('nsconp');
		}

		$fecha  = $do->get('fecha');

		$iva=$stotal=0;
		$cana=$do->count_rel('itscon');
		for($i=0;$i<$cana;$i++){
			$itcana    = $do->get_rel('itscon','cana',$i);
			$itprecio  = $do->get_rel('itscon','precio',$i);
			$itiva     = $do->get_rel('itscon','iva',$i);
			$itimporte = $itprecio*$itcana;
			$iiva      = $itimporte*($itiva/100);

			$do->set_rel('itscon','importe',$itimporte,$i);
			$do->set_rel('itscon','numero' ,$numero   ,$i);

			$iva    +=$iiva ;
			$stotal +=$itimporte;
		}

		$gtotal=$stotal+$iva;
		$do->set('numero'  ,$numero);
		$do->set('stotal'  ,round($stotal,2));
		$do->set('gtotal'  ,round($gtotal,2));
		$do->set('impuesto',round($iva   ,2));
		$do->set('status'  ,'T');

		return true;
	}

	function _post_insert($do){
		$tipod  = $do->get('tipod');
		$codigo = $do->get('numero');
		$id     = $do->get('id');
		$almacen= $do->get('almacen');
		$tipo   = $do->get('tipo');
		$fact   = ($tipod=='E') ? -1 : 1;

		$cana=$do->count_rel('itscon');
		for($i=0;$i<$cana;$i++){
			$itcodigoa = $do->get_rel('itscon','codigo',$i);
			$itcana    = $do->get_rel('itscon','cana'  ,$i);
			$this->datasis->sinvcarga($itcodigoa, $almacen, $fact*$itcana);
		}

		$codigo=$do->get('numero');
		logusu('scon',"Prestamo de inventario ${codigo} CREADO");
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='Deshabilitado';
		return false;
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='Deshabilitado';
		return false;
	}


	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
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

	function instalar(){
		if(!$this->db->table_exists('scon')){
			$mSQL="CREATE TABLE `scon` (
			  `numero` char(8) DEFAULT NULL,
			  `fecha` date DEFAULT NULL,
			  `tipo` char(1) DEFAULT NULL,
			  `tipod` char(1) DEFAULT NULL,
			  `status` char(1) DEFAULT 'T',
			  `asociado` char(8) DEFAULT NULL,
			  `clipro` char(5) DEFAULT NULL,
			  `almacen` char(4) DEFAULT NULL,
			  `nombre` char(40) DEFAULT NULL,
			  `direc1` char(40) DEFAULT NULL,
			  `direc2` char(40) DEFAULT NULL,
			  `observ1` char(33) DEFAULT NULL,
			  `observ2` char(33) DEFAULT NULL,
			  `stotal` decimal(12,2) DEFAULT NULL,
			  `impuesto` decimal(12,2) DEFAULT NULL,
			  `gtotal` decimal(12,2) DEFAULT NULL,
			  `peso` decimal(10,3) DEFAULT NULL,
			  `origen` char(1) NOT NULL DEFAULT 'L',
			  `id` int(15) unsigned NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `numero` (`numero`,`tipo`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('itscon')){
			$mSQL="CREATE TABLE `itscon` (
			  `numero` char(8) DEFAULT NULL,
			  `codigo` varchar(15) DEFAULT NULL,
			  `desca` varchar(40) DEFAULT NULL,
			  `cana` decimal(5,0) DEFAULT NULL,
			  `recibido` decimal(5,0) DEFAULT NULL,
			  `precio` decimal(12,2) DEFAULT NULL,
			  `importe` decimal(12,2) DEFAULT NULL,
			  `iva` decimal(8,2) DEFAULT NULL,
			  `id_scon` int(15) unsigned NOT NULL,
			  `id` int(15) unsigned NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  KEY `id_scon` (`id_scon`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('scon');
		if(!in_array('origen',$campos)){
			$mSQL="ALTER TABLE scon ADD COLUMN origen CHAR(1) NOT NULL DEFAULT 'L' AFTER peso";
			$this->db->simple_query($mSQL);
		}


		//$campos=$this->db->list_fields('itscon');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}

/*
class scon extends Controller {

	function scon(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('320',1);
		$this->back_dataedit='inventario/scon/index';
	}

	function index() {
		$this->rapyd->load('datagrid','datafilter');

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'   => "'+((screen.availWidth/2)-400)+'",
			'screeny'   => "'+((screen.availHeight/2)-300)+'"
		);

		$scli=array(
		'tabla'   =>'scli',
		'columnas'=>array(
		'cliente' =>'C&oacute;digo Cliente',
		'nombre'=>'Nombre',
		'contacto'=>'Contacto'),
		'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'cod_cli'),
		'titulo'  =>'Buscar Cliente');

		$boton=$this->datasis->modbus($scli);


		$filter = new DataFilter('Filtro de consignaciones','scon');

		$filter->fechad = new dateonlyField('Desde', 'fechad');
		$filter->fechah = new dateonlyField('Hasta', 'fechah');
		$filter->fechad->clause  =$filter->fechah->clause ='where';
		$filter->fechad->db_name =$filter->fechah->db_name='fecha';
		$filter->fechah->size=$filter->fechad->size=10;
		$filter->fechad->operator='>=';
		$filter->fechah->operator='<=';

		$filter->numero = new inputField('N&uacute;mero', 'numero');
		$filter->numero->size = 30;

		$filter->factura = new inputField('Factura', 'factura');
		$filter->factura->size = 30;

		$filter->cliente = new inputField('Cliente/Proveedor','clipro');
		$filter->cliente->size = 30;
		$filter->cliente->append($boton);

		$filter->buttons('reset','search');
		$filter->build();

		$uri  = anchor('inventario/scon/dataedit/<#tipo#>/show/<#id#>','<#numero#>');
		$uri2 = anchor_popup('formatos/verhtml/PSINV/<#id#>','Ver HTML',$atts);

		function asoc($id,$origen,$asociado,$clipro,$numero){
			$asociado=trim($asociado);
			if($origen=='L'){
				$atts = array(
					'width'      => '400',
					'height'     => '300',
					'scrollbars' => 'yes',
					'status'     => 'yes',
					'resizable'  => 'yes',
					'screenx'   => "'+((screen.availWidth/2)-200)+'",
					'screeny'   => "'+((screen.availHeight/2)-150)+'"
				);
				if(empty($asociado)){
					$asociado='Ninguno';
				}
				$acti =anchor_popup('/inventario/scon/traeasoc/'.raencode($id).'/'.raencode($clipro).'/'.raencode($numero) ,$asociado,$atts);
			}else{
				$acti=$asociado;
			}
			return $acti;
		}

		$grid = new DataGrid();
		$grid->use_function('asoc');
		$grid->order_by('numero','desc');
		$grid->per_page = 15;

		$grid->column_orderby('N&uacute;mero' ,$uri,'numero');
		$grid->column_orderby('Asociado'      ,'<asoc><#id#>|<#origen#>|<#asociado#>|<#clipro#>|<#numero#></asoc>','asociado');
		$grid->column_orderby('Fecha'         ,'<dbdate_to_human><#fecha#></dbdate_to_human>','fecha','align=\'center\'');
		$grid->column_orderby('Nombre'        ,'nombre','nombre');
		$grid->column_orderby('Mov.'          ,'tipod','tipod');
		$grid->column_orderby('Sub.Total'     ,'<nformat><#stotal#></nformat>'  ,'stotal','align=\'right\'');
		$grid->column_orderby('IVA'           ,'<nformat><#impuesto#></nformat>','iva'   ,'align=\'right\'');
		$grid->column_orderby('Total'         ,'<nformat><#gtotal#></nformat>'  ,'gtotal','align=\'right\'');
		//$grid->column_orderby("Vista",$uri2,"align='center'");

		$grid->add('inventario/scon/agregar');
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] = $filter->output.$grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Consignaci&oacute;n de inventario');
		$this->load->view('view_ventanas', $data);
	}

	function agregar(){

		$ul=array();
		$ul[] = anchor('inventario/scon/dataedit/P/create','Consignaci&oacute;n de <b>proveedor</b>').': Recibir o Devolver mercanc&iacute;a a proveedor.';
		//$ul[] = anchor('inventario/scon/dataedit/P/create','Devolver Consignacion recibida por proveedor');
		$ul[] = anchor('inventario/scon/dataedit/C/create','Consignaci&oacute;n de <b>cliente</b>').': Recibir o Devolver mercanc&iacute;a a cliente.';
		//$ul[] = anchor('inventario/scon/dataedit/C/create','Devolver Consignacion dada a cliente');

		$data['content'] = heading('Seleccione una modalidad:',2);
		$data['content'].= ul($ul).anchor('inventario/scon/index','Regresar');
		$data['title']   = heading('Inventario a consignaci&oacute;n');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function devoscon(){
		$this->rapyd->load('datagrid','datafilter');

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$scli=array(
		'tabla'   =>'scli',
		'columnas'=>array(
		'cliente' =>'C&oacute;digo Cliente',
		'nombre'=>'Nombre',
		'contacto'=>'Contacto'),
		'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'cod_cli'),
		'titulo'  =>'Buscar Cliente');

		$boton=$this->datasis->modbus($scli);

		$filter = new DataFilter('Filtro de consignaciones','scon');
		$filter->db->select(array('a.clipro','a.nombre','SUM(IF(a.tipod=\'E\',1,-1)*a.gtotal) AS saldo'));
		$filter->db->from('scon AS a');
		$filter->db->join('itscon AS b','a.id=b.id_scon');
		$filter->db->where('a.tipo','C');
		$filter->db->groupby('a.clipro');

		$filter->cliente = new inputField('Cliente/Proveedor','clipro');
		$filter->cliente->size = 30;
		$filter->cliente->append($boton);

		$filter->buttons('reset','search');
		$filter->build();

		$grid = new DataGrid();
		$grid->order_by('numero','desc');
		$grid->per_page = 15;

		$grid->column_orderby('Cliente' ,'(<#clipro#>)-<#nombre#>','nombre');
		$grid->column_orderby('Saldo'   ,'<nformat><#stotal#></nformat>'  ,'stotal','align=\'right\'');
		//$grid->column_orderby('Editar'   ,'<nformat><#gtotal#></nformat>'  ,'gtotal');
		//$grid->column_orderby("Vista",$uri2,"align='center'");

		$grid->add('inventario/scon/agregar');
		$grid->build();
		echo $grid->db->last_query();

		$data['content'] = $filter->output.$grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Consignaci&oacute;n de inventario');
		$this->load->view('view_ventanas', $data);
	}

	function traeasoc($id,$scli,$numero){
		$num=$this->_traerasociado($scli,$numero);
		if($num===false){
			echo '<center>No se encontro n&uacute;mero asociado</center>';
		}
		elseif(empty($num)){
			echo '<center>No se encontro n&uacute;mero asociado, probablemente no fue cargado en la sucursal</center>';
			$dbid  =$this->db->escape($id);
			$sql = $this->db->update_string('scon',array('asociado' => $num),"id = $dbid");
			$this->db->simple_query($sql);
		}else{
			$dbid  =$this->db->escape($id);
			$sql = $this->db->update_string('scon',array('asociado' => $num),"id = $dbid");
			$this->db->simple_query($sql);
			echo '<center>El N&uacute;mero Asociado es:'.$num.'</center>';
		}
	}

	function _traerasociado($scli,$numero){
		$dbscli=$this->db->escape($scli);

		$sql="SELECT b.proveed,b.grupo,b.puerto,b.proteo,b.url,b.usuario,b.clave,b.tipo,b.depo,b.margen1,b.margen2,b.margen3,b.margen4,b.margen5 FROM sprv AS a JOIN b2b_config AS b ON a.proveed=b.proveed WHERE a.cliente=${dbscli}";
		$config=$this->datasis->damerow($sql);
		if(count($config)==0) return false;

		$er=0;
		$this->load->helper('url');
		$server_url = reduce_double_slashes($config['url'].'/'.$config['proteo'].'/'.'rpcserver');

		$this->load->library('xmlrpc');
		$this->xmlrpc->xmlrpc_defencoding=$this->config->item('charset');
		//$this->xmlrpc->set_debug(TRUE);
		$puerto= (empty($config['puerto'])) ? 80 : $config['puerto'];

		$this->xmlrpc->server($server_url , $puerto);
		$this->xmlrpc->method('consinu');

		$request = array($numero,$config['proveed'],$config['usuario'],md5($config['clave']));
		$this->xmlrpc->request($request);

		if (!$this->xmlrpc->send_request()){
			memowrite($this->xmlrpc->display_error(),'scon');
			return false;
		}else{
			$res=$this->xmlrpc->display_response();
			if(isset($res[0]))
				return $res[0];
			else
				return null;
		}
		return null;
	}

	function _pre_update($do){
		return false;
	}

	function instalar(){

	}*/
