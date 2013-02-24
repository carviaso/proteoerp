<?php 
//require_once(BASEPATH.'application/controllers/validaciones.php');
//include_once(BASEPATH.'application/controllers/inventario/line.php');
class Grup extends Controller {
	var $mModulo = 'GRUP';
	var $titp    = 'Grupos de Inventario';
	var $tits    = 'Grupos de Inventario';
	var $url     = 'inventario/grup/';

	function Grup(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'GRUP', $ventana=0 );
	}

	function index(){
		/*if ( !$this->datasis->iscampo('grup','id') ) {
			$this->db->simple_query('ALTER TABLE grup DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE grup ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE grup ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};*/
		
		$campos = $this->db->list_fields('grup');
		if(!in_array('precio'  ,$campos)){
			$this->db->simple_query("ALTER TABLE grup ADD COLUMN precio CHAR(1) NULL DEFAULT '0'");
		}
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

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
		array("id"=>"fgrupo", "title"=>"Agregar/Editar Registro"),
		array("id"=>"fborra", "title"=>"Eliminar registro")
		);
		
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '
		function fstatus(el, val, opts){
			var meco=\'<div><img src="'.base_url().'images/S.gif" width="20" height="18" border="0" /></div>\';
			if ( el == "B" ){
				meco=\'<div><img src="'.base_url().'images/N.gif" width="20" height="20" border="0" /></div>\';
			}
			return meco;
		}';

		$param['WestPanel']   = $WestPanel;
		$param['funciones']   = $funciones;

		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('GRUP', 'JQ');
		$param['otros']       = $this->datasis->otros('GRUP', 'JQ');
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
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function grupadd() {
			$.post("'.site_url('inventario/grup/dataedit/create').'",
			function(data){
				$("#fgrupo").html(data);
				$("#fgrupo").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function grupedit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url('inventario/grup/dataedit/modify').'/"+id, function(data){
					$("#fedita").html("");
					$("#fgrupo").html(data);
					$("#fgrupo").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		};';

		//Borrar 
		$bodyscript .= '
		function grupdel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				if(confirm(" Seguro desea anular el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						$("#fedita").html("");
						$("#fborra").html(data);
						$("#fborra").dialog( "open" );
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		$("#fborra").dialog({
			autoOpen: false, height: 300, width: 300, modal: true,
			buttons: {
				"Aceptar": function() {
					$( this ).dialog( "close" );
					grid.trigger("reloadGrid");
				}
			}
			//close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
		});';


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
		$("#fgrupo").dialog({
			autoOpen: false, height: 550, width: 600, modal: true,
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
							$( "#fgrupo" ).dialog( "close" );
							grid.trigger("reloadGrid");
							return true;
						} else { 
							$("#fgrupo").html(r);
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
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('status');
		$grid->label('Status');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
			'formatter'     => 'fstatus'
		));


		$grid->addField('depto');
		$grid->label('Depto.');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));

		$grid->addField('linea');
		$grid->label('Linea');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));

		$grid->addField('grupo');
		$grid->label('Grupo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('nom_grup');
		$grid->label('nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));

/*
		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));
*/

		$grid->addField('comision');
		$grid->label('Comision');
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
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('cu_cost');
		$grid->label('Cu_cost');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('cu_venta');
		$grid->label('Cu_venta');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('cu_devo');
		$grid->label('Cu_devo');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('margen');
		$grid->label('Margen');
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


		$grid->addField('margenc');
		$grid->label('Margenc');
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

		$grid->addField('precio');
		$grid->label('Precio Minimo');
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

		$grid->setBarOptions("\t\taddfunc: grupadd,\n\t\teditfunc: grupedit");
		//, delfunc: grupdel

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
		$mWHERE = $grid->geneTopWhere('grup');

		$response   = $grid->getData('grup', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Busca la data en el Servidor por json Cuando se llama desde otra clase
	*/
	function getdataE()
	{
		$id = $this->uri->segment(4);
		if ($id == false ){
			$id = $this->datasis->dameval("SELECT id FROM line ORDER BY depto, linea LIMIT 1");
		}
		$depto = $this->datasis->dameval("SELECT depto FROM line WHERE id=$id");
		$linea = $this->datasis->dameval("SELECT linea FROM line WHERE id=$id");
		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM grup WHERE depto='$depto' AND linea='$linea'";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;

/*
		$grid       = $this->jqdatagrid;
		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('grup');

		$response   = $grid->getData('grup', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
*/
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
		$mcodp  = "grupo";
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
			$codigo = $this->datasis->dameval("SELECT grupo FROM grup WHERE id=$id");
			$check  =  $this->datasis->dameval("SELECT COUNT(*) FROM sinv WHERE grupo='$codigo'");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM grup WHERE id=$id ");
				logusu('GRUP',"Grupo $codigo ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}



/*
class Grup extends validaciones {

	function grup(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id(305,1);
		$esta = $this->datasis->dameval( "SHOW columns FROM grup WHERE Field='status'" );
		if ( empty($esta) ) $this->db->simple_query("ALTER TABLE grup ADD status CHAR(1) DEFAULT='A' ");
		$this->db->simple_query("UPDATE grup SET status='A' WHERE status IS NULL ");
	}

	function index(){
		if ( !$this->datasis->iscampo('grup','id') ) {
			$this->db->simple_query('ALTER TABLE grup DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE grup ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE grup ADD UNIQUE INDEX grupo (grupo)');
		}
		$this->datasis->modulo_id(306,1);
		$this->grupextjs();
		//redirect("inventario/grup/filteredgrid");
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

		$filter->db->select("a.grupo AS grupo, a.nom_grup AS nom_grup, a.comision AS comision,a.margen AS margen,a.margenc AS margenc, b.descrip AS linea,c.descrip AS depto, a.cu_inve AS cu_inve, a.cu_venta AS cu_venta, a.cu_cost AS cu_cost, a.cu_devo AS cu_devo");
		$filter->db->from("grup AS a");
		$filter->db->join("line AS b","a.linea=b.linea");
		$filter->db->join("dpto AS c","b.depto=c.depto");

		$filter->grupo = new inputField("Grupo","grupo");
		$filter->grupo->size=20;
		$filter->grupo->group = 'UNO';

		$filter->nombre = new inputField("Descripci&oacute;n","nom_grup");
		$filter->nombre->size=20;
		$filter->nombre->group = 'UNO';

		$filter->linea = new inputField("L&iacute;nea","b.descrip");
		$filter->linea->size=20;
		$filter->linea->group = 'DOS';

		$filter->depto = new inputField("Departamento","c.descrip");
		$filter->depto->size=20;
		$filter->depto->group = 'DOS';

		$filter->buttons("reset","search");
		$filter->build('dataformfiltro');


		$mtool  = "<table background='#554455'><tr>";
		$mtool .= "<td>&nbsp;</td>";
		$mtool .= "<td>&nbsp;<a href='".base_url()."inventario/grup/dataedit/create'>";
		$mtool .= img(array('src' => 'images/agregar.jpg', 'alt' => 'Agregar Registro', 'title' => 'Agregar Registro','border'=>'0','height'=>'32'));
		$mtool .= "</a>&nbsp;</td>";
		$mtool .= "</tr></table>";

		$uri = anchor('inventario/grup/dataedit/show/<raencode><#grupo#></raencode>','<#grupo#>');
		$uri_2 = anchor('inventario/grup/dataedit/create/<raencode><#grupo#></raencode>','Duplicar');

		$grid = new DataGrid("Lista de Grupos de Inventario");
		$grid->order_by("grupo","asc");
		$grid->per_page = 60;

		$grid->column_sigma("Depto",           		"depto",     "", "width: 40, frozen: true");
		$grid->column_sigma("Linea",                    "linea",     '',      "width: 40, frozen: true");
		$grid->column_sigma("Grupo", 		        "grupo",     '',      'width: 50,  frozen: true, renderer: grupver ' );
		$grid->column_sigma("Descripci&oacute;n",       "nom_grup",  '',      "width: 200, editor: { type: 'text' }" );
		$grid->column_sigma("Comisi&oacute;n",          "comision",  'float', "width: 80,  align: 'right', editor: { type: 'text' }");
		$grid->column_sigma("Mrgn/Venta",             "margen" ,   'float', "width: 80,  align: 'right', editor: { type: 'text' }");
		$grid->column_sigma("Mrgn/Compra",            "margenc" ,  'float', "width: 80,  align: 'right', editor: { type: 'text' }");
		$grid->column_sigma("Cuenta Inventario",        "cu_inve",   '',      "align: 'left'");
		$grid->column_sigma("Cuenta Costo",             "cu_cost",   '',      "align: 'left'");
		$grid->column_sigma("Cuenta Venta",             "cu_venta",  '',      "align: 'left'");
		$grid->column_sigma("Cuenta Devoluci&oacute;n", "cu_devo",   '',      "align: 'left'");

		$sigmaA     = $grid->sigmaDsConfig();
		$dsOption   = $sigmaA["dsOption"];
		$grupver    = "
function grupver(value, record, columnObj, grid, colNo, rowNo){
	var url = '';
	url = '<a href=\"#\" onclick=\"window.open(\'".base_url()."inventario/grup/dataedit/show/'+value+ '\', \'_blank\', \'width=800, height=600, scrollbars=Yes, status=Yes, resizable=Yes, screenx='+((screen.availWidth/2)-400)+',screeny='+((screen.availHeight/2)-300)+'\')\"; heigth=\"600\" >';
	url = url +value+'</a>';
	return url;	
}

";
		$colsOption = $sigmaA["colsOption"];
		$gridOption = "
var gridOption={
	id : 'grid1',
	loadURL : '".base_url()."inventario/grup/controlador',
	width: 550,
	height: 400,
	container : 'grid1_container',
	replaceContainer: true,
	dataset : dsOption ,
	columns : colsOption,
	allowCustomSkin: true,
	skin: 'vista',
	pageSize: ".$grid->per_page.",
	pageSizeList: [30,60,90,120],
	toolbarPosition : 'bottom',
	toolbarContent: 'nav | pagesize | reload print excel pdf filter state',
	afterEdit: guardar,
	//showGridMenu : true,
	clickStartEdit: false,
	remotePaging: true,
	remoteSorting: true,
	remoteFilter: true,
	autoload: true
};

function guardar(value, oldValue, record, col, grid) {
	var murl='';
	murl = '".base_url()."/inventario/grup/grupmodi/'+record['grupo']+'/'+col.id+'/'+encodeURIComponent(value);
	if ( value != oldValue ) {
		$.ajax({
			url: murl,
			context: document.body,
			//success: function(m){ alert('Guardado '+m);}
		});

	}
};

var mygrid=new Sigma.Grid(gridOption);
Sigma.Util.onLoad( Sigma.Grid.render(mygrid) );
";		
		$SigmaCont = "<center><div id=\"grid1_container\" style=\"width:550px;height:400px;\"></div></center>";
		$grid->add("inventario/grup/dataedit/create");
		$grid->build('datagridSG');
		//echo $grid->db->last_query();


		$data['style']  = style("redmond/jquery-ui.css");
		$data['style'] .= style('gt_grid.css');
		$data['style'] .= style('skin/vista/skinstyle.css');

		$data["script"]  = script("jquery.js");
		$data['script'] .= script("gt_msg_es.js");
		$data['script'] .= script("gt_grid_all.js");
		$data['script'] .= "<script type=\"text/javascript\" >\n";
		$data['script'] .= $dsOption.$grupver."\n";
		$data['script'] .= $colsOption."\n";
		$data['script'] .= $gridOption;

		//$data['script'] .= "$(function() { $(\"p\").text(\"Meco\") } );";

		$data['script'] .= "\n</script>";


		$data['content'] = $mtool.$SigmaCont;  //$grid->output;
		
		//$data['filtro']  = ''; //$filter->output;
		$data['title']   = "<h1>Grupos de Inventario</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	// sigma grid
	function controlador(){
		//header('Content-type:text/javascript;charset=UTF-8');
		if (isset($_POST["_gt_json"]) ) {
			$json=json_decode(stripslashes($_POST["_gt_json"]));
			if($json->{'action'} == 'load') {
				$pageNo   = $json->{'pageInfo'}->{'pageNum'};
				$pageSize = $json->{'pageInfo'}->{'pageSize'};
				$filter = '';

				if(isset($json->{'sortInfo'}[0]->{'columnId'})){
					$sortField = $json->{'sortInfo'}[0]->{'columnId'};
				} else {
					$sortField = "grupo";
				}    
	 
				if(isset($json->{'sortInfo'}[0]->{'sortOrder'})){
					$sortOrder = $json->{'sortInfo'}[0]->{'sortOrder'};
				} else {
					$sortOrder = "ASC";
				}    
	
				for ($i = 0; $i < count($json->{'filterInfo'}); $i++) {
					if($json->{'filterInfo'}[$i]->{'logic'} == "equal"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . "='" . $json->{'filterInfo'}[$i]->{'value'} . "' ";
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "notEqual"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . "!='" . $json->{'filterInfo'}[$i]->{'value'} . "' ";    
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "less"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . "<" . $json->{'filterInfo'}[$i]->{'value'} . " ";
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "lessEqual"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . "<=" . $json->{'filterInfo'}[$i]->{'value'} . " ";    
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "great"){
							$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . ">" . $json->{'filterInfo'}[$i]->{'value'} . " ";
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "greatEqual"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . ">=" . $json->{'filterInfo'}[$i]->{'value'} . " ";        
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "like"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . " LIKE '%" . $json->{'filterInfo'}[$i]->{'value'} . "%' ";        
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "startWith"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . " LIKE '" . $json->{'filterInfo'}[$i]->{'value'} . "%' ";        
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "endWith"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . " LIKE '%" . $json->{'filterInfo'}[$i]->{'value'} . "' ";                
					}
					$filter .= " AND ";
				}


				//to get how many total records.
				$mSQL = "SELECT count(*) FROM grup WHERE $filter grupo IS NOT NULL";
				$totalRec = $this->datasis->dameval($mSQL);
 
 
				//make sure pageNo is inbound
				if($pageNo<1||$pageNo>ceil(($totalRec/$pageSize))){
					$pageNo = 1;
				}
 
				$mSQL = "SELECT grupo, nom_grup, comision, margen, margenc, depto, linea, cu_inve, cu_cost, cu_venta, cu_devo ";
				$mSQL .= "FROM grup WHERE $filter grupo IS NOT NULL ORDER BY ".$sortField." ".$sortOrder." LIMIT ".($pageNo - 1)*$pageSize.", ".$pageSize;
				$query = $this->db->query($mSQL);
				if ($query->num_rows() > 0){
					$retArray = array();
					foreach( $query->result_array() as  $row ) {
						$retArray[] = $row;
					}
					$data = json_encode($retArray);
					$ret = "{data:" . $data .",\n";
					$ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
					$ret .= "recordType : 'object'}";
				} else {
					$ret = '{data : []}';
				}
				echo $ret;

			}else if($json->{'action'} == 'save'){
/ *				$sql = "";
				$params = array();
				$errors = "";
  
				//deal with those deleted
				$deletedRecords = $json->{'deletedRecords'};
				foreach ($deletedRecords as $value){
					$params[] = $value->id;
				}
				$sql = "delete from dbtable where id in (" . join(",", $params) . ")";
				if(mysql_query($sql)==FALSE){
					$errors .= mysql_error();
				}
				//deal with those updated
				$sql = "";
				$updatedRecords = $json->{'updatedRecords'};
				foreach ($updatedRecords as $value){
					$sql = "update `dbtable` set ".
					//fill out fields to be updated here
					"where `id`=".$value->id;
					if(mysql_query($sql)==FALSE){
						$errors .= mysql_error();
					}
				}
				//deal with those inserted
				$sql = "";
				$insertedRecords = $json->{'insertedRecords'};
				foreach ($insertedRecords as $value){
					$sql = "insert into dbtable (//fields to be inserted)";
					if(mysql_query($sql)==FALSE){
						$errors .= mysql_error();
					}
				}
				$ret = "{success : true,exception:''}";
				echo $ret;* /
			}
		} else {
			// no hay _gt_json
			/ *
			$pageNo = 1;
			$sortField = "numero";
			$sortOrder = "DESC";
			$pageSize = 50;//10 rows per page

			//to get how many records totally.
			$sql = "select count(*) as cnt from spre";
			$totalRec = $this->datasis->dameval($sql);

			//make sure pageNo is inbound
			if($pageNo<1||$pageNo>ceil(($totalRec/$pageSize))){
				$pageNo = 1;
			}

			//pageno starts with 1 instead of 0
			$mSQL = "SELECT grupo, nom_grup, comision, margen, margenc, depto, linea, cu_inve, cu_cost, cu_venta, cu_devo";
			$mSQL .=" FROM grup ORDER BY grupo ASC LIMIT ".($pageNo - 1)*$pageSize.", ".$pageSize;

			$query = $this->db->query($mSQL);
	
			if ($query->num_rows() > 0){
				$retArray = array();
				foreach( $query->result_array() as  $row ) {
					$retArray[] = $row;
				}
				$data = json_encode($retArray);
				$ret = "{data:" . $data .",\n";
				$ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
				$ret .= "recordType : 'object'}";
			} else {
				$ret = '{data : []}';
			}
			* /
			echo '{data : []}';
		}
	}

	function grupmodi(){
		$valor = $this->uri->segment($this->uri->total_segments());
		$campo = $this->uri->segment($this->uri->total_segments()-1);
		$grupo = $this->uri->segment($this->uri->total_segments()-2);
		$mSQL = "UPDATE grup SET ".$campo."='".addslashes($valor)."' WHERE grupo='".$grupo."' ";
		$this->db->simple_query($mSQL);
		echo "$valor $campo $grupo";
	}
*/

	function dataedit($status='',$id=''){
		$this->rapyd->load("dataobject","dataedit");

		$qformato=$this->qformato=$this->datasis->formato_cpla();
		$link=site_url('inventario/grup/ultimo');
		$link2=site_url('inventario/common/sugerir_grup');

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
	
			function get_linea(){
				$.ajax({
					type: "POST",
					url: "'.site_url('reportes/sinvlineas').'",
					data: $("#dpto").serialize(),
					success: function(msg){
						$("#td_linea").html(msg);
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

		$do = new DataObject("grup");
		$do->set('tipo', 'I');
		if($status=="create" && !empty($id)){
			$do->load($id);
			$do->set('grupo', '');
		}

		$edit = new DataEdit("",$do);
		//$edit->back_url = site_url("inventario/grup/filteredgrid");
		$edit->script($script, "modify");
		$edit->script($script, "create");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->pre_process('delete','_pre_delete');
		//$edit->pre_process('update','_pre_update');
		//$edit->pre_process('delete','_pre_delete');

		$edit->depto = new dropdownField("Departamento", "dpto");
		$edit->depto->db_name='depto';
		$edit->depto->rule ="required";
		$edit->depto->onchange = "get_linea();";
		$edit->depto->option("","Seleccionar");
		$edit->depto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");

		$edit->linea = new dropdownField("L&iacute;nea","linea");
		$edit->linea->rule ="required";
		if($edit->_status=='modify' or $edit->_status=='show' or $edit->_status=='idle' or $edit->_status=='create'){
			$depto = ($this->input->post('dpto')===FALSE) ? $edit->_dataobject->get("depto") : $this->input->post('dpto');
			$edit->linea->options("SELECT linea, descrip FROM line WHERE depto='$depto' ORDER BY descrip");
		}else{
			$edit->linea->option("","Seleccione un Departamento");
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
		$edit->nom_grup->size = 40;
		$edit->nom_grup->maxlength=40;
		$edit->nom_grup->rule = "trim|strtoupper|required";

		$edit->comision = new inputField("Comisi&oacute;n. %", "comision");
		$edit->comision->size = 18;
		$edit->comision->maxlength=10;
		$edit->comision->css_class='inputnum';
		$edit->comision->rule='trim|callback_chporcent|numeric|callback_positivo';

		$edit->status = new dropdownField("Status", "status");
		$edit->status->db_name=("status");
		$edit->status->option("A","Activo");
		$edit->status->option("B","Bloqueado");
		$edit->status ->style='width:120px;';


		$edit->margen = new inputField("Margen de Venta", "margen");
		$edit->margen->size = 18;
		$edit->margen->maxlength=10;
		$edit->margen->css_class='inputnum';
		$edit->margen->group='Margenes';
		$edit->margen->rule='trim|callback_chporcent|callback_positivo';

		$edit->margenc = new inputField("Margen de Compra", "margenc");
		$edit->margenc->size = 18;
		$edit->margenc->maxlength=10;
		$edit->margenc->css_class='inputnum';
		$edit->margenc->group='Margenes';
		$edit->margenc->rule='trim|callback_chporcent|numeric|callback_positivo';

		$edit->precio = new dropdownField("Precio Minimo ", "precio");
		$edit->precio->option("0","No Aplica");
		//$edit->precio->option("1","Precio 1");
		$edit->precio->option("2","Precio 2");
		$edit->precio->option("3","Precio 3");
		$edit->precio->option("4","Precio 4");
		$edit->precio->style='width:120px;';


		$edit->cu_inve =new inputField("Cuenta Inventario", "cu_inve");
		$edit->cu_inve->size = 18;
		$edit->cu_inve->maxlength=15;
		$edit->cu_inve->rule ="trim|callback_chcuentac";
		$edit->cu_inve->append($bcu_inve);
		$edit->cu_inve->group='Cuentas contables';

		$edit->cu_cost =new inputField("Cuenta Costo", "cu_cost");
		$edit->cu_cost->size = 18;
		$edit->cu_cost->maxlength=15;
		$edit->cu_cost->rule ="trim|callback_chcuentac";
		$edit->cu_cost->append($bcu_cost);
		$edit->cu_cost->group='Cuentas contables';

		$edit->cu_venta  =new inputField("Cuenta Venta", "cu_venta");
		$edit->cu_venta->size =18;
		$edit->cu_venta->maxlength=15;
		$edit->cu_venta->rule ="trim|callback_chcuentac";
		$edit->cu_venta->append($bcu_venta);
		$edit->cu_venta->group='Cuentas contables';

		$edit->cu_devo = new inputField("Cuenta Devoluci&oacute;n","cu_devo");
		$edit->cu_devo->size = 18;
		$edit->cu_devo->maxlength=15;
		$edit->cu_devo->rule ="trim|callback_chcuentac";
		$edit->cu_devo->append($bcu_devo);
		$edit->cu_devo->group='Cuentas contables';

		$edit->status = new dropdownField("Status", "status");
		$edit->status->db_name=("status");
		$edit->status->option("A","Activo");
		$edit->status->option("B","Bloqueado");
		$edit->status ->style='width:120px;';

		//$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$link=site_url('inventario/grup/get_linea');


		$mtool  = "<table background='#554455'><tr>";
		$mtool .= "<td>&nbsp;</td>";
		$mtool .= "<td>&nbsp;<a href='javascript:exento(\"".$edit->grupo->value."\")'>";
		$mtool .= img(array('src' => 'images/casa.png', 'alt' => 'Exonerar Productos', 'title' => 'Exonerar Productos','border'=>'0','height'=>'32'));
		$mtool .= "</a>&nbsp;</td>";
		$mtool .= "</tr></table>";

		$script = '
<script type="text/javascript">
function exento(mgrupo){
	$.prompt("Exonerar Productos del Grupo "+mgrupo, {
		callback: function(v,m,f){
			if ( v == 1 ) {
				$.ajax({
					url: "'.base_url().'inventario/grup/sinvexento/"+mgrupo+"/E",
					global: false,
					async: false,
					success: function(sino)  { $.prompt( "Marcaje exitoso "); }
				});

			} else if ( v == 2 ) {
				$.ajax({
					url: "'.base_url().'inventario/grup/sinvexento/"+mgrupo+"/N",
					global: false,
					async: false,
					success: function(sino)  { $.prompt( "Finalizado el desmarcaje "); }
				});
			};
		 },
		buttons:{ Marcar: 1, Desmarcar: 2, Cancelar: 3 }
	});
};

</script>
';


		$script= '';

		$data['content'] = $edit->output;
		$data['script'] = $script;
		$this->load->view('jqgrid/ventanajq', $data);

/*
		$data['content'] = $mtool.$edit->output;

		$data["script"]  = script("jquery.js");
		$data["script"] .= script("jquery.alerts.js");
		$data["script"] .= script("jquery-impromptu.js");
		$data['script'] .= $script;

		$data['style']	 = style("jquery.alerts.css");
		$data['style']	.= style("impromptu.css");

		
		$data['title']   = "<h1>Grupos de Inventario</h1>";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
*/
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

	function _pre_delete($do) {
		$codigo=$do->get('grupo');
		$check  =  $this->datasis->dameval("SELECT COUNT(*) FROM sinv WHERE grupo='$codigo'");
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='El grupo contiene productos, por ello no puede ser eliminado. Elimine primero todos los productos que pertenezcan a este grupo';
			return False;
		}
		return True;
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

	function instala(){
		$mSQL="ALTER TABLE `grup`  
				ADD COLUMN `margen` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `comision`,
				ADD COLUMN `margenc` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `margen`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `grup` ADD COLUMN `status` CHAR(1) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
	}

	//***************************
	//
	// Marca Productos que se pueden vender sin iva
	//
	function sinvexento() {
		$grupo  = $this->uri->segment($this->uri->total_segments()-1);
		$sino   = $this->uri->segment($this->uri->total_segments());
		$this->db->simple_query("UPDATE sinv SET exento='".$sino."' WHERE grupo='$grupo'");
		memowrite("UPDATE sinv SET exento='".$sino."' WHERE grupo='$grupo'","marcagrup");
		logusu("SINV","Productos marcados por Grupos $grupo ");
		echo "Productos Marcados '$sino'";
	}

/*
	function grid(){
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 50;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : '[{"property":"grupo","direction":"ASC"}]';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;

		$where = $this->datasis->extjsfiltro($filters);
		
		$this->db->_protect_identifiers=false;
		$this->db->select('*');
		$this->db->from('grup');
		if (strlen($where)>1) $this->db->where($where, NULL, FALSE); 
		
		$sort = json_decode($sort, true);
		if ( count($sort) == 0 ) $this->db->order_by( 'grupo', 'asc' );
		
		for ( $i=0; $i<count($sort); $i++ ) {
			$this->db->order_by($sort[$i]['property'],$sort[$i]['direction']);
		}

		$this->db->limit($limit, $start);

		$query = $this->db->get();
		$results = $this->db->count_all('grup');

		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data", results:'. $results.', data:'.json_encode($arr).'}';
	}


	function crear(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos   = $data['data'];
		$grupo = $campos['grupo'];

		if ( !empty($grupo) ) {
			unset($campos['id']);
			// Revisa si existe ya ese contrato
			if ($this->datasis->dameval("SELECT COUNT(*) FROM grup WHERE grupo='$grupo'") == 0)
			{
				$mSQL = $this->db->insert_string("grup", $campos );
				$this->db->simple_query($mSQL);
				logusu('grup',"DEPARTAMENTO $grupo CREADO");
				echo "{ success: true, message: 'grupo Agregada'}";
			} else {
				echo "{ success: false, message: 'Ya existe un grupo con ese Codigo!!'}";
			}
			
		} else {
			echo "{ success: false, message: 'Ya existe un grupo con ese Codigo!!'}";
		}
	}

	function modificar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];

		$grupo = $campos['grupo'];
		unset($campos['grupo']);
		unset($campos['id']);

		$mSQL = $this->db->update_string("grup", $campos,"id='".$data['data']['id']."'" );
		$this->db->simple_query($mSQL);
		logusu('grup',"GRUPOSS DE INVENTARIO $grupo ID ".$data['data']['id']." MODIFICADO");
		echo "{ success: true, message: 'Grupo Modificado -> ".$data['data']['grupo']."'}";
	}

	function eliminar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];

		$grupo = $campos['grupo'];
		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM grup WHERE grupo='$grupo'");
		if ($check > 0){
			echo "{ success: false, message: 'Linea, con movimiento, no puede ser Borrado'}";
		} else {
			$this->db->simple_query("DELETE FROM grup WHERE grupo='$grupo'");
			logusu('grup',"GRUPO $grupo ELIMINADO");
			echo "{ success: true, message: 'Grupo Eliminado'}";
		}
	}


//****************************************************************8
//
//
//
//****************************************************************8
	function grupextjs(){
		$encabeza='GRUPOS DE INVENTARIO';
		$listados= $this->datasis->listados('grup');
		$otros=$this->datasis->otros('grup', 'grup');

		$mSQL = "SELECT depto, CONCAT(depto,' ', descrip) descrip FROM dpto WHERE tipo='I' ORDER BY depto ";
		$depto = $this->datasis->llenacombo($mSQL);


		$mSQL = "SELECT depto, CONCAT(depto,' ', descrip) descrip FROM dpto WHERE tipo='I' ORDER BY depto ";
		$depto = $this->datasis->llenacombo($mSQL);

		$urlajax = 'inventario/grup/';
		$variables = "
		var mdepto   = ''
		var mlinea   = ''
		var mcuentaV = ''
		var mcuentaI = ''
		var mcuentaC = ''
		var mcuentaD = ''
		";
		$funciones = "
function ftipo(val){
	if ( val == 'I'){
		return 'Inventario';
	} else if ( val == 'G'){
		return  'Gasto';
	}
}

function fstatus(val){
	if ( val == 'A'){
		return 'Activo';
	} else {
		return  'Bloqueado';
	}
}
";

		$valida = "
		{ type: 'length', field: 'grupo',   min: 1 },
		{ type: 'length', field: 'nom_grup', min: 1 }
		";
		
		$columnas = "
			{ header: 'Grupo',    width: 60, sortable: true, dataIndex: 'grupo',    field: { type: 'textfield' },  filter: { type: 'string' }},
			{ header: 'Nombre',   width:200, sortable: true, dataIndex: 'nom_grup', field: { type: 'textfield' },  filter: { type: 'string' }},
			{ header: 'Tipo',     width: 60, sortable: true, dataIndex: 'tipo',     field: { type: 'textfield' },  filter: { type: 'string' }, renderer: ftipo },

			{ header: 'Status',   width: 60, sortable: true, dataIndex: 'status',   field: { type: 'textfield' },  filter: { type: 'string' }, renderer: fstatus },
			{ header: 'Comision', width: 60, sortable: true, dataIndex: 'comision', field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'Margen',   width: 60, sortable: true, dataIndex: 'margen',   field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'Margenc',  width: 60, sortable: true, dataIndex: 'margenc',  field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},

			{ header: 'Linea',    width: 60, sortable: true, dataIndex: 'linea',    field: { type: 'textfield' },  filter: { type: 'string' }},
			{ header: 'Depto',    width: 60, sortable: true, dataIndex: 'depto',    field: { type: 'textfield' },  filter: { type: 'string' }},

			{ header: 'Cta.Inventario', width:100, sortable: true, dataIndex: 'cu_inve',  field: { type: 'textfield' },  filter: { type: 'string' }},
			{ header: 'Cta.Costo',      width:100, sortable: true, dataIndex: 'cu_cost',  field: { type: 'textfield' },  filter: { type: 'string' }},
			{ header: 'Cta.Venta',      width:100, sortable: true, dataIndex: 'cu_venta', field: { type: 'textfield' },  filter: { type: 'string' }},
			{ header: 'Cta.Devol.',     width:100, sortable: true, dataIndex: 'cu_devo',  field: { type: 'textfield' },  filter: { type: 'string' }},
	      ";

		$campos = "'id','grupo','nom_grup','tipo','comision','linea','depto','cu_inve','cu_cost','cu_venta','cu_devo','margen','margenc','status'";
		
		$camposforma = "
							{
							layout: 'column',
							frame: false,
							border: false,
							labelAlign: 'right',
							defaults: { xtype:'fieldset', labelWidth:80 },
							style:'padding:4px',
							items: [
									{ fieldLabel: 'Grupo',   name: 'grupo',    width:140, xtype: 'textfield', id: 'grupo' },
									{ fieldLabel: 'Tipo',    name: 'tipo',     width:260, xtype: 'combo', store: [['I','Inventario'],['G','Gastos']], labelWidth:160  },
									{ fieldLabel: 'Nombre',  name: 'nom_grup', width:400, xtype: 'textfield' },

									{ xtype: 'combo', fieldLabel: 'Departamento',  name: 'depto',  allowBlank: false, width: 400, store: [".$depto."],
										listeners:{select:{fn:function(combo, value) {
											var modelCmp = Ext.getCmp('linea');
											mdepto  = combo.getValue();
											mlinea  = '';
											modelCmp.setValue('');
											lineStore.proxy.extraParams.depto = mdepto ;
											lineStore.load({ params: { 'depto': mdepto, linea: '', 'origen': 'depto' } });
											var modelCmp = Ext.getCmp('linea');
											//modelCmp.store.reload({params: { depto: combo.getValue() }});
										}}}
									},
									{
										xtype: 'combo',
										fieldLabel: 'Linea',
										name: 'linea',
										id:   'linea',
										mode: 'remote',
										hideTrigger: false,
										typeAhead: true,
										forceSelection: true,										valueField: 'item',
										displayField: 'valor',
										store: lineStore,
										width: 400,
									},

									{ fieldLabel: 'Status',          name: 'status',   width:160, xtype: 'combo', store: [['A','Activo'],['B','Bloqueado']], labelWidth:80  },
									{ fieldLabel: 'Margen Ventas',   name: 'margen',   width:240, labelWidth:170, xtype: 'numberfield', hideTrigger: true, fieldStyle: 'text-align: right', renderer : Ext.util.Format.numberRenderer('0,000.00')},
									{ fieldLabel: 'Comision',        name: 'comision', width:140, labelWidth: 80, xtype: 'numberfield', hideTrigger: true, fieldStyle: 'text-align: right', renderer : Ext.util.Format.numberRenderer('0,000.00')},
									{ fieldLabel: 'Margen Compras',  name: 'margenc',  width:260, labelWidth:190, xtype: 'numberfield', hideTrigger: true, fieldStyle: 'text-align: right', renderer : Ext.util.Format.numberRenderer('0,000.00')},


								]
							},{
								frame: false,
								border: false,
								labelAlign: 'right',
								defaults: {xtype:'fieldset'  },
								style:'padding:4px',
								items: [
									{
										xtype: 'combo',
										fieldLabel: 'Cuenta Ventas ',
										labelWidth:100,
										name: 'cu_venta',
										id:   'cuenta1',
										mode: 'remote',
										hideTrigger: true,
										typeAhead: true,
										forceSelection: true,										valueField: 'item',
										displayField: 'valor',
										store: cplaStoreV,
										width: 400
									},
									{
										xtype: 'combo',
										fieldLabel: 'Cuenta Inventario',
										labelWidth:100,
										name: 'cu_inve',
										id:   'cuenta2',
										mode: 'remote',
										hideTrigger: true,
										typeAhead: true,
										forceSelection: true,										valueField: 'item',
										displayField: 'valor',
										store: cplaStoreI,
										width: 400
									},
									{
										xtype: 'combo',
										fieldLabel: 'Cuenta de Costo',
										labelWidth:100,
										name: 'cu_cost',
										id:   'cuenta3',
										mode: 'remote',
										hideTrigger: true,
										typeAhead: true,
										forceSelection: true,										valueField: 'item',
										displayField: 'valor',
										store: cplaStoreC,
										width: 400
									},
									{
										xtype: 'combo',
										fieldLabel: 'Cta. Devolucion',
										labelWidth:100,
										name: 'cu_devo',
										id:   'cuenta4',
										mode: 'remote',
										hideTrigger: true,
										typeAhead: true,
										forceSelection: true,										valueField: 'item',
										displayField: 'valor',
										store: cplaStoreD,
										width: 400
									}
								]
							}
		";

		$titulow = 'Grupos de Inventario';

		$dockedItems = "
				{ iconCls: 'icon-reset', itemId: 'close', text: 'Cerrar',   scope: this, handler: this.onClose },
				{ iconCls: 'icon-save',  itemId: 'save',  text: 'Guardar',  disabled: false, scope: this, handler: this.onSave }
		";

		$winwidget = "
				closable: false,
				closeAction: 'destroy',
				width: 450,
				height: 400,
				resizable: false,
				modal: true,
				items: [writeForm],
				listeners: {
					beforeshow: function() {
						var form = this.down('writerform').getForm();
						this.activeRecord = registro;
						
						if (registro) {
							mcuentaV  = registro.data.cu_venta;
							cplaStoreV.proxy.extraParams.cu_venta   = mcuentaV ;
							cplaStoreV.load({ params: { 'cuenta': registro.data.cu_venta, 'origen': 'beforeform' } });
							
							mcuentaI  = registro.data.cu_inve;
							cplaStoreI.proxy.extraParams.cu_inve   = mcuentaI ;
							cplaStoreI.load({ params: { 'cuenta': registro.data.cu_inve, 'origen': 'beforeform' } });
							
							mcuentaC  = registro.data.cu_cost;
							cplaStoreC.proxy.extraParams.cu_cost   = mcuentaC ;
							cplaStoreC.load({ params: { 'cuenta': registro.data.cu_cost, 'origen': 'beforeform' } });

							mcuentaD  = registro.data.cu_devo;
							cplaStoreD.proxy.extraParams.cu_devo   = mcuentaD ;
							cplaStoreD.load({ params: { 'cuenta': registro.data.cu_devo, 'origen': 'beforeform' } });

							mdepto  = registro.data.depto;
							mlinea  = registro.data.linea;
							lineStore.proxy.extraParams.depto   = mdepto ;
							lineStore.load({ params: { 'depto': registro.data.depto, linea: registro.data.linea, 'origen': 'beforeform' } });

							form.loadRecord(registro);
						} else {
							mcuentaV  = '';
							mcuentaI  = '';
							mcuentaC  = '';
							mcuentaD  = '';
							mdepto    = '';
							mlinea    = '';
						}
					}
				}
";

		$stores = "
var cplaStoreV = new Ext.data.Store({
	fields: [ 'item', 'valor'],
	autoLoad: false,autoSync: false,pageSize: 50,
	pruneModifiedRecords: true,totalProperty: 'results',
	proxy: {
		type: 'ajax',
		url : urlApp + 'contabilidad/cpla/cplabusca',
		extraParams: {  'cuenta': mcuentaV, 'origen': 'store' },
		reader: {type: 'json',	totalProperty: 'results',root: 'data'
		}
	},
	method: 'POST'
});

var cplaStoreI = new Ext.data.Store({
	fields: [ 'item', 'valor'],
	autoLoad: false,autoSync: false,pageSize: 50,
	pruneModifiedRecords: true,totalProperty: 'results',
	proxy: {
		type: 'ajax',
		url : urlApp + 'contabilidad/cpla/cplabusca',
		extraParams: {  'cuenta': mcuentaI, 'origen': 'store' },
		reader: {type: 'json',	totalProperty: 'results',root: 'data'
		}
	},
	method: 'POST'
});

var cplaStoreC = new Ext.data.Store({
	fields: [ 'item', 'valor'],
	autoLoad: false,autoSync: false,pageSize: 50,
	pruneModifiedRecords: true,totalProperty: 'results',
	proxy: {
		type: 'ajax',
		url : urlApp + 'contabilidad/cpla/cplabusca',
		extraParams: {  'cuenta': mcuentaC, 'origen': 'store' },
		reader: {type: 'json',	totalProperty: 'results',root: 'data'
		}
	},
	method: 'POST'
});

var cplaStoreD = new Ext.data.Store({
	fields: [ 'item', 'valor'],
	autoLoad: false,autoSync: false,pageSize: 50,
	pruneModifiedRecords: true,totalProperty: 'results',
	proxy: {
		type: 'ajax',
		url : urlApp + 'contabilidad/cpla/cplabusca',
		extraParams: {  'cuenta': mcuentaD, 'origen': 'store' },
		reader: {type: 'json',	totalProperty: 'results',root: 'data'
		}
	},
	method: 'POST'
});


var lineStore = new Ext.data.Store({
	fields: [ 'item', 'valor'],
	autoLoad: false,autoSync: false,pageSize: 50,
	pruneModifiedRecords: true,totalProperty: 'results',
	proxy: {
		type: 'ajax',
		url : urlApp + 'inventario/line/linebusca/',
		extraParams: {  'depto': mdepto, 'linea': mlinea, 'origen': 'store' },
		reader: {type: 'json',	totalProperty: 'results',root: 'data'
		}
	},
	method: 'POST'
});

		";

		$features = "features: [ filters],";
		$filtros = "var filters = { ftype: 'filters', encode: 'json', local: false }; ";

		$data['listados']    = $listados;
		$data['otros']       = $otros;
		$data['encabeza']    = $encabeza;
		$data['urlajax']     = $urlajax;
		$data['variables']   = $variables;
		$data['funciones']   = $funciones;
		$data['valida']      = $valida;
		$data['columnas']    = $columnas;
		$data['campos']      = $campos;
		$data['stores']      = $stores;
		$data['camposforma'] = $camposforma;
		$data['titulow']     = $titulow;
		$data['dockedItems'] = $dockedItems;
		$data['winwidget']   = $winwidget;
		$data['features']    = $features;
		$data['filtros']     = $filtros;
		
		$data['title']  = heading('Grupos de Inventario');
		$this->load->view('extjs/extjsven',$data);
	}
*/
}

?>
