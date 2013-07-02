<?php
class Tiket extends Controller {
	var $mModulo='TIKET';
	var $titp='Tickets de Servicio';
	var $tits='Tickets de Servicio';
	var $url ='supervisor/tiket/';

	function Tiket(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_nombre( $modulo, $ventana=0 );
	}

	function index(){
		$this->datasis->modintramenu( 1024, 500, 'supervisor/tiket' );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridIt();
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 120, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		$WestPanel = '
<div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content">
<div class="otros">
<table id="west-grid" align="center">
	<tr>
		<td><div class="tema1"><table id="listados"></table></div></td>
	</tr>
	<tr>
		<td><div class="tema1"><table id="otros"></table></div></td>
	</tr>
</table>

<table id="west-grid" align="center">
	<tr>
		<td colspan="2" style="text-align:center;border-width:1px;border-style:solid;border-color:#CCCCCC;"><spam style="font-size:16px;font-weight:bold;">Prioridades</spam></td>
	</tr>
	<tr>
		<td><div class="tema1"><a style="width:90px" href="#" id="subep">Subir'.img(array('src' => 'images/arrow_up.png', 'alt' => 'Bajar',  'title' => 'Bajar', 'border'=>'0')).'</a></div></td>
		<td><div class="tema1"><a style="width:90px" href="#" id="bajap">Bajar'.img(array('src' => 'images/arrow_down.png',   'alt' => 'Subir',  'title' => 'Subir', 'border'=>'0')).'</a></div></td>
	</tr>
	<tr style="background:#CCCCDD;">
		<td><img src="'.base_url().'images/circulorojo.png" width="20" height="18" border="0" />Muy Alta</td>
		<td><img src="'.base_url().'images/circulonaranja.png" width="20" height="18" border="0" />Alta</td>
	</tr>
	<tr style="background:#CCCCDD;">
		<td><img src="'.base_url().'images/circuloamarillo.png" width="20" height="18" border="0" />Media</td>
		<td><img src="'.base_url().'images/circuloazul.png" width="20" height="18" border="0" />Baja</td>
	</tr>
	<tr style="background:#CCCCDD;">
		<td colspan="2"><img src="'.base_url().'images/circuloverde.png" width="20" height="18" border="0" />Muy Baja</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center;border-width:1px;border-style:solid;border-color:#CCCCCC;"><spam style="font-size:16px;font-weight:bold;">Estados</spam></td>
	</tr>
	<tr>
		<td colspan="2"><div class="tema1"><a style="width:190px" href="#" id="cestado">Cambio de Estado'.img(array('src' => 'images/face-smile.png', 'alt' => 'Estado',  'title' => 'Estado', 'border'=>'0')).'</a></div></td>
	</tr>
	<tr style="background:#CCDDCC;">
		<td><img src="'.base_url().'images/face-crying.png" width="20" height="18" border="0" />Nuevo</td>
		<td><img src="'.base_url().'images/face-tired.png"  width="20" height="18" border="0" />Pendiente</td>
	</tr>
	<tr style="background:#CCDDCC;">
		<td><img src="'.base_url().'images/face-cool.png" width="20" height="18" border="0" />Cerrado</td>
		<td><img src="'.base_url().'images/face-smile.png" width="20" height="18" border="0" />Resuelto</td>
	</tr>
</table>
</div>
</div> <!-- #LeftPane -->
';


		//Panel Central
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar conversacion'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$readyscript = "\tvar master = -1;\n";

		$funciones = '
	function festado(el, val, opts){
		var meco=\'<div><img src="'.base_url().'images/face-devilish.png" width="18" height="18" border="0" /></div>\';
		if ( el == "N" ){
			meco=\'<div><img src="'.base_url().'images/face-crying.png" width="20" height="18" border="0" /></div>\';
		} else if (el == "C") {
			meco=\'<div><img src="'.base_url().'images/face-cool.png" width="20" height="18" border="0" /></div>\';
		} else if (el == "R") {
			meco=\'<div><img src="'.base_url().'images/face-smile.png" width="20" height="20" border="0" /></div>\';
		} else if (el == "P") {
			meco=\'<div><img src="'.base_url().'images/face-tired.png" width="20" height="20" border="0" /></div>\';
		}
		return meco;
	};
	function fprioridad(el, val, opts){
		var meco=\'<div><img src="'.base_url().'images/circuloverde.png" width="20" height="18" border="0" /></div>\';
		if ( el == "5" ){
			meco=\'<div><img src="'.base_url().'images/circulorojo.png" width="20" height="18" border="0" /></div>\';
		} else if (el == "4") {
			meco=\'<div><img src="'.base_url().'images/circulonaranja.png" width="20" height="18" border="0" /></div>\';
		} else if (el == "3") {
			meco=\'<div><img src="'.base_url().'images/circuloamarillo.png" width="20" height="20" border="0" /></div>\';
		} else if (el == "2") {
			meco=\'<div><img src="'.base_url().'images/circuloazul.png" width="20" height="20" border="0" /></div>\';
		} else if (el == "1") {
			meco=\'<div><img src="'.base_url().'images/circuloverde.png" width="20" height="20" border="0" /></div>\';
		}
		return meco;
	}	
';

		$param['WestPanel']  = $WestPanel;
		$param['funciones']  = $funciones;

		$param['readyLayout']  = $readyLayout;
		$param['readyscript']  = $readyscript;

		//$param['EastPanel']  = $EastPanel;
		$param['listados']   = $this->datasis->listados('BCAJ', 'JQ');
		$param['otros']      = $this->datasis->otros('BCAJ', 'JQ');
		//$param['funciones']  = $funciones;

		$param['centerpanel'] = $centerpanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['temas']       = array('proteo','darkness','anexos1');

		//$param['tema']     = 'bootstrap';
		$param['bodyscript'] = $bodyscript;
		$param['tabs']       = false;
		$param['encabeza']   = $this->titp;

		$this->load->view('jqgrid/crud2',$param);
	}


