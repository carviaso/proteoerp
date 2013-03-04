<?php
class Riva extends Controller {
	var $mModulo='RIVA';
	var $titp='Retenciones de I.V.A.';
	var $tits='Retenciones I.V.A.';
	var $url ='finanzas/riva/';

	function Riva(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_nombre( $modulo, $ventana=0 );
	}

	function index(){
		if ( !$this->datasis->iscampo('riva','id') ) {
			$this->db->simple_query('ALTER TABLE riva DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE riva ADD UNIQUE INDEX comprob (nrocomp)');
			$this->db->simple_query('ALTER TABLE riva ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$bodyscript = '
		<script type="text/javascript">

			jQuery("#a1").click( function(){
				var id = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
				if (id)	{
					var ret = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getRowData\',id);
					window.open(\''.base_url().'formatos/ver/RIVA/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
				} else { $.prompt("<h1>Por favor Seleccione una Retenci&oacute;n</h1>");}
			});

		</script>';

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		$WestPanel = $grid->deploywestp();

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'a1','img'=>'assets/default/images/print.png','alt' => 'Imprimir documento', 'label'=>'Reimprimir Documento'));
		$WestPanel = $grid->deploywestp();

		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'));

		$param['WestPanel']  = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['SouthPanel'] = $SouthPanel;
		$param['listados'] = $this->datasis->listados('RIVA', 'JQ');
		$param['otros']    = $this->datasis->otros('RIVA', 'JQ');
		$param['temas']     = array('proteo','darkness','anexos1');
		$param['bodyscript'] = $bodyscript;
		$param['tabs'] = false;
		$param['encabeza'] = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('periodo');
		$grid->label('Per&iacute;odo');
		$grid->params(array(
			'align'         => '"center"',
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 8 }',
		));

		$grid->addField('nrocomp');
		$grid->label('Comprob.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));

		$grid->addField('emision');
		$grid->label('Emisi&oacute;n');
		$grid->params(array(
			'align'         => '"center"',
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Emision" }'
		));

		$grid->addField('tipo_doc');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => '"center"',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
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

		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 90,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));

		$grid->addField('clipro');
		$grid->label('Prov.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre del Proveedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));

		$grid->addField('nfiscal');
		$grid->label('Nro.fiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 90,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));


		$grid->addField('afecta');
		$grid->label('Afecta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false }',
			'editoptions'   => '{ size:20, maxlength: 20 }',
			'formoptions'   => '{ label:"Fact.Afectada" }'
		));

		$grid->addField('rif');
		$grid->label('R.I.F.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 14 }',
		));

/*
		$grid->addField('exento');
		$grid->label('Exento');
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

		$grid->addField('tasa');
		$grid->label('Tasa');
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

		$grid->addField('general');
		$grid->label('General');
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

		$grid->addField('geneimpu');
		$grid->label('Geneimpu');
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

		$grid->addField('tasaadic');
		$grid->label('Tasa Adic.');
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


		$grid->addField('adicional');
		$grid->label('Adicional');
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


		$grid->addField('adicimpu');
		$grid->label('Adicimpu');
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


		$grid->addField('tasaredu');
		$grid->label('Tasaredu');
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


		$grid->addField('reducida');
		$grid->label('Reducida');
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


		$grid->addField('reduimpu');
		$grid->label('Reduimpu');
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
*/

		$grid->addField('stotal');
		$grid->label('Sub Total');
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


		$grid->addField('impuesto');
		$grid->label('Impuesto');
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


		$grid->addField('gtotal');
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


		$grid->addField('reiva');
		$grid->label('Retencion');
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


