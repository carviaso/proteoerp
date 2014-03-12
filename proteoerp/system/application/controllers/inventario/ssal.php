<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Ssal extends Controller {
	var $mModulo = 'SSAL';
	var $titp    = 'Ajustes de Inventario';
	var $tits    = 'Ajustes de Inventario';
	var $url     = 'inventario/ssal/';

	function Ssal(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'SSAL', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->creaintramenu(array('modulo'=>'326','titulo'=>'Ajustes de Inventario','mensaje'=>'Ajustes de Inventario','panel'=>'TRANSACCIONES','ejecutar'=>'inventario/ssal','target'=>'popu','visible'=>'S','pertenece'=>'3','ancho'=>800,'alto'=>600));
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

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 165, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'boton1',  'img'=>'assets/default/images/print.png','alt' => 'Formato PDF',      'label'=>'Reimprimir Documento'));
		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = "radicional", $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar Ajuste de Inventario'),
			array('id'=>'fshow' ,  'title'=>'Ver Ajuste de Inventario')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '
		function ltransac(el, val, opts){
			var link=\'<div><a href="#" onclick="tconsulta(\'+"\'"+el+"\'"+\');">\' +el+ \'</a></div>\';
			return link;
		};';

		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('SSAL', 'JQ');
		$param['otros']        = $this->datasis->otros('SSAL', 'JQ');
		$param['centerpanel']  = $centerpanel;
		$param['funciones']    = $funciones;
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
		jQuery("#boton1").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('formatos/ver/SSAL').'/\'+id+"/id"').';
			} else { $.prompt("<h1>Por favor Seleccione una Factura</h1>");}
		});';


		$bodyscript .= '
		function ssaladd() {
			$.post("'.site_url('inventario/ssal/dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function ssalshow() {
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
		function ssaledit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url('inventario/ssal/dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
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
		$("#fedita").dialog({
			autoOpen: false, height: 500, width: 840, modal: true,
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
							if ( r.length == 0 ) {
								apprise("Registro Guardado");
								$( "#fedita" ).dialog( "close" );
								grid.trigger("reloadGrid");
								'.$this->datasis->jwinopen(site_url('formatos/ver/SSAL').'/\'+res.id+\'/id\'').';
								return true;
							} else {
								$("#fedita").html(r);
							}
						}
				})},
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
				allFields.val( "" ).removeClass( "ui-state-error" );
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
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
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


		$grid->addField('cargo');
		$grid->label('Cargo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
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


		$grid->addField('motivo');
		$grid->label('Motivo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
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


		$grid->addField('transac');
		$grid->label('Transaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
			'formatter'     => 'ltransac'
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

/*
		$grid->addField('depto');
		$grid->label('Depto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:3, maxlength: 3 }',
		));
*/

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
			}'
		);

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setOndblClickRow('');
		$grid->setAdd(    $this->datasis->sidapuede('SSAL','INCLUIR%' ));
		$grid->setEdit( false );    //  $this->datasis->sidapuede('SSAL','MODIFICA%'));
		$grid->setDelete( false );  //  $this->datasis->sidapuede('SSAL','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('SSAL','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: ssaladd,editfunc: ssaledit,viewfunc: ssalshow');

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
		$mWHERE = $grid->geneTopWhere('ssal');

		$response   = $grid->getData('ssal', array(array()), array(), false, $mWHERE, 'id', 'desc' );
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
		$mcodp  = "numero";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM ssal WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					//$this->db->insert('ssal', $data);
					//echo "Registro Agregado";

					logusu('SSAL',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$numero = $this->datasis->dameval("SELECT $mcodp FROM ssal WHERE id=$id");
			unset($data['numero']);
			$this->db->where("id", $id);
			$this->db->update('ssal', $data);
			logusu('SSAL',"Ajustes de Inventario  ".$numero." MODIFICADO");
			echo "Ajuste $numero Modificado";

		} elseif($oper == 'del') {
			//$meco = $this->datasis->dameval("SELECT $mcodp FROM ssal WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM ssal WHERE id='$id' ");
			//if ($check > 0){
			//	echo " El registro no puede ser eliminado; tiene movimiento ";
			//} else {
				//$this->db->simple_query("DELETE FROM ssal WHERE id=$id ");
				//logusu('SSAL',"Registro ????? ELIMINADO");
				echo "los Ajustes no se Eliminan; debe hacer un reverso";
			//}
		};
	}



	function tabla( $id = 0 ) {
		$transac = $this->datasis->dameval("SELECT transac FROM ssal WHERE id='$id'");
		$salida = '';

		// Revisa formas de pago sfpa
		$mSQL = "SELECT * from gser WHERE transac='$transac' ";
		$query = $this->db->query($mSQL);
		if ( $query->num_rows() > 0 ){
			$salida .= "<br><table width='100%' border=1>";
			$salida .= "<tr bgcolor='#e7e3e7'><td colspan=3>Gasto</td></tr>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tipo</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row)
			{
				$salida .= "<tr>";
				$salida .= "<td>".$row['tipo_doc']."</td>";
				$salida .= "<td>".$row['numero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['totneto']).   "</td>";
				$salida .= "</tr>";
			}
			$salida .= "</table>";
		}

		// Cuentas por Cobrar
		$mSQL = "SELECT * FROM otin WHERE transac='$transac' ";
		$query = $this->db->query($mSQL);
		$saldo = 0;
		if ( $query->num_rows() > 0 ){
			$salida .= "<br><table width='100%' border=1>";
			$salida .= "<tr bgcolor='#e7e3e7'><td colspan=3>Otros Ingresos</td></tr>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
			$i = 1;
			foreach ($query->result_array() as $row){
					$salida .= "<tr>";
					$salida .= "<td>".$row['tipo_doc']."</td>";
					$salida .= "<td>".$row['numero'].  "</td>";
					$salida .= "<td align='right'>".nformat($row['totalg']).   "</td>";
					$salida .= "</tr>";
			}
			$salida .= '</table>';
		}
		$query->free_result();

		echo $salida;
	}




	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

/*
		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));
*/

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('cantidad');
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

/*
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


		$grid->addField('transac');
		$grid->label('Transac');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));
*/

		$grid->addField('gasto');
		$grid->label('Gasto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));


		$grid->addField('concepto');
		$grid->label('Concepto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
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

/*
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
*/

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('130');
		$grid->setTitle('');
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd( false);      //   $this->datasis->sidapuede('ITSSAL','INCLUIR%' ));
		$grid->setEdit( false );    //   $this->datasis->sidapuede('ITSSAL','MODIFICA%'));
		$grid->setDelete( false );  //   $this->datasis->sidapuede('ITSSAL','BORR_REG%'));
		$grid->setSearch( false );  //$this->datasis->sidapuede('ITSSAL','BUSQUEDA%'));
		$grid->setRowNum(90);
		$grid->setShrinkToFit('false');

		//$grid->setBarOptions("\t\taddfunc: itssaladd,\n\t\teditfunc: itssaledit");

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
	function getdatait($id = 0){

		if($id == 0){
			$id = $this->datasis->dameval("SELECT MAX(id) AS id FROM ssal");
		}
		$dbid = intval($id);
		if(empty($dbid)) return '';

		$numero   = $this->datasis->dameval("SELECT numero FROM ssal WHERE id=${dbid}");
		$dbnumero = $this->db->escape($numero);

		$orderby= '';
		$sidx=$this->input->post('sidx');
		if($sidx){
			$campos = $this->db->list_fields('itssal');
			if(in_array($sidx,$campos)){
				$sidx = trim($sidx);
				$sord   = $this->input->post('sord');
				$orderby="ORDER BY `${sidx}` ".(($sord=='asc')? 'ASC':'DESC');
			}
		}

		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM itssal WHERE numero=${dbnumero} ${orderby}";
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
		$mcodp  = "??????";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM itssal WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('itssal', $data);
					echo "Registro Agregado";

					logusu('ITSSAL',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM itssal WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM itssal WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE itssal SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("itssal", $data);
				logusu('ITSSAL',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('itssal', $data);
				logusu('ITSSAL',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM itssal WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM itssal WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM itssal WHERE id=$id ");
				logusu('ITSSAL',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}


	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');

		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo'  =>'C&oacute;digo',
				'descrip' =>'Descripci&oacute;n',
				'ultimo' =>'Costo',
				),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array(
				'codigo' =>'codigo_<#i#>',
				'descrip'=>'itdescrip_<#i#>',
				'ultimo' =>'costo_<#i#>'
			),
			'p_uri'   => array(4=>'<#i#>'),
			'titulo'  => 'Buscar Art&iacute;culo',
			'where'   => '`activo` = "S" AND tipo="Articulo"',
		);
		$btn=$this->datasis->p_modbus($modbus,'<#i#>');


		$modbusic=array(
			'tabla'   =>'icon',
			'columnas'=>array(
				'codigo'   =>'C&oacute;digo',
				'concepto' =>'Descripci&oacute;n',
				'tipo'=>'Tipo'
			),
			'filtro'  =>array('codigo' =>'C&oacute;digo','concepto'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo' =>'concepto_<#i#>'),
			'p_uri'   => array(4=>'<#i#>'),
			'titulo'  => 'Buscar Art&iacute;culo',
			'where'   => '`tipo` = "E"',
		);
		$btnc1=$this->datasis->p_modbus($modbusic,'<#i#>');

		$modbusic2=array(
			'tabla'   =>'icon',
			'columnas'=>array(
				'codigo'   =>'C&oacute;digo',
				'concepto' =>'Descripci&oacute;n',
				'tipo'=>'Tipo'
			),
			'filtro'  =>array('codigo' =>'C&oacute;digo','concepto'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo' =>'concepto_<#i#>'),
			'p_uri'   => array(4=>'<#i#>'),
			'titulo'  => 'Buscar Art&iacute;culo',
			'where'   => '`tipo` = "I"',
		);
		$btnc2=$this->datasis->p_modbus($modbusic2,'<#i#>',800,600,'iconI');

		$do = new DataObject('ssal');
		$do->rel_one_to_many('itssal', 'itssal', 'numero');
		$do->pointer('caub' ,'caub.ubica=ssal.almacen','ubides AS caububides','left');
		$do->rel_pointer('itssal','sinv','itssal.codigo=sinv.codigo','sinv.descrip AS sinvdescrip, sinv.base1 AS sinvprecio1, sinv.base2 AS sinvprecio2, sinv.base3 AS sinvprecio3, sinv.base4 AS sinvprecio4, sinv.iva AS sinviva, sinv.peso AS sinvpeso,sinv.tipo AS sinvtipo');

		$edit = new DataDetails('Entradas y Salidas', $do);
		//$edit->set_rel_title('itssal','Producto <#o#>');

		$edit->pre_process('insert' ,'_pre_insert' );
		$edit->pre_process('update' ,'_pre_update' );
		$edit->pre_process('delete' ,'_pre_delete' );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->mode='autohide';
		$edit->numero->maxlength=8;
		$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'required';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->readonly = true;
		$edit->fecha->calendar = false;
		$edit->fecha->size = 12;

		$edit->tipo = new  dropdownField ('Tipo', 'tipo');
		$edit->tipo->option('S','Salida');
		$edit->tipo->option('E','Entrada');
		$edit->tipo->onchange='chtipo()';
		$edit->tipo->style='width:80px;';
		$edit->tipo->rule='enum[S,E]|required';
		$edit->tipo->size = 5;

		$edit->almacen = new dropdownField('Almacen','almacen');
		$edit->almacen->option('','Seleccionar');
		$edit->almacen->options('SELECT ubica, CONCAT(ubica, " ", ubides) descrip FROM caub WHERE invfis="N" ORDER BY ubica');
		$edit->almacen->rule ='required|existecaub';
		$edit->almacen->style='width:200px;';

		$edit->depto = new dropdownField('Depto.','depto');
		$edit->depto->option('','Seleccionar');
		$edit->depto->options('SELECT depto, CONCAT(depto, " ", descrip) descrip FROM dpto WHERE tipo="G" ORDER BY depto');
		$edit->depto->rule ='required';
		$edit->depto->style='width:180px;';

		$edit->cargo = new dropdownField('Cargo','cargo');
		$edit->cargo->option('','Seleccionar');
		$edit->cargo->options('SELECT codigo, CONCAT(codigo, " ", nombre) descrip FROM usol ORDER BY codigo');
		$edit->cargo->rule ='required';
		$edit->cargo->style='width:180px;';

		$edit->descrip = new inputField('Descripci&oacute;n','descrip');
		$edit->descrip->size = 40;
		$edit->descrip->maxlength=50;

		$edit->motivo = new inputField('Motivo','motivo');
		$edit->motivo->size = 40;
		$edit->motivo->maxlength=50;

		//Para saber que precio se le va a dar al cliente
		$edit->caububides = new hiddenField('', 'caububides');
		$edit->caububides->db_name     = 'caububides';
		$edit->caububides->pointer     = true;
		$edit->caububides->insertValue = 1;


		//**************************
		//  Campos para el detalle
		//**************************
		$edit->codigo = new inputField('C&oacute;digo <#o#>', 'codigo_<#i#>');
		$edit->codigo->size     = 12;
		$edit->codigo->db_name  = 'codigo';
		$edit->codigo->rel_id   = 'itssal';
		$edit->codigo->rule     = 'required';
		$edit->codigo->append($btn);

		$edit->itdescrip = new inputField('Descripci&oacute;n <#o#>', 'itdescrip_<#i#>');
		$edit->itdescrip->size=36;
		$edit->itdescrip->db_name='descrip';
		$edit->itdescrip->maxlength=50;
		$edit->itdescrip->readonly  = true;
		$edit->itdescrip->rel_id='itssal';

		$edit->cantidad = new inputField('Cantidad <#o#>', 'cantidad_<#i#>');
		$edit->cantidad->db_name  = 'cantidad';
		$edit->cantidad->css_class= 'inputnum';
		$edit->cantidad->rel_id   = 'itssal';
		$edit->cantidad->maxlength= 10;
		$edit->cantidad->size     = 6;
		$edit->cantidad->rule     = 'required|positive';
		$edit->cantidad->autocomplete=false;

		$edit->costo = new inputField('Costo <#o#>', 'costo_<#i#>');
		$edit->costo->db_name   = 'costo';
		$edit->costo->css_class = 'inputnum';
		$edit->costo->rel_id    = 'itssal';
		$edit->costo->size      = 10;
		$edit->costo->rule      = 'required|positive';
		$edit->costo->readonly  = true;

		// busca concepto en icon, si tipo=E en icon=I si es tipo=S icon=E
		$edit->concepto = new inputField('Concepto <#o#>', 'concepto_<#i#>');
		$edit->concepto->db_name   = 'concepto';
		$edit->concepto->rel_id    = 'itssal';
		$edit->concepto->size      = 10;
		$edit->concepto->rule      = 'required|callback_chconcepto';
		$edit->concepto->append('<span id="mbE_<#i#>">'.$btnc1.'</span><span id="mbI_<#i#>">'.$btnc2.'</span>');

		$edit->usuario = new autoUpdateField('usuario',$this->secu->usuario(),$this->secu->usuario());

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back','add_rel');
		$edit->build();

		$conten['form']  =&  $edit;
		$data['content'] = $this->load->view('view_ssal', $conten,false);

	}

	function chconcepto($val){
		$tipo    = $this->input->post('tipo');
		if($tipo=='E'){
			$tipo='I';
		}elseif($tipo=='S'){
			$tipo='E';
		}else{
			$this->validation->set_message('chconcepto', 'Debe elegir un tipo para seleccionar un concepto');
			return false;
		}

		$dbtipo  = $this->db->escape($tipo);
		$dbcodigo= $this->db->escape($val);

		$cana= $this->datasis->dameval("SELECT COUNT(*) FROM icon WHERE tipo=${dbtipo} AND codigo=${dbcodigo}");
		if($cana >0){
			return true;
		}
		$this->validation->set_message('chconcepto', 'El campo %s possee un concepto que no corresponde al tipo del ajuste');
		return false;
	}

	function _pre_insert($do){
		$numero =$this->datasis->fprox_numero('nssal');
		$transac=$this->datasis->fprox_numero('ntransa');
		$usuario=$do->get('usuario');
		$estampa=date('Ymd');
		$hora   =date('H:i:s');

		$cana=$do->count_rel('itssal');
		for($i=0;$i<$cana;$i++){
			$do->set_rel('itssal','estampa',$estampa  ,$i);
			$do->set_rel('itssal','usuario',$usuario  ,$i);
			$do->set_rel('itssal','hora'   ,$hora     ,$i);
			$do->set_rel('itssal','transac',$transac  ,$i);
		}
		$do->set('numero',$numero);
		$do->set('estampa',$estampa);
		$do->set('hora'   ,$hora);
		$do->set('numero' ,$numero);
		$do->set('transac',$transac);
		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='Los ajustes no se pueden modificar.';
		return false;
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='Los ajustes de inventario no se pueden eliminar, debe hacer el reverso.';
		return false;
	}

	function _post_insert($do) {
		$numero = $do->get('numero');
		$alma   = $do->get('alma');
		$tipo   = $do->get('tipo');

		// Actualiza Inventario
		$mc = $this->db->query('SELECT codigo, cantidad FROM itssal WHERE numero='.$this->db->escape($numero));
		if ( $mc->num_rows() > 0) {
			foreach ($mc->result() as $row){
				if ( $tipo == 'S' )
					$this->datasis->sinvcarga( $row->codigo, $alma, -$row->cantidad);
				else
					$this->datasis->sinvcarga( $row->codigo, $alma, $row->cantidad);
			}
		}
		$monto = $this->datasis->dameval('SELECT SUM(costo*cantidad) FROM itssal WHERE numero='.$this->db->escape($numero));

		//Segun el Caso hace GASTO o OTIN
		if($tipo == 'S'){  // GASTO
			$data['fecha']    = $do->get('fecha');
			$data['numero']   = $numero;
			$data['proveed']  = 'AJUSI';
			$data['nombre']   = 'AJUSTES DE INVENTARIO';
			$data['vence']    = $do->get('fecha');
			$data['totpre']   = $monto;
			$data['totiva']   =  0;
			$data['totbruto'] = $monto;
			$data['reten']    = 0;
			$data['totneto']  = $monto;
			$data['codb1']    = '  ';
			$data['tipo1']    = '';
			$data['cheque1']  = '';
			$data['monto1']   = 0;
			$data['credito']  = $monto;
			$data['anticipo'] = 0;
			$data['orden']    = "";
			$data['tipo_doc'] = "AJ";
			$data['transac']  = $do->get('transac');
			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');
			$data['usuario']  = $do->get('usuario');
			$this->db->insert('gser', $data);

			$mSQL = "INSERT INTO gitser ( fecha, numero, proveed, codigo, descrip, precio, iva, importe, departa, sucursal, transac, usuario, estampa, hora )
					  SELECT c.fecha fecha, c.numero, 'AJUSI' proveed,
						b.gasto, a.descrip, SUM(a.costo*a.cantidad) precio, 0 iva, SUM(a.costo*a.cantidad) importe,
						d.depto departa, d.sucursal, a.transac, a.usuario, a.estampa, a.hora
						FROM itssal a JOIN icon b ON a.concepto=b.codigo
						JOIN ssal c ON a.numero=c.numero
						LEFT JOIN usol d ON c.cargo=d.codigo
						WHERE a.numero='".$numero."' GROUP BY a.concepto ";
			$this->db->query($mSQL);

		}else{  //
			$mNUMERO = $this->datasis->prox_sql('notiot');
			$mNUMERO = 'O'.substr($mNUMERO,1,7);
			$data['tipo_doc']  = 'OT';
			$data['numero']    = $mNUMERO;
			$data['fecha']     = $do->get('fecha');
			$data['orden']     = '';
			$data['cod_cli']   = 'AJUSI';
			$data['rifci']     = '';
			$data['nombre']    = 'AJUSTES DE INVENTARIO';
			$data['direc']     = '';
			$data['dire1']     = '';
			$data['totals']    = $monto;
			$data['iva']       = 0;
			$data['totalg']    = $monto;
			$data['vence']     = $do->get('fecha');
			$data['observa1']  = $do->get('descrip');
			$data['observa2']  = $do->get('motivo');
			$data['transac']   = $do->get('transac');
			$data['estampa']   = $do->get('estampa');
			$data['hora']      = $do->get('hora');
			$data['usuario']   = $do->get('usuario');

			$this->db->insert('otin', $data);
			$mSQL="	INSERT INTO itotin (tipo_doc, numero, codigo, descrip, precio, impuesto, importe, usuario, estampa, hora, transac )
					SELECT 'OT' AS tipo_doc, '${mNUMERO}' AS numero,   b.ingreso codigo, 'AJUSTES DE INVENTARIO' descrip, SUM(a.costo*a.cantidad) precio, 0 impuesto, SUM(a.costo*a.cantidad) importe, a.usuario, a.estampa, a.hora, a.transac
					FROM itssal a JOIN icon b ON a.concepto=b.codigo WHERE a.numero='".$numero."' GROUP BY a.concepto";

			$this->db->query($mSQL);
		}

		logusu('ssal',"Entradas y Salidas ${numero} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('numero');
		logusu('ssal',"Entradas y Salidas ${codigo} MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('numero');
		logusu('ssal',"Entradas y Salidas ${codigo} ELIMINADO");
	}

	function instalar(){

		if (!$this->db->table_exists('ssal')) {
			$mSQL="CREATE TABLE `ssal` (
			  `numero` char(8) NOT NULL DEFAULT '',
			  `fecha` date DEFAULT NULL,
			  `tipo` char(1) DEFAULT NULL,
			  `almacen` char(4) DEFAULT NULL,
			  `cargo` char(6) DEFAULT NULL,
			  `descrip` char(30) DEFAULT NULL,
			  `motivo` char(40) DEFAULT NULL,
			  `estampa` date DEFAULT NULL,
			  `usuario` char(12) DEFAULT NULL,
			  `hora` char(8) DEFAULT NULL,
			  `transac` char(8) DEFAULT NULL,
			  `modificado` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  `depto` char(3) DEFAULT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `numero` (`numero`),
			  KEY `modificado` (`modificado`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('itssal')) {
			$mSQL="CREATE TABLE `itssal` (
			  `numero` char(8) DEFAULT NULL,
			  `codigo` char(15) DEFAULT NULL,
			  `descrip` char(40) DEFAULT NULL,
			  `cantidad` decimal(12,3) DEFAULT NULL,
			  `costo` decimal(17,2) DEFAULT '0.00',
			  `estampa` date DEFAULT NULL,
			  `usuario` char(12) DEFAULT NULL,
			  `hora` char(8) DEFAULT NULL,
			  `transac` char(8) DEFAULT NULL,
			  `gasto` char(6) DEFAULT NULL,
			  `concepto` char(6) DEFAULT NULL,
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `modificado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`),
			  KEY `numero` (`numero`),
			  KEY `codigo` (`codigo`),
			  KEY `modificado` (`modificado`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('ssal');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE ssal DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE ssal ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE ssal ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
	}
}
