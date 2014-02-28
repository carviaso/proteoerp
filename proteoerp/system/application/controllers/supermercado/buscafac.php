<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Buscafac extends Controller {

	function Buscafac(){
		parent::Controller();
		$this->load->library('rapyd');
	}

	function index() {
		$this->rapyd->load('datagrid');
		$this->rapyd->load('datafilter');
		$control=array(false , false);

		$filter = new DataFilter('Filtro de Facturas');

		$filter->fechad = new dateField('Desde','fechad','d/m/Y');
		$filter->fechad->operator='>=';
		$filter->fechah = new dateField('Hasta','fechah','d/m/Y');
		$filter->fechah->operator='<=';
		$filter->fechah->clause =$filter->fechad->clause ='where';
		$filter->fechah->db_name=$filter->fechad->db_name='fecha';
		$filter->fechah->size   =$filter->fechad->size   =10;
		$filter->fechah->insertValue=$filter->fechad->insertValue=date('Y/m/d');
		$filter->fechah->group=$filter->fechad->group='Fecha';

		$filter->nombre = new inputField('Nombre', 'nombre');
		$filter->nombre->db_name="CONCAT(b.nombres,' ',b.apellidos)";

		$filter->cedula = new inputField('C&eacute;dula/RIF', 'cedula');

		$filter->buttons('reset','search');

		$action = "javascript:window.location='".site_url('supermercado/poscuadre')."'";
		$filter->button('btn_regresa', 'Regresar', $action, 'TR');

		$filter->build();

		if(empty($filter->fechah->value) or $filter->fechah->value==date('Y/m/d')) $control[0]=true;
		if(empty($filter->fechah->value) or $filter->fechah->value==date('Y/m/d')) $control[1]=true;

		$grid = new DataGrid();
		$grid->per_page = 10;

		$select=array(
			'b.cedula',"DATE_FORMAT(a.fecha, '%d/%m/%Y') fecha",'a.cajero','a.caja',
			'a.tipo','a.numero',"DATE_FORMAT(a.fecha, '%Y%m%d') qfecha",
			"IF(b.nombres IS NULL,a.nombres,CONCAT(b.nombres,' ',b.apellidos)) nombres",
			'a.impuesto','a.gtotal','a.hora'
		);

		$grid->db->select($select);
		$grid->db->from('viefac a');
		$grid->db->join('club b','a.cliente=b.cod_tar','LEFT');
		if($control[0]) $grid->db->where('a.fecha>=','NOW()');
		if($control[1]) $grid->db->where('a.fecha<=','NOW()');
		$grid->db->orderby('a.fecha, a.caja, a.numero');

		$grid->column_detail('Caja' ,'caja', site_url("supermercado/buscafac/verfactura/<#caja#>/<#cajero#>/<#qfecha#>/<#numero#>"));
		$grid->column('Cajero'  ,'cajero'  );
		$grid->column('Tipo'    ,'tipo'    );
		$grid->column('N&uacute;mero'  ,'numero'  );
		$grid->column('Fecha'   ,'fecha'   );
		$grid->column('Cedula'  ,'cedula'  );
		$grid->column('Nombres' ,'nombres' );
		$grid->column('Impuesto','impuesto');
		$grid->column('Total'   ,'gtotal'  );
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = '<h1>Consulta de facturas</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function verfactura($caja,$cajero,$fecha,$numero){
 		$this->rapyd->load('datagrid2');

		$grid = new DataGrid2();
		$grid->db->select=array('a.codigo','b.descrip','a.precio','a.monto','a.cantidad','a.impuesto');
		$grid->db->from('vieite AS a');
		$grid->db->join('maes AS b','a.codigo=b.codigo');
		$grid->db->where('fecha'   ,$fecha);
		$grid->db->where('cajero'  ,$cajero);
		$grid->db->where('caja'    ,$caja);
		$grid->db->where('a.numero',$numero);
		$grid->db->orderby('a.fecha, a.caja, a.numero');

		$grid->column('C&oacute;digo'     ,'codigo'  );
		$grid->column('Descripci&oacute;n','descrip' );
		$grid->column('Cantidad'   ,'cantidad','align="right"');
		$grid->column('Precio'     ,'precio'  ,'align="right"');
		$grid->column('Impuesto'   ,'impuesto','align="right"');
		$grid->column('Total'      ,'monto'   ,'align="right"');
		$grid->totalizar('monto');
		$grid->build();
		$back=anchor('supermercado/buscafac/index/search/osp','Regresar');

		$data['content'] = $grid->output.$back;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = '<h1>Factura</h1>';
		$this->load->view('view_ventanas', $data);
	}

}
