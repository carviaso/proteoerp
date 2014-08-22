<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Noco extends Controller {
	var $mModulo='NOCO';
	var $titp='Contratos de Nomina';
	var $tits='Contratos de Nomina';
	var $url ='nomina/noco/';

	function Noco(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_nombre('NOCO',1);
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu(900,700,'nomina/noco');
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$param['grids'][] = $grid1->deploy();

		$grid2   = $this->defgridcon();
		$param['grids'][] = $grid2->deploy();


		$readyLayout = '
		$(\'body\').layout({
			minSize: 30,
			north__size: 60,
			resizerClass: \'ui-state-default\',
			west__size: 212,
			west__onresize: function (pane, $Pane){jQuery("#west-grid").jqGrid(\'setGridWidth\',$Pane.innerWidth()-2);},
		});

		$(\'div.ui-layout-center\').layout({
			minSize: 30,
			resizerClass: "ui-state-default",
			center__paneSelector: ".centro-centro",
			south__paneSelector:  ".centro-sur",
			south__size: 260,
			center__onresize: function (pane, $Pane) {
				jQuery("#newapi'.$param['grids'][0]['gridname'].'").jqGrid(\'setGridWidth\',$Pane.innerWidth()-6);
				jQuery("#newapi'.$param['grids'][0]['gridname'].'").jqGrid(\'setGridHeight\',$Pane.innerHeight()-100);
			}
		});';


		$grid0=$param['grids'][0]['gridname'];
		$bodyscript = '
		<script type="text/javascript">
		var idnoco = 0;
		$(function() {
			var grid = jQuery("#newapi'.$grid0.'");
			$( "input:submit, a, button", ".boton1" ).button();

			jQuery("#boton1").click( function(){
				var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
				if (id)	{
					var ret = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getRowData\',id);
					window.open(\''.site_url('formatos/descargar/NOCO').'/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
				} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
			});

			$("#fedita").dialog({
				autoOpen: false, height: 350, width: 600, modal: true,
				buttons: {
					"Guardar": function() {
						var bValid = true;
						var murl = $("#df1").attr("action");
						$.ajax({
							type: "POST", dataType: "html", async: false,
							url: murl,
							data: $("#df1").serialize(),
							success: function(r,s,x){
								try{
									var json = JSON.parse(r);
									if (json.status == "A"){
										$.prompt("Registro Guardado");
										$( "#fedita" ).dialog( "close" );
										grid.trigger("reloadGrid");
										return true;
									} else {
										$.prompt(json.mensaje);
									}
								}catch(e){
									$("#fedita").html(r);
								}
							}
						})
					},
					"Cancelar": function() {
						$("#fedita").html("");
						$(this).dialog( "close" );
					}
				},
				close: function() {
					$("#fedita").html("");
				}
			});

			$("#fshow").dialog({
				autoOpen: false, height: 300, width: 600, modal: true,
				buttons: {
					"Aceptar": function() {
						$("#fshow").html("");
						$( this ).dialog( "close" );
					},
				},
				close: function() {
					$("#fshow").html("");
				}
			});

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
			});

		});

		function nocoadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		}

		function nocoedit(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		}

		function nocoshow(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/show').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		}

		function nocodel(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						try{
							var json = JSON.parse(data);
							if (json.status == "A"){
								$.prompt("Registro eliminado");
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
							}else{
								$.prompt("Registro no se puede eliminado");
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
		}
		</script>';

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		$WestPanel = '
		<div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content">
			<div class="anexos">
			<table id="west-grid" align="center">
				<tr>
					<td><div class="tema1"><table id="listados"></table></div></td>
				</tr><tr>
					<td><div class="tema1"><table id="otros"></table></div></td>
				</tr>
			</table>
			</div>
			<table id="west-grid" align="center">
				<tr>
					<td><div class="tema1 boton1"><a style="width:190px" href="#" id="boton1">Imprimir Contrato '.img(array('src' => 'assets/default/images/print.png', 'alt' => 'Formato PDF',  'title' => 'Imprimir contrato', 'border'=>'0')).'</a></div></td>
				</tr>
			</table>
			<br><br>
			<br><br>
			<br><br>
			<b>Ayuda:</b>
			<hr>
			Para <b>AGREGAR</b> un concepto al contrato, seleccione uno y haga doble click sobre un <b>"Conceptos Disponibles"</b>.
			<br><hr>
			Para <b>ELIMINAR</b> un concepto del contrato, seleccione uno y haga doble click sobre un <b>"Concepto del Contrato</b>".
			<hr>
			<div id="concdescrip"  style="background-color:#E4E4E4;border:1px solid black;" >
			<p style="text-align:center;">Seleccione algun concepto para ver detalles</p>
			</div>
		</div>

		<!-- #LeftPane -->';

		$centerpanel = '
		<div id="RightPane" class="ui-layout-center">
			<div class="centro-centro">
				<table id="newapi'.$param['grids'][0]['gridname'].'"></table>
				<div id="pnewapi'.$param['grids'][0]['gridname'].'"></div>
			</div>
			<div class="centro-sur" id="adicional" style="overflow:auto;">
			<table width="100%"><tr>
				<td><table id="newapi'.$param['grids'][1]['gridname'].'"></table></td>
				<td><table id="newapi'.$param['grids'][2]['gridname'].'"></table></td>
			</tr></table>
			</div>
		</div> <!-- #RightPane -->';


		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);


		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('NOCO', 'JQ');
		$param['otros']        = $this->datasis->otros('NOCO', 'JQ');

		$param['centerpanel']  = $centerpanel;
		//$param['funciones']    = $funciones;

		$param['temas']        = array('proteo','darkness','anexos1');

		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;

		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'true';

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('tipo');
		$grid->label('Freq.');
		$grid->params(array(
			'align'         => "'center'",
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"S":"Semanal", "Q":"Quincenal", "B":"Bisemanal", "M":"Mensual" }, style:"width:170px"} ',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ label: "Frecuencia" }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('observa1');
		$grid->label('Observaciones 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('observa2');
		$grid->label('Observaciones 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Modificado" }'
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
		$grid->setHeight('220');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
				if (id){
					idnoco = id;
					jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
				}
			}');
		$grid->setOndblClickRow('');


		//$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		//$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 480, height:220, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 480, height:220, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit('$.prompt("Respuesta:"+a.responseText); return [true, a ];');

		#show/hide navigations buttons
		$grid->setBarOptions('addfunc: nocoadd, editfunc: nocoedit, delfunc: nocodel, viewfunc: nocoshow');


		#show/hide navigations buttons
		//$grid->setOndblClickRow('');
		$grid->setAdd(    $this->datasis->sidapuede('NOCO','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('NOCO','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('NOCO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('NOCO','BUSQUEDA%'));

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
		$grid = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('noco');

		$response = $grid->getData('noco', array(array()), array(), false, $mWHERE, 'codigo' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = intval($this->input->post('id'));
		if($id>0){
			return '';
		}
		$dbid   = $this->db->escape($id);
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$codigo = $data['codigo'];
				$msql = "SELECT COUNT(0) FROM noco WHERE codigo=".$this->db->escape($codigo);
				if ($this->datasis->dameval($msql) == 0 ) {
					$this->db->insert('noco', $data);
					echo 'Contrato Agregado';
					logusu('NOCO','Contrato de Nomina '.$codigo.' INCLUIDO');
				} else {
					echo 'Codigo ya existe';
				}
			} else
			echo 'Fallo Agregado!!!';

		}elseif($oper == 'edit') {
			$codigo = $data['codigo'];
			unset($data['codigo']);
			$this->db->where('id', $id);
			$this->db->update('noco', $data);
			logusu('NOCO','Contrato de Nomina '.$codigo.' MODIFICADO');
			echo 'Contrato Modificado';

		}elseif($oper == 'del'){
			$codigo = $this->datasis->dameval("SELECT codigo FROM noco WHERE id=${dbid}");
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM nomina WHERE trabaja=".$this->db->escape($codigo)." or contrato=".$this->db->escape($codigo));
			if ($check > 0){
				echo 'El registro no puede ser eliminado; tiene movimiento';
			} else {
				$this->db->simple_query("DELETE FROM noco   WHERE id=${dbid}");
				$this->db->simple_query("DELETE FROM itnoco WHERE codigo=".$this->db->escape($codigo) );
				logusu('NOCO','Contrato de Nomina '.$codigo.' ELIMINADO');
				echo 'Contrato Eliminado';
			}
		}
	}


	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('concepto');
		$grid->label('Cod.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 35,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 4 }',
		));


		$grid->addField('descrip');
		$grid->label('Concepto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 35 }',
		));


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 35,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false',
			'hidden'        => 'true'
		));

		$grid->showpager(true);
		$grid->setWidth('310');
		$grid->setHeight('195');
		$grid->setTitle('Conceptos del Contrato');
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
				if(id>0){
					$.ajax({
						url: "'.site_url($this->url.'dconc').'/"+id+"/it",
						success: function(msg){
							$("#concdescrip").html(msg);
						}
					});
				}
			}');

		$grid->setOndblClickRow('
			,ondblClickRow: function(id){
				if(idnoco<=0){
					$.prompt("Debe seleccionar un contrato primero");
				}else{
					if(id>0){
						$.prompt( "Eliminar Concepto del Contrato? ",{
							buttons: { Eliminar:true, Cancelar:false},
							submit: function(e,v,m,f){
								if (v == true) {
									$.get("'.base_url().$this->url.'elimina/"+id,
									function(data){
										//alert(data);
										jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+idnoco+"/", page:1});
										jQuery(gridId2).trigger("reloadGrid");
									});
								}
							}
						});
					}else{
						$.prompt("Concepto no seleccionado");
					}
				}
			}');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		#Set url
		//$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdatait/'));

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
		$grid  = $this->jqdatagrid;
		$id    = intval($this->uri->segment(4));
		if($id == false){
			$id = $this->datasis->dameval("SELECT MIN(id) FROM noco");
		}
		$dbid   = $this->db->escape($id);
		$codigo = $this->datasis->dameval("SELECT codigo FROM noco WHERE id=${dbid}");

		$orderby= 'ORDER BY concepto';
		$sidx=$this->input->post('sidx');
		if($sidx){
			$campos = $this->db->list_fields('itnoco');
			if(in_array($sidx,$campos)){
				$sidx = trim($sidx);
				$sord   = $this->input->post('sord');
				$orderby="ORDER BY `${sidx}` ".(($sord=='asc')? 'ASC':'DESC');
			}
		}

		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM itnoco WHERE codigo=".$this->db->escape($codigo)."  ${orderby}";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;

	}

	/**
	* Elimina Concepto a Contrato
	*/
	function elimina(){
		$id     = $this->uri->segment(4);
		$idnoco = $this->uri->segment(5);
		$dbid   = $this->db->escape($id);
		$msql = "DELETE FROM itnoco WHERE id=${dbid}";
		$this->db->query($msql);
		$rs = 'Concepto eliminado del Contrato';
		//logusu('noco',"Contrato de Nomina ${codigo} MODIFICADO");
		echo $rs;
	}



	//******************************************************************
	//Grid de conceptos disponibles
	//
	function defgridcon( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('concepto');
		$grid->label('C&oacute;d.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 35,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 4 }',
		));


		$grid->addField('descrip');
		$grid->label('Concepto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 35 }',
		));


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 35,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false',
			'hidden'        => 'true'
		));


		$grid->showpager(true);
		$grid->setWidth('310');
		$grid->setHeight('195');
		$grid->setTitle('Conceptos Disponibles');
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		$grid->setOndblClickRow('
			,ondblClickRow: function(id){
				if(idnoco<=0){
					$.prompt("Debe seleccionar un contrato primero");
				}else{

					if (id>0){
						$.prompt( "Agregar Concepto al Contrato? ",{
							buttons: { Agregar:1, Cancelar:0 },
							submit: function(e,v,m,f){
								if ( v == 1) {
									$.get("'.base_url().$this->url.'agrega/"+id+"/"+idnoco,
									function(data){
										//alert(data);
										jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+idnoco+"/", page:1});
										jQuery(gridId2).trigger("reloadGrid");
									});
								}
							}
						});
					}else{
						$.prompt("Concepto no seleccionado");
					}
				}
			}');

		$grid->setonSelectRow('
			function(id){
				if(id>0){
					$.ajax({
						url: "'.site_url($this->url.'dconc').'/"+id,
						success: function(msg){
							$("#concdescrip").html(msg);
						}
					});
				}
			}
		');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);

		$grid->setRowNum(300);
		$grid->setShrinkToFit('false');

		#GET url
		$grid->setUrlget(site_url($this->url.'getdatacon/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	function dconc($id,$c='con'){
		session_write_close();
		$id  = intval($id);
		if($id>0){
			if($c=='it'){
				$mSQL= "SELECT a.concepto,a.descrip,a.aplica,a.formula,a.tipod,a.tipoa,a.ctade,a.ctaac
					FROM conc AS a
					JOIN itnoco AS b ON a.concepto=b.concepto
					WHERE b.id=${id}";
			}else{
				$mSQL= "SELECT concepto,descrip,aplica,formula,tipod,tipoa,ctade,ctaac FROM conc WHERE id=${id}";
			}
			$row = $this->datasis->damerow($mSQL);
			if(!empty($row)){
				echo '<p>';
				echo '<b>C&oacute;digo:</b> '.trim($row['concepto']).' - '.trim($row['descrip']).br();
				echo '<b>Formula:</b> '.trim($row['formula']).br();
				echo '<b>Deudor:</b>'.$row['tipod'].' '.trim($row['ctade']).br();
				echo '<b>Acreedor:</b>'.$row['tipoa'].' '.trim($row['ctaac']).br();
				echo '</p>';
			}
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdatacon(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('conc');

		$response   = $grid->getData('conc', array(array()), array(), false, $mWHERE, 'concepto' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}


	/**
	* Agrega Concepto a Contrato
	*/
	function agrega(){
		$id      = $this->uri->segment(4);
		$idnoco  = $this->uri->segment(5);
		$dbid    = $this->db->escape($id);
		$dbidnoco= $this->db->escape($idnoco);

		$codigo   = $this->datasis->dameval("SELECT codigo   FROM noco WHERE id=${dbidnoco}");
		if(empty($codigo)){
			echo 'Contrato no encontrado';
			return '';
		}
		$concepto = $this->datasis->dameval("SELECT concepto FROM conc WHERE id=${dbid}");
		if(empty($concepto)){
			echo 'Concepto no encontrado';
			return '';
		}

		$msql = 'SELECT COUNT(*) AS cana FROM itnoco WHERE codigo='.$this->db->escape($codigo).' AND concepto='.$this->db->escape($concepto);
		$cana = $this->datasis->dameval($msql);

		if(empty($cana)){
			$msql  = 'INSERT IGNORE INTO itnoco ( codigo, concepto, descrip, tipo, grupo )
				SELECT '.$this->db->escape($codigo).' codigo, concepto, descrip, tipo, grupo
				FROM conc WHERE concepto='.$this->db->escape($concepto);
			$this->db->query($msql);
			$rs = 'Concepto '.$concepto.' agregado al Contrato';
		}else{
			$rs = 'El Contrato ya contienen ese Concepto '.$concepto;
		}
		echo $rs;
	}


	function dataedit(){
 		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});';

		$edit = new DataEdit($this->tits, 'noco');
		//$edit->script($script,'modify');
		//$edit->script($script,'create');
		$edit->on_save_redirect=false;

		//$edit->pre_process('insert' ,'_pre_insert');
		//$edit->pre_process('update' ,'_pre_update');
		$edit->pre_process( 'delete','_pre_delete');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo = new inputField('C&oacute;digo', 'codigo');
		$edit->codigo->size = 10;
		$edit->codigo->rule= 'required|alpha_numeric';
		$edit->codigo->mode= 'autohide';
		$edit->codigo->maxlength=8;

		$edit->nombre  = new inputField('Nombre', 'nombre');
		$edit->nombre->maxlength=40;
		$edit->nombre->rule='required';
		$edit->nombre->size = 30;

		$edit->tipo = new dropdownField('Tipo', 'tipo');
		$edit->tipo->style='width:110px';
		$edit->tipo->option('','Seleccionar');
		$edit->tipo->option('S','Semanal');
		$edit->tipo->option('Q','Quincenal');
		$edit->tipo->option('B','Bisemanal');
		$edit->tipo->option('M','Mensual');
		$edit->tipo->option('O','Otro');
		$edit->tipo->rule='required|enum[S,Q,M,O]';

		$edit->observa1  = new inputField('Observaciones', 'observa1');
		$edit->observa1->maxlength=60;
		$edit->observa1->size = 50;

		$edit->observa2  = new inputField('', 'observa2');
		$edit->observa2->maxlength=60;
		$edit->observa2->size = 50;

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

	function _post_insert($do){
		$codigo=$do->get('codigo');
		logusu('noco',"Contrato de Nomina ${codigo} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('codigo');
		logusu('noco',"Contrato de Nomina ${codigo} MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('codigo');
		logusu('noco',"Contrato de Nomina ${codigo} ELIMINADO");
	}

	function _pre_delete($do){
		$codigo  =$do->get('codigo');
		$dbcodigo=$this->db->escape($codigo);
		$check =  0;
		$check += $this->datasis->dameval("SELECT COUNT(*) AS cana FROM nomina WHERE contrato=${dbcodigo}");
		$check += $this->datasis->dameval("SELECT COUNT(*) AS cana FROM prenom WHERE contrato=${dbcodigo}");

		if(empty($check)){
			return true;
		}

		$do->error_message_ar['pre_del']='No se puede eliminar el contrato por estar relacionado con algunas nominas';
		return false;
	}

	function instalar(){
		$campos=$this->db->list_fields('noco');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE noco DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE noco ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE noco ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		$campos=$this->db->list_fields('itnoco');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE itnoco DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE itnoco ADD UNIQUE INDEX codigo (codigo, concepto)');
			$this->db->simple_query('ALTER TABLE itnoco ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		$campos=$this->db->list_fields('conc');
		if(!in_array('id',$campos)){
			$this->db->query('ALTER TABLE conc DROP PRIMARY KEY');
			$this->db->query('ALTER TABLE conc ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->query('ALTER TABLE conc ADD UNIQUE INDEX concepto (concepto)');
		}
	}
}
