<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
class Lacida extends Controller {
	var $mModulo = 'LACIDA';
	var $titp    = 'Notificaci&oacute; de leche acida';
	var $tits    = 'Notificaci&oacute; de leche acida';
	var $url     = 'leche/lacida/';

	function Lacida(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'LACIDA', $ventana=0 );
	}

	function index(){
		$this->datasis->creaintramenu(array('modulo'=>'225','titulo'=>'Leche Acida','mensaje'=>'Notificacion Leche Acida','panel'=>'LECHE','ejecutar'=>'leche/lacida','target'=>'popu','visible'=>'S','pertenece'=>'2','ancho'=>900,'alto'=>600));	
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

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Notificacion de Leche Acida'),
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('LACIDA', 'JQ');
		$param['otros']       = $this->datasis->otros('LACIDA', 'JQ');
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
		function lacidaadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function lacidaedit(){
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
		function lacidashow(){
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
		function lacidadel() {
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
			autoOpen: false, height: 410, width: 600, modal: true,
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/LACIDA').'/\'+res.id+\'/id\'').';
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
	//Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;


		$grid->addField('id');
		$grid->label('Numero');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));

		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('ruta');
		$grid->label('Ruta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('vaquera');
		$grid->label('Vaquera');
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


		$grid->addField('nomvaca');
		$grid->label('Nomvaca');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:45, maxlength: 45 }',
		));


		$grid->addField('litros');
		$grid->label('Litros');
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

		$grid->addField('acidez');
		$grid->label('Acidez');
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

		$grid->addField('alcohol');
		$grid->label('Alcohol');
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

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

		$grid->addField('descrip');
		$grid->label('Descrip');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:45, maxlength: 45 }',
		));

		$grid->addField('precio');
		$grid->label('Precio');
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

