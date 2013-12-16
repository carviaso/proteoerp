<?php require_once(BASEPATH.'application/controllers/validaciones.php');
//crucecuentas
class Mvcerti extends validaciones {
	var $mModulo='MVCERTI';
	var $titp='Certificados Mision Vivienda';
	var $tits='Certificados Mision Vivienda';
	var $url ='ventas/mvcerti/';
	var $data_type = null;
	var $data = null;

	function mvcerti(){
		parent::Controller();
		$this->load->helper('url');
		$this->load->helper('text');
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'MVCERTI', $ventana=0 );
	}


	function index(){
		$this->datasis->modintramenu( 750, 550, substr($this->url,0,-1) );
		$this->datasis->modulo_id('13C',1);
		redirect($this->url.'jqdatag');
	}


	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"imprimir",   "img"=>"images/pdf_logo.gif",  "alt" => 'Formato PDF', "label"=>""));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '';

		//$param['WestPanel']  = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['funciones']   = $funciones;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('MVCERTI', 'JQ');
		$param['otros']       = $this->datasis->otros('MVCERTI', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$this->load->view('jqgrid/crud2',$param);

	}

	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';
		$ngrid      = '#newapi'.$grid0;

		$bodyscript .= $this->jqdatagrid->bsshow('mvcerti', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'mvcerti', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'mvcerti', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('mvcerti', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '280', '450' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '450' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '200', '400' );

		$bodyscript .= '});';
		$bodyscript .= '</script>';
		return $bodyscript;
	}



	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i = 1;
		$link  = site_url('ajax/buscascli');

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'hidden'   => 'true',
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 40,
			'editable' => 'false',
			'search'   => 'false'
		));

		$grid->addField('status');
		$grid->label('Estatus');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 50,
			'edittype'      => "'select'",
			'stype'         => "'text'",
			'edittype' => "'select'",
			'editoptions' => '{value: {"A":"ACTIVO", "C":"CERRADO"} }'
		));

		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('cliente');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 50,
			'edittype'      => "'text'",
			'editoptions' => '{'.$grid->autocomplete($link, 'cliente','clicli','<div id=\"clicli\">Nombre:<b>"+ui.item.nombre+"</b><br>RIF:<b>"+ui.item.rifci+"<b></div>').'}',
		));

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 280,
			'edittype'      => "'text'",
		));

		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 220,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:40, maxlength:32}'
		));