	//******************************************************************
	//Funciones de los Botones
	//
	function bodyscript( $grid0, $grid1 ){
		$bodyscript = '<script type="text/javascript">';

		$bodyscript .= '
		function masterval() { return master; };

		$(function() {
			$( "input:submit, a, button", ".otros" ).button();
		});
		';

		$bodyscript .= '
		$( "#subep" ).click(function() {
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'. $grid0.'").jqGrid(\'getRowData\',id);
				$.get("'.base_url().'supervisor/tiket/subep/"+id,
				function(data){
					alert(data);
					jQuery("#newapi'. $grid0.'").trigger("reloadGrid");
				});
			} else { $.prompt("<h1>Por favor Seleccione un Ticket</h1>");}
		});
		';

		$bodyscript .= '
		$( "#bajap" ).click(function() {
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'. $grid0.'").jqGrid(\'getRowData\',id);
				$.get("'.base_url().'supervisor/tiket/bajap/"+id,
				function(data){
					alert(data);
					jQuery("#newapi'. $grid0.'").trigger("reloadGrid");
				});
			} else { $.prompt("<h1>Por favor Seleccione un Ticket</h1>");}
		});
		';

		$bodyscript .= '
		jQuery("#cestado").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'. $grid1.'").jqGrid(\'getRowData\',id);
				$.prompt(
					"<h1>Cambiar Estado del Tiket "+id+"</h1>",
					{
						buttons: { "Cerrado":1,  "Resuelto":2, "Pendiente":3, "Cancelar": 0 }, focus: 1,
						callback: function(e,v,m,f){
							if (v != 0) {
								$.get("'.base_url().'supervisor/tiket/cestado/"+id+"/"+v,
								function(data){ alert(data); });
							}
						}
					}
				);
			} else { $.prompt("<h1>Por favor Seleccione un Ticket</h1>");}
		});
		';

		$bodyscript .= '
		jQuery("#a1").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'. $grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.base_url().'formatos/ver/TIKET/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});
		';


		$bodyscript .= '
		function tiketadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").dialog({title:"Agregar un nuevo ticket"});
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function tiketedit(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'datadeta/create').'/"+id, function(data){
				$("#fedita").dialog({title:"Agregar resuesta a ticket "+id});
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function tiketshow(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'mostrar').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function tiketdel() {
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
			autoOpen: false, height: 350, width: 600, modal: true,
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/TIKET').'/\'+res.id+\'/id\'').';
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
			autoOpen: false, height: 450, width: 700, modal: true,
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

		return $bodyscript;
	}


	//******************************************************************
	// Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "true";

		$estado    = array("N"=>"Nuevo","P"=>"Pendiente","R"=>"Resueltos","C"=>"Cerrado");
		$prioridad = array("1"=>"Muy Alta","2"=>"Alta","3"=>"Media","4"=>"Baja","5"=>"Muy baja");

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Numero');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 50,
			'editable'      => 'false',
			'search'        => 'false',
			'editoptions'   => '{ readonly: "readonly", size:8 }'
		));

		$grid->addField('estampa');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 130,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

/*
		$grid->addField('actualizado');
		$grid->label('Actualizado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 120,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));
*/

		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 50 }'
		));

		$grid->addField('contenido');
		$grid->label('Contenido');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 400,
			'edittype'      => "'textarea'",
			'editoptions'   => "{ rows:24, cols:80}",
			'formoptions'   => '{ label:"Cont." }'
		));

		$grid->addField('prioridad');
		$grid->label('Prioridad');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"1":"1 Muy Alta","2":" 2 Alta","3":" 3 Media","4":" 4 Baja","5":"5 Muy baja" } }',
			'formatter'     => 'fprioridad'
		));

		$grid->addField('estado');
		$grid->label('Estado');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
			'formatter'     => 'festado'
		));

