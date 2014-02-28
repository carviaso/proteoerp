<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
require_once(APPPATH.'/controllers/finanzas/common.php');

class Lpago extends Controller {
	var $mModulo = 'LPAGO';
	var $titp    = 'Modulo de pagos';
	var $tits    = 'Modulo de pagos';
	var $url     = 'leche/lpago/';
	var $table   = 'lpago';

	function Lpago(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'LPAGO', $ventana=0 );
		$this->genesal=true;
	}

	function index(){
		$this->instalar();

		//Arregla los totales de la recepcion
		$mSQL = 'UPDATE lrece a SET lista=(SELECT SUM(lista) FROM itlrece b WHERE a.id=b.id_lrece );';
		$this->db->query($mSQL);
		$mSQL = 'UPDATE lrece SET litros=lleno, neto=lleno, diferen=lleno-lista WHERE vacio=0 AND lleno>0;';
		$this->db->query($mSQL);
		$mSQL = 'UPDATE lrece SET lista=TRUNCATE(lista,0);';
		$this->db->query($mSQL);
		$mSQL = 'UPDATE lrece SET litros=TRUNCATE(ROUND((lleno-vacio)/densidad,2),0), neto=lleno-vacio, diferen=ROUND((lleno-vacio)/densidad,2)-lista
		WHERE vacio>0 AND lleno>0;';
		$this->db->query($mSQL);
		$mSQL = 'UPDATE lrece SET diferen=litros-lista;';
		$this->db->query($mSQL);
		//Fin del arreglo de los totales de la recepcion

		$this->datasis->creaintramenu(array('modulo'=>'227','titulo'=>'Pagos de Producción','mensaje'=>'Pagos de Producción','panel'=>'LECHE','ejecutar'=>'leche/lpago','target'=>'popu','visible'=>'S','pertenece'=>'2','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	// Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'bimpri'    ,'img'=>'assets/default/images/print.png', 'alt' => 'Imprimir Documento','label'=>'Imprimir recibo'    ));
		$grid->wbotonadd(array('id'=>'bimprilote','img'=>'assets/default/images/print.png', 'alt' => 'Imprimir lote'     ,'label'=>'Imprimir x Lote'    ));
		$grid->wbotonadd(array('id'=>'bcheque'   ,'img'=>'assets/default/images/print.png', 'alt' => 'Imprimir Cheque'   ,'label'=>'Imprimir cheque'    ));
		$grid->wbotonadd(array('id'=>'blote'     ,'img'=>'images/agrega4.png'             , 'alt' => 'Pagar lote'        ,'label'=>'Pagar lote'         ));
		$grid->wbotonadd(array('id'=>'bimpriau'  ,'img'=>'assets/default/images/print.png', 'alt' => 'Imprimir Auditoria','label'=>'Imprimir Auditoria' ));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita', 'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' , 'title'=>'Ver Registro'),
			array('id'=>'flote' , 'title'=>'Pago por Lote'),
			array('id'=>'fborra', 'title'=>'Elimina registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('LPAGO', 'JQ');
		$param['otros']       = $this->datasis->otros('LPAGO', 'JQ');
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
		function lpagoshow() {
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
		function lpagoadd() {
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			});
		};';

		$bodyscript .= '
		function lpagoedit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		};';

		$bodyscript .= '
		function lpagodel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(r){
						try{
							var json = JSON.parse(r);
							if (json.status == "A"){
								apprise("Registro Eliminado");
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
								return true;
							} else {
								apprise(json.mensaje);
							}
						}catch(e){
							$("#fborra").html(r);
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
		jQuery("#blote").click( function(){
			$.post("'.site_url($this->url.'lote').'"+"/create",
			function(data){
				$("#flote").html(data);
				$("#flote").dialog( "open" );
			});
		});';

		$bodyscript .= '
		jQuery("#bimpri").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('formatos/ver/LPAGO').'/\'+id+\'/id\'').';
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		});';

		$bodyscript .= '
		jQuery("#bimprilote").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				if(ret.id_lpagolote!=""){
					'.$this->datasis->jwinopen(site_url($this->url.'limplote').'/\'+ret.id_lpagolote+\'\'').';
				}else{
					$.prompt("<h1>El pago seleccionado no fue en lote</h1>");
				}
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		});';

		$bodyscript .= '
		jQuery("#bimpriau").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('formatos/ver/LPAGOAUD').'/\'+id+\'/id\'').';
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		});';

		$bodyscript .= '
		jQuery("#bcheque").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url($this->url.'impcheque').'/\'+id+\'/id\'').';
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		});';

		$bodyscript .= '
		$("#fshow").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
			buttons: {
				"Aceptar": function() {
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				$("#fshow").html("");
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/LPAGO').'/\'+json.pk.id+\'/id\'').';
									return true;
								} else {
									apprise(json.mensaje);
								}
							}catch(e){
								$("#fedita").html(r);
							}
						}
					});
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
		$("#fborra").dialog({
			autoOpen: false, height: 300, width: 300, modal: true,
			buttons: {
				"Aceptar": function() {
					$( this ).dialog( "close" );
					grid.trigger("reloadGrid");
				}
			},
			close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});';

		$bodyscript .= '
		$("#flote").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
			buttons: {
				"Guardar": function() {
					$(":button:contains(\'Guardar\')").button("disable");
					var bValid = true;
					var murl = $("#df1").attr("action");
					allFields.removeClass( "ui-state-error" );
					var totallote = Number($("#totallote").val());

					if(totallote>0){
						$.ajax({
							type: "POST", dataType: "html", async: false,
							url: murl,
							data: $("#df1").serialize(),
							success: function(r,s,x){
								try{
									var json = JSON.parse(r);
									if (json.status == "A"){
										apprise(json.mensaje);
										$( "#flote" ).dialog( "close" );
										'.$this->datasis->jwinopen(site_url('reportes/ver/LPAGOLOTE').'/\'+json.pk.id+\'/id\'').';
										grid.trigger("reloadGrid");
										return true;
									} else {
										apprise(json.mensaje);
									}
								}catch(e){
									$("#flote").html(r);
								}
								$(":button:contains(\'Guardar\')").button("enable");
							}
						});
					}else{
						alert("El monto del pago por lote debe ser mayor a cero");
						$(":button:contains(\'Guardar\')").button("enable");
					}
				},
				"Cerrar": function() {
					$(":button:contains(\'Guardar\')").button("enable");
					$("#flote").html("");
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				$(":button:contains(\'Guardar\')").button("enable");
				$("#flote").html("");
				allFields.val("").removeClass( "ui-state-error" );
			}
		});';

		$bodyscript .= '});'."\n";

		$bodyscript .= "</script>";

		return $bodyscript;
	}

	//***********************************************
	//   Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		/*$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));*/


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


		$grid->addField('proveed');
		$grid->label('Proveed');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'text'",
			'formatoptions' => '{size:10, maxlength: 5 }'
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->addField('banco');
		$grid->label('Banco');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('numche');
		$grid->label('N.Cheque');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         =>50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 100 }',
		));


		$grid->addField('benefi');
		$grid->label('Beneficiario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 100 }',
		));


		$grid->addField('monto');
		$grid->label('Monto');
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


		$grid->addField('deduc');
		$grid->label('Deducciones');
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


		$grid->addField('montopago');
		$grid->label('Monto final');
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



		$grid->addField('id_lpagolote');
		$grid->label('Lote');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100
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
		$grid->setEdit(false);
		$grid->setAdd(    $this->datasis->sidapuede('LPAGO','INCLUIR%' ));
		$grid->setDelete( $this->datasis->sidapuede('LPAGO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('LPAGO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: lpagoadd,editfunc: lpagoedit,delfunc: lpagodel,viewfunc: lpagoshow');

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
		$mWHERE = $grid->geneTopWhere('lpago');

		$response   = $grid->getData('lpago', array(array()), array(), false, $mWHERE, 'id', 'desc' );
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
/*
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM lpago WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('lpago', $data);
					echo "Registro Agregado";

					logusu('LPAGO',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";
*/

		} elseif($oper == 'edit') {
			$numero   = $data['numero'];
			$anterior = $this->datasis->dameval("SELECT numero FROM lpago WHERE id=$id");
			unset($data[$mcodp]);
			$this->db->where("id", $id);
			$this->db->update('lpago', $data);
			logusu('LPAGO',"Pago de Leche  ".$numero." MODIFICADO");
			echo "Pago $mcodp Modificado";

		} elseif($oper == 'del') {
/*
			$meco = $this->datasis->dameval("SELECT $mcodp FROM lpago WHERE id=$id");
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM lpago WHERE id='$id' ");

			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM lpago WHERE id=$id ");
				logusu('LPAGO',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
*/

		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$script="
		$(function(){
			$('.inputnum').numeric('.');
			$('#fecha').datepicker({   dateFormat: 'dd/mm/yy' });
			$('#proveed').autocomplete({
				delay: 600,
				autoFocus: true,
				source: function( req, add){
					$.ajax({
						url:  '".site_url('ajax/buscasprv')."',
						type: 'POST',
						dataType: 'json',
						data: 'q='+req.term,
						success:
							function(data){
								if(data.length==0){
									$('#nombre').val('');
									$('#nombre_val').text('');
									$('#proveed').val('');
								}else{
									var sugiere = [];
									$.each(data,
										function(i, val){
											sugiere.push( val );
										}
									);
									add(sugiere);
								}
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$('#proveed').attr('readonly', 'readonly');
					$('#nombre').val(ui.item.nombre);
					$('#nombre_val').text(ui.item.nombre);
					$('#proveed').val(ui.item.proveed);
					setTimeout(function() { $('#proveed').removeAttr('readonly'); }, 1500);

					$.ajax({
						url:  '".site_url($this->url.'ajaxmonto')."',
						type: 'POST',
						dataType: 'json',
						data: 'proveed='+ui.item.proveed,
						success:
							function(ddata){
								var monto = roundNumber(ddata.monto+ddata.tmonto,2);
								var diff  = roundNumber(monto-ddata.deduc,2);
								$('#monto').val(monto);
								$('#deduc').val(ddata.deduc);

								$('#montopago').val(diff);
								$('#montopago_val').text(nformat(diff,2));
							},
					});
				}
			});
		});

		function totaliza(){
			var monto = Number($('#monto').val());
			var deduc = Number($('#deduc').val());
			var diff  = roundNumber(monto-deduc,2);

			$('#montopago').val(diff);
			$('#montopago_val').text(nformat(diff,2));
		}
		";

		$edit = new DataEdit($this->tits, 'lpago');
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('update','_pre_update');
		$edit->pre_process('delete','_pre_delete');

		$edit->proveed = new inputField('Proveedor','proveed');
		$edit->proveed->rule='max_length[5]|required';
		$edit->proveed->size =7;

		/*$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule='max_length[8]';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;*/

		/*$edit->tipo = new inputField('Tipo','tipo');
		$edit->tipo->rule='max_length[1]';
		$edit->tipo->size =3;
		$edit->tipo->maxlength =1;*/

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->insertValue=date('Y-m-d');
		$edit->fecha->calendar=false;
		$edit->fecha->maxlength =8;

		$edit->nombre = new inputField('','nombre');
		$edit->nombre->rule='max_length[100]';
		$edit->nombre->type='inputhidden';
		$edit->nombre->in  ='proveed';

		$edit->banco = new dropdownField('Pagar desde','banco');
		$edit->banco->option('','Seleccionar');
		$edit->banco->options("SELECT codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE activo='S' AND tipocta<>'Q' ORDER BY codbanc");
		//$edit->banco->onchange='desactivacampo(this.value)';
		$edit->banco->rule='max_length[50]|required';
		$edit->banco->group='Detalles de pago';

		$edit->numche = new inputField('N&uacute;mero','numche');
		$edit->numche->rule='max_length[100]';
		$edit->numche->rule='condi_required|callback_chobligaban';
		$edit->numche->size =52;
		$edit->numche->maxlength =100;
		$edit->numche->group='Detalles de pago';
		//$edit->numche->append('Aplica si repone desde un Banco');

		$edit->benefi = new inputField('Beneficiario','benefi');
		$edit->benefi->rule='max_length[100]|strtoupper';
		$edit->benefi->size =52;
		$edit->benefi->maxlength =100;
		$edit->benefi->group='Detalles de pago';

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[12]|numeric|mayorcero';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =14;
		$edit->monto->onkeyup='totaliza()';
		$edit->monto->maxlength =12;

		$edit->deduc = new inputField('Deducciones','deduc');
		$edit->deduc->rule='max_length[12]|numeric';
		$edit->deduc->css_class='inputnum';
		$edit->deduc->onkeyup='totaliza()';
		$edit->deduc->size =14;
		$edit->deduc->maxlength =12;

		$edit->montopago = new inputField('Monto del pago','montopago');
		$edit->montopago->rule='max_length[12]|numeric|mayorcero';
		$edit->montopago->css_class='inputnum';
		$edit->montopago->size =14;
		$edit->montopago->maxlength =12;

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			if($this->genesal){
				echo $edit->output;
			}else{
				$rt=array(
					'status' =>'B',
					'mensaje'=>html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string)),
					'pk'     =>null
				);
				echo json_encode($rt);
			}
		}
	}

	function lote(){
		$this->rapyd->load('dataedit');

		$script= '
		$(function() {
			$(".inputnum").numeric(".");
			$(".inputonlynum").numeric();

			jQuery("#loteresu").jqGrid({
				datatype: "local",
				height: 230,
				colNames:["Cod.","Nombre", "Monto", "Adic. y Dedu.","Total pago"],
				colModel:[
					{name:"proveed"  , index:"proveed"   , width:60  },
					{name:"nombre"   , index:"nombre"    , width:240 },
					{name:"monto"    , index:"monto"     , width:110 , align:"right" , sorttype:"float"},
					{name:"deduc"    , index:"deduc"     , width:90  , align:"right" , sorttype:"float"},
					{name:"montopago", index:"montopago" , width:110 , align:"right" , sorttype:"float"},
				],
				multiselect: false,
				caption: "Resumen de pago en lote",
				rowNum:9000000000
			});
		});

		function llenaresu(){
			jQuery("#loteresu").jqGrid("clearGridData",true).trigger("reloadGrid");

			var total   = 0;
			var tipo    = $("#tipo").val();
			var enbanco = $("#banco").val();

			if(tipo!="" && enbanco!=""){
				$.post("'.site_url($this->url.'resumenlote').'",{ enbanco: enbanco, tipo: tipo },
				function(data){
					var jjson = JSON.parse(data);
					for(var i=0;i<jjson.length;i++){
						total = total+jjson[i].montopago;
						jQuery("#loteresu").jqGrid("addRowData",i+1,jjson[i]);
					}
					//alert(total);
					$("#totalval").text(nformat(total));
					$("#totallote").val(total);
				});
			}
		}';

		$edit = new DataEdit('', 'lpagolote');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_lote_insert');
		$edit->post_process('update','_post_lote_update');
		$edit->post_process('delete','_post_lote_delete');
		$edit->pre_process( 'insert', '_pre_lote_insert');
		$edit->pre_process( 'update', '_pre_lote_update');
		$edit->pre_process( 'delete', '_pre_lote_delete');

		$edit->enbanco = new dropdownField('Pagar con banco','enbanco');
		$edit->enbanco->option('','Seleccionar');
		$edit->enbanco->options("SELECT codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE activo='S' AND tipocta<>'Q' AND tbanco<>'CAJ' ORDER BY codbanc");
		$edit->enbanco->rule='max_length[50]|required';
		$edit->enbanco->style='width:200px;';
		$edit->enbanco->append('Banco desde el que se emiten los pagos');

		$edit->tipo = new dropdownField('Preferencia de pago','tipo');
		$edit->tipo->option('T','Transferencia');
		$edit->tipo->option('D','Deposito');
		$edit->tipo->onchange='llenaresu()';
		$edit->tipo->rule = 'required';
		$edit->tipo->style = 'width:140px;';

		$edit->banco = new dropdownField('Banco a depositar','banco');
		$edit->banco->option('','Seleccionar');
		$edit->banco->options('SELECT cod_banc, CONCAT_WS(\'-\',cod_banc,nomb_banc) AS label FROM tban WHERE cod_banc<>"CAJ" ORDER BY cod_banc');
		$edit->banco->rule='max_length[5]|required';
		$edit->banco->onchange='llenaresu()';
		$edit->banco->style='width:200px;';
		$edit->banco->append('Banco en donde se le depositar&aacute;n a los clientes');

		$edit->numero = new inputField('Cheque Num/Benefi','numero');
		$edit->numero->rule='max_length[50]';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;

		$edit->benefi = new inputField('Beneficiario','benefi');
		$edit->benefi->rule='max_length[100]';
		$edit->benefi->in = 'numero';
		$edit->benefi->maxlength =100;

		$edit->totalval = new freeField('Total','','<b id="totalval">0,00</b><input type="hidden" name="totallote" id="totallote" value="0"> ');

		$edit->container = new containerField('alert','<table id="loteresu"></table>');
		$edit->container->when = array('create');

		//$edit->numero = new inputField('N&uacute;mero','numero');
		//$edit->numero->rule='max_length[100]';
		//$edit->numero->rule='condi_required|callback_chobligaban';
		//$edit->numero->maxlength =100;
		//$edit->numero->group='Detalles de pago';
        //
		//$edit->banco = new dropdownField('Pagar desde','banco');
		//$edit->banco->option('','Seleccionar');
		//$edit->banco->options("SELECT codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE activo='S' AND tipocta<>'Q' ORDER BY codbanc");
		//$edit->banco->rule='max_length[50]|required';
		//$edit->banco->group='Detalles de pago';
        //
		//$edit->benefi = new inputField('Beneficiario','benefi');
		//$edit->benefi->rule='max_length[100]';
		//$edit->benefi->maxlength =100;

		//$edit->fecha = new dateField('Fecha','fecha');
		//$edit->fecha->rule='chfecha';
		//$edit->fecha->size =10;
		//$edit->fecha->calendar=false;
		//$edit->fecha->maxlength =8;
		//$edit->monto = new inputField('Monto','monto');
		//$edit->monto->rule='max_length[12]|numeric';
		//$edit->monto->css_class='inputnum';
		//$edit->monto->size =14;
		//$edit->monto->maxlength =12;
		//$edit->buttons('modify', 'save', 'undo', 'delete', 'back');

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>empty($edit->_dataobject->comment)? 'Registro guardado':$edit->_dataobject->comment,
				'pk'     =>$edit->_dataobject->pk
			);

			echo json_encode($rt);
		}else{
			echo $edit->output;
		}
	}

	function chobligaban($val){
		$banc=$this->input->post('banco');
		$sql='SELECT tbanco FROM banc WHERE codbanc='.$this->db->escape($banc);
		$tipo=$this->datasis->dameval($sql);

		if($tipo=='CAJ'){
			return true;
		}elseif(empty($val)){
			$this->validation->set_message('chobligaban', 'El campo %s se necesario cuando se paga por banco.');
			return false;
		}else{
			return true;
		}
	}

	function resumenlote(){

		$bbanco = $this->input->post('enbanco');
		$tipo   = $this->input->post('tipo');
		if($bbanco!==false && $tipo!==false){
			$arr=array();
			$this->db->select(array('proveed','nombre'));
			$this->db->from('sprv');
			$this->db->where('banco1'  ,$bbanco );
			$this->db->where('prefpago',$tipo );
			$this->db->orderby('nombre');
			$query = $this->db->get();
			//echo $this->db->last_query();
			foreach ($query->result() as $row){
				$rt   = $this->_cmonto($row->proveed);
				$monto= round($rt['monto']+$rt['tmonto'],2);

				if($monto>0){
					$arr[]=array(
						'proveed'   => utf8_encode($row->proveed),
						'nombre'    => utf8_encode($row->nombre),
						'deduc'     => $rt['deduc'],
						'monto'     => $monto,
						'montopago' => $monto-$rt['deduc']
					);
				}
			}

			echo json_encode($arr);
		}
	}

	function impcheque($id_gser){
		$dbid=$this->db->escape($id_gser);
		$fila=$this->datasis->damerow('SELECT a.banco,a.benefi,a.nombre,a.montopago AS monto,a.id_lpagolote FROM lpago AS a WHERE a.id='.$dbid);

		if(empty($fila['id_lpagolote'])){
			$fila['benefi']= trim($fila['benefi']);
			$fila['nombre']= trim($fila['nombre']);

			$banco  = Common::_traetipo($fila['banco']);

			if($banco!='CAJ'){
				$this->load->library('cheques');
				$nombre = (empty($fila['benefi']))? $fila['nombre']: $fila['benefi'];
				$monto  = $fila['monto'];
				$fecha  = date('Y-m-d');
				$banco  = $banco;
				$this->cheques->genera($nombre,$monto,$banco,$fecha,true);
			}else{
				echo 'Egreso no fue pagado con cheque de banco';
			}
		}else{
			$fila=$this->datasis->damerow('SELECT enbanco AS banco,benefi,monto,tipo FROM lpagolote AS a WHERE a.id='.$fila['id_lpagolote']);
			$fila['benefi']= trim($fila['benefi']);
			//$banco  = $fila['banco'];
			$tipo  = $fila['tipo'];

			$banco  = Common::_traetipo($fila['banco']);
			if($banco!='CAJ' && $tipo=='D'){
				$this->load->library('cheques');
				$nombre = $fila['benefi'];
				$monto  = $fila['monto'];
				$fecha  = date('Y-m-d');
				$banco  = $banco;
				$this->cheques->genera($nombre,$monto,$banco,$fecha,true);
			}else{
				echo 'Egreso no fue pagado con cheque de banco';
			}
		}
	}

	//Hace la consulta para el pago por productores
	function _sqlprod($idlprecio,$proveed,$fcorte,$sum=true){
		if($sum){
			$ssum='SUM';
		}else{
			$ssum='';
		}

		$sel=array($ssum.'(ROUND(a.lista*if(c.tipolec="F",if(c.animal="V",if(c.zona="0112",e.tarifa5,e.tarifa1),e.tarifa3), if(c.animal="V",e.tarifa2,e.tarifa4)),2)) AS monto');
		$this->db->select($sel);
		$this->db->from('itlrece AS a');
		$this->db->join('lrece   AS b','a.id_lrece=b.id');
		$this->db->join('lvaca   AS c','a.id_lvaca=c.id');
		$this->db->join('sprv    AS d','c.codprv=d.proveed','left');
		$this->db->join('lprecio AS e','e.id='.$this->db->escape($idlprecio));
		$this->db->where('a.lista >','0');
		$this->db->where('MID(b.ruta,1,1) <>','G');
		//$this->db->where("((b.fecha<='$fcorte' AND b.transporte<=0) OR (b.fecha<=ADDDATE('$fcorte',INTERVAL 1 DAY)  AND b.transporte>0))");
		$this->db->where("b.fechar <= '$fcorte'");
		$this->db->where('(a.pago IS NULL OR a.pago=0)');
		$this->db->where('c.codprv',$proveed);
	}

	//Hace la consulta para el pago de los transportistas
	function _sqltran($idlprecio,$proveed,$fcorte,$sum=true){

		if($sum){
			$ssum='SUM';
		}else{
			$ssum='';
		}

		$sel=array($ssum.'(ROUND(a.lista*b.tarifa,2)+ROUND((litros-lista)*b.tarsob,2)) AS monto');
		$this->db->select($sel);
		$this->db->from('lrece AS a');
		$this->db->join('lruta AS b','a.ruta=b.codigo');
		$this->db->join('sprv  AS c','b.codprv=c.proveed');
		$this->db->where('a.lista >',0);
		$this->db->where('(a.pago IS NULL OR a.pago=0)');
		$this->db->where('MID(a.ruta,1,1) <>','G');
		//$this->db->where("((a.fecha<='$fcorte' AND a.transporte<=0) OR (a.fecha<=ADDDATE('$fcorte',INTERVAL 1 DAY)  AND a.transporte>0))");
		//$this->db->where("LEAST(a.fechal,a.fecha) <= '$fcorte'");
		$this->db->where("a.fechar <= '$fcorte'");

		$this->db->where('b.codprv',$proveed);
	}

	function _cmonto($proveed=null){

		$rt = array('deduc'=>0 , 'tmonto'=>0 , 'monto'=>0);
		if(!empty($proveed)){
			$this->db->_escape_char='';
			$this->db->_protect_identifiers=false;
			$fcorte   = date('Y-m-d',mktime(0, 0, 0, date('n'),date('j')-1*date('w')));
			$dbfcorte = $this->db->escape($fcorte);

			//Deducciones
			$sel=array('SUM(a.total*IF(a.tipo="A",-1,1)) AS val');
			$this->db->select($sel);
			$this->db->from('lgasto AS a');
			$this->db->where('(a.pago IS NULL OR a.pago=0)');
			$this->db->where('a.proveed',$proveed);
			$this->db->where('a.fecha <=',$fcorte);
			$query = $this->db->get();
			if ($query->num_rows() > 0){
				$row = $query->row();
				if(!empty($row->val)) $rt['deduc'] = round(floatval($row->val),2);
			}

			$idlprecio = $this->datasis->dameval("SELECT id FROM lprecio WHERE fecha <= $dbfcorte ORDER BY fecha DESC LIMIT 1");

			//Productores
			$this->_sqlprod($idlprecio,$proveed,$fcorte);
			$query = $this->db->get();
			if($query->num_rows() > 0){
				$row = $query->row();
				if(!empty($row->monto)) $rt['monto']  = round(floatval($row->monto),2);
			}

			//echo $this->db->last_query();
			//Transportista
			$this->_sqltran($idlprecio,$proveed,$fcorte);
			$query = $this->db->get();
			if ($query->num_rows() > 0){
				$row = $query->row();
				if(!empty($row->monto)) $rt['tmonto'] = round(floatval($row->monto),2);
			}
			//echo $this->db->last_query();
		}
		return $rt;
	}

	function limplote($idlote){
		$query = $this->db->query('SELECT id FROM lpago WHERE id_lpagolote='.$this->db->escape($idlote));
		$imp = '';
		foreach ($query->result() as $row){
			$id = $row->id;
			$url=$this->_direccion='http://localhost/'.site_url('formatos/verhtmllocal/LPAGO/'.$id);
			$data = file_get_contents($url);

			if(preg_match('/<!\-\-\@size_paper (?P<x>[0-9\.]+)x(?P<y>[0-9\.]+)\-\->/', $data, $matches)){
				$x = $matches['x'];
				$y = $matches['y'];
				$papersize = '<!--@size_paper '.$x.'x'.$y.'-->';
			}else{
				$papersize = '';
			}

			if(preg_match('/<body(?P<bodyparr>[^>]+)>/', $data, $matches)){
				$bodyparr = $matches['bodyparr'];
			}else{
				$bodyparr = '';
			}

			$inicio= stripos($data, '<body');
			$fin   = stripos($data, '</body>');
			$encab = substr($data,0,$inicio);
			$piepa = substr($data,$fin);

			$data  = substr($data,0,$fin);
			$data  = substr($data,$inicio);

			$data = preg_replace('/<.*body[^>]*>/', '', $data);
			$data = preg_replace('/<!--[^>]*-->/' , '', $data);

			$imp .= $data.'<div style="page-break-before: always;"></div>';
		}
		$imp = $encab."\n<body ${bodyparr}>\n".$papersize.$imp.$piepa;

		$this->load->library('dompdf/cidompdf');
		$this->cidompdf->html2pdf($imp,'lote'.$idlote.'.pdf',true);
	}

	function ajaxmonto(){
		$proveed=$this->input->post('proveed');

		if($proveed!==false){
			$rt=$this->_cmonto($proveed);
			echo json_encode($rt);
		}else{
			echo '[]';
		}
	}

	function _pre_insert($do){
		$numero=$this->datasis->fprox_numero('nlpago');
		$do->set('numero',$numero);
		$proveed=$do->get('proveed');
		$benefi =$do->get('benefi');

		if(empty($benefi)){
			$nombre = $this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$this->db->escape($proveed));
			$do->set('benefi',$nombre);
		}

		if(isset($this->id_lote)){

			$do->set('id_lpagolote',$this->id_lote);
		}

		$fcorte=date('Y-m-d',mktime(0, 0, 0, date('n'),date('j')-1*date('w')));
		$this->fcorte=$fcorte;

		//Calcula el moto que se le debe
		$rt=array('deduc'=>0,'monto'=>0,'tmonto'=>0);

		$montos = $this->_cmonto($proveed);
		if(isset($montos['monto']) && isset($montos['tmonto']) && isset($montos['deduc'])){
			$rt=array(
				'deduc' => $montos['deduc'],
				'monto' => $montos['monto'],
				'tmonto'=> $montos['tmonto']
			);
		}
		//Fin del calculo

		//Determina el tipo, si es transportista, productor o ambos
		if($rt['tmonto']*$rt['monto']>0){
			$do->set('tipo','A');
		}elseif($rt['tmonto']>0){
			$do->set('tipo','T');
		}else{
			$do->set('tipo','P');
		}
		//Fin del tipo

		return true;
	}

	function _pre_update($do){
		return false;
	}

	function _pre_delete($do){
		return true;
	}

	function _post_insert($do){
		$tipo     = $do->get('tipo');
		$proveed  = $do->get('proveed');
		$id       = $do->get('id');
		$dbid     = $this->db->escape($id);
		$dbproveed= $this->db->escape($proveed);
		$fcorte   = $this->fcorte;
		$dbfcorte = $this->db->escape($fcorte);
		$idlprecio= $this->datasis->dameval("SELECT id FROM lprecio WHERE fecha <= $dbfcorte ORDER BY fecha DESC LIMIT 1");

		//Marca los pagos por transporte
		if($tipo=='T' || $tipo=='A'){

			$this->db->select('a.id');
			$this->_sqltran($idlprecio,$proveed,$fcorte,false);
			$query = $this->db->get();
			if($query->num_rows() > 0){
				foreach ($query->result() as $row){
					$mSQL='UPDATE lrece SET montopago='.$row->monto.', pago='.$dbid.' WHERE id='.$row->id;
					$this->db->simple_query($mSQL);
				}
			}
		}

		//Marca los pagos por productor
		if($tipo=='P' || $tipo=='A'){

			$this->db->select('a.id');
			$this->_sqlprod($idlprecio,$proveed,$fcorte,false);
			$query = $this->db->get();
			if($query->num_rows() > 0){
				foreach ($query->result() as $row){
					$mSQL='UPDATE itlrece SET montopago='.$row->monto.', pago='.$dbid.' WHERE id='.$row->id;
					$this->db->simple_query($mSQL);
				}
			}
		}

		//Marca la deducciones
		$mSQL="UPDATE lgasto SET pago=${dbid} WHERE proveed=${dbproveed} AND fecha <=${dbfcorte} AND (pago IS NULL OR pago=0)";
		$this->db->query($mSQL);

		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _post_delete($do){

		$id       = $do->get('id');
		$dbid     = $this->db->escape($id);

		//Desmarca los pagos por transporte
		$mSQL="UPDATE
			lrece AS a
			JOIN lruta AS b ON a.ruta=b.codigo
		SET a.pago=0 WHERE a.pago=${dbid}";
		$this->db->query($mSQL);

		//Desmarca los pagos por productor
		$mSQL="UPDATE
			itlrece AS a
			JOIN lrece AS b ON a.id_lrece=b.id
			JOIN lvaca AS c ON a.id_lvaca=c.id
		SET a.pago=0 WHERE a.pago=${dbid}";
		$this->db->query($mSQL);

		//Desmarca la deducciones
		$mSQL="UPDATE lgasto SET pago=0 WHERE pago=${dbid}";
		$this->db->query($mSQL);

		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}


	function _post_lote_insert($do){
		$banco   = $do->get('enbanco');
		$bbanco  = $do->get('banco');

		$tipo    = $do->get('tipo');
		$fecha   = date('Y-m-d');
		$hfecha  = date('d/m/Y');
		$id_lote = $do->get('id');
		$sumonto = $reg=0;
		$this->genesal=false;

		$this->id_lote=$id_lote;

		$this->db->select(array('proveed','nombre'));
		$this->db->from('sprv');
		$this->db->where('banco1'  ,$bbanco );
		$this->db->where('prefpago',$tipo );
		$query = $this->db->get();
		foreach ($query->result() as $row){
			$_POST['proveed'] = $row->proveed;
			$_POST['nombre']  = $row->nombre;

			//Calcula el moto que se le debe
			$montos = $this->_cmonto($row->proveed);
			if(isset($montos['monto']) && isset($montos['tmonto']) && isset($montos['deduc'])){
				$_POST['deduc']     = round($montos['deduc'],2);
				$_POST['monto']     = round($montos['monto']+$montos['tmonto'],2);
				$_POST['montopago'] = round($_POST['monto']-$_POST['deduc'],2);
			}
			//Fin del calculo

			if($_POST['montopago']>0){
				$_POST['fecha']   = $hfecha;
				$_POST['banco']   = $banco;
				$_POST['benefi']  = '';
				$_POST['numche']  = '**LOTE**';

				ob_start();
					$this->dataedit();
					$jsresult=ob_get_contents();
				@ob_end_clean();
				$rt = json_decode($jsresult,true);
				if($rt['status']=='B'){
					memowrite($rt['mensaje'],'LPAGO');
				}else{
					$reg++;
					$sumonto +=  $_POST['montopago'];
				}

				$this->validation->_error_array    = array();
				$this->validation->_rules          = array();
				$this->validation->_fields         = array();
				$this->validation->_error_messages = array();
			}
		}
		$mSQL = "UPDATE lpagolote SET monto = ${sumonto} WHERE id=${id_lote}";
		$this->db->simple_query($mSQL);
		$do->comment = "Registros creados: ${reg} Monto: ".nformat($sumonto);
	}

	function _post_lote_update($do){
		return false;
	}

	function _post_lote_delete($do){

	}

	function _pre_lote_insert($do){
		$do->set('fecha',date('Y-m-d'));
		return true;
	}

	function _pre_lote_update($do){

	}

	function _pre_lote_delete($do){

	}

	function imppago(){
		// Si manda el valor en el uri

		if ( $this->uri->total_segments() == 6 ) {
			$tt = $this->uri->segment($this->uri->total_segments());
			if ( $this->uri->segment(5) == 'procesar' ) {
				$_POST['valor'] = $tt;
			}
		} else
			$tt = 'procesar';

		$this->rapyd->load('datagrid','dataform');

		$filter = new dataForm('contabilidad/casi/localizador/'.$tipo.'/procesar');

		$filter->valor = new inputField($tit, 'valor');
		$filter->valor->rule = 'required'.$rul;
		$filter->valor->autocomplete=false;
		$filter->valor->maxlength=$maxlen;
		$filter->valor->size=15;

		if ( $tt <> 'procesar') {
			$filter->valor->insertValue = $tt;
		}

		$action = "javascript:window.location='".site_url('contabilidad/casi/auditoria')."'";
		$filter->button('btn_regresa', 'Regresar', $action, 'BL');
		$filter->submit('btnsubmit','Buscar');
		$filter->build_form();

		$sal='';

/*
		$verdad = ($filter->on_success() && $filter->is_valid());
		if ( $tt <> 'procesar') {
			$verdad = true;
		}
*/

		if ( $filter->on_success() && $filter->is_valid() ) {
			$this->load->library('table');
			$this->table->set_heading('Tabla', 'Campo', 'Coincidencias');
			//$valor = str_pad($filter->valor->newValue,8,'0', STR_PAD_LEFT);

			if ( $valor == '00000000' )
				$valor = $tt;

			$valor = $this->db->escape($valor);

			$tables = $this->db->list_tables();
			foreach ($tables as $table){
				if (preg_match("/^view_.*$|^sp_.*$|^viemovinxventas$/i",$table)) continue;

				$fields = $this->db->list_fields($table);
				if (in_array($cc, $fields)){
					$mSQL="SELECT COUNT(*) AS cana FROM `$table` WHERE $cc = $valor";

					$cana=$this->datasis->dameval($mSQL);
					if($cana>0){

						$grid = new DataGrid("$table: $cana");
						//$grid->per_page = $cana;
						$grid->db->from($table);
						$grid->db->where("$cc = $valor");
						$grid->db->limit(200);
						if(in_array('id', $fields)){
							$grid->db->orderby('id','desc');
						}

						foreach($fields as $ff){
							$grid->column($ff , $ff);
						}
						$grid->build();
						$sal.=$grid->output;
					}
				}
			}
		}

		$data['content'] = $filter->output.$sal;
		$data['title']   = heading('Localizador de Transacciones');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}



	function instalar(){
		if(!$this->db->table_exists('lpago')) {
			$mSQL="CREATE TABLE `lpago` (
				`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`numero` VARCHAR(8) NULL DEFAULT NULL,
				`tipo` CHAR(1) NULL DEFAULT NULL COMMENT 'Transportista y Productor',
				`fecha` DATE NULL DEFAULT NULL,
				`proveed` VARCHAR(10) NULL DEFAULT NULL,
				`nombre` VARCHAR(100) NULL DEFAULT NULL,
				`banco` VARCHAR(50) NULL DEFAULT NULL,
				`numche` VARCHAR(100) NULL DEFAULT NULL,
				`benefi` VARCHAR(200) NULL DEFAULT NULL,
				`monto` DECIMAL(12,2) NULL DEFAULT NULL,
				`deduc` DECIMAL(12,2) NULL DEFAULT NULL,
				`montopago` DECIMAL(12,2) NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `proveed` (`proveed`),
				INDEX `numero` (`numero`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('lpago');
		if (!in_array('id_lpagolote',$campos)){
			$mSQL="ALTER TABLE `lpago` ADD COLUMN `id_lpagolote` INT NULL DEFAULT NULL";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('lpagolote')) {
			$mSQL="CREATE TABLE `lpagolote` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`enbanco` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Banco con que se paga',
				`banco` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Banco donde se deposita',
				`tipo` CHAR(2) NULL DEFAULT NULL,
				`numero` VARCHAR(50) NULL DEFAULT NULL,
				`benefi` VARCHAR(100) NULL DEFAULT NULL,
				`fecha` DATE NULL DEFAULT NULL,
				`monto` DECIMAL(12,2) NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('lpagolote');
		if (!in_array('banco',$campos)){
			$mSQL="ALTER TABLE `lpagolote` CHANGE COLUMN `enbanco` `enbanco` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Banco con que se paga' AFTER `id`, ADD COLUMN `banco` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Banco donde se deposita' AFTER `enbanco`";
			$this->db->simple_query($mSQL);
		}
	}
}
