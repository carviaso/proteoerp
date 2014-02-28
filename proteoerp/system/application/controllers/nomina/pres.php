<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
//adelantoprestamos
class Pres extends Controller {
	var $mModulo='PRES';
	var $titp='Descuento de Prestamos por Nomina';
	var $tits='Descuento de Prestamos por Nomina';
	var $url ='nomina/pres/';

	function Pres(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('NNN',1);
	}

	function index(){
		$this->instalar();
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$bodyscript = '<script type="text/javascript">
		jQuery("#a1").click( function(){
			var id = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getRowData\',id);
				window.open(\''.base_url().'formatos/ver/PRES/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});
		</script>';

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		//$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('PRES', 'JQ');
		$param['otros']       = $this->datasis->otros('PRES', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

/*
Estatus:
	Reposo:     33% del sueldo ()
	Vacaciones: 15 dias  Bono Vacacional 15+annos calcula segun ultimo mes
	Todos aplican los descuentos
*/


	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';
		$ngrid      = '#newapi'.$grid0;

		$bodyscript .= $this->jqdatagrid->bsshow('pres', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'pres', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'pres', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('pres', $ngrid, $this->url );

		$SouthPanel = '
		<div id="BottomPane" class="ui-layout-south ui-widget ui-widget-content">
		<p>'.$this->datasis->traevalor('TITULO1').'</p>
		</div>';

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('PRES', 'JQ');
		$param['otros']       = $this->datasis->otros('PRES', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
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

		$grid  = new $this->jqdatagrid;
		$link = site_url('ajax/buscapers');
		$despues ='
				$("input#nombre").val(ui.item.nombre);
				$("input#cod_cli").val(ui.item.enlace);
				_cargo = ui.item.enlace;';

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{'.$grid->autocomplete($link, 'codigo','aaaaaa','<div id=\"aaaaaa\"></div>',$despues,'\'#editmod\'+gridId1.substring(1)').'}',
			'formoptions'   => '{ label:"Codigo del Trabajador" }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30, readonly: true }',
		));


		$grid->addField('cod_cli');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5, readonly: true }',
			'formoptions'   => '{ label:"Enlace Administrativo" }',
		));

		$link1 = site_url('ajax/buscasmovep');
		$despues1 ='
				$("input#tipo_doc").val(ui.item.tipo_doc);
				$("input#monto").val(ui.item.monto);
				';


		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, '.$grid->autocomplete($link1, 'codigo','aaaaaa','<div id=\"aaaaaa\"></div>',$despues1,'\'#editmod\'+gridId1.substring(1)').'}',
			'formoptions'   => '{ label:"Numero de Efecto" }',
		));

		$grid->addField('tipo_doc');
		$grid->label('Tipo Doc.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
			'formoptions'   => '{ label:"Tipo de Documento" }',
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
			'formatoptions' => '{label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
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
			'formoptions'   => '{ label:"Fecha" }',
			//'editoptions'   => '{ defaultValue:"'.date('Y-m-d').'"}'
		));


		$grid->addField('nroctas');
		$grid->label('Nro.Cuotas');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ defaultValue:"1",size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); },
				dataEvents: [{
					type: "change", fn: function(e){
						var cuotas = Number($(e.target).val());
						var monto  = Number($("input#monto").val());
						var cuota  = 0;
						if ( cuotas==0) { cuotas=1; };
						cuota = monto/cuotas;
						$("input#cuota").val(cuota);
					}
				}]

			}',
			'formatter'     => "'number'",
			'formatoptions' => '{label:"Numero de Cuotas", decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('cuota');
		$grid->label('Cuota');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ defaultValue:"0", size:10, maxlength: 10, readonly: true, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{label:"Descuento por Nomina",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('apartir');
		$grid->label('Apartir');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Inicio del descuento" }'
		));

		$grid->addField('cadano');
		$grid->label('Intervalo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"1":"Cada Nomina", "2":"Cas 2 Nominas"} }',
			'editrules'     => '{ required:true}',
		));

		$grid->addField('observ1');
		$grid->label('Observaciones');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			//'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 46 }',
		));

		$grid->addField('oberv2');
		$grid->label('Obervaciones');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			//'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 46 }',
		));

		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
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

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:450, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:450, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		//$grid->setBarOptions('addfunc: presadd, editfunc: presedit, delfunc: presdel, viewfunc: presshow');


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

