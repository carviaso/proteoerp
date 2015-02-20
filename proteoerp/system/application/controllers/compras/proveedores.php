<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Proveedores extends Controller {

	function Proveedores() {
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->helper('openflash');
		$this->datasis->modulo_id(230,1);
	}

	function index() {
		redirect ('compras/proveedores/anuales');
	}

	function anuales(){
		$this->rapyd->load('datagrid2');
		$this->rapyd->load('dataform');
		$this->load->helper('openflash');

		if($this->uri->segment(4))$anio=$this->uri->segment(4);	elseif(isset($_POST['anio']))$anio=$_POST['anio'];
		if (empty($anio))$anio=date("Y");

		$fechai=$anio.'0101';
		$fechaf=$anio.'1231';

		$filter = new DataForm('compras/proveedores/anuales');
		$filter->title('Filtro de compras Anuales');

		$filter->anio = new inputField('A&ntilde;o', 'anio');
		$filter->anio->size=4;
		$filter->anio->insertValue=$anio;
		$filter->anio->rule = 'max_length[4]';

		$filter->button('btnsubmit', 'Buscar', form2uri(site_url('compras/proveedores/anuales'),array('anio')), $position="BL");
		$filter->build_form();

		$grid = new DataGrid2();
		$select=array('proveed', 'fecha','nombre',
			"SUM(montonet*IF(tipo_doc!='FC',-1,1)) AS grantotal",'MONTH(fecha) AS mes',
			"SUM(inicial*IF( tipo_doc!='FC',-1,1)) AS contado",
			"SUM(credito*IF( tipo_doc!='FC',-1,1)) AS credito",
			"SUM(montotot*IF(tipo_doc!='FC',-1,1)) AS subtotal",
			"SUM(montoiva*IF(tipo_doc!='FC',-1,1)) AS impuesto",
			'COUNT(*) AS numfac'
		);

		$grid->db->select($select);
		$grid->db->from('scst');
		$grid->db->where('tipo_doc <>','NE');
		$grid->db->where('fecha >= ',$fechai);
		$grid->db->where('fecha <= ',$fechaf);
		$grid->db->where('actuali >= fecha');
		$grid->db->groupby('proveed');
		$grid->db->orderby('grantotal DESC');
		$grid->per_page = 15;
		//$grid->db->limit(15,0);

		$grid->column('Proveedor'  , 'nombre','align=\'left\'');
		$grid->column('Sub-Total'  , '<nformat><#subtotal#>|2</nformat>' ,'align=\'right\'');
		$grid->column('Impuesto'   , '<nformat><#impuesto#>|2</nformat>' ,'align=\'right\'');
		$grid->column('Total'      , '<nformat><#grantotal#>|2</nformat>','align=\'right\'');
		$grid->column('Contado'    , '<nformat><#contado#>|2</nformat>'  ,'align=\'right\'');
		$grid->column('Credito'    , '<nformat><#credito#>|2</nformat>'  ,'align=\'right\'');
		$grid->column('Cant. Fact' , 'numfac'   ,'align=\'right\'');

		$grid->totalizar('impuesto','grantotal','contado','credito','subtotal');
		$grid->build();

		$grafico = open_flash_chart_object(700,450, site_url("compras/proveedores/ganuales/${anio}/"));
		$data['content']  = $grafico;
		$data['content'] .= $filter->output.$grid->output;
		$data['head']     = $this->rapyd->get_head();
		$data['title']    = heading('Compras Anuales');
		$this->load->view('view_ventanas', $data);
	}

	function mensuales($anio='',$proveed=''){
		$this->rapyd->load('datagrid2');
		$this->rapyd->load('dataform');
		$this->load->helper('openflash');

		$proveed=radecode($proveed);

		if(isset($_POST['anio']) && empty($anio)) $anio=$_POST['anio'];
		if(isset($_POST['proveed']) && empty($proveed)) $proveed=$_POST['proveed'];

		if (empty($anio) || empty($proveed)) redirect("compras/proveedores/anuales/${anio}");

		$fechai=$anio.'0101';
		$fechaf=$anio.'1231';

		$scli=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'Còdigo proveedor',
				'nombre'  =>'Nombre'
			),
			'filtro'  =>array('proveed'=>'Código proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar proveedor'
		);

		$cboton=$this->datasis->modbus($scli);

		$filter = new DataForm('compras/proveedores/mensuales');
		$filter->title('Filtro de Compras Mensuales');

		$filter->anio = new inputField('A&ntilde;o', 'anio');
		$filter->anio->size=4;
		$filter->anio->insertValue=$anio;
		$filter->anio->rule = 'max_length[4]';

		$filter->proveedor = new inputField('Proveedor', 'proveed');
		$filter->proveedor->size=10;
		$filter->proveedor->insertValue=$proveed;
		//$filter->proveedor->rule = "max_length[4]";
		$filter->proveedor->append($cboton);

		//$filter->button("btnsubmit", "Buscar", form2uri(site_url('compras/proveedores/mensuales/'),array('anio','proveed')), $position="BL");
		$filter->submit('btnsubmit','Buscar');
		$filter->build_form();

		$grid = new DataGrid2();
		$select=array('proveed', 'fecha',
			"SUM(montonet*IF(tipo_doc='NC',-1,1))  AS grantotal","DATE_FORMAT(fecha,'%m/%Y')AS mes",
			"SUM(credito*IF( tipo_doc='NC',-1,1))  AS contado",
			"SUM(inicial*IF( tipo_doc='NC',-1,1))  AS credito",
			"SUM(montotot*IF(tipo_doc='NC',-1,1))  AS subtotal",
			"SUM(montoiva*IF(tipo_doc='NC',-1,1))  AS impuesto",
			'COUNT(*) AS numfac'
		);

		$grid->db->select($select);
		$grid->db->from('scst');
		$grid->db->where('tipo_doc <>','NE');
		$grid->db->where('fecha >= ', $fechai);
		$grid->db->where('fecha <= ', $fechaf);
		$grid->db->where('proveed ' , $proveed);
		$grid->db->where('actuali >= fecha');
		$grid->db->groupby('mes');

		$grid->column('Fecha'       ,'mes','align=\'center\'');
		$grid->column('Sub-Total'   , '<number_format><#subtotal#>|2|,|.</number_format>' ,'align=\'right\'');
		$grid->column('Impuesto'    , '<number_format><#impuesto#>|2|,|.</number_format>' ,'align=\'right\'');
		$grid->column('Total'       , '<number_format><#grantotal#>|2|,|.</number_format>','align=\'right\'');
		$grid->column('Contado'     , '<number_format><#contado#>|2|,|.</number_format>'  ,'align=\'right\'');
		$grid->column('Credito'     , '<number_format><#credito#>|2|,|.</number_format>'  ,'align=\'right\'');
		$grid->column('Cant. Fact'  , 'numfac'   ,'align=\'right\'');

		$grid->totalizar('impuesto','grantotal','contado','credito','subtotal');
		$grid->build();

		$proveed=raencode($proveed);

		$grafico = open_flash_chart_object(680,350, site_url("compras/proveedores/gmensuales/${anio}/${proveed}"));
		$data['content']  =$grafico;
		$data['content'] .=  $filter->output.$grid->output;
		$data['head']     = $this->rapyd->get_head();
		$data['title']    = '<h1>Compras Mensuales</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function diarias($anio='',$proveed='',$mes=''){
		$this->rapyd->load('datagrid2');
		$this->rapyd->load('dataform');
		$this->load->helper('openflash');

		$proveed=radecode($proveed);

		if(isset($_POST['anio'])    && empty($anio))    $anio=$_POST['anio'];
		if(isset($_POST['mes'])     && empty($mes))     $mes=$_POST['mes'];
		if(isset($_POST['proveed']) && empty($proveed)) $proveed=$_POST['proveed'];

		if(empty($mes)) redirect("compras/proveedores/mensuales/${anio}/${proveed}");

		$fechai=$anio.str_pad($mes, 2, '0', STR_PAD_LEFT).'01';
		$fechaf=$anio.str_pad($mes, 2, '0', STR_PAD_LEFT).'31';

		$scli=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'Codigo proveedor',
				'nombre'  =>'Nombre'
			),
			'filtro'  =>array('proveed'=>'Còdigo proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar proveedor'
		);

		$cboton=$this->datasis->modbus($scli);

		$filter = new DataForm('compras/proveedoresdiarios');
		$filter->attributes=array('onsubmit'=>"this.action='index/'+this.form.mes.value+'/'+this.form.anio.value+'/';return FALSE;");
		$filter->title('Filtro de Compras Diarias');

		$filter->mes = new dropdownField('Mes/A&ntilde;o', 'mes');
		for($i=1;$i<13;$i++)
			$filter->mes->option(str_pad($i, 2, '0', STR_PAD_LEFT),str_pad($i, 2, '0', STR_PAD_LEFT));
		$filter->mes->size=2;
		$filter->mes->style='';
		$filter->mes->insertValue=$mes;

		$filter->anio = new inputField('A&ntilde;o', 'anio');
		$filter->anio->in='mes';
		$filter->anio->size=4;
		$filter->anio->insertValue=$anio;
		$filter->anio->rule = 'max_length[4]';

		$filter->proveedor = new inputField('Proveedor', 'proveed');
		$filter->proveedor->size=10;
		$filter->proveedor->insertValue=$proveed;
		$filter->proveedor->rule = 'max_length[4]';
		$filter->proveedor->append($cboton);

		$filter->button('btnsubmit', 'Buscar', form2uri(site_url('compras/proveedores/diarias/'),array('anio','proveed','mes')), $position='BL');
		$filter->build_form();

		$grid = new DataGrid2();
		$select=array('proveed', "DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha",
			"SUM(montonet*IF(tipo_doc='NC',-1,1)) AS grantotal",
			"SUM(inicial*IF( tipo_doc='NC',-1,1)) AS contado",
			"SUM(credito*IF( tipo_doc='NC',-1,1)) AS credito",
			"SUM(montotot*IF(tipo_doc='NC',-1,1)) AS subtotal",
			"SUM(montoiva*IF(tipo_doc='NC',-1,1)) AS impuesto",
			'COUNT(*) AS numfac'
		);

		$grid->db->select($select);
		$grid->db->from('scst');
		$grid->db->where('tipo_doc <> ','NE');
		$grid->db->where('fecha >= '   ,$fechai);
		$grid->db->where('fecha <= '   ,$fechaf);
		$grid->db->where('proveed'     ,$proveed);
		$grid->db->where('actuali >= fecha');
		$grid->db->groupby('fecha');

		$grid->column('Fecha'       , "fecha","align='center'");
		$grid->column('Sub-Total'   , "<number_format><#subtotal#>|2|,|.</number_format>" ,'align=right');
		$grid->column('Impuesto'    , "<number_format><#impuesto#>|2|,|.</number_format>" ,'align=right');
		$grid->column('Total'       , "<number_format><#grantotal#>|2|,|.</number_format>",'align=right');
		$grid->column('Contado'     , "<number_format><#contado#>|2|,|.</number_format>"  ,'align=right');
		$grid->column('Credito'     , "<number_format><#credito#>|2|,|.</number_format>"  ,'align=right');
		$grid->column('Cant. Fact'  , "numfac"   ,'align=right');

		$grid->totalizar('impuesto','grantotal','contado','credito','subtotal');
		$grid->build();

		$proveed=raencode($proveed);

		$grafico = open_flash_chart_object(680,350, site_url("compras/proveedores/gdiarias/${anio}/${proveed}/${mes}"));
		$data['content']  = $grafico;
		$data['content'] .=  $filter->output.$grid->output;
		$data['head']     = $this->rapyd->get_head();
		$data['title']    = '<h1>Compras Diarias</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function ganuales($anio=''){
		$this->load->library('Graph');
		if (empty($anio)) return;

		$fechai=$anio.'0101';
		$fechaf=$anio.'1231';

		$dbfechai = $this->db->escape($fechai);
		$dbfechaf = $this->db->escape($fechaf);

		$mSQL = "SELECT LEFT(nombre,20) AS nombre,proveed,
		SUM(montonet*IF(tipo_doc='NC', -1, 1)) AS grantotal,
		SUM(credito*IF( tipo_doc='NC', -1, 1)) AS credito,
		SUM(inicial*IF( tipo_doc='NC', -1, 1)) AS contado
		FROM scst
		WHERE tipo_doc<>'NE' AND fecha>=${dbfechai} AND fecha<=${dbfechaf}
			AND actuali >= fecha
		GROUP BY proveed ORDER BY grantotal DESC LIMIT 10";
		//echo $mSQL;

		$maxval=0;
		$query = $this->db->query($mSQL);
		foreach($query->result() as $row ){
			if ($row->grantotal>$maxval) $maxval=$row->grantotal;
			$proveed[]=$row->proveed;
			$nombre[] =str_replace('&','',$row->nombre);
			//$data_1[]=$row->contado;
			//$data_2[]=$row->credito;
			$data_3[]=$row->grantotal;
		}

		$om=1;while($maxval/$om>100) $om=$om*10;

		//$bar_1 = new bar(75, '#0053A4');
		//$bar_2 = new bar(75, '#9933CC');
		$bar_3 = new bar(75, '#639F45');

		//$bar_1->key('Contado',10);
		//$bar_2->key('Credito',10);
		$bar_3->key('Monto Bs.'  ,10);

		for($i=0;$i<count($data_3);$i++ ){
			//$bar_1->add_data_tip($data_1[$i]/$om, graph::esc( number_format($data_1[$i],2,',','.')));
			//$bar_2->add_data_tip($data_2[$i]/$om, graph::esc( number_format($data_2[$i],2,',','.')));
			$bar_3->add_data_tip($data_3[$i]/$om, graph::esc( number_format($data_3[$i],2,',','.')));

			//$bar_1->links[]= site_url("/compras/proveedores/mensuales/${anio}/".raencode($proveed[$i]));
			//$bar_2->links[]= site_url("/compras/proveedores/mensuales/${anio}/".raencode($proveed[$i]));
			$bar_3->links[]= site_url("/compras/proveedores/mensuales/${anio}/".raencode($proveed[$i]));
		}

		$g = new graph();
		$g->set_is_decimal_separator_comma(1);
		if($maxval>0){
			$g->title('Los 10 proveedores a los que mas se le a comprado en el '.$anio,'{font-size: 16px; color:#0F3054}' );
			//$g->data_sets[] = $bar_1;
			//$g->data_sets[] = $bar_2;
			$g->data_sets[] = $bar_3;

			$g->set_x_labels($nombre);
			$g->set_x_label_style( 9, '#000000', 2, 1 );
			$g->set_x_axis_steps( 8 );
			$g->set_x_legend('Proveedores', 16, '#004381' );

			$g->bg_colour = '#FFFFFF';
			$g->set_tool_tip( '#key#<br>Proveedor: #x_label# <br>Monto: #tip#' );
			$g->set_y_max(ceil($maxval/$om));
			$g->y_label_steps(5);
			$g->set_y_legend('Compras x '.number_format($om,0,'','.').' (Bs)', 16, '#004381' );
		}else{
			$g->title( 'No existen compras en el a&ntilde;o seleccionado','{font-size:18px; color: #d01f3c}');
		}
		$g->bg_colour='#FFFFFF';
		echo utf8_encode($g->render());
	}

	function gmensuales($anio='',$proveed=''){
		$this->load->library('Graph');
		$this->lang->load('calendar');

		if (empty($anio) or empty($proveed)) return;

		$proveed=radecode($proveed);

		$fechai=$anio.'0101';
		$fechaf=$anio.'1231';

		$dbfechai = $this->db->escape($fechai);
		$dbfechaf = $this->db->escape($fechaf);
		$dbproveed= $this->db->escape($proveed);

		$mSQL = "SELECT  LEFT(nombre,10)as nombre,proveed,MONTHNAME(fecha)AS mes,MONTH(fecha) AS mmes,
		SUM(montonet*IF(tipo_doc='NC', -1, 1))AS grantotal,
		SUM(credito*IF( tipo_doc='NC', -1, 1)) AS credito,
		SUM(inicial*IF( tipo_doc='NC', -1, 1))AS contado
		FROM scst
		WHERE tipo_doc<>'NE' AND fecha>=${dbfechai} AND fecha<=${dbfechaf} AND proveed=${dbproveed}
			AND actuali >= fecha
		GROUP BY MONTH(fecha) ORDER BY fecha,grantotal DESC LIMIT 12";
		//echo $mSQL;

		$maxval=0; $query = $this->db->query($mSQL);

		foreach($query->result() as $row ){ if ($row->grantotal>$maxval) $maxval=$row->grantotal;
			$nmes[]  =$this->lang->line('cal_'.strtolower($row->mes));
			$mmes[]  =$row->mmes;
			$nombre  =str_replace('&','',$row->nombre);
			//$data_1[]=$row->contado;
			//$data_2[]=$row->credito;
			$data_3[]=$row->grantotal;
		}

		$om=1;while($maxval/$om>100) $om=$om*10;

		//$bar_1 = new bar(75, '#0053A4');
		//$bar_2 = new bar(75, '#9933CC');
		$bar_3 = new bar(75, '#639F45');

		//$bar_1->key('Contado',10);
		//$bar_2->key('Credito',10);
		$bar_3->key('Total Bs.'  ,10);

		for($i=0;$i<count($data_3);$i++ ){
			//$bar_1->add_data_tip($data_1[$i]/$om, graph::esc( number_format($data_1[$i],2,',','.')));
			//$bar_2->add_data_tip($data_2[$i]/$om, graph::esc( number_format($data_2[$i],2,',','.')));
			$bar_3->add_data_tip($data_3[$i]/$om, graph::esc( number_format($data_3[$i],2,',','.')));

			$mes=$mmes[$i];
			//$bar_1->links[]= site_url("/compras/proveedores/diarias/${anio}/".raencode($proveed)."/${mes}");
			//$bar_2->links[]= site_url("/compras/proveedores/diarias/${anio}/".raencode($proveed)."/${mes}");
			$bar_3->links[]= site_url("/compras/proveedores/diarias/${anio}/".raencode($proveed)."/${mes}");
		}

		$g = new graph();
		$g->set_is_decimal_separator_comma(1);
		if($maxval>0){
			$g->title( 'Compras a '.$nombre.' en el a&ntilde;o '.$anio,'{font-size: 16px; color:#0F3054}' );
			//$g->data_sets[] = $bar_1;
			//$g->data_sets[] = $bar_2;
			$g->data_sets[] = $bar_3;

			$g->set_x_labels($nmes);
			$g->set_x_label_style( 10, '#000000', 3, 1 );
			$g->set_x_axis_steps( 10 );
			$g->set_x_legend('Meses', 16, '#004381' );

			$g->bg_colour = '#FFFFFF';
			$g->set_tool_tip( '#key#<br>Mes: #x_label# <br>Monto: #tip#' );
			$g->set_y_max(ceil($maxval/$om));
			$g->y_label_steps(5);
			$g->set_y_legend('Compras x '.number_format($om,0,'','.').' (Bs)', 16, '#004381' );
		}else{
			$g->title( 'No existen compras con los datos seleccionados','{font-size:18px; color: #d01f3c}');
		}
		$g->bg_colour='#FFFFFF';
		echo utf8_encode($g->render());
	}

	function gdiarias($anio='',$proveed='',$mes=''){
		$this->load->library('Graph');

		if (empty($mes) or empty($anio)or empty($proveed)) return;

		$proveed=radecode($proveed);

		$fechai=$anio.str_pad($mes, 2, '0', STR_PAD_LEFT).'01';
		$fechaf=$anio.str_pad($mes, 2, '0', STR_PAD_LEFT).'31';

		$dbfechai = $this->db->escape($fechai);
		$dbfechaf = $this->db->escape($fechaf);
		$dbproveed= $this->db->escape($proveed);

		$mSQL = "SELECT  LEFT(nombre,10)as nombre,proveed,DAYOFMONTH(fecha) AS dia ,
		SUM(montonet*IF(tipo_doc='NC', -1, 1)) AS grantotal,
		SUM(credito*IF( tipo_doc='NC', -1, 1)) AS credito,
		SUM(inicial*IF( tipo_doc='NC', -1, 1)) AS contado
		FROM scst
		WHERE tipo_doc<>'NE' AND fecha>=${dbfechai} AND fecha<=${dbfechaf} AND proveed=${dbproveed}
			AND actuali >= fecha
		GROUP BY fecha ORDER BY fecha,grantotal DESC LIMIT 31";
		//echo $mSQL;

		$maxval=0;
		$query = $this->db->query($mSQL);

		foreach($query->result() as $row ){
			if ($row->grantotal>$maxval) $maxval=$row->grantotal;
			$fecha[]=$row->dia;
			$nombre =str_replace('&','',$row->nombre);;
			//$data_1[]=$row->contado;
			//$data_2[]=$row->credito;
			$data_3[]=$row->grantotal;
		}

		$om=1;while($maxval/$om>100) $om=$om*10;

		//$bar_1 = new bar(75, '#0053A4');
		//$bar_2 = new bar(75, '#9933CC');
		$bar_3 = new bar(75, '#639F45');

		//$bar_1->key('Contado',10);
		//$bar_2->key('Credito',10);
		$bar_3->key('Total Bs.'  ,10);

		for($i=0;$i<count($data_3);$i++ ){
			//$bar_1->add_data_tip($data_1[$i]/$om, graph::esc( number_format($data_1[$i],2,',','.')));
			//$bar_2->add_data_tip($data_2[$i]/$om, graph::esc( number_format($data_2[$i],2,',','.')));
			$bar_3->add_data_tip($data_3[$i]/$om, graph::esc( number_format($data_3[$i],2,',','.')));
		}

		$g = new graph();
		$g->set_is_decimal_separator_comma(1);
		if($maxval>0){
			$g->title( 'Compras a '.$nombre.'  en el mes '.$mes.'/'.$anio,'{font-size: 16px; color:##00264A}' );
			//$g->data_sets[] = $bar_1;
			//$g->data_sets[] = $bar_2;
			$g->data_sets[] = $bar_3;

			$g->set_x_labels($fecha);
			$g->set_x_label_style( 10, '#000000', 3, 1 );
			$g->set_x_axis_steps( 10 );
			$g->set_x_legend('Dias', 14, '#004381' );

			$g->bg_colour = '#FFFFFF';
			$g->set_tool_tip( '#key#<br>Dia: #x_label# <br>Monto: #tip#' );
			$g->set_y_max(ceil($maxval/$om));
			$g->y_label_steps(5);
			$g->set_y_legend('Compras x '.number_format($om,0,'','.').' (Bs)', 16, '#004381' );
		}else{
			$g->title( 'No existen compras con los datos seleccionados','{font-size:18px; color: #d01f3c}');
		}
		$g->bg_colour='#FFFFFF';
		echo utf8_encode($g->render());
	}
}
