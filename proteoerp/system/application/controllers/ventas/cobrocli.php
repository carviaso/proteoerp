<?php require_once(BASEPATH.'application/controllers/validaciones.php');
//Cliente
class Cobrocli extends validaciones {

	function cobrocli(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->load->library("menues");
		//$this->datasis->modulo_id(131,1);
		$this->load->database();
	}

	function index(){
		$mSCLId=array(
		'tabla'   =>'scli',
		'columnas'=>array(
			'cliente' =>'Código Cliente',
			'nombre'=>'Nombre',
			'contacto'=>'Contacto'),
		'filtro'  =>array('cliente'=>'Código Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'cod_cli'),
		'titulo'  =>'Buscar Cliente');

		$bsclid =$this->datasis->modbus($mSCLId);

		$this->rapyd->load("datafilter","datagrid");

		$filter = new DataForm("ventas/cobrocli/index/process");

		$filter->cliente = new inputField("Cliente", "cod_cli");
		$filter->cliente->rule = "required";
		$filter->cliente->append($bsclid);
		$filter->cliente->size=10;

		$filter->caja= new inputField("Caja", "caja");
		$filter->caja->rule = "required";
		$filter->caja->group = "Selecci&oacute;n de caja";
		$filter->caja->size  =4;

		/*$filter->clave= new inputField("Clave", "clave");
		$filter->clave->rule = "required";
		$filter->clave->group = "Selecci&oacute;n de caja";
		$filter->clave->type  = "password";
		$filter->clave->size  =4;*/

		$filter->submit("btnsubmit","aceptar");
		$filter->build_form();

		if ($filter->on_success()){

		}

		$data['content'] = $filter->output;
		$data['title']   = "<h1>Cobro a clientes</h1>";
		$data["head"]    = $this->rapyd->get_head();

		$this->load->view('view_ventanas', $data);
	}

	function pago(){
		$this->rapyd->load("datafilter","datagrid");

		$grid = new DataGrid("Lista de Clientes");

		$grid->order_by("nombre","asc");
		$grid->per_page=15;

		$grid->column("Cliente",$uri);
		$grid->column("Nombre","nombre","nombre");
		$grid->column("RIF/CI","rifci");
		$grid->column("Contribuyente","tiva","align='center'");
		$grid->add("ventas/scli/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Clientes</h1>";
		$data["head"]    = $this->rapyd->get_head();

		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load("dataedit");

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

		$boton =$this->datasis->modbus($mSCLId);
		$bcpla =$this->datasis->modbus($mCPLA);

		$smenu['link']=barra_menu('131');
		$consulrif=$this->datasis->traevalor('CONSULRIF');
		$script ='
		$(function() {
			$(".inputnum").numeric(".");
			$("#tiva").change(function () { anomfis(); }).change();
		});

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

		function consulrif(){
				vrif=$("#riffis").val();
				if(vrif.length==0){
					alert("Debe introducir primero un RIF");
				}else{
					vrif=vrif.toUpperCase();
					$("#riffis").val(vrif);
					window.open("'.$consulrif.'"+"?p_rif="+vrif,"CONSULRIF","height=350,width=410");
				}
		}

		';

		$lriffis='<a href="javascript:consulrif();" title="Consultar RIF en el SENIAT" onclick="">Consultar RIF en el SENIAT</a>';
		$edit = new DataEdit("Clientes", "scli");
		$edit->back_url = site_url("ventas/scli/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");

		$edit->pre_process('delete','_pre_del');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->cliente = new inputField("C&oacute;digo", "cliente");
		$edit->cliente->rule = "trim|strtoupper|required|callback_chexiste";
		$edit->cliente->mode = "autohide";
		$edit->cliente->size = 9;
		$edit->cliente->maxlength = 5;

		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->rule = "trim|strtoupper|required";
		$edit->nombre->size = 60;
		$edit->nombre->maxlength = 45;

		$edit->contacto = new inputField("Contacto", "contacto");
		$edit->contacto->rule = "trim";
		$edit->contacto->size = 60;
		$edit->contacto->maxlength = 40;

		$edit->grupo = new dropdownField("Grupo", "grupo");
		$edit->grupo->option("","Seleccione un grupo");
		$edit->grupo->options("SELECT grupo, gr_desc FROM grcl ORDER BY gr_desc");
		$edit->grupo->rule = "required";
		$edit->grupo->size = 6;
		$edit->grupo->maxlength = 4;

		$edit->rifci = new inputField("RIF o Cedula Identidad", "rifci");
		$edit->rifci->rule = "trim|strtoupper|required|callback_chci";
		$edit->rifci->maxlength =13;
		$edit->rifci->append($lriffis);
		$edit->rifci->size =18;

		$edit->dire11 = new inputField("Direcci&oacute;n","dire11");
		$edit->dire11->rule = "trim";
		$edit->dire11->size = 60;
		$edit->dire11->maxlength = 40;

		$edit->dire12 = new inputField('&nbsp;',"dire12");
		$edit->dire12->rule = "trim";
		$edit->dire12->maxlength = 40;
		$edit->dire12->size = 60;

		$edit->ciudad1 = new dropdownField("Ciudad","ciudad1");
		$edit->ciudad1->rule = "trim";
		$edit->ciudad1->option("","");
		$edit->ciudad1->options("SELECT ciudad codigo, ciudad FROM ciud ORDER BY ciudad");
		$edit->ciudad1->maxlength = 40;
		$edit->ciudad1->size = 60;

		$edit->dire21 = new inputField("Direcci&oacute;n","dire21");
		$edit->dire21->rule = "trim";
		$edit->dire21->maxlength = 40;
		$edit->dire21->size = 60;

		$edit->dire22 = new inputField('&nbsp;',"dire22");
		$edit->dire22->rule = "trim";
		$edit->dire22->maxlength = 40;
		$edit->dire22->size = 60;

		$edit->ciudad2 = new dropdownField("Ciudad", "ciudad2");
		$edit->ciudad2->option("","Seleccionar");
		$edit->ciudad2->options("SELECT ciudad codigo, ciudad FROM ciud ORDER BY ciudad");
		$edit->ciudad2->style =$edit->ciudad1->style = "width:180px";
		$edit->ciudad2->maxlength = 40;
		$edit->ciudad2->size = 60;

		$edit->repre  = new inputField("Representante Legal", "repre");
		$edit->repre->rule = "trim";
		$edit->repre->maxlength =30;
		$edit->repre->size = 40;

		$edit->cirepre = new inputField("C&eacute;dula de Rep.", "cirepre");
		$edit->cirepre->rule = "trim|strtoupper|callback_chci";
		$edit->cirepre->maxlength =13;
		$edit->cirepre->size = 16;

		$edit->socio = new inputField("Socio", "socio");
		$edit->socio->rule = "trim";
		$edit->socio->size = 8;
		$edit->socio->maxlength =5;
		$edit->socio->append($boton);

		$edit->tiva = new dropdownField("Tipo Fiscal", "tiva");
		$edit->tiva->option("","Seleccionar");
		$edit->tiva->options(array("C"=>"Contribuyente","N"=>"No Contribuyente","E"=>"Especial","R"=>"Regimen Exento","O"=>"Otro"));
		$edit->tiva->style = "width:140px";
		$edit->tiva->rule='required|callback_chdfiscal';
		$edit->tiva->group = "Informaci&oacute;n fiscal";

		$edit->nomfis = new inputField("Nombre Fiscal", "nomfis");
		$edit->nomfis->rule = "trim";
		$edit->nomfis->size=80;
		$edit->nomfis->maxlength =80;
		$edit->nomfis->group = "Informaci&oacute;n fiscal";


		$edit->riffis = new inputField("RIF Fiscal", "riffis");
		$edit->riffis->size = 13;
		$edit->riffis->maxlength =10;
		$edit->riffis->rule = "trim|callback_chrif";
		$edit->riffis->append($lriffis);
		$edit->riffis->group = "Informaci&oacute;n fiscal";

		$edit->zona = new dropdownField("Zona", "zona");
		$edit->zona->rule = "trim|required";
		$edit->zona->option("","Seleccionar");
		$edit->zona->options("SELECT codigo, nombre FROM zona ORDER BY nombre");

		$edit->pais = new inputField("Pa&iacute;s", "pais");
		$edit->pais->rule = "trim";
		$edit->pais->size =40;
		$edit->pais->maxlength =30;

		$edit->email  = new inputField("E-mail", "email");
		$edit->email->rule = "trim";
		$edit->email->rule = "valid_email";
		$edit->email->size =28;
		$edit->email->maxlength =18;

		$edit->cuenta = new inputField("Cuenta contable", "cuenta");
		$edit->cuenta->rule='trim|existecpla';
		$edit->cuenta->append($bcpla);
		$edit->cuenta->size=20;
		$edit->cuenta->maxlength =15;

		$edit->telefono = new inputField("Telefonos", "telefono");
		$edit->telefono->rule = "trim";
		$edit->telefono->size=30;
		$edit->telefono->maxlength =30;

		$edit->telefon2 = new inputField("Fax", "telefon2");
		$edit->telefon2->rule = "trim";
		$edit->telefon2->size=25;
		$edit->telefon2->maxlength =25;

		$edit->tipo = new dropdownField("Precio Asignado", "tipo");
		$edit->tipo->options(array("1"=> "Precio 1","2"=>"Precio 2", "3"=>"Precio 3","4"=>"Precio 4","0"=>"Inactivo"));
		$edit->tipo->style = "width:90px";
		$edit->tipo->group = "Informaci&oacute;n financiera";

		$edit->formap = new inputField("D&iacute;as de Cr&eacute;dito", "formap");
		$edit->formap->css_class='inputnum';
		$edit->formap->rule="integer|trim";
		$edit->formap->maxlength =10;
		$edit->formap->size =6;
		$edit->formap->group = "Informaci&oacute;n financiera";

		$edit->limite = new inputField("L&iacute;mite de Cr&eacute;dito", "limite");
		$edit->limite->css_class='inputnum';
		$edit->limite->rule='trim|numeric';
		$edit->limite->maxlength =15;
		$edit->limite->size = 20;
		$edit->limite->group = "Informaci&oacute;n financiera";

		$edit->vendedor = new dropdownField("Vendedor", "vendedor");
		$edit->vendedor->option("","Ninguno");
		$edit->vendedor->options("SELECT vendedor, nombre FROM vend WHERE tipo IN ('V','A') ORDER BY nombre");
		$edit->vendedor->group = "Informaci&oacute;n financiera";

		$edit->porvend = new inputField("% Comisi&oacute;n venta", "porvend");
		$edit->porvend->css_class='inputnum';
		$edit->porvend->rule='trim|numeric';
		$edit->porvend->size=8;
		$edit->porvend->maxlength =5;
		$edit->porvend->group = "Informaci&oacute;n financiera";

		$edit->cobrador = new dropdownField("Cobrador", "cobrador");
		$edit->cobrador->option("","Ninguno");
		$edit->cobrador->options("SELECT vendedor, nombre FROM vend WHERE tipo IN ('C','A') ORDER BY nombre");
		$edit->cobrador->group = "Informaci&oacute;n financiera";

		$edit->porcobr = new inputField("% Comisi&oacute;n cobro", "porcobr");
		$edit->porcobr->css_class='inputnum';
		$edit->porcobr->rule='trim|numeric';
		$edit->porcobr->size=8;
		$edit->porcobr->maxlength =5;
		$edit->porcobr->group = "Informaci&oacute;n financiera";

		$edit->observa = new textareaField("Observaci&oacute;n", "observa");
		$edit->observa->rule = "trim";
		$edit->observa->cols = 80;
		$edit->observa->rows =3;

 		$edit->mensaje = new inputField("Mensaje", "mensaje");
 		$edit->mensaje->rule = "trim";
		$edit->mensaje->size = 50;
		$edit->mensaje->maxlength =40;

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		//$data['script']  ='<script type="text/javascript">
		//function pelusa(){
		//	alert(screen.availWidth);
		//}
		//</script>';

		$data['content'] = $edit->output;
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['title']   = "<h1>Clientes</h1>";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function chdfiscal($tiva){
		$nomfis=$this->input->post('nomfis');
		$riffis=$this->input->post('riffis');
		if($tiva=='C' OR $tiva=='E' OR $tiva=='R')
			if(empty($nomfis) OR empty($riffis)){
				$this->validation->set_message('chdfiscal', "Debe introducir rif y nombre fiscal cuando el cliente es contribuyente, especial o regimen excento");
				return FALSE;
			}
		return TRUE;
	}

	function _pre_del($do) {
		$codigo=$do->get('cliente');
		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sfac WHERE cod_cli='$codigo'");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM smov WHERE cod_cli='$codigo'");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM snot WHERE cod_cli='$codigo'");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM snte WHERE cod_cli='$codigo'");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM otin WHERE cod_cli='$codigo'");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM pfac WHERE cod_cli='$codigo'");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE enlace='$codigo'");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM bmov WHERE clipro='C' AND codcp='$codigo'");

		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Cliente con Movimiento no puede ser Borrado';
			return False;
		}
		return True;
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
	function instalar(){
		$seniat='http://www.seniat.gov.ve/BuscaRif/BuscaRif.jsp';
		$mSQL="INSERT INTO valores (nombre,valor,descrip) VALUES ('CONSULRIF','$seniat','Pagina de consulta de rif del seniat') ON DUPLICATE KEY UPDATE valor='$seniat'";
		$this->db->simple_query($mSQL);
	}
}
?>