/*

#editmodnewapi_14591061.ui-widget.ui-widget-content.ui-corner-all.ui-jqdialog.jqmID2 2
#edithdnewapi_14591061.ui-jqdialog-titlebar.ui-widget-header.ui-corner-all.ui-helper-clearfix 3Modificar registro
#editcntnewapi_14591061.ui-jqdialog-content.ui-widget-content 4



*/


	/**
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('pres');

		$response   = $grid->getData('pres', array(array()), array(), false, $mWHERE );
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
		$check  = 0;

		// Valida
		if($oper != 'del'){
			$codigo   = $this->input->post('codigo');
			$cod_cli  = $this->input->post('cod_cli');
			$numero   = $this->input->post('numero');
			$tipo_doc = $this->input->post('tipo_doc');
			$check = $this->datasis->dameval('SELECT count(*) FROM pers WHERE codigo='.$this->db->escape($codigo));
			if ( $check == 0 ){
				echo "No se encontro esa persona en los registros ".$codigo;
				return;
			}
			$check = $this->datasis->dameval('SELECT count(*) FROM scli WHERE cliente='.$this->db->escape($cod_cli));
			if ( $check == 0 ){
				echo "No se encontro el enlace Administrativo ".$cod_cli;
				return;
			}
			$check = $this->datasis->dameval('SELECT count(*) FROM smov WHERE cod_cli='.$this->db->escape($cod_cli)." AND tipo_doc='$tipo_doc' AND numero='$numero' AND monto>abonos " );
			if ( $check == 0 ){
				echo "No se encontro la deuda a cobrar ".$tipo_doc.$numero;
				return;
			}
			$data['nombre']  = $this->datasis->dameval("SELECT CONCAT(TRIM(nombre),' ',TRIM(apellido)) nombre FROM pers WHERE codigo='$codigo'");
		}
		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				//Busca si ya existe
				$check = $this->datasis->dameval('SELECT count(*) FROM pres WHERE cod_cli='.$this->db->escape($cod_cli)." AND tipo_doc='$tipo_doc' AND numero='$numero' " );
				if ( $check == 0 ) {
					$this->db->insert('pres', $data);
					echo "Registro Agregado";
					logusu('PRES',"Registro  INCLUIDO");
				} else
				echo "Registro ya Agregado!!!";

			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$this->db->where('id', $id);
			$this->db->update('pres', $data);
			logusu('PRES',"Registro ????? MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM pres WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM pres WHERE id=$id ");
				logusu('PRES',"Registro ????? ELIMINADO");
				echo 'Registro Eliminado';
			}
		}
	}


	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");

			$("#cod_cli").autocomplete({
				delay: 600,
				autoFocus: true,
				source: function(req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscapers').'",
						type: "POST",
						dataType: "json",
						data: {"q":req.term},
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#nombre").val("");
									$("#nombre_val").text("");

									$("#rifci").val("");
									$("#rifci_val").text("");
									$("#sclitipo").val("1");

									$("#direc").val("");
									$("#direc_val").text("");
								}else{
									$.each(data,
										function(i, val){
											sugiere.push( val );
										}
									);
								}
								add(sugiere);
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$("#cod_cli").attr("readonly", "readonly");

					$("#nombre").val(ui.item.nombre);
					$("#nombre_val").text(ui.item.nombre);

					$("#rifci").val(ui.item.rifci);
					$("#rifci_val").text(ui.item.rifci);

					$("#cod_cli").val(ui.item.cod_cli);
					$("#sclitipo").val(ui.item.tipo);

					$("#direc").val(ui.item.direc);
					$("#direc_val").text(ui.item.direc);
					setTimeout(function() {  $("#cod_cli").removeAttr("readonly"); }, 1500);
				}
			});

		});';

		$edit = new DataEdit('', 'pres');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$edit->cod_cli = new inputField('C&oacute;digo Cliente','cod_cli');
		$edit->cod_cli->rule='';
		$edit->cod_cli->size =7;
		$edit->cod_cli->maxlength =5;

		$edit->tipo_doc = new inputField('Tipo Doc.','tipo_doc');
		$edit->tipo_doc->rule='';
		$edit->tipo_doc->size =4;
		$edit->tipo_doc->maxlength =2;

		$edit->numero = new inputField('N&uacute;mero de efecto','numero');
		$edit->numero->rule='';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;
		$edit->fecha->calendar=false;

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->rule='';
		$edit->codigo->size =17;
		$edit->codigo->maxlength =15;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='';
		$edit->nombre->size =32;
		$edit->nombre->maxlength =30;
		$edit->nombre->in='codigo';

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =16;
		$edit->monto->maxlength =14;

		$edit->nroctas = new inputField('Nro. Cuotas','nroctas');
		$edit->nroctas->rule='numeric';
		$edit->nroctas->css_class='inputnum';
		$edit->nroctas->size =4;
		$edit->nroctas->maxlength =2;

		$edit->cuota = new inputField('Cuota','cuota');
		$edit->cuota->rule='numeric';
		$edit->cuota->css_class='inputnum';
		$edit->cuota->size =16;
		$edit->cuota->maxlength =14;

		$edit->apartir = new dateonlyField('Inicio del cobro','apartir');
		$edit->apartir->rule='chfecha';
		$edit->apartir->size =10;
		$edit->apartir->maxlength =8;
		$edit->apartir->insertValue = date('Y-m-d');
		$edit->apartir->calendar=false;

		$edit->cadano = new dropdownField ('Intervalo', 'cadano');
		$edit->cadano->rule='required|enum[1,2]';
		$edit->cadano->style='width:120px;';
		$edit->cadano->options(array(
			'1'=> 'Cada Nomina',
			'2'=> 'Cada 2 Nominas'
		));

		$edit->observ1 = new inputField('Observaciones','observ1');
		$edit->observ1->rule='';
		$edit->observ1->size =48;
		$edit->observ1->maxlength =46;

		$edit->oberv2 = new inputField('Obervaciones 2','oberv2');
		$edit->oberv2->rule='';
		$edit->oberv2->size =48;
		$edit->oberv2->maxlength =46;

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

	function grid(){
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 50;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : '[{"property":"fecha","direction":"ASC"}]';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;

		$where = $this->datasis->extjsfiltro($filters);
		$this->db->_protect_identifiers=false;
		$this->db->select('*');
		$this->db->from('pres');
		if (strlen($where)>1) $this->db->where($where, NULL, FALSE);

		$sort = json_decode($sort, true);
		for ($i=0;$i<count($sort);$i++) {
			$this->db->order_by($sort[$i]['property'],$sort[$i]['direction']);
		}

		$this->db->limit($limit, $start);
		$query = $this->db->get();

		$results = $this->db->count_all('pres');
		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}

	function instalar(){
		$campos=$this->db->list_fields('pres');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE pres DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE pres ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE pres ADD UNIQUE INDEX cliente (cod_cli, tipo_doc, numero )');
		}
	}
}
