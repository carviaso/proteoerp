<?php require_once(BASEPATH.'application/controllers/validaciones.php');
class Sclicol extends validaciones {
	var $genesal=true;

	function sclicol(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('pi18n');
		//$this->load->library("menues");
		$this->datasis->modulo_id(131,1);
		$this->load->database();
		if ( !$this->datasis->iscampo('scli','mmargen') ) $this->db->simple_query("ALTER TABLE scli ADD mmargen DECIMAL(7,2) DEFAULT 0 COMMENT 'Margen al Mayor'");

		//$this->instalar();
	}

	function index(){
		//echo $this->_numatri();
		redirect('ventas/sclicol/filteredgrid');
	}

	function filteredgrid(){
		$this->pi18n->cargar('scli','filteredgrid');
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter('Filtro de Clientes', 'scli');

		$filter->cliente = new inputField('C&oacute;digo', 'cliente');
		$filter->cliente->size=6;
		$filter->cliente->group = "CLIENTE";

		$filter->nombre= new inputField('Nombre','nombre');
		$filter->nombre->size=30;
		$filter->nombre->group = "CLIENTE";

		$filter->grupo = new dropdownField('Grupo', 'grupo');
		$filter->grupo->option('','Todos');
		$filter->grupo->options('SELECT grupo, gr_desc FROM grcl ORDER BY gr_desc');
		$filter->grupo->style = 'width:140px';
		$filter->grupo->group = "CLIENTE";

		$filter->rifci= new inputField('Rif/CI','rifci');
		$filter->rifci->size=15;
		$filter->rifci->group = "VALORES";

		$filter->cuenta= new inputField('Cuenta Contable','cuenta');
		$filter->cuenta->like_side='after';
		$filter->cuenta->size=15;
		$filter->cuenta->group = "VALORES";

		$filter->buttons('reset','search');
		$filter->build('dataformfiltro');

		$uri = anchor('ventas/sclicol/dataedit/show/<#id#>','<#cliente#>');

		$grid = new DataGrid('Lista de Clientes');
		$grid->order_by('nombre','asc');
		$grid->per_page=50;

		$cclave=anchor('ventas/sclicol/claveedit/modify/<#id#>',img(array('src'=>'images/candado.jpg','border'=>'0','alt'=>'Clave','height'=>'12','title'=>'Clave')));

		$uri_2  = anchor('ventas/sclicol/dataedit/show/<#id#>',img(array('src'=>'images/editar.png','border'=>'0','alt'=>'Editar','height'=>'12','title'=>'Editar')));
		$uri_2 .= anchor('ventas/sclicol/consulta/<#id#>',img(array('src'=>'images/estadistica.jpeg','border'=>'0','alt'=>'Consultar','height'=>'12','title'=>'Consultar')));
		$uri_2 .= $cclave;
		$uri_2 .= img(array('src'=>'images/<siinulo><#tipo#>|N|S</siinulo>.gif','border'=>'0','alt'=>'Estado','title'=>'Estado'));

		$grid->column('Acci&oacute;n',$uri_2);
		$grid->column_orderby('Cliente',$uri,'cliente');
		$grid->column_orderby('Nombre','nombre','nombre');
		$grid->column_orderby($this->pi18n->msj('rifci','Rif/CI'),'rifci','rifci');
		$grid->column_orderby($this->pi18n->msj('tiva','Tipo') ,'tiva','tiva','align=\'center\'');
		$grid->column_orderby('Telefono','telefono','telefono');
		$grid->column_orderby('Contacto','contacto','contacto');
		$grid->column_orderby('Nombre Fiscal','nomfis','nomfis');
		$grid->column_orderby('Grupo','grupo','grupo','align=\'center\'');
		$grid->column_orderby('Credito','limite','limite','align=\'right\'');
		$grid->column_orderby('Cuenta','cuenta','cuenta');

		$grid->add('ventas/sclicol/dataedit/create','Agregar');
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
.fakeContainer { /* The parent container */
    margin: 5px;
    padding: 0px;
    border: none;
    width: 740px; /* Required to set */
    height: 320px; /* Required to set */
    overflow: hidden; /* Required to set */
}
</style>';
//****************************************

		$data['style']   = $style;
		$data['style']  .= style('superTables.css');

		$data['extras']  = $extras;

		$data['content'] = $grid->output;
		$data['content'].= $this->pi18n->fallas();
		$data['filtro']  = $filter->output;
		$data['title']   = heading('Modulo de Clientes');
		$data['script']  = script('jquery.js');
		$data['script'] .= script('superTables.js');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}


