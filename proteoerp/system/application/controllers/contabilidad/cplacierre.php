<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/validaciones.php');
class Cplacierre extends validaciones {
	function cplacierre(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(604,1);
	}

	function index() {
		$this->rapyd->load("datagrid","datafilter");

		$filter = new DataFilter("Filtro de Cierre de cuentas",'cplacierre');

		$filter->cuenta   = new inputField("Cuenta","cuenta");
		$filter->cuenta->like_side='after';
		$filter->cuenta->size=15;

		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('contabilidad/cplacierre/dataedit/show/<#id#>','<#cuenta#>');

		$grid = new DataGrid();
		$grid->order_by("id","asc");
		$grid->per_page = 15;

//		$grid->column_orderby('ID',$uri,'id');
		$grid->column_orderby("Cuenta"    ,$uri,"cuenta");
		$grid->column_orderby('A&ntilde;o','anno','anno');
		$grid->column_orderby('Descripci&oacute;n','descrip','descrip');
		$grid->column_orderby('Monto'     ,'monto','monto',"align='center'");

		$grid->add('contabilidad/cplacierre/dataedit/create');
		$grid->build();

		$data['content'] =$filter->output.$grid->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   ='<h1>Cierre de Cuentas</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load("dataedit");

		$edit = new DataEdit("Cierre Plan de cuenta","cplacierre");
		$edit->back_url = "contabilidad/cplacierre";
//		$edit->pre_process('delete','_pre_del');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->cuenta = new inputField("Cuenta", "cuenta");
		$edit->cuenta->rule= "trim|required|existecpla";
		$edit->cuenta->mode="autohide";
		$edit->cuenta->size=20;
		$edit->cuenta->maxlength =15 ;

		$edit->descrip = new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->rule= "strtoupper|required";
		$edit->descrip->size=45;
		$edit->descrip->maxlength =35;
		
		$edit->anno = new dateonlyField("A&ntilde;o", "anno","Y");
		$edit->anno->rule= "strtoupper|required";
		$edit->anno->size=15;
		$edit->anno->maxlength =15;

		$edit->monto = new inputField("Monto", "monto");
		$edit->monto->rule= "strtoupper|required";
		$edit->monto->size=15;
		$edit->monto->maxlength =15;

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   = '<h1>Cierre Plan de Cuentas</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function chcodigo($codigo){
		if (preg_match("/^[0-9]+(\.[0-9]+)*$/",$codigo)>0){
			$formato=$this->datasis->dameval('SELECT formato FROM cemp LIMIT 1');
			$farr=explode('.',$formato);
			$carr=explode('.',$codigo);
			$max =count($carr);
			$mmac=count($farr);
			if($mmac>=$max){
				for($i=0;$i<$max;$i++){
					if(strlen($farr[$i])!=strlen($carr[$i])){
						$this->validation->set_message('chcodigo',"El c&oacute;digo dado no coincide con el formato: $formato");
						return false;
					}
				}
			}else{
				$this->validation->set_message('chcodigo',"El c&oacute;digo dado no coincide con el formato: $formato");
				return false;
			}
			$pos=strrpos($codigo,'.');
			if($pos!==false){
				$str=substr($codigo,0,$pos);
				$cant=$this->datasis->dameval("SELECT COUNT(*) FROM cpla WHERE codigo='$str'");
				if($cant==0){
					$this->validation->set_message('chcodigo',"No existe la cuenta padre ($str) para registrar esa cuenta");
					return false;
				}
			}
		}else{
			$this->validation->set_message('chcodigo',"El c&oacute;digo parece tener formato invalido");
			return false;
		}
		return true;
	}

	function autocomplete($campo,$cod=FALSE){
		if($cod!==false){
			$cod=$this->db->escape_like_str($cod);
			$qformato=$this->datasis->formato_cpla();
			$data['codigo']="SELECT codigo AS c1 ,descrip AS c2 FROM cpla WHERE $campo LIKE '$cod%' AND codigo LIKE '$qformato' ORDER BY $campo LIMIT 10";
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

	function _post_insert($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('descrip');
		logusu('cplacierre',"CIERRE PLAN DE CUENTA $codigo NOMBRE  $nombre CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('descrip');
		logusu('cplacierre',"CIERRE PLAN DE CUENTA $codigo NOMBRE  $nombre  MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('descrip');
		logusu('cplacierre',"CIERRE PLAN DE CUENTA $codigo NOMBRE  $nombre  ELIMINADO ");
	}

	function _pre_del($do) {
		$codigo=$do->get('codigo');
		$check =   $this->datasis->dameval("SELECT COUNT(*) FROM cplacierre WHERE codigo LIKE '$codigo.%'");
		$check +=  $this->datasis->dameval("SELECT COUNT(*) FROM itcasi WHERE cuenta='$codigo'");
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Plan de Cuenta tiene derivados o movimientos';
			return False;
		}
		return True;
	}

	function generar(){

		$this->rapyd->load('dataform');

		$form = new DataForm("contabilidad/cplacierre/generar/process");
		$form->fecha = new dateonlyField("Fecha","fecha",'Y');
		$form->fecha->insertValue = date("Y-m-d");
		$form->fecha->dbformat='Y';
		$form->fecha->rule ="required";
		$form->fecha->size =12;
		$form->submit("btnsubmit","Generar");
		$form->build_form();

		$msj='';
		if ($form->on_success()){
			$anno=$form->fecha->newValue;
			if ($this->_generar($anno)) 
				$msj='Cierre Creado';
			else
				$msj='Error creando los cierres,';
		}

		$data['content'] = $form->output.$msj;
		$data['title']   = '<h1>Generar Balance de cierre</h1>';
		$data['script']  = '';
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _generar($anno){
		if(is_numeric($anno)){
			$fdesde=$anno.'0101';
			$fhasta=$anno.'1231';

			$mSQL = "DELETE FROM cplacierre WHERE anno = $anno";
			$rt=$this->db->simple_query($mSQL);
			if(!$rt){ memowrite($mSQL,'cplacierre'); return $rt;}
			//var_dump($rt);

			$mSQL = 'INSERT INTO  cplacierre (anno,cuenta,descrip,monto) SELECT '.$anno.', a.cuenta, b.descrip, sum(a.debe)-sum(a.haber) from itcasi AS a JOIN cpla AS b ON a.cuenta=b.codigo ';
			$mSQL.= "WHERE fecha BETWEEN $fdesde AND $fhasta ";
			$mSQL.= 'GROUP BY a.cuenta';
			$rt=$this->db->simple_query($mSQL);
			if(!$rt){ memowrite($mSQL,'cplacierre'); return $rt;}
			//var_dump($rt);

			$mSQL='INSERT IGNORE INTO cplacierre (anno,cuenta,descrip,monto) SELECT '.$anno.' AS anno, codigo, descrip,0 FROM cpla';
			$rt=$this->db->simple_query($mSQL);
			if(!$rt){ memowrite($mSQL,'cplacierre'); return $rt;}
			//var_dump($rt);
			
			return TRUE;
		}
	}

	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `cplacierre` (
		  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
		  `anno` int(10) DEFAULT NULL,
		  `cuenta` varchar(250) DEFAULT NULL,
		  `descrip` varchar(250) DEFAULT NULL,
		  `monto` decimal(15,2) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `ac` (`anno`,`cuenta`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Cierres contables'";
		$this->db->simple_query($mSQL);
	}
}
?>
