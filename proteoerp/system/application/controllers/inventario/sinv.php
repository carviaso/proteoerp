<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
include('common.php');
class Sinv extends Controller {
	var $mModulo = 'SINV';
	var $titp    = 'Inventario de Productos';
	var $tits    = 'Inventario de Productos';
	var $url     = 'inventario/sinv/';

	function Sinv(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'SINV', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 950, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}


	//******************************************************************
	//Layout en la Ventana
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		$readyLayout = $grid->readyLayout2( 216	, 135, $param['grids'][0]['gridname']);

		$WpAdic = "
		<tr><td><div class=\"anexos\">
			<table cellpadding='0' cellspacing='0'>
				<tr>
					<td style='vertical-align:top;'><div class='botones'><a style='width:94px;text-align:left;vertical-align:top;' href='#' id='gmarcas'>".img(array('src' =>"images/tux1.png",  'height' => 15, 'alt' => 'Crear Marcas',    'title' => 'Crear Marcas',   'border'=>'0'))."+Marcas</a></div></td>
					<td style='vertical-align:top;'><div class='botones'><a style='width:94px;text-align:left;vertical-align:top;' href='#' id='gunidad'>".img(array('src' =>"images/unidad.gif",'height' => 15, 'alt' => 'Crear Unidades',  'title' => 'Crear Unidades', 'border'=>'0'))."+Unidad</a></div></td>
				</tr>
				<tr>
					<td style='vertical-align:top;'><div class='botones'><a style='width:94px;text-align:left;vertical-align:top;' href='#' id='hinactivo'>".img(array('src' =>"images/basura.png", 'height' => 15, 'alt'=>'Mostrar/Ocultar Inactivos', 'title' => 'Mostrar/Ocultar Inactivos', 'border'=>'0'))."Inactivos</a></div></td>
					<td style='vertical-align:top;'><div class='botones'><a style='width:94px;text-align:left;vertical-align:top;' href='#' id='bbarras'  >".img(array('src' =>"images/barcode.png",'height' => 15, 'alt'=>'Barras Adicionales',        'title' => 'Barras Adicionales',        'border'=>'0'))."Barras</a></div></td>
				</tr>";

		if ( $this->datasis->traevalor('SUNDECOP') == 'S'){
			$WpAdic .= "
				<tr>
					<td style='vertical-align:top;'><div class='botones'><a style='width:94px;text-align:left;vertical-align:top;' href='#' id='sundecop'>".img(array('src'=>"images/sundecop.jpeg", 'height'=>15, 'alt'=>'Parametros SUNDECOP', 'title'=>'Parametros SUNDECOP', 'border'=>'0'))."SuNdeCoP</a></div></td>
					<td style='vertical-align:top;'><div class='botones'><a style='width:94px;text-align:left;vertical-align:top;' href='#' id='bpactivo'>".img(array('src'=>"images/lab.png",       'height'=>15, 'alt'=>'Crear Unidades',      'title'=>'Crear Unidades',      'border'=>'0'))."P.Activo</a></div></td>
				</tr>
			";
		}

		$WpAdic .= "
			</table>
			</div>
		</td></tr>\n
		";

		$grid->setWpAdicional($WpAdic);

		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita', 'title'=>'Agregar/Editar Registro'),
			array('id'=>'fborra', 'title'=>'Eliminar registro'),
			array('id'=>'fshow' , 'title'=>'Varios')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$link2  =site_url('inventario/sinv/recalcular');

		$funciones = $this->funciones( $param['grids'][0]['gridname']);

		//Panel Central y Sur
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'] );

