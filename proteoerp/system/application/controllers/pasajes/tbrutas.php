<?php
class Tbrutas extends Controller {
	var $mModulo = 'TBRUTAS';
	var $titp    = 'RUTAS';
	var $tits    = 'RUTAS';
	var $url     = 'pasajes/tbrutas/';

	function Tbrutas(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'TBRUTAS', $ventana=0 );
	}

	function index(){
		$this->datasis->creaintramenu(array('modulo'=>'166','titulo'=>'Rutas','mensaje'=>'Rutas','panel'=>'PASAJES','ejecutar'=>'pasajes/tbrutas','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
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
		<tr><td><div class=\"tema1\"><table id=\"bpos1\"></table></div><div id='pbpos1'></div></td></tr>\n
		<tr><td><div class=\"tema1\">
			<table cellpadding='0' cellspacing='0' style='width:90%;'>
				<tr>
					<td style='text-align:center;' colspan='3'><div class='botones' style='background:#EAEAEA;font-size:12pt;'>DESTINOS</div></td>
				</tr>
				<tr>
					<td style='vertical-align:top;text-align:center;'><div class='botones'><a style='width:60px;text-align:left;vertical-align:top;' href='#' id='agregad'>".img(array('src' =>"images/agrega4.png",  'height' => 18, 'alt' => 'Agregar', 'title' => 'Agregar', 'border'=>'0'))."</a></div></td>
					<td style='vertical-align:top;text-align:center;'><div class='botones'><a style='width:60px;text-align:left;vertical-align:top;' href='#' id='modifid'>".img(array('src' =>"images/editar.png",  'height' => 18, 'alt' => 'Agregar', 'title' => 'Agregar', 'border'=>'0'))."</a></div></td>
					<td style='vertical-align:top;text-align:center;'><div class='botones'><a style='width:60px;text-align:left;vertical-align:top;' href='#' id='elimind'>".img(array('src' =>"images/delete.png",  'height' => 18, 'alt' => 'Agregar', 'title' => 'Agregar', 'border'=>'0'))."</a></div></td>
				</tr>
			</table>
			<br>
			<table cellpadding='0' cellspacing='0' style='width:90%;'>
				<tr >
					<td style='text-align:center;'>
						<div class='botones' style='font-size:12pt;' align='right'>GASTOS</div></td>
					<td style='vertical-align:top;text-align:center;'><div class='botones'><a style='width:40px;text-align:left;vertical-align:top;' href='#' id='agregag'>".img(array('src' =>"images/agrega4.png",  'height' => 18, 'alt' => 'Agregar', 'title' => 'Agregar', 'border'=>'0'))."</a></div></td>
				</tr>
			</table>
			</div>
		</td></tr>\n
		";

		$grid->setWpAdicional($WpAdic);


		//Botones Panel Izq
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['readyLayout'] = $readyLayout;
		$param['SouthPanel']  = $SouthPanel;
		$param['centerpanel'] = $centerpanel;
		$param['listados']    = $this->datasis->listados('TBRUTAS', 'JQ');
		$param['otros']       = $this->datasis->otros('TBRUTAS', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	// Funciones de los Botones
	//
	function bodyscript( $grid0, $grid1 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function tbrutasadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function tbrutasedit(){
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
		function tbrutasshow(){
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
		function tbrutasdel() {
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

		$noco = $this->datasis->llenaopciones("SELECT codofi, CONCAT(codofi,' ', desofi) FROM tbofici ORDER BY codofi", false, 'moficina');
		$noco = str_replace('"',"'",$noco);

		// Modificar Destino
		$bodyscript .= '
		jQuery("#modifid").click( function(){
			var id = jQuery("#newapi'.$grid1.'").jqGrid(\'getGridParam\',\'selrow\');
			var chec = \'\';
			if (id)	{
				var ret = jQuery("#newapi'.$grid1.'").jqGrid(\'getRowData\',id);
				if ( ret.mostrar == \'S\' ){
					chec = \'checked\';
				}
				var mcome1 = "<h1>Destinos</h1>"+
					"<table align=\'center\'>"+
					"<tr><td>Hora: </td><td><input id=\'mhora\'  name=\'mhora\'  size=\'6\' class=\'input\' value=\'"+ret.hora+"\'></td>"+
					"<tr><td>Orden:</td><td><input id=\'morden\' name=\'morden\' size=\'6\' class=\'input\' value=\'"+ret.orden+"\'></td>"+
					"<td align=\'right\'>Mostrar:</td><td align=\'left\'><input type=\'checkbox\' id=\'mmostrar\' name=\'mmostrar\' class=\'input\' value=\'S\' "+chec+" ></td></tr>"+
					"</table>";
				var mprepanom =
				{
					state0: {
						html: mcome1,
						buttons: { Guardar: true, Cancelar: false },
						submit: function(e,v,m,f){
							moficina = f.moficina;
							if (v) {
								$.post("'.site_url('pasajes/tbrutas/destino').'/", { hora: f.mhora, mostrar: f.mmostrar, orden: f.morden, mid: id, oper: \'Edit\' },
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

			} else { $.prompt("<h1>Por favor Seleccione un Destino</h1>");}
		});';


		// Eliminar Destino
		$bodyscript .= '
		jQuery("#elimind").click( function(){
			var id = jQuery("#newapi'.$grid1.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid1.'").jqGrid(\'getRowData\',id);
				$.prompt("<h1>Eliminar Destino Seleccionado</h1>", {
					buttons: { Eliminar: true, Salir: false },
					callback: function(e,v,m,f){
						if (v) {
							$.post("'.site_url('pasajes/tbrutas/destino').'/", { mid: id, oper: \'Del\' },
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


		// Agrgaga Destinos
		$bodyscript .= '
		jQuery("#agregad").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				var mcome1 = "<h1>Destinos</h1>"+
					"<table align=\'center\'>"+
					"<tr><td>Oficina:</tdtd><td colspan=\'3\'>"+"'.$noco.'</td></tr>"+
					"<tr><td>Hora: </td><td><input               id=\'mhora\'    name=\'mhora\'    size=\'6\' class=\'input\' value=\'\'></td>"+
					"<td align=\'right\'>Mostrar:  </td><td align=\'left\'><input type=\'checkbox\' id=\'mmostrar\' name=\'mmostrar\' class=\'input\' value=\'S\'></td></tr>"+
					"</table>";
				var mprepanom =
				{
					state0: {
						html: mcome1,
						buttons: { Guardar: true, Cancelar: false },
						submit: function(e,v,m,f){
							moficina = f.moficina;
							if (v) {
								$.post("'.site_url('pasajes/tbrutas/destino').'/", { oficina: f.moficina, hora: f.mhora, mostrar: f.mmostrar, mid: id, oper: \'Add\' },
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


		$gasto = $this->datasis->llenaopciones("SELECT codgas, CONCAT(codgas,' ', nomgas) FROM tbgastos ORDER BY codgas", false, 'mgasto');
		$gasto = str_replace('"',"'",$gasto);

		// Agrgaga Gastos
		$bodyscript .= '
		jQuery("#agregag").click( function(){
			var id = jQuery("#newapi'.$grid1.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid1.'").jqGrid(\'getRowData\',id);
				var mcome2 = "<h1>Gastos</h1>"+
					"<table align=\'center\'>"+
					"<tr><td>Gasto:</tdtd><td colspan=\'3\'>"+"'.$gasto.'</td></tr>"+
					"<tr><td>Monto: </td><td><input id=\'mmonto\' name=\'mmonto\' size=\'10\' class=\'inputnum\' value=\'0.00\'></td>"+
					"</table>";
				var mprepanom1 =
				{
					state0: {
						html: mcome2,
						buttons: { Guardar: true, Cancelar: false },
						submit: function(e,v,m,f){
							moficina = f.moficina;
							if (v) {
								$.post("'.site_url('pasajes/tbrutas/gasto').'/", { gasto: f.mgasto, monto: f.mmonto, mid: id, oper: \'Add\' },
									function(data){
										$.prompt.getStateContent(\'state1\').find(\'#in_prome3\').text(data);
										$.prompt.goToState(\'state1\');
								});
								return false;
							}
						}
					},
					state1: {
						html: "<h1>Resultado</h1><span id=\'in_prome3\'></span>",
						focus: 1,
						buttons: { Ok:true }
					}
				};
				$.prompt(mprepanom1);

			} else { $.prompt("<h1>Por favor Seleccione una Ruta</h1>");}
		});';

		// Eliminar Gasto
		$bodyscript .= '
		function gastodel(mid){
			var id = jQuery("#newapi'.$grid1.'").jqGrid(\'getGridParam\',\'selrow\');
			$.post("'.site_url($this->url.'gastodel').'/"+mid,
			function(data){
				$.ajax({
					url: "'.base_url().$this->url.'tabla/"+id,
					success: function(msg){
						$("#ladicional").html(msg);
					}
				});
			})
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
			autoOpen: false, height: 300, width: 500, modal: true,
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/TBRUTAS').'/\'+res.id+\'/id\'').';
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
	//  Gestiona Destinos
	//
	function destino(){
		$mid   = $this->input->post('mid');
		$oper  = $this->input->post('oper');
		if ( $oper == 'Add') {
			$moficina = $this->input->post('oficina');
			$mhora    = strtoupper($this->input->post('hora'));
			$mostrar  = $this->input->post('mostrar');

			if ( $mostrar == '' ) $mostrar = 'N';
			$data = array();

			$codrut = $this->datasis->dameval("SELECT codrut FROM tbrutas a WHERE a.id=$mid");

			// Guarda la Primera

			$orden = $this->datasis->dameval("SELECT max(orden)+1 FROM tbdestinos WHERE codrut='".$codrut."' AND codofiorg='".substr($codrut,0,2)."'");
			if ( empty($orden) ) $orden = 1;
			$data['codrut']    = $codrut;
			$data['codofiorg'] = substr($data['codrut'],0,2);
			$data['codofides'] = $moficina;
			$data['hora']      = $mhora;
			$data['mostrar']   = $mostrar;
			$data['orden']     = $orden;
			$this->db->insert('tbdestinos', $data);

			// Guarda la Salida
			if ( $orden > 1 ){
				$data['codrut']    = $codrut;
				$data['codofiorg'] = $moficina;
				$data['codofides'] = $moficina;
				$data['hora']      = $mhora;
				$data['mostrar']   = $mostrar;
				$data['orden']     = $orden;
				$this->db->insert('tbdestinos', $data);
			}

			// Guarda los toques
			if ( $orden > 2 ) {
				$mSQL = "SELECT * FROM tbdestinos WHERE codrut='".$codrut."' AND codofiorg='".substr($codrut,0,2)."' AND orden>1 AND orden<${orden} ORDER BY orden";
				$query = $this->db->query($mSQL);
				if ( $query->num_rows() > 0 ) {
					$i = 1;
					foreach ($query->result() as $row){
						$data['codrut']    = $codrut;
						$data['codofiorg'] = $row->codofides;
						$data['codofides'] = $moficina;
						$data['hora']      = $mhora;
						$data['mostrar']   = $mostrar;
						$data['orden']     = $orden;
						$this->db->insert('tbdestinos', $data);
						$i++;
					}
				}
			}

			$salida = "Destino Guardado";



		} elseif ($oper == 'Edit') {
			$moficina = $this->input->post('oficina');
			$mhora    = strtoupper($this->input->post('hora'));
			$mostrar  = $this->input->post('mostrar');
			$orden    = $this->input->post('orden');

			if ( $mostrar == '' ) $mostrar = 'N';
			$data = array();

			$codrut = $this->datasis->dameval("SELECT codrut FROM tbdestinos a WHERE a.id=$mid");

			$data['hora']     = $mhora;
			$data['mostrar']  = $mostrar;
			$data['orden']    = $orden;

			$this->db->where('id',$mid);
			$this->db->update('tbdestinos', $data);

			$salida = "Destino Guardado";

		} elseif ($oper == 'Del') {
			$codrut = $this->datasis->dameval("SELECT codrut FROM tbdestinos a WHERE a.id=$mid");
			$this->db->query("DELETE FROM tbdestinos WHERE id=$mid");
			$salida = "Destino Eiminado $mid";
		}
/*
		//Arregla la Secuencia
		$mSQL = "SELECT orden, id FROM tbdestinos WHERE codrut='".$codrut."' AND codofiorg='".substr($codrut,0,2)."' ORDER BY orden";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$i = 1;
			foreach ($query->result() as $row){
				$mSQL = "UPDATE tbdestinos SET orden=$i WHERE id=".$row->id;
				$this->db->query($mSQL);
				$i++;
			}
		}
*/


		echo $salida;

	}


	//******************************************************************
	//  Gestiona Gastos
	//
	function gasto(){
		$mid   = $this->input->post('mid');
		$oper  = $this->input->post('oper');
		if ( $oper == 'Add') {
			$gasto = $this->input->post('gasto');
			$monto = $this->input->post('monto');

			$data = array();
			$codrut = $this->datasis->dameval("SELECT codrut    FROM tbdestinos a WHERE a.id=$mid");
			$codofi = $this->datasis->dameval("SELECT codofides FROM tbdestinos a WHERE a.id=$mid");

			$data['codrut'] = $codrut;
			$data['codofi'] = $codofi;
			$data['codgas'] = $gasto;
			$data['monto']  = $monto;

			$this->db->insert('tbgastoruta', $data);

			$salida = "Fasto Agregado";
		}

		echo $salida;

	}


	//******************************************************************
	//  Eliminar Gastos
	//
	function gastodel($mid){

		$this->db->query("DELETE FROM tbgastoruta WHERE id=$mid");
		$salida = "Gasto Eiminado $mid";
		echo $salida;

	}



	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;


		$grid->addField('codrut');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));


		$grid->addField('horsal');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));


		$grid->addField('tipuni');
		$grid->label('Bus');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'align'         => "'center'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('origen');
		$grid->label('Or&iacute;gen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 155,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->addField('destino');
		$grid->label('Destino Final');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 155,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));

		$grid->addField('tipserv');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
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
				}
			}'
		);


		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('TBRUTAS','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('TBRUTAS','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('TBRUTAS','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('TBRUTAS','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: tbrutasadd, editfunc: tbrutasedit, delfunc: tbrutasdel, viewfunc: tbrutasshow");

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
		$mWHERE = $grid->geneTopWhere('tbrutas');

		$response   = $grid->getData('tbrutas', array(array()), array(), false, $mWHERE, 'codrut' );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM tbrutas WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('tbrutas', $data);
					echo "Registro Agregado";

					logusu('TBRUTAS',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM tbrutas WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM tbrutas WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE tbrutas SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("tbrutas", $data);
				logusu('TBRUTAS',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('tbrutas', $data);
				logusu('TBRUTAS',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM tbrutas WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM tbrutas WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM tbrutas WHERE id=$id ");
				logusu('TBRUTAS',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codrut');
		$grid->label('Codrut');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));

		$grid->addField('orden');
		$grid->label('Orden');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'width'         => 60,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));

		$grid->addField('codofiorg');
		$grid->label('Ofic.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));

		$grid->addField('odesofi');
		$grid->label('Origen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'left'",
			'edittype'      => "'text'",
			'width'         => 150,
		));

		$grid->addField('codofides');
		$grid->label('Ofic');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));

		$grid->addField('ddesofi');
		$grid->label('Destino');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'left'",
			'edittype'      => "'text'",
			'width'         => 150,
		));

		$grid->addField('mostrar');
		$grid->label('Ver');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'center'",
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));

		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
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
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
				if (id){
					$.ajax({
						url: "'.site_url($this->url).'/tabla/"+id,
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
		$grid->setAdd(    true ); //$this->datasis->sidapuede('TBDESTINOS','INCLUIR%' ));
		$grid->setEdit(   true ); //$this->datasis->sidapuede('TBDESTINOS','MODIFICA%'));
		$grid->setDelete( true ); //$this->datasis->sidapuede('TBDESTINOS','BORR_REG%'));
		$grid->setSearch( false); //$this->datasis->sidapuede('TBDESTINOS','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: tbdestinosadd, editfunc: tbdestinosedit, delfunc: tbdestinosdel, viewfunc: tbdestinosshow");

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
			$id = $this->datasis->dameval("SELECT id FROM tbrutas ORDER BY codrut LIMIT 1");
		}
		if(empty($id)) return '';
		$dbid = $this->db->escape($id);

		$row = $this->datasis->damerow('SELECT codrut FROM tbrutas WHERE id='.$dbid);

		$codrut = $this->db->escape($row['codrut']);

		$grid    = $this->jqdatagrid;
		$mSQL    = "
		SELECT a.*, b.desofi odesofi, c.desofi ddesofi
		FROM tbdestinos a
			JOIN tbofici b ON a.codofiorg=b.codofi
			JOIN tbofici c ON a.codofides=c.codofi
		WHERE codrut=${codrut}
		ORDER BY orden, a.codofiorg ";

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
				$check = $this->datasis->dameval("SELECT count(*) FROM tbdestinos WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('tbdestinos', $data);
					echo "Registro Agregado";

					logusu('TBDESTINOS',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM tbdestinos WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM tbdestinos WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE tbdestinos SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("tbdestinos", $data);
				logusu('TBDESTINOS',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('tbdestinos', $data);
				logusu('TBDESTINOS',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM tbdestinos WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM tbdestinos WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM tbdestinos WHERE id=$id ");
				logusu('TBDESTINOS',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}



	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$("#horsal").mask("99:99a");
		});
		';

		$edit = new DataEdit( '', 'tbrutas');

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

		$edit->codrut = new inputField('Codigo','codrut');
		$edit->codrut->rule='';
		$edit->codrut->size =8;
		$edit->codrut->maxlength =6;

		$edit->horsal = new inputField('Hora','horsal');
		$edit->horsal->rule='';
		$edit->horsal->size =8;
		$edit->horsal->maxlength =6;

		$edit->tipuni = new dropdownField('Unidad','tipuni');
		$edit->tipuni->rule='required';
		$edit->tipuni->options('SELECT tipbus, CONCAT(tipbus, " ", desbus) nombre FROM tbmodbus ORDER BY tipbus');

		$edit->origen = new inputField('Origen','origen');
		$edit->origen->rule='';
		$edit->origen->size =30;
		$edit->origen->maxlength =100;

		$edit->destino = new inputField('Destino','destino');
		$edit->destino->rule='';
		$edit->destino->size =30;
		$edit->destino->maxlength =100;

		$edit->tipserv = new dropdownField('Tipo','tipserv');
		$edit->tipserv->rule='required';
		$edit->tipserv->options('SELECT tipserv, CONCAT(tipserv, " ", desserv) nombre FROM tbtipserv ORDER BY tipserv');

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
		if (!$this->db->table_exists('tbrutas')) {
			$mSQL="CREATE TABLE `tbrutas` (
			  `tipserv` varchar(2) DEFAULT '',
			  `codrut` varchar(6) NOT NULL DEFAULT '',
			  `horsal` varchar(6) DEFAULT '',
			  `tipuni` varchar(5) DEFAULT '',
			  `origen` varchar(100) DEFAULT '',
			  `destino` varchar(100) DEFAULT '',
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codrut` (`codrut`)
			) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('tbrutas');
		//if(!in_array('<#campo#>',$campos)){ }
	}

	//******************************************************************
	//
	//
	function tabla() {
		$id = $this->uri->segment($this->uri->total_segments());

		$codrut = $this->datasis->dameval("SELECT codrut   FROM tbdestinos WHERE id='$id'");
		$codofi = $this->datasis->dameval("SELECT codofides FROM tbdestinos WHERE id='$id'");

		$mSQL = "SELECT a.codgas, b.nomgas, a.monto, a.id FROM tbgastoruta a JOIN tbgastos b ON a.codgas=b.codgas WHERE a.codrut='$codrut' AND a.codofi='$codofi' ORDER BY a.codgas ";
		$query = $this->db->query($mSQL);
		$salida = '';
		if ( $query->num_rows() > 0 ){
			$salida = "<br><table width='100%' border=1>";
			$salida .= "<tr bgcolor='#E7E3E7'><td>Cod</td><td align='center'>Gasto</td><td align='center'>Monto</td><td>X</td></tr>";

			foreach ($query->result_array() as $row)
			{
				$salida .= "<tr>";
				$salida .= "<td>".$row['codgas']."</td>";
				$salida .= "<td>".$row['nomgas'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
				$salida .= "<td align='center'><a  style='vertical-align:center;' href='#' onclick='gastodel(".$row['id'].")' >".img(array('src' =>"images/N.gif",  'height' => 8, 'alt' => 'Eliminar', 'title' => 'Eliminar', 'border'=>'0'))."</a></td>";
				$salida .= "</tr>";
			}
			$salida .= "</table>";
		}
		echo $salida;


	}
}
?>