/*
		$grid->addField('obra');
		$grid->label('Obra');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 350,
			'edittype'      => "'textarea'",
			'editrules'     => '{required:true}',
			'editoptions'   => '{ rows:"2", cols:"60"}'


		));
*/
		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		//$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		//$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');

		$grid->setFormOptionsE('
			closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true,
			afterSubmit: function(a,b){
				if (a.responseText.length > 0) $.prompt(a.responseText);
					return [true, a ];
			},
			beforeShowForm: function(frm){
					$(\'<a href="#">MISION V<span class="ui-icon ui-icon-disk"></span></a>\').click(function(){
						consulmv();
					}).addClass("fm-button ui-state-default ui-corner-all fm-button-icon-left").prependTo("#Act_Buttons>td.EditButton");
				},
			afterShowForm: function(frm){$("select").selectmenu({style:"popup"});}'
		);

		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},
			beforeShowForm: function(frm){
					$(\'<a href="#">MISION V<span class="ui-icon ui-icon-disk"></span></a>\').click(function(){
						consulrmv();
					}).addClass("fm-button ui-state-default ui-corner-all fm-button-icon-left").prependTo("#Act_Buttons>td.EditButton");
				},
			afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} '
		);


		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		$grid->setOndblClickRow('');
		$grid->setAdd(    $this->datasis->sidapuede('MVCERTI','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('MVCERTI','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('MVCERTI','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('MVCERTI','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: mvcertiadd, editfunc: mvcertiedit, delfunc: mvcertidel, viewfunc: mvcertishow");

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
		$mWHERE = $grid->geneTopWhere('mvcerti');

		$response   = $grid->getData('view_mvcerti', array(array()), array(), false, $mWHERE );
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

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$this->db->insert('mvcerti', $data);
			}
			return "Registro Agregado";

		} elseif($oper == 'edit') {
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sfac WHERE certificado=".$this->db->escape($data['numero']));
			if ( $check > 0 ) {
				unset($data['numero']);
				unset($data['cliente']);
			}
			$this->db->where('id', $id);
			$this->db->update('mvcerti', $data);
			return "Registro Modificado";

		} elseif($oper == 'del') {
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sfac WHERE certificado=".$this->db->escape($data['numero']));
			if ($check > 0){
				echo " El certificado no puede ser eliminado, tiene facturas asignadas ";
			} else {
				$this->db->simple_query("DELETE FROM mvcerti WHERE id=$id ");
				logusu('mvcerti',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit','dataobject');
		$script= '

		function consulmv(){
			mnumero=$("#numero").val();
			if(mnumero.length==0){
				alert("Debe introducir primero el numero de certificado");
			}else{
				mnumero=mnumero.toUpperCase();
				$("#numero").val(mnumero);
				window.open("'.site_url('ventas/mvcerti/traepdf/').'/"+encodeURIComponent(mnumero),"CONSULTA MV","height=350,width=410");
			}
			return false;
		}

		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");

			$("#cliente").autocomplete({
				delay: 600,
				autoFocus: true,
				source: function(req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscascli').'",
						type: "POST",
						dataType: "json",
						data: {"q":req.term},
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#sclinombre").val("");
									$("#sclinombre_val").text("");
									$("#sclirifci").val("");
									$("#sclirifci_val").text("");
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
					$("#cliente").attr("readonly", "readonly");
					$("#sclinombre").val(ui.item.nombre);
					$("#sclinombre_val").text(ui.item.nombre);
					$("#sclirifci").val(ui.item.rifci);
					$("#sclirifci_val").text(ui.item.rifci);
					setTimeout(function() {  $("#cliente").removeAttr("readonly"); }, 1500);
				}
			});
		});';

		$do = new DataObject('mvcerti');
		$do->pointer('scli' ,'mvcerti.cliente =scli.cliente' ,'`scli`.`nombre`  AS sclinombre, `scli`.`rifci`  AS sclirifci'  ,'left');

		$edit = new DataEdit('', $do);
		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule='required|strtoupper|trim|unique';
		$edit->numero->mode='autohide';
		$edit->numero->size =34;
		$edit->numero->maxlength =32;
		$edit->numero->append('<a href="#" onclick="consulmv();">Consultar</a>');

		$edit->status = new  dropdownField('Estatus','status');
		$edit->status->option('A','Activo');
		$edit->status->option('C','Cerrado');
		$edit->status->style='width:120px;';
		$edit->status->rule='required|enum[A,C]';

		$edit->cliente = new inputField('Cliente','cliente');
		$edit->cliente->rule='required|existescli';
		$edit->cliente->size =7;

		$edit->nombre = new inputField('Nombre','sclinombre');
		$edit->nombre->type='inputhidden';
		$edit->nombre->pointer=true;
		$edit->nombre->in = 'cliente';

		$edit->rifci = new inputField('RIF/CI','sclirifci');
		$edit->rifci->type='inputhidden';
		$edit->rifci->pointer=true;

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->insertValue=date('Y-m-d');
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;
		$edit->fecha->calendar=false;

		$edit->obra= new textareaField('Obra','obra');
		$edit->obra->cols = 40;
		$edit->obra->rows = 2;
		$edit->obra->rule = 'required';
		//$edit->obra->maxlength =200;

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
		$numero  = $do->get('numero');
		$dbnumero= $this->db->escape($numero);

		$mSQL='SELECT COUNT(*) AS cana FROM sfac WHERE certificado='.$dbnumero;
		$cana=$this->datasis->dameval($mSQL);
		if(empty($cana)){
			return true;
		}
		$do->error_message_ar['pre_del']='No se puede eliminar el certificado por que ya fue utilizado';
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
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits ${primary} ");
	}


	function traepdf($certificado){
		$this->load->helper('pdf2text');

		$host='http://www.minvih.gob.ve/constancia/index.php/consulta';
		$data=array('ConsultaForm[codigo]'=>$certificado);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $host);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3');
		$output = curl_exec($ch);
		$error  = curl_errno($ch);
		$derror = curl_error($ch);
		curl_close($ch);
		if(stripos($output,'errores de ingreso')===false){
			$tt=fluj2text($output);
			$desde=stripos($tt,'Se hace constar');
			$msj=substr($tt,$desde);
		}else{
			$msj='Certificado no encontrado';
		}

		$data['content'] = utf8_encode($msj);
		$data['head']    = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
		$this->load->view('view_ventanas_sola', $data);
	}

	function instalar(){
		if (!$this->db->table_exists('mvcerti')) {
			$mSQL = "CREATE TABLE mvcerti (
					id BIGINT(20) NOT NULL AUTO_INCREMENT,
					cliente CHAR(5) NULL DEFAULT NULL COMMENT 'Codigo del Cliente',
					numero CHAR(32) NULL DEFAULT NULL COMMENT 'Numero de Certificado',
					fecha DATE NULL DEFAULT NULL COMMENT 'Fecha del certificado',
					obra VARCHAR(200) NULL DEFAULT NULL COMMENT 'Nombre de la Obra',
					status CHAR(1) NULL DEFAULT 'A' COMMENT 'Activo Cerrado',
					PRIMARY KEY (id),
					UNIQUE INDEX numero (numero),
					INDEX cliente (cliente)
				)
				COLLATE='latin1_swedish_ci'
				ENGINE=MyISAM
				ROW_FORMAT=DEFAULT";
			$this->db->simple_query($mSQL);

			$mSQL = "CREATE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `view_mvcerti` AS
				select `a`.`id` AS `id`,if((`a`.`status` = 'A'),'ACTIVO','CERRADO') AS `status`,`a`.`cliente` AS `cliente`,`b`.`nombre` AS `nombre`,`a`.`fecha` AS `fecha`,`a`.`numero` AS `numero`,`a`.`obra` AS `obra`
				from (`mvcerti` `a` join `scli` `b` on((`a`.`cliente` = `b`.`cliente`)))
				order by `a`.`id` desc";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('view_mvcerti')){
			$mSQL = "CREATE ALGORITHM=UNDEFINED
					DEFINER=`".$this->db->username."`@`".$this->db->hostname."`
					SQL SECURITY INVOKER VIEW `view_mvcerti` AS
					select `a`.`id` AS `id`,if((`a`.`status` = 'A'),'ACTIVO','CERRADO') AS `status`,`a`.`cliente` AS `cliente`,`b`.`nombre` AS `nombre`,`a`.`fecha` AS `fecha`,`a`.`numero` AS `numero`,`a`.`obra` AS `obra`
					from (`mvcerti` `a` join `scli` `b` on((`a`.`cliente` = `b`.`cliente`)))
					order by `a`.`id` desc";
			$this->db->simple_query($mSQL);
		}
	}
}