	// **************************************
	//     DATAEDIT
	//
	// **************************************
	function dataedit(){
		$this->pi18n->cargar('scli','dataedit');
		$this->rapyd->load('dataedit');

		$mSCLId=array(
			'tabla'   =>'scli',
			'columnas'=>array(
				'cliente' =>'C&oacute;digo Socio',
				'nombre'=>'Nombre',
				'cirepre'=>'Rif/Cedula',
				'dire11'=>'Direcci&oacute;n'),
			'filtro'  =>array('cliente'=>'C&oacute;digo Socio','nombre'=>'Nombre'),
			'retornar'=>array('cliente'=>'socio'),
			'titulo'  =>'Buscar Socio');

		$qformato=$this->datasis->formato_cpla();

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

		$boton =$this->datasis->modbus($mSCLId);
		$bcpla =$this->datasis->modbus($mCPLA);

		$smenu['link']=barra_menu('131');
		$consulrif=trim($this->datasis->traevalor('CONSULRIF'));
		$lcuenta=site_url('contabilidad/cpla/autocomplete/codigo');
		$lsocio =site_url('ventas/sclicol/autocomplete/cliente');

		$link20=site_url('inventario/sclicol/scliexiste');
		$link21=site_url('inventario/sclicol/sclicodigo');


		$script ='
<script type="text/javascript" >
$(function() {

	//Default Action
	$(".inputnum").numeric(".");
	$("#tiva").change(function () { anomfis(); }).change();
	$("#cuenta").autocomplete("'.$lcuenta.'",{
		delay:10,
		//minChars:2,
		matchSubset:1,
		matchContains:1,
		cacheLength:10,
		formatItem:formato,
		width:350,
		autoFill:true
	});

	$("#socio").autocomplete("'.$lsocio.'",{
		delay:10,
		matchSubset:1,
		matchContains:1,
		cacheLength:10,
		formatItem:formato,
		width:350,
		autoFill:true
	});
	//$(":input").enter2tab();
	$( "#maintabcontainer" ).tabs();
	numero=$("#rifci").val();
	c_crc(numero);
	valor=$("#docui").val();
	cg_docui(valor);
});

function formato(row) {
	return row[0] + "-" + row[1];
}

function anomfis(){
	vtiva=$("#tiva").val();
	if(vtiva=="C" || vtiva=="E" || vtiva=="R"){
		$("#tr_nomfis").show();
		$("#tr_riffis").show();
	}else{
		$("#nomfis").val("");
		$("#riffis").val("");
		$("#tr_nomfis").hide();
		$("#tr_riffis").hide();
	}
}

function v_rut(numero){
	numero=numero.replace(/\D/g, "");
	var n;
	var o=0;
	var acum=0;
	var serie= new Array(71,67,59,53,47,43,41,37,29,23,19,17,13,7,3);
	for(i=numero.length;i>=0;i--){
		o=i-1;
		n=Number(numero.substring(o,i));
		acum+=n*serie.pop();
	}
	mo = acum % 11;

	if(mo==0)
		return mo;
	else if(mo==1)
		return mo;
	else
		return 11-mo;
}

function c_crc(numero){
	val=v_rut(numero.trim());
	$(\'#crc\').val(val);
}

function fusionar(mviejo){
	var yurl = "";
	//var mcodigo=jPrompt("Ingrese el Codigo a ");
	jPrompt("Codigo Nuevo","" ,"Codigo Nuevo", function(mcodigo){
		if( mcodigo==null ){
			jAlert("Cancelado por el usuario","Informacion");
		} else if( mcodigo=="" ) {
			jAlert("Cancelado,  Codigo vacio","Informacion");
		} else {
			//mcodigo=jQuery.trim(mcodig);
			//jAlert("Aceptado "+mcodigo);
			yurl = encodeURIComponent(mcodigo);
			$.ajax({
				url: "'.$link20.'",
				global: false,
				type: "POST",
				data: ({ codigo : encodeURIComponent(mcodigo) }),
				dataType: "text",
				async: false,
				success: function(sino) {
					if (sino.substring(0,1)=="S"){
						jConfirm(
							"Ya existe el codigo <div style=\"font-size: 200%;font-weight: bold \">"+mcodigo+"</"+"div>"+sino.substring(1)+"<p>si prosigue se eliminara el producto anterior y<br/> todo el movimiento de este, pasara al codigo "+mcodigo+"</"+"p> <p style=\"align: center;\">Desea <strong>Fusionarlos?</"+"strong></"+"p>",
							"Confirmar Fusion",
							function(r){
							if (r) { sclicambia("S", mviejo, mcodigo); }
							}
						);
					} else {
						jConfirm(
							"Sustitur el codigo actual  por: <center><h2 style=\"background: #ddeedd\">"+mcodigo+"</"+"h2></"+"center> <p>Al cambiar de codigo el producto, todos los<br/> movimientos y estadisticas se cambiaran<br/> correspondientemente.</"+"p> ",
							"Confirmar cambio de codigo",
							function(r) {
								if (r) { sclicambia("N", mviejo, mcodigo); }
							}
						)
					}
				},
				error: function(h,t,e) { jAlert("Error..codigo="+yurl+" ",e) } 
			});
		}
	})
};

function sclicambia( mtipo, mviejo, mcodigo ) {
	$.ajax({
		url: "'.$link21.'",
		global: false,
		type: "POST",
		data: ({ tipo:  mtipo,
			 viejo: mviejo,
			 codigo: encodeURIComponent(mcodigo) }),
		dataType: "text",
		async: false,
		success: function(sino) {
			jAlert("Cambio finalizado "+sino,"Finalizado Exitosamente")
		},
		error: function(h,t,e) {jAlert("Error..","Finalizado con Error" )
		}
	});
	
	if( mtipo=="N" ) {
		location.reload(true);
	} else {
		location.replace("'.site_url("inventario/sinv/filteredgrid").'");
	}
}

function cg_docui(valor){
	if(valor!="R"){
		$("#tr_nombre2").show();
		$("#tr_apellido1").show();
		$("#tr_apellido2").show();
	}else{
		$("#tr_nombre2").hide();
		$("#tr_apellido1").hide();
		$("#tr_apellido2").hide();
	}
	
}
</script>';

		$edit = new DataEdit('Clientes', 'scli');
		$edit->back_url = site_url('ventas/sclicol/filteredgrid');
		//$edit->script($script, 'create');
		//$edit->script($script, 'modify');

		$edit->pre_process('delete','_pre_del');
		$edit->pre_process('insert','_pre_ins');
		$edit->pre_process('update','_pre_ins');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->cliente = new inputField('C&oacute;digo', 'cliente');
		$edit->cliente->rule = 'trim|strtoupper|callback_chexiste';
		$edit->cliente->mode = 'autohide';
		$edit->cliente->size = 9;
		$edit->cliente->maxlength = 5;

		$edit->docui = new dropdownField('Tipo de Documento', 'docui');
		$edit->docui->option('','Seleccionar');
		$edit->docui->option('R','RUT');
		$edit->docui->option('C','Cedula');
		$edit->docui->option('P','Pasaporte');
		$edit->docui->option('T','Tarjeta de identidad');
		$edit->docui->option('E','Extrangero');
		$edit->docui->onchange='cg_docui(this.value)';
		$edit->docui->rule  = 'required';
		$edit->docui->style = 'width:160px';

		$edit->crc = new inputField('C&oacute;digo de Validaci&oacute;n', 'crc');
		$edit->crc->rule = 'trim|strtoupper|required';
		$edit->crc->size = 2;
		$edit->crc->maxlength = 1;

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->rule = 'trim|strtoupper|required';
		$edit->nombre->size = 55;
		$edit->nombre->maxlength = 45;
		$edit->nombre->style = 'width:100%;';
		$edit->nombre->when=array('show');

		$edit->nombre1 = new inputField('Nombre', 'nombre1');
		$edit->nombre1->rule = 'trim|strtoupper|required';
		$edit->nombre1->size = 55;
		$edit->nombre1->maxlength = 45;
		$edit->nombre1->style = 'width:100%;';

		$edit->nombre2 = new inputField('Segundo Nombre', 'nombre2');
		$edit->nombre2->rule = 'trim|strtoupper';
		$edit->nombre2->size = 55;
		$edit->nombre2->maxlength = 45;
		$edit->nombre2->style = 'width:100%;';

		$edit->apellido1 = new inputField('Primer Apellido', 'apellido1');
		$edit->apellido1->rule = 'trim|strtoupper|callback_chnomb|condi_required';
		$edit->apellido1->size = 55;
		$edit->apellido1->maxlength = 45;
		$edit->apellido1->style = 'width:100%;';

		$edit->apellido2 = new inputField('Segundo Apellido', 'apellido2');
		$edit->apellido2->rule = 'trim|strtoupper';
		$edit->apellido2->size  = 55;
		$edit->apellido2->maxlength = 45;
		$edit->apellido2->style = 'width:100%;';

		$edit->contacto = new inputField('Contacto', 'contacto');
		$edit->contacto->rule = 'trim';
		$edit->contacto->size = 55;
		$edit->contacto->maxlength = 40;
		$edit->contacto->style = 'width:100%;';

		$edit->grupo = new dropdownField('Grupo', 'grupo');
		$edit->grupo->option('','Seleccione un grupo');
		$edit->grupo->options('SELECT grupo, CONCAT(grupo," ",gr_desc) gr_desc FROM grcl ORDER BY gr_desc');
		$edit->grupo->rule = 'required';
		$edit->grupo->style = 'width:160px';
		$edit->grupo->insertValue = $this->datasis->dameval('SELECT grupo FROM grcl WHERE gr_desc like "CONSUMIDOR FINAL%"');

		$edit->rifci = new inputField($this->pi18n->msj('rifci','RIF o C.I.'), 'rifci');
		$edit->rifci->rule = 'trim|strtoupper|required|callback_chci';
		$edit->rifci->maxlength =13;
		$edit->rifci->onkeyup='c_crc(this.value)';
		$edit->rifci->size =14;

		$obj  ="dire11";
		$edit->$obj = new inputField('Oficina',$obj);
		$edit->$obj->rule = 'trim';
		$edit->$obj->size      = 45;
		$edit->$obj->maxlength = 40;
		$edit->$obj->style = 'width:95%;';

		$obj  ="dire12";
		$edit->$obj = new inputField('',$obj);
		$edit->$obj->rule = 'trim';
		$edit->$obj->size      = 45;
		$edit->$obj->maxlength = 40;
		$edit->$obj->style = 'width:95%;';

		$obj="ciudad1";
		$edit->$obj = new dropdownField('Ciudad',$obj);
		$edit->$obj->rule = 'trim';
		$edit->$obj->option('','Seleccionar');
		$edit->$obj->options('SELECT ciudad codigo, ciudad FROM ciud ORDER BY ciudad');
		$edit->$obj->style = 'width:200px';
		$edit->$obj->insertValue = $this->datasis->traevalor("CIUDAD");

		$obj  ="dire21";
		$edit->$obj = new inputField('Envio',$obj);
		$edit->$obj->rule = 'trim';
		$edit->$obj->size      = 45;
		$edit->$obj->maxlength = 40;
		$edit->$obj->style = 'width:95%;';

		$obj  ="dire22";
		$edit->$obj = new inputField('',$obj);
		$edit->$obj->rule = 'trim';
		$edit->$obj->size      = 45;
		$edit->$obj->maxlength = 40;
		$edit->$obj->style = 'width:95%;';

		$obj="ciudad2";
		$edit->$obj = new dropdownField('Ciudad',$obj);
		$edit->$obj->rule = 'trim';
		$edit->$obj->option('','Seleccionar');
		$edit->$obj->options('SELECT ciudad codigo, ciudad FROM ciud ORDER BY ciudad');
		$edit->$obj->style = 'width:200px';

		$edit->repre  = new inputField('Representante', 'repre');
		$edit->repre->rule = 'trim';
		$edit->repre->maxlength =30;
		$edit->repre->size = 30;

		$edit->cirepre = new inputField('C&eacute;dula de Rep.', 'cirepre');
		$edit->cirepre->rule = 'trim|strtoupper|callback_chci';
		$edit->cirepre->maxlength =13;
		$edit->cirepre->size = 14;

		$edit->socio = new inputField('Socio del cliente', 'socio');
		$edit->socio->rule = 'trim';
		$edit->socio->size = 8;
		$edit->socio->maxlength =5;
		$edit->socio->append($boton);

		$edit->tiva = new dropdownField('Tipo F&iacute;scal', 'tiva');
		$edit->tiva->option('','Seleccionar');
		$edit->tiva->option('S','Regimen Simplificado');
		$edit->tiva->option('C','Regimen Com&uacute;n');
		$edit->tiva->option('G','Gran contribuyente');
		$edit->tiva->option('A','Autoretenedor');
		$edit->tiva->option('O','Otros');
		//$edit->tiva->options($arr_tiva);
		$edit->tiva->style = 'width:160px';
		$edit->tiva->insertValue = 'N';

		//$edit->tiva->rule='required|callback_chdfiscal';

		$edit->riffis = new inputField('RIF F&iacute;scal', 'riffis');
		$edit->riffis->size = 13;
		$edit->riffis->maxlength =10;

		$edit->zona = new dropdownField('Zona', 'zona');
		$edit->zona->rule = 'trim|required';
		$edit->zona->option('','Seleccionar');
		$edit->zona->options('SELECT codigo, CONCAT(codigo," ", nombre) nombre FROM zona ORDER BY nombre');
		$edit->zona->style = 'width:166px';
		$edit->zona->insertValue = $this->datasis->traevalor("ZONAXDEFECTO");

		$edit->pais = new inputField('Pa&iacute;s','pais');
		$edit->pais->rule = 'trim';
		$edit->pais->size =20;
		$edit->pais->maxlength =30;

		$edit->email = new inputField('E-mail', 'email');
		$edit->email->rule = 'trim|valid_email';
		$edit->email->size =20;
		$edit->email->maxlength =100;

		$edit->cuenta = new inputField('Contable', 'cuenta');
		$edit->cuenta->rule='trim|existecpla';
		$edit->cuenta->append($bcpla);
		$edit->cuenta->size=15;
		$edit->cuenta->maxlength =15;
		$edit->cuenta->insertValue = $this->datasis->dameval('SELECT cuenta FROM grcl WHERE gr_desc like "CONSUMIDOR FINAL%"');

		$edit->telefono = new inputField('Tel&eacute;fonos', 'telefono');
		$edit->telefono->rule = 'trim';
		$edit->telefono->size=20;
		$edit->telefono->maxlength =30;

		$edit->telefon2 = new inputField('Fax', 'telefon2');
		$edit->telefon2->rule = 'trim';
		$edit->telefon2->size=20;
		$edit->telefon2->maxlength =25;

		$edit->tipo = new dropdownField('Tipo ', 'tipo');
		$edit->tipo->options(array('1'=> 'Precio 1','2'=>'Precio 2', '3'=>'Precio 3','4'=>'Precio 4','0'=>'Inactivo'));
		$edit->tipo->style = 'width:90px';

		$edit->formap = new inputField('D&iacute;as', 'formap');
		$edit->formap->css_class='inputnum';
		$edit->formap->rule='trim|integer';
		$edit->formap->maxlength =10;
		$edit->formap->size =6;

		$edit->limite = new inputField('L&iacute;mite', 'limite');
		$edit->limite->css_class='inputnum';
		$edit->limite->rule='trim|numeric';
		$edit->limite->maxlength =12;
		$edit->limite->size = 10;

		$edit->vendedor = new dropdownField('Vendedor', 'vendedor');
		$edit->vendedor->option('','Ninguno');
		$edit->vendedor->options("SELECT vendedor, CONCAT(vendedor,'-',nombre) AS nom FROM vend WHERE tipo IN ('V','A') ORDER BY vendedor");
		$edit->vendedor->style = 'width:250px';

		$edit->porvend = new inputField('Comisi&oacute;n%', 'porvend');
		$edit->porvend->css_class='inputnum';
		$edit->porvend->rule='trim|numeric';
		$edit->porvend->size=4;
		$edit->porvend->maxlength =5;

		$edit->cobrador = new dropdownField('Cobrador', 'cobrador');
		$edit->cobrador->option('','Ninguno');
		$edit->cobrador->options("SELECT vendedor, CONCAT(vendedor,'-',nombre) nombre FROM vend WHERE tipo IN ('C','A') ORDER BY vendedor");
		$edit->cobrador->style = 'width:250px';

		$edit->porcobr = new inputField('Comisi&oacute;n%', 'porcobr');
		$edit->porcobr->css_class='inputnum';
		$edit->porcobr->rule='trim|numeric';
		$edit->porcobr->size=4;
		$edit->porcobr->maxlength =5;

		$edit->observa = new textareaField('Observaci&oacute;n', 'observa');
		$edit->observa->rule = 'trim';
		$edit->observa->cols = 70;
		$edit->observa->rows =3;

		$edit->mensaje = new inputField('Mensaje', 'mensaje');
		$edit->mensaje->rule = 'trim';
		$edit->mensaje->size = 50;
		$edit->mensaje->maxlength =40;

		$edit->mmargen = new inputField("Margen al Mayor",'mmargen');
		$edit->mmargen->css_class='inputnum';
		$edit->mmargen->size=10;
		$edit->mmargen->maxlength=10;

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');

		if($this->genesal){
			$edit->build();
			$style = '
<style type="text/css">
.maintabcontainer {width: 780px; margin: 5px auto;}
</style>';

			$conten['pais']  = $this->pi18n->pais;
			$conten['form']  =& $edit;
			$data['content'] = $this->load->view('view_sclicol', $conten,true);

			$data['content'].= $this->pi18n->fallas();
			$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);

			if($this->pi18n->pais=='COLOMBIA'){
				$data['title']   = heading('Clientes');
			}else{
				$data['title']   = heading('('.$edit->cliente->value.') '.substr($edit->nombre->value,0,30));
			}

			$data['script']   = script('jquery.js');
			$data['script']  .= script('jquery-ui.js');
			$data['script']  .= script('jquery.alerts.js');
			$data['script']  .= script('plugins/jquery.numeric.pack.js');
			$data['script']  .= script('plugins/jquery.floatnumber.js');
			$data['script']  .= script('plugins/jquery.autocomplete.js');
			$data['script']  .= script('plugins/jquery.blockUI.js');
			//$data["script"]  .= script("sinvmaes.js");
			$data['script']  .= $script;

			$data['style']  = style("jquery.alerts.css");
			$data['style'] .= style("redmond/jquery-ui.css");
			$data['style'] .= style('jquery.autocomplete.css');
			$data['style'] .= $style;

			$data['head']    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}else{
			$edit->on_save_redirect=false;
			$edit->build();

			if($edit->on_success()){
				$rt= 'Cliente Guardado';
			}elseif($edit->on_error()){
				$rt= html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string));
			}
			return $rt;
		}
	}

	function claveedit(){
		//$this->pi18n->cargar('scli','dataedit');
		$this->rapyd->load('dataedit');
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence('ventas/sclicol/filteredgrid', $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ? $persistence['back_uri'] : site_url('ventas/sclicol/filteredgrid');

		$edit = new DataEdit('Clientes', 'scli');
		$id=$edit->_dataobject->pk['id'];
		$edit->back_url    = $back;
		$edit->back_save   = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save=true;

		$edit->cliente = new inputField('Cliente', 'cliente');
		$edit->cliente->mode = 'autohide';
		$edit->cliente->when=array('show','modify');
		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->mode = 'autohide';
		$edit->nombre->in='cliente';
		$edit->nombre->when=array('show','modify');

		$edit->clave = new inputField('Clave', 'clave');
		$edit->clave->type = 'password';
		$edit->clave->rule = 'matches[clave1]';
		$edit->clave->when = array('modify');

		$edit->clave1 = new inputField('Confirmaci&oacute;n de clave', 'clave1');
		$edit->clave1->type    = 'password';
		$edit->clave1->db_name = 'clave';
		$edit->clave1->when    = array('modify');

		$edit->clave->size      = $edit->clave1->size = 8;
		$edit->clave->maxlength = $edit->clave1->maxlength = 12;

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$this->rapyd->jquery[]="$('#df1').submit(function(){
			if( $('#clave').val() != '' ) {
				pwEncrypt = $().crypt( {
					method: 'md5',
					source: $('#clave').val()
				});
				$('#clave').val(pwEncrypt);

				pwEncrypt = $().crypt( {
					method: 'md5',
					source: $('#clave1').val()
				});
				$('#clave1').val(pwEncrypt);
			}
			return true;
		});";
		$data['content'] = $edit->output;
		$data['title']   = heading('Asignaci&oacute;n de contrase&ntilde;a a cliente');
		$data['head']    = $this->rapyd->get_head().script('plugins/jquery.crypt.js');
		$this->load->view('view_ventanas', $data);
	}

	//Permite crear un clientes desde otras interfaces
	function creascli(){
		//print_r($_POST);
		$rifci=$this->input->post('rifci');
		if(preg_match('/[VEJG][0-9]{9}$/',$rifci)>0){
			$_POST['tiva']='C';
		}else{
			$_POST['tiva']='N';
		}
		$_POST['tipo']='1';
		$this->genesal=false;
		$rt=$this->dataedit();
		echo $rt;
	}

	// Revisa si existe el codigo
	function scliexiste(){
		$cliente = rawurldecode($this->input->post('codigo'));
		$existe  = $this->datasis->dameval("SELECT count(*) FROM scli WHERE cliente='".addslashes($cliente)."'");
		$devo    = 'N '.$id;
		if ($existe > 0 ) {
			$devo  ='S';
			$devo .= $this->datasis->dameval("SELECT descrip FROM sinv WHERE codigo='".addslashes($id)."'");
		}
		echo $devo;
	}

	function chdfiscal($tiva){
		$nomfis=$this->input->post('nomfis');
		$riffis=$this->input->post('riffis');
		if($tiva=='C' OR $tiva=='E' OR $tiva=='R')
			if(empty($nomfis)){
				$this->validation->set_message('chdfiscal', "Debe introducir el nombre fiscal cuando el cliente es contribuyente");
				return FALSE;
			}
			//elseif (empty($riffis)) {
			//	$this->validation->set_message('chdfiscal', "Debe introducir rif fiscal");
			//	return FALSE;
			//}
		return TRUE;
	}

	function _pre_del($do) {
		$codigo=$this->db->escape($do->get('cliente'));
		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sfac WHERE cod_cli=$codigo");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM smov WHERE cod_cli=$codigo");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM snot WHERE cod_cli=$codigo");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM snte WHERE cod_cli=$codigo");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM otin WHERE cod_cli=$codigo");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM pfac WHERE cod_cli=$codigo");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE enlace=$codigo");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM bmov WHERE clipro='C' AND codcp=$codigo");

		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Cliente con Movimiento no puede ser Borrado';
			return False;
		}
		return True;
	}

	function _pre_ins($do) {
		$do->set('riffis',trim($do->get('rifci')));
		$nomfis = $do->get('nomfis');
		if ( empty( $nomfis ) ) {
			$do->set('nomfis',trim($do->get('nombre')));
		}

		$cliente = $do->get('cliente');
		if(empty($cliente)){
			$do->set('cliente',$this->_numatri());
		}

		$docui = $do->get('docui');
		if($docui=='R'){
			$do->set('nombre2','');
			$do->set('apellido1','');
			$do->set('apellido2','');
		}

		$nombre = $do->get('nombre1').' ';
		$nombre.= $do->get('nombre2').' ';
		$nombre.= $do->get('apellido1').' ';
		$nombre.= $do->get('apellido2');
		$do->set('nombre',trim($nombre));

		return true;
	}

	function _post_insert($do){
		$codigo=$do->get('cliente');
		$limite=$do->get('limite');
		logusu('scli',"CLIENTE $codigo CREADO, LIMITE $limite");
	}

	function _post_update($do){
		$codigo=$do->get('cliente');
		$limite=$do->get('limite');
		logusu('scli',"CLIENTE $codigo MODIFICADO, LIMITE $limite");
	}

	function _post_delete($do){
		$codigo=$do->get('cliente');
		$limite=$do->get('limite');
		logusu('scli',"CLIENTE $codigo ELIMINADO, LIMITE $limite");
	}

	function chexiste($codigo){
		$codigo=$this->input->post('cliente');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM scli WHERE cliente='$codigo'");
		if ($check > 0){
			$mSQL_1=$this->db->query("SELECT nombre, rifci FROM scli WHERE cliente='$codigo'");
			$row = $mSQL_1->row();
			$nombre =$row->nombre;
			$rifci  =$row->rifci;
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el cliente $nombre  $rifci ");
			return FALSE;
		}else {
		return TRUE;
		}
	}

	function autocomplete($campo,$cod=FALSE){
		if($cod!==false){
			$cod=$this->db->escape_like_str($cod);
			$qformato=$this->datasis->formato_cpla();
			$data['cliente']="SELECT cliente AS c1 ,nombre AS c2 FROM scli WHERE cliente LIKE '$cod%' ORDER BY cliente LIMIT 10";
			if(isset($data[$campo])){
				$query=$this->db->query($data[$campo]);
				if($query->num_rows() > 0){
					foreach($query->result() AS $row){
						echo $row->c1.'|'.$row->c2."\n";
					}
				}
			}
		}
	}

	function consulta(){
		$this->load->helper('openflash');
		$this->rapyd->load("datagrid");
		$fields = $this->db->field_data('scli');
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

		$mCodigo = $this->datasis->dameval("SELECT cliente FROM scli WHERE id=".$claves['id']."");
		
		$grid = new DataGrid('Ventas por Mes');
		$grid->db->_protect_identifiers=false;
		$grid->db->select( array('a.tipo_doc','a.fecha', 'a.numero', 'a.monto', 'a.abonos', 'a.monto-a.abonos saldo' ) );
		$grid->db->from('smov a');
		$grid->db->where('a.cod_cli', $mCodigo );
		$grid->db->where('a.monto <> a.abonos');
		$grid->db->where('a.tipo_doc IN ("FC","ND","GI") ' );
		$grid->db->orderby('a.fecha');
			
		$grid->column("Fecha"  ,  "fecha" );
		$grid->column("Tipo"   ,  "tipo_doc",'align="CENTER"');
		$grid->column("Numero" ,  "numero",'align="LEFT"');
		$grid->column("Monto"  ,  "<nformat><#monto#></nformat>" ,'align="RIGHT"');
		$grid->column("Abonos" ,  "<nformat><#abonos#></nformat>",'align="RIGHT"');
		$grid->column("Saldo"  ,  "<nformat><#saldo#></nformat>" ,'align="RIGHT"');
		$grid->build();

		$nombre = $this->datasis->dameval("SELECT nombre FROM scli WHERE id=".$claves['id']." ");

		/*
		$descrip = $this->datasis->dameval("SELECT descrip FROM sinv WHERE id=".$claves['id']." ");
		$data['content'] = "
		<table width='100%'>
			<tr>
				<td valign='top' colspan='2'>
					<div style='border: 2px outset #EFEFEF;background: #EFEFFF '>
					".$grid->output."
					</div>
				</td>
			</tr>
			<tr>
				<td valign='top'>
				".open_flash_chart_object( 300,200, site_url("inventario/sinv/ventas/$mCodigo"))."
				</td>
				<td valign='top'>".
				open_flash_chart_object( 300,200, site_url("inventario/sinv/compras/".raencode($mCodigo)))."
				</td>
			</tr>
			
		</table>";
		*/
		$data['content'] = $grid->output;
		$data["head"]     = script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$data['title']    = '<h1>Consulta de Clientes</h1>';
		$data["subtitle"] = "
			<div align='center' style='border: 2px outset #EFEFEF;background: #EFEFEF;font-size:18px'>
				<a href='javascript:javascript:history.go(-1)'>(".$mCodigo.") ".$nombre."</a>
			</div>";
		$this->load->view('view_ventanas', $data);
		
	}

	function _numatri(){
		/*LOCAL mNUMERO := PROX_SQL("ncodcli")
		FUNCTION NUMATRI(mNUMERO)
			LOCAL mCONVE   := ''
			LOCAL mRESIDUO := mNUMERO
			LOCAL mBASE    := 36
			DO WHILE mRESIDUO > mBASE-1
				mTEMPO   := MOD(mRESIDUO,mBASE)
				mRESIDUO := INT(mRESIDUO/mBASE)
				IF mTEMPO > 9
					mCONVE += CHR(mTEMPO+55)
				ELSE
					mCONVE += STR(mTEMPO,1)
				ENDIF
			ENDDO
			IF mRESIDUO > 9
				mCONVE += CHR(mRESIDUO+55)
			ELSE
				mCONVE += STR(mRESIDUO,1)
			ENDIF
		RETURN mCONVE*/

		$numero = $this->datasis->prox_numero('ncodcli');
		$residuo= $numero;
		$mbase  = 36;
		$conve='';
		while($residuo > $mbase-1){
			$mtempo  = $residuo % $mbase;
			$residuo = intval($residuo/$mbase);
			if($mtempo >9 ){
				$conve .= chr($mtempo+55);
			}else{
				$conve .= $mtempo;
			}
		}
		if($mtempo >9 ){
			$conve .= chr($mtempo+55);
		}else{
			$conve .= $mtempo;
		}
		return $conve;
	}

	function chnomb($val){
		$docui=$this->input->post('docui');
		if($docui!='R' && empty($val)){
			$this->validation->set_message('chnomb',"El campo %s es obligatorio en este caso");
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function instalar(){
		$seniat='http://www.seniat.gov.ve/BuscaRif/BuscaRif.jsp';
		$mSQL="REPLACE INTO valores (nombre,valor,descrip) VALUES ('CONSULRIF','$seniat','Pagina de consulta de rif del seniat') ON DUPLICATE KEY UPDATE valor='$seniat'";
		$this->db->simple_query($mSQL);
		
		if (!$this->db->field_exists('modifi','scli')) {
			$mSQL='ALTER TABLE `scli` ADD `modifi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL AFTER `mensaje`';
			$this->db->simple_query($mSQL);
		}
		
		if (!$this->db->field_exists('id','scli')) {
			$mSQL='ALTER TABLE `scli` DROP PRIMARY KEY, ADD UNIQUE `cliente` (`cliente`)';
			$this->db->simple_query($mSQL);
			$mSQL='ALTER TABLE scli ADD id INT AUTO_INCREMENT PRIMARY KEY';
			$this->db->simple_query($mSQL);
			$mSQL='ALTER TABLE `scli`  CHANGE COLUMN `formap` `formap` INT(6) NULL DEFAULT 0';
			$this->db->simple_query($mSQL);
			$mSQL='ALTER TABLE `scli`  CHANGE COLUMN `email` `email` VARCHAR(100) NULL DEFAULT NULL';
			$this->db->simple_query($mSQL);
			$mSQL='ALTER TABLE `scli`  CHANGE COLUMN `clave` `clave` VARCHAR(50) NULL DEFAULT NULL AFTER `tiva`';
			$this->db->simple_query($mSQL);
		}
		if (!$this->db->field_exists('nombre1','scli')) {
			$mSQL="ALTER TABLE `scli`  ADD COLUMN `nombre1` VARCHAR(100) NULL AFTER `mmargen`,  ADD COLUMN `nombre2` VARCHAR(100) NULL AFTER `nombre1`,  ADD COLUMN `apellido1` VARCHAR(100) NULL AFTER `nombre2`,  ADD COLUMN `apellido2` VARCHAR(100) NULL AFTER `apellido1`";
			$this->db->simple_query($mSQL);
		}
		if (!$this->db->field_exists('crc' ,'scli')) {
			$mSQL="ALTER TABLE `scli`  ADD COLUMN `crc` INT(1) NULL AFTER `mmargen`";
			$this->db->simple_query($mSQL);
		}
		if (!$this->db->field_exists('docui','scli')) {
			$mSQL="ALTER TABLE `scli` ADD COLUMN `docui` CHAR(1) NULL AFTER `crc`";
			$this->db->simple_query($mSQL);
		}
		if (!$this->db->field_exists('tipocol','rete')) {
			$mSQL="ALTER TABLE rete ADD COLUMN tipocol CHAR(1) NULL DEFAULT NULL AFTER cuenta;";
			$this->db->simple_query($mSQL);
		}
	}
}