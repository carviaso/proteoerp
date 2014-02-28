<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Posfact extends Controller {

	function Posfact(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('115',1);
	}

	function index() {
		$this->rapyd->load('datagrid2');
		$this->rapyd->load('dataform');
		$this->load->helper('openflash');

		$diai =$this->uri->segment(4);
		$mesi =$this->uri->segment(5);
		$anoi =$this->uri->segment(6);
		$diaf =$this->uri->segment(7);
		$mesf =$this->uri->segment(8);
		$anof =$this->uri->segment(9);
		$sucu =$this->uri->segment(10);

		if($diai===FALSE or $mesi===FALSE or $anoi===FALSE or $diaf===FALSE or $mesf===FALSE or $anof===FALSE){
			$usema   = mktime(0, 0, 0, date('m'), date('d'),  date('Y'));
			$fechai  = $fechaf  =date('Y/m/d',$usema);
			$qfechai = $qfechaf =date('Ymd',$usema);
		}else{
			$qfechai = $anoi.$mesi.$diai ;
			$qfechaf = $anof.$mesf.$diaf ;
			$fechai  ="$anoi/$mesi/$diai";
			$fechaf  ="$anof/$mesf/$diaf";
		}

		$select=array(
		"date_format(fecha,'%d/%m/%Y') AS fecha",
		"SUM((gtotal-impuesto)*(SUBSTRING(numero,1,1)<>'X')) AS subtotal",
		"SUM(impuesto*(SUBSTRING(numero,1,1)<>'X')) AS iva",
		"SUM(gtotal*(SUBSTRING(numero,1,1)<>'X')) AS total",
		"SUM(gtotal*(SUBSTRING(numero,1,1)<>'X' AND gtotal<0) ) AS devolu",
		"SUM(gtotal*(SUBSTRING(numero,1,1)='X')) AS nulas",
		"COUNT(*) AS trans"
		);

		$grid = new DataGrid2();
		$grid->db->select($select);
		$grid->db->from('posfact');
		$grid->db->join('scaj','posfact.cajero=scaj.cajero');
		$grid->db->join('caub','scaj.almacen=caub.ubica');
		$grid->db->where('caub.sucursal LIKE',"$sucu%");
		$grid->db->where('fecha','CURDATE()');
		$grid->db->groupby('fecha');
		$grid->column('Fecha'         , 'fecha');
		$grid->column('Sub-Total'     , 'subtotal' ,'align=right');
		$grid->column('I.V.A.'        , 'iva'      ,'align=right');
		$grid->column('Total'         , 'total'    ,'align=right');
		$grid->column('Devoluciones'  , 'devolu'   ,'align=right');
		$grid->column('Anuladas'      , 'nula'     ,'align=right');
		$grid->column('Transferencias', 'trans'    ,'align=right');
		$grid->build();

		//echo $grid->db->last_query();

		$filter = new DataForm('/venta/posfact/index');
		$filter->title('Filtro de ventas cerradas');
		$filter->fechai = new dateField("Desde","fechai","d/m/Y");
		$filter->fechai->insertValue=$fechai;
		$filter->fechaf = new dateField("Hasta","fechaf","d/m/Y");
		$filter->fechaf->insertValue=$fechaf;
		$filter->fechai->size=$filter->fechaf->size=10;

		$filter->sucu = new dropdownField("Sucursal","sucu");
		$filter->sucu->option("","Todas");
		$filter->sucu->options("SELECT codigo,sucursal FROM sucu");

		$filter->button("btnsubmit", "Buscar", form2uri(site_url('supermercado/posfact/index'),array('fechai','fechaf','sucu')), $position="BL");
		$filter->build_form();

		$grid2 = new DataGrid2('Resultados');
		$grid2->totalizar('subtotal','iva' ,'devolu','total');
		$grid2->db->select($select);
		$grid2->db->from('viefac');
		$grid2->db->join('scaj','viefac.cajero=scaj.cajero');
		$grid2->db->join('caub','scaj.almacen=caub.ubica');
		$grid2->db->where("caub.sucursal LIKE","$sucu%");
		$grid2->db->where("fecha >=","$qfechai");
		$grid2->db->where("fecha <=","$qfechaf");
		$grid2->db->groupby("fecha");
		$grid2->column("Fecha"         , "fecha"   );
		$grid2->column("Sub-Total"     , "<number_format><#subtotal#>|2|,|.</number_format>",'align=right');
		$grid2->column("I.V.A."        , "<number_format><#iva#>|2|,|.</number_format>"     ,'align=right');
		$grid2->column("Total"         , "<number_format><#total#>|2|,|.</number_format>"   ,'align=right');
		$grid2->column("Devoluciones"  , "devolu"  ,'align=right');
		$grid2->column("Anuladas"      , "nulas"   ,'align=right');
		$grid2->column("Transferencias", "trans"   ,'align=right');
		$grid2->build();

		$data['content'] =  open_flash_chart_object(680,400, site_url("supermercado/posfact/grafico/$qfechai/$qfechaf/$sucu/"));
		$data['content'] .= '<h3>Ventas en Curso</h3>'.$grid->output;
		$data['content'] .= '<h3>Ventas ya Cerradas</h3>';
		$data['content'] .= $filter->output.$grid2->output;
		$data['title']   = "<h1>An&aacute;lisis de Ventas</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function grafico(){
		$this->load->library('Graph');
		$data = array();
		$dia  = array();

		$qfechai =$this->uri->segment(4);
		$qfechaf =$this->uri->segment(5);
		$sucu    =$this->uri->segment(6);
		//$qfechai ='20070101';
		//$qfechaf ='20070131';

		$mSQL = "SELECT if(b.tipo IS NULL,a.tipo,b.nombre) nombre, sum(a.monto*(SUBSTRING(a.numero,1,1)<>'X')) monto
			 FROM viepag a LEFT JOIN tarjeta b ON a.tipo=b.tipo
			 JOIN scaj c ON a.cajero=c.cajero JOIN caub d ON c.almacen=d.ubica
			 WHERE fecha BETWEEN $qfechai AND $qfechaf AND d.sucursal LIKE '$sucu%'
			 GROUP BY a.tipo ORDER BY monto DESC";

		$res = $this->db->query($mSQL) or die("Bad SQL 1");
		$total = 0;
		foreach( $res->result() as $row )
			if ($row->monto<0) $total += $row->monto*-1; else $total += $row->monto;
		$res->first_row();
		foreach( $res->result() as $row ) {
			$titu[] = substr($row->nombre,0,20);
			if ($row->monto<0) $data[] = round( ($row->monto*100/$total)*-1,0); else $data[] = round( $row->monto*100/$total,0);
		}

		$g = new Graph();
		$g->title( 'DISTRIBUCION DE LA COBRANZA ','{font-size:18px; color: #d01f3c}');
		$g->set_data( $data );
		$g->bar_filled( 80, '#9933CC', '#8010A0', '', 10 );
		$g->set_y_max( 100 );
		$g->bg_colour='#FFFFFF';
		$g->y_label_steps( 5 );
		$g->set_x_labels($titu);
		$g->set_y_legend( 'Porcentaje de Venta', 14,'0x639F45' );
		$g->set_x_label_style( 10, '#9933CC',2 );
		$g->set_x_legend( 'Forma de Pago', 14,'0x639F45' );
		$g->set_bg_image(site_url('/images/ventafon.png'), 'center', 'middle' );
		$g->set_tool_tip( '#val#%25' );
		echo $g->render();
		$res->free_result();
	}
}
