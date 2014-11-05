<?php
class Edinmue extends Controller {
	var $mModulo = 'EDINMUE';
	var $titp    = 'INMUEBLES';
	var $tits    = 'INMUEBLES';
	var $url     = 'construccion/edinmue/';

	function Edinmue(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->instalar();
		$this->datasis->modulo_nombre( 'EDINMUE', $ventana=0 );
	}

	function index(){
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
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
		$grid->wbotonadd(array("id"=>"galicuota", "img"=>"images/pdf_logo.gif",  "alt" => "Alicuotas", "label"=>"Alicuotas"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'falicu',  'title'=>'Agregar/Editar Alicuota'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('EDINMUE', 'JQ');
		$param['otros']       = $this->datasis->otros('EDINMUE', 'JQ');
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
		$bodyscript = '<script type="text/javascript">';
		$ngrid = '#newapi'.$grid0;

		$bodyscript .= $this->jqdatagrid->bsshow('edinmue', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'edinmue', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'edinmue', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('edinmue', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '450', '570' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

		$bodyscript .= '});';

		$bodyscript .= '
		jQuery("#galicuota").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				$.post("'.site_url('construccion/edalicuota/dataefla/create').'/"+id, function(data){
					$("#falicu").html(data);
					$("#falicu").dialog( "open" );
				})
			} else { $.prompt("<h1>Por favor Seleccione un Inmueble</h1>");}
		});';

		$bodyscript .= '
		$("#falicu").dialog({
			autoOpen: false, height: 200, width: 400, modal: true,
			buttons: {
				"Guardar": function() {
					var vurl = $("#df1").attr("action");
					$.ajax({
						type: "POST", dataType: "html", async: false,
						url: vurl,
						data: $("#df1").serialize(),
						success: function(r,s,x){
							try{
								var json = JSON.parse(r);
								if (json.status == "A"){
									//$.prompt("<h1>Registro Guardado</h1>");
									$( "#falicu" ).dialog( "close" );
									idvisita = json.pk.id;
									return true;
								} else {
									$.prompt(json.mensaje);
								}
							} catch(e) {
								$("#falicu").html(r);
							}
						}
					})
				},
				"Guardar y Seguir": function(){
					var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
					var vurl = $("#df1").attr("action");
					$.ajax({
						type: "POST", dataType: "html", async: false,
						url: vurl,
						data: $("#df1").serialize(),
						success: function(r,s,x){
							try{
								var json = JSON.parse(r);
								if (json.status == "A"){
									$.prompt("<h1>Registro Guardado con exito</h1>");
									idalicu = json.pk.id;
									$.post("'.site_url('construccion/edialicuota/dataefla').'/create/"+id+"/"+idalicu,
									function(data){
										$("#falicu").html(data);
									});
									return true;
								} else {
									$.prompt(json.mensaje);
								}
							} catch(e) {
								$("#falicu").html(r);
							}
						}
					})				
				},
				"Cancelar": function() {
					$("#falicu").html("");
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				$("#falicu").html("");
			}
		});
		';

		$bodyscript .= '
		function elialicu(id){
			$.prompt("<h1>Eliminar alicuota</h1>", {
				buttons: { Eliminar: true, Cancelar: false },
				submit: function(e,v,m,f){
					if (v) {
						$.ajax({ url: "'.site_url('construccion/edalicuota/elimina').'/"+id,
							complete: function(){ 
								alert("Alicuota Eliminada");
							}
						});
					}
				}
			});
		}
		';


		$bodyscript .= '</script>';

		return $bodyscript;
	}

	//******************************************************************
	// Definicion del Grid o Tabla 
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'hidden'        => 'true',
			'frozen'        => 'true',
			'width'         => 30,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));

		$grid->addField('aplicacion');
		$grid->label('Aplic');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

		$grid->addField('descripcion');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->addField('edificacion');
		$grid->label('Edificacion');
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


		$grid->addField('uso');
		$grid->label('Uso');
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


		$grid->addField('usoalter');
		$grid->label('Usoalter');
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


