<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
class Medhisto extends Controller {
	var $mModulo = 'MEDHISTO';
	var $titp    = 'HISTORIAS MEDICAS';
	var $tits    = 'HISTORIAS MEDICAS';
	var $url     = 'medico/medhisto/';

	function Medhisto(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'MEDHISTO', $ventana=0 );
	}

	function index(){
		$this->datasis->creaintramenu(array('modulo'=>'170','titulo'=>'Historias Medicas','mensaje'=>'Historias Medicas','panel'=>'SALUD','ejecutar'=>'medico/medhisto','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>900,'alto'=>600));
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
		$grid->wbotonadd(array('id'=>'phistoria', 'img'=>'images/circuloazul.png'  ,'alt' => 'Formato PDF', 'label'=>'Historia'));
		$grid->wbotonadd(array('id'=>'gvisitas' , 'img'=>'images/circuloverde.png' ,'alt' => 'Visistas'   , 'label'=>'Visitas' ));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita' ,  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fvisita',  'title'=>'Agregar/Editar Visita'),
			array('id'=>'fshow'  ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra' ,  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('MEDHISTO', 'JQ');
		$param['otros']       = $this->datasis->otros('MEDHISTO', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('medhisto', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'medhisto', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'medhisto', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('medhisto', $ngrid, $this->url );

		$imp = '
		'.$this->datasis->jwinopen(site_url('formatos/ver/HISTORIA').'/\'+idactual').';';

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '500', '600', 'fedita', $imp );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

		$bodyscript .= '});';

		$bodyscript .= '
		jQuery("#phistoria").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('formatos/ver/HISTORIA').'/\'+id').';
			} else { $.prompt("<h1>Por favor Seleccione una Historia</h1>");}
		});';

		$bodyscript .= '
		jQuery("#gvisitas").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				$.post("'.site_url('medico/medhvisita/dataefla/create').'/"+id, function(data){
					$("#fvisita").html(data);
					$("#fvisita").dialog( "open" );
				})
			} else { $.prompt("<h1>Por favor Seleccione una Historia</h1>");}
		});';

		$bodyscript .= '
		$("#fvisita").dialog({
			autoOpen: false, height: 500, width: 600, modal: true,
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
									$( "#fvisita" ).dialog( "close" );
									idvisita = json.pk.id;
									return true;
								} else {
									$.prompt(json.mensaje);
								}
							} catch(e) {
								$("#fvisita").html(r);
							}
						}
					})
				},
				"Imprimir": function() {
					var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
					'.$this->datasis->jwinopen(site_url('formatos/ver/HISTORIA').'/\'+id+"/id"').';
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
									idvisita = json.pk.id;
									$.post("'.site_url('medico/medhvisita/dataefla').'/create/"+id+"/"+idvisita,
									function(data){
										$("#fvisita").html(data);
									});
									return true;
								} else {
									$.prompt(json.mensaje);
								}
							} catch(e) {
								$("#fvisita").html(r);
							}
						}
					})
				},
				"Cancelar": function() {
					$("#fvisita").html("");
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				$("#fvisita").html("");
			}
		});
		';

		$bodyscript .= '</script>';

		return $bodyscript;
	}

	//******************************************************************
	// Definicion del Grid o Tabla
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));

		/*$grid->addField('nacional');
		$grid->label('Nac');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'align'         => "'center'",
			'width'         => 40,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));*/


		/*$grid->addField('cedula');
		$grid->label('Cedula');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));*/


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		/*$grid->addField('papellido');
		$grid->label('Apellidos');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));*/

