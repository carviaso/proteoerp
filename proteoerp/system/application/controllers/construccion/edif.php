<?php
class Edif extends Controller {
	var $mModulo = 'EDIF';
	var $titp    = 'Edificaciones';
	var $tits    = 'Edificaciones';
	var $url     = 'construccion/edif/';

	function Edif(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'EDIF', $ventana=0 );
	}

	function index(){
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

		$WpAdic = "
		<tr><td><div class=\"anexos\">
			<table cellpadding='0' cellspacing='0'>
				<tr>
					<td style='vertical-align:top;'><div class='botones'><a style='width:94px;text-align:left;vertical-align:top;' href='#' id='gtipo'>".img(array('src' =>"images/tux1.png",  'height' => 15, 'alt' => 'Tipos de Edificacion',    'title' => 'Tipo de Edificacion',   'border'=>'0'))." Tipos</a></div></td>
					<td style='vertical-align:top;'><div class='botones'><a style='width:94px;text-align:left;vertical-align:top;' href='#' id='gusos'>".img(array('src' =>"images/unidad.gif",'height' => 15, 'alt' => 'Usos de las areas',  'title' => 'Usos de los locales', 'border'=>'0'))." Usos</a></div></td>
				</tr>
				<tr>
					<td style='vertical-align:top;' colspan='2'><div class='botones'><a style='width:190px;text-align:left;vertical-align:top;' href='#' id='gubica'>".img(array('src' =>"images/basura.png", 'height' => 15, 'alt'=>'Mostrar/Ocultar Inactivos', 'title' => 'Mostrar/Ocultar Inactivos', 'border'=>'0'))." Areas</a></div></td>
				</tr>
			</table>
			</div>
		</td></tr>\n
		";

		$grid->setWpAdicional($WpAdic);



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
		$param['listados']    = $this->datasis->listados('EDIF', 'JQ');
		$param['otros']       = $this->datasis->otros('EDIF', 'JQ');
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


		// Tipos de Edificaciones
		$bodyscript .= '
		$("#gtipo").click(function(){
			$.post("'.site_url('construccion/edif/tipoform').'",
			function(data){
				$("#fshow").html(data);
				$("#fshow").dialog( { title:"TIPOS", width: 320, height: 400, modal: true } );
				$("#fshow").dialog( "open" );
			});
		});';

		// Usos de las Areas
		$bodyscript .= '
		$("#gusos").click(function(){
			$.post("'.site_url('construccion/edif/usosform').'",
			function(data){
				$("#fshow").html(data);
				$("#fshow").dialog( { title:"USOS", width: 320, height: 400, modal: true } );
				$("#fshow").dialog( "open" );
			});
		});';

		// Ubicacion interna
		$bodyscript .= '
		$("#gubica").click(function(){
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("'.$ngrid.'").getRowData(id);
				$.post("'.site_url('construccion/edif/ubicaform')."/".'"+id,
				function(data){
					$("#fshow").html(data);
					$("#fshow").dialog( { title:"AREAS DE LA EDIFICACION", width: 320, height: 400, modal: true } );
					$("#fshow").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione una Edificacion</h1>");
			}
		});';


		$bodyscript .= $this->jqdatagrid->bsshow('edif', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'edif', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'edif', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('edif', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '350', '600' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

		$bodyscript .= '});';

		$bodyscript .= '</script>';

		return $bodyscript;
	}

	//******************************************************************
	// Forma de Tipos
	//
	function tipoform(){
		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('ID');
		$grid->params(array(
			'hidden'      => 'true',
			'align'       => "'center'",
			'width'       => 20,
			'editable'    => 'false',
			'editoptions' => '{readonly:true,size:10}'
			)
		);

		$grid->addField('descrip');
		$grid->label('Tipo');
		$grid->params(array(
			'width'     => 180,
			'editable'  => 'true',
			'edittype'  => "'text'",
			'editrules' => '{required:true}'
			)
		);

		$grid->showpager(true);
		$grid->setViewRecords(false);
		$grid->setWidth('300');
		$grid->setHeight('240');

		$grid->setUrlget(site_url('construccion/edif/tipogetdata/'));
		$grid->setUrlput(site_url('construccion/edif/tiposetdata/'));

		$mgrid = $grid->deploy();

		$msalida  = '<script type="text/javascript">'."\n";
		$msalida .= '
		$("#newapi'.$mgrid['gridname'].'").jqGrid({
			ajaxGridOptions : {type:"POST"}
			,jsonReader : { root:"data", repeatitems: false }
			'.$mgrid['table'].'
			,scroll: true
			,pgtext: null, pgbuttons: false, rowList:[]
		})
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'navGrid\',  "#pnewapi'.$mgrid['gridname'].'",{edit:false, add:false, del:true, search: false});
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'inlineNav\',"#pnewapi'.$mgrid['gridname'].'");
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'filterToolbar\');
		';

		$msalida .= "\n</script>\n";
		$msalida .= '<id class="anexos"><table id="newapi'.$mgrid['gridname'].'"></table>';
		$msalida .= '<div   id="pnewapi'.$mgrid['gridname'].'"></div></div>';

		echo $msalida;

	}

	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function tipogetdata(){
		$grid       = $this->jqdatagrid;
		$mWHERE = $grid->geneTopWhere('ediftipo');
		$response   = $grid->getData('ediftipo', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion del Grid o Tabla
	//
	function tiposetData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		$id = str_replace('jqg','',$id);
		unset($data['oper']);
		unset($data['id']);

		// ver si puede borrar
		if ($oper == 'del') {
			// si tiene edificios no puede borrar
			$id   = $this->input->post('id');
			$mSQL = "SELECT COUNT(*) FROM edif WHERE tipo=$id";
			if ($this->datasis->dameval($mSQL) == 0 ){
				$grid     = $this->jqdatagrid;

				$response = $grid->operations('ediftipo','id');
				//$this->db->query("DELETE FROM ediftipo WHERE id=$id ");
				logusu('EDIFTIPO',"Registro $id ELIMINADO");


				echo 'Registro Borrado!!!';
			} else {
				echo 'No se puede borrar, existen edificios con ese tipo';
			}
		} elseif($oper == 'edit') {
			$this->db->where('id', $id);
			$this->db->update('ediftipo', $data);
			logusu('EDIFTIPO',"Registro $id MODIFICADO");
			echo $mSQL;

		} elseif($oper == 'add'){
			if(false == empty($data)){
				$this->db->insert('ediftipo', $data);
				echo "Registro Agregado";
				logusu('EDIFTIPO',"Registro  INCLUIDO");
			} else
			echo "Fallo Agregado!!!";
		}
	}


	//******************************************************************
	// Forma de Usos
	//
	function usosform(){
		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('ID');
		$grid->params(array(
			'hidden'      => 'true',
			'align'       => "'center'",
			'width'       => 20,
			'editable'    => 'false',
			'editoptions' => '{readonly:true,size:10}'
			)
		);

		$grid->addField('uso');
		$grid->label('Uso');
		$grid->params(array(
			'width'     => 180,
			'editable'  => 'true',
			'edittype'  => "'text'",
			'editrules' => '{required:true}'
			)
		);

		$grid->showpager(true);
		$grid->setViewRecords(false);
		$grid->setWidth('300');
		$grid->setHeight('240');

		$grid->setUrlget(site_url('construccion/edif/usosgetdata/'));
		$grid->setUrlput(site_url('construccion/edif/usossetdata/'));

		$mgrid = $grid->deploy();

		$msalida  = '<script type="text/javascript">'."\n";
		$msalida .= '
		$("#newapi'.$mgrid['gridname'].'").jqGrid({
			ajaxGridOptions : {type:"POST"}
			,jsonReader : { root:"data", repeatitems: false }
			'.$mgrid['table'].'
			,scroll: true
			,pgtext: null, pgbuttons: false, rowList:[]
		})
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'navGrid\',  "#pnewapi'.$mgrid['gridname'].'",{edit:false, add:false, del:true, search: false});
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'inlineNav\',"#pnewapi'.$mgrid['gridname'].'");
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'filterToolbar\');
		';

		$msalida .= "\n</script>\n";
		$msalida .= '<id class="anexos"><table id="newapi'.$mgrid['gridname'].'"></table>';
		$msalida .= '<div   id="pnewapi'.$mgrid['gridname'].'"></div></div>';

		echo $msalida;

	}

	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function usosgetdata(){
		$grid     = $this->jqdatagrid;
		$mWHERE   = $grid->geneTopWhere('eduso');
		$response = $grid->getData('eduso', array(array()), array(), false, $mWHERE );

		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion del Grid o Tabla
	//
	function usossetData( $mid = 0 ){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		$id = str_replace('jqg','',$id);
		unset($data['oper']);
		unset($data['id']);

		// ver si puede borrar
		if ($oper == 'del') {
			$grid     = $this->jqdatagrid;
			$response = $grid->operations('eduso','id');
			echo 'Registro Borrado!!!';
		} elseif($oper == 'edit') {
			$id       = $this->input->post('id');
			$grid     = $this->jqdatagrid;
			$response = $grid->operations('eduso','id');
			echo 'Registro Borrado!!!';


		} elseif($oper == 'add'){
			if(false == empty($data)){
				$this->db->insert('eduso', $data);
				echo "Registro Agregado";
				logusu('EDUSO',"Registro INCLUIDO");
			} else
			echo "Fallo Agregado!!!";
		}
	}



	//******************************************************************
	// Forma de Tipos
	//
	function ubicaform( $mid = 0 ){
		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('ID');
		$grid->params(array(
			'hidden'      => 'true',
			'align'       => "'center'",
			'width'       => 20,
			'editable'    => 'false',
			'editoptions' => '{readonly:true,size:10}'
			)
		);

		$grid->addField('descripcion');
		$grid->label('Tipo');
		$grid->params(array(
			'width'     => 180,
			'editable'  => 'true',
			'edittype'  => "'text'",
			'editrules' => '{required:true}'
			)
		);

		$grid->showpager(true);
		$grid->setViewRecords(false);
		$grid->setWidth('300');
		$grid->setHeight('240');

		$grid->setUrlget(site_url('construccion/edif/ubicagetdata/')."/$mid");
		$grid->setUrlput(site_url('construccion/edif/ubicasetdata/')."/$mid");

		$mgrid = $grid->deploy();

		$msalida  = '<script type="text/javascript">'."\n";
		$msalida .= '
		$("#newapi'.$mgrid['gridname'].'").jqGrid({
			ajaxGridOptions : {type:"POST"}
			,jsonReader : { root:"data", repeatitems: false }
			'.$mgrid['table'].'
			,scroll: true
			,pgtext: null, pgbuttons: false, rowList:[]
		})
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'navGrid\',  "#pnewapi'.$mgrid['gridname'].'",{edit:false, add:false, del:true, search: false});
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'inlineNav\',"#pnewapi'.$mgrid['gridname'].'");
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'filterToolbar\');
		';

		$msalida .= "\n</script>\n";
		$msalida .= '<id class="anexos"><table id="newapi'.$mgrid['gridname'].'"></table>';
		$msalida .= '<div   id="pnewapi'.$mgrid['gridname'].'"></div></div>';

		echo $msalida;

	}

	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function ubicagetdata( $mid = 0 ){
		$grid       = $this->jqdatagrid;
		$mWHERE = $grid->geneTopWhere('edifubica');
		$mWHERE[] = array('', 'id_edif', $mid, '' );
		$response   = $grid->getData('edifubica', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion del Grid o Tabla
	//
	function ubicasetData( $mid = 0 ){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		$id = str_replace('jqg','',$id);
		unset($data['oper']);
		unset($data['id']);

		// ver si puede borrar
		if ($oper == 'del') {
			$id       = $this->input->post('id');
			$grid     = $this->jqdatagrid;
			$response = $grid->operations('edifubica','id');
			echo 'Registro Borrado!!!';

		} elseif($oper == 'edit') {
			$data['id_edif'] = $mid;
			$this->db->where('id', $id);
			$this->db->update('edifubica', $data);
			logusu('EDIFUBICA',"Registro $id MODIFICADO");
			echo $mSQL;

		} elseif($oper == 'add'){
			if(false == empty($data)){
				$data['id_edif'] = $mid;
				$this->db->insert('edifubica', $data);
				echo "Registro Agregado";
				logusu('EDIFUBICA',"Registro  INCLUIDO");
			} else
			echo "Fallo Agregado!!!";
		}
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
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:120, maxlength: 120 }',
		));


		$grid->addField('tipo');
		$grid->label('Tipo');
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


		$grid->addField('direccion');
		$grid->label('Direccion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('descripcion');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('promotora');
		$grid->label('Promotora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
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
		$grid->setAdd(    $this->datasis->sidapuede('EDIF','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('EDIF','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('EDIF','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('EDIF','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: edifadd, editfunc: edifedit, delfunc: edifdel, viewfunc: edifshow");

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
		$mWHERE = $grid->geneTopWhere('edif');

		$response   = $grid->getData('edif', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM edif WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('edif', $data);
					echo "Registro Agregado";

					logusu('EDIF',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM edif WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM edif WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE edif SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("edif", $data);
				logusu('EDIF',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('edif', $data);
				logusu('EDIF',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM edif WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM edif WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM edif WHERE id=$id ");
				logusu('EDIF',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	// Edicion 

	function dataedit(){
		$this->rapyd->load('dataedit');

		$scli=array(
		'tabla'   =>'scli',
		'columnas'=>array(
			'cliente' =>'C&oacute;digo Cliente',
			'nombre'=>'Nombre',
			'contacto'=>'Contacto'),
		'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'promotora'),
		'titulo'  =>'Buscar Cliente');

		$boton=$this->datasis->modbus($scli);

		$edit = new DataEdit('', 'edif');
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('update','_pre_update');
		$edit->pre_process('delete','_pre_delete');

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[120]|required';
		$edit->nombre->maxlength =120;

		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option('','Seleccionar');
		$edit->tipo->options('SELECT id,descrip FROM `ediftipo` ORDER BY descrip');
		$edit->tipo->rule='max_length[10]|required';

		$edit->direccion = new textareaField('Direcci&oacute;n','direccion');
		$edit->direccion->cols = 70;
		$edit->direccion->rows = 4;

		$edit->descripcion = new textareaField('Descripci&oacute;n','descripcion');
		$edit->descripcion->cols = 70;
		$edit->descripcion->rows = 4;

		$edit->promotora = new inputField('Promotora','promotora');
		$edit->promotora->rule='max_length[5]|existescli';
		$edit->promotora->size =7;
		$edit->promotora->maxlength =5;
		$edit->promotora->append($boton);

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
		if (!$this->db->table_exists('edif')) {
			$mSQL="CREATE TABLE `edif` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `nombre` char(120) DEFAULT NULL,
			  `tipo` int(10) DEFAULT NULL,
			  `direccion` text,
			  `descripcion` text,
			  `promotora` char(5) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Edificaciones'";
			$this->db->query($mSQL);
		}
		//$campos=$this->db->list_fields('edif');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}


/*
include('ediftipo.php');
class edif extends Controller {
	var $titp='Edificaciones';
	var $tits='Edificaciones';
	var $url ='construccion/edif/';

	function edif(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('A00',1);
		$this->instalar();
	}

	function index(){
		redirect($this->url.'filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		$this->load->helper('text');

		$filter = new DataFilter($this->titp, 'edif');

		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->rule      ='max_length[120]';
		$filter->nombre->maxlength =120;

		$filter->tipo = new dropdownField('Tipo','tipo');
		$filter->tipo->option('','Todos');
		$filter->tipo->options('SELECT id,descrip FROM `ediftipo` ORDER BY descrip');

		$filter->direccion = new inputField('Direcci&oacute;n','direccion');
		$filter->direccion->rule      ='max_length[8]';

		$filter->descripcion = new inputField('Descripci&oacute;n','descripcion');
		$filter->descripcion->rule      ='max_length[8]';

		$filter->promotora = new inputField('Promotora','promotora');
		$filter->promotora->rule      ='max_length[5]';
		$filter->promotora->size      =7;
		$filter->promotora->maxlength =5;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->use_function('character_limiter');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('N&uacute;mero'     ,$uri,'id','align="left"');
		$grid->column_orderby('Nombre'            ,'nombre','nombre','align="left"');
		$grid->column_orderby('Tipo'              ,'<nformat><#tipo#></nformat>','tipo','align="right"');
		$grid->column_orderby('Direcci&oacute;n'  ,'<character_limiter><#direccion#></character_limiter>','direccion','align="left"');
		$grid->column_orderby('Descripci&oacute;n','<character_limiter><#descripcion#></character_limiter>','descripcion','align="left"');
		$grid->column_orderby('Promotora'         ,'promotora','promotora','align="left"');

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

		$scli=array(
		'tabla'   =>'scli',
		'columnas'=>array(
			'cliente' =>'C&oacute;digo Cliente',
			'nombre'=>'Nombre',
			'contacto'=>'Contacto'),
		'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'promotora'),
		'titulo'  =>'Buscar Cliente');

		$boton=$this->datasis->modbus($scli);

		$edit = new DataEdit($this->tits, 'edif');

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('update','_pre_update');
		$edit->pre_process('delete','_pre_delete');

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[120]|required';
		$edit->nombre->maxlength =120;

		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option('','Seleccionar');
		$edit->tipo->options('SELECT id,descrip FROM `ediftipo` ORDER BY descrip');
		$edit->tipo->rule='max_length[10]|required';

		$edit->direccion = new textareaField('Direcci&oacute;n','direccion');
		//$edit->direccion->rule='max_length[255]';
		$edit->direccion->cols = 70;
		$edit->direccion->rows = 4;

		$edit->descripcion = new textareaField('Descripci&oacute;n','descripcion');
		//$edit->descripcion->rule='max_length[512]';
		$edit->descripcion->cols = 70;
		$edit->descripcion->rows = 4;

		$edit->promotora = new inputField('Promotora','promotora');
		$edit->promotora->rule='max_length[5]|existescli';
		$edit->promotora->size =7;
		$edit->promotora->maxlength =5;
		$edit->promotora->append($boton);

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back','add');
		$edit->build();
		$data['content'] = $edit->output;
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
		ediftipo::instalar();
		if (!$this->db->table_exists('edif')) {
			$mSQL="CREATE TABLE `edif` (
				  `id` INT(11) NOT NULL AUTO_INCREMENT,
				  `nombre` CHAR(120) NULL DEFAULT NULL,
				  `tipo` INT(10) NULL DEFAULT NULL,
				  `direccion` TEXT NULL,
				  `descripcion` TEXT NULL,
				  `promotora` CHAR(5) NULL DEFAULT NULL,
				  PRIMARY KEY (`id`)
				  )
				  COMMENT='Edificaciones'
				  COLLATE='latin1_swedish_ci'
				  ENGINE=MyISAM
				  ROW_FORMAT=DEFAULT";
			$this->db->simple_query($mSQL);
		}
	}

}
*/
?>
