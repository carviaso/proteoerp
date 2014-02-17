<?php
class Bconci extends Controller {
	var $mModulo = 'BCONCI';
	var $titp    = 'Conciliaci&oacute;n Bancaria';
	var $tits    = 'Conciliaci&oacute;n Bancaria';
	var $url     = 'finanzas/bconci/';

	function Bconci(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'BCONCI', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->creaintramenu(array('modulo'=>'52D','titulo'=>'Conciliacion Bancaria','mensaje'=>'Conciliacion Bancaria','panel'=>'TESORERIA','ejecutar'=>'finanzas/bconci','target'=>'popu','visible'=>'S','pertenece'=>'5','ancho'=>900,'alto'=>600));
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
		$grid->wbotonadd(array('id'=>'impbtn', 'img' =>'assets/default/images/print.png', 'alt' => 'Imprimir Conciliaci&oacute;n','label'=>'Imprimir Conciliaci&oacute;n'));
		$grid->wbotonadd(array('id'=>'cabtn' , 'img'=>'images/precio.png'               , 'alt' => 'Procesar o reversar consignaci&oacute;n', 'label'=>'Procesar/Reversar conciliaci&oacute;n'));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('BCONCI', 'JQ');
		$param['otros']       = $this->datasis->otros('BCONCI', 'JQ');
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
		$bodyscript = '<script type="text/javascript">';

		$bodyscript .= '
		function bconciadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function bconciedit(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("#newapi'.$grid0.'").getRowData(id);
				if(ret.status=="C"){
					$.prompt("<h1>No se puede modificar una conciliaci&oacute;n cerrada.</h1>");
				}else{
					mId = id;
					$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
						$("#fedita").html(data);
						$("#fedita").dialog( "open" );
					});
				}
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function bconcishow(){
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
		function bconcidel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						try{
							var json = JSON.parse(data);
							if (json.status == "A"){
								$.prompt("Registro eliminado");
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
							}else{
								$.prompt("Registro no se puede eliminado");
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
		$("#impbtn").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = jQuery("'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url('formatos/ver/BCONCI').'/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			} else { $.prompt("<h1>Por favor Seleccione un registro</h1>");}
		});';

		//$bodyscript .= '
		//	$("#impbtn").click( function(){
		//		var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
		//		if(id){
		//			var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
        //
		//			var form = document.createElement("form");
		//			form.setAttribute("method", "post");
		//			form.setAttribute("action", "'.site_url('reportes/ver/BCONCI/search/osp').'");
        //
		//			form.setAttribute("target", "repoconci");
        //
		//			var fecha = document.createElement("input");
		//			fecha.setAttribute("name" , "fecha");
		//			fecha.setAttribute("value", ret.fecha.substr(0,7));
        //
		//			var banco = document.createElement("input");
		//			banco.setAttribute("name" , "codbanc");
		//			banco.setAttribute("value", ret.codbanc);
        //
		//			form.appendChild(fecha);
		//			form.appendChild(banco);
		//			document.body.appendChild(form);
        //
		//			window.open(\''.site_url('reportes/ver/BCONCI').'\', \'repoconci\', \'scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,status=yes\');
        //
		//			form.submit();
		//		}else{
		//			$.prompt("<h1>Por favor Seleccione una Conciliaci&oacute;n.</h1>");
		//		}
		//	});';

		$bodyscript .= '
			$("#cabtn").click( function(){
				var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
				if(id){
					var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
					$.post("'.site_url($this->url.'cstatus').'/"+ret.id,
						function(data){
							try{
								var json = JSON.parse(data);
								if(json.status == "A"){
									jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
								}else{
									$.prompt(json.msj);
								}
							}catch(e){
								$.prompt("Problemas al actualizar, favor intente mas tarde");
							}

						}
					);
				}else{
					$.prompt("<h1>Por favor Seleccione una Conciliaci&oacute;n</h1>");
				}
			});';

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 540, width: 750, modal: true,
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
									$.prompt("Registro Guardado");
									$( "#fedita" ).dialog( "close" );
									grid.trigger("reloadGrid");
									'.$this->datasis->jwinopen(site_url('formatos/ver/BCONCI').'/\'+res.id+\'/id\'').';
									return true;
								} else {
									$.prompt(json.mensaje);
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
			autoOpen: false, height: 400, width: 500, modal: true,
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


		$grid->addField('codbanc');
		$grid->label('Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('numcuent');
		$grid->label('Nro.Cuenta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 180,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:18, maxlength: 18 }',
		));


		$grid->addField('banco');
		$grid->label('Nombre Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('saldoi');
		$grid->label('Saldo inicial');
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


		$grid->addField('saldof');
		$grid->label('Saldo final');
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


		$grid->addField('deposito');
		$grid->label('Dep&oacute;sitos');
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


		$grid->addField('credito');
		$grid->label('N.Cr&eacute;ditos');
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


		$grid->addField('cheque');
		$grid->label('Cheques');
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


		$grid->addField('debito');
		$grid->label('N.D&eacute;bitos');
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


		$grid->addField('status');
		$grid->label('Estatus');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 45,
			'edittype'      => "'text'",
			'align'         => "'center'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 45,
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


		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'align'         => "'center'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
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

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOnSelectRow('
			function(id){ },
			afterInsertRow:
			function(rid, aData, rowe){
				if(aData.status=="A"){
					$(this).jqGrid( "setCell", rid, "fecha","", {color:"#OOOOOO", background:"#FFDD00" });
				}
				if(aData.status=="C" || aData.status==""){
					$(this).jqGrid( "setCell", rid, "fecha","", {color:"#FFFFFF", background:"#337AC8" });
				}
			}
		');

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('BCONCI','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('BCONCI','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('BCONCI','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('BCONCI','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: bconciadd, editfunc: bconciedit, delfunc: bconcidel, viewfunc: bconcishow');

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
		$mWHERE = $grid->geneTopWhere('bconci');

		$response   = $grid->getData('bconci', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
		echo 'Deshabilitado';
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'bconci');
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert', '_pre_insert');
		$edit->pre_process( 'update', '_pre_update');
		$edit->pre_process( 'delete', '_pre_delete');

		$edit->fecha = new dateonlyField('Fecha','fecha','m/Y');
		$edit->fecha->mode = 'autohide';
		$edit->fecha->rule = 'chfecha[m/Y]';
		$edit->fecha->size = 10;
		$edit->fecha->maxlength =8;
		$edit->fecha->insertValue=date('Y-m-d',mktime(0, 0, 0, date('n'),0));
		$edit->fecha->calendar=false;

		$edit->codbanc = new dropdownField('Banco','codbanc');
		$edit->codbanc->style= 'width:480px';
		$edit->codbanc->mode = 'autohide';
		$edit->codbanc->rule = 'required';
		$edit->codbanc->option('','Seleccionar');
		$edit->codbanc->options("SELECT TRIM(codbanc) AS codbanc,CONCAT_WS(' ',codbanc,banco,numcuent) AS desca FROM banc WHERE tbanco<>'CAJ'");

		$edit->saldoi = new inputField('Saldo Inicial','saldoi');
		$edit->saldoi->rule='numeric|required';
		$edit->saldoi->insertValue='0.0';
		$edit->saldoi->css_class='inputnum';
		$edit->saldoi->onkeyup='totalizar()';
		$edit->saldoi->size =15;
		$edit->saldoi->maxlength =18;

		$edit->saldof = new inputField('Saldo Final','saldof');
		$edit->saldof->insertValue='0.0';
		$edit->saldof->rule='numeric|required';
		$edit->saldof->css_class='inputnum';
		$edit->saldof->onkeyup='totalizar()';
		$edit->saldof->size =15;
		$edit->saldof->maxlength =18;

		$edit->deposito = new inputField('Dep&oacute;sitos','deposito');
		$edit->deposito->rule = 'numeric';
		$edit->deposito->type = 'inputhidden';
		$edit->deposito->insertValue='0.0';
		$edit->deposito->css_class='inputnum';
		$edit->deposito->size =20;
		$edit->deposito->maxlength =18;

		$edit->credito = new inputField('Notas de Cr&eacute;dito','credito');
		$edit->credito->rule='numeric';
		$edit->credito->type = 'inputhidden';
		$edit->credito->insertValue='0.0';
		$edit->credito->css_class='inputnum';
		$edit->credito->size =20;
		$edit->credito->maxlength =18;

		$edit->cheque = new inputField('Cheques','cheque');
		$edit->cheque->rule='numeric';
		$edit->cheque->type = 'inputhidden';
		$edit->cheque->insertValue='0.0';
		$edit->cheque->css_class='inputnum';
		$edit->cheque->size =20;
		$edit->cheque->maxlength =18;

		$edit->debito = new inputField('Notas de D&eacute;bito','debito');
		$edit->debito->rule='numeric';
		$edit->debito->type = 'inputhidden';
		$edit->debito->insertValue='0.0';
		$edit->debito->css_class='inputnum';
		$edit->debito->size =20;
		$edit->debito->maxlength =18;

		$edit->cdeposito = new inputField('Dep&oacute;sitos','cdeposito');
		$edit->cdeposito->rule = 'numeric';
		$edit->cdeposito->insertValue='0.0';
		$edit->cdeposito->css_class='inputnum';
		$edit->cdeposito->onkeyup='totalizar()';
		$edit->cdeposito->size =12;
		$edit->cdeposito->maxlength =18;

		$edit->ccredito = new inputField('Notas de Cr&eacute;dito','ccredito');
		$edit->ccredito->rule='numeric';
		$edit->ccredito->insertValue='0.0';
		$edit->ccredito->css_class='inputnum';
		$edit->ccredito->onkeyup='totalizar()';
		$edit->ccredito->size =12;
		$edit->ccredito->maxlength =18;

		$edit->ccheque = new inputField('Cheques','ccheque');
		$edit->ccheque->rule='numeric';
		$edit->ccheque->insertValue='0.0';
		$edit->ccheque->css_class='inputnum';
		$edit->ccheque->onkeyup='totalizar()';
		$edit->ccheque->size =12;
		$edit->ccheque->maxlength =18;

		$edit->cdebito = new inputField('Notas de D&eacute;bito','cdebito');
		$edit->cdebito->rule='numeric';
		$edit->cdebito->insertValue='0.0';
		$edit->cdebito->css_class='inputnum';
		$edit->cdebito->onkeyup='totalizar()';
		$edit->cdebito->size =12;
		$edit->cdebito->maxlength =18;

		//$edit->status = new inputField('Estatus','status');
		//$edit->status->rule='';
		//$edit->status->size =3;
		//$edit->status->maxlength =1;

		$edit->usuario = new autoUpdateField('usuario',$this->secu->usuario(),$this->secu->usuario());
		$edit->estampa = new autoUpdateField('estampa',date('Ymd')  , date('Ymd'));
		$edit->hora    = new autoUpdateField('hora'   ,date('H:i:s'), date('H:i:s'));

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
			return true;
		}

		if($edit->on_error()){
			$rt=array(
				'status' =>'B',
				'mensaje'=>preg_replace('/<[^>]*>/', '', $edit->error_string),
				'pk'     =>null,
			);
			echo json_encode($rt);
			$act = false;
			return true;
		}

		if($edit->on_show()){
			$conten['form'] =&  $edit;
			$this->load->view('view_bconci', $conten);
		}
	}

	function localizador($id=null){
		if(!empty($id)){
			$dbid      = $this->db->escape($id);
			$transac   = $this->datasis->dameval("SELECT transac FROM bmov WHERE id=${dbid}");
			$dbtransac = $this->db->escape($transac);

			$mSQL='SELECT cod_cli, nombre,tipo_doc,numero FROM smov WHERE transac='.$dbtransac;
			$query = $this->db->query($mSQL);
			foreach ($query->result() as $i=>$row){
				if($i==0){
					echo 'Movimiento de cliente ('.$row->cod_cli.') '.$row->nombre.'<br>';
				}
				echo ' <b>'.trim($row->tipo_doc).'</b>-'.trim($row->numero).'<br>';
			}

			$mSQL='SELECT cod_cli, nombre,tipo_doc,numero FROM sfac WHERE transac='.$dbtransac;
			$query = $this->db->query($mSQL);
			foreach ($query->result() as $i=>$row){
				if($i==0){
					echo 'Facturaci&oacute;n ('.$row->cod_cli.') '.$row->nombre.'<br>';
				}
				echo ' <b>'.trim($row->tipo_doc).'</b>-'.trim($row->numero).'<br>';
			}

			$mSQL='SELECT cod_prv, nombre,tipo_doc,numero FROM sprm WHERE transac='.$dbtransac;
			$query = $this->db->query($mSQL);
			foreach ($query->result() as $i=>$row){
				if($i==0){
					echo 'Movimiento de proveedor ('.$row->cod_prv.') '.$row->nombre.'<br>';
				}
				echo ' <b>'.trim($row->tipo_doc).'</b>-'.trim($row->numero).'<br>';
			}

			$mSQL='SELECT cod_cli, nombre,tipo_doc,numero FROM otin WHERE transac='.$dbtransac;
			$query = $this->db->query($mSQL);
			foreach ($query->result() as $i=>$row){
				if($i==0){
					echo 'Otro ingreso ('.$row->cod_cli.') '.$row->nombre.'<br>';
				}
				echo ' <b>'.trim($row->tipo_doc).'</b>-'.trim($row->numero).'<br>';
			}

			$mSQL='SELECT tipo,numero FROM bcaj WHERE transac='.$dbtransac;
			$query = $this->db->query($mSQL);
			foreach ($query->result() as $i=>$row){
				if($i==0){
					echo 'Movimiento de caja<br>';
				}
				echo ' <b>'.trim($row->tipo).'</b>-'.trim($row->numero).'<br>';
			}
			echo '<p style="text-align:center;font-size:0.9em">Transacci&oacute;n: '.$transac.'</p>';
		}
	}

	function concilia(){
		session_write_close();
		$id    = $this->input->post('id');
		$fecha = $this->input->post('fecha');
		$act   = $this->input->post('act');
		$afectados=0;

		$rt=array('status'=>'B', 'msj' => 'Problema al conciliar el efecto, intente mas tarde.' );

		if($fecha !==false && $id !==false && $act !== false){
			$dbid    = $this->db->escape($id);
			$act     = ($act=='false')? false : true;
			$arr_fec = explode('/',$fecha);
			$ffecha  = date('Y-m-d', mktime(0, 0, 0,$arr_fec[0]+1, 0,$arr_fec[1]));

			if($act){
				$factor     = 1;
				$concilia   = $ffecha;
				$dbconcilia = $this->db->escape($concilia);
				$dbstatus   = '\'û\'';
			}else{
				$factor     = -1;
				//$dbconcilia = 'NULL';
				$dbconcilia = '\'0000-00-00\'';
				$dbstatus   = '\'P\'';
			}

			$row = $this->datasis->damerow("SELECT tipo_op,codbanc,monto FROM bmov WHERE id = ${dbid}");
			if(!empty($row)){
				$ittipo   = $row['tipo_op'];
				$dbcodbanc= $this->db->escape($row['codbanc']);
				$dbffecha = $this->db->escape($ffecha);
				$monto    = $factor*$row['monto'];

				$status = $this->datasis->dameval("SELECT status FROM bconci WHERE fecha=${dbffecha} AND codbanc=${dbcodbanc}");
				if($status!='C'){
					if($ittipo=='NC'){
						$campo ='credito' ;
					}elseif($ittipo=='ND'){
						$campo ='debito'  ;
					}elseif($ittipo=='CH'){
						$campo ='cheque'  ;
					}elseif($ittipo=='DE'){
						$campo ='deposito';
					}else{
						$rt['status'] = 'B';
						echo json_encode($rt);
						return false;
					}
					$mSQL = "UPDATE bconci SET ${campo}=${campo}+(${monto}) WHERE fecha=${dbffecha} AND codbanc=${dbcodbanc}";
					$ban=$this->db->simple_query($mSQL);
					if(!$ban){
						$rt['msj'] = 'Problema al actualizar la conciliacion, intente mas tarde.';
						echo json_encode($rt);
						return false;
					}else{
						$afectados=$this->db->affected_rows();
					}
				}else{
					$rt['msj'] = 'Conciliacion cerrada.';
					echo json_encode($rt);
					return false;
				}
			}

			$mSQL = "UPDATE bmov SET concilia=${dbconcilia}, status=${dbstatus} WHERE id = ${dbid}";
			$ban=$this->db->simple_query($mSQL);
			if($ban){
				$rt['msj']    = '';
				$rt['status'] = 'A';
			}else{
				if($afectados>0){
					$mSQL = "UPDATE bconci SET ${campo}=${campo}-(${monto}) WHERE fecha=${dbffecha} AND codbanc=${dbcodbanc}";
					$ban=$this->db->simple_query($mSQL);
				}
			}
		}

		echo json_encode($rt);
	}

	function _pre_insert($do){
		$codbanc  = $do->get('codbanc');
		$dbcodbanc= $this->db->escape($codbanc);
		$fecha    = $do->get('fecha');
		$fecha    = substr($fecha,0,6).days_in_month(substr($fecha,4,2),substr($fecha,0,4));
		$do->set('fecha',$fecha);

		$dbfecha  = $this->db->escape($fecha);
		$ant = intval($this->datasis->dameval('SELECT COUNT(*) AS cana FROM bconci WHERE codbanc='.$dbcodbanc.' AND fecha='.$dbfecha));
		if($ant>0){
			$do->error_message_ar['pre_ins']='Ya existe una conciliacion con esa fecha para el mismo banco.';
			return false;
		}

		return $this->_pre_inserup($do);
	}

	function _pre_update($do){
		$id    = $do->get('id');
		$status= $this->datasis->dameval('SELECT status FROM bconci WHERE id='.$id);
		if($status!='C'){
			return $this->_pre_inserup($do);
		}else{
			$do->error_message_ar['pre_upd']='Conciliacion ya fue cerrada, no se puede modificar.';
			return false;
		}
	}

	function _pre_inserup($do){
		$do->set('status','A');
		$codbanc  = $do->get('codbanc');
		$fecha    = $do->get('fecha');

		$dbcodbanc= $this->db->escape($codbanc);
		$row = $this->datasis->damerow('SELECT numcuent,banco FROM banc WHERE codbanc='.$dbcodbanc);
		if(!empty($row)){
			$do->set('numcuent',$row['numcuent']);
			$do->set('banco'   ,$row['banco']);
		}else{
			$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='Banco no valido';
			return false;
		}

		$cana=$conciliado=$nc=$nd=$ch=$de=0;
		//$this->mSQLs=array();
		foreach($_POST as $ind=>$val){
			if (preg_match("/^itid_(?P<con>\d+)$/", $ind,$matches) && $val>0) {
				$con    = $matches['con'];
				$ittipo = $this->input->post('ittipo_'.$con);
				$itmonto= $this->input->post('itmonto_'.$con);
				if($ittipo===false || $itmonto===false){
					$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='Error en la data.';
					return false;
				}

				//Calcula el monto conciliado
				if($ittipo=='CH' || $ittipo=='ND'){
					$conciliado += $itmonto;
				}else{
					$conciliado -= $itmonto;
				}

				if($ittipo=='NC'){
					$nc+=$itmonto;
				}elseif($ittipo=='ND'){
					$nd+=$itmonto;
				}elseif($ittipo=='CH'){
					$ch+=$itmonto;
				}elseif($ittipo=='DE'){
					$de+=$itmonto;
				}else{
					$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']="Tipo de transaccion ${ittipo} no valido.";
					return false;
				}

				//$dbval=$this->db->escape($val);
				//$this->mSQLs[] = "UPDATE bmov SET concilia=${dbfecha} WHERE id=${dbval}";
				$cana++;
			}
		}

		if($cana==0){
			$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='Necesita seleccionar al menos un efecto.';
			return false;
		}

		$do->set('credito' ,$nc);
		$do->set('debito'  ,$nd);
		$do->set('cheque'  ,$ch);
		$do->set('deposito',$de);

		return true;
	}

	function _pre_delete($do){
		$status=$do->get('status');
		if($status=='C'){
			$do->error_message_ar['pre_del']='No se puede eliminar una conciliacion cerrada.';
			return false;
		}
		return true;
	}

	function _post_insert($do){
		$fecha = $do->get('fecha');
		//foreach($this->mSQLs AS $mSQL){
		//	$this->db->simple_query($mSQL);
		//}

		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary fecha ${fecha}");
	}

	function _post_update($do){
		$fecha = $do->get('fecha');
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary fecha ${fecha}");
	}

	function _post_delete($do){
		$fecha    = $do->get('fecha');
		$codbanc  = $do->get('codbanc');
		$dbfecha  = $this->db->escape($fecha);
		$dbcodbanc= $this->db->escape($codbanc);

		$mSQL= 'UPDATE bmov SET concilia=\'0000-00-00\', status=\'P\' WHERE concilia='.$dbfecha.' AND codbanc='.$dbcodbanc;
		$this->db->simple_query($mSQL);

		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits ${primary} fecha ${fecha}");
	}

	function cstatus($id){
		$dbid = $this->db->escape($id);
		$row  = $this->datasis->damerow('SELECT status,saldoi, saldof,fecha,codbanc FROM bconci WHERE id='.$dbid);

		$rt=array('msj'=>'', 'status'=>'B');
		if(empty($row)){
			$rt['msj']    = 'Registro no encontrado';
			$rt['status'] = 'B';
			echo json_encode($rt);
			return true;
		}
		$status  = $row['status'];
		$dbcodbanc = $this->db->escape($row['codbanc']);
		$arrfecha  = explode('-',$row['fecha']);

		$dbfantes  = $this->db->escape(date('Y-m-d',mktime(0, 0, 0, $arrfecha[1]  , 0, $arrfecha[0])));
		$dbfdespu  = $this->db->escape(date('Y-m-d',mktime(0, 0, 0, $arrfecha[1]+2, 0, $arrfecha[0])));

		//$mSQL = "SELECT saldof FROM bconci WHERE codbanc=${dbcodban} AND fecha=${dbfantes}";
		//$mSQL = "SELECT saldoi FROM bconci WHERE codbanc=${dbcodban} AND fecha=${dbfdespu}";

		if($status=='A'){
			$dbcstatus='\'C\'';
		}else{
			$dbcstatus='\'A\'';
		}

		$mSQL = "UPDATE bconci SET status=${dbcstatus} WHERE id = ${dbid}";
		$ban=$this->db->simple_query($mSQL);
		if($ban){
			$rt['msj']    = '';
			$rt['status'] = 'A';

			$accion = ($status=='A')? 'CERRADA' : 'ABIERTA';
			logusu('bconci',"CONCILIACION ${id} ${accion}");
		}else{
			$rt['msj']    = '';
			$rt['status'] = 'B';
		}
		echo json_encode($rt);
		return true;

	}

	function instalar(){
		if(!$this->db->table_exists('bconci')){
			$mSQL="CREATE TABLE `bconci` (
				`fecha` DATE NULL DEFAULT NULL,
				`codbanc` CHAR(2) NULL DEFAULT NULL,
				`numcuent` VARCHAR(18) NULL DEFAULT NULL,
				`banco` VARCHAR(30) NULL DEFAULT NULL,
				`saldoi` DECIMAL(18,2) NULL DEFAULT '0',
				`saldof` DECIMAL(18,2) NULL DEFAULT '0',
				`deposito` DECIMAL(18,2) NULL DEFAULT '0',
				`credito` DECIMAL(18,2) NULL DEFAULT '0',
				`cheque` DECIMAL(18,2) NULL DEFAULT '0',
				`debito` DECIMAL(18,2) NULL DEFAULT '0',
				`cdeposito` DECIMAL(18,2) NULL DEFAULT '0',
				`ccredito` DECIMAL(18,2) NULL DEFAULT '0',
				`ccheque` DECIMAL(18,2) NULL DEFAULT '0',
				`cdebito` DECIMAL(18,2) NULL DEFAULT '0',
				`status` CHAR(1) NULL DEFAULT 'A',
				`usuario` VARCHAR(4) NULL DEFAULT NULL,
				`estampa` DATE NULL DEFAULT NULL,
				`hora` VARCHAR(8) NULL DEFAULT NULL,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `fecha_codbanc` (`fecha`, `codbanc`),
				INDEX `fecha` (`fecha`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('bconci');
		if(!in_array('cdeposito',$campos)){
			$mSQL="ALTER TABLE `bconci`
			ADD COLUMN `cdeposito` DECIMAL(18,2) NULL DEFAULT NULL AFTER `debito`,
			ADD COLUMN `ccredito` DECIMAL(18,2) NULL DEFAULT NULL AFTER `cdeposito`,
			ADD COLUMN `ccheque` DECIMAL(18,2) NULL DEFAULT NULL AFTER `ccredito`,
			ADD COLUMN `cdebito` DECIMAL(18,2) NULL DEFAULT NULL AFTER `ccheque`";
			$this->db->simple_query($mSQL);
		}
	}
}
