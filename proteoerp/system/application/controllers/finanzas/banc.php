<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Banc extends Controller {
	var $mModulo='BANC';
	var $titp='Bancos y Cajas';
	var $tits='Bancos y Cajas';
	var $url ='finanzas/banc/';

	function Banc(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'BANC', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 900, 600, substr($this->url,0,-1) );
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
		//$grid->wbotonadd(array('id'=>'recalban', 'img'=>'images/pdf_logo.gif',  'alt' => 'Formato PDF', 'label'=>'Recalcular Saldo'));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Banco o Caja'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '
		function factivo(el, val, opts){
			var meco=\'<div><img src="'.base_url().'images/S.gif" width="20" height="18" border="0" /></div>\';
			if ( el == "N" ){
				meco=\'<div><img src="'.base_url().'images/N.gif" width="20" height="20" border="0" /></div>\';
			}
			return meco;
		}
		';

		$param['WestPanel']   = $WestPanel;
		$param['funciones']   = $funciones;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('BANC', 'JQ');
		$param['otros']       = $this->datasis->otros('BANC', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);

	}

	//******************************************************************
	//Funciones de los Botones
	//
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';
		$ngrid      = "#newapi".$grid0;

		$mSQL = "INSERT IGNORE INTO bsal (codbanc,
		ano,  saldo,   saldo01, saldo02, saldo03, saldo04, saldo05,
		saldo06, saldo07, saldo08, saldo09, saldo10, saldo11, saldo12)
		SELECT codbanc, YEAR(curdate()), 0 saldo, 0 saldo01, 0 saldo02, 0 saldo03, 0 saldo04, 0 saldo05,
		0 saldo06, 0 saldo07, 0 saldo08, 0 saldo09, 0 saldo10, 0 saldo11, 0 saldo12
		FROM banc WHERE activo='S'";
		$this->db->query($mSQL);

		$mSQL = "SELECT ano, ano nombre FROM bsal WHERE ano <= YEAR(curdate()) GROUP BY ano ORDER BY ano DESC";
		$mano = $this->datasis->llenaopciones($mSQL, false, 'mmano');
		$mano = str_replace('"',"'",$mano);


		$bodyscript .= '
		function crecalban() {
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("'.$ngrid.'").jqGrid(\'getRowData\',id);
				$.prompt( "<h1>Recalcular Saldo de Banco:</h1><br/><center>Periodo: '.$mano.' Saldo Inicial: <input class=\'inputnum\' type=\'text\' id=\'msaldo\' name=\'msaldo\' value=\'0.00\' maxlengh=\'10\' size=\'10\' ></center><br/>",
				{
					buttons: { Aplicar: true, Cancelar: false },
					submit: function(e,v,m,f){
						if (v) {
							if( f.mmano==null ){
								apprise("Cancelado por el usuario");
							} else if( f.mmano=="" ) {
								apprise("<h1>Cancelado</h1>Fecha vacia");
							} else {
								saldo = Math.round(f.msaldo*100,0);
								frecalbanco( id, f.mmano, saldo );							}
						}
					}
				});
			} else { $.prompt("<h1>Por favor Seleccione un Banco</h1>");}
		};
		';

		$bodyscript .= '
		function fresumen( id, ano ){
			$.ajax({
				url: "'.base_url().$this->url.'resumen/"+id+"/"+ano,
				success: function(msg){
					$("#ladicional").html(msg);
				}
			});
		};
		';

		$bodyscript .= '
		function frecalbanco( id, ano, saldo ){
			$.blockUI({message: "<h1>Calculando Saldos.....</h1><img  src=\''.base_url().'images/doggydig.gif\' width=\'131px\' height=\'79px\'  /> "});
			$.post("'.site_url('finanzas/banc/recalban').'/"+id+"/"+ano+"/"+saldo, function(){ $.unblockUI(); })
		};
		';

		$bodyscript .= '
		function bancadd() {
			$.post("'.site_url('finanzas/banc/dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function bancshow(){
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
		function bancedit() {
			var id     = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("'.$ngrid.'").getRowData(id);
				mId = id;
				$.post("'.site_url('finanzas/banc/dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		};';

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);
		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, $height = "450", $width = "750" );
		$bodyscript .= $this->jqdatagrid->bsfshow( $height = "450", $width = "750" );

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
		$linea   = 1;
		$grid  = new $this->jqdatagrid;

		$grid->addField('activo');
		$grid->label('-');
		$grid->params(array(
			'align'         => '"center"',
			'search'        => 'false',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 1 }',
			'formatter'     => 'factivo',
		));

		$grid->addField('codbanc');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'align'         => '"center"',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:3, maxlength: 2 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$mSQL = "SELECT cod_banc, CONCAT(cod_banc, ' ', nomb_banc) descrip FROM tban WHERE activo='S' ORDER BY cod_banc ";
		$tbanco = $this->datasis->llenajqselect($mSQL, false );

		$grid->addField('tbanco');
		$grid->label('Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{value: '.$tbanco.',  style:"width:300px" }',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('banco');
		$grid->label('Nombre del Banco/Caja');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 180,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2, label:"Nombre" }'
		));

		$mSQL = "SELECT moneda, CONCAT(moneda, ' ', descrip) descrip FROM mone ORDER BY moneda ";
		$moneda  = $this->datasis->llenajqselect($mSQL, false );

		$linea = $linea + 1;
		$grid->addField('moneda');
		$grid->label('-');
		$grid->params(array(
			'align'         => '"center"',
			'search'        => 'false',
			'editable'      => $editar,
			'width'         => 30,
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$moneda.',  style:"width:120px"}',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label:"Moneda" }'
		));

		$grid->addField('numcuent');
		$grid->label('Cuenta Nro.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 180,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:25, maxlength: 25 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$grid->addField('saldo');
		$grid->label('Saldo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$linea = $linea + 1;
		$grid->addField('tipocta');
		$grid->label('Tipo Cta.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"C":"Corriente","K":"Caja","A":"Ahorro", "P":"Plazo", "T":"T. Credito", "Q":"Caja Chica" },  style:"width:120px" }',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label:"Tipo" }'
		));

		$linea = $linea + 1;
		$grid->addField('dire1');
		$grid->label('Direcci&oacute;n 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$grid->addField('dire2');
		$grid->label('Direcci&oacute;n 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));


		$linea = $linea + 1;
		$grid->addField('telefono');
		$grid->label('Tel&eacute;fono');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));


		$linea = $linea + 1;
		$grid->addField('nombre');
		$grid->label('Contacto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));


		$grid->addField('proxch');
		$grid->label('Pr&oacute;ximo cheque');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 90,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label:"Prox Cheque" }'
		));

		$linea = $linea + 1;
		$grid->addField('dbporcen');
		$grid->label('I.D.B. %');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 60,
			'editrules'     => '{ required:false }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1  }'
		));


		$mSQL = "SELECT COUNT(*) FROM mgas a JOIN grga b ON a.grupo=b.grupo WHERE b.nom_grup='GASTOS FINANCIEROS' ORDER BY codigo ";
		if ( $this->datasis->dameval($mSQL) == 0 )
			$mSQL = "SELECT codigo, CONCAT(codigo,' ',descrip) descrip FROM mgas a JOIN grga b ON a.grupo=b.grupo ORDER BY codigo ";
		else
			$mSQL = "SELECT codigo, CONCAT(codigo,' ',descrip) descrip FROM mgas a JOIN grga b ON a.grupo=b.grupo WHERE b.nom_grup='GASTOS FINANCIEROS' ORDER BY codigo ";
		$gastocom  = $this->datasis->llenajqselect($mSQL, false );

		$grid->addField('gastoidb');
		$grid->label('Gasto IDB');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'select'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{value: '.$gastocom.',  style:"width:300px" }',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2, label:"Gasto IDB" }'
		));


		$linea = $linea + 1;
		$grid->addField('gastocom');
		$grid->label('Comisiones');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'select'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{value: '.$gastocom.',  style:"width:300px" }',
			'editrules'     => '{ required:false}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2, label:"Comisiones" }'
		));

