<?php require_once(BASEPATH.'application/controllers/validaciones.php');
class Pers extends Controller {
	var $mModulo='PERS';
	var $titp='Personal Trabajador';
	var $tits='Personal Trabajador';
	var $url ='nomina/pers/';

	function Pers(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'PERS', $ventana=0 );
	}

	function index(){
		if ( !$this->datasis->iscampo('pers','id') ) {
			$this->db->simple_query('ALTER TABLE pers DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE pers ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE pers ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
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
		$grid->wbotonadd(array("id"=>"a1", "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Imprimir"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
		array("id"=>"fedita",  "title"=>"Agregar/Editar Registro")
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']  = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['SouthPanel'] = $SouthPanel;
		$param['listados']   = $this->datasis->listados('PERS', 'JQ');
		$param['otros']      = $this->datasis->otros('PERS', 'JQ');
		$param['temas']      = array('proteo','darkness','anexos1');
		$param['bodyscript'] = $bodyscript;
		$param['tabs']       = false;
		$param['encabeza']   = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}


	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		jQuery("#a1").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.base_url().'formatos/ver/PERS/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});';

		$fvari = '"<h1>Variables del Trabajador</h1>Cliente: <b>"+ret.nombre+"</b><br><br><table align=center><tr><td>';
		$fvari .= $this->datasis->traevalor('NOMVARI1').':</td><td> <input type=\'text\' id=\'xvari1\' name=\'xvari1\' size=\'6\' maxlength=\'5\' value=\""+ret.vari1+"\"></td></tr><tr><td>';
		$fvari .= $this->datasis->traevalor('NOMVARI2').':</td><td> <input type=\'text\' id=\'xvari2\' name=\'xvari2\' size=\'6\' maxlength=\'5\' value=\""+ret.vari2+"\"></td></tr><tr><td>';
		$fvari .= $this->datasis->traevalor('NOMVARI3').':</td><td> <input type=\'text\' id=\'xvari3\' name=\'xvari3\' size=\'6\' maxlength=\'5\' value=\""+ret.vari3+"\"></td></tr><tr><td>';
		$fvari .= $this->datasis->traevalor('NOMVARI4').':</td><td> <input type=\'text\' id=\'xvari4\' name=\'xvari4\' size=\'6\' maxlength=\'5\' value=\""+ret.vari4+"\"></td></tr><tr><td>';
		$fvari .= $this->datasis->traevalor('NOMVARI5').':</td><td> <input type=\'text\' id=\'xvari5\' name=\'xvari5\' size=\'6\' maxlength=\'5\' value=\""+ret.vari5+"\"></td></tr><tr><td>';
		$fvari .= $this->datasis->traevalor('NOMVARI6').':</td><td> <input type=\'text\' id=\'xvari6\' name=\'xvari6\' size=\'6\' maxlength=\'5\' value=\""+ret.vari6+"\"></td></tr></table>"';

		$bodyscript .= '
		function variables(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var mnuevo = "";
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				$.prompt('.$fvari.',{
					buttons: { Cambiar:true, Salir:false},
					callback: function(e,v,m,f){
						mvari1 = f.xvari1;
						mvari2 = f.xvari2;
						mvari3 = f.xvari3;
						mvari4 = f.xvari4;
						mvari5 = f.xvari5;
						mvari6 = f.xvari6;
						if (v) {
							$.ajax({
								url: "'.site_url('nomina/pers/variables').'",
								global: false,
								type: "POST",
								data: ({ mid: id, xvari1 : encodeURIComponent(mvari1), xvari2 : encodeURIComponent(mvari2), xvari3 : encodeURIComponent(mvari3), xvari4 : encodeURIComponent(mvari4), xvari5 : encodeURIComponent(mvari5), xvari6 : encodeURIComponent(mvari6) }),
								dataType: "text",
								async: false,
								success: function(sino) {
									apprise(sino);
									jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
								},
								error: function(h,t,e) { apprise("Error..codigo="+yurl+" ",e) }
							});
						}
					}
				});
			} else
				$.prompt("<h1>Por favor Seleccione un Cliente</h1>");
		}
		';

		$bodyscript .= '
		function persadd() {
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function persedit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
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
						try{
							var json = JSON.parse(r);
							if (json.status == "A"){
								apprise("Registro Guardado");
								$( "#fedita" ).dialog( "close" );
								grid.trigger("reloadGrid");
								'.$this->datasis->jwinopen(site_url('formatos/ver/PERS').'/\'+res.id+\'/id\'').';
								return true;
							} else {
								apprise(json.mensaje);
							}
						}catch(e){
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
		$linea = 1;

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 15 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('nacional');
		$grid->label('Nac.');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{value: {"V":"Venezolana","E":"Extranjera","P":"Pasaporte" }, style:"width:120px" }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label: "Nacionalidad" }'
		));

		$grid->addField('apellido');
		$grid->label('Apellido');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('cedula');
		$grid->label('Cedula');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label: "C.I./Pasaporte" }'
		));

		$grid->addField('direc1');
		$grid->label('Direccion 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));


		$linea = $linea + 1;
		$grid->addField('tipo');
		$grid->label('Frec.');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"Q":"Quincenal","S":"Semanal","B":"Bisemanal","M":"Mensual","O":"Otro" }, style:"width:120px" }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label: "Frecuencia" }'
		));

		$grid->addField('direc2');
		$grid->label('Direccion 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));


		$linea = $linea + 1;
		$grid->addField('status');
		$grid->label('Status');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"A":"Activo","V":"Vacaciones","I":"Inactivo","R":"Retirado","P":"Permiso" }, style:"width:120px" }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label: "Frecuencia" }'
		));


		$grid->addField('direc3');
		$grid->label('Direccion 3');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));


		$linea = $linea + 1;
		$grid->addField('nacimi');
		$grid->label('Nacio');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label:"Nacimiento" }'
		));

		$grid->addField('telefono');
		$grid->label('Telefono');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));


