<?php
class Tbofici extends Controller {
	var $mModulo = 'TBOFICI';
	var $titp    = 'OFICINAS';
	var $tits    = 'Modulo TBOFICI';
	var $url     = 'pasajes/tbofici/';

	function Tbofici(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'TBOFICI', $ventana=0 );
	}

	function index(){
		$this->datasis->creaintramenu(array('modulo'=>'161','titulo'=>'Oficinas','mensaje'=>'Oficinas','panel'=>'PASAJES','ejecutar'=>'pasajes/tbofici','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>900,'alto'=>600));
		$this->instalar();
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	//Layout en la Ventana
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$grid->setHeight('160');
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$grid1->setHeight('130');
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 190, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = "radicional", $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );


		$WpAdic = "
		<tr><td><div class=\"tema1\">
			<table cellpadding='0' cellspacing='0' style='width:90%;'>
				<tr >
					<td style='text-align:center;'>
						<div class='botones' style='font-size:12pt;' align='right'>GASTOS &nbsp;</div></td>
					<td style='vertical-align:top;text-align:center;'><div class='botones'><a style='width:45px;text-align:left;vertical-align:top;' href='#' id='agregag'>".img(array('src' =>"images/agrega4.png", 'height' => 18, 'alt' => 'Agregar', 'title' => 'Agregar', 'border'=>'0'))."</a></div></td>
					<td style='vertical-align:top;text-align:center;'><div class='botones'><a style='width:45px;text-align:left;vertical-align:top;' href='#' id='eliming'>".img(array('src' =>"images/delete.png",  'height' => 18, 'alt' => 'Agregar', 'title' => 'Agregar', 'border'=>'0'))."</a></div></td>
				</tr>
			</table>
			</div>
		</td></tr>\n
		";

		$grid->setWpAdicional($WpAdic);

		//Botones Panel Izq
		$grid->wbotonadd(array("id"=>"precios", "img"=>"images/recalcular.png", "alt" => "Precios",    "label"=>"Precios",    "tema"=>'anexos'));
		$grid->wbotonadd(array("id"=>"descue1", "img"=>"images/recalcular.png", "alt" => "Descuentos", "label"=>"Descuentos", "tema"=>'anexos'));
		$grid->wbotonadd(array("id"=>"asignao", "img"=>"images/recalcular.png", "alt" => "Asignar Oficina a usuario", "label"=>"Asignar Of.", "tema"=>'anexos'));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '';

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['funciones']   = $funciones;
		$param['readyLayout'] = $readyLayout;
		$param['SouthPanel']  = $SouthPanel;
		$param['centerpanel'] = $centerpanel;
		$param['listados']    = $this->datasis->listados('TBOFICI', 'JQ');
		$param['otros']       = $this->datasis->otros('TBOFICI', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	//Funciones de los Botones
	//
	function bodyscript( $grid0, $grid1 ){
		$bodyscript = '<script type="text/javascript">';

		$bodyscript .= '
		function tboficiadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function tboficiedit(){
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
		$("#precios").click(function(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'precioforma').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog( { title:"LISTA DE PRECIOS", width: 480, height: 500 } );
					$("#fshow").dialog( "open" );
				});

			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		});';

		$usu = $this->datasis->llenaopciones("SELECT us_codigo, CONCAT_WS(' ',us_nombre, us_codigo, '(',pasaje,')') nombre FROM usuario ORDER BY us_nombre", false, 'musuario');
		$usu = str_replace('"',"'",$usu);

		$bodyscript .= '
		$("#asignao").click(function(){
			var id  = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				var mcome1 = "<h1>Asignar usuario a la Oficina "+ret.codofi+" "+ret.desofi+"</h1>"+
					"<table align=\'center\'>"+
					"<tr><td>Usuario :</tdtd><td colspan=\'3\'>"+"'.$usu.'</td></tr>"+
					"</table>";
				var masigna = 
				{
					state0: {
						html: mcome1,
						buttons: { Guardar: true, Cancelar: false },
						submit: function(e,v,m,f){
							moficina = f.moficina;
							if (v) {
								$.post("'.site_url('pasajes/tbofici/asignau').'/", { usuario: f.musuario, mid: id, codofi: ret.codofi }, 
									function(data){
										$.prompt.getStateContent(\'state1\').find(\'#us_prome2\').text(data);
										$.prompt.goToState(\'state1\');
										$("#newapi'.$grid1.'").trigger("reloadGrid");
								});
								return false;
							} 
						}
					},
					state1: { 
						html: "<h1>Resultado</h1><span id=\'us_prome2\'></span>",
						focus: 1,
						buttons: { Ok:true }
					}		
				};
				$.prompt(masigna);

			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		});';


		$bodyscript .= '
		$("#descue1").click(function(){
			$.post("'.site_url($this->url.'descueforma').'/", function(data){
				$("#fshow").html(data);
				$("#fshow").dialog( { title:"DESCUENTOS", width: 350, height: 350 } );
				$("#fshow").dialog( "open" );
			});
		});';


		$bodyscript .= '
		function tboficishow(){
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
		function tboficidel() {
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

		// Eliminar Gasto
		$bodyscript .= '
		$("#eliming").click( function(){
			var id = jQuery("#newapi'.$grid1.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid1.'").jqGrid(\'getRowData\',id);
				$.prompt("<h1>Eliminar Gasto Seleccionado</h1>", {
					buttons: { Eliminar: true, Salir: false },
					callback: function(e,v,m,f){
						if (v) {
							$.post("'.site_url('pasajes/tbofici/gastos').'/", { mid: id, oper: \'Del\' }, 
								function(data){
									//$.prompt.getStateContent(\'state1\').find(\'#in_prome2\').text(data);
									//$.prompt.goToState(\'state1\');
									$("#newapi'.$grid1.'").trigger("reloadGrid");
								});
							};
						}
					})
			} else { $.prompt("<h1>Por favor Seleccione un Destino</h1>");}
		});';

		$noco = $this->datasis->llenaopciones("SELECT codgas, CONCAT(codgas,' ', nomgas) FROM tbgastos ORDER BY codgas", false, 'mgasto');
		$noco = str_replace('"',"'",$noco);

		// Agrgaga Destinos
		$bodyscript .= '
		$("#agregag").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				var mcome1 = "<h1>Destinos</h1>"+
					"<table align=\'center\'>"+
					"<tr><td>Gasto :</tdtd><td colspan=\'3\'>"+"'.$noco.'</td></tr>"+
					"</table>";
				var mprepanom = 
				{
					state0: {
						html: mcome1,
						buttons: { Guardar: true, Cancelar: false },
						submit: function(e,v,m,f){
							moficina = f.moficina;
							if (v) {
								$.post("'.site_url('pasajes/tbofici/gastos').'/", { gasto: f.mgasto, mid: id, oper: \'Add\' }, 
									function(data){
										$.prompt.getStateContent(\'state1\').find(\'#in_prome2\').text(data);
										$.prompt.goToState(\'state1\');
										$("#newapi'.$grid1.'").trigger("reloadGrid");
								});
								return false;
							} 
						}
					},
					state1: { 
						html: "<h1>Resultado</h1><span id=\'in_prome2\'></span>",
						focus: 1,
						buttons: { Ok:true }
					}		
				};
				$.prompt(mprepanom);
				$("#mhora").mask("99:99 a");

			} else { $.prompt("<h1>Por favor Seleccione una Ruta</h1>");}
		});';

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
									'.$this->datasis->jwinopen(site_url('formatos/ver/TBOFICI').'/\'+res.id+\'/id\'').';
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

	//******************************************************************
	// Formato de la ventana
	//
	function precioforma( $id = 0 ){
		$msalida = '<script type="text/javascript">'."\n";
		$msalida .= 'var mid='.$id.";\n";

		$codofi  = $this->datasis->dameval("SELECT codofi FROM tbofici WHERE id=$id");
		$oficina = $this->datasis->dameval("SELECT CONCAT(codofi,' ',desofi) oficina FROM tbofici WHERE id=$id");

		$mSQL  = "SELECT codofi, CONCAT(codofi, ' ', desofi) descrip FROM tbofici WHERE id<>$id AND codofi NOT IN (SELECT codofiorg FROM tbprecios WHERE codofides='$codofi') ORDER BY codofi ";
		$oficinas = $this->datasis->llenajqselect($mSQL, false );
		
		$msalida .= '
		$("#bpos1").jqGrid({
			url:\''.site_url($this->url.'precios').'/\'+mid,
			ajaxGridOptions: { type: "POST"},
			jsonReader: { root: "data", repeatitems: false},
			datatype: "json",
			hiddengrid: false,
			postdata: { tboficiid: "wapi"},
			width: 450,
			height: 300, 
			colNames:[\'id\', \'Codigo\',\'Oficina\', \'BusCama.\',\'Ejecutivo\'],
			colModel:[
				{name:\'id\',       index:\'id\',        width: 10, hidden:true},
				{name:\'codofides\',index:\'codofides\', width: 50, editable:true, edittype: \'select\', editoptions: { value: '.$oficinas.',  style:"width:160px"} },
				{name:\'desofi\',   index:\'desofi\',    width:150, editable:true, editoptions: {readonly:\'readonly\'}},
				{name:\'prec_01\',  index:\'prec_01\',   width: 60, editable:true, editoptions: {size:10,maxlength:10,dataInit:function(elem){$(elem).numeric();}},formatter:\'number\',formatoptions:{decimalSeparator:".",thousandsSeparator:",",decimalPlaces:2}, align:\'right\' },
				{name:\'prec_02\',  index:\'prec_02\',   width: 60, editable:true, editoptions: {size:10,maxlength:10,dataInit:function(elem){$(elem).numeric();}},formatter:\'number\',formatoptions:{decimalSeparator:".",thousandsSeparator:",",decimalPlaces:2}, align:\'right\' },
			],
			rowNum:1000,
			pginput: false,
			pgbuttons: false,
			rowList:[],
			pager: \'#pbpos1\',
			sortname: \'id\',
			viewrecords: false,
			sortorder: "desc",
			editurl: \''.site_url($this->url.'precios').'/\'+mid,
			caption: "Lista de Precios desde '.$oficina.'"
		});
		jQuery("#bpos1").jqGrid(\'navGrid\',"#pbpos1",
			{edit:true, add:true, del:true, search: false },
			{ beforeShowForm: function(frm){ $(\'#codofides\').hide(); }},
			{ beforeShowForm: function(frm){ $(\'#codofides\').show(); } }
		);
		';
		$msalida .= "\n</script>\n";
		$msalida .= "<div class=\"tema1\"><center><table id=\"bpos1\"></table></div><div id='pbpos1'></center></div>\n";
	
		echo $msalida;

	}

	//******************************************************************
	// Formato de la ventana
	//
	function descueforma(){
		$msalida = '<script type="text/javascript">'."\n";

		$msalida .= '
		$("#descu1").jqGrid({
			url:\''.site_url($this->url.'descuento').'\',
			ajaxGridOptions: { type: "POST"},
			jsonReader: { root: "data", repeatitems: false},
			datatype: "json",
			hiddengrid: false,
			postdata: { tbdescuid: "wapi"},
			width: 330,
			height: 170, 
			colNames:[\'id\', \'Codigo\',\'Descripcion\', \'Procentaje.\'],
			colModel:[
				{name:\'id\',     index:\'id\',     width: 10, hidden:true},
				{name:\'coddes\', index:\'coddes\', width: 50, editable:true },
				{name:\'desdes\', index:\'desdes\', width:150, editable:true },
				{name:\'mont\',   index:\'mont\',   width: 60, editable:true, editoptions: {size:10,maxlength:10,dataInit:function(elem){$(elem).numeric();}},formatter:\'number\',formatoptions:{decimalSeparator:".",thousandsSeparator:",",decimalPlaces:2}, align:\'right\' },
			],
			rowNum:1000,
			pginput: false,
			pgbuttons: false,
			rowList:[],
			pager: \'#pdescu1\',
			sortname: \'id\',
			viewrecords: false,
			sortorder: "desc",
			editurl: \''.site_url($this->url.'descuento').'\',
			caption: "Descuentos"
		});
		';

		$msalida .= '
		$("#descu1").jqGrid(\'navGrid\',"#pdescu1",
			{ edit:true, add:true, del:true, search: false },
			{ beforeShowForm: function(frm){ $(\'#coddes\').attr("readonly", "readonly"); }},
			{ beforeShowForm: function(frm){ $(\'#coddes\').removeAttr("readonly"); } }
		);
		';

		$msalida .= "\n</script>\n";

		$msalida .= "<div><center><table id=\"descu1\"></table></div><div id='pdescu1'></center></div>\n";
	
		echo $msalida;

	}

	//******************************************************************
	// Lista de Precios
	//
	function precios( $id = 0 ){
		$this->load->library('jqdatagrid');

		$oper  = $this->input->post('oper');
		$mid   = $this->input->post('id');
		$data  = $_POST;

		unset($data['oper']);
		unset($data['id']);

		if($oper == 'add'){
			if(false == empty($data)){
				$codofi  = $this->datasis->dameval("SELECT codofi FROM tbofici WHERE id=$id");
				$check = $this->datasis->dameval("SELECT count(*) FROM tbprecios WHERE codofiorg=$codofi AND codofides=".$this->db->escape($data['codofides']));
				if ( $check == 0 ){
					unset($data['desofi']);
					$data['codofiorg'] = $codofi; 
					$this->db->insert('tbprecios', $data);
					logusu('TBOFICI',"Precio Agregado ".$this->db->escape($data['codofides'])." INCLUIDO");
					echo "Registro Agregado id=".$id;
				} else
					echo "Ya existe un registro con ese destino";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			unset($data['codofi']);
			$this->db->where("id", $mid);
			$this->db->update('tbprecios', $data);
			logusu('TBOFICI',"Precio Modificado ".$mid." MODIFICADO");
			echo "Precio Modificado";
			
		} elseif ($oper == 'del'){
			// Borra
			$this->db->query("DELETE FROM tbprecios WHERE id=$mid ");
			logusu('TBOFICI',"Registro   ELIMINADO");
			echo "Registro Eliminado";


		} elseif ( $oper == false ) {
			$id = $this->uri->segment(4);
			if ( $id > 0 ) {
				$codofi  = $this->datasis->dameval("SELECT codofi FROM tbofici WHERE id=$id");
				
				$this->db->select(array('a.id', 'a.codofides', 'CONCAT(b.desofi," (",a.codofides,")") desofi', 'a.prec_01', 'a.prec_02'));
				$this->db->from('tbprecios a');
				$this->db->join('tbofici b','a.codofides=b.codofi');
				$this->db->where('codofiorg',$codofi);

				$rs = $this->datasis->codificautf8($this->db->get()->result_array());
				$response['data'] = $rs;
				$rs = json_encode( $response);
				echo $rs;
			}
		}
		
	}

	//******************************************************************
	// Lista de Precios
	//
	function asignau( $id = 0 ){
		$this->load->library('jqdatagrid');

		$usuario = $this->input->post('usuario');
		$mid     = $this->input->post('mid');
		$codofi  = $this->input->post('codofi');

		$data    = array( 'pasaje' => $codofi );

		$this->db->where("us_codigo", $usuario);
		$this->db->update('usuario', $data);
		logusu('TBOFICI',"Usuario ".$usuario." Asignado a la oficina ".$codofi." ASIGNADO");
		echo "Usuario ".$usuario." asignado a la oficina ".$codofi;

		
	}



	//******************************************************************
	// Descuentos
	//
	function descuento(){
		$this->load->library('jqdatagrid');

		$oper  = $this->input->post('oper');
		$mid   = $this->input->post('id');
		$data  = $_POST;

		unset($data['oper']);
		unset($data['id']);

		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM tbdescu WHERE coddes=".$this->db->escape($data['coddes']));
				if ( $check == 0 ){ 
					$this->db->insert('tbdescu', $data);
					logusu('TBOFICI',"Descuento Agregado ".$this->db->escape($data['coddes'])." INCLUIDO");
					echo "Registro Agregado id=".$id;
				} else
					echo "Ya existe un descuento con ese codigo";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			unset($data['coddes']);
			$this->db->where("id", $mid);
			$this->db->update('tbdescu', $data);
			logusu('TBOFICI',"Descuento Modificado ".$mid." MODIFICADO");
			echo "Descuento Modificado";
			
		} elseif ($oper == 'del'){
			// Borra
			$this->db->query("DELETE FROM tbdescu WHERE id=$mid ");
			logusu('TBOFICI',"Descuento ELIMINADO");
			echo "Descuento Eliminado";


		} elseif ( $oper == false ) {
			$id = $this->uri->segment(4);
				
			$this->db->select(array('a.id', 'a.coddes', 'a.desdes', 'a.mont', 'a.opc'));
			$this->db->from('tbdescu a');
			$this->db->orderby('a.coddes');

			$rs = $this->datasis->codificautf8($this->db->get()->result_array());
			$response['data'] = $rs;
			$rs = json_encode( $response);
			echo $rs;

		}
	}



	//******************************************************************
	//   Funciones
	//
	function funciones($grid0 = ''){
		$funciones = '';
		return $funciones;
	}



	//******************************************************************
	//  Gestiona Destinos
	//
	function gastos(){
		$mid   = $this->input->post('mid');
		$oper  = $this->input->post('oper');
		$salida = $oper.' '.$mid;
		
		if ( $oper == 'Add') {
			$codgas = $this->input->post('gasto');
			$codofi = $this->datasis->dameval("SELECT codofi FROM tbofici WHERE id=$mid");
		
			$data = array();
	
			$data['codgas'] = $codgas;
			$data['codofi'] = $codofi;
 
			$this->db->insert('tbgasofi', $data);
			$salida = "Gasto Agregado..";
	
		} elseif ($oper == 'Del') {
			$this->db->query("DELETE FROM tbgasofi WHERE id=$mid");
			$salida = "Gasto Eiminado $mid";
		}
		
		echo $salida;
		
	}



	//******************************************************************
	// Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codofi');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('desofi');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->addField('dirofi');
		$grid->label('Dirección');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->addField('telofi');
		$grid->label('Tel Oficina');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('gereofi');
		$grid->label('Gerente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('telegofi');
		$grid->label('Tel Gerente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('estado');
		$grid->label('Estado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('zona');
		$grid->label('Zona');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));

/*
		$grid->addField('id');
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
		$grid->setHeight('290');
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
					jQuery("#bpos1").jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'precios/').'/"+id+"/", page:1});
					jQuery("#bpos1").trigger("reloadGrid");
				}
			}'
		);

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('TBOFICI','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('TBOFICI','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('TBOFICI','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('TBOFICI','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: tboficiadd, editfunc: tboficiedit, delfunc: tboficidel, viewfunc: tboficishow");

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

	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('tbofici');

		$response   = $grid->getData('tbofici', array(array()), array(), false, $mWHERE, 'codofi' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion
	//
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
				$check = $this->datasis->dameval("SELECT count(*) FROM tbofici WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('tbofici', $data);
					echo "Registro Agregado";

					logusu('TBOFICI',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM tbofici WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM tbofici WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE tbofici SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("tbofici", $data);
				logusu('TBOFICI',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('tbofici', $data);
				logusu('TBOFICI',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM tbofici WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM tbofici WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM tbofici WHERE id=$id ");
				logusu('TBOFICI',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}


	//******************************************************************
	// Definicion del Grid y la Forma
	//
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codgas');
		$grid->label('Codigo Gasto');
		$grid->params(array(
			'search'        => 'true',
			'align'         => "'center'",
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->addField('codofi');
		$grid->label('Codofi');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));

		$grid->addField('nomgas');
		$grid->label('Nombre del Gasto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));



		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'hidden'        => 'true',
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->showpager(false);
		$grid->setWidth('');
		$grid->setHeight('290');
		//$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('TBGASOFI','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('TBGASOFI','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('TBGASOFI','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('TBGASOFI','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: tbgasofiadd, editfunc: tbgasofiedit, delfunc: tbgasofidel, viewfunc: tbgasofishow");

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

	/*******************************************************************
	* Busca la data en el Servidor por json
	*/
	function getdatait(){
		$id = $this->uri->segment(4);
		if ($id === false ){
			$id = $this->datasis->dameval("SELECT id FROM tbofici ORDER BY codofi LIMIT 1");
		}
		
		if(empty($id)) return '';
		$dbid = $this->db->escape($id);

		$row = $this->datasis->damerow('SELECT codofi FROM tbofici WHERE id='.$dbid);

		$codofi = $this->db->escape($row['codofi']);

		$grid       = $this->jqdatagrid;
		$mSQL    = "SELECT a.*, b.nomgas FROM tbgasofi a JOIN tbgastos b ON a.codgas=b.codgas WHERE a.codofi=${codofi} ORDER BY a.codgas";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;

	}

	/*******************************************************************
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
				$check = $this->datasis->dameval("SELECT count(*) FROM tbgasofi WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('tbgasofi', $data);
					echo "Registro Agregado";

					logusu('TBGASOFI',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM tbgasofi WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM tbgasofi WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE tbgasofi SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("tbgasofi", $data);
				logusu('TBGASOFI',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('tbgasofi', $data);
				logusu('TBGASOFI',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM tbgasofi WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM tbgasofi WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM tbgasofi WHERE id=$id ");
				logusu('TBGASOFI',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}


	//******************************************************************
	//
	//
	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
		});
		';

		$edit = new DataEdit($this->tits, 'tbofici');

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
		});		';
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->codofi = new inputField('Codigo','codofi');
		$edit->codofi->rule='';
		$edit->codofi->size =7;
		$edit->codofi->maxlength =5;
		$edit->codofi->mode = "autohide";

		$edit->desofi = new inputField('Descripción','desofi');
		$edit->desofi->rule='';
		$edit->desofi->size =50;
		$edit->desofi->maxlength =100;

		$edit->dirofi = new inputField('Dirección','dirofi');
		$edit->dirofi->rule='';
		$edit->dirofi->size =50;
		$edit->dirofi->maxlength =100;

		$edit->telofi = new textareaField('Tel Oficina','telofi');
		$edit->telofi->rule='trim';
		$edit->telofi->cols =47;
		$edit->telofi->rows =2;
		$edit->telofi->maxlength =50;

		$edit->gereofi = new inputField('Gerente','gereofi');
		$edit->gereofi->rule='';
		$edit->gereofi->size =50;
		$edit->gereofi->maxlength =50;

		$edit->telegofi = new inputField('Tel Gerente','telegofi');
		$edit->telegofi->rule='';
		$edit->telegofi->size =50;
		$edit->telegofi->maxlength =50;

		$edit->estado = new inputField('Estado','estado');
		$edit->estado->rule='';
		$edit->estado->size =50;
		$edit->estado->maxlength =50;

		$edit->zona = new dropdownField('Zona','zona');
		$edit->zona->rule='required';
		$edit->zona->options('select codigo, CONCAT(codigo, " ", nombre) nombre FROM zona ORDER BY codigo');

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
		return true;
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

	function tabla() {
		$id = $this->uri->segment($this->uri->total_segments());
/*
		$transac = $this->datasis->dameval("SELECT transac FROM gser WHERE id='$id'");
		$mSQL = "SELECT cod_prv, MID(CONCAT(TRIM(cod_prv),' ',nombre),1,25) nombre, tipo_doc, numero, monto, abonos FROM sprm WHERE transac='$transac' ORDER BY cod_prv ";
		$query = $this->db->query($mSQL);
		$codprv = 'XXXXXXXXXXXXXXXX';
		$salida = '';
		$saldo = 0;
		if ( $query->num_rows() > 0 ){
			$salida = "<br><table width='100%' border=1>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";

			foreach ($query->result_array() as $row)
			{
				if ( $codprv != $row['cod_prv']){
					$codprv = $row['cod_prv'];
					$salida .= "<tr bgcolor='#c7d3c7'>";
					$salida .= "<td colspan=4>".trim($row['nombre']). "</td>";
					$salida .= "</tr>";
				}
				if ( $row['tipo_doc'] == 'FC' ) {
					$saldo = $row['monto']-$row['abonos'];
				}
				$salida .= "<tr>";
				$salida .= "<td>".$row['tipo_doc']."</td>";
				$salida .= "<td>".$row['numero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
				$salida .= "</tr>";
			}
			$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'>Saldo : ".nformat($saldo). "</td></tr>";
			$salida .= "</table>";
		}

		$mSQL = "SELECT codbanc, banco, tipo_op tipo_doc, numero, monto FROM bmov WHERE transac='$transac' ORDER BY codbanc ";
		$query = $this->db->query($mSQL);
		$salida .= "\n";
		if ( $query->num_rows() > 0 ){
			$salida .= "<br><table width='100%' border=1>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>Banco</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row)
			{
				$salida .= "<tr>";
				$salida .= "<td>".$row['codbanc']."</td>";
				$salida .= "<td>".$row['banco'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto'])."</td>";
				$salida .= "</tr>";
			}
			$salida .= "</table>";
		}
		echo $salida;
*/
	
	}



	function instalar(){
		if (!$this->db->table_exists('tbofici')) {
			$mSQL="CREATE TABLE `tbofici` (
			  `codofi` varchar(5) NOT NULL DEFAULT '',
			  `desofi` varchar(100) DEFAULT '',
			  `dirofi` varchar(100) DEFAULT NULL,
			  `telofi` varchar(50) DEFAULT NULL,
			  `gereofi` varchar(50) DEFAULT NULL,
			  `telegofi` varchar(50) DEFAULT NULL,
			  `estado` varchar(50) DEFAULT NULL,
			  `zona` varchar(4) DEFAULT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codofi` (`codofi`)
			) ENGINE=MyISAM AUTO_INCREMENT=132 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('tbofici');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}

?>