		$param['script']      = script('sinvmaes.js');
		$param['WestPanel']   = $WestPanel;
		$param['funciones']   = $funciones;
		$param['readyLayout'] = $readyLayout;
		$param['centerpanel'] = $centerpanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('SINV', 'JQ');
		$param['otros']       = $this->datasis->otros('SINV', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;

		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	//   Funciones
	//
	function funciones($grid0){

		//Barras adicionales
		$funciones = '
		$("#bpos1").jqGrid({
			url:\''.site_url('inventario/sinv/bpos1').'\',
			ajaxGridOptions: { type: "POST"},
			jsonReader: { root: "data", repeatitems: false},
			datatype: "json",
			hiddengrid: true,
			postdata: { sinvid: "wapi"},
			width: 190,
			height: 100,
			colNames:[\'id\', \'codigo\', \'Adicional\'],
			colModel:[
				{name:\'id\',index:\'id\', width:10, hidden:true},
				{name:\'codigo\',index:\'codigo\', width:105, editable:false, hidden:true, },
				{name:\'suplemen\',index:\'suplemen\', width:105, editable:true},
			],
			rowNum:1000,
			pginput: false,
			pgbuttons: false,
			rowList:[],
			pager: \'#pbpos1\',
			sortname: \'id\',
			viewrecords: false,
			sortorder: "desc",
			editurl: \''.site_url('inventario/sinv/bpos1').'\',
			caption: "Barras Adicionales"
		});
		jQuery("#bpos1").jqGrid(\'navGrid\',"#pbpos1",{edit:false, add:true, del:true, search: false, addfunc: bposadd });

		function bposadd(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				$.prompt( "<h1>Agregar nuevo codigo de Barras</h1><center><input type=\'text\' id=\'mcodbar\' name=\'mcodbar\' value=\'\' maxlengh=\'15\' size=\'15\' ></center><br/>", {
					buttons: { Agregar: true, Cancelar: false },
					submit: function(e,v,m,f){
						if(v){
							if( f.mcodbar > 0 ) {
								$.ajax({
									type: "POST",
									url: "'.site_url('inventario/sinv/sinvbarras').'",
									data: { id: id, codigo: f.mcodbar },
									complete: function(){
										$("#bpos1").trigger("reloadGrid");
									}
								});
							} else {
								alert("Debe colocar un numero");
							}
						}
					}
				});
			}
		}';

		$funciones = '
		function factivo(el, val, opts){
			var meco=\'<div><img src="'.base_url().'images/blank.png" width="20" height="18" border="0" /></div>\';
			if ( el == "N" ){
				meco=\'<div><img src="'.base_url().'images/basura.png" width="20" height="20" border="0" /></div>\';
			}
			return meco;
		}';

		//Recalcular Precios
		$funciones .= '
		function recalcular(){
			var seguro = true;
			var mtipo="M";
			$.prompt( "<h1>Recalcular Precios de Inventario</h1><p><b>Margenes:</b> Recalcula los margenes dejando fijos los precios</p><p><b>Precios:</b> Recalcula los precios seg&uacute;n los margenes</p>", {
				buttons: { Margenes: 1, Precios: 2, Cancelar: 0 },
				submit: function(e,v,m,f){
					if(v == 1){
						$.ajax({ url: "'.site_url('inventario/sinv/recalcular/M').'",
							complete: function(){ alert(("Recalculo Finalizado")) }})
					}else if( v == 2){
						$.ajax({ url: "'.site_url('inventario/sinv/recalcular/P').'",
							complete: function(){ alert(("Recalculo Finalizado")) }})
					}
				}
			})
		}';


		//Permite asignar a varios producto el mismo precio
		$aprecios="<form id='setprecio'>
		<fieldset>
			<legend>Cambio de precios:</legend>
			Precio1: <input name='_p1' id='_p1' style='text-align: right;width: 100px' type='text'>
			Precio3: <input name='_p3' id='_p3' style='text-align: right;width: 100px' type='text'><br>
			Precio2: <input name='_p2' id='_p2' style='text-align: right;width: 100px' type='text'>
			Precio4: <input name='_p4' id='_p4' style='text-align: right;width: 100px' type='text'><br>
			<p style='text-align:center'>Costo:   <input name='_cos' id='_cos' style='text-align: right;width: 100px' type='text'></p>
		</fieldset>
		</form>";
		$aprecios=str_replace(array("\n","\t"),array('',''),$aprecios);
		$funciones .= '
		function asignaprecios(){
			var s = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selarrrow\');
			if(s.length > 0){
				$.prompt("<h1>Precios de Productos</h1>'.$aprecios.'", {
					buttons: { Cambiar: 1, Cancelar: 2 },
					submit: function(e,v,m,f){
						if(v==1){
							$.ajax({
								type: "POST",
								data: {p1:$("#_p1").val(),p2:$("#_p2").val(),p3:$("#_p3").val(),p4:$("#_p4").val(),cos:$("#_cos").val(),ids:s },
								url: "'.site_url($this->url.'asignaprecio').'",
								complete:
									function(r,s,x){
										alert(r);
									}
							});
						}else if( v == 2){

						}
					}
				});
			}else{
				alert("Seleccione al menos un producto");
			}
		}';

		// Consulta de Movimiento
		$funciones .= '
		function consulta(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				window.open(\''.site_url('inventario/sinv/consulta/').'/\'+ret.id, \'_blank\', \'width=800, height=600, scrollbars=yes, status=yes, resizable=yes,screenx=((screen.availHeight/2)-300), screeny=((screen.availWidth/2)-400)\');
			} else {
				$.prompt("<h1>Por favor Seleccione un Producto</h1>");
			}
		};
		';


		// Redondear Precios
		$funciones .= '
		function redondear(){
			var fredo=$.prompt("<h1>Redondear solo cuando el precio sea mayor a:</h1><center><input class=\'inputnum\' type=\'text\' id=\'fredo_mayor\' name=\'mayor\' value=\'0.00\' maxlengh=\'10\' size=\'10\' > <br>Tenga en cuenta de que esta operacion es irreversible.</center><br/>", {
				buttons: { Redondear: true, Cancelar: false },
				submit: function(e,v,m,f){
					if (v) {
						if( f.mayor > 0 ) {
							$.ajax({ url: "'.site_url('inventario/sinv/redondear').'/"+f.mayor,
							complete: function(){ alert(("Redondeo Finalizado")) }
							});
						} else {
							alert("Debe colocar un numero mayor que 0");
						}
					}
				}
			});
			fredo.bind("promptloaded", function(e){ $("#fredo_mayor").numeric("."); });
		};';

		//Aumento de Precios
		$funciones .= '
		function auprec(){
			$.prompt( "<h1>Porcentaje de Aumento o Disminucion (-):</h1><center><input class=\'inputnum\' type=\'text\' id=\'porcen\' name=\'porcen\' value=\'0.00\' maxlengh=\'10\' size=\'10\' ></center><br/>", {
				buttons: { Aplicar: true, Cancelar: false },
				submit: function(e,v,m,f){
					if (v) {
						if( f.porcen != 0 ) {
							$.ajax({ url: "'.site_url('inventario/sinv/auprec').'/"+f.porcen,
							success: function(data){ alert((data)) }
							});
						} else {
							alert("Debe colocar un porcentaje diferente a 0");
						}
					}
				}
			});
		};
		';

		// Fija Margenes
		$funciones .= '
		function fijamarg(){
			$margen1 = 35;
			$margen2 = 30
			$margen3 = 25
			$margen4 = 20;
			$.prompt( "<h1>Fijar Porcentaje:</h1><br><center><table width=\'100%\'><tr><td>Margen 1</td><td><input class=\'inputnum\' type=\'text\' id=\'margen1\' name=\'margen1\' value=\'35.00\' maxlengh=\'10\' size=\'10\' ></tb></tr><tr><td>Margen 2</td><td><input class=\'inputnum\' type=\'text\' id=\'margen2\' name=\'margen2\' value=\'30.00\' maxlengh=\'10\' size=\'10\' ></tb></tr><tr><td>Margen 3</td><td><input class=\'inputnum\' type=\'text\' id=\'margen3\' name=\'margen3\' value=\'25.00\' maxlengh=\'10\' size=\'10\' ></tb></tr><tr><td>Margen 4</td><td><input class=\'inputnum\' type=\'text\' id=\'margen4\' name=\'margen4\' value=\'20.00\' maxlengh=\'10\' size=\'10\' ></tb></tr></table></center><br/>", {
				buttons: { Aplicar: true, Cancelar: false },
				submit: function(e,v,m,f){
					if (v) {
						if( f.margen1>0  && f.margen2>0 && f.margen3>0 && f.margen4>0 ) {
							$.ajax({ url: "'.site_url('inventario/sinv/fijamarg').'/"+f.margen1+"/"+f.margen2+"/"+f.margen3+"/"+f.margen4,
							success: function(data){ alert((data)) }
							});
						} else {
							alert("Debe colocar porcentajes mayores a 0");
						}
					}
				}
			});
		}
		';

		//Aumento de Precios al Mayor
		$funciones .= '
		function auprecm(){
			$.prompt( "<h1>Porcentaje de Aumento o Disminucion (-) Precios al Mayor:</h1><center><input class=\'inputnum\' type=\'text\' id=\'porcen\' name=\'porcen\' value=\'0.00\' maxlengh=\'10\' size=\'10\' ></center><br/>", {
				buttons: { Aplicar: true, Cancelar: false },
				submit: function(e,v,m,f){
					if (v) {
						if( f.porcen != 0 ) {
							$.ajax({ url: "'.site_url('inventario/sinv/auprecm').'/"+f.porcen,
							complete: function(){ alert(("Aumento Finalizado")) }
							});
						} else {
							alert("Debe colocar un porcentaje mayor que 0");
						}
					}
				}
			});
		};
		';

		//Cambio de IVA
		$mfecha = $this->datasis->dameval('SELECT MAX(fecha) FROM civa');
		$mSQL = "
		SELECT 0 exento,  'EXCENTO 0' nombre
		UNION ALL
		SELECT tasa,      CONCAT('TASA ',tasa)      nombre FROM civa WHERE fecha='$mfecha'
		UNION ALL
		SELECT redutasa,  CONCAT('REDUCIDA ',redutasa)  nombre FROM civa WHERE fecha='$mfecha'
		UNION ALL
		SELECT sobretasa, CONCAT('SOBRETASA ',sobretasa) nombre FROM civa WHERE fecha='$mfecha'
		";
		$ivas = $this->datasis->llenaopciones($mSQL, true, 'mivas');
		$ivas = str_replace('"',"'",$ivas);
		$funciones .= '
		function cambiva(){
			$.prompt( "<h1>Cambio de I.V.A.:</h1><br/><center>'.$ivas.'</center><br/>", {
				buttons: { Aplicar: true, Cancelar: false },
				submit: function(e,v,m,f){
					if (v) {
						$.ajax({ url: "'.site_url('inventario/sinv/cambiva').'/"+f.mivas,
							success: function(data){ alert((data)) }
						});
					}
				}
			});
		};
		';

		// Cambiar Ubicaciones
		$funciones .= '
		function cambiaubica(){
			$.prompt( "<h1>Cambiar Ubicacion de los productos filtrados):</h1><br/><center><input  type=\'text\' id=\'mubica\' name=\'mubica\' value=\'\' maxlengh=\'9\' size=\'10\' ></center><br/>", {
				buttons: { Aplicar: true, Cancelar: false },
				submit: function(e,v,m,f){
					if (v) {
						$.ajax({
							url: "'.site_url('inventario/sinv/cambiaubica').'/"+f.mubica,
							complete: function(){ alert(("Cambio Finalizado")) }
						});
					}
				}
			});
		};
		';

		//Cambia y fusiona codigo
		$funciones .= '
		function sinvcodigo(mviejo){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				var yurl = "";
				$.prompt("<h1>Cambiar el codigo ("+ret.codigo+") por:</h1><center><input type=\'text\' id=\'mcodigo\' name=\'mcodigo\' value=\'"+$.trim(ret.codigo)+"\' maxlengh=\'10\' size=\'15\' ><br/>Mantener producto anterior <input type=\'checkbox\' id=\'mdeja\' name=\'mdeja\' value=\'1\' ><br/></center>", {
					buttons: { Cambiar: true, Cancelar: false },
					submit: function(e,v,m,f){
						if (v) {
							if ( f.mdeja != 1 ) { f.mdeja = 0;}
							if( f.mcodigo == null ){
								alert("Cancelado por el usuario");
							} else if( f.mcodigo == "" ) {
								alert("Cancelado,  Codigo vacio");
							} else if( $.trim(f.mcodigo) == $.trim(ret.codigo) ) {
								alert("No registro ningun cambio");
							} else {
								yurl = encodeURIComponent(mcodigo);
								$.ajax({
									url: \''.site_url('inventario/sinv/sinvcodigoexiste').'\',
									global: false,
									type: "POST",
									data: ({ codigo : f.mcodigo }),
									dataType: "text",
									async: false,
									success: function(sino) {
										if (sino.substring(0,1)=="S"){
											apprise(
												"Ya existe el codigo <div style=\"font-size: 200%;font-weight: bold \">"+f.mcodigo+"</"+"div><p>si prosigue se eliminara el producto anterior y<br/> todo el movimiento de este, pasara al codigo "+mcodigo+"</"+"p> <p style=\"align: center;\">Desea <strong>Fusionarlos?</"+"strong></"+"p>",
												{"verify":true,"textYes":"Confirmar Fusion","textNo":"Cancelar"},
												function(r){
													if (r) { sinvcodigocambia("S", $.trim(ret.codigo), f.mcodigo, f.mdeja ); }
												}
											);
										} else {
											apprise(
												"<h1>Sustitur el codigo actual Por:</h1> <center><h2 style=\"background: #ddeedd\">"+f.mcodigo+"</"+"h2></"+"center> <p>Al cambiar de codigo el producto, todos los<br/> movimientos y estadisticas se cambiaran<br/> correspondientemente.</"+"p> ",
												{"verify":true,"textYes":"Aceptar","textNo":"Cancelar"},
												function(r) {
													if (r) { sinvcodigocambia("N", $.trim(ret.codigo), f.mcodigo, f.mdeja); }
												}
											)
										}
									},
									error: function(h,t,e) { alert("Error..codigo="+yurl+" ",e) }
								});
							}
						}
					}
				})

			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};
		';

		//Cambia y fusiona codigo
		$funciones .= '
		function sinvcodigocambia( mtipo, mviejo, mcodigo, mdeja ) {
			var id   = $("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			var ret  = $("#newapi'.$grid0.'").getRowData(id);
			$.ajax({
				url: \''.site_url('inventario/sinv/sinvcodigo').'\',
				global: false,
				type: "POST",
				data: ({ tipo:  mtipo,
					viejo: encodeURIComponent(mviejo),
					codigo: encodeURIComponent(mcodigo),
					deja: mdeja }),
				dataType: "text",
				async: false,
				success: function(sino) {
					alert("Cambio finalizado "+sino);
					$("#newapi'.$grid0.'").trigger("reloadGrid");
				},
				error: function(h,t,e) {alert("Error.." )}
			});
		};
		';

		// Busca si esta la opcion en tmenus
		$mSQL = "SELECT COUNT(*) FROM tmenus WHERE modulo='SINVOTR' AND proteo='cambiamarca'";
		if ( $this->datasis->dameval($mSQL) == 0 )
			$this->db->query("INSERT INTO tmenus SET modulo='SINVOTR', secu=14, titulo='Cambiar Marcas', mensaje='Cambia las Marcas de los productos seleccionados', proteo='cambiamarca'");

		// Cambia las marcas de los productos seleccionados
		$mSQL = "SELECT marca, marca nombre FROM marc ORDER BY marca";
		$marca = $this->datasis->llenaopciones($mSQL, true, 'mmarca');
		$marca = str_replace('"',"'",$marca);
		$funciones .= '
		function cambiamarca(){
			var s = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selarrrow\');
			if ( s.length == 0 ){
				$.prompt("<h1>Debe seleccionar al menos un Producto</h1>");
			} else {
				var estados = {
				state0: {
					html : "<h1>Cambiar Marca de los productos seleccionados:</h1><br/><center>'.$marca.'</center><br/>",
					buttons: { Aplicar: true, Cancelar: false },
					focus: 1,
					submit: function(e,v,m,f){
						if (v) {
							if( f.mmarca==null ){
								alert("Cancelado por el usuario"); // No se da
							} else if( f.mmarca=="-" ) {
								$.prompt.goToState("state0");
							} else {
								e.preventDefault();
								$.ajax({
									url: "'.site_url('inventario/sinv/sinvcammarca/').'",
									global: false,
									type: "POST",
									data: ({ marca : f.mmarca, productos : s }),
									dataType: "text",
									async: false,
									success: function(sino) {
										$("#newapi'.$grid0.'").trigger("reloadGrid");
										$.prompt.getStateContent(\'state1\').find(\'#in_prome1\').text(sino);
										$.prompt.goToState(\'state1\');
									},
									error: function(h,t,e)  {
										$.prompt.getStateContent(\'state1\').find(\'#in_prome1\').text("Error..marca="+f.marca+" "+e);
										$.prompt.goToState(\'state1\');
								}});
							}
						}
					}
				},
				state1: {
					html: "<h1><div id=\'in_prome1\'></div></h1>",
					buttons: { Volver: -1, Salir: 0  },
					submit: function(e,v,m,f){
						if ( v == 0) $.prompt.close()
						else $.prompt.goToState("state0");
						e.preventDefault();
					}
				}};
				$.prompt(estados);
			}
		};
		';

		// Busca si estan la opcion en tmenus
		$mSQL = "SELECT COUNT(*) FROM tmenus WHERE modulo='SINVOTR' AND proteo='cambiagrupo'";
		if ( $this->datasis->dameval($mSQL) == 0 )
			$this->db->query("INSERT INTO tmenus SET modulo='SINVOTR', secu=15, titulo='Cambiar Grupos', mensaje='Cambia el Grupo de los productos seleccionados', proteo='cambiagrupo'");


		// Cambia los grupos de los productos seleccionados
		$mSQL   = "SELECT grupo, CONCAT(grupo,' ', nom_grup) nombre FROM grup WHERE tipo='I' ORDER BY grupo";
		$mgrupo = $this->datasis->llenaopciones($mSQL, true, 'mgrupo');
		$mgrupo = str_replace('"',"'",$mgrupo);
		$funciones .= '
		function cambiagrupo(){
			var s = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selarrrow\');
			if ( s.length == 0 ){
				$.prompt("<h1>Debe seleccionar al menos un Producto</h1>");
			} else {
				$.prompt( "<h1>Cambiar Grupo de los productos seleccionados:</h1><br/><center>'.$mgrupo.'</center><br/>",
				{
					buttons: { Aplicar: true, Cancelar: false },
					submit: function(e,v,m,f){
						if (v) {
							if( f.mgrupo==null ){
								apprise("Cancelado por el usuario");
							} else if( f.mgrupo == "" ) {
								apprise("<h1>Cancelado</h1>Grupos vacios");
							} else {
								$.ajax({
									url: "'.site_url("inventario/sinv/sinvcamgrup/").'",
									global: false,
									type: "POST",
									data: ({ grupo : f.mgrupo, productos : s }),
									dataType: "text",
									async: false,
									success: function(sino) {
										alert("Informacion: "+sino);
										$("#newapi'.$grid0.'").trigger("reloadGrid");
									},
									error: function(h,t,e)  { apprise("Error..grupo="+f.mgrupo+" ",e) }
								});
							}

						}
					}
				});
			}
		};
		';

		// Busca si estan la opcion en tmenus
		$mSQL = "SELECT COUNT(*) FROM tmenus WHERE modulo='SINVOTR' AND proteo='recaldolar'";
		if ( $this->datasis->dameval($mSQL) == 0 )
			$this->db->query("INSERT INTO tmenus SET modulo='SINVOTR', secu=16, titulo='Recalcular $', mensaje='Recalcular precios segun cambio de dolar a los productos seleccionados', proteo='recaldolar'");

		// Cambia los precios segun el valor del dolar
		$funciones .= '
		function recaldolar(){
			$.prompt( "<h1>Modificar precios segun nueva tasa de Cambio:</h1><br/><center><input class=\'inputnum\' type=\'text\' id=\'mcambio\' name=\'mcambio\' value=\'1\' maxlengh=\'10\' size=\'15\' ></center><br/>",
			{
				buttons: { Aplicar: true, Cancelar: false },
				submit: function(e,v,m,f){
					if (v) {
						if( f.mcambio==1 ){
							apprise("Para una tasa de 1 no es necesario hacer nada!!");
						} else if( f.mcambio == 0 ) {
							apprise("<h1>Cancelado</h1>Tasa = 0 ");
						} else {
							$.ajax({
								url: "'.site_url("inventario/sinv/recaldolar/").'",
								global: false,
								type: "POST",
								data: ({ cambio : f.mcambio }),
								dataType: "text",
								async: false,
								success: function(sino) {
									alert("Informacion: "+sino);
									$("#newapi'.$grid0.'").trigger("reloadGrid");
								},
								error: function(h,t,e)  { apprise("Error..grupo="+f.mgrupo+" ",e) }
							});
						}
					}
				}
			});
		};
		';

		return $funciones;

	}

	function asignaprecio(){
		$pp1  =round(floatval($this->input->post('p1')),2);
		$pp2  =round(floatval($this->input->post('p2')),2);
		$pp3  =round(floatval($this->input->post('p3')),2);
		$pp4  =round(floatval($this->input->post('p4')),2);
		$costo=round(floatval($this->input->post('cos')),2);
		$ids  = $this->input->post('ids');

		if(is_array($ids)){
			if($pp1>=$pp2 && $pp2>=$pp3 && $pp3>=$pp4 && $pp1>$costo && $pp2>$costo && $pp3>$costo && $pp4>$costo && $pp1*$pp2*$pp3*$pp4>0){
				foreach($ids as $id){
					$id=intval($id);
					if($id>0){
						if($costo>0){
							$setcosto=",ultimo  = IF(ultimo=0,${costo},ultimo),
							pond    = IF(pond=0  ,${costo},pond) ";
						}
						$mSQL="UPDATE sinv SET
							precio1 = ${pp1}, base1=ROUND(${pp1}*100/(100+iva),2),
							precio2 = ${pp2}, base2=ROUND(${pp2}*100/(100+iva),2),
							precio3 = ${pp3}, base3=ROUND(${pp3}*100/(100+iva),2),
							precio4 = ${pp4}, base4=ROUND(${pp4}*100/(100+iva),2) ${setcosto}
							WHERE id=${id} AND formcal IN ('P','U','M')";
						$this->db->simple_query($mSQL);

						$mSQL="UPDATE sinv SET
							margen1=ROUND(100-((IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(pond,ultimo)))*100)/base1),2),
							margen2=ROUND(100-((IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(pond,ultimo)))*100)/base2),2),
							margen3=ROUND(100-((IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(pond,ultimo)))*100)/base3),2),
							margen4=ROUND(100-((IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(pond,ultimo)))*100)/base4),2)
							WHERE formcal IN ('P','U','M') AND id=${id}";
						$this->db->simple_query($mSQL);

						logusu('sinv','Cambio de precios producto id='.$id);
					}
				}
				echo 'Precios cambiados';
			}else{
				echo 'Precios no cumplen con la jerarquia';
			}
		}
	}
	//******************************************************************
	// Funciones de los Botones
	//
	//
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';
		$ngrid = '#newapi'.$grid0;
		$bodyscript .= '
		var verinactivos = 0;
		var mstatus = "";
		';

		// Agregar
		$bodyscript .= '
		function sinvadd() {
			var id   = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			var murl = "'.site_url('inventario/sinv/dataedit/create').'";
			var grid = jQuery("'.$ngrid.'");
			mstatus = "I";
			if (id)  murl = murl+"/"+id ;
			$.post(murl,
			function(data){
				$("#fborra").html("");
				$("#fedita").html(data);
				$("#fedita").dialog({
					autoOpen: false, height: 450, width: 700, modal: true,
					buttons: {
						"Guardar y Cerrar": function(){
							var murl = $("#df1").attr("action");
							$.ajax({
								type: "POST", dataType: "html", async: false,
								url: murl,
								data: $("#df1").serialize(),
								success: function(r,s,x){
									try{
										var json = JSON.parse(r);
										if(json.status == "A"){
											$("#fedita").dialog( "close" );
											grid.trigger("reloadGrid");
											$.prompt("<h1>Registro Guardado</h1>",{
												submit: function(e,v,m,f){
													setTimeout(function(){ $("'.$ngrid.'").jqGrid(\'setSelection\',json.pk.id);}, 500);
												}
											});
											idactual = json.pk.id;
											return true;
										}else{
											$.prompt("Error: "+json.mensaje);
										}
									}catch(e){
										$("#fedita").html(r);
									}
								}
							})
						},
						"Guardar y Seguir": function(){
							var bValid = true;
							var murl = $("#df1").attr("action");
							$.ajax({
								type: "POST", dataType: "html", async: false,
								url: murl,
								data: $("#df1").serialize(),
								success: function(r,s,x){
									try{
										var json = JSON.parse(r);
										if(json.status == "A"){
											$("#fedita").dialog( "close" );
											grid.trigger("reloadGrid");
											$.prompt("<h1>Registro Guardado</h1>",{
												submit: function(e,v,m,f){
													setTimeout(function(){ $("'.$ngrid.'").jqGrid(\'setSelection\',json.pk.id);}, 500);
												}
											});
											idactual = json.pk.id;
											return true;
										}else{
											$.prompt("Error: "+json.mensaje);
										}
									}catch(e){
										$("#fedita").html(r);
									}
								}
							})
						},
						"Cancelar": function(){
							$("#fedita").html("");
							$( this ).dialog( "close" );
						}
					},
					close: function(){
						$("#fedita").html("");
					}
				});
				$("#fedita").dialog( "open" );
			})
		};';


		$bodyscript .= '
		function sinvedit() {
			var grid = jQuery("'.$ngrid.'");
			var id     = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret     = $("'.$ngrid.'").getRowData(id);
				var mstatus = "E";
				$.post("'.site_url('inventario/sinv/dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog({
						autoOpen: false, height: 450, width: 700, modal: true,
						buttons: {
							"Guardar": function(){
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
													}
												});
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
							}
						},
						close: function(){
							$("#fedita").html("");
						}
					});
					$("#fedita").dialog( "open" );
				});
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function sinvdel() {
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm("Seguro desea eliminar el registro?")){
					var ret    = $("'.$ngrid.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						$("#fedita").html("");
						try{
							var json = JSON.parse(data);
							if(json.status == "A"){
								$.prompt("<h1>Registro eliminado</h1>");
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
							}else{
								$.prompt("<h1>Registro no se puede eliminado</h1>");
							}
						}catch(e){
							$("#fborra").html(data);
							$("#fborra").dialog({
								autoOpen: false, height: 350, width: 450, modal: true,
								buttons: {"Aceptar": function() {
										$(this).dialog("close");
										jQuery("'.$ngrid.'").trigger("reloadGrid");
									}
								}
							});
							$("#fborra").dialog( "open" );
						}
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function sinvshow(){
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

		// Fotos
		$bodyscript .= '
		function verfotos(){
			var id     = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				window.open(\''.site_url("inventario/fotos/dataedit").'/\'+id+\'/create\', \'_blank\', \'width=800, height=600, scrollbars=yes, status=yes, resizable=yes,screenx=((screen.availHeight/2)-300), screeny=((screen.availWidth/2)-400)\');
			} else {
				$.prompt("<h1>Por favor Seleccione un Producto</h1>");
			}
		};
		';

		// Pagina Web
		$bodyscript .= '
		function irurl(){
			var id     = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret  = $("'.$ngrid.'").getRowData(id);
				if ( ret.url.length > 10 )
					window.open(ret.url);
			} else {
				$.prompt("<h1>Por favor Seleccione un Producto</h1>");
			}
		};
		';

		// Codigo QR
		$bodyscript .= '
		function codqr(){
			var id     = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret  = $("'.$ngrid.'").getRowData(id);
				window.open(\''.site_url("inventario/sinv/sinvqr").'/\'+ret.id, \'_blank\', \'width=420,height=450,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-225), screeny=((screen.availWidth/2)-250)\');
			} else {
				$.prompt("<h1>Por favor Seleccione un Producto</h1>");
			}
		};
		';

		//VALORES PARA EL SUNDECOP
		$bodyscript .= '
		function sundecop() {
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = $("'.$ngrid.'").getRowData(id);
				mId = id;
				$.post("'.site_url('inventario/sinv/desundecop/modify').'/"+id, function(data){
					$("#fborra").html("");
					$("#fedita").html(data);
					$("#fedita").dialog({
						autoOpen: false, height: 450, width: 550, modal: true,
						title:"Parametros del SUNDECOP",
						buttons: {
							"Guardar": function() {
							var murl = $("#df1").attr("action");
							$.ajax({
								type: "POST", dataType: "html", async: false,
								url: murl,
								data: $("#df1").serialize(),
								success: function(r,s,x){
									if ( r.length == 0 ) {
										apprise("Registro Guardado");
										$( "#fedita" ).dialog( "close" );
										return true;
									} else {
										$("#fedita").html(r);
									}
								}
							})},
						"Cancelar": function() { $( this ).dialog( "close" ); }
						}
					});
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		};';

		$bodyscript .= '
		function actdesc(){
			var ids = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selarrrow\');
			$.ajax({
				type: "POST", dataType: "html", async: false,
				url: "'.site_url($this->url.'actdesc').'",
				data: {ids:ids},
				success: function(r,s,x){

					if(r.trim().length>0){
						alert(r);
					}else{
						jQuery("'.$ngrid.'").trigger("reloadGrid");
					}
				}
			});
		}';

		// Detalle del Registro
		$bodyscript .= '
		function detalle(mid){
			var ret = $("'.$ngrid.'").getRowData(mid);
			var mSalida = "<table width=\'100%\' style=\'background:#AAAAAA\'>"
			var mClaser = "<tr class=\'littletablerow\'>";
			var mClaseh = "<tr class=\'littletableheaderc\'>";
			var mBlanco = "<tr class=\'littletablerow\'><td>&nbsp;</td></tr>";
			var mTabla = "<table class=\'bordetabla\' cellpadding=\'1\' cellspacing=\'0\'";

			mSalida += "<tr><td width=\'255\' 	style=\'vertical-align: text-top;\'>";
			mSalida += mTabla+" width=\'250\'>";
			mSalida += "<tr class=\'tableheaderm\'><th colspan=\'3\'>Precios de Venta</th></tr>";
			mSalida += "<tr class=\'tableheader\'><th>%</th><th>Base</th><th>Precio</th></tr>";
			mSalida += mClaser+"<td align=\'right\'>"+ret.margen1+"</td><td align=\'right\'>"+nformat(ret.base1,2)+"</td><td align=\'right\'>"+nformat(ret.precio1,2)+"</td></tr>";
			mSalida += mClaser+"<td align=\'right\'>"+ret.margen2+"</td><td align=\'right\'>"+nformat(ret.base2,2)+"</td><td align=\'right\'>"+nformat(ret.precio2,2)+"</td></tr>";
			mSalida += mClaser+"<td align=\'right\'>"+ret.margen3+"</td><td align=\'right\'>"+nformat(ret.base3,2)+"</td><td align=\'right\'>"+nformat(ret.precio3,2)+"</td></tr>";
			mSalida += mClaser+"<td align=\'right\'>"+ret.margen4+"</td><td align=\'right\'>"+nformat(ret.base4,2)+"</td><td align=\'right\'>"+nformat(ret.precio4,2)+"</td></tr>";
			mSalida += "</table>";

			mSalida += "</td><td width=\'205\' style=\'vertical-align: text-top;\'>";

			mSalida += "<div id=\'itsinv\'>";
			mSalida += mTabla+" width=\'200\'>";
			mSalida += "<tr class=\'tableheader\'><th>Almacenes</th></tr>";
			mSalida += mClaser+"<td>Buscando existencias..</td></tr>";
			mSalida += mBlanco+mBlanco+mBlanco+mBlanco;
			mSalida += "</table>";
			mSalida += "</div>";

			mSalida += "</td><td width=\'205\' style=\'vertical-align: text-top;\'>";

			mSalida += mTabla+" width=\'200\'>";
			mSalida += "<tr class=\'tableheaderm\'><th colspan=\'2\'>Codigos Asociados</th></tr>";
			mSalida += mClaser+"<td title=\'Codigo de Barras\'>Barras         </td><td>"+ret.barras+ "</td></tr>";
			mSalida += mClaser+"<td>Alterno        </td><td>"+ret.alterno+"</td></tr>";
			mSalida += mClaser+"<td>Caja           </td><td>"+ret.enlace+ "</td></tr>";
			mSalida += mClaser+"<td>Nr. Sanitario </td><td>"+ret.mpps+   "</td></tr>";
			mSalida += mClaser+"<td>C.P.E.</td><td>"+ret.cpe+    "</td></tr>";
			mSalida += "</table>";

			mSalida += "</td><td width=\'105\' style=\'vertical-align: text-top;\'>";
			mSalida += mTabla+" width=\'100\'>";
			mSalida += "<tr class=\'tableheader\'><th colspan=\'2\'>Medidas</th></tr>";
			mSalida += mClaser+"<td>Peso  </td><td align=\'right\'>"+nformat(ret.peso,2)+ "</td></tr>";
			mSalida += mClaser+"<td>Alto  </td><td align=\'right\'>"+ret.alto+ "</td></tr>";
			mSalida += mClaser+"<td>Ancho </td><td align=\'right\'>"+ret.ancho+"</td></tr>";
			mSalida += mClaser+"<td>Largo </td><td align=\'right\'>"+ret.largo+"</td></tr>";
			mSalida += mClaser+"<td>Unidad</td><td>"+ret.unidad+"</td></tr>";
			mSalida += "</table>";

			mSalida += "</td><td style=\'vertical-align: text-top;\'>";

			mSalida += mTabla+" width=\'250\'>";
			mSalida += "<tr class=\'tableheaderm\'><th colspan=\'3\'>&Uacute;ltimas Compras</th></tr>";
			mSalida += "<tr class=\'tableheader\'><th>Prov.</th><th>Fecha</th><th>Costo</th></tr>";
			mSalida += mClaser+"<td>"+ret.prov1+"</td><td align=\'center\'>"+ret.pfecha1.substring(8,10)+"/"+ret.pfecha1.substring(5,7)+"/"+ret.pfecha1.substring(0,4)+"</td><td align=\'right\'>"+nformat(ret.prepro1,2)+"</td></tr>";
			mSalida += mClaser+"<td>"+ret.prov2+"</td><td align=\'center\'>"+ret.pfecha2.substring(8,10)+"/"+ret.pfecha2.substring(5,7)+"/"+ret.pfecha2.substring(0,4)+"</td><td align=\'right\'>"+nformat(ret.prepro2,2)+"</td></tr>";
			mSalida += mClaser+"<td>"+ret.prov3+"</td><td align=\'center\'>"+ret.pfecha3.substring(8,10)+"/"+ret.pfecha3.substring(5,7)+"/"+ret.pfecha3.substring(0,4)+"</td><td align=\'right\'>"+nformat(ret.prepro3,2)+"</td></tr>";
			mSalida += mBlanco;
			mSalida += "</table>";
			mSalida += "</td></tr>";
			mSalida += "</table>";
			return mSalida;
		}';

		$mSQL = 'SELECT nombre, nombre descrip FROM formatos WHERE nombre LIKE "ETIQUETA%" AND (proteo IS NOT NULL OR tcpdf IS NOT NULL) AND (proteo!="" OR tcpdf!="") ORDER BY nombre';
		$etiq = $this->datasis->llenaopciones($mSQL, false, 'mforma');
		$etiq = str_replace('"',"'",$etiq);


		// Etiquetas
		$bodyscript .= '
		function etiquetas(){
			var s = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selarrrow\');
			$.prompt(
			"<h1>Generar Etiquetas:</h1><br/><center>Cantidad: <input class=\'inputnum\' type=\'text\' id=\'mcantidad\' name=\'mcantidad\' value=\'1\' size=\'3\' />&nbsp;&nbsp; Forma: '.$etiq.' Tipo: <select id=\'mtipo\' name=\'mtipo\'><option value=\'A\'>A</option><option value=\'B\'>B</option></select></center><br/>",
			{
				buttons: { Seleccionados: 1, Filtrados: 2 , Cancelar: false },
				submit: function(e,v,m,f){
					if (v == 1) {
						if ( s.length == 0 ){
							$.prompt("<h1>Debe seleccionar al menos un Producto</h1>");
						} else {
							ventana = window.open("'.site_url("formatos/ver").'/"+f.mforma+"/"+f.mcantidad+"/"+s.toString().replace(/,/g,"_"),"Etiquetas","width=800,height=600");
						}
					} else if ( v==2 )  {
						ventana = window.open("'.site_url("formatos/ver").'/"+f.mforma+"/"+f.mcantidad+"/0","Etiquetas","width=800,height=600");
					}
				}
			});
		};';


		//Wraper de javascript
		$bodyscript .= '
		$(function() {
			$("#dialog:ui-dialog").dialog( "destroy" );
			var mId = 0;
			var montotal = 0;
			var grid = jQuery("#newapi'.$grid0.'");
			var s;
			var tips = $( ".validateTips" );
			s = grid.getGridParam(\'selarrrow\');
			';

		// Barras Adicionales
		$bodyscript .= '
		$("#bbarras").click(
			function(){
				var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
				if(id){
					var ret = $("'.$ngrid.'").getRowData(id);
					$.post("'.site_url('inventario/sinv/barrasform')."/".'"+id,
					function(data){
						$("#fshow").html(data);
						$("#fshow").dialog( { title:"BARRAS ADICIONALES", width: 235, height: 320, modal: true } );
						$("#fshow").dialog( "open" );
					});
				} else {
					$.prompt("<h1>Por favor Seleccione un Registro</h1>");
				}
		})';

		// Principios Activos
		$bodyscript .= '
		$("#bpactivo").click(function(){
			$.post("'.site_url('inventario/sinv/pactivosform').'",
			function(data){
				$("#fshow").html(data);
				$("#fshow").dialog( { title:"PRINCIPIOS ACTIVOS", width: 350, height: 400, modal: true } );
				$("#fshow").dialog( "open" );
			});
		});';

		// Marcas
		$bodyscript .= '
		$("#gmarcas").click(function(){
			$.post("'.site_url('inventario/sinv/marcaform').'",
			function(data){
				$("#fshow").html(data);
				$("#fshow").dialog( { title:"MARCAS", width: 320, height: 400, modal: true } );
				$("#fshow").dialog( "open" );
			});
		});';

		// Unidades
		$bodyscript .= '
		$("#gunidad").click(function(){
			$.post("'.site_url('inventario/sinv/uniform').'",
			function(data){
				$("#fshow").html(data);
				$("#fshow").dialog( { title:"UNIDAES DE PRESENTACION", width: 270, height: 400, modal: true } );
				$("#fshow").dialog( "open" );
			});
		});';

		// Inactivos
		$bodyscript .= '
		$("#hinactivo").click( function(){
			if (verinactivos==0){ verinactivos=1; } else { verinactivos=0;};
			$("'.$ngrid.'").jqGrid(\'setGridParam\', {postData: { verinactivos: verinactivos }})
			$("'.$ngrid.'").trigger("reloadGrid");
			//alert("inactivo="+verinactivos);
		});';

		// Kardex
		$bodyscript .= '
		$("#kardex").click( function(){
			var id = $("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				window.open(\''.site_url("inventario/kardex/kardexpres").'/\'+id, \'_blank\', \'width=420,height=450,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-225), screeny=((screen.availWidth/2)-250)\');
			} else {
				$.prompt("<h1>Por favor Seleccione un Producto</h1>");
			}
		});';

		// Sundec
		$bodyscript .= '$("#sundecop").click( function(){ sundecop();}); ';

		$bodyscript .= '
		$("#fshow").dialog({
			autoOpen: false, height: 450, width: 700, modal: true,
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

		$grid->addField('activo');
		$grid->label('*');
		$grid->params(array(
			'align'        => '"center"',
			'search'        => 'false',
			'editable'      => $editar,
			'width'         => 20,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
			'formatter'     => 'factivo'
		));

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 110,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 260,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 45 }',
		));

		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));

		$grid->addField('grupo');
		$grid->label('Grupo');
		$grid->params(array(
			'align'         => '"center"',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));

		$grid->addField('existen');
		$grid->label('Existencia');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 60,
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
			'width'         => 80,
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
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$mSQL   = "SELECT marca, marca FROM marc ORDER BY marca ";
		$amarca = $this->datasis->llenajqselect($mSQL, true );


		$grid->addField('marca');
		$grid->label('Marca');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 120,
			'edittype'      => "'select'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ value: '.$amarca.',  style:"width:200px"}',
			'stype'         => "'text'",
		));
/*
		$grid->addField('descrip2');
		$grid->label('Descripci&oacute;n 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:45, maxlength: 45 }',
		));
*/

		$grid->addField('unidad');
		$grid->label('Unidad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('ubica');
		$grid->label('Ubica');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:9, maxlength: 9 }',
		));


		$grid->addField('clave');
		$grid->label('Clave');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('comision');
		$grid->label('Comision');
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


		$grid->addField('enlace');
		$grid->label('Enlace');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('prov1');
		$grid->label('Prov.1');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'searchoptions' => '{ searchhidden: true}',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('prepro1');
		$grid->label('Precio.Prov.1');
		$grid->params(array(
			'hidden'        => 'true',
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


		$grid->addField('pfecha1');
		$grid->label('P.Fecha1');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('prov2');
		$grid->label('Prov.2');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('prepro2');
		$grid->label('Precio.Prov.2');
		$grid->params(array(
			'hidden'        => 'true',
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


		$grid->addField('pfecha2');
		$grid->label('P.Fecha2');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('prov3');
		$grid->label('Prov.3');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('prepro3');
		$grid->label('Precio.Prov.3');
		$grid->params(array(
			'hidden'        => 'true',
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


		$grid->addField('pfecha3');
		$grid->label('Prov.Fecha.3');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('pond');
		$grid->label('Costo Pond.');
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


		$grid->addField('ultimo');
		$grid->label('Costo Ultimo');
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

		$grid->addField('pvp_s');
		$grid->label('Pvp_s');
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


		$grid->addField('pvp_bs');
		$grid->label('Pvp_bs');
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


		$grid->addField('pvpprc');
		$grid->label('Pvpprc');
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


		$grid->addField('contbs');
		$grid->label('Contbs');
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


		$grid->addField('contprc');
		$grid->label('Contprc');
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


		$grid->addField('mayobs');
		$grid->label('Mayobs');
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


		$grid->addField('mayoprc');
		$grid->label('Mayoprc');
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

		$grid->addField('exmin');
		$grid->label('E.M&iacute;nima');
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


		$grid->addField('exmax');
		$grid->label('E.M&aacute;xima');
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


		$grid->addField('exord');
		$grid->label('Ordenado');
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


		$grid->addField('exdes');
		$grid->label('Despacho');
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


		$grid->addField('fechav');
		$grid->label('F. Venta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
		));


		$grid->addField('pfecha1');
		$grid->label('F. Compra');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
		));


		$grid->addField('iva');
		$grid->label('I.V.A.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 50,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('fracci');
		$grid->label('Fracci&oacute;n');
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

		$grid->addField('barras');
		$grid->label('Codigo de Barras');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
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


		$grid->addField('tdecimal');
		$grid->label('Decimal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('dolar');
		$grid->label('Dolar');
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


		$grid->addField('redecen');
		$grid->label('Redondeo');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('formcal');
		$grid->label('Forma calculo');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('fordeci');
		$grid->label('Fordeci');
		$grid->params(array(
			'hidden'        => 'true',
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

/*
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


		$grid->addField('fechac2');
		$grid->label('Fechac2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));
*/

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


		$grid->addField('pondcal');
		$grid->label('Pond.Calculado');
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


		$grid->addField('alterno');
		$grid->label('Alterno');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

/*
		$grid->addField('aumento');
		$grid->label('Aumento');
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

		$grid->addField('modelo');
		$grid->label('Modelo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));



		$grid->addField('clase');
		$grid->label('Clase');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
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
		$grid->label('F.Desde');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('fhasta');
		$grid->label('F.Hasta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('derivado');
		$grid->label('Derivado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('cantderi');
		$grid->label('Cantderi');
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
		$grid->addField('ppos1');
		$grid->label('Ppos1');
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


		$grid->addField('ppos2');
		$grid->label('Ppos2');
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


		$grid->addField('ppos3');
		$grid->label('Ppos3');
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


		$grid->addField('ppos4');
		$grid->label('Ppos4');
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

		$grid->addField('linea');
		$grid->label('Linea');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


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


		$grid->addField('bonifica');
		$grid->label('Bonifica');
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


		$grid->addField('bonicant');
		$grid->label('Bonicant');
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


		$grid->addField('standard');
		$grid->label('Standard');
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


		$grid->addField('alto');
		$grid->label('Alto');
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


		$grid->addField('ancho');
		$grid->label('Ancho');
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


		$grid->addField('largo');
		$grid->label('Largo');
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

		$grid->addField('exento');
		$grid->label('Exento');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('mmargen');
		$grid->label('Mmargen');
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


		$grid->addField('pm');
		$grid->label('Pm');
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


		$grid->addField('pmb');
		$grid->label('Pmb');
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


		$grid->addField('mmargenplus');
		$grid->label('Mmargenplus');
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


		$grid->addField('escala1');
		$grid->label('Escala1');
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


		$grid->addField('pescala1');
		$grid->label('Pescala1');
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


		$grid->addField('escala2');
		$grid->label('Escala2');
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


		$grid->addField('pescala2');
		$grid->label('Pescala2');
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


		$grid->addField('escala3');
		$grid->label('Escala3');
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


		$grid->addField('pescala3');
		$grid->label('Pescala3');
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


		if ( $this->datasis->traevalor('SUNDECOP') == 'S') {

			$grid->addField('mpps');
			$grid->label('MPPS');
			$grid->params(array(
				'search'        => 'true',
				'editable'      => $editar,
				'width'         => 100,
				'edittype'      => "'text'",
				'editrules'     => '{ required:true}',
				'editoptions'   => '{ size:20, maxlength: 20 }',
			));

			$grid->addField('cpe');
			$grid->label('CPE');
			$grid->params(array(
				'search'        => 'true',
				'editable'      => $editar,
				'width'         => 100,
				'edittype'      => "'text'",
				'editrules'     => '{ required:true}',
				'editoptions'   => '{ size:20, maxlength: 20 }',
			));

			$grid->addField('dcomercial');
			$grid->label('Dest.Com.');
			$grid->params(array(
				'search'        => 'true',
				'editable'      => $editar,
				'align'         => "'right'",
				'edittype'      => "'text'",
				'width'         => 50,
				'editrules'     => '{ required:true }',
				'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
				'formatter'     => "'number'",
				'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
			));

			$grid->addField('rubro');
			$grid->label('Rubro');
			$grid->params(array(
				'search'        => 'true',
				'editable'      => $editar,
				'align'         => "'right'",
				'edittype'      => "'text'",
				'width'         => 50,
				'editrules'     => '{ required:true }',
				'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
				'formatter'     => "'number'",
				'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
			));

			$grid->addField('subrubro');
			$grid->label('SubRubro');
			$grid->params(array(
				'search'        => 'true',
				'editable'      => $editar,
				'align'         => "'right'",
				'edittype'      => "'text'",
				'width'         => 50,
				'editrules'     => '{ required:true }',
				'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
				'formatter'     => "'number'",
				'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
			));

			$grid->addField('cunidad');
			$grid->label('Unidad');
			$grid->params(array(
				'search'        => 'true',
				'editable'      => $editar,
				'align'         => "'right'",
				'edittype'      => "'text'",
				'width'         => 50,
				'editrules'     => '{ required:true }',
				'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
				'formatter'     => "'number'",
				'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
			));

			$grid->addField('cmarca');
			$grid->label('Marca');
			$grid->params(array(
				'search'        => 'true',
				'editable'      => $editar,
				'align'         => "'right'",
				'edittype'      => "'text'",
				'width'         => 50,
				'editrules'     => '{ required:true }',
				'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
				'formatter'     => "'number'",
				'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
			));

			$grid->addField('Material');
			$grid->label('Cmaterial');
			$grid->params(array(
				'search'        => 'true',
				'editable'      => $editar,
				'align'         => "'right'",
				'edittype'      => "'text'",
				'width'         => 50,
				'editrules'     => '{ required:true }',
				'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
				'formatter'     => "'number'",
				'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
			));

			$grid->addField('cforma');
			$grid->label('Forma');
			$grid->params(array(
				'search'        => 'true',
				'editable'      => $editar,
				'align'         => "'right'",
				'edittype'      => "'text'",
				'width'         => 50,
				'editrules'     => '{ required:true }',
				'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
				'formatter'     => "'number'",
				'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
			));

			$grid->addField('cpactivo');
			$grid->label('Cpactivo');
			$grid->params(array(
				'search'        => 'true',
				'editable'      => $editar,
				'align'         => "'right'",
				'edittype'      => "'text'",
				'width'         => 50,
				'editrules'     => '{ required:true }',
				'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
				'formatter'     => "'number'",
				'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
			));
		}

		$grid->addField('url');
		$grid->label('Sitio Web');
		$grid->params(array(
			'align'         => "'left'",
			'frozen'        => 'true',
			'width'         => 100,
			'editable'      => 'false',
			'search'        => 'false'
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
		$grid->setMultiSelect(true);
		//$grid->setOndblClickRow('');

		$grid->setOnSelectRow('
			function(id){
				if (id){
					var ret = $(gridId1).getRowData(id);
					var url= "'.site_url('inventario/fotos/thumbnail').'/"+id;
					var sitio = "";
					var codqr = "<div class=\'botones\'><button class=\'anexos\' onclick=\'codqr();\'>Etiqueta</button></div>";
					if ( ret.url.length > 12 )
						sitio = "<button onclick=\'irurl();\'>Pagina Web</button>";
					$("#ladicional").html("<center><img src=\'"+url+"\' width=\'160\' ondblclick=\'verfotos()\' ><br>"+codqr+sitio+"<center><div id=\'textofoto\' style=\'text-align:center;\'></div>");
					$("#radicional").html(detalle(id));
					$.get(\''.site_url('inventario/sinv/sinvitems').'/\'+id,
						function(data){
							$("#itsinv").html(data);
					});
				}
			}');


		$grid->setAfterInsertRow('
			function( rid, aData, rowe){
				if ( aData.activo == "N" ){
					$(this).jqGrid( "setCell", rid, "activo","", {color:"#FFFFFF", background:"#960F18" });
					$(this).jqGrid( "setCell", rid, "codigo","", {color:"#FFFFFF", background:"#960F18" });
				}
				if ( aData.tipo == "Servicio" ){
					$(this).jqGrid( "setCell", rid, "tipo","", {color:"#FFFFFF", background:"#0488db" });
				}
				if ( aData.existen < 0 ){
					$(this).jqGrid( "setCell", rid, "existen","", {color:"RED", background:"#FFFFFF", "font-weight":"bold" });
				}
			}
		');


		//$grid->setFormOptionsE('-');  //'closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		//$grid->setFormOptionsA('-');  //'closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		//$grid->setAfterSubmit('-');   //"$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('SINV','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('SINV','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('SINV','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('SINV','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: sinvadd, editfunc: sinvedit, delfunc: sinvdel, viewfunc: sinvshow');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdata/'));

		if($deployed){
			return $grid->deploy();
		}else{
			return $grid;
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid   = $this->jqdatagrid;
		$iactivo= $this->input->post('verinactivos');

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('sinv');

		if($iactivo == 0){
			$mWHERE[] = array('=', 'activo', 'S', '' );
		}

		$response   = $grid->getData('sinv', array(array()), array(), false, $mWHERE, 'codigo');
		$rs = $grid->jsonresult($response);

		//Guarda en la BD el Where para usarlo luego
		$querydata = array('data1' => $this->session->userdata('dtgQuery'));
		$emp = strpos($querydata['data1'],'WHERE ');

		if($emp > 0){
			$querydata['data1'] = substr( $querydata['data1'], $emp );
			$emp = strpos($querydata['data1'],'ORDER BY ');
			if($emp > 0){
				$querydata['data1'] = substr( $querydata['data1'], 0, $emp );
			}
		}else{
			$querydata['data1'] = '';
		}

		$ids = $this->datasis->guardasesion($querydata);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion
	//
	function setdata(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$mcodp  = 'codigo';
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);

		if($oper == 'add'){
			echo 'Deshabilitado';
		}elseif($oper == 'edit'){

			if(!$this->datasis->sidapuede('SINV','MODIFICA%')){
				echo 'No tiene acceso a modificar';
				return false;
			}

			$posibles=array('descrip','modelo','marca');
			foreach($data as $ind=>$val){
				if(!in_array($ind,$posibles)){
					echo 'Campo no permitido ('.$ind.')';
					return false;
				}
			}

			$row = $this->datasis->damerow("SELECT codigo FROM sinv WHERE id=${id}");
			if(empty($row)){
				echo 'Registro no encontrado';
				return false;
			}

			$this->db->where('id'   , $id);
			$this->db->update('sinv', $data);

			logusu('SINV','Registro '.$row['codigo'].' MODIFICADO');
			echo 'Registro Modificado';

		}elseif($oper == 'del'){
			echo 'Deshabilitado';
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function bpos1(){
		$oper   = $this->input->post('oper');
		if ($oper == 'del'){
			// Borra
			$id     = $this->input->post('id');
			$this->db->simple_query("DELETE FROM barraspos WHERE id=$id ");
			logusu('BARRASPOS',"Registro  ELIMINADO");
			echo "Registro Eliminado";

		} elseif ( $oper == false ) {
			$id = $this->uri->segment(4);
			if ( $id > 0 ) {
				$codigo = $this->datasis->dameval("SELECT codigo FROM sinv WHERE id=$id");
				$this->db->select(array('id', 'codigo', 'suplemen'));
				$this->db->from('barraspos');
				$this->db->where('codigo',$codigo);

				$rs = $this->datasis->codificautf8($this->db->get()->result_array());
				$response['data'] = $rs;
				$rs = json_encode( $response);
				echo $rs;
			}
		}

	}

	/**
	* Busca la data en el Servidor por json
	*/
	function pactivo(){
		$oper   = $this->input->post('oper');
		if ($oper == 'del'){
			// Borra
			$id     = $this->input->post('id');
			$this->db->query("DELETE FROM sinvpa WHERE id=$id ");
			logusu('SINV',"Principio Activo desligado del producto $id ELIMINADO");
			echo "Registro Eliminado";

		} elseif ( $oper == false ) {
			$id = $this->uri->segment(4);
			if ( $id > 0 ) {
				$codigo = $this->datasis->dameval("SELECT codigo FROM sinv WHERE id=$id");

				$this->db->select(array('a.id', 'b.nombre'));
				$this->db->from('sinvpa as a');
				$this->db->join('pactivo as b','a.pactivo=b.id');
				$this->db->where('a.codigo',$codigo);

				$rs = $this->datasis->codificautf8($this->db->get()->result_array());
				$response['data'] = $rs;
				$rs = json_encode( $response);
				echo $rs;
			}
		}
	}


	//******************************************************************
	//
	//   DATAEDIT
	//
	//******************************************************************
	function dataedit($status='',$id='' ) {
		$this->rapyd->load('dataedit','datadetails');

		$modbus = array(
			'tabla' => 'sinv',
			'columnas'=>array(
				'codigo'  =>'C&oacute;digo',
				'descrip' =>'Descripci&oacute;n',
				'precio1' =>'Precio 1',
				'precio2' =>'Precio 2',
				'precio3' =>'Precio 3',
				'existen' =>'Existencia',
			),
			'filtro' => array('codigo' => 'C&oacute;digo'
			,'descrip' => 'Descripci&oacute;n')
			,'retornar'  => array(
				 array('codigo'  => 'itcodigo_<#i#>')
				,array('descrip' => 'itdescrip_<#i#>')
				,array('descrip' => 'itdescrip_<#i#>_val')
				,array('formcal' => 'itformcal_<#i#>')
				,array('ultimo'  => 'itultimo_<#i#>_val')
				,array('ultimo'  => 'itultimo_<#i#>')
				,array('pond'    => 'itpond_<#i#>')
				,array('pond'    => 'itpond_<#i#>_val')
				,array('base1'   => 'itprecio1_<#i#>')
			),
			'p_uri' => array(4 => '<#i#>'),
			'titulo' => 'Buscar Articulo',
			'where' => '`activo` = "S"',
			'script' => array('totalizarcombo()')
		);
		$bSINV_C = $this->datasis->p_modbus($modbus, '<#i#>');

		$modbus = array(
			'tabla' => 'sinv',
			'columnas'=>array(
				'codigo'  =>'C&oacute;digo',
				'descrip' =>'Descripci&oacute;n',
				'precio1' =>'Precio 1',
				'precio2' =>'Precio 2',
				'precio3' =>'Precio 3',
				'existen' =>'Existencia',
				),
			'filtro' => array('codigo' => 'C&oacute;digo'
			,'descrip' => 'Descripci&oacute;n')
			,'retornar'  => array(
				 array('codigo'  => 'it2codigo_<#i#>')
				,array('descrip' => 'it2descrip_<#i#>')
				,array('descrip' => 'it2descrip_<#i#>_val')
				,array('formcal' => 'it2formcal_<#i#>')
				,array('ultimo'  => 'it2ultimo_<#i#>')
				,array('pond'    => 'it2pond_<#i#>')
				,array('id'      => 'it2id_sinv_<#i#>')

			),
			'p_uri' => array(4 => '<#i#>'),
			'titulo' => 'Buscar Articulo',
			'where' => '`activo` = "S"',
			'script' => array('totalizarpitem()')
		);
		$bSINV_I = $this->datasis->p_modbus($modbus, '<#i#>',800,600,'sinv_i');

		$do = new DataObject('sinv');
		$do->pointer('grup' , 'grup.grupo=sinv.grupo' , 'grup.grupo AS grupgrupo' , 'left');
		$do->pointer('line' , 'line.linea=grup.linea' , 'line.linea AS linelinea' , 'left');
		$do->pointer('dpto' , 'dpto.depto=line.depto' , 'dpto.depto AS dptodepto' , 'left');
		$do->pointer('sinv AS csinv', 'csinv.codigo=sinv.enlace','csinv.formcal AS cformcal,csinv.pond AS cpond,csinv.ultimo AS cultimo,csinv.descrip AS cdescrip,csinv.base1 AS cbase1,csinv.base2 AS cbase2,csinv.base3 AS cbase3,csinv.base4 AS cbase4','left');
		$do->rel_one_to_many('sinvcombo' , 'sinvcombo' , array('codigo' => 'combo'));
		$do->rel_one_to_many('sinvpitem' , 'sinvpitem' , array('codigo' => 'producto'));
		$do->rel_one_to_many('sinvplabor', 'sinvplabor', array('codigo' => 'producto'));
		$do->rel_pointer('sinvcombo'     , 'sinv AS p' , 'p.codigo=sinvcombo.codigo', 'p.descrip AS sinvdescrip,p.pond AS sinvpond,p.ultimo sinvultimo,p.formcal sinvformcal,p.precio1 sinvprecio1');

		if($status=='create' && !empty($id)){
			$do->load($id);
			$do->set('codigo' , '');
			$do->set('alterno', '');
		}

		$edit = new DataDetails('', $do);
		$edit->on_save_redirect=false;

		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_del'    );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$ultimo ='<a href="javascript:ultimo();" title="Consultar ultimo c&oacute;digo ingresado">&Uacute;ltimo</a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un c&oacute;digo aleatorio">Sugerir</a>';

		$edit->codigo = new inputField('C&oacute;digo', 'codigo');
		$edit->codigo->size=15;
		$edit->codigo->maxlength=15;
		$edit->codigo->rule = 'alpha_dash_slash|trim|strtoupper|callback_chexiste';
		$edit->codigo->mode = 'autohide';
		$edit->codigo->append($sugerir);
		//$edit->codigo->append($ultimo);

		$edit->alterno = new inputField('Alterno', 'alterno');
		$edit->alterno->size=15;
		$edit->alterno->maxlength=15;
		$edit->alterno->rule = 'trim|strtoupper|callback_chalterno';

		$edit->enlace  = new inputField('Caja', 'enlace');
		$edit->enlace ->size=15;
		$edit->enlace->maxlength=15;
		$edit->enlace->rule = 'trim|condi_required|callback_chenlace';

		$edit->cdescrip = new inputField('', 'cdescrip');
		$edit->cdescrip->pointer=true;
		$edit->cdescrip->db_name='cdescrip';
		$edit->cdescrip->type='inputhidden';

		$edit->aumento = new inputField('Aumento %', 'aumento');
		$edit->aumento->css_class='inputnum';
		$edit->aumento->size=5;
		$edit->aumento->maxlength=6;
		$edit->aumento->rule='condi_required|callback_chobligafraccion';
		$edit->aumento->autocomplete = false;
		//$edit->aumento->append('Solo si es fracci&oacute;n');

		$edit->maxven = new inputField('Venta m&aacute;xima', 'maxven');
		$edit->maxven->css_class   = 'inputnum';
		$edit->maxven->insertValue = '0';
		$edit->maxven->size        = 6;
		$edit->maxven->rule='numeric';
		$edit->maxven->autocomplete = false;

		$edit->minven = new inputField('Venta m&iacute;nima', 'minven');
		$edit->minven->css_class='inputnum';
		$edit->minven->insertValue='0';
		$edit->minven->size=6;
		$edit->minven->rule='numeric|callback_chminven';
		$edit->minven->autocomplete = false;

		$edit->barras = new inputField('C&oacute;digo Barras', 'barras');
		$edit->barras->size=15;
		$edit->barras->maxlength=15;
		$edit->barras->rule = 'trim|unique';

		$edit->tipo = new dropdownField('Tipo', 'tipo');
		$edit->tipo->style='width:100px;';
		$edit->tipo->option('Articulo' ,'Art&iacute;culo');
		$edit->tipo->option('Servicio' ,'Servicio');
		$edit->tipo->option('Descartar','Descartar');
		$edit->tipo->option('Fraccion' ,'Fracci&oacute;n');
		$edit->tipo->option('Lote'     ,'Lote');
		$edit->tipo->option('Combo'    ,'Combo');
		$edit->tipo->rule='callback_chtipo';

		$AddUnidad='<a href="javascript:add_unidad();" title="Haz clic para Agregar una unidad nueva">'.image('list_plus.png','Agregar',array("border"=>"0")).'</a>';
		$edit->unidad = new dropdownField('Unidad','unidad');
		$edit->unidad->style='width:100px;';
		$edit->unidad->option('','Seleccionar');
		$edit->unidad->options('SELECT unidades, unidades AS valor FROM unidad ORDER BY unidades');
		$edit->unidad->append($AddUnidad);

		$edit->clave = new inputField('Clave', 'clave');
		$edit->clave->size=10;
		$edit->clave->maxlength=8;
		$edit->clave->rule = 'trim|strtoupper';

		$edit->ubica = new inputField('Ubicaci&oacute;n', 'ubica');
		$edit->ubica->size=9;
		$edit->ubica->maxlength=9;
		$edit->ubica->rule = 'trim|strtoupper';

		$AddDepto='<a href="javascript:add_depto();" title="Haz clic para Agregar un nuevo Departamento">'.image('list_plus.png','Agregar',array("border"=>"0")).'</a>';
		$edit->depto = new dropdownField('Departamento', 'depto');
		$edit->depto->rule ='required';
		$edit->depto->style='width:250px;white-space:nowrap;';
		$edit->depto->option('','Seleccione un Departamento');
		$edit->depto->options('SELECT depto, CONCAT(depto,\'-\',descrip) descrip FROM dpto WHERE tipo=\'I\' ORDER BY depto');
		$edit->depto->db_name='dptodepto';
		$edit->depto->pointer=true;

		$AddLinea='<a href="javascript:add_linea();" title="Haz clic para Agregar una nueva Linea;">'.image('list_plus.png','Agregar',array("border"=>"0")).'</a>';
		$edit->linea = new dropdownField('L&iacute;nea','linea');
		$edit->linea->rule    = 'required';
		$edit->linea->style   = 'width:250px;';
		$edit->linea->db_name = 'linelinea';
		$edit->linea->pointer = true;
		$depto=$edit->getval('depto');
		if($depto!==false){
			$dbdepto=$this->db->escape($depto);
			$edit->linea->options("SELECT linea, CONCAT(LINEA,'-',descrip) descrip FROM line WHERE depto=$dbdepto ORDER BY descrip");
		}else{
			$edit->linea->option('','Seleccione un Departamento primero');
		}

		$AddGrupo='<a href="javascript:add_grupo();" title="Haz clic para Agregar un nuevo Grupo;">'.image('list_plus.png','Agregar',array("border"=>"0")).'</a>';
		$edit->grupo = new dropdownField('Grupo', 'grupo');
		$edit->grupo->rule ='required';
		$edit->grupo->style='width:250px;';

		$linea=$edit->getval('linea');
		if($linea!==false){
			$dblinea=$this->db->escape($linea);
			$edit->grupo->options("SELECT grupo, CONCAT(grupo,'-',nom_grup) nom_grup FROM grup WHERE linea=$dblinea ORDER BY nom_grup");
		}else{
			$edit->grupo->option('','Seleccione un Departamento primero');
		}

		$edit->comision  = new inputField('Comisi&oacute;n %', 'comision');
		$edit->comision ->size=7;
		$edit->comision->maxlength=5;
		$edit->comision->css_class='inputnum';
		$edit->comision->rule='numeric|callback_positivo|trim';

		$edit->fracci  = new inputField('Cant. X Empaque', 'fracci');
		$edit->fracci ->size=10;
		$edit->fracci->maxlength=4;
		$edit->fracci->css_class='inputnum';
		$edit->fracci->rule='condi_required|trim|callback_chobligafraccion';
		$edit->fracci->insertValue='1';

		$edit->activo = new dropdownField('Activo', 'activo');
		$edit->activo->style='width:50px;';
		$edit->activo->option('S','Si');
		$edit->activo->option('N','No');

		$edit->serial2 = new freeField('','free','Serial');
		$edit->serial2->in='activo';

		$edit->serial = new dropdownField ('Usa Seriales', 'serial');
		$edit->serial->style='width:80px;';
		$edit->serial->option('N','No');
		$edit->serial->option('S','Si');
		$edit->serial->option('V','Vehicular');
		$edit->serial->in='activo';

		$edit->premin = new dropdownField('Precio M&iacute;nimo', 'premin');
		$edit->premin->style='width:100px;';
		$edit->premin->option('0','Todos');
		$edit->premin->option('2','Precio 2');
		$edit->premin->option('3','Precio 3');
		$edit->premin->option('4','Precio 4');

		$edit->vnega = new dropdownField('Venta Negativa', 'vnega');
		$edit->vnega->style='width:60px;';
		$edit->vnega->option('S','Si');
		$edit->vnega->option('N','No');

		$edit->tdecimal2 = new freeField('','free','Usa Decimales');
		$edit->tdecimal2->in='activo';

		$edit->tdecimal = new dropdownField('Usa Decimales', 'tdecimal');
		$edit->tdecimal->style='width:80px;';
		$edit->tdecimal->option('N','No');
		$edit->tdecimal->option('S','Si');
		$edit->tdecimal->in='activo';

		$edit->descrip = new inputField('Descripci&oacute;n', 'descrip');
		$edit->descrip->size=45;
		$edit->descrip->maxlength=45;
		$edit->descrip->rule = 'trim|required|strtoupper';

		$edit->descrip2 = new inputField('Descripci&oacute;n adicional', 'descrip2');
		$edit->descrip2->size=45;
		$edit->descrip2->maxlength=45;
		$edit->descrip2->rule = 'trim|strtoupper';

		$edit->url = new inputField('Sitio Web', 'url');
		$edit->url->size=80;
		$edit->url->maxlength=200;

		$edit->ficha = new textareaField('Ficha Tecnica', 'ficha');
		$edit->ficha->rule = 'trim';
		$edit->ficha->cols = 85;
		$edit->ficha->rows = 9;

		$edit->peso  = new inputField('Peso', 'peso');
		$edit->peso->size=10;
		$edit->peso->maxlength=12;
		$edit->peso->css_class='inputnum';
		$edit->peso->rule='numeric|callback_positivo';
		$edit->peso->insertValue = 0;

		$edit->alto = new inputField('Alto', 'alto');
		$edit->alto->size=10;
		$edit->alto->maxlength=12;
		$edit->alto->css_class='inputnum';
		$edit->alto->rule='numeric|callback_positivo';
		$edit->alto->insertValue = 0;

		$edit->ancho = new inputField('Ancho', 'ancho');
		$edit->ancho->size=10;
		$edit->ancho->maxlength=12;
		$edit->ancho->css_class='inputnum';
		$edit->ancho->rule='numeric|callback_positivo';
		$edit->ancho->insertValue = 0;

		$edit->largo = new inputField('Largo', 'largo');
		$edit->largo->size=10;
		$edit->largo->maxlength=12;
		$edit->largo->css_class='inputnum';
		$edit->largo->rule='numeric|callback_positivo';
		$edit->largo->insertValue = 0;

		$edit->garantia = new inputField('Garantia', 'garantia');
		$edit->garantia->size=9;
		$edit->garantia->maxlength=3;
		$edit->garantia->css_class='inputonlynum';
		$edit->garantia->rule='numeric|callback_positivo';
		$edit->garantia->insertValue = 0;

		$edit->marca = new dropdownField('Marca', 'marca');
		$edit->marca->rule = 'required';
		$edit->marca->style='width:180px;';
		$edit->marca->option('','Seleccionar');
		$edit->marca->options('SELECT marca AS codigo, marca FROM marc ORDER BY marca');

		$edit->modelo  = new inputField('Modelo', 'modelo');
		$edit->modelo->size=20;
		$edit->modelo->maxlength=20;
		$edit->modelo->rule = 'trim|strtoupper';

		$edit->clase= new dropdownField('Clase', 'clase');
		$edit->clase->style='width:100px;';
		$edit->clase->option('A','Alta Rotacion');
		$edit->clase->option('B','Media Rotacion');
		$edit->clase->option('C','Baja Rotacion');
		$edit->clase->option('I','Importacion Propia');

		$ivas=$this->datasis->ivaplica();
		$edit->iva = new dropdownField('IVA %', 'iva');
		foreach($ivas as $tasa=>$ivamonto){
			$edit->iva->option($ivamonto,nformat($ivamonto));
		}
		$edit->iva->style='width:100px;';
		$edit->iva->insertValue=$ivas['tasa'];
		$edit->iva->onchange='calculos(\'S\');';

		$edit->exento = new dropdownField('Vender Exento', 'exento');
		$edit->exento->style='width:50px;';
		$edit->exento->option('N','No' );
		$edit->exento->option('E','Si' );

		$edit->dolar = new inputField('Precio en $', 'dolar');
		$edit->dolar->css_class    = 'inputnum';
		$edit->dolar->size         = 10;
		$edit->dolar->maxlength    = 13;
		$edit->dolar->autocomplete = false;
		$edit->dolar->insertValue = 0;


		$edit->margenu = new inputField('Margen Unico', 'margenu');
		$edit->margenu->css_class    = 'inputnum';
		$edit->margenu->size         = 10;
		$edit->margenu->maxlength    = 13;
		$edit->margenu->autocomplete = false;
		$edit->margenu->insertValue = 0;


		$edit->ultimo = new inputField('&Uacute;ltimo', 'ultimo');
		$edit->ultimo->css_class    = 'inputnum';
		$edit->ultimo->size         = 10;
		$edit->ultimo->maxlength    = 13;
		$edit->ultimo->onkeyup      = 'calculos(\'S\');';
		$edit->ultimo->rule         = 'required|mayorcero';
		$edit->ultimo->autocomplete = false;

		$edit->pond = new inputField('Promedio', 'pond');
		$edit->pond->css_class='inputnum';
		$edit->pond->size=10;
		$edit->pond->maxlength=13;
		$edit->pond->onkeyup = 'calculos(\'S\');';
		$edit->pond->rule='required|mayorcero';
		$edit->pond->autocomplete = false;

		//Para el caso de las fraccciones
		$edit->cultimo = new hiddenField('', 'cultimo');
		$edit->cultimo->pointer=true;
		$edit->cultimo->db_name='cultimo';
		$edit->cpond = new hiddenField('', 'cpond');
		$edit->cpond->pointer=true;
		$edit->cpond->db_name='cpond';

		$edit->standard = new inputField('Estandar', 'standard');
		$edit->standard->css_class='inputnum';
		$edit->standard->size=10;
		$edit->standard->maxlength=13;
		$edit->standard->insertValue=0;
		$edit->standard->autocomplete = false;

		$edit->formcal = new dropdownField('Base C&aacute;lculo', 'formcal');
		$edit->formcal->style='width:110px;';
		$edit->formcal->rule='required|enum[U,P,M,S]';
		$edit->formcal->option('U','Ultimo');
		$edit->formcal->option('P','Promedio');
		$edit->formcal->option('M','Mayor');
		$edit->formcal->option('S','Standard');
		$edit->formcal->insertValue='U';
		$edit->formcal->onchange = 'requeridos();calculos(\'S\');';

		$edit->cformcal = new hiddenField('', 'cformcal');
		$edit->cformcal->pointer=true;
		$edit->cformcal->db_name='cformcal';

		$edit->redecen = new dropdownField('Redondear', 'redecen');
		$edit->redecen->style='width:110px;';
		$edit->redecen->option('N','No Cambiar');
		$edit->redecen->option('M','Solo un Decimal');
		$edit->redecen->option('F','Sin Decimales');
		$edit->redecen->option('D','Decenas');
		$edit->redecen->option('C','Centenas');
		$edit->redecen->rule='enum[N,M,F,D,C]';
		$edit->redecen->onchange='calculos(\'S\');';

		$edit->linfe = new dropdownField('Limitar ventas seguidas', 'linfe');
		$edit->linfe->style='width:45px;';
		$edit->linfe->option('N' ,'No');
		$edit->linfe->option('S' ,'Si');
		$edit->linfe->insertValue='N';
		$edit->linfe->rule='enum[N,S]|callback_chlinfe';
		$edit->linfe->title='Activar si desea evitar que este producto no sea vendido a la misma persona en un per&iacute;odo de d&iacute;as';

		$edit->lindia = new inputField('D&iacute;as de limitaci&oacute;n', 'lindia');
		$edit->lindia->css_class='inputnum';
		$edit->lindia->size=3;
		$edit->lindia->maxlength=5;
		$edit->lindia->rule='numeric';
		$edit->lindia->insertValue='0';
		$edit->lindia->autocomplete = false;

		for($i=1;$i<=4;$i++){
			$objeto="margen$i";
			$edit->$objeto = new inputField("Margen $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->maxlength=6;
			$edit->$objeto->onkeyup = 'calculos(\'I\');';
			$edit->$objeto->autocomplete=false;
			$edit->$objeto->rule='required';

			$objeto="base$i";
			$edit->$objeto = new inputField("Base $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->maxlength=13;
			$edit->$objeto->autocomplete=false;
			$edit->$objeto->in="margen$i";
			$edit->$objeto->onkeyup = 'cambiobase(\'I\');';
			$edit->$objeto->rule='required|mayorcero';

			$objeto="precio$i";
			$edit->$objeto = new inputField("Precio $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->autocomplete=false;
			$edit->$objeto->maxlength=13;
			$edit->$objeto->in="margen$i";
			$edit->$objeto->onkeyup = 'cambioprecio(\'I\');';
			$edit->$objeto->rule='required|mayorcero';

			//para el caso de las fraccciones
			$objeto="cbase$i";
			$edit->$objeto = new hiddenField('', $objeto);
			$edit->$objeto->pointer=true;
			$edit->$objeto->db_name=$objeto;
			//$edit->$objeto->type='inputhidden';
		}

		$edit->existen = new inputField('Cantidad Actual','existen');
		$edit->existen->size=10;
		$edit->existen->readonly = true;
		$edit->existen->css_class='inputonlynum';
		$edit->existen->style='background:#F5F6CE;';

		$edit->exmin = new inputField('M&iacute;nimo', 'exmin');
		$edit->exmin->size=10;
		$edit->exmin->maxlength=12;
		$edit->exmin->css_class='inputonlynum';
		$edit->exmin->rule='numeric|callback_positivo|trim';
		$edit->exmin->insertValue = '0';

		$edit->exmax = new inputField('M&aacute;ximo', 'exmax');
		$edit->exmax->size=10;
		$edit->exmax->maxlength=12;
		$edit->exmax->css_class='inputonlynum';
		$edit->exmax->rule='numeric|callback_positivo|trim';
		$edit->exmax->insertValue = '0';

		$edit->exord = new inputField('Orden Proveedor','exord');
		$edit->exord->readonly = true;
		$edit->exord->insertValue = '0';
		$edit->exord->size=10;
		$edit->exord->css_class='inputonlynum';
		$edit->exord->style='background:#F5F6CE;';

		$edit->exdes = new inputField('Pedidos Cliente','exdes');
		$edit->exdes->readonly = true;
		$edit->exdes->size=10;
		$edit->exdes->css_class='inputonlynum';
		$edit->exdes->style='background:#F5F6CE;';

		$edit->fechav = new dateField('&Uacute;ltima Venta','fechav','d/m/Y');
		$edit->fechav->readonly = true;
		$edit->fechav->size=10;

		$edit->fdesde = new dateField('Desde','fdesde','d/m/Y');
		$edit->fdesde->calendar=false;
		$edit->fdesde->size=10;

		$edit->fhasta = new dateField('Desde','fhasta','d/m/Y');
		$edit->fhasta->calendar=false;
		$edit->fhasta->size=10;

		$edit->bonicant = new inputField('Cant. Bonifica', 'bonicant');
		$edit->bonicant->size=10;
		$edit->bonicant->maxlength=12;
		$edit->bonicant->css_class='inputonlynum';
		$edit->bonicant->rule='numeric|callback_positivo|trim';

		$edit->bonifica = new inputField('Bonifica', 'bonifica');
		$edit->bonifica->size=10;
		$edit->bonifica->maxlength=12;
		$edit->bonifica->css_class='inputonlynum';
		$edit->bonifica->rule='numeric|callback_positivo|trim';

		if($this->datasis->traevalor('SUNDECOP') == 'S'){
			$edit->mpps = new inputField('MPPS','mpps');
			$edit->mpps->rule='max_length[20]';
			$edit->mpps->size =22;
			$edit->mpps->maxlength =20;

			$edit->cpe = new inputField('CPE','cpe');
			$edit->cpe->rule='max_length[20]';
			$edit->cpe->size =22;
			$edit->cpe->maxlength =20;

			$edit->dcomercial = new dropdownField('Destino Comercial','dcomercial');
			$edit->dcomercial->style='width:200px;';
			$edit->dcomercial->option('','Seleccionar');
			$edit->dcomercial->options('SELECT codigo, descrip FROM sc_dcomercial ORDER BY descrip');

			$edit->rubro = new dropdownField('Rubro','rubro');
			$edit->rubro->style='width:200px;';
			$edit->rubro->option('','Seleccionar');
			$edit->rubro->options('SELECT codigo, concat(codigo, " ", descrip) descrip FROM sc_rubro ORDER BY codigo');

			$edit->subrubro = new dropdownField('Sub Rubro','subrubro');
			$edit->subrubro->style='width:200px;';
			$edit->subrubro->option('','Seleccionar');
			$edit->subrubro->options('SELECT codigo, concat(codigo, " ", descrip) descrip FROM sc_subrubro ORDER BY codigo');

			$edit->cunidad = new dropdownField('Unidad Med.','cunidad');
			$edit->cunidad->style='width:200px;';
			$edit->cunidad->option('','Seleccionar');
			$edit->cunidad->options('SELECT codigo, descrip descrip FROM sc_unidad ORDER BY codigo');

			$edit->cmarca = new inputField('Marca','cmarca');
			$edit->cmarca->rule='max_length[6]|integer';
			$edit->cmarca->css_class='inputonlynum';
			$edit->cmarca->size =8;
			$edit->cmarca->maxlength =6;

			$edit->cmaterial = new dropdownField('Material','cmaterial');
			$edit->cmaterial->style='width:200px;';
			$edit->cmaterial->option('','Seleccionar');
			$edit->cmaterial->options('SELECT codigo,  descrip FROM sc_material ORDER BY descrip');

			$edit->cforma = new dropdownField('Forma','cforma');
			$edit->cforma->style='width:200px;';
			$edit->cforma->option('','Seleccionar');
			$edit->cforma->options('SELECT codigo, descrip FROM sc_forma ORDER BY descrip');

			$edit->cpactivo = new inputField('Principio Act.','cpactivo');
			$edit->cpactivo->rule='max_length[6]|integer';
			$edit->cpactivo->css_class='inputonlynum';
			$edit->cpactivo->size =8;
			$edit->cpactivo->maxlength =6;
		}

		//Descuentos por escala
		for($i=1;$i<=3;$i++){
			$objeto="pescala$i";
			$edit->$objeto = new inputField('Descuento por escala '.$i,$objeto);
			$edit->$objeto->rule='numeric|callback_positivo|trim';
			$edit->$objeto->insertValue='0';
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=5;
			$edit->$objeto->autocomplete=false;

			$objeto="escala$i";
			$edit->$objeto = new inputField('Cantidad m&iacute;nima para la escala '.$i,$objeto);
			$edit->$objeto->rule='numeric|callback_positivo|trim';
			$edit->$objeto->insertValue='0';
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->autocomplete=false;
		}

		for($i=1;$i<=3;$i++){
			$objeto="pfecha$i";
			$edit->$objeto = new dateField("Fecha $i",$objeto,'d/m/Y');
			$edit->$objeto->when =array('show');
			$edit->$objeto->size=10;

			$objeto="Eprepro$i";
			$edit->$objeto = new freeField('','','Precio');
			$edit->$objeto->in="pfecha$i";
			$edit->$objeto->when =array('show');

			$objeto="prepro$i";
			$edit->$objeto = new inputField('',$objeto);
			$edit->$objeto->when =array('show');
			$edit->$objeto->size=10;
			$edit->$objeto->in="pfecha$i";

			$objeto="prov$i";
			$edit->$objeto = new inputField('',$objeto);
			$edit->$objeto->when =array('show');
			$edit->$objeto->size=10;

			$objeto="Eprov$i";
			$edit->$objeto = new freeField('','','Proveedor');
			$edit->$objeto->in="pfecha$i";
			$edit->$objeto->when =array('show');

			if($edit->_status=='show'){
				$prov=$edit->_dataobject->get('prov'.$i);
				$dbprov=$this->db->escape($prov);
				$proveed=$this->datasis->dameval("SELECT nombre FROM sprv WHERE proveed=$dbprov LIMIT 1");
				$objeto="proveed$i";
				$edit->$objeto= new freeField('','',$proveed);
				$edit->$objeto->in="pfecha$i";
			}
		}

		$codigo=$edit->_dataobject->get('codigo');
		$edit->almacenes = new containerField('almacenes',$this->_detalle($codigo));
		$edit->almacenes->when = array('show','modify');

		$edit->mmargen = new inputField('Margen al Mayor','mmargen');
		$edit->mmargen->css_class='inputnum';
		$edit->mmargen->size=10;
		$edit->mmargen->insertValue='0';
		$edit->mmargen->maxlength=10;

		$edit->mmargenplus = new inputField('Descuento +','mmargenplus');
		$edit->mmargenplus->css_class='inputnum';
		$edit->mmargenplus->insertValue='0';
		$edit->mmargenplus->size=10;
		$edit->mmargenplus->maxlength=10;

		$edit->pm = new inputField('Descuento al Mayor A','pm');
		$edit->pm->css_class='inputnum';
		$edit->pm->rule='numeric';
		$edit->pm->size=10;
		$edit->pm->insertValue='0';
		$edit->pm->maxlength=10;

		$edit->pmb = new inputField('Descuento al Mayor B','pmb');
		$edit->pmb->css_class='inputnum';
		$edit->pmb->rule='numeric';
		$edit->pmb->insertValue='0';
		$edit->pmb->size=10;
		$edit->pmb->maxlength=10;

		/*INICIO SINV COMBO*/
		$edit->itcodigo = new inputField('C&oacute;digo <#o#>', 'itcodigo_<#i#>');
		$edit->itcodigo->size    = 12;
		$edit->itcodigo->db_name = 'codigo';
		$edit->itcodigo->rel_id  = 'sinvcombo';
		$edit->itcodigo->rule    = 'callback_chtiposinv';
		$edit->itcodigo->append($bSINV_C);

		$edit->itdescrip = new inputField('Descripci&oacute;n <#o#>', 'itdescrip_<#i#>');
		$edit->itdescrip->size       = 32;
		$edit->itdescrip->db_name    = 'descrip';
		$edit->itdescrip->maxlength  = 50;
		$edit->itdescrip->readonly   = true;
		$edit->itdescrip->rel_id     = 'sinvcombo';
		$edit->itdescrip->type       = 'inputhidden';

		$edit->itcantidad = new inputField('Cantidad <#o#>', 'itcantidad_<#i#>');
		$edit->itcantidad->db_name      = 'cantidad';
		$edit->itcantidad->css_class    = 'inputnum';
		$edit->itcantidad->rel_id       = 'sinvcombo';
		$edit->itcantidad->maxlength    = 10;
		$edit->itcantidad->size         = 5;
		$edit->itcantidad->rule         = 'condi_required|positive';
		$edit->itcantidad->autocomplete = false;
		$edit->itcantidad->onkeyup      = 'totalizarcombo();';
		$edit->itcantidad->insertValue  = '1';

		$edit->itprecio = new inputField('Precio <#o#>', 'itprecio_<#i#>');
		$edit->itprecio->size       = 15;
		$edit->itprecio->db_name    = 'precio';
		$edit->itprecio->maxlength  = 50;
		$edit->itprecio->rel_id     = 'sinvcombo';
		$edit->itprecio->onkeyup    = 'totalizarcombo();';
		$edit->itprecio->css_class  = 'inputnum';

		$edit->itultimo = new inputField('Ultimo <#o#>', 'itultimo_<#i#>');
		$edit->itultimo->size       = 32;
		$edit->itultimo->db_name    = 'ultimo';
		$edit->itultimo->maxlength  = 50;
		$edit->itultimo->readonly   = true;
		$edit->itultimo->rel_id     = 'sinvcombo';
		$edit->itultimo->type       = 'inputhidden';

		$edit->itpond = new inputField('Promedio <#o#>', 'itpond_<#i#>');
		$edit->itpond->size       = 32;
		$edit->itpond->db_name    = 'pond';
		$edit->itpond->maxlength  = 50;
		$edit->itpond->readonly   = true;
		$edit->itpond->rel_id     = 'sinvcombo';
		$edit->itpond->type       = 'inputhidden';

		$ocultos=array('precio1','formcal');
		foreach($ocultos as $obj){
			$obj2='it'.$obj;
			$edit->$obj2 = new hiddenField($obj.' <#o#>', $obj2 . '_<#i#>');
			$edit->$obj2->db_name = 'sinv'.$obj;
			$edit->$obj2->rel_id  = 'sinvcombo';
			$edit->$obj2->pointer = true;
		}
		/*FIN SINV COMBO*/

		/*INICIO SINV ITEM RECETAS*/
		$edit->it2codigo = new inputField('C&oacute;digo <#o#>', 'it2codigo_<#i#>');
		$edit->it2codigo->size    = 12;
		$edit->it2codigo->db_name = 'codigo';
		$edit->it2codigo->rel_id  = 'sinvpitem';
		$edit->it2codigo->append($bSINV_I);

		$edit->it2descrip = new inputField('Descripci&oacute;n <#o#>', 'it2descrip_<#i#>');
		$edit->it2descrip->size       = 32;
		$edit->it2descrip->db_name    = 'descrip';
		$edit->it2descrip->maxlength  = 50;
		$edit->it2descrip->readonly   = true;
		$edit->it2descrip->rel_id     = 'sinvpitem';
		$edit->it2descrip->type       = 'inputhidden';

		$edit->it2cantidad = new inputField('Cantidad <#o#>', 'it2cantidad_<#i#>');
		$edit->it2cantidad->db_name      = 'cantidad';
		$edit->it2cantidad->css_class    = 'inputnum';
		$edit->it2cantidad->rel_id       = 'sinvpitem';
		$edit->it2cantidad->maxlength    = 10;
		$edit->it2cantidad->size         = 5;
		$edit->it2cantidad->rule         = 'positive';
		$edit->it2cantidad->autocomplete = false;
		$edit->it2cantidad->onkeyup      = 'totalizarpitem(<#i#>)';
		$edit->it2cantidad->insertValue  = '1';

		$edit->itunidad = new dropdownField('Unidad <#o#>','itunidad_<#i#>');
		$edit->itunidad->style   = 'width:150px;';
		$edit->itunidad->option('','Seleccionar');
		$edit->itunidad->options('SELECT unidades, unidades descrip FROM unidad ORDER BY unidades');
		$edit->itunidad->rel_id   = 'sinvpitem';
		$edit->itunidad->db_name  = 'unidad';

		$edit->itfactor = new inputField('Factor <#o#>', 'itfactor_<#i#>');
		$edit->itfactor->size       = 10;
		$edit->itfactor->db_name    = 'factor';
		$edit->itfactor->maxlength  = 15;
		$edit->itfactor->css_class  = 'inputnum';
		$edit->itfactor->rel_id     = 'sinvpitem';
		$edit->itfactor->insertValue= '0';
		$edit->itfactor->autocomplete= false;

		$edit->it2merma = new inputField('Ultimo <#o#>', 'it2merma_<#i#>');
		$edit->it2merma->size       = 5;
		$edit->it2merma->db_name    = 'merma';
		$edit->it2merma->maxlength  = 15;
		$edit->it2merma->css_class  = 'inputnum';
		$edit->it2merma->rel_id     = 'sinvpitem';
		$edit->it2merma->insertValue= '0';
		$edit->it2merma->autocomplete= false;

		$ocultos=array('ultimo','pond','formcal','id_sinv');
		foreach($ocultos as $obj){
			$obj2='it2'.$obj;
			$edit->$obj2 = new hiddenField($obj.' <#o#>', $obj2 . '_<#i#>');
			$edit->$obj2->db_name = $obj;
			$edit->$obj2->rel_id  = 'sinvpitem';
		}

		/*INICIO SINV LABOR  ESTACIONES*/
		$edit->it3estacion = new  dropdownField('Estacion <#o#>', 'it3estacion_<#i#>');
		$edit->it3estacion->option('','Seleccionar');
		$edit->it3estacion->options('SELECT estacion,CONCAT(estacion,\'-\',nombre) AS lab FROM esta ORDER BY estacion');
		$edit->it3estacion->style   = 'width:250px;';
		$edit->it3estacion->db_name = 'estacion';
		$edit->it3estacion->rel_id  = 'sinvplabor';

		$edit->it3actividad = new inputField('Actividad <#o#>', 'it3actividad_<#i#>');
		$edit->it3actividad->size       = 32;
		$edit->it3actividad->db_name    = 'actividad';
		$edit->it3actividad->maxlength  = 50;
		$edit->it3actividad->rel_id     = 'sinvplabor';

		$edit->it3tunidad = new dropdownField ('', 'it3tunidad_<#i#>');
		$edit->it3tunidad->option('H','Horas');
		$edit->it3tunidad->option('D','Dias');
		$edit->it3tunidad->option('S','Semanas');
		$edit->it3tunidad->style       = 'width:80px;';
		$edit->it3tunidad->db_name     = 'tunidad';
		$edit->it3tunidad->css_class   = 'inputnum';
		$edit->it3tunidad->rel_id      = 'sinvplabor';
		$edit->it3tunidad->rule        = 'enum[H,S,D]';
		$edit->it3tunidad->insertValue = 'H';

		$edit->it3tiempo = new inputField('', 'it3tiempo_<#i#>');
		$edit->it3tiempo->db_name      = 'tiempo';
		$edit->it3tiempo->css_class    = 'inputnum';
		$edit->it3tiempo->rel_id       = 'sinvplabor';
		$edit->it3tiempo->maxlength    = 10;
		$edit->it3tiempo->size         = 5;
		$edit->it3tiempo->rule         = 'positive';
		$edit->it3tiempo->autocomplete = false;
		$edit->it3tiempo->insertValue  = '1';

		$inven=array();
		$query=$this->db->query('SELECT TRIM(codigo) AS codigo ,TRIM(descrip) AS descrip,tipo,base1,base2,base3,base4,iva,peso,precio1,pond,ultimo FROM sinv WHERE activo=\'S\' AND tipo=\'Articulo\'');
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$ind='_'.$row->codigo;
				$inven[$ind]=array($row->descrip,$row->tipo,$row->base1,$row->base2,$row->base3,$row->base4,$row->iva,$row->peso,$row->precio1,$row->pond);
			}
		}

		$edit->button_status('btn_add_sinvcombo' ,'Agregar','javascript:add_sinvcombo()' ,'CO','modify','button_add_rel');
		$edit->button_status('btn_add_sinvcombo' ,'Agregar','javascript:add_sinvcombo()' ,'CO','create','button_add_rel');
		$edit->button_status('btn_add_sinvpitem' ,'Agregar','javascript:add_sinvpitem()' ,'IT','create','button_add_rel');
		$edit->button_status('btn_add_sinvpitem' ,'Agregar','javascript:add_sinvpitem()' ,'IT','modify','button_add_rel');
		$edit->button_status('btn_add_sinvplabor','Agregar','javascript:add_sinvplabor()','LA','create','button_add_rel');
		$edit->button_status('btn_add_sinvplabor','Agregar','javascript:add_sinvplabor()','LA','modify','button_add_rel');

/*
		$plim = $this->datasis->sidapuede('SINVOTR', 'FIJA_MARG()');
		if(!$plim){
			$edit->pond->mode    = 'autohide';
			$edit->ultimo->mode  = 'autohide';
			$edit->margenu->mode = 'autohide';
			//$edit->motivo->mode  = 'autohide';
		}
*/
		$edit->build();

		$mcodigo = $edit->codigo->value;
		$mfdesde = $this->datasis->dameval("SELECT ADDDATE(MAX(fecha),-30) FROM costos WHERE codigo='".addslashes($mcodigo)."'");
		$mfhasta = $this->datasis->dameval("SELECT MAX(fecha) FROM costos WHERE codigo='".addslashes($mcodigo)."'");

		if($edit->on_success()){
			$rt=array(
				'status' => 'A',
				'mensaje'=> 'Registro guardado',
				'pk'     => $edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form']  =& $edit;
			$this->load->view('view_sinv', $conten );
		}

	}

	function chminven($val){
		$min=intval($val);
		$max=intval($this->input->post('maxven'));
		if($max==0) return true;
		if($min>=$max){
			$this->validation->set_message('chminven',"El campo %s no puede ser mayor que el de venta m&aacute;xima.");
			return false;
		}
		return true;
	}

	function chtiposinv($codigo){
		$dbcodigo = $this->db->escape($codigo);
		$tipo     = $this->datasis->dameval("SELECT tipo FROM sinv WHERE codigo=${dbcodigo}");
		if(empty($tipo )){
			$this->validation->set_message('chtiposinv','El producto colocado en el campo %s no existe.');
			return false;
		}
		if($tipo[0]!='A'){
			$this->validation->set_message('chtiposinv','No puede colocar producto tipo '.$tipo.' en el campo %s.');
			return false;
		}
		return true;
	}

	function almubica(){
		$alma   = $this->input->post('alma');
		$mid    = $this->input->post('mid');
		$ubica  = $this->input->post('ubica');
		$codigo = $this->datasis->dameval('SELECT codigo FROM sinv WHERE id='.$mid);

		$mSQL = 'UPDATE sinvalub SET ubica='.$this->db->escape($ubica).' WHERE codigo='.$this->db->escape($codigo).' AND alma='.$this->db->escape($alma);
		if($this->db->query($mSQL)){
			echo 'Ubicacion del almacen '.$alma.' cambiada a '.$ubica.$mSQL;
		}else{
			echo 'Fallo al intentar hacer el cambio '.$mSQL;
		}
	}

	function _pre_update($do){
		//Chequea las politicas de sinvcontrol
		$codigo  = $do->get('codigo');
		$dbcodigo= $this->db->escape($codigo);
		$ccpre   = $this->datasis->traevalor('SCSTCAMBIAPRECIO');
		if($ccpre=='N'){
			$sucursal    = $this->datasis->traevalor('SUCURSAL');
			$dbsucursal  = $this->db->escape($sucursal);
			$sinvcontrol = $this->datasis->dameval("SELECT precio FROM sinvcontrol WHERE sucursal=${dbsucursal} AND codigo=${dbcodigo}");
			if($sinvcontrol!='S'){
				$rowprec = $this->datasis->damerow('SELECT precio1,precio2,precio3,precio4 FROM sinv WHERE codigo='.$dbcodigo);
				if(!empty($rowprec)){
					$do->set('precio1',$rowprec['precio1']);
					$do->set('precio2',$rowprec['precio2']);
					$do->set('precio3',$rowprec['precio3']);
					$do->set('precio4',$rowprec['precio4']);
				}else{
					$do->error_message_ar['pre_upd']='Producto inexistente';
				}
			}
		}
		//Fin de las politicas de sinvcontrol
		return $this->_pre_inserup($do);
	}

	function _pre_insert($do){
		$codigo = $do->get('codigo');
		$do->set('existen',0);

		$meco = $do->get('peso');
		if(empty($meco))  $do->set('peso',0);
		$meco = $do->get('exord');
		if(empty($meco)) $do->set('exord',0);
		$meco = $do->get('exdes');
		if(empty($meco)) $do->set('exdes',0);
		$meco = $do->get('garantia');
		if(empty($meco)) $do->set('garantia',0);



		if(empty($codigo)){
			$size='6';
			$mSQL="SELECT LPAD(a.hexa,${size},0) AS val FROM serie AS a LEFT JOIN sinv AS b ON b.codigo=LPAD(a.hexa,${size},0) WHERE valor<16777215 AND b.codigo IS NULL LIMIT 1";
			$codigo=$this->datasis->dameval($mSQL);
			if(empty($codigo)){
				$do->error_message_ar['pre_ins']='C&oacute;digos agotados';
				return false;
			}
			$do->set('codigo',$codigo);
		}
		return $this->_pre_inserup($do);
	}

	function _pre_inserup($do){
		$tipo   = $do->get('tipo');
		$estampa= date('Ymd');
		$hora   = date('H:i:s');
		$usuario= $this->secu->usuario();
		$base1  = $do->get('base1');
		$base2  = $do->get('base2');
		$base3  = $do->get('base3');
		$base4  = $do->get('base4');
		$ultimo = $do->get('ultimo');
		$pond   = $do->get('pond');
		$codigo = $do->get('codigo');

		//SINVCOMBO
		if($tipo[0]!='C'){
			$do->truncate_rel('sinvcombo');
		}else{
			//Limpia los vacios y totaliza las bases
			$combobase=$combopond=$comboultimo=0;
			foreach($do->data_rel['sinvcombo'] as $k=>$v){
				if(empty($v['codigo'])){
					$do->rel_rm('sinvcombo',$k);
				}else{
					$combocana  = floatval($do->get_rel('sinvcombo','cantidad',$k));
					$combobase  +=$combocana*floatval($do->get_rel('sinvcombo','precio'  ,$k));
					$combopond  +=$combocana*floatval($do->get_rel('sinvcombo','pond'    ,$k));
					$comboultimo+=$combocana*floatval($do->get_rel('sinvcombo','ultimo'  ,$k));
				}
			}
			$combobase=round($combobase,2);

			if(abs($combobase-$base1)>0.01 || abs($combobase-$base2)>0.01 || abs($combobase-$base3)>0.01 || abs($combobase-$base4)>0.01){
				$do->error_message_ar['pre_upd']=$do->error_message_ar['pre_ins']='Cuando el articulo es un combo las 4 bases deben ser iguales a '.nformat($combobase);
				return false;
			}

			$cana=$do->count_rel('sinvcombo');
			if($cana <= 0){
				$error='ERROR. El Combo debe tener almenos un art&iacute;culo';
				$do->error_message_ar['pre_upd']=$do->error_message_ar['pre_ins']=$error;
				return false;
			}

			for($i=0;$i<$cana;$i++){
				$do->set_rel('sinvcombo','estampa',$estampa,$i);
				$do->set_rel('sinvcombo','hora'   ,$hora   ,$i);
				$do->set_rel('sinvcombo','usuario',$usuario,$i);
			}
		}

		if($tipo[0]!='F'){
			$do->set('aumento',0);
		}

		$comision = $do->get('comision');
		if(empty($comision)) $do->set('comision',0);

		//SINVPITEM
		foreach($do->data_rel['sinvpitem'] as $k=>$v){
			if(empty($v['codigo'])) $do->rel_rm('sinvpitem',$k);
		}
		$cana=$do->count_rel('sinvpitem');
		for($i=0;$i<$cana;$i++){
			$do->set_rel('sinvpitem','estampa',$estampa,$i);
			$do->set_rel('sinvpitem','hora'   ,$hora   ,$i);
			$do->set_rel('sinvpitem','usuario',$usuario,$i);
		}

		//SINVPLABOR
		foreach($do->data_rel['sinvplabor'] as $k=>$v){
			if(empty($v['estacion'])) $do->rel_rm('sinvplabor',$k);
		}
		$cana=$do->count_rel('sinvplabor');
		for($i=0;$i<$cana;$i++){
			$do->set_rel('sinvplabor','estampa',$estampa,$i);
			$do->set_rel('sinvplabor','hora'   ,$hora   ,$i);
			$do->set_rel('sinvplabor','usuario',$usuario,$i);
		}

		//Llena los valores de los detalles
		$cana=$do->count_rel('sinvcombo');
		for($i=0;$i<$cana;$i++){
			$do->set_rel('sinvcombo','estampa',$estampa,$i);
			$do->set_rel('sinvcombo','hora'   ,$hora   ,$i);
			$do->set_rel('sinvcombo','usuario',$usuario,$i);
		}

		//Valida los precios
		for($i=1;$i<5;$i++){
			$prec='precio'.$i;
			$$prec=round($do->get($prec),2); //obtenemos el precio
		}

		$modopre  = $this->datasis->traevalor('SINVMODOPRECIO');

		if ( $modopre == 'S' )
			$mp = ($precio1 >= $precio4 && $precio2 >= $precio4 && $precio3 >= $precio4);
		else
			$mp = ($precio1 >= $precio2 && $precio2 >= $precio3 && $precio3 >= $precio4);

		//if($precio1 >= $precio2 && $precio2 >= $precio3 && $precio3 >= $precio4){
		//if($precio1 >= $precio4 && $precio2 >= $precio4 && $precio3 >= $precio4){

		if ( $mp ){
			$formcal= $do->get('formcal');
			$iva    = $do->get('iva');
			$ultimo = floatval($do->get('ultimo'));
			$pond   = floatval($do->get('pond'));
			$standar= floatval($do->get('standard'));
			if($formcal=='U'){
				$costo = $ultimo;
			}elseif($formcal=='P'){
				$costo = $pond;
			}elseif($formcal=='S'){
				$costo = $standar;
			}else{
				//Toma el mayor
				$costo = ($pond>$ultimo)? $pond : $ultimo;
			}

			for($i=1;$i<5;$i++){
				$prec = 'precio'.$i;
				$base = 'base'.$i;
				$marg = 'margen'.$i;

				//$$base = $$prec*100/(100+$iva);   //calcula la base
				//$$marg = 100-($costo*100/$$base); //calcula el margen

				$mbase = $$prec*100/(100+$iva);   //calcula la base
				$mmarg = 100-($costo*100/$mbase); //calcula el margen


				$do->set($prec,round($$prec,2));
				$do->set($base,round($mbase,2));
				$do->set($marg,round($mmarg,2));

				//$do->set($base,round($$base,2));
				//$do->set($marg,round($$marg,2));
			}
		}else{
			if ( $modopre == 'S' )
				$do->error_message_ar['pre_upd'] =$do->error_message_ar['pre_ins'] = 'Los precios deben cumplir con:<br> El Precio 4 debe ser el menor';
			else
				$do->error_message_ar['pre_upd'] =$do->error_message_ar['pre_ins'] = 'Los precios deben cumplir con:<br> Precio 1 mayor o igual al Precio 2 mayor o igual al  Precio 3 mayor o igual al Precio 4';

			return false;
		}

		//valida las escalas
		for($i=1;$i<4;$i++){
			$esca='pescala'.$i;
			$$esca=$do->get($esca);
			$esca='escala'.$i;
			$$esca=$do->get($esca);
		}

		if(!($pescala3>=$pescala2 && $pescala2>=$pescala1 && $escala3>=$escala2 && $escala2>=$escala1)){
			$do->error_message_ar['pre_upd'] = 'Las escalas deben cumplir con:<br> Escala 3 mayor o igual a la Escala 2 mayor o igual a la Escala 3, en cantidades y descuentos';
			return false;
		}

		$existen=floatval($this->datasis->dameval('SELECT SUM(existen) AS exist FROM itsinv WHERE codigo='.$this->db->escape($codigo)));
		$do->set('existen',$existen);
		return true;
	}

	/* REDONDEA LOS PRECIOS DE TODOS LOS PRODUCTOS */
	function redondear($maximo=null){
		if(empty($maximo)) return null;
		$maximo    = floatval($this->uri->segment($this->uri->total_segments()));
		$manterior = floatval($this->datasis->traevalor('SINVREDONDEO'));
		if(!empty($manterior)){
			if($manterior > $maximo){
				$this->db->simple_query("UPDATE sinv SET redecen='F' WHERE precio1<=${manterior}");
			}
		}
		$this->datasis->ponevalor('SINVREDONDEO',$maximo);
		$mSQL = $this->db->update_string('sinv', array('redecen'=>'N'), "precio1<=${maximo}");
		$this->db->simple_query($mSQL);
		$this->datasis->sinvredondear();

		logusu('SINV',"Redondea Precios ${maximo}");
	}

	// **************************************
	/* RECALCULA LOS PRECIOS DE TODOS LOS PRODUCTOS */
	function recalcular() {
		$mtipo = $this->uri->segment($this->uri->total_segments());
		$this->datasis->sinvrecalcular($mtipo);
		$this->datasis->sinvredondear();
		logusu('SINV',"Recalcula Precios ${mtipo}");
	}


	// **************************************
	//
	// -- Aumento de Precios -- //
	//
	function auprec( $porcent= 0) {
		$data = $this->datasis->damesesion();
		$where = $data['data1'];

		// Respalda los precios anteriores
		$mN = $this->datasis->prox_sql('nsinvplog');
		$ms_codigo = $this->session->userdata('usuario');

		$mSQL = "INSERT INTO sinvplog ";
		$mSQL .= "SELECT '".$mN."', '".addslashes($ms_codigo)."', NOW(), CURTIME(), a.codigo, a.precio1, a.precio2, a.precio3, a.precio4 ";
		$mSQL .= "FROM sinv a ".$where;
		$this->db->query($mSQL);

		if ( $porcent > 0 )
		$mSQL = "SET
			a.precio1=ROUND(a.precio1*(100+$porcent)/100,2),
			a.precio2=ROUND(a.precio2*(100+$porcent)/100,2),
			a.precio3=ROUND(a.precio3*(100+$porcent)/100,2),
			a.precio4=ROUND(a.precio4*(100+$porcent)/100,2)";
		else
		$mSQL = "SET
			a.precio1=ROUND(a.precio1*100/(100-$porcent),2),
			a.precio2=ROUND(a.precio2*100/(100-$porcent),2),
			a.precio3=ROUND(a.precio3*100/(100-$porcent),2),
			a.precio4=ROUND(a.precio4*100/(100-$porcent),2)";

		$this->db->query("UPDATE sinv a ".$mSQL." ".$where);
		$this->datasis->sinvrecalcular("M");
		$this->datasis->sinvredondear();

		echo "Aumento Concluido ($porcent) ";
	}

	// **************************************
	//
	// -- Fija Margenes -- //
	//
	function fijamarg( $margen1=0, $margen2=0, $margen3=0, $margen4=0 ) {
		$data = $this->datasis->damesesion();
		$where = $data['data1'];

		$margen1 = floatval($margen1);
		$margen2 = floatval($margen2);
		$margen3 = floatval($margen3);
		$margen4 = floatval($margen4);

		if ( $margen1*$margen2*$margen3*$margen4 > 0 ){

			if ( $margen1>=$margen2 && $margen2>=$margen3 && $margen3>=$margen4 ){
				// Respalda los precios anteriores
				$mN = $this->datasis->prox_sql('nsinvplog');
				$ms_codigo = $this->session->userdata('usuario');

				$mSQL = "INSERT INTO sinvplog ";
				$mSQL .= "SELECT '".$mN."', '".addslashes($ms_codigo)."', now(), curtime(), a.codigo, a.precio1, a.precio2, a.precio3, a.precio4 ";
				$mSQL .= "FROM sinv a ".$where;
				$this->db->query($mSQL);

				$mSQL = " SET a.margen1=$margen1, a.margen2=$margen2, a.margen3=$margen3, a.margen4=$margen4 ";

				$this->db->query("UPDATE sinv a ".$mSQL." ".$where);

				$this->datasis->sinvrecalcular("P");
				$this->datasis->sinvredondear();

				echo "Cambio Concluido  ";
			} else {
				echo "Los margenes deben ir de Mayor (1) a Menor (4)";
			}
		} else {
			echo "Los Margenes deben ser mayores qie 0";
		}
	}


	// **************************************
	//
	//  -- Aumento de Precios al Mayor --  //
	//
	function auprecm($porcent = 0) {
		$data = $this->datasis->damesesion();
		$where = $data['data1'];
		if ( $porcent > 0 ){
			$mSQL = "SET mmargen=round(round(ultimo*(100+mmargen)/100,2)*100/(100-$porcent),2)*100/ultimo -100 ";
			$this->db->simple_query("UPDATE sinv a ".$mSQL." ".$where);
			echo " Aumento Concluido";
		} else {
			$mSQL = "SET mmargen=round( round(ultimo*(100+mmargen)/100,2)*(100+$porcent)/100,2)*100/ultimo -100 ";
			$this->db->simple_query("UPDATE sinv a ".$mSQL." ".$where);
			echo " Descuento  Concluido";
		}
	}


	// **************************************
	//
	// -- Cambio de IVA                 -- //
	//
	function cambiva($iva) {
		$dbiva = floatval($iva);
		$data  = $this->datasis->damesesion();
		$where = $data['data1'];
		$tasa = $this->datasis->damereg("SELECT tasa, redutasa, sobretasa FROM civa ORDER BY fecha DESC LIMIT 1");
		$mSQL = "SET
			a.precio1=ROUND(a.base1*(100+$iva)/100,2),
			a.precio2=ROUND(a.base2*(100+$iva)/100,2),
			a.precio3=ROUND(a.base3*(100+$iva)/100,2),
			a.precio4=ROUND(a.base4*(100+$iva)/100,2),
			a.iva=${dbiva}";

		$this->db->query("UPDATE sinv a ".$mSQL." ".$where);
		$this->datasis->sinvrecalcular("M");
		$this->datasis->sinvredondear();

		echo "Cambio Concluido ($iva) ";
	}



	// **************************************
	//
	// -- Cambio de Ubicaciones -- //
	//
	// **************************************
	function cambiaubica($mubica) {
		$data = $this->datasis->damesesion();
		$where = $data['data1'];
		if ( !empty($where)){
			$mSQL = "SET ubica=".$this->db->escape($mubica)." ";
			$this->db->query("UPDATE sinv a ".$mSQL." ".$where);
			echo "Aumento Concluido";
		} else
			echo "No se filtraron los registros";
	}



	//*****************************
	//
	//  Cambia el Grupo
	//
	function sinvcamgrup() {
		$productos  = $this->input->post('productos');
		$mgrupo     = rawurldecode($this->input->post('grupo'));

		if($this->datasis->dameval("SELECT COUNT(*) FROM grup WHERE grupo='$mgrupo'") == 0 ){
			echo "Grupo no existe $mgrupo";
		} else {
			//Busca el Depto y Linea del grupo
			$productos = implode(",",$productos);
			$depto = $this->datasis->dameval("SELECT depto FROM grup WHERE grupo='$mgrupo'");
			$linea = $this->datasis->dameval("SELECT linea FROM grup WHERE grupo='$mgrupo'");
			//echo "$mgrupo $productos";
			$mSQL = "UPDATE sinv SET grupo='$mgrupo', linea='$linea', depto='$depto' WHERE id IN ($productos) ";
			$this->db->simple_query($mSQL);
			logusu("SINV","Cambio grupo ".$mgrupo."-->".$productos);
			echo "Cambiado a Depto $depto, linea $linea, grupo $mgrupo Exitosamente";
		}
	}


	//******************************************************************
	//  Recalcula segun dolares
	//
	function recaldolar($cambio = 0){
		$cambio = rawurldecode($this->input->post('cambio'));
		$data   = $this->datasis->damesesion();

		$where = ' WHERE standard>0 ';
		if ( isset($data['data1']) ) $where = $data['data1'].' AND standard>0 ';


		$cambio = floatval($cambio);
		if( $cambio > 0 ){
			$mSQL = "UPDATE sinv SET standard=ultimo WHERE dolar>0 AND formcal='S' AND standard=0";
			$this->db->query($mSQL);

			$mSQL = "SET
			precio1=ROUND(precio1*dolar*${cambio}/standard,2),
			precio2=ROUND(precio1*dolar*${cambio}/standard,2),
			precio3=ROUND(precio1*dolar*${cambio}/standard,2),
			precio4=ROUND(precio1*dolar*${cambio}/standard,2) ";
			$this->db->query("UPDATE sinv a ".$mSQL." ".$where." AND dolar > 0 AND formcal='S'");


			$mSQL = "SET standard=ROUND( dolar*${cambio},2) ";
			$this->db->query("UPDATE sinv a ".$mSQL." ".$where." AND dolar > 0 AND formcal='S'");


			$this->datasis->sinvrecalcular("P");
			$this->datasis->sinvredondear();
			echo " Cambio Concluido ";
		} else
			echo " Cambio debe ser mayor que 0 ";


		//} else echo " Debe filtrar los productos!  ";
		//} else	echo " Debe filtrar los productos!  ";

	}


	//*****************************
	//
	//  Cambia el Marca
	//
	function sinvcammarca() {
		$productos  = $this->input->post('productos');
		$mmarca     = rawurldecode($this->input->post('marca'));

		if($this->datasis->dameval("SELECT COUNT(*) FROM marc WHERE TRIM(marca)='".addslashes($mmarca)."'") == 0 ){
			echo "Marca no existe $mmarca";
		} else {
			$productos = implode(",",$productos);
			$mSQL = "UPDATE sinv SET marca='".addslashes($mmarca)."' WHERE id IN ($productos) ";
			$this->db->simple_query($mSQL);
			logusu("SINV","Cambio marca ".$mmarca."-->".$productos);
			echo "Cambiadas las  marcas $mmarca Exitosamente";
		}
	}

	//*****************************
	//
	//  Existe el Codigo
	//
	function sinvcodigoexiste(){
		$id = rawurldecode($this->input->post('codigo'));
		//$id = $this->uri->segment($this->uri->total_segments());
		$existe = $this->datasis->dameval("SELECT count(*) FROM sinv WHERE codigo='".addslashes($id)."'");
		$devo = 'N '.$id;
		if ($existe > 0 ) {
			$devo  ='S';
			$devo .= $this->datasis->dameval("SELECT descrip FROM sinv WHERE codigo='".addslashes($id)."'");
		}
		echo $devo;
	}

	//*****************************
	//
	// Cambia el codigo
	function sinvcodigo() {
		$mexiste  = $this->input->post('tipo');
		$mmcodigo = rawurldecode($this->input->post('codigo'));
		$mviejoid = rawurldecode($this->input->post('viejo'));
		$mdeja    = rawurldecode($this->input->post('deja'));

		$mmviejo  = $mviejoid;
		$mviejoid = $this->datasis->dameval('SELECT id FROM sinv WHERE codigo='.$this->db->escape($mviejoid));
		$mviejo   = $this->db->escape($mmviejo);
		$mcodigo  = $this->db->escape($mmcodigo);

		$vpond    = $this->datasis->dameval('SELECT pond   FROM sinv WHERE id='.$this->db->escape($mviejoid));
		$vultimo  = $this->datasis->dameval('SELECT ultimo FROM sinv WHERE id='.$this->db->escape($mviejoid));
		$vexisten = $this->datasis->dameval('SELECT COALESCE(SUM(existen),0) FROM itsinv WHERE codigo='.$this->db->escape($mviejo));

		if( $mexiste == 'S' ){
			// Elimina anterior
			$mSQL = "UPDATE sinv SET existen = 0 WHERE codigo=".$mviejo;
			$this->db->query($mSQL);
			if ( $mdeja == 0 ){
				$mSQL = "DELETE FROM sinv WHERE codigo=".$mviejo;
				$this->db->query($mSQL);
			}
		} else {
			$mSQL = "UPDATE sinv SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
			$this->db->query($mSQL);
		}

		if ( $mexiste=='S' ) {
			$mSQL  = "SELECT * FROM itsinv WHERE codigo=".$mviejo;
			$query = $this->db->query($mSQL);
			$mexisten = 0;
			if ($query->num_rows() > 0 ) {
				foreach ($query->result() as $row ) {
					$dbalma = $this->db->escape($row->alma);
					$mSQL = "INSERT IGNORE INTO itsinv SET codigo=".$mcodigo.", alma=${dbalma}, existen=0";
					$this->db->query($mSQL);
					$mSQL = "UPDATE itsinv SET existen=existen+".$row->existen."
						WHERE codigo=${mcodigo} AND alma=${dbalma}";
					$this->db->query($mSQL);
					$mexisten += $row->existen;
				}
			}

			//Actualiza sinv
			$mSQL = "UPDATE sinv SET
						pond   = (pond*existen   +".$mexisten."*".$vpond."  )/(existen +".$mexisten."),
						ultimo = (ultimo*existen +".$mexisten."*".$vultimo.")/(existen +".$mexisten.")
			WHERE codigo=".$mcodigo;
			$this->db->query($mSQL);

			$mSQL = "UPDATE sinv SET existen=existen+".$mexisten." WHERE codigo=".$mcodigo;
			$this->db->query($mSQL);

			// Borra los items
			$mSQL = "DELETE FROM itsinv WHERE codigo=".$mviejo;
			$this->db->query($mSQL);
		}else{
			$mSQL = "UPDATE itsinv SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
			$this->db->query($mSQL);
		}

		$mSQL = "UPDATE itstra SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE itscst SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE sitems SET codigoa=".$mcodigo." WHERE codigoa=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE itsnot SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE itsnte SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE itspre SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE itssal SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE itconv SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE seri SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE itpfac SET codigoa=".$mcodigo." WHERE codigoa=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE itordc SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE IGNORE invresu SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE IGNORE invresu SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE IGNORE barraspos SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE IGNORE sinvpa SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE IGNORE sinvfot SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE IGNORE sinvpromo SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE IGNORE sinvprov SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE IGNORE costos SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
		$this->db->query($mSQL);

		$mSQL = "UPDATE sinv SET enlace=".$mcodigo." WHERE enlace=".$mviejo;
		$this->db->query($mSQL);

		// Inventario invfel
		if(!$this->db->table_exists('invfelr')){
			$m      = 1;
			$mubica = 99;
			$mSQL = "UPDATE IGNORE invfelr SET codigo=".$mcodigo." WHERE codigo=".$mviejo;
			$this->db->query($mSQL);
			$m = $this->datasis->dameval("SELECT COUNT(*) FROM invfelr WHERE codigo=".$mviejo);
			while ( $m > 0) {
				$mSQL = "UPDATE IGNORE invfelr SET codigo=".$mcodigo.", ubica=$mubica WHERE codigo=".$mviejo;
				$this->db->query($mSQL);
				$m = $this->datasis->dameval("SELECT COUNT(*) FROM invfelr WHERE codigo=".$mviejo);
				$mubica = $mubica -1;
			}
		}

		if ( $this->db->table_exists('sinvfusion') == false ) {
			$mSQL  = "CREATE TABLE sinvfusion ( ";
			$mSQL .= "	id INT(10) NOT NULL AUTO_INCREMENT, ";
			$mSQL .= "	anterior VARCHAR(15) NOT NULL, ";
			$mSQL .= "	nuevo VARCHAR(15) NOT NULL, ";
			$mSQL .= "	usuario VARCHAR(15) NOT NULL, ";
			$mSQL .= "	fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, ";
			$mSQL .= "	PRIMARY KEY (id) ";
			$mSQL .= ") ";
			$mSQL .= "COLLATE='latin1_swedish_ci' ";
			$mSQL .= "ENGINE=MyISAM ";
			$this->db->simple_query($mSQL);
		}

		$this->datasis->sinvrecalcular("P",$mcodigo);

		$mSQL = "INSERT INTO sinvfusion SET anterior=".$mviejo.", nuevo=".$mcodigo.", usuario=".$this->db->escape($this->session->userdata('usuario'));
		$this->db->simple_query($mSQL);

		logusu("SINV","Cambio codigo ".$mmviejo."-->".$mmcodigo);
	}

	//*****************************
	//
	// Busca Principio Activo
	function autopactivo() {
		$q   = $this->input->post('tecla');
		$data = '[{ }]';
		if($q!==false){
			$mid = $this->db->escape('%'.$q.'%');
			$mSQL = "SELECT * FROM pactivo
			WHERE nombre LIKE ${mid} ORDER BY nombre LIMIT 30";

			$query = $this->db->query($mSQL);
			$retArray = array();
			$retorno = array();
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']   = $row['nombre'];
					$retArray['label']   = utf8_encode(trim($row['nombre']));
					$retArray['pactivo'] = utf8_encode(trim($row['nombre']));
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}
		}
		echo $data;

	}



	function _sinvcodig(){
		$mexiste  = $this->input->post('tipo');
		$mmcodigo = rawurldecode($this->input->post('codigo'));
		$mviejoid = $this->input->post('viejo');

		$mmviejo  = $this->datasis->dameval('SELECT codigo FROM sinv WHERE id='.$this->db->escape($mviejoid));
		$mviejo   = $this->db->escape($mmviejo);
		$mcodigo  = $this->db->escape($mmcodigo);
		//echo "$mexiste  $mcodigo  $mviejo ";

		if($mexiste=='S'){
			$mSQL = "DELETE FROM sinv WHERE codigo=$mviejo";
			$this->db->simple_query($mSQL);
		} else {
			$mSQL = "UPDATE sinv SET codigo=$mcodigo WHERE codigo=$mviejo";
			$this->db->simple_query($mSQL);
		}

		if ( $mexiste=='S' ) {
			$mSQL  = "SELECT * FROM itsinv WHERE codigo=$mviejo";
			$query = $this->db->query($mSQL);
			$mexisten = 0;
			if ($query->num_rows() > 0 ) {
				foreach ($query->result() as $row ) {
					$dbalma = $this->db->escape($row->alma);
					$mSQL   = "UPDATE itsinv SET existen=existen+".$row->existen."
						WHERE codigo=$mcodigo AND alma=$dbalma";
					$this->db->simple_query($mSQL);
					$mexisten += $row->existen;
				}
			}
			//Actualiza sinv
			$mSQL = "UPDATE sinv SET existen=exiten+".$mexisten." WHERE codigo=$mcodigo";
			// Borra los items
			$mSQL = "DELETE FROM itsinv WHERE codigo=$mviejo";
			$this->db->simple_query($mSQL);
		}else{
			$mSQL = "UPDATE itsinv SET codigo=$mcodigo WHERE codigo=$mviejo";
			$this->db->simple_query($mSQL);
		}

		$mSQL = "UPDATE itstra SET codigo=$mcodigo WHERE codigo=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE itscst SET codigo=$mcodigo WHERE codigo=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE sitems SET codigoa=$mcodigo WHERE codigoa=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE itsnot SET codigo=$mcodigo WHERE codigo=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE itsnte SET codigo=$mcodigo WHERE codigo=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE itspre SET codigo=$mcodigo WHERE codigo=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE itssal SET codigo=$mcodigo WHERE codigo=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE itconv SET codigo=$mcodigo WHERE codigo=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE seri SET codigo=$mcodigo WHERE codigo=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE itpfac SET codigoa=$mcodigo WHERE codigoa=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE itordc SET codigo=$mcodigo WHERE codigo=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE IGNORE invresu SET codigo=$mcodigo WHERE codigo=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE IGNORE invresu SET codigo=$mcodigo WHERE codigo=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE IGNORE barraspos SET codigo=$mcodigo WHERE codigo=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE IGNORE sinvfot SET codigo=$mcodigo WHERE codigo=$mviejo";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE IGNORE sinvpromo SET codigo=$mcodigo WHERE codigo=$mviejo";
		$this->db->simple_query($mSQL);

		logusu("SINV","Cambio codigo ".$mmviejo."-->".$mmcodigo);
	}



	// Codigos de barra suplementarios
	function sinvbarras() {
		$mid      = $this->input->post('id');
		$mbarras  = trim(rawurldecode($this->input->post('codigo')));
		$mcodigo  = trim($this->datasis->dameval("SELECT codigo FROM sinv WHERE id=$mid"));
		$htmlcod  = addslashes($mcodigo);

		//Busca si ya esta
		$check = $this->datasis->dameval("SELECT COUNT(*) FROM sinv WHERE codigo='$mbarras' OR barras='$mbarras' OR alterno='$mbarras' ");
		if ($check > 0 && !empty($barras) ) {
			echo "Codigo ya existen en Inventario";
		} else {
			$check = $this->datasis->dameval("SELECT COUNT(*) FROM barraspos WHERE suplemen='$mbarras' ");
			if ($check > 0 ) {
				echo "Codigo ya existen en codigos suplementarios";
			} else {
				$mSQL = "INSERT INTO barraspos SET codigo='$htmlcod', suplemen='$mbarras'";
				$this->db->simple_query($mSQL);
				logusu("SINV","Codigo de Barras Agregado".$mcodigo."-->".$mbarras);
				echo "Registro de Codigo Exitoso";
			}
		}
	}

	// Borra Codigo de barras suplementarios
	function sinvborrasuple() {
		$codigo   = $this->input->post('codigo');
		$mSQL = "DELETE FROM barraspos WHERE suplemen='$codigo'";
		$this->db->simple_query($mSQL);
		logusu("SINV","Eliminado Codigo Suplementario ".$codigo);
		echo "Codigo Eliminado";
	}

	// Borra Codigo de proveedores
	function sinvborraprv() {
		$codigo   = $this->input->post('codigo');
		$proveed  = $this->input->post('proveed');

		$mSQL = "DELETE FROM sinvprov WHERE codigop='$codigo' AND proveed='$proveed'";
		$this->db->simple_query($mSQL);
		logusu("SINV","Eliminado Codigo de proveedor $codigo => $proveed");
		echo "Codigo Eliminado";
	}

	// Busca Proveedor por autocomplete
	function sinvproveed(){
		$mid   = $this->input->post('tecla');
		if (empty($mid)) $mid='AN';
		$mSQL  = "SELECT CONCAT(TRIM(nombre),' (',RPAD(proveed,5,' '),')') nombre, proveed codigo FROM sprv WHERE nombre LIKE '%".$mid."%' ORDER BY nombre LIMIT 10";
		$data = "[]";
		$query = $this->db->query($mSQL);
		$retArray = array();
		$retorno = array();
		if ($query->num_rows() > 0){
			foreach( $query->result_array() as  $row ) {
				$retArray['label'] = $row['nombre'];
				$retArray['codigo'] = $row['codigo'];
				array_push($retorno, $retArray);
			}
			$data = json_encode($retorno);
		} else {
			$ret = '{data : []}';
		}
		echo $data;
	}

	// Busca Cliente por autocomplete
	function sinvcliente(){
		$mid   = $this->input->post('tecla');
		if (empty($mid)) $mid='AN';
		$mSQL  = "SELECT CONCAT(TRIM(nombre),' (',RPAD(cliente,5,' '),')') nombre, cliente codigo FROM scli WHERE nombre LIKE '%".$mid."%' ORDER BY nombre LIMIT 10";
		$data = "[]";
		$query = $this->db->query($mSQL);
		$retArray = array();
		$retorno = array();
		if ($query->num_rows() > 0){
			foreach( $query->result_array() as  $row ) {
				$retArray['label'] = $row['nombre'];
				$retArray['codigo'] = $row['codigo'];
				array_push($retorno, $retArray);
			}
			$data = json_encode($retorno);
		} else {
			$ret = '{data : []}';
		}
		echo $data;
	}

	// Agrega el codigo del producto segun el Proveedor
	function sinvsprv(){
		$codigo  = $this->uri->segment($this->uri->total_segments());
		$cod_prv = $this->uri->segment($this->uri->total_segments()-1);
		$id      = $this->uri->segment($this->uri->total_segments()-2);
		$mSQL = "REPLACE INTO sinvprov SELECT '$cod_prv' proveed, '$codigo' codigop, codigo FROM sinv WHERE id=$id ";
		$this->db->simple_query($mSQL);
		echo " codigo=$codigo guardado al prv $cod_prv " ;
	}

	//*************************
	//
	// Promociones
	//
	function sinvpromo() {
		$mid     = $this->input->post('id');
		$margen  = $this->input->post('margen');
		$mcodigo = $this->datasis->dameval("SELECT codigo FROM sinv WHERE id=$mid");
		$htmlcod = addslashes($mcodigo);

		//Busca si ya esta
		$check = $this->datasis->dameval("SELECT count(*) FROM sinvpromo WHERE codigo='".$htmlcod."'");

		if ($check == 0 ) {
			$this->db->simple_query("INSERT INTO sinvpromo SET codigo='"+$htmlcod+"'");
		}

		if ( $margen == 0 ) {
			$mSQL = "DELETE FROM sinvpromo WHERE WHERE codigo='$htmlcod' ";
		} else {
			$mSQL = "UPDATE sinvpromo SET margen=$margen WHERE codigo='$htmlcod' ";
		}
		$this->db->simple_query($mSQL);
		logusu("SINV","Promocion ".$htmlcod."-->".$margen);
		echo "Cambio Exitoso";
	}

	//***************************
	//
	// Promociones a clientes
	function sinvdescu() {
		$tipo     = $this->uri->segment($this->uri->total_segments());
		$porcent  = $this->uri->segment($this->uri->total_segments()-1);
		$cod_cli  = $this->uri->segment($this->uri->total_segments()-2);
		$id       = $this->uri->segment($this->uri->total_segments()-3);

		$codigo   = $this->datasis->dameval("SELECT codigo FROM sinv WHERE id=$id");
		$htmlcod = addslashes($codigo);

		//Busca si ya esta
		$check = $this->datasis->dameval("SELECT count(*) FROM sinvpromo a JOIN sinv b ON a.codigo=b.codigo WHERE b.id=$id AND cliente='".$cod_cli."'");

		if ($check == 0 ) {
			$this->db->simple_query("INSERT INTO sinvpromo SET codigo='".$htmlcod."', cliente='$cod_cli'");
		}

		if ( $porcent == 0 ) {
			$mSQL = "DELETE FROM sinvpromo WHERE codigo='$htmlcod' AND cliente='$cod_cli'";
		} else {
			$mSQL = "UPDATE sinvpromo SET margen=$porcent, tipo='$tipo' WHERE codigo='$htmlcod' AND cliente='$cod_cli'";
		}
		$this->db->simple_query($mSQL);
		logusu("SINV","Promocion cliente $cod_cli codigo ".$htmlcod."-->".$porcent);

		echo "Descuento Guardado ";
	}

	function cprecios(){
		$this->rapyd->uri->keep_persistence();

		$cpre=$this->input->post('pros');
		if($cpre!==false){
			$msj=$this->_cprecios();
		}else{
			$msj='';
		}

		$this->rapyd->load("datafilter2","datagrid");
		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;digo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
				'retornar'=>array('proveed'=>'proveed'),
				'titulo'  =>'Buscar Proveedor');

		$bSPRV=$this->datasis->modbus($mSPRV);

		$link2=site_url('inventario/common/get_linea');
		$link3=site_url('inventario/common/get_grupo');

		$script='
		$(document).ready(function(){
			$(".inputnum").numeric(".");
			$("#depto").change(function(){
				depto();
				$.post("'.$link2.'",{ depto:$(this).val() },function(data){$("#linea").html(data);})
				$.post("'.$link3.'",{ linea:"" },function(data){$("#grupo").html(data);})
			});
			$("#linea").change(function(){
				linea();
				$.post("'.$link3.'",{ linea:$(this).val() },function(data){$("#grupo").html(data);})
			});
			$("#grupo").change(function(){
				grupo();
			});
			$("#sinvprecioc").submit(function() {
				return confirm("Se van a actualizar todos los precios en pantalla \nEstas seguro de que quieres seguir??");
			});
			depto();
			linea();
			grupo();
		});

		function depto(){
			if($("#depto").val()!=""){
				$("#nom_depto").attr("disabled","disabled");
			}
			else{
				$("#nom_depto").attr("disabled","");
			}
		}

		function linea(){
			if($("#linea").val()!=""){
				$("#nom_linea").attr("disabled","disabled");
			}
			else{
				$("#nom_linea").attr("disabled","");
			}
		}

		function grupo(){
			if($("#grupo").val()!=""){
				$("#nom_grupo").attr("disabled","disabled");
			}
			else{
				$("#nom_grupo").attr("disabled","");
			}
		}';

		$filter = new DataFilter2('Filtro por Producto');

		$select=array(
			'IF(formcal=\'U\',ultimo,IF(formcal=\'P\',pond,IF(formcal=\'S\',standard,GREATEST(ultimo,pond)))) AS costo',
			'a.existen','a.marca','a.tipo','a.id',
			'TRIM(codigo) AS codigo',
			'a.descrip','precio1','precio2','precio3','precio4','b.nom_grup','b.grupo',
			'c.descrip AS nom_linea','c.linea','d.descrip AS nom_depto','d.depto AS depto',
			'a.base1','a.base2','a.base3','a.base4'
		);

		$filter->db->select($select);
		$filter->db->from('sinv AS a');
		$filter->db->join('grup AS b','a.grupo=b.grupo');
		$filter->db->join('line AS c','b.linea=c.linea');
		$filter->db->join('dpto AS d','c.depto=d.depto');
		$filter->db->where('a.activo','S');
		$filter->script($script);

		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo-> size=15;
		$filter->codigo->group = "Uno";

		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->db_name='CONCAT_WS(" ",a.descrip,a.descrip2)';
		$filter->descrip-> size=30;
		$filter->descrip->group = "Uno";

		$filter->tipo = new dropdownField("Tipo", "tipo");
		$filter->tipo->db_name=("a.tipo");
		$filter->tipo->option('',"Todos");
		$filter->tipo->option("Articulo","Art&iacute;culo");
		$filter->tipo->option("Servicio","Servicio");
		$filter->tipo->option("Descartar","Descartar");
		$filter->tipo->option("Consumo","Consumo");
		$filter->tipo->option("Fraccion","Fracci&oacute;n");
		$filter->tipo->style='width:120px;';
		$filter->tipo->group = "Uno";

		$filter->clave = new inputField("Clave", "clave");
		$filter->clave -> size=15;
		$filter->clave->group = "Uno";

		$filter->proveed = new inputField("Proveedor", "proveed");
		$filter->proveed->append($bSPRV);
		$filter->proveed->db_name='CONCAT_WS("-",`a`.`prov1`, `a`.`prov2`, `a`.`prov3`)';
		$filter->proveed -> size=10;
		$filter->proveed->group = "Dos";

		$filter->depto2 = new inputField("Departamento", "nom_depto");
		$filter->depto2->db_name="d.descrip";
		$filter->depto2 -> size=5;
		$filter->depto2->group = "Dos";

		$filter->depto = new dropdownField("Departamento","depto");
		$filter->depto->db_name="d.depto";
		$filter->depto->option("","Seleccione un Departamento");
		$filter->depto->options("SELECT depto, CONCAT(depto,'-',descrip) descrip FROM dpto WHERE tipo='I' ORDER BY depto");
		$filter->depto->in="depto2";
		$filter->depto->group = "Dos";
		$filter->depto->style='width:190px;';

		$filter->linea = new inputField('Linea', 'nom_linea');
		$filter->linea->db_name='c.descrip';
		$filter->linea -> size=5;
		$filter->linea->group = 'Dos';

		$filter->linea2 = new dropdownField('L&iacute;nea','linea');
		$filter->linea2->db_name='c.linea';
		$filter->linea2->option('',"Seleccione un Departamento primero");
		$filter->linea2->in='linea';
		$filter->linea2->group = 'Dos';
		$filter->linea2->style='width:190px;';

		$depto=$filter->getval('depto');
		if($depto!==false){
			$filter->linea2->options("SELECT linea, CONCAT(linea,'-',descrip) descrip FROM line WHERE depto='$depto' ORDER BY descrip");
		}else{
			$filter->linea2->option("","Seleccione un Departamento primero");
		}

		$filter->grupo2 = new inputField('Grupo', 'nom_grupo');
		$filter->grupo2->db_name='b.nom_grup';
		$filter->grupo2 -> size=5;
		$filter->grupo2->group = 'Dos';

		$filter->grupo = new dropdownField('Grupo', 'grupo');
		$filter->grupo->db_name='b.grupo';
		$filter->grupo->option('','Seleccione una L&iacute;nea primero');
		$filter->grupo->in='grupo2';
		$filter->grupo->group = 'Dos';
		$filter->grupo->style='width:190px;';

		$linea=$filter->getval('linea2');
		if($linea!==false){
			$filter->grupo->options("SELECT grupo, CONCAT(grupo,'-',nom_grup) nom_grup FROM grup WHERE linea='$linea' ORDER BY nom_grup");
		}else{
			$filter->grupo->option("","Seleccione un Departamento primero");
		}

		$filter->marca = new dropdownField("Marca", "marca");
		$filter->marca->option('','Todas');
		$filter->marca->options("SELECT TRIM(marca) AS clave, TRIM(marca) AS valor FROM marc ORDER BY marca");
		$filter->marca->style='width:220px;';
		$filter->marca->group = "Dos";

		$filter->buttons('reset','search');
		$filter->build('dataformfiltro');

		$ggrid='';
		if($filter->is_valid()){
			$attr=array('id'=>'sinvprecioc');
			$ggrid =form_open(uri_string(),$attr);
			foreach ($filter->_fields as $field_name => $field_copy){
				$ggrid.= form_hidden($field_copy->id, $field_copy->value);
			}

			$grid = new DataGrid('Art&iacute;culos de Inventario');
			$grid->order_by('codigo','asc');
			$grid->per_page = 15;
			$link  = anchor('inventario/sinv/dataedit/show/<#id#>','<#codigo#>');
			$uri_2 = anchor('inventario/sinv/dataedit/create/<#id#>','Duplicar');

			$grid->column_orderby('C&oacute;digo','codigo','codigo');
			$grid->column_orderby('Descripci&oacute;n','descrip','descrip');
			$grid->column_orderby('Marca','marca','marca');
			for($i=1;$i<5;$i++){
				$obj='precio'.$i;
				$$obj = new inputField($obj, $obj);
				$$obj->grid_name=$obj.'[<#id#>]';
				$$obj->status   ='modify';
				$$obj->size     =8;
				$$obj->css_class='inputnum';
				$$obj->autocomplete=false;

				$grid->column("Precio $i",$$obj,'align=right');
			};
			$grid->column('Costo'     ,'<nformat><#costo#></nformat>'  ,'align=right');
			$grid->column('Existencia','<nformat><#existen#></nformat>','align=right');

			$grid->submit('pros', 'Cambiar','BR');
			$grid->build();
			$ggrid.=$grid->output;
			$ggrid.=form_close();
			//echo $this->db->last_query();
		}

		$data['content'] = '<div class="alert">'.$msj.'</div>';
		$data['content'].= $ggrid;
		$data['filtro']  = $filter->output;
		$data['title']   = heading('Cambio de precios');
		$data['head']    = $this->rapyd->get_head().script('jquery.pack.js');
		$data['head']   .= script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$this->load->view('view_ventanas', $data);
	}

	//Segun coicoi cambia los precios
	function _cprecios(){
		$precio1=$this->input->post('precio1');
		$precio2=$this->input->post('precio2');
		$precio3=$this->input->post('precio3');
		$precio4=$this->input->post('precio4');

		$msj=''; $error=0;
		foreach($precio1 as $id => $p1){
			$dbid=$this->db->escape($id);
			$p2=floatval($precio2[$id]);
			$p3=floatval($precio3[$id]);
			$p4=floatval($precio4[$id]);
			$dbcosto=$this->datasis->dameval("SELECT IF(formcal='U',ultimo,IF(formcal='P',pond,IF(formcal='S',standard,GREATEST(ultimo,pond)))) AS costo FROM sinv WHERE id=${dbid}");

			if($p1>=$p2 && $p2>=$p3 && $p4>=$p4 && $p1*$p2*$p3*$p4>0 && $p1>=$dbcosto && $p2>=$dbcosto && $p3>=$dbcosto && $p4>=$dbcosto){
				$sql=array();
				for($i=1;$i<5;$i++){
					$pprecio='p'.$i;
					$precio=round($$pprecio,2);
					$base  = "${precio}*100/(100+iva)";
					$costo = "IF(formcal='U',ultimo,IF(formcal='P',pond,IF(formcal='S',standard,GREATEST(ultimo,pond))))";

					$sql[]="precio${i}=${precio}";
					$sql[]="base${i}  =ROUND(${base},2)";
					$sql[]="margen${i}=ROUND(100-((${costo})*100/(${base})),2)";

				}
				$campos=implode(',',$sql);

				$mSQL="UPDATE `sinv` SET ${campos} WHERE id=${dbid}";
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'sinv'); $error++; }

				$cod=$this->datasis->dameval('SELECT codigo FROM sinv WHERE id='.$dbid);
				logusu('sinv',"Cambio de precios a $cod $p1; $p2; $p3; $p4");
			}else{
				$codigo=$this->datasis->dameval("SELECT codigo FROM sinv WHERE id=${dbid}");
				$msj.='En el art&iacute;culo '.TRIM($codigo).' no se actualizo porque los precios deben tener valores mayores que el costo y en forma decrecientes (Precio 1 >= Precio 2 >= Precio 3 >= Precio 4).'.br();
			}

		}
		if($error>0) $msj.='Hubo alg&uacute;n error, se gener&oacute; un centinela';
		return $msj;
	}

	// Sugiere proximo codigo de inventario
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

	// Busca el Ultimo codigo
	function ultimo(){
		$ultimo=$this->datasis->dameval("SELECT codigo FROM sinv ORDER BY codigo DESC LIMIT 1");
		echo $ultimo;
	}

	function sugerir(){
		$long = 6;
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,${long},0) FROM serie LEFT JOIN sinv ON LPAD(codigo,${long},0)=LPAD(hexa,${long},0) WHERE valor<65535 AND codigo IS NULL LIMIT 1");
		echo $ultimo;
	}

	function chexiste($codigo){
		$dbcodigo=$this->db->escape($codigo);
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM sinv WHERE codigo=$dbcodigo");
		if($check > 0){
			$descrip=$this->datasis->dameval("SELECT descrip FROM sinv WHERE codigo=$dbcodigo");
			$this->validation->set_message('chexiste',"El codigo ${codigo} ya existe para el producto ${descrip}");
			return false;
		}else{
		 return true;
		}
	}

	function chlinfe($val){
		$lindia=intval($this->input->post('lindia'));

		if($val=='S' && $lindia<=0){
			$this->validation->set_message('chlinfe',"Si activa la limitaci&oacute;n de ventas seguidas debe colocar la cantidad de d&iacute;as");
			return false;
		}
		return true;
	}

	function chobligafraccion($enlace){
		$tipo=$this->input->post('tipo');

		if($tipo[0]=='F' && empty($enlace)){
			$this->validation->set_message('chobligafraccion',"Cuando en producto es fraccion es obligatorio el campo %s");
			return false;
		}

		if($tipo[0]=='F' && !is_numeric($enlace)){
			$this->validation->set_message('chobligafraccion','El campo %s debe ser num&eacute;rico');
			return false;
		}

		return true;
	}

	function chenlace($enlace){

		$tipo=$this->input->post('tipo');
		if($tipo[0]=='F'){
			if(empty($enlace)){
				$this->validation->set_message('chenlace','El campo %s es requerido cuando el producto es Fraccion');
				return false;
			}else{
				$dbcodigo=$this->db->escape($enlace);
				$tipo = $this->datasis->dameval('SELECT tipo FROM sinv WHERE codigo='.$dbcodigo);

				if(empty($tipo)){
					$this->validation->set_message('chenlace','El producto en el campo %s no existe');
					return false;
				}

				if($tipo[0]!='A'){
					$this->validation->set_message('chenlace','El producto en el campo %s debe ser tipo Articulo');
					return false;
				}
			}
		}
		return true;
	}

	// Si existe el codigo Alterno
	function chalterno($alterno){
		$alterno = trim($alterno);
		if (empty($alterno)){
			return true;
		}else{
			if(!$this->validation->_dataobject->is_unique($this->validation->_current_field,$alterno)){
				$codigo   =$this->validation->_dataobject->get('codigo');
				if(!empty($codigo)) $ww=' AND codigo<>'.$this->db->escape($codigo); else $ww='';
				$dbalterno=$this->db->escape($alterno);
				$descrip  =$this->datasis->dameval("SELECT descrip FROM sinv WHERE alterno=$dbalterno ${ww} LIMIT 1");
				$this->validation->set_message('chalterno',"El c&oacute;digo alterno $alterno ya existe para el producto $descrip.");
				return false;
			}else{
				return true;
			}
		}
	}

	//
	function _detalle($codigo){
		$salida='';
		$estilo='';
		if(!empty($codigo)){
			$this->rapyd->load('dataedit','datagrid');
			$grid = new DataGrid('Existencias por Almac&eacute;n');
			$grid->db->select(array('b.ubides','a.codigo','a.alma','a.existen',"IF(b.ubides IS NULL,'SIN ALMACEN',b.ubides) AS nombre"));
			$grid->db->from('itsinv AS a');
			$grid->db->join('caub as b','a.alma=b.ubica','LEFT');
			$grid->db->where('codigo',$codigo);

			//$link=anchor('/inventario/caub/dataedit/show/<#alma#>','<#alma#>');
			$link  = "<a href=\"javascript:void(0);\" onclick=\"window.open('".base_url();
			$link .= "inventario/caub', '_blank', 'width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx='+((screen.availWidth/2)-400)+',screeny='+((screen.availHeight/2)-300)+'');\" heigth=\"600\"><#alma#></a>";

			$grid->column('Almacen' ,$link, "style='font-size:12px;font-weight:bold;'");
			$grid->column('Nombre'  ,'nombre',"style='font-size: 10px'");
			$grid->column('Cantidad','existen','align="right" '."style='font-size: 10px'");

			$grid->build('datagridsimple');

			if($grid->recordCount>0) $salida=$grid->output;
			$salida = html_entity_decode($salida);
			$estilo="
			<style type='text/css'>
			.simplerow  { color: #153D51;border-bottom: 1px solid #ECECEC; font-family: Lucida Grande, Verdana, Geneva, Sans-serif;	font-size: 12px; font-weight: bold;}
			.simplehead { background: #382408; border-bottom: 1px solid #ECECEC;color: #EEFFEE;font-family: Lucida Grande, Verdana, Geneva, Sans-serif; font-size: 12px;padding-left:5px;}
			.simpletabla { width:100%;colspacing:0px; colpadding:0px}
			</style>";
		}
		return $estilo.$salida;
	}


	//Manda una tabla para la consulta
	function sinvitems($id = 0){
		$dbid=intval($id);
		$salida = 'No hay Existencias';
		$codigo = $this->datasis->dameval("SELECT codigo FROM sinv WHERE id=${dbid}");

		if(!empty($codigo)){
			$mSQL  = "SELECT a.codigo, a.alma, a.existen, IF(b.ubides IS NULL,'SIN ALMACEN',b.ubides) AS nombre ";
			$mSQL .= "FROM itsinv AS a LEFT JOIN caub as b ON a.alma=b.ubica ";
			$mSQL .= "WHERE codigo=".$this->db->escape($codigo);

			$query = $this->db->query($mSQL);

			if( $query->num_rows() > 0 ){
			    $salida  = "<table class='bordetabla' cellpadding=1 cellspacing=0 width='200'>";
			    $salida .= "<tr class='tableheader'><th colspan='3'>Almacenes</th></tr>";
				$i = 0;
				foreach($query->result() as $row){
						$salida .= "<tr class='littletablerow'><td>";
						$salida .= $row->alma;
						$salida .= '</td><td>';
						$salida .= $row->nombre;
						$salida .= '</td><td>';
						$salida .= $row->existen;
						$salida .= "</td></tr>\n";
						$i++;
				}
				while($i<5){
						$salida .= "<tr class='littletablerow'><td>";
						$salida .= "&nbsp;</td><td>";
						$salida .= "&nbsp;</td><td>";
						$salida .= "&nbsp;</td></tr>\n";
						$i++;
				}

				$salida .= '</table>';
			}
		}
		echo $salida;
	}


	function _pre_del($do){
		$codigo=$this->db->escape($do->get('codigo'));
		$check =  intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM sitems WHERE codigoa=${codigo}"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM itscst WHERE codigo=${codigo}"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM itstra WHERE codigo=${codigo}"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM itspre WHERE codigo=${codigo}"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM itsnot WHERE codigo=${codigo}"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM itsnte WHERE codigo=${codigo}"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM itsinv WHERE codigo=${codigo} AND existen>0"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM sinvpitem WHERE codigo=${codigo}"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM sinvcombo WHERE codigo=${codigo}"));

		if($this->db->table_exists('ordpitem')){
			$check += $this->datasis->dameval("SELECT COUNT(*) AS cana FROM ordpitem WHERE codigo=${codigo}");
		}

		if($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Producto con Movimiento no puede ser Borrado, solo se puede inactivar';
			return false;
		}

		$check = intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM sinv WHERE enlace=${codigo}"));
		if($check>0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='El producto esta enlazado a otros producto';
			return false;
		}

		return true;
	}

	// Trae la descripcion de una Barra
	function barratonombre(){
		if($this->input->post('barra')){
			$barra=$this->db->escape($this->input->post('barra'));
			echo $this->datasis->dameval("SELECT descrip FROM sinv WHERE barras=$barra");
		}
	}

	//Consulta rapida
	function consulta(){
		$this->load->helper('openflash');
		$this->rapyd->load("datagrid");
		$fields = $this->db->field_data('sinv');
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
		$id = $claves['id'];

		$mCodigo = $this->datasis->dameval("SELECT codigo FROM sinv WHERE id=".$claves['id']."");

		$mSQL  = 'SELECT a.tipoa, MID(a.fecha,1,7) mes, sum(a.cana*(a.tipoa="F")) cventa, sum(a.cana*(a.tipoa="D")) cdevol, sum(a.cana*if(a.tipoa="D",-1,1)) cana, sum(a.tota*(a.tipoa="F")) mventa, sum(a.tota*(a.tipoa="D")) mdevol, sum(a.tota*if(a.tipoa="D",-1,1)) tota ';
		$mSQL .= "FROM sitems a WHERE a.codigoa='".addslashes($mCodigo)."' ";
		$mSQL .= "AND a.fecha >= CONCAT(MID(SUBDATE(curdate(),365),1,8),'01') ";
		$mSQL .= "GROUP BY MID( a.fecha ,1,7)  WITH ROLLUP LIMIT 24";
		$mGrid1 = '';

		$mSQL  = 'SELECT a.usuario, a.fecha, MID(a.hora,1,5) hora, MID(REPLACE(a.comenta,"ARTICULO DE INVENTARIO",""),1,30) comenta, a.modulo ';
		$mSQL .= 'FROM logusu a WHERE a.comenta LIKE "%'.addslashes($mCodigo).'%" ';
		$mSQL .= "ORDER BY a.fecha DESC LIMIT 30";

		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			$mGrid2 = '
			<div id="tableDiv_Logusu" class="tableDiv">
			<table id="Open_text_Logusu" class="FixedTables" >
			<thead>
			<tr>
				<th>Fecha</th>
				<th>Usuario</th>
				<th>Hora</th>
				<th>Modulo</th>
				<th>Accion</th>
			</tr>
			</thead>
			<tbody>';

			$m = 1;
			foreach ($query->result() as $row){
				if($m == 1) { $mGrid2.='<tr id="firstTr">'; } else { $mGrid2.='<tr>'; };
				$mGrid2.="
				<tr>
					<td>".$row->fecha."</td>
					<td>".$row->usuario."</td>
					<td>".$row->hora."</td>
					<td>".$row->modulo."</td>
					<td>".$row->comenta."</td>
				</tr>";
				$m++;
			}
			$mGrid2 .= "
			</tbody>
			</table>
			</div>";
		} else {
			$mGrid2 = "NO SE ENCONTRO MOVIMIENTO";
		}

		$descrip = $this->datasis->dameval("SELECT descrip FROM sinv WHERE id=".$claves['id']." ");

		/*mes, cventa, mventa, mpvp, ccompra, mcompra,util, margen, promedio*/
		$script = "
		<script type=\"text/javascript\" >

		<!-- All the scripts will go here  -->
		var dsOption= {
			fields :[
				{name : 'mes'},
				{name : 'cventa',   type: 'float' },
				{name : 'mventa',   type: 'float' },
				{name : 'mpvp' ,    type: 'float' },
				{name : 'ccompra',  type: 'float' },
				{name : 'mcompra',  type: 'float' },
				{name : 'util',     type: 'float' },
				{name : 'margen',   type: 'float' },
				{name : 'promedio', type: 'float' }
			],
			recordType : 'object'
		}

		var colsOption = [
			{id: 'mes',      header: 'Mes',          width :60, frozen: true   },
			{id: 'cventa' ,  header: 'Cant. Venta',  width :80, align: 'right' },
			{id: 'mventa' ,  header: 'Costo Venta',  width :80, align: 'right' },
			{id: 'mpvp' ,    header: 'Precio Venta', width :80, align: 'right' },
			{id: 'ccompra' , header: 'Cant Compra',  width :80, align: 'right' },
			{id: 'mcompra' , header: 'Monto Compra', width :80, align: 'right' },
			{id: 'util' ,    header: 'Utilidad',     width :80, align: 'right' },
			{id: 'margen' ,  header: 'Margen %',     width :80, align: 'right' },
			{id: 'promedio', header: 'Costo Prom.',  width :80, align: 'right' }
		];

		var gridOption={
			id : 'grid1',
			loadURL : '/proteoerp/inventario/sinv/consulta_ventas/".$id."',
			container : 'grid1_container',
			dataset : dsOption ,
			columns : colsOption,
			allowCustomSkin: true,
			skin: 'vista',
			toolbarContent: 'pdf'
		};

		var dsOption1= {
			fields :[
				{name : 'fecha'   },
				{name : 'usuario' },
				{name : 'hora'    },
				{name : 'modulo'  },
				{name : 'comenta' }
			],
			recordType : 'object'
		}

		var colsOption1 = [
			{id: 'fecha',   header: 'Fecha',      width :70, frozen: true },
			{id: 'usuario', header: 'Usuario',    width :60 },
			{id: 'hora' ,   header: 'Hora',       width :60 },
			{id: 'modulo' , header: 'Modulo',     width :60 },
			{id: 'comenta', header: 'Comentario', width :200 }
		];

		var gridOption1={
			id : 'grid2',
			loadURL : '/proteoerp/inventario/sinv/consulta_logusu/".$id."',
			container : 'grid2_container',
			dataset : dsOption1 ,
			columns : colsOption1,
			toolbarContent: 'pdf',
			allowCustomSkin: true,
			skin: 'vista'
		};

		var mygrid=new Sigma.Grid(gridOption);
		Sigma.Util.onLoad( Sigma.Grid.render(mygrid) );

		var mygrid1=new Sigma.Grid(gridOption1);
		Sigma.Util.onLoad( Sigma.Grid.render(mygrid1) );
		</script>";

		$style = '';

		$data['content'] = "
		<table align='center' border='0' cellspacing='2' cellpadding='2' width='98%'>
			<tr>
				<td valign='top'>
					<div style='border: 3px outset #EFEFEF;background: #EFEFFF '>
					<div id='grid1_container' style='width:500px;height:250px'></div>
					</div>
				</td>
				<td>".
				open_flash_chart_object( 250,180, site_url("inventario/sinv/ventas/$id"))."
				</td>
			</tr>
			<tr>
				<td>
					<div style='border: 3px outset #EFEFEF;background: #EFEFFF '>
					<div id='grid2_container' style='width:500px;height:250px'></div>
					</div>

				</td>
				<td>".
				open_flash_chart_object( 250,180, site_url("inventario/sinv/compras/$id"))."
				</td>
			</tr>
		</table>";

		$data['title']    = '<h1>Consulta de Articulo de Inventario</h1>';

		$data['script']   = script("plugins/jquery.numeric.pack.js");
		$data['script']  .= script("plugins/jquery.floatnumber.js");
		$data['script']  .= script("gt_msg_en.js");
		$data['script']  .= script("gt_grid_all.js");
		$data['script']  .= $script;

		$data['style']    = style('gt_grid.css');
		$data["subtitle"] = "
			<div align='center' style='border: 2px outset #EFEFEF;background: #EFEFEF;font-size:18px'>
				<a href='javascript:javascript:history.go(-1)'>(".addslashes($mCodigo).") ".$descrip."</a>
			</div>";

		$data['head']  = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function consulta_ventas() {
		$id = $this->uri->segment($this->uri->total_segments());
		$mCodigo = $this->datasis->dameval("SELECT codigo FROM sinv WHERE id=".$id."");

		$mSQL  = 'SELECT MID(a.fecha,1,7) mes, sum(a.cana*(a.tipoa="F")) cventa, sum(a.cana*(a.tipoa="D")) cdevol, sum(a.cana*if(a.tipoa="D",-1,1)) cana, sum(a.tota*(a.tipoa="F")) mventa, sum(a.tota*(a.tipoa="D")) mdevol, sum(a.tota*if(a.tipoa="D",-1,1)) tota ';
		$mSQL .= "FROM sitems a WHERE a.codigoa='".addslashes($mCodigo)."' ";
		$mSQL .= "AND a.fecha >= CONCAT(MID(SUBDATE(curdate(),365),1,8),'01') ";
		$mSQL .= "GROUP BY MID( a.fecha ,1,7)  WITH ROLLUP LIMIT 60";

		$mSQL  = "
		SELECT
			MID(a.fecha,1,7) mes,
			sum(a.cantidad*(a.origen='3I')) cventa,
			ROUND(sum(a.promedio*a.cantidad*(a.origen='3I')),2) mventa,
			ROUND(sum(a.venta*(a.origen='3I')),2) mpvp,
			sum(a.cantidad*(a.origen='2C')) ccompra,
			sum(a.monto*(a.origen='2C')) mcompra,
			ROUND(sum((a.venta-a.cantidad*a.promedio)*(a.origen='3I')),2) util,
			100- ROUND( sum(a.cantidad*a.promedio*(a.origen='3I'))*100/SUM(a.venta), 2) margen,
			round(avg(promedio),2) promedio
		FROM costos a WHERE a.codigo='".addslashes($mCodigo)."' AND a.origen IN ('3I','2C')
			AND a.fecha >= CONCAT(MID(SUBDATE(curdate(),365),1,8),'01')
		GROUP BY MID( a.fecha ,1,7)  WITH ROLLUP LIMIT 24";

		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			$retArray = array();
			foreach( $query->result_array() as  $row ) {
				$retArray[] = $row;
			}
			$data = json_encode($retArray);
			$ret = "{data:" . $data .",\n";
			$ret .= "recordType : 'array'}";
		} else {
			$ret = '{data : []}';
		}
		echo $ret;
	}

	function _post_insert($do){
		$codigo=$do->get('codigo');

		$precio1=$do->get('precio1');
		$precio2=$do->get('precio2');
		$precio3=$do->get('precio3');
		$precio4=$do->get('precio4');

		$query = $this->db->query('SELECT ubica FROM caub WHERE gasto=\'N\' AND invfis=\'N\'');
		foreach ($query->result() as $row){
			$sql = $this->db->insert_string('itsinv', array('codigo' => $codigo,'alma'=>$row->ubica, 'existen'=>0));
			$this->db->simple_query($sql);
		}

		$ccpre   = $this->datasis->traevalor('SCSTCAMBIAPRECIO');
		if($ccpre=='N'){
			$sucu= $this->datasis->traevalor('SUCURSAL');
			if(!empty($sucu)){
				$data = array('sucursal' => $sucu, 'codigo' => $codigo, 'precio' => 'S');
				$sql  = $this->db->insert_string('sinvcontrol', $data);
				$this->db->simple_query($sql);
			}
		}

		logusu('sinv',"Creo  ${codigo} precios: ${precio1}, ${precio2}, ${precio3}, ${precio4}");
	}

	function _post_update($do){
		$codigo=$do->get('codigo');

		$precio1=$do->get('precio1');
		$precio2=$do->get('precio2');
		$precio3=$do->get('precio3');
		$precio4=$do->get('precio4');
		logusu('sinv',"Modifico $codigo precios: ${precio1},${precio2},${precio3}, ${precio4}");
	}

	function _post_delete($do){
		$codigo   = $do->get('codigo');
		$dbcodigo = $this->db->escape($codigo);
		$mSQL="DELETE FROM itsinv WHERE codigo=${dbcodigo}";
		$this->db->simple_query($mSQL);

		if($this->db->table_exists('sinvcontrol')){
			$mSQL="DELETE FROM sinvcontrol WHERE codigo=${dbcodigo}";
			$this->db->simple_query($mSQL);
		}
		logusu('sinv',"Elimino ${codigo}");
	}

	function consulta_logusu() {
		$id = $this->uri->segment($this->uri->total_segments());
		$mCodigo = $this->datasis->dameval("SELECT codigo FROM sinv WHERE id=".$id."");

		$mSQL  = 'SELECT a.fecha, a.usuario,  MID(a.hora,1,5) hora, a.modulo, MID(REPLACE(a.comenta,"ARTICULO DE INVENTARIO",""),1,30) comenta ';
		$mSQL .= 'FROM logusu a WHERE a.comenta LIKE "%'.addslashes($mCodigo).'%" ';
		$mSQL .= "ORDER BY a.fecha DESC LIMIT 60";
		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			$retArray = array();
			foreach( $query->result_array() as  $row ) {
				$retArray[] = $row;
			}
			$data = json_encode($retArray);
			$ret = "{data:" . $data .",\n";
			$ret .= "recordType : 'array'}";
			//$ret .= $mSQL;
		} else {
			$ret = '{data : []}';
		}
		echo $ret;
	}

	function ventas($id=''){
		if (empty($id)) return;
		$this->load->library('Graph');

		$codigo = $this->datasis->dameval("SELECT codigo FROM sinv WHERE id=$id");
		$mSQL = "SELECT	a.tipoa,MID(a.fecha,1,7) mes,
			sum(a.cana*(a.tipoa='F')) cventa,
			sum(a.cana*(a.tipoa='D')) cdevol,
			sum(a.cana*if(a.tipoa='D',-1,1)) cana,
			sum(a.tota*(a.tipoa='F')) mventa,
			sum(a.tota*(a.tipoa='D')) mdevol,
			sum(a.tota*if(a.tipoa='D',-1,1)) tota
		FROM sitems a
		WHERE a.codigoa='$codigo' AND a.tipoa IN ('F','D') AND a.fecha >= CONCAT(MID(SUBDATE(curdate(),365),1,8),'01')
		GROUP BY MID( a.fecha, 1,7 )  LIMIT 7";

		$maxval = 0;
		$query = $this->db->query($mSQL);
		$data_1=$data_2=$meses=array();
		foreach($query->result() as $row ){
			if ($row->cana>$maxval) $maxval=$row->cana;
			$meses[]   = $row->mes;
			$data_1[]  = $row->cana;
		}
		$om=1;while($maxval/$om>100) $om=$om*10;

		$bar_1 = new bar(75, '#0053A4');

		$bar_1->key('Venta',10);

		for($i=0;$i<count($data_1);$i++ ){
			$bar_1->add_data_tip($data_1[$i]/$om, graph::esc( number_format($data_1[$i],2,',','.')));
			$bar_1->links[]= site_url("/ventas/clientes/mensuales/$codigo/".$meses[$i]);
		}
		$g = new graph();
		$g->set_is_decimal_separator_comma(1);
		if($maxval > 0 ) {
			$g->title( 'Ventas por Mes ','{font-size: 16px; color:#0F3054}' );
			$g->data_sets[] = $bar_1;

			$g->set_x_labels($meses);
			$g->set_x_label_style( 10, '#000000', 2, 1 );
			$g->set_x_axis_steps( 10 );
			$g->set_x_legend( 'Meses ', 14,'#004381' );

			$g->bg_colour = '#FFFFFF';
			$g->set_tool_tip( '#key#<br>Mes: #x_label# <br>Cantidad: #tip#' );
			$g->set_y_max(ceil($maxval/$om));
			$g->y_label_steps(5);
			$g->set_y_legend('Ventas x '.number_format($om,0,'','.'), 16, '#004381' );
		} else
			$g->title( 'No existen ventas en el a&ntilde;o seleccionado','{font-size:18px; color: #d01f3c}');
		$g->bg_colour='#FFFFFF';
		echo utf8_encode($g->render());
	}

	function compras($id=''){
		if (empty($id)) return;
		$this->load->library('Graph');

		$codigo = $this->datasis->dameval("SELECT codigo FROM sinv WHERE id=$id");
		$mSQL = "SELECT	MID(a.fecha,1,7) mes,
			sum(a.cantidad*(b.tipo_doc='FC')) cventa,
			sum(a.cantidad*(b.tipo_doc='NC')) cdevol,
			sum(a.cantidad*if(b.tipo_doc='NC',-1,1)) cana,
			sum(a.importe*(b.tipo_doc='FC')) mventa,
			sum(a.importe*(b.tipo_doc='NC')) mdevol,
			sum(a.importe*if(b.tipo_doc='NC',-1,1)) tota
		FROM itscst a JOIN scst b ON a.control=b.control
		WHERE a.codigo='$codigo' AND b.tipo_doc IN ('FC','NC') AND b.fecha >= CONCAT(MID(SUBDATE(curdate(),365),1,8),'01')
				AND  a.fecha <= b.actuali
		GROUP BY MID( b.fecha, 1,7 ) LIMIT 7  ";

		$maxval = 0;
		$query = $this->db->query($mSQL);
		$data_1=$data_2=$meses=array();
		foreach($query->result() as $row ){
			if ($row->cana>$maxval) $maxval=$row->cana;
			$meses[]   = $row->mes;
			$data_1[]  = $row->cana;
		}
		$om=1;while($maxval/$om>100) $om=$om*10;

		$bar_1 = new bar(75, '#9053A4');

		$bar_1->key('Compra',10);

		for($i=0;$i<count($data_1);$i++ ){
			$bar_1->add_data_tip($data_1[$i]/$om, graph::esc( number_format($data_1[$i],2,',','.')));
			$bar_1->links[]= site_url("/ventas/clientes/mensuales/$codigo/".$meses[$i]);
		}
		$g = new graph();
		$g->set_is_decimal_separator_comma(1);
		if($maxval > 0 ) {
			$g->title( 'Compras por Mes ','{font-size: 16px; color:#0F3054}' );
			$g->data_sets[] = $bar_1;

			$g->set_x_labels($meses);
			$g->set_x_label_style( 10, '#000000', 2, 1 );
			$g->set_x_axis_steps( 10 );
			$g->set_x_legend( 'Meses ', 14,'#004381' );

			$g->bg_colour = '#FFFFFF';
			$g->set_tool_tip( '#key#<br>Mes: #x_label# <br>Cantidad: #tip#' );
			$g->set_y_max(ceil($maxval/$om));
			$g->y_label_steps(5);
			$g->set_y_legend('Compras x '.number_format($om,0,'','.'), 16, '#004381' );
		} else
			$g->title( 'No existen ventas en el a&ntilde;o seleccionado','{font-size:18px; color: #d01f3c}');
		$g->bg_colour='#FFFFFF';
		echo utf8_encode($g->render());
	}

	// Qr del Inventario
	function sinvqr($id = 0 ){
		$this->load->library('qr');
		$reg = $this->datasis->damereg("SELECT codigo, descrip, barras, ficha FROM sinv WHERE id=$id");
		header('Content-type: image/pnp');
		echo $this->qr->imgcode("Codigo: ".trim($reg['codigo'])."\n".trim($reg['descrip'])."\nBarras: ".trim($reg['barras']).trim($reg['ficha']) );
	}

	function ibarras($id = 0, $alto=20){
		//error_reporting(0);
		require_once 'Image/Barcode2.php';
		$codigo = $this->datasis->dameval("SELECT codigo FROM sinv WHERE id=$id");
		Image_Barcode2::draw($codigo, 'code128', 'png', true, $alto);
	}

	//******************************************************************
	//
	//
	//******************************************************************
	function desundecop(){
		$this->rapyd->load('dataedit');

		$script='';
		$arr= array( 'cdpactivo'=>'pactivo', 'cmarca'=>'marca', 'cunidad'=>'unidad' );

		foreach($arr as $campo=>$autocom){
			$script.= "
			$('#${campo}').autocomplete({
				appendTo: '#fedita',
				source: function( req, add){
					$.ajax({
						url:  '".site_url('ajax/buscasundecob/'.$autocom)."',
						type: 'POST', dataType: 'json', data: 'q='+req.term,
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$('#${campo}').val('');
									$('#${campo}descrip_val').text('');
								}else{
									$.each( data, function(i, val){ sugiere.push( val );} );
								}
								add(sugiere);
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$('#${campo}').val(ui.item.codigo);
					$('#${campo}descrip_val').text(ui.item.descrip);
				}
			});";
		}


		$do = new DataObject('sinv');
		$do->pointer('sc_unidad' ,'sc_unidad.codigo =sinv.cunidad' ,'sc_unidad.descrip  AS cunidaddescrip'  ,'left');
		$do->pointer('sc_pactivo','sc_pactivo.codigo=sinv.cpactivo','sc_pactivo.descrip AS cpactivodescrip' ,'left');
		$do->pointer('sc_marca'  ,'sc_marca.codigo  =sinv.cmarca'  ,'sc_marca.descrip   AS cmarcadescrip'   ,'left');

		$edit = new DataEdit('', $do);
		$edit->script($script,'modify');
		//$edit->on_save_redirect=false;

		//$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert', '_scpost_insert');
		$edit->post_process('update', '_scpost_update');
		$edit->post_process('delete', '_scpost_delete');
		$edit->pre_process('insert',  '_scpre_insert');
		$edit->pre_process('update',  '_scpre_update');
		$edit->pre_process('delete',  '_scpre_delete');

		$edit->mpps = new inputField('MPPS','mpps');
		$edit->mpps->rule      = 'max_length[20]';
		$edit->mpps->size      = 22;
		$edit->mpps->maxlength = 20;

		$edit->cpe = new inputField('CPE','cpe');
		$edit->cpe->rule      = 'max_length[20]';
		$edit->cpe->size      = 22;
		$edit->cpe->maxlength = 20;

		$edit->cdpactivo = new inputField('Principio Act.','cdpactivo');
		$edit->cdpactivo->db_name = 'cpactivo';
		$edit->cdpactivo->rule = 'max_length[6]|integer';
		$edit->cdpactivo->size = 8;

		$edit->cpactivodescrip = new inputField('', 'cdpactivodescrip');
		$edit->cpactivodescrip->db_name = 'cpactivodescrip';
		$edit->cpactivodescrip->pointer = true;
		$edit->cpactivodescrip->type    = 'inputhidden';
		$edit->cpactivodescrip->in      = 'cdpactivo';

		$edit->dcomercial = new dropdownField('Destino Comercial','dcomercial');
		$edit->dcomercial->style='width:230px;';
		$edit->dcomercial->option('','Seleccionar');
		$edit->dcomercial->options('SELECT codigo, descrip FROM sc_dcomercial ORDER BY codigo');

		$edit->rubro = new dropdownField('Rubro','rubro');
		$edit->rubro->style='width:230px;';
		$edit->rubro->option('','Seleccionar');
		$edit->rubro->options('SELECT codigo, descrip FROM sc_rubro ORDER BY descrip');

		$edit->subrubro = new dropdownField('Sub Rubro','subrubro');
		$edit->subrubro->style='width:230px;';
		$edit->subrubro->option('','Seleccionar');
		$edit->subrubro->options('SELECT codigo, concat(codigo, " ", descrip) descrip FROM sc_subrubro ORDER BY codigo');

		$edit->cunidad = new inputField('Unidad Med.','cunidad');
		$edit->cunidad->rule='max_length[6]|integer';
		$edit->cunidad->size =8;

		$edit->cunidaddescrip = new inputField('', 'cunidaddescrip');
		$edit->cunidaddescrip->db_name     = 'cunidaddescrip';
		$edit->cunidaddescrip->pointer     = true;
		$edit->cunidaddescrip->type='inputhidden';
		$edit->cunidaddescrip->in = 'cunidad';

		$edit->cmarca = new inputField('Marca','cmarca');
		$edit->cmarca->rule='max_length[6]|integer';
		$edit->cmarca->size =8;

		$edit->cmarcadescrip = new inputField('', 'cmarcadescrip');
		$edit->cmarcadescrip->db_name     = 'cmarcadescrip';
		$edit->cmarcadescrip->pointer     = true;
		$edit->cmarcadescrip->type='inputhidden';
		$edit->cmarcadescrip->in = 'cmarca';

		$edit->cmaterial = new dropdownField('Material','cmaterial');
		$edit->cmaterial->style='width:230px;';
		$edit->cmaterial->option('','Seleccionar');
		$edit->cmaterial->options('SELECT codigo,  descrip FROM sc_material ORDER BY descrip');

		$edit->cforma = new dropdownField('Forma','cforma');
		$edit->cforma->style = 'width:230px;';
		$edit->cforma->option('','Seleccionar');
		$edit->cforma->options('SELECT codigo, descrip FROM sc_forma ORDER BY descrip');

		$edit->fracci = new inputField('Cantidad','fracci');
		$edit->fracci->rule='max_length[6]|numeric';
		$edit->fracci->css_class='inputnum';
		$edit->fracci->size =8;

		$edit->build();

		$script= '';

		$data['content'] = $edit->output;
		$this->load->view('jqgrid/ventanajq', $data);

	}

	function _scpre_insert($do){
		return true;
	}

	function _scpre_update($do){
		return true;
	}

	function _scpre_delete($do){
		return true;
	}

	function _scpost_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}

	function _scpost_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _scpost_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	//******************************************************************
	// Forma de Marcas
	//
	function marcaform(){
		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('ID');
		$grid->params(array(
			'hidden'      => 'true',
			'align'       => "'center'",
			'width'       => 20,
			'editable'    => 'false',
			'editoptions' => '{readonly:true,size:10}'
			)
		);

		$grid->addField('marca');
		$grid->label('Marca');
		$grid->params(array(
			'width'     => 180,
			'editable'  => 'true',
			'edittype'  => "'text'",
			'editrules' => '{required:true}'
			)
		);

		$grid->showpager(true);
		$grid->setViewRecords(false);
		$grid->setWidth('300');
		$grid->setHeight('240');

		$grid->setUrlget(site_url('inventario/marc/getdata/'));
		$grid->setUrlput(site_url('inventario/marc/setdata/'));

		$mgrid = $grid->deploy();

		$msalida  = '<script type="text/javascript">'."\n";
		$msalida .= '
		$("#newapi'.$mgrid['gridname'].'").jqGrid({
			ajaxGridOptions : {type:"POST"}
			,jsonReader : { root:"data", repeatitems: false }
			'.$mgrid['table'].'
			,scroll: true
			,pgtext: null, pgbuttons: false, rowList:[]
		})
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'navGrid\',  "#pnewapi'.$mgrid['gridname'].'",{edit:false, add:false, del:true, search: false});
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'inlineNav\',"#pnewapi'.$mgrid['gridname'].'");
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'filterToolbar\');
		';

		$msalida .= "\n</script>\n";
		$msalida .= '<id class="anexos"><table id="newapi'.$mgrid['gridname'].'"></table>';
		$msalida .= '<div   id="pnewapi'.$mgrid['gridname'].'"></div></div>';

		echo $msalida;

	}


	//******************************************************************
	// Forma para Unidades
	//
	function uniform(){
		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'hidden'      => 'true',
			'align'       => "'center'",
			'width'       => 20,
			'editable'    => 'false',
			'editoptions' => '{readonly:true,size:10}'
		));

		$grid->addField('unidades');
		$grid->label('Nombre');
		$grid->params(array(
			'width'       => 180,
			'editable'    => 'true',
			'edittype'    => "'text'",
			'editrules'   => '{required:true}'
		));

		$grid->showpager(true);
		$grid->setViewRecords(true);
		$grid->setWidth('250');
		$grid->setHeight('240');

		$grid->setUrlget(site_url('inventario/unidad/getdata/'));
		$grid->setUrlput(site_url('inventario/unidad/setdata/'));

		$mgrid = $grid->deploy();

		$msalida  = '<script type="text/javascript">'."\n";
		$msalida .= '
		$("#newapi'.$mgrid['gridname'].'").jqGrid({
			ajaxGridOptions : {type:"POST"}
			,jsonReader : { root:"data", repeatitems: false }
			'.$mgrid['table'].'
			,scroll: true
			,pgtext: null, pgbuttons: false, rowList:[]
		})
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'navGrid\',  "#pnewapi'.$mgrid['gridname'].'",{edit:false, add:false, del:true, search: false});
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'inlineNav\',"#pnewapi'.$mgrid['gridname'].'");
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'filterToolbar\');
		';

		$msalida .= "\n</script>\n";
		$msalida .= '<id class="anexos"><table id="newapi'.$mgrid['gridname'].'"></table>';
		$msalida .= '<div   id="pnewapi'.$mgrid['gridname'].'"></div></div>';

		echo $msalida;

	}


	//******************************************************************
	// Barras Adicionles
	//
	function barrasform( $id = 0 ){
		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('ID');
		$grid->params(array(
			'hidden'      => 'true',
			'align'       => "'center'",
			'width'       => 20,
			'editable'    => 'false',
			'editoptions' => '{readonly:true,size:10}'
		));

		$grid->addField('suplemen');
		$grid->label('Barra Adicional');
		$grid->params(array(
			'width'       => 150,
			'editable'    => 'true',
			'edittype'    => "'text'",
			'editrules'   => '{required:true}'
		));

		#show paginator
		$grid->showpager(true);
		$grid->setViewRecords(true);
		$grid->setWidth('200');
		$grid->setHeight('180');

		$grid->setUrlget(site_url('inventario/sinv/getbarras')."/$id");
		$grid->setUrlput(site_url('inventario/sinv/setbarras')."/$id");

		$mgrid = $grid->deploy();

		$msalida  = '<script type="text/javascript">'."\n";
		$msalida .= '
		$("#newapi'.$mgrid['gridname'].'").jqGrid({
			ajaxGridOptions : {type:"POST"}
			,jsonReader : { root:"data", repeatitems: false }
			'.$mgrid['table'].'
			,scroll: true
			,pgtext: null, pgbuttons: false, rowList:[]
		})
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'navGrid\',  "#pnewapi'.$mgrid['gridname'].'",{edit:false, add:false, del:true, search: false});
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'inlineNav\',"#pnewapi'.$mgrid['gridname'].'");
		';

		//$("#newapi'.$mgrid['gridname'].'").jqGrid(\'filterToolbar\');

		$msalida .= "\n</script>\n";
		$msalida .= '<id class="anexos"><table id="newapi'.$mgrid['gridname'].'"></table>';
		$msalida .= '<div   id="pnewapi'.$mgrid['gridname'].'"></div></div>';

		echo $msalida;

	}


	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function getbarras( $id = 0 ){
		$grid       = $this->jqdatagrid;

		if ( $id == 0 ){
			echo '';
			return;
		}
		$codigo = $this->datasis->dameval("SELECT codigo FROM sinv WHERE id=$id");
		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('barraspos');
		$mWHERE[] = array('', 'codigo', $codigo, '' );

		$response   = $grid->getData('barraspos', array(array()), array(), false, $mWHERE, 'suplemen' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}


	//******************************************************************
	// Guarda la Informacion
	//
	function setbarras( $mid = 0 ){
		$mid = $this->uri->segment($this->uri->total_segments());
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;
		$codigo = $this->datasis->dameval("SELECT codigo FROM sinv WHERE id=$mid");

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check  = $this->datasis->dameval("SELECT count(*) FROM barraspos WHERE suplemen=".$this->db->escape($data['suplemen']));
				$check += $this->datasis->dameval("SELECT count(*) FROM sinv      WHERE barras=".$this->db->escape($data['suplemen']));
				if ( $check == 0 ){
					$data['codigo'] = $codigo;
					$this->db->insert('barraspos', $data);
					logusu('BARRASPOS',"Barra '".$codigo.' => '.$data['suplemen']."' INCLUIDA");
					echo "Barra adicional agregada";
				} else
					echo "Ya existe un registro con esa barra";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$check = $this->datasis->dameval("SELECT count(*) FROM barraspos WHERE id <> $id AND suplemen=".$this->db->escape($data['suplemen']));
			$check += $this->datasis->dameval("SELECT count(*) FROM sinv     WHERE barras=".$this->db->escape($data['suplemen']));
			if ( $check == 0 ){
				$this->db->where("id", $id);
				$this->db->update('barraspos', $data);
				logusu('BARRASPOS',"Barra Adicional  ".$codigo.'=>'.$data['suplemen']." MODIFICADO");
				echo "Codigo $codigo => ".$data['suplemen']." Modificado";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'del') {
			$this->db->query("DELETE FROM barraspos WHERE id=$id ");
			logusu('BARRASPOS',"Barra adicional $id ELIMINADA");
			echo "Codigo Adicional Eliminado Eliminado";
		};
	}


	//******************************************************************
	// Forma para Principios activos
	//
	function pactivosform( $id = 0){
		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'hidden'      => 'true',
			'align'       => "'center'",
			'width'       => 20,
			'editable'    => 'false',
			'editoptions' => '{readonly:true,size:10}'
		));

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'width'       => 180,
			'editable'    => 'true',
			'edittype'    => "'text'",
			'editrules'   => '{required:true}'
		));

		#show paginator
		$grid->showpager(true);
		$grid->setViewRecords(true);
		$grid->setWidth('330');
		$grid->setHeight('280');

		$grid->setUrlget(site_url('inventario/sinv/pagetdata'));
		$grid->setUrlput(site_url('inventario/sinv/pasetdata'));

		$mgrid = $grid->deploy();

		$msalida  = '<script type="text/javascript">'."\n";
		$msalida .= '
		$("#newapi'.$mgrid['gridname'].'").jqGrid({
			ajaxGridOptions : {type:"POST"}
			,jsonReader : { root:"data", repeatitems: false }
			'.$mgrid['table'].'
			,scroll: true
			,pgtext: null, pgbuttons: false, rowList:[]
		})
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'navGrid\',  "#pnewapi'.$mgrid['gridname'].'",{edit:false, add:false, del:true, search: false});
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'inlineNav\',"#pnewapi'.$mgrid['gridname'].'");
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'filterToolbar\');
		';

		$msalida .= "\n</script>\n";
		$msalida .= '<id class="anexos"><table id="newapi'.$mgrid['gridname'].'"></table>';
		$msalida .= '<div   id="pnewapi'.$mgrid['gridname'].'"></div></div>';

		echo $msalida;

	}


	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function pagetdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('pactivo');

		$response   = $grid->getData('pactivo', array(array()), array(), false, $mWHERE, 'nombre' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}


	//******************************************************************
	// Guarda la Informacion
	//
	function pasetdata(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$mcodp  = 'nombre';
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM pactivo WHERE nombre=".$this->db->escape($data['nombre']));
				if ( $check == 0 ){
					$this->db->insert('pactivo', $data);
					echo "Principio Agregado";
					logusu('PACTIVO',"Registro '".$data['nombre']."' INCLUIDO");
				} else
					echo "Ya existe un registro con ese ${mcodp}";
			} else
				echo 'Fallo Agregado!!!';

		} elseif($oper == 'edit') {
			$nombre  = $data['nombre'];
			$this->db->where("id", $id);
			$this->db->update('pactivo', $data);
			logusu('PACTIVO',"Principio Activo  ".$nombre." MODIFICADO");
			echo "${nombre} Modificado";

		} elseif($oper == 'del') {
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sinv WHERE cpactivo='${id}'");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene inventario asociado";
			} else {
				$this->db->simple_query("DELETE FROM pactivo WHERE id=${id}");
				logusu('PACTIVO',"Registro ${id} ELIMINADO");
				echo 'Principio Activo Eliminado';
			}
		};
	}

