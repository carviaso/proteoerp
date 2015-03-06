<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
class Masco extends Controller {
	var $mModulo = 'MASCO';
	var $titp    = 'Mascotas';
	var $tits    = 'Mascotas';
	var $url     = 'ventas/masco/';

	function Masco(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'MASCO', $ventana=0, $this->titp  );
	}

	function index(){
		$this->instalar();
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
		//$grid->wbotonadd(array("id"=>"funcion",   "img"=>"images/engrana.png",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro'),
			array('id'=>'scliexp', 'title'=>'Ficha de Cliente' ),
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('MASCO', 'JQ');
		$param['otros']       = $this->datasis->otros('MASCO', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('masco', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'masco', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'masco', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('masco', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '500', '450' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '500', '450' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '450' );

		$bodyscript .= '
		$("#scliexp").dialog({
			autoOpen:false, modal:true, width:500, height:350,
			buttons: {
				"Guardar": function(){
					var murl = $("#sclidialog").attr("action");
					$.ajax({
						type: "POST", dataType: "json", async: false,
						url: murl,
						data: $("#sclidialog").serialize(),
						success: function(r,s,x){
							if(r.status=="B"){
								$("#sclidialog").find(".alert").html(r.mensaje);
							}else{
								$("#scliexp").dialog( "close" );
								$("#id_scli").val(r.data.id);

								$("#sclicliente").val(r.data.cliente);

								$("#sclinombre").val(r.data.nombre);
								$("#sclinombre_val").text(r.data.nombre);

								$("#sclirifci").val(r.data.rifci);
								$("#sclirifci_val").text(r.data.rifci);

								return true;
							}
						}
					});

				},
				"Cancelar": function(){
					$("#scliexp").html("");
					$(this).dialog("close");
				}
			},
			close: function(){
				$("#scliexp").html("");
			}
		});';

		$bodyscript .= '});';
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

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->addField('id_scli');
		$grid->label('Id_scli');
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


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:200, maxlength: 200 }',
		));


		$grid->addField('sexo');
		$grid->label('Sexo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('nacimiento');
		$grid->label('Nacimiento');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('fbaja');
		$grid->label('Fec.Baja');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('falta');
		$grid->label('Fec.Alta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('caracter');
		$grid->label('Caracter');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('id_habitat');
		$grid->label('Id_habitat');
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


		$grid->addField('id_raza');
		$grid->label('Id_raza');
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


		$grid->addField('pedigri');
		$grid->label('Pedigri');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
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

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('MASCO','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('MASCO','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('MASCO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('MASCO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: mascoadd, editfunc: mascoedit, delfunc: mascodel, viewfunc: mascoshow');

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
		$mWHERE = $grid->geneTopWhere('masco');

		$response   = $grid->getData('masco', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM masco WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('masco', $data);
					echo "Registro Agregado";

					logusu('MASCO',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM masco WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM masco WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE masco SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("masco", $data);
				logusu('MASCO',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('masco', $data);
				logusu('MASCO',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM masco WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM masco WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM masco WHERE id=$id ");
				logusu('MASCO',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	// Edicion

	function dataedit(){
		$this->rapyd->load('dataobject','dataedit');
		$script= "
		$(function() {
			$('#falta').datepicker({dateFormat:'dd/mm/yy'});
			$('#fbaja').datepicker({dateFormat:'dd/mm/yy'});
			$('#nacimiento').datepicker({dateFormat:'dd/mm/yy'});
			$('.inputnum').numeric('.');

			$('#sclicliente').autocomplete({
				source: function( req, add){
					$.ajax({
						url:  '".site_url('ajax/buscascli')."',
						type: 'POST',
						dataType: 'json',
						data: {'q':req.term},
						success:
							function(data){
								var sugiere = [];

								if(data.length==0){
									$('#id_scli').val('');
									$('#sclinombre').val('');
									$('#sclinombre_val').text('');
									$('#sclirifci').val('');
									$('#sclirifci_val').text('');

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
				select: function( event, ui ){
					$('#sclinombre').attr('readonly', 'readonly');

					$('#id_scli').val(ui.item.id);
					$('#sclinombre').val(ui.item.nombre);
					$('#sclinombre_val').text(ui.item.nombre);
					$('#sclirifci').val(ui.item.rifci);
					$('#sclirifci_val').text(ui.item.rifci);

					setTimeout(function() {  $('#sclinombre').removeAttr('readonly'); }, 1500);
				}
			});

		});

		function scliadd() {
			$.post('".site_url('ventas/scli/dataeditdialog/create')."', function(data){
				$('#scliexp').html(data);
				$('#scliexp').dialog('open');
			});
		};
		";

		$do = new DataObject('masco');
		$do->pointer('scli' ,'scli.id=masco.id_scli','scli.nombre AS sclinombre, scli.rici AS sclirifci,scli.cliente AS sclicliente','left');

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

		$edit->sclicliente = new inputField('Cliente','sclicliente');
		$edit->sclicliente->pointer= true;
		$edit->sclicliente->size =20;
		$edit->sclicliente->group='Datos del due&ntilde;o';
		$edit->sclicliente->append('<a href="#" title="Agregar nuevo cliente" onClick="scliadd();" >'.image('add1-.png','Agregar nuevo cliente',array('title'=>'Agregar nuevo cliente')).'</a>');

		$edit->sclinombre = new inputField('Nombre de due&ntilde;o','sclinombre');
		$edit->sclinombre->pointer= true;
		$edit->sclinombre->size =30;
		$edit->sclinombre->group='Datos del due&ntilde;o';
		$edit->sclinombre->type = 'inputhidden';

		$edit->sclirifci = new inputField('C&eacute;dula','sclirifci');
		$edit->sclirifci->pointer= true;
		$edit->sclirifci->type = 'inputhidden';
		$edit->sclirifci->group='Datos del due&ntilde;o';

		$edit->id_scli = new hiddenField('id','id_scli');
		$edit->id_scli->rule='integer';
		$edit->id_scli->css_class='inputonlynum';
		$edit->id_scli->size =13;
		$edit->id_scli->maxlength =11;
		$edit->id_scli->in = 'sclinombre';

		$edit->nombre = new inputField('Nombre Mascota','nombre');
		$edit->nombre->rule='';
		$edit->nombre->size =50;
		$edit->nombre->maxlength =200;
		$edit->nombre->rule='required';

		$edit->id_habitat = new dropdownField('Habitat','id_habitat');
		$edit->id_habitat->option('' ,'Seleccionar');
		$edit->id_habitat->options('SELECT id, nombre FROM mascohabitat ORDER BY nombre');

		$razas=array();
		$mSQL='SELECT a.id,b.nombre AS especie,a.nombre
			FROM mascorazas AS a
			JOIN mascoespecies AS b ON a.id_mascoespecies=b.id
		ORDER BY b.id,a.nombre';
		$query = $this->db->query($mSQL);
		foreach ($query->result() as $row){
			$razas[$row->especie][$row->id]=$row->nombre;
		}
		//print_r($razas);exit();

		$edit->id_raza = new dropdownField('Raza','id_raza',$razas);
		//$edit->id_raza->option('' ,'Seleccionar');
		//$edit->id_raza->options($razas);

		$edit->sexo = new dropdownField('Sexo','sexo');
		$edit->sexo->option('' ,'Seleccionar');
		$edit->sexo->option('M','Masculino'   );
		$edit->sexo->option('F','Femenino'    );
		$edit->sexo->option('H','Hermafrodita');
		$edit->sexo->option('I','Indefinido'  );
		$edit->sexo->rule = 'required';

		$edit->nacimiento = new dateonlyField('Nacimiento','nacimiento');
		$edit->nacimiento->rule='chfecha';
		$edit->nacimiento->calendar=false;
		$edit->nacimiento->size =15;
		$edit->nacimiento->maxlength =8;

		$edit->fbaja = new dateonlyField('Fecha de baja','fbaja');
		$edit->fbaja->rule='chfecha';
		$edit->fbaja->calendar=false;
		$edit->fbaja->size =15;
		$edit->fbaja->maxlength =8;
		$edit->fbaja->insertValue = date('Y-m-d');

		$edit->falta = new dateonlyField('Fecha de alta','falta');
		$edit->falta->rule='chfecha';
		$edit->falta->calendar=false;
		$edit->falta->size =15;
		$edit->falta->maxlength =8;

		$edit->caracter = new dropdownField('Car&aacute;cter','caracter');
		$edit->caracter->option(''   ,'Seleccionar ');
		$edit->caracter->option('AGRESIVO'   ,'Agresivo');
		$edit->caracter->option('MALO'       ,'Malo');
		$edit->caracter->option('NORMAL'     ,'Normal');
		$edit->caracter->option('BUENO'      ,'Bueno');
		$edit->caracter->option('DESCONFIADO','Desconfiado' );
		$edit->caracter->option('TRANQUILO'  ,'Tranquilo' );
		$edit->caracter->option('DOCIL'      ,'Docil');
		$edit->caracter->rule = 'required';

		$edit->pedigri= new checkboxField('Pedigri','pedigri', 'S','N');
		$edit->pedigri->insertValue = 'N';

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
		if (!$this->db->table_exists('masco')) {
			$mSQL="CREATE TABLE masco (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  id_scli    INT(11)      DEFAULT NULL,
			  nombre     VARCHAR(200) DEFAULT NULL,
			  sexo       CHAR(1)      DEFAULT NULL,
			  nacimiento DATE         DEFAULT NULL,
			  fbaja      DATE         DEFAULT NULL,
			  falta      DATE         DEFAULT NULL,
			  caracter   VARCHAR(20)  DEFAULT NULL,
			  id_habitat INT(11)      DEFAULT NULL,
			  id_raza    INT(11)      DEFAULT NULL,
			  pedigri    CHAR(1)      DEFAULT NULL,
			  PRIMARY KEY (id)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->query($mSQL);
		}

		if(!$this->db->table_exists('mascohabitat')){
			$mSQL="CREATE TABLE `mascohabitat` (
				id      INT(11)      NOT NULL AUTO_INCREMENT,
				nombre  VARCHAR(50)  NULL DEFAULT NULL,
				descrip VARCHAR(200) NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			)
			COMMENT='Habitat de mascotas'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);

			$this->db->simple_query("INSERT INTO `mascohabitat` (`nombre`, `descrip`) VALUES ('CAMPO', NULL)");
			$this->db->simple_query("INSERT INTO `mascohabitat` (`nombre`, `descrip`) VALUES ('CASA', NULL)");
			$this->db->simple_query("INSERT INTO `mascohabitat` (`nombre`, `descrip`) VALUES ('INTERPERIE', NULL)");
			$this->db->simple_query("INSERT INTO `mascohabitat` (`nombre`, `descrip`) VALUES ('JARDIN', NULL)");
			$this->db->simple_query("INSERT INTO `mascohabitat` (`nombre`, `descrip`) VALUES ('CRIADERO', NULL)");
			$this->db->simple_query("INSERT INTO `mascohabitat` (`nombre`, `descrip`) VALUES ('URBANO', NULL)");
			$this->db->simple_query("INSERT INTO `mascohabitat` (`nombre`, `descrip`) VALUES ('APARTAMENTO', NULL)");
			$this->db->simple_query("INSERT INTO `mascohabitat` (`nombre`, `descrip`) VALUES ('INDUSTRIAL', NULL)");
		}

		if(!$this->db->table_exists('mascoespecies')){
			$mSQL="
			CREATE TABLE `mascoespecies` (
				id       INT(11)      NOT NULL AUTO_INCREMENT,
				nombre   VARCHAR(50)  NOT NULL,
				descrip  VARCHAR(200) NOT NULL,
				PRIMARY KEY (`id`)
			)
			COMMENT='Especies de mascota'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);

			$this->db->simple_query("INSERT INTO mascoespecies (nombre, descrip) VALUES ('CANINA', '')");
			$this->db->simple_query("INSERT INTO mascoespecies (nombre, descrip) VALUES ('FELINA', '')");
		}

		if(!$this->db->table_exists('mascorazas')){
			$mSQL="
			CREATE TABLE `mascorazas` (
				id               INT(11)     NOT NULL AUTO_INCREMENT,
				id_mascoespecies INT(11)     NULL DEFAULT NULL,
				nombre           VARCHAR(50) NULL DEFAULT NULL,
				PRIMARY KEY (id)
			)
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);

			$this->db->simple_query("INSERT INTO `mascorazas` (`id_mascoespecies`, `nombre`) VALUES (1, 'BOXER')");
			$this->db->simple_query("INSERT INTO `mascorazas` (`id_mascoespecies`, `nombre`) VALUES (1, 'MESTIZO')");
			$this->db->simple_query("INSERT INTO `mascorazas` (`id_mascoespecies`, `nombre`) VALUES (2, 'MESTIZO')");
		}

	}
}
