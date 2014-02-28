<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
class Pamarillas extends Controller {
	var $mModulo='PAMARILLAS';
	var $titp='Modulo PAMARILLAS';
	var $tits='Modulo PAMARILLAS';
	var $url ='ventas/pamarillas/';

	function Pamarillas(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_nombre( $modulo, $ventana=0 );
	}

	function index(){
		/*if ( !$this->datasis->iscampo('pamarillas','id') ) {
			$this->db->simple_query('ALTER TABLE pamarillas DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE pamarillas ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE pamarillas ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};*/
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$bodyscript = $this->bodyscript($param['grids'][0]['gridname']);

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		$grid->wbotonadd(array("id"=>"btnatencion", "img"=>"images/face-smile.png",  "alt" => "Atención", "label"=>"Atención"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array("id"=>"fatencion", "title"=>"Atención al prospecto")
		);

		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor("TITULO1"),$adic);

		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['SouthPanel'] = $SouthPanel;
		$param['listados']   = $this->datasis->listados('PAMARILLAS', 'JQ');
		$param['otros']       = $this->datasis->otros('PAMARILLAS', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript'] = $bodyscript;
		$param['tabs'] = false;
		$param['encabeza'] = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'true';

		$grid  = new $this->jqdatagrid;


		$grid->addField('estado');
		$grid->label('Estado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 100 }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 100 }',
		));

		$grid->addField('telf');
		$grid->label('Telféfono');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 100 }',
		));

		$grid->addField('rif');
		$grid->label('RIF');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false }',
			'editoptions'   => '{ size:30, maxlength: 15 }',
		));


		$grid->addField('descrip');
		$grid->label('Actividad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 200 }',
		));


		$grid->addField('ciudad');
		$grid->label('Ciudad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 100 }',
		));


		$grid->addField('direc');
		$grid->label('Direción');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 200 }',
		));


		$grid->addField('status');
		$grid->label('Estatus');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:2, maxlength: 1 }',
		));


		$grid->addField('observa');
		$grid->label('Observacion');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:200, maxlength: 255 }',
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
					var ret = $(gridId1).jqGrid(\'getRowData\',id);
					$("#ladicional").html(ret.observa);
				}
			}'
		);


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
		$mWHERE = $grid->geneTopWhere('pamarillas');

		$response   = $grid->getData('pamarillas', array(array()), array(), false, $mWHERE );
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
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM pamarillas WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('pamarillas', $data);
					echo "Registro Agregado";

					logusu('PAMARILLAS',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {

				unset($data['id']);
				$this->db->where("id", $id);
				$this->db->update('pamarillas', $data);
				logusu('PAMARILLAS',"Grupo de Cliente  ".$id." MODIFICADO");
				echo "$mcodp Modificado";
			//}

		}elseif($oper == 'del') {
		$meco = $this->datasis->dameval("SELECT $mcodp FROM pamarillas WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM pamarillas WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM pamarillas WHERE id=$id ");
				logusu('PAMARILLAS',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function fatencion(){
		$id   = $this->uri->segment($this->uri->total_segments());
		$dbid = $this->db->escape($id);
		$reg  = $this->datasis->damereg("SELECT estado, nombre, rif FROM `pamarillas` WHERE id=$dbid");

		$mSQL   = "SELECT id,descrip FROM `pamarillas_status` ORDER BY descrip";
		$status = $this->datasis->llenaopciones($mSQL, true, 'fstatus');

		$salida = '
<script type="text/javascript">

</script>
	<div style="background-color:#D0D0D0;font-weight:bold;font-size:14px;text-align:center"><table width="100%"><tr>
	<td>Estado: '.$reg['estado'].'</td><td>'.utf8_encode($reg['nombre']).'</td><td>RIF: '.$reg['rif'].'</td></tr></table></div>
	<p class="validateTips"></p>
	<form id="atencionforma">
	<table width="90%" align="center" border="0">
	<tr>
		<td class="CaptionTD" align="right">Estatus</td>
		<td>&nbsp;'.$status.'</td>
	</tr>
	<tr>
		<td class="CaptionTD" align="right">Observaci&oacute;n:</td>
		<td >&nbsp; <textarea id="fobserva" name="fobserva" rows="4" cols="80"></textarea></td>
	</tr>
	</table>
	<input id="fid"      name="fid"      type="hidden" value="'.$id.'">
	<br>
	</form>
';
		echo $salida;
	}

	function bodyscript($grid){

		$bodyscript = '<script type="text/javascript">';

		//Imprimir Estado de Cuenta
		$bodyscript .= '
		jQuery("#edocta").click( function(){
			var id = jQuery("#newapi'. $grid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'. $grid.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('reportes/ver/SPRMECU/SPRM/').'/\'+ret.cod_prv').';
			} else { $.prompt("<h1>Por favor Seleccione un Proveedor</h1>");}
		});';

		//Imprimir Estado de Cuenta
		$bodyscript .= '
		jQuery("#preapro").click( function(){
			var id = jQuery("#newapi'. $grid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'. $grid.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('reportes/ver/SPRMPRE').'/\'+ret.id').';
			} else { $.prompt("<h1>Por favor Seleccione un Proveedor</h1>");}
		});';

		//Wraper de javascript
		$bodyscript .= '
		$(function() {
			$("#dialog:ui-dialog").dialog( "destroy" );
			var mId = 0;
			var montotal = 0;
			var ffecha = $("#ffecha");
			var grid = jQuery("#newapi'.$grid.'");
			var s;
			var allFields = $( [] ).add( ffecha );

			var tips = $( ".validateTips" );

			s = grid.getGridParam(\'selarrrow\');
			//$( "input:submit, a, button", ".otros" ).button();';

		//Prepara Pago o Abono
		$bodyscript .= '
			$( "#btnatencion" ).click(function() {
				var id     = jQuery("#newapi'.$grid.'").jqGrid(\'getGridParam\',\'selrow\');
				if (id)	{
					var ret    = $("#newapi'.$grid.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'fatencion').'/"+id, function(data){
						$("#fatencion").html(data);
					});
					$( "#fatencion" ).dialog( "open" );
				} else { $.prompt("<h1>Por favor Seleccione un Prospecto</h1>");}
			});
			$( "#fatencion" ).dialog({
				autoOpen: false, height: 470, width: 790, modal: true,
				buttons: {
					"Guardar": function() {
						var bValid  = true;
						var observa = $("#fobserva").val();

						if(observa.length <= 1){
							alert("El campo observacion es obligatorio");
							bValid = false;
						}

						allFields.removeClass( "ui-state-error" );
						if ( bValid ) {
							$.ajax({
								type: "POST", dataType: "html", async: false,
								url:"'.site_url($this->url."setatencion").'",
								data: $("#atencionforma").serialize(),
								success: function(r,s,x){
									alert(r);
								}
							});
						}
					},
					Cancel: function() { $( this ).dialog( "close" ); }
				},
				close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
			});';


		//Abonos
		$bodyscript .= '
			$( "#abonos" ).click(function() {
				var id     = jQuery("#newapi'.$grid.'").jqGrid(\'getGridParam\',\'selrow\');
				if (id)	{
					var ret    = $("#newapi'.$grid.'").getRowData(id);
					mId = id;
					$.post("'.site_url('finanzas/ppro/formaabono').'/"+id, function(data){
						$("#fpreabono").html("");
						$("#fabono").html(data);
					});
					$( "#fabono" ).dialog( "open" );
				} else { $.prompt("<h1>Por favor Seleccione un Proveedor</h1>");}
			});

			$( "#fabono" ).dialog({
				autoOpen: false, height: 470, width: 790, modal: true,
				buttons: {
					"Abonar": function() {
						var bValid = true;
						var rows = $("#abonados").jqGrid("getGridParam","data");
						var paras = new Array();
						for(var i=0;i < rows.length; i++){
							var row=rows[i];
							paras.push($.param(row));
						}
						allFields.removeClass( "ui-state-error" );
						if ( bValid ) {
							// Coloca el Grid en un input
							$("#fgrid").val(JSON.stringify(paras));
							$.ajax({
								type: "POST", dataType: "html", async: false,
								url:"'.site_url("finanzas/ppro/abono").'",
								data: $("#abonoforma").serialize(),
								success: function(r,s,x){
									var res = $.parseJSON(r);
									if ( res.status == "A"){
										apprise(res.mensaje);
										grid.trigger("reloadGrid");
										'.$this->datasis->jwinopen(site_url('formatos/ver/PPROABB').'/\'+res.id').';
										$( "#fabono" ).dialog( "close" );
										return [true, a ];
									} else {
										apprise("<div style=\"font-size:16px;font-weight:bold;background:red;color:white\">Error:</div> <h1>"+res.mensaje+"</h1>");
									}
								}
							});
						}
					},
					Cancel: function() { $( this ).dialog( "close" ); }
				},
				close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
			});';


		//Notas de Credito
		$bodyscript .= '
			$( "#ncredito" ).click(function() {
				var id     = jQuery("#newapi'.$grid.'").jqGrid(\'getGridParam\',\'selrow\');
				if (id)	{
					var ret    = $("#newapi'.$grid.'").getRowData(id);
					mId = id;
					$.post("'.site_url('finanzas/ppro/formancredito').'/"+id, function(data){
						$("#fpreabono").html("");
						$("#fabono").html("");
						$("#fncredito").html(data);
					});
					$( "#fncredito" ).dialog( "open" );
				} else { $.prompt("<h1>Por favor Seleccione un Proveedor</h1>");}
			});

			$( "#fncredito" ).dialog({
				autoOpen: false, height: 470, width: 690, modal: true,
				buttons: {
					"Abonar": function() {
						var bValid = true;
						var rows = $("#abonados").jqGrid("getGridParam","data");
						var paras = new Array();
						for(var i=0;i < rows.length; i++){
							var row=rows[i];
							paras.push($.param(row));
						}
						allFields.removeClass( "ui-state-error" );
						if ( bValid ) {
							// Coloca el Grid en un input
							$("#fgrid").val(JSON.stringify(paras));
							$.ajax({
								type: "POST", dataType: "html", async: false,
								url:"'.site_url("finanzas/ppro/ncredito").'",
								data: $("#ncreditoforma").serialize(),
								success: function(r,s,x){
									var res = $.parseJSON(r);
									if ( res.status == "A"){
										apprise(res.mensaje);
										grid.trigger("reloadGrid");
										'.$this->datasis->jwinopen(site_url('formatos/ver/PPRONC').'/\'+res.id').';
										$( "#fabono" ).dialog( "close" );
										return [true, a ];
									} else {
										apprise("<div style=\"font-size:16px;font-weight:bold;background:red;color:white\">Error:</div> <h1>"+res.mensaje+"</h1>");
									}
								}
							});
						}
					},
					Cancel: function() { $( this ).dialog( "close" ); }
				},
				close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
			});
		});';
		$bodyscript .= "\n</script>\n";
		return $bodyscript;
	}

	function setatencion(){
		$status  = $this->input->post('festatus');
		$observa = $this->input->post('fobserva');
		$id      = $this->input->post('fid');
		$fecha   = date('d/m/Y h:i:s');

		if(strlen($observa)>1){
			$observa   = "Fecha $fecha:\n".$observa;

			$dbstatus  = $this->db->escape($status);
			$dbobserva = $this->db->escape($observa);
			$dbid      = $this->db->escape($id);

			$mSQL = "UPDATE pamarillas SET status=$dbstatus, observa=CONCAT_WS(\"\\n\",observa, $dbobserva) WHERE id=$dbid";
			$this->db->simple_query($mSQL);
			//echo $mSQL;
			echo 'Registro Guardado';
		}else{
			echo 'El campo observacion es obligatorio';
		}
		//print_r($_POST);
	}

}