	function actdesc(){
		$error=0;
		$ids=$this->input->post('ids');
		if(is_array($ids)){
			foreach($ids as $id){
				$id=intval($id);
				if($id>0){
					$mSQL="UPDATE sinv SET activo=IF(activo='S','N','S') WHERE id=${id}";
					$ban=$this->db->simple_query($mSQL);
					if(!$ban){ memowrite($mSQL,'sinv'); $error++; }
				}
			}
		}
		if($error>0){
			echo 'Hubo problemas en la operacion';
		}
	}

/*
	// Principios Activos
	function prinactivo() {
		$mid      = $this->input->post('id');
		$mpactivo = trim(rawurldecode($this->input->post('pactivo')));
		$mpaid    = trim($this->datasis->dameval("SELECT id FROM pactivo WHERE nombre=".$this->db->escape($mpactivo) ));

		$mcodigo  = trim($this->datasis->dameval("SELECT codigo FROM sinv WHERE id=$mid"));
		$htmlcod  = $this->db->escape($mcodigo);

		//Busca si ya esta
		$mSQL = "INSERT IGNORE INTO sinvpa SET codigo=".$htmlcod.", pactivo=$mpaid";
		$this->db->query($mSQL);
		logusu("SINV","Principio Activo Agregado".$mcodigo."-->".$mpactivo);
		echo "Registro de Codigo Exitoso";

	}
*/

