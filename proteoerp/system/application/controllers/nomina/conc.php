<?php require_once(BASEPATH.'application/controllers/validaciones.php');
//concepto
class Conc extends Controller {
	var $mModulo='CONC';
	var $titp='Conceptos de Nomina';
	var $tits='Conceptos de Nomina';
	var $url ='nomina/conc/';

	function Conc(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('NNN',1);
	}

	function index(){
		if ( !$this->datasis->iscampo('conc','id') ) {
			$this->db->simple_query('ALTER TABLE conc DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE conc ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE conc ADD UNIQUE INDEX concepto (concepto)');
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

		$readyscript = '
		var linkdeudor = "'.site_url('ajax/buscasprv').'";
		var linkacreed = "'.site_url('ajax/buscamgas').'";
		';

		$bodyscript = '
		<script type="text/javascript">
		jQuery("#a1").click( function(){
			var id = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getRowData\',id);
				window.open(\''.base_url().'formatos/ver/CONC/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});
		</script>
		';

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));
	
		//$grid->wbotonadd(array("id" => "boton1", "img" => 'images/agrega4.png', "alt" => 'Agregar', "label" => 'Agregar Venta'));
		$WestPanel = $grid->deploywestp();

		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'));

		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']    = $EastPanel;
		$param['readyscript']  = $readyscript;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('CONC', 'JQ');
		$param['otros']        = $this->datasis->otros('CONC', 'JQ');
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		
		$param['tabs'] = false;
		$param['encabeza'] = $this->titp;
		
		$this->load->view('jqgrid/crud2',$param);
	}



	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function concadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function concedit(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function concshow(){
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
		function concdel() {
		var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
		if(id){
			if(confirm(" Seguro desea eliminar el registro?")){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
					try{
						var json = JSON.parse(data);
						if (json.status == "A"){
							apprise("Registro eliminado");
						}else{
							apprise("Registro no se puede eliminado");
						}
					}catch(e){
						$("#fborra").html(data);
						$("#fborra").dialog( "open" );
					}
				});
			}
		}else{
			$.prompt("<h1>Por favor Seleccione un Registro</h1>");
		}
		};';
		//Wraper de javascript
		$bodyscript .= '
		$(function(){
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
							try{
								var json = JSON.parse(r);
								if (json.status == "A"){
									apprise("Registro Guardado");
									$( "#fedita" ).dialog( "close" );
									grid.trigger("reloadGrid");
									'.$this->datasis->jwinopen(site_url('formatos/ver/CONC').'/\'+res.id+\'/id\'').';
									return true;
								} else {
									apprise(json.mensaje);
								}
							}catch(e){
								$("#fedita").html(r);
							}
						}
					})
				},
				"Cancelar": function() {
					$("#fedita").html("");
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				$("#fedita").html("");
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});';

		$bodyscript .= '
		$("#fshow").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fshow").html("");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				$("#fshow").html("");
			}
		});';

		$bodyscript .= '
		$("#fborra").dialog({
			autoOpen: false, height: 300, width: 400, modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fborra").html("");
					jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
				$("#fborra").html("");
			}
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
		$linea   = 1;

		$grid  = new $this->jqdatagrid;

		$grid->addField('concepto');
		$grid->label('Codigo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 4 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('descrip');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 35 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"A":"Asignacion", "D":"Deduccion"}, style:"width:100px"} ',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));


		$grid->addField('aplica');
		$grid->label('Aplica');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"S   ":"Semanal", "Q   ":"Quincenal", "B   ":"Bisemanal", "M   ":"Mensual",  "SQ  ":"Semanal/Quincenal", "SQM ":"Sem/Quin/Mensual", "SQMB":"Sem/Quin/Men/Bisemanal"}, style:"width:200px"} ',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ label: "Aplicacion" }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('grupo');
		$grid->label('Grupo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:5, maxlength: 4 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));


		$grid->addField('liquida');
		$grid->label('Liquidacion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'editrules'     => '{ required:true}',
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"S":"Si", "N":"No"}, style:"width:80px" }',
			'formoptions'   => '{label:"Liquidaciones", rowpos:'.$linea.', colpos:2 }'
		));


		$linea = $linea + 1;
		$grid->addField('encab1');
		$grid->label('Encabezado 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('encab2');
		$grid->label('Encabezado 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('formula');
		$grid->label('Formula');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ rows:4, cols: 40 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('tipod');
		$grid->label('Deuda');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"P":"Proveedor", "G":"Gasto"}, style:"width:100px",
				dataEvents: [{
					type: "change", fn: function(e){
						var v=$(e.target).val();
						_cargo = v+_cargo.substr(1,2);
						$("input#ctade").val("");
					}
				}]
			}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$link = site_url('ajax/buscasprvmgas');
		
		$grid->addField('ctade');
		$grid->label('Cta. Deudor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editoptions'   => '{'.$grid->autocomplete($link.'/d', 'ctade','aaaaaa','<div id=\"aaaaaa\"><b>"+ui.item.label+"</b></div>').'}',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));


		$linea = $linea + 1;
		$grid->addField('tipoa');
		$grid->label('Acreedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"P":"Proveedor", "G":"Gasto"}, style:"width:100px",
				dataEvents: [{
					type: "change", fn: function(e){
						var v=$(e.target).val();
						_cargo = _cargo.substr(0,2)+v;
						$("input#ctaac").val("");
					}
				}]
			}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('ctaac');
		$grid->label('Cta.Acreed');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editoptions'   => '{'.$grid->autocomplete($link.'/a', 'ctaac','bbbbbb','<div id=\"bbbbbb\"><b>"+ui.item.label+"</b></div>').'}',
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
		$grid->setHeight('380');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
				if (id){
					var mtipod = $(gridId1).getCell(id,"tipod");
					var mtipoa = $(gridId1).getCell(id,"tipoa");
					_cargo = mtipoa+" "+mtipod;
					lastsel2 = id;
				} else { lastsel2 = 0 }
			}
		');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 650, height:360, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 650, height:360, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
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
		$mWHERE = $grid->geneTopWhere('conc');

		$response   = $grid->getData('conc', array(array()), array(), false, $mWHERE );
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
				$concepto  = $this->input->post('concepto');
				$this->db->insert('conc', $data);
				echo "Registro Agregado ".$concepto;
				logusu('CONC',"Registro ".$concepto." INCLUIDO");
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			//unset($data['ubica']);
			$concepto  = $this->input->post('concepto');
			$this->db->where('id', $id);
			$this->db->update('conc', $data);
			logusu('CONC',"Registro ".$concepto." MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			$concepto  = $this->input->post('concepto');
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM nomina WHERE concepto='$concepto'");
			$check += $this->datasis->dameval("SELECT COUNT(*) FROM asig   WHERE concepto='$concepto'");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM conc WHERE id=$id ");
				logusu('CONC',"Registro ".$concepto." ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}


/*
	function conc(){
		parent::Controller(); 
		$this->load->library("rapyd");
		define ("THISFILE",   APPPATH."controllers/nomina". $this->uri->segment(2).EXT);
	}

	function index(){
		$this->datasis->modulo_id(704,1);
		if ( !$this->datasis->iscampo('conc','id') ) {
			$this->db->simple_query('ALTER TABLE conc DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE conc ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE conc ADD UNIQUE INDEX concepto (concepto)');
		}
		$this->concextjs();
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Descripci&oacute;n", 'conc');
		
		$filter->concepto = new inputField("Concepto", "concepto");
		$filter->concepto->size = 5;
		
		$filter->descrip  = new inputField("Descripci&oacute;n", "descrip");
		
		$filter->buttons("reset","search");
		$filter->build('dataformfiltro');
		
		$uri = anchor('nomina/conc/dataedit/show/<#concepto#>','<#concepto#>');
		$uri_2  = anchor('nomina/conc/dataedit/modify/<#concepto#>',img(array('src'=>'images/editar.png','border'=>'0','alt'=>'Editar','height'=>'12')));
		
		$mtool  = "<table background='#554455'><tr>";
		$mtool .= "<td>&nbsp;</td>";

		$mtool .= "<td>&nbsp;<a href='".base_url()."nomina/conc/dataedit/create'>";
		$mtool .= img(array('src' => 'images/agregar.jpg', 'alt' => 'Agregar Registro', 'title' => 'Agregar Registro','border'=>'0','height'=>'32'));
		$mtool .= "</a>&nbsp;</td>";

		$mtool .= "</tr></table>";

		$grid = new DataGrid($mtool);
		$grid->order_by("concepto","asc");
		$grid->per_page = 30;

		$grid->column('Acci&oacute;n',$uri_2,'align=center');
		$grid->column_orderby("Concepto",$uri,'concepto');
		$grid->column_orderby("Tipo","tipo",'tipo');
		$grid->column_orderby("Descripci&oacute;n","descrip",'descrip');
		$grid->column_orderby("Tipoa","tipoa",'tipoa');
		$grid->column_orderby("Aplica","aplica",'aplica');
		$grid->column_orderby("Liquida","liquida",'liquida');
		$grid->column("F&oacute;rmula","formula");
		
		//$grid->add("nomina/conc/dataedit/create");
		$grid->build('datagridST');
		
				//************ SUPER TABLE ************* 
		$extras = '
<script type="text/javascript">
//<![CDATA[
(function() {
	var mySt = new superTable("demoTable", {
	cssSkin : "sSky",
	fixedCols : 1,
	headerRows : 1,
	onStart : function () {	this.start = new Date();},
	onFinish : function () {document.getElementById("testDiv").innerHTML += "Finished...<br>" + ((new Date()) - this.start) + "ms.<br>";}
	});
})();
//]]>
</script>
';
		$style ='
<style type="text/css">
.fakeContainer { 
    margin: 5px;
    padding: 0px;
    border: none;
    width: 640px;
    height: 320px;
    overflow: hidden;
}
</style>	
';
//****************************************
	
		$data['style']   = $style;
		$data['style']  .= style('superTables.css');
		$data['extras']  = $extras;
		
		$data['content'] = $grid->output;
		$data['filtro']  = $filter->output;
		
		$data['title']  = heading('Conceptos');
		$data['script'] = script('jquery.js');
		$data["script"].= script('superTables.js');
		$data['head']   = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
*/
	function getctade($tipoa=NULL){
		$this->rapyd->load("fields");
		$uadministra = new dropdownField("ctade", "ctade");
		$uadministra->status = "modify";
		$uadministra->style ="width:400px;";
		//echo 'de nuevo:'.$tipoa;
		if ($tipoa!==false){
		if($tipoa=='P'){
					$uadministra->options("SELECT proveed,nombre FROM sprv ORDER BY proveed");
			}else{
				if($tipoa=='G'){
					$uadministra->options("SELECT codigo,descrip FROM mgas ORDER BY codigo");
				}else{
					$uadministra->options("SELECT cliente,nombre FROM sprv ORDER BY cliente");
				}
			}
		}else{
 				$uadministra->option("Seleccione un opcion");
		}
		$uadministra->build(); 
		echo $uadministra->output;
	}

	function dataedit(){
		$this->rapyd->load("dataobject","dataedit2");
			
		$qformato=$this->qformato=$this->datasis->formato_cpla();
		
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
		
		$bcuenta  =$this->datasis->p_modbus($modbus ,'cuenta');
		$bcontra  =$this->datasis->p_modbus($modbus ,'contra');
		
		$edit = new DataEdit2("Conceptos", "conc");
		$edit->back_url = site_url("nomina/conc/filteredgrid");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->concepto = new inputField("Concepto", "concepto");
		$edit->concepto->rule = "required|callback_chexiste";
		$edit->concepto->mode = "autohide";
		$edit->concepto->maxlength= 4;
		$edit->concepto->size = 7;
		
		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->style ="width:100px;";
		$edit->tipo->option("","");
		$edit->tipo->options(array("A"=> "Asignaci&oacute;n","O"=>"Otros","D"=> "Deducci&oacute;n"));
		
		$edit->descrip =  new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->size =45;
		$edit->descrip->maxlength=35;
		$edit->descrip->rule = "strtoupper|required";
		
		$edit->grupo = new inputField("Grupo", "grupo");
		$edit->grupo->size =7;
		$edit->grupo->maxlength=4;
		
		$edit->encab1 = new inputField("Encabezado 1", "encab1");
		$edit->encab1->size = 22;
		$edit->encab1->maxlength=12;
		
		$edit->encab2 =   new inputField("Encabezado 2&nbsp;", "encab2");
		$edit->encab2->size = 22;
		$edit->encab2->maxlength=12;
				
		$edit->formula = new textareaField("F&oacute;rmula","formula");
		$edit->formula->rows = 4;
		$edit->formula->cols=90;
		
		$edit->cuenta = new inputField("Debe", "cuenta");
		$edit->cuenta->size =19;
		$edit->cuenta->maxlength=15;
		$edit->cuenta->group="Enlase Contable";
		$edit->cuenta->rule='callback_chcuentac';
		$edit->cuenta->append($bcuenta);
		
		$edit->contra =  new inputField("Haber", "contra"); 
		$edit->contra->size = 19;   
		$edit->contra->maxlength=15;
		$edit->contra->group="Enlase Contable";
		$edit->contra->rule='callback_chcuentac';
		$edit->contra->append($bcontra);
		
		$edit->tipod = new dropdownField ("Deudor", "tipod");
		$edit->tipod->style ="width:100px;";
		$edit->tipod->option(" "," "); 
		$edit->tipod->option("G","Gasto");
		$edit->tipod->option("C","Cliente");
		$edit->tipod->option("P","Proveedor");
		$edit->tipod->onchange = "get_ctaac();";
		$edit->tipod->group="Enlase Administrativo";

		$edit->ctade = new dropdownField("Cuenta Deudor", "ctade");
		$edit->ctade->style ="width:400px;";
		$edit->ctade->group="Enlase Administrativo";

		$tipod  =$edit->getval("tipod");
		if($tipod=='P'){
				$edit->ctade->options("SELECT proveed,CONCAT_WS(' ',proveed,nombre)a FROM sprv ORDER BY proveed");
		}else{
			if($tipod=='G'){
				$edit->ctade->options("SELECT codigo,CONCAT_WS(' ',codigo,descrip)a FROM mgas ORDER BY codigo");
			}else{
				$edit->ctade->options("SELECT cliente,CONCAT_WS(' ',proveed,nombre)a FROM sprv ORDER BY cliente");
			}
		}
		
		$edit->tipoa = new dropdownField ("Acreedor", "tipoa");  
		$edit->tipoa->style ="width:100px;";
		$edit->tipoa->option(" "," "); 
		$edit->tipoa->option("G","Gasto");    
		$edit->tipoa->option("C","Cliente");  
		$edit->tipoa->option("P","Proveedor");
		$edit->tipoa->group="Enlase Administrativo";
		$edit->tipoa->onchange = "get_ctade();";
		
		$edit->ctaac =   new dropdownField("Cuenta Acreedor", "ctaac"); 
		$edit->ctaac->style ="width:400px;";     
		$edit->ctaac->group="Enlase Administrativo";
		$tipod  =$edit->getval("tipoa");
		if($tipod=='P'){
				$edit->ctaac->options("SELECT proveed,CONCAT_WS(' ',proveed,nombre)a FROM sprv ORDER BY proveed");
		}else{
			if($tipod=='G'){
				$edit->ctaac->options("SELECT codigo,CONCAT_WS(' ',codigo,descrip)a FROM mgas ORDER BY codigo");
			}else{
				$edit->ctaac->options("SELECT cliente,CONCAT_WS(' ',proveed,nombre)a FROM sprv ORDER BY cliente");
			}
		}
			
		$edit->aplica =   new dropdownField("Aplica para liquidacion", "liquida"); 
		$edit->aplica->style ="width:50px;";     
		$edit->aplica->option("S","S");
		$edit->aplica->option("N","N"); 
    			
		$edit->buttons("modify", "save", "undo", "back","delete");
		$edit->build();
		
		$link=site_url('nomina/conc/getctade');
		$link2=site_url('nomina/conc/getctade');
	$data['script']  =<<<script
		<script type="text/javascript" charset="utf-8">
		function get_ctade(){
				var tipo=$("#tipoa").val();
				$.ajax({
					url: "$link"+'/'+tipo,
					success: function(msg){
						$("#td_ctade").html(msg);								
					}
				});
									//alert(tipo);
			} 
		function get_ctaac(){
				var tipo=$("#tipod").val();
				$.ajax({
					url: "$link2"+'/'+tipo,
					success: function(msg){
						$("#td_ctaac").html(msg);
					}
				});
			} 	
		</script>
script;
	
		$data['content'] = $edit->output;           
		$data['title']   = "<h1>Conceptos</h1>";        
		$data["head"]    = $this->rapyd->get_head();                                                                                         
		$data["head"]   .='<script src="'.base_url().'assets/default/script/jquery.js'.'" type="text/javascript" charset="utf-8"></script>';
		$this->load->view('view_ventanas', $data);  
	}

	function _post_insert($do){
		$codigo=$do->get('concepto');
		$nombre=$do->get('descrip');
		logusu('conc',"CONCEPTO $codigo NOMBRE  $nombre CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('concepto');
		$nombre=$do->get('descrip');
		logusu('conc',"CONCEPTO $codigo NOMBRE  $nombre  MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('concepto');
		$nombre=$do->get('descrip');
		logusu('conc',"CONCEPTO $codigo NOMBRE  $nombre  ELIMINADO ");
	}

	function chexiste($codigo){
		$codigo=$this->input->post('concepto');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM conc WHERE concepto='$codigo'");
		if ($check > 0){
			$nombre=$this->datasis->dameval("SELECT descrip FROM conc WHERE concepto='$codigo'");
			$this->validation->set_message('chexiste',"El concepto $codigo nombre $nombre ya existe");
			return FALSE;
		}else {
  			return TRUE;
		}
	}

/*
	function instalar(){
		$mSQL="ALTER TABLE conc ADD PRIMARY KEY (concepto);";
		$this->db->simple_query($mSQL);	
	}

	function grid(){
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 50;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : '[{"property":"concepto","direction":"ASC"}]';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;

		$where = $this->datasis->extjsfiltro($filters);

		$this->db->_protect_identifiers=false;
		$this->db->select('concepto, tipo, descrip, aplica, grupo, encab1, encab2, formula, tipod, ctade, tipoa, ctaac, if(liquida="S", "true","false") liquida, id');
		$this->db->from('conc');

		if (strlen($where)>1){$this->db->where($where);	}
		
		$sort = json_decode($sort, true);

		if ( count($sort) == 0 ) $this->db->order_by( 'concepto', 'asc' );
		
		for ( $i=0; $i<count($sort); $i++ ) {
			$this->db->order_by($sort[$i]['property'],$sort[$i]['direction']);
		}

		$this->db->limit($limit, $start);

		$query = $this->db->get();
		$results = $this->db->count_all('conc');
		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data", results:'. $results.', data:'.json_encode($arr).'}';
	}

	function conccta() {
		$start    = isset($_REQUEST['start'])  ? $_REQUEST['start'] :   0;
		$limit    = isset($_REQUEST['limit'])  ? $_REQUEST['limit'] :  25;
		$tipo     = isset($_REQUEST['tipo'])   ? $_REQUEST['tipo']  : 'P';
		$cuenta   = isset($_REQUEST['cuenta']) ? $_REQUEST['cuenta']:  '';
		$semilla  = isset($_REQUEST['query'])  ? $_REQUEST['query'] :  '';

		$mSQL = '';
	
		if ( $tipo == 'G' ) {
			$mSQL = "SELECT codigo item, CONCAT(codigo, ' ', descrip) valor FROM mgas WHERE tipo='G' ";
			if ( strlen($semilla)>0 ){
				$mSQL .= " AND (codigo LIKE '$semilla%' OR descrip LIKE '%$semilla%') ";
			} else {
				if ( strlen($cuenta)>0 ) $mSQL .= " AND (codigo LIKE '$cuenta%' OR descrip LIKE '%$cuenta%') ";
			}
			$mSQL .= "ORDER BY descrip ";
			$results = $this->db->count_all('mgas');
		} elseif ( $tipo == 'P' ) {
			$mSQL = "SELECT proveed item, CONCAT(proveed, ' ', nombre) valor FROM sprv WHERE tipo<>'0' ";
			if ( strlen($semilla)>0 ){
				$mSQL .= " AND ( proveed LIKE '$semilla%' OR nombre LIKE '%$semilla%' OR  rif LIKE '%$semilla%') ";
			} else {
				if ( strlen($cuenta)>0 ) $mSQL .= " AND (proveed LIKE '$cuenta%' OR nombre LIKE '%$cuenta%' OR  rif LIKE '%$cuenta%') ";
			}
			$mSQL .= "ORDER BY nombre ";
			$results = $this->db->count_all('sprv');
		} elseif ( $tipo == 'C' ) {
			$mSQL = "SELECT cliente item, CONCAT(cliente, ' ', nombre) valor FROM scli WHERE tipo<>'0' ";
			if ( strlen($semilla)>0 ){
				$mSQL .= " AND (cliente LIKE '$semilla%' OR nombre LIKE '%$semilla%' OR rifci LIKE '%$semilla%') ";
			} else {
				if ( strlen($cuenta)>0 ) $mSQL .= " AND (cliente LIKE '$cuenta%' OR nombre LIKE '%$cuenta%' OR rifci LIKE '%$cuenta%') ";
			}
			$mSQL .= "ORDER BY nombre ";
			$results = $this->db->count_all('scli');
		} 

		if ( empty($mSQL)) {
			echo '{success:true, message:"mSQL vacio, Loaded data", results: 0, data:'.json_encode(array()).'}';
		} else {
			$mSQL .= " limit $start, $limit ";
			$query = $this->db->query($mSQL);
			$arr = array();
			foreach ($query->result_array() as $row)
			{
				$meco = array();
				foreach( $row as $idd=>$campo ) {
					$meco[$idd] = utf8_encode($campo);
				}
				$arr[] = $meco;
			}
			echo '{success:true, message:"'.$mSQL.'", results:'. $results.', data:'.json_encode($arr).'}';
		}
	}

	function crear(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos   = $data['data'];
		$concepto = $campos['concepto'];
		$descrip  = $campos['descrip'];

		if ($campos['liquida'] ) {
			$campos['liquida'] = 'S';
		} else {
			$campos['liquida']='N';
		}

		if ( !empty($concepto) ) {
			unset($campos['id']);
			// Revisa si existe ya ese contrato
			if ($this->datasis->dameval("SELECT COUNT(*) FROM conc WHERE concepto='$concepto'") == 0)
			{
				$mSQL = $this->db->insert_string("conc", $campos );
				$this->db->simple_query($mSQL);
				logusu('conc',"CONCEPTOS DE NOMINA $concepto NOMBRE  $descrip CREADO");
				echo "{ success: true, message: 'Concepto Agregado'}";
			} else {
				echo "{ success: false, message: 'Ya existe un contrato con ese Codigo!!'}";
			}
			
		} else {
			echo "{ success: false, message: 'Ya existe un contrato con ese Codigo!!'}";
		}
	}

	function modificar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];

		$concepto = $campos['concepto'];

		unset($campos['concepto']);
		$liquida = $campos['liquida'];

		if ($campos['liquida'] ) {
			$campos['liquida'] = 'S';
		} else {
			$campos['liquida']='N';
		}

		//print_r($campos);
		$mSQL = $this->db->update_string("conc", $campos,"id='".$data['data']['id']."'" );
		$this->db->simple_query($mSQL);
		logusu('conc',"CONCEPTO DE NOMINA $concepto ID ".$data['data']['id']." MODIFICADO");
		echo "{ success: true, message: 'Trabajador Modificado $liquida -> '".$data['data']['liquida']."}";
	}

	function eliminar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];

		$concepto = $campos['concepto'];
		$descrip  = $campos['descrip'];

		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM nomina WHERE concepto='$concepto'");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM asig   WHERE concepto='$concepto'");

		if ($check > 0){
			echo "{ success: false, message: 'Concepto con Movimiento no puede ser Borrado'}";
		} else {
			$this->db->simple_query("DELETE FROM conc WHERE concepto='$concepto'");
			logusu('conc',"CONCEPTO $concepto $descrip ELIMINADO");
			echo "{ success: true, message: 'Concepto Eliminado'}";
		}
	}



//

//****************************************************************8
//
//
//
//****************************************************************8
	function concextjs(){


		$listados= $this->datasis->listados('conc');
		$otros=$this->datasis->otros('conc', 'conc');

		$encabeza='CONCEPTOS DE NOMINA';
		$listados= $this->datasis->listados('conc');
		$otros=$this->datasis->otros('conc', 'nomina/conc');

		$urlajax = 'nomina/conc/';
		$variables = "
var mtipod ;
var mtipoa ;
var mctade ;
var mctaac ;
";

		$funciones = "
//coloca un boton verde cuando la va para la liquidacion
function rliquida(val) {
	if ( val == 'true' ){
		return  '<img src=\"'+urlApp+'images/S.gif\">';
	} else {
		return  '<img src=\"'+urlApp+'images/N.gif\">';
	}
};
";
		$valida = "
		{ type: 'length', field: 'concepto',  min: 1 },
		{ type: 'length', field: 'descrip',   min: 1 }
";
		
		$columnas = "
		{ header: 'Codigo',      width:  50, sortable: true, dataIndex: 'concepto', field: { type: 'textfield' }, filter: { type: 'string' } }, 
		{ header: 'Tipo',        width:  30, sortable: true, dataIndex: 'tipo',     field: { type: 'textfield' }, filter: { type: 'string' } }, 
		{ header: 'Descripcion', width: 200, sortable: true, dataIndex: 'descrip',  field: { type: 'textfield' }, filter: { type: 'string' } }, 
		{ header: 'Aplica',      width:  50, sortable: true, dataIndex: 'aplica',   field: { type: 'textfield' }, filter: { type: 'string' } }, 
		{ header: 'Grupo',       width:  50, sortable: true, dataIndex: 'grupo',    field: { type: 'textfield' }, filter: { type: 'string' } },  
		{ header: 'Titulo1',     width: 100, sortable: true, dataIndex: 'encab1',   field: { type: 'textfield' }, filter: { type: 'string' } }, 
		{ header: 'Titulo2',     width: 100, sortable: true, dataIndex: 'encab2',   field: { type: 'textfield' }, filter: { type: 'string' } }, 
		{ header: 'Formula',     width: 200, sortable: true, dataIndex: 'formula',  field: { type: 'textfield' }, filter: { type: 'string' } }, 
		{ header: 'TipoDB',      width:  70, sortable: true, dataIndex: 'tipod',    field: { type: 'textfield' }, filter: { type: 'string' } }, 
		{ header: 'CuentaDB',    width:  70, sortable: true, dataIndex: 'ctade',    field: { type: 'textfield' }, filter: { type: 'string' } }, 
		{ header: 'TipoHB',      width:  70, sortable: true, dataIndex: 'tipoa',    field: { type: 'textfield' }, filter: { type: 'string' } }, 
		{ header: 'CuentaHB',    width:  70, sortable: true, dataIndex: 'ctaac',    field: { type: 'textfield' }, filter: { type: 'string' } }, 
		{ header: 'Liquida',     width:  60, sortable: true, dataIndex: 'liquida',  field: { type: 'textfield' }, filter: { type: 'string' }, renderer: rliquida }
	";

		$campos = "'id', 'concepto', 'tipo', 'descrip', 'aplica', 'grupo', 'encab1', 'encab2', 'formula', 'tipod', 'ctade', 'tipoa', 'ctaac', 'liquida'";

		$camposforma = "
							{
								layout: 'column',
								frame: false,
								border: false,
								labelAlign: 'right',
								defaults: {xtype:'fieldset'  },
								style:'padding:4px',
								items: [
									{ xtype: 'textfield',   fieldLabel: 'Codigo',      labelWidth:50, name: 'concepto', allowBlank: false, columnWidth : 0.18, id: 'concepto' },
									{ xtype: 'combo',       fieldLabel: 'Tipo.',       labelWidth:40, name: 'tipo',     store: [['A','Asignaciones'],['D','Deducciones'],['O','Otro']], columnWidth: 0.22 },
									{ xtype: 'textfield',   fieldLabel: 'Descripcion', labelWidth:70, name: 'descrip',  allowBlank: false, columnWidth : 0.60, id: 'descrip' },
									{ xtype: 'textfield',   fieldLabel: 'Aplica',      labelWidth:50, name: 'aplica',   allowBlank: false, columnWidth: 0.18  },
									{ xtype: 'textfield',   fieldLabel: 'Grupo',       labelWidth:40, name: 'grupo',    allowBlank: true, columnWidth: 0.22  },
									{ xtype: 'textfield',   fieldLabel: 'Titulos',     labelWidth:70, name: 'encab1',   allowBlank: false, columnWidth: 0.35  },
									{ xtype: 'textfield',   fieldLabel: '-',           labelWidth: 5, name: 'encab2',   allowBlank: true, columnWidth: 0.25  },
								]
							},{
								layout: 'column',
								frame: false,
								border: false,
								labelAlign: 'right',
								defaults: {xtype:'fieldset' },
								style:'padding:4px',
								items: [
									{ xtype: 'textareafield', fieldLabel: 'Formula', labelWidth:50, name: 'formula',   allowBlank: false, columnWidth: 1 }
								]
							},{
								layout: 'column',
								labelAlign: 'right',
								defaults: {xtype:'fieldset' },
								style:'padding:4px',
								items: [
									{
										xtype: 'combo',
										fieldLabel: 'Deudora',
										labelWidth:60,
										name: 'tipod',
										id: 'tipod',
										queryMode: 'local',
										triggerAction: 'all',
										forceSelection: true,
										store: [['G','Gasto'], ['P','Proveedor'], ['C', 'Cliente']],
										columnWidth: 0.30,
										listeners: { 'select' : function( field, nval, oval ) {
												mtipod = this.getValue();
												ctadeStore.proxy.extraParams.tipo   = mtipod ;
												ctadeStore.proxy.extraParams.cuenta = '' ;
												ctadeStore.load({ params: {'tipo': this.getValue(), 'origen': 'combotipod' } });
											}
										}
									},
									{
										xtype: 'combo',
										fieldLabel: 'Cuenta',
										labelWidth:60,
										name: 'ctade',
										id:   'ctade',
										mode: 'remote',
										hideTrigger: true,
										typeAhead: true,
										forceSelection: true,										valueField: 'item',
										displayField: 'valor',
										store: ctadeStore,
										columnWidth: 0.70
									},
									{
										xtype: 'combo',
										fieldLabel: 'Acreedora',
										labelWidth:60,
										name: 'tipoa',
										id: 'tipoa',
										queryMode: 'local',
										triggerAction: 'all',
										forceSelection: true,
										store: [['G','Gasto'],['P','Proveedor'],['C', 'Cliente']],
										columnWidth: 0.30,
										listeners: { 'select' : function( field, nval, oval ) {
												mtipoa = this.getValue();
												ctaacStore.proxy.extraParams.tipo   = mtipoa ;
												ctaacStore.proxy.extraParams.cuenta = '' ;
												ctaacStore.load({ params: {'tipo': this.getValue(), 'origen': 'combotipoa' }});
											}
										}
									},
									{
										xtype: 'combo',
										fieldLabel: 'Cuenta',
										labelWidth:60,
										//pageSize: 50,
										hideTrigger: true,
										typeAhead: true,
										forceSelection: true,										mode: 'remote',
										name: 'ctaac',
										id:   'ctaac',
										valueField: 'item',
										displayField: 'valor',
										store: ctaacStore,
										columnWidth: 0.70
									},
									{
										xtype: 'checkboxfield',
										fieldLabel: 'Aplica para calculo de las liquidaciones',
										labelWidth:300,
										name: 'liquida',
										id:   'liquida',
										columnWidth: 0.99
									}
								]
							}
		";

		$titulow = 'Conceptos de Nomina';

		$winwidget = "
				closable: false,
				closeAction: 'destroy',
				width: 650,
				height: 370,
				resizable: false,
				modal: true,
				items: [writeForm],
				listeners: {
					beforeshow: function() {
						var form = this.down('writerform').getForm();
						this.activeRecord = registro;
						
						if (registro) {
							mctade = registro.data.ctade;
							mctaac = registro.data.ctaac;
							mtipod = registro.data.tipod;
							mtipoa = registro.data.tipoa;
							ctaacStore.proxy.extraParams.tipo   = mtipoa ;
							ctaacStore.proxy.extraParams.cuenta = mctaac ;
							ctadeStore.proxy.extraParams.tipo   = mtipod ;
							ctadeStore.proxy.extraParams.cuenta = mctade ;
							ctadeStore.load({ params: {'tipo': registro.data.tipod, 'cuenta':registro.data.ctade, 'origen': 'beforeform' } });
							ctaacStore.load({ params: {'tipo': registro.data.tipoa, 'cuenta':registro.data.ctaac, 'origen': 'beforeform' } });
							form.loadRecord(registro);
							form.findField('concepto').setReadOnly(true);
						} else {
							form.findField('concepto').setReadOnly(false);
							mctade = '';
							mctaac = '';
							mtipod = '';
							mtipoa = '';
						}
					}
				}
";

		$stores = "
var ctadeStore = new Ext.data.Store({
	fields: [ 'item', 'valor'],
	autoLoad: false,
	autoSync: false,
	pageSize: 50,
	pruneModifiedRecords: true,
	totalProperty: 'results',
	proxy: {
		type: 'ajax',
		url : urlApp + 'nomina/conc/conccta',
		extraParams: { 'tipo': mtipod, 'cuenta': mctade, 'origen': 'store' },
		reader: {type: 'json',totalProperty: 'results',root: 'data'}
	},
	method: 'POST'
});
     
var ctaacStore = new Ext.data.JsonStore({
	fields: [ 'item', 'valor'],
	autoLoad: false,
	pageSize: 50,
	autoSync: false,
	pruneModifiedRecords: true,
	proxy: {
		type: 'ajax',
		url : urlApp + 'nomina/conc/conccta',
		extraParams: { 'tipo': mtipoa, 'cuenta': mctaac, 'origen': 'store' },
		reader: {type: 'json',totalProperty: 'results',	root: 'data'}
	},
	method: 'POST'
});
";

		$features = "features: [{ ftype: 'grouping', groupHeaderTpl: '{name}' },{ ftype: 'filters', encode: 'json', local: false }],";
		$agrupar = "";

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
		$data['winwidget']   = $winwidget;
		$data['features']    = $features;

		$data['title']  = heading('Conceptos de Nomina');
		$this->load->view('extjs/extjsven',$data);

	}
*/
}
?>
