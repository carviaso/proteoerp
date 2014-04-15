<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Calcomi extends Controller {

	var $titp  = 'Penalizaci&oacute;n';
	var $tits  = 'Penalizaci&oacute;n';
	var $url   = 'ventas/calcomi/';

	function Calcomi(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id('12C',1);
	}

	function index() {
		redirect($this->url.'filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('dataform');

		$porcomi1 =$this->datasis->traevalor('PORCOMI1');
		$porcomi2 =$this->datasis->traevalor('PORCOMI2');
		$porcomi3 =$this->datasis->traevalor('PORCOMI3');
		$diacomi1 =$this->datasis->traevalor('DIACOMI1');
		$diacomi2 =$this->datasis->traevalor('DIACOMI2');
		$diacomi3 =$this->datasis->traevalor('DIACOMI3');

		$form = new DataForm($this->url.'filteredgrid/process');

		$form->vd = new dropdownField('Vendedor', 'vd');
		$form->vd->options("SELECT TRIM(vendedor) AS vd, CONCAT_WS(' ',vendedor,nombre) AS nom FROM vend ORDER BY vendedor");
		$form->vd->rule='required';
		//$form->vd->multiple=true;
		$form->vd->style='width:320px;';

		$form->diacomi1 = new inputField('1- Hasta (d&iacute;as)', 'diacomi1');
		$form->diacomi1->css_class='inputnum';
		$form->diacomi1->size=3;
		$form->diacomi1->maxlength=3;
		$form->diacomi1->rule='required|numeric';
		$form->diacomi1->insertValue = $diacomi1;
		$form->diacomi1->group='Condiciones';

		$form->p1 = new freeField('','','Porcentaje ');
		$form->p1->in='diacomi1';

		$form->porcomi1 = new inputField('Porcent ', 'porcomi1');
		$form->porcomi1->css_class='inputnum';
		$form->porcomi1->size=3;
		$form->porcomi1->maxlength=3;
		$form->porcomi1->in='diacomi1';
		$form->porcomi1->rule='required|numeric|porcent';
		$form->porcomi1->insertValue = $porcomi1;
		$form->porcomi1->append('%');

		$form->diacomi2 = new inputField('2- Hasta (d&iacute;as)', 'diacomi2');
		$form->diacomi2->css_class='inputnum';
		$form->diacomi2->size=3;
		$form->diacomi2->maxlength=3;
		$form->diacomi2->rule='required|numeric';
		$form->diacomi2->insertValue = $diacomi2;
		$form->diacomi2->group='Condiciones';

		$form->p2 = new freeField('','','Porcentaje ');
		$form->p2->in="diacomi2";

		$form->porcomi2 = new inputField('Porcent ', 'porcomi2');
		$form->porcomi2->css_class='inputnum';
		$form->porcomi2->size=3;
		$form->porcomi2->maxlength=3;
		$form->porcomi2->in='diacomi2';
		$form->porcomi2->rule='required|numeric|porcent';
		$form->porcomi2->insertValue = $porcomi2;
		$form->porcomi2->append('%');

		$form->diacomi3 = new inputField('3- Hasta (d&iacute;as)', 'diacomi3');
		$form->diacomi3->css_class='inputnum';
		$form->diacomi3->size=3;
		$form->diacomi3->maxlength=3;
		$form->diacomi3->rule='required|numeric';
		$form->diacomi3->insertValue = $diacomi3;
		$form->diacomi3->group='Condiciones';

		$form->p3 = new freeField('','','Porcentaje ');
		$form->p3->in='diacomi3';

		$form->porcomi3 = new inputField('Porcent ', 'porcomi3');
		$form->porcomi3->css_class='inputnum';
		$form->porcomi3->size=3;
		$form->porcomi3->maxlength=3;
		$form->porcomi3->in='diacomi3';
		$form->porcomi3->rule='required|numeric|porcent';
		$form->porcomi3->insertValue = $porcomi3;
		$form->porcomi3->append('%');

		$form->submit('btnsubmit','Calcular');

		$form->build_form();

		if($form->on_success()){
			print_r($_POST);
			//$porcomi1 = floatval($form->porcomi1->newValue);
			//$porcomi2 = floatval($form->porcomi2->newValue);
			//$porcomi3 = floatval($form->porcomi3->newValue);
			//$diacomi1 = intval($form->diacomi1->newValue);
			//$diacomi2 = intval($form->diacomi2->newValue);
			//$diacomi3 = intval($form->diacomi3->newValue);
			//$vd       = trim($form->vd->newValue);
            //
			//$rt=$this->_actualiza($porcomi1,$porcomi2,$porcomi3,$diacomi1,$diacomi2,$diacomi3,$vd);
			//redirect($this->url.'vista/'.$vd);
		}

		$data['content'] = $form->output;
		$data['title']   = heading($this->tits);
		$data['head']    = script('jquery.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function vista($vd='') {
		$this->rapyd->load('datagrid2');

		$select=array('vd','tipo_doc','numero','fecha','vence','pagada','dias','comision','comical','cod_cli','nombre','sepago','comision');
		$grid = new DataGrid2('Lista de '.$this->titp);
		$grid->db->select($select);
		$grid->db->from('sfac');
		$grid->db->where('tipo_doc <>','X');
		if(!empty($vd)) $grid->db->where('vd',$vd);
		$grid->db->where('sepago','N');
		$grid->db->where('pagada >= fecha');
		$grid->order_by('numero','desc');
		$grid->per_page = 1000;
		$grid->use_function('substr','str_pad','comi');
		$grid->use_function('sta');

		$grid->column('Vendedor'                 ,'vd');
		$grid->column('Tipo'                     ,'<colum><#tipo_doc#></colum>');
		$grid->column('N&uacute;mero'            ,'numero');
		$grid->column('Fecha'                    ,'<dbdate_to_human><#fecha#></dbdate_to_human>' );
		$grid->column('Vence'                    ,'<dbdate_to_human><#vence#></dbdate_to_human>' );
		$grid->column('Pagada'                   ,'<dbdate_to_human><#pagada#></dbdate_to_human>');
		$grid->column('Dias'                     ,'dias'        ,'align=\'right\'');
		$grid->column('Comisi&oacute;n'          ,'<nformat><#comision#></nformat>','align=\'right\'');
		$grid->column('Comisi&oacute;n Calculada','<nformat><#comical#></nformat>','align=\'right\'');
		$grid->column('Cliente'                  ,'cod_cli');
		$grid->column('Nombre'                   ,'nombre' );
		//$grid->totalizar('reiva');

		$grid->build();
		$data['content'] = anchor($this->url."filteredgrid",'Atras').$grid->output;
		$data['title']   = "<h1>$this->titp</h1>";
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function procesar(){
		foreach($_POST['calculada'] as $key=>$value){
			$a=explode('AA',$key);
			$dbnumero= $this->db->escape($a[1]);
			$dbtipo  = $this->db->escape($a[0]);
			$dbvalue = floatval($value);
			$mSQL="UPDATE sfac SET comical=${dbvalue} WHERE numero=${dbnumero} AND tipo_doc=${dbtipo}";
			$this->db->simple_query($mSQL);
		}
		redirect($this->url.'filteredgrid');
	}

	function _actualiza($porcomi1,$porcomi2,$porcomi3,$diacomi1,$diacomi2,$diacomi3,$vd){
		$dbvd = $this->db->escape($vd);

		$ban  = 0;
		$ban += !$this->db->simple_query("UPDATE sfac SET comical=comision WHERE vd=${dbvd} AND sepago='N' AND pagada>=fecha");

		$ban += !$this->db->simple_query("UPDATE sfac SET comical=comision*(100-${porcomi1})/100 WHERE vd=${dbvd} AND sepago='N' AND dias>${diacomi1} AND pagada>=fecha");
		if($porcomi1!=$porcomi2 && $diacomi2!=$diacomi1){
			$ban += !$this->db->simple_query("UPDATE sfac SET comical=comision*(100-${porcomi2})/100 WHERE vd=${dbvd} AND sepago='N' AND dias>${diacomi2} AND pagada>=fecha");
		}
		if($porcomi3!=$porcomi2 && $diacomi2!=$diacomi3){
			$ban += !$this->db->simple_query("UPDATE sfac SET comical=comision*(100-${porcomi3})/100 WHERE vd=${dbvd} AND sepago='N' AND dias>${diacomi3} AND pagada>=fecha");
		}

		$this->db->simple_query("UPDATE valores SET valor='${porcomi1}' WHERE nombre='PORCOMI1'");
		$this->db->simple_query("UPDATE valores SET valor='${porcomi2}' WHERE nombre='PORCOMI2'");
		$this->db->simple_query("UPDATE valores SET valor='${porcomi3}' WHERE nombre='PORCOMI3'");
		$this->db->simple_query("UPDATE valores SET valor='${diacomi1}' WHERE nombre='DIACOMI1'");
		$this->db->simple_query("UPDATE valores SET valor='${diacomi2}' WHERE nombre='DIACOMI2'");
		$this->db->simple_query("UPDATE valores SET valor='${diacomi3}' WHERE nombre='DIACOMI3'");

		if($ban>0){
			return false;
		}else{
			return true;
		}
	}

}
