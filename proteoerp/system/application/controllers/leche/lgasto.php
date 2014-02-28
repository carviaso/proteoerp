<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
class Lgasto extends Controller {
	var $mModulo = 'LGASTO';
	var $titp    = 'Adiciones y Deducciones';
	var $tits    = 'Adiciones y Deducciones';
	var $url     = 'leche/lgasto/';
	var $tabla   = 'lgasto';

	function Lgasto(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->creaintramenu(array('modulo'=>'226','titulo'=>'Adiciones y Deducciones','mensaje'=>'Adiciones y Deducciones','panel'=>'LECHE','ejecutar'=>'leche/lgasto','target'=>'popu','visible'=>'S','pertenece'=>'2','ancho'=>900,'alto'=>600));
		$this->datasis->modulo_nombre( 'LGASTO', $ventana=0 );
	}

	function index(){
		$this->instalar();
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
			array('id'=>'fedita', 'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' , 'title'=>'Mostrar Registro'),
			array('id'=>'fborra', 'title'=>'Elimina registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('LGASTO', 'JQ');
		$param['otros']       = $this->datasis->otros('LGASTO', 'JQ');
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
		function lgastoadd() {
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function lgastoedit() {
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
		function lgastoshow() {
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
		function lgastodel() {
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
		$("#fedita").dialog({
			autoOpen: false, height: 300, width: 550, modal: true,
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
								'.$this->datasis->jwinopen(site_url('formatos/ver/LGASTO').'/\'+res.id+\'/id\'').';
								return true;
							} else {
								apprise(json.mensaje);
							}
						}catch(e){
							$("#fedita").html(r);
						}
					}
			})},
			"Guardar y Seguir": function() {
				var bValid = true;
				var murl = $("#df1").attr("action");
				$.ajax({
					type: "POST", dataType: "html", async: false,
					url: murl,
					data: $("#df1").serialize(),
					success: function(r,s,x){
						if ( r.length == 0 ) {
							apprise("Registro Guardado");
							grid.trigger("reloadGrid");
							return true;
						} else {
							$("#fedita").html(r);
					}}
			})},
			"Cancelar": function() { $( this ).dialog( "close" ); }
			},
			close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
		});';

		$bodyscript .= '
		$("#fshow").dialog({
			autoOpen: false, height: 300, width: 400, modal: true,
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

		$bodyscript .= '});'."\n";

		$bodyscript .= "\n</script>\n";
		$bodyscript .= "";
		return $bodyscript;
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('proveed');
		$grid->label('Proveed');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
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


		$grid->addField('descrip');
		$grid->label('Descrip');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->addField('cantidad');
		$grid->label('Cantidad');
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


		$grid->addField('precio');
		$grid->label('Precio');
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


		$grid->addField('total');
		$grid->label('Total');
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


		$grid->addField('pago');
		$grid->label('Pago');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
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
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('LGASTO','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('LGASTO','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('LGASTO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('LGASTO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: lgastoadd, editfunc: lgastoedit, delfunc: lgastodel,viewfunc: lgastoshow');

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
		$mWHERE = $grid->geneTopWhere('lgasto');

		$response   = $grid->getData('lgasto', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM lgasto WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('lgasto', $data);
					echo "Registro Agregado";

					logusu('LGASTO',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM lgasto WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM lgasto WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE lgasto SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("lgasto", $data);
				logusu('LGASTO',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('lgasto', $data);
				logusu('LGASTO',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM lgasto WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM lgasto WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM lgasto WHERE id=$id ");
				logusu('LGASTO',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$script="
		$(function(){
			$('.inputnum').numeric('.');
			$('#fecha').datepicker({   dateFormat: 'dd/mm/yy' });
			$('#proveed').autocomplete({
				source: function( req, add){
					$.ajax({
						url:  '".site_url('ajax/buscasprv')."',
						type: 'POST',
						dataType: 'json',
						data: 'q='+req.term,
						success:
							function(data){
								var sugiere = [];
								$.each(data,
									function(i, val){
										sugiere.push( val );
									}
								);
								add(sugiere);
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$('#proveed').attr('readonly', 'readonly');
					$('#nombre').val(ui.item.nombre);
					$('#nombre_val').text(ui.item.nombre);
					$('#proveed').val(ui.item.proveed);
					$('#sprvreteiva').val(ui.item.reteiva);
					setTimeout(function() { $('#proveed').removeAttr('readonly'); }, 1500);
				}
			});
		});
		function totaliza(){
			if($('#cantidad').val().length>0) cantidad  =parseFloat($('#cantidad').val()); else cantiad =0;
			if($('#precio').val().length >0)  precio    =parseFloat($('#precio').val());   else precio  =0;


			total=roundNumber(cantidad*precio,2);
			$('#total').val(total);
			$('#total_val').text(nformat(total));
		}
		";

		$edit = new DataEdit('', 'lgasto');
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
		$edit->proveed->rule='max_length[5]|required|existesprv';
		$edit->proveed->size =10;
		$edit->proveed->maxlength =15;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[100]';
		$edit->nombre->size =45;
		$edit->nombre->in = 'proveed';
		$edit->nombre->type='inputhidden';
		$edit->nombre->maxlength =100;

		$edit->tipo = new  dropdownField('Tipo', 'tipo');
		$edit->tipo->option('D','Deducci&oacute;n');
		$edit->tipo->option('A','Asignaci&oacute;n');
		$edit->tipo->style='width:150px;';
		$edit->tipo->size = 5;
		$edit->tipo->rule='required';

		$edit->referen = new inputField('N.Referencia','referen');
		$edit->referen->rule='max_length[100]';
		$edit->referen->size =12;
		$edit->referen->maxlength =10;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule='chfecha|required';
		$edit->fecha->calendar=false;
		$edit->fecha->insertValue=date('Y-m-d');
		$edit->fecha->size =12;
		$edit->fecha->maxlength =8;

		$edit->descrip = new inputField('Descripci&oacute;n','descrip');
		$edit->descrip->rule='max_length[100]|trim|strtoupper';
		$edit->descrip->size =45;
		$edit->descrip->maxlength =100;

		$edit->cantidad = new inputField('Cantidad','cantidad');
		$edit->cantidad->rule='max_length[17]|numeric|required';
		$edit->cantidad->css_class='inputnum';
		$edit->cantidad->size =19;
		$edit->cantidad->autocomplete=false;
		$edit->cantidad->onkeyup ='totaliza()';
		$edit->cantidad->maxlength =17;

		$edit->precio = new inputField('Precio','precio');
		$edit->precio->rule='max_length[17]|numeric|required';
		$edit->precio->css_class='inputnum';
		$edit->precio->onkeyup ='totaliza()';
		$edit->precio->autocomplete=false;
		$edit->precio->size =19;
		$edit->precio->maxlength =17;

		$edit->total = new inputField('Total','total');
		$edit->total->rule='max_length[17]|numeric|required';
		$edit->total->css_class='inputnum';
		$edit->total->size =19;
		$edit->total->type='inputhidden';
		$edit->total->maxlength =17;

		$edit->build();

		$script= '';

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
		$do->set('status','A');
		return true;
	}

	function _pre_update($do){
		return true;
	}

	function _pre_delete($do){
		$pago = $do->get('pago');
		if(empty($pago) || $pago==0){
			return true;
		}
		$do->error_message_ar['pre_del']='El efecto ya fue deducido o  acreditado, no se puede eliminar.';
		return false;
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits ${primary} ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits ${primary} ");
	}

	function _post_delete($do){
		$primary     = implode(',',$do->pk);
		$id_lpagolote= $do->get('id_lpagolote');
		if(empty($id_lpagolote)){
			$idlote  = ' lote: '.$id_lpagolote;
		}else{
			$idlote  = '';
		}
		logusu($do->table,"Elimino $this->tits ${primary} ${idlote}");
	}

	function instalar(){
		if (!$this->db->table_exists('lgasto')) {
			$mSQL="CREATE TABLE `lgasto` (
				`proveed` CHAR(5) NULL DEFAULT NULL COMMENT 'productor',
				`nombre` VARCHAR(100) NULL DEFAULT NULL COMMENT 'nombre',
				`tipo` CHAR(1) NULL DEFAULT 'D' COMMENT 'Dedudccion, Adicion',
				`referen` VARCHAR(100) NULL DEFAULT NULL,
				`status` CHAR(1) NULL DEFAULT 'A',
				`fecha` DATE NULL DEFAULT NULL COMMENT 'nombre',
				`descrip` VARCHAR(100) NULL DEFAULT NULL COMMENT 'finca',
				`cantidad` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'ruta a en lruta',
				`precio` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'zona',
				`total` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'direccion',
				`pago` INT(11) NULL DEFAULT '0' COMMENT 'id de pago lpago',
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				INDEX `proveed` (`proveed`),
				INDEX `fecha` (`fecha`)
			)
			COMMENT='Gastos'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('lgasto');
		if (!in_array('tipo',$campos)){
			$mSQL="ALTER TABLE `lgasto` ADD COLUMN `tipo` CHAR(1) NULL DEFAULT 'D' COMMENT 'Dedudccion, Adicion' AFTER `nombre`";
			$this->db->simple_query($mSQL);
		}
	}
}
