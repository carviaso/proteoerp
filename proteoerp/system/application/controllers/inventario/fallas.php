<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class fallas extends Controller {
	var $falla;

	function fallas(){
		parent::Controller();
		$this->datasis->modulo_id(313,1);
		$this->load->library('rapyd');

		$this->falla[]=array('sql' =>'precio1 <= 0 OR precio2 <= 0 OR precio3 <= 0 OR precio4 <= 0 OR precio1 = NULL OR precio2 = NULL OR precio3 = NULL OR precio4 = NULL', 'nombre' => 'Productos sin precios');
		$this->falla[]=array('sql' =>'ultimo <= 0 OR ultimo IS NULL', 'nombre'                                                 => 'Productos sin costos');
		$this->falla[]=array('sql' =>'base1 <= 0 OR base2 <= 0  OR base3 <= 0  OR base4 <= 0 ', 'nombre'                      => 'Productos sin bases');
		$this->falla[]=array('sql' =>'precio1*100/(100+iva) < ultimo OR precio2*100/(100+iva) < ultimo OR precio3*100/(100+iva) < ultimo OR precio4*100/(100+iva) < ultimo', 'nombre' => 'Productos con precio por debajo de costo');
		$this->falla[]=array('sql' =>'LENGTH(descrip) < 5', 'nombre'                                                          => 'Productos con descripciones menor a 5 caracteres');
		$this->falla[]=array('sql' =>'margen1 > 100 OR margen2 > 100 OR margen3 > 100 OR margen4 > 100', 'nombre'             => 'Productos con margenes altos');
		$this->falla[]=array('sql' =>'margen1 < 10 OR margen2 < 10 OR margen3 < 10 OR margen4 < 10', 'nombre'                 => 'Productos con margenes bajos');
		$this->falla[]=array('sql' =>'existen < 0', 'nombre'                                                                  => 'Productos con existencia negativa');
		$this->falla[]=array('sql' =>'( precio2>precio1 OR precio3>precio2 OR precio3>precio1 OR precio4>precio3 OR precio4>precio2 OR precio4>precio1)', 'nombre'              => 'Precios con orden no secuenciales decreciente');
	}

	function index(){
		$this->rapyd->load('datagrid','dataform','datafilter');
		$this->rapyd->uri->keep_persistence();

		$form = new DataFilter('Seleccione las fallas');
		foreach($this->falla AS $ind=>$checkbox){
			$id='f_'.$ind;
			$form->$id = new checkboxField($checkbox['nombre'], $id, '1');
			$form->$id->clause='';
		}
		$form->submit('reset','Resetear');
		$form->submit('btnsubmit','Buscar');
		$form->build_form();

		$algo['falla']=$this->falla;
		$algo['form'] =& $form;
		$salida=$this->load->view('view_fallas', $algo,true);

		if($this->input->post('btnsubmit')){
			$grid = new DataGrid('Lista de Productos');
			$grid->db->select=array('codigo','LEFT(descrip,20)AS descrip','margen1','margen2','margen3','margen4','base1','base2','base3','base4','precio1','precio2','precio3','precio4','id','existen','ultimo','pond');
			$grid->db->from('sinv');
			$grid->per_page = 15;
			$grid->order_by('existen','desc');
			foreach($this->falla AS $ind=>$data){
				$id='f_'.$ind;
				if($this->input->post($id)){
					$grid->db->or_where($data['sql']);
				}
			}
			$atts = array(
				'width'      => '800',
				'height'     => '600',
				'scrollbars' => 'yes',
				'status'     => 'yes',
				'resizable'  => 'yes',
				'screenx'    => '0',
				'screeny'    => '0'
			);
			$link=anchor_popup('/inventario/sinv/dataedit/show/<#id#>','<#codigo#>', $atts);

			$grid->column('C&oacute;digo',$link);
			$grid->column('Descripci&oacute;n','descrip');
			$grid->column('Margenes'   ,'<ol><li><#margen1#></li><li><#margen2#></li><li><#margen3#></li><li><#margen4#></li></ol>');
			$grid->column('Bases'      ,'<ol><li><#base1#></li><li><#base2#></li><li><#base3#></li><li><#base4#></li></ol>');
			$grid->column('Precios'    ,'<ol><li><#precio1#></li><li><#precio2#></li><li><#precio3#></li><li><#precio4#></li></ol>');
			$grid->column('Costos'     ,'<ul><li><b>Ultimo:</b><#ultimo#></li><li><b>Promedio:</b><#pond#></li></ul>');
			$grid->column_orderby('Existencia' ,'existen','existen' ,'align=\'right\'');
			$grid->build();
			//echo $grid->db->last_query();
			$salida.=$grid->output;
			$salida.=$grid->recordCount.' Registros encontrados';
		}
		$data['content'] = $salida;
		$data['title']   = '<h1>Productos con fallas</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	//Repite el precio superior cuando no existe
	function arreglaprecios(){
		$mSQL='UPDATE sinv SET precio2=precio1, base2=base1, margen2=margen1 WHERE precio2=0 OR precio2 IS NULL';
		var_dump($this->db->simple_query($mSQL));
		$mSQL='UPDATE sinv SET precio3=precio2, base3=base2, margen3=margen2 WHERE precio3=0 OR precio3 IS NULL';
		var_dump($this->db->simple_query($mSQL));
		$mSQL='UPDATE sinv SET precio4=ROUND(ultimo*100/(100-(margen3-0.5))*(1+(iva/100)),2), base4=ROUND(ultimo*100/(100-(margen3-0.5)),2), margen4=margen3-.5 WHERE precio4=0 OR precio4 IS NULL';
		var_dump($this->db->simple_query($mSQL));
	}

	//Arregla la secuencia de precios respetando el precio jerarquico superior
	function arreglasecu(){
		$mSQL='UPDATE sinv SET precio2=precio1, base2=base1, margen2=margen1 WHERE margen2>margen1';
		var_dump($this->db->simple_query($mSQL));
		$mSQL='UPDATE sinv SET precio3=precio2, base3=base2, margen3=margen2 WHERE margen3>margen2';
		var_dump($this->db->simple_query($mSQL));
		$mSQL='UPDATE sinv SET precio4=ROUND(ultimo*100/(100-(margen3-0.5))*(1+(iva/100)),2), base4=ROUND(ultimo*100/(100-(margen3-0.5)),2), margen4=margen3-.5 WHERE margen4>=margen3';
		var_dump($this->db->simple_query($mSQL));
	}

	//Arregla la secuencia reordenando los precios
	function arreglasecuord(){
		$mSQL='SELECT codigo,precio1,precio2,precio3,precio4 FROM sinv WHERE precio2>precio1 OR precio3>precio2 OR precio3>precio1 OR precio4>precio3 OR precio4>precio2 OR precio4>precio1';
		$query = $this->db->query($mSQL);
		foreach ($query->result() as $row){
			$dbcodigo= $this->db->escape($row->codigo);
			$precios = array($row->precio1,$row->precio2,$row->precio3,$row->precio4);
			sort($precios);
			$precios=array_reverse($precios);

			$data=array();
			foreach($precios as $i=>$prec){
				$o=$i+1;
				$ind='precio'.$o;
				$data[$ind] = $prec;
			}

			$where = "codigo = ${dbcodigo}";
			$sql = $this->db->update_string('sinv', $data, $where);
			$this->db->query($sql);
		}
		$this->arreglamarbases();
	}

	//Recalcula los margenes y las bases respetando el precio
	function arreglamarbases(){
		$mSQL="UPDATE sinv SET
		base1=ROUND(precio1*100/(100+iva),2),
		base2=ROUND(precio2*100/(100+iva),2),
		base3=ROUND(precio3*100/(100+iva),2),
		base4=ROUND(precio4*100/(100+iva),2)";
		$this->db->query($mSQL);

		$mSQL="UPDATE sinv SET
		margen1=ROUND(100-((IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(pond,ultimo)))*100)/base1),2),
		margen2=ROUND(100-((IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(pond,ultimo)))*100)/base2),2),
		margen3=ROUND(100-((IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(pond,ultimo)))*100)/base3),2),
		margen4=ROUND(100-((IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(pond,ultimo)))*100)/base4),2)";
		$this->db->query($mSQL);
	}

	//Arregla los margenes negativos
	function arreglamargneg(){
		$mSQL='UPDATE sinv SET precio2=precio1, base2=base1, margen2=margen1 WHERE margen2<=0';
		var_dump($this->db->simple_query($mSQL));
		$mSQL='UPDATE sinv SET precio3=precio2, base3=base2, margen3=margen2 WHERE margen3<=0';
		var_dump($this->db->simple_query($mSQL));
		$mSQL='UPDATE sinv SET precio4=ROUND(ultimo*100/(100-(margen3*0.90))*(1+(iva/100)),2), base4=ROUND(ultimo*100/(100-(margen3*0.9)),2), margen4=margen3*.9 WHERE margen4<=0';
		var_dump($this->db->simple_query($mSQL));
	}
}
