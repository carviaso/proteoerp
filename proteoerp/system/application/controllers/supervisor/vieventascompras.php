<?php
class vieventascompras extends Controller {
	var $titp='Ventas-Compras';
	var $tits='Ventas-Compras';
	var $url ='supervisor/vieventascompras/';
        
	function vieventascompras(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('20D',1);
	}
	function index(){
		redirect($this->url.'viewinventario');
	}
	function filteredgrid($barras='',$farmacia=''){
		$this->rapyd->load('datafilter','datagrid');

		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
                                'barras' =>'Barras',
				'descrip'=>'Descripci&oacute;n',
				'precio1'=>'Precio 1',
				'precio2'=>'Precio 2',
				'precio3'=>'Precio 3',
				'precio4'=>'Precio 4'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','barras' =>'Barras','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('barras'=>'barras'),
			'titulo'  =>'Buscar en inventario',
                        'dbgroup'=>'consolidado');

		$boton=$this->datasis->modbus($modbus);
		$barrase=$this->db->escape($barras);   

		$sql="
		SELECT fecha,fe,fa,descrip,venta,compras,saldo,semanal FROM (
		select '' fe,farmacia fa,fecha,farmacia,descrip,venta,compras,saldo,(venta/4)  semanal
		from  vieventascompras 
		where barras=$barrase AND farmacia is not null
		UNION ALL
		select '','',fecha,farmacia,'TOTALES',SUM(venta),SUM(compras),SUM(saldo),(venta/4)  semanal 
		from  vieventascompras 
		where barras=$barrase AND farmacia is not null
		GROUP BY fecha with rollup
		UNION ALL
		select CONCAT(MID(fecha,1,4),'-',MID(fecha,5,5)) fe,'',fecha,farmacia,'','','','','' 
		from  vieventascompras 
		where barras=$barrase AND farmacia is not null
		GROUP BY fecha 
		)todo
		ORDER BY fecha is null,fecha ,fe='',descrip='TOTALES',farmacia
		";

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
			);

		$descrip=$this->datasis->dameval("SELECT descrip FROM sinv WHERE barras=$barrase LIMIT 10 ");
		$uri = anchor_popup($this->url."kardex/$barras/<#fa#>/<#fecha#>",'<#fa#>',$atts);

		$sql=$this->db->query($sql);
		$sql=$sql->result_array();

		function negrita($descrip,$fecha=NULL){
			if($descrip=='TOTALES' && !(strlen($fecha)>0))
			return "<span style='font-size:1.5em'><strong>TOTAL GENERAL</strong></span>";
			elseif($descrip=='TOTALES')
			return "<span style='font-size:1.2em'><strong>TOTALES</strong></span>";
			else
			return $descrip;
		}

		function negrita2($descrip,$monto){
			if($descrip=='TOTALES')
			return "<span style='font-size:1.2em'><strong>".nformat($monto)."</strong></span>";
			elseif(strlen($monto)>0)
			return nformat($monto);
			else return '';
		}

		$grid = new DataGrid(anchor($this->url.'viewinventario','Ir al Inventario por sucursales'),$sql);
		$grid->db->_escape_char='';
		$grid->db->_protect_identifiers=false;
		$grid->use_function('negrita','negrita2');

		//$grid->column_orderby('Barras'            ,$uri                                           ,'align="left"'  );
		$grid->column('A&ntilde;o/Mes'    ,"fe"                                             ,'align="center"');
		$grid->column('Farmacia'          ,$uri                                             ,'align="left"'  );
		$grid->column('Descripci&oacute;n',"<negrita><#descrip#>|<#fecha#></negrita>"                 ,'align="left"'  );
		$grid->column('Mensual'           ,"<negrita2><#descrip#>|<#venta#></negrita2>"     ,'align="right"' );
		$grid->column('Semanal'           ,"<negrita2><#descrip#>|<#semanal#></negrita2>"   ,'align="right"' );
		$grid->column('Compras'           ,"<negrita2><#descrip#>|<#compras#></negrita2>"   ,'align="right"' );
		$grid->column('Saldo'             ,"<negrita2><#descrip#>|<#saldo#></negrita2>"     ,'align="right"' );

		$grid->build();
		//echo $grid->db->last_query();

		//$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = heading($descrip);
		$this->load->view('view_ventanas', $data);
	}

	function viewinventario(){
		$this->rapyd->load('datafilter','datagrid');
		
		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
                                'barras' =>'Barras',
				'descrip'=>'Descripci&oacute;n',
				'precio1'=>'Precio 1',
				'precio2'=>'Precio 2',
				'precio3'=>'Precio 3',
				'precio4'=>'Precio 4'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','barras' =>'Barras','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('barras'=>'barras'),
			'titulo'  =>'Buscar en inventario',
                        'dbgroup'=>'consolidado');

		$boton=$this->datasis->modbus($modbus);

		$filter = new DataFilter($this->titp, 'viewinventario');

		$filter->barras = new inputField('Barras','barras');
		$filter->barras->size      =17;
		$filter->barras->maxlength =15;
		$filter->barras->append($boton);

		$filter->descrip = new inputField('Descripci&oacute;n','descrip');
		$filter->descrip->size      =47;
		$filter->descrip->maxlength =45;

		$filter->existen = new inputField('Existencia','existen');
		$filter->existen->size      =36;
		$filter->existen->maxlength =34;

		$filter->marca = new inputField('Marca','marca');
		$filter->marca->size      =24;
		$filter->marca->maxlength =22;

		$filter->grupo = new inputField('Grupo','grupo');
		$filter->grupo->size      =6;
		$filter->grupo->maxlength =4;

		$filter->buttons('reset', 'search');
		$filter->build();

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
			);

		$uri = anchor_popup($this->url.'filteredgrid/<#barras#>/','<#barras#>',$atts);

		$grid = new DataGrid('');
		$grid->order_by('barras');
		$grid->per_page = 40;

		$grid->column_orderby('Barras'            ,"$uri"                            ,'barras'    ,'align="left"');
		$grid->column_orderby('Descripci&oacute;n',"descrip"                         ,'descrip'   ,'align="left"');
		$grid->column_orderby('Existencia'        ,"<nformat><#existen#></nformat>"  ,'existen'   ,'align="right"');
		$grid->column_orderby('Marca'             ,"marca"                           ,'marca'     ,'align="left"');
		$grid->column_orderby('Grupo'             ,"grupo"                           ,'grupo'     ,'align="left"');
		$grid->column_orderby('Farmia'            ,"<nformat><#farmia#></nformat>"   ,'farmia'    ,'align="right"');
		$grid->column_orderby('Botica'            ,"<nformat><#botica#></nformat>"   ,'botica'    ,'align="right"');
		$grid->column_orderby('Elcarmen'          ,"<nformat><#elcarmen#></nformat>" ,'elcarmen'  ,'align="right"');
		$grid->column_orderby('Ermita'            ,"<nformat><#ermita#></nformat>"   ,'ermita'    ,'align="right"');
		$grid->column_orderby('Estacion'          ,"<nformat><#estacion#></nformat>" ,'estacion'  ,'align="right"');
		$grid->column_orderby('Galenica'          ,"<nformat><#galenica#></nformat>" ,'galenica'  ,'align="right"');
		$grid->column_orderby('Gema'              ,"<nformat><#gema#></nformat>"     ,'gema'      ,'align="right"');
		$grid->column_orderby('Sebastian'         ,"<nformat><#sebastian#></nformat>",'sebastian' ,'align="right"');

		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);
	}

	function kardex($barras,$farmacia,$fecha){
		
		$f=array(
		 'FARMIA'        => 'FA',
		 'BOTICA'        => 'BO',
		 'DEL CARMEN'    => 'EC',
		 'ERMITA'        => 'ER',
		 'ESTACION'      => 'ES',
		 'GALENICA'      => 'GA',
		 'GEMA'          => 'GE',
		 'SAN SEBASTIAN' => 'SS'
		);
		
		$barrase  =$this->db->escape($barras);
		$farmaciae=$this->db->escape($farmacia);
		$codigo=$this->datasis->dameval("SELECT codigo FROM costos WHERE barras=$barrase AND farmacia=(case $farmaciae when 'FARMIA' then 'FA' when 'BOTICA' then 'BO' when 'DEL CARMEN' then 'EC' when 'ERMITA' then 'ER' when 'ESTACION' then 'ES' when 'GALENICA' then 'GA' when 'GEMA' then 'GE' when 'SAN SEBASTIAN' then 'SS' else 'INDEFINIDA' end)");
		
		redirect('inventario/kardex/filteredgrid/'.raencode($codigo).'/'.$fecha.'/'.$f[$farmacia]);
	}
}