	function instalar(){

		$campos = $this->db->list_fields('sinv');
		if (!in_array('id',$campos)){
			$mSQL='ALTER TABLE `sinv` DROP PRIMARY KEY';
			$this->db->simple_query($mSQL);
			$mSQL='ALTER TABLE `sinv` ADD UNIQUE `codigo` (`codigo`)';
			$this->db->simple_query($mSQL);
			$mSQL='ALTER TABLE sinv ADD id INT AUTO_INCREMENT PRIMARY KEY';
			$this->db->simple_query($mSQL);
		}

		if(!$this->datasis->iscampo('formatos','tcpdf') ) {
			$this->db->simple_query('ALTER TABLE formatos ADD COLUMN tcpdf TEXT NULL COMMENT "Formas TCPDF"');
		};

		if(!in_array('url',$campos)) {
			$this->db->simple_query('ALTER TABLE sinv ADD COLUMN url VARCHAR(200) NULL COMMENT "Pagina Web"');
		};

		if(!in_array('ficha',$campos)) {
			$this->db->simple_query('ALTER TABLE sinv ADD COLUMN ficha TEXT NULL COMMENT "Ficha Tecnica"');
		};

		if(!in_array('maxven',$campos)) {
			$this->db->simple_query("ALTER TABLE sinv ADD COLUMN `maxven` INT(10) NULL DEFAULT '0' COMMENT 'Maximo de venta', ADD COLUMN `minven` INT(10) NULL DEFAULT '0' COMMENT 'Minimo de venta' AFTER `maxven`");
		};

		if(!in_array('premin',$campos)) {
			$mSQL="ALTER TABLE sinv ADD COLUMN premin CHAR(1) NULL DEFAULT '0' COMMENT 'Precio Minimo de Venta' ";
			$this->db->query($mSQL);
		}

		if(!in_array('vnega',$campos)) {
			$mSQL="ALTER TABLE sinv ADD COLUMN vnega CHAR(1) NULL DEFAULT 'S' COMMENT 'Permitir Venta Negativa' ";
			$this->db->query($mSQL);
		}

		if ( $this->datasis->traevalor('SUNDECOP') == 'S') {
			if (!in_array('mpps',       $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `mpps`        VARCHAR(20) NULL  COMMENT 'Numero de Ministerior de Salud'");
			if (!in_array('cpe',        $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cpe`         VARCHAR(20) NULL  COMMENT 'Registro de CPE'");
			if (!in_array('dcomercial', $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `dcomercial`  INT(6)      NULL  COMMENT 'Destino Comercial'");
			if (!in_array('rubro',      $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `rubro`       INT(6)      NULL  COMMENT 'Rubro'");
			if (!in_array('subrubro',   $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `subrubro`    INT(6)      NULL  COMMENT 'Sub Rubro'");
			if (!in_array('cunidad',    $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cunidad`     INT(6)      NULL  COMMENT 'Unidad de Medida'");
			if (!in_array('cmarca',     $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cmarca`      INT(6)      NULL  COMMENT 'Marca'");
			if (!in_array('cmaterial',  $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cmaterial`   INT(6)      NULL  COMMENT 'Material'");
			if (!in_array('cpresenta',  $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cforma`      INT(6)      NULL  COMMENT 'Forma o Presentacion'");
			if (!in_array('cpactivo',   $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cpactivo`    INT(6)      NULL  COMMENT 'Principio Activo'");
		}

		// Arregla Otros Menus
		$this->db->query("UPDATE tmenus SET proteo='consulta'    WHERE modulo='SINVOTR' AND ejecutar LIKE 'SINVANAL%' ");
		$this->db->query("UPDATE tmenus SET proteo='recalcular'  WHERE modulo='SINVOTR' AND ejecutar LIKE 'RECALCU%' ");
		$this->db->query("UPDATE tmenus SET proteo='redondear'   WHERE modulo='SINVOTR' AND ejecutar LIKE 'REDOPRES%' ");
		$this->db->query("UPDATE tmenus SET proteo='sinvcodigo'  WHERE modulo='SINVOTR' AND ejecutar LIKE 'SINVCODIGO%' ");
		$this->db->query("UPDATE tmenus SET proteo='cambiaubica' WHERE modulo='SINVOTR' AND ejecutar LIKE 'CAMBIAUBICA%' ");
		$this->db->query("UPDATE tmenus SET proteo='auprec'      WHERE modulo='SINVOTR' AND ejecutar LIKE 'AUPREC%' ");
		$this->db->query("UPDATE tmenus SET proteo='verfotos'    WHERE modulo='SINVOTR' AND ejecutar LIKE 'SINVFOTO%' ");
		$this->db->query("UPDATE tmenus SET proteo='etiquetas'   WHERE modulo='SINVOTR' AND ejecutar LIKE 'SINVETIQ%' ");


		if ( $this->datasis->dameval('SELECT COUNT(*) FROM tmenus WHERE modulo="SINVOTR" AND proteo="cambiva" ') == 0 ){
			//crea elmodulo en tmenus
			$mSQL  = "INSERT INTO tmenus SET modulo='SINVOTR',  secu=17, titulo='Cambiar IVA', mensaje='Cambiar el IVA', ejecutar='', proteo='cambiva' ";
			$this->db->query($mSQL);
		}

		if ( !$this->datasis->istabla('sinvalub') ) {
			$mSQL = "CREATE TABLE sinvalub (
					codigo VARCHAR(15) NOT NULL DEFAULT '',
					alma   VARCHAR(4)  NOT NULL DEFAULT '',
					ubica  VARCHAR(12) NULL DEFAULT NULL,
					PRIMARY KEY (codigo, alma)
					)
					COLLATE='latin1_swedish_ci' ENGINE=MyISAM";
			$this->db->query($mSQL);
		}

		if ( !$this->datasis->istabla('sinvpa') ) {
			$mSQL = "CREATE TABLE sinvpa (
					codigo CHAR(15) NOT NULL DEFAULT '',
					pactivo INT(11) NOT NULL,
					id INT(11) NOT NULL AUTO_INCREMENT,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `codigo` (`codigo`, `pactivo`)
					)
					COLLATE='latin1_swedish_ci'
					ENGINE=MyISAM";
			$this->db->query($mSQL);
		}


		if ( !$this->datasis->istabla('pactivo') ) {
			$mSQL = "CREATE TABLE pactivo (
					id INT(11) NOT NULL AUTO_INCREMENT,
					nombre VARCHAR(250) NULL DEFAULT NULL,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `nombre` (`nombre`)
					)
					COMMENT='Principios Activos'
					COLLATE='latin1_swedish_ci'
					ENGINE=MyISAM";
			$this->db->query($mSQL);
		}

		$this->db->query("INSERT IGNORE INTO sinvalub SELECT a.codigo, b.ubica, a.ubica FROM sinv a JOIN caub b WHERE MID(a.tipo,1,1) <> 'S' AND b.gasto='N' ");

		if (!in_array('alto'       ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN alto          DECIMAL(10,2)");
		if (!in_array('ancho'      ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN ancho         DECIMAL(10,2)");
		if (!in_array('largo'      ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN largo         DECIMAL(10,2)");
		if (!in_array('forma'      ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN forma         VARCHAR(50)");
		if (!in_array('exento'     ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN exento        CHAR(1) DEFAULT 'N'");
		if (!in_array('mmargen'    ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN mmargen       DECIMAL(7,2)       DEFAULT 0 COMMENT 'Margen al Mayor'");
		if (!in_array('pm'         ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `pm`          DECIMAL(19,2) NULL DEFAULT '0.00' COMMENT 'porcentaje mayor'");
		if (!in_array('pmb'        ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `pmb`         DECIMAL(19,2) NULL DEFAULT '0.00' COMMENT 'porcentaje mayor'");
		if (!in_array('mmargenplus',$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `mmargenplus` DECIMAL(7,2)  NULL DEFAULT '0.00' COMMENT 'Margen al Mayor'");
		if (!in_array('escala1'    ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `escala1`     DECIMAL(12,2) NULL DEFAULT '0.00'");
		if (!in_array('pescala1'   ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `pescala1`    DECIMAL(5,2)  NULL DEFAULT '0.00' COMMENT 'porcentaje descuento escala1'");
		if (!in_array('escala2'    ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `escala2`     DECIMAL(12,2) NULL DEFAULT '0.00'");
		if (!in_array('pescala2'   ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `pescala2`    DECIMAL(5,2)  NULL DEFAULT '0.00' COMMENT 'porcentaje descuento escala2'");
		if (!in_array('escala3'    ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `escala3`     DECIMAL(12,2) NULL DEFAULT '0.00'");
		if (!in_array('pescala3'   ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `pescala3`    DECIMAL(5,2)  NULL DEFAULT '0.00' COMMENT 'porcentaje descuento escala3'");
		if (!in_array('mpps'       ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `mpps`        VARCHAR(20)   NULL  COMMENT 'Numero de Ministerior de Salud'");
		if (!in_array('cpe'        ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cpe`         VARCHAR(20)   NULL  COMMENT 'Registro de CPE'");
		if (!in_array('tasa'       ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cpe`         VARCHAR(20)   NULL  COMMENT 'Tasa asociada'");

		if (!in_array('linfe'      ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `linfe`       CHAR(1)       NULL DEFAULT 'N' ");
		if (!in_array('lindia'     ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `lindia`      INT(5)        NULL DEFAULT '0'");
		if (!in_array('margenu'    ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `margenu`     DECIMAL(10,2) NULL DEFAULT '0'");

		if ( $this->datasis->traevalor('SUNDECOP') == 'S') {
			if (!in_array('dcomercial', $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `dcomercial`  INT(6)     NULL  COMMENT 'Destino Comercial'");
			if (!in_array('rubro',      $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `rubro`       INT(6)     NULL  COMMENT 'Rubro'");
			if (!in_array('subrubro',   $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `subrubro`    INT(6)     NULL  COMMENT 'Sub Rubro'");
			if (!in_array('cunidad',    $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cunidad`     INT(6)     NULL  COMMENT 'Unidad de Medida'");
			if (!in_array('cmarca',     $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cmarca`      INT(6)     NULL  COMMENT 'Marca'");
			if (!in_array('cmaterial',  $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cmaterial`   INT(6)     NULL  COMMENT 'Material'");
			if (!in_array('cpresenta',  $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cforma`      INT(6)     NULL  COMMENT 'Forma o Presentacion'");
			if (!in_array('cpactivo',   $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cpactivo`    INT(6)     NULL  COMMENT 'Principio Activo'");
		}

		if(!$this->db->table_exists('sinvcombo')){
			$mSQL="CREATE TABLE `sinvcombo` (
				`id`       INT(11)   NOT NULL AUTO_INCREMENT,
				`combo`    CHAR(15)  NOT NULL,
				`codigo`   CHAR(15)  NOT NULL DEFAULT '',
				`descrip`  CHAR(30)      NULL DEFAULT NULL,
				`cantidad` DECIMAL(10,3) NULL DEFAULT NULL,
				`precio`   DECIMAL(15,2) NULL DEFAULT NULL,
				`transac`  CHAR(8)       NULL DEFAULT NULL,
				`estampa`  DATE          NULL DEFAULT NULL,
				`hora`     CHAR(8)       NULL DEFAULT NULL,
				`usuario`  CHAR(12)      NULL DEFAULT NULL,
				`costo`    DECIMAL(17,2) NULL DEFAULT '0.00',
				`ultimo`   DECIMAL(19,2) NULL DEFAULT '0.00',
				`pond`     DECIMAL(19,2) NULL DEFAULT '0.00',
				PRIMARY KEY (`id`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}


		$camposcomb = $this->db->list_fields('sinvcombo');
		if(!in_array('ultimo',$camposcomb)){
			$mSQL="ALTER TABLE `sinvcombo` ADD COLUMN `ultimo` DECIMAL(19,2) NULL DEFAULT '0.00' AFTER `costo`";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('pond',$camposcomb)){
			$mSQL="ALTER TABLE `sinvcombo` ADD COLUMN `pond` DECIMAL(19,2) NULL DEFAULT '0.00' AFTER `ultimo`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('sinvpitem')){
			$mSQL="CREATE TABLE `sinvpitem` (
				`producto` VARCHAR(15) NULL DEFAULT NULL COMMENT 'codigo del prod terminado (sinv)',
				`codigo` VARCHAR(15) NULL DEFAULT NULL COMMENT 'codigo del Insumo (sinv)',
				`descrip` VARCHAR(40) NULL DEFAULT NULL,
				`cantidad` DECIMAL(14,3) NULL DEFAULT '0.000',
				`merma` DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Porcentaje de merma',
				`estampa` DATE NULL DEFAULT NULL,
				`usuario` VARCHAR(12) NULL DEFAULT '',
				`hora` VARCHAR(8) NULL DEFAULT '',
				`modificado` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				`id_sinv` INT(11) NULL DEFAULT NULL,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`ultimo` DECIMAL(19,2) NOT NULL DEFAULT '0.00',
				`pond` DECIMAL(19,2) NOT NULL DEFAULT '0.00',
				`formcal` CHAR(1) NOT NULL,
				PRIMARY KEY (`id`),
				INDEX `modificado` (`modificado`)
			)
			COMMENT='Insumos de un producto terminado'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DYNAMIC
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		$camposcomb = $this->db->list_fields('sinvpitem');
		if(!in_array('unidad',$camposcomb)){
			$mSQL="ALTER TABLE sinvpitem ADD COLUMN unidad VARCHAR(12) NULL AFTER cantidad";
			$this->db->query($mSQL);
		}

		if(!in_array('factor',$camposcomb)){
			$mSQL="ALTER TABLE sinvpitem ADD COLUMN factor DECIMAL(12,4) NULL DEFAULT 1 AFTER unidad";
			$this->db->query($mSQL);
		}

/*
			$mSQL="ALTER TABLE sinvpitem
			ADD COLUMN unidad VARCHAR(12)   NULL AFTER cantidad,
			ADD COLUMN factor DECIMAL(12,4) NULL DEFAULT '1' AFTER unidad;";
*/

		if(!$this->db->table_exists('sinvplabor')){
			$mSQL="CREATE TABLE `sinvplabor` (
				`producto` VARCHAR(15) NULL DEFAULT '' COMMENT 'Producto Terminado',
				`estacion` VARCHAR(5) NULL DEFAULT NULL,
				`nombre` VARCHAR(40) NULL DEFAULT NULL,
				`actividad` VARCHAR(100) NOT NULL,
				`minutos` INT(6) NULL DEFAULT '0',
				`segundos` INT(6) NULL DEFAULT '0',
				`estampa` DATE NULL DEFAULT NULL,
				`usuario` VARCHAR(12) NULL DEFAULT '',
				`hora` VARCHAR(8) NULL DEFAULT '',
				`modificado` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				INDEX `modificado` (`modificado`)
			)
			COMMENT='Acciones de la Orden de Produccion'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		if ($this->db->field_exists('minutos', 'sinvplabor')){
			$mSQL="ALTER TABLE `sinvplabor`
			ADD COLUMN `tiempo` DECIMAL(10,2) NULL DEFAULT '0' AFTER `actividad`,
			ADD COLUMN `tunidad` CHAR(1) NULL DEFAULT 'H' COMMENT 'Unidad de tiempo Horas Dias Semanas' AFTER `tiempo`,
			DROP COLUMN `minutos`,
			DROP COLUMN `segundos`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('esta')){
			$mSQL="CREATE TABLE `esta` (
				`estacion` VARCHAR(5) NOT NULL DEFAULT '',
				`nombre` VARCHAR(30) NULL DEFAULT NULL,
				`descrip` TEXT NULL,
				`ubica` TEXT NULL,
				`jefe` VARCHAR(5) NULL DEFAULT NULL COMMENT 'tecnico',
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `vendedor` (`estacion`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('sinvprov')){
			$mSQL="
			CREATE TABLE sinvprov (proveed CHAR(5) NOT NULL DEFAULT '',
				codigop CHAR(15) NOT NULL DEFAULT '', codigo CHAR(15) NOT NULL DEFAULT '',
				PRIMARY KEY (`proveed`, `codigop`, `codigo`))
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
		}

		if(!$this->db->table_exists('barraspos')){
			$query="
			CREATE TABLE `barraspos` (
				`codigo` CHAR(15) NOT NULL DEFAULT '',
				`suplemen` CHAR(15) NOT NULL DEFAULT '',
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `codigo` (`codigo`, `suplemen`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM;
			";
			$this->db->query($query);
		}

		if(!$this->datasis->iscampo('barraspos','id') ){
			$this->db->query('ALTER TABLE barraspos DROP PRIMARY KEY');
			$this->db->query('ALTER TABLE barraspos ADD UNIQUE INDEX codigo (codigo, suplemen)');
			$this->db->query('ALTER TABLE barraspos ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};

		if(!$this->db->table_exists('invfelr')){
			$query="CREATE TABLE `invfelr` (
				`codigo` CHAR(15) NOT NULL DEFAULT '',
				`fecha` DATE NOT NULL DEFAULT '0000-00-00',
				`precio` DECIMAL(17,2) NOT NULL DEFAULT '0.00',
				`existen` DECIMAL(17,2) NULL DEFAULT NULL,
				`anterior` DECIMAL(17,2) NULL DEFAULT NULL,
				`parcial` DECIMAL(17,2) NULL DEFAULT NULL,
				`alma` CHAR(4) NOT NULL DEFAULT '',
				`tipo` CHAR(1) NULL DEFAULT NULL,
				`fhora` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
				`usuario` CHAR(12) NULL DEFAULT NULL,
				`ubica` CHAR(10) NOT NULL DEFAULT ''
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			$this->db->simple_query($query);
		}

		if(!$this->db->table_exists('sinvlote')){
			$mSQL="CREATE TABLE sinvlote (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`codigo` VARCHAR(15) NULL,
				`cantidad` DECIMAL(10,2) NULL,
				`precio` DECIMAL(12,2) NULL,
				`costo` DECIMAL(12,2) NULL,
				`vence` DATE NULL,
				`estampa` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				INDEX `codigo` (`codigo`),
				UNIQUE INDEX `codigo_costo_vence` (`codigo`, `costo`, `vence`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
		}


	}
}