/*
		$grid->addField('sso');
		$grid->label('SSO');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 110,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 11 }',
		));
*/

		$linea = $linea + 1;
		$grid->addField('sexo');
		$grid->label('Sexo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"F":"Femenino","M":"Masculino","O":"Otro" }, style:"width:120px" }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label: "Sexo" }'
		));

		$mSQL  = "SELECT division, CONCAT(division, ' ', descrip) descrip FROM divi ORDER BY division ";
		$adivi = $this->datasis->llenajqselect($mSQL, false );

		$grid->addField('divi');
		$grid->label('Divi');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$adivi.',  style:"width:250px"}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2, label: "Division" }'
		));

		$linea = $linea + 1;
		$grid->addField('civil');
		$grid->label('Edo.Civil');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"S":"Soltero","C":"Casado","D":"Divorciado","V":"Viudo" }, style:"width:120px" }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label: "Estado Civil" }'
		));

		$mSQL  = "SELECT departa, CONCAT(departa, ' ', depadesc) descrip FROM depa ORDER BY departa ";
		$adepa = $this->datasis->llenajqselect($mSQL, false );

		$grid->addField('depto');
		$grid->label('Depto.');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$adepa.',  style:"width:250px"}',
			'stype'         => "'text'",
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2, label:"Departamento" }'
		));

		$mSQL  = "SELECT cargo, CONCAT(cargo, ' ', descrip) descrip FROM carg ORDER BY cargo ";
		$acargo = $this->datasis->llenajqselect($mSQL, false );

		$linea = $linea + 1;
		$grid->addField('ingreso');
		$grid->label('Ingreso');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label:"Fecha Ingreso" }'
		));

		$grid->addField('cargo');
		$grid->label('Cargo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$acargo.',  style:"width:250px"}',
			'stype'         => "'text'",
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2, label:"Cargo" }'
		));


		$linea = $linea + 1;
		$grid->addField('sueldo');
		$grid->label('Sueldo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label:"Sueldo" }'
		));

		$mSQL  = "SELECT codigo, profesion FROM prof ORDER BY profesion ";
		$aprof = $this->datasis->llenajqselect($mSQL, false );
		$grid->addField('profes');
		$grid->label('Profes');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$aprof.',  style:"width:250px"}',
			'stype'         => "'text'",
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2, label:"Profesion" }'
		));

		$linea = $linea + 1;
		$grid->addField('niveled');
		$grid->label('Nivel F.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"01":"Primaria","02":"Bachillerato","03":"Tecnico Medio","04":"T.S.U.","05":"Universitario","06":"PHD/Master" }, style:"width:150px" }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label: "Formacion" }'
		));


		$grid->addField('cuenta');
		$grid->label('Cuenta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:15, maxlength: 20 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2, label: "Cta. Bancaria" }'
		));

/*
		$grid->addField('retiro');
		$grid->label('Retiro');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Retiro" }'
		));
*/

		$linea = $linea + 1;
		$grid->addField('dialib');
		$grid->label('Dialib');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:3, maxlength: 2 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label:"Dias Libres" }'
		));


		$mSQL  = "SELECT codigo, CONCAT(codigo, ' ', nombre) nombre FROM noco ORDER BY codigo ";
		$anoco = $this->datasis->llenajqselect($mSQL, false );
		$grid->addField('contrato');
		$grid->label('Contrato');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$anoco.',  style:"width:250px"}',
			'stype'         => "'text'",
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2, label:"Contrato" }'
		));



/*
		$grid->addField('banco');
		$grid->label('Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 15 }',
		));


		$grid->addField('cutipo');
		$grid->label('Cutipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));
*/

		$grid->addField('vari1');
		$grid->label('Vari1');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:false }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('vari2');
		$grid->label('Vari2');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:false }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('vari3');
		$grid->label('Vari3');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:false }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('vari4');
		$grid->label('Vari4');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:false }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));


		$grid->addField('vari5');
		$grid->label('Vari5');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:false, date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('vari6');
		$grid->label('Vari6');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:false }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

