<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/validaciones.php');
class sinvpromo extends validaciones {
	function sinvpromo(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		//$this->datasis->modulo_id(312,1);
		redirect('inventario/sinvpromo/filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter2','datagrid');

		$link2=site_url('inventario/common/get_linea');
		$link3=site_url('inventario/common/get_grupo');

		$mSINV=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
			'codigo' =>'C&oacute;odigo',
			'descrip'=>'Descripci&oacute;n',
			'descrip2'=>'Descripci&oacute;n 2'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo'),
			'titulo'  =>'Buscar Codigo');
		$bSINV=$this->datasis->modbus($mSINV);

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Proveedor');
		$bSPRV=$this->datasis->modbus($mSPRV);


		$attr = array(
		  'width'      => '800',
		  'height'     => '600',
		  'scrollbars' => 'yes',
		  'status'     => 'yes',
		  'resizable'  => 'yes',
		  'screenx'    => '0',
		  'screeny'    => '0'
		);

		$op = array();
		$mSQL='SELECT margen,CONCAT(margen,"%") AS val FROM sinvpromo GROUP BY margen';
		$query=$this->db->query($mSQL);
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$op[$row->margen]=$row->val;
			}
		}

		$sop=serialize($op);
		function dropdown($cod,$margen,$sop){
			$op=unserialize($sop);
			return form_dropdown($cod,$op,$margen);
		}

		$js='function depto(){
			if($("#depto").val()!=""){
				$("#nom_depto").attr("disabled","disabled");
			}else{
				$("#nom_depto").attr("disabled","");
			}
		}
		function linea(){
			if($("#linea").val()!=""){
				$("#nom_linea").attr("disabled","disabled");
			}else{
				$("#nom_linea").attr("disabled","");
			}
		}
		function grupo(){
			if($("#grupo").val()!=""){
				$("#nom_grupo").attr("disabled","disabled");
			}else{
				$("#nom_grupo").attr("disabled","");
			}
		}';

		$select=array('a.id','a.codigo','b.ultimo','b.descrip','b.marca'
		,'b.precio1','a.margen','a.cantidad','b.precio1*(1-(a.margen/100)) AS pfinal','b.id AS sinvid');

		$filter = new DataFilter2('Filtro de promociones');
		$filter->script($js);
		$filter->db->select($select);
		$filter->db->from('sinvpromo AS a');
		$filter->db->join('sinv AS b','a.codigo=b.codigo');

		$filter->codigo = new inputField('C&oacute;digo de producto', 'codigo');
		$filter->codigo->db_name   ='a.codigo';
		$filter->codigo->size      = 15;
		$filter->codigo->maxlength = 15;
		$filter->codigo->append($bSINV);

		$filter->descrip = new inputField('Descripci&oacute;n', 'descrip');
		$filter->descrip->db_name   ='b.descrip';

		$filter->proveed = new inputField('Proveedor', 'proveed');
		$filter->proveed->append($bSPRV);
		$filter->proveed->db_name='b.prov1';
		$filter->proveed->size=25;

		$filter->depto = new dropdownField('Departamento','depto');
		$filter->depto->db_name='b.depto';
		$filter->depto->option("","Seleccione un Departamento");
		$filter->depto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");

		$filter->linea2 = new dropdownField("L&iacute;nea","linea");
		$filter->linea2->db_name="b.linea";
		$filter->linea2->option("","Seleccione un Departamento primero");
		$depto=$filter->getval('depto');
		if($depto!==FALSE){
			$filter->linea2->options("SELECT linea, descrip FROM line WHERE depto='$depto' ORDER BY descrip");
		}else{
			$filter->linea2->option("","Seleccione un Departamento primero");
		}

		$filter->grupo = new dropdownField("Grupo", "grupo");
		$filter->grupo->db_name='c.grupo';
		$filter->grupo->option("","Seleccione una L&iacute;nea primero");
		$linea=$filter->getval('linea2');
		if($linea!==FALSE){
			$filter->grupo->options("SELECT grupo, nom_grup FROM grup WHERE linea='$linea' ORDER BY nom_grup");
		}else{
			$filter->grupo->option('','Seleccione un Departamento primero');
		}

		$filter->margen = new dropdownField('Promoci&oacute;n', 'margen');
		$filter->margen->db_name ='a.margen';
		$filter->margen->option('','Todas');
		$filter->margen->options($op );
		$filter->margen->style='width:120px;';

		$filter->marca = new dropdownField('Marca', 'marca');
		$filter->marca->db_name='b.marca';
		$filter->marca->option('','Todas');
		$filter->marca->options('SELECT TRIM(marca) AS clave, TRIM(marca) AS valor FROM marc ORDER BY marca');
		$filter->marca->style='width:220px;';

		$filter->buttons('reset','search');
		$filter->build();

		$link =anchor('/inventario/sinvpromo/dataedit/modify/<#id#>','<#codigo#>');
		$llink=anchor_popup('inventario/consultas/preciosgeneral/<#codigo#>', 'Consultar precio', $attr);
		$attr['width']  = '420';
		$attr['height'] = '400';
		$llin2=anchor_popup('inventario/precios_sinv/dataedit/modify/<#sinvid#>', '<nformat><#precio1#></nformat>', $attr);

		function pinta($u,$d,$char){
			if($u > $d) return "<b style='color:red'>$char</b>";
			return "<b style='color:green'>$char</b>";
		}

		$grid = new DataGrid('Lista de Art&iacute;culos');
		$grid->use_function('dropdown','pinta');
		$grid->order_by('codigo','asc');
		$grid->per_page = 15;

		$grid->column_orderby('C&oacute;digo'     ,$link     ,'codigo');
		$grid->column_orderby('Descripci&oacute;n','<pinta><#ultimo#>|<#pfinal#>|<#descrip#></pinta>' ,'descrip');
		$grid->column_orderby('Costo'  ,'<nformat><#ultimo#></nformat>'  ,'ultimo' ,'align=\'right\'');
		$grid->column_orderby('P.Final','<nformat><#pfinal#></nformat>'  ,'pfinal' ,'align=\'right\'');
		$grid->column_orderby('PVP'    ,$llin2    ,'precio1','align=\'right\'');
		$grid->column_orderby('Marca'  ,'marca'   ,'marca'  );
		$grid->column_orderby('Promoci&oacute;n',"<dropdown><#id#>|<#margen#>|$sop</dropdown>",'margen','align="right"');
		$grid->column('Consulta' ,$llink);
		//$grid->column_orderby('F.Desde'        ,'<dbdate_to_human>fechad</dbdate_to_human>','fechad');
		//$grid->column_orderby('F.Hasta'        ,'<dbdate_to_human>fechah</dbdate_to_human>','fechah');

		$grid->add('inventario/sinvpromo/dataedit/create');
		$grid->build();
		//echo $grid->db->last_query();

		$this->rapyd->jquery[]='
		$("select:not(#margen)").change( function() {
			$.ajax({
				type: "POST",
				url: "'.site_url('inventario/sinvpromo/cmargen').'",
				data: "cod="+$(this).attr("name")+"&margen="+$(this).val(),
				success: function(msg){
					if (msg=="0"){
						alert("Error al actualizar");
					}
				 } });
		});';

		$this->rapyd->jquery[]='
		$("#depto").change(function(){
			depto();
			$.post("'.$link2.'",{ depto:$(this).val() },function(data){$("#linea").html(data);})
			$.post("'.$link3.'",{ linea:"" },function(data){$("#grupo").html(data);})
		});
		$("#linea").change(function(){
			linea();
			$.post("'.$link3.'",{ linea:$(this).val() },function(data){$("#grupo").html(data);})
		});

		$("#grupo").change(function(){
			grupo();
		});
		depto();linea();grupo();';

		$data['content'] = $filter->output.form_open('',array('id' => 'selec_promo')).$grid->output.form_close();
		$data['title']   = '<h1>Descuentos promocionales</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit() {
		$this->rapyd->load('dataedit');

		$mSINV=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
			'codigo' =>'C&oacute;odigo',
			'descrip'=>'Descripci&oacute;n',
			'descrip2'=>'Descripci&oacute;n 2'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo'),
			'titulo'  =>'Buscar Codigo');
		$bSINV=$this->datasis->modbus($mSINV);

		$script='
		<script language="javascript" type="text/javascript">
		$(function(){
			$(".inputnum").numeric(".");
		});
		</script>';

		$edit = new DataEdit('Art&iacute;culo en Promoci&oacute;n', 'sinvpromo');
		$edit->back_url = site_url('inventario/sinvpromo/filteredgrid/');

		$edit->codigo = new inputField('C&oacute;digo', 'codigo');
		$edit->codigo->size      =  15;
		$edit->codigo->maxlength =  15;
		$edit->codigo->rule      = 'required';
		$edit->codigo->append($bSINV);

		$edit->margen = new inputField('Porcentaje de descuento', 'margen');
		$edit->margen->size      = 15;
		$edit->margen->maxlength = 15;
		$edit->margen->css_class = 'inputnum';
		$edit->margen->rule      = 'required|callback_chporcent';

		/*$edit->cantidad = new inputField('Cantidad', 'cantidad');
		$edit->cantidad->size     = 15;
		$edit->cantidad->maxlength= 15;
		$edit->cantidad->css_class= 'inputnum';
		$edit->cantidad->rule     = 'required';*/

		/*$edit->fechad = new dateonlyField('Desde', 'fechad','d/m/Y');
		$edit->fechad->insertValue = date('Y-m-d',mktime(0,0,0,date('m')+3,date('j'),date('Y')));

		$edit->fechah = new dateonlyField('Hasta', 'fechah','d/m/Y');
		$edit->fechah->insertValue = date('Y-m-d');*/

		$edit->buttons('modify', 'save','undo','delete' ,'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['head']    = script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head().$script;
		$data['title']   = '<h1>C&oacute;digo Barras de Inventario</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function cierraventana(){
		$script='
		<script language="javascript" type="text/javascript">
		$(function(){
			$(window).unload(function() { window.opener.location.reload(); });
			window.close();
		});
		</script>';

		$data['content'] = '<center>Operaci&oacute;n Exitosa</center>';
		$data['head']    = script('jquery.js').$script;
		$data['title']   = '';
		$this->load->view('view_ventanas', $data);
	}


	function dataeditexpress($codigo){
		$this->rapyd->load('dataedit');

		$script='
		<script language="javascript" type="text/javascript">
		$(function(){
			$(".inputnum").numeric(".");
		});
		</script>';

		$edit = new DataEdit('Art&iacute;culo en Promoci&oacute;n', 'sinvpromo');
		$edit->back_save   = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		$edit->back_url = site_url('inventario/sinvpromo/cierraventana');

		$descrip=$this->datasis->dameval('SELECT descrip FROM sinv WHERE codigo='.$this->db->escape($codigo));
		$edit->free = new freeField('Descripci&oacute;n','libre',$descrip);

		$edit->codigo = new hiddenField('', 'codigo');
		$edit->codigo->rule       = 'required|existesinv|unique';
		$edit->codigo->insertValue= $codigo;

		$edit->margen = new inputField('Porcentaje de descuento', 'margen');
		$edit->margen->size      = 15;
		$edit->margen->maxlength = 15;
		$edit->margen->css_class = 'inputnum';
		$edit->margen->rule      = 'required|callback_chporcent';

		$edit->buttons('modify', 'save','undo','delete');
		$edit->build();

		$data['content'] = $edit->output;
		$data['head']    = script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head().$script;
		$data['title']   = '<h1>C&oacute;digo Barras de Inventario</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function cmargen(){
		$margen=$this->input->post('margen');
		$codigo=$this->input->post('cod');
		if($margen!==false && $codigo!==false){
			//$codigo=trim($codigo);
			$mSQL='UPDATE sinvpromo SET margen='.$this->db->escape($margen).' WHERE id='.$this->db->escape($codigo);
			//memowrite($mSQL);
			//echo $mSQL;
			$rt=$this->db->simple_query($mSQL);
			echo ($rt)? 1 : 0;
		}
		echo 0;
	}

	function instala(){
		if (!$this->db->table_exists('sinvpromo')) {
			$mSQL="CREATE TABLE `sinvpromo` (
				`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
				`cliente` CHAR(5) NULL DEFAULT NULL,
				`codigo` VARCHAR(15) NULL DEFAULT NULL,
				`tipo` CHAR(1) NULL DEFAULT NULL,
				`margen` DECIMAL(18,2) NULL DEFAULT NULL,
				`cantidad` DECIMAL(18,3) NULL DEFAULT NULL,
				`fdesde` DATETIME NULL DEFAULT NULL,
				`fhasta` DATETIME NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `codigo` (`codigo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM;";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('sinvpromo');
		if(!in_array('fdesde',$campos)){
			$mSQL="ALTER TABLE `sinvpromo`
			ADD COLUMN `fdesde` DATETIME NULL DEFAULT NULL AFTER `cantidad`,
			ADD COLUMN `fhasta` DATETIME NULL DEFAULT NULL AFTER `fdesde`";
			$this->db->simple_query($mSQL);
		}
	}
}
/*
<?php
class Sinvpromo extends Controller {
	var $mModulo = 'SINVPROMO';
	var $titp    = 'Modulo de descuentos promocionales';
	var $tits    = 'Modulo de descuentos promocionales';
	var $url     = 'inventario/sinvpromo/';

	function Sinvpromo(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'SINVPROMO', $ventana=0 );
	}

	function index(){
		/*if ( !$this->datasis->iscampo('sinvpromo','id') ) {
			$this->db->simple_query('ALTER TABLE sinvpromo DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE sinvpromo ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE sinvpromo ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};* /
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		$this->instalar();
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
		$param['listados']    = $this->datasis->listados('SINVPROMO', 'JQ');
		$param['otros']       = $this->datasis->otros('SINVPROMO', 'JQ');
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
		function sinvpromoadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function sinvpromoedit(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
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
		function sinvpromoshow(){
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
		function sinvpromodel() {
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
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
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
			autoOpen: false, height: 320, width: 400, modal: true,
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
			autoOpen: false, height: 320, width: 400, modal: true,
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
			autoOpen: false, height: 320, width: 400, modal: true,
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

		$bodyscript .= '});';
		$bodyscript .= '</script>';
		return $bodyscript;
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('ID');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		//$grid->addField('tipo');
		//$grid->label('Tipo');
		//$grid->params(array(
		//	'search'        => 'true',
		//	'editable'      => $editar,
		//	'width'         => 40,
		//	'edittype'      => "'text'",
		//	'editrules'     => '{ required:true}',
		//	'editoptions'   => '{ size:1, maxlength: 1 }',
		//));


		$grid->addField('margen');
		$grid->label('Margen %');
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


		$grid->addField('cantidad');
		$grid->label('Cantidad');
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


		$grid->addField('fdesde');
		$grid->label('Fecha Desde');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('fhasta');
		$grid->label('Fecha Hasta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
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
		$grid->setAdd(    $this->datasis->sidapuede('SINVPROMO','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('SINVPROMO','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('SINVPROMO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('SINVPROMO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setOndblClickRow('');

		$grid->setBarOptions("addfunc: sinvpromoadd, editfunc: sinvpromoedit, delfunc: sinvpromodel, viewfunc: sinvpromoshow");

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
	* /
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('sinvpromo');

		$response   = $grid->getData('sinvpromo', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	* /
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
				$check = $this->datasis->dameval("SELECT count(*) FROM sinvpromo WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('sinvpromo', $data);
					echo "Registro Agregado";

					logusu('SINVPROMO',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM sinvpromo WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM sinvpromo WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE sinvpromo SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("sinvpromo", $data);
				logusu('SINVPROMO',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('sinvpromo', $data);
				logusu('SINVPROMO',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM sinvpromo WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sinvpromo WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM sinvpromo WHERE id=$id ");
				logusu('SINVPROMO',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$(".inputnum").numeric(".");
			$("#fdesde").datepicker({dateFormat:"dd/mm/yy"});
			$("#fhasta").datepicker({dateFormat:"dd/mm/yy"});

			$("#codigo").autocomplete({
				delay: 600,
				autoFocus: true,
				source: function( req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscasinv').'",
						type: "POST",
						dataType: "json",
						data: {"q":req.term},
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#codigo").val("");
									$("#sinvdescrip_val").text("");
								}else{
									$.each(data,
										function(i, val){
											sugiere.push( val );
										}
									);
									add(sugiere);
								}
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$("#codigo").attr("readonly", "readonly");
					$("#codigo").val(ui.item.codigo);
					$("#sinvdescrip_val").text(ui.item.descrip);
					setTimeout(function() {  $("#codigo").removeAttr("readonly"); }, 1500);
				}
			});
		});';

		$do = new DataObject('sinvpromo');
		$do->pointer('sinv' , 'sinvpromo.codigo=sinv.codigo' , 'sinv.descrip AS sinvdescrip' , 'left');

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

		//$mSINV=array(
		//	'tabla'   =>'sinv',
		//	'columnas'=>array(
		//	'codigo' =>'C&oacute;odigo',
		//	'descrip'=>'Descripci&oacute;n',
		//	'descrip2'=>'Descripci&oacute;n 2'),
		//	'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
		//	'retornar'=>array('codigo'=>'codigo'),
		//	'titulo'  =>'Buscar Codigo');
		//$bSINV=$this->datasis->modbus($mSINV);

		$edit->codigo = new inputField('C&oacute;digo', 'codigo');
		$edit->codigo->size      =  15;
		$edit->codigo->maxlength =  15;
		$edit->codigo->rule      = 'required|existesinv';
		//$edit->codigo->append($bSINV);

		$edit->sinvdescrip = new inputField('Descripci&oacute;n', 'sinvdescrip');
		$edit->sinvdescrip->type     = 'inputhidden';
		$edit->sinvdescrip->pointer  = true;

		$edit->margen = new inputField('Porcentaje de descuento', 'margen');
		$edit->margen->size      = 15;
		$edit->margen->maxlength = 15;
		$edit->margen->css_class = 'inputnum';
		$edit->margen->rule      = 'required|callback_chporcent';

		$edit->cantidad = new inputField('Cantidad', 'cantidad');
		$edit->cantidad->size     = 15;
		$edit->cantidad->maxlength= 15;
		$edit->cantidad->autocomplete = false;
		$edit->cantidad->css_class= 'inputnum';
		$edit->cantidad->rule     = 'required';

		$edit->fdesde = new dateonlyField('Desde', 'fdesde','d/m/Y');
		$edit->fdesde->insertValue = date('Y-m-d',mktime(0,0,0,date('m')+3,date('j'),date('Y')));
		$edit->fdesde->size        = 15;
		$edit->fdesde->calendar    = false;
		$edit->fdesde->group       = 'Validez';

		$edit->fhasta = new dateonlyField('Hasta', 'fhasta','d/m/Y');
		$edit->fhasta->size        = 15;
		$edit->fhasta->insertValue = date('Y-m-d');
		$edit->fhasta->calendar    = false;
		$edit->fhasta->group       = 'Validez';

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

	function _pre_inserup($do){
		$id     = $do->get('id');
		$codigo = $do->get('codigo');
		$fdesde = $do->get('fdesde');
		$fhasta = $do->get('fhasta');

		$dbcodigo = $this->db->escape($codigo);
        $dbfdesde = $this->db->escape($fdesde);
        $dbfhasta = $this->db->escape($fhasta);

        $mSQL = "SELECT COUNT(*) AS cana
			FROM sinvpromo
			WHERE codigo=${dbcodigo} AND (${dbfdesde} BETWEEN fdesde AND fhasta OR ${dbfhasta} BETWEEN fdesde AND fhasta)";
		if(!empty($id)){
			 $mSQL .= ' AND id<>'.$this->db->escape($id);
		}
		$cana=$this->datasis->dameval($mSQL);

		if($cana>0){
			$do->error_message_ar['pre_ins']='La promoci&oacute;n se solapa con otra';
			$do->error_message_ar['pre_upd']='La promoci&oacute;n se solapa con otra';
			return false;
		}
		return true;

	}

	function _pre_insert($do){
		return $this->_pre_inserup($do);
	}

	function _pre_update($do){
		return $this->_pre_inserup($do);
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='';
		return true;
	}

	function _post_insert($do){
		$codigo = $do->get('codigo');
		$margen = $do->get('margen');
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo promocion ${codigo} margen ${margen}");
	}

	function _post_update($do){
		$codigo = $do->get('codigo');
		$margen = $do->get('margen');
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico promocion ${codigo} margen ${margen}");
	}

	function _post_delete($do){
		$codigo = $do->get('codigo');
		$margen = $do->get('margen');
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino promocion ${codigo} margen ${margen}");
	}


	function cierraventana(){
		$script='
		<script language="javascript" type="text/javascript">
		$(function(){
			$(window).unload(function() { window.opener.location.reload(); });
			window.close();
		});
		</script>';

		$data['content'] = '<center>Operaci&oacute;n Exitosa</center>';
		$data['head']    = script('jquery.js').$script;
		$data['title']   = '';
		$this->load->view('view_ventanas', $data);
	}


	function dataeditexpress($codigo){
		$this->rapyd->load('dataedit');

		$script='
		<script language="javascript" type="text/javascript">
		$(function(){
			$(".inputnum").numeric(".");
		});
		</script>';

		$edit = new DataEdit('Art&iacute;culo en Promoci&oacute;n', 'sinvpromo');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );

		$edit->back_save   = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		$edit->back_url = site_url('inventario/sinvpromo/cierraventana');

		$descrip=$this->datasis->dameval('SELECT descrip FROM sinv WHERE codigo='.$this->db->escape($codigo));
		$edit->free = new freeField('Descripci&oacute;n','libre',$descrip);

		$edit->codigo = new hiddenField('', 'codigo');
		$edit->codigo->rule       = 'required|existesinv|unique';
		$edit->codigo->insertValue= $codigo;

		$edit->margen = new inputField('Porcentaje de descuento', 'margen');
		$edit->margen->size      = 15;
		$edit->margen->maxlength = 15;
		$edit->margen->css_class = 'inputnum';
		$edit->margen->rule      = 'required|callback_chporcent';

		$edit->cantidad = new inputField('Cantidad', 'cantidad');
		$edit->cantidad->size     = 15;
		$edit->cantidad->maxlength= 15;
		$edit->cantidad->autocomplete = false;
		$edit->cantidad->css_class= 'inputnum';
		$edit->cantidad->rule     = 'required';

		$edit->fdesde = new dateonlyField('Desde', 'fdesde','d/m/Y');
		$edit->fdesde->insertValue = date('Y-m-d',mktime(0,0,0,date('m')+3,date('j'),date('Y')));
		$edit->fdesde->size        = 15;
		$edit->fdesde->calendar    = false;
		$edit->fdesde->group       = 'Validez';

		$edit->fhasta = new dateonlyField('Hasta', 'fhasta','d/m/Y');
		$edit->fhasta->size        = 15;
		$edit->fhasta->insertValue = date('Y-m-d');
		$edit->fhasta->calendar    = false;
		$edit->fhasta->group       = 'Validez';

		$edit->buttons('modify', 'save','undo','delete');
		$edit->build();

		$data['content'] = $edit->output;
		$data['head']    = script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head().$script;
		$data['title']   = '<h1>C&oacute;digo Barras de Inventario</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function cmargen(){
		$margen=$this->input->post('margen');
		$codigo=$this->input->post('cod');
		if($margen!==false && $codigo!==false){
			//$codigo=trim($codigo);
			$mSQL='UPDATE sinvpromo SET margen='.$this->db->escape($margen).' WHERE id='.$this->db->escape($codigo);
			//memowrite($mSQL);
			//echo $mSQL;
			$rt=$this->db->simple_query($mSQL);
			echo ($rt)? 1 : 0;
		}
		echo 0;
	}

	function instalar(){
		if (!$this->db->table_exists('sinvpromo')) {
			$mSQL="CREATE TABLE `sinvpromo` (
				`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
				`cliente` CHAR(5) NULL DEFAULT NULL,
				`codigo` VARCHAR(15) NULL DEFAULT NULL,
				`tipo` CHAR(1) NULL DEFAULT NULL,
				`margen` DECIMAL(18,2) NULL DEFAULT NULL,
				`cantidad` DECIMAL(18,3) NULL DEFAULT NULL,
				`fdesde` DATETIME NULL DEFAULT NULL,
				`fhasta` DATETIME NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `codigo` (`codigo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM;";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('sinvpromo');
		if(!in_array('fdesde',$campos)){
			$mSQL="ALTER TABLE `sinvpromo`
			ADD COLUMN `fdesde` DATETIME NULL DEFAULT NULL AFTER `cantidad`,
			ADD COLUMN `fhasta` DATETIME NULL DEFAULT NULL AFTER `fdesde`";
			$this->db->simple_query($mSQL);
		}
	}
}


*/