		$grid->addField('ubicacion');
		$grid->label('Ubicacion');
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


		$grid->addField('caracteristicas');
		$grid->label('Caracteristicas');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('area');
		$grid->label('Area');
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


		$grid->addField('estaciona');
		$grid->label('Estaciona');
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


		$grid->addField('deposito');
		$grid->label('Deposito');
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


		$grid->addField('preciomt2e');
		$grid->label('Preciomt2e');
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


		$grid->addField('preciomt2c');
		$grid->label('Preciomt2c');
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


		$grid->addField('preciomt2a');
		$grid->label('Preciomt2a');
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


		$grid->addField('objeto');
		$grid->label('Objeto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('status');
		$grid->label('Status');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
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
				$.ajax({
					url: "'.site_url('construccion/edalicuota').'/tabla/"+id,
					success: function(msg){
						$("#ladicional").html(msg);
					}
				});
			}}
		');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('EDINMUE','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('EDINMUE','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('EDINMUE','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('EDINMUE','BUSQUEDA%'));

		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: edinmueadd, editfunc: edinmueedit, delfunc: edinmuedel, viewfunc: edinmueshow");

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
		$mWHERE = $grid->geneTopWhere('edinmue');

		$response   = $grid->getData('edinmue', array(array()), array(), false, $mWHERE, 'codigo' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion del Grid o Tabla
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
				$check = $this->datasis->dameval("SELECT count(*) FROM edinmue WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('edinmue', $data);
					echo "Registro Agregado";

					logusu('EDINMUE',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM edinmue WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM edinmue WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE edinmue SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("edinmue", $data);
				logusu('EDINMUE',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('edinmue', $data);
				logusu('EDINMUE',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM edinmue WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM edinmue WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM edinmue WHERE id=$id ");
				logusu('EDINMUE',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	// Edicion 

	function dataedit(){
		$this->rapyd->load('dataedit');

		$link1=site_url('construccion/common/get_ubic');
		$script ='
		$(function() {
			$("#edificacion").change(function(){ edif_change(); });
			$(".inputnum").numeric(".");
			$(".inputonlynum").numeric();
		});

		function edif_change(){
			$.post("'.$link1.'",{ edif:$("#edificacion").val() }, function(data){ $("#ubicacion").html(data);})
		}
		';


		$edit = new DataEdit('', 'edinmue');
		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		//$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert');
		$edit->pre_process( 'update','_pre_update');
		$edit->pre_process( 'delete','_pre_delete');

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->rule='max_length[15]|unique';
		$edit->codigo->size =17;
		$edit->codigo->maxlength =15;

		$edit->aplicacion = new dropdownField('Aplicacion','aplicacion');
		$edit->aplicacion->option('','Seleccionar');
		$edit->aplicacion->options('SELECT depto, CONCAT(depto," ",descrip) descrip FROM dpto WHERE tipo="G" AND depto NOT IN ("CO","GP") ORDER BY depto');
		$edit->aplicacion->rule='max_length[11]';
		$edit->aplicacion->style='width:150px;';

		$edit->descripcion = new inputField('Descripci&oacute;n','descripcion');
		$edit->descripcion->rule='max_length[100]';
		$edit->descripcion->maxlength =100;

		$edit->objeto = new dropdownField('Objeto','objeto');
		$edit->objeto->option('','Seleccionar');
		$edit->objeto->option('A','Alquiler');
		$edit->objeto->option('V','Venta');
		$edit->objeto->rule='max_length[1]|required';
		$edit->objeto->style='width:150px;';

		$edit->status = new dropdownField('Estatus','status');
		$edit->status->option('D','Disponible');
		$edit->status->option('A','Alquilado');
		$edit->status->option('V','Vendido');
		$edit->status->option('R','Reservado');
		$edit->status->option('O','Otro');
		$edit->status->rule='max_length[11]';
		$edit->status->style='width:150px;';

		$edit->edificacion = new dropdownField('Edificaci&oacute;n','edificacion');
		$edit->edificacion->option('','Seleccionar');
		$edit->edificacion->options('SELECT id,TRIM(nombre) AS nombre FROM edif ORDER BY nombre');
		$edit->edificacion->rule='max_length[11]';
		$edit->edificacion->style='width:150px;';

		$edit->ubicacion = new dropdownField('Ubicaci&oacute;n','ubicacion');
		$edit->ubicacion->rule='max_length[11]|integer';
		$edit->ubicacion->style='width:150px;';
		$edif=$edit->getval('edificacion');
		if($edif!==false){
			$dbedif=$this->db->escape($edif);
			$edit->ubicacion->option('','Seleccionar');
			$edit->ubicacion->options("SELECT id,descripcion FROM `edifubica` WHERE id_edif=$dbedif ORDER BY descripcion");
		}else{
			$edit->ubicacion->option('','Seleccione una edificacion');
		}

		$edit->uso = new dropdownField('Uso','uso');
		$edit->uso->option('','Seleccionar');
		$edit->uso->options('SELECT id,uso FROM `eduso` ORDER BY uso');
		$edit->uso->rule='max_length[11]|required';
		$edit->uso->style='width:150px;';

		$edit->usoalter = new dropdownField('Uso Alternativo','usoalter');
		$edit->usoalter->option('','Seleccionar');
		$edit->usoalter->options('SELECT id,uso FROM `eduso` ORDER BY uso');
		$edit->usoalter->rule='max_length[11]';
		$edit->usoalter->style='width:150px;';

		$edit->caracteristicas = new textareaField('Caracter&iacute;sticas','caracteristicas');
		//$edit->caracteristicas->rule='max_length[8]';
		$edit->caracteristicas->cols = 65;
		$edit->caracteristicas->rows = 2;

		$edit->area = new inputField('&Aacute;rea Mt2','area');
		$edit->area->rule='max_length[15]|numeric';
		$edit->area->css_class='inputnum';
		$edit->area->size =10;
		//$edit->area->maxlength =15;

		$edit->estaciona = new inputField('Estacionamiento','estaciona');
		$edit->estaciona->rule='max_length[10]|integer';
		$edit->estaciona->size =15;
		$edit->estaciona->css_class='inputonlynum';
		$edit->estaciona->maxlength =10;

		$edit->deposito = new inputField('Dep&oacute;sito','deposito');
		$edit->deposito->rule='max_length[11]|integer';
		$edit->deposito->size =15;
		$edit->deposito->maxlength =11;
		$edit->deposito->css_class='inputonlynum';

		$edit->preciomt2e = new inputField('Precio x mt2 (Contado)','preciomt2e');
		$edit->preciomt2e->rule='max_length[15]|numeric';
		$edit->preciomt2e->css_class='inputnum';
		$edit->preciomt2e->size =15;
		$edit->preciomt2e->maxlength =15;

		$edit->preciomt2c = new inputField('Precio x mt2 (Cr&eacute;dito)','preciomt2c');
		$edit->preciomt2c->rule='max_length[15]|numeric';
		$edit->preciomt2c->css_class='inputnum';
		$edit->preciomt2c->size =15;
		$edit->preciomt2c->maxlength =15;

		$edit->preciomt2a = new inputField('Precio x mt2 (Alquiler)','preciomt2');
		$edit->preciomt2a->rule='max_length[15]|numeric';
		$edit->preciomt2a->css_class='inputnum';
		$edit->preciomt2a->size =15;
		$edit->preciomt2a->maxlength =15;

		$edit->alicuota = new inputField('Alicuota %','alicuota');
		$edit->alicuota->rule='max_length[15]|numeric';
		$edit->alicuota->css_class='inputnum';
		$edit->alicuota->size =15;

		$edit->propietario = new inputField('Propietario','propietario');
		$edit->propietario->rule='';
		$edit->propietario->size =7;
		$edit->propietario->maxlength =5;

		$edit->ocupante = new inputField('Ocupante','ocupante');
		$edit->ocupante->rule='';
		$edit->ocupante->size =7;
		$edit->ocupante->maxlength =5;

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back','add');
		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form']  =&  $edit;
			$data['content']  =  $this->load->view('view_edinmue', $conten, false);
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
		if (!$this->db->table_exists('edinmue')) {
			$mSQL="
			CREATE TABLE edinmue (
				id              INT(11)   NOT NULL AUTO_INCREMENT,
				codigo          CHAR(15)  DEFAULT NULL,
				aplicacion      CHAR(2)   NULL DEFAULT NULL,
				descripcion     CHAR(100) DEFAULT NULL,
				edificacion     INT(11)   DEFAULT NULL,
				uso             INT(11)   DEFAULT NULL,
				usoalter        INT(11)   DEFAULT NULL,
				ubicacion       INT(11)   DEFAULT NULL,
				caracteristicas TEXT,
				area            DECIMAL(15,2) DEFAULT NULL,
				estaciona       INT(10)   DEFAULT NULL,
				deposito        INT(11)   DEFAULT NULL,
				preciomt2e      DECIMAL(15,2) DEFAULT NULL,
				preciomt2c      DECIMAL(15,2) DEFAULT NULL,
				preciomt2a      DECIMAL(15,2) DEFAULT NULL,
				objeto          CHAR(1) NOT NULL,
				status          CHAR(1) NOT NULL COMMENT 'Alquilado, Vendido, Reservado, Otro',
			  PRIMARY KEY (id),
			  UNIQUE KEY codigo (codigo)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Facilidades'";
			$this->db->query($mSQL);
		}
		$campos = $this->db->list_fields('edinmue');
		if(!in_array('aplicacion',$campos)) $this->db->query('ALTER TABLE edinmue ADD COLUMN aplicacion CHAR(2) NULL DEFAULT NULL AFTER codigo');


	}
}

/*
class edinmue extends Controller {
	var $titp='Inmuebles';
	var $tits='Inmuebles';
	var $url ='construccion/edinmue/';

	function edinmue(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('A03',1);
		$this->instalar();
	}

	function index(){
		redirect($this->url.'filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp);
		$sel=array('a.id','a.codigo','a.descripcion','a.edificacion','c.uso','d.uso AS usoalter','e.descripcion AS ubicacion','a.caracteristicas','a.area','a.estaciona','a.deposito','b.nombre');

		$filter->db->select($sel);
		$filter->db->from('edinmue AS a');
		$filter->db->join('edif  AS b','a.edificacion=b.id');
		$filter->db->join('eduso AS c','a.uso=c.id');
		$filter->db->join('eduso AS d','a.usoalter=d.id','left');
		$filter->db->join('edifubica AS e','a.ubicacion=e.id AND a.edificacion=e.id_edif');

		$filter->codigo = new inputField('C&oacute;digo','codigo');
		$filter->codigo->rule      ='max_length[15]';
		$filter->codigo->size      =17;
		$filter->codigo->maxlength =15;

		$filter->objeto = new dropdownField('Objeto','objeto');
		$filter->objeto->option('','Todos');
		$filter->objeto->option('A','Alquiler');
		$filter->objeto->option('V','Venta');

		$filter->descripcion = new inputField('Descripci&oacute;n','descripcion');
		$filter->descripcion->rule      ='max_length[100]';
		$filter->descripcion->maxlength =100;

		$filter->edificacion = new dropdownField('Edificaci&oacute;n','edificacion');
		$filter->edificacion->option('','Seleccionar');
		$filter->edificacion->options('SELECT id,TRIM(nombre) AS nombre FROM edif ORDER BY nombre');

		$filter->uso = new dropdownField('Uso','uso');
		$filter->uso->option('','Todos');
		$filter->uso->options('SELECT id,uso FROM `eduso` ORDER BY uso');

		$filter->ubicacion = new dropdownField('Ubicaci&oacute;n','ubicacion');
		$filter->ubicacion->option('','Seleccionar');
		$filter->ubicacion->options('SELECT id,descripcion FROM `edifubica` ORDER BY descripcion');

		$filter->status = new dropdownField('Estatus','status');
		$filter->status->option('D','Disponible');
		$filter->status->option('A','Alquilado');
		$filter->status->option('D','Vendido');
		$filter->status->option('R','Reservado');
		$filter->status->option('O','Otro');

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Id',$uri,'id','align="left"');
		$grid->column_orderby('C&oacute;digo','codigo','codigo','align="left"');
		$grid->column_orderby('Descripci&oacute;n','descripcion','descripcion','align="left"');
		$grid->column_orderby('Edificaci&oacute;n','nombre','nombre');
		$grid->column_orderby('Uso','uso','uso');
		$grid->column_orderby('Uso alterno','usoalter','usoalter');
		$grid->column_orderby('Ubicaci&oacute;n','ubicacion','ubicacion');
		$grid->column_orderby('&Aacute;rea'     ,'<nformat><#area#></nformat>','area','align="right"');
		$grid->column_orderby('Estacionamiento' ,'estaciona','estaciona','align="right"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = heading($this->titp);
		$this->load->view('view_ventanas', $data);

	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'edinmue');

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert');
		$edit->pre_process( 'update','_pre_update');
		$edit->pre_process( 'delete','_pre_delete');

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->rule='max_length[15]|unique';
		$edit->codigo->size =17;
		$edit->codigo->maxlength =15;

		$edit->descripcion = new inputField('Descripci&oacute;n','descripcion');
		$edit->descripcion->rule='max_length[100]';
		$edit->descripcion->maxlength =100;

		$edit->objeto = new dropdownField('Objeto','objeto');
		$edit->objeto->option('','Seleccionar');
		$edit->objeto->option('A','Alquiler');
		$edit->objeto->option('V','Venta');
		$edit->objeto->rule='max_length[1]|required';

		$edit->status = new dropdownField('Estatus','status');
		$edit->status->option('D','Disponible');
		$edit->status->option('A','Alquilado');
		$edit->status->option('V','Vendido');
		$edit->status->option('R','Reservado');
		$edit->status->option('O','Otro');
		$edit->status->rule='max_length[11]';

		$edit->edificacion = new dropdownField('Edificaci&oacute;n','edificacion');
		$edit->edificacion->option('','Seleccionar');
		$edit->edificacion->options('SELECT id,TRIM(nombre) AS nombre FROM edif ORDER BY nombre');
		$edit->edificacion->rule='max_length[11]';

		$edit->ubicacion = new dropdownField('Ubicaci&oacute;n','ubicacion');
		$edit->ubicacion->rule='max_length[11]|integer';
		$edif=$edit->getval('edificacion');
		if($edif!==false){
			$dbedif=$this->db->escape($edif);
			$edit->ubicacion->option('','Seleccionar');
			$edit->ubicacion->options("SELECT id,descripcion FROM `edifubica` WHERE id_edif=$dbedif ORDER BY descripcion");
		}else{
			$edit->ubicacion->option('','Seleccione una edificacion');
		}

		$edit->uso = new dropdownField('Uso','uso');
		$edit->uso->option('','Seleccionar');
		$edit->uso->options('SELECT id,uso FROM `eduso` ORDER BY uso');
		$edit->uso->rule='max_length[11]|required';

		$edit->usoalter = new dropdownField('Uso Alternativo','usoalter');
		$edit->usoalter->option('','Seleccionar');
		$edit->usoalter->options('SELECT id,uso FROM `eduso` ORDER BY uso');
		$edit->usoalter->rule='max_length[11]';

		$edit->caracteristicas = new textareaField('Caracter&iacute;sticas','caracteristicas');
		//$edit->caracteristicas->rule='max_length[8]';
		$edit->caracteristicas->cols = 70;
		$edit->caracteristicas->rows = 4;

		$edit->area = new inputField('&Aacute;rea Mt2','area');
		$edit->area->rule='max_length[15]|numeric';
		$edit->area->css_class='inputnum';
		$edit->area->size =10;
		//$edit->area->maxlength =15;

		$edit->estaciona = new inputField('Estacionamiento','estaciona');
		$edit->estaciona->rule='max_length[10]|integer';
		$edit->estaciona->size =10;
		$edit->estaciona->css_class='inputonlynum';
		$edit->estaciona->maxlength =10;

		$edit->deposito = new inputField('Dep&oacute;sito','deposito');
		$edit->deposito->rule='max_length[11]|integer';
		$edit->deposito->size =10;
		$edit->deposito->maxlength =11;
		$edit->deposito->css_class='inputonlynum';

		$edit->preciomt2e = new inputField('Precio x mt2 (Contado)','preciomt2e');
		$edit->preciomt2e->rule='max_length[15]|numeric';
		$edit->preciomt2e->css_class='inputnum';
		$edit->preciomt2e->size =10;
		$edit->preciomt2e->maxlength =15;

		$edit->preciomt2c = new inputField('Precio x mt2 (Cr&eacute;dito)','preciomt2c');
		$edit->preciomt2c->rule='max_length[15]|numeric';
		$edit->preciomt2c->css_class='inputnum';
		$edit->preciomt2c->size =10;
		$edit->preciomt2c->maxlength =15;

		$edit->preciomt2a = new inputField('Precio x mt2 (Alquiler)','preciomt2');
		$edit->preciomt2a->rule='max_length[15]|numeric';
		$edit->preciomt2a->css_class='inputnum';
		$edit->preciomt2a->size =10;
		$edit->preciomt2a->maxlength =15;

		$link1=site_url('construccion/common/get_ubic');
		$script ='<script type="text/javascript" >
		$(function() {
			$("#edificacion").change(function(){ edif_change(); });
			$(".inputnum").numeric(".");
			$(".inputonlynum").numeric();
		});

		function edif_change(){
			$.post("'.$link1.'",{ edif:$("#edificacion").val() }, function(data){ $("#ubicacion").html(data);})
		}

		</script>';

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back','add');
		$edit->build();
		$data['content'] = $edit->output;
		$data['script']  = script('jquery.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').$script;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading($this->tits);
		$this->load->view('view_ventanas', $data);
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
		if (!$this->db->table_exists('edinmue')) {
			$mSQL="CREATE TABLE `edinmue` (
			  `id` INT(11) NOT NULL AUTO_INCREMENT,
			  `codigo` CHAR(15) NULL DEFAULT NULL,
			  `descripcion` CHAR(100) NULL DEFAULT NULL,
			  `edificacion` INT(11) NULL DEFAULT NULL,
			  `uso` INT(11) NULL DEFAULT NULL,
			  `usoalter` INT(11) NULL DEFAULT NULL,
			  `ubicacion` INT(11) NULL DEFAULT NULL,
			  `caracteristicas` TEXT NULL,
			  `area` DECIMAL(15,2) NULL DEFAULT NULL,
			  `estaciona` INT(10) NULL DEFAULT NULL,
			  `deposito` INT(11) NULL DEFAULT NULL,
			  `preciomt2` DECIMAL(15,2) NULL DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE INDEX `codigo` (`codigo`)
			)
			COMMENT='Inmuebles'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('preciomt2e', 'edinmue')) {
			$mSQL="ALTER TABLE `edinmue`  CHANGE COLUMN `preciomt2` `preciomt2e` DECIMAL(15,2) NULL AFTER `deposito`,  ADD COLUMN `preciomt2c` DECIMAL(15,2) NULL AFTER `preciomt2e`,  ADD COLUMN `preciomt2a` DECIMAL(15,2) NULL AFTER `preciomt2c`";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('objeto', 'edinmue')) {
			$mSQL="ALTER TABLE `edinmue`  ADD COLUMN `objeto` CHAR(1) NOT NULL AFTER `preciomt2a`";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('status', 'edinmue')) {
			$mSQL="ALTER TABLE `edinmue`  ADD COLUMN `status` CHAR(1) NOT NULL COMMENT 'Alquilado, Vendido, Reservado,Disponible, Otro' AFTER `objeto`;";
			$this->db->simple_query($mSQL);
		}

	}

}*/
?>