/*
		$grid->addField('padre');
		$grid->label('Padre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));


		$grid->addField('pertenece');
		$grid->label('Pertenece');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
		));
*/

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('170');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');
		$grid->setOndblClickRow(',ondblClickRow: function(rid){ $(gridId1).jqGrid("viewGridRow", rid, {closeOnEscape:true}); }');

		$grid->setOnSelectRow(' function(id){
				if (id){
					var ret = $("#titulos").getRowData(id);
					jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
					master = id;
				}
			}'
		);

		$grid->setFormOptionsE('
			closeAfterEdit:true, 
			mtype: "POST", 
			width: 620, 
			height:400, 
			closeOnEscape: true, 
			top: 50, 
			left:20, 
			recreateForm:true, 
			afterSubmit: function(a,b){
				if (a.responseText.length > 0) 
					$.prompt(a.responseText); 
					return [true, a ];
				} 
		');

		$grid->setFormOptionsA('
			closeAfterAdd:true,
			mtype: "POST",
			width: 620,
			height:400,
			closeOnEscape: true,
			top: 50,
			left:20,
			recreateForm:true,
			afterSubmit: function(a,b){
				if (a.responseText.length > 0)
					$.prompt(a.responseText);
				return [true, a ];
			}
		');


		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		$grid->setAfterPager(
'		{beforeShowForm: function ($form) {
			$form.css({"max-height":0.50*screen.height+"px","width": "680px"});
			$form.find("td.DataTD").each(function () {
				var $this = $(this), html = $this.html();
				if (html.substr(0, 6) === "&nbsp;") {
					$(this).html(html.substr(6));
				}
				$this.children("span").css({
					overflow: "auto",
					"text-align": "inherit", 
					display: "inline-block",
					"max-height": "350px",
					"width": "590px"
				});
			});
			$form.find("td.CaptionTD").each(function () {
				var $this = $(this), html = $this.html();
				$this.removeAttr("width");
				$this.attr("width","80");
				

			});
			},
			width: 700}');


		#show/hide navigations buttons   td.DataTD
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(false);
		$grid->setSearch(false);
		$grid->setRowNum(20);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: tiketadd, editfunc: tiketedit, delfunc: tiketdel, viewfunc: tiketshow");

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
	function getdata()
	{
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('tiket');
		$mWHERE[] = array( '','padre','S','' );

		$response   = $grid->getData('tiket', array(array()), array(), false, $mWHERE, 'tiket.id desc, tiket.estampa ', 'desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion
	//
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
				$data['usuario'] = $this->secu->usuario();
				$data['estampa'] = date('Y-m-d H:i:s');
				$data['estado']  = 'N';
				$data['padre']   = 'S';
				
				$this->db->insert('tiket', $data);
				echo "Registro Agregado";

				logusu('TIKET',"Registro Numero $id INCLUIDO");
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			//echo "Registro No Modificable";

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM tiket WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM tiket WHERE id=$id ");
				logusu('TIKET',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	// Definicion del Grid y la Forma
	//
	function defgridIt( $deployed = false ){
		$i      = 1;
		$editar = "true";

		$estado    = array("N"=>"Nuevo","P"=>"Pendiente","R"=>"Resueltos","C"=>"Cerrado");
		$prioridad = array("1"=>"Muy Alta","2"=>"Alta","3"=>"Media","4"=>"Baja","5"=>"Muy baja");

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Numero');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 50,
			'hidden'        => 'true',
			'search'        => 'false',
			'editoptions'   => '{ readonly: "readonly", size:8 }'
		));

		$grid->addField('estampa');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 130,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('pertenece');
		$grid->label('Pertenece');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 50,
			'edittype'      => "'text'",
			'hidden'        => "true",
			'editrules'     => "{ edithidden:true,  }",
			'editoptions'   => '{ readonly: "readonly", size:8, value: masterval }'
		));

/*
		$grid->addField('actualizado');
		$grid->label('Actualizado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 90,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));
*/
		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 50 }'
		));

		$grid->addField('contenido');
		$grid->label('Contenido');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 550,
			'edittype'      => "'textarea'",
			'editoptions'   => "{ rows:20, cols:80}",
			'formoptions'   => '{ label:"Cont." }'
		));
