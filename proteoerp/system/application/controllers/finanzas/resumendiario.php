<?php
class Resumendiario extends Controller {

	var $tits  = 'Resumen Diario';
	var $url   = 'finanzas/resumendiario/';

	function Resumendiario(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->fecha=date('Ymd');
		//$this->datasis->modulo_id('51C',1);
		$this->rapyd->config->set_item("theme","proteo");
	}

	function index() {
		redirect($this->url."resumen");
	}

	function resumen(){
		$this->rapyd->load("datagrid2","datafilter","datatable");

		$form = new DataForm("finanzas/resumendiario/resumen/process");
		$form->fecha = new dateonlyField("Fecha","fecha");
		$form->fecha->insertValue = date("Y-m-d");
		$form->fecha->rule ="required|chfecha";
		$form->fecha->size =12;
		$form->submit("btnsubmit","Consultar");
		$form->build_form();

		if ($form->on_success()){
			$this->fecha = $form->fecha->newValue;
		}
		$dbfecha=$this->db->escape($this->fecha);

		//***********************************
		//      RESUMEN DE CAJAS (RCAJ)
		//***********************************

		$grid = new DataGrid2("Resumen de Cajas");

		$grid->db->select(array("cajero", "ingreso AS venta", "recibido", "(ingreso - recibido) AS diferencia"));
		$grid->db->from("rcaj");
		$grid->db->where("fecha",$this->fecha);

		$grid->order_by("caja","asc");
		//$grid->per_page = 15;

		$grid->column("Caja"       ,"cajero"                            ,"align='center'");
		$grid->column("Venta"      ,"<nformat><#venta#></nformat>"      ,"align='right'");
		$grid->column("Recibido"   ,"<nformat><#recibido#></nformat>"   ,"align='right'");
		$grid->column("Diferencia" ,"<nformat><#diferencia#></nformat>" ,"align='right'");

		$grid->totalizar("venta","recibido","diferencia");
		$grid->build();

		//***********************************
		//DISTRIBUCION DE LA COBRANZA (SFPA)
		//***********************************

		$grid2 = new DataGrid2("Distribuci&oacute;n de la cobranza");

		$grid2->db->select(array("a.tipo","b.nombre", "sum(a.monto) AS monto","COUNT(*) AS cantidad"));
		$grid2->db->from("sfpa a");
		$grid2->db->join("tarjeta b","a.tipo=b.tipo");
		$grid2->db->where("f_factura",$this->fecha);
		$grid2->db->groupby('a.tipo');
		$grid2->order_by("a.tipo","asc");
		//$grid2->per_page = 15;

		$grid2->column("Tipo"     ,"(<#tipo#>) <#nombre#>"         ,"align='left'");
		$grid2->column("Cantidad" ,"cantidad"                      ,"align='right'");
		$grid2->column("Monto"    ,"<nformat><#monto#></nformat>"  ,"align='right'");

		$grid2->totalizar("monto");
		$grid2->build();

		//***********************************
		//      VENTAS Y DEVOLUCIONES
		//***********************************

		function vdnom($tipo_doc,$referen){
			$nombre = ($tipo_doc=='D') ? 'Devoluciones ': 'Ventas ';
			$nombre.= ($referen=='E')  ? 'al Contado'   : 'a Credito';
			return $nombre;
		}

		$grid3 = new DataGrid2("Distribuci&oacute;n de la cobranza");
		$grid3->db->select(array('tipo_doc','referen','COUNT(*) AS cana','SUM(totals)*IF(tipo_doc = "D" ,-1,1) AS monto'));
		$grid3->db->from("sfac a");
		$grid3->db->where('referen <>','P');
		$grid3->db->where('fecha',$this->fecha);
		$grid3->db->groupby('tipo_doc');
		$grid3->db->groupby('referen');
		$grid3->use_function('vdnom');

		$grid3->column("Tipo"     ,"<vdnom><#tipo_doc#>|<#referen#></vdnom>","align='left'");
		$grid3->column("Cantidad" ,"<nformat><#cana#>|0</nformat>"          ,"align='right'");
		$grid3->column("Monto"    ,"<nformat><#monto#></nformat>"           ,"align='right'");

		$grid3->totalizar("monto");
		$grid3->build();

		//***********************************
		//   RESUMEN DE VENTAS
		//***********************************
		$udia=days_in_month(substr($this->fecha,4,2),substr($this->fecha,0,4));
		$fdesde= substr($this->fecha,0,6).'01';
		$fhasta= substr($this->fecha,0,6).$udia;
		$ano   = substr($this->fecha,0,4);

		$row1 = $this->datasis->damerow("SELECT COUNT(*) AS a,SUM(totals*(IF(tipo_doc = 'F',1,-1))) AS b FROM sfac WHERE tipo_doc <>'X' AND YEAR(fecha) = $ano AND fecha < $dbfecha");
		$row2 = $this->datasis->damerow("SELECT COUNT(*) AS a,SUM(totals*(IF(tipo_doc = 'F',1,-1))) AS b FROM sfac WHERE tipo_doc <>'X' AND fecha BETWEEN $fdesde AND $fhasta");
		$row3 = $this->datasis->damerow("SELECT COUNT(*) AS a,SUM(totals*(IF(tipo_doc = 'F',1,-1))) AS b FROM sfac WHERE tipo_doc <>'X' AND fecha = $dbfecha");

		$cost1 = $this->datasis->dameval("SELECT SUM(costo*cana*(IF(tipoa = 'F',1,-1))) AS a FROM sitems WHERE tipoa <>'X' AND YEAR(fecha) = $ano AND fecha < $dbfecha");
		$cost2 = $this->datasis->dameval("SELECT SUM(costo*cana*(IF(tipoa = 'F',1,-1))) AS a FROM sitems WHERE tipoa <>'X' AND fecha BETWEEN $fdesde AND $dbfecha");
		$cost3 = $this->datasis->dameval("SELECT SUM(costo*cana*(IF(tipoa = 'F',1,-1))) AS a FROM sitems WHERE tipoa <>'X' AND fecha = $dbfecha");

		if(empty($row1)) $row1=array("a"=>0,"b"=>0);
		if(empty($row2)) $row2=array("a"=>0,"b"=>0);
		if(empty($row3)) $row3=array("a"=>0,"b"=>0);

		$row1['c'] = $cost1;
		$row2['c'] = $cost2;
		$row3['c'] = $cost3;

		$rdata[0]=array('a'=>$row1['a'],'b'=>$row1['b'],'c'=>$row1['c'],'d'=>($row1['b']-$row1['c']),'razon'=>'Ventas en lo que va de año');
		$rdata[1]=array('a'=>$row2['a'],'b'=>$row2['b'],'c'=>$row2['c'],'d'=>($row2['b']-$row2['c']),'razon'=>'Ventas en lo que va de mes');
		$rdata[2]=array('a'=>$row3['a'],'b'=>$row3['b'],'c'=>$row3['c'],'d'=>($row3['b']-$row3['c']),'razon'=>'Ventas de hoy');

		$grid8 = new DataGrid("Resumen de Ventas ".$this->fecha,$rdata);
		$grid8->column("Raz&oacute;n", "razon");
		$grid8->column("Cantidad",     "a","align='right'");
		$grid8->column("Costo",        "<nformat><#c#></nformat>","align='right'");
		$grid8->column("Monto",        "<nformat><#b#></nformat>","align='right'");
		$grid8->column("Margen",       "<nformat><#d#></nformat>","align='right'");
		$grid8->build();
		$rdata=array();

		//***********************************
		//   CUENTAS POR COBRAR (smov)
		//***********************************
		$grid4 = new DataGrid2("Cuentas por Cobrar");
		$grid4->db->select(array("c.gr_desc grupo","SUM((a.monto-a.abonos)*IF(tipo_doc='AN',-1,1))saldo"));
		$grid4->db->from("smov a");
		$grid4->db->join("scli b","a.cod_cli = b.cliente");
		$grid4->db->join("grcl c","b.grupo = c.grupo"    );
		$grid4->db->where("a.tipo_doc IN ('FC','ND','GI','AN')");
		$grid4->db->groupby('c.gr_desc');
		$grid4->order_by("c.gr_desc","asc");
		//$grid4->per_page = 15;

		$grid4->column("Grupo de Clientes" ,"grupo"                         ,"align='left'" );
		$grid4->column("Monto"             ,"<nformat><#saldo#></nformat>"  ,"align='right'");

		$grid4->totalizar("saldo");
		$grid4->build();

		//***********************************
		//            GASTOS
		//***********************************
		$row  = $this->datasis->dameval("SELECT SUM(montotot) AS a FROM scst WHERE tipo_doc = 'FC' AND recep = $dbfecha");
		$row2 = $this->datasis->dameval("SELECT SUM(totbruto) AS a FROM gser WHERE tipo_doc = 'FC' AND fecha = $dbfecha");
		$rdata[0]=array('nombre'=>'Total de Compras','monto'=>$row );
		$rdata[1]=array('nombre'=>'Total de Gastos' ,'monto'=>$row2);

		$grid7 = new DataGrid2("Total Compras y Gastos de Hoy",$rdata);
		$grid7->column("Raz&oacute;n" ,"nombre");
		$grid7->column("Monto"        ,"<nformat><#monto#></nformat>"  ,"align='right'");
		$grid7->totalizar("monto");
		$grid7->build();

		//***********************************
		//   CUENTAS POR PAGAR (sprm)
		//***********************************

		$grid5 = new DataGrid2("Cuentas por Pagar");

		$grid5->db->select(array("c.gr_desc grupo", "SUM((a.monto-a.abonos)*IF(tipo_doc = 'AN',-1,1)) saldo"));
		$grid5->db->from("sprm a");
		$grid5->db->join("sprv b","a.cod_prv = b.proveed");
		$grid5->db->join("grpr c","b.grupo = c.grupo");
		$grid5->db->where("a.tipo_doc IN ('FC','ND','GI','AN')");
		$grid5->db->groupby('c.gr_desc');

		$grid5->order_by("c.gr_desc","asc");
		//$grid5->per_page = 15;

		$grid5->column("Grupo de Proveedoores" ,"grupo"                        ,"align='left'");
		$grid5->column("Monto"                 ,"<nformat><#saldo#></nformat>" ,"align='right'");

		$grid5->totalizar("saldo");
		$grid5->build();

		//***********************************
		//   PROMEDIO INVENTARIO (sinv)
		//***********************************

		$grid6 = new DataGrid2("Total de Inventario");
		$grid6->db->select(array("d.descrip AS descrip","SUM(a.pond*a.existen) AS suma"));
		$grid6->db->from("sinv a");
		$grid6->db->join("grup b ", "a.grupo = b.grupo");
		$grid6->db->join("line c ", "b.linea = c.linea");
		$grid6->db->join("dpto d ", "d.depto = c.depto");
		$grid6->db->groupby('c.depto');
		$grid6->db->order_by('d.descrip');
		//$grid6->per_page = 15;

		$grid6->column("Departamento" ,"descrip"                     ,"align='left'");
		$grid6->column("Monto"        ,"<nformat><#suma#></nformat>" ,"align='right'");

		$grid6->totalizar("suma");
		$grid6->build();

		$data['rcaj']     = $grid->output;
		$data['sfpa']     = $grid2->output;
		$data['tot']      = $grid3->output;
		$data['resven']   = $grid8->output;
		$data['smov']     = $grid4->output;
		$data['scstgser'] = $grid7->output;
		$data['sprm']     = $grid5->output;
		$data['sinv']     = $grid6->output;

		$data0["content"]     = $form->output.$this->load->view('view_resumendiario', $data,TRUE);
		$data0["head"]        = $this->rapyd->get_head();
		$data0['title']       ="<h1>".$this->tits." para la fecha ".dbdate_to_human($this->fecha)."</h1>";
		$this->load->view('view_ventanas', $data0);
	}
}
?>
