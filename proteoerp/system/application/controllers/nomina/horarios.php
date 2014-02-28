<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
class Horarios extends Controller {
	
	var $url ="nomina/horarios/";
	var $titp="Horarios de Personal";
	var $tits="Horario de Personal";

	function Horarios(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id('70A',1);
	}
	
	function index() {		
		$this->rapyd->load("datagrid","datafilter2");
		
		$filter = new DataFilter2("Filtro de ".$this->titp,'horarios');
		
		$filter->id   = new inputField("Id","id");
		$filter->id->size=10;
		$filter->id->clause="likerigth";
		
		$filter->codigo   = new inputField("C&oacute;digo","codigo");
		$filter->codigo->size=10;
		$filter->codigo->clause="likerigth";
		
		$filter->denomi   = new inputField("Denominaci&oacute;n","denomi");
		$filter->denomi->size=10;
		$filter->denomi->clause="likerigth";
		
		$filter->turno = new inputField("Turno", "turno");
		$filter->turno->size=10;
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<#id#>','<#id#>');

		$grid = new DataGrid();
		$grid->order_by("codigo","asc");
		$grid->per_page = 15;
		
		$grid->column("Id"     ,$uri);
		$grid->column("C&oacute;ndigo"    ,"codigo"                       );
		$grid->column("Denomina&oacute;n" ,"denomi"                 );		
		$grid->column("Turno"             ,"turno"         ,"align='left'");
		$grid->column("Entrada"           ,"entrada"       ,"align='left'");
		$grid->column("Salida"            ,"salida"        ,"align='left'");
		
		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		$data['content'] =$filter->output.$grid->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   ="<h1>$this->titp</h1>";
		$this->load->view('view_ventanas', $data);
	}
	function dataedit(){

 		$this->rapyd->load('dataobject','dataedit');
 		
 		$script='
	 		$(function(){
	 			$(".inputonlynum").numeric("0");
	 			
	 			$("#temporal").change(function(){
	 				temporal = $("#temporal").val();
	 				if(temporal=="X_x"){
	 					$("#tr_codigo").show();
	 					$("#tr_denomi").show();
					}else{
						$("#tr_codigo").hide();
						$("#tr_denomi").hide();
					}
	 			});
	 		});
 		';
 		
		$edit = new DataEdit($this->tits,"horarios");
		
		$edit->script($script,"create");
		$edit->script($script,"modify");
		
		$edit->back_url = $this->url."index/osp";
		$edit->pre_process('delete','_pre_del');
		$edit->pre_process('insert','_valida');
		$edit->pre_process('update','_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->id = new inputField("Id", "id");
		$edit->id->mode="autohide";
		$edit->id->when =array('');
				
		$edit->temporal = new dropdownField("Horario","temporal");
		//$edit->temporal->rule ="required";
		$edit->temporal->option("X_x","Agregar nuevo horario de personal");
		$edit->temporal->options("SELECT codigo, CONCAT_WS(' *',codigo,denomi) FROM horarios GROUP BY codigo");
		$edit->temporal->when =array('modify','create');
				
		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		//$edit->codigo->rule     = "required";//|callback_chcodigo";
		$edit->codigo->size     = 5;
		$edit->codigo->maxlength= 4;
				
		$edit->denomi = new inputField("Ddenominaci&oacute;n", "denomi");
		//$edit->denomi->rule= "required";//strtoupper|
		$edit->denomi->size=40;
		$edit->denomi->maxlength =50;
		
		$edit->turno = new inputField("Turno","turno");
		$edit->turno->rule ="required|callback_chexiste_turno";
		$edit->turno->size      = 1;
		$edit->turno->maxlength= 1;
		
		$edit->entrada = new inputField("Entrada","entrada");
		$edit->entrada->rule ="required|callback_hora";
		$edit->entrada->size     = 3;
		$edit->entrada->maxlength= 4;
		$edit->entrada->css_class='inputonlynum';
		$edit->entrada->append("Inserte la hora en formato HHMM (0830)");
		
		$edit->salida = new inputField("salida","salida");
		$edit->salida->rule ="required|callback_hora";
		$edit->salida->size     = 3;
		$edit->salida->maxlength= 4;
		$edit->salida->css_class='inputonlynum';
		$edit->salida->append("Inserte la hora en formato HHMM (1300)");
		
		$edit->buttons("modify", "save", "undo", "back");//, "delete"
		$edit->build();

		$data['content'] = $edit->output;
		$data["head"]    = $this->rapyd->get_head().script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").script("sinvmaes.js");
		$data['title']   = "<h1>$this->tits</h1>";
		$this->load->view('view_ventanas', $data);
	}
	
	function _valida($do){
		
		$error    = '';
		$temporal = $do->get('temporal');	
	  $codigo   = $do->get('codigo'  );
	  $denomi   = $do->get('denomi'  );
	  
	  if($temporal == "X_x"){
	  	if(empty($codigo))$error.="<div class='alert'><p>El campo C&Oacute;DIGO no puede estar en blanco si no selecciono un horario</p></div>";
	  	if(empty($denomi))$error.="<div class='alert'><p>El campo DENOMINACI&Oacute;N no puede estar en blanco si no selecciono un horario</p></div>";
	  
	  }else{
	  	
	  	$denominacion = $this->datasis->dameval("SELECT denomi FROM horarios WHERE codigo = '$temporal' LIMIT 1 ");
	  	$do->set('codigo', $temporal     );
	    $do->set('denomi', $denominacion);
	  }
	  
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	
	}
	
	function chexiste_turno($turno){		

		$turno = $this->db->escape($turno);
		$codigo= $this->db->escape($this->input->post('codigo'));
		$chek=$this->datasis->dameval("SELECT denomi FROM horarios WHERE turno=$turno AND codigo=$codigo");
		if (!empty($chek)){
			$this->validation->set_message('chexiste_turno',"El turno $turno ya existe para el horario $chek");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	
	function hora($hora){		
		
		if (!($hora >= 0000 AND $hora <= 2359)){
			$this->validation->set_message('hora',"La Hora es inv&aacute;lida, debe tener formato HHMM y ser mayor a (0000) y menor a (2359)");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	
 function _post_insert($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('denominacion');
		logusu('bcta',"OTROS INGRESOS BANCARIOS $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('denominacion');
		logusu('bcta',"OTROS INGRESOS BANCARIOS $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('denominacion');
		logusu('bcta',"OTROS INGRESOS BANCARIOS $codigo NOMBRE  $nombre  ELIMINADO ");
	}	
	function _pre_del($do) {
		$codigo=$do->get('codigo');
		$chek =   $this->datasis->dameval("SELECT COUNT(*) FROM cpla WHERE codigo LIKE '$codigo.%'");
		$chek +=  $this->datasis->dameval("SELECT COUNT(*) FROM itcasi WHERE cuenta='$codigo'");
		
		if ($chek > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Plan de Cuenta tiene derivados o movimientos';
			return False;
		}
		return True;
	}
	
	function instalar(){
		$query = "
			CREATE TABLE IF NOT EXISTS `horarios` (
			`id` INT(10) NOT NULL AUTO_INCREMENT,
			`codigo` CHAR(4) NULL DEFAULT NULL,
			`denomi` VARCHAR(50) NULL DEFAULT NULL,
			`turno` CHAR(1) NULL DEFAULT NULL,
			`entrada` CHAR(4) NULL DEFAULT NULL,
			`salida` CHAR(4) NULL DEFAULT NULL,
			`temporal` CHAR(4) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		)
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT		
		";
		
		echo $this->db->simple_query($query);
		$query="ALTER TABLE `pers`  ADD COLUMN `horario` CHAR(4) NULL DEFAULT NULL";
		echo $this->db->simple_query($query);
	
	}
}
?>