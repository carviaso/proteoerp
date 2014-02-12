<?php
class Casi extends Controller {
	var $mModulo='CASI';
	var $titp='Comprobantes de Contabilidad';
	var $tits='Comprobantes de Contabilidad';
	var $url ='contabilidad/casi/';

	function Casi(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'CASI', $ventana=0 );
		$this->chrepetidos = array();
	}

	function index(){
		$mSQL='UPDATE itcasi JOIN casi ON itcasi.comprob=casi.comprob SET itcasi.idcasi=casi.id WHERE itcasi.idcasi IS NULL';
		$this->db->simple_query($mSQL);

		$this->instalar();
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$grid->setHeight('145');
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$param['grids'][] = $grid1->deploy();

		$readyLayout = $grid->readyLayout2( 212, 220, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'bimp'  , 'img'=>'assets/default/images/print.png', 'alt' => 'Reiprimir Asiento', 'label'=>'Imprimir Asiento'));
		$grid->wbotonadd(array('id'=>'boton4', 'img'=>'images/checklist.png'           , 'alt' => 'Auditoria'        , 'label'=>'Herramientas'));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita' , 'title'=>'Agregar/Editar Pedido'),
			array('id'=>'fshow'  , 'title'=>'Mostrar registro')
		);

		//Panel Central y Sur
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '
		function ltransac(el, val, opts){
			var meco=\'<div><a href="#" onclick="tconsulta(\'+"\'"+el+"\'"+\');">\' +el+ \'</a></div>\';
			return meco;
		};';


		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('CASI', 'JQ');
		$param['otros']        = $this->datasis->otros('CASI', 'JQ');

		$param['centerpanel']  = $centerpanel;
		$param['funciones']    = $funciones;

		$param['temas']        = array('proteo','darkness','anexos1');

		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		$param['tamano']       = $this->datasis->getintramenu( substr($this->url,0,-1) );

		$this->load->view('jqgrid/crud2',$param);
	}


	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';
		$ngrid      = "#newapi".$grid0;

		$bodyscript .= '
		function tconsulta(transac){
			if (transac)	{
				window.open(\''.site_url('contabilidad/casi/localizador/transac/procesar').'/\'+transac, \'_blank\', \'width=800, height=600, scrollbars=yes, status=yes, resizable=yes,screenx=((screen.availHeight/2)-300), screeny=((screen.availWidth/2)-400)\');
			} else {
				$.prompt("<h1>Transaccion invalida</h1>");
			}
		};';


		$bodyscript .= '
		function casiadd() {
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function casiedit() {
			var id     = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("'.$ngrid.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		};';

		$bodyscript .= '
		function casishow() {
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
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
		function casidel() {
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("'.$ngrid.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						$("#fborra").html(data);
						$("#fborra").dialog( "open" );
					});
					jQuery("'.$ngrid.'").trigger("reloadGrid");
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		jQuery("#bimp").click( function(){
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("'.$ngrid.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url('formatos/ver/CASI').'/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});';

		$bodyscript .= '
		jQuery("#boton2").click( function(){
				window.open(\''.site_url('contabilidad/casi/dataedit/create/').'\', \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
		});';

		$bodyscript .= '
		jQuery("#boton3").click( function(){
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("'.$ngrid.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url('contabilidad/casi/dataedit/modify').'/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});';

		$bodyscript .= '
		jQuery("#boton4").click( function() {
			window.open(\''.site_url('contabilidad/casi/auditoria/').'\', \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
		});';


		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);
		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, $height = "450", $width = "750" );
		$bodyscript .= $this->jqdatagrid->bsfshow( $height = "500", $width = "700" );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, "300", "300" );

		$bodyscript .= '});';
		$bodyscript .= '</script>';
		return $bodyscript;
	}



	//******************************************************************
	//Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('comprob');
		$grid->label('Comprobate');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
			'formatter'     => 'ltransac'
		));


		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 60 }',
		));


		$grid->addField('total');
		$grid->label('Saldo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('debe');
		$grid->label('Debe');
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


		$grid->addField('haber');
		$grid->label('Haber');
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
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));

		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 10 }',
		));

		$grid->addField('origen');
		$grid->label('Or&iacute;gen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 20 }',
		));

/*
		$grid->addField('transac');
		$grid->label('Transac');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));
*/

		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 4 }',
		));


		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
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
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));