/*
		$grid->addField('prioridad');
		$grid->label('Prioridad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
		));

		$grid->addField('estado');
		$grid->label('Estado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));
*/

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('90');
		//$grid->setTitle($this->titp);
		//$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');
		$grid->setOndblClickRow(',ondblClickRow: function(rid){ $(gridId2).jqGrid("viewGridRow", rid, {closeOnEscape:true}); }');

		//$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 680, height:500, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('
			closeAfterAdd:true,
			mtype: "POST",
			width: 680,
			height:500,
			closeOnEscape: true,
			top: 50, left:20,
			recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},
			beforeInitData: function(){
				if ( master > 0 ){
					return true;
				} else {
					$.prompt("<h1>Seleecione un Ticket</h1>");
					return false;
				}
			}
       ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		$grid->setAfterPager(
'		{beforeShowForm: function ($form) {
			$form.css({"max-height":0.50*screen.height+"px","width": "680px"});
			$form.find("td.DataTD").each(function () {
				var $this = $(this), html = $this.html();
				if (html.substr(0, 6) === "&nbsp;") {
					$(this).html(html.substr(6));
				}
				$this.children("span").css({
					overflow: "auto",
					"text-align": "inherit", 
					display: "inline-block",
					"max-height": "350px",
					"width": "590px"
				});
			});
			$form.find("td.CaptionTD").each(function () {
				var $this = $(this), html = $this.html();
				$this.removeAttr("width");
				$this.attr("width","80");
			});
			},
                width: 700}');



		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(false);
		$grid->setDelete(false);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdataIt/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdataIt/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function getdataIt() {
		$id = $this->uri->segment(4);
		if ($id == false ){
			$id = $this->datasis->dameval("SELECT MAX(id) FROM tiket WHERE padre='S' AND estado NOT IN ('C','R')");
		}

		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('tiket');
		$mWHERE[] = array( '','padre','N','' );
		$mWHERE[] = array( '','pertenece',$id,'' );

		$response   = $grid->getData('tiket', array(array()), array(), false, $mWHERE, 'tiket.id', 'desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion
	//
	function setDataIt()
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
				$data['usuario'] = $this->secu->usuario();
				$data['estampa'] = date('Y-m-d H:i:s');
				$data['estado']  = 'N';
				$data['padre']   = 'N';
				$this->db->insert('tiket', $data);
				$id = $this->db->insert_id();

				// Modifica El Padre
				$this->db->where('id', $data['pertenece']);
				$this->db->update('tiket',array("estado"=>"P"));
				echo "Registro Agregado";
				logusu('TIKET',"Registro Numero $id Pertenece ".$data['pertenece']." INCLUIDO");
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			//unset($data['ubica']);
			//$this->db->where('id', $id);
			//$this->db->update('tiket', $data);
			//logusu('TIKET',"Registro ????? MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM tiket WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM tiket WHERE id=$id ");
				logusu('TIKET',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}



	//******************************************************************
	// Baja la Prioridad
	//
	function bajap(){
		$id = $this->uri->segment($this->uri->total_segments());
		if ( $id > 0 ){
			$prioridad  = $this->datasis->dameval("SELECT prioridad FROM tiket WHERE id=$id");
			if ( $prioridad <= 1 ){
				echo "No puede Bajarla del Minimo";
			} else {
				$prioridad = $prioridad - 1;
				$this->db->where('id', $id);
				$this->db->update('tiket', array("prioridad"=>$prioridad));
				echo "Prioridad Disminuida";
			}
		} else {
			echo "Error de seleccion ";
		}
	}


	//******************************************************************
	// Baja la Prioridad
	//
	function subep(){
		$id = $this->uri->segment($this->uri->total_segments());
		if ( $id > 0 ){
			$prioridad  = $this->datasis->dameval("SELECT prioridad FROM tiket WHERE id=$id");
			if ( $prioridad >= 5  ){
				echo "No puede Subirla mas del Maximo";
			} else {
				$prioridad = $prioridad + 1;
				$this->db->where('id', $id);
				$this->db->update('tiket', array("prioridad"=>$prioridad));
				echo "Prioridad Aumentada";
			}
		} else {
			echo "Error de seleccion ";
		}
	}

	//******************************************************************
	// Baja la Prioridad
	//
	function cestado(){
		$id  = $this->uri->segment($this->uri->total_segments()-1);
		$edo = $this->uri->segment($this->uri->total_segments());
		if ( $id > 0 && $edo > 0 ){
			if ( $edo == 1 )
				$estado = "C";
			elseif ( $edo == 2)
				$estado = "R";
			else
				$estado = "P";

				$this->db->where('id', $id);
				$this->db->update('tiket', array("estado"=>$estado));
				echo "Estado Cambiado";
		} else {
			echo "Error de seleccion ";
		}
	}



	//******************************************************************
	// DataEdit
	//
	function dataedit(){

		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit('', 'tiket');

		$edit->on_save_redirect=false;
		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );


		$edit->contenido = new textareaField('Contenido','contenido');
		$edit->contenido->rule = '';
		$edit->contenido->cols = 60;
		$edit->contenido->rows = 11;

		$edit->prioridad = new dropdownField("Prioridad", "prioridad");
		$edit->prioridad->options(array("1"=>"Muy Alta","2"=>"Alta","3"=>"Media","4"=>"Baja","5"=>"Muy baja"));
		$edit->prioridad->insertValue=5;

/*
		$edit->actualizado = new inputField('Actualizado','actualizado');
		$edit->actualizado->rule      = '';
		$edit->actualizado->size      = 10;
		$edit->actualizado->maxlength = 8;

		$edit->estado = new inputField('Estado','estado');
		$edit->estado->rule  = '';
		$edit->estado->size  = 3;
		$edit->estado->maxlength =1;
*/
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		//$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));

		$edit->estado = new autoUpdateField('estado','N', 'N');
		$edit->padre  = new autoUpdateField('padre' ,'S', 'S');

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


	//******************************************************************
	// DataEdit
	//
	function datadeta(){

		$id  = $this->uri->segment($this->uri->total_segments());

		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit('', 'tiket');

		$edit->on_save_redirect=false;
		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$edit->pertenece = new inputField('Pertenece','pertenece');
		$edit->pertenece->rule='integer';
		$edit->pertenece->css_class='inputonlynum';
		$edit->pertenece->size =12;
		$edit->pertenece->maxlength =20;
		$edit->pertenece->insertValue = $id;
		$edit->pertenece->readonly = true;

		$edit->prioridad = new dropdownField("Prioridad", "prioridad");
		$edit->prioridad->options(array("1"=>"Muy Alta","2"=>"Alta","3"=>"Media","4"=>"Baja","5"=>"Muy baja"));
		$edit->prioridad->insertValue=5;

		$edit->contenido = new textareaField('Contenido','contenido');
		$edit->contenido->rule = '';
		$edit->contenido->cols = 60;
		$edit->contenido->rows = 11;

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		//$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));

		$edit->estado = new autoUpdateField('estado','N', 'N');
		$edit->padre  = new autoUpdateField('padre' ,'N', 'N');

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

	//******************************************************************
	// Muestra la conversacion
	//
	function mostrar(){
		$id  = $this->uri->segment($this->uri->total_segments());
		$msalida = '';
		
		$mSQL = 'SELECT date(a.estampa) fecha, time(a.estampa) hora , a.usuario, b.us_nombre, a.contenido FROM tiket a JOIN usuario b ON a.usuario=b.us_codigo WHERE a.id='.$id ;
		$trae = $this->datasis->damereg($mSQL);

		//$msalida .= '<h2>Planteamiento</h2>';
		$msalida .= '<table width="98%" align="center" cellspacing="0" cellpadding="0">';
		
		$msalida .= '<tr style="font-size:1.5em;background:#AAAAAA;border-bottom:1px solid;"><td>Fecha: '.$trae['fecha'].'</td><td>Hora: '.$trae['hora'].'</td><td> Usuario: '.$trae['us_nombre'].'</td></tr>';
		$contenido = str_replace("\n","<br>",$trae['contenido']);
		$msalida .= '<tr><td colspan="3">'.$contenido.'</tdtd></tr>';

		$mSQL = 'SELECT date(a.estampa) fecha, time(a.estampa) hora , a.usuario, b.us_nombre, a.contenido FROM tiket a JOIN usuario b ON a.usuario=b.us_codigo WHERE a.pertenece='.$id.' ORDER BY estampa';

		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$msalida .= '<tr style="font-size:1.5em;background:#AAAAAA;border-bottom:1px solid;"><td>Fecha: '.$row->fecha.'</td><td>Hora: '.$row->hora.'</td><td> Usuario: '.$row->us_nombre.'</td></tr>';
				$contenido = str_replace("\n","<br>",$row->contenido );
				$msalida .= '<tr><td colspan="3">'.$contenido.'</tdtd></tr>';
			}
		}
		$msalida .= '</table>';
		
		echo $msalida;
	
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
		if (!$this->db->table_exists('tiket')) {
			$mSQL="CREATE TABLE `tiket` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `padre` char(1) DEFAULT NULL,
			  `pertenece` bigint(20) unsigned DEFAULT NULL,
			  `prioridad` smallint(5) unsigned DEFAULT NULL,
			  `usuario` varchar(50) DEFAULT NULL,
			  `contenido` text,
			  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  `actualizado` timestamp NULL DEFAULT NULL,
			  `estado` char(1) DEFAULT 'N',
			  PRIMARY KEY (`id`),
			  KEY `id` (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
	}

}

/*
class Tiket extends Controller {

	var $estado;
	var $prioridad;
	var $modulo;

	function Tiket(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->load->library("menues");
		$this->modulo=908;
		$this->estado=array(
		"N"=>"Nuevo",
		"P"=>"Pendiente",
		"R"=>"Resueltos",
		"C"=>"Cerrado");

		$this->prioridad=array(
		 "1"=>"Muy Alta",
     "2"=>"Alta",
     "3"=>"Media",
     "4"=>"Baja",
     "5"=>"Muy baja");
	}

	function index(){
		redirect("supervisor/tiket/filteredgrid");
	}

	function filteredgrid(){
		$this->datasis->modulo_id($this->modulo,1);
		$this->rapyd->load("datafilter","datagrid");
 		$modbus=array(
			'tabla'   =>'usuario',
			'columnas'=>array(
				'us_codigo' =>'C&oacute;digo',
				'us_nombre'=>'Nombre'),
			'filtro'  =>array('us_nombre'=>'nombre'),
			'retornar'=>array('us_codigo'=>'usuario'),
			'titulo'  =>'Buscar Usuario');

		$filter = new DataFilter("Filtro de Tikets");

		$filter->id = new inputField("N&uacute;mero", "id");
		$filter->id->size=11;

		$filter->estampa = new dateonlyField("Fecha", "estampa");
		$filter->estampa->clause  ="where";
		$filter->estampa->operator="=";
		//$filter->estampa->insertValue = date("Y-m-d");

		$filter->estado = new dropdownField("Estado", "estado");
		$filter->estado->option("","Todos");
		$filter->estado->options($this->estado);

		$filter->prioridad = new dropdownField("Prioridad", "prioridad");
		$filter->prioridad->option("","Todos");
		$filter->prioridad->options($this->prioridad);

		$filter->usuario = new inputField("C&oacute;digo de usuario", "usuario");
		$filter->usuario->size=11;
		$filter->usuario->append($this->datasis->modbus($modbus));

		$filter->contenido = new inputField("Contenido", "contenido");
		//$filter->contenido->clause ="likesensitive";
		//$filter->contenido->append("Sencible a las Mayusc&uacute;las");

		$filter->buttons("reset","search");

		$select=array("usuario","contenido","prioridad","IF(estado='N','Nuevo',IF(estado='R','Resuelto',IF(estado='P','Pendiente','Cerrado')))as estado","estampa","id","actualizado");		
		$filter->db->select($select);
		$filter->db->from('tiket');
		$filter->db->orderby('estampa','desc');
		$filter->db->where('padre',"S");
		$filter->build();

		$grid = new DataGrid("Lista de Tikets");
		$grid->per_page = 10;
		$link=anchor("supervisor/tiket/ver/<#id#>", "<#id#>");

		$grid->column("N&uacute;mero",$link);
		$grid->column("Fecha de Ingreso","<dbdate_to_human><#estampa#>|d/m/Y h:m:s</dbdate_to_human>");
		$grid->column_orderby("Actualizado","<dbdate_to_human><#actualizado#>|d/m/Y h:m:s</dbdate_to_human>","actualizado");
		//$grid->column("Actualizado","<dbdate_to_human><#actualizado#>|d/m/Y h:m:s</dbdate_to_human>");
		$grid->column("Usuario","usuario");
		$grid->column("Contenido","contenido");
		$grid->column("Prioridad","prioridad");
		$grid->column("Estado","estado");

		$grid->add("supervisor/tiket/dataedit/create");
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] = $grid->output;
		$data['filtro']  = $filter->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   = '<h1>Control de Tikets</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$parametros = $this->uri->uri_to_assoc(4);
		$this->datasis->modulo_id($this->modulo,1);
		$this->rapyd->load("dataedit");

		$edit = new DataEdit("Tiket", "tiket");
		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('delete','_pre_del');
		$edit->post_process("insert","_post_insert");
		$edit->post_process("update","_post_update");
		$edit->post_process("delete","_post_del");

		$edit->contenido = new textareaField("Contenido", "contenido");
		$edit->contenido->rule = "required";
		$edit->contenido->rows = 6;
		$edit->contenido->cols = 90;

		$edit->padre = new inputField(" ", "padre");
		$edit->padre->style='display: none;';
		$edit->padre->type='hidden';
		$edit->padre->when= array("create");

		if(!array_key_exists('pertenece',$parametros)) {

			//$edit->back_url = site_url("supervisor/tiket/filteredgrid");
			$edit->back_uri="supervisor/tiket/filteredgrid";
			$edit->padre->insertValue='S';

			$edit->prioridad = new dropdownField("Prioridad", "prioridad");
			$edit->prioridad->options($this->prioridad);
			$edit->prioridad->insertValue=5;

			$edit->estado = new inputField(" ", "estado");
			$edit->estado->style='display: none;';
			$edit->estado->type='hidden';
			$edit->estado->when= array("create");
			$edit->estado->insertValue='N';
		}else{
			//$edit->back_url = site_url("supervisor/tiket/ver/").$parametros['pertenece'];
			$edit->back_uri="supervisor/tiket/ver/".$parametros['pertenece'];
			$edit->padre->insertValue='N';

			$edit->pertenece = new inputField(" ", "pertenece");
			$edit->pertenece->style='display: none;';
			$edit->pertenece->type='hidden';
			$edit->pertenece->when= array("create");
			$edit->pertenece->insertValue=$parametros['pertenece'];
		}

		$edit->buttons("modify", "save", "undo", "delete",'back');
		$edit->build();

		$data['content'] =$edit->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   ='<h1>Crear Tiket</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function estapriori($status,$id=NULL){
		$this->rapyd->load("dataedit");
		$this->datasis->modulo_id($this->modulo,1);

		$edit = new DataEdit("Tiket", "tiket");
		$edit->post_process("update","_post_update");
		$edit->back_url = site_url("supervisor/tiket/ver/$id");

		$edit->prioridad = new dropdownField("Prioridad", "prioridad");
		$edit->prioridad->options($this->prioridad);

		$edit->estado = new dropdownField("Estado", "estado");
		$edit->estado->options($this->estado);

		$edit->buttons("modify", "save", "undo", "back");
		$edit->build();

		$data['content'] =$edit->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   ='<h1>Cambiar estado o prioridad</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function ver($id=NULL){
		$this->datasis->modulo_id($this->modulo,1);
		if(empty($id)) redirect("supervisor/tiket/filteredgrid");
		$this->rapyd->load("datatable");
		$query = $this->db->query("SELECT prioridad,estado FROM tiket WHERE $id=$id");
		$estado=$prioridad='';
		if ($query->num_rows() > 0){
			$row = $query->row();
			$prioridad = $row->prioridad;
			$estado    = $row->estado;
		}
		$link=($this->datasis->puede(908001))? anchor('/supervisor/tiket/dataedit/delete/<#id#>','borrar'):'';

		$table = new DataTable(null);
		$table->cell_attributes = 'style="vertical-align:middle;"';
		$select=array("usuario","contenido","prioridad","estado","estampa","id","padre","pertenece");

		$table->db->select($select);
		//$table->db->select("usuario,contenido,prioridad,estado,estampa,id,padre,pertenece");
		$table->db->from("tiket");
		//$table->db->where("id",$id or 'pertenece',$id);
		$table->db->where('id',$id);
		$table->db->or_where('pertenece',$id);
		$table->db->orderby("id");
		$this->db->_escape_char='';
		$this->db->_protect_identifiers=false;

		$table->per_row  = 1;
		$table->per_page = 50;
		$table->cell_template = "<div class='marco1' ><#contenido#><br><b class='mininegro'>&nbsp;<dbdate_to_human><#estampa#>|d/m/Y h:m:s</dbdate_to_human> Usuario: <#usuario#> $link</b></div><br>";
		$table->build();
		//echo $table->db->last_query();

		$prop=array('type'=>'button','value'=>'Agregar','name'=>'mas'  ,'onclick' => "javascript:window.location='".site_url("supervisor/tiket/dataedit/pertenece/$id/create")."'");
		$form=form_input($prop);

		$prop2=array('type'=>'button','value'=>'Cambiar estado o prioridad','name'=>'mas'  ,'onclick' => "javascript:window.location='".site_url("supervisor/tiket/estapriori/modify/$id")."'");
		$form2=form_input($prop2);

		$prop3=array('type'=>'button','value'=>'Regresar','name'=>'mas'  ,'onclick' => "javascript:window.location='".site_url("supervisor/tiket/filteredgrid")."'");
		$form3=form_input($prop3);

		$data['content']  = "<br>Prioridad: <b>".$this->prioridad[$prioridad]."</b>, Estado: <b>".$this->estado[$estado]."</b><br>";
		$data['content'] .= $table->output.$form.$form2.$form3;
		$data["head"]     = $this->rapyd->get_head().style('marcos.css').style('estilos.css');
		$data['title']    = "<h1>Tiket N&uacute;mero: $id</h1> ";
		$this->load->view('view_ventanas', $data);
	}

	function _post_insert($do) {
		$pertenece=$do->get('pertenece');
		$mSQL="UPDATE tiket SET estado='P', actualizado=NOW() WHERE id=$pertenece";
		$this->db->simple_query($mSQL);
	}

	function _post_update($do) {
		$pertenece=$do->get('pertenece');
		if(empty($pertenece)) $pertenece=$do->get('id');
		$mSQL="UPDATE tiket SET actualizado=NOW() WHERE id=$pertenece";
		$this->db->simple_query($mSQL);
	}

	function _pre_del($do) {
		$retorno=$this->datasis->puede(908001);
		return $retorno;
	}

	function _pre_insert($do) {
		$do->set('usuario', $this->session->userdata('usuario'));
	}

	function _post_del($do){
		$numero=$do->get('id');
		$sql = "DELETE FROM tiket WHERE pertenece=$numero";
		$this->db->query($sql);
	}

	function traertiket($codigoc=null){
		//$this->datasis->modulo_id($this->modulo,1);
		//$this->load->helper('url');
		if(empty($codigoc)){
			$where='';
		}else{
			$where="WHERE cliente=".$this->db->escape($codigoc);
		}
		$mSQL="SELECT cliente,url,sistema,id FROM tiketconec ".$where;
		$host=$this->db->query($mSQL);
		foreach($host->result() as  $row){
			
			if(!empty($row->sistema)) $ruta=trim_slashes($row->sistema.'/rpcserver'); else $ruta='rpcserver';
			if(!empty($row->phtml))   $url=trim_slashes($row->url).':'.$row->phtml ; else $url=trim_slashes($row->url);
			$sucursal=$row->id;
			$cliente=$row->cliente;
			
			$server_url =$url.'/'.reduce_double_slashes($ruta);

			//$server_url = site_url('rpcserver');
			echo '<pre>'."\n";
			echo '('.$row->cliente.')-'.$server_url."\n";

			$fechad=$this->datasis->dameval('SELECT MAX(a.estampa) FROM tiketc AS a JOIN tiketconec AS b  ON a.sucursal=b.id  WHERE b.cliente='.$this->db->escape($cliente));
			if(empty($fechad)) $fechad=date('Ymd');
			echo $this->_traeticketrpc($server_url,array($fechad),$row->id);
			echo '</pre>'."\n";
		}
			$link=anchor("supervisor/conec/filteredgrid", "Regresar a Información de Conexión");
			echo $link."\n";
	}


	function traer(){
		$this->datasis->modulo_id($this->modulo,1);
		//$this->datasis->modulo_id(11D,1);
		$this->rapyd->load("dataform","datatable",'datagrid');
		$this->load->library('table');

		$scli=array(
		'tabla'   =>'scli',
		'columnas'=>array(
		'cliente' =>'C&oacute;digo Cliente',
		'nombre'  =>'Nombre',
		'contacto'=>'Contacto'),
		'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'cliente'),
		'titulo'  =>'Buscar Cliente');

		$boton=$this->datasis->modbus($scli);

		$filter = new DataForm('supervisor/tiketrpc/tiket/process');
		$filter->title('Filtro de fecha');

		$filter->cliente = new inputField("Cliente", "cliente");
		$filter->cliente->size = 15;
		$filter->cliente->append($boton);

		//$filter->button("btnsubmit", "Consultar", '', $position="BL");
		$filter->button("btnsubmit", "Traer Tikets", form2uri(site_url("/supervisor/tiket/traertiket"),array('cliente')), $position="BL");//
		//$filter->button("btnsubmit", "Traer Tikets", form2uri(site_url("/supervisor/tiket/traertiket"), $position="BL");//
		$filter->build_form();

		$data=array();
		$mSQL="SELECT a.id,a.cliente,a.ubicacion,a.url,a.basededato,a.puerto,a.usuario,a.clave,a.observacion, b.nombre FROM tiketconec AS a JOIN scli AS b ON a.cliente=b.cliente WHERE url REGEXP '^([[:alnum:]]+\.{0,1})+$' ORDER BY id";

		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			foreach ($query->result_array() as $row){
				$data[]=$row;
			}
		}
		$grid = new DataGrid("Clientes",$data);

		$grid->column("Cliente"    , '<b><#nombre#></b>');
		$grid->column("URL"        , 'url');

		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Traer tikets de clientes</h1>";
		$data["head"]    = $this->rapyd->get_head().script("jquery.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}

	function _traeticketrpc($server_url,$parametros,$sucursal='N/A'){
		$this->load->library('xmlrpc');
		$this->xmlrpc->xmlrpc_defencoding=$this->config->item('charset');

		$this->xmlrpc->server($server_url, 80);
		$this->xmlrpc->method('ttiket');

		$request = $parametros;
		$this->xmlrpc->request($request);

		$error=0;
		if (!$this->xmlrpc->send_request()){
			$rt=$this->xmlrpc->display_error();
		}else{
			$respuesta=$this->xmlrpc->display_response();
			foreach($respuesta AS $res){
				$arr=unserialize($res);
				foreach($arr AS $i=>$val)
				    $arr[$i]=base64_decode($val);
				$arr['idt']       =$arr['id'];
				$arr['sucursal']  =$sucursal;
				//$arr['asignacion']='KATHI';
				unset($arr['id']);

				$mSQL = $this->db->insert_string('tiketc', $arr);
				$rt=$this->db->simple_query($mSQL);
				if($rt===FALSE){ $error++; memowrite($mSQL,'tiketc');}
			}
			if($error==0) $rt="<b style='color:green;'>Transferencia Correcta</b>"; else $rt="<b style='color:red;'>Hubo algunos problemas en la insercion se genero un centinela</b>";
		}
		return $rt;
	}

	function instalar(){
		$mSQL="CREATE TABLE `tiket` (
		  `id` bigint(20) unsigned NOT NULL auto_increment,
		  `padre` char(1) default NULL,
		  `pertenece` bigint(20) unsigned default NULL,
		  `prioridad` smallint(5) unsigned default NULL,
		  `usuario` varchar(50) default NULL,
		  `contenido` text,
		  `estampa` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  `actualizado` timestamp NULL default NULL,
		  `estado` char(1) default 'N',
		  PRIMARY KEY  (`id`),
		  KEY `id` (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1";
		$this->db->simple_query($mSQL);
	}
}

*/
?>
