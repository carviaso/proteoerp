<?php
class Invresu extends Controller {
	var $mModulo= 'INVRESU';
	var $titp   = 'Libro de Inventario';
	var $tits   = 'Libro de Inventario';
	var $url    = 'finanzas/invresu/';

	function Invresu(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'INVRESU', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	// Layout en la Ventana
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'genera',   'img'=>'images/engrana.png',  'alt' => 'Generar Listado', 'label'=>'Generar Listado'));

		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita' , 'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow'  , 'title'=>'Mostrar registro'),
			array('id'=>'fborra' , 'title'=>'Eliminar registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('INVRESU', 'JQ');
		$param['otros']       = $this->datasis->otros('INVRESU', 'JQ');
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
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function invresuadd() {
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function invresuedit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		};';

		$bodyscript .= '
		function invresudel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				if(confirm(" Seguro desea anular el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						$("#fborra").html(data);
						$("#fborra").dialog( "open" );
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function invresushow() {
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
		jQuery("#genera").click(function(){
			window.open(\''.base_url().'finanzas/invresu/genelibro/\', \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
		});';

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
			buttons: {
				"Guardar": function(){
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/INVRESU').'/\'+res.id+\'/id\'').';
									return true;
								}else{
									apprise(json.mensaje);
								}
							}catch(e){
								$("#fedita").html(r);
							}
						}
					});
				},
				"Cancelar": function(){
					$("#fedita").html("");
					$( this ).dialog("close");
				}
			},
			close: function(){
				$("#fedita").html("");
				allFields.val("").removeClass( "ui-state-error" );
			}
		});';

		$bodyscript .= '
		$("#fshow").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fshow").html("");
					$(this).dialog( "close" );
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
					$(this).dialog( "close" );
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

	//******************************************************************
	// Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('mes');
		$grid->label('A&ntilde;o Mes');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10  }',
			//'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		/*$grid->addField('mes');
		$grid->label('Mes');
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
		));*/


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
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:45, maxlength: 45 }',
		));


		$grid->addField('inicial');
		$grid->label('Cant.Inicial');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('minicial');
		$grid->label('Monto inicial');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('final');
		$grid->label('Cant.Final');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('mfinal');
		$grid->label('Monto final');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('compras');
		$grid->label('Compras');
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


		$grid->addField('conver');
		$grid->label('Conversion');
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


		$grid->addField('ventas');
		$grid->label('Ventas');
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


		$grid->addField('trans');
		$grid->label('Transferencias');
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

		$grid->addField('ajuste');
		$grid->label('Ajustes');
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


		$grid->addField('fisico');
		$grid->label('F&iacute;sico');
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


		$grid->addField('notas');
		$grid->label('Notas de Entrega');
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


		$grid->addField('trans');
		$grid->label('Transferencias');
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


		$grid->addField('mventas');
		$grid->label('Monto ventas');
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


		$grid->addField('mtrans');
		$grid->label('Monto trans.');
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


		$grid->addField('mfisico');
		$grid->label('Monto f&iacute;sico');
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


		$grid->addField('mnotas');
		$grid->label('Monto notas');
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

		$grid->addField('mpventa');
		$grid->label('Monto precio venta');
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
		$grid->setAdd(    $this->datasis->sidapuede('INVRESU','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('INVRESU','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('INVRESU','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('INVRESU','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: invresuadd,editfunc: invresuedit,delfunc: invresudel, viewfunc: invresushow");
		//$grid->setBarOptions("addfunc: sfacadd, editfunc: sfacedit, delfunc: sfacdel, viewfunc: sfacshow");

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
		$mWHERE = $grid->geneTopWhere('invresu');
		$response   = $grid->getData('invresu', array(array()), array(), false, $mWHERE );
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
		$mcodp  = "mes";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);

		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM invresu WHERE mes=".$data['mes']." AND codigo=".$this->db->escape($data['codigo'])  );
				if ( $check == 0 ){
					$this->db->insert('invresu', $data);
					echo "Registro Agregado";
					logusu('INVRESU',"Registro ".$data['mes']." ".$data['codigo']." INCLUIDO");
				} else
					echo "Ya existe un registro con ese mes y codigo";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM invresu WHERE id=$id");
			unset($data[$mcodp]);
			$this->db->where("id", $id);
			$this->db->update('invresu', $data);
			logusu('INVRESU',"Libro de Inventario  ".$nuevo." MODIFICADO");
			echo "$mcodp Modificado";

		} elseif($oper == 'del') {
			//$codigo = $this->datasis->dameval("SELECT $mcodp FROM invresu WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM invresu WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM invresu WHERE id=$id ");
				logusu('INVRESU',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$script="
		$(function(){
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
									$('#codigo').val('');
									$('#descrip').val('');
									$('#descrip_val').text('');
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

					setTimeout(function() {  $('#codigo').removeAttr('readonly'); }, 1500);
				}
			})
		});
		";

		$edit = new DataEdit($this->tits, 'invresu');
		$edit->script($script,'create');
		$edit->script($script,'modify');
		$edit->on_save_redirect=false;
		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert', '_post_insert');
		$edit->post_process('update', '_post_update');
		$edit->post_process('delete', '_post_delete');
		$edit->pre_process( 'insert', '_pre_insert');
		$edit->pre_process( 'update', '_pre_update');
		$edit->pre_process( 'delete', '_pre_delete');

		$edit->mes = new inputField('A&ntilde;o/Mes','mes');
		$edit->mes->rule='max_length[6]||integer';
		$edit->mes->css_class='inputonlynum';
		$edit->mes->insertValue=date('Ym');
		$edit->mes->maxlength =6;
		$edit->mes->size = 8;
		$edit->mes->append('Formato AAAAMM');

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->rule='max_length[15]|existesinv';
		$edit->codigo->size =17;
		$edit->codigo->maxlength =15;

		$edit->descrip = new inputField('Descripici&oacute;n','descrip');
		$edit->descrip->rule='max_length[45]';
		$edit->descrip->size =47;
		$edit->descrip->maxlength =45;
		$edit->descrip->type='inputhidden';
		$edit->descrip->in = 'codigo';

		$edit->inicial = new inputField('Cant. Inicial','inicial');
		$edit->inicial->rule='max_length[20]|numeric|required';
		$edit->inicial->css_class='inputnum';
		$edit->inicial->size =22;
		$edit->inicial->insertValue='0';
		$edit->inicial->maxlength =20;

	    $edit->inilabel = new freeField('','','Monto Bs.');
	    $edit->inilabel->in = 'inicial';

		$edit->minicial = new inputField('Monto inicial','minicial');
		$edit->minicial->rule='max_length[20]|numeric|required';
		$edit->minicial->css_class='inputnum';
		$edit->minicial->insertValue='0';
		$edit->minicial->size =22;
		$edit->minicial->maxlength =20;
		$edit->minicial->in = 'inicial';

		$edit->final = new inputField('Cant. Final','final');
		$edit->final->rule='max_length[20]|numeric|required';
		$edit->final->insertValue='0';
		$edit->final->css_class='inputnum';
		$edit->final->size =22;
		$edit->final->maxlength =20;

	    $edit->ini2label = new freeField('','','Monto Bs.');
	    $edit->ini2label->in = 'final';

		$edit->mfinal = new inputField('Monto final','mfinal');
		$edit->mfinal->rule='max_length[20]|numeric|required';
		$edit->mfinal->insertValue='0';
		$edit->mfinal->css_class='inputnum';
		$edit->mfinal->size =22;
		$edit->mfinal->maxlength =20;
		$edit->mfinal->in='final';

		$edit->compras = new inputField('Compras','compras');
		$edit->compras->rule='max_length[20]|numeric';
		$edit->compras->css_class='inputnum';
		$edit->compras->size =22;
		$edit->compras->maxlength =20;
		$edit->compras->group='Detalle';

	    $edit->ini3label = new freeField('','','Monto Bs.');
	    $edit->ini3label->in = 'compras';

		$edit->mcompras = new inputField('Mcompras','mcompras');
		$edit->mcompras->rule='max_length[20]|numeric';
		$edit->mcompras->css_class='inputnum';
		$edit->mcompras->size =22;
		$edit->mcompras->maxlength =20;
		$edit->mcompras->group='Detalle';
		$edit->mcompras->in = 'compras';

		$edit->ventas = new inputField('Cant. Ventas','ventas');
		$edit->ventas->rule='max_length[20]|numeric';
		$edit->ventas->css_class='inputnum';
		$edit->ventas->size =22;
		$edit->ventas->maxlength =20;
		$edit->ventas->group='Detalle';

	    $edit->ini4label = new freeField('','','Monto Bs.');
	    $edit->ini4label->in = 'ventas';

		$edit->mventas = new inputField('Monto de ventas','mventas');
		$edit->mventas->rule='max_length[20]|numeric';
		$edit->mventas->css_class='inputnum';
		$edit->mventas->size =22;
		$edit->mventas->maxlength =20;
		$edit->mventas->group='Detalle';
		$edit->mventas->in = 'ventas';

		$edit->mpventa = new inputField('Monto precio venta','mpventa');
		$edit->mpventa->rule='max_length[20]|numeric';
		$edit->mpventa->css_class='inputnum';
		$edit->mpventa->size =22;
		$edit->mpventa->maxlength =20;
		$edit->mpventa->group='Detalle';

		$edit->trans = new inputField('Cant. Transferencias','trans');
		$edit->trans->rule='max_length[20]|numeric';
		$edit->trans->css_class='inputnum';
		$edit->trans->size =22;
		$edit->trans->maxlength =20;
		$edit->trans->group='Detalle';

	    $edit->ini6label = new freeField('','','Monto Bs.');
	    $edit->ini6label->in = 'trans';

		$edit->mtrans = new inputField('Monto transferencia','mtrans');
		$edit->mtrans->rule='max_length[20]|numeric';
		$edit->mtrans->css_class='inputnum';
		$edit->mtrans->size =22;
		$edit->mtrans->maxlength =20;
		$edit->mtrans->group='Detalle';
		$edit->mtrans->in = 'trans';

		$edit->fisico = new inputField('F&iacute;sico','fisico');
		$edit->fisico->rule='max_length[20]|numeric';
		$edit->fisico->css_class='inputnum';
		$edit->fisico->size =22;
		$edit->fisico->maxlength =20;
		$edit->fisico->group='Detalle';

	    $edit->ini7label = new freeField('','','Monto Bs.');
	    $edit->ini7label->in = 'fisico';

		$edit->mfisico = new inputField('Mfisico','mfisico');
		$edit->mfisico->rule='max_length[20]|numeric';
		$edit->mfisico->css_class='inputnum';
		$edit->mfisico->size =22;
		$edit->mfisico->maxlength =20;
		$edit->mfisico->group='Detalle';
		$edit->mfisico->in = 'fisico';

		$edit->notas = new inputField('Cant. Notas de entrega','notas');
		$edit->notas->rule='max_length[20]|numeric';
		$edit->notas->css_class='inputnum';
		$edit->notas->size =22;
		$edit->notas->maxlength =20;
		$edit->notas->group='Detalle';

	    $edit->ini8label = new freeField('','','Monto Bs.');
	    $edit->ini8label->in = 'notas';

		$edit->mnotas = new inputField('Monto notas','mnotas');
		$edit->mnotas->rule='max_length[20]|numeric';
		$edit->mnotas->css_class='inputnum';
		$edit->mnotas->size =22;
		$edit->mnotas->maxlength =20;
		$edit->mnotas->in = 'notas';
		$edit->mnotas->group='Detalle';

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

	function genelibro(){
		$this->rapyd->load('datafilter','datagrid','fields');
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter('','view_invresutotal');

		$mes = $this->datasis->dameval('SELECT MID(MAX(mes),1,4) FROM invresu');

		$estFecha = $this->datasis->dameval('SELECT MAX(fecha) AS fecha FROM costos');
		if(empty($estFecha)){
			$estMsj='No existen estad&iacute;sticas generadas, debe generarse primero para usar este modulo.';
		}else{
			$estMsj='Por favor tenga en cuenta que este modulo utiliza las estad&iacute;sticas del sistema, por lo tanto los movimientos se podr&aacute;n generar solo hasta el '.dbdate_to_human($estFecha).'.';
		}
		$filter->container = new containerField('alert',"<b style='color:#E50E0E;'>${estMsj}</b>");
		$filter->container->clause='';

		$filter->fecha = new inputField('A&ntilde;o', 'anno');
		$filter->fecha->size     = 4;
		$filter->fecha->operator = '=';
		$filter->fecha->clause   = 'where';
		$filter->fecha->insertValue = $mes;

		$actionb = 'bobo(\''.site_url('finanzas/invresu/calcula').'/\'+$(\'#anno\').val()+\'01\');return true;';

		$filter->button('btn_recalculo', 'Generar todo', $actionb, 'BR','show');

		$filter->buttons('search');
		$filter->build();

		$monto = new inputField('Monto', 'monto');

		$monto->grid_name='monto[<#anno#>][<#mes#>]';

		$monto->size     = 14;
		$monto->css_class= 'inputnum';
		$monto->autocomplete=false;

		$grid = new DataGrid('Lista');
		$grid->per_page = 12;

		$uri2 = anchor('#',img(array('src'=>'images/engrana.png','border'=>'0','alt'=>'Calcula' ,'title'=>'Calcular')),                  array('onclick'=>'bobo(\''.base_url().'finanzas/invresu/calcula/<#anno#><#mes#>\');return false;'));
		$uri2 .= "&nbsp;&nbsp;";
		$uri2 .= anchor('#',img(array('src'=>'images/refresh.png','border'=>'0','alt'=>'Rebaja' ,'title'=>'Rebajar' )),                  array('onclick'=>'foo(\''.base_url().'finanzas/invresu/recalcula/<#anno#><#mes#>\');return false;'));
		$uri2 .= "&nbsp;&nbsp;";
		$uri2 .= anchor('#',img(array('src'=>'images/ojo.png',    'border'=>'0','alt'=>'Consulta','title'=>'Consultar', 'height'=>'18')),array('onclick'=>'fconsulta(\'<#anno#><#mes#>\'); return false;'));

		$grid->column('Accion',$uri2,  'align=\'center\' bgcolor=\'#041C87\'');
		$grid->column('A&ntilde;o',    'anno' ,'align="center"');
		$grid->column('Mes',           'mes'  ,'align="center"');
		$grid->column('Inicial',       '<nformat><#inicial#></nformat>',  'align=\'right\'');
		$grid->column('Compras',       '<nformat><#compras#></nformat>',  'align=\'right\'');
		$grid->column('Ventas',        '<nformat><#ventas#></nformat>',   'align=\'right\'');
		$grid->column('Retiros',       '<nformat><#retiros#></nformat>',  'align=\'right\'');
		$grid->column('Por Despachar', '<nformat><#despachar#></nformat>','align=\'right\'');
		$grid->column('Final',         '<nformat><#final#></nformat>',    'align=\'right\'');

		$grid->build();

		$ggrid =form_open('finanzas/invresu/index/search');
		$ggrid.=form_hidden('fecha', $filter->fecha->newValue);
		$ggrid.=$grid->output;
		$ggrid.=form_close();

		$script ='
<script type="text/javascript">
		$(function() {
			$(".inputnum").numeric(".");

			$("#consulta").dialog({
				autoOpen: false, height: 400, width: 600, modal: true,
				buttons: {
					"Salir": function() {
						$("#consulta").html("");
						$( this ).dialog( "close" );
					}
				},
				close: function() {
					$("#consulta").html("");
				}
			});
		});

		function foo(url){
			valor=$("#porcent").val();
			uurl=url+"/"+valor;'."
			$.blockUI({
				message: $('#displayBox'),
				css: {
				top:  ($(window).height() - 400) /2 + 'px',
				left: ($(window).width() - 400) /2 + 'px',
				width: '400px'
				}".'
			});
			$.get(uurl, function(data) {
				setTimeout($.unblockUI, 2);
				alert(data);
			});
			return false;
		}

		function bobo(url){'."
			if(confirm('Al generar de nuevo el libro se perderan las rebajas, esta consciente de eso?')){
				$.blockUI({
					message: $('#displayBox'),
					css: {
					top:  ($(window).height() - 400) /2 + 'px',
					left: ($(window).width() - 400) /2 + 'px',
					width: '400px'
					}".'
				});
				$.get(url, function(data) {
					setTimeout($.unblockUI, 2);
					alert(data);
				});
			}
			return false;
		}

		function fconsulta( mes ) {
			$.post("'.site_url($this->url.'consulta/').'"+\'/\'+mes,
			function(data){
				$("#consulta").html(data);
				$("#consulta").dialog("open");
			})
		};
</script>';

		$espera  = "\n".'<div id="displayBox" style="display:none" ><p>Espere.....</p><img  src="'.base_url().'images/doggydig.gif" width="131px" height="79px"  /></div>';
		$espera .= "\n".'<div id="consulta" name="consulta" title="Detalle del Mes"></div>';

		$porcent  = "\n<div align='center' style='font-size:16pt;'><a href='".base_url()."reportes/ver/INVENTA/SINV'>Emitir Listado</a></div> ";
		$porcent .= "\n<div align='right'>Porcentaje de Variaci&oacute;n";
		$porcent .= form_input(array('name'=>'porcent','id'=>'porcent','value'=>'0','size'=>'10','style'=>'text-align:right' ) );
		$porcent .= "\n</div>";


		$data['content'] = $filter->output.$porcent.$ggrid.$espera;
		$data['title']   = heading('Libro de inventario');
		$data['style']   = style('impromptu/default.css');
		$data['style']  .= style('themes/proteo/proteo.css');

		$data['script']  = script('jquery-min.js');
		$data['script'] .= script('jquery-migrate-min.js');
		$data['script'] .= script('plugins/jquery.numeric.pack.js');
		$data['script'] .= script('plugins/jquery.floatnumber.js');
		$data['script'] .= script('plugins/jquery.blockUI.js');
		$data['script'] .= script('jquery-ui.custom.min.js');

		$data['script'] .= script('jquery-impromptu.js');

		$data['script'] .= $script;

		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function consulta($mes){

		$mSQL = '
		SELECT a.mes   AS mes, a.codigo AS codigo, if(isnull(b.descrip),
			concat(a.codigo, "Articulo fusionado"), b.descrip) AS descrip,
			sum(a.minicial) AS minicial,
			sum(a.mcompras) AS mcompras,
			sum(a.mconver)  AS mconver,
			sum(a.mventas)  AS mventas,
			sum(a.mtrans)   AS mtrans,
			sum(a.majuste)  AS majuste,
			sum(a.mfisico)  AS mfisico,
			sum(a.mnotas)   AS mnotas,
			sum(a.mfinal)   AS mfinal,
			sum(a.minicial*(minicial<0) ) AS ininega,
			sum(a.mfinal*(mfinal<0) )     AS finnega
 		FROM invresu a LEFT JOIN sinv b ON a.codigo = b.codigo
		WHERE a.mes='.$mes.'
		GROUP BY a.mes';

		$reg = $this->datasis->damereg($mSQL);
		$sale = "<div style='font-size:12pt;font-weight:bold;text-align:center;'>RESUMEN DE MOVIMIENTO DEL ${mes}</div>";

		$sale .= "
		<table align='center' width='95%'>
			<tr>
				<td>Valor Inicial</td><td>&nbsp;</td><td align='right'><b>".nformat($reg['minicial'])."</b></td>
			</tr>
			<tr>
				<td>Compras</td><td align='right'>".nformat($reg['mcompras'])."</td><td>&nbsp;</td>
			</tr>
			<tr>
				<td>Ventas</td><td align='right'>".nformat($reg['mventas'])."</td><td>&nbsp;</td>
			</tr>
			<tr>
				<td>Compra-Venta</td><td>&nbsp;</td><td align='right'><b>".nformat($reg['mcompras']-$reg['mventas'])."</b></td>
			</tr>
			<tr>
				<td>Conversiones</td><td align='right'>".nformat($reg['mconver'])."</td><td>&nbsp;</td>
			</tr>
			<tr>
				<td>Transferencias</td><td align='right'>".nformat($reg['mtrans'])."</td><td>&nbsp;</td>
			</tr>
			<tr>
				<td>Ajustes de Inventario</td><td align='right'>".nformat($reg['majuste'])."</td><td>&nbsp;</td>
			</tr>
			<tr>
				<td>Notas de Entrega</td><td align='right'>".nformat($reg['mnotas'])."</td><td>&nbsp;</td>
			</tr>
			<tr>
				<td>Inventario Fisico</td><td align='right'>".nformat($reg['mfisico'])."</td><td>&nbsp;</td>
			</tr>
			<tr>
				<td>Total Retiros</td><td>&nbsp;</td><td align='right'><b>".nformat($reg['mconver']+$reg['mtrans']-$reg['mnotas']+$reg['mfisico'])."</b></td>
			</tr>
			<tr>
				<td>Valor Final</td><td align='right'>".nformat($reg['mfinal'])."</td><td align='right'><b>".nformat($reg['minicial']+$reg['mcompras']-$reg['mventas']+$reg['mconver']+$reg['mtrans']-$reg['mnotas']+$reg['mfisico']  )."</b></td>
			</tr>
		</table>
		<br>
		<table align='center' width='95%' style='border:1px solid;' cellspacing='2' cellpadding='2'>
			<tr>
				<td>Productos en Negativo</td><td align='right'>".nformat($reg['ininega'])."</td><td>Inicial Ajustado</td><td align='right'><b>".nformat($reg['minicial']-$reg['ininega'])."</b></td>
			</tr>
			<tr>
				<td>Monto Final Negativo</td><td align='right'>".nformat($reg['finnega'])."</td><td>Final Ajustado</td><td align='right'><b>".nformat($reg['mfinal']-$reg['finnega'])."</b></td>
			</tr>
		</table>
		";

		echo $sale;
	}

	function calcula(){
		$meco = $this->uri->segment(4);
		$ano = substr($meco,0,4)*100;

		while ( $meco-$ano < 13 ) {
			$this->_calcula($meco);
			$meco++;
		}
		logusu('invresu','Genero para todo el '.substr($ano,0,4));
		echo "Calculo Concluido";
	}

	function _calcula( $mes){
		//Borra lo que hay
		$this->db->query("DELETE FROM invresu WHERE mes=${mes}");

		// Chequea si existe le sp
		$bd   = $this->db->database;
		$mSQL = '
			SELECT COUNT(*) 
			FROM INFORMATION_SCHEMA.ROUTINES 
			WHERE ROUTINE_SCHEMA = "'.$bd.'"    AND 
				  ROUTINE_NAME   = "sp_invresu" AND 
				  ROUTINE_TYPE   = "PROCEDURE"
		';
		$tipo = $this->datasis->dameval($mSQL);
		if ( $tipo == 1 ){
			$this->db->query('CALL sp_invresu('.$mes.',1)');
		} else {
			// Carga desde costos
			$mSQL = "
			INSERT INTO invresu ( mes, codigo, descrip, inicial, compras, conver, ventas, trans, ajuste, fisico, notas, final, minicial, mcompras, mconver, mventas, mtrans, majuste, mfisico, mnotas, mfinal, mpventa )
			SELECT
			EXTRACT(YEAR_MONTH FROM a.fecha) AS mes, a.codigo, b.descrip,
			0                                                                AS inicial,
			sum(a.cantidad*(a.origen IN ('2C','2D'))*IF(a.origen='2D',-1,1)) AS compras,
			sum(a.cantidad*(a.origen IN ('6C') ))                            AS conver,
			sum(a.cantidad*(a.origen IN ('3I','3M') ))                       AS ventas,
			sum(a.cantidad*(a.origen IN ('1T') ))                            AS trans,
			sum(a.cantidad*(a.origen IN ('5C') ))                            AS ajuste,
			sum((a.cantidad-a.anteri)*(a.origen IN ('0F','8F')))             AS fisico,
			sum(a.cantidad*(a.origen='4N'))                                  AS notas,
			0                                                                AS final,
			0                                                                AS minicial,
			sum(a.monto*(a.origen IN ('2C','2D'))*IF(a.origen='2D',-1,1))    AS mcompras,
			sum(a.cantidad*a.promedio*(a.origen IN ('6C') ))                 AS mconver,
			sum(a.cantidad*a.promedio*(a.origen IN ('3I','3M')))             AS mventas,
			sum(a.cantidad*a.promedio*(a.origen IN ('1T','6C','5C') ))       AS mtrans,
			sum(a.cantidad*a.promedio*(a.origen IN ('5C') ))                 AS majuste,
			sum((a.cantidad-a.anteri)*a.promedio*(a.origen IN ('0F','8F')))  AS mfisico,
			sum(a.cantidad*a.promedio*(a.origen='4N'))                       AS mnotas,
			0                                                                AS mfinal,
			sum(venta)                                                       AS mpventas
			FROM costos AS a LEFT JOIN sinv AS b ON a.codigo=b.codigo
			WHERE EXTRACT(YEAR_MONTH FROM a.fecha)=${mes} AND MID(b.tipo,1,1) != 'S'
			GROUP BY EXTRACT(YEAR_MONTH FROM a.fecha),a.codigo";
			$this->db->query($mSQL);

			// Trae saldos Iniciales
			$mesante = $this->datasis->dameval("SELECT mes FROM invresu WHERE mes < ${mes} ORDER BY mes DESC LIMIT 1");

			if($mesante){
				// Agrega codigos desde los anteriores
				$mSQL = "
				INSERT IGNORE INTO invresu ( mes, codigo, descrip, inicial,   compras,   conver,   ventas,   trans,   ajuste,   fisico,   notas,   final,   minicial,   mcompras,   mconver,   mventas,   mtrans,   majuste,   mfisico,   mnotas,   mfinal,    mpventa )
				SELECT ${mes}                mes, codigo, '',    0 inicial, 0 compras, 0 conver, 0 ventas, 0 trans, 0 ajuste, 0 fisico, 0 notas, 0 final, 0 minicial, 0 mcompras, 0 mconver, 0 mventas, 0 mtrans, 0 majuste, 0 mfisico, 0 mnotas, 0 mfiscal, 0 mpventas FROM invresu WHERE mes=${mesante};
				";
				$this->db->query($mSQL);

				// Coloca saldos anteriores
				$mSQL = "
				UPDATE invresu a JOIN  invresu b ON a.codigo=b.codigo AND a.mes=${mes} AND b.mes=${mesante}
				SET a.inicial=b.final, a.minicial=b.mfinal;";
				$this->db->query($mSQL);
			}

			// Recalcula saldo final
			$mSQL = "
			UPDATE invresu SET
			final  =  inicial + compras  + conver  - ventas  - notas  + trans  + ajuste  + fisico,
			mfinal = minicial + mcompras + mconver - mventas - mnotas + mtrans + majuste + mfisico
			WHERE mes = ${mes} ";
			$this->db->query($mSQL);
		}
/*
		$mSQL = "
SELECT mes INTO @mPAPA FROM invresu WHERE mes < mFECHA ORDER BY mes DESC LIMIT 1;
IF @mPAPA > 0 THEN
	REPLACE INTO invresu ( mes, codigo, descrip, inicial, compras, ventas, trans, fisico, notas, final, minicial, mcompras, mventas, mtrans, mfisico, mnotas, mfinal, mpventa )
	SELECT mFECHA mes, codigo, '',       final,  0 compras, 0 ventas, 0 trans, 0 fisico, 0 notas, final, mfinal, 0 mcompras, 0 mventas, 0 mtrans, 0 mfisico, 0 mnotas, 0 mfiscal, 0 mpventas  FROM invresu WHERE mes=@mPAPA;
END IF;
DROP TABLE IF EXISTS INVRESUTEM;
CREATE TABLE INVRESUTEM
SELECT EXTRACT(YEAR_MONTH FROM a.fecha) AS mes, a.codigo, b.descrip, 0 AS inicial,
sum(a.cantidad*(a.origen IN ('2C','2D'))*IF(a.origen='2D',-1,1)) AS compras,
sum(a.cantidad*(a.origen IN ('3I','3M') )) AS ventas,
sum(a.cantidad*(a.origen IN ('1T','6C','5C')  )) AS trans,
sum((a.cantidad-a.anteri)*(a.origen IN ('0F','9F'))) AS fisico,
sum(a.cantidad*(a.origen='4N')) AS notas,  0 AS final,  0 AS minicial,
sum(a.monto*(a.origen IN ('2C','2D'))*IF(a.origen='2D',-1,1)) AS mcompras,
sum(a.cantidad*a.promedio*(a.origen IN ('3I','3M'))) AS mventas,
sum(a.cantidad*a.promedio*(a.origen IN ('1T','6C','5C') )) AS mtrans,
sum((a.cantidad-a.anteri)*a.promedio*(a.origen IN ('0F','9F'))) AS mfisico,
sum(a.cantidad*a.promedio*(a.origen='4N')) AS mnotas,
0 AS mfinal, sum(venta)
FROM costos AS a LEFT JOIN sinv AS b ON a.codigo=b.codigo
WHERE EXTRACT(YEAR_MONTH FROM a.fecha) = mFECHA AND MID(b.tipo,1,1)!='S'
GROUP BY EXTRACT(YEAR_MONTH FROM a.fecha),a.codigo ;
UPDATE invresu a JOIN INVRESUTEM b ON a.mes=b.mes AND a.codigo=b.codigo
SET a.compras=b.compras, a.ventas = b.ventas,a.notas = b.notas,a.trans = b.trans,a.fisico = b.fisico,
a.mcompras = b.mcompras,a.mventas = b.mventas,a.mnotas = b.mnotas,a.mtrans = b.mtrans,
a.mfisico = b.mfisico ;
DROP TABLE IF EXISTS INVRESUTEM;
UPDATE invresu SET final=inicial+compras-ventas-notas+trans+fisico,mfinal=minicial+mcompras-mventas-mnotas+mtrans+mfisico WHERE mes=mFECHA;";
*/
	}

	function recalcula(){
		$meco = $this->uri->segment(4);
		$porcent = $this->uri->segment(5);
		$ano = substr($meco,0,4)*100;
		if ( abs($porcent) > 0  ) {
			$this->db->simple_query("CALL sp_invresufix(".$meco.",".$porcent.")");
			$meco++;
			// debe pasar los saldos a las siguientes meses
			while ( $meco-$ano < 13 ) {
				$this->db->simple_query("CALL sp_invresusum(".$meco.")");
				$meco++;
			};
			logusu('invresu','Rebajo para la fecha '.$ano.' '.$porcent.'%');
			echo "Recalculo Concluido";
		} else {
			echo "Debe colocar un porcentaje";
		};

	}

	function _pre_insert($do){
		return true;
	}

	function _pre_update($do){
		return true;
	}

	function _pre_delete($do){
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

	function instalar(){
		if(!$this->datasis->iscampo('invresu','id') ) {
			$this->db->simple_query('ALTER TABLE invresu DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE `invresu` ADD UNIQUE INDEX `mes_codigo` (`mes`, `codigo`);');
			$this->db->simple_query('ALTER TABLE invresu ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};

		if(!$this->datasis->iscampo('invresu','conver') ) {
			$this->db->simple_query('ALTER TABLE invresu ADD COLUMN conver  DECIMAL(20,3) NULL DEFAULT "0.00" COMMENT "CONVERSIONES" AFTER compras');
		};

		if(!$this->datasis->iscampo('invresu','mconver') ) {
			$this->db->simple_query('ALTER TABLE invresu ADD COLUMN mconver DECIMAL(20,3) NULL DEFAULT "0.00" COMMENT "CONVERSIONES" AFTER mcompras');
		};

		if(!$this->datasis->iscampo('invresu','ajuste') ) {
			$this->db->simple_query('ALTER TABLE invresu ADD COLUMN ajuste  DECIMAL(20,3) NULL DEFAULT "0.00" COMMENT "AJUSTES DE INVENTARIO" AFTER trans');
		};

		if(!$this->datasis->iscampo('invresu','majuste') ) {
			$this->db->simple_query('ALTER TABLE invresu ADD COLUMN majuste DECIMAL(20,3) NULL DEFAULT "0.00" COMMENT "AJUSTES DE INVENTARIO" AFTER mtrans ');
		};

	}
}