/*
		$grid->addField('uaumento');
		$grid->label('Uaumento');
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


		$grid->addField('formato');
		$grid->label('Formato');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));


		$grid->addField('dialab');
		$grid->label('Dialab');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:3, maxlength: 2 }',
		));


		$grid->addField('xdialab');
		$grid->label('Xdialab');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
		));


		$grid->addField('sucursal');
		$grid->label('Sucursal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
		));
		$grid->addField('carnet');
		$grid->label('Carnet');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 10 }',
		));
*/

		$linea = $linea + 1;
		$grid->addField('enlace');
		$grid->label('Enlace');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label:"Enlace" }'
		));


		$grid->addField('vence');
		$grid->label('Vence');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:false,date:true}',
			'formoptions'   => '{ label:"Vencimiento" }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2, label:"Vencimiento" }'
		));



/*
		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));

		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));

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

		$grid->addField('cuentab');
		$grid->label('Cuentab');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 20 }',
		));

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
			'hidden'        => 'true',
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));

/*
		$grid->addField('horario');
		$grid->label('Horario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 4 }',
		));
*/

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('380');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 700, height:450, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 700, height:450, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('PERS','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('PERS','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('PERS','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('PERS','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("\t\taddfunc: persadd,\n\t\teditfunc: persedit");

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
		$mWHERE = $grid->geneTopWhere('pers');

		$response   = $grid->getData('pers', array(array()), array(), false, $mWHERE );
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
		$mcodp  = "codigo";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM pers WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('pers', $data);
					echo "Registro Agregado";
					logusu('PERS',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM pers WHERE id=$id");
			//if ( $nuevo <> $anterior ){
			//	//si no son iguales borra el que existe y cambia
			//	$this->db->query("DELETE FROM pers WHERE $mcodp=?", array($mcodp));
			//	$this->db->query("UPDATE pers SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
			//	$this->db->where("id", $id);
			//	$this->db->update("pers", $data);
			//	logusu('PERS',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
			//	echo "Grupo Cambiado/Fusionado en clientes";
			//} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('pers', $data);
				logusu('PERS',"Personal  ".$nuevo." MODIFICADO");
				echo "$nuevo Modificado";
			//}

		} elseif($oper == 'del') {
			$codigo = $this->datasis->dameval("SELECT $mcodp FROM pers WHERE id=$id");
			$check  = $this->datasis->dameval("SELECT COUNT(*) FROM nomina WHERE codigo=".$this->db->escape($codigo));
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM pers WHERE id=$id ");
				logusu('PERS',"Registro $codigo ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function variables() {
		$id      = $_REQUEST['mid'];
		$mvari1  = $_REQUEST['xvari1'];
		$mvari2  = $_REQUEST['xvari2'];
		$mvari3  = $_REQUEST['xvari3'];
		$mvari4  = $_REQUEST['xvari4'];
		$mvari5  = $_REQUEST['xvari5'];
		$mvari6  = $_REQUEST['xvari6'];

		$this->db->where("id", $id);
		$this->db->update('pers', array( "vari1"=>$mvari1, "vari2"=>$mvari2, "vari3"=>$mvari3, "vari4"=>$mvari4, "vari5"=>$mvari5, "vari6"=>$mvari6 ));

		echo "Trabajador Avtualizado";
		//ELIMINAR DE SCLI
	}




	//******************************
	//
	//  DataEdit
	//
	function dataedit(){

		$this->rapyd->load("dataedit");
		$consulrif=$this->datasis->traevalor('CONSULRIF');

		$script ='
		function consulrif(){
				vrif=$("#rif").val();
				if(vrif.length==0){
					alert("Debe introducir primero un RIF");
				}else{
					vrif=vrif.toUpperCase();
					$("#rif").val(vrif);
					window.open("'.$consulrif.'"+"?p_rif="+vrif,"CONSULRIF","height=350,width=410");
				}
		}
		';

		$edit = new DataEdit("Personal", 'pers');
		$edit->on_save_redirect=false;
		//$edit->back_url = site_url("nomina/pers/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");

		$edit->pre_process('delete','_pre_del');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$sucu=array(
		'tabla'   =>'sucu',
		'columnas'=>array(
		'codigo'  =>'C&oacute;digo de Sucursal',
		'sucursal'=>'Sucursal'),
		'filtro'  =>array('codigo'=>'C&oacute;digo de Sucursal','sucursal'=>'Sucursal'),
		'retornar'=>array('codigo'=>'sucursal'),
		'titulo'  =>'Buscar Sucursal');

		$boton=$this->datasis->modbus($sucu);

		$cargo=array(
		'tabla'   =>'carg',
		'columnas'=>array(
			'cargo'  =>'C&oacute;digo de Cargo',
			'descrip'=>'Descripci&oacute;n'
		),
		'filtro'  =>array('codigo'=>'C&oacute;digo de Cargo','descrip'=>'Descripcion'),
		'retornar'=>array('cargo'=>'cargo'),
		'titulo'  =>'Buscar Cargo');

		$boton1=$this->datasis->modbus($cargo);

		$scli=array(
		'tabla'   =>'scli',
		'columnas'=>array(
		'cliente' =>'C&oacute;digo Cliente',
		'nombre'  =>'Nombre',
		'rifci'   =>'Rif/CI',
		'contacto'=>'Contacto'),
		'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre','rifci'=>'Rif/CI'),
		'retornar'=>array('cliente'=>'enlace'),
		'titulo'  =>'Buscar Empleado');

		$cboton=$this->datasis->modbus($scli);

		$edit->codigo =  new inputField("C&oacute;digo", "codigo");
		$edit->codigo->rule="trim|alpha_numeric|required|callback_chexiste";
		$edit->codigo->mode="autohide";
		$edit->codigo->maxlength=15;
		$edit->codigo->size=10;

		$edit->nacional = new dropdownField("C&eacute;dula", "nacional");
		$edit->nacional->style = "width:100px;";
		$edit->nacional->option("V","Venezolano");
		$edit->nacional->option("E","Extranjero");
		$edit->nacional->group = "Datos del Trabajador";

		$edit->tipo = new dropdownField("Frecuencia","tipo");
		$edit->tipo->options(array("Q"=> "Quincenal","M"=>"Mensual","S"=>"Semanal","B"=>"BiSemanal"));
		$edit->tipo->group = "Relaci&oacute;n Laboral";
		$edit->tipo->style = "width:90px;";

		$edit->cedula =  new inputField("", "cedula");
		$edit->cedula->size = 9;
		$edit->cedula->maxlength= 8;
		$edit->cedula->in = "nacional";
		$edit->cedula->rule="trim|required";
		$edit->cedula->css_class='inputnum';

		//$lriffis='<a href="javascript:consulrif();" title="Consultar RIF en el SENIAT" onclick="">SENIAT</a>';
		//$edit->rif->mode="autohide";
		//$edit->rif->append($lriffis);

		$edit->rif =  new inputField("RIF", "rif");
		$edit->rif->rule      = "trim|strtoupper|callback_chrif";
		$edit->rif->maxlength = 13;
		$edit->rif->size      = 12;
		$edit->rif->group     = "Datos del Trabajador";

		$edit->nombre =  new inputField("Nombre", "nombre");
		$edit->nombre->group = "Datos del Trabajador";
		$edit->nombre->size = 30;
		$edit->nombre->maxlength=30;
		$edit->nombre->rule="trim|required|strtoupper";

		$edit->apellido = new inputField("Apellidos", "apellido");
		$edit->apellido->group = "Datos del Trabajador";
		$edit->apellido->size = 30;
		$edit->apellido->maxlength=30;
		//$edit->apellido->in = "nombre";
		$edit->apellido->rule="trim|required|strtoupper";

		$edit->sexo = new dropdownField("Sexo", "sexo");
		$edit->sexo->style = "width:100px;";
		$edit->sexo->option("F","Femenino");
		$edit->sexo->option("M","Masculino");
		$edit->sexo->group = "Datos del Trabajador";

		//$edit->label1 = new freeField("EC","EC","<id class='littletableheader'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Estado Civil&nbsp;&nbsp; </id>");
		//$edit->label1->in = "sexo";

		$edit->civil = new dropdownField("Estado Civil", "civil");
		$edit->civil->style = "width:100px;";
		$edit->civil->option("S","Soltero");
		$edit->civil->option("C","Casado");
		$edit->civil->option("D","Divorciado");
		$edit->civil->option("V","Viudo");
		$edit->civil->group = "Datos del Trabajador";
		//$edit->civil->in = "sexo";

		$edit->direc1 = new inputField("Direcci&oacute;n", "direc1");
		$edit->direc1->group = "Datos del Trabajador";
		$edit->direc1->size =30;
		$edit->direc1->maxlength=30;
		$edit->direc1->rule="trim|strtoupper";

		$edit->direc2 = new inputField("&nbsp;", "direc2");
		$edit->direc2->size =30;
		$edit->direc2->group = "Datos del Trabajador";
		$edit->direc2->maxlength=30;
		$edit->direc2->rule="trim|strtoupper";

		$edit->direc3 = new inputField("&nbsp;", "direc3");
		$edit->direc3->size =30;
		$edit->direc3->group = "Datos del Trabajador";
		$edit->direc3->maxlength=30;
		$edit->direc3->rule="trim|strtoupper";

		$edit->telefono = new inputField("Tel&eacute;fono", "telefono");
		$edit->telefono->size =30;
		$edit->telefono->group = "Datos del Trabajador";
		$edit->telefono->maxlength=30;
		$edit->telefono->rule="trim|strtoupper";

		$edit->email = new inputField("Email","email");
		$edit->email->size =30;
		$edit->email->group = "Datos del Trabajador";
		$edit->email->maxlength=50;
		$edit->email->rule="trim";


		//$edit->posicion = new dropdownField("Tipo de Escritura" ,"escritura");
		//$edit->posicion->option("","");
		//$edit->posicion->options("SELECT codigo,posicion FROM posicion  ORDER BY codigo");
		//$edit->posicion->group = "Datos del Trabajador";
		//$edit->posicion->rule="trim|strtoupper";
		//$edit->posicion->style ="width:170px;";


		$edit->civil = new dropdownField("Estado Civil", "civil");
		$edit->civil->style = "width:80px;";
		$edit->civil->option("S","Soltero");
		$edit->civil->option("C","Casado");
		$edit->civil->option("D","Divorciado");
		$edit->civil->option("V","Viudo");
		$edit->civil->group = "Datos del Trabajador";

		$edit->profes = new dropdownField("Profesion","profes");
		$edit->profes->options("SELECT codigo,profesion FROM prof ORDER BY profesion");
		$edit->profes->style = "width:200px;";

		$edit->nacimi = new DateonlyField("Nacimiento", "nacimi","d/m/Y");
		//$edit->nacimi->insertValue = date('Y-m-d');
		$edit->nacimi->size = 10;
		$edit->nacimi->rule ='required';
		$edit->nacimi->calendar=false;

/*

		$edit->nacimi->size = 12;
		$edit->nacimi->group = "Datos del Trabajador";
		$edit->nacimi->rule="trim|chfecha";
*/

		$edit->sucursal = new dropdownField("Sucursal", "sucursal");
		$edit->sucursal->style ="width:120px;";
		$edit->sucursal->options("SELECT codigo, CONCAT(codigo,' ',sucursal) desrip FROM sucu ORDER BY sucursal");
		//$edit->sucursal->size =4;
		//$edit->sucursal->maxlength=2;
		$edit->sucursal->group = "Relaci&oacute;n Laboral";
		//$edit->sucursal->append($boton);
		//$edit->sucursal->rule="trim|strtoupper";

		$edit->divi = new dropdownField("Divisi&oacute;n", "divi");
		$edit->divi->style ="width:200px;";
		$edit->divi->option("","");
		$edit->divi->options("SELECT division,descrip FROM divi ORDER BY division");
		$edit->divi->onchange = "get_depto();";
		$edit->divi->group = "Relaci&oacute;n Laboral";

		$edit->depa = new dropdownField("Departamento", "depto");
		$edit->depa->style ="width:200px;";
		$edit->depa->option("","");

		//if($edit->_status=='modify' || $edit->_status=='show' ){
		$divi=$edit->getval('divi');
			if($divi!==FALSE){
				$edit->depa->options("SELECT departa,depadesc FROM depa where division='$divi' ORDER BY division");
			}else{
				$edit->depa->option("","Seleccione un Division");
			}
		//}
		$edit->depa->group = "Relaci&oacute;n Laboral";

		$edit->contrato = new dropdownField("Contrato","contrato");
		$edit->contrato->style ="width:350px;";
		$edit->contrato->option("","");
		$edit->contrato->options("SELECT codigo,CONCAT('',codigo,nombre)as nombre FROM noco ORDER BY codigo");
		//$edit->contrato->group = "Relaci&oacute;n Laboral";

		$edit->vencimiento = new DateonlyField('Vencimiento', 'vence','d/m/Y');
		$edit->vencimiento->size = 10;
		$edit->vencimiento->group = 'Relaci&oacute;n Laboral';
		$edit->vencimiento->rule  = 'trim|chfecha';
		$edit->vencimiento->calendar = false;

		$edit->cargo = new dropdownField('Cargo', 'cargo');
		$edit->cargo->style = 'width:200px;';
		$edit->cargo->group = 'Relaci&oacute;n Laboral';
		$edit->cargo->options("SELECT cargo, CONCAT(descrip, ' ', cargo) descrip FROM carg ORDER BY cargo");

		$edit->enlace = new inputField('Enlace','enlace');
		$edit->enlace->size =11;
		$edit->enlace->maxlength=5;
		$edit->enlace->group = 'Relaci&oacute;n Laboral';
		$edit->enlace->append($cboton);
		$edit->enlace->rule='trim|strtoupper|existesinv';

		$edit->sso = new inputField('Nro. SSO', 'sso');
		$edit->sso->size =13;
		$edit->sso->maxlength=11;
		$edit->sso->group = 'Relaci&oacute;n Laboral';
		//$edit->sso->rule="trim|numeric";
		$edit->sso->css_class='inputnum';

		$edit->observa = new textareaField("Observaci&oacute;n", "observa");
		$edit->observa->rule = "trim";
		$edit->observa->cols = 70;
		$edit->observa->rows =3;
		$edit->observa->group = "Relaci&oacute;n Laboral";

		$edit->ingreso = new DateonlyField("Fecha de Ingreso", "ingreso","d/m/Y");
		$edit->ingreso->size = 10;
		$edit->ingreso->group = "Relaci&oacute;n Laboral";
		$edit->ingreso->rule="trim|chfecha";
		$edit->ingreso->calendar = false;

		$edit->label2 = new freeField("Edo. C","edoci","<id class='littletableheader'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha de Retiro&nbsp;&nbsp; </id>");
		$edit->label2->in = "ingreso";

		$edit->retiro =  new DateonlyField("Fecha de Retiro", "retiro","d/m/Y");
		$edit->retiro->size = 10;
		$edit->retiro->in = "ingreso";
		$edit->retiro->rule="trim|chfecha";
		$edit->retiro->calendar = false;

		//$edit->trabaja = new dropdownField("Tipo de Trabajador","tipot");
		//$edit->trabaja->option("","");
		//$edit->trabaja->options("SELECT codigo,tipo  FROM tipot ORDER BY codigo");
		//$edit->trabaja->group = "Relaci&oacute;n Laboral";
		//$edit->trabaja->style = "width:200px;";

		$edit->dialib = new inputField("Dias libres", "dialib");
		$edit->dialib->group = "Relaci&oacute;n Laboral";
		$edit->dialib->size =4;
		$edit->dialib->maxlength=2;
		$edit->dialib->rule="trim|numeric";
		$edit->dialib->css_class='inputnum';

		$edit->label3 = new freeField("DL","DL","<id class='littletableheader'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dias Laborables&nbsp;&nbsp; </id>");
		$edit->label3->in = "dialib";

		$edit->dialab =  new inputField("Dias laborables", "dialab");
		$edit->dialab->group = "Relaci&oacute;n Laboral";
		$edit->dialab->size =4;
		$edit->dialab->maxlength=2;
		$edit->dialab->in = "dialib";
		//$edit->dialab->rule="trim|numeric";
		//$edit->dialab->css_class='inputnum';

		$edit->status = new dropdownField("Estatus", "status");
		//$edit->status->option("","");
		$edit->status->options(array("A"=> "Activo","V"=>"Vacaciones","R"=>"Retirado","I"=>"Inactivo","P"=>"Permiso"));
		$edit->status->group = "Relaci&oacute;n Laboral";
		$edit->status->style = "width:100px;";

		$edit->carnet =  new inputField("Nro. Carnet", "carnet");
		$edit->carnet->size = 13;
		$edit->carnet->maxlength=10;
		$edit->carnet->group = "Relaci&oacute;n Laboral";
		$edit->carnet->rule="trim";

		$edit->turno = new dropdownField("Turno", "turno");
		$edit->turno->option("","");
		$edit->turno->options(array("D"=> "Diurno","N"=>"Nocturno"));
		$edit->turno->group = "Relaci&oacute;n Laboral";
		$edit->turno->style = "width:100px;";

		$edit->horame  = new inputField("Turno Ma�ana","horame");
		$edit->horame->maxlength=8;
		$edit->horame->size=10;
		$edit->horame->rule='trim|callback_chhora';
		$edit->horame->append('hh:mm:ss');
		$edit->horame->group="Relaci&oacute;n Laboral";

		$edit->horams  = new inputField("Turno Ma�ana","horams");
		$edit->horams->maxlength=8;
		$edit->horams->size=10;
		$edit->horams->rule='trim|callback_chhora';
		$edit->horams->append('hh:mm:ss');
		$edit->horams->in="horame";
		$edit->horams->group="Relaci&oacute;n Laboral";

		$edit->horate  = new inputField("Turno Tarde","horate");
		$edit->horate->maxlength=8;
		$edit->horate->size=10;
		$edit->horate->rule='trim|callback_chhora';
		$edit->horate->append('hh:mm:ss');
		$edit->horate->group="Relaci&oacute;n Laboral";

		$edit->horats  = new inputField("Turno Tarde","horats");
		$edit->horats->maxlength=8;
		$edit->horats->size=10;
		$edit->horats->rule='trim|callback_chhora';
		$edit->horats->append('hh:mm:ss');
		$edit->horats->in="horate";
		$edit->horats->group="Relaci&oacute;n Laboral";

		$edit->sueldo = new inputField("Sueldo","sueldo");
		$edit->sueldo->group = "Relaci&oacute;n Laboral";
		$edit->sueldo->size =10;
		$edit->sueldo->maxlength=15;
		$edit->sueldo->rule="trim|numeric";
		$edit->sueldo->css_class='inputnum';

		$edit->tipocuent = new dropdownField("Tipo Cuenta", "tipoe");
		$edit->tipocuent->option('','');
		$edit->tipocuent->options(array('A'=> 'Ahorro','C'=>'Corriente'));
		$edit->tipocuent->group = 'Datos Cuenta Bancaria';
		$edit->tipocuent->style = 'width:100px;';

		$edit->cuentab = new inputField('Nro. Cuenta', 'cuentab');
		$edit->cuentab->group = 'Datos Cuenta Bancaria';
		$edit->cuentab->size =20;
		$edit->cuentab->maxlength=40;


		$vari1 = $this->datasis->traevalor('NOMVARI1');
		$vari2 = $this->datasis->traevalor('NOMVARI2');
		$vari3 = $this->datasis->traevalor('NOMVARI3');
		$vari4 = $this->datasis->traevalor('NOMVARI4');
		$vari5 = $this->datasis->traevalor('NOMVARI5');
		$vari6 = $this->datasis->traevalor('NOMVARI6');

		$edit->vari1 = new inputField($vari1, "vari1");
		$edit->vari1->group = "Variables";
		$edit->vari1->size =10;
		$edit->vari1->maxlength=14;
		$edit->vari1->rule="trim|numeric";
		$edit->vari1->css_class='inputnum';

		$edit->vari2 = new inputField($vari2, "vari2");
		$edit->vari2->group = "Variables";
		$edit->vari2->size =10;
		$edit->vari2->maxlength=14;
		$edit->vari2->rule="trim|numeric";
		$edit->vari2->css_class='inputnum';

		$edit->vari3 = new inputField($vari3, "vari3");
		$edit->vari3->group = "Variables";
		$edit->vari3->size =10;
		$edit->vari3->maxlength=14;
		$edit->vari3->rule="trim|numeric";
		$edit->vari3->css_class='inputnum';

		$edit->vari4 = new inputField($vari4, "vari4");
		$edit->vari4->group = "Variables";
		$edit->vari4->size =10;
		$edit->vari4->maxlength=11;
		$edit->vari4->rule="trim|numeric";
		$edit->vari4->css_class='inputnum';

		$edit->vari5 = new DateField($vari5, "vari5");
		$edit->vari5->group = "Variables";
		$edit->vari5->size =10;
		$edit->vari5->maxlength=12;
		$edit->vari5->rule="trim|chfecha";

		$edit->vari6 = new inputField($vari6, "vari6");
		$edit->vari6->group = "Variables";
		$edit->vari6->size =10;
		$edit->vari6->maxlength=14;
		$edit->vari6->rule="trim|numeric";
		$edit->vari6->css_class='inputnum';

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form']  =& $edit;
			$this->load->view('view_pers', $conten);
		}

		//$data['content'] = $this->load->view('view_pers', $conten,true);
		//$data['content'] = $edit->output;
		//$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		//$data['title']   = '<h1>Personal</h1>';
		//$this->load->view('view_ventanas', $data);

	}

	function depto($divi=NULL){
		$this->rapyd->load('fields');
		$depa = new dropdownField('Departamento', 'depto');
		$depa->status = 'modify';
		$depa->style  = 'width:200px;';
		//echo 'de nuevo:'.$tipoa;
		if ($divi!==false){
			$depa->options("SELECT departa,depadesc FROM depa where division='$divi' ORDER BY division");
		}else{
			$depa->option('Seleccione un Division');
		}
		$depa->build();
		echo $depa->output;
	}

	function _pre_del($do) {
		$codigo=$do->get('codigo');
		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM nomina WHERE codigo='$codigo'");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM asig   WHERE codigo='$codigo'");

		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Trabajador con Movimiento no puede ser Borrado';
			return False;
		}
		return True;
	}

	function _post_insert($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		logusu('pers',"PERSONAL $codigo NOMBRE  $nombre CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		logusu('pers',"PERSONAL $codigo NOMBRE  $nombre  MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		logusu('pers',"PERSONAL $codigo NOMBRE  $nombre  ELIMINADO ");
	}

	function chexiste($codigo){
		$codigo = $this->input->post('codigo');
		$check  = $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE codigo='$codigo'");
		if ($check > 0){
			$nombre=$this->datasis->dameval("SELECT nombre FROM pers WHERE codigo='$codigo'");
			$this->validation->set_message('chexiste',"Personal con el codigo $codigo nombre $nombre ya existe");
			return FALSE;
		}else {
			return TRUE;
		}
	}

	function instalar(){
		if ( !$this->datasis->iscampo('pers','email') )
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `email` VARCHAR(100) NULL");

		if ( !$this->datasis->iscampo('pers','tipoe') )
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `tipoe` VARCHAR(10)");

		if ( !$this->datasis->iscampo('pers','escritura') )
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `escritura` VARCHAR(25)");

		if ( !$this->datasis->iscampo('pers','rif') )
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `rif` VARCHAR(15)");

		if ( !$this->datasis->iscampo('pers','observa') )
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `observa` TEXT ");

		if ( !$this->datasis->iscampo('pers','turno') )
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `turno` CHAR(2) NULL");

		if ( !$this->datasis->iscampo('pers','horame') )
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `horame` VARCHAR(10)");

		if ( !$this->datasis->iscampo('pers','horams') )
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `horams` VARCHAR(10)");

		if ( !$this->datasis->iscampo('pers','horate') )
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `horate` VARCHAR(10)");

		if ( !$this->datasis->iscampo('pers','horats') )
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `horats` VARCHAR(10)");

		if ( !$this->datasis->iscampo('pers','modificado') )
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `modificado` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER vence");

		if ( !$this->datasis->iscampo('pers','id') )
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `id` INT(11) NULL AUTO_INCREMENT AFTER modificado, DROP PRIMARY KEY, ADD PRIMARY (id), ADD UNIQUE INDEX codigo (codigo)");

		if ( !$this->datasis->istabla('tipot') )
			$this->db->simple_query("CREATE TABLE tipot (codigo int(10) unsigned NOT NULL AUTO_INCREMENT,tipo varchar(50) DEFAULT NULL,PRIMARY KEY (codigo) )");

		if ( !$this->datasis->istabla('posicion') )
			$this->db->simple_query("CREATE TABLE `posicion`(`codigo` varchar(10) NOT NULL,`posicion` varchar(30) DEFAULT NULL,PRIMARY KEY (`codigo`))");

		if ( !$this->datasis->istabla('posicion') )
			$this->db->simple_query("CREATE TABLE tipoe (codigo varchar(10) NOT NULL DEFAULT '', tipo varchar(50) DEFAULT NULL, PRIMARY KEY (codigo))");

		if ( !$this->datasis->istabla('nedu') ){
			$this->db->simple_query("CREATE TABLE IF NOT EXISTS nedu (codigo varchar(4) NOT NULL, nivel varchar(40) DEFAULT NULL, PRIMARY KEY (`codigo`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC");
			$this->db->simple_query("INSERT INTO nedu (codigo, nivel) VALUES ('00', 'Sin Educacion Formal'),('01', 'Primaria'),('02', 'Secundaria'),('03', 'Tecnico'),	('04', 'T.S.U.'),('05', 'Universitario'),('06', 'Post Universitario'),('07', 'Doctor'),('08', 'Guru')");
		}
	}

/*

	function modificar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];

		$codigo = $data['data']['codigo'];
		$nombre = trim($data['data']['nombre']).' '.$data['data']['apellido'];;

		unset($campos['nomcont']);
		unset($campos['codigo']);

		//print_r($campos);
		$mSQL = $this->db->update_string("pers", $campos,"id='".$data['data']['id']."'" );
		$this->db->simple_query($mSQL);
		logusu('pers',"PERSONAL $codigo NOMBRE  $nombre MODIFICADO");
		echo "{ success: true, message: 'Trabajador Modificado'}";
	}

	function eliminar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];

		$codigo = $data['data']['codigo'];
		$nombre = trim($data['data']['nombre']).' '.$data['data']['apellido'];;

		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM nomina WHERE codigo='$codigo'");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM asig   WHERE codigo='$codigo'");

		if ($check > 0){
			echo "{ success: false, message: 'Trabajador con Movimiento no puede ser Borrado'}";
		} else {
			$this->db->simple_query("DELETE FROM pers WHERE codigo='$codigo'");
			logusu('pers',"PERSONAL $codigo NOMBRE  $nombre ELIMINADO");
			echo "{ success: true, message: 'Trabajador Eliminado'}";
		}
	}

*/
	//Busca Trabajadores
	function persbusca() {
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']  :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']  : 15;
		$codigo  = isset($_REQUEST['codigo']) ? $_REQUEST['codigo'] : '';
		$semilla = isset($_REQUEST['query'])  ? $_REQUEST['query']  : '';

		$semilla = trim($semilla);

		$mSQL = "SELECT codigo item, CONCAT(codigo, ' ', TRIM(nombre),' ',TRIM(apellido)) valor, sueldo FROM pers WHERE codigo IS NOT NULL ";
		if ( strlen($semilla)>0 ){
			$mSQL .= " AND ( codigo LIKE '$semilla%' OR nombre LIKE '%$semilla%' OR  apellido LIKE '%$semilla%') ";
		} else {
			if ( strlen($codigo)>0 ) $mSQL .= " AND (codigo LIKE '$codigo%' OR nombre LIKE '%$codigo%' OR  apellido LIKE '%$codigo%') ";
		}
		$mSQL .= "ORDER BY nombre ";
		$results = $this->db->count_all('pers');

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
			echo '{success:true, message:"Todo bien", results:'. $results.', data:'.json_encode($arr).'}';
		}
	}

}

?>