/*
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

		$grid->addField('rendimiento');
		$grid->label('Rendimiento');
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


		$grid->addField('gadm');
		$grid->label('Gasto Adm');
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


		$grid->addField('precioref');
		$grid->label('P.Referen');
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


		$grid->addField('pleche');
		$grid->label('Pagar');
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

		$grid->addField('descuento');
		$grid->label('Descuento');
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

		$grid->addField('pago');
		$grid->label('Nro.Pago');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 70,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0	 }'
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
		$grid->setAdd(    $this->datasis->sidapuede('LACIDA','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('LACIDA','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('LACIDA','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('LACIDA','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: lacidaadd, editfunc: lacidaedit, delfunc: lacidadel, viewfunc: lacidashow");

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

	/*******************************************************************
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('lacida');

		$response   = $grid->getData('lacida', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/*******************************************************************
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
				$check = $this->datasis->dameval("SELECT count(*) FROM lacida WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('lacida', $data);
					echo "Registro Agregado";

					logusu('LACIDA',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM lacida WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM lacida WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE lacida SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("lacida", $data);
				logusu('LACIDA',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('lacida', $data);
				logusu('LACIDA',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM lacida WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM lacida WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM lacida WHERE id=$id ");
				logusu('LACIDA',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	//  Data Edit
	//
	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'lacida');


		$script= "
		$(document).ready(function() {
			$('#vaquera').autocomplete({
				source: function( req, add){
					$.ajax({
						url:  '".site_url('ajax/buscalvaca')."',
						type: 'POST',
						dataType: 'json',
						data: 'q='+req.term,
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$('#vaquera').val('')
									$('#nomvaca').val('');
									$('#nomvaca_val').text('');
								}else{
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
					$('#vaquera').attr('readonly', 'readonly');
					$('#vaquera').val(ui.item.vaquera);
					$('#nomvaca').val(ui.item.nombre);
					setTimeout(function() {  $('#vaquera').removeAttr('readonly'); }, 1500);
				}
			});

			$('#codigo').autocomplete({
				source: function( req, add){
					$.ajax({
						url:  '".site_url('ajax/buscasinv')."',
						type: 'POST',
						dataType: 'json',
						data: 'q='+req.term,
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$('#codigo').val('')
									$('#descrip').val('');
									$('#descrip_val').text('');
									$('#precio').val('');
									$('#precio_val').text('');
								}else{
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
					$('#codigo').attr('readonly', 'readonly');
					$('#codigo').val(ui.item.codigo);
					$('#descrip').val(ui.item.descrip);
					$('#descrip_val').text(ui.item.descrip);
					$('#precio').val(ui.item.base1);
					$('#precio_val').text(ui.item.base1);

					setTimeout(function() {  $('#codigo').removeAttr('readonly'); }, 1500);
				}
			});
		});
		";

		$script .= " 
		function totalizar(){
			var precio = Number($('#precio'   ).val());
			var promed = Number($('#promedio' ).val());
			var gastos = Number($('#gadm'     ).val());
			var descue = Number($('#desuento' ).val());
			var preref = Number($('#precioref').val());
			var litros = Number($('#litros'   ).val());

			if ( precio.length = 0 ){ precio = 30   };
			if ( promed.length = 0 ){ promed = 1   };
			if ( gastos.length = 0 ){ gastos = 0.4 };
			if ( descue.length = 0 ){ descue = 5   };
			if ( preref.length = 0 ){ preref = 5   };
			if ( litros.length = 0 ){ litros = 1   };

			$(\"#pleche\").val(roundNumber( precio/promed - gastos ,2));
			if ( preref > (precio/promed-gastos ) ) {
				$(\"#descuento\").val(roundNumber( (preref-(precio/promed-gastos ))*litros,2));
			} else {
				$(\"#descuento\").val(0.00);
			}

		};
			
		$('#precio'   ).change( function(){ totalizar();} );
		$('#promedio' ).change( function(){ totalizar();} );
		$('#gadm'     ).change( function(){ totalizar();} );
		$('#precioref').change( function(){ totalizar();} );
		$('#litros'   ).change( function(){ totalizar();} );
		
		
		";

		$script .= ' 
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
		});';



		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('update','_pre_update');
		$edit->pre_process('delete','_pre_delete');

		$edit->id = new inputField('Numero','id');
		$edit->id->rule='numeric';
		$edit->id->css_class='inputnum';
		$edit->id->size =12;
		$edit->id->maxlength =10;
		$edit->id->readonly = true;
		$edit->id->insertValue=date('0');

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->calendar = false;
		$edit->fecha->insertValue=date('Y-m-d');

		$edit->ruta = new dropdownField('Ruta', 'ruta');
		$edit->ruta->rule = 'trim';
		$edit->ruta->option('','Seleccionar');
		$edit->ruta->options('SELECT codigo, CONCAT( nombre," ",codigo) nombre FROM lruta ORDER BY nombre');
		$edit->ruta->style = 'width:350px';

		$edit->vaquera = new inputField('Vaquera','vaquera');
		$edit->vaquera->size = 6;
		$edit->vaquera->maxlength =10;

		$edit->nomvaca = new inputField('Nombre','nomvaca');
		$edit->nomvaca->size = 35;
		$edit->nomvaca->maxlength = 45;

		$edit->litros = new inputField('Litros','litros');
		$edit->litros->rule='numeric';
		$edit->litros->css_class='inputnum';
		$edit->litros->size = 8;
		$edit->litros->maxlength = 16;

		$edit->acidez = new inputField('Acidez','acidez');
		$edit->acidez->rule='numeric';
		$edit->acidez->css_class='inputnum';
		$edit->acidez->size = 6;
		$edit->acidez->maxlength = 6;
		$edit->acidez->insertValue=date('21');

		$edit->alcohol = new inputField('Alcohol','alcohol');
		$edit->alcohol->rule='numeric';
		$edit->alcohol->css_class='inputnum';
		$edit->alcohol->size = 6;
		$edit->alcohol->maxlength = 6;
		$edit->alcohol->insertValue=date('1');

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->size =17;
		$edit->codigo->maxlength =15;

		$edit->descrip = new inputField('Descripcion','descrip');
		$edit->descrip->size =47;
		$edit->descrip->maxlength =45;

		$edit->precio = new inputField('Precio','precio');
		$edit->precio->rule='numeric';
		$edit->precio->css_class='inputnum';
		$edit->precio->size =10;
		$edit->precio->maxlength =10;

		$edit->peso = new inputField('Peso','peso');
		$edit->peso->rule='numeric';
		$edit->peso->css_class='inputnum';
		$edit->peso->size =12;
		$edit->peso->maxlength =10;

		$edit->rendimiento = new inputField('Rendimiento','rendimiento');
		$edit->rendimiento->rule='numeric';
		$edit->rendimiento->css_class='inputnum';
		$edit->rendimiento->size =12;
		$edit->rendimiento->maxlength =10;

		$edit->promedio = new inputField('Promedio','promedio');
		$edit->promedio->rule='numeric';
		$edit->promedio->css_class='inputnum';
		$edit->promedio->size =6;
		$edit->promedio->maxlength =6;

		$edit->gadm = new inputField('Gastos Adm.','gadm');
		$edit->gadm->rule='numeric';
		$edit->gadm->css_class='inputnum';
		$edit->gadm->size =6;
		$edit->gadm->maxlength =6;
		$edit->gadm->insertValue=date('0.4');

		$edit->pleche = new inputField('Precio','pleche');
		$edit->pleche->rule = 'numeric';
		$edit->pleche->css_class = 'inputnum';
		$edit->pleche->size = 8;
		$edit->pleche->maxlength = 10;
		$edit->pleche->readonly  = true;

		$edit->precioref = new inputField('Precio Pagado','precioref');
		$edit->precioref->rule      = 'numeric';
		$edit->precioref->css_class = 'inputnum';
		$edit->precioref->size      =  8;
		$edit->precioref->maxlength = 10;

		$edit->descuento = new inputField('Monto a descontar','descuento');
		$edit->descuento->rule      = 'numeric';
		$edit->descuento->css_class = 'inputnum';
		$edit->descuento->size      = 8;
		$edit->descuento->maxlength =10;
		$edit->descuento->readonly  = true;


		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form']   =&  $edit;
			$conten['script'] =  '';
			$this->load->view('view_lacida', $conten);

			//echo $edit->output;
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
		if (!$this->db->table_exists('lacida')) {
			$mSQL="
			CREATE TABLE `lacida` (
				fecha     DATE          NULL DEFAULT NULL,
				ruta      CHAR(4)       NULL DEFAULT NULL   COMMENT 'Ruta ',
				vaquera   INT(11)       NULL DEFAULT NULL   COMMENT 'Vaquera',
				nomvaca   VARCHAR(45)   NULL DEFAULT NULL   COMMENT 'Nombre de la ruta o vaquera',
				litros    DECIMAL(16,2) NULL DEFAULT NULL   COMMENT 'Litros de Leche Acida',
				acidez    DECIMAL(10,0) NULL DEFAULT NULL   COMMENT 'Acidez',
				alcohol   DECIMAL(10,0) NULL DEFAULT '0'    COMMENT 'Alcohol',
				codigo    VARCHAR(15)   NULL DEFAULT NULL   COMMENT 'Queso producido ',
				descrip   VARCHAR(45)   NULL DEFAULT NULL   COMMENT 'Descripcion del Producto',
				precio    DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Precio del queso',
				precioref DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Precio Referencia',
				descuento DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Monto a Descontar',
				promedio  DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Promedio Litros/Kg',
				gadm      DECIMAL(10,2) NULL DEFAULT '0.40' COMMENT 'Gasstos Administrativos',
				pleche    DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Precio de la leche',
				pago      INT(11)       NULL DEFAULT '0'    COMMENT 'Nro de Pago',
				id        INT(11)   NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (id),
				INDEX fecha (fecha)
			)
			COMMENT='Notificacion de leche Acida'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			";
			$this->db->simple_query($mSQL);
		}
	}
}

?>
