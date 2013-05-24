<?php
class Grup extends Controller {
	var $mModulo = 'GRUP';
	var $titp    = 'GRUPOS DE INVENTARIO';
	var $tits    = 'GRUPOS DE INVENTARIO';
	var $url     = 'supermercado/grup/';

	function Grup(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'GRUP', $ventana=0 );
	}

	function index(){
		if ( !$this->datasis->iscampo('grup','id') ) {
			$this->db->simple_query('ALTER TABLE grup DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE grup ADD UNIQUE INDEX principal  (depto, familia, grupo)');
			$this->db->simple_query('ALTER TABLE grup ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		$this->datasis->creaintramenu(array('modulo'=>'330','titulo'=>'Grupo de Inventario','mensaje'=>'Grupos de Inventario','panel'=>'SUPERMERCADO','ejecutar'=>'supermercado/grup','target'=>'popu','visible'=>'S','pertenece'=>'3','ancho'=>900,'alto'=>600));
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
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('GRUP', 'JQ');
		$param['otros']       = $this->datasis->otros('GRUP', 'JQ');
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
		function grupadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function grupedit(){
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
		function grupshow(){
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
		function grupdel() {
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/GRUP').'/\'+res.id+\'/id\'').';
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

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('grupo');
		$grid->label('Grupo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('nom_grup');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('depto');
		$grid->label('Depto');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:3, maxlength: 3 }',
		));


		$grid->addField('familia');
		$grid->label('Familia');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:3, maxlength: 3 }',
		));



		$grid->addField('comision');
		$grid->label('Comision');
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


		$grid->addField('margen1');
		$grid->label('Margen1');
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


		$grid->addField('margen2');
		$grid->label('Margen2');
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


		$grid->addField('margen3');
		$grid->label('Margen3');
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


		$grid->addField('margen4');
		$grid->label('Margen4');
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


		$grid->addField('margen5');
		$grid->label('Margen5');
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


		$grid->addField('cu_inve');
		$grid->label('Cu_inve');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('cu_cost');
		$grid->label('Cu_cost');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('cu_venta');
		$grid->label('Cu_venta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('cu_devo');
		$grid->label('Cu_devo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

/*
		$grid->addField('precio');
		$grid->label('Comision');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));
*/

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
		$grid->setAdd(    $this->datasis->sidapuede('GRUP','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('GRUP','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('GRUP','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('GRUP','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: grupadd, editfunc: grupedit, delfunc: grupdel, viewfunc: grupshow");

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
		$mWHERE = $grid->geneTopWhere('grup');

		$response   = $grid->getData('grup', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM grup WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('grup', $data);
					echo "Registro Agregado";

					logusu('GRUP',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM grup WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM grup WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE grup SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("grup", $data);
				logusu('GRUP',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('grup', $data);
				logusu('GRUP',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM grup WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM grup WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM grup WHERE id=$id ");
				logusu('GRUP',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
		});
		';

		$edit = new DataEdit($this->tits, 'grup');

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

		$edit->grupo = new inputField('Grupo','grupo');
		$edit->grupo->rule='';
		$edit->grupo->size =6;
		$edit->grupo->maxlength =4;

		$edit->nom_grup = new inputField('Nom_grup','nom_grup');
		$edit->nom_grup->rule='';
		$edit->nom_grup->size =32;
		$edit->nom_grup->maxlength =30;

		$edit->tipo = new dropdownField("Tipo","tipo");
		$edit->tipo->style='width:100px;';
		$edit->tipo->option("I","Inventario" );
		$edit->tipo->option("G","Gasto"  );

/*
		$edit->tipo = new inputField('Tipo','tipo');
		$edit->tipo->rule='';
		$edit->tipo->size =3;
		$edit->tipo->maxlength =1;


		$edit->linea = new inputField('Linea','linea');
		$edit->linea->rule='';
		$edit->linea->size =4;
		$edit->linea->maxlength =2;

		$edit->depto = new inputField('Depto','depto');
		$edit->depto->rule='';
		$edit->depto->size =5;
		$edit->depto->maxlength =3;

		$edit->familia = new inputField('Familia','familia');
		$edit->familia->rule='';
		$edit->familia->size =5;
		$edit->familia->maxlength =3;
*/


		$edit->depto = new dropdownField("Departamento", "dpto");
		$edit->depto->db_name='depto';
		$edit->depto->rule ="required";
		$edit->depto->onchange = "get_familia();";
		$edit->depto->option("","Seleccionar");
		$edit->depto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");

		$edit->familia = new dropdownField("Familia","familia");
		$edit->familia->rule ="required";
		if($edit->_status=='modify' or $edit->_status=='show' or $edit->_status=='idle' or $edit->_status=='create'){
			$depto = ($this->input->post('dpto')===FALSE) ? $edit->_dataobject->get("depto") : $this->input->post('dpto');
			$edit->familia->option("","");
			$edit->familia->options("SELECT familia, descrip FROM fami WHERE depto='$depto' ORDER BY descrip");
		}else{
			$edit->familia->option("","Seleccione un Departamento");
		}

		$edit->cu_inve = new inputField('Cu_inve','cu_inve');
		$edit->cu_inve->rule='';
		$edit->cu_inve->size =17;
		$edit->cu_inve->maxlength =15;

		$edit->cu_cost = new inputField('Cu_cost','cu_cost');
		$edit->cu_cost->rule='';
		$edit->cu_cost->size =17;
		$edit->cu_cost->maxlength =15;

		$edit->cu_venta = new inputField('Cu_venta','cu_venta');
		$edit->cu_venta->rule='';
		$edit->cu_venta->size =17;
		$edit->cu_venta->maxlength =15;

/*
		$edit->cu_devo = new inputField('Cu_devo','cu_devo');
		$edit->cu_devo->rule='';
		$edit->cu_devo->size =17;
		$edit->cu_devo->maxlength =15;
*/

		$edit->comision = new inputField('Comision','comision');
		$edit->comision->rule='numeric';
		$edit->comision->css_class='inputnum';
		$edit->comision->size =10;
		$edit->comision->maxlength =8;

		$edit->margen1 = new inputField('Margen1','margen1');
		$edit->margen1->rule='numeric';
		$edit->margen1->css_class='inputnum';
		$edit->margen1->size =10;
		$edit->margen1->maxlength =8;

		$edit->margen2 = new inputField('Margen2','margen2');
		$edit->margen2->rule='numeric';
		$edit->margen2->css_class='inputnum';
		$edit->margen2->size =10;
		$edit->margen2->maxlength =8;

		$edit->margen3 = new inputField('Margen3','margen3');
		$edit->margen3->rule='numeric';
		$edit->margen3->css_class='inputnum';
		$edit->margen3->size =10;
		$edit->margen3->maxlength =8;

		$edit->margen4 = new inputField('Margen4','margen4');
		$edit->margen4->rule='numeric';
		$edit->margen4->css_class='inputnum';
		$edit->margen4->size =10;
		$edit->margen4->maxlength =8;

		$edit->margen5 = new inputField('Margen5','margen5');
		$edit->margen5->rule='numeric';
		$edit->margen5->css_class='inputnum';
		$edit->margen5->size =10;
		$edit->margen5->maxlength =8;

		$edit->precio = new inputField('Precio','precio');
		$edit->precio->rule='';
		$edit->precio->size =3;
		$edit->precio->maxlength =1;

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
		if (!$this->db->table_exists('grup')) {
			$mSQL="CREATE TABLE `grup` (
			  `grupo` varchar(4) NOT NULL DEFAULT '',
			  `nom_grup` varchar(30) DEFAULT NULL,
			  `tipo` char(1) NOT NULL DEFAULT 'I',
			  `linea` char(2) NOT NULL DEFAULT '',
			  `familia` char(3) NOT NULL DEFAULT '',
			  `cu_inve` varchar(15) DEFAULT NULL,
			  `cu_cost` varchar(15) DEFAULT NULL,
			  `cu_venta` varchar(15) DEFAULT NULL,
			  `cu_devo` varchar(15) DEFAULT NULL,
			  `depto` char(3) NOT NULL DEFAULT '',
			  `comision` decimal(8,2) NOT NULL DEFAULT '0.00',
			  `margen1` decimal(8,2) DEFAULT NULL,
			  `margen2` decimal(8,2) DEFAULT NULL,
			  `margen3` decimal(8,2) DEFAULT NULL,
			  `margen4` decimal(8,2) DEFAULT NULL,
			  `margen5` decimal(8,2) DEFAULT NULL,
			  `precio` char(1) DEFAULT '0',
			  PRIMARY KEY (`depto`,`familia`,`grupo`),
			  KEY `grupo` (`grupo`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
	}


/*
class Grup extends validaciones {
	
	function grup(){
		parent::Controller();
		$this->load->library("rapyd");
	  //$this->datasis->modulo_id(304,1);
	}
	
	function index(){		
		redirect("supermercado/grup/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();
		
		function blanco($num){
			if(empty($num)||$num==0){
			 return '';
			}else{
				return number_format($num,2,',','.');
			}
		}
		
		$filter = new DataFilter("Filtro de Grupo de Inventario");
		
		$filter->db->select("a.grupo AS grupo, a.nom_grup AS nom_grup, a.comision AS comision,b.familia AS fami, b.descrip AS familia,c.depto AS dpto,c.descrip AS depto, a.cu_inve AS cu_inve, a.cu_venta AS cu_venta, a.cu_cost AS cu_cost, a.cu_devo AS cu_devo");
		$filter->db->from("grup AS a");
		$filter->db->join("fami AS b","a.familia=b.familia");
		$filter->db->join("dpto AS c","b.depto=c.depto");
		
		$filter->grupo = new inputField("Grupo","grupo");
		$filter->grupo->size=20;
		
		$filter->nombre = new inputField("Descripci&oacute;n","nom_grup");
		$filter->nombre->size=20;
		
		$filter->comision = new inputField("Comisi&oacute;n","comision");
		$filter->comision->size=20;
		
		$filter->depto = new inputField("Departamento","c.descrip");
		$filter->depto->size=20;
		
		$filter->linea = new inputField("Familia","b.descrip");
		$filter->linea->size=20;

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('supermercado/grup/dataedit/show/<raencode><#grupo#></raencode>/<raencode><#fami#></raencode>/<raencode><#dpto#></raencode>','<#grupo#>');
		$uri_2 = anchor('supermercado/grup/dataedit/create/<raencode><#grupo#></raencode>/<raencode><#fami#></raencode>/<raencode><#dpto#></raencode>','Duplicar');

		$grid = new DataGrid("Lista de Grupos de Inventario");
		$grid->order_by("grupo","asc");
		$grid->per_page = 20;
		$grid->use_function('blanco');

		$grid->column("Grupo"                       ,$uri                            ,"align='center'");
		$grid->column("Descripci&oacute;n"                 ,"nom_grup"                      ,"align='left'");
		$grid->column("Comisi&oacute;n"                    ,"<blanco><#comision#></blanco>" ,"align='right'");		
		$grid->column("Departamento"                ,"depto"                         ,"align='left'");
		$grid->column("Familia"                     ,"familia"                       ,"align='left'");
		$grid->column("Cuenta Inventario"           ,"cu_inve"                       ,"align='center'");
		$grid->column("Cuenta Costo"                ,"cu_cost"                       ,"align='center'");
		$grid->column("Cuenta Venta"                ,"cu_venta"                      ,"align='center'");
		$grid->column("Cuenta Devoluci&oacute;n"    ,"cu_devo"                       ,"align='center'");
		$grid->column("Duplicar"                    ,$uri_2                          ,"align='center'");

		$grid->add("supermercado/grup/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Grupos de Inventario</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}



	function dataedit($status='',$grupo='',$familia='',$depto='')
 	{
		$this->rapyd->load("dataobject","dataedit");
		
		$qformato=$this->qformato=$this->datasis->formato_cpla();
		$link=site_url('supermercado/grup/ultimo');
		$link2=site_url('supermercado/grup/sugerir');
		
		$script ='
			$(function() {
				$(".inputnum").numeric(".");
			});
			
			function ultimo(){
				$.ajax({
					url: "'.$link.'",
					success: function(msg){
					  alert( "El ultimo codigo ingresado fue: " + msg );
					}
				});
			}
		
			function sugerir(){
				$.ajax({
						url: "'.$link2.'",
						success: function(msg){
							if(msg){
								$("#grupo").val(msg);
							}
							else{
								alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
							}
						}
					});
			}
    	
			function get_familia(){
				$.ajax({
					type: "POST",
					url: "'.site_url('supermercado/grup/familia').'",
					data: $("#dpto").serialize(),
					success: function(msg){
						$("#td_familia").html(msg);
					},
					error: function(msg){
						alert("Error en la comunicaci&oacute;n");
					}
				});
			}';

		$modbus=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'<#i#>'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
			'p_uri'=>array(4=>'<#i#>')
		);

		$bcu_venta = $this->datasis->p_modbus($modbus,'cu_venta');
		$bcu_inve  = $this->datasis->p_modbus($modbus,'cu_inve' );
		$bcu_cost  = $this->datasis->p_modbus($modbus,'cu_cost' );
		$bcu_devo  = $this->datasis->p_modbus($modbus,'cu_devo' );

		$do = new DataObject("grup"); $do->set('tipo', 'I'); if($status=="create" && 
		!empty($grupo) && !empty($familia) && !empty($depto)){ $do->load(array("familia"=> "$familia","grupo"=> "$grupo","depto"=> "$depto")); 
		$do->set('grupo', ''); }

		$edit = new DataEdit("Grupos de Inventario",$do);
		$edit->back_url = site_url("supermercado/grup/filteredgrid");
		$edit->script($script, "modify");
		$edit->script($script, "create");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->depto = new dropdownField("Departamento", "dpto");
		$edit->depto->db_name='depto';
		$edit->depto->rule ="required";
		$edit->depto->onchange = "get_familia();";
		$edit->depto->option("","Seleccionar");
		$edit->depto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");

		$edit->familia = new dropdownField("Familia","familia");
		$edit->familia->rule ="required";
		if($edit->_status=='modify' or $edit->_status=='show' or $edit->_status=='idle' or $edit->_status=='create'){
			$depto = ($this->input->post('dpto')===FALSE) ? $edit->_dataobject->get("depto") : $this->input->post('dpto');
			$edit->familia->option("","");
			$edit->familia->options("SELECT familia, descrip FROM fami WHERE depto='$depto' ORDER BY descrip");
		}else{
			$edit->familia->option("","Seleccione un Departamento");
		}

		$ultimo='<a href="javascript:ultimo();" title="Consultar ultimo c&oacute;digo ingresado"> Consultar ultimo c&oacute;digo</a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->grupo =  new inputField("C&oacute;digo Grupo", "grupo");
		$edit->grupo->mode="autohide";
		$edit->grupo->maxlength=4;
		$edit->grupo->size =6;
		$edit->grupo->rule ="trim|strtoupper|required|callback_chexiste";
		$edit->grupo->append($sugerir);
		$edit->grupo->append($ultimo);
		
		$edit->nom_grup =  new inputField("Nombre del Grupo", "nom_grup");
		$edit->nom_grup->size = 35;
		$edit->nom_grup->maxlength=30;
		$edit->nom_grup->rule = "trim|strtoupper|required";
		
	  //$edit->tipo = new dropdownField("Tipo","tipo");
	  //$edit->tipo->style='width:100px;';
		//$edit->tipo->option("I","Inventario" );
		//$edit->tipo->option("G","Gasto"  );
		
		$edit->comision = new inputField("Comisi&oacute;n. %", "comision");
		$edit->comision->size = 18;
		$edit->comision->maxlength=10;
		$edit->comision->css_class='inputnum';
		$edit->comision->rule='trim|numeric|callback_positivo';
		
		for($i=1;$i<=5;$i++){
			$obj="margen$i";
			$edit->$obj = new inputField("Margen $i. %", $obj);
			$edit->$obj->size = 18;
			$edit->$obj->maxlength=7;
			$edit->$obj->css_class='inputnum';
			$edit->$obj->rule='trim|numeric|callback_positivo';
		}
		
		$edit->cu_inve =new inputField("Cuenta Inventario", "cu_inve");
		$edit->cu_inve->size = 18;
		$edit->cu_inve->maxlength=15;
		$edit->cu_inve->rule ="trim|existecpla";
		$edit->cu_inve->append($bcu_inve);
		$edit->cu_inve->group='Cuentas contables';
		
		$edit->cu_cost =new inputField("Cuenta Costo", "cu_cost");
		$edit->cu_cost->size = 18;
    $edit->cu_cost->maxlength=15;
    $edit->cu_cost->rule ="trim|existecpla";
    $edit->cu_cost->append($bcu_cost);
    $edit->cu_cost->group='Cuentas contables';
		
		$edit->cu_venta  =new inputField("Cuenta Venta", "cu_venta");
		$edit->cu_venta->size =18;
		$edit->cu_venta->maxlength=15;
		$edit->cu_venta->rule ="trim|existecpla";
		$edit->cu_venta->append($bcu_venta);
		$edit->cu_venta->group='Cuentas contables';
		
    $edit->cu_devo = new inputField("Cuenta Devoluci&oacute;n","cu_devo");
    $edit->cu_devo->size = 18;
    $edit->cu_devo->maxlength=15;
    $edit->cu_devo->rule ="trim|existecpla";
    $edit->cu_devo->append($bcu_devo);
    $edit->cu_devo->group='Cuentas contables';
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$link=site_url('supermercado/grup/get_familia');

		$data['content'] = $edit->output;
    $data['title']   = "<h1>Grupos de Inventario</h1>";        
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
	
	function _post_insert($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('nom_grup');
		logusu('grup',"GRUPO DE INVENTARIO $codigo NOMBRE  $nombre CREADO");
	}
	
	function _post_update($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('nom_grup');
		logusu('grup',"GRUPO DE INVENTARIO $codigo NOMBRE  $nombre  MODIFICADO");
	}
	
	function _post_delete($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('nom_grup');
		logusu('grup',"GRUPO DE INVENTARIO $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	
	function chexiste($codigo){
		$codigo=$this->input->post('grupo');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM grup WHERE grupo='$codigo'");
		if ($check > 0){
			$grupo=$this->datasis->dameval("SELECT nom_grup FROM grup WHERE grupo='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el grupo $grupo");
			return FALSE;
		}else {
  		return TRUE;
		}
	}

	function _pre_del($do) {
		$codigo=$do->get('grupo');
		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sinv WHERE grupo='$codigo'");
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='El grupo contiene productos, por ello no puede ser eliminado. Elimine primero todos los productos que pertenezcan a este grupo';
			return False;
		}
		return True;
	}
	
	function ultimo(){
		$ultimo=$this->datasis->dameval("SELECT grupo FROM grup ORDER BY grupo DESC");
		echo $ultimo;
	}
	
	function positivo($valor){
		if ($valor < 0){
			$this->validation->set_message('positivo',"El campo comisi&oacute;n debe ser positivo");
			return FALSE;
		}
  	return TRUE;
	}
	
	function sugerir(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN grup ON LPAD(grupo,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND grupo IS NULL LIMIT 1");
		echo $ultimo;
	}
	
	function familia(){
		if (!empty($_POST["dpto"])){ 
			$departamento=$_POST["dpto"];
		}elseif (!empty($_POST["depto"])){
 			$departamento=$_POST["depto"];
		}
		
		$this->rapyd->load("fields");  
		$where = "";  
		$sql = "SELECT familia, descrip FROM fami ";
		$familia = new dropdownField("Subcategoria", "familia");

		if (!empty($departamento)){
		  $where = "WHERE depto = ".$this->db->escape($departamento);
		  $sql = "SELECT familia, descrip FROM fami $where";
		  $familia->option("","");
			$familia->options($sql);
		}else{
			 $familia->option("","Seleccione Un Departamento"); 
		} 
		$familia->status   = "modify";
		$familia->build();
		echo $familia->output;
	}
*/
}
?>
