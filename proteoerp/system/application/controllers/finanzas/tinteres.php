<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
class Tinteres extends Controller {
	var $mModulo = 'TINTERES';
	var $titp    = 'Tasas de interés';
	var $tits    = 'Tasas de interés';
	var $url     = 'finanzas/tinteres/';

	function Tinteres(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'TINTERES', $ventana=0 );
	}

	function index(){
		$this->instalar();
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
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('TINTERES', 'JQ');
		$param['otros']       = $this->datasis->otros('TINTERES', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('tinteres', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'tinteres', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'tinteres', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('tinteres', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '300', '300' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

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

		$grid->addField('anio');
		$grid->label('Año');
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


		$grid->addField('mes');
		$grid->label('Mes');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('promedio');
		$grid->label('T.Promedio');
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


		$grid->addField('activa');
		$grid->label('T.Activa');
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


		$grid->addField('gaceta');
		$grid->label('Gaceta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('gfecha');
		$grid->label('F.Gaceta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
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

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('TINTERES','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('TINTERES','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('TINTERES','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('TINTERES','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: tinteresadd, editfunc: tinteresedit, delfunc: tinteresdel, viewfunc: tinteresshow');

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
		$mWHERE = $grid->geneTopWhere('tinteres');

		$response   = $grid->getData('tinteres', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion del Grid o Tabla
	//
	function setData(){
		echo 'Deshabilitado';
	}

	//******************************************************************
	// Edicion

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#gfecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
			$(".inputonlynum").numeric();
		});
		';

		$edit = new DataEdit('', 'tinteres');

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

		$edit->anio = new inputField('A&ntilde;o','anio');
		$edit->anio->rule='integer|required';
		$edit->anio->css_class='inputonlynum';
		$edit->anio->size =6;
		$edit->anio->maxlength =11;

		$edit->mes = new dropdownField('Mes', 'mes');
		$edit->mes->style='width:100px;';
		$edit->mes->option('01' ,'Enero'     );
		$edit->mes->option('02' ,'Febrero'   );
		$edit->mes->option('03' ,'Marzo'     );
		$edit->mes->option('04' ,'Abril'     );
		$edit->mes->option('05' ,'Mayo'      );
		$edit->mes->option('06' ,'Junio'     );
		$edit->mes->option('07' ,'Julio'     );
		$edit->mes->option('08' ,'Agosto'    );
		$edit->mes->option('09' ,'Septiembre');
		$edit->mes->option('10' ,'Octubre'   );
		$edit->mes->option('11' ,'Noviembre' );
		$edit->mes->option('12' ,'Diciembre' );
		$edit->mes->rule='required';

		$edit->promedio = new inputField('Tasa Promedio','promedio');
		$edit->promedio->rule='numeric|required';
		$edit->promedio->css_class='inputnum';
		$edit->promedio->size =12;
		$edit->promedio->maxlength =10;

		$edit->activa = new inputField('Tasa Activa','activa');
		$edit->activa->rule='numeric|required';
		$edit->activa->css_class='inputnum';
		$edit->activa->size =12;
		$edit->activa->maxlength =10;

		$edit->gaceta = new inputField('Nro. Gaceta','gaceta');
		$edit->gaceta->rule='';
		$edit->gaceta->size =12;
		$edit->gaceta->maxlength =50;

		$edit->gfecha = new dateonlyField('Fecha de la gaceta','gfecha');
		$edit->gfecha->rule='chfecha';
		$edit->gfecha->calendar=false;
		$edit->gfecha->size =10;
		$edit->gfecha->insertValue=date('Y-m-d');
		$edit->gfecha->maxlength =8;

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

	function instalar(){
		$this->datasis->creaintramenu(array('modulo'=>'531','titulo'=>'Tasa de interés','mensaje'=>'Tasas de interés','panel'=>'OBLIGACIONES','ejecutar'=>'finanzas/teinteres','target'=>'popu','visible'=>'S','pertenece'=>'7','ancho'=>900,'alto'=>600));

		if(!$this->db->table_exists('tinteres')){
			$mSQL="CREATE TABLE `tinteres` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `anio` int(11) DEFAULT NULL,
			  `mes` char(2) DEFAULT NULL,
			  `promedio` decimal(10,2) DEFAULT NULL,
			  `activa` decimal(10,2) DEFAULT NULL,
			  `gaceta` varchar(50) DEFAULT NULL,
			  `gfecha` date DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `anio_mes` (`anio`,`mes`)
			) ENGINE=MyISAM DEFAULT";
			$this->db->query($mSQL);

			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1997, '07', 19.00, 23.00, '36.276', '1997-08-25')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1997, '08', 19.00, 24.00, '36.301', '1997-09-29')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1997, '09', 18.00, 22.00, '36.321', '1997-10-28')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1997, '10', 18.00, 21.00, '36.340', '1997-11-24')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1997, '11', 18.00, 21.00, '36.360', '1997-12-22')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1997, '12', 21.00, 25.00, '36.377', '1998-01-20')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1998, '01', 21.00, 24.00, '36.400', '1998-02-20')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1998, '02', 29.00, 34.00, '36.420', '1998-03-24')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1998, '03', 30.00, 35.00, '36.440', '1998-04-24')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1998, '04', 32.00, 36.00, '36.459', '1998-05-22')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1998, '05', 38.00, 41.00, '36.475', '1998-06-15')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1998, '06', 38.00, 42.00, '36.503', '1998-07-27')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1998, '07', 53.00, 60.00, '36.522', '1998-08-21')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1998, '08', 51.00, 56.00, '36.549', '1998-09-29')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1998, '09', 63.00, 72.00, '36.567', '1998-10-26')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1998, '10', 47.00, 49.00, '36.581', '1998-11-13')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1998, '11', 42.00, 44.00, '36.614', '1999-01-05')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1998, '12', 39.00, 44.00, '36.624', '1999-01-19')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1999, '01', 36.00, 38.00, '36.652', '1999-03-02')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1999, '02', 35.00, 39.00, '36.670', '1999-03-26')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1999, '03', 30.00, 34.00, '36.682', '1999-04-16')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1999, '04', 27.00, 30.00, '36.703', '1999-05-18')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1999, '05', 24.00, 28.00, '36.726', '1999-06-18')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1999, '06', 24.00, 31.00, '36.749', '1999-07-17')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1999, '07', 23.00, 30.00, '36.770', '1999-08-23')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1999, '08', 21.00, 29.00, '36.793', '1999-09-23')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1999, '09', 21.00, 28.00, '36.812', '1999-10-21')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1999, '10', 21.00, 29.00, '36.837', '1999-11-25')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1999, '11', 22.00, 28.00, '36.857', '1999-12-27')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (1999, '12', 22.00, 28.00, '36.871', '2000-01-17')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2000, '01', 23.00, 29.00, '36.898', '2000-02-23')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2000, '02', 22.00, 28.00, '36.916', '2000-03-22')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2000, '03', 19.00, 25.00, '36.939', '2000-04-27')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2000, '04', 20.00, 25.00, '36.952', '2000-05-17')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2000, '05', 19.00, 23.00, '36.976', '2000-06-20')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2000, '06', 21.00, 26.00, '36.996', '2000-07-19')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2000, '07', 18.00, 23.00, '37.020', '2000-08-23')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2000, '08', 19.00, 23.00, '37.040', '2000-09-20')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2000, '09', 18.00, 23.00, '37.064', '2000-10-26')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2000, '10', 17.00, 21.00, '37.084', '2000-11-23')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2000, '11', 17.00, 21.00, '37.114', '2001-01-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2000, '12', 17.00, 21.00, '37.121', '2001-01-17')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2001, '01', 17.00, 22.00, '37.142', '2001-02-17')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2001, '02', 16.00, 21.00, '37.160', '2001-03-16')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2001, '03', 16.00, 21.00, '37.180', '2001-04-18')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2001, '04', 16.00, 20.00, '37.200', '2001-05-18')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2001, '05', 16.00, 20.00, '37.221', '2001-06-18')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2001, '06', 18.00, 23.00, '37.240', '2001-07-16')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2001, '07', 18.00, 22.00, '37.265', '2001-08-21')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2001, '08', 19.00, 24.00, '37.287', '2001-09-20')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2001, '09', 27.00, 35.00, '37.307', '2001-10-19')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2001, '10', 25.00, 31.00, '37.330', '2001-11-22')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2001, '11', 21.00, 26.00, '37.347', '2001-12-17')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2001, '12', 23.00, 27.00, '37.369', '2002-01-22')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2002, '01', 28.00, 35.00, '37.388', '2002-02-20')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2002, '02', 39.00, 53.00, '37.405', '2002-03-15')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2002, '03', 50.00, 55.00, '37.420', '2002-04-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2002, '04', 43.00, 48.00, '37.440', '2002-05-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2002, '05', 36.00, 38.00, '37.463', '2002-06-12')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2002, '06', 31.00, 35.00, '37.481', '2002-07-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2002, '07', 29.00, 32.00, '37.504', '2002-08-13')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2002, '08', 26.00, 30.00, '37.527', '2002-09-14')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2002, '09', 26.00, 30.00, '37.547', '2002-10-11')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2002, '10', 29.00, 32.00, '38.141', '2005-03-07')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2002, '11', 30.00, 33.00, '37.589', '2002-12-11')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2002, '12', 29.00, 33.00, '37.607', '2003-01-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2003, '01', 31.00, 36.00, '37.630', '2003-02-12')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2003, '02', 29.00, 33.00, '37.647', '2003-03-11')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2003, '03', 25.00, 31.00, '37.667', '2003-04-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2003, '04', 24.00, 29.00, '37.685', '2003-05-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2003, '05', 20.00, 25.00, '37.709', '2003-06-11')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2003, '06', 18.00, 23.00, '37.728', '2003-07-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2003, '07', 18.00, 22.00, '37.748', '2003-08-07')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2003, '08', 18.00, 23.00, '37.771', '2003-09-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2003, '09', 19.00, 22.00, '37.793', '2003-10-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2003, '10', 16.00, 21.00, '37.815', '2003-11-11')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2003, '11', 17.00, 19.00, '37.835', '2003-12-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2003, '12', 16.00, 19.00, '37.856', '2004-01-13')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2004, '01', 15.00, 18.00, '37.876', '2004-02-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2004, '02', 14.00, 18.00, '37.895', '2004-03-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2004, '03', 15.00, 17.00, '37.916', '2004-04-13')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2004, '04', 15.00, 17.00, '37.935', '2004-05-11')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2004, '05', 15.00, 17.00, '37.955', '2004-06-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2004, '06', 14.00, 17.00, '37.975', '2004-07-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2004, '07', 14.00, 17.00, '37.998', '2004-08-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2004, '08', 15.00, 17.00, '38.017', '2004-09-07')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2004, '09', 15.00, 16.00, '38.039', '2004-10-07')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2004, '10', 15.00, 17.00, '38.061', '2004-11-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2004, '11', 14.00, 16.00, '38.083', '2004-12-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2004, '12', 15.00, 16.00, '38.104', '2005-01-11')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2005, '01', 14.00, 16.00, '38.124', '2005-02-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2005, '02', 14.00, 16.00, '38.143', '2005-03-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2005, '03', 14.00, 16.00, '38.164', '2005-04-12')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2005, '04', 13.00, 15.00, '38.183', '2005-05-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2005, '05', 14.00, 16.00, '38.205', '2005-06-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2005, '06', 13.00, 15.00, '38.226', '2005-07-12')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2005, '07', 13.00, 15.00, '38.247', '2005-08-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2005, '08', 13.00, 15.00, '38.268', '2005-09-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2005, '09', 12.00, 14.00, '38.291', '2005-10-11')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2005, '10', 13.00, 15.00, '38.309', '2005-11-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2005, '11', 12.00, 15.00, '38.332', '2005-12-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2005, '12', 12.00, 14.00, '38.354', '2006-01-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2006, '01', 12.00, 14.00, '38.376', '2006-02-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2006, '02', 12.00, 15.00, '38.394', '2006-03-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2006, '03', 12.00, 14.00, '38.414', '2006-04-06')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2006, '04', 12.00, 14.00, '38.429', '2006-05-04')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2006, '05', 12.00, 14.00, '38.452', '2006-06-06')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2006, '06', 11.00, 13.00, '38.476', '2006-07-11')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2006, '07', 12.00, 14.00, '38.495', '2006-08-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2006, '08', 12.00, 14.00, '38.517', '2006-09-07')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2006, '09', 12.00, 14.00, '38.537', '2006-10-05')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2006, '10', 12.00, 14.00, '38.560', '2006-11-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2006, '11', 12.00, 15.00, '38.580', '2006-12-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2006, '12', 12.00, 15.00, '38.600', '2007-01-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2007, '01', 12.00, 15.00, '38.622', '2007-02-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2007, '02', 12.00, 15.00, '38.640', '2007-03-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2007, '03', 12.00, 14.00, '38.660', '2007-04-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2007, '04', 13.00, 15.00, '38.680', '2007-05-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2007, '05', 13.00, 15.00, '38.700', '2007-06-07')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2007, '06', 12.00, 14.00, '38.722', '2007-07-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2007, '07', 13.00, 16.00, '38.743', '2007-08-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2007, '08', 13.00, 16.00, '38.766', '2007-09-11')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2007, '09', 13.00, 16.00, '38.783', '2007-10-04')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2007, '10', 14.00, 16.00, '38.806', '2007-11-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2007, '11', 15.00, 19.00, '38.826', '2007-12-06')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2007, '12', 16.00, 21.00, '38847', '2008-01-10');");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2008, '01', 18.00, 24.00, '38.869', '2008-02-13')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2008, '02', 17.00, 22.00, '38.885', '2008-03-06')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2008, '03', 18.00, 22.00, '38.905', '2008-04-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2008, '04', 18.00, 22.00, '38.926', '2008-05-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2008, '05', 20.00, 24.00, '38.946', '2008-06-05')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2008, '06', 20.00, 22.00, '38.968', '2008-07-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2008, '07', 20.00, 23.00, '38.989', '2008-08-07')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2008, '08', 20.00, 22.00, '39.009', '2008-09-04')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2008, '09', 19.00, 22.00, '39.034', '2008-10-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2008, '10', 19.00, 22.00, '39.053', '2008-11-06')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2008, '11', 20.00, 23.00, '39.073', '2008-12-04')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2008, '12', 19.00, 21.00, '39.097', '0000-00-00')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2009, '01', 19.00, 22.00, '39.114', '2009-02-05')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2009, '02', 19.00, 22.00, '39.135', '2009-03-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2009, '03', 19.00, 22.00, '39.155', '2009-04-07')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2009, '04', 18.00, 21.00, '39.174', '2009-05-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2009, '05', 18.00, 21.00, '39.193', '2009-06-04')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2009, '06', 17.00, 20.00, '39.217', '2009-07-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2009, '07', 17.00, 20.00, '39.239', '2009-08-11')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2009, '08', 17.00, 19.00, '39.259', '2009-09-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2009, '09', 16.00, 18.00, '39.281', '2009-10-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2009, '10', 17.00, 20.00, '39.300', '2009-11-05')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2009, '11', 17.00, 18.00, '39.323', '2009-12-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2009, '12', 16.00, 18.00, '39.344', '2010-01-12')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2010, '01', 16.00, 18.00, '39.362', '2010-02-05')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2010, '02', 16.00, 18.00, '39.380', '2010-03-05')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2010, '03', 16.00, 18.00, '39.402', '2010-04-13')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2010, '04', 16.00, 17.00, '39.420', '2010-05-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2010, '05', 16.00, 17.00, '39.441', '2010-06-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2010, '06', 16.00, 17.00, '39.461', '2010-07-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2010, '07', 16.00, 17.00, '39.484', '2010-08-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2010, '08', 16.00, 17.00, '39.504', '2010-09-07')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2010, '09', 16.00, 17.00, '39.526', '2010-10-07')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2010, '10', 16.00, 17.00, '39.548', '2010-11-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2010, '11', 16.00, 17.00, '39.570', '2010-12-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2010, '12', 16.00, 17.00, '39.591', '2011-01-11')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2011, '01', 16.00, 17.00, '39.611', '2011-02-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2011, '02', 16.00, 17.00, '39.631', '2011-03-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2011, '03', 16.00, 17.00, '39.651', '2011-04-07')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2011, '04', 16.00, 17.00, '39.670', '2011-05-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2011, '05', 16.00, 18.00, '39.692', '2011-06-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2011, '06', 16.00, 17.00, '39.711', '2011-07-12')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2011, '07', 16.00, 18.00, '39.731', '2011-08-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2011, '08', 15.00, 17.00, '39.753', '2011-09-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2011, '09', 16.00, 17.00, '39.776', '2011-10-11')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2011, '10', 16.00, 18.00, '39.797', '2011-11-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2011, '11', 15.00, 16.00, '39.817', '2011-12-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2011, '12', 15.00, 15.00, '39.839', '2012-01-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2012, '01', 15.00, 16.00, '39,863', '2012-02-13')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2012, '02', 15.00, 15.00, '39,879', '2012-03-08')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2012, '03', 14.00, 15.00, '39,902', '2012-04-13')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2012, '04', 15.00, 16.00, '39,914', '2012-05-03')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2012, '05', 15.00, 16.00, '39,943', '2012-06-13')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2012, '06', 15.00, 16.00, '39,961', '2012-07-10')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2012, '07', 15.00, 16.00, '39.980', '2012-08-07')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2012, '08', 15.00, 16.00, '40,005', '2012-09-11')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2012, '09', 15.00, 16.00, '40,025', '2012-10-09')");
			$this->db->simple_query("INSERT IGNORE INTO `tinteres` (`anio`, `mes`, `promedio`, `activa`, `gaceta`, `gfecha`) VALUES (2012, '10', 15.00, 16.00, '40,047', '2012-11-09')");
		}
		//$campos=$this->db->list_fields('tinteres');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}
