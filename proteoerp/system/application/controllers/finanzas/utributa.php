<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Utributa extends Controller {
	var $mModulo = 'UTRIBUTA';
	var $titp    = 'Unidades Tributarias';
	var $tits    = 'Unidades Tributarias';
	var $url     = 'finanzas/utributa/';

	function Utributa(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'UTRIBUTA', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->creaintramenu( $opcion = array('modulo'=>'52A','titulo'=>'Unidad Tributaria','mensaje'=>'Unidad Tributaria','panel'=>'OBLIGACIONES','ejecutar'=>'finanzas/utributa','target'=>'popu','visible'=>'S','pertenece'=>'5','ancho'=>530,'alto'=>400) );   // Crea opcion en el menu
		$this->datasis->modintramenu( 530, 400, substr($this->url,0,-1) );
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

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
		array('id'=>'fedita',  'title'=>'Agregar/Editar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);


		//$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['WestSize']    = 0;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('UTRIBUTA', 'JQ');
		$param['otros']       = $this->datasis->otros('UTRIBUTA', 'JQ');
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
		function utributaadd() {
			$.post("'.site_url('finanzas/utributa/dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function utributaedit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url('finanzas/utributa/dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
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
						if ( r.length == 0 ) {
							apprise("Registro Guardado");
							$( "#fedita" ).dialog( "close" );
							grid.trigger("reloadGrid");
							'.$this->datasis->jwinopen(site_url('formatos/ver/UTRIBUTA').'/\'+res.id+\'/id\'').';
							return true;
						} else {
							$("#fedita").html(r);
						}
					}
			})},
			"Cancelar": function() { $( this ).dialog( "close" ); }
			},
			close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
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
		$editar = "true";

		$grid  = new $this->jqdatagrid;

		$grid->addField('ano');
		$grid->label('Ano');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'width'         => 70,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:6, maxlength: 4, dataInit: function (elem) { $(elem).numeric(); }  }',
			//'formatter'     => "'number'",
			//'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));


		$grid->addField('gaceta');
		$grid->label('Gaceta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 50 }',
		));


		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 90,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('valor');
		$grid->label('Valor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 110,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'hidden'        => 'true',
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

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 320, height:200, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 320, height:200, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('UTRIBUTA','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('UTRIBUTA','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('UTRIBUTA','BORR_REG%'));
		$grid->setSearch( false ); //$this->datasis->sidapuede('UTRIBUTA','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		//$grid->setBarOptions("\t\taddfunc: utributaadd,\n\t\teditfunc: utributaedit");

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
		$mWHERE = $grid->geneTopWhere('utributa');

		$response   = $grid->getData('utributa', array(array()), array(), false, $mWHERE, 'id', 'desc' );
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
		$mcodp  = "ano";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$ano  = $data['ano'];
				$this->db->insert('utributa', $data);
				echo "Registro Agregado";
				logusu('UTRIBUTA',"Unidad Tributaria $ano Incluida");
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			//unset($data[$mcodp]);
			$this->db->where("id", $id);
			$this->db->update('utributa', $data);
			logusu('UTRIBUTA',"Grupo de Cliente  ".$nuevo." MODIFICADO");
			echo "$mcodp Modificado";

		} elseif($oper == 'del') {
			$this->db->simple_query("DELETE FROM utributa WHERE id=$id ");
			logusu('UTRIBUTA',"Unidad Tributaria $id ELIMINADA");
			echo "Registro Eliminado";
		};
	}

	function instalar(){
		if(!$this->db->table_exists('utributa')){
			$mSQL="CREATE TABLE `utributa` (
				`ano` INT(6) NULL DEFAULT NULL,
				`gaceta` VARCHAR(50) NULL DEFAULT NULL,
				`fecha` DATE NULL DEFAULT NULL,
				`valor` DECIMAL(12,2) NULL DEFAULT NULL,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `gaceta` (`gaceta`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		$this->db->simple_query("INSERT IGNORE INTO `utributa` (`ano`, `gaceta`, `fecha`, `valor`) VALUES (2005, '38.116', '2005-01-27', 29400.00)");
		$this->db->simple_query("INSERT IGNORE INTO `utributa` (`ano`, `gaceta`, `fecha`, `valor`) VALUES (2006, '38.350', '2006-01-04', 33600.00)");
		$this->db->simple_query("INSERT IGNORE INTO `utributa` (`ano`, `gaceta`, `fecha`, `valor`) VALUES (2007, '38.603', '2007-01-12', 37632.00)");
		$this->db->simple_query("INSERT IGNORE INTO `utributa` (`ano`, `gaceta`, `fecha`, `valor`) VALUES (2008, '38.855', '2008-01-22', 46.00)");
		$this->db->simple_query("INSERT IGNORE INTO `utributa` (`ano`, `gaceta`, `fecha`, `valor`) VALUES (2009, '39.127', '2009-02-26', 55.00)");
		$this->db->simple_query("INSERT IGNORE INTO `utributa` (`ano`, `gaceta`, `fecha`, `valor`) VALUES (2010, '39.361', '2010-02-04', 65.00)");
		$this->db->simple_query("INSERT IGNORE INTO `utributa` (`ano`, `gaceta`, `fecha`, `valor`) VALUES (2011, '39.623', '2011-02-24', 76.00)");
		$this->db->simple_query("INSERT IGNORE INTO `utributa` (`ano`, `gaceta`, `fecha`, `valor`) VALUES (2012, '39.866', '2012-02-16', 90.00)");
		$this->db->simple_query("INSERT IGNORE INTO `utributa` (`ano`, `gaceta`, `fecha`, `valor`) VALUES (2013, '40.106', '2014-02-06', 107.00)");
		$this->db->simple_query("INSERT IGNORE INTO `utributa` (`ano`, `gaceta`, `fecha`, `valor`) VALUES (2014, '40.359', '2014-02-19', 127.00)");

	}
}