/*
		$grid->addField('dbcta');
		$grid->label('Gasto IDB');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('dbgas');
		$grid->label('Dbgas');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));

		$grid->addField('impucu');
		$grid->label('Impucu');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 15 }',
		));


		$grid->addField('comicu');
		$grid->label('Comicu');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 15 }',
		));


		$grid->addField('comipr');
		$grid->label('Comipr');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 15 }',
		));
*/



		$linea = $linea + 1;
		$grid->addField('codprv');
		$grid->label('Proveedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
//			'editoptions'   => '{'.$grid->autocomplete(site_url('ajax/buscasprv'), 'codprv','ncodprv','<div id=\"ncodprv\"><b>"+ui.item.nombre+"</b></div>').', size:6}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label:"Proveedor" }'
		));


		$mSQL = "SELECT depto, CONCAT( depto,' ', descrip ) FROM dpto  WHERE tipo='G' ORDER BY depto ";
		$dpto  = $this->datasis->llenajqselect($mSQL, false );
		$grid->addField('depto');
		$grid->label('Depto.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
//			'editoptions'   => '{value: '.$dpto.',  style:"width:250px" }',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2, label:"Departamento" }'
		));


		$linea = $linea + 1;


		$grid->addField('cuenta');
		$grid->label('Contabilidad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 110,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
//			'editoptions'   => '{'.$grid->autocomplete(site_url('ajax/buscacpla'), 'cuenta','cucucu','<div id=\"cucucu\"><b>"+ui.item.descrip+"</b></div>').', size:12}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label:"Cta. Contable" }'
		));



		$mSQL = "SELECT codigo, CONCAT(codigo,' ',sucursal) sucursal FROM sucu ORDER BY codigo";
		$sucu = $this->datasis->llenajqselect($mSQL);
		$grid->addField('sucur');
		$grid->label('Sucursal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
//			'editoptions'   => '{value: '.$sucu.',  style:"width:250px" }',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
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
		$grid->setHeight('360');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
		function(id){
			if (id){
				var ret = jQuery(gridId1).jqGrid(\'getRowData\',id);
				fresumen( id, 0 );
			}
		}'
		);


/*
		$grid->setOnSelectRow('
		function(id){
			if (id){
				var ret = jQuery(gridId1).jqGrid(\'getRowData\',id);
				$.ajax({
					url: "'.base_url().$this->url.'resumen/"+id,
					success: function(msg){
						$("#ladicional").html(msg);
					}
				});
			}
		}'
		);
*/

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('BANC','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('BANC','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('BANC','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('BANC','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: bancadd,editfunc: bancedit, viewfunc: bancshow');

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
		$mWHERE = $grid->geneTopWhere('banc');

		$response   = $grid->getData('banc', array(array()), array(), false, $mWHERE );
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
		$dbid   = $this->db->escape($id);
		$data   = $_POST;
		$mcodp  = 'codbanc';
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper=='add'){
			echo 'Opcion deshabilitada';
		}elseif($oper == 'edit') {
			$nuevo    = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT ${mcodp} FROM banc WHERE id=${dbid}");
			$meco     = $this->datasis->dameval("SELECT ${mcodp} FROM banc WHERE id=${dbid}");
			unset($data[$mcodp]);
			$this->db->where('id', $id);
			$this->db->update('banc', $data);
			logusu('BANC','Banco o Caja  '.$meco.' MODIFICADO');
			echo 'Banco o Caja '.$meco.' Modificado';
		}elseif($oper == 'del'){
			$codbanc = $this->datasis->dameval("SELECT codbanc FROM banc WHERE id=${dbid}");
			$this->db->query("UPDATE banc SET activo = IF(activo='S','N','S') WHERE id=${dbid}");
			logusu('BANC',"Registro ${codbanc} DESACTIVADO/ACTIVADO");
			echo 'Registro Desactivado/Avtivado';
		};
	}


	//***************************************
	//
	//   Dataedit
	//
	function dataedit(){
		$this->rapyd->load('dataedit');

		$atts = array(
			'width'     =>'800',
			'height'    =>'600',
			'scrollbars'=>'yes',
			'status'    =>'yes',
			'resizable' =>'yes',
			'screenx'   =>'5',
			'screeny'   =>'5');

		$qformato=$this->qformato=$this->datasis->formato_cpla();

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

		$bcpla = $this->datasis->modbus($mCPLA);

		$modbus=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;digo Proveedor',
			'nombre'=>'Nombre',
			'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'codprv'),
			'titulo'  =>'Buscar Proveedor');

		$boton=$this->datasis->modbus($modbus);

		$mTBAN=array(
			'tabla'   =>'tban',
			'columnas'=>array(
				'cod_banc' =>'C&oacute;digo',
				'nomb_banc'=>'Banco'),
			'filtro'  =>array('cod_banc'=>'C&oacute;digo','nomb_banc'=>'Banco'),
			'retornar'=>array('cod_banc'=>'tbanco','nomb_banc'=>'banco'),
			'titulo'  =>'Buscar Banco'
			);

		$bTBAN =$this->datasis->modbus($mTBAN);

		$link=site_url('finanzas/banc/ubanc');

		$script ='
		function  add_proveed(){
			$.prompt("<h1>Opci&oacute;n no habilitada</h1>");
		}

		function gasto(){
			a=parseInt(dbporcen.value);
			if(a>0 && a<100){
				$("#tr_gastoidb").show();
			}else{
				$("#tr_gastoidb").hide();
			}
		}

		function ultimo(){
			$.ajax({
				url: "'.$link.'",
				success: function(msg){
				  alert( "El ultimo codigo ingresado fue: " + msg );
				}
			});
		}
		$(function() {
			gasto();
			$(".inputnum").numeric(".");
		});

		$("#rif").focusout(function(){
			if ( $(this).val() != "CAJ"  ){
				rif = $(this).val().toUpperCase();
				$(this).val(rif);
				patt = /[EJPGV][0-9]{4,9} */g;
				if(!patt.test(rif)){
					alert("El RIF o Cedula introducida no es correcta, por favor verifique e intente de nuevo.");
					$(this).val("");
				}
			}
		});

		$("#tbanco").change(function(){
			tbanco = $(this).val();
			if ( tbanco != "CAJ" ){
				$.post("'.site_url('finanzas/banc/traerif').'/"+tbanco,
				function(data){
					$("#rif").val(data);
				})
			} else {
				$("#rif").val("");
			}
		});
		';

		$edit = new DataEdit('', 'banc');
		$edit->on_save_redirect=false;

		$edit->script($script, 'create');
		$edit->script($script, 'modify');

		$edit->pre_process( 'insert','_pre_insert');
		$edit->pre_process( 'update','_pre_update');
		$edit->pre_process( 'delete','_pre_delete' );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$lultimo='<a href="javascript:ultimo();" title="Consultar ultimo codigo ingresado" onclick="">Consultar ultimo codigo</a>';
		$edit->codbanc = new inputField('C&oacute;digo', 'codbanc');
		$edit->codbanc->rule = 'trim|required|callback_chexiste';
		$edit->codbanc->mode ='autohide';
		$edit->codbanc->maxlength = 2;
		$edit->codbanc->size = 3;
		//$edit->codbanc->append($lultimo);

		$edit->activo = new dropdownField('Activo', 'activo');
		$edit->activo->style ='width:50px;';
		$edit->activo->rule='required|enum[S,N]';
		$edit->activo->options(array('S'=>'Si','N'=>'No' ));

		$edit->tbanco = new dropdownField('Caja/Banco', 'tbanco');
		$edit->tbanco->option('','Seleccione');
		$edit->tbanco->options("SELECT cod_banc, concat(cod_banc, ' ',nomb_banc) descrip FROM tban ORDER BY nomb_banc");
		$edit->tbanco->rule='required';
		$edit->tbanco->style = "width:200px";

		$edit->banco = new inputField('Nombre', 'banco');
		$edit->banco->size =22;
		$edit->banco->maxlength=30;
		//$edit->banco->readonly=true;

		$edit->numcuent = new inputField('Nro. de Cuenta', 'numcuent');
		$edit->numcuent->rule='trim';
		$edit->numcuent->size = 24;
		$edit->numcuent->maxlength=25;

		$edit->dire1 = new inputField('Direcci&oacute;n', 'dire1');
		$edit->dire1->rule='trim';
		$edit->dire1->size =40;
		$edit->dire1->maxlength=40;

		$edit->dire2 = new inputField('', 'dire2');
		$edit->dire2->rule='trim';
		$edit->dire2->size =40;
		$edit->dire2->maxlength=40;

		$edit->telefono = new inputField('Tel&eacute;fono', 'telefono');
		$edit->telefono->rule='trim';
		$edit->telefono->size =40;
		$edit->telefono->maxlength=40;

		$edit->nombre = new inputField('Contacto', 'nombre');
		$edit->nombre->rule='trim';
		$edit->nombre->size =40;
		$edit->nombre->maxlength=40;

		$edit->moneda = new dropdownField('Moneda','moneda');
		$edit->moneda->options('SELECT moneda, descrip FROM mone ORDER BY moneda');
		$edit->moneda->style ='width:100px;';

		$edit->tipocta = new dropdownField('Cuenta Tipo', 'tipocta');
		$edit->tipocta->style ='width:100px;';
		$edit->tipocta->options(array('K'=>'Caja','C'=>'Corriente','A' =>'Ahorros','P'=>'Plazo Fijo', 'T'=>'Tarjeta', 'Q'=>'Caja Chica' ));

		$edit->proxch = new inputField('Pr&oacute;ximo CH', 'proxch');
		$edit->proxch->rule='trim';
		$edit->proxch->size =12;
		$edit->proxch->maxlength=12;

		$edit->saldo = new inputField('Saldo Actual','saldo');
		$edit->saldo->mode ='autohide';
		$edit->saldo->size = 12;
		$edit->saldo->when=array('show');
		$edit->saldo->css_class='inputnum';
		$edit->saldo->readonly=true;

		$edit->dbporcen = new inputField('Debito %','dbporcen');
		$edit->dbporcen->rule='trim';
		$edit->dbporcen->size =12;
		$edit->dbporcen->maxlength=5;
		$edit->dbporcen->css_class='inputnum';
		$edit->dbporcen->rule = 'callback_chporcent';
		$edit->dbporcen->onchange='gasto()';

		$edit->cuenta = new inputField('Cta. Contable', 'cuenta');
		$edit->cuenta->rule='trim|existecpla';
		$edit->cuenta->size =12;
		$edit->cuenta->append($bcpla);

		$lsprv='<a href="javascript:add_proveed();" title="Agregar un proveedor para este banco">'.image('list_plus.png','Agregar',array('border'=>'0')).'</a>';
		$edit->codprv = new inputField('Proveedor', 'codprv');
		$edit->codprv->rule= 'condi_required|callback_chiscaja|trim';
		$edit->codprv->append($boton);
		$edit->codprv->append($lsprv);
		$edit->codprv->size = 6;

		$edit->depto = new dropdownField('Departamento', 'depto');
		$edit->depto->option('','Seleccionar');
		$edit->depto->options("SELECT depto, descrip FROM dpto ORDER BY descrip");
		$edit->depto->rule='required';
		$edit->depto->style ='width:220px;';

		$edit->sucur = new dropdownField('Sucursal', 'sucur');
		$edit->sucur->option('','Ninguna');
		$edit->sucur->options('SELECT codigo, TRIM(sucursal) FROM sucu ORDER BY sucursal');
		$edit->sucur->style ='width:150px;';

		$mSQL="SELECT codigo, CONCAT_WS('-',TRIM(descrip),TRIM(codigo)) AS descrip FROM mgas ORDER BY descrip";
		$edit->gastoidb = new dropdownField('Gasto I.D.B.','gastoidb');
		$edit->gastoidb->option('','Seleccionar');
		$edit->gastoidb->rule= 'condi_required|callback_chisidb';
		$edit->gastoidb->options($mSQL);
		$edit->gastoidb->style ='width:280px;';

		$edit->gastocom = new dropdownField('Comisi&oacute;n', 'gastocom');
		$edit->gastocom->rule= 'condi_required|callback_chiscaja|trim';
		$edit->gastocom->option('','Seleccionar');
		$edit->gastocom->options($mSQL);
		$edit->gastocom->style ='width:280px;';

		$rif = '';
		$tbanco = $edit->getval('tbanco');
		if ( $tbanco && $tbanco!='CAJ' && $tbanco!='FON' )
			$rif = $this->datasis->dameval('SELECT rif FROM tban WHERE cod_banc="'.$edit->getval('tbanco').'"');

		$edit->rif = new inputField('RIF del Banco', 'rif');
		$edit->rif->rule='trim';
		$edit->rif->size =12;
		$edit->rif->maxlength=12;
		$edit->rif->updateValue = $rif;
		$edit->rif->showValue = $rif;

		$mSQL="SELECT codbanc, CONCAT_WS(' ',TRIM(codbanc),TRIM(numcuent),TRIM(banco)) AS descrip FROM banc WHERE tbanco<>'FON' ORDER BY codbanc";
		$edit->ctasoc = new dropdownField('Cuenta asociada', 'ctasoc');
		$edit->ctasoc->rule= '';
		$edit->ctasoc->option('','Seleccionar');
		$edit->ctasoc->options($mSQL);
		$edit->ctasoc->style ='width:200px;';

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' => 'A',
				'mensaje'=> 'Registro guardado',
				'pk'     => $edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form']  =&  $edit;
			$data['content']  =  $this->load->view('view_banc', $conten, false);
		}
	}

	function traerif($tbanco){
		$rif = $this->datasis->dameval("SELECT rif FROM tban WHERE cod_banc='".$tbanco."'");
		echo $rif;
	}


	function _pre_delete($do){
		$codigo  =$do->get('codbanc');
		$dbcodigo=$this->db->escape($codigo);

		$check=$this->datasis->dameval("SELECT COUNT(*) AS cana FROM bmov WHERE codbanc=${dbcodigo}");
		if($check > 0){
			$do->error_message_ar['pre_del']='El banco presenta movimientos no puede ser eliminado';
			return false;
		}

		return true;
	}

	function _pre_insert($do){

		$this->bacsprv($do);
		$do->error_message_ar['pre_ins']='';
		return true;
	}

	function _pre_update($do){
		$rif    = trim($this->input->post('rif'));
		$tbanco = $do->get('tbanco');
		if ( $rif=='' && $tbanco<>'CAJ' && $tbanco<>'FON'){
			$do->error_message_ar['pre_upd']="Favor coloque el RIF del banco";
			return false;
		} else {
			$this->bacsprv($do);
			$do->error_message_ar['pre_upd']="";
			return true;
		}
	}

	//******************************************************************
	// Crea el proveedor
	//
	function bacsprv($do){
		$rif    = $this->input->post('rif');
		$tbanco = $do->get('tbanco');
		$do->rm_get('rif');
		// Si es banco revisa si existe el Proveedor
		if ( $tbanco <> 'CAJ' ){
			$tbrif = $this->datasis->dameval('SELECT rif FROM tban WHERE cod_banc="'.$tbanco.'"');
			if ( empty($tbrif) ){
				$this->db->query("UPDATE tban SET rif='".$rif."' WHERE cod_banc='".$tbanco."'");
			}
			// Busca el Proveedor
			$codprv = $this->datasis->dameval("SELECT proveed FROM sprv WHERE rif='".$rif."' LIMIT 1");
			if (empty($codprv)) {
				//Crea el Proveedor
				$grpr = $this->datasis->dameval('SELECT grupo FROM grpr WHERE gr_desc LIKE "BANCO%"');
				$codprv = '3B'.$tbanco;
				$hay = $this->datasis->dameval("SELECT count(*) FROM sprv WHERE proveed='".$codprv."'");
				$letras = array('C'.'D','E','F','E','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
				$i = 0;
				while ( $hay > 0 ){
					$codprv = '3'.$letras[$i].$tbanco;
					$hay = $this->datasis->dameval("SELECT count(*) FROM sprv WHERE proveed='".$codprv."'");
					$i++;
				}
				$data = array();
				$data['proveed']  = $codprv;
				$data['rif']      = $rif;

				$data['nombre']   = $do->get('banco');
				$data['nomfis']   = $do->get('banco');
				$data['contacto'] = $do->get('nombre');
				$data['direc1']   = $do->get('dire1');
				$data['direc2']   = $do->get('dire2');
				$data['telefono'] = $do->get('telefono');

				$data['grupo']    = $grpr;
				$data['prefpago'] = 'T';
				$data['reteiva']  = 0.00;
				$data['tipo']     = '5';
				$data['tiva']     = 'N';

				$this->db->insert('sprv',$data);
			}
			$do->set('codprv',$codprv);
		} else {
			$do->set('rif', '');

		}
		return true;
	}




	function _post_insert($do){
		$codigo=$do->get('codbanc');
		$nombre=$do->get('banco');
		logusu('banc',"BANCO ${codigo} NOMBRE  ${nombre} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('codbanc');
		$nombre=$do->get('banco');
		logusu('banc',"BANCO ${codigo} NOMBRE  ${nombre}  MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('codbanc');
		$nombre=$do->get('banco');
		logusu('banc',"BANCO ${codigo} NOMBRE  ${nombre}  ELIMINADO ");
	}

	function chexiste($codigo){
		//$codigo=$this->input->post('codbanc');
		$dbcodigo=$this->db->escape($codigo);
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM banc WHERE codbanc=${dbcodigo}");
		if ($check > 0){
			$banco=$this->datasis->dameval("SELECT banco FROM grup WHERE codbanc=${dbcodigo}");
			$this->validation->set_message('chexiste',"El codigo ${codigo} ya existe para el banco ${banco}");
			return false;
		}else {
			return true;
		}
	}

	function chiscaja($proveed){
		$tbanco=$this->input->post('tbanco');
		if ($tbanco!='CAJ' && $tbanco!='FON' && strlen(trim($proveed))==0){
			$this->validation->set_message('chiscaja',"El campo '%s' es obligatorio cuando el registro no es una caja");
			return false;
		}else {
			return true;
		}
	}

	function chisidb($gastoidb){
		$dbporcen=$this->input->post('dbporcen');
		if ($dbporcen>0 && strlen(trim($gastoidb))==0){
			$this->validation->set_message('chisidb',"El campo '%s' es obligatorio cuando existe porcentaje de d&eacute;bito");
			return false;
		}else {
			return true;
		}
	}

	function banco_delete($llave) {
		return false;
	}

	function ubanc(){
		$consul=$this->datasis->dameval("SELECT codbanc FROM banc ORDER BY codbanc DESC");
		echo $consul;
	}

	function consulta(){
		$this->rapyd->load("datagrid");
		$fields = $this->db->field_data('banc');
		$url_pk = $this->uri->segment_array();
		$coun=0; $pk=array();
		foreach ($fields as $field){
			if($field->primary_key==1){
				$coun++;
				$pk[]=$field->name;
			}
		}
		$values=array_slice($url_pk,-$coun);
		$claves=array_combine (array_reverse($pk) ,$values );

		$grid = new DataGrid('Movimientos ultimos 30 dias');
		$grid->db->select( array('a.fecha', 'a.tipo_op','a.numero','CONCAT(a.concepto," ",a.concep2) concepto', 'a.monto') );
		$grid->db->from('bmov a');
		$grid->db->where('a.codbanc', $claves['codbanc'] );
		$grid->db->where('a.fecha > SUBDATE(curdate(),90)' );
		$grid->db->orderby('fecha DESC');
		//$grid->db->limit();

		$grid->column("Fecha"   ,"fecha" );
		$grid->column("Tipo"   ,"tipo_op" );
		$grid->column("Numero" ,"numero");
		$grid->column("Concepto"   ,"concepto" );
		//$grid->column("Nombre"  ,"nombre");
		$grid->column("Monto"   ,"<nformat><#monto#></nformat>",'align="RIGHT"');
		$grid->build();
		//echo $grid->db->last_query();
		$descrip = $this->datasis->dameval("SELECT CONCAT(banco, '', cuenta) cuenta FROM banc WHERE codbanc='".$claves['codbanc']."'");
		$data['content'] = "
		<table width='100%'>
			<tr>
				<td valign='top'>
					<div style='border: 2px outset #EFEFEF;background: #EFEFFF '>".
					$grid->output."
					</div>".
				"</td>
			</tr>
		</table>";
		$data["head"]     = script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$data['title']    = '<h1>Consulta de Banco</h1>';
		$data["subtitle"] = "<div align='center' style='border: 2px outset #EFEFEF;background: #EFEFEF '><a href='javascript:javascript:history.go(-1)'>(".$claves['codbanc'].") ".$descrip."</a></div>";
		$this->load->view('view_ventanas', $data);

	}

	//****************************
	//
	//Resumen rapido
	//
	function resumen( $id, $ano = 0 ) {

		$row = $this->datasis->damereg("SELECT codbanc, saldo, activo, YEAR(curdate()) anno FROM banc WHERE id=$id");
		$codbanc  = $row['codbanc'];
		$saldo    = $row['saldo'];
		$activo   = $row['activo'];

		if ( $ano == 0)
			$ano = $row['anno'];

		$mSQL = "SELECT saldo Inicial, saldo01 Ene, saldo02 Feb, saldo03 Mar, saldo04 Abr, saldo05 May, saldo06 Jun, saldo07 jul, saldo08 Ago, saldo09 Sep, saldo10 Oct, saldo11 Nov, saldo12 Dic  FROM bsal WHERE ano = $ano AND codbanc=".$this->db->escape($codbanc);

		$query = $this->db->query($mSQL);
		$data = $query->row();
		$salida = '';
		$salida  .= '<table width="90%" border="1" align="center">';
		if ( $activo == 'S')
			$salida  .= '<tr><th colspan="2" style="background:#A6FAA6;">Movimientos para el '.$ano.'</th></tr>';
		else
			$salida  .= '<tr><th colspan="2" style="background:#F97070;">Movimientos por Mes</th></tr>';
		$total = 0;
		foreach( $data AS $mes=>$saldo ){
			if ( $saldo < 0 )
				$salida .= "<tr><td>".$mes."</td><td align='right' style='color:red;'>".nformat($saldo)."</td></tr>\n";
			else
				$salida .= "<tr><td>".$mes."</td><td align='right'>".nformat($saldo)."</td></tr>\n";

			$total += $saldo;
		}
		$salida .= "<tr><td>Final</td><td align='right'>".nformat($total)."</td></tr>\n";
		$salida .= "</table>\n";
		$anterior = $ano-1;
		$proximo  = $ano+1;
		$salida  .= '<table width="90%" border="0" align="center" style="border:1px solid; background:#E4E4E4;"><tr>';
		$salida .= '<td align="center"><a href="#" onclick="fresumen('.$id.','.$anterior.')"> '.img('images/arrow_left.png').'</a></td>';
		$salida .= '<td align="center"><a href="#" onclick="crecalban()" >RECALCULAR</a></td>';
		$salida .= '<td align="center"><a href="#" onclick="fresumen('.$id.','.$proximo.')">'.img('images/arrow_right.png').'</a>';
		$salida .= "</td></tr></table>\n";

		echo $salida;
	}


	//*****************************
	//    RECALCULAR BSAL
	//
	function recalban( $id, $ano = 0, $saldo = 0 ) {

		if ( $ano > date('Y') )
			$ano = date('Y');
		//
		if ( $ano < date('Y') - 20 )
			$ano = date('Y');

		$saldo = $saldo/100;

		$codbanc = $this->datasis->dameval("SELECT codbanc FROM banc WHERE id=$id");

		$mSQL = "INSERT IGNORE INTO bsal SET
		codbanc=".$this->db->escape($codbanc).",
		ano=$ano,  saldo=0,   saldo01=0, saldo02=0, saldo03=0, saldo04=0,saldo05=0,
		saldo06=0, saldo07=0, saldo08=0, saldo09=0, saldo10=0, saldo11=0, saldo12=0 ";
		$this->db->query($mSQL);

		//Coloca Saldo Inicial
		if ( $saldo <> 0 ){
			$mSQL = 'UPDATE bsal SET saldo='.$saldo.' WHERE ano='.$ano.' AND codbanc='.$this->db->escape($codbanc);
			$this->db->query($mSQL);
		}

		$mSQL = "SELECT
		SUM( monto*(month(fecha)= 1)*(tipo_op NOT IN ('CH','ND')) - monto*(month(fecha)=1)*(tipo_op  IN ('CH','ND'))) saldo01,
		SUM( monto*(month(fecha)= 2)*(tipo_op NOT IN ('CH','ND')) - monto*(month(fecha)=2)*(tipo_op  IN ('CH','ND'))) saldo02,
		SUM( monto*(month(fecha)= 3)*(tipo_op NOT IN ('CH','ND')) - monto*(month(fecha)=3)*(tipo_op  IN ('CH','ND'))) saldo03,
		SUM( monto*(month(fecha)= 4)*(tipo_op NOT IN ('CH','ND')) - monto*(month(fecha)=4)*(tipo_op  IN ('CH','ND'))) saldo04,
		SUM( monto*(month(fecha)= 5)*(tipo_op NOT IN ('CH','ND')) - monto*(month(fecha)=5)*(tipo_op  IN ('CH','ND'))) saldo05,
		SUM( monto*(month(fecha)= 6)*(tipo_op NOT IN ('CH','ND')) - monto*(month(fecha)=6)*(tipo_op  IN ('CH','ND'))) saldo06,
		SUM( monto*(month(fecha)= 7)*(tipo_op NOT IN ('CH','ND')) - monto*(month(fecha)=7)*(tipo_op  IN ('CH','ND'))) saldo07,
		SUM( monto*(month(fecha)= 8)*(tipo_op NOT IN ('CH','ND')) - monto*(month(fecha)=8)*(tipo_op  IN ('CH','ND'))) saldo08,
		SUM( monto*(month(fecha)= 9)*(tipo_op NOT IN ('CH','ND')) - monto*(month(fecha)=9)*(tipo_op  IN ('CH','ND'))) saldo09,
		SUM( monto*(month(fecha)=10)*(tipo_op NOT IN ('CH','ND')) - monto*(month(fecha)=10)*(tipo_op IN ('CH','ND'))) saldo10,
		SUM( monto*(month(fecha)=11)*(tipo_op NOT IN ('CH','ND')) - monto*(month(fecha)=11)*(tipo_op IN ('CH','ND'))) saldo11,
		SUM( monto*(month(fecha)=12)*(tipo_op NOT IN ('CH','ND')) - monto*(month(fecha)=12)*(tipo_op IN ('CH','ND'))) saldo12
		FROM bmov WHERE year(fecha)=$ano AND codbanc=".$this->db->escape($codbanc)."
		GROUP BY codbanc, YEAR(fecha)";

		//memowrite($mSQL);

		$query = $this->db->query($mSQL);
		$data  = $query->row();

		if ( count($data) == 1 ){
			$this->db->where('codbanc', $codbanc);
			$this->db->where('ano',     $ano);
			$this->db->update('bsal',   $data);
		} else {
			//Coloca todo en 0
			$data = array(
			"saldo01"=>0, "saldo02"=>0, "saldo03"=>0, "saldo04"=>0,
			"saldo05"=>0, "saldo06"=>0, "saldo07"=>0, "saldo08"=>0,
			"saldo09"=>0, "saldo10"=>0, "saldo11"=>0, "saldo12"=>0 );
			$this->db->where('codbanc', $codbanc);
			$this->db->where('ano',     $ano);
			$this->db->update('bsal',   $data);
		}

		$anoactual = date('Y');
		//Actualiza Banc
		$mSQL = 'SELECT saldo+saldo01+saldo02+saldo03+saldo04+saldo05+saldo06+
			saldo07+saldo08+saldo09+saldo10+saldo11+saldo12 FROM bsal
			WHERE codbanc='.$this->db->escape($codbanc).' AND ano='.$anoactual;
		$saldo = $this->datasis->dameval($mSQL)+0;

		$mSQL = 'UPDATE banc SET saldo='.$saldo.' WHERE codbanc='.$this->db->escape($codbanc);
		$this->db->query($mSQL);

	}

	function instalar(){
		$campos=$this->db->list_fields('banc');

		if(!in_array('id',$campos)) {
			$this->db->simple_query('ALTER TABLE banc DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE banc ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE banc ADD UNIQUE INDEX codbanc (codbanc)');
		}

		if(!in_array('rif',$campos)) {
			$mSQL="ALTER TABLE `banc` ADD COLUMN `rif` VARCHAR(15) NULL DEFAULT NULL COMMENT 'RIF del Banco' AFTER tipocta;";
			$this->db->query($mSQL);
		}

		if (!$this->db->field_exists('rif','tban')) {
			$mSQL="ALTER TABLE `tban` ADD COLUMN `rif` VARCHAR(15) NULL DEFAULT NULL COMMENT 'RIF del Banco';";
			$this->db->query($mSQL);
		}

		if (!$this->db->field_exists('activo','tban')) {
			$mSQL="ALTER TABLE `tban` ADD COLUMN `activo` CHAR(1) NULL DEFAULT 'S' COMMENT 'Activar/Desactivar';";
			$this->db->query($mSQL);
		}

		if (!$this->db->field_exists('ctasoc','banc')) {
			$mSQL="ALTER TABLE banc ADD COLUMN ctasoc VARCHAR(2) NULL DEFAULT NULL COMMENT 'Cuenta Asociada' AFTER rif;";
			$this->db->query($mSQL);
		}

	}

}