		$grid->addField('transac');
		$grid->label('Transaccion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
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


		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));


		$grid->addField('ffactura');
		$grid->label('Fecha Fac.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 130,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 60,
			'editable' => $editar,
			'search'   => 'false'
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(false);
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

	/**
	* Busca la data en el Servidor por json
	*/
	function getdata()
	{
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('riva');

		$response   = $grid->getData('riva', array(array()), array(), false, $mWHERE, 'id', 'desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData()
	{
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$this->db->insert('riva', $data);
				//echo "Registro Agregado";
				//logusu('RIVA',"Registro ????? INCLUIDO");
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			//unset($data['ubica']);
			$this->db->where('id', $id);
			$this->db->update('riva', $data);
			logusu('RIVA',"Registro ????? MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM riva WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				//$this->db->simple_query("DELETE FROM riva WHERE id=$id ");
				//logusu('RIVA',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}
}

/*
class riva extends Controller {
	var $titp='';
	var $tits='Retenciones';
	var $url ='finanzas/riva/';

	function riva(){
		parent::Controller();
		$this->load->library('rapyd');
		//$this->datasis->modulo_id('523',1);
	}

	function index(){
		redirect($this->url.'filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'riva');

		$filter->nrocomp = new inputField('Comprobante','nrocomp');
		$filter->nrocomp->rule      ='max_length[8]';
		$filter->nrocomp->size      =10;
		$filter->nrocomp->maxlength =8;

		$filter->emision = new dateField('Fecha de Emisi&oacute;n','emision');
		$filter->emision->rule      ='chfecha';
		$filter->emision->size      =10;
		$filter->emision->maxlength =8;

		$filter->fecha = new dateField('Fecha','fecha');
		$filter->fecha->rule      ='chfecha';
		$filter->fecha->size      =10;
		$filter->fecha->maxlength =8;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#nrocomp#></raencode>','<#nrocomp#>');

		$grid = new DataGrid('');
		$grid->order_by('nrocomp','desc');
		$grid->per_page = 40;

		$grid->column_orderby('Comprobante',"$uri",'nrocomp','align="left"');
		$grid->column_orderby('Emisi&oacute;n'    ,"<dbdate_to_human><#emision#></dbdate_to_human>",'emision','align="center"');
		$grid->column_orderby('Tipo Doc.'  ,"tipo_doc",'tipo_doc','align="left"');
		$grid->column_orderby('Fecha'      ,"<dbdate_to_human><#fecha#></dbdate_to_human>",'fecha','align="center"');
		$grid->column_orderby('N&uacute;mero',"numero",'numero','align="left"');
		$grid->column_orderby('Num. f&iacute;scal',"nfiscal",'nfiscal','align="left"');
		$grid->column_orderby('Nombre'     ,"(<#clipro#>) <#nombre#>",'nombre','align="left"');
		$grid->column_orderby('RIF'        ,"rif",'rif','align="left"');
		$grid->column_orderby('Monto Retenido',"<nformat><#reiva#></nformat>",'reiva','align="right"');
		$grid->column_orderby('Transaci&oacute;n',"transac",'transac','align="left"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'riva');
		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert' ,'_pre_insert');
		$edit->pre_process('update' ,'_pre_update');
		$edit->pre_process('delete' ,'_pre_delete');

		$edit->nrocomp = new inputField('Nro. Comprobante','nrocomp');
		$edit->nrocomp->rule='max_length[8]';
		$edit->nrocomp->size =10;
		$edit->nrocomp->maxlength =8;

		$edit->emision = new dateField('Emisi&oacute;n','emision');
		$edit->emision->rule='chfecha';
		$edit->emision->size =10;
		$edit->emision->maxlength =8;

		$edit->periodo = new inputField('Per&iacute;odo','periodo');
		$edit->periodo->rule='max_length[8]';
		$edit->periodo->size =10;
		$edit->periodo->maxlength =8;

		$edit->tipo_doc = new inputField('Tipo Doc.','tipo_doc');
		$edit->tipo_doc->rule='max_length[2]';
		$edit->tipo_doc->size =4;
		$edit->tipo_doc->maxlength =2;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule='max_length[12]';
		$edit->numero->size =14;
		$edit->numero->maxlength =12;

		$edit->nfiscal = new inputField('N&uacute;mero F&iacute;scal','nfiscal');
		$edit->nfiscal->rule='max_length[12]';
		$edit->nfiscal->size =14;
		$edit->nfiscal->maxlength =12;

		$edit->afecta = new inputField('Doc. Afectado','afecta');
		$edit->afecta->rule='max_length[8]';
		$edit->afecta->size =10;
		$edit->afecta->maxlength =8;

		$edit->clipro = new inputField('Cliente/Proveedor','clipro');
		$edit->clipro->rule='max_length[5]';
		$edit->clipro->size =7;
		$edit->clipro->maxlength =5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[40]';
		$edit->nombre->size =42;
		$edit->nombre->maxlength =40;
		$edit->nombre->in='clipro';

		$edit->rif = new inputField('RIF','rif');
		$edit->rif->rule='max_length[14]';
		$edit->rif->size =16;
		$edit->rif->maxlength =14;

		$edit->exento = new inputField('Monto exento','exento');
		$edit->exento->rule='max_length[15]|numeric';
		$edit->exento->css_class='inputnum';
		$edit->exento->size =17;
		$edit->exento->maxlength =15;

		$edit->tasa = new inputField('Tasa General','tasa');
		$edit->tasa->rule='max_length[5]|numeric';
		$edit->tasa->css_class='inputnum';
		$edit->tasa->size =7;
		$edit->tasa->maxlength =5;
		$edit->tasa->group='Alicuota General';

		$edit->general = new inputField('Base general','general');
		$edit->general->rule='max_length[15]|numeric';
		$edit->general->css_class='inputnum';
		$edit->general->size =17;
		$edit->general->maxlength =15;
		$edit->general->group='Alicuota General';

		$edit->geneimpu = new inputField('Impuesto general','geneimpu');
		$edit->geneimpu->rule='max_length[15]|numeric';
		$edit->geneimpu->css_class='inputnum';
		$edit->geneimpu->size =17;
		$edit->geneimpu->maxlength =15;
		$edit->geneimpu->group='Alicuota General';

		$edit->tasaadic = new inputField('Tasa adicional','tasaadic');
		$edit->tasaadic->rule='max_length[5]|numeric';
		$edit->tasaadic->css_class='inputnum';
		$edit->tasaadic->size =7;
		$edit->tasaadic->maxlength =5;
		$edit->tasaadic->group='Alicuota Adicional';

		$edit->adicional = new inputField('Base adicional','adicional');
		$edit->adicional->rule='max_length[15]|numeric';
		$edit->adicional->css_class='inputnum';
		$edit->adicional->size =17;
		$edit->adicional->maxlength =15;
		$edit->adicional->group='Alicuota Adicional';

		$edit->adicimpu = new inputField('Impuesto adicional','adicimpu');
		$edit->adicimpu->rule='max_length[15]|numeric';
		$edit->adicimpu->css_class='inputnum';
		$edit->adicimpu->size =17;
		$edit->adicimpu->maxlength =15;
		$edit->adicimpu->group='Alicuota Adicional';

		$edit->tasaredu = new inputField('Tasa reducida','tasaredu');
		$edit->tasaredu->rule='max_length[5]|numeric';
		$edit->tasaredu->css_class='inputnum';
		$edit->tasaredu->size =7;
		$edit->tasaredu->maxlength =5;
		$edit->tasaredu->group='Alicuota Reducida';

		$edit->reducida = new inputField('Base reducida','reducida');
		$edit->reducida->rule='max_length[15]|numeric';
		$edit->reducida->css_class='inputnum';
		$edit->reducida->size =17;
		$edit->reducida->maxlength =15;
		$edit->reducida->group='Alicuota Reducida';

		$edit->reduimpu = new inputField('Impuesto reducido','reduimpu');
		$edit->reduimpu->rule='max_length[15]|numeric';
		$edit->reduimpu->css_class='inputnum';
		$edit->reduimpu->size =17;
		$edit->reduimpu->maxlength =15;
		$edit->reduimpu->group='Alicuota Reducida';

		$edit->stotal = new inputField('Sub-total','stotal');
		$edit->stotal->rule='max_length[15]|numeric';
		$edit->stotal->css_class='inputnum';
		$edit->stotal->size =17;
		$edit->stotal->maxlength =15;
		$edit->stotal->group='Res&uacute;men';

		$edit->impuesto = new inputField('Impuesto total','impuesto');
		$edit->impuesto->rule='max_length[15]|numeric';
		$edit->impuesto->css_class='inputnum';
		$edit->impuesto->size =17;
		$edit->impuesto->maxlength =15;
		$edit->impuesto->group='Res&uacute;men';

		$edit->gtotal = new inputField('Total','gtotal');
		$edit->gtotal->rule='max_length[15]|numeric';
		$edit->gtotal->css_class='inputnum';
		$edit->gtotal->size =17;
		$edit->gtotal->maxlength =15;
		$edit->gtotal->group='Res&uacute;men';

		$edit->reiva = new inputField('Monto retenido','reiva');
		$edit->reiva->rule='max_length[15]|numeric';
		$edit->reiva->css_class='inputnum';
		$edit->reiva->size =17;
		$edit->reiva->maxlength =15;
		$edit->reiva->group='Res&uacute;men';

		$edit->transac = new inputField('Transaci&oacute;n','transac');
		$edit->transac->rule='max_length[8]';
		$edit->transac->size =10;
		$edit->transac->maxlength =8;

		//$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		//$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));
		//$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		$edit->ffactura = new dateField('Fecha de factura','ffactura');
		$edit->ffactura->rule='chfecha';
		$edit->ffactura->size =10;
		$edit->ffactura->maxlength =8;

		$edit->buttons('back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

	}

	function _pre_insert($do){
		return false;
	}

	function _pre_delete($do){
		return false;
	}

	function _pre_update($do){
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
		$mSQL="CREATE TABLE `riva` (
		  `nrocomp` char(8) NOT NULL DEFAULT '',
		  `emision` date DEFAULT NULL,
		  `periodo` char(8) DEFAULT NULL,
		  `tipo_doc` char(2) DEFAULT NULL,
		  `fecha` date DEFAULT NULL,
		  `numero` char(12) DEFAULT NULL,
		  `nfiscal` char(12) DEFAULT NULL,
		  `afecta` char(8) DEFAULT NULL,
		  `clipro` char(5) DEFAULT NULL,
		  `nombre` char(40) DEFAULT NULL,
		  `rif` char(14) DEFAULT NULL,
		  `exento` decimal(15,2) DEFAULT NULL,
		  `tasa` decimal(5,2) DEFAULT NULL,
		  `general` decimal(15,2) DEFAULT NULL,
		  `geneimpu` decimal(15,2) DEFAULT NULL,
		  `tasaadic` decimal(5,2) DEFAULT NULL,
		  `adicional` decimal(15,2) DEFAULT NULL,
		  `adicimpu` decimal(15,2) DEFAULT NULL,
		  `tasaredu` decimal(5,2) DEFAULT NULL,
		  `reducida` decimal(15,2) DEFAULT NULL,
		  `reduimpu` decimal(15,2) DEFAULT NULL,
		  `stotal` decimal(15,2) DEFAULT NULL,
		  `impuesto` decimal(15,2) DEFAULT NULL,
		  `gtotal` decimal(15,2) DEFAULT NULL,
		  `reiva` decimal(15,2) DEFAULT NULL,
		  `transac` char(8) DEFAULT NULL,
		  `estampa` date DEFAULT NULL,
		  `hora` char(8) DEFAULT NULL,
		  `usuario` char(12) DEFAULT NULL,
		  `ffactura` date DEFAULT '0000-00-00',
		  PRIMARY KEY (`nrocomp`),
		  UNIQUE KEY `rivatra` (`transac`),
		  KEY `Numero` (`numero`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		$this->db->simple_query($mSQL);
	}
}
*/
?>
