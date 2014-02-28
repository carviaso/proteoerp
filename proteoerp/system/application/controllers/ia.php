<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
class ia extends Controller {
	var $titp='Centro de IA';
	var $tits='Modelos de Neuronas';
	var $url ='ia/';

	function ia(){
		parent::Controller();
		$this->load->library('rapyd');
		//$this->datasis->modulo_id(216,1);
		$this->instalar();
		$this->E=0.5; //Factor de aprendizaje
	}

	function index(){
		redirect($this->url.'filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'IA');

		$filter->nombre = new inputField('nombre','nombre');
		$filter->nombre->rule      ='max_length[100]';
		$filter->nombre->size      =102;
		$filter->nombre->maxlength =100;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Id',$uri,'id','align="left"');
		$grid->column_orderby('Nombre','nombre','nombre','align="left"');
		$grid->column_orderby('Pos','pos','pos','align="left"');
		$grid->column_orderby('Peso','w','w','align="left"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'IA');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('update','_pre_update');
		$edit->pre_process('delete','_pre_delete');

		$edit->id = new inputField('Id','id');
		$edit->id->rule='max_length[10]';
		$edit->id->size =12;
		$edit->id->maxlength =10;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[100]';
		$edit->nombre->size =102;
		$edit->nombre->maxlength =100;

		$edit->w = new inputField('W','w');
		$edit->w->rule='max_length[8]';
		$edit->w->size =10;
		$edit->w->maxlength =8;

		$edit->pos = new inputField('Pos','pos');
		$edit->pos->rule='max_length[10]';
		$edit->pos->size =12;
		$edit->pos->maxlength =10;

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);
	}

	function aprende($nombre,$val,$esp){
		$dbnombre=$this->db->escape($nombre);
		$mSQL='SELECT pos,w FROM `IA` WHERE nombre='.$dbnombre;
		$query = $this->db->query($mSQL);
		$ws0=array();
		$arw=$query->result_array();
		foreach ($query->result() as $row){
			$ws0[$row->pos]=$row->w;
		}
		array_unshift($val,-1);

		$wsn=array();
		foreach ($val as $i=>$ent){
			$wsn[$i]=$ws0[$i]+(2*$this->E*$esp*$ent);
			//echo $ws0[$i].'+(2*'.$this->E."*$esp*$ent".br();
		}
		foreach($wsn as $i=>$wn){
			$sql='UPDATE `IA` SET w='.$wn.' WHERE pos='.$i.' AND nombre='.$dbnombre;
			//echo $sql;
			$this->db->simple_query($sql);
		}
		return true;
	}

	//Funciones de la neurona
	function neurona($param){
		$data= func_get_args();
		$nombre = array_shift($data);

		$sig= $this->propagacion($nombre,$data);
		$act= $this->activacion($sig);
		return($this->transferencia($act));
	}

	function propagacion($nombre,$val){
		$dbnombre=$this->db->escape($nombre);
		$mSQL='SELECT pos,w FROM `IA` WHERE nombre='.$dbnombre;
		$query = $this->db->query($mSQL);
		$ws=array();
		$arw=$query->result_array();
		foreach ($query->result() as $row){
			$ws[$row->pos]=$row->w;
		}

		//Hace la suma
		$rt=0;
		foreach ($val as $id=>$ent){
			$i=$id+1;
			if(isset($ws[$i])){
				$rt+=$ent*$ws[$i];
			}else{
				$rt+=$ent;

				$data = array('nombre'=>$nombre, 'pos'=>$i, 'w'=>1);
				$sql = $this->db->insert_string('IA', $data);
				$this->db->simple_query($sql);
			}
		}

		//Coloca la atenuacion
		if(isset($ws[0])){
				$rt-=$ws[0];
		}else{
			$data = array('nombre'=>$nombre, 'pos'=>0, 'w'=>1);
			$sql = $this->db->insert_string('IA', $data);
			$this->db->simple_query($sql);
			$rt-=1;
		}

		return $rt;
	}

	function activacion($int){
		if($int > 0) return 1;
		if($int < 0) return -1;
		return 0;
	}

	function transferencia($val){
		return $val;
	}

	function _pre_insert($do){
		return true;
	}

	function _pre_update($do){
		return true;
	}

	function _pre_delete($do){
		return true;
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
		if (!$this->db->table_exists('IA')) {
			$mSQL="CREATE TABLE `IA` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `nombre` varchar(100) NOT NULL,
			  `w` float NOT NULL DEFAULT '1',
			  `pos` int(10) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Pesos de las neuronas'";
			$this->db->simple_query($mSQL);
		}
	}
}