/*
		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));
*/

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 60,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('165');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
				if(id){
					var ret = $("#titulos").getRowData(id);
					jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
				}
			}
		');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		$grid->setBarOptions('addfunc: casiadd, editfunc: casiedit, delfunc: casidel, viewfunc: casishow');

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('CASI','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('CASI','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('CASI','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('CASI','BUSQUEDA%'));
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

	/**
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('casi');

		$response   = $grid->getData('casi', array(array()), array(), false, $mWHERE, 'id', 'desc' );
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
				$this->db->insert('casi', $data);
				echo 'Registro Agregado';

				logusu('CASI',"Registro ????? INCLUIDO");
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			//unset($data['ubica']);
			$this->db->where('id', $id);
			$this->db->update('casi', $data);
			logusu('CASI',"Registro ????? MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM casi WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM casi WHERE id=${id}");
				logusu('CASI',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}


	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

/*
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


		$grid->addField('comprob');
		$grid->label('Comprob');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));
*/


		$grid->addField('cuenta');
		$grid->label('Cuenta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 15 }',
		));


		$grid->addField('referen');
		$grid->label('Referen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));


		$grid->addField('concepto');
		$grid->label('Concepto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 60 }',
		));


		$grid->addField('debe');
		$grid->label('Debe');
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


		$grid->addField('haber');
		$grid->label('Haber');
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

		$grid->addField('origen');
		$grid->label('Origen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 20 }',
		));



		$grid->addField('ccosto');
		$grid->label('C.Costo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));


		$grid->addField('sucursal');
		$grid->label('Sucu.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));

		$grid->addField('comprob');
		$grid->label('Comprob');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
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

/*
		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('idcasi');
		$grid->label('Idcasi');
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
*/

		$grid->showpager(false);
		$grid->setWidth('');
		$grid->setHeight('170');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('-');
		$grid->setFormOptionsA('-');
		$grid->setAfterSubmit('-');

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(1500);
		$grid->setShrinkToFit('false');

		#Set url
		//$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdatait/'));

		$grid->setOndblClickRow("");

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdatait(){
		$id = $this->uri->segment(4);
		if($id){
			$dbid = intval($id);
			$comprob  = $this->datasis->dameval("SELECT comprob FROM casi WHERE id=${dbid}");
			$dbcomprob= $this->db->escape($comprob);

			$orderby= '';
			$sidx=$this->input->post('sidx');
			if($sidx){
				$campos = $this->db->list_fields('itcasi');
				if(in_array($sidx,$campos)){
					$sidx = trim($sidx);
					$sord   = $this->input->post('sord');
					$orderby="ORDER BY `${sidx}` ".(($sord=='asc')? 'ASC':'DESC');
				}
			}

			$grid    = $this->jqdatagrid;
			$mSQL = "SELECT origen, cuenta, referen, concepto, debe, haber, ccosto, sucursal, id, comprob FROM itcasi WHERE comprob=${dbcomprob} ${orderby}";
			$response   = $grid->getDataSimple($mSQL);
			$rs = $grid->jsonresult( $response);
		}else{
			$rs ='';
		}
		echo $rs;
	}

	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');
		$this->qformato=$qformato=$this->datasis->formato_cpla();
 		$modbus=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array(
				'codigo' =>'cuenta_<#i#>',
				'departa'=>'ccosto<#i#>',
				'descrip'=>'concepto_<#i#>',
				'departa'=>'cpladeparta_<#i#>',
				'ccosto' =>'cplaccosto_<#i#>'
			),
			'titulo'  =>'Buscar Cuenta',
			'p_uri'=>array(4=>'<#i#>'),
			'where'=>"codigo LIKE \"$qformato\"",
			'script'=>array('post_modbus(<#i#>)')
			);
		$btn=$this->datasis->p_modbus($modbus,'<#i#>');

		$uri='/contabilidad/casi/dpto/';

		$do = new DataObject('casi');
		$do->rel_one_to_many('itcasi', 'itcasi', array('id'=>'idcasi'));
		$do->rel_pointer('itcasi','cpla','itcasi.cuenta=cpla.codigo','cpla.ccosto AS cplaccosto,cpla.departa AS cpladeparta');

		$edit = new DataDetails('Asientos', $do);
		$edit->back_url = site_url('contabilidad/casi/dataedit/create');
		$edit->on_save_redirect=false;
		$edit->set_rel_title('itcasi','cuenta contables');

		$edit->pre_process( 'insert','_pre_insert');
		$edit->pre_process( 'update','_pre_update');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('delete','_post_delete');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'required|chfecha';
		$edit->fecha->size = 12;
		$edit->fecha->calendar=false;

		$edit->comprob = new inputField('N&uacute;mero', 'comprob');
		$edit->comprob->size     = 12;
		$edit->comprob->maxlength= 8;
		$edit->comprob->rule     ='required|unique';
		//$edit->comprob->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario

		$edit->descrip = new inputField('Descripci&oacute;n', 'descrip');
		$edit->descrip->size      = 60;
		$edit->descrip->maxlength = 60;

		$edit->status = new  dropdownField ('Estatus', 'status');
		$edit->status->option('A','Actualizado');
		$edit->status->option('D','Diferido');
		$edit->status->style='width:110px;';
		$edit->status->size = 5;

		//**************************
		//  Campos para el detalle
		//**************************
		$edit->cuenta = new inputField('Cuenta <#o#>', 'cuenta_<#i#>');
		$edit->cuenta->size     = 15;
		$edit->cuenta->db_name  = 'cuenta';
		$edit->cuenta->rel_id   = 'itcasi';
		$edit->cuenta->rule     = 'required';
		$edit->cuenta->append($btn);

		$edit->referen = new inputField('Referencia <#o#>', 'referen_<#i#>');
		$edit->referen->size      = 20;
		$edit->referen->db_name   = 'referen';
		$edit->referen->maxlength = 12;
		$edit->referen->rel_id    = 'itcasi';

		$edit->concepto = new inputField('Concepto <#o#>', 'concepto_<#i#>');
		$edit->concepto->size      = 25;
		$edit->concepto->db_name   = 'concepto';
		$edit->concepto->maxlength = 50;
		$edit->concepto->rel_id    = 'itcasi';

		$edit->itdebe = new inputField('Debe <#o#>', 'itdebe_<#i#>');
		$edit->itdebe->db_name      = 'debe';
		$edit->itdebe->css_class    = 'inputnum';
		$edit->itdebe->rel_id       = 'itcasi';
		$edit->itdebe->maxlength    = 19;
		$edit->itdebe->size         = 18;
		$edit->itdebe->rule         = 'required|positive';
		$edit->itdebe->autocomplete = false;
		$edit->itdebe->showformat   = 'decimal';
		$edit->itdebe->onkeyup      = 'validaDebe(<#i#>)';

		$edit->ithaber = new inputField('Haber <#o#>', 'ithaber_<#i#>');
		$edit->ithaber->db_name      = 'haber';
		$edit->ithaber->css_class    = 'inputnum';
		$edit->ithaber->rel_id       = 'itcasi';
		$edit->ithaber->maxlength    = 19;
		$edit->ithaber->size         = 18;
		$edit->ithaber->rule         = 'required|positive';
		$edit->ithaber->showformat   = 'decimal';
		$edit->ithaber->autocomplete = false;
		$edit->ithaber->onkeyup      = 'validaHaber(<#i#>)';

		$edit->itccosto = new dropdownField('Centro de costo', 'itccosto_<#i#>');
		$edit->itccosto->option('','Ninguno');
		$edit->itccosto->options("SELECT depto,descrip FROM dpto WHERE tipo='G' ORDER BY descrip");
		$edit->itccosto->db_name   = 'ccosto';
		$edit->itccosto->rel_id    = 'itcasi';
		$edit->itccosto->rule      = 'condi_required|callback_chdepaccosto[<#i#>]';
		$edit->itccosto->style     = 'width:150px;';

		$edit->itsucursal =  new dropdownField('Sucursal', 'itsucursal_<#i#>');
		$edit->itsucursal->option('','Ninguno');
		$edit->itsucursal->options("SELECT codigo,CONCAT(codigo,'-', sucursal) AS sucursal FROM sucu ORDER BY codigo");
		$edit->itsucursal->db_name   = 'sucursal';
		$edit->itsucursal->rel_id    = 'itcasi';
		$edit->itsucursal->rule      = 'condi_required|callback_chdepaccosto[<#i#>]';
		$edit->itsucursal->style     = 'width:150px';

		$edit->cplaccosto = new hiddenField('', 'cplaccosto_<#i#>');
		$edit->cplaccosto->db_name   = 'cplaccosto';
		$edit->cplaccosto->rel_id    = 'itcasi';
		$edit->cplaccosto->pointer   = true;

		$edit->cpladeparta = new hiddenField('', 'cpladeparta_<#i#>');
		$edit->cpladeparta->db_name   = 'cpladeparta';
		$edit->cpladeparta->rel_id    = 'itcasi';
		$edit->cpladeparta->pointer   = true;
		//**************************
		//fin de campos para detalle
		//**************************

		$edit->debe = new inputField('Debe', 'debe');
		$edit->debe->css_class ='inputnum';
		$edit->debe->readonly  =true;
		$edit->debe->size      = 10;
		$edit->debe->showformat   = 'decimal';
		$edit->debe->type ='inputhidden';

		$edit->haber = new inputField('Haber', 'haber');
		$edit->haber->css_class ='inputnum';
		$edit->haber->readonly  =true;
		$edit->haber->size      = 10;
		$edit->haber->showformat= 'decimal';
		$edit->haber->type ='inputhidden';

		$edit->total = new inputField('Saldo', 'total');
		$edit->total->css_class ='inputnum';
		$edit->total->readonly  =true;
		$edit->total->size      = 10;
		$edit->total->showformat= 'decimal';
		$edit->total->type ='inputhidden';

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->origen = new autoUpdateField('origen'  ,'MANUAL','MANUAL');

		$edit->buttons('add_rel');
		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form'] =&  $edit;
			$this->load->view('view_casi', $conten);
		}

	}

	function grid1(){
		$page  = 1;//$this->input->post('page');
		$limit = 50;//$this->input->post('rows'); // get how many rows we want to have into the grid - rowNum parameter in the grid
		$sidx  = 1; //$this->input->post('sidx'); // get index row - i.e. user click to sort. At first time sortname parameter -after that the index from colModel
		$sord  = 'DESC';//$this->input->post('sord');
		$tabla = 'casi';

		$this->db->from($tabla);

		if(!$sidx) $sidx =1;// if we not pass at first time index use the first column for the index or what you want

		$mSQL=$this->db->_compile_select($this->db->_count_string . $this->db->_protect_identifiers('numrows'));

		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			$row = $query->row();
			$count= $row->numrows;
		}else{
			$count=0;
		}

		if( $count > 0 && $limit > 0) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}

		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit;
		if($start <0) $start = 0;
		$sidx = 'comprob';

		$this->load->helper('xml');
		header("Content-type: text/xml;charset=".$this->config->item('charset'));
		$s = "<?xml version='1.0' encoding='".$this->config->item('charset')."'?>";
		$s .= '<rows>';
		$s .= '<page>'.$page.'</page>';
		$s .= '<total>'.$total_pages.'</total>';
		$s .= '<records>'.$count.'</records>';

		$this->db->orderby($sidx,$sord);
		$this->db->limit($limit,$start);
		$query = $this->db->get();

		$campos = $this->db->field_data('casi');

		foreach ($query->result() as $row){
			$s .= "<row id='". $row->id."'>";
			$s .= '<cell>'.xml_convert($row->comprob).'</cell>';
			$s .= '<cell>'.xml_convert($row->fecha).'</cell>';
			$s .= '<cell>'.xml_convert($row->descrip).'</cell>';
			$s .= '<cell>'.xml_convert($row->debe).'</cell>';
			$s .= '<cell>'.xml_convert($row->haber).'</cell>';
			$s .= '<cell>'.xml_convert($row->total).'</cell>';
			$s .= '</row>';
		}
		$s .= '</rows>';
		echo $s;
	}



	function chrepetidos($cod){
		if(array_search($cod, $this->chrepetidos)===false){
			$this->chrepetidos[]=$cod;
			return true;
		}else{
			$this->validation->set_message('chrepetidos', 'La cuenta '.$cod.' esta repetido');
			return false;
		}
	}

	function chdepaccosto($val,$ind){;
		$codigo   = $this->input->post('cuenta_'.$ind);
		$dbcodigo = $this->db->escape($codigo);
		$departa  = $this->datasis->dameval('SELECT departa FROM cpla WHERE codigo='.$dbcodigo);
		if($departa=='S' && empty($val)){
			$this->validation->set_message('chdepaccosto', 'El campo %s es requerido para la cuenta contable '.$codigo);
			return false;
		}
		return true;
	}

	function auditoria(){

		$arr[] = anchor('contabilidad/casi/auditcasi'  ,'Auditoria en Asientos'         ,'title="Registros con cuentas contables inv&aacute;lidas"');
		$arr[].= anchor('contabilidad/casi/auditsprv'  ,'Auditoria en Proveedores'      ,'title="Registros con cuentas contables inv&aacute;lidas"');
		$arr[].= anchor('contabilidad/casi/auditscli'  ,'Auditoria en Clientes'         ,'title="Registros con cuentas contables inv&aacute;lidas"');
		$arr[].= anchor('contabilidad/casi/auditbotr'  ,'Auditoria en Conceptos'        ,'title="Registros con cuentas contables inv&aacute;lidas"');
		$arr[].= anchor('contabilidad/casi/auditmgas'  ,'Auditoria en Maestro de gatos' ,'title="Registros con cuentas contables inv&aacute;lidas"');
		$arr[].= anchor('contabilidad/casi/auditban'   ,'Auditoria en Bancos'           ,'title="Registros con cuentas contables inv&aacute;lidas"');
		$arr[].= anchor('contabilidad/casi/auditreglas','Auditoria en Reglas contables' ,'title="Registros con cuentas contables inv&aacute;lidas"');
		$arr[].= anchor('contabilidad/casi/auditline'  ,'Auditoria en L&iacute;neas de inventario' ,'title="Registros con cuentas contables inv&aacute;lidas"');
		$arr[].= anchor('contabilidad/casi/auditconc'  ,'Auditoria en Conceptos de nomina' ,'title="Registros con cuentas contables inv&aacute;lidas"');
		$arr[].= anchor('contabilidad/casi/auditrete'  ,'Auditoria en Retenciones'      ,'title="Registros con cuentas contables inv&aacute;lidas"');
		$arr[].= anchor('contabilidad/casi/localizador/transac' ,'Localizador de Transacciones' ,'title="Busca una transacci&oacute;n en la base de datos"');
		$arr[].= anchor('contabilidad/casi/localizador/cuenta'  ,'Localizador de Cuentas'       ,'title="Busca una cuenta en la base de datos"');


		$data['content'] = '<p>M&oacute;dulo para ayudar a encontrar y solucionar problemas de inconsistencias en los registros que producen asientos descuadrados.</p>';
		$data['content'].= ul($arr);
		$data['head']    = '';
		$data['title']   = heading('Auditoria de Contabilidad');
		$this->load->view('view_ventanas', $data);
	}

	function auditcasi(){
		$this->rapyd->load('datagrid','datafilter');

		$filter = new DataFilter('Auditoria de Asientos');
		$filter->db->select(array('a.comprob','a.fecha','a.concepto','a.origen','a.debe','a.haber','a.cuenta'));
		$filter->db->from('itcasi AS a');
		//$filter->db->join('casi AS c' ,'a.comprob=c.comprob');
		$filter->db->join('cpla AS b' ,'a.cuenta=b.codigo','LEFT');
		$filter->db->where('b.codigo IS NULL');
		//$filter->db->where('cuenta NOT REGEXP \'^([0-9]+\.)+[0-9]+\' OR cuenta IS NULL');

		$filter->fechad = new dateonlyField('Desde', 'fechad','d/m/Y');
		$filter->fechah = new dateonlyField('Hasta', 'fechah','d/m/Y');
		$filter->fechad->clause  =$filter->fechah->clause ='where';
		$filter->fechad->db_name =$filter->fechah->db_name='fecha';
		$filter->fechah->size=$filter->fechad->size=12;
		$filter->fechad->operator='>=';
		$filter->fechah->operator='<=';
		$filter->fechad->group=$filter->fechah->group='Fecha';

		$filter->comprob = new inputField('N&uacute;mero'     , 'comprob');
		$filter->comprob->size=15;

		$filter->origen = new dropdownField('Or&iacute;gen', 'origen');
		$filter->origen->option('','Todos');
		$filter->origen->style = 'width:180px';
		$filter->origen->options('SELECT modulo, modulo valor FROM reglascont GROUP BY modulo');

		$filter->buttons('reset','search');
		$filter->build();

		function regla($origen){
			if(preg_match('/(?P<regla>[A-Za-z]+)(?P<numero>\d+)/', $origen, $match)>0){
				$regla  =$match['regla'];
				$numero =$match['numero'];

				$atts = array(
					'width'      => '800',
					'height'     => '600',
					'scrollbars' => 'yes',
					'status'     => 'yes',
					'resizable'  => 'yes',
					'screenx'    => '0',
					'screeny'    => '0'
				);

				$rt = anchor_popup('contabilidad/reglas/dataedit/'.$regla.'/show/'.$regla.'/'.$numero,$origen,$atts);
			}else{
				$rt=$origen;
			}
			return $rt;
		}

		$grid = new DataGrid('Registros cuya cuenta no existe en el plan de cuentas');
		$grid->use_function('regla');
		$grid->order_by('fecha','asc');
		$grid->per_page = 40;
		$grid->column_orderby('N&uacute;mero','comprob','comprob');
		$grid->column_orderby('Cuenta','cuenta','cuenta');
		//$grid->column_orderby('Transac','transac','transac');
		$grid->column_orderby('Fecha'   ,'<dbdate_to_human><#fecha#></dbdate_to_human>','fecha',"align='center'");
		$grid->column_orderby('Concepto','concepto','concepto');
		$grid->column_orderby('Or&iacute;gen','<regla><#origen#></regla>','origen',"align='center'");
		$grid->column_orderby('Debe'    ,'<nformat><#debe#></nformat>'   ,'debe'  ,"align='right'" );
		$grid->column_orderby('Haber'   ,'<nformat><#haber#></nformat>'  ,'haber' ,"align='right'" );
		$action = "javascript:window.location='".site_url('contabilidad/casi/auditoria')."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');
		$grid->build();

		$data['content'] =$filter->output.$grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   =heading('Auditoria de Asientos');
		$this->load->view('view_ventanas', $data);
	}

	function auditmgas(){
		$this->rapyd->load('datagrid','dataform');

		$qformato=$this->datasis->formato_cpla();
		$mCPLA=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
			        'codigo' =>'C&oacute;digo',
			        'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'cuenta'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
		);
		$bcpla =$this->datasis->modbus($mCPLA);

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$uri = anchor_popup('finanzas/mgas/dataedit/modify/<#id#>','<#codigo#>',$atts);
		$grid = new DataGrid('Registros cuya cuenta no existe en el plan de cuentas');
		$grid->order_by('a.codigo');
		$grid->db->select(array('a.codigo','a.tipo','a.descrip','a.cuenta','a.grupo','a.id'));
		$grid->db->from('mgas AS a');
		$grid->db->join('cpla AS b','a.cuenta=b.codigo','LEFT');
		$grid->db->where('b.codigo IS NULL');
		$grid->per_page = 40;
		$grid->column_orderby('C&oacute;digo',$uri,'codigo');
		$grid->column_orderby('Descripci&oacute;n','descrip','descrip');
		$grid->column_orderby('Tipo','tipo','tipo');
		$grid->column_orderby('Grupo','grupo' ,'grupo');
		$grid->column_orderby('Cuenta','cuenta','cuenta');
		$action = "javascript:window.location='".site_url('contabilidad/casi/auditoria')."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');
		$grid->build();

		$form = new DataForm('contabilidad/casi/auditmgas/process');
		$form->cuenta = new inputField('Cuenta contable', 'cuenta');
		$form->cuenta->rule = 'trim|required|existecpla';
		$form->cuenta->size =15;
		$form->cuenta->append($bcpla.'Coloque la cuenta contable para ser asginada a todos los registros encontrados y presione cambiar.');
		$form->grupo = new dropdownField('Grupo', 'grupo');
		$form->grupo->option('','Todos');
		$form->grupo->options("SELECT grupo,grupo AS val FROM mgas GROUP BY grupo");
		$form->grupo->style = 'width:140px';
		$form->grupo->append('Adicionalmente puede elegir un grupo para condicionar la asignaci&oacute;n');

		$form->submit('btnsubmit','Cambiar');
		$form->build_form();

		if ($form->on_success()){
			$cuenta = $this->db->escape(trim($form->cuenta->newValue));
			$grupo  = $form->grupo->newValue;
			if(!empty($grupo)) $ww=' AND a.grupo='.$this->db->escape($grupo); else $ww='';
			$mSQL='UPDATE mgas AS a LEFT JOIN cpla AS b ON a.cuenta=b.codigo SET a.cuenta='.$cuenta.' WHERE b.codigo IS NULL'.$ww;
			$this->db->simple_query($mSQL);
			redirect('contabilidad/casi/auditmgas');
		}

		$data['content'] = ($grid->recordCount > 0) ? $form->output : '';
		$data['content'].= $grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Auditoria de cuentas en maestro de gatos');
		$this->load->view('view_ventanas', $data);
	}

	function auditban(){
		$this->rapyd->load('datagrid','dataform');

		$qformato=$this->datasis->formato_cpla();
		$mCPLA=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
			        'codigo' =>'C&oacute;digo',
			        'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'cuenta'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
		);
		$bcpla =$this->datasis->modbus($mCPLA);

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$uri = anchor_popup('finanzas/mgas/dataedit/modify/<#id#>','<#codbanc#>',$atts);
		$grid = new DataGrid('Registros cuya cuenta no existe en el plan de cuentas');
		$grid->order_by('a.codbanc');
		$grid->db->select(array('a.codbanc','a.tbanco','a.banco','a.cuenta','a.id'));
		$grid->db->from('banc AS a');
		$grid->db->join('cpla AS b','a.cuenta=b.codigo','LEFT');
		$grid->db->where('b.codigo IS NULL');
		$grid->per_page = 40;
		$grid->column_orderby('C&oacute;digo',$uri,'codbanc');
		$grid->column_orderby('Banco','banco','banco');
		$grid->column_orderby('Tipo' ,'tbanco','tbanco');
		$grid->column_orderby('Cuenta','cuenta','cuenta');
		$action = "javascript:window.location='".site_url('contabilidad/casi/auditoria')."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');
		$grid->build();

		$form = new DataForm('contabilidad/casi/auditban/process');
		$form->cuenta = new inputField('Cuenta contable', 'cuenta');
		$form->cuenta->rule = 'trim|required|existecpla';
		$form->cuenta->size =15;
		$form->cuenta->append($bcpla.'Coloque la cuenta contable para ser asginada a todos los registros encontrados y presione cambiar.');

		$form->submit('btnsubmit','Cambiar');
		$form->build_form();

		if ($form->on_success()){
			$cuenta= $this->db->escape(trim($form->cuenta->newValue));
			$mSQL='UPDATE banc AS a LEFT JOIN cpla AS b ON a.cuenta=b.codigo SET a.cuenta='.$cuenta.' WHERE b.codigo IS NULL';
			$this->db->simple_query($mSQL);
			redirect('contabilidad/casi/auditban');
		}

		$data['content'] = ($grid->recordCount > 0) ? $form->output : '';
		$data['content'].= $grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Auditoria de cuentas en bancos');
		$this->load->view('view_ventanas', $data);
	}

	function auditreglas(){
		$this->rapyd->load('datagrid','dataform');
		$qformato=$this->datasis->formato_cpla();

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		function extraecuenta($cuenta){
			if(preg_match_all('/(?P<cuenta>([0-9]+\.?)+)/', $cuenta, $matches)>0){
				$CI =& get_instance();
				$err=false;
				foreach($matches['cuenta'] as $cc){
					$cc=$CI->db->escape($cc);
					$cana=$CI->datasis->dameval('SELECT COUNT(*) FROM cpla WHERE codigo='.$cc);
					if(!($cana>0)){
						$err=true;
					}
				}
			}
			if($err){
				return "<b style='color:red'>$cuenta</b>";
			}else{
				return $cuenta;
			}
		}

		$uri = anchor_popup('contabilidad/reglas/dataedit/<#modulo#>/modify/<#modulo#>/<#regla#>','<#modulo#>:<#regla#>',$atts);
		$grid = new DataGrid('Registros de reglas que imponen cuentas contables, las que no existen estan remarcadas en <b style=\'color:red\'>rojo</b>');
		$grid->use_function('extraecuenta');
		$grid->order_by('a.modulo');
		$grid->db->select(array('a.modulo','a.tabla','a.descripcion','a.cuenta','a.regla'));
		$grid->db->from('reglascont AS a');
		$grid->db->where('a.cuenta REGEXP','([0-9]+\.[0-9]+\.?)+');
		$grid->per_page = 40;
		$grid->column_orderby('C&oacute;digo',$uri,'modulo');
		$grid->column_orderby('Tabla','tabla','tabla');
		$grid->column_orderby('Descripci&oacute;n' ,'descripcion','descripcion');
		$grid->column_orderby('Cuenta','<extraecuenta><#cuenta#></extraecuenta>','cuenta');
		$action = "javascript:window.location='".site_url('contabilidad/casi/auditoria')."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Auditoria de cuentas en reglas');
		$this->load->view('view_ventanas', $data);
	}

	function auditscli(){
		$this->rapyd->load('datagrid','dataform');

		$mSQL="SELECT COUNT(*) AS cana FROM scli WHERE cliente='RETED'";
		$cana=intval($this->datasis->dameval($mSQL));
		if($cana<=0){
			$data = array(
				'cliente'    => 'RETED',
				'nombre'     => 'RETENCION I.S.L.R TDC/BANCOS',
				'grupo'      => '17',
				'gr_desc'    => 'TRIBUTOS',
				'formap'     => 15,
				'cuenta'     => '',
				'tipo'       => '1',
				'limite'     => 0,
				'socio'      => 'RETEN',
				'estado'     => 0,
				'vendedor'   => '',
				'porvend'    => 0.0,
				'porcobr'    => 0.0,
				'copias'     => 0,
				'porcomi'    => 0.00,
				'rifci'      => 'J000000',
				'fecha2'     => NULL,
				'tiva'       => 'C',
				'riffis'     => 'J000000',
				'credito'    => 'N',
				'tolera'     => 0.00,
				'maxtole'    => 0.00,
				'zona'       => '','ciudad1'    => '',
				'separa'     => '','regimen'    => '',
				'url'        => NULL,'mensaje'    => NULL,
				'pin'        => NULL,'sucursal'   => NULL,
				'fb'         => NULL,'mmargen'    => NULL,
				'twitter'    => NULL,'repre'      => NULL,
				'upago'      => NULL,'cirepre'    => NULL,
				'tarifa'     => NULL,'ciudad'     => NULL,
				'tarimonto'  => NULL,'clave'      => NULL,
				'aniversario'=> NULL,'nomfis'     => NULL,
				'dire21'     => NULL,'contacto'   => NULL,
				'dire22'     => NULL,'dire11'     => NULL,
				'ciudad2'    => NULL,'dire12'     => NULL,
				'telefono'   => NULL,'pais'       => NULL,
				'telefon2'   => NULL,'email'      => NULL,
				'observa'    => NULL,'nit'        => NULL,
				'fecha1'     => NULL,'canticipo'  => NULL,
				'comisio'    => NULL,'cobrador'   => NULL,
			);
			$sql = $this->db->insert_string('scli', $data);
			$this->db->simple_query($sql);
		}


		$qformato=$this->datasis->formato_cpla();
		$mCPLA=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
			        'codigo' =>'C&oacute;digo',
			        'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'cuenta'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
		);
		$bcpla =$this->datasis->modbus($mCPLA);

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$uri = anchor_popup('ventas/scli/dataedit/modify/<#id#>','<#cliente#>',$atts);
		$grid = new DataGrid('Registros cuya cuenta no existe en el plan de cuentas');
		$grid->db->select(array('a.cliente','a.rifci','a.nombre','a.cuenta','a.grupo','a.id'));
		$grid->db->from('scli AS a');
		$grid->db->join('cpla AS b','a.cuenta=b.codigo','LEFT');
		$grid->db->where('b.codigo IS NULL');
		$grid->db->orwhere('a.cuenta NOT LIKE',$qformato);
		$grid->per_page = 40;
		$grid->column_orderby('C&oacute;digo',$uri,'cliente');
		$grid->column_orderby('Grupo','grupo','grupo');
		$grid->column_orderby('Nombre','nombre','nombre');
		$grid->column_orderby('Rif/CI','rifci' ,'rifci');
		$grid->column_orderby('Cuenta','cuenta','cuenta');
		$action = "javascript:window.location='".site_url('contabilidad/casi/auditoria')."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');
		$grid->build();

		$form = new DataForm('contabilidad/casi/auditscli/process');
		$form->cuenta = new inputField('Cuenta contable', 'cuenta');
		$form->cuenta->rule = 'trim|required|existecpla';
		$form->cuenta->size =15;
		$form->cuenta->append($bcpla.'Coloque la cuenta contable para ser asginada a todos los registros encontrados y presione cambiar.');
		$form->grupo = new dropdownField('Grupo', 'grupo');
		$form->grupo->option('','Todos');
		$form->grupo->options("SELECT grupo,grupo AS val FROM scli GROUP BY grupo");
		$form->grupo->style = 'width:140px';
		$form->grupo->append('Adicionalmente puede elegir un grupo para condicionar la asignaci&oacute;n');

		$form->submit('btnsubmit','Cambiar');
		$form->build_form();

		if ($form->on_success()){
			$cuenta= $this->db->escape(trim($form->cuenta->newValue));
			$grupo  = $form->grupo->newValue;
			if(!empty($grupo)) $ww=' AND a.grupo='.$this->db->escape($grupo); else $ww='';
			$mSQL='UPDATE scli AS a LEFT JOIN cpla AS b ON a.cuenta=b.codigo SET a.cuenta='.$cuenta.' WHERE b.codigo IS NULL'.$ww;
			$this->db->simple_query($mSQL);
			redirect('contabilidad/casi/auditscli');
		}

		$data['content'] = ($grid->recordCount > 0) ? $form->output : '';
		$data['content'].= $grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Auditoria de cuentas en clientes');
		$this->load->view('view_ventanas', $data);
	}

	function auditbotr(){
		$this->rapyd->load('datagrid','dataform');

		$qformato=$this->datasis->formato_cpla();
		$mCPLA=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
			        'codigo' =>'C&oacute;digo',
			        'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'cuenta'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
		);
		$bcpla =$this->datasis->modbus($mCPLA);

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$uri = anchor_popup('finanzas/botr/dataedit/modify/<#codigo#>','<#codigo#>',$atts);
		$grid = new DataGrid('Registros cuya cuenta no existe en el plan de cuentas');
		$grid->db->select(array('a.codigo','a.nombre','a.cuenta'));
		$grid->db->from('botr AS a');
		$grid->db->join('cpla AS b','a.cuenta=b.codigo','LEFT');
		$grid->db->where('b.codigo IS NULL');
		$grid->per_page = 40;
		$grid->column_orderby('C&oacute;digo', $uri ,'codigo');
		$grid->column_orderby('Nombre','nombre','nombre');
		$grid->column_orderby('Cuenta','cuenta','cuenta');
		$action = "javascript:window.location='".site_url('contabilidad/casi/auditoria')."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');
		$grid->build();

		$form = new DataForm('contabilidad/casi/auditbotr/process');
		$form->cuenta = new inputField('Cuenta contable', 'cuenta');
		$form->cuenta->rule = 'trim|required|existecpla';
		$form->cuenta->size =15;
		$form->cuenta->append($bcpla.'Coloque la cuenta contable para ser asginada a todos los registros encontrados y presione cambiar.');

		$form->submit('btnsubmit','Cambiar');
		$form->build_form();

		if ($form->on_success()){
			$cuenta= $this->db->escape(trim($form->cuenta->newValue));
			$mSQL='UPDATE botr AS a LEFT JOIN cpla AS b ON a.cuenta=b.codigo SET a.cuenta='.$cuenta.' WHERE b.codigo IS NULL';
			$this->db->simple_query($mSQL);
			redirect('contabilidad/casi/auditbotr');
		}

		$data['content'] = ($grid->recordCount > 0) ? $form->output : '';
		$data['content'].= $grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Auditoria en otros conceptos contables');
		$this->load->view('view_ventanas', $data);
	}

	function auditrete(){
		$this->rapyd->load('datagrid','dataform');

		$qformato=$this->datasis->formato_cpla();
		$mCPLA=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
			        'codigo' =>'C&oacute;digo',
			        'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'cuenta'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
		);
		$bcpla =$this->datasis->modbus($mCPLA);

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$uri = anchor_popup('finanzas/rete/dataedit/modify/<#codigo#>','<#codigo#>',$atts);
		$grid = new DataGrid('Registros cuya cuenta no existe en el plan de cuentas');
		$grid->db->select(array('a.codigo','a.activida','a.cuenta'));
		$grid->db->from('rete AS a');
		$grid->db->join('cpla AS b','a.cuenta=b.codigo','LEFT');
		$grid->db->where('b.codigo IS NULL');
		$grid->per_page = 40;
		$grid->column_orderby('C&oacute;digo', $uri ,'codigo');
		$grid->column_orderby('Actividad','activida','activida');
		$grid->column_orderby('Cuenta','cuenta','cuenta');
		$action = "javascript:window.location='".site_url('contabilidad/casi/auditoria')."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');
		$grid->build();

		$form = new DataForm('contabilidad/casi/auditrete/process');
		$form->cuenta = new inputField('Cuenta contable', 'cuenta');
		$form->cuenta->rule = 'trim|required|existecpla';
		$form->cuenta->size =15;
		$form->cuenta->append($bcpla.'Coloque la cuenta contable para ser asginada a todos los registros encontrados y presione cambiar.');

		$form->submit('btnsubmit','Cambiar');
		$form->build_form();

		if ($form->on_success()){
			$cuenta= $this->db->escape(trim($form->cuenta->newValue));
			$mSQL='UPDATE rete AS a LEFT JOIN cpla AS b ON a.cuenta=b.codigo SET a.cuenta='.$cuenta.' WHERE b.codigo IS NULL';
			$this->db->simple_query($mSQL);
			redirect('contabilidad/casi/auditrete');
		}

		$data['content'] = ($grid->recordCount > 0) ? $form->output : '';
		$data['content'].= $grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Auditoria en Retenciones');
		$this->load->view('view_ventanas', $data);
	}

	function auditconc(){
		$this->rapyd->load('datagrid','dataform');

		$qformato=$this->datasis->formato_cpla();
		$mCPLA=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
			        'codigo' =>'C&oacute;digo',
			        'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'cuenta'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
		);
		$bcpla =$this->datasis->modbus($mCPLA);

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$uri = anchor_popup('nomina/conc/dataedit/modify/<#concepto#>','<#concepto#>',$atts);
		$grid = new DataGrid('Registros cuya cuenta no existe en el plan de cuentas');
		$grid->db->select(array('a.concepto','a.descrip','a.cuenta','a.contra'));
		$grid->db->from('conc AS a');
		$grid->db->join('cpla AS b','a.cuenta=b.codigo','LEFT');
		$grid->db->join('cpla AS c','a.cuenta=b.codigo','LEFT');
		$grid->db->where('b.codigo IS NULL');
		$grid->db->orwhere('c.codigo IS NULL');
		$grid->per_page = 40;
		$grid->column_orderby('Concepto', $uri ,'concepto');
		$grid->column_orderby('Descripcion','descrip','descrip');
		$grid->column_orderby('Cuenta','cuenta','cuenta');
		$grid->column_orderby('Contra','contra','contra');
		$action = "javascript:window.location='".site_url('contabilidad/casi/auditoria')."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');
		$grid->build();

		$form = new DataForm('contabilidad/casi/auditconc/process');
		$form->cuenta = new inputField('Cuenta contable', 'cuenta');
		$form->cuenta->rule = 'trim|required|existecpla';
		$form->cuenta->size =15;
		$form->cuenta->append($bcpla.'Coloque la cuenta contable para ser asginada a todos los registros encontrados y presione cambiar.');
		$form->campo = new dropdownField('Campo', 'campo');
		$form->campo->option('','Seleccionar');
		$form->campo->option('cuenta' ,'Cuenta');
		$form->campo->option('contra' ,'Contra');
		$form->campo->style = 'width:140px';
		$form->campo->rule='required|enum[cuenta,contra]';

		$form->submit('btnsubmit','Cambiar');
		$form->build_form();

		if ($form->on_success()){
			$cuenta= $this->db->escape(trim($form->cuenta->newValue));
			$campo = $form->campo->newValue;
			$mSQL="UPDATE conc AS a LEFT JOIN cpla AS b ON a.$campo=b.codigo SET a.$campo=$cuenta WHERE b.codigo IS NULL";
			$this->db->simple_query($mSQL);
			redirect('contabilidad/casi/auditconc');
		}

		$data['content'] = ($grid->recordCount > 0) ? $form->output : '';
		$data['content'].= $grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Auditoria en conceptos de nomina');
		$this->load->view('view_ventanas', $data);
	}

	function auditline(){
		$this->rapyd->load('datagrid','dataform');

		$qformato=$this->datasis->formato_cpla();
		$mCPLA=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
			        'codigo' =>'C&oacute;digo',
			        'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'cuenta'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
		);
		$bcpla =$this->datasis->modbus($mCPLA);

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$uri = anchor_popup('inventario/line/dataedit/modify/<#linea#>','<#linea#>',$atts);
		$grid = new DataGrid('Registros cuya cuenta no existe en el plan de cuentas');
		$grid->db->select(array('a.linea','a.descrip','a.cu_cost','a.cu_inve','a.cu_venta','a.cu_devo'));
		$grid->db->from('line AS a');
		$grid->db->join('cpla AS b','a.cu_cost =b.codigo' ,'LEFT');
		$grid->db->join('cpla AS c','a.cu_inve =b.codigo' ,'LEFT');
		$grid->db->join('cpla AS d','a.cu_venta=b.codigo','LEFT');
		$grid->db->join('cpla AS e','a.cu_devo =b.codigo' ,'LEFT');

		$grid->db->where('b.codigo IS NULL');
		$grid->db->orwhere('c.codigo IS NULL');
		$grid->db->orwhere('d.codigo IS NULL');
		$grid->db->orwhere('e.codigo IS NULL');
		$grid->per_page = 40;
		$grid->column_orderby('C&oacute;digo', $uri ,'codigo');
		$grid->column_orderby('Nombre','descrip','descrip');
		$grid->column_orderby('C. Costo','cu_cost'  ,'cu_cost' );
		$grid->column_orderby('C. Inven','cu_inve'  ,'cu_inve' );
		$grid->column_orderby('C. Venta','cu_venta' ,'cu_venta');
		$grid->column_orderby('C. Devol','cu_devo'  ,'cu_devo' );

		$action = "javascript:window.location ='".site_url('contabilidad/casi/auditoria')."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');
		$grid->build();

		$form = new DataForm('contabilidad/casi/auditline/process');
		$form->cuenta = new inputField('Cuenta contable', 'cuenta');
		$form->cuenta->rule = 'trim|required|existecpla';
		$form->cuenta->size =15;
		$form->cuenta->append($bcpla.'Coloque la cuenta contable para ser asginada a todos los registros encontrados y presione cambiar.');
		$form->campo = new dropdownField('Campo', 'campo');
		$form->campo->option('','Seleccionar');
		$form->campo->option('cu_cost' ,'Costo');
		$form->campo->option('cu_inve' ,'Inventario');
		$form->campo->option('cu_venta','Ventas');
		$form->campo->option('cu_devo' ,'Devolucion');
		$form->campo->style = 'width:140px';
		$form->campo->rule='required|enum[cu_cost,cu_inve,cu_venta,cu_devo]';

		$form->submit('btnsubmit','Cambiar');
		$form->build_form();

		if ($form->on_success()){
			$cuenta= $this->db->escape(trim($form->cuenta->newValue));
			$campo = $form->campo->newValue;
			$mSQL="UPDATE line AS a LEFT JOIN cpla AS b ON a.$campo=b.codigo SET a.$campo=$cuenta WHERE b.codigo IS NULL";
			$this->db->simple_query($mSQL);
			redirect('contabilidad/casi/auditline');
		}

		$data['content'] = ($grid->recordCount > 0) ? $form->output : '';
		$data['content'].= $grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Auditoria en contable en lineas');
		$this->load->view('view_ventanas', $data);
	}

	function auditsprv(){
		$this->rapyd->load('datagrid','dataform');

		$qformato=$this->datasis->formato_cpla();
		$mCPLA=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
			        'codigo' =>'C&oacute;digo',
			        'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'cuenta'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
		);
		$bcpla =$this->datasis->modbus($mCPLA);

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$uri = anchor_popup('compras/sprv/dataedit/modify/<#id#>','<#proveed#>',$atts);
		$grid = new DataGrid('Registros cuya cuenta no existe en el plan de cuentas');
		$grid->db->select(array('a.proveed','a.rif','a.nombre','a.cuenta','a.grupo','a.id'));
		$grid->db->from('sprv AS a');
		$grid->db->join('cpla AS b','a.cuenta=b.codigo','LEFT');
		$grid->db->where('b.codigo IS NULL');
		$grid->per_page = 40;
		$grid->column_orderby('C&oacute;digo',$uri,'proveed');
		$grid->column_orderby('Grupo' ,'grupo','grupo');
		$grid->column_orderby('Nombre','nombre','nombre');
		$grid->column_orderby('Rif'   ,'rif'   ,'rif');
		$grid->column_orderby('Cuenta','cuenta','cuenta');
		$action = "javascript:window.location='".site_url('contabilidad/casi/auditoria')."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');
		$grid->build();

		$form = new DataForm('contabilidad/casi/auditsprv/process');
		$form->cuenta = new inputField('Cuenta contable', 'cuenta');
		$form->cuenta->rule = 'trim|required|existecpla';
		$form->cuenta->size =15;
		$form->cuenta->append($bcpla.'Coloque la cuenta contable para ser asginada a todos los registros encontrados y presione cambiar.');
		$form->grupo = new dropdownField('Grupo', 'grupo');
		$form->grupo->option('','Todos');
		$form->grupo->options("SELECT grupo,grupo AS val FROM sprv GROUP BY grupo");
		$form->grupo->style = 'width:140px';
		$form->grupo->append('Adicionalmente puede elegir un grupo para condicionar la asignaci&oacute;n');

		$form->submit('btnsubmit','Cambiar');
		$form->build_form();

		if ($form->on_success()){
			$cuenta= $this->db->escape($form->cuenta->newValue);
			$grupo  = $form->grupo->newValue;
			if(!empty($grupo)) $ww=' AND a.grupo='.$this->db->escape($grupo); else $ww='';
			$mSQL='UPDATE sprv AS a LEFT JOIN cpla AS b ON a.cuenta=b.codigo SET a.cuenta='.$cuenta.' WHERE b.codigo IS NULL'.$ww;
			$this->db->simple_query($mSQL);
			redirect('contabilidad/casi/auditsprv');
		}

		$data['content'] = ($grid->recordCount > 0) ? $form->output : '';
		$data['content'].= $grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Auditoria de cuentas en proveedores');
		$this->load->view('view_ventanas', $data);
	}

	function localizador($tipo){
		if($tipo=='cuenta'){
			$cc = 'cuenta';
			$tit= 'N&uacute;mero de cuenta';
			$rul= '';
			$maxlen='20';
		}else{
			$cc = 'transac';
			$tit= 'N&uacute;mero de transacci&oacute;n';
			$rul= '|callback_chvalidt';
			$maxlen='8';
		}

		// Si manda el valor en el uri
		if ( $this->uri->total_segments() == 6 ) {
			$tt = $this->uri->segment($this->uri->total_segments());
			if ( $this->uri->segment(5) == 'procesar' ) {
				$_POST['valor'] = $tt;
			}
		} else
			$tt = 'procesar';

		$this->rapyd->load('datagrid','dataform');

		$filter = new dataForm('contabilidad/casi/localizador/'.$tipo.'/procesar');

		$filter->valor = new inputField($tit, 'valor');
		$filter->valor->rule = 'required'.$rul;
		$filter->valor->autocomplete=false;
		$filter->valor->maxlength=$maxlen;
		$filter->valor->size=10;
		if ( $tt <> 'procesar') {
			$filter->valor->insertValue = $tt;
		}

		$action = "javascript:window.location='".site_url('contabilidad/casi/auditoria')."'";
		$filter->button('btn_regresa', 'Regresar', $action, 'BL');
		$filter->submit('btnsubmit','Buscar');
		$filter->build_form();

		$sal='';
		$verdad = ($filter->on_success() && $filter->is_valid());

		if ( $tt <> 'procesar') {
			$verdad = true;
		}

		if ( $verdad ) {
			$this->load->library('table');
			$this->table->set_heading('Tabla', 'Campo', 'Coincidencias');
			$valor = str_pad($filter->valor->newValue,8,'0', STR_PAD_LEFT);

			if ( $valor == '00000000' )
				$valor = $tt;

			$valor = $this->db->escape($valor);

			$tables = $this->db->list_tables();
			foreach ($tables as $table){
				if (preg_match("/^view_.*$|^sp_.*$|^viemovinxventas$|^vietodife$/i",$table)) continue;

				$fields = $this->db->list_fields($table);
				if (in_array($cc, $fields)){
					$mSQL="SELECT COUNT(*) AS cana FROM `${table}` WHERE ${cc} = ${valor}";

					$cana=$this->datasis->dameval($mSQL);
					if($cana>0){

						$grid = new DataGrid("${table}: ${cana}");
						//$grid->per_page = $cana;
						$grid->db->from($table);
						$grid->db->where("${cc} = ${valor}");
						$grid->db->limit(200);
						if(in_array('id', $fields)){
							$grid->db->orderby('id','desc');
						}

						foreach($fields as $ff){
							$grid->column($ff , $ff);
						}
						$grid->build();
						$sal.=$grid->output;
					}
				}
			}
		}
		$data['content'] = $filter->output.$sal;
		$data['title']   = heading('Localizador de Transacciones');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function chvalidt($transac){
		if (preg_match("/^[0-9]{1,8}$/i",$transac)) return true;

		$this->validation->set_message('chvalidt','La transacci&oacute;n no parece v&aacute;lida, debe tener una longitud no mayor a 8 y caracteres num&eacute;ricos');
		return false;
	}

	function _pre_insert($do){
		$comprob=$do->get('comprob');
		$fecha  =$do->get('fecha');
		$monto=$debe=$haber=0;

		$cana=$do->count_rel('itcasi');
		for($i=0;$i<$cana;$i++){ $o=$i+1;
			$adebe =$do->get_rel('itcasi','debe' ,$i);
			$ahaber=$do->get_rel('itcasi','haber',$i);
			$do->set_rel('itcasi','comprob',$comprob,$i);
			$do->set_rel('itcasi','fecha'  ,$fecha  ,$i);

			if ($adebe!=0 && $ahaber!=0){
				$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert']='No puede tener debe y haber en el asiento '.$o;
				return false;
			}
			if ($adebe==0 && $ahaber==0){
				$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert']='Debe tener debe o haber en el asiento '.$o;
				return false;
			}
			if($adebe != 0){
				$debe+=$adebe;
			}
			if($ahaber != 0){
				$haber+=$ahaber;
			}
		}
		if ($debe == 0){
			$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert']='Debe ingresar al menos un monto en la columna de debe.';
			return false;
		}
		if ($haber == 0){
			$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert']='Debe ingresar al menos un monto en la columna de haber.';
			return false;
		}
		if($debe-$haber != 0){ $do->set('status' ,'D'); }

		$transac=$this->datasis->fprox_numero('ntransa');
		$usuario=$do->get('usuario');
		$estampa=date('Ymd');
		$hora   =date('H:i:s');

		$do->set('debe' ,$debe);
		$do->set('haber',$haber);
		$do->set('total',$debe-$haber);
		$do->set('estampa',$estampa);
		$do->set('hora'   ,$hora);
		$do->set('transac',$transac);

		return true;
	}

	function _pre_update($do){

		$comprob= $do->get('comprob');
		$fecha  = $do->get('fecha');
		$cana   = $do->count_rel('itcasi');
		$monto  = $debe=$haber=0;

		for($i=0;$i<$cana;$i++){ $o=$i+1;
			$adebe = $do->get_rel('itcasi','debe',$i);
			$ahaber= $do->get_rel('itcasi','haber' ,$i);

			$do->set_rel('itcasi','comprob',$comprob,$i);
			$do->set_rel('itcasi','fecha'  ,$fecha  ,$i);

			if ($adebe!=0 && $ahaber!=0){
				$do->error_message_ar['pre_upd'] = $do->error_message_ar['update']='No puede tener debe y haber en el asiento '.$o;
				return false;
			}
			if ($adebe==0 && $ahaber==0){
				$do->error_message_ar['pre_upd'] = $do->error_message_ar['update']='Debe tener debe o haber en el asiento '.$o;
				return false;
			}
			if($adebe != 0){
				$debe+=$adebe;
			}
			if($ahaber != 0){
				$haber+=$ahaber;
			}
		}
		if ($debe == 0){
			$do->error_message_ar['pre_upd'] = $do->error_message_ar['update']='Debe ingresar al menos un monto en la columna de debe.';
			return false;
		}
		if ($haber == 0){
			$do->error_message_ar['pre_upd'] = $do->error_message_ar['update']='Debe ingresar al menos un monto en la columna de haber.';
			return false;
		}
		if($debe-$haber != 0){ $do->set('status' ,'D'); }

		$do->set('debe' ,$debe);
		$do->set('haber',$haber);
		$do->set('total',$debe-$haber);

		return true;
	}

	function _post_update($do){
		//trafrac ittrafrac
		$codigo=$do->get('comprob');
		logusu('casi',"Asiento $codigo MODIFICADO");
	}

	function _post_insert($do){
		//trafrac ittrafrac
		$codigo=$do->get('comprob');
		logusu('casi',"Asiento $codigo CREADO");
	}

	function _post_delete($do){
		$codigo=$do->get('comprob');
		logusu('casi',"Asiento $codigo ELIMINADO");
	}

	// Postea la tabla principal a Extjs
	function grid(){
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 30;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : '[{"property":"fecha","direction":"DESC"},{"property":"comprob","direction":"DESC"}]';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;

		$where = $this->datasis->extjsfiltro($filters,'casi');

		$this->db->_protect_identifiers=false;

		$this->db->select('*');
		$this->db->from('casi');
		if (strlen($where)>1){
			$this->db->where($where);
		}

		$sql = $this->db->_compile_select($this->db->_count_string . $this->db->_protect_identifiers('numrows'));
		$results = $this->datasis->dameval($sql);

		$sort = json_decode($sort, true);
		for ($i=0;$i<count($sort);$i++) {
			$this->db->order_by($sort[$i]['property'],$sort[$i]['direction']);
		}

		$this->db->limit($limit, $start);
		$query = $this->db->get();

		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}

	function griditcasi(){
		$comprob   = isset($_REQUEST['comprob'])  ? $_REQUEST['comprob']   :  '';
		if ($comprob == '' ) $comprob = $this->datasis->dameval("SELECT MAX(comprob) FROM casi") ;

		$mSQL = "SELECT * FROM itcasi a WHERE a.comprob='$comprob' ORDER BY a.cuenta";
		$query = $this->db->query($mSQL);
		$results =  0;
		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}

	function instalar(){
		$campos=$this->db->list_fields('casi');
		if(!in_array('id',$campos)){
			$mSQL='ALTER TABLE `casi` DROP PRIMARY KEY, ADD UNIQUE `comprob` (`comprob`)';
			$this->db->simple_query($mSQL);
			$mSQL='ALTER TABLE casi ADD id INT AUTO_INCREMENT PRIMARY KEY';
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('itcasi');
		if(!in_array('idcasi',$campos)){
			$mSQL='ALTER TABLE itcasi ADD idcasi INT(11)';
			$this->db->simple_query($mSQL);
			$mSQL='ALTER TABLE itcasi ADD INDEX idcasi (idcasi)';
			$this->db->simple_query($mSQL);
			$mSQL = "UPDATE itcasi a JOIN casi b ON a.comprob=b.comprob SET a.idcasi=b.id";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('idcasi',$campos)){
			$mSQL="ALTER TABLE `itcasi` ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`)";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('cpla');
		if(!in_array('ccosto',$campos)){
			$mSQL="ALTER TABLE `cpla` ADD COLUMN `ccosto` CHAR(1) NULL DEFAULT 'N' AFTER `sucursal`";
			$this->db->simple_query($mSQL);
		}
	}
}