/*
		$grid->addField('sapellido');
		$grid->label('Segundo Apellido');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));
*/

		/*$grid->addField('sexo');
		$grid->label('Sexo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 40,
			'align'         => "'center'",
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:1, maxlength: 1  }',
		));*/

		$grid->addField('ingreso');
		$grid->label('Ingreso');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		/*$grid->addField('nacio');
		$grid->label('F.Nacio');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));*/


		/*$grid->addField('estado');
		$grid->label('Estado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));*/

		/*$grid->addField('ciudad');
		$grid->label('Ciudad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));*/

		/*$grid->addField('ecivil');
		$grid->label('Edo.civil');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'align'         => "'center'",
			'width'         => 50,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('ocupacion');
		$grid->label('Ocupacion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('direccion');
		$grid->label('Direccion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('telefono');
		$grid->label('Telefono');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('referido');
		$grid->label('Referido');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('email');
		$grid->label('Email');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));*/


		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
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
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));


		/*$grid->addField('edad');
		$grid->label('Edad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:255, maxlength: 255 }',
		));*/


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

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('MEDHISTO','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('MEDHISTO','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('MEDHISTO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('MEDHISTO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: medhistoadd, editfunc: medhistoedit, delfunc: medhistodel, viewfunc: medhistoshow');

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
		$mWHERE = $grid->geneTopWhere('medhisto');

		$response   = $grid->getData('medhisto', array(array()), array(), false, $mWHERE, 'numero', 'desc' );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM medhisto WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('medhisto', $data);
					echo "Registro Agregado";

					logusu('MEDHISTO',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM medhisto WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM medhisto WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE medhisto SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("medhisto", $data);
				logusu('MEDHISTO',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('medhisto', $data);
				logusu('MEDHISTO',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM medhisto WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM medhisto WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM medhisto WHERE id=$id ");
				logusu('MEDHISTO',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	// Edicion

	function dataedit(){
		$this->rapyd->load('dataedit','datadetails');
		$script= '
		$(function() {
			$("#ingreso").datepicker({dateFormat:"dd/mm/yy"});
			$("#nacio").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$do = new DataObject('medhisto');
		//$do->rel_one_to_many('medhvisita', 'medhvisita', array('numero'=>'historia'));

		$edit = new DataEdit('', $do);

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );

		$edit->numero = new inputField('Historia Nro','numero');
		$edit->numero->rule='';
		$edit->numero->size =22;
		$edit->numero->maxlength =20;
		$edit->numero->readonly = true;
		$edit->numero->mode = 'autohide';
		$edit->numero->when = array('modify');
		//$edit->numero->hidden = true;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='strtoupper|required';
		$edit->nombre->size =52;
		$edit->nombre->maxlength =50;

		$edit->ingreso = new dateonlyField('Ingreso','ingreso');
		$edit->ingreso->rule='chfecha';
		$edit->ingreso->calendar=false;
		$edit->ingreso->size =10;
		$edit->ingreso->maxlength =8;
		$edit->ingreso->insertValue = date('Y-m-d');

		//************************************************
		//inicio detalle
		//************************************************
		$i=0;
		$sel=array('a.id','a.nombre','b.descripcion AS value','b.id AS itid');
		$this->db->from('medhtab AS a');
		$this->db->where('a.grupo','1');
		$historia=$edit->get_from_dataobjetct('numero');
		$dbhistoria = $this->db->escape($historia);
		$this->db->join('medhvisita AS b',"a.id=b.tabula AND b.historia = ${dbhistoria}",'left');

		$this->db->select($sel);
		$this->db->order_by('a.indice');
		$query = $this->db->get();
		foreach ($query->result() as $row){
			$obj='descripcion_'.$i;
			$edit->$obj = new inputField($row->nombre,'itdetalle['.$row->id.']');
			$edit->$obj->db_name     = '-';
			$edit->$obj->data        = null;
			$edit->$obj->size        = 20;
			$edit->$obj->maxlength   = 20;
			$edit->$obj->value       = $row->value;
			$edit->$obj->insertValue = $row->value;
			$edit->$obj->updateValue = $row->value;
			$edit->$obj->pointer     = true;

			$obj='itid_'.$i;
			$edit->$obj = new hiddenField('','itid['.$row->id.']');
			$edit->$obj->db_name     = '-';
			$edit->$obj->value       = $row->itid;
			$edit->$obj->insertValue = $row->itid;
			$edit->$obj->updateValue = $row->itid;
			$edit->$obj->pointer     = true;
			$edit->$obj->data        = null;
			$edit->$obj->in          = 'descripcion_'.$i;

			$i++;
		}
		//************************************************
		//fin de campos para detalle
		//************************************************

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));

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
		$mSQL = 'SELECT COUNT(*) AS cana FROM medhisto WHERE numero=';
		do {
			$numero   = $this->datasis->fprox_numero('nmedhis');
			$dbnumero = $this->db->escape($numero);
			$cuantos  = intval($this->datasis->dameval($mSQL.$dbnumero));
		} while ($cuantos != 0);

		$do->set('numero',$numero);
		$do->error_message_ar['pre_ins']='';
		return true;

		// Busca Cedula repetida
		//$cedula = $do->get('cedula');
		//$mSQL = "SELECT count(*) FROM medhisto WHERE cedula=".$cedula;
		//$cuantos = $this->datasis->dameval($mSQL);
		//if ( $cuantos > 0 ) {
		//	$error='ERROR. Cedula Repetida!!!!';
		//	$do->error_message_ar['pre_ins']=$error;
		//	return false;
		//} else {
		//	$numero = $this->datasis->fprox_numero('nmedhis');
		//	// REVISA SI EXISTE EL NRO
		//	$mSQL = "SELECT count(*) FROM medhisto WHERE numero=";
		//	$cuantos = $this->datasis->dameval($mSQL.$numero);
		//	while ($cuantos <> 0) {
		//		$numero = $this->datasis->fprox_numero('nmedhis');
		//		$cuantos = $this->datasis->dameval($mSQL.$numero);
		//	}
		//	$do->set('numero',$numero);
		//	$do->error_message_ar['pre_ins']='';
		//	return true;
		//}
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
		$historia = $do->get('numero');
		$fecha    = date('Y-m-d');

		$itdetalle = $this->input->post('itdetalle');
		if(is_array($itdetalle)){
			$data = array('historia' => $historia, 'fecha' => $fecha);
			foreach($itdetalle as $tabula=>$descrip){
				$tabula = intval($tabula);
				if($tabula>0 && !empty($descrip)){
					$data['descripcion']= $descrip;
					$data['tabula']     = $tabula;

					$sql = $this->db->insert_string('medhvisita', $data);
					$this->db->simple_query($sql);
				}
			}
		}

		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits ${primary}");
	}

	function _post_update($do){
		$historia = $do->get('numero');
		$fecha    = date('Y-m-d');

		$itid      = $this->input->post('itid');
		$itdetalle = $this->input->post('itdetalle');
		if(is_array($itdetalle)){
			$data = array('historia' => $historia, 'fecha' => $fecha);
			foreach($itdetalle as $tabula=>$descrip){
				$tabula = intval($tabula);
				if($tabula>0 && !empty($descrip)){
					$data['descripcion']= $descrip;
					$data['tabula']     = $tabula;

					if(isset($itid[$tabula])){
						$iitid = intval($itid[$tabula]);
					}else{
						$iitid = 0;
					}

					if($iitid>0){
						$sql   = $this->db->update_string('medhvisita', $data, 'id='.$iitid );
					}else{
						$sql = $this->db->insert_string('medhvisita', $data);
					}
					$this->db->simple_query($sql);
				}
			}
		}
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits ${primary} ");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		if (!$this->db->table_exists('medhisto')) {
			$mSQL="
			CREATE TABLE `medhisto` (
			  `numero` varchar(20) DEFAULT NULL,
			  `ingreso` date DEFAULT NULL,
			  `nombre` varchar(50) DEFAULT NULL,
			  `papellido` varchar(50) DEFAULT NULL,
			  `sapellido` varchar(50) DEFAULT NULL,
			  `nacional` int(11) DEFAULT NULL,
			  `cedula` varchar(20) DEFAULT NULL,
			  `sexo` int(11) DEFAULT NULL,
			  `nacio` date DEFAULT NULL,
			  `estado` varchar(50) DEFAULT NULL,
			  `ciudad` varchar(50) DEFAULT NULL,
			  `ecivil` int(11) DEFAULT NULL,
			  `ocupacion` varchar(50) DEFAULT NULL,
			  `direccion` text,
			  `telefono` varchar(50) DEFAULT NULL,
			  `referido` varchar(50) DEFAULT NULL,
			  `email` varchar(100) DEFAULT NULL,
			  `usuario` varchar(20) DEFAULT NULL,
			  `estampa` date DEFAULT NULL,
			  `hora` varchar(10) DEFAULT NULL,
			  `edad` varchar(255) DEFAULT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  KEY `numero` (`numero`)
			) ENGINE=InnoDB AUTO_INCREMENT=27260 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT COMMENT='Historias Medicas'

			";
			$this->db->query($mSQL);
		}
		//$campos=$this->db->list_fields('medhisto');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}

